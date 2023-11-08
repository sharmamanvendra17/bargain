<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasecategory extends CI_Controller {

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
        $this->load->model('purchase/purchase_category_model','category_model');      
                     
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function index(){    
        $data['title'] = "Category";
    	$data['categories'] = $this->category_model->GetCategories();
    	//echo "<pre>"; print_r($data['categories']); die;
    	$this->load->view('purchase/category/category',$data);

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
 

	public function edit_category(){   
        $data['title'] = "Update Category";
		$category_id =  base64_decode($this->uri->segment(4));
		$data['product'] = $this->category_model->GetCategoryByCategoryId($category_id); 
		//echo "<pre>"; print_r($category_id); die;
		if($category_id)
		{
			$condition = array('id' => $category_id);
			if(!empty($_POST))
			{ 
				$name = $this->input->post('name');  

	            $this->form_validation->set_rules('name', 'Product Name','required'); 

	            if ($this->form_validation->run() == false) {
	            }
	            else { 
	                $updatedata = array('category_name' =>$name);

	                $result = $this->category_model->UpdateCategory($updatedata,$condition);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Category updated successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	                redirect('purchase/purchasecategory');
	            }
			} 
			
			//echo "<pre>"; print_r($data['product']); die;
			$this->load->model('brand_model'); 
	        $data['brands'] = $this->brand_model->GetAllBrand(); 
	    }  
		$this->load->view('purchase/category/category_edit',$data);
	}  

	 	
 
    public function add(){
        $data['title'] = "Category Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name');  

            $this->form_validation->set_rules('name', 'Product Name','required'); 

            if ($this->form_validation->run() == false) {
            }
            else { 
                $insertdata = array('category_name' =>$name); 
				//print_r($insertdata); die;
                $result = $this->category_model->AddCategory($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Category added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.'); 
                redirect('purchase/purchasecategory');
            }
        }  
		//echo "<pre>"; print_r($data['alias']); die;
        $this->load->view('purchase/category/category_add',$data);
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
        redirect('pur_category');  
    }


    public function updateStatus() {
        $id = $this->input->post('id');         
        $status = $this->input->post('status'); 
        if (empty($id)) {
            echo '0';
            die;
        }
        if ($this->category_model->updateStatus($id, $status,'pur_category')) {
            echo '1';
        } else {
            echo '0';
            } 
    }   
}