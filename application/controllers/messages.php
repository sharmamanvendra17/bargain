<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends CI_Controller {

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
        $this->load->model(array('message_model','vendor_model','admin_model','booking_model'));         
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();          
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
    } 
    public function index(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Whatsapp Meaasges";  
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = ''; 
        //echo "<pre>"; print_r($_SESSION['search_purchase_report_data']); die;
        $data["links"] = '';
        $data['messages'] = array();
        if(!empty($_POST) || isset($_SESSION['search_message_data']))
        //if(!empty($_POST))
        { 

            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search_message_data'] = $_POST;
            else
                $_POST = $_SESSION['search_message_data']; 

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            }  
            $config = array();
            $config["base_url"] = base_url() . "messages/index/";
            $condition = array();  
            if(isset($_POST['party']) && !empty($_POST['party']))
                $condition['whatsapp_messages.mobile_number'] = $_POST['party'];
            if(isset($_POST['employee']) && !empty( trim($_POST['employee'])))
                $condition['whatsapp_messages.mobile_number'] = trim($_POST['employee']);
            if(isset($_POST['date_from']) && !empty($_POST['date_from']))
                $condition['DATE(whatsapp_messages.receiving_time) >= '] =date('Y-m-d', strtotime($_POST['date_from']));
            if(isset($_POST['date_to']) && !empty($_POST['date_to']))
                $condition['DATE(whatsapp_messages.receiving_time) <='] = date('Y-m-d', strtotime($_POST['date_to']));

            if( (isset($_POST['party']) && !empty($_POST['party'])) && (isset($_POST['employee']) && !empty( trim($_POST['employee']))) )
            {
                $condition['both_filter'] = 1;
                $condition['party_mobile_number'] = $_POST['party'];
            }
            if(isset($_POST['channel']) && !empty($_POST['channel']))
                $condition['whatsapp_messages.sender_number'] = trim($_POST['channel']);

            $total_rows =  $this->message_model->CountMessages($condition);
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
            $data['messages'] = $this->message_model->GetMessages($condition,$limit,$page); 
        }       
        $data['parties'] = $this->vendor_model->GetUsers();
        $data['employess'] = $this->admin_model->GetAdmin();
        //echo "<pre>"; print_r($data); die;
        $this->load->view('messages_list',$data);
    }

    public function view($id){   
        $condition = array('whatsapp_messages.id' => $id);
        $response = $this->message_model->GetMessageInfo($condition);
        if($response)
        {
            if($response['recieved_file'])
            {
                $media_type = $response['media_type'];
                $content_type =  $response['content_type'];
                $recieved_file =  $response['recieved_file'];
                $caption =  $response['caption'];
                if($response['media_type']=='image')
                    echo ' <img src="data:'.$content_type.';base64, '.$recieved_file.'" alt="'.$caption.'" />';
                if($response['media_type']=='document' || $response['media_type']=='video')
                echo '<iframe src="data:'.$content_type.';base64,'.$recieved_file.'" height="100%" width="100%"></iframe>';
            }
        }
    }
}