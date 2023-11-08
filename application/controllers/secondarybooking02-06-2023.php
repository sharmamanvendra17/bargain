<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secondarybooking extends CI_Controller {

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
        $this->load->model(array('secondarybooking_model','brand_model','vendor_model','category_model','broker_model','admin_model','distributor_model','booking_model',));      
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();          
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
    } 
    public function getcurrentdata($vendor_id) {             
        $party_id = $vendor_id;  
        $party=  $vendor_id;  
        $post_month = date('m');
        $post_year = date('Y');
        $month = $post_month-1;
        $year = $post_year;
        $previous_month= $month;
        $previous_year =$year;
        if($month==0)
        {
            $previous_month= 12;
            $previous_year =$year-1;
        } 
        //$previous_month = $previous_year.'-'.$previous_month;
        $previous_month = $previous_year.'-'.sprintf("%02d", $previous_month);
        $previous_month_condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$previous_month.'%'); 

        $month = $post_year.'-'.sprintf("%02d", $post_month);
        $condition = array('cnf_sales_history.party_id' => $vendor_id,'stock_date like' => '%'.$month.'%'); 
        $status= 'sku';
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
                    
                    
                    $insertdata[$value['product_id']]['brand_name'] = $value['brand_name'];
                    $insertdata[$value['product_id']]['category_name'] = $value['category_name'];
                    $insertdata[$value['product_id']]['name'] = $value['name'];
                    $insertdata[$value['product_id']]['party_id'] = $value['party_id'];
                    $insertdata[$value['product_id']]['brand_id'] = $value['brand_id'];
                    $insertdata[$value['product_id']]['category_id'] = $value['category_id'];
                    $insertdata[$value['product_id']]['product_id'] = $value['product_id']; 
                    $month_buy = $month_buy+$previous_month_buy[$key_exists]['purchased_qty'];  
                    $month_buy_weight = $month_buy_weight+$previous_month_buy[$key_exists]['purchased_weight'];
                    //echo "<pre>"; print_r($previous_month_buy[$key_exists]); die;
                    $insertdata[$value['product_id']]['opening_qty'] = $opening_qty;
                    $insertdata[$value['product_id']]['closing_qty'] = $month_buy;

                    $insertdata[$value['product_id']]['opening_weight'] = $opening_weight;
                    $insertdata[$value['product_id']]['closing_weight'] = $month_buy_weight; 


                    $insertdata[$value['product_id']]['stock_date'] = date("Y-m-t", strtotime("last month"));
                    //$insertdata[$value['product_id']]['opening_qty'] = $value['closing_qty']+$previous_month_buy[$key_exists]['purchased_qty']-$previous_month_buy[$key_exists]['saled_quantity'];
                    $buy_qty = $previous_month_buy[$key_exists]['purchased_qty'];
                    $insertdata[$value['product_id']]['buy_qty'] = $buy_qty;
                    $insertdata[$value['product_id']]['sale_qty'] = $sale_qty;
                    $buy_weight = $previous_month_buy[$key_exists]['purchased_weight'];
                    $insertdata[$value['product_id']]['buy_weight'] = $buy_weight;
                    $insertdata[$value['product_id']]['sale_weight'] = $sale_weight;

                    $bargain_amount = $previous_month_buy[$key_exists]['purchased_amount'];
                    $month_bargain_amount = $month_bargain_amount+$previous_month_buy[$key_exists]['purchased_amount'];
                    $insertdata[$value['product_id']]['bargain_amount'] = $bargain_amount;
                    $insertdata[$value['product_id']]['secondary_amount'] = $secondary_amount;  

                    $insertdata[$value['product_id']]['opening_amount'] = $opening_amount; 
                    $insertdata[$value['product_id']]['closing_amount'] = $month_bargain_amount; 


                    unset($previous_month_buy[$key_exists]);
                    $previous_month_buy = array_values($previous_month_buy);
                } 

                elseif($sales_key_exists===0 || $sales_key_exists)
                {
                    //echo "<pre>"; print_r($previous_month_sale); die;
                    $insertdata[$value['product_id']]['brand_name'] = $value['brand_name'];
                    $insertdata[$value['product_id']]['category_name'] = $value['category_name'];
                    $insertdata[$value['product_id']]['name'] = $value['name'];
                    $insertdata[$value['product_id']]['party_id'] = $value['party_id'];
                    $insertdata[$value['product_id']]['brand_id'] = $value['brand_id'];
                    $insertdata[$value['product_id']]['category_id'] = $value['category_id'];
                    $insertdata[$value['product_id']]['product_id'] = $value['product_id'];
                    $insertdata[$value['product_id']]['opening_qty'] = $opening_qty;
                    $insertdata[$value['product_id']]['stock_date'] = date("Y-m-t", strtotime("last month"));
                    $insertdata[$value['product_id']]['closing_qty'] = $month_buy-$previous_month_sale[$sales_key_exists]['saled_quantity'];
                    $insertdata[$value['product_id']]['buy_qty'] = $buy_qty;
                    $insertdata[$value['product_id']]['sale_qty'] = $previous_month_sale[$sales_key_exists]['saled_quantity'];
                    $sale_weight = $previous_month_sale[$sales_key_exists]['saled_weight'];
                    $insertdata[$value['product_id']]['buy_weight'] = $buy_weight;
                    $insertdata[$value['product_id']]['sale_weight'] = $sale_weight;

                    $secondary_amount =$previous_month_sale[$sales_key_exists]['saled_amount'];

                    $insertdata[$value['product_id']]['bargain_amount'] = $bargain_amount;
                    $insertdata[$value['product_id']]['secondary_amount'] = $secondary_amount; 

                    $month_buy_weight = $month_buy_weight-$previous_month_sale[$sales_key_exists]['saled_weight'];
                    $insertdata[$value['product_id']]['opening_weight'] = $opening_weight;
                    $insertdata[$value['product_id']]['closing_weight'] = $month_buy_weight; 

                    $month_bargain_amount = $month_bargain_amount-$previous_month_sale[$sales_key_exists]['saled_amount'];

                    $insertdata[$value['product_id']]['opening_amount'] = $opening_amount; 
                    $insertdata[$value['product_id']]['closing_amount'] = $month_bargain_amount; 
                    
                } 
                else
                { 
                        $insertdata[$value['product_id']]['brand_name'] = $value['brand_name'];
                        $insertdata[$value['product_id']]['category_name'] = $value['category_name'];
                        $insertdata[$value['product_id']]['name'] = $value['name'];
                        $insertdata[$value['product_id']]['party_id'] = $value['party_id'];
                        $insertdata[$value['product_id']]['brand_id'] = $value['brand_id'];
                        $insertdata[$value['product_id']]['category_id'] = $value['category_id'];
                        $insertdata[$value['product_id']]['product_id'] = $value['product_id'];
                        $insertdata[$value['product_id']]['opening_qty'] = $opening_qty;
                        $insertdata[$value['product_id']]['stock_date'] = date("Y-m-t", strtotime("last month"));
                        $insertdata[$value['product_id']]['closing_qty'] = $opening_qty;
                        $insertdata[$value['product_id']]['buy_qty'] = $buy_qty;
                        $insertdata[$value['product_id']]['sale_qty'] = 0;
                        $sale_weight = 0;
                        $insertdata[$value['product_id']]['buy_weight'] = 0;
                        $insertdata[$value['product_id']]['sale_weight'] = $sale_weight;

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
            
            if($previous_month_buy)
            {

                foreach ($previous_month_buy as $key => $value) { 

                    if($status=='amount') 
                        $sales_key_exists = array_search($value['category_id'], array_column($previous_month_sale, 'category_id')); 
                    else
                        $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id')); 

                    $insertdata[$value['product_id']]['brand_name'] = $value['brand_name'];
                    $insertdata[$value['product_id']]['category_name'] = $value['category_name'];
                    $insertdata[$value['product_id']]['name'] = $value['name'];
                    $insertdata[$value['product_id']]['party_id'] = $value['party_id'];
                    $insertdata[$value['product_id']]['brand_id'] = $value['brand_id'];
                    $insertdata[$value['product_id']]['category_id'] = $value['category_id'];
                    $insertdata[$value['product_id']]['product_id'] = $value['product_id'];
                    $insertdata[$value['product_id']]['opening_qty'] = $value['purchased_qty'];
                    $insertdata[$value['product_id']]['stock_date'] = date("Y-m-t", strtotime("last month"));
                    $buy_qty = $value['purchased_qty'];
                    $buy_weight = $value['purchased_weight'];
                    $bargain_amount = $value['purchased_amount'];
                    if($sales_key_exists===0 || $sales_key_exists)
                    {
                        //echo "<pre>"; print_r($previous_month_sale); die;
                         
                        $insertdata[$value['product_id']]['closing_qty'] = $value['purchased_qty']-$previous_month_sale[$sales_key_exists]['saled_quantity'];
                        $sale_qty = $previous_month_sale[$sales_key_exists]['saled_quantity'];
                        $sale_weight = $previous_month_sale[$sales_key_exists]['saled_weight'];
                        $secondary_amount = $previous_month_sale[$sales_key_exists]['saled_amount'];

                        $month_buy_weight = $value['purchased_weight']-$previous_month_sale[$sales_key_exists]['saled_weight'];
                        $month_bargain_amount =$value['purchased_amount']-$previous_month_sale[$sales_key_exists]['saled_amount'];
                    }
                    else
                    {
                        $insertdata[$value['product_id']]['closing_qty'] = $value['purchased_qty'];
                        $sale_qty = 0;
                        $sale_weight = 0;
                        $secondary_amount = 0;
                        $month_buy_weight = $value['purchased_weight'];
                        $month_bargain_amount = $value['purchased_amount'];
                    }

                    $insertdata[$value['product_id']]['buy_qty'] = $buy_qty;
                    $insertdata[$value['product_id']]['sale_qty'] = $sale_qty;

                    $insertdata[$value['product_id']]['buy_weight'] = $buy_weight;
                    $insertdata[$value['product_id']]['sale_weight'] = $sale_weight;


                    $insertdata[$value['product_id']]['bargain_amount'] = $bargain_amount;
                    $insertdata[$value['product_id']]['secondary_amount'] = $secondary_amount; 

                    $insertdata[$value['product_id']]['opening_weight'] = $opening_weight;
                    $insertdata[$value['product_id']]['closing_weight'] = $month_buy_weight; 


                    $insertdata[$value['product_id']]['opening_amount'] = $opening_amount; 
                    $insertdata[$value['product_id']]['closing_amount'] = $month_bargain_amount;  
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
                    $insertdata[$value['product_id']]['brand_name'] = $value['brand_name'];
                    $insertdata[$value['product_id']]['category_name'] = $value['category_name'];
                    $insertdata[$value['product_id']]['name'] = $value['name'];
                    $insertdata[$value['product_id']]['party_id'] = $value['party_id'];
                    $insertdata[$value['product_id']]['brand_id'] = $value['brand_id'];
                    $insertdata[$value['product_id']]['category_id'] = $value['category_id'];
                    $insertdata[$value['product_id']]['product_id'] = $value['product_id'];
                    $insertdata[$value['product_id']]['opening_qty'] = $value['purchased_qty'];
                    $insertdata[$value['product_id']]['stock_date'] = date("Y-m-t", strtotime("last month"));
                    $buy_qty = $value['purchased_qty'];
                    $buy_weight = $value['purchased_weight'];
                    $bargain_amount = $value['purchased_amount'];
                    if($status=='amount') 
                        $sales_key_exists = array_search($value['category_id'], array_column($previous_month_sale, 'category_id'));
                    else
                        $sales_key_exists = array_search($value['product_id'], array_column($previous_month_sale, 'product_id'));
                    if($sales_key_exists===0 || $sales_key_exists) 
                    {
                        $insertdata[$value['product_id']]['closing_qty'] = $value['purchased_qty']-$previous_month_sale[$sales_key_exists]['saled_quantity'];

                        $month_buy_weight = $value['purchased_weight']-$previous_month_sale[$sales_key_exists]['saled_weight'];
                        $month_bargain_amount =$value['purchased_amount']-$previous_month_sale[$sales_key_exists]['saled_amount'];
                        $sale_qty = $previous_month_sale[$sales_key_exists]['saled_quantity'];
                        $sale_weight = $previous_month_sale[$sales_key_exists]['saled_weight'];
                        $secondary_amount = $previous_month_sale[$sales_key_exists]['saled_amount']; 
                    }
                    else
                    {
                        $insertdata[$value['product_id']]['closing_qty'] = $value['purchased_qty'];
                        $month_buy_weight = $value['purchased_weight'];
                        $month_bargain_amount  =  $value['purchased_amount'];
                        $sale_qty = 0;
                        $sale_weight =  0;
                        $secondary_amount = 0;
                    }
                    $insertdata[$value['product_id']]['buy_qty'] = $buy_qty;
                    $insertdata[$value['product_id']]['sale_qty'] = $sale_qty;

                    $insertdata[$value['product_id']]['buy_weight'] = $buy_weight;
                    $insertdata[$value['product_id']]['sale_weight'] = $sale_weight;

                    $insertdata[$value['product_id']]['bargain_amount'] = $bargain_amount;
                    $insertdata[$value['product_id']]['secondary_amount'] = $secondary_amount; 

                    $insertdata[$value['product_id']]['opening_weight'] = $opening_weight;
                    $insertdata[$value['product_id']]['closing_weight'] = $month_buy_weight; 

                    $insertdata[$value['product_id']]['opening_amount'] = $opening_amount; 
                    $insertdata[$value['product_id']]['closing_amount'] = $month_bargain_amount; 

                    $i++;
                }
            }
        }  
        //echo "<pre>"; print_r($insertdata); die;
        return $insertdata; 
    }
    
    public function index(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Secondary Order Booking"; 

        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];   

        $condition = array('is_lock' => 1);
        if($role==1) //maker
        {
            $condition = array('admin_id' => $userid);
        }
        elseif ($role==2) { //checker
            $condition = array('admin.team_lead_id' => $userid);
        } 
        elseif ($role==4) { //checker
            $condition = array();
        }  

        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $config = array();
        $config["base_url"] = base_url() . "booking/index/";
        $total_rows =  $this->secondarybooking_model->CountBookingList($condition);
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
        if ($this->uri->segment(3)) {
            $page = ($this->uri->segment(3));
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
        $data['bookings'] = $this->secondarybooking_model->GetBookingList($condition,$limit,$page); 

        $state_id = $admin_info['state_id'];
        $condition = array('state_id' => $state_id);
        $vendor_id = $admin_info['vendor_id'];
        $data['super_disributers'] = $this->vendor_model->GetUsersByids($vendor_id);
        $condition = array('distributors.vendor_id' => $vendor_id);
        $data['disributers'] = array();// $this->distributor_model->GetDistributorsbystate($condition); 
        $condition = array('booking_booking.party_id' => $vendor_id); 
        $data['skulists'] = array();//$this->secondarybooking_model->GetVendorSkus($condition);  
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;      

        //echo "<pre>"; print_r($data['bookings']); die;

        $this->load->view('secondarybooking',$data);
    }
        
    public function GetDistributors(){ 
        $vendor_id = $_POST['vendor_id'];
        $condition = array('distributors.vendor_id' => $vendor_id,'vendors.id' => $vendor_id);
        $disributers = $this->distributor_model->GetDistributorsbystate($condition); 
        $response = "<option value=''>Select Supply To</option>";
        if($disributers)
        {
            foreach ($disributers as $key => $value) {
                $response .= "<option value='".$value['distributor_id']."'>".$value['name']."</option>";
            }
        }
        echo $response; 
    }

    public function GetBookingSkus(){ 
        $vendor_id = $_POST['vendor_id'];
        $condition = array('booking_booking.party_id' => $vendor_id); 
        $skulists = $this->secondarybooking_model->GetVendorSkuswithcnfrate($condition); 
        $stocklists = array();
        if($vendor_id)
            $stocklists = $this->getcurrentdata($vendor_id);
        //echo "<pre>"; print_r($stocklists); die;
        $res = "" ;
        if($skulists)
        {
            $i = 1;            
            foreach ($skulists as $key => $value) {
                $available_stock = 0;
                if( array_key_exists($value['product_id'], $stocklists) )
                    $available_stock = $stocklists[$value['product_id']]['closing_qty'];
                $res .= '<div class="row"><div class="col-md-2"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Brand</label>';
                $res .= '<select class="form-control" id="" name="brand[]">';
                $res .= '<option value="'.$value['brand_id'].'">'.$value['brand_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-2"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Category</label>';
                $res .= '<select class="form-control" id="category" name="category[]">';
                $res .= '<option value="'.$value['category_id'].'">'.$value['category_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-2"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Product</label>';
                $res .= '<select class="form-control product_packing" id="" name="product[]">';
                $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $res .= '<option value="'.$value['id'].'">'.$value['name'].$packing_items_qty.'</option>';
                $res .= '</select></div></div><div class="col-md-2"><div class="form-group">';
                $cnf_rate_enable = '';
                $cnf_rate = '';
                if($value['cnf'])
                {
                    $cnf_rate_enable = 'readonly';
                    $cnf_rate = $value['rate'];
                }

                if($i==1)
                $res .= '<label for="quantity">Rate per piece</label>';
                $res .='<input type="text" class="form-control rate rate'.$i.'" id="" name="rate[]"  value="'.$cnf_rate.'" '.$cnf_rate_enable.'><span class="amount_display"></span>';
                $res .='</div></div><div class="col-md-2"><div class="form-group">';
                $v = '';
                $mt = 0;
                $mt1 = ''; 
                $placeholder = "Number of cartons";
                if($value['packing_items_qty']==1)
                    $placeholder = "Number of tins";
                                   
                if($i==1)
                $res .= '<label for="quantity">Quantity</label>';
                $res .= '<input type="hidden" class="packing_weight" name="packing_weight[]" value="'.$mt.'"><input type="hidden" class="packing_type" name="packing_type[]" value="'.$value['packaging_type'].'"><input type="hidden" class="packed_items_quantity" name="packed_items_quantity[]" value="'.$value['packing_items'].'" ><input type="text" class="form-control quantity_packed quantity'.$i.'" id="" name="quantity[]"  value="'.$v.'" placeholder="'.$placeholder.'">';
				
				if($value['cnf'])
				{
					$res .= 'Available :<span class="available">'.$available_stock.'</span>';
				}	
				$res .= '</div></div><div class="col-md-2 packing_weight_input_section"><div class="form-group">';

                if($i==1)
                $res .= '<label for="quantity">Weight (MT)</label>';
                $res .='<input type="text" class="form-control packing_weight_input" id="" name=""  value="'.$mt1.'" readonly>';
                $res .='</div></div></div>';
                $i++;
            }            
        } 
        echo $res; die;
    }

    public function add_booking(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        if(isset($_POST) && !empty($_POST))
        { 
            //echo "<pre>"; print_r($_POST); die;
            $supply_from = $_POST['supply_from'];
            $supply_to = $_POST['supply_to'];
            $delivery_date = $_POST['delivery_date'];
            $total_weight_input = $_POST['total_weight_input'];
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            $new_booking_id  = $this->secondarybooking_model->getlast_booking_id($book_chek_date);  
            $secondary_bargain_id = $new_booking_id+1;
            $insertdata = array(
                'secondary_booking_id' =>$secondary_bargain_id,
                'supply_from' => $supply_from,
                'supply_to' => $supply_to,
                'delivery_date' => date('Y-m-d', strtotime($delivery_date)),
                'total_weight' => $total_weight_input,
                'admin_id' => $userid,
                'remark' => $_POST['remark'],
                'payment_term' => $_POST['payment_term'],
            );
            $secondary_booking_id = $this->secondarybooking_model->AddBooking($insertdata); 
            if($secondary_booking_id)
            {
                $quantity = $_POST['quantity'];
                foreach ($quantity as $key => $value) {
                   if($value)
                   { 
                        $skudata = array(
                            'secondary_bargain_id' =>$secondary_bargain_id,
                            'secondary_booking_id' => $secondary_booking_id,
                            'brand_id' => $_POST['brand'][$key],
                            'category_id' => $_POST['category'][$key],
                            'product_id' => $_POST['product'][$key],
                            'quantity' => $value,
                            'weight' => $_POST['packing_weight'][$key],
                            'rate' => $_POST['rate'][$key],
                        );
                        $this->secondarybooking_model->AddSKU($skudata);
                    }
                }
                addlog("Added Secondary Booking DATA/SEC/".$secondary_booking_id);
                echo $secondary_booking_id; 
                //$this->add_accounts_history($supply_from);
            }
        }
    }


    public function add_accounts_history_01_02_2023($party){ 
        $party_id = $party;  
        if($this->booking_model->check_sales_history($party)==0)
        {
            $insertdata = array();
            $previous_month_stock = $this->booking_model->previous_month_stock($party);   
            $closing_qty  =0;
            $closing_weight = 0;
            $closing_amount = 0;
            



            $previous_month_buy = $this->booking_model->previous_month_buy($party);
            $previous_month_sale = $this->booking_model->previous_month_sale($party);  






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
                        $closing_qty  = $previous_month_stock['closing_qty'];
                        $closing_weight  = $previous_month_stock['closing_weight'];
                        $closing_amount  = $previous_month_stock['closing_amount'];
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
                    if($key_exists===0 || $key_exists)
                    {
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
                        $insertdata[$i]['opening_qty'] = $value['purchased_qty'];
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
                $closing_amount = 0;
                $opening_weight = 0;
                if($previous_month_buy)
                {
                    foreach ($previous_month_buy as $key => $value) { 
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $value['purchased_qty'];
                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
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
            //echo "<pre>"; print_r($insertdata);die; 
            if($insertdata)
                $this->booking_model->AddStock($insertdata);
            //echo "<pre>"; print_r($insertdata);die; 
        }    

    }

    public function add_accounts_history($party){ 
        $party_id = $party;  
        if($this->booking_model->check_sales_history($party)==0)
        {
            $insertdata = array();
            $previous_month_stock = $this->booking_model->previous_month_stock($party);   
            $closing_qty  =0;
            $closing_weight = 0;
            $closing_amount = 0;
            



            $previous_month_buy = $this->booking_model->previous_month_buy($party);
            $previous_month_sale = $this->booking_model->previous_month_sale($party);  






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
                        $closing_qty  = $previous_month_stock['closing_qty'];
                        $closing_weight  = $previous_month_stock['closing_weight'];
                        $closing_amount  = $previous_month_stock['closing_amount'];
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
                    if($key_exists===0 || $key_exists)
                    {
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
                        $insertdata[$i]['opening_qty'] = $value['purchased_qty'];
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
                $closing_amount = 0;
                $opening_weight = 0;
                $opening_amount  = 0;
                if($previous_month_buy)
                {
                    foreach ($previous_month_buy as $key => $value) { 
                        $insertdata[$i]['party_id'] = $value['party_id'];
                        $insertdata[$i]['brand_id'] = $value['brand_id'];
                        $insertdata[$i]['category_id'] = $value['category_id'];
                        $insertdata[$i]['product_id'] = $value['product_id'];
                        $insertdata[$i]['opening_qty'] = $value['purchased_qty'];
                        $insertdata[$i]['stock_date'] = date("Y-m-t", strtotime("last month"));
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
            //echo "<pre>"; print_r($insertdata);die; 
            if($insertdata)
                $this->booking_model->AddStock($insertdata);
            //echo "<pre>"; print_r($insertdata);die; 
        }    

    }

    public function GetBookingInfoDetails(){ 
        //$_POST['booking_id'] = 682;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
        $condition = array();


        $bargain_id = $_POST['booking_id'];
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        //$condition = array('secondary_booking_skus.secondary_booking_id' => $booking_info['secondary_booking_id']);
		$condition = array('secondary_booking_skus.secondary_booking_id' => $booking_info['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition); 

        $distributor_name  = 'M/S '.$booking_info['distributor_name'].' - '.$booking_info['distributor_city_name'];
        $party_name = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $order_date = date('d-m-Y', strtotime($booking_info['created_at'])); 
        $bargain_number = 'DATA/SEC/'.$booking_info['secondary_booking_id'];
        $dispatch_date = date('d-m-Y', strtotime($booking_info['delivery_date'])); 
        $ordered_total_weight = $booking_info['total_weight']; 
        $payment_term = $booking_info['payment_term']; 
        $remark = $booking_info['remark']; 
        $id = $booking_info['id']; 
         
        $rate = $booking_info['rate']; 
        //echo "<pre>"; print_r($skus); die;
        $item_total = 0;
        if($skus)
        {
            $sr = 1;
            foreach ($skus as $key => $value) {
                $item_total = $item_total+$value['quantity'];
            }
        }


        $result = '<div class="modal-body">
            <h2 style="text-align:center">'.$distributor_name.'</h2>
            <table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                <thead>
                    <tr>
                        <th class="text-center">Orderd Date </th>
                        <th class="text-center">Supply From </th>
                        <th class="text-center">Secondary Bargain Number</th>
                    </tr>
                    <tr>
                        <td> '.$order_date.'</td>
                        <td>'.$party_name.'</td>
                        <td>'.$bargain_number.'</td>
                    </tr> 
                    <tr>
                        <th class="text-center">Total Weight (MT)</th>
                        <th class="text-center">Payment Term</th>
                        <th class="text-center">Total Items </th>
                    </tr>
                    <tr>
                        <td>'.$ordered_total_weight.'</td>
                        <td>'.$payment_term.'</td>
                        <td>'.$item_total.'</td>
                    </tr>'; 
                    $result .= '<tr>
                        <th class="text-left" colspan="3">Remark</th> 
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left">'.$remark.'</td> 
                    </tr>'; 
                $result .= '</thead>
            </table>';
             $result .= '<div class="table-responsive"><table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                <thead>
                    <tr>
                        <th class="text-center">Sr.No.</th>
                        <th class="text-center">Packing</th>
                        <th class="text-center">Qty (Tins/Cartons)</th>
                        <th class="text-center">Weight (MT)</th>
                        <th class="text-center">Rate</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>'; 
            if($skus)
            {
                $sr = 1;
                $total_amount = 0;
                foreach ($skus as $key => $value) {
                    $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                    $result .= '<tr>
                        <td>'.$sr.'</td>
                        <td>'.$value['name'].$packing.'</td>
                        <td>'.$value['quantity'].'</td>
                        <td>'.$value['weight'].'</td>
                        <td>'.number_format($value['rate']*$value['packing_items_qty'],2).'</td>
                        <td>'.number_format($value['rate']*$value['quantity']*$value['packing_items_qty'],2).'</td>
                    </tr>';
                    $total_amount = $total_amount+($value['rate']*$value['quantity']*$value['packing_items_qty']);
                    $sr++;
                }
                $result .= '<tr>
                        <td>Total</td>
                        <td></td>
                        <td>'.$item_total.'</td>
                        <td>'.$booking_info['total_weight'].'</td>
                        <td>Total Amount </td>
                        <td>'.number_format($total_amount,2).'</td>
                    </tr>';
            }
            $result .= '</table></div></div>';
            $mail_text = "Send mail to supplier";
            $mail_btn_class ='';
            if($booking_info['is_mail']==1)
            {
                $mail_text = "Mail sent to supplier";
                $mail_btn_class = 'btn_report1';
            }
            $result .= '<div class="modal-footer">                       
                    <span class="send_mail_vendor btn btn-default '.$mail_btn_class.'" rel="'.$id.'">'.$mail_text.'</span> 
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>';
        echo $result; die;        
    }

    public function GetBookingInfoDetailsPdf_old(){ 
        $_POST['booking_id'] = 39;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
         
 

        $bookings = $this->secondarybooking_model->GetBookingInfoById($_POST['booking_id']);        
        $condition = array('secondary_booking_skus.secondary_booking_id' => $bookings['secondary_booking_id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition); 

        $updated = $this->secondarybooking_model->updatebargiansmail($_POST['booking_id']); 

        echo "<pre>"; print_r($bookings); die; 
        $secondary_booking_id =  $bookings['secondary_booking_id'];
        $party_name = $bookings['party_name'];
        $city_name = $bookings['city_name'];
        $total_weight = $bookings['total_weight'];
        $distributor_name = $bookings['distributor_name'];
        $distributor_city_name = $bookings['distributor_city_name'];


        $vendor_address = $bookings['vendor_address'];
        $vendor_gst_no = $bookings['vendor_gst_no'];
        $vendor_zipcode = $bookings['vendor_zipcode'];
        $vendor_mobile = $bookings['vendor_mobile'];
        $vendor_state_name = $bookings['state_name'];

        $distributor_state_name = $bookings['distributor_state_name'];
        $distributors_address = $bookings['distributors_address'];
        $distributors_gst_no = $bookings['distributors_gst_no'];
        $distributors_zipcode = $bookings['distributors_zipcode'];
        $distributors_mobile = $bookings['distributors_mobile'];

        $distributors_email = $bookings['distributors_email'];
        $vendors_email = $bookings['vendors_email'];
        $sales_executive_name = $bookings['sales_executive_name']; 
        $sales_executive_mobile = $bookings['sales_executive_mobile'];
        $maker_mobile = $bookings['maker_mobile'];
        $maker_email = $bookings['maker_email'];
        $booking_date = date("d-m-Y", strtotime($bookings['created_at'])); 
        $remarks = $bookings['remark'];
        $booking_id_base = base64_encode($bookings['id']);
        $result = ' 
            <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <style>
            body {font-family: Poppins; }
            .table-border td {
              border: none;
              border-collapse: collapse;
            }
            .table-border th {
              border: none;
              border-collapse: collapse;
            }
            </style>  
                    <table colspan style="width:750px;margin:0 auto; page-break-inside: avoid; ">
                         
                        <tr>
                            <td colspan="2" style="padding: 0px 40px;">
                                <table style="width:100%;">
                                    <tr>
                                        <td colspan="" style="width:60%">
                                        Order To :
                                        <p style="text-align: left;"><strong>'.$bookings['party_name'].' - '.$bookings['city_name'].'</strong><br>Address : '.$vendor_address.' '.$vendor_state_name.' '.$vendor_zipcode.'<br> GSTIN : '.$vendor_gst_no.'<br>Mobile: '.$vendor_mobile.'
                                        </p>
                                        </td>
                                        <td colspan="" style="padding: 10px 0px 10px 10px;background: #f2fbfe;">
                                        <p style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;">Bargain No : <span style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;padding-left: 20px;">#DATA/SEC/'.$secondary_booking_id.'</span></p>
                                        <p style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;">Booking Date : <span style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;">'.$booking_date.'</span></p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <tr style="">
                            <td colspan="2" style="padding: 0px 20px;">
                                <table class="table-border" colspan style="width:100%;margin-top:10px;border-collapse: collapse;">
                                    <thead>
                                    <tr>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">S.No.</p></th>
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Brand Name</p></th>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Product</p></th>
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Packed In</p></th>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Qty (Tins/Cartons)</p></th>
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Weight (MT)</p></th> 
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Rate</p></th> 
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Amount</p></th> 
                                        
                                    </tr>
                                    </thead>
                                    <tbody>'; 
                                        if($skus) { 
                                            $i = 1;
                                            $total = 0;
                                            $total_amount = 0;
                                            $total_qty = 0;
                                            foreach ($skus as $key => $value) { 
                                                $rate = $value['rate'];
                                                $amount = $rate*$value['quantity'];
                                                $total_amount = $total_amount+$amount;
                                                $total_qty = $total_qty+$value['quantity'];
                                        $result .= '<tr  style="background:#d9f3fd;">
                                            <td style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$i.'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['brand_name'].'</p></td>
                                            <td style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['category_name'].'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['name'].'</p></td>
                                            <td style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['quantity'].'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['weight'].'</p></td>
                                            <td style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$rate.'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$amount.'</p></td>';
                                                $i++;
                                            }
                                        }
                                        $result .= '</tbody>
                                    <tr  style="background:#404041;border-top: 3px solid #fff;">
                                        <td colspan="4" style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:right;color: #fff;line-height:22px;">Total</p></td>
                                        
                                        <td style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #fff;line-height:22px;">'.$total_qty.'</p></td>
                                        <td colspan="" style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #fff;line-height:22px;">'.$total_weight.'</p></td>
                                        <td colspan="" style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #fff;line-height:22px;"></p></td>
                                        <td style="padding:10px 5px; text-align:center;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #fff;line-height:22px;">'.$total_amount.'</p></td>
                                    </tr>
                                    
                                </table>
                            </td>               
                        </tr>';
                        if($remarks)
                        {
                        $result .= '<tr>
                            <td colspan="2" style="padding: 0px 20px;"> 
                                <p style="font-size: 14px;font-family: Poppins;font-weight: 600;color: #404041;line-height:32px;margin-top: 22px;margin-bottom: 0px;border-bottom:1px solid #0294d8;width:200px;">Remark </p>
                                 <ol>';
                                 $result .= $remarks;
                            $result .= '</ol></td>
                        </tr>';
                        }
                        $result .= '
                    </table>'; 
            //echo APPPATH."third_party/mpdf/mpdf.php"; die;
            //echo $result; die;
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','0','0','20','18','0','0'); 
            $header = '<div style="text-align:center;"><strong>'.ucwords($distributor_name).'</strong>
                                            <br> Address : '.$distributors_address.' '.$distributor_city_name.' '.$distributor_state_name.' '.$distributors_zipcode.'<br>
                                            GSTIN : '.$distributors_gst_no.'<br>
                                            Mobile : '.$distributors_mobile.'</div>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($result);
            $f_name = $distributor_name.$secondary_booking_id.'.pdf';
            $f_name = str_replace(' ', '-', $f_name);
            $pdf_file = base_url().'invoices/secondary/'.$f_name;
            $f_name = FCPATH.'/invoices/secondary/'.$f_name;  
            
            $mpdf->Output($f_name,'F'); 
            include 'mailer/email.php'; 
            $from = "webmaster@dil.in";
            $from_name = $bookings['party_name'];
            $subject   = 'Supply to '.$bookings['distributor_name'].' - '.$bookings['distributor_city_name']; 
            $email = $bookings['email'];
            //$email = 'manvendra.s@bharatsync.com'; 
            $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong><br><br><br>* This is system generated email please do not reply.</strong>';
            $cc= '';
            $bcc= '';            
            if($distributors_email)
                    $cc= $distributors_email;  
            smtpmailer($email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            if($vendors_email)
            { 
                $approval_link = 'Plase check attached invoice and approve the order by clicking the below Link. <br> <a href="'.base_url().'approve/index/'.$booking_id_base.'">Click Here</a>';
                $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong>'.$approval_link.'<br><br><br>* This is system generated email please do not reply.</strong>'; 
                $cc= '';
                $bcc= ''; 
                smtpmailer($vendors_email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            }
 
            $message_params = urlencode($distributor_name.'~'.$bookings['party_name'].'~'.$sales_executive_name);
            
            $curl_watsappapi = curl_init();
            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$maker_mobile.'&TID=8909629&P='.$message_params.'&PATH='.$pdf_file,
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


    public function report(){    
        //unset($_SESSION['search__secondary_report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Secondary Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';
        $data['disributers'] = array();
        if(!empty($_POST) || isset($_SESSION['search__secondary_report_data']))
        //if(!empty($_POST))
        {
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__secondary_report_data'] = $_POST;
            else
                $_POST = $_SESSION['search__secondary_report_data']; 
            $supply_from = $_POST['supply_from'];
            $supply_to = $_POST['supply_to'];  
            $employee = $_POST['employee'];
            $booking_date_from = date('Y-m-d',strtotime($_POST['booking_date_from']));
            $booking_date_to = date('Y-m-d',strtotime($_POST['booking_date_to']));   
            $role = $this->session->userdata('admin')['role']; 

            $employee = $_POST['employee'];   

            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            }
            $this->load->library("pagination");
            $limit = 20;
            $conditions_data['limit'] = $limit;
            $conditions_data['booking_date_from'] = $booking_date_from;
            $conditions_data['booking_date_to'] = $booking_date_to;
            $conditions_data['supply_from'] = $supply_from;
            $conditions_data['supply_to'] = $supply_to;
            $conditions_data['employee'] = $employee;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 

            $config = array();
            $config["base_url"] = base_url() . "secondarybooking/report/";

            ///$condtition[]  
            $total_rows =  $this->secondarybooking_model->CountSecondaryBooking($conditions_data);
            $config["total_rows"] = $total_rows;
            // Number of items you intend to show per page.
            $config["per_page"] = 20;
            // Use pagination number for anchor URL.
            $config['use_page_numbers'] = TRUE;
            //Set that how many number of pages you want to view.
            $config['num_links'] = 2;
            /*$config['uri_segment'] = 4; 
            $config["per_page"] = $limit;
            $config['use_page_numbers'] = TRUE; */
            $this->pagination->initialize($config);
            if ($this->uri->segment(3)) {
                $page = ($this->uri->segment(3));
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
            //echo "<pre>"; print_r($data);  print_r($config); die;
            //echo $booking_status;
            $data['bookings'] = $this->secondarybooking_model->GetReportBooking($limit,$page,$conditions_data);
            //echo "<pre>"; print_r($data); die;
            if($supply_from)
            {
                $condition = array('distributors.vendor_id' => $supply_from);
                $data['disributers'] = $this->distributor_model->GetDistributorslist($condition);
            }
        }   

        
        $state_id = $admin_info['state_id'];
        $condition = array('state_id' => $state_id);
        $data['super_disributers'] = $this->vendor_model->GetUsersByState($state_id);
        //$condition = array('distributors.state_id' => $state_id);
        //$data['disributers'] = $this->distributor_model->GetDistributorsbystate($condition);
        $data['logged_in_id'] = $admin_id;
        $data['logged_role'] = $role;    
        $data['employees'] =  array();
        
        if($role==1)
        {
            $condition = array('role' => 6, 'team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetSecondaryMakers($condition);
        } 
        if($role==4 || $role==5)
        {
            $condition = array('role' => 6);
            $data['employees'] = $this->admin_model->GetSecondaryMakers($condition);
        }

        //echo "<pre>"; print_r($data['employees']); die;
        $this->load->view('secondary_booking_report',$data);
    }


    public function getDistributers(){   
        $vendor_id = $_POST['vendor_id'];
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 

        $condition = array('distributors.vendor_id' => $vendor_id);
        if($role==1)
        {
           $condition['distributors.maker_id'] = $admin_id;
        }
        $distributors = $this->distributor_model->GetDistributorslist($condition);
        $res = "<option value=''>Select Supply To</option>";
        if($distributors)
        {
            foreach ($distributors as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['name'].' - '.$value['city_name'].'</option>';
            }
        }
        echo $res; die;
    }

    /*  ============================== End  ====================================================*/

    public function GetBookingInfoDetailsMail($booking_id){ 
        //$_POST['booking_id'] = 682;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
        $condition = array();


        $bargain_id = $booking_id;
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition); 

        $party_name = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $broker_name = $booking_info['broker_name'];
        $sales_executive_name = $booking_info['sales_executive_name'];
        $bargain_number = 'DATA/SEC/'.$booking_info['booking_id'];
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $production_unit = $booking_info['production_unit'];
        $ordered_total_weight = $booking_info['total_weight'];
        $sku_total_weight = $booking_info['total_weight_input']; 
        $sku_total_weight = $booking_info['total_weight_input']; 
        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
        //echo "<pre>"; print_r($skus); die;
        $item_total = 0;
        if($skus)
        {
            $sr = 1;
            foreach ($skus as $key => $value) {
                $item_total = $item_total+$value['quantity'];
            }
        }


        $result = '
            <h2 style="text-align:center">'.$party_name.'</h2>
            <table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                <thead>
                    <tr>
                        <th class="text-center">Orderd Date </th>
                        <th class="text-center">Broker </th>
                        <th class="text-center">Sales Executive</th>
                    </tr>
                    <tr>
                        <td> '.$order_date.'</td>
                        <td>'.$broker_name.'</td>
                        <td>'.$sales_executive_name.'</td>
                    </tr>
                    <tr>
                        <th class="text-center">Bargain Number</th>
                        <th class="text-center">Dispatch Date</th>
                        <th class="text-center">Production Unit</th>
                    </tr>
                    <tr>
                        <td>'.$bargain_number.'</td>
                        <td>'.$dispatch_date.'</td>
                        <td>'.$production_unit.'</td>
                    </tr>
                    <tr>
                        <th class="text-center">Orderd Total Weight</th>
                        <th class="text-center">SKU Total Weight</th>
                        <th class="text-center">Total Items </th>
                    </tr>
                    <tr>
                        <td>'.$ordered_total_weight.'</td>
                        <td>'.$sku_total_weight.'</td>
                        <td>'.$item_total.'</td>
                    </tr>
                    <tr>
                        <th class="text-center">Insurace Included in price </th>
                        <th class="text-center">Price ex-factory</th>
                        <th class="text-center"></th>
                    </tr>
                    <tr>
                        <td>'.$insurance.'</td>
                        <td>'.$is_for.'</td>
                        <td></td>
                    </tr>'; 
                    $result .= '<tr>
                        <th class="text-left" colspan="3">Remark</th> 
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left">'.$remark.'</td> 
                    </tr>'; 
                $result .= '</thead>
            </table>';
             $result .= '<table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                <thead>
                    <tr>
                        <th class="text-center">Sr.No.</th>
                        <th class="text-center">Packing</th>
                        <th class="text-center">Quantity(Tins/Cartons)</th>
                        <th class="text-center">Weight</th>
                    </tr>
                </thead>'; 
            if($skus)
            {
                $sr = 1;
                foreach ($skus as $key => $value) {
                    $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                    $result .= '<tr>
                        <td>'.$sr.'</td>
                        <td>'.$value['name'].$packing.'</td>
                        <td>'.$value['quantity'].'</td>
                        <td>'.$value['weight'].'</td>
                    </tr>';
                    $sr++;
                }
                $result .= '<tr>
                        <td>Total</td>
                        <td></td>
                        <td>'.$item_total.'</td>
                        <td>'.$booking_info['total_weight_input'].'</td>
                    </tr>';
            }
            $result .= '</table>';

        return  ''.$result; die;

        
    }

    

    
    public function BookingList(){  
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $condition = array();
        if($role==1) //maker
        {
            $condition = array('admin_id' => $userid);
        }
        elseif ($role==2) { //checker
            $condition = array('admin.team_lead_id' => $userid);
        } 

        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $config = array();
        $config["base_url"] = base_url() . "booking/index/";
        $total_rows =  $this->secondarybooking_model->CountBookingList($condition);
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
        if ($this->uri->segment(3)) {
            $page = ($this->uri->segment(3));
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



        $bookings = $this->secondarybooking_model->GetBookingList($condition,$limit,$page); 
        $i = 1;
        $response ='';
        if($bookings)
        {
            foreach ($bookings as $key => $value) { 
                $response .='<tr class="odd gradeX"><td>'.$i.'</td><td><span title="'.$value['admin_name'].'">DATA/SEC/'.$value['booking_id'].'</span></td>

                <td>'.$value['party_name'].'</td>

                <td>'.$value['city_name'].'</td><td>'.$value['brand_name'].'</td><td>'.$value['category_name'].'</td><td>'.$value['quantity'].'</td><td>'.$value['rate'].'</td><td>'.date("d-m-Y", strtotime($value['created_at'])).'</td><td>';
                if($value['status']==0) { 
                    $response .='<a href="'.base_url().'booking/edit/'.base64_encode($value['id']).'" class="btn btn-default detail">Edit</a>';
                }
                if($value['is_lock']) { 
                                                
                $response .='<a href="javascript:void(0)" rel="'.$value['booking_id'].'" class="btn btn-default detail btn_report1" data-production_unit="'.$value['production_unit'].'">Report</a>';
                } else { if($role==1) { 
                $response .='<a href="'.base_url().'booking/sku/'.base64_encode($value['booking_id']).'" rel="'.$value['booking_id'].'"  class="btn btn-default detail">Add SKU</a>';
             }  } 

                $response .='</td></tr>';
                $i++;
            }
            $response .='<tr><td>'.$data["links"].'</td></tr>';
        }
        else
        {
            $response .='<tr class="odd gradeX"><td colspan="7">No Record Found</td></tr>';
        }
        echo $response;
    }

    public function approve_order()
    {
         
        $bargain_id = $_POST['booking_id']; 
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];  

        $condition = array('booking_id' => $bargain_id);
        //$data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById(base64_decode($bargain_id));
        $updatedata = array('status'=>2);
        echo  $this->secondarybooking_model->UpdateBooking($updatedata,$condition);
    }

    public function sendmail_plant()
    {
        $from = "webmaster@dil.in";
        $from_name = "Bargain Invoice";
        $subject   = 'Bargain Invoice';  
        $bargain_id = $_POST['booking_id'];
        
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array(); 
        $condition = array('booking_id' => $bargain_id);
        //$data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById(base64_decode($bargain_id));
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->broker_model->GetBrokers(); 
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();

        $data['categories'] = $this->category_model->GetCategories();

        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/SEC/'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
         

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);
        $mail_message = "Hello , <br><br> Order <strong>#DATA/SEC/".$booking_info['booking_id']."</strong> is locked for <strong>".$booking_info['party_name']." - ".$booking_info['city_name']."</strong> <br><br> You can check order details in attached file";

        ///return $fileName.'______'.$mail_message;


        $email = 'manvendra.s@bharatsync.com'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'rohittak@dil.in';
         
        $file_name = $fileName;
        $mail_mesage = $mail_message; 
        $attach_file = FCPATH.'/'.$file_name; 
        include 'mailer/t.php'; 
        unlink($attach_file);
    }


    public function sendmail_plant_lock($bargain_id)
    {
        $from = "webmaster@dil.in";
        $from_name = "Bargain Invoice";
        $subject   = 'Bargain Invoice';  
        //$bargain_id = $_POST['booking_id'];
        
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array(); 
        $condition = array('booking_id' => $bargain_id);
        //$data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById(base64_decode($bargain_id));
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->broker_model->GetBrokers(); 
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();

        $data['categories'] = $this->category_model->GetCategories();

        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/SEC'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
         

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);
        $mail_message = "Hello , <br><br> Order <strong>#DATA/".$booking_info['booking_id']."</strong> is locked for <strong>".$booking_info['party_name']." - ".$booking_info['city_name']."</strong> <br><br> You can check order details in attached file";

        ///return $fileName.'______'.$mail_message;


        $email = 'manvendra.s@bharatsync.com'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'rohittak@dil.in';

        /*$email = 'vopsales@dil.in'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'pankajkumar.gupta@datafoods.com';*/
         
        $file_name = $fileName;
        $mail_mesage = $mail_message; 
        $attach_file = FCPATH.'/'.$file_name; 
        include 'mailer/t.php'; 
        unlink($attach_file);
    }

    public function sendmail_plant_lock_bulk($party_id)
    {
        
        //$party_id = $_POST['party_id'];
        $from = "webmaster@dil.in";
        $from_name = "Bargain Invoice";
        $subject   = 'Bargain Invoice';  
        //$bargain_id = $_POST['booking_id'];
        
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 

        $condition  = array('party_id'=>$party_id,'is_mail'=>0,'is_lock'=>1,'status'=>2);
        $bargains = $this->secondarybooking_model->getpenidngbargainInfo($condition);
        //echo "<pre>"; print_r($bargains); die;
        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        

        if($bargains)
        {
            $bargain_loop = 1;
            $i = 1;
            $row = 11;
            $party_name = '';
            $party_city = '';
            $bargaind_ids = array();
            $bargaind_numbers = array();
            $fileName = "";
            foreach ($bargains as $key => $value) {
                $col= 'B';
                $booking_info = $value;
                $condition = array('booking_skus.booking_id' => $booking_info['id']); 
                $bargaind_ids[] =$booking_info['id'];
                $bargaind_numbers[] ='#DATA/'.$booking_info['booking_id'];
                $party_name = $booking_info['party_name'];
                $city_name = $booking_info['city_name'];

                $skus = $this->secondarybooking_model->GetSkuinfo($condition);  
                $data['logged_in_id'] = $userid;
                $data['logged_role'] = $role;
                $data['bargain_id'] = $booking_info['id'];
                $data['category_name'] = $booking_info['category_name']; 
                $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
                $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
                $remark = $booking_info['remark']; 
                
                

                if($bargain_loop==1)
                {
                    $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
                    $fileName = $booking_info['party_name'].'.xls'; 
                    $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
                    $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);
                    $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);
                }
                $bargain_loop++;

                if($skus)
                { 
                
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

                    $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                    $row++;
                    $item_total = 0;
                    if($skus)
                    { 
                        $sr_no = 1;
                        foreach ($skus as $key => $value) {
                            $sku_col = 'B';
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$sr_no);
                            $sku_col++;
                            $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$value['name'].$packing);
                            $sku_col++;
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$value['quantity']);
                            $sku_col++;
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$value['weight']);
                            $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                            $row++;
                            $sr_no++;
                            $item_total = $item_total+$value['quantity'];
                        }

                        $total_col = 'C';
                        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$total_col++;
                        $objPHPExcel->getActiveSheet()->setCellValue($total_col.$row,$item_total);$total_col++;
                        $objPHPExcel->getActiveSheet()->setCellValue($total_col.$row,$booking_info['total_weight_input']);
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1); 
                        $row = ($row-$sr_no)-8;  
                        $header_row_start = $row;
                        $header_row = $row;
                        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Orderd Date : '.$order_date);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Broker : '.$booking_info['broker_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Sales Executive : '.$booking_info['sales_executive_name']);
                        $row++; $header_row++;
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Bargain Number : DATA/'.$booking_info['booking_id']);
                        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Dispatch Date : '.$dispatch_date);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Production Unit : '.$booking_info['production_unit']);
                        $row++;$header_row++;

                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Brand: '.$booking_info['brand_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Product : '.$booking_info['category_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Rate : '.$booking_info['rate']);
                        $row++;$header_row++;

                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Orderd Total Weight : '.$booking_info['total_weight']);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'SKU Total Weight : '.$booking_info['total_weight_input']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Total Items : '.$item_total);
                        $row++;$header_row++;


                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Insurace Included in price  : '.$insurance);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Price ex-factory : '.$is_for); 
                        $row++;$header_row++;
                        $objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':E'.$row);
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Remark  : '.$remark); 
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$header_row_start.':E'.$header_row)->applyFromArray($styleArray1);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$header_row_start.':E'.$header_row)->applyFromArray($BorderstyleArray);


                        $objPHPExcel->getActiveSheet()->getStyle('C'.$header_row.':E'.$header_row)->getAlignment()->applyFromArray(
                            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
                        );
                        $row = ($row+$sr_no)+13;

                    }
                }


                $row++;
            }
        }

         
        //echo $fileName; die;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);  
        
        
        $order_ids = implode(',', $bargaind_numbers);

        $mailremark  = '';
        $mail_message = "Hello , <br><br> Order <strong>".$order_ids."</strong> is locked for <strong>".$party_name." - ".$city_name."</strong> <br><br> You can check order details in attached file";

        if(isset($_POST['reamrk']))
        {
            $mail_message  .= '<br> Remark : '.trim($_POST['reamrk']); 
        }

        ///return $fileName.'______'.$mail_message;


       
        

        $email = 'vopsales@dil.in'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'pankajkumar.gupta@datafoods.com';
         
        $file_name = $fileName;
        $mail_mesage = $mail_message; 
        $attach_file = FCPATH.'/'.$file_name; 
        include 'mailer/t.php';  
        //unlink($attach_file); 
        $this->secondarybooking_model->updatebargiansmail($bargaind_ids);
    }

    public function add_skus(){ 
        //echo "<pre>"; print_r($_POST); die;
        if(!empty($_POST))
        {
            $id = $this->input->post('id'); 
            $booking_id = base64_decode($this->input->post('booking_id')); 
            $category = $this->input->post('category'); 
            $brand = $this->input->post('brand'); 
            $products = $_POST['product'];

            $quantities = $_POST['quantity']; 
            $packing_weight = $_POST['packing_weight'];

            if($products)
            {
                $added = 1;
                if($_POST['update_data'])
                {
                    $weight_update =  $this->input->post('update_weight'); 
                    $condition_booking = array('id' => $id);
                    $update_data_booking = array('brand_id' => $brand,'category_id' => $category,'total_weight' => $weight_update);
                    $this->secondarybooking_model->UpdateBookingBooking($update_data_booking,$condition_booking); 
                    $condition_all = array('booking_id' => $id);
                    $this->secondarybooking_model->DeleteSKU($condition_all); 
                } 
                $update_data_booking_weight = array('total_weight_input' => $_POST['total_weight_input'],'remaining_weight' => $_POST['remaining_weight'],'is_lock' => $_POST['flag'],'production_unit' => $_POST['production_unit']);
                $condition_booking_weight = array('id' => $id);
                $this->secondarybooking_model->UpdateBookingBooking($update_data_booking_weight,$condition_booking_weight); 

                foreach ($products as $key => $product) {                    
                    
                        $condition = array( 
                            'booking_id' => $id,
                            'bargain_id' => $booking_id,
                            'product_id' => $product, 
                        );
                        $skudata = array(
                            'brand_id' => $brand,
                            'category_id' => $category, 
                            'booking_id' => $id,
                            'bargain_id' => $booking_id,
                            'product_id' => $product,
                            'weight' => $packing_weight[$key],
                            'quantity' => ($quantities[$key]) ? $quantities[$key] : NULL,
                        ); 
                    if($quantities[$key])
                    {
                        $flag = $this->secondarybooking_model->AddSKU($condition,$skudata); 
                        if(!$flag)
                            $added = 0;
                        
                        if($_POST['flag']){
                            //$this->sendmail_plant_lock($booking_id);
                        }
                    }
                    else
                    {
                        $this->secondarybooking_model->DeleteSKU($condition); 
                    }             
                } 
                     
                //echo $added;

                if($_POST['flag']==1 && $added)
                { 
                    //$this->sendmail_plant_lock($booking_id);
                }
                echo $added; die;
            }
        }
    }
    

    public function update_booking(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        if(isset($_POST) && !empty($_POST))
        { 
            //echo "<pre>"; print_r($_POST); die;
            $supply_from = $_POST['supply_from'];
            $supply_to = $_POST['supply_to'];
            $delivery_date = $_POST['delivery_date'];
            $total_weight_input = $_POST['total_weight_input'];
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            
            $condition = array('id' => $_POST['updated_id']);
            $updatedata = array( 
                'supply_from' => $supply_from,
                'supply_to' => $supply_to,
                'delivery_date' => date('Y-m-d', strtotime($delivery_date)),
                'total_weight' => $total_weight_input, 
                'remark' => $_POST['remark'],
                'payment_term' => $_POST['payment_term'],
            );
            $secondary_booking_id = $_POST['updated_id'];
            $secondary_bargain_id = $_POST['updated_bargain_id'];
            $updated = $this->secondarybooking_model->UpdateBooking($updatedata,$condition); 
            if($updated)
            {
                $cond_sku = array('secondary_booking_id' => $secondary_booking_id);
                $this->secondarybooking_model->DeleteSecondarySKU($cond_sku);
                $quantity = $_POST['quantity'];
                foreach ($quantity as $key => $value) {
                   if($value)
                   { 
                        $skudata = array(
                            'secondary_bargain_id' =>$secondary_bargain_id,
                            'secondary_booking_id' => $secondary_booking_id,
                            'brand_id' => $_POST['brand'][$key],
                            'category_id' => $_POST['category'][$key],
                            'product_id' => $_POST['product'][$key],
                            'quantity' => $value,
                            'weight' => $_POST['packing_weight'][$key],
                            'rate' => $_POST['rate'][$key],
                        );
                        $this->secondarybooking_model->AddSKU($skudata);
                    }
                }
                addlog("Added Secondary Booking DATA/SEC/".$secondary_booking_id);
                echo $secondary_booking_id; 
            }
        }
    }
    public function update_booking1(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category');  
            $rate = $this->input->post('rate');                  
            $quantity = $this->input->post('quantity');  
            $weight = $this->input->post('weight');   
            $booking_date = $this->input->post('booking_date'); 
            $booking_number = 0;
            $today_cur_date =  date("dm"); 
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            $broker = ($_POST['broker']!='') ? $this->input->post('broker') : NULL; // $this->input->post('broker');   
            $insurance = (isset($_POST['insurance'])>0) ? $this->input->post('insurance') : 0;
            $is_for = (isset($_POST['ex_factory'])) ? $this->input->post('ex_factory') : 0;

            $remark = $this->input->post('remark'); 
            $sales_executive = $this->input->post('sales_executive'); 
            $shipment_date = $this->input->post('shipment_date'); 
                
           
            $insertdata = array( 
                'party_id' =>$party,
                'brand_id' =>$brand,
                'category_id' =>$category, 
                'quantity' =>$quantity,
                //'weight' =>$quantity*15,
                'rate' =>$rate,
                'broker_id' =>$broker,
                'insurance' =>$insurance,
                'is_for' =>$is_for,
                //'admin_id' =>$admin_id,
                'total_weight' => $weight,
                'production_unit' => $_POST['production_unit'],
                'remark' =>$remark,
                'sales_executive_id' =>$sales_executive,
                'shipment_date' =>date('Y-m-d', strtotime($shipment_date)),
            );
            $condition = array('id' => $_POST['booking_number']);
            echo $result = $this->secondarybooking_model->UpdateBooking($insertdata,$condition);
        }
    }

    public function booked_sku_info()
    { 
        $condition = array('booking_skus.booking_id' => $_POST['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition);
        //echo "<pre>"; print_r($skus); die;
        $res = "<table class='table table-striped table-bordered table-hover dataTable no-footer'><tr><td>S.No.</td><td>Name</td><td>Quantity</td></tr>";
        if($skus)
        {
            $i = 1;
            foreach ($skus as $key => $value) {
                $qty = '';
                if($value['packing_items_qty'])
                    $qty = '*'.$value['packing_items_qty'];
                $res .= "<tr><td>".$i."</td><td>".$value['name'].$qty."</td><td>".$value['quantity']."</td></tr>";
                $i++;
            }
        }
        else
        {
            $res .= "<tr><td colspan='3'>No Recod Found</td>";
        }
        $res .= "</table>";
        $res .= "<span style='color:red'><strong>Note</strong> If you lock this order. This will not change </span>";
        echo $res;
    }

    public function sku($bargain_id){   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById(base64_decode($bargain_id));
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_id' => $data['booking_info']['id']);
        $data['skus'] = $this->secondarybooking_model->GetAllSkus($condition);
 
        $data['products'] = $this->category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);
        //echo "<pre>"; print_r($data['products']); die;

        //echo "<pre>"; print_r($data['skus']); die;
        $data['brokers'] = $this->broker_model->GetBrokers(); 
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();

        $data['categories'] = $this->category_model->GetCategories();

        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name'];
        
        $data['bookings'] = array();
        $this->load->view('sku',$data);

    }

    public function createexcel($bargain_id)
    { 
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById(base64_decode($bargain_id));
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->broker_model->GetBrokers(); 
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();

        $data['categories'] = $this->category_model->GetCategories();

        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
         

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);
        $mail_message = "Hello , <br><br> Order <strong>#DATA/".$booking_info['booking_id']."</strong> is locked for <strong>".$booking_info['party_name']." - ".$booking_info['city_name']."</strong> <br><br> Please  login with given link and check the order. <a href='".base_url()."booking'>Login</a> <br><br> You can check order details in attached file";
        return $fileName.'______'.$mail_message;
        //redirect(base_url().$fileName);  
        //echo "string"; die;
    }
    public function downloadreport($bargain_id)
    {
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById(base64_decode($bargain_id));
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->broker_model->GetBrokers(); 
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();

        $data['categories'] = $this->category_model->GetCategories();

        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save('php://output');
        //redirect(base_url().$fileName);  
        //echo "string"; die;
    }

    public function add(){ 
        $data['title'] = "New Booking";

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category'); 
            $product = $this->input->post('product'); 
            $quantity = $this->input->post('quantity'); 
            $rate = $this->input->post('rate');   
            $booking_date = $this->input->post('booking_date');   

            $this->form_validation->set_rules('party', 'Party Name','required');
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required');
            $this->form_validation->set_rules('product', 'Product','required');
            $this->form_validation->set_rules('quantity', 'Quantity','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                $product_info = $this->category_model->Productinfobyid($product);
                $loose_rate = $product_info['loose_rate'];
                $weight = $product_info['weight'];
                //$for_rate = $product_info['for_rate'];
                $vendor_condition = array('id' =>$party);
                $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);
                //echo "<pre>"; print_r($vendor_info); die;
                $for_rate = $vendor_info['for_rate'];

                $total_loose_rate = $loose_rate*$quantity;
                $total_weight = $weight*$quantity;
                $total_price = $rate*$quantity;
                $total_for_price = 0;
                if($is_for==0)
                    $total_for_price = $for_rate*$quantity;

                $insurance_amount = (($total_price*$insurance)/100)+$total_price;

                
                $insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate,'loose_rate' =>$loose_rate,'total_loose_rate' =>$total_loose_rate,'total_weight' =>$total_weight,'total_price' =>$total_price,'for_total' => $total_for_price);
                if($booking_date!='')
                     $insertdata['created_at'] = $booking_date.': 00'; 
                $result = $this->secondarybooking_model->AddBooking($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Booked successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('booking');
            }
        } 


        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();
        //$data['categories'] = $this->category_model->GetCategories();
        $this->load->view('booking_add',$data);
    }

    public function edit(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $booking_id = base64_decode($this->uri->segment(3));
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Secondary Order Booking"; 

        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];   

        $condition = array('is_lock' => 1);
        if($role==1) //maker
        {
            $condition = array('admin_id' => $userid);
        }
        elseif ($role==2) { //checker
            $condition = array('admin.team_lead_id' => $userid);
        } 
        elseif ($role==4) { //checker
            $condition = array();
        }  
         
        
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById1($booking_id); 

        $state_id = $admin_info['state_id'];
        $condition = array('state_id' => $state_id);
        $vendor_id = $admin_info['vendor_id'];
        $data['super_disributers'] = $this->vendor_model->GetUsersByids($vendor_id);
        $condition = array('distributors.vendor_id' => $vendor_id);
        $data['disributers'] = array();// $this->distributor_model->GetDistributorsbystate($condition); 
        $condition = array('booking_booking.party_id' => $vendor_id); 
        $data['skulists'] = array();//$this->secondarybooking_model->GetVendorSkus($condition);  
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;  

        $vendor_id = $data['booking_info']['supply_from'];
        $condition = array('booking_booking.party_id' => $vendor_id); 
        $data['skulists']  = $this->secondarybooking_model->GetVendorSkuswithcnfrate($condition);   

        $data['stocklists'] = $this->getcurrentdata($vendor_id);

        $condition = array('secondary_booking_skus.secondary_booking_id' => $booking_id);
        $data['booked_sku'] = $this->secondarybooking_model->Bookedskus($condition); 

        $condition = array('distributors.vendor_id' => $vendor_id,'vendors.id' => $vendor_id);
        $data['disributers'] = $this->distributor_model->GetDistributorsbystate($condition); 
        //echo "<pre>"; print_r($data['skulists']); die;
        $this->load->view('secondarybooking_edit',$data);

    }
    public function edit_old(){ 
        $data['title'] = "Booking Edit";
        $booking_id = base64_decode($this->uri->segment(3));
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category'); 
            $product = $this->input->post('product'); 
            $quantity = $this->input->post('quantity'); 
            $rate = $this->input->post('rate');    
            $insurance = $this->input->post('insurance');
            $booking_date = $this->input->post('booking_date');   
            $loose_rate = $this->input->post('loose_rate');  
            $broker = $this->input->post('broker');  
            $is_for = $this->input->post('is_for');  
            $dispatch_delivery_terms = $this->input->post('dispatch_delivery_terms');   
            $payment_terms = $this->input->post('payment_terms');


            $this->form_validation->set_rules('party', 'Party Name','required');
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required');
            $this->form_validation->set_rules('product', 'Product','required');
            $this->form_validation->set_rules('quantity', 'Quantity','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                //$insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate);

                $product_info = $this->category_model->Productinfobyid($product); 
                $weight = $product_info['weight'];
                $loose_rate = $product_info['loose_rate'];
                //$for_rate = $product_info['for_rate'];
                $vendor_condition = array('id' =>$party);
                $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);
                //echo "<pre>"; print_r($vendor_info); die;
                $for_rate_per_kg = $vendor_info['for_rate'];
                //$total_for_price = 0;
                $for_price = $for_rate_per_kg;
                
                $total_loose_rate = $loose_rate*$quantity;
                $total_weight = $weight*$quantity;
                
                //$total_for_price = $for_rate;
                $rate1 = $rate;
                $total_for_price1 = 0;
                if($is_for==0)
                {
                    $for_rate = $for_rate_per_kg*$weight;                    
                    $total_for_price1 = $rate;
                    $rate1 = $rate-$for_rate;
                }
                $total_price = $rate1*$quantity;
                if($broker=='')
                    $broker = 0;
                if($insurance=='')
                    $insurance = 0.00;
                $insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate1,'total_loose_rate' =>$total_loose_rate,'total_weight' =>$total_weight,'total_price' =>$total_price,'insurance' =>$insurance,'insurance_amount' =>$insurance_amount,'broker_id' =>$broker,'is_for' =>$is_for,'for_total' => $total_for_price1,'for_price' => $for_price,'dispatch_delivery_terms' => trim($dispatch_delivery_terms),'payment_terms' => trim($payment_terms));


                if($booking_date!='')
                     $insertdata['created_at'] = $booking_date; 
                $condition = array('id' =>$booking_id);
                $result = $this->secondarybooking_model->UpdateBooking($insertdata,$condition);
                if($result)
                    $this->session->set_flashdata('suc_msg','Booked updated successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('booking');
            }
        } 


        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['brokers'] = $this->broker_model->GetBrokers();
        $data['users'] = $this->vendor_model->GetUsers();
        $data['booking_info'] = $this->secondarybooking_model->GetBookingInfoById($booking_id);
        //echo "<pre>"; print_r($data['booking_info']); die;
        //$data['categories'] = $this->category_model->GetCategories();
        $this->load->view('booking_edit',$data);
    }
    
    public function delete(){
        $data['title'] = "Order Edit";
        $booking_id = base64_decode($this->uri->segment(3));
        if($booking_id)
        {
            $condition = array('id' =>$booking_id);
            $result = $this->secondarybooking_model->DeleteBooking($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Booking deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('booking');  
    }

    public function report_28_10_2022(){   
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';
        if(!empty($_POST) || isset($_SESSION['search__report_data']))
        {
            //echo "<pre>"; print_r($_POST); die;
            //$this->session->set_userdata('search__report_data', $_POST); 
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__report_data'] = $_POST;
            else
                $_POST = $_SESSION['search__report_data'];
            $party_id = $_POST['party'];
            $brand_id = $_POST['brand'];
            $category_id = $_POST['category']; 
            $booking_date_from = $_POST['booking_date_from'];
            $booking_date_to = $_POST['booking_date_to'];
            $booking_status = (isset($_POST['status'])) ? $_POST['status'] : '';
            $role = $this->session->userdata('admin')['role'];
            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            }
            $this->load->library("pagination");

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 

            $config = array();
            $config["base_url"] = base_url() . "booking/report/";
            $total_rows =  $this->secondarybooking_model->CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status);
            $config["total_rows"] = $total_rows;
            // Number of items you intend to show per page.
            $config["per_page"] = 20;
            // Use pagination number for anchor URL.
            $config['use_page_numbers'] = TRUE;
            //Set that how many number of pages you want to view.
            $config['num_links'] = 2;
            /*$config['uri_segment'] = 4; 
            $config["per_page"] = $limit;
            $config['use_page_numbers'] = TRUE; */
            $this->pagination->initialize($config);
            if ($this->uri->segment(3)) {
                $page = ($this->uri->segment(3));
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
            //echo "<pre>"; print_r($data);  print_r($config); die;

            $data['bookings'] = $this->secondarybooking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page);
            //echo "<pre>"; print_r($data); die;
        } 
        
        //echo "<pre>"; print_r($data['bookings']); die;
        $data['categories'] = $this->category_model->GetCategories();
        $data['brands'] = $this->brand_model->GetAllBrand();
        $data['users'] = $this->vendor_model->GetUsers();
        $data['booking_date_from'] = $booking_date_from;
        $data['booking_date_to'] = $booking_date_to;

        $data['party_id'] = $party_id;
        $data['booking_status'] = $booking_status;
        $data['brand_id'] = $brand_id;
        $data['category_id'] = $category_id;
        $data['product_id'] = $product_id; 
        $data['distinct_categories'] = $this->category_model->GetCategories1(); 
        $data['logged_in_id'] = $admin_id;
        $data['logged_role'] = $role;
        $this->load->view('booking_report',$data);

    }

    


    public function report_print(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';

        $party_id = $_REQUEST['party'];
        $brand_id = $_REQUEST['brand'];
        $category_id = $_REQUEST['product']; 
        $booking_date_from = date('Y-m-d',strtotime($_REQUEST['from']));
        $booking_date_to = date('Y-m-d',strtotime($_REQUEST['to'])); 
        $booking_status = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : '';
        $role = $this->session->userdata('admin')['role'];

        $employee = $_REQUEST['employee']; 
        $unit = $_REQUEST['production_unit']; 

        $booked_by = '';
        $condition = array();
        if($role==1) //maker
        {
            $booked_by = $this->session->userdata('admin')['id'];
        }
        $limit = 2000000;
        $page = 1;
        $bookings = $this->secondarybooking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);
        $html_print = '';

            $html_print .= '
            <table  style="border-left:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">';
            if($bookings)
            {
                $sno = 1;
                foreach ($bookings as $key => $value) {
                    $bg_color = "#fff";
                    if($sno%2==0)
                        $bg_color = "#ece9e9";
                    $b_status = '';
                    if($value['status']==3)  
                    {
                        $b_status = "Rejected";
                        if($value['is_lock'])
                             $b_status =  $b_status.' (Locked)';
                    }
                    elseif($value['status']==2)
                    {
                        $b_status = "Approved"; 
                        if($value['is_lock'])
                             $b_status =  $b_status.' (Locked)';
                    }
                    else 
                    {
                        $b_status = "Approval Pending"; 
                        if($value['is_lock'])
                             $b_status =  $b_status.' (Locked)';
                    }
                    $ex = 'For';
                    if($value['is_for'])
                        $ex = 'Ex';
                        $html_print .= '<tr style="background-color:'.$bg_color.';">
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:60px;">'.$sno.'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:100px;">DATA/'.$value['booking_id'].'</td>


                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px;">'.$value['party_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.$value['city_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['brand_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['category_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['quantity'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['rate'].' ('.$ex.')</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['weight'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['production_unit'].'</td>

                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.date("d-m-Y", strtotime($value['shipment_date'])).'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['remark'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'. date("d-m-Y", strtotime($value['created_at'])).'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>'.$b_status.'</strong></td>
                            </tr>'; 
                            $sno++;
                } 

            }

            $html_print .= '</table>';




        include(FCPATH."mpdf1/mpdf.php");


        $mpdf=new mPDF('utf-8','A4-L','0','0','10','10','20','0','0','0'); 
        $header = '<table  style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr style="background-color:#c8c8c8;">
                                        <td colspan="14" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; width:60px; text-align:center"><h3>Bargain Report</h3></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:60px;"><strong>S.No</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Bargain No</strong></td>


                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Party Name</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px"><strong>Place</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Brand</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Product</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Quantity (Tins)</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Rate (15Ltr Tin)</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Weight (MT)</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Pr. Unit</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Delivery Date</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Remark</strong></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Date</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Status</strong></td>
                                    </tr>
                                </table>';
            $footer = '{PAGENO} of {nbpg}';
        //echo $footer; die;
        $mpdf->SetHTMLHeader($header); 
        $mpdf->SetHTMLFooter($footer);   
        $mpdf->WriteHTML($html_print);
        $f_name = 'Report'.'pdf';
        $invpice_name = FCPATH.'/'.$f_name; 
        $mpdf->Output($f_name,'I'); 
    }

    public function report_print_excel(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';

        $party_id = $_REQUEST['party'];
        $brand_id = $_REQUEST['brand'];
        $category_id = $_REQUEST['product']; 
        $booking_date_from = date('Y-m-d',strtotime($_REQUEST['from']));
        $booking_date_to = date('Y-m-d',strtotime($_REQUEST['to'])); 
        $booking_status = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : '';
        $role = $this->session->userdata('admin')['role'];

        $employee = $_REQUEST['employee']; 
        $unit = $_REQUEST['production_unit']; 
        $booked_by = '';
        $condition = array();
        if($role==1) //maker
        {
            $booked_by = $this->session->userdata('admin')['id'];
        }
        $limit = 2000000;
        $page = 1;
        $bookings = $this->secondarybooking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);

        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            ),
        );
         
        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );
         

        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $col='A';
        $row = 2;
        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );  

        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');
        $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(10);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Bargain No');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Party Name');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Place');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Brand');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Product');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity (Tins)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Rate (15Ltr Tin)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weight (MT)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Production Unit');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Delivery Date');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Remark');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Date');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Status');

        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$col.'1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1','Bargain Report');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray3);
        $row++;
         

        if($bookings)
        {
            
            $sno = 1;
            foreach ($bookings as $key => $value) {
                $col='A';
                $bg_color = "#fff";
                if($sno%2==0)
                    $bg_color = "#ece9e9";
                $b_status = '';
                if($value['status']==3)  
                {
                    $b_status = "Rejected";
                    if($value['is_lock'])
                         $b_status =  $b_status.' (Locked)';
                }
                elseif($value['status']==2)
                {
                    $b_status = "Approved"; 
                    if($value['is_lock'])
                         $b_status =  $b_status.' (Locked)';
                }
                else 
                {
                    $b_status = "Approval Pending"; 
                    if($value['is_lock'])
                         $b_status =  $b_status.' (Locked)';
                }
                $ex = 'For';
                if($value['is_for'])
                    $ex = 'Ex';

                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sno);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'DATA/'.$value['booking_id']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['party_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['city_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['brand_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['category_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['rate'].' ('.$ex.')');$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['production_unit']);$col++;
                
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,date("d-m-Y", strtotime($value['shipment_date'])));$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['remark']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,date("d-m-Y", strtotime($value['created_at'])));$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$b_status);$col++; 
                $row++;
                $sno++;
            }
        }

        $fileName = 'Report.xls'; 
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save('php://output'); 
    }


    public function summary_print(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';
        $booked_by = ''; 
        if($role==1) //maker
        {
            $booked_by = $this->session->userdata('admin')['id'];
        }
        $party_id = $_GET['party'];
        $brand_id = $_GET['brand'];
        $category_id = $_GET['product']; 
        $booking_date_from = date('Y-m-d',strtotime($_GET['from']));
        $booking_date_to = date('Y-m-d',strtotime($_GET['to']));   
        $booking_status = $_GET['status']; 
        $employee = $_GET['employee']; 
        $unit = $_GET['production_unit']; 
        $group_by  = array('status');
        $sum_report = $this->secondarybooking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,'',$unit);

        $tot_sum_report = $this->secondarybooking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,1,$unit);

        $locked = $this->secondarybooking_model->GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,$unit);  

        //echo "<pre>"; print_r($data['locked']); die; 
        if($_GET['type']=='place')
        {
            $group_by  = array('place','brand_id','category_id');
            $bookings_brand_product_place = $this->secondarybooking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);

            $html_print = '
            <table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($tot_sum_report_value['weight'],2).'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                      $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($locked_weight,2).'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($sum_report_value['weight'],2).'</td>
                                        </tr>'; 
                                    } }  }
                                $html_print .= '</table>
            <table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                        <thead>
                            <tr><th colspan="8" style="text-align:center;border-bottom:1px solid #000;">Order summary based on state from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">S.No</th>    
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">State</th>  
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Brand</th>   
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Product</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Qty <br> (Tins/Cartons)</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Weight <br>  (MT)</th>
                                <th style="text-align:left; border-bottom:1px solid #000;"  colspan="2">Avg Rate / Loose(kg)</th>
                            </tr>
                            <tr>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                    $avg_rate_other = '';
                                    $avg_rate_aasam1 = '';
                                    if($bookings_brand_product_place1['avg_rate_other'])
                                    {
                                        $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                        $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                        $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                        $loose_rate = round($loose_rate,2);
                                        $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                    }

                                    if($bookings_brand_product_place1['avg_rate_aasam'])
                                    {
                                        $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);
                                        $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                        $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;

                                        

                                        $loose_rate_aasam = round($loose_rate_aasam,2);
                                        $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                    }



                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['state_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 
                                    
                                    <td style="text-align:left; border-bottom:1px solid #000;border-right:1px solid #000;">'.$avg_rate_other.'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td> 
                                </tr>';
                            $sn++; }
                            $html_print .= '<tr style="color:red;"><td colspan="4"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td></td></tr>';
                            } else { 
                                $html_print .= '<tr><td colspan="7">No Record Found</td></tr>';
                            }
                        $html_print .= '</tbody>
                    </table>';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }
        elseif ($_GET['type']=='brand') 
        {
            $group_by  = array('brand_id','category_id');        
            $bookings_brand_product_place = $this->secondarybooking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
            $html_print = '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['weight'].'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                        $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_weight.'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['weight'].'</td>
                                        </tr>'; 
                                        }   
                                    } } 
                                $html_print .= '</table><table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                        <thead>
                            <tr><th colspan="6" style="text-align:center;border-bottom:1px solid #000;">Order summary based on brand and product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">S.No</th>   
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Brand</th>   
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Product</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Qty (Tins/Cartons)</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Weight (MT)</th>
                                <th style="text-align:left; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate'],2);
                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                    $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                    $loose_rate = round($loose_rate,2);

                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                     
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['weight'].'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate.' / '.$loose_rate.'</td> 
                                </tr>';
                            $sn++; }
                            $html_print .= '<tr style="color:red;"><td colspan="3"></td><td>Total</td><td >'.$weight_total_summary.'</td><td></td></tr>';
                            } else { 
                                $html_print .= '<tr><td colspan="6">No Record Found</td></tr>';
                            }
                        $html_print .= '</tbody>
                    </table>';
                    include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }
        elseif ($_GET['type']=='product') 
        {
            $group_by  = array('category_name');      
            $bookings_brand_product_place = $this->secondarybooking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
            $html_print = '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['weight'].'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                        $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_weight.'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['weight'].'</td>
                                        </tr>'; 
                                        }   
                                    } } 
                                $html_print .= '</table><table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <thead>
                                        <tr><th colspan="6" style="text-align:center;border-bottom:1px solid #000;">Order summary based on product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">S.No</th>    
                                            <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Product</th>
                                            <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Qty (Tins/Cartons)</th>
                                            <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Weight (MT)</th>
                                            <th style="text-align:left; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                                $avg_rate =  round($bookings_brand_product_place1['avg_rate'],2);
                                                $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                                $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                                $loose_rate = round($loose_rate,2);

                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['weight'].'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate.' / '.$loose_rate.'</td> 
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="2"></td><td>Total</td><td >'.$weight_total_summary.'</td><td></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="6">No Record Found</td></tr>';
                                        }
                                    $html_print .= '</tbody>
                                </table>';
                    include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }
        else
        {
            
            $html_print = '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($tot_sum_report_value['weight'],2).'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                        $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($locked_weight,2).'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($sum_report_value['weight'],2).'</td>
                                        </tr>'; 
                                        }   
                                    } } 
                                $html_print .= '</table>';
                        $group_by  = array('category_name');      
                        $bookings_brand_product_place = $this->secondarybooking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
                        $html_print .= '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;margin-top:20px;">
                                    <thead>
                                        <tr><th colspan="6" style="text-align:center;border-bottom:1px solid #000;">Order summary based on product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">S.No</th>    
                                            <th rowspan="2"  style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Product</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Qty <br> (Tins/Cartons)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle; ">Weight <br> (MT)</th>
                                            <th colspan="2"  style="text-align:center; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                        <tr>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                                $avg_rate_other = '';
                                                $avg_rate_aasam1 = '';
                                                
                                                if($bookings_brand_product_place1['avg_rate_other'])
                                                {
                                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                                    $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                                    $loose_rate = round($loose_rate,2);
                                                    $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                                }
                                                if($bookings_brand_product_place1['avg_rate_aasam'])
                                                {
                                                    $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);


                                                    $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                                    $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15; 
                                                    $loose_rate_aasam = round($loose_rate_aasam,2);
                                                    $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                                }

                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$avg_rate_other.'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td> 
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="2"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td colspan="2"></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="7">No Record Found</td></tr>';
                                        }
                                    $html_print .= '</tbody>
                                </table>';


                        $group_by  = array('brand_id','category_id');        
                        $bookings_brand_product_place = $this->secondarybooking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);

                        $html_print .= '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%; margin-top:20px; ">
                                    <thead>
                                        <tr><th colspan="7" style="text-align:center;border-bottom:1px solid #000;">Order summary based on brand and product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">S.No</th>   
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Brand</th>   
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Product</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Qty <br>(Tins/Cartons)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Weight  <br> (MT)</th>
                                            <th colspan="2" style="text-align:center; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                        <tr>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                                $avg_rate_other = '';
                                                $avg_rate_aasam1 = '';
                                                if($bookings_brand_product_place1['avg_rate_other'])
                                                {
                                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                                    $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                                    $loose_rate = round($loose_rate,2);
                                                    $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                                }

                                                if($bookings_brand_product_place1['avg_rate_aasam'])
                                                {
                                                    $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);
                                                  

                                                    $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                                    $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;


                                                    $loose_rate_aasam = round($loose_rate_aasam,2);
                                                    $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                                }
                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$avg_rate_other.'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td>  
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="3"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="6">No Record Found</td></tr>';
                                        }
                                    $html_print .= '</tbody>
                                </table>';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }

    }

    public function updatestatus(){  
        $time = date('Y-m-d H:i:s');
        $update_data = array('status' => $_POST['status']);
        if($_POST['status']==3 || $_POST['status']==2 || $_POST['status']==0)
        {
            $update_data['approve_reject_time'] = $time;
            $update_data['reject_remark'] = trim($_POST['remark']); 

            if($_POST['status']==0)
            {
                $update_data['is_lock'] = 0;
            }
        }
        else
        {
            $update_data['check_time'] = $time;
        }


        $condition  = array('id' => base64_decode($_POST['booking_id']));
        echo $result= $this->secondarybooking_model->UpdateBooking($update_data,$condition);
    }


    public function details(){  
       
        $condition  = array('id' => base64_decode($_POST['booking_id']));
        $approve_reject_time= $this->secondarybooking_model->Bookingdetils($condition);
        echo $approve_reject_time = date("d-m-Y H:i:s", strtotime($approve_reject_time));
    } 
    
    public function status_update(){    
        $data['title'] = "";
        $status =  $this->uri->segment(3);
        $category_id =  base64_decode($this->uri->segment(4)); 
        $update_data = array('is_enable' => $status);
        $condition  = array('id' => $category_id);
        $result= $this->category_model->UpdateCategory($update_data,$condition);
        if($result)
            $this->session->set_flashdata('suc_msg','Category updated successfully.');
        else
            $this->session->set_flashdata('err_msg','Something went wrong.');
        redirect('category');
    } 
 

    public function edit_category(){   
        $data['title'] = "Update Product";
        $category_id =  base64_decode($this->uri->segment(3));
        $data['product'] = $this->category_model->GetCategoryByCategoryId($category_id);
        $old_hsn  = $data['product']['hsn'];

        if($category_id)
        {
            $condition = array('id' => $category_id);
            if(!empty($_POST))
            { 
                $name = $this->input->post('name'); 
                $brand = $this->input->post('brand'); 
                $is_enable = $this->input->post('is_enable'); 
                $sort_order = $this->input->post('sort_order'); 
                $hsn = $this->input->post('hsn');   


                $this->form_validation->set_rules('name', 'Product Name','required');
                $this->form_validation->set_rules('brand', 'Brand','required');
                $this->form_validation->set_rules('is_enable', 'Enable','required');
                $this->form_validation->set_rules('sort_order', 'Sort Order','required');
                $this->form_validation->set_rules('hsn', 'HSN','required');


                if ($this->form_validation->run() == false) {
                }
                else { 
                    $updatedata = array('category_name' =>$name,'brand_id' =>$brand,'is_enable' =>$is_enable,'sort_order' =>$sort_order,'hsn' =>$hsn);
                    $result = $this->category_model->UpdateCategory($updatedata,$condition);
                    if($result)
                        $this->session->set_flashdata('suc_msg','Category updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    redirect('category');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die;
            $this->load->model('brand_model'); 
            $data['brands'] = $this->brand_model->GetAllBrand(); 
        }
        else
        {
            redirect('category');
        }
        $this->load->view('category_edit',$data);
    }  

     
 
    

    public function delete_category(){
        $data['title'] = "Category EDelete";
        $category_id = base64_decode($this->uri->segment(3));
        if($category_id)
        {
            $condition = array('id' =>$category_id);
            $result = $this->category_model->DeleteCategory($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Category deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('category');  
    }


    public function getbargainweight()
    {
        $party_id = $_POST['party_id'];
        $condition  = array('party_id'=>$party_id,'is_mail'=>0,'is_lock'=>1,'status'=>2);
        $weight = $this->secondarybooking_model->getbargainweight($condition); 
        $pending_condition  = array('party_id'=>$party_id,'is_mail'=>0,'is_lock'=>0,'status <> '=>3,);
        $bargains = $this->secondarybooking_model->getpenidngbargain($pending_condition); 

        //echo "<pre>"; print_r($bargains); die;
        echo $weight['weight'].'__'.$bargains;
        //echo "<pre>"; print_r($weight); die;
    }


    public function GetBookingInfoDetailsPdf_19_01(){ 
        $_POST['booking_id'] = 18;
        $skus_pi_data = array();
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
         
 

        $bookings = $this->secondarybooking_model->GetBookingInfoById($_POST['booking_id']);        
        $condition = array('secondary_booking_skus.secondary_booking_id' => $bookings['secondary_booking_id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition); 

        $updated = $this->secondarybooking_model->updatebargiansmail($_POST['booking_id']); 

        //echo "<pre>"; print_r($skus); die; 
        $secondary_booking_id =  $bookings['secondary_booking_id'];
        $party_name = $bookings['party_name'];
        $party_id = $bookings['party_id'];
        $city_name = $bookings['city_name'];
        $total_weight = $bookings['total_weight'];
        $distributor_name = $bookings['distributor_name'];
        $distributor_city_name = $bookings['distributor_city_name'];
        $cnf_party = 0;//$bookings['cnf'];
        $cnf_party_enable = $bookings['cnf'];

        $vendor_address = $bookings['vendor_address'];
        $vendor_gst_no = $bookings['vendor_gst_no'];
        $vendor_zipcode = $bookings['vendor_zipcode'];
        $vendor_mobile = $bookings['vendor_mobile'];
        $vendor_state_name =  $bookings['state_name'];

        $distributor_state_name = $bookings['distributor_state_name'];
        $distributors_address = $bookings['distributors_address'];
        $distributors_gst_no = $bookings['distributors_gst_no'];
        $distributors_zipcode = $bookings['distributors_zipcode'];
        $distributors_mobile = $bookings['distributors_mobile'];

        $distributors_email = $bookings['distributors_email'];
        $vendors_email = $bookings['vendors_email'];
        $sales_executive_name = $bookings['sales_executive_name']; 
        $sales_executive_mobile = $bookings['sales_executive_mobile'];
        $maker_mobile = $bookings['maker_mobile'];
        $maker_email = $bookings['maker_email'];
        $booking_date = date("d-m-Y", strtotime($bookings['created_at'])); 
        $remarks = $bookings['remark'];
        $booking_id_base = base64_encode($bookings['id']);


        $header_html = '
            <div style="margin: 0px; padding: 0px; height: 100%; width: 100%;">
                <table width="100%" border="0" align="center" cellpadding="0" bgcolor="#ffffff" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px;  padding:20px; font-size:14px; font-family:arial, verdana, tahoma">
                    <tr>
                        <td style="margin:0px; padding:20px; text-align:center; border-collapse:collapse; background:#ffffff;" valign="top">
                            <table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto;  padding:0px;">
                                <tr>
                                    <td colspan="3" style="margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <h5 style="font-size:20px; line-height:38px; color:#202020; font-weight:bold; margin:0px; padding:0px;">'.$bookings['party_name'].' - '.$bookings['city_name'].'</h5>
                                        <p style="font-size:12px; color:#202020; margin:0px; padding:0px;">'.$vendor_address.' '.$vendor_state_name.' '.$vendor_zipcode.'</p>
                                    </td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:12px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"><strong>PI</strong></td>
                                </tr>
                                <tr>
                                    <td style=" margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$vendor_gst_no.'</p>
                                    </td>
                                    <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PAN No. : </p>
                                    </td>
                                    <td style="white-space:nowrap; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">CIN No. : </p>
                                    </td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:10px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"> REVERSE CHARGE :  </td>
                                </tr>
                                <tr>
                                    <td style=" width:200px;margin:0px; padding:10px 0 5px 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Invoice No: #DATA/SEC/'.$secondary_booking_id.'</p>
                                    </td>
                                    <td colspan="2" style="margin:0px; padding:10px 10px 5px 10px; line-height:20px;  text-align:center; border-collapse:collapse;" valign="top">
                                        <p style="font-size:14px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PERFORMA INVOICE</p>
                                    </td>
                                    <td style="width:200px;margin:0px; padding:10px 0 5px 0; font-size:12px;  font-weight:bold; text-align:right; border-collapse:collapse;" valign="top"> Date: '.strtoupper(date('d M Y')).' </td>
                                </tr>
                                <tr>
                                    <td style="width:50%; margin:0px; padding:5px 10px 10px 0; line-height:20px; border-top:1px solid #000000;  border-bottom:1px solid #000000;  text-align:left; border-collapse:collapse;" valign="top" colspan="2">
                                        <p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Invoice To</p>
                                        <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.ucwords($distributor_name).' '.$distributor_city_name.'</p>
                                        <p style="font-size:12px; font-weight:normal; color:#202020; margin:0px; padding:0px;">'.$distributors_address.' '.$distributor_city_name.' '.$distributor_state_name.' '.$distributors_zipcode.'</p>
                                        <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.ucwords($distributor_name).' '.$distributor_city_name.', State :'.$distributor_state_name.', PIN
                                        </p>
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$distributors_gst_no.' </p>
                                    </td>
                                    <td  style="margin:0px; border-top:1px solid #000000; border-left:1px solid #000000;  border-bottom:1px solid #000000;   padding:5px 0px 10px 10px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top" colspan="2">
                                        <p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Ship To</p>
                                        <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">As Invoiced to</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="margin:0px; padding:5px 10px 10px 0; line-height:20px; text-align:left; border-collapse:collapse;" valign="top" colspan="4">
                                        <table width="100%" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                                            <tr>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Transport: </td>
                                                <td colspan="3" style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Supply Place: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$distributor_city_name.' '.$distributor_state_name.' </td>
                                            </tr>
                                            <tr>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Vehicle No: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Credit Days: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Cust. Ref.: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                            </tr>
                                            <tr>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">GR / LR No. </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">GR/LR Date: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Broker: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                            </tr>
                                            <tr>
                                                <td  style="margin:0px; padding:10px 5px 0 0; line-height:20px; font-size:14px;  text-align:left; border-collapse:collapse; font-weight:bold;" valign="top">e WayBill No. </td>
                                                <td colspan="2" style="margin:0px; padding:10px 5px 0 0; font-size:14px;  line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td colspan="3" style="margin:0px; padding:10px 5px 0 0; line-height:20px;  text-align:left; font-size:10px; border-collapse:collapse;" valign="top"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <div>';            
            $footerHtml = '<table  style="width:100%; ">
                        <tr>
                          <td style="vertical-align:bottom; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"><span >Page {PAGENO} of {nbpg}</span></td>
                          <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:12px;" valign="top">For '.ucwords($distributor_name).'  
                          <br><br><br>
                            <p style="display:block; margin:0px; padding:0px 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:11px;">Authorised Signatory
                            <br>*This is system generated PI, no signature required. <br> PI is subject to change at the time of invoice.</p>
                          </td>
                        </tr>
                      </table>'; 


            $html_response = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">
                    <tr>
                      <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. </td>
                      <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Item Name </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Wt.(MT) </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">HSN </td> 
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty. (NOS) </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; font-weight:bold;  line-height:20px;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Rate </td> 
                      <td  style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Amount Rs. </td>
                    </tr>';
                
                $total_invoice_amount = 0;
                                        $total_invoice_weight = 0;
                                        $total_invoice_qty = 0;                                 
                                        $sno = 1;
                                        $sku_rate_total  = 0;
                                        $total_amount = 0; 
                                        $hsns = array() ;
                                        $invoice_nos = array();
                                        $invoice_nos_pi = array();
                                        $sku_nos_pi = array(); 
                                        foreach ($skus as $key => $value) { 
                                            $rate = $value['rate'];
                                            if($cnf_party_enable)
                                            {
                                              $rate =  ($rate*100)/105;
                                            }
                                            $amount = $rate*$value['quantity']*$value['packing_items_qty'];
                                            $total_amount = $total_amount+$amount;
                                            //$total_invoice_qty = $total_invoice_qty+$value['quantity'];
                                            $sku_total_with_gst =$amount;

                                            $invoice_nos_pi[] = $bookings['secondary_booking_id'];
                                            $sku_nos_pi[] = $value['id'];
                                            $invoice_nos[$value['secondary_bargain_id']] = 'SEC/DATA/'.$bookings['secondary_booking_id']; 
                                            $sku_rate = $rate;

                                            $html_response .= '<tr>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['brand_name'].' '.$value['category_name'].' '.$value['name'].'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['weight'].'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['hsn'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.number_format(round($sku_rate*$value['packing_items_qty'],3),2).'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.number_format(round($sku_total_with_gst,2),2).'</td>
                                            </tr> ';
                                            $total_invoice_amount = $total_invoice_amount+$sku_total_with_gst;
                                            $total_invoice_weight = $total_invoice_weight+$value['weight'];
                                            $total_invoice_qty = $total_invoice_qty+$value['quantity'];
                                            $sno++;
         

                                            if(array_key_exists($value['hsn'],$hsns))
                                            {
                                                $hsns[$value['hsn']] = $hsns[$value['hsn']]+$sku_total_with_gst;
                                            }
                                            else
                                            {
                                                $hsns[$value['hsn']] = $sku_total_with_gst;
                                            }
                                        } 
                                        $html_response .= '<tr>
                                          <td colspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" valign="top">Items Total </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000; " valign="top">'.number_format($total_invoice_weight,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.$total_invoice_qty.'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" colspan="" valign="top">Taxable Amount </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_amount,2).'</td>
                                        </tr>';
                $html_response .= '</table>';

                $state_id = $bookings['distributors_state_id'];


                if($distributor_state_name==$vendor_state_name)
                {
                    if($cnf_party)
                    {
                        $SGST = 0.00;
                        $CGST = 0.00;
                        $IGST  = 0.00;
                    }
                    else
                    {
                        $SGST = 2.5;
                        $CGST = 2.5;
                        $IGST  = 0.00;
                    }
                  $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                  $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                  $igst_amount = 0;
                }
                else
                {
                    if($cnf_party)
                    {
                        $SGST = 0.00;
                        $CGST = 0.00;
                        $IGST  = 0.00;
                    }
                    else
                    {
                        $SGST = 0.0;
                        $CGST = 0.0;
                        $IGST  = 5.00;
                    }
                  $sgst_amount = 0;
                  $cgst_amount = 0;
                  $igst_amount = round((($total_invoice_amount*$IGST)/100),2);
                }
                $total_amount_without_gst = $total_invoice_amount-($sgst_amount+$cgst_amount+$igst_amount);
                $html_response .= '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                        <tr>
                          <td  colspan="6" rowspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; font-weight:bold; border-collapse:collapse;  " valign="top"> </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"></td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">CGST </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.number_format($cgst_amount,2).'</td>
                        </tr>
                        <tr>
                          <td colspan="6" rowspan="7" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left;  border-collapse:collapse;  " valign="top"><h5 style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:center;">HSN WISE GST PAYABLE DETAILS</h5>
                            <table  border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; text-align:center; margin:0px auto; font-size:10px;  padding:0px;">';
                            
                              $html_response .= '<tr>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> HSN</td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> TAXABLE AMT </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> SGST% </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> SGST AMT </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> CGST% </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> CGST AMT </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> IGST% </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> IGST AMT </td>
                              </tr>';
                            foreach ($hsns as $key => $hsn_amount) { 

                                $state_id = $bookings['distributors_state_id'];
                                if($distributor_state_name==$vendor_state_name)
                                {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 2.5;
                                        $CGST = 2.5;
                                        $IGST  = 0.00;
                                    }
                                    $sgst_amount = round(((($hsn_amount*$SGST)/100)),2);
                                    $cgst_amount = round(((($hsn_amount*$CGST)/100)),2);
                                    $igst_amount = 0;
                                }
                              else
                              {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 5.00;
                                    }
                                $sgst_amount = 0;
                                $cgst_amount = 0;
                                $igst_amount = round((($hsn_amount*$IGST)/100),2);
                              }
                              $total_amount_without_gst = $hsn_amount-($sgst_amount+$cgst_amount+$igst_amount);
                              $html_response .= '<tr>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.$key.' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($hsn_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($SGST,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($sgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($CGST,2).'  </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($cgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($IGST,2).' </td>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 ; border-right:1px solid #000000 " valign="top"> '.number_format($igst_amount,2).' </td>
                              </tr>';
                            }

                                $state_id = $bookings['distributors_state_id'];
                                if($distributor_state_name==$vendor_state_name)
                                {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 2.5;
                                        $CGST = 2.5;
                                        $IGST  = 0.00;
                                    }
                                        $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                                        $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                                        $igst_amount = 0;
                                }
                              else
                              {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST = 5.00;
                                    }
                                $sgst_amount = 0;
                                $cgst_amount = 0;
                                $igst_amount = round((($total_invoice_amount*$IGST)/100),2);
                              }
                              $total_amount_without_gst = $total_invoice_amount-($sgst_amount+$cgst_amount+$igst_amount);
                            $html_response .= '  <tr>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> TOTAL </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($total_invoice_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($SGST,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($sgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($CGST,2).'  </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($cgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($IGST,2).' </td>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 ; border-right:1px solid #000000 " valign="top"> '.number_format($igst_amount,2).' </td>
                              </tr>
                            </table>
                            <p style="margin:0px; padding:0px;">1. No quality complaints shall be entertained after two days of receipt of material.</p>
                            <p style="margin:0px; padding:0px;">2. All disputes subject to >'.$bookings['party_name'].' Jurisdiction only.</p>
                            <p style="margin:0px; padding:0px;">3. I/We hereby certify that food/foods mentioned in this invoice is/are warranted to be of the nature and quality which it/these purports/purported to be.</p></td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">SGST </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.number_format($sgst_amount,2).' </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">IGST </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.number_format($igst_amount,2).'</td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Forwarding </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"> </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Other Charges </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Misc Charges </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Round Off </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td colspan="3" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"  valign="top">Insurance Policy:   <br>
                            Freight to Drive: 
                           </td>
                          <td  colspan="5" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-size:10px; font-weight:bold" valign="top"></td>
                          <td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-size:14px;" valign="top"></td>
                        </tr>
                        <tr>
                          <td colspan="6" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:15px;"  valign="top"></td>
                          <td  colspan="2" style="width: 152px; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">For Grand Total</td>'; 
                            $gst = ((round($total_invoice_amount,2))*5)/100;
                            if($cnf_party)
                                $gst = ((round($total_invoice_amount,2))*0)/100;
                           $gross_toatl  = round($total_invoice_amount,2)+$gst;
                           $amount_in_words = $this->convert_number(round($gross_toatl,2));
                            $html_response .= '<td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">'.number_format(round($gross_toatl,2),2).'</td>
                        </tr> 
                        <tr>
                          <td  colspan="6" rowspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; font-weight:bold; border-collapse:collapse;  " valign="top">Amount in Words : '.$amount_in_words.' </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"></td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                        </tr>                                 
                    </table>';    
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','0','0','100','25','0','0');
            //echo $html_response; die; 
            //echo $html_response; die;
            $mpdf->SetHTMLHeader($header_html);
            $mpdf->SetHTMLFooter($footerHtml);   
            $mpdf->WriteHTML($html_response);
            $f_name = $distributor_name.$secondary_booking_id.'.pdf';
            $f_name = str_replace(' ', '-', $f_name);
            $invoice_file = $f_name;
            $pdf_file = base_url().'invoices/secondary/'.$f_name;
            $f_name = FCPATH.'/invoices/secondary/'.$f_name;  
              
            addlog("PI generated for  Secondary Booking DATA/SEC/".$secondary_booking_id);

            $mpdf->Output($f_name,'F');  
            include 'mailer/email.php'; 
            $from = "webmaster@dil.in";
            $from_name = $bookings['party_name'];
            $subject   = 'Supply to '.$bookings['distributor_name'].' - '.$bookings['distributor_city_name']; 
            $email = $bookings['email'];
            $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong><br><br><br>* This is system generated email please do not reply.</strong>';
            $cc= '';
            $bcc= '';            
            if($distributors_email)
                    $cc= $distributors_email;  
            smtpmailer($email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            if($vendors_email)
            { 
                $approval_link = 'Plase check attached invoice and approve the order by clicking the below Link. <br> <a href="'.base_url().'approve/index/'.$booking_id_base.'">Click Here</a>';
                $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong>'.$approval_link.'<br><br><br>* This is system generated email please do not reply.</strong>'; 
                $cc= '';
                $bcc= ''; 
                smtpmailer($vendors_email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            }
 
            $message_params = urlencode($distributor_name.'~'.$bookings['party_name'].'~'.$sales_executive_name);
            
            $curl_watsappapi = curl_init();

            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$maker_mobile.'&TID=8909629&P='.$message_params.'&PATH='.$pdf_file,
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

            $pi_invoice['sku_ids'] = implode(',', $sku_nos_pi);  
            $pi_invoice['created_by'] =$admin_id;  
            $pi_invoice['booking_id'] =$_POST['booking_id'];  
            $pi_invoice['pi_amount'] =$gross_toatl;  
            $pi_invoice['invoice_file'] = $invoice_file;
            $pi_invoice['party_id'] = $party_id;
            $pi_invoice['total_weight_pi'] =number_format($total_invoice_weight,2);  
            $pi_invoice_number = $this->secondarybooking_model->AddPiHistory($pi_invoice);
            
            $updatedata = array('pi_id' => $pi_invoice_number);
            $condition_sku_update = array('sku_ids' => $pi_invoice['sku_ids']);
            $this->secondarybooking_model->UpdateSecondaryBookingSkuPiStatus($updatedata,$condition_sku_update);

            $updatedata = array('pi_id' => $pi_invoice_number);
            $condition_sku_update = array('id' => $_POST['booking_id']);
            $this->secondarybooking_model->UpdateBooking($updatedata,$condition_sku_update);
            
    }

    public function GetBookingInfoDetailsPdf(){ 
        $skus_pi_data = array();
        //$_POST['booking_id'] = 18;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
         
 

        $bookings = $this->secondarybooking_model->GetBookingInfoById($_POST['booking_id']);        
        //$condition = array('secondary_booking_skus.secondary_booking_id' => $bookings['secondary_booking_id']);
		$condition = array('secondary_booking_skus.secondary_booking_id' => $bookings['id']);
        $skus = $this->secondarybooking_model->GetSkuinfo($condition); 

        $updated = $this->secondarybooking_model->updatebargiansmail($_POST['booking_id']); 

        //echo "<pre>"; print_r($bookings); die; 
        $secondary_booking_id =  $bookings['secondary_booking_id'];
        $party_name = $bookings['party_name'];
        $party_id = $bookings['party_id'];
        $city_name = $bookings['city_name'];
        $total_weight = $bookings['total_weight'];
        $distributor_name = $bookings['distributor_name'];
        $distributor_city_name = $bookings['distributor_city_name'];
        $cnf_party = 0;//$bookings['cnf'];
        $cnf_party_enable = $bookings['cnf'];

        $vendor_address = $bookings['vendor_address'];
        $vendor_gst_no = $bookings['vendor_gst_no'];
        $vendor_zipcode = $bookings['vendor_zipcode'];
        $vendor_mobile = $bookings['vendor_mobile'];
        $vendor_state_name =  $bookings['state_name'];

        $distributor_state_name = $bookings['distributor_state_name'];
        $distributors_address = $bookings['distributors_address'];
        $distributors_gst_no = $bookings['distributors_gst_no'];
        $distributors_zipcode = $bookings['distributors_zipcode'];
        $distributors_mobile = $bookings['distributors_mobile'];

        $distributors_email = $bookings['distributors_email'];
        $vendors_email = $bookings['vendors_email'];
        $sales_executive_name = $bookings['sales_executive_name']; 
        $sales_executive_mobile = $bookings['sales_executive_mobile'];
        $maker_mobile = $bookings['maker_mobile'];
        $maker_email = $bookings['maker_email'];
        $booking_date = date("d-m-Y", strtotime($bookings['created_at'])); 
        $remarks = $bookings['remark'];
        $booking_id_base = base64_encode($bookings['id']);


        $pi_invoice_file = $bookings['invoice_file'];
        if($pi_invoice_file)
        { 
            include 'mailer/email.php'; 
            $from = "webmaster@dil.in";
            $from_name = $bookings['party_name'];
            $subject   = 'Supply to '.$bookings['distributor_name'].' - '.$bookings['distributor_city_name']; 
            $email = $bookings['email'];
            $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong><br><br><br>* This is system generated email please do not reply.</strong>';
            $cc= '';
            $bcc= '';            
            if($distributors_email)
                    $cc= $distributors_email;  
            smtpmailer($email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            if($vendors_email)
            { 
                $approval_link = 'Plase check attached invoice and approve the order by clicking the below Link. <br> <a href="'.base_url().'approve/index/'.$booking_id_base.'">Click Here</a>';
                $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong>'.$approval_link.'<br><br><br>* This is system generated email please do not reply.</strong>'; 
                $cc= '';
                $bcc= ''; 
                smtpmailer($vendors_email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            }
 
            $message_params = urlencode($distributor_name.'~'.$bookings['party_name'].'~'.$sales_executive_name);
            
            $curl_watsappapi = curl_init();
            $pdf_file = base_url().'invoices/secondary/'.$pi_invoice_file;
            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$maker_mobile.'&TID=8909629&P='.$message_params.'&PATH='.$pdf_file,
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
        { 
                       
            $footerHtml = '<table  style="width:100%; ">
                        <tr>
                          <td style="vertical-align:bottom; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"><span >Page {PAGENO} of {nbpg}</span></td>
                          <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:12px;" valign="top">For '.ucwords($distributor_name).'  
                          <br><br><br>
                            <p style="display:block; margin:0px; padding:0px 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:11px;">Authorised Signatory
                            <br>*This is system generated PI, no signature required. <br> PI is subject to change at the time of invoice.</p>
                          </td>
                        </tr>
                      </table>'; 


            $html_response = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">
                    <tr>
                      <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. </td>
                      <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Item Name </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Wt.(MT) </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">HSN </td> 
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty. (NOS) </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; font-weight:bold;  line-height:20px;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Rate </td> 
                      <td  style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Amount Rs. </td>
                    </tr>';
                
                $total_invoice_amount = 0;
                                        $total_invoice_weight = 0;
                                        $total_invoice_qty = 0;                                 
                                        $sno = 1;
                                        $sku_rate_total  = 0;
                                        $total_amount = 0; 
                                        $hsns = array() ;
                                        $invoice_nos = array();
                                        $invoice_nos_pi = array();
                                        $sku_nos_pi = array(); 
                                        foreach ($skus as $key => $value) { 
                                            $rate = $value['rate'];
                                            if($cnf_party_enable)
                                            {
                                              $rate =  ($rate*100)/105;
                                            }
                                            $amount = $rate*$value['quantity']*$value['packing_items_qty'];
                                            $total_amount = $total_amount+$amount;
                                            //$total_invoice_qty = $total_invoice_qty+$value['quantity'];
                                            $sku_total_with_gst =$amount;

                                            $invoice_nos_pi[] = $bookings['secondary_booking_id'];
                                            $sku_nos_pi[] = $value['id'];
                                            $invoice_nos[$value['secondary_bargain_id']] = 'SEC/DATA/'.$bookings['secondary_booking_id']; 
                                            $sku_rate = $rate;

                                            $html_response .= '<tr>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['brand_name'].' '.$value['category_name'].' '.$value['name'].'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['weight'].'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['hsn'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.number_format(round($sku_rate*$value['packing_items_qty'],3),2).'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.number_format(round($sku_total_with_gst,2),2).'</td>
                                            </tr> ';
                                            $total_invoice_amount = $total_invoice_amount+$sku_total_with_gst;
                                            $total_invoice_weight = $total_invoice_weight+$value['weight'];
                                            $total_invoice_qty = $total_invoice_qty+$value['quantity'];
                                            $sno++;
         

                                            if(array_key_exists($value['hsn'],$hsns))
                                            {
                                                $hsns[$value['hsn']] = $hsns[$value['hsn']]+$sku_total_with_gst;
                                            }
                                            else
                                            {
                                                $hsns[$value['hsn']] = $sku_total_with_gst;
                                            }

                                            $skus_pi_data[] = array(
                                                'brand_id' => $value['brand_id'],
                                                'category_id' => $value['category_id'],
                                                'product_id' => $value['product_id'],
                                                'amount' => $sku_total_with_gst,
                                                'weight' => $value['weight'],
                                                'quantity' => $value['quantity'],
                                                'booked_by' => $value['booked_by'],
                                                'booking_id' => $value['secondary_booking_id'],
                                            );
                                        }  
                                        $html_response .= '<tr>
                                          <td colspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" valign="top">Items Total </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000; " valign="top">'.number_format($total_invoice_weight,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.$total_invoice_qty.'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" colspan="" valign="top">Taxable Amount </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_amount,2).'</td>
                                        </tr>';
                $html_response .= '</table>';

                $state_id = $bookings['distributors_state_id'];


                if($distributor_state_name==$vendor_state_name)
                {
                    if($cnf_party)
                    {
                        $SGST = 0.00;
                        $CGST = 0.00;
                        $IGST  = 0.00;
                    }
                    else
                    {
                        $SGST = 2.5;
                        $CGST = 2.5;
                        $IGST  = 0.00;
                    }
                  $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                  $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                  $igst_amount = 0;
                }
                else
                {
                    if($cnf_party)
                    {
                        $SGST = 0.00;
                        $CGST = 0.00;
                        $IGST  = 0.00;
                    }
                    else
                    {
                        $SGST = 0.0;
                        $CGST = 0.0;
                        $IGST  = 5.00;
                    }
                  $sgst_amount = 0;
                  $cgst_amount = 0;
                  $igst_amount = round((($total_invoice_amount*$IGST)/100),2);
                }
                $total_amount_without_gst = $total_invoice_amount-($sgst_amount+$cgst_amount+$igst_amount);
                $html_response .= '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                        <tr>
                          <td  colspan="6" rowspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; font-weight:bold; border-collapse:collapse;  " valign="top"> </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"></td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">CGST </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.number_format($cgst_amount,2).'</td>
                        </tr>
                        <tr>
                          <td colspan="6" rowspan="7" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left;  border-collapse:collapse;  " valign="top"><h5 style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:center;">HSN WISE GST PAYABLE DETAILS</h5>
                            <table  border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; text-align:center; margin:0px auto; font-size:10px;  padding:0px;">';
                            
                              $html_response .= '<tr>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> HSN</td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> TAXABLE AMT </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> SGST% </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> SGST AMT </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> CGST% </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> CGST AMT </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> IGST% </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse;  " valign="top"> IGST AMT </td>
                              </tr>';
                            foreach ($hsns as $key => $hsn_amount) { 

                                $state_id = $bookings['distributors_state_id'];
                                if($distributor_state_name==$vendor_state_name)
                                {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 2.5;
                                        $CGST = 2.5;
                                        $IGST  = 0.00;
                                    }
                                    $sgst_amount = round(((($hsn_amount*$SGST)/100)),2);
                                    $cgst_amount = round(((($hsn_amount*$CGST)/100)),2);
                                    $igst_amount = 0;
                                }
                              else
                              {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 5.00;
                                    }
                                $sgst_amount = 0;
                                $cgst_amount = 0;
                                $igst_amount = round((($hsn_amount*$IGST)/100),2);
                              }
                              $total_amount_without_gst = $hsn_amount-($sgst_amount+$cgst_amount+$igst_amount);
                              $html_response .= '<tr>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.$key.' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($hsn_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($SGST,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($sgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($CGST,2).'  </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($cgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($IGST,2).' </td>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 ; border-right:1px solid #000000 " valign="top"> '.number_format($igst_amount,2).' </td>
                              </tr>';
                            }

                                $state_id = $bookings['distributors_state_id'];
                                if($distributor_state_name==$vendor_state_name)
                                {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST  = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 2.5;
                                        $CGST = 2.5;
                                        $IGST  = 0.00;
                                    }
                                        $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                                        $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                                        $igst_amount = 0;
                                }
                              else
                              {
                                    if($cnf_party)
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST = 0.00;
                                    }
                                    else
                                    {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST = 5.00;
                                    }
                                $sgst_amount = 0;
                                $cgst_amount = 0;
                                $igst_amount = round((($total_invoice_amount*$IGST)/100),2);
                              }
                              $total_amount_without_gst = $total_invoice_amount-($sgst_amount+$cgst_amount+$igst_amount);
                            $html_response .= '  <tr>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> TOTAL </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($total_invoice_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($SGST,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($sgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($CGST,2).'  </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 " valign="top"> '.number_format($cgst_amount,2).' </td>
                                <td   style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000  " valign="top"> '.number_format($IGST,2).' </td>
                                <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center;  border-collapse:collapse; border-left:1px solid #000000; border-top:1px solid #000000; border-bottom:1px solid #000000 ; border-right:1px solid #000000 " valign="top"> '.number_format($igst_amount,2).' </td>
                              </tr>
                            </table>
                            <p style="margin:0px; padding:0px;">1. No quality complaints shall be entertained after two days of receipt of material.</p>
                            <p style="margin:0px; padding:0px;">2. All disputes subject to >'.$bookings['party_name'].' Jurisdiction only.</p>
                            <p style="margin:0px; padding:0px;">3. I/We hereby certify that food/foods mentioned in this invoice is/are warranted to be of the nature and quality which it/these purports/purported to be.</p></td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">SGST </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.number_format($sgst_amount,2).' </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">IGST </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.number_format($igst_amount,2).'</td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Forwarding </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"> </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Other Charges </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Misc Charges </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Round Off </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                        </tr>
                        <tr>
                          <td colspan="3" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"  valign="top">Insurance Policy:   <br>
                            Freight to Drive: 
                           </td>
                          <td  colspan="5" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-size:10px; font-weight:bold" valign="top"></td>
                          <td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-size:14px;" valign="top"></td>
                        </tr>
                        <tr>
                          <td colspan="6" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:15px;"  valign="top"></td>
                          <td  colspan="2" style="width: 152px; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">For Grand Total</td>'; 
                            $gst = ((round($total_invoice_amount,2))*5)/100;
                            if($cnf_party)
                                $gst = ((round($total_invoice_amount,2))*0)/100;
                           $gross_toatl  = round($total_invoice_amount,2)+$gst;
                           $amount_in_words = $this->convert_number(round($gross_toatl,2));
                            $html_response .= '<td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">'.number_format(round($gross_toatl,2),2).'</td>
                        </tr> 
                        <tr>
                          <td  colspan="6" rowspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; font-weight:bold; border-collapse:collapse;  " valign="top">Amount in Words : '.$amount_in_words.' </td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"></td>
                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                        </tr>                                 
                    </table>';    
            
            $f_name = $distributor_name.$secondary_booking_id.'.pdf';
            $f_name = str_replace(' ', '-', $f_name);
            $invoice_file = $f_name;

            $pi_invoice['sku_ids'] = implode(',', $sku_nos_pi);  
            $pi_invoice['created_by'] =$admin_id;  
            $pi_invoice['booking_id'] =$_POST['booking_id'];  
            $pi_invoice['pi_amount'] =$gross_toatl;  
            $pi_invoice['invoice_file'] = $invoice_file;
            $pi_invoice['party_id'] = $party_id;
            $pi_invoice['total_weight_pi'] =number_format($total_invoice_weight,2);  
            $pi_invoice_number = $this->secondarybooking_model->AddPiHistory($pi_invoice);

            $header_html = '
            <div style="margin: 0px; padding: 0px; height: 100%; width: 100%;">
                <table width="100%" border="0" align="center" cellpadding="0" bgcolor="#ffffff" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px;  padding:20px; font-size:14px; font-family:arial, verdana, tahoma">
                    <tr>
                        <td style="margin:0px; padding:20px; text-align:center; border-collapse:collapse; background:#ffffff;" valign="top">
                            <table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto;  padding:0px;">
                                <tr>
                                    <td colspan="3" style="margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <h5 style="font-size:20px; line-height:38px; color:#202020; font-weight:bold; margin:0px; padding:0px;">'.$bookings['party_name'].' - '.$bookings['city_name'].'</h5>
                                        <p style="font-size:12px; color:#202020; margin:0px; padding:0px;">'.$vendor_address.' '.$vendor_state_name.' '.$vendor_zipcode.'</p>
                                    </td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:12px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"><strong>PI</strong></td>
                                </tr>
                                <tr>
                                    <td style=" margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$vendor_gst_no.'</p>
                                    </td>
                                    <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PAN No. : </p>
                                    </td>
                                    <td style="white-space:nowrap; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">CIN No. : </p>
                                    </td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:10px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"> REVERSE CHARGE :  </td>
                                </tr>
                                <tr>
                                    <td style=" width:200px;margin:0px; padding:10px 0 5px 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">
                                        <p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PI No: '.$pi_invoice_number.'</p>
                                    </td>
                                    <td colspan="2" style="margin:0px; padding:10px 10px 5px 10px; line-height:20px;  text-align:center; border-collapse:collapse;" valign="top">
                                        <p style="font-size:14px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PERFORMA INVOICE</p>
                                    </td>
                                    <td style="width:200px;margin:0px; padding:10px 0 5px 0; font-size:12px;  font-weight:bold; text-align:right; border-collapse:collapse;" valign="top"> Date: '.strtoupper(date('d M Y')).' </td>
                                </tr>
                                <tr>
                                    <td style="width:50%; margin:0px; padding:5px 10px 10px 0; line-height:20px; border-top:1px solid #000000;  border-bottom:1px solid #000000;  text-align:left; border-collapse:collapse;" valign="top" colspan="2">
                                        <p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Invoice To</p>
                                        <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.ucwords($distributor_name).' '.$distributor_city_name.'</p>
                                        <p style="font-size:12px; font-weight:normal; color:#202020; margin:0px; padding:0px;">'.$distributors_address.' '.$distributor_city_name.' '.$distributor_state_name.' '.$distributors_zipcode.'</p>
                                        <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.ucwords($distributor_name).' '.$distributor_city_name.', State :'.$distributor_state_name.', PIN
                                        </p>
                                        <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$distributors_gst_no.' </p>
                                    </td>
                                    <td  style="margin:0px; border-top:1px solid #000000; border-left:1px solid #000000;  border-bottom:1px solid #000000;   padding:5px 0px 10px 10px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top" colspan="2">
                                        <p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Ship To</p>
                                        <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">As Invoiced to</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="margin:0px; padding:5px 10px 10px 0; line-height:20px; text-align:left; border-collapse:collapse;" valign="top" colspan="4">
                                        <table width="100%" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                                            <tr>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Transport: </td>
                                                <td colspan="3" style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Supply Place: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$distributor_city_name.' '.$distributor_state_name.' </td>
                                            </tr>
                                            <tr>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Vehicle No: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Credit Days: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Cust. Ref.: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">#DATA/SEC/'.$secondary_booking_id.'</td>
                                            </tr>
                                            <tr>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">GR / LR No. </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">GR/LR Date: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Broker: </td>
                                                <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                            </tr>
                                            <tr>
                                                <td  style="margin:0px; padding:10px 5px 0 0; line-height:20px; font-size:14px;  text-align:left; border-collapse:collapse; font-weight:bold;" valign="top">e WayBill No. </td>
                                                <td colspan="2" style="margin:0px; padding:10px 5px 0 0; font-size:14px;  line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                                <td colspan="3" style="margin:0px; padding:10px 5px 0 0; line-height:20px;  text-align:left; font-size:10px; border-collapse:collapse;" valign="top"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <div>'; 

            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','0','0','100','25','0','0');
            //echo $html_response; die; 
            //echo $html_response; die;
            $mpdf->SetHTMLHeader($header_html);
            $mpdf->SetHTMLFooter($footerHtml);   
            $mpdf->WriteHTML($html_response);
            $f_name = $distributor_name.$secondary_booking_id.'.pdf';
            $f_name = str_replace(' ', '-', $f_name);
            $invoice_file = $f_name;
            $pdf_file = base_url().'invoices/secondary/'.$f_name;
            $f_name = FCPATH.'/invoices/secondary/'.$f_name;  
              
            addlog("PI generated for  Secondary Booking DATA/SEC/".$secondary_booking_id);

            $mpdf->Output($f_name,'F');
			 //die;
            include 'mailer/email.php'; 
            $from = "webmaster@dil.in";
            $from_name = $bookings['party_name'];
            $subject   = 'Supply to '.$bookings['distributor_name'].' - '.$bookings['distributor_city_name']; 
            $email = $bookings['email'];
            $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong><br><br><br>* This is system generated email please do not reply.</strong>';
            $cc= '';
            $bcc= '';            
            if($distributors_email)
                    $cc= $distributors_email;  
            smtpmailer($email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            if($vendors_email)
            { 
                $approval_link = 'Plase check attached invoice and approve the order by clicking the below Link. <br> <a href="'.base_url().'approve/index/'.$booking_id_base.'">Click Here</a>';
                $body_message = 'Find Atteched order to supplier to supply material as per your agreed terms with your disributer. <br> <strong>'.$approval_link.'<br><br><br>* This is system generated email please do not reply.</strong>'; 
                $cc= '';
                $bcc= ''; 
                smtpmailer($vendors_email, $from,$from_name,$subject, $body_message,$f_name,$cc,$bcc);
            }
 
            $message_params = urlencode($distributor_name.'~'.$bookings['party_name'].'~'.$sales_executive_name);
            
            $curl_watsappapi = curl_init();

            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$maker_mobile.'&TID=8909629&P='.$message_params.'&PATH='.$pdf_file,
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

            $pi_invoice['sku_ids'] = implode(',', $sku_nos_pi);  
            $pi_invoice['created_by'] =$admin_id;  
            $pi_invoice['booking_id'] =$_POST['booking_id'];  
            $pi_invoice['pi_amount'] =$gross_toatl;  
            $pi_invoice['invoice_file'] = $invoice_file;
            $pi_invoice['party_id'] = $party_id;
            $pi_invoice['total_weight_pi'] =number_format($total_invoice_weight,2);  
            //$pi_invoice_number = $this->secondarybooking_model->AddPiHistory($pi_invoice);
            

            $updated_skus_pi_data = array();
            if($skus_pi_data)
            {
                foreach ($skus_pi_data as $skus_pi_data_key => $skus_pi_data_value) {
                    $updated_skus_pi_data[$skus_pi_data_key] = $skus_pi_data_value;
                    $updated_skus_pi_data[$skus_pi_data_key]['pi_number'] = $pi_invoice_number;
                    $updated_skus_pi_data[$skus_pi_data_key]['party_id'] = $party_id;
                }
                $this->secondarybooking_model->AddPiSkuHistorysecondary_booking($updated_skus_pi_data);
            }


            $updatedata = array('pi_id' => $pi_invoice_number);
            $condition_sku_update = array('sku_ids' => $pi_invoice['sku_ids']);
            $this->secondarybooking_model->UpdateSecondaryBookingSkuPiStatus($updatedata,$condition_sku_update);

            $updatedata = array('pi_id' => $pi_invoice_number);
            $condition_sku_update = array('id' => $_POST['booking_id']);
            $this->secondarybooking_model->UpdateBooking($updatedata,$condition_sku_update);
        } 
            
    }

    public function convert_number($number){ 
      $hyphen      = '-';
      $conjunction = ' and ';
      $separator   = ', ';
      $negative    = 'negative ';
      $decimal     = ' and ';
      $dictionary  = array(
          0                   => 'zero',
          1                   => 'One',
          2                   => 'Two',
          3                   => 'Three',
          4                   => 'Four',
          5                   => 'Five',
          6                   => 'Six',
          7                   => 'Seven',
          8                   => 'Eight',
          9                   => 'Nine',
          10                  => 'Ten',
          11                  => 'Eleven',
          12                  => 'Twelve',
          13                  => 'Thirteen',
          14                  => 'Fourteen',
          15                  => 'Fifteen',
          16                  => 'Sixteen',
          17                  => 'Seventeen',
          18                  => 'Eighteen',
          19                  => 'Nineteen',
          20                  => 'Twenty',
          30                  => 'Thirty',
          40                  => 'Fourty',
          50                  => 'Fifty',
          60                  => 'Sixty',
          70                  => 'Seventy',
          80                  => 'Eighty',
          90                  => 'Ninety',
          100                 => 'Hundred',
          1000                => 'Thousand',
          1000000             => 'Million',
          1000000000          => 'Billion',
          1000000000000       => 'Trillion',
          1000000000000000    => 'Quadrillion',
          1000000000000000000 => 'Quintillion'
      );
      if (!is_numeric($number)) {
          return false;
      }
      if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
          // overflow
          trigger_error(
              'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
              E_USER_WARNING
          );
          return false;
      }
      if ($number < 0) {
          return $negative . $this->convert_number(abs($number));
      }
      $string = $fraction = null;
      if (strpos($number, '.') !== false) {
          list($number, $fraction) = explode('.', $number);
      }
      switch (true) {
          case $number < 21:
              $string = $dictionary[$number];
              break;
          case $number < 100:
              $tens   = ((int) ($number / 10)) * 10;
              $units  = $number % 10;
              $string = $dictionary[$tens];
              if ($units) {
                  $string .= $hyphen . $dictionary[$units];
              }
              break;
          case $number < 1000:
              $hundreds  = $number / 100;
              $remainder = $number % 100;
              $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
              if ($remainder) {
                  $string .= $conjunction . $this->convert_number($remainder);
              }
              break;
          default:
              $baseUnit = pow(1000, floor(log($number, 1000)));
              $numBaseUnits = (int) ($number / $baseUnit);
              $remainder = $number % $baseUnit;
              $string = $this->convert_number($numBaseUnits) . ' ' . $dictionary[$baseUnit];
              if ($remainder) {
                  $string .= $remainder < 100 ? $conjunction : $separator;
                  $string .= $this->convert_number($remainder)."";
              }
              break;
      }
      if (null !== $fraction && is_numeric($fraction)) {
          $string .= $decimal;
          $words = array();
          foreach (str_split((string) $fraction) as $number) {
              $words[] = $dictionary[$number];
          }
          $string .= implode(' ', $words). "";
      }
      return $string;    
    }
}