<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasecity extends CI_Controller {

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
        $this->load->model('purchase/purchase_city_model','city_model');      
                     
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();
    }

    public function index(){    
        $data['title'] = "City";
        $data['cities'] = $this->city_model->GetCities();
        //echo "<pre>"; print_r($data['categories']); die;
        $this->load->view('purchase/city/city',$data);
    }

    public function add(){
        $data['title'] = "City Add";
        $data['states'] = $this->city_model->GetStates();
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name');
            $state = $this->input->post('state');  
            $this->form_validation->set_rules('state', 'State Name','required'); 
            $this->form_validation->set_rules('name', 'City Name','required'); 
            if ($this->form_validation->run() == false) {
            }
            else { 
                $insertdata = array('state_id' =>$state,'name' =>$name); 
                //print_r($insertdata); die;
                $result = $this->city_model->AddCity($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','City added successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.'); 
                redirect('purchase/purchasecity');
            }
        }  
        //echo "<pre>"; print_r($data['alias']); die;
        $this->load->view('purchase/city/city_add',$data);
    }

    public function edit_city(){   
        $data['title'] = "Update Category";
        $city_id =  base64_decode($this->uri->segment(4));
        $data['city'] = $this->city_model->GetCityInfo($city_id); 
        $data['states'] = $this->city_model->GetStates();
        //echo "<pre>"; print_r($category_id); die;
        if($city_id)
        {
            $condition = array('id' => $city_id);
            if(!empty($_POST))
            { 
                $name = $this->input->post('name');  
                $state = $this->input->post('state');  
                $this->form_validation->set_rules('name', 'City Name','required'); 
                $this->form_validation->set_rules('state', 'State Name','required'); 
                if ($this->form_validation->run() == false) {
                }
                else { 
                    $updatedata = array('state_id' =>$state,'name' =>$name);

                    $result = $this->city_model->UpdateCity($updatedata,$condition);
                    if($result)
                        $this->session->set_flashdata('suc_msg','City updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    redirect('purchase/purchasecity');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die;
            $this->load->model('brand_model'); 
            $data['brands'] = $this->brand_model->GetAllBrand(); 
        }  
        $this->load->view('purchase/city/city_edit',$data);
    } 

    public function status_update(){ 
        $data['title'] = "City Update";
        $status =  $this->uri->segment(3);
        $category_id =  base64_decode($this->uri->segment(4)); 
        $update_data = array('is_enable' => $status);
        $condition  = array('id' => $category_id);
        $result= $this->city_model->UpdateCategory($update_data,$condition);
        if($result)
            $this->session->set_flashdata('suc_msg','Category updated successfully.');
        else
            $this->session->set_flashdata('err_msg','Something went wrong.');
        redirect('pur_category');
    } 
    

    public function delete_category(){
        $data['title'] = "Category EDelete";
        $category_id = base64_decode($this->uri->segment(3));
        if($category_id)
        {
            $condition = array('id' =>$category_id);
            $result = $this->city_model->DeleteCategory($condition); 
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
        if ($this->city_model->updateStatus($id, $status,'pur_category')) {
            echo '1';
        } else {
            echo '0';
            } 
    }   
}