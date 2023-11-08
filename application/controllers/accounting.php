<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends CI_Controller {

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
        $this->load->model('booking_model');  
        $this->load->model('secondarybooking_model');  
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    } 

    public function getdispatchlist($part_id,$product_id,$available_stock){ 

        $condition = array('pi_sku_history.party_id' => $part_id,'pi_sku_history.product_id ' => $product_id);
        $results =  $this->booking_model->getdispatchlist($part_id,$product_id);  
        //echo "<pre>"; print_r($results);
        $response ="<table class='table table-striped table-bordered table-hover'> <tr><td>S.No.</td><td>Qty</td><td>Dispatch</td></tr>";
        if($results)
        {
            $i =1;
            foreach ($results as $key => $value) {
                if($available_stock>0)
                {
                    
                    if($available_stock>$value['quantity'])
                        $qty = $value['quantity'];
                    else
                         $qty = $available_stock;
                     $available_stock = $available_stock-$value['quantity'];
					 if($value['dispatch_date']){
                    	$response .="<tr><td>".$i."</td><td>".$qty."</td><td>".date('d-m-Y', strtotime($value['dispatch_date']))." [ ".$value['days']." Days ]"."</td></tr>";
					 }else{
						$response .= "<tr><td colspan=3>Dispatch Pending.</td></tr>";
					 }
                    $i++;
                }
                else
                {
                    break;
                }
            }

        }
        $response .="<table>";
        echo $response; die;
    }

    public function party_list(){  
        $party_type = trim($_POST['party_type']);
        $condition = array('vendors.cnf' => $party_type); 
        $vendors = $this->vendor_model->GetCnfVendor($condition);  
        $response = "<option value=''>Select Vendor</option>";
        if($vendors)
        {
            foreach ($vendors as $key => $value) {
                $response .= "<option value='".$value['id']."'>".$value['name']." - ".$value['city_name']."</option>";
            }
        }
        echo $response;
    }

    public function add_accounts_history($party,$month,$status){ 
        $party_id = $party;  
        //echo $status; die;

        $month_year = $month;
        $month_array = explode('-', $month_year);
        $post_month = $month_array[1];
        $post_year = $month_array[0];
        $month2 = $post_month-1;
        $year2 = $post_year;
        $post_month = $month2;
        if($month2==0)
        {
            $post_month = 12;
            $previous_month= 12;
            $post_year =$post_year-1;
        } 
        $stock_date  = $post_year."-".sprintf("%02d", $post_month);
        //echo $this->booking_model->check_sales_history_stock_1($party,$stock_date);

        if($this->booking_model->check_sales_history_stock_1($party,$stock_date)==0)
        {
            $insertdata = array();

            $month_year = $month;

            $month_array = explode('-', $month_year);

            $post_month = $month_array[1];
            $post_year = $month_array[0];

            $month2 = $post_month-1;
            $year2 = $post_year;
            $previous_month = $month2;
            $previous_year = $year2;


            $last_date_mnoth  = cal_days_in_month(CAL_GREGORIAN, $post_month, $post_year);
            $stock_date  = $post_year."-".$post_month."-".$last_date_mnoth;
            if($month2==0)
            {
                $previous_month= 12;
                $previous_year =$previous_year-1;
            } 
            $previous_month = $previous_year.'-'.sprintf("%02d", $previous_month);

            $previous_month_stock    = $this->booking_model->past_month_stock($party,$previous_month); 

            $closing_qty  =0;
            $closing_weight = 0;
            $closing_amount = 0;
            



            $previous_month_buy = $this->booking_model->past_month_buy($party,$month);
            $previous_month_sale = $this->booking_model->past_month_sale($party,$month);

          




            $insertdata = array();
            $i = 0;
            //echo "<pre>"; print_r($previous_month_sale); die;

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

                    $key_exists = array_search($value['product_id'], array_column($previous_month_buy, 'product_id')); 
                    $buy_qty = 0;
                    $sale_qty = 0;

                    $buy_weight = 0;
                    $sale_weight = 0;
                    
                    $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id')); 

                    $month_buy = $value['closing_qty'];
                    $month_buy_weight = $value['closing_weight'];
                    $month_bargain_amount = $value['closing_amount'];
                    $match = 0;
                    if($key_exists===0 || $key_exists)
                    {
                        $match = 1;
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $month_buy = $month_buy+$previous_month_buy[$key_exists]['purchased_qty'];
                        $month_buy_weight = $month_buy_weight+$previous_month_buy[$key_exists]['purchased_weight'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['closing_qty'] = $month_buy;

                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $month_buy_weight; 


                        $insertdata[$i]['stock_date'] = $stock_date;
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
                        $match = 1;
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['stock_date'] = $stock_date;
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
                    if($match==0)
                    {
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['stock_date'] = $stock_date;
                        $insertdata[$i]['closing_qty'] = $opening_qty;
                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = 0;
                        $sale_weight = 0;
                        $insertdata[$i]['buy_weight'] = $buy_weight;
                        $insertdata[$i]['sale_weight'] = $sale_weight;

                        $secondary_amount =0;

                        $insertdata[$i]['bargain_amount'] = $bargain_amount;
                        $insertdata[$i]['secondary_amount'] = $secondary_amount; 

                        //$month_buy_weight = 0;
                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $opening_weight; 

                        $month_bargain_amount = $month_bargain_amount-0;

                        $insertdata[$i]['opening_amount'] = $opening_amount; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount; 
                    }
                    $i++;
                }
                $closing_qty  =0;
                $closing_weight = 0;
                $closing_amount = 0;
                $opening_weight = 0;
                if($previous_month_buy)
                {
                    foreach ($previous_month_buy as $key => $value) { 

                        $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id')); 

                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = 0;
                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
                        $buy_qty = $value['purchased_qty'];
                        $buy_weight = $value['purchased_weight'];
                        $bargain_amount = $value['purchased_amount'];
                        if($sales_key_exists===0 || $sales_key_exists)
                        {
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


                        $insertdata[$i]['opening_amount'] = 0; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount; 

                        $i++;
                    }
                }
            }
            else
            { 
                $opening_qty  =0;
                $closing_qty  =0;
                $closing_weight = 0;
                $closing_amount = 0;
                $opening_weight = 0;
                $opening_amount = 0;
                if($previous_month_buy)
                {
                    foreach ($previous_month_buy as $key => $value) { 
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id']; 
                        $insertdata[$i]['stock_date'] = $stock_date;
                        $buy_qty = $value['purchased_qty'];
                        $buy_weight = $value['purchased_weight'];
                        $bargain_amount = $value['purchased_amount'];
                        $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id'));
                        if($sales_key_exists===0 || $sales_key_exists) 
                        {
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
                            $month_buy_weight = $value['purchased_weight'];
                            $month_bargain_amount  =  $value['purchased_amount'];
                            $sale_qty = 0;
                            $sale_weight =  0;
                            $secondary_amount = 0;
                        }
                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = $sale_qty;
                        $insertdata[$i]['opening_qty'] = $opening_qty;

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
            //\\echo "<pre>"; print_r($insertdata);die; 
            if($insertdata && $status=='sku')
                $this->booking_model->AddStock($insertdata);
            //echo "<pre>"; print_r($insertdata);die; 
        }  
    }
    public function index(){  
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
            //echo "<pre>"; print_r($_POST); die;

            $current_month = date('m');
            $current_year = date('Y');

            $vendor_id = $_POST['vendor'];
            $month = $_POST['year'].'-'.$_POST['month'];
            $condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$month.'%');   
            if($current_month!=$_POST['month'] || $current_year!=$_POST['year'])
            {    
                if($this->booking_model->check_sales_history_month($vendor_id,$month)==0)
                { 

                    $this->add_accounts_history($vendor_id,$month,$_POST['status']);
                } 
                //echo "string"; die;
                //echo $this->booking_model->check_sales_history_month($vendor_id,$month); die;
                if($this->booking_model->check_sales_history_month($vendor_id,$month))
                {  
                    if($_POST['status']=='amount')
                        $data['skulists'] = $this->booking_model->Getcnfaccountinghistoryamount($condition); 
                    else
                        $data['skulists'] = $this->booking_model->Getcnfaccountinghistory($condition); 
                }
                else
                {   
                    $data['skulists'] = $this->getpastdata($vendor_id,$_POST['status'],$_POST['year'],$_POST['month']);
                    //echo "<pre>"; print_r( $data['skulists']); die;
                }
            }
            else
            {   
                if($current_month<$_POST['month'] && $current_year==$_POST['year'])
                {
                    $data['skulists'] = array();
                } 
                else
                {
                    if($this->booking_model->check_sales_history_month($vendor_id,$month)==0)
                    {
                        $this->add_accounts_history($vendor_id,$month,$_POST['status']);
                    }
                    //echo "string"; die;
                    $data['skulists'] = $this->getcurrentdata($vendor_id,$_POST['status'],$_POST['year'],$_POST['month']);
                }

                //echo "<pre>"; print_r($data['skulists']); die;
                //$data['bargains'] = $this->booking_model->current_month_buy($vendor_id); 
                //$data['secondary'] = $this->booking_model->current_month_sale($vendor_id); 

            }  

            //$condition = array('party_id' => $vendor_id,'created_at like' => '%'.$month.'%');  
           // $data['bargains'] = $this->booking_model->GetBargainhistory($condition); 
            //$data['secondary'] = $this->booking_model->GetSecondaryHistory($condition); 



            /*$condition = array('pi_history.status' => 0,'pi_history.party_id' => $vendor_id,'pi_history.created_at like' => '%'.$month.'%');  
            $data['primary_pi'] = $this->booking_model->GetPrimaryPi($condition); 
            $condition = array('pi_history_secondary_booking.status' => 0,'pi_history_secondary_booking.party_id' => $vendor_id,'pi_history_secondary_booking.created_at like' => '%'.$month.'%');              
            $data['secondary_pi'] = $this->booking_model->GetSecondaryPi($condition);  */ 
            $party_type = trim($_POST['cnf']);
            $condition = array('vendors.cnf' => $party_type); 
            $data['vendors'] = $this->vendor_model->GetCnfVendor($condition);  

        }
        else
        {
            $condition = array('vendors.cnf' => 1); 
            $data['vendors'] = $this->vendor_model->GetCnfVendor($condition);  
        }
        $this->load->view('cnf_accounting',$data);
    } 

    public function getcurrentdata($vendor_id,$status,$post_year,$post_month){  
            $party_id = $vendor_id;  
            $party= $vendor_id;  

            $month = $post_month-1;
            $year = $post_year;
            $previous_month = $month;
            $previous_year = $year;
            if($month==0)
            {
                $previous_month= 12;
                $previous_year =$year-1;
            } 
            $previous_month = $previous_year.'-'.sprintf("%02d", $previous_month);
            $previous_month_condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$previous_month.'%'); 

            $month = $post_year.'-'.sprintf("%02d", $post_month);
            $condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$month.'%');  
            if($status=='amount')
            {
                $previous_month_stock = $this->booking_model->Getcnfaccountinghistoryamount($previous_month_condition);
                //echo "<pre>"; print_r($previous_month_stock); die;
                $previous_month_buy = $this->booking_model->current_month_buy_amount($party);
                //echo "<pre>"; print_r($previous_month_buy); die;
                $previous_month_sale = $this->booking_model->current_month_sale_amount($party); 
                //echo "<pre>"; print_r($previous_month_sale); die;

            }
            else
            {
                $previous_month_stock = $this->booking_model->Getcnfaccountinghistory($previous_month_condition); 
                //echo "<pre>"; print_r($previous_month_stock); die;
                $previous_month_buy = $this->booking_model->current_month_buy($party);
                //echo "<pre>"; print_r($previous_month_buy); die;
                $previous_month_sale = $this->booking_model->current_month_sale($party); 
            }
            //echo "<pre>"; print_r($previous_month_stock); die;

            $insertdata = array();  
            



             
            //echo "<pre>"; print_r($previous_month_buy); die;

            $closing_qty  =0;
            $closing_weight = 0;
            $closing_amount = 0;



            $insertdata = array();
            $i = 0; 
            //echo "<pre>"; print_r($previous_month_stock); die;

            if($previous_month_stock)
            {
                
                $bargain_amount = 0;
                $secondary_amount = 0; 
                foreach ($previous_month_stock as $key => $value) {  
                    $bargain_amount = 0;
                $secondary_amount = 0; 
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
                    $match = 0; 
                    if($key_exists=== 0 || $key_exists)
                    {
                        
                        $match = 1;
                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['packing_items_qty'] = $value['packing_items_qty'];
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
                        $match = 1;
                        //echo "<pre>"; print_r($previous_month_sale); die;
                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['packing_items_qty'] = $value['packing_items_qty'];
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

                    if($match == 0)
                    {   

                        $bargain_amount = 0;
                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['packing_items_qty'] = $value['packing_items_qty'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
                        $insertdata[$i]['closing_qty'] = $opening_qty;
                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = 0;
                        $sale_weight = 0;
                        $insertdata[$i]['buy_weight'] = 0;
                        $insertdata[$i]['sale_weight'] = $sale_weight;

                        $secondary_amount =0;

                        $insertdata[$i]['bargain_amount'] = $bargain_amount;
                        $insertdata[$i]['secondary_amount'] = $secondary_amount; 

                        $month_buy_weight = $month_buy_weight-0;
                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $month_buy_weight; 

                        $month_bargain_amount = $month_bargain_amount-0;

                        $insertdata[$i]['opening_amount'] = $opening_amount; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount;    
                    }
                    $i++;
                }
                
                $closing_qty  =0;
                $closing_weight = 0;
                $opening_weight = 0;
                $closing_amount = 0;
                $opening_amount = 0;
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
                        $insertdata[$i]['packing_items_qty'] = $value['packing_items_qty'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = 0;
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

                        $insertdata[$i]['opening_weight'] = 0;
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
                $opening_qty = 0;
                if($previous_month_buy)
                {

                    foreach ($previous_month_buy as $key => $value) { 
                        $insertdata[$i]['brand_name'] = $value['brand_name'];
                        $insertdata[$i]['category_name'] = $value['category_name'];
                        $insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['packing_items_qty'] = $value['packing_items_qty'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
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
            //echo "<pre>"; print_r($insertdata); die;
            return $insertdata; 

    }

    public function getpastdata($vendor_id,$status,$post_year,$post_month){ 
            $party_id = $vendor_id;  
            $party= $vendor_id;  

            $month = $post_month-1;
            $year = $post_year;
            $previous_month = $month;
            $previous_year = $year;
            if($month==0)
            {
                $previous_month= 12;
                $previous_year =$year-1;
            } 

            $previous_month = $previous_year.'-'.sprintf("%02d", $previous_month);

            $previous_month_condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$previous_month.'%'); 

            $month = $post_year.'-'.sprintf("%02d", $post_month);
            $condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$month.'%'); 

            $last_date_mnoth  = cal_days_in_month(CAL_GREGORIAN, $post_month, $post_year); 
            //echo "<pre>"; print_r($condition); die;
            $stock_date  = $month."-".$last_date_mnoth;
            if($status=='amount')
            {
                $previous_month_stock = $this->booking_model->Getcnfaccountinghistoryamount($previous_month_condition);
                //echo "<pre>"; print_r($previous_month_condition); die;
                $previous_month_buy = $this->booking_model->past_month_buy_amount($party,$month);
                $previous_month_sale = $this->booking_model->past_month_sale_amount($party,$month); 

            }
            else
            {
                $previous_month_stock = $this->booking_model->Getcnfaccountinghistory($previous_month_condition); 
                $previous_month_buy = $this->booking_model->past_month_buy($party,$month);
                $previous_month_sale = $this->booking_model->past_month_sale($party,$month); 
            }
            //echo "<pre>"; print_r($previous_month_buy); die;

            $insertdata = array();  
            



             
            //echo "<pre>"; print_r($previous_month_stock); die;

            $closing_qty  =0;
            $closing_weight = 0;
            $closing_amount = 0;



            $insertdata = array();
            $insertdata1 = array();
            $i = 0; 
            //echo "<pre>"; print_r($previous_month_buy); die;

            if($previous_month_stock)
            {
                
               $bargain_amount = 0;
                $secondary_amount = 0; 
                foreach ($previous_month_stock as $key => $value) {  
                    $bargain_amount = 0;
                    $secondary_amount = 0; 
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
                    $match = 0; 
                    if($key_exists=== 0 || $key_exists)
                    {
                        $match = 1; 
                        
                        //$insertdata[$i]['brand_name'] = $value['brand_name'];
                        //$insertdata[$i]['category_name'] = $value['category_name'];
                        //$insertdata[$i]['name'] = $value['name'];
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


                        $insertdata[$i]['stock_date'] = $stock_date;
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

                        $insertdata1[$i] = $insertdata[$i];
                        $insertdata1[$i]['brand_name'] = $value['brand_name'];
                        $insertdata1[$i]['category_name'] = $value['category_name'];
                        $insertdata1[$i]['name'] = $value['name'];
                        $insertdata1[$i]['packing_items_qty'] = $value['packing_items_qty'];

                        unset($previous_month_buy[$key_exists]);
                        $previous_month_buy = array_values($previous_month_buy);
                    } 

                    if($sales_key_exists===0 || $sales_key_exists)
                    {
                        $match = 1; 
                        //echo "<pre>"; print_r($previous_month_sale); die;
                        //$insertdata[$i]['brand_name'] = $value['brand_name'];
                        //$insertdata[$i]['category_name'] = $value['category_name'];
                        //$insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['stock_date'] = $stock_date;
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

                        $insertdata1[$i] = $insertdata[$i];
                        $insertdata1[$i]['brand_name'] = $value['brand_name'];
                        $insertdata1[$i]['category_name'] = $value['category_name'];
                        $insertdata1[$i]['name'] = $value['name'];
                        $insertdata1[$i]['packing_items_qty'] = $value['packing_items_qty'];
                    }

                    if($match == 0)
                    {   

                        $bargain_amount = 0;
                        //$insertdata[$i]['brand_name'] = $value['brand_name'];
                        //$insertdata[$i]['category_name'] = $value['category_name'];
                        //$insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $opening_qty;
                        $insertdata[$i]['stock_date'] = $stock_date;
                        $insertdata[$i]['closing_qty'] = $opening_qty;
                        $insertdata[$i]['buy_qty'] = $buy_qty;
                        $insertdata[$i]['sale_qty'] = 0;
                        $sale_weight = 0;
                        $insertdata[$i]['buy_weight'] = 0;
                        $insertdata[$i]['sale_weight'] = $sale_weight;

                        $secondary_amount =0;

                        $insertdata[$i]['bargain_amount'] = $bargain_amount;
                        $insertdata[$i]['secondary_amount'] = $secondary_amount; 

                        $month_buy_weight = $month_buy_weight-0;
                        $insertdata[$i]['opening_weight'] = $opening_weight;
                        $insertdata[$i]['closing_weight'] = $month_buy_weight; 

                        $month_bargain_amount = $month_bargain_amount-0;

                        $insertdata[$i]['opening_amount'] = $opening_amount; 
                        $insertdata[$i]['closing_amount'] = $month_bargain_amount;    


                        $insertdata1[$i] = $insertdata[$i];
                        $insertdata1[$i]['brand_name'] = $value['brand_name'];
                        $insertdata1[$i]['category_name'] = $value['category_name'];
                        $insertdata1[$i]['name'] = $value['name'];
                        $insertdata1[$i]['packing_items_qty'] = $value['packing_items_qty'];
                    } 
                    $i++;
                }
                
                $closing_qty  =0;
                $closing_weight = 0;
                $opening_weight = 0;
                $closing_amount = 0;
                $opening_amount = 0;
                if($previous_month_buy)
                {

                    foreach ($previous_month_buy as $key => $value) { 

                        if($status=='amount') 
                            $sales_key_exists = array_search($value['category_id'], array_column($previous_month_sale, 'category_id')); 
                        else
                            $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id')); 

                        //$insertdata[$i]['brand_name'] = $value['brand_name'];
                        //$insertdata[$i]['category_name'] = $value['category_name'];
                        //$insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] =0;// $value['purchased_qty'];
                        $insertdata[$i]['stock_date'] =$stock_date;
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

                        $insertdata1[$i] = $insertdata[$i];
                        $insertdata1[$i]['brand_name'] = $value['brand_name'];
                        $insertdata1[$i]['category_name'] = $value['category_name'];
                        $insertdata1[$i]['name'] = $value['name'];
                        $insertdata1[$i]['packing_items_qty'] = $value['packing_items_qty'];
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
                        //$insertdata[$i]['brand_name'] = $value['brand_name'];
                        //$insertdata[$i]['category_name'] = $value['category_name'];
                        //$insertdata[$i]['name'] = $value['name'];
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $value['purchased_qty'];
                        $insertdata[$i]['stock_date'] = $stock_date;
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


                        $insertdata1[$i] = $insertdata[$i];
                        $insertdata1[$i]['brand_name'] = $value['brand_name'];
                        $insertdata1[$i]['category_name'] = $value['category_name'];
                        $insertdata1[$i]['name'] = $value['name'];
                        $insertdata1[$i]['packing_items_qty'] = $value['packing_items_qty'];
                        $i++;
                    }
                }
            }  
            //echo "<pre>"; print_r($insertdata); die;
         if($insertdata && $status=='sku')
            $this->booking_model->AddStock($insertdata);
        return $insertdata1; 
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
