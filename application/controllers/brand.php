<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends CI_Controller {

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
        $this->load->model('brand_model');                   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function index(){
    	$data['title'] = "Brand";
    	$data['categories'] = $this->brand_model->GetAllBrand(); 
    	$this->load->view('brand',$data);
	} 
    
    public function edit(){
        $data['title'] = "Brand Edit";
        $barnd_id = base64_decode($this->uri->segment(3));
        if($barnd_id)
        {
            $condition = array('id' =>$barnd_id);
            $data['brand'] = $this->brand_model->GetBrandinfo($condition);  
            //echo "<pre>"; print_r($data['barnd']); die;
        }
        else
        {
            redirect('brand');
        }

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die;
            $name = $this->input->post('name'); 
            $this->form_validation->set_rules('name', 'Brand Name','required');
            if ($this->form_validation->run() == false) {
            }
            else {
                $updatedata = array('name' =>$name);
                $result = $this->brand_model->UpdateBrand($updatedata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','Brand updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('brand');
            }
        }
        $data['barnds'] = $this->brand_model->GetAllBrand(); 
        $this->load->view('brand_update',$data);
    }

    public function delete(){
        $data['title'] = "Brand Edit";
        $barnd_id = base64_decode($this->uri->segment(3));
        if($barnd_id)
        {
            $condition = array('id' =>$barnd_id);
            $result = $this->brand_model->DeleteBrand($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Brand deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('brand');  
    }

    public function add(){
        $data['title'] = "Brand Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name'); 
            $this->form_validation->set_rules('name', 'Brand Name','required');
            if ($this->form_validation->run() == false) {
            }
            else {
                $name = $_POST['name'];
                $insertdata = array('name' =>$name);
                $result = $this->brand_model->AddBrand($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Brand added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('brand');
            }
        } 
        $this->load->view('brand_add',$data);
    }
    public function updateStatus() {
        $id = $this->input->post('id');         
        $status = $this->input->post('status');
        if (empty($id)) {
            echo '0';
            die;
        }
        if ($this->brand_model->updateStatus($id, $status)) {
            echo '1';
        } else {
            echo '0';
            } 
    }   
}