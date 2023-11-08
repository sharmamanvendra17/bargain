<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Targets extends CI_Controller {

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
        $this->load->model('target_model');   
        $this->load->model('vendor_model');  
        $this->load->model('booking_model');  
        $this->load->model('secondarybooking_model');  
        $this->load->model('admin_model');  
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    } 
    
    public function index(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Target";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role; 
        $data['targets'] =  array(); 
        if(isset($_POST) && !empty($_POST))
        {
            
            //echo "<pre>"; print_r($_POST); die;
            $employee = $_POST['employee'];
            $month = $_POST['month'];
            $year = $_POST['year'];
            $targent_month_year = $month.'-'.$year;
            $condititon = array('targets.year' => $year);
            if(isset($_POST['employee']) && !empty($_POST['employee']))
                $condititon['targets.user_id'] = $employee;
            $data['targets'] = $this->target_model->gettarget($condititon); 
            //echo "<pre>"; print_r($data['targets']); die;
        }   
        
        if($role==1)
        {
            $condition = array('role' => 6, 'team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetSecondaryMakers($condition);
        } 
        else
        {
            $data['employees'] =$this->admin_model->GetAllMakersSecondary(); 
        }
        //echo "<pre>"; print_r($data['targets']); die;
        $this->load->view('targets',$data);
    }  

    public function add(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Target";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role; 
        $data['skulists'] =  array();
        if(isset($_POST) && !empty($_POST))
        {
            $this->form_validation->set_rules('employee', 'Employee ','required'); 
            $this->form_validation->set_rules('month', 'From Month','required');
            $this->form_validation->set_rules('to_month', 'To Month','required');
            $this->form_validation->set_rules('to_year', 'To Yeat','required');

            $this->form_validation->set_rules('weight', 'Weight ','required'); 
            //$this->form_validation->set_rules('retailer_visit', 'Retailer Visit ','required'); 
            $this->form_validation->set_rules('distributor_visit', 'Visit ','required'); 

            if ($this->form_validation->run() == false) {
            }
            else { 
            
                //echo "<pre>"; print_r($_POST); die;
                $employee = $_POST['employee'];
                $from_month = $_POST['month'];
                $to_month = $_POST['to_month'];
                $to_year = $_POST['to_year'];

                $weight = $_POST['weight'];
                $retailer_visit = $_POST['retailer_visit'];
                $distributor_visit = $_POST['distributor_visit'];
                $state = NULL;
                if(isset($_POST['state']))
                {
                    $state = implode(',', $_POST['state']);
                }
                
                $current_year = date("Y");
                $insertdata = array();
                $dataupdated = 0;
                $state = array();
                if(isset($_POST['state']))
                {
                    $state = $_POST['state'];
                }

                for ($i=$from_month; $i <=$to_month ; $i++) { 
                     

                    if(count($state))
                    {

                        foreach ($state as $state_key => $state_value) {

                            $condititon =  array(
                                'user_id' => $employee,                                              
                                'state_ids' => $state_value,                        
                                'targent_month_year' => sprintf("%02d", $i).'-'.$to_year,
                                'month' => sprintf("%02d", $i),       
                                'year' => $to_year,       
                            );

                            $exists = $this->target_model->Check_Target($condititon);
                            //echo "<pre>"; print_r($condititon); die;
                            if($exists)
                            {
                                $updatedata = array(
                                    'user_id' => $employee,
                                    'distributor_visits' => $distributor_visit,
                                    'retailer_visits' => $retailer_visit,
                                    'weight' => $weight,                     
                                    'state_ids' => $state_value,                        
                                    'targent_month_year' => sprintf("%02d", $i).'-'.$to_year,

                                    'month' => sprintf("%02d", $i),       
                                    'year' => $to_year,       
                                ); 
                                $dataupdated = 1;
                                $this->target_model->UpdateTarget($updatedata,$condititon);
                            }
                            else
                            {
                                $adddata = array(
                                    'user_id' => $employee,
                                    'distributor_visits' => $distributor_visit,
                                    'retailer_visits' => $retailer_visit,
                                    'weight' => $weight,                     
                                    'state_ids' => $state_value,                        
                                    'targent_month_year' => sprintf("%02d", $i).'-'.$to_year,

                                    'month' => sprintf("%02d", $i),       
                                    'year' => $to_year,       
                                ); 
                                $this->target_model->Add_Data($adddata);
                                $dataupdated = 1;
                            } 
                        }
                    }
                    else
                    {
                        $condititon =  array(
                            'user_id' => $employee,
                            'targent_month_year' => sprintf("%02d", $i).'-'.$to_year,
                            'month' => sprintf("%02d", $i),       
                            'year' => $to_year,       
                        );

                        $exists = $this->target_model->Check_Target($condititon);
                        if($exists)
                        {
                            $condititon =  array(
                                'user_id' => $employee,
                                'distributor_visits' => $distributor_visit,
                                'retailer_visits' => $retailer_visit,
                                'weight' => $weight,                                 
                                'targent_month_year' => sprintf("%02d", $i).'-'.$to_year,
                                'month' => sprintf("%02d", $i),       
                                'year' => $to_year,       
                            );
                            $dataupdated = 1;
                            $this->target_model->UpdateTarget($updatedata,$condititon);
                        }
                        else
                        {
                            $adddata = array(
                                'user_id' => $employee,
                                'distributor_visits' => $distributor_visit,
                                'retailer_visits' => $retailer_visit,
                                'weight' => $weight,                         
                                'targent_month_year' => sprintf("%02d", $i).'-'.$to_year,
                                'month' => sprintf("%02d", $i),       
                                'year' => $to_year,       
                            );
                            $this->target_model->Add_Data($adddata);
                            $dataupdated = 1;
                        } 
                    }
 
                }
                //echo "<pre>"; print_r($insertdata); die;
                /*for ($current_year; $current_year <=$to_year ; $current_year++) { 

                    $tomonth =  12;
                    if($current_year==$to_year) 
                        $tomonth = $to_month; 
                    for ($i=$from_month; $i <=$tomonth ; $i++) { 
                        $insertdata[] = array(
                            'user_id' => $employee,
                            'distributor_visits' => $distributor_visit,
                            'retailer_visits' => $retailer_visit,
                            'weight' => $weight,                     
                            'state_ids' => implode(',', $_POST['state']),                        
                            'targent_month_year' => sprintf("%02d", $i).'-'.$current_year,
                        );
                    }
                } */
                if($dataupdated)
                {
                    //$added = $this->target_model->Add_Target($insertdata);
                    if($dataupdated)
                        $this->session->set_flashdata('suc_msg','Target added successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                }
                else
                {
                    if($dataupdated)
                        $this->session->set_flashdata('suc_msg','Target added successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                }
                redirect('targets');
            }
        }   
        if($role==1)
        {
            $condition = array('status' => 1,'role' => 6, 'team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetSecondaryMakers($condition);
        } 
        else
        {
            $condition = array('status' => 1);
            $data['employees'] =$this->admin_model->GetAllMakersSecondary($condition); 
        }
        //echo "<pre>"; print_r($data['employees']); die;
        $this->load->model('vendor_model');  
        $data['states'] = $this->vendor_model->GetStates(); 
        $this->load->view('targets_add',$data);
    }  

    public function edit($target_id){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Target";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role; 
        $data['skulists'] =  array();
        $condititon = array('id' => base64_decode($target_id));
        $data['info'] =$this->target_model->targetinfo($condititon); 
        if(isset($_POST) && !empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die;
            $this->form_validation->set_rules('employee', 'Employee ','required'); 
            $this->form_validation->set_rules('month', 'From Month','required'); 
            $this->form_validation->set_rules('to_year', 'To Yeat','required');

            $this->form_validation->set_rules('weight', 'Weight ','required'); 
            //$this->form_validation->set_rules('retailer_visit', 'Retailer Visit ','required'); 
            $this->form_validation->set_rules('distributor_visit', 'Visit ','required'); 

            if ($this->form_validation->run() == false) { 
            }
            else {                 
                $employee = $_POST['employee'];
                $from_month = $_POST['month']; 
                $to_year = $_POST['to_year'];

                $weight = $_POST['weight'];
                $retailer_visit = $_POST['retailer_visit'];
                $distributor_visit = $_POST['distributor_visit'];
                
                $current_year = date("Y");
                $condititon = array('id' => $_POST['target_id']);
                $updatedata = array(
                            'user_id' => $employee,
                            'distributor_visits' => $distributor_visit,
                            'retailer_visits' => $retailer_visit,
                            'weight' => $weight,        
                            'state_ids' => implode(',', $_POST['state']),                                        
                            'targent_month_year' => sprintf("%02d", $_POST['month']).'-'.$_POST['to_year'],
                        );
 
                if($updatedata)
                {
                    $added = $this->target_model->UpdateTarget($updatedata,$condititon);
                    if($added)
                        $this->session->set_flashdata('suc_msg','Target updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                }
                redirect('targets');
            }
        }   
        $this->load->model('vendor_model');  
        $data['states'] = $this->vendor_model->GetStates(); 
        $data['employees'] =$this->admin_model->GetAllMakersSecondary(); 
        //echo "<pre>"; print_r($data['info']); die;
        $this->load->view('targets_edit',$data);
    }  
}
