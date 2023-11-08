<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location extends CI_Controller {

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
        $this->load->model(array('location_model'));      
                     
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function states(){    
        $data['title'] = "States"; 
        $data['states'] = $this->location_model->GetStates(); 
    	$this->load->view('states',$data);
	}

	public function state_add(){ 
        $data['title'] = "New State";

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $state_name = $this->input->post('state_name');  
            $this->form_validation->set_rules('state_name', 'State Name','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                 

                $insertdata = array('name' =>$state_name);
                $result = $this->location_model->AddState($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','State added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('states');
            }
        } 
    	$this->load->view('state_add',$data);
    }


    public function edit_state(){ 
        $data['title'] = "State Edit";
        $state_id = base64_decode($this->uri->segment(3));
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $state_name = $this->input->post('state_name');  
            $this->form_validation->set_rules('state_name', 'State Name','required');


            if ($this->form_validation->run() == false) {
            }
            else { 

                $insertdata = array('name' =>$state_name);
                $condition = array('id' =>$state_id);
                $result = $this->location_model->UpdateState($insertdata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','State updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('states');
            }
        } 
        $data['state_info'] = $this->location_model->GetStateById($state_id);
        //echo "<pre>"; print_r($data['state_info']); die;
        $this->load->view('state_edit',$data);
    }
	
    public function delete_state(){
        $data['title'] = "State Delete";
        $state_id = base64_decode($this->uri->segment(3));
        if($state_id)
        {
            $condition = array('id' =>$state_id);
            $result = $this->location_model->DeleteState($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','State deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('states');  
    }



    public function districts(){   
        $data['title'] = "Districts"; 
        $data['districts'] = $this->location_model->GetDisctrict(); 
        //echo "<pre>"; print_r($data['districts']); die;
        $this->load->view('districts',$data);
    }

    public function district_add(){ 
        $data['title'] = "New District";

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $state_id = $this->input->post('state_name');
            $district_name = $this->input->post('district_name');  
            $this->form_validation->set_rules('state_name', 'State Name','required');
            $this->form_validation->set_rules('district_name', 'District Name','required');

            if ($this->form_validation->run() == false) {
            }
            else { 
                 

                $insertdata = array('name' =>$district_name,'state_id' =>$state_id);
                $result = $this->location_model->AddDisctrict($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','State added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('districts');
            }
        } 
        $data['states'] = $this->location_model->GetStates();
        $this->load->view('district_add',$data);
    }


    public function edit_district(){ 
        $data['title'] = "State Edit";
        $district_id = base64_decode($this->uri->segment(3));
        if(!empty($_POST))
        {
            $state_id = $this->input->post('state_name');
            $district_name = $this->input->post('district_name');  
            $this->form_validation->set_rules('state_name', 'State Name','required');
            $this->form_validation->set_rules('district_name', 'District Name','required');


            if ($this->form_validation->run() == false) {
            }
            else { 

                $insertdata = array('name' =>$district_name,'state_id' =>$state_id);
                $condition = array('id' =>$district_id);
                $result = $this->location_model->UpdateDisctrict($insertdata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','District updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('districts');
            }
        } 
        $data['district_info'] = $this->location_model->GetDisctrictById($district_id);
        $data['states'] = $this->location_model->GetStates();
        //echo "<pre>"; print_r($data['state_info']); die;
        $this->load->view('districts_edit',$data);
    }
    
    public function delete_district(){
        $data['title'] = "State Delete";
        $district_id = base64_decode($this->uri->segment(3));
        if($district_id)
        {
            $condition = array('id' =>$district_id);
            $result = $this->location_model->DeleteDisctrict($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','District deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('districts');  
    }




    public function city(){   
        $data['title'] = "Cities"; 
        $data['cities'] = $this->location_model->GetCities(); 
        //echo "<pre>"; print_r($data['districts']); die;
        $this->load->view('cities',$data);
    }

    public function city_add(){ 
        $data['title'] = "New City";

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $state_id = $this->input->post('state_name');
            $city_name = $this->input->post('city_name');  
            $this->form_validation->set_rules('state_name', 'State Name','required');
            $this->form_validation->set_rules('city_name', 'City Name','required');

            if ($this->form_validation->run() == false) {
            }
            else { 
                 

                $insertdata = array('name' =>$city_name,'state_id' =>$state_id);
                $result = $this->location_model->AddCity($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','City added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('city');
            }
        } 
        $data['states'] = $this->location_model->GetStates();
        $this->load->view('city_add',$data);
    }


    public function edit_city(){ 
        $data['title'] = "City Edit";
        $city_id = base64_decode($this->uri->segment(3));
        if(!empty($_POST))
        {
            $state_id = $this->input->post('state_name');
            $city_name = $this->input->post('city_name');  
            $this->form_validation->set_rules('state_name', 'State Name','required');
            $this->form_validation->set_rules('city_name', 'City Name','required');

            if ($this->form_validation->run() == false) { 
            }
            else { 

                $insertdata = array('name' =>$city_name,'state_id' =>$state_id);
                $condition = array('id' =>$city_id);
                $result = $this->location_model->UpdateCity($insertdata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','City updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('city');
            }
        } 
        $data['city_info'] = $this->location_model->GetCityById($city_id);
        $data['states'] = $this->location_model->GetStates();
        //echo "<pre>"; print_r($data['state_info']); die;
        $this->load->view('city_edit',$data);
    }
    
    public function delete_city(){
        $data['title'] = "State Delete";
        $city_id = base64_decode($this->uri->segment(3));
        if($city_id)
        {
            $condition = array('id' =>$city_id);
            $result = $this->location_model->DeleteCity($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','City deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('city');  
    }

    
}