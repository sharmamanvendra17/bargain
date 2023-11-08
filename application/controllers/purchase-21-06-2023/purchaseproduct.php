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
	                $updatedata = array('product_name' =>$name,'category_id' =>$category);

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
                $insertdata = array('product_name' =>$name,'category_id' =>$category); 
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
                $res .= '<option value="'.$value['id'].'">'.$value['product_name'].'</option>';
            }
        } 
        echo $res; die;
    } 
}