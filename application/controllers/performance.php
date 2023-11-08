<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Performance extends CI_Controller {

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
        $this->load->model('rate_model');   
        $this->load->model('vendor_model');  
        $this->load->model('admin_model');  
        $this->load->model('booking_model');  
        $this->load->model('secondarybooking_model');  
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    }  
    
    public function index(){   
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Sales Report";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;  

        
        $makers = array();
        $data['result'] = array();
        $data['logged_role'] = $logged_role;
        $postdata = array();
        $postdata_secondary = array();
        $response = array();
        $states= $this->vendor_model->GetStates(); 
        if(isset($_POST) && !empty($_POST))
        {
            if(!isset($_POST['report_type']) && $role==5)
                $_POST['report_type'] = 1;
            if(!isset($_POST['report_type']) && $role!=5)
                $_POST['report_type'] = 2;

            if(isset($_POST['temalead']) && !empty($_POST['temalead']))
            {
                $_POST['report_type'] = 1;
            }
            else
            {
              $_POST['temalead'] = '';
            }
            $_POST['year'] = date('Y', strtotime($_POST['booking_date_from']));
            $_POST['month'] = date('m', strtotime($_POST['booking_date_from']));
            $postdata = $_POST ;
            $postdata_secondary = $_POST ;
            $employee = $_POST['employee'];
            $report_type = 2;
            if($role==5)
                $report_type = 1;
            if(isset($_POST['report_type']))
                $report_type = $_POST['report_type'];
            if($logged_role==6 || $logged_role ==1)
            {
                $employee = $userid;
            } 
            if($employee)
            {
                $emp_condition1 = array("id"=> $employee) ;
                $empliyee_info = $this->admin_model->GetAddminbyId($employee);  

                if($empliyee_info['role'] ==6)
                {
                    $states= $this->vendor_model->GetStatesbyuservendorsecondary($employee); 
                }
                else
                {
                    $states= $this->vendor_model->GetStatesbyuservendor($employee); 
                }

                

                $emp_condition = array("team_lead_id"=> $employee) ;
                $sec_makers_array = $this->admin_model->GetSecondaryMakersbyadminid($emp_condition); 
                //echo "<pre>"; print_r($sec_makers_array); die;
                $secondary_makers  = $sec_makers_array['secondary_makers']; 
                $postdata_secondary['employee_sec'] = $secondary_makers;
                $postdata['employee_sec'] = $secondary_makers;
                //echo "<pre>"; print_r($postdata); die;
            }
        }
        else
        {
            $POST1['employee'] = "";
            $POST1['temalead'] = "";
            if($logged_role==1 || $logged_role== 2)
                $POST1['employee'] = $userid;
            $POST1['report_type'] = 2;
            if($role==5)
                $POST1['report_type'] = 1;
            $POST1['year'] = date('Y');
            $POST1['month'] = date('m');
            $POST1['booking_date_from'] = date('01-m-Y');
            $POST1['booking_date_to'] = date('t-m-Y'); 
            $postdata = $POST1 ;

            /*if($logged_role==6 || $logged_role ==1)
            {
                $_POST['employee'] = $userid;
                $_POST['year'] = date('Y');
                $_POST['month'] = date('m');
                $postdata = $_POST ;
                $postdata_secondary = $_POST ;
                $employee = $_POST['employee'];
                if($employee)
                {
                    $states= $this->vendor_model->GetStatesbyuser($employee);
                    $emp_condition = array("team_lead_id"=> $employee) ;
                    $sec_makers_array = $this->admin_model->GetSecondaryMakersbyadminid($emp_condition); 
                    //echo "<pre>"; print_r($sec_makers_array); die;
                    $secondary_makers  = $sec_makers_array['secondary_makers']; 
                    $postdata_secondary['employee_sec'] = $secondary_makers;
                    $postdata['employee_sec'] = $secondary_makers;
                    //echo "<pre>"; print_r($postdata); die;
                } 
            } */
        }

        //echo "dfdsfds"; print_r($states); die;
        $data['postdata'] = $postdata;
        //echo "<pre>"; print_r($postdata); die;
        if(isset($_POST) && !empty($_POST))
        {
             
            if($states)
            {
                $response = array();
                foreach ($states as $key => $value) {
                    $id = $value['id'] ; 
                    //$condition = array(" FIND_IN_SET($id, admin.state_id )");
                    $condition = array("state_id" => $value['id']);
                    $response[$key]['state'] =$value;
                    $response[$key]['makers'] = $this->admin_model->GetBookingsummary_date( $value['id'],$postdata); 
                    $condition = array("state_id" => $value['id'],'role' => 6);
                    $response[$key]['secondry_makers'] = $this->admin_model->GetSecondaryMakers_date($value['id'],$postdata_secondary); 
                }
            }
        }
        else
        { 
            $states = array();
            $response = array();
        }
        $data['states'] =$states; 
        $data['response'] =$response; 
        $data['users'] =$this->admin_model->GetAllMakersSecondary(); 
        
        $data['team_leads']   = array();
        if($role==4)
            $data['team_leads'] =$this->admin_model->Getteamleads(); 
        if($role==1 || $role==5)
            $_POST['employee'] = $userid;
        //echo "<pre>"; print_r($data); die;
        $this->load->view('performance_date',$data);
    }

    public function getcurrentdata($vendor_id,$status,$post_year,$post_month){ 
            $party_id = $vendor_id;  
            $party= $vendor_id;  

            $month = $post_month-1;
            $year = $post_year;
            if($month==0)
            {
                $previous_month= 12;
                $previous_year =$year-1;
            } 
            $previous_month = $previous_year.'-'.$previous_month;
            $previous_month_condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$previous_month.'%'); 

            $month = $post_year.'-'.$post_month;
            $condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$month.'%'); 

            if($status=='amount')
            {
                $previous_month_stock = $this->booking_model->Getcnfaccountinghistoryamount($previous_month_condition);
                $previous_month_buy = $this->booking_model->current_month_buy_amount($party);
                $previous_month_sale = $this->booking_model->current_month_sale_amount($party);  
            }
            else
            {
                $previous_month_stock = $this->booking_model->Getcnfaccountinghistory($previous_month_condition); 
                $previous_month_buy = $this->booking_model->current_month_buy($party);
                $previous_month_sale = $this->booking_model->current_month_sale($party); 
            }
            //echo "<pre>"; print_r($previous_month_buy); die;

            $insertdata = array();  
            



             
            //echo "<pre>"; print_r($previous_month_stock); die;

            $closing_qty  =0;
            $closing_weight = 0;
            $closing_amount = 0;



            $insertdata = array();
            $i = 0; 
            //echo "<pre>"; print_r($previous_month_buy); die;

            if($previous_month_stock)
            {
                
               $bargain_amount = 0;
                $secondary_amount = 0; 
                foreach ($previous_month_stock as $key => $value) {  
                    if($value)
                    { 
                        $closing_qty  = $value['closing_qty'];
                        $closing_weight  = $value['closing_weight'];
                        $closing_amount  = $value['closing_amount'];
                    }
                    $opening_qty = $closing_qty;
                    $opening_weight = $closing_weight;
                    $opening_amount = $closing_amount;

                    if($status=='amount') 
                        $key_exists = array_search($value['category_id'], array_column($previous_month_buy, 'category_id'));
                    else
                        $key_exists = array_search($value['product_id'], array_column($previous_month_buy, 'product_id')); 
                    $buy_qty = 0;
                    $sale_qty = 0;

                    $buy_weight = 0;
                    $sale_weight = 0;
                    if($status=='amount') 
                        $sales_key_exists = array_search($value['category_id'], array_column($previous_month_sale, 'category_id')); 
                    else
                        $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id')); 

                    $month_buy = $value['closing_qty'];
                    $month_buy_weight = $value['closing_weight'];
                    $month_bargain_amount = $value['closing_amount'];   
                    if($key_exists=== 0 || $key_exists)
                    {
                        
                        
                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id']; 
                        $month_buy = $month_buy+$previous_month_buy[$key_exists]['purchased_qty'];  
                        $month_buy_weight = $month_buy_weight+$previous_month_buy[$key_exists]['purchased_weight'];
                        //echo "<pre>"; print_r($previous_month_buy[$key_exists]); die;
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['closing_qty'] = $month_buy;

                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $month_buy_weight; 


                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
                        //$insertdata[$i]['opening_qty'] = $value['closing_qty']+$previous_month_buy[$key_exists]['purchased_qty']-$previous_month_buy[$key_exists]['saled_quantity'];
                        $buy_qty = $previous_month_buy[$key_exists]['purchased_qty'];
                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = $sale_qty;
                        $buy_weight = $previous_month_buy[$key_exists]['purchased_weight'];
                        $insertdata[$i]['buy_weight'] = $buy_weight;
                        $insertdata[$i]['sale_weight'] = $sale_weight;

                        $bargain_amount = $previous_month_buy[$key_exists]['purchased_amount'];
                        $month_bargain_amount = $month_bargain_amount+$previous_month_buy[$key_exists]['purchased_amount'];
                        $insertdata[$i]['bargain_amount'] = $bargain_amount;
                        $insertdata[$i]['secondary_amount'] = $secondary_amount;  

                        $insertdata[$i]['opening_amount'] = $opening_amount; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount; 


                        unset($previous_month_buy[$key_exists]);
                        $previous_month_buy = array_values($previous_month_buy);
                    } 

                    if($sales_key_exists===0 || $sales_key_exists)
                    {
                        //echo "<pre>"; print_r($previous_month_sale); die;
                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
                        $insertdata[$i]['closing_qty'] = $month_buy-$previous_month_sale[$sales_key_exists]['saled_quantity'];
                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = $previous_month_sale[$sales_key_exists]['saled_quantity'];
                        $sale_weight = $previous_month_sale[$sales_key_exists]['saled_weight'];
                        $insertdata[$i]['buy_weight'] = $buy_weight;
                        $insertdata[$i]['sale_weight'] = $sale_weight;

                        $secondary_amount =$previous_month_sale[$sales_key_exists]['saled_amount'];

                        $insertdata[$i]['bargain_amount'] = $bargain_amount;
                        $insertdata[$i]['secondary_amount'] = $secondary_amount; 

                        $month_buy_weight = $month_buy_weight-$previous_month_sale[$sales_key_exists]['saled_weight'];
                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $month_buy_weight; 

                        $month_bargain_amount = $month_bargain_amount-$previous_month_sale[$sales_key_exists]['saled_amount'];

                        $insertdata[$i]['opening_amount'] = $opening_amount; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount; 
                        
                    } 
                    $i++;
                }
                
                $closing_qty  =0;
                $closing_weight = 0;
                $opening_weight = 0;
                $closing_amount = 0;
                
                if($previous_month_buy)
                {

                    foreach ($previous_month_buy as $key => $value) { 

                        if($status=='amount') 
                            $sales_key_exists = array_search($value['category_id'], array_column($previous_month_sale, 'category_id')); 
                        else
                            $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id')); 

                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $value['purchased_qty'];
                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
                        $buy_qty = $value['purchased_qty'];
                        $buy_weight = $value['purchased_weight'];
                        $bargain_amount = $value['purchased_amount'];
                        if($sales_key_exists===0 || $sales_key_exists)
                        {
                            //echo "<pre>"; print_r($previous_month_sale); die;
                             
                            $insertdata[$i]['closing_qty'] = $value['purchased_qty']-$previous_month_sale[$sales_key_exists]['saled_quantity'];
                            $sale_qty = $previous_month_sale[$sales_key_exists]['saled_quantity'];
                            $sale_weight = $previous_month_sale[$sales_key_exists]['saled_weight'];
                            $secondary_amount = $previous_month_sale[$sales_key_exists]['saled_amount'];

                            $month_buy_weight = $value['purchased_weight']-$previous_month_sale[$sales_key_exists]['saled_weight'];
                            $month_bargain_amount =$value['purchased_amount']-$previous_month_sale[$sales_key_exists]['saled_amount'];
                        }
                        else
                        {
                            $insertdata[$i]['closing_qty'] = $value['purchased_qty'];
                            $sale_qty = 0;
                            $sale_weight = 0;
                            $secondary_amount = 0;
                            $month_buy_weight = $value['purchased_weight'];
                            $month_bargain_amount = $value['purchased_amount'];
                        }

                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = $sale_qty;

                        $insertdata[$i]['buy_weight'] = $buy_weight;
                        $insertdata[$i]['sale_weight'] = $sale_weight;


                        $insertdata[$i]['bargain_amount'] = $bargain_amount;
                        $insertdata[$i]['secondary_amount'] = $secondary_amount; 

                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $month_buy_weight; 


                        $insertdata[$i]['opening_amount'] = $opening_amount; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount;  
                        $i++;
                    }
                }
            }
            else
            {
                $closing_qty  =0;
                $closing_weight = 0;
                $opening_weight = 0;
                $closing_amount = 0;
                $opening_amount = 0;
                if($previous_month_buy)
                {
                    foreach ($previous_month_buy as $key => $value) { 
                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $value['purchased_qty'];
                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
                        $buy_qty = $value['purchased_qty'];
                        $buy_weight = $value['purchased_weight'];
                        $bargain_amount = $value['purchased_amount'];
                        if($status=='amount') 
                            $sales_key_exists = array_search($value['category_id'], array_column($previous_month_sale, 'category_id'));
                        else
                            $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id'));
                        if($sales_key_exists===0 || $sales_key_exists) 
                        {
                            $insertdata[$i]['closing_qty'] = $value['purchased_qty']-$previous_month_sale[$sales_key_exists]['saled_quantity'];

                            $month_buy_weight = $value['purchased_weight']-$previous_month_sale[$sales_key_exists]['saled_weight'];
                            $month_bargain_amount =$value['purchased_amount']-$previous_month_sale[$sales_key_exists]['saled_amount'];
                            $sale_qty = $previous_month_sale[$sales_key_exists]['saled_quantity'];
                            $sale_weight = $previous_month_sale[$sales_key_exists]['saled_weight'];
                            $secondary_amount = $previous_month_sale[$sales_key_exists]['saled_amount']; 
                        }
                        else
                        {
                            $insertdata[$i]['closing_qty'] = $value['purchased_qty'];
                            $month_buy_weight = $value['purchased_weight'];
                            $month_bargain_amount  =  $value['purchased_amount'];
                            $sale_qty = 0;
                            $sale_weight =  0;
                            $secondary_amount = 0;
                        }
                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = $sale_qty;

                        $insertdata[$i]['buy_weight'] = $buy_weight;
                        $insertdata[$i]['sale_weight'] = $sale_weight;

                        $insertdata[$i]['bargain_amount'] = $bargain_amount;
                        $insertdata[$i]['secondary_amount'] = $secondary_amount; 

                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $month_buy_weight; 

                        $insertdata[$i]['opening_amount'] = $opening_amount; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount; 

                        $i++;
                    }
                }
            }  
            return $insertdata; 

    }
    public function index1(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Accounting";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role; 
        $data['skulists'] =  array();
        if(isset($_POST) && !empty($_POST))
        {
            $vendor_id = $_POST['vendor'];
            $month = $_POST['year'].'-'.$_POST['month'];
            $condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$month.'%');  
            $data['skulists'] = $this->booking_model->Getcnfaccountinghistory($condition); 

            $condition = array('pi_history.status' => 0,'pi_history.party_id' => $vendor_id,'pi_history.created_at like' => '%'.$month.'%');  
            $data['primary_pi'] = $this->booking_model->GetPrimaryPi($condition); 
            $condition = array('pi_history_secondary_booking.status' => 0,'pi_history_secondary_booking.party_id' => $vendor_id,'pi_history_secondary_booking.created_at like' => '%'.$month.'%');              
            $data['secondary_pi'] = $this->booking_model->GetSecondaryPi($condition); 

            //echo "<pre>"; print_r($data); die;
        }

        $condition = array('vendors.cnf' => 1); 
        $data['vendors'] = $this->vendor_model->GetCnfVendor($condition);  
    	$this->load->view('cnf_accounts',$data);
	} 
}
