<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brokers extends CI_Controller {

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
        $this->load->model('broker_model'); 
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();              

    }

    public function index(){ 
    	$data['title'] = "Brokers";
    	$data['brokers'] = $this->broker_model->GetBrokers(); 
    	$this->load->view('brokers',$data);

	}

    public function add(){
        $data['title'] = "Broker Add";
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $name = $this->input->post('name'); 
            $pan = $this->input->post('pan'); 
            $mobile = $this->input->post('mobile'); 
            $address = $this->input->post('address'); 
            $state = $this->input->post('state');   
            $city = $this->input->post('city');   
            $zipcode = $this->input->post('zipcode');      


            $this->form_validation->set_rules('name', 'Party Name','required');
            $this->form_validation->set_rules('pan', 'Pan Number','required|is_unique[brokers.pan_card]');
            $this->form_validation->set_rules('mobile', 'Mobile','required');
            $this->form_validation->set_rules('address', 'Address','required');
            $this->form_validation->set_rules('state', 'State','required');
            $this->form_validation->set_rules('city', 'City','required'); 


            if ($this->form_validation->run() == false) {
            }
            else { 
                
            	$condition = array('pan_card' =>$pan);

            	$added = $this->broker_model->GetUserbyId($condition);

            	if(count($added))
            	{
            		$this->session->set_flashdata('err_msg','Already added');
            	}
	            else 
	            {
	                $insertdata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'pan_card' =>$pan,'zipcode' =>$zipcode);
	                $result = $this->broker_model->AddBroker($insertdata);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Broker added successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	            }


                redirect('brokers');




            }
        } 
        $data['states'] = $this->broker_model->GetStates();
        $this->load->view('broker_add',$data);
    }

    public function edit_broker(){   
        $data['title'] = "Broker Product";
        $vendor_id =  base64_decode($this->uri->segment(3));
        $data['vendor'] = $this->broker_model->GetBrokerbyId($vendor_id); 

        if($vendor_id)
        {
            $condition = array('id' => $vendor_id);
            if(!empty($_POST))
            { 
                $name = $this->input->post('name'); 
                $pan = $this->input->post('pan'); 
                $mobile = $this->input->post('mobile'); 
                $address = $this->input->post('address'); 
                $state = $this->input->post('state');   
                $city = $this->input->post('city');   
                $zipcode = $this->input->post('zipcode');      


                $this->form_validation->set_rules('name', 'Party Name','required');
                $this->form_validation->set_rules('pan', 'PAN Number','required');
                $this->form_validation->set_rules('mobile', 'Mobile','required');
                $this->form_validation->set_rules('address', 'Address','required');
                $this->form_validation->set_rules('state', 'State','required');
                $this->form_validation->set_rules('city', 'City','required');  

                if ($this->form_validation->run() == false) {
                }
                else { 
                    $updatedata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'pan_card' =>$pan,'zipcode' =>$zipcode);
                    $result = $this->broker_model->UpdateBroker($updatedata,$condition);
                    if($result)
                        $this->session->set_flashdata('suc_msg','Broker updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    redirect('brokers');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die; 
        }
        else
        {
            redirect('brokers');
        }
        $data['states'] = $this->broker_model->GetStates(); 
        $this->load->view('broker_edit',$data);
    } 


    public function getcity(){   
        $state_id = $_POST['state_id'];
        $condition = array('state_id' => $state_id);
        $cities = $this->broker_model->GetCity($condition);
        $res = "";
        if($cities)
        {
            foreach ($cities as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        }
        echo $res; die;
    }

    public function delete_broker(){
        $data['title'] = "Delete Vendor";
        $vendor_id = base64_decode($this->uri->segment(3));
        if($vendor_id)
        {
            $condition = array('id' =>$vendor_id);
            $result = $this->broker_model->DeleteBroker($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Broker deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('brokers');  
    }
}