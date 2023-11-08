<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasebrokers extends CI_Controller {

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
        $this->load->model('purchase/purchase_broker_model','broker_model'); 
        $this->load->model('purchase/purchase_category_model','category_model');      
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();              

    }

    public function index(){ 
    	$data['title'] = "Brokers"; 
    	$data['brokers'] = $this->broker_model->GetBrokers(); 
    	$this->load->view('purchase/broker/brokers',$data);

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
	                $broker_id = $this->broker_model->AddBroker($insertdata);
	                if($broker_id)
                    {
                        $category_ids = $_POST['category_id'];
                        $brokerage = $_POST['brokerage'];
                        $brokerage_rates = array();
                        if($category_ids)
                        {
                            foreach ($category_ids as $key => $value) {
                                $brokerage_rates[] =array(
                                    'broker_id' => $broker_id,
                                    'category_id' => $value,
                                    'brokerage_rate' => $brokerage[$key],
                                ); 
                            }
                            $broker_id = $this->broker_model->AddBrokerageRates($brokerage_rates);
                        }
	                    $this->session->set_flashdata('suc_msg','Broker added successfully.');  
                    }
	                else
                    {
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    }
	            }


                redirect('purchase/purchasebrokers');




            }
        } 
        $data['states'] = $this->broker_model->GetStates();
        $data['categories'] = $this->category_model->GetCategories();
        $this->load->view('purchase/broker/broker_add',$data);
    }

    public function edit_broker(){   
        $data['title'] = "Broker Product";
        $vendor_id =  base64_decode($this->uri->segment(4));
        $data['vendor'] = $this->broker_model->GetBrokerbyId($vendor_id); 

        if($vendor_id)
        {
            $condition = array('id' => $vendor_id);
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
                    {
                        $this->session->set_flashdata('suc_msg','Broker updated successfully.');  
                        $category_ids = $_POST['category_id'];
                        $brokerage = $_POST['brokerage'];
                        $brokerage_rates = array();
                        if($category_ids)
                        {
                            foreach ($category_ids as $key => $value) {
                                $brokerage_rates[] =array(
                                    'broker_id' => $vendor_id,
                                    'category_id' => $value,
                                    'brokerage_rate' => $brokerage[$key],
                                ); 
                            }
                            $broker_id = $this->broker_model->AddBrokerageRates($brokerage_rates);
                        }

                        $category_ids = $_POST['category_update_id'];
                        $brokerage = $_POST['brokerage_update'];
                        $brokerage_rates = array();
                        if($category_ids)
                        {
                            foreach ($category_ids as $key => $value) {
                                $brokerage_rates =array( 
                                    'brokerage_rate' => $brokerage[$key],
                                );
                                $brokerage_rates_condition =array(
                                    'broker_id' => $vendor_id,
                                    'category_id' => $value, 
                                ); 
                                $this->broker_model->UpdateBrokerageRates($brokerage_rates,$brokerage_rates_condition);
                            }
                            
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    }
                    redirect('purchase/purchasebrokers');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die; 
        }
        else
        {
            redirect('purchase/purchasebrokers');
        }
        $data['categories'] = $this->category_model->GetCategories();
        $condition = array('broker_id' => $vendor_id);
        $data['categories_rates'] = $this->broker_model->GetBrokerbrokerage_rate($condition);
        //echo "<pre>"; print_r($data['categories_rates']); die;
        $data['states'] = $this->broker_model->GetStates(); 
        $this->load->view('purchase/broker/broker_edit',$data);
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
        $vendor_id = base64_decode($this->uri->segment(4));
        if($vendor_id)
        {
            $condition = array('id' =>$vendor_id);
            $result = $this->broker_model->DeleteBroker($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Broker deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('purchase/purchasebrokers');  
    }
}