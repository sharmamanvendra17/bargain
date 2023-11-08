<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distributors extends CI_Controller {

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
        $this->load->model('distributor_model');  
        $this->load->model('admin_model'); 
        $this->load->model('vendor_model');   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();                  

    }

    public function index(){ 
    	$data['title'] = "Distributors";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $state_id = $this->session->userdata('admin')['state_id'];
        $condition = array();
        if(!is_null($state_id) && $state_id!='')
            $condition = array('distributors.state_id' => $state_id);
    	$data['users'] = $this->distributor_model->GetDistributorsbystatemakers($condition); 
    	$this->load->view('distributors',$data);

	}

    public function add(){
        $data['title'] = "Add Distributor";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = ucwords($this->input->post('name')); 
            $gst = $this->input->post('gst'); 
            $mobile = $this->input->post('mobile'); 
            $address = ucwords($this->input->post('address')); 
            $state = $this->input->post('state');   
            $city = $this->input->post('city');   
            $zipcode = $this->input->post('zipcode');   
            $supplier = implode(',', $this->input->post('supplier')); 
            $email = strtolower($this->input->post('email'));    


            $this->form_validation->set_rules('name', 'Party Name','required');
            $this->form_validation->set_rules('gst', 'GST Number','required|is_unique[vendors.gst_no]');
            $this->form_validation->set_rules('mobile', 'Mobile','required');
            $this->form_validation->set_rules('address', 'Address','required');
            $this->form_validation->set_rules('state', 'State','required');
            $this->form_validation->set_rules('city', 'City','required'); 
            $this->form_validation->set_rules('email', 'Email','required'); 
            $this->form_validation->set_rules('supplier', 'Supplier','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                
            	$condition = array('gst_no' =>$gst);
            	$added = $this->distributor_model->GetUserbyId($condition);
            	if(count($added))
            	{
            		$this->session->set_flashdata('err_msg','Already added');
            	}
	            else 
	            {
	                $insertdata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'gst_no' =>$gst,'vendor_id' =>$supplier,'zipcode' =>$zipcode,'maker_id' => $admin_id,'email' => $email); 
	                $result = $this->distributor_model->AddDistributor($insertdata);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Distributor added successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	            }
                redirect('distributors');
            }
        } 
        $data['states'] = $this->distributor_model->GetStates();  
        $data['suppliers'] = $this->distributor_model->GetSuppliersbystate(); 
        $this->load->view('distributor_add',$data);
    }

    public function edit_distributor(){  
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        $data['title'] = "Update Distributor";
        $distributor_id =  base64_decode($this->uri->segment(3));
        $data['vendor'] = $this->distributor_model->GetDistributorbyId($distributor_id); 
        //echo "<pre>"; print_r($data['vendor']); die;
        if($distributor_id)
        {
            $condition = array('id' => $distributor_id);
            if(!empty($_POST))
            { 
                $name = ucwords($this->input->post('name')); 
                $gst = $this->input->post('gst'); 
                $mobile = $this->input->post('mobile'); 
                $address = ucwords($this->input->post('address')); 
                $state = $this->input->post('state');   
                $city = $this->input->post('city');   
                $zipcode = $this->input->post('zipcode');   
                $supplier = implode(',', $this->input->post('supplier')); 
                $email = strtolower($this->input->post('email'));  

                $this->form_validation->set_rules('name', 'Party Name','required');
                $this->form_validation->set_rules('gst', 'GST Number','required');
                $this->form_validation->set_rules('mobile', 'Mobile','required');
                $this->form_validation->set_rules('address', 'Address','required');
                $this->form_validation->set_rules('state', 'State','required');
                $this->form_validation->set_rules('city', 'City','required'); 
                $this->form_validation->set_rules('email', 'Email','required'); 
                $this->form_validation->set_rules('supplier', 'Supplier','required');

                if ($this->form_validation->run() == false) {
                }
                else { 
                    $updatedata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'gst_no' =>$gst,'vendor_id' =>$supplier,'zipcode' =>$zipcode,'email' =>$email);
                    $result = $this->distributor_model->UpdateDistributor($updatedata,$condition);
                    if($result)
                        $this->session->set_flashdata('suc_msg','Distributor updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    redirect('distributors');
                }
            }  
        }
        else
        {
            redirect('distributors');
        }
        $data['states'] = $this->distributor_model->GetStates();  
        //$state_id = $data['vendor']['state_id'];
        //$data['vendors'] = $this->vendor_model->GetUsersByState($state_id);        
        $data['vendors'] = $this->distributor_model->GetSuppliersbystate($condition); 
        $this->load->view('distributor_edit',$data);
    } 

    public function getvendors(){   
        $state_id = $_POST['state_id'];
        $condition = array('state_id' => $state_id);
        $cities = $this->vendor_model->GetUsersByState($state_id);
        $res = "<option value=''>Select Supplier</option>";
        if($cities)
        {
            foreach ($cities as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].' - '.$value['city_name'].'</option>';
            }
        }
        echo $res; die;
    }

    public function getcity(){   
        $state_id = $_POST['state_id'];
        $condition = array('state_id' => $state_id);
        $cities = $this->distributor_model->GetCity($condition);
        $res = "<option value=''>Select City</option>";
        if($cities)
        {
            foreach ($cities as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        }
        echo $res; die;
    }

    public function delete_distributor(){
        $data['title'] = "Delete Distributor";
        $vendor_id = base64_decode($this->uri->segment(3));
        if($vendor_id)
        {
            $condition = array('id' =>$vendor_id);
            $result = $this->distributor_model->DeleteDistributor($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Distributor deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('distributors');  
    }
}