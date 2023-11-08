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
        $this->load->model(array('admin_model','employee_model','vendor_model'));  
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn(); 
    }

    public function GetSuperDistributers(){  
        $state_ids = implode(',', $_POST['state']); 
        $condition = array('state_ids' => $state_ids);       
        $vendors = $this->vendor_model->GetUsersByState($state_ids);
        //echo "<pre>"; print_r($vendors); die;
        $response = '<option value="">Select Super Distributor</option>';
        if($vendors)
        {
            foreach ($vendors as $key => $value) {
                $response .= '<option value="'.$value['id'].'">'.$value['name'].' - '.$value['city_name'].'</option>';
            }
        }
        echo $response; 
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

            if($role==6)
            {
                $teamlead = $this->input->post('maker');     
                $team_lead_id = ($teamlead) ? $teamlead : NULL; 
            }
            $this->form_validation->set_rules('name', 'Name','required');
            $this->form_validation->set_rules('username', 'Username','required|is_unique[admin.username]');
            $this->form_validation->set_rules('mobile', 'Mobile','required'); 
            $this->form_validation->set_rules('role', 'Role','required');
            $this->form_validation->set_rules('business_role', 'Business Role','required');

            if ($this->form_validation->run() == false) {
            }
            else {  
                $state_id = implode(',', $_POST['state']);
                $business_role = $_POST['business_role'];
                $vendor_id = 0; 
                if($role==6)
                    $vendor_id = ($_POST['vendor']) ? implode(',', $_POST['vendor']) : 0;

                $insertdata = array('name' =>$name,'username' =>$username,'mobile' =>$mobile,'role' =>$role,'team_lead_id' =>$team_lead_id,'unauthorized_viewers' =>$viewers,'state_id' =>$state_id,'business_role' =>$business_role,'vendor_id' =>$vendor_id);
                $insertdata['allow_rate'] = 0;
                if($role!=6)
                {
                    if(isset($_POST['allow_rate']))
                        $insertdata['allow_rate'] = 1;
                }



                $insertdata['allow_rate_booking'] = 0;
                if($role!=6)
                {
                    if(isset($_POST['allow_rate_booking']))
                        $insertdata['allow_rate_booking'] = 1;
                }
                
                if($role!=6)
                {
                    if($employee)
                            $insertdata['employee_id'] = $employee;
                }

                $insertdata['pi_access'] = NULL;

                if($role==8)
                {
                    if(isset($_POST['pi_access']) && !empty($_POST['pi_access']))
                    {
                        if(strtolower($_POST['pi_access'])=='all')
                            $pi_access = NULL;
                        else
                            $pi_access = $_POST['pi_access'];
                        $insertdata['pi_access'] = $pi_access;
                    }
                }
                $insertdata['pi_making_access'] = 0;
                if($role==1)
                {
                    if(isset($_POST['pi_making_access']))
                        $insertdata['pi_making_access'] = 1;
                }
                $insertdata['performance_viewer'] = NULL;
                if($role==1 || $role==6)
                {
                    if(isset($_POST['performance_viewer']) && !empty($_POST['performance_viewer']))
                        $insertdata['performance_viewer'] = $_POST['performance_viewer'];
                }
                $insertdata['rate_pdf'] = 0;
                if(isset($_POST['rate_whatsapp']))
                {
                    $insertdata['rate_pdf'] = 1;
                }

                $insertdata['persona_user'] = NULL;
                if($role==1)
                {
                    if(isset($_POST['persona_user']) && $_POST['persona_user']!='')
                        $insertdata['persona_user'] = implode(',',$_POST['persona_user']);
                }

                    
                if(isset($_POST['joining_date']) && $_POST['joining_date']!='')
                    $insertdata['joining_date'] = date('Y-m-d', strtotime(trim($_POST['joining_date'])));
                $result = $this->admin_model->AddAdmin($insertdata);
                if($result)
                {
                    include 'mailer/welcome-email.php'; 
                    $from = "hr@dil.in";
                    $from_name = "HR";
                    $subject   = 'Welcome To Sales Portal';  
                    $body_message = '<p>Dear '.$name.'</p>
                                    <p>You have been successfully added to the portal. Please ensure you have startd XgenPlus app and successfully logged in with your official email address.  To login into Sales Portal,  visit https://sales.datagroup.in with weblogin code. The code can be generated from XgenPlus app</p>
                                    <p>This portal is your primary place to report your performace and work and also know your targets. You are supposed to be in touch with your reporting head and follow instructions. </p>
                                    <p>Please ensure that you login every day and submit your progress.  This portal guides your reporting head , accounts and HR for your working and reembursements of Salary and all the expenses you make. So take it very sincerely and follow as guided. In case you have any issue in using it, talk to HR department. </p>
                                    <p>Best Wishes.</p>
                                    <p>HR Depart.</p>';
                    if(trim($username) && ($role==6 || $role==1))
                        smtpmailer($username, $from,$from_name,$subject,$body_message,"");
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
        $data['makers'] = $this->admin_model->GetAllMakers();
        $this->load->view('admin_add',$data);
    }

    public function edit_admin(){   
        $data['title'] = "Update Team";
        $admin_id =  base64_decode($this->uri->segment(3));
        $data['admin'] = $this->admin_model->GetAddminbyId($admin_id); 
        //echo "<pre>";  print_r($_POST); die;
        if($admin_id)
        {

            $condition = array('id' => $admin_id);
            if(!empty($_POST))
            { 
                //echo "<pre>";  print_r($_POST); die;
                $name = $this->input->post('name'); 
                $username = $this->input->post('username'); 
                $change_password = $this->input->post('change_password'); 
                $role = $this->input->post('role');    
                $employee_id = $this->input->post('employee_id');  
                $mobile = $this->input->post('mobile'); 
                $team_lead_id = NULL;
                $viewers =  '';
                if($role==1)
                {
                    $teamlead = $this->input->post('teamlead');     
                    $team_lead_id = ($teamlead) ? $teamlead : NULL;
                    $viewer = "";
                    if(isset($_POST['viewer']))
                    {
                        $viewer = $this->input->post('viewer');  
                        $viewers = (count($viewer)) ? implode(',', $viewer) : '';
                    }
                } 
                if($role==6)
                {
                    $teamlead = $this->input->post('maker');     
                    $team_lead_id = ($teamlead) ? $teamlead : NULL;

                }
                $this->form_validation->set_rules('name', 'Name','required');
                $this->form_validation->set_rules('username', 'Username','required');
                $this->form_validation->set_rules('role', 'Role','required');
                $this->form_validation->set_rules('business_role', 'Business Role','required');
                $this->form_validation->set_rules('mobile', 'Mobile','required'); 

                if($change_password)
                {
                    $this->form_validation->set_rules('password', 'Password','required');
                    $this->form_validation->set_rules('confirm_password', 'Confirm Password','required|matches[password]');
                }

                if ($this->form_validation->run() == false) {
                }
                else { 
                    $state_id = '';
                    if(isset($_POST['state']))
                        $state_id = implode(',', $_POST['state']);
                    $business_role = $_POST['business_role'];
                    $vendor_id = 0;
                    if($role==6)
                        $vendor_id = ($_POST['vendor']) ? implode(',', $_POST['vendor']) : 0;
                    $updatedata = array('name' =>$name,'username' =>$username,'role' =>$role,'team_lead_id' =>$team_lead_id,'unauthorized_viewers' =>$viewers,'state_id' =>$state_id,'business_role' =>$business_role,'vendor_id' =>$vendor_id,'mobile' =>$mobile,);

                    $updatedata['allow_rate'] = 0;
                    if($role!=6)
                    {
                        if(isset($_POST['allow_rate']))
                            $updatedata['allow_rate'] = 1;
                    }

                    $updatedata['allow_rate_booking'] = 0;
                    if($role!=6)
                    {
                        if(isset($_POST['allow_rate_booking']))
                            $updatedata['allow_rate_booking'] = 1;
                    }
                    
                    if($change_password)
                        $updatedata['password'] = md5($password);
                     $condition = array('id' => $admin_id); 
                    $updatedata['pi_access'] = NULL;
                    //echo $_POST['pi_access']; die;
                    if($role==8)
                    {
                        if(isset($_POST['pi_access']) && !empty($_POST['pi_access']))
                        {
                            if(strtolower($_POST['pi_access'])=='all')
                                $pi_access = NULL;
                            else
                                $pi_access = $_POST['pi_access'];
                            $updatedata['pi_access'] = $pi_access;
                        }
                    }
                    $updatedata['pi_making_access'] = 0;
                    if($role==1)
                    {
                        if(isset($_POST['pi_making_access']))
                            $updatedata['pi_making_access'] = 1;
                    }

                    $updatedata['performance_viewer'] = NULL;
                    if($role==1 || $role==6)
                    {
                        if(isset($_POST['performance_viewer']) && !empty($_POST['performance_viewer']))
                            $updatedata['performance_viewer'] = $_POST['performance_viewer'];
                    }
                    //echo "<pre>"; print_r($_POST); die;

                    $updatedata['rate_pdf'] = 0;
                    if(isset($_POST['rate_whatsapp']))
                    {
                        $updatedata['rate_pdf'] = 1;
                    }
                    //echo "<pre>"; print_r($updatedata); die;
                    $updatedata['persona_user'] = NULL;
                    if($role==1)
                    {
                        if(isset($_POST['persona_user']) && $_POST['persona_user']!='')
                            $updatedata['persona_user'] = implode(',',$_POST['persona_user']);
                    }

                    if(isset($_POST['joining_date']) && $_POST['joining_date']!='')
                    $updatedata['joining_date'] = date('Y-m-d', strtotime(trim($_POST['joining_date'])));
                    $result = $this->admin_model->UpdateAdmin($updatedata,$condition);
                    if($result)
                    {
                         
                        if($role==1)
                        {
                            $condition  = array('team_lead_id' => $admin_id);
                            $updatedata1['performance_viewer'] = $_POST['performance_viewer'];
                            $this->admin_model->UpdateAdmin($updatedata1,$condition);
                        }
                        $this->session->set_flashdata('suc_msg','Admin updated successfully.');  
                    }
                    else
                    {
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    }
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
        $data['makers'] = $this->admin_model->GetAllMakers();
        $state_ids = $data['admin']['state_id'];
        $data['vendors'] =  array();
        if($state_ids)
        {
            $data['vendors'] = $this->vendor_model->GetUsersByState($state_ids);  
        }
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

    public function updateStatus() {
        $id = $this->input->post('id');         
        $status = $this->input->post('status'); 
        if (empty($id)) {
            echo '0';
            die;
        }
        if ($this->admin_model->updateStatus($id, $status,'admin')) {
            echo '1';
        } else {
            echo '0';
            } 
    }
}