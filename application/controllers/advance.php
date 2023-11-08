<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Advance extends CI_Controller {
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
        $this->load->model(array('booking_model','vendor_model','admin_model','advance_model'));      
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();          
        $admin_info = $this->session->userdata('admin');  
    }

    public function index(){ 
        $data['title'] = "Advance/BG";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;     
        $data['users'] = array();
        $data['links'] = ''; 
        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        }  
        $config = array();
        $config["base_url"] = base_url() . "advance/index/";
        $condition = array();
        if(!empty($_POST) || isset($_SESSION['search_advance_data']))
        {      
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search_advance_data'] = $_POST;
            else
                $_POST = $_SESSION['search_advance_data'];  
            
            if(isset($_POST['party']) && !empty($_POST['party']))
                $condition['party_advance.party_id'] =$_POST['party'];
            if(isset($_POST['company']) && !empty($_POST['company']))
                $condition['party_advance.company_id'] =$_POST['company'];
            if(isset($_POST['verified_by']) && !empty($_POST['verified_by']))
                $condition['party_advance.verified_by'] =$_POST['verified_by'];
            if(isset($_POST['added_by']) && !empty($_POST['added_by']))
                $condition['party_advance.added_by'] =$_POST['added_by']; 
        }

        $total_rows =  $this->advance_model->CountAdvance($condition);
        $config["total_rows"] = $total_rows;
        // Number of items you intend to show per page.
        $config["per_page"] = $limit;
        // Use pagination number for anchor URL.
        $config['use_page_numbers'] = TRUE;
        //Set that how many number of pages you want to view.
        $config['num_links'] = 2;
        $config['uri_segment'] = 3; 
        /*$config["per_page"] = $limit;
        $config['use_page_numbers'] = TRUE; */
        $this->pagination->initialize($config); 
        if ($this->uri->segment(3)) {
            $page = ($this->uri->segment(3));
        } else {
            $page = 1;
        }
        $data["links"] = $this->pagination->create_links();
        //echo "<pre>"; print_r($data["links"]); die;
        $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
        $data["total_page_count"] = ceil($config["total_rows"] / $limit);
        $page_no = ceil($config["total_rows"] / $limit);
        $data['total_page_no'] = $page_no;
        $data['current_page_no'] = $page;
        $data['limit'] = $limit;
        $data['users'] = $this->advance_model->GetAdvanceList($condition,$limit,$page); 

        $condition = array('vendors.status' => 1);
        $data['parties'] = $this->vendor_model->GetUsers($condition); 
        $condition = array();
        $data['compnies'] = $this->booking_model->CompnayList($condition); 
        $condition = array('admin.status' => 1);
        $data['viewers'] = $this->admin_model->GetAllViewers($condition);

        $condition = array('admin.status' => 1,'admin.role' => '7,4');
        $data['added_by'] = $this->admin_model->allemployess($condition);
        
        $this->load->view('advance',$data);
    }



    public function add(){
        $data['title'] = "Add Advance/BG";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        if(!empty($_POST))
        {
            $this->form_validation->set_rules('party', 'Party Name','required');
            $this->form_validation->set_rules('verified_by', 'Verified By','required');
            $this->form_validation->set_rules('type', 'Type','required');
            $this->form_validation->set_rules('amount', 'Amount','required');
            $this->form_validation->set_rules('company', 'Company','required');
            if($_POST['type']=='bg')
                $this->form_validation->set_rules('expiry_date', 'expiry_date','required');  


            if ($this->form_validation->run() == false) {
            }
            else { 
                
                $added_by = $admin_id;
                $verified_by = $_POST['verified_by'];
                $party_id = $_POST['party'];
                $advance_type = $_POST['type'];
                $amount = $_POST['amount'];
                $expiry_date = date('Y-m-d',strtotime($_POST['expiry_date']));
                $company_name = $_POST['company'];

                $insertdata = array('added_by' =>$added_by,'verified_by' =>$verified_by,'party_id' =>$party_id,'advance_type' =>$advance_type,'amount' =>$amount,'expiry_date' =>$expiry_date,'company_id' =>$company_name); 
                //echo "<pre>"; print_r($insertdata); die;
                $result = $this->advance_model->AddAdvance($insertdata);
                if($result)
                {
                    $this->session->set_flashdata('suc_msg','Advance added successfully.'); 
                    $condition = array('party_advance.id' => $result); 
                    $info = $this->advance_model->GetAdvanceInfo($condition);
                    $party_name =  $info['party_name'];
                    $amount =  $info['amount']; 
                    $company_name =  $info['company_name'];
                    $verified_by_name =  $info['verified_by_name'];
                    $added_by_name =  $info['added_by_name'];
                    $created_at =  date('d-m-Y', strtotime($info['created_at']));
                    $expiry_date =  date('d-m-Y', strtotime($info['expiry_date']));                     

                    $whatsapp_message = urlencode($party_name."~".$amount."~".$company_name."~".$created_at."~".$verified_by_name."~".$added_by_name); 
                    $tid = 1026484402;
                    if($advance_type=='bg')
                    {
                        $whatsapp_message = urlencode($party_name."~".$amount."~".$expiry_date."~".$company_name."~".$verified_by_name."~".$added_by_name);  
                        $tid = 1026432103;
                    }

                   $mobile_numbars = 7792047479;
                   $curl_watsappapi = curl_init();
                    curl_setopt_array($curl_watsappapi, array( 
                    CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbars.'&TID='.$tid.'&P='.$whatsapp_message,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS =>'',
                      CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                      ),
                    ));
                    $response = curl_exec($curl_watsappapi); 
                    curl_close($curl_watsappapi); 

                }
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                 
                redirect('advance');
            }
        } 
        $condition = array('vendors.status' => 1);
        $data['parties'] = $this->vendor_model->GetUsers($condition); 
        $condition = array();
        $data['compnies'] = $this->booking_model->CompnayList($condition); 
        $condition = array('admin.status' => 1);
        $data['viewers'] = $this->admin_model->GetAllViewers($condition);
        $this->load->view('advance_add',$data);
    }
}