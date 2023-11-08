<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

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
                     
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function index(){  
        $data['title'] = "Category";
    	$data['categories'] = $this->category_model->GetCategories();
    	$this->load->view('category',$data);

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
 

	public function edit_category(){  
        $data['title'] = "Update Category";
		$category_id =  base64_decode($this->uri->segment(3));
		$data['product'] = $this->category_model->GetCategoryByCategoryId($category_id);
		$old_hsn  = $data['product']['hsn'];

		if($category_id)
		{
			$condition = array('id' => $category_id);
			if(!empty($_POST))
			{ 
				$name = $this->input->post('name'); 
	            $brand = $this->input->post('brand');      


	            $this->form_validation->set_rules('name', 'Product Name','required');
	            $this->form_validation->set_rules('brand', 'Brand','required'); 


	            if ($this->form_validation->run() == false) {
	            }
	            else { 
	                $updatedata = array('category_name' =>$name,'brand_id' =>$brand);
	                $result = $this->category_model->UpdateCategory($updatedata,$condition);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Category updated successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	                redirect('category');
	            }
			} 
			
			//echo "<pre>"; print_r($data['product']); die;
			$this->load->model('brand_model'); 
	        $data['brands'] = $this->brand_model->GetAllBrand(); 
	    }
	    else
	    {
	    	redirect('category');
	    }
		$this->load->view('category_edit',$data);
	}  

	public function edit_product_old(){   
        $data['title'] = "Update Product";
		$product_id =  base64_decode($this->uri->segment(3));
		if(!empty($_POST))
		{ 
			$packages = $_POST['package'];
            $mrp_rates = $_POST['mrp']; 
			if(count($packages))
			{
				foreach ($packages as $key => $price) {
                    $mrp = $mrp_rates[$key];
                    
					$update_data = array('price' => $price,'mrp' => $mrp);
					$condition  = array('id' => $key);
                    $result= $this->category_model->UpdatePackaging($update_data,$condition);

				} 
				$this->session->set_flashdata('suc_msg','Product updated successfully.');
			}
			else
				$this->session->set_flashdata('err_msg','Nothing to update.');
			redirect('product_admin');
		}
		$data['packagings'] = $this->category_model->GetProductPackagingByProductId($product_id);
		$data['product'] = $this->category_model->GetProductByProductId($product_id);
		$this->load->view('packagings',$data);
	} 	
 
    public function add(){
        $data['title'] = "Category Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name'); 
            $brand = $this->input->post('brand');  


            $this->form_validation->set_rules('name', 'Product Name','required');
            $this->form_validation->set_rules('brand', 'Brand','required'); 


            if ($this->form_validation->run() == false) {
            }
            else { 


                $insertdata = array('category_name' =>$name,'brand_id' =>$brand);
                $checked = $this->category_model->CheckCategory($insertdata);
                if(!$checked)
                {

	                $result = $this->category_model->AddCategory($insertdata);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Category added successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	            }
	            else
	                $this->session->set_flashdata('err_msg','Already registered.');



                redirect('category');
            }
        } 
        $this->load->model('brand_model'); 
        $data['brands'] = $this->brand_model->GetAllBrand(); 
        $data['packages'] = $this->category_model->GetAllPackaging(); 
        $this->load->view('category_add',$data);
    }

    public function delete_category(){
        $data['title'] = "Category EDelete";
        $category_id = base64_decode($this->uri->segment(3));
        if($category_id)
        {
            $condition = array('id' =>$category_id);
            $result = $this->category_model->DeleteCategory($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Category deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('category');  
    }
}