<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trendreport extends CI_Controller {

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
        $this->load->model(array('purchase/purchase_model','purchase/purchase_brand_model','purchase/purchase_vendor_model','purchase/purchase_category_model','purchase/purchase_broker_model','admin_model'));      
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();          
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
    } 

    public function index(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Trend Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search_purchase_report_data']); die;
        $data["links"] = '';
        $data['inventories'] = array();
        if(!empty($_POST) || isset($_SESSION['search_purchase_trend_report_data']))
        //if(!empty($_POST))
        { 

            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search_purchase_trend_report_data'] = $_POST;
            else
                $_POST = $_SESSION['search_purchase_trend_report_data']; 

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 
            $product_id = $_POST['product']; 
            $config = array();
            $config["base_url"] = base_url() . "purchase/purchase/inventoryreport/";
            $condition = array(); 
            if(isset($_POST['party']) && !empty($_POST['party']))
                $condition['purchase_order.party_id'] = $_POST['party'];
            if(isset($_POST['category']) && !empty($_POST['category']))
                $condition['purchase_order.category_id'] = $_POST['category'];
			else
				$_POST['category'] = $condition['purchase_order.category_id'] = 1;
            if(isset($_POST['product']) && !empty($_POST['product']))
                $condition['purchase_order.product_id'] = $_POST['product'];
            if(isset($_POST['employee']) && !empty($_POST['employee']))
                $condition['purchase_order.admin_id'] = $_POST['employee'];
            if(isset($_POST['broker']) && !empty($_POST['broker']))
                $condition['purchase_order.broker_id'] = $_POST['broker']; 

            if(isset($_POST['booking_date_from']) && !empty($_POST['booking_date_from']))
                $condition['booking_date_from'] = date('Y-m-d',strtotime($_POST['booking_date_from'])); 
            if(isset($_POST['booking_date_to']) && !empty($_POST['booking_date_to']))
                $condition['booking_date_to'] = date('Y-m-d',strtotime($_POST['booking_date_to'])); 


            if(isset($_POST['erp_sr_no']) && !empty($_POST['erp_sr_no']))
                $condition['pur_inventory.erp_sr_no'] = $_POST['erp_sr_no']; 
            if(isset($_POST['code']) && !empty($_POST['code']))
                $condition['pur_inventory.code'] = $_POST['code']; 
            //echo "<pre>"; print_r($_POST); die;

            $total_rows =  $this->purchase_model->CountInventoryTrendReportList($condition);
            $config["total_rows"] = $total_rows;
            // Number of items you intend to show per page.
            $config["per_page"] = $limit;
            // Use pagination number for anchor URL.
            $config['use_page_numbers'] = TRUE;
            //Set that how many number of pages you want to view.
            $config['num_links'] = 2;
            $config['uri_segment'] = 4; 
            /*$config["per_page"] = $limit;
            $config['use_page_numbers'] = TRUE; */
            $this->pagination->initialize($config); 
            if ($this->uri->segment(4)) {
                $page = ($this->uri->segment(4));
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
            $inventories = $this->purchase_model->GetInventoryTrendReportList($condition,$limit,$page);  
            $inventories_data = array();
            if($inventories)
            {
                foreach ($inventories as $key => $value) {
                    /*if(array_key_exists($value['party_id'], $inventories_data))
                       $inventories_data[$value['party_id']]  = $value;
                    else
                        $inventories_data[$value['party_id']]  = $value; */

                    $inventories_data[$value['supplier_name']][]  = $value; 

                } 
            }
            $data['inventories'] = $inventories_data;
            //echo "<pre>"; print_r($data['inventories']); die;

        }         
        ///echo "<pre>"; print_r($data['inventories']); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers();  
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->purchase_vendor_model->GetUsersByState($states_ids);  
        $condition = array('pur_category.status' => 1)      ;

        $data['categories'] = $this->purchase_category_model->GetCategories($condition); 
        //echo "<pre>"; print_r($data); die;
        $data['booking_date_from'] = $booking_date_from;
        $data['booking_date_to'] = $booking_date_to;

        $data['party_id'] = $party_id;
        $data['booking_status'] = $booking_status; 
        $data['category_id'] = $category_id;
        $data['product_id'] = $product_id;  
        $data['logged_in_id'] = $admin_id;
        $data['logged_role'] = $role;
        $data['employees'] = array();
        if($role==4 || $role==5)
        { 
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers();
        }
        if($role==2)
        {
            $condition = array('team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers($condition);
        }
        //echo "<pre>"; print_r($data); die;
        $this->load->view('purchase/purchase/trend_report',$data);
    }

    public function seed(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Seed Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search_purchase_report_data']); die;
        $data["links"] = '';
        $data['inventories'] = array();
        if(!empty($_POST) || isset($_SESSION['search_purchase_seed_report_data']))
        //if(!empty($_POST))
        { 

            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search_purchase_seed_report_data'] = $_POST;
            else
                $_POST = $_SESSION['search_purchase_seed_report_data']; 

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            }  
            $config = array();
            $config["base_url"] = base_url() . "purchase/purchase/inventoryreport/";
            $condition = array(); 
            if(isset($_POST['party']) && !empty($_POST['party']))
                $condition['purchase_order.party_id'] = $_POST['party'];
            

            if(isset($_POST['booking_date_from']) && !empty($_POST['booking_date_from']))
                $condition['booking_date_from'] = date('Y-m-d',strtotime($_POST['booking_date_from'])); 
            if(isset($_POST['booking_date_to']) && !empty($_POST['booking_date_to']))
                $condition['booking_date_to'] = date('Y-m-d',strtotime($_POST['booking_date_to'])); 
 
 
            $inventories = $this->purchase_model->GetInventorySeedOilReportList($condition);  
            $inventories_data = array();
            if($inventories)
            {
                foreach ($inventories as $key => $value) {
                    /*if(array_key_exists($value['party_id'], $inventories_data))
                       $inventories_data[$value['party_id']]  = $value;
                    else
                        $inventories_data[$value['party_id']]  = $value; */

                    $inventories_data[$value['supplier_name']][]  = $value; 

                } 
            }
            $data['inventories'] = $inventories_data;
            //echo "<pre>"; print_r($data['inventories']); die;

        }         
        ///echo "<pre>"; print_r($data['inventories']); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers();  
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->purchase_vendor_model->GetUsersByState($states_ids);  
        $condition = array('pur_category.status' => 1)      ;

        $data['categories'] = $this->purchase_category_model->GetCategories($condition); 
        //echo "<pre>"; print_r($data); die;
        $data['booking_date_from'] = $booking_date_from;
        $data['booking_date_to'] = $booking_date_to;

        $data['party_id'] = $party_id;
        $data['booking_status'] = $booking_status; 
        $data['category_id'] = $category_id;
        $data['product_id'] = $product_id;  
        $data['logged_in_id'] = $admin_id;
        $data['logged_role'] = $role;
        $data['employees'] = array();
        if($role==4 || $role==5)
        { 
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers();
        }
        if($role==2)
        {
            $condition = array('team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers($condition);
        }
        //echo "<pre>"; print_r($data); die;
        $this->load->view('purchase/purchase/seed_report',$data);
    }
}