<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

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
        $this->load->model('category_model');      
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    }

    public function index(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Products";
    	$data['products'] = $this->category_model->GetProducts();
    	//echo "<pre>"; print_r($data['products']); die;
    	$this->load->view('products',$data);

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
		redirect('category');
	} 
 

	public function edit_product(){ 
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Update Product";
		$product_id =  base64_decode($this->uri->segment(3));
		$data['product'] = $this->category_model->GetProductByProductId($product_id); 
		//echo "<pre>"; print_r($data['product']); die;
		$this->load->model('brand_model'); 
	    $data['brands'] = $this->brand_model->GetAllBrand(); 

		if($product_id)
		{
			$condition = array('id' => $product_id);
			if(!empty($_POST))
			{ 
				$name = $this->input->post('name'); 
	            $brand = $this->input->post('brand'); 
                $category = $this->input->post('category'); 
	            //$is_enable = $this->input->post('is_enable'); 
	            //$sku = $this->input->post('sku'); 
	            $weight = $this->input->post('weight');  
                $packing_weight = $this->input->post('packing_weight');   

                $loose_rate = $this->input->post('loose_rate');
                $for_rate = $this->input->post('for_rate'); 
                $product_type = $this->input->post('product_type');

                $weight_unit = $this->input->post('weight_unit');
                $quantity = $this->input->post('quantity');

	            $this->form_validation->set_rules('name', 'Product Name','required');
	            $this->form_validation->set_rules('brand', 'Brand','required');
	            $this->form_validation->set_rules('category', 'Category','required');
	            //$this->form_validation->set_rules('is_enable', 'Enable','required');
	            //$this->form_validation->set_rules('sku', 'SKU','required');
	            //$this->form_validation->set_rules('weight', 'weight','required');
                $this->form_validation->set_rules('weight', 'Weight','required');
                $this->form_validation->set_rules('packing_weight', 'Packing Weight','required');
                $this->form_validation->set_rules('loose_rate', 'Loose Rate','required');
                $this->form_validation->set_rules('product_type', 'Product Type','required');

                $this->form_validation->set_rules('weight_unit', 'Weight Unit','required');
                $this->form_validation->set_rules('quantity', 'Quantity','required');

	            if ($this->form_validation->run() == false) {
	            }
	            else { 

                    if($weight_unit==3)
                    {
                       $packing_items = ($weight/1000)*$quantity;
                       $gross_weight = ((($packing_weight/.91)+$weight)/1000)*$quantity;

                    }
                    elseif ($weight_unit==2) {
                        $packing_items = ($weight)*$quantity;
                        $gross_weight = ((($packing_weight/.91)+$weight))*$quantity;
                    }
                    else
                    {
                        $packing_items = $weight*$quantity;
                        $gross_weight =  ($packing_weight+$weight)*$quantity;
                    }

	                $updatedata = array('name' =>$name,'category_id' =>$category,'brand_id' =>$brand,'weight' =>$weight,'loose_rate' =>$loose_rate,'for_rate' =>$for_rate,'product_type' =>$product_type,'packaging_type' => $weight_unit,'packing_items_qty' => $quantity,'packing_items' => $packing_items ,'gross_weight' => $gross_weight,'packing_weight' => $packing_weight); 
                    //echo "<pre>"; print_r($_POST); print_r($updatedata); die;
	                $result = $this->category_model->UpdateProduct($updatedata,$condition);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Category updated successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	                redirect('product');
	            }
			}
	    }
	    else
	    {
	    	redirect('product');
	    }
		$this->load->view('product_edit',$data);
	}  

	 	
 
    public function add(){
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Product Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name'); 
            $category = $this->input->post('category');
            $brand = $this->input->post('brand'); 
            //$is_enable = $this->input->post('is_enable'); 
            //$sku = $this->input->post('sku'); 
            $weight = $this->input->post('weight');
            //$oil_weight = $this->input->post('oil_weight');  
            $packing_weight = $this->input->post('packing_weight'); 
            //$weight = $oil_weight+$packing_weight;
            $loose_rate = $this->input->post('loose_rate');
            $for_rate = $this->input->post('for_rate');   
            $product_type = $this->input->post('product_type');

            $weight_unit = $this->input->post('weight_unit');
            $quantity = $this->input->post('quantity');

            $this->form_validation->set_rules('name', 'Product Name','required');
            $this->form_validation->set_rules('category', 'Category','required');
            $this->form_validation->set_rules('brand', 'Brand','required');
            //$this->form_validation->set_rules('is_enable', 'Enable','required');
            //$this->form_validation->set_rules('sku', 'SKU','required|is_unique[products.sku]');
            $this->form_validation->set_rules('weight', 'Weight','required');
            //$this->form_validation->set_rules('oil_weight', 'Oil Weight','required');
            $this->form_validation->set_rules('packing_weight', 'Packing Weight','required');
            $this->form_validation->set_rules('loose_rate', 'Loose Rate','required');
            $this->form_validation->set_rules('product_type', 'Product Type','required');
            //$this->form_validation->set_rules('for_rate', 'FOR Rate','required');

            $this->form_validation->set_rules('weight_unit', 'Weight Unit','required');
            $this->form_validation->set_rules('quantity', 'Quantity','required');
            if ($this->form_validation->run() == false) {
            }
            else { 

                if($weight_unit==3)
                {
                   $packing_items = ($weight/1000)*$quantity;
                   //$gross_weight = ($packing_weight/1000)*$quantity;
                   $gross_weight = (($packing_weight+$weight)/1000)*$quantity;
                }
                else
                {
                    $packing_items = $weight*$quantity;
                    //$gross_weight = $packing_weight*$quantity;
                    $gross_weight =  ($packing_weight+$weight)*$quantity;
                }

                $insertdata = array('name' =>$name,'category_id' =>$category,'brand_id' =>$brand,'weight' =>$weight,'loose_rate' =>$loose_rate,'for_rate' =>$for_rate,'product_type' =>$product_type,'packaging_type' => $weight_unit,'packing_items_qty' => $quantity,'packing_items' => $packing_items,'gross_weight' => $gross_weight,'packing_weight' => $packing_weight);
                $result = $this->category_model->AddProduct($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Product added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('product');
            }
        } 
        $this->load->model('brand_model'); 
        $data['brands'] = $this->brand_model->GetAllBrand(); 
        $this->load->view('product_add',$data);
    }

    public function delete_product(){
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Category EDelete";
        $product_id = base64_decode($this->uri->segment(3));
        if($product_id)
        {
            $condition = array('id' =>$product_id);
            $result = $this->category_model->DeleteProduct($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Product deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('product');  
    }


    public function getcategory(){   
    	//if (!$this->session->userdata('admin'))
          // redirect('/');  
       	$brand_id = $_POST['brand_id'];
       	$condition = array('brand_id' => $brand_id);
    	$categories = $this->category_model->GetCategory($condition);
    	$res = "<option value=''>Select Category</option>";
    	if($categories)
    	{
    		foreach ($categories as $key => $value) {
    			$res .= '<option value="'.$value['id'].'">'.$value['category_name'].'</option>';
    		}
    	}
    	echo $res; die;
	}

    public function getactivecategory(){   
        //if (!$this->session->userdata('admin'))
          // redirect('/');  
        $brand_id = $_POST['brand_id'];
        $condition = array('brand_id' => $brand_id,'status' => 1);
        $categories = $this->category_model->GetCategory($condition);
        $res = "<option value=''>Select Category</option>";
        if($categories)
        {
            foreach ($categories as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['category_name'].'</option>';
            }
        }
        echo $res; die;
    }


    public function getproduct(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');  
        $category_id = $_POST['category_id'];
        $condition = array('category_id' => $category_id);
        $products = $this->category_model->GetProductsbycategpry_id($category_id);
        $res = "<option value=''>Select Packed In</option>";
        if($products)
        {
            foreach ($products as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        }
        echo $res; die;
    }

    public function getproductlisting(){      
        $category_id = $_POST['category_id'];
        $condition = array('category_id' => $category_id);
        $products = $this->category_model->GetProductsbycategpry_id($category_id);
        //echo "<pre>"; print_r($products); die;
        $this->load->model('booking_model');
        $condition = array('booking_id' => $_POST['id']);
        $skus = $this->booking_model->GetAllSkus($condition);

        $res = "" ;
        if($products)
        {
            $i = 1;            
            foreach ($products as $key => $value) {
                $res .= '<div class="row"><div class="col-md-4"><div class="form-group">';
                if($i==1)
                $res .= '<label for="name">Packed In</label>';
                $res .= '<select class="form-control product_packing" id="" name="product[]">';
                $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $res .= '<option value="'.$value['id'].'">'.$value['name'].$packing_items_qty.'</option>';
                $res .= '</select></div></div><div class="col-md-4"><div class="form-group">';
                $v = '';
                $mt = 0;
                $mt1 = '';
                $lock_img = '';
                $locked_readonly = '';
                $locked = 0;
                $sku_status  =0;
                $sku_id  ='';
                $update_sku_status = '';
                if (array_key_exists($value['id'],$skus))
                {
                    $sku_status  = $skus[$value['id']]['is_lock'];
                    $lock_img = '/un-lock.png';
                    $sku_id  =$skus[$value['id']]['id'];
                    $update_sku_status = 'update_sku_status';
                    if($skus[$value['id']]['is_lock']==1)
                    {
                        $update_sku_status = '';
                        $locked = 1;
                        $locked_readonly = 'readonly';
                        $lock_img = '/lock.png';
                    }
                    $v = $skus[$value['id']]['quantity'];
                    $packing_type = $value['packaging_type'];
                    $packed_items_quantity = $value['packing_items']; 
                    $l_to_kg = 1; 
                    if($packing_type!=1)
                        $l_to_kg = .91; 
                    
                    if($l_to_kg ==.91 && strtolower($value['category_name'])=='vanaspati' )
                        $l_to_kg = .897; 

                    $total_weight_kg= (($v*$packed_items_quantity)*$l_to_kg);
                    $mt =  ($total_weight_kg/1000);
                    $mt_rond = round($mt,2);    
                    $mt1 = round($mt,4).' MT';
                }
                $placeholder = "Number of cartons";
                if($value['packing_items_qty']==1)
                    $placeholder = "Number of tins";
                                   
                if($i==1)
                $res .= '<label for="quantity">Quantity</label>';
                $res .= '<input type="hidden" class="packing_weight" name="packing_weight[]" value="'.$mt.'"><input type="hidden" class="packing_type" name="packing_type[]" value="'.$value['packaging_type'].'"><input type="hidden" class="packed_items_quantity" name="packed_items_quantity[]" value="'.$value['packing_items'].'" ><input type="text" class="form-control quantity_packed" id="" name="quantity[]"  value="'.$v.'" placeholder="'.$placeholder.'"></div></div><div class="col-md-3"><div class="form-group">';

                if($i==1)
                $res .= '<label for="quantity">Weight (MT)</label>';
                $res .='<input type="text" class="form-control packing_weight_input" id="" name=""  value="'.$mt1.'" readonly>';
                $res .='</div></div><div class="col-md-1"><div class="form-group">';
                if($i==1)
                $res .= '<label for="quantity">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>';
          
                if($lock_img)
                 $res .='<span class="btn btn-danger '.$update_sku_status.'" data-id="'.$_POST['id'].'"  data-status="'.$sku_status.'" rel="'.base64_encode($sku_id).'"><img style="height: 17px;width: 17px;" src="'.base_url('assets/img').$lock_img.'"></span>'; 
                $res .='</div></div></div>';
                $i++;
            }
            
        } 
        echo $res; die;
    }
    

    public function getproductlist(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');  
        $category_id = $_POST['category_id'];
        $condition = array('category_id' => $category_id);
        $products = $this->category_model->GetProductsbycategpry_id($category_id);
        $res = "<option value=''>Select Packed In</option>";
        if($products)
        {
            foreach ($products as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        } 
        echo $res."__".count($products); die;
    }

    public function getrate(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');
          //echo "<pre>"  ; print_r($_POST); die;
        $category_id = $_POST['category_id'];
        $brand_id = $_POST['brand_id'];
        $condition = array('brand_id' => $brand_id,'id' => $category_id);
        $rates = $this->category_model->GetRate($condition); 
        $rate = 0;
        if($rates)
        {
            $rate = $rates['product_price'].'_'.$rates['is_ex_rate'].'_'.$rates['insurance_included'];
             
        }
        echo $rate;
    }


    public function getrate_booking(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');
          //echo "<pre>"  ; print_r($_POST); die;
        $category_id = $_POST['category_id'];
        $brand_id = $_POST['brand_id'];
        $condition = array('brand_id' => $brand_id,'category_id' => $category_id);
        $rates = $this->category_model->GetRateBooking($condition); 
        $rate = 0;
        if($rates)
        {
            
            $rate = $rates['rate'].'_'.$rates['is_ex_rate'].'_'.$rates['insurance_included'].'_'.date('d-m-Y H:i:s',strtotime($rates['created_at']));
             
        }
        echo $rate;
    }
    public function updateStatus() {
        $id = $this->input->post('id');         
        $status = $this->input->post('status'); 
        if (empty($id)) {
            echo '0';
            die;
        }
        if ($this->category_model->updateStatus($id, $status,'products')) {
            echo '1';
        } else {
            echo '0';
            } 
    }  
}