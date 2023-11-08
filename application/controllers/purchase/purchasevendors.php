<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasevendors extends CI_Controller {

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
        $this->load->model('purchase/purchase_vendor_model','vendor_model');  
        $this->load->model('admin_model');   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();                  

    }

    public function index(){ 
    	$data['title'] = "Vendors";
    	$data['users'] = $this->vendor_model->GetUsers(); 
    	$this->load->view('purchase/vendor/vendors',$data);

	}

    public function add(){
        $data['title'] = "Vendor Add";

        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
        $logged_role = $admin_role; 
        $data['logged_role'] = $logged_role;
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name'); 
            $gst = $this->input->post('gst'); 
            $mobile = $this->input->post('mobile'); 
            $address = $this->input->post('address'); 
            $state = $this->input->post('state');   
            $city = $this->input->post('city');   
            $zipcode = $this->input->post('zipcode');    
            $email = $this->input->post('email');

            $this->form_validation->set_rules('name', 'Party Name','required');
            $this->form_validation->set_rules('gst', 'GST Number','required|is_unique[pur_vendors.gst_no]');
            $this->form_validation->set_rules('mobile', 'Mobile','required');
            $this->form_validation->set_rules('address', 'Address','required');
            $this->form_validation->set_rules('state', 'State','required');
            $this->form_validation->set_rules('city', 'City','required'); 
            //$this->form_validation->set_rules('email', 'Email','required');  

            if ($this->form_validation->run() == false) {
            }
            else { 
                
            	$condition = array('gst_no' =>$gst);
            	$added = $this->vendor_model->GetUserbyId($condition);
            	if(count($added))
            	{
            		$this->session->set_flashdata('err_msg','Already added');
            	}
	            else 
	            {
	                $insertdata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'gst_no' =>$gst,'zipcode' =>$zipcode,'email' =>$email); 
	                $result = $this->vendor_model->AddVendor($insertdata);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Vendor added successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	            }
                redirect('purchase/purchasevendors');
            }
        } 
        $data['states'] = $this->vendor_model->GetStates();  
        $this->load->view('purchase/vendor/vendor_add',$data);
    }

    public function edit_vendor(){  
        $data['title'] = "Update Vendor";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
        $logged_role = $admin_role; 
        $data['logged_role'] = $logged_role;
        $vendor_id =  base64_decode($this->uri->segment(4));
        $data['vendor'] = $this->vendor_model->GetVendorbyId($vendor_id); 
        //echo "<pre>"; print_r($data['vendor']); die;
        if($vendor_id)
        {
            $condition = array('id' => $vendor_id);
            if(!empty($_POST))
            { 
                $name = $this->input->post('name'); 
                $gst = $this->input->post('gst'); 
                $mobile = $this->input->post('mobile'); 
                $address = $this->input->post('address'); 
                $state = $this->input->post('state');   
                $city = $this->input->post('city');   
                $zipcode = $this->input->post('zipcode');    
                $email = $this->input->post('email');

                $this->form_validation->set_rules('name', 'Party Name','required');
                $this->form_validation->set_rules('gst', 'GST Number','required');
                $this->form_validation->set_rules('mobile', 'Mobile','required');
                $this->form_validation->set_rules('address', 'Address','required');
                $this->form_validation->set_rules('state', 'State','required');
                $this->form_validation->set_rules('city', 'City','required');  
                //$this->form_validation->set_rules('email', 'Email','required'); 

                if ($this->form_validation->run() == false) {
                }
                else { 
                    $updatedata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'gst_no' =>$gst,'zipcode' =>$zipcode,'email' =>$email);
                    $result = $this->vendor_model->UpdateVendor($updatedata,$condition);
                    if($result)
                        $this->session->set_flashdata('suc_msg','Vendor updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    redirect('purchase/purchasevendors');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die; 
        }
        else
        {
            redirect('purchase/purchasevendors');
        }
        $data['states'] = $this->vendor_model->GetStates();
        //$data['employees'] = $this->vendor_model->GetEmployees(); 
        $data['employees'] = $this->admin_model->GetAllMakers(); 
        $this->load->view('purchase/vendor/vendor_edit',$data);
    } 


    public function getcity(){   
        $state_id = $_POST['state_id'];
        $condition = array('state_id' => $state_id);
        $cities = $this->vendor_model->GetCity($condition);
        $res = "<option value=''>Select City</option>";
        if($cities)
        {
            foreach ($cities as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        }
        echo $res; die;
    }

    public function delete_vendor(){
        $data['title'] = "Delete Vendor";
        $vendor_id = base64_decode($this->uri->segment(4));
        if($vendor_id)
        {
            $condition = array('id' =>$vendor_id);
            $result = $this->vendor_model->DeleteVendor($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Vendor deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('purchasevendors');  
    }
}