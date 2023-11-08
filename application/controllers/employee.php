<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

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
        $this->load->model('employee_model');                   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function index(){
    	$data['title'] = "Employee";
    	$data['employees'] = $this->employee_model->GetAllEmployee(); 
    	$this->load->view('employee',$data);
	} 
    
    public function edit(){
        $data['title'] = "Employee Edit";
        $employee_id = base64_decode($this->uri->segment(3));
        if($employee_id)
        {
            $condition = array('id' =>$employee_id);
            $data['brand'] = $this->employee_model->GetEmployeeinfo($condition);  
            //echo "<pre>"; print_r($data['barnd']); die;
        }
        else
        {
            redirect('employee');
        }

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die;
            $name = $this->input->post('name'); 
            $this->form_validation->set_rules('name', 'Employee Name','required');
            if ($this->form_validation->run() == false) {
            }
            else {
                $updatedata = array('name' =>$name);
                $result = $this->employee_model->UpdateEmployee($updatedata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','Employee updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('employee');
            }
        } 
        $this->load->view('employee_update',$data);
    }

    public function delete(){
        $data['title'] = "Employee Edit";
        $employee_id = base64_decode($this->uri->segment(3));
        if($employee_id)
        {
            $condition = array('id' =>$employee_id);
            $result = $this->employee_model->DeleteEmployee($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Employee deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('employee');  
    }

    public function add(){
        $data['title'] = "Employee Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name'); 
            $this->form_validation->set_rules('name', 'Employee Name','required');
            if ($this->form_validation->run() == false) {
            }
            else {
                $name = $_POST['name'];
                $insertdata = array('name' =>$name);
                $result = $this->employee_model->AddEmployee($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Employee added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('employee');
            }
        } 
        $this->load->view('employee_add',$data);
    }   
}