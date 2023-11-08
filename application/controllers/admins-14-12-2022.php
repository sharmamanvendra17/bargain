<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admins extends CI_Controller {

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
        $this->load->model(array('admin_model','employee_model'));  
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();           

    }

    public function index(){ 
    	$data['title'] = "Team";
    	$data['users'] = $this->admin_model->GetAdmin(); 
    	$this->load->view('admins',$data);

	}

    public function add(){
        $data['title'] = "Team Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name'); 
            $username = $this->input->post('username'); 
            $mobile = $this->input->post('mobile'); 
            $role = $this->input->post('role'); 
            $team_lead_id = NULL;
            $viewers =  '';
            if($role==1)
            {
                $teamlead = $this->input->post('teamlead');     
                $team_lead_id = ($teamlead) ? $teamlead : NULL;

                $viewer = $this->input->post('viewer');     
                $viewers = (count($viewer)) ? implode(',', $viewer) : '';
            }
            $this->form_validation->set_rules('name', 'Name','required');
            $this->form_validation->set_rules('username', 'Username','required|is_unique[admin.username]');
            $this->form_validation->set_rules('mobile', 'Mobile','required'); 
            $this->form_validation->set_rules('role', 'Role','required');


            if ($this->form_validation->run() == false) {
            }
            else {  
                $state_id = implode(',', $_POST['state']);
                $insertdata = array('name' =>$name,'username' =>$username,'mobile' =>$mobile,'role' =>$role,'team_lead_id' =>$team_lead_id,'unauthorized_viewers' =>$viewers,'state_id' =>$state_id);
                $insertdata['allow_rate'] = 0;
                if(isset($_POST['allow_rate']))
                    $insertdata['allow_rate'] = 1;


                $insertdata['allow_rate_booking'] = 0;
                if(isset($_POST['allow_rate_booking']))
                    $insertdata['allow_rate_booking'] = 1;
                
                if($employee)
                        $insertdata['employee_id'] = $employee;
                $result = $this->admin_model->AddAdmin($insertdata);
                if($result)
                {
                    $this->session->set_flashdata('suc_msg','Admin added successfully.');
                     
                }
                else
                {
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                }
                redirect('admins');
            }
        } 

        $data['checkers'] = $this->admin_model->GetAllCheckers();
        $data['viewers'] = $this->admin_model->GetAllViewers();
        $data['roles'] = $this->admin_model->GetRoles();
        //print_r($data['roles']); die;
        $this->load->model('vendor_model');  
        $data['states'] = $this->vendor_model->GetStates(); 
        $this->load->view('admin_add',$data);
    }

    public function edit_admin(){   
        $data['title'] = "Update Team";
        $admin_id =  base64_decode($this->uri->segment(3));
        $data['admin'] = $this->admin_model->GetAddminbyId($admin_id);  
        if($admin_id)
        {
            $condition = array('id' => $admin_id);
            if(!empty($_POST))
            { 
                $name = $this->input->post('name'); 
                $username = $this->input->post('username'); 
                $change_password = $this->input->post('change_password'); 
                $role = $this->input->post('role');    
                $employee_id = $this->input->post('employee_id');   
                $team_lead_id = NULL;
                $viewers =  '';
                if($role==1)
                {
                    $teamlead = $this->input->post('teamlead');     
                    $team_lead_id = ($teamlead) ? $teamlead : NULL;

                    $viewer = $this->input->post('viewer');     
                    $viewers = (count($viewer)) ? implode(',', $viewer) : '';
                } 
                $this->form_validation->set_rules('name', 'Name','required');
                $this->form_validation->set_rules('username', 'Username','required');
                $this->form_validation->set_rules('role', 'Role','required');

                if($change_password)
                {
                    $this->form_validation->set_rules('password', 'Password','required');
                    $this->form_validation->set_rules('confirm_password', 'Confirm Password','required|matches[password]');
                }

                if ($this->form_validation->run() == false) {
                }
                else { 
                    $state_id = implode(',', $_POST['state']);
                    $updatedata = array('name' =>$name,'username' =>$username,'role' =>$role,'team_lead_id' =>$team_lead_id,'unauthorized_viewers' =>$viewers,'state_id' =>$state_id);

                    $updatedata['allow_rate'] = 0;
                    if(isset($_POST['allow_rate']))
                        $updatedata['allow_rate'] = 1;

                    $updatedata['allow_rate_booking'] = 0;
                    if(isset($_POST['allow_rate_booking']))
                        $updatedata['allow_rate_booking'] = 1;
                    
                    if($change_password)
                        $updatedata['password'] = md5($password);
                     $condition = array('id' => $admin_id);
                    $result = $this->admin_model->UpdateAdmin($updatedata,$condition);
                    if($result)
                        $this->session->set_flashdata('suc_msg','Admin updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    redirect('admins');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die; 
        }
        else
        {
            redirect('admins');
        }
        $data['checkers'] = $this->admin_model->GetAllCheckers();
        $data['viewers'] = $this->admin_model->GetAllViewers();
        $data['roles'] = $this->admin_model->GetRoles();
        $this->load->model('vendor_model');  
        $data['states'] = $this->vendor_model->GetStates(); 
        $this->load->view('admin_edit',$data);
    } 


    public function getcity(){     
        $state_id = $_POST['state_id'];
        $condition = array('state_id' => $state_id);
        $cities = $this->vendor_model->GetCity($condition);
        $res = "";
        if($cities)
        {
            foreach ($cities as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        }
        echo $res; die;
    }

    public function delete_admin(){
        $data['title'] = "Delete Vendor";
        $admin_id = base64_decode($this->uri->segment(3));
        $employee_id = base64_decode($this->uri->segment(4));
        if($admin_id)
        {
            $condition = array('id' =>$admin_id);
            $result = $this->admin_model->DeleteAdmin($condition); 
            if($result)
            {
                $this->session->set_flashdata('suc_msg','Admin deleted successfully.'); 
                $updatedata = array('is_admin' => 0);
                $condition = array('id' => $employee_id);
                $this->admin_model->UpdateEmployee($updatedata,$condition); 
            }
            else
            {
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
            }
        }
        redirect('admins');  
    }
}