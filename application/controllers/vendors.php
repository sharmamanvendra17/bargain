<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendors extends CI_Controller {

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
        $this->load->model('vendor_model');  
        $this->load->model('admin_model');   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();                  

    }

    public function index(){ 
    	$data['title'] = "Vendors";
    	$data['users'] = $this->vendor_model->GetUsers(); 
    	$this->load->view('vendors',$data);

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
            $employee = $this->input->post('employee');   
            $for_rate = $this->input->post('for_rate');
            $email = $this->input->post('email');
            $bank_details = $this->input->post('bank_details');
            $other_info = $this->input->post('other_info');
            $invoice_prefix = $this->input->post('invoice_prefix');
            $this->form_validation->set_rules('name', 'Party Name','required');
            $this->form_validation->set_rules('gst', 'GST Number','required|is_unique[vendors.gst_no]');
            $this->form_validation->set_rules('mobile', 'Mobile','required');
            $this->form_validation->set_rules('address', 'Address','required');
            $this->form_validation->set_rules('state', 'State','required');
            $this->form_validation->set_rules('city', 'City','required'); 
            $this->form_validation->set_rules('email', 'Email','required'); 
            $this->form_validation->set_rules('employee', 'Employee','required');


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
	                $insertdata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'gst_no' =>$gst,'employee_id' =>$employee,'zipcode' =>$zipcode,'for_rate' =>$for_rate,'email' =>$email,'bank_details' =>$bank_details,'invoice_prefix' =>$invoice_prefix,'other_info' =>$other_info);
                    $insertdata['cnf'] = 0;
                    if(isset($_POST['cnf']))
                        $insertdata['cnf'] = 1;

                    $insertdata['tax_included'] = 0;
                    if(isset($_POST['tax_included']))
                        $insertdata['tax_included'] = 1;

                    $insertdata['freight_included'] = 0;
                    if(isset($_POST['freight_included']))
                        $insertdata['freight_included'] = 1;

	                $result = $this->vendor_model->AddVendor($insertdata);
	                if($result)
	                    $this->session->set_flashdata('suc_msg','Vendor added successfully.');  
	                else
	                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
	            }


                redirect('vendors');




            }
        } 
        $data['states'] = $this->vendor_model->GetStates();
        //$data['employees1'] = $this->vendor_model->GetMakers();
        $data['employees'] = $this->admin_model->GetAllMakers(); 
        $this->load->view('vendor_add',$data);
    }

    public function edit_vendor(){  
        $data['title'] = "Update Vendor";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $logged_role = $admin_role; 
        $data['logged_role'] = $logged_role;
        $vendor_id =  base64_decode($this->uri->segment(3));
        $data['vendor'] = $this->vendor_model->GetVendorbyId($vendor_id); 

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
                $employee = $this->input->post('employee');   
                $for_rate = $this->input->post('for_rate');
                $email = $this->input->post('email');
                $bank_details = $this->input->post('bank_details');
                $invoice_prefix = $this->input->post('invoice_prefix');
                $other_info = $this->input->post('other_info');
                $this->form_validation->set_rules('name', 'Party Name','required');
                $this->form_validation->set_rules('gst', 'GST Number','required');
                $this->form_validation->set_rules('mobile', 'Mobile','required');
                $this->form_validation->set_rules('address', 'Address','required');
                $this->form_validation->set_rules('state', 'State','required');
                $this->form_validation->set_rules('city', 'City','required'); 
                $this->form_validation->set_rules('employee', 'Employee','required');
                $this->form_validation->set_rules('email', 'Email','required'); 

                if ($this->form_validation->run() == false) {
                }
                else { 
                    $updatedata = array('name' =>$name,'mobile' =>$mobile,'address' =>$address,'city_id' =>$city,'state_id' =>$state,'gst_no' =>$gst,'employee_id' =>$employee,'zipcode' =>$zipcode,'for_rate' =>$for_rate,'email' =>$email,'bank_details' =>$bank_details,'invoice_prefix' =>$invoice_prefix,'other_info' =>$other_info);
                    $updatedata['cnf'] = 0;
                    if(isset($_POST['cnf']))
                        $updatedata['cnf'] = 1;

                    $updatedata['tax_included'] = 0;
                    if(isset($_POST['tax_included']))
                        $updatedata['tax_included'] = 1;

                    $updatedata['freight_included'] = 0;
                    if(isset($_POST['freight_included']))
                        $updatedata['freight_included'] = 1;
                    
                    $result = $this->vendor_model->UpdateVendor($updatedata,$condition);
                    if($result)
                    {
                        $updatedata['updated_by'] = $admin_id;
                        $updatedata['vendor_id'] = $vendor_id;
                        $this->vendor_model->AddVendorTransaction($updatedata);
                        $this->session->set_flashdata('suc_msg','Vendor updated successfully.');  
                    }
                    else
                    {
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    }
                    redirect('vendors');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die; 
        }
        else
        {
            redirect('vendors');
        }
        $data['states'] = $this->vendor_model->GetStates();
        //$data['employees'] = $this->vendor_model->GetEmployees(); 
        $data['employees'] = $this->admin_model->GetAllMakers(); 
        $this->load->view('vendor_edit',$data);
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
        $vendor_id = base64_decode($this->uri->segment(3));
        if($vendor_id)
        {
            $condition = array('id' =>$vendor_id);
            $result = $this->vendor_model->DeleteVendor($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Vendor deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('vendors');  
    }


    public function updateStatus() {
        $id = $this->input->post('id');         
        $status = $this->input->post('status'); 
        if (empty($id)) {
            echo '0';
            die;
        }
        if ($this->vendor_model->updateStatus($id, $status)) {
            echo '1';
        } else {
            echo '0';
            } 
    } 


    public function get_vendorForinfo(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');
        //echo "<pre>"  ; print_r($_POST); die;
        $party_id = $_POST['party_id']; 
        $condition = array('id' => $party_id);
        $info = $this->vendor_model->GetUserbyId($condition); 
        $for_info = 0;
        if($info)
        {
            
            $for_info = $info['for_rate'].'_'.$info['tax_included'].'_'.$info['freight_included'];
             
        }
        echo $for_info;
    }



    public function logs($venodr_id){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $vendor_id = base64_decode($venodr_id);
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Vendor Log"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];          

        $limit = 20000;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $condition = array('vendors_transactions.vendor_id' => $vendor_id);
        $config = array();
        $config["base_url"] = base_url() . "vendors/logs/".$venodr_id."/";
        $total_rows =  $this->vendor_model->CountUsersTransactions($condition);
        $config["total_rows"] = $total_rows;
        // Number of items you intend to show per page.
        $config["per_page"] = $limit;
        // Use pagination number for anchor URL.
        $config['use_page_numbers'] = TRUE;
        //Set that how many number of pages you want to view.
        $config['num_links'] = 2;
        /*$config['uri_segment'] = 4; 
        $config["per_page"] = $limit;
        $config['use_page_numbers'] = TRUE; */
        $this->pagination->initialize($config);
        if ($this->uri->segment(4)) {
            $page = ($this->uri->segment(4));
        } else {
            $page = 1;
        }
        $data["links"] = $this->pagination->create_links();
        $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
        $data["total_page_count"] = ceil($config["total_rows"] / $limit);
        $page_no = ceil($config["total_rows"] / $limit);
        $data['total_page_no'] = $page_no;
        $data['current_page_no'] = $page;
        $data['limit'] = $limit;



        $data['users'] = $this->vendor_model->GetUsersTransactions($condition,$limit,$page);  
        //echo "<pre>"; print_r($data); die;
        $this->load->view('vendor_transactions',$data);

    }
}