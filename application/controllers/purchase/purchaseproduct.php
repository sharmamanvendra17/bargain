<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseproduct extends CI_Controller {

	function __construct()
    { 
        parent::__construct();
        $this->load->library(array('session','user_agent'));
        $this->load->helper(array(
            'form',
            'url',
            'common'));

        $this->load->library('form_validation');
        $this->load->library('pagination'); 

        $this->load->model('purchase/product_model');    
        $this->load->model('purchase/purchase_category_model','category_model');      
                     
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function index(){    
        $data['title'] = "Products";
    	$data['products'] = $this->product_model->GetProducts();
    	//echo "<pre>"; print_r($data['categories']); die;
    	$this->load->view('purchase/product/products',$data);

	}
	 
	public function status_update(){ 
        $data['title'] = "";
		$status =  $this->uri->segment(3);
		$category_id =  base64_decode($this->uri->segment(4)); 
		$update_data = array('is_enable' => $status);
		$condition  = array('id' => $category_id);
		$result= $this->category_model->UpdateCategory($update_data,$condition);
		if($result)
			$this->session->set_flashdata('suc_msg','Category updated successfully.');
		else
			$this->session->set_flashdata('err_msg','Something went wrong.');
		redirect('pur_category');
	} 
 

	public function edit_product(){   
        $data['title'] = "Update Category";
		$product_id =  base64_decode($this->uri->segment(4));
		$data['product'] = $this->product_model->GetProductByProductId($product_id); 
		//echo "<pre>"; print_r($category_id); die;
		if($product_id)
		{
			$condition = array('id' => $product_id);
			if(!empty($_POST))
			{ 
				$name = $this->input->post('name');  
                $category = $this->input->post('category');  
	            $this->form_validation->set_rules('name', 'Product Name','required'); 

	            if ($this->form_validation->run() == false) {
	            }
	            else { 
                    $attributes = implode(',', $_POST['attributes']);
	                $updatedata = array('product_name' =>$name,'category_id' =>$category,'attributes' =>$attributes);

	                $result = $this->product_model->UpdateProduct($updatedata,$condition);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Category updated successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	                redirect('purchase/purchaseproduct');
	            }
			} 
			
			//echo "<pre>"; print_r($data['product']); die;
			
	    }  
        $data['categories'] = $this->category_model->GetCategories(); 
        $data['attributes'] = $this->product_model->GetProductsAttributes();
		$this->load->view('purchase/product/product_edit',$data);
	}  

	 	
 
    public function add(){
        $data['title'] = "Category Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name');  
            $category = $this->input->post('category'); 
            $this->form_validation->set_rules('name', 'Product Name','required'); 
            $this->form_validation->set_rules('name', 'Category Name','required'); 

            if ($this->form_validation->run() == false) {
            }
            else { 
                $attributes = implode(',', $_POST['attributes']);
                $insertdata = array('product_name' =>$name,'category_id' =>$category,'attributes' =>$attributes); 
				//print_r($insertdata); die;
                $result = $this->product_model->AddProduct($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Category added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.'); 
                redirect('purchase/purchaseproduct/');
            }
        }  
		//echo "<pre>"; print_r($data['alias']); die;
        $data['categories'] = $this->category_model->GetCategories();        
        $data['attributes'] = $this->product_model->GetProductsAttributes();
        $this->load->view('purchase/product/product_add',$data);
    } 


    public function updateStatus() {
        $id = $this->input->post('id');         
        $status = $this->input->post('status'); 
        if (empty($id)) {
            echo '0';
            die;
        }
        if ($this->product_model->updateStatus($id, $status,'pur_products')) {
            echo '1';
        } else {
            echo '0';
            } 
    }  

    public function getproductlist(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');  
        $category_id = $_POST['category_id'];
        $condition = array('pur_products.category_id' => $category_id,'pur_products.status' => 1);
        $products = $this->category_model->GetProductsbycategpry_id($condition);
        $res = "<option value=''>Select Product</option>";
        if($products)
        {
            foreach ($products as $key => $value) {
                $res .= '<option value="'.$value['id'].'" data-attributes="'.$value['attributes'].'">'.$value['product_name'].'</option>';
            }
        } 
        echo $res; die;
    } 

    public function getattributes(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');  
        $product_id = $_POST['product_id'];
        //$condition = array('pur_products.product_id' => $product_id,'pur_products.status' => 1);
        //$attributes = $this->category_model->Getattributesbyproduct_id($attributes);
        $condition_rouduct = array('pur_products.id'=> $product_id);
        $attributes = $this->category_model->Getallattributesbyproduct_id($condition_rouduct);

        //echo "<pre>"; print_r($attributes); die;
        $res = "";
        if($attributes)
        {
            foreach ($attributes as $key => $value) {
                $res .= '<div class="col-md-4">
                            <div class="form-group"> 
                                <label for="'.$value['name'].'">'.$value['name'].' '.$value['data_range'].'</label> 
                                <input default_value="'.$value['default_value'].'" max_value="'.$value['max_value'].'" min_value="'.$value['min_value'].'"  type="text" class="form-control custom_attributes_input" id="'.$value['alias'].'" name="'.$value['alias'].'"  value="'.$value['default_value'].'" placeholder="'.$value['name'].'" required>  
                                <span class="txt-danger v_'.$value['alias'].'"></span>
                            </div>
                        </div>';
            }
        } 
        echo $res; die;
    } 


    public function getreportattributes(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');  
        $product_id = $_POST['product_id'];
        $inventory_id = $_POST['inventory_id'];
        //$condition = array('pur_products.product_id' => $product_id,'pur_products.status' => 1);
        //$attributes = $this->category_model->Getattributesbyproduct_id($attributes);
        $condition_rouduct = array('pur_products.id'=> $product_id);
        $attributes = $this->category_model->Getallattributesbyproduct_id($condition_rouduct);

        $this->load->model(array('purchase/purchase_model')); 
        $condition =  array('pur_inventory.id' => $inventory_id);
        $purchase_info = $this->purchase_model->GetInventoryReportInfo($condition);

        
        $res = "";
        if($attributes)
        {
            foreach ($attributes as $key => $value) {
                if($value['alias']!='mandi_expenses')
                {
                    $col_name= 'lab_result_'.$value['alias'];
                    $attr_val =  ($purchase_info[$col_name]) ? $purchase_info[$col_name] : '';
                    $res .= '<div class="col-md-4">
                            <div class="form-group"> 
                                <label for="'.$value['name'].'">'.$value['name'].' '.$value['data_range'].'</label>
                                <input type="text" class="form-control custom_attributes_input" id="'.$value['alias'].'" name="'.$value['alias'].'"  value="'.$attr_val.'" placeholder="'.$value['name'].'" required>  
                                <span class="txt-danger v_'.$value['alias'].'"></span>
                            </div>
                        </div>';
                }
            }
        } 
        $res .= '<div class="col-md-4">
                    <label for="sales_executive">Color</label> 
                    <div class="form-group"> 
                        <input type="text" class="form-control" name="color" id="color" placeholder="Color" value='.$purchase_info['lab_result_color'].'>
                    </div>
                </div> 
                <div class="col-md-4">
                    <label for="sales_executive">Smell</label> 
                    <div class="form-group"> 
                        <input type="text" class="form-control" name="smell" id="smell" placeholder="Smell" value='.$purchase_info['lab_result_smell'].'>
                    </div>
                </div> 
                <div class="col-md-4">
                    <label for="sales_executive">Remark</label> 
                    <div class="form-group"> 
                        <textarea class="form-control" name="remark" id="remark" placeholder="Remark">'.$purchase_info['lab_result_remark'].'</textarea>
                    </div>
                </div> ';
        echo $res; die;
    } 

}