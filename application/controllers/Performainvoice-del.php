<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Performainvoice extends CI_Controller {

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
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model','admin_model','pi_model'));   
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();     
    }
    
    public function index(){    
        //unset($_SESSION['search__pi_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Performa Invoice"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';
        $pi_making_access = $admin_info['pi_making_access'];
        if($role==1 && $pi_making_access != 1)
            redirect('dashboard');
        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__pi_data']); die;
        $data["links"] = '';
        if(!empty($_POST) || isset($_SESSION['search__pi_data']))
        //if(!empty($_POST))
        {
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__pi_data'] = $_POST;
            else
                $_POST = $_SESSION['search__pi_data']; 
            $party_id = $_POST['party'];
            $brand_id = $_POST['brand'];
            $category_id = $_POST['category']; 
            $booking_date_from = date('Y-m-d',strtotime($_POST['booking_date_from']));
            $booking_date_to = date('Y-m-d',strtotime($_POST['booking_date_to'])); 
            $booking_status = (isset($_POST['status'])) ? $_POST['status'] : '';
            $role = $this->session->userdata('admin')['role'];

            $employee = $_POST['employee']; 
            $unit = $_POST['production_unit']; 

            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            }
            if(!isset($_POST['summary_submit']))
            {
                //echo "<pre>"; print_r($_POST); die;
                //$this->session->set_userdata('search__pi_data', $_POST); 
                
                $this->load->library("pagination");

                $limit = 20;
                if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                    $limit = $conditions_data['limit'];
                } 

                $config = array();
                $config["base_url"] = base_url() . "booking/report/";
                $total_rows =  $this->booking_model->CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$employee,$unit);
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
                $data['bookings'] = $this->booking_model->GetReportBookingPerforma($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);
                //echo "<pre>"; print_r($data); die;
            } 
        } 
        
        //echo "<pre>"; print_r($data['bookings']); die;
        $data['categories'] = $this->category_model->GetCategories();
        $data['brands'] = $this->brand_model->GetAllBrand();
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->vendor_model->GetUsersByState($states_ids);
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
        $data['employees'] = array();
        if($role==4 || $role==5)
            $data['employees'] = $this->admin_model->GetAllMakers();
        if($role==2)
        {
            $condition = array('team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetAllMaker($condition);
        }
        //echo "<pre>"; print_r($data['employees']); die;
        $this->load->view('performainvoice',$data);

    }

    public function index_old(){    
        //unset($_SESSION['search__pi_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Performa Invoice"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__pi_data']); die;
        $data["links"] = '';
        if(!empty($_POST) || isset($_SESSION['search__pi_data']))
        //if(!empty($_POST))
        {
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__pi_data'] = $_POST;
            else
                $_POST = $_SESSION['search__pi_data']; 
            $party_id = $_POST['party'];
            $brand_id = $_POST['brand'];
            $category_id = $_POST['category']; 
            $booking_date_from = date('Y-m-d',strtotime($_POST['booking_date_from']));
            $booking_date_to = date('Y-m-d',strtotime($_POST['booking_date_to'])); 
            $booking_status = (isset($_POST['status'])) ? $_POST['status'] : '';
            $role = $this->session->userdata('admin')['role'];

            $employee = $_POST['employee']; 
            $unit = $_POST['production_unit']; 

            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            }
            if(!isset($_POST['summary_submit']))
            {
                //echo "<pre>"; print_r($_POST); die;
                //$this->session->set_userdata('search__pi_data', $_POST); 
                
                $this->load->library("pagination");

                $limit = 20;
                if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                    $limit = $conditions_data['limit'];
                } 

                $config = array();
                $config["base_url"] = base_url() . "booking/report/";
                $total_rows =  $this->booking_model->CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$employee,$unit);
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
                $data['bookings'] = $this->booking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);
                //echo "<pre>"; print_r($data); die;
            }
            else
            {
                $data['search_summary'] = 1;
                //echo "<pre>"; print_r($_POST); die;
                $group_by  = array('category_name');
                //echo "<pre>"; print_r($group_by); die;
                $data['bookings_product'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
                //echo "<pre>"; print_r($data['bookings_product']); die;
                $group_by  = array('brand_id','category_id');
                //echo "<pre>"; print_r($group_by); die;
                $data['bookings_brand_product'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
                //echo "<pre>"; print_r($data['bookings_brand_product']); die;


                $group_by  = array('place','brand_id','category_id');
                $data['bookings_brand_product_place'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);

                //echo "<pre>"; print_r($data['bookings_brand_product_place']); die;

                $group_by  = array('status');
                $data['sum_report'] = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,'',$unit);

                //echo "<pre>"; print_r($data['sum_report']); die;

                $data['tot_sum_report'] = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,'',$unit); 

                $data['locked'] = $this->booking_model->GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,$unit); 

                //echo "<pre>"; print_r($data['locked'] ); die;
            }
        } 
        
        //echo "<pre>"; print_r($data['bookings']); die;
        $data['categories'] = $this->category_model->GetCategories();
        $data['brands'] = $this->brand_model->GetAllBrand();
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->vendor_model->GetUsersByState($states_ids);
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
        $data['employees'] = array();
        if($role==4 || $role==5)
            $data['employees'] = $this->admin_model->GetAllMakers();
        if($role==2)
        {
            $condition = array('team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetAllMaker($condition);
        }
        //echo "<pre>"; print_r($data['employees']); die;
        $this->load->view('performainvoice',$data);

    }
      
    public function preview()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142;
        //$_POST['bargains'] = array(214);
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];  

        $freights = $_POST['freight'];
        if($freights)
        {
            foreach ($freights as $freights_key => $freight) { 

                $freight_charge = ($freight) ? $freight : 0.000;
                $compnay_id = $freights_key;
                $bargain_ids_array  =  $_POST['bargains'];
                $bargain_id = $bargain_ids_array[0];
                $condition = array('booking_id' => $bargain_id);
                $booking_info = $this->booking_model->GetBookingInfoByIdPI($bargain_id);

                $conditions = array('booking_skus.bargain_id'=>$bargain_id);
                $bargain_ids = implode(',',$bargain_ids_array);
                $skus = $this->booking_model->GetAllSkupibycomany($bargain_ids,$compnay_id);
                //echo "<pre>"; print_r($skus); die;
                $booking_id = 'DATA/'.$booking_info['booking_id'];
                $invoice_date = strtoupper(date('d M Y',strtotime($booking_info['created_at'])));
                $party_name = $booking_info['party_name'];
                $party_city_name = $booking_info['city_name'];
                $party_state_name = $booking_info['state_name'];
                $party_gst_no = $booking_info['gst_no'];
                $broker_name = ($booking_info['broker_name']) ? $booking_info['broker_name'] : 'Direct';
         


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
                                        $invoice_nos = array() ;
                                        foreach ($skus as $key => $value) { 
                                            $invoice_nos[$value['bargain_id']] = 'DATA/'.$value['bargain_id']; 
                                            $sku_rate = 0;
                                            $insurance_percentage = 0;
                                            if($booking_info['insurance'])
                                                $insurance_percentage = $value['insurance_percentage'];
                                            $v = $value['quantity'];
                                            $mt = 0;
                                            $mt1 = '';
                                            $approx_weight=0.02;
                                            $l_to_kg_rate = 1/.91;
                                            if(strtolower($value['category_name'])=='vanaspati')
                                                $l_to_kg_rate = 1/.897;
                                            $empty_tin_charge = ($value['empty_tin_rate']*$value['packing_items_qty']); 
                                            $nort_east_rate_tin = 0;
                                            $skurate1 = 0;
                                            if($value['packaging_type']!=1)
                                            {
                                                if(($booking_info['state_id']==4 || $booking_info['state_id']==22 || $booking_info['state_id']== 23 || $booking_info['state_id']==24|| $booking_info['state_id']==25|| $booking_info['state_id']==30|| $booking_info['state_id']==33) && $value['base_rate']==0) 
                                                {
                                                    $nort_east_rate_tin = 31.85;
                                                    if(strpos($value['name'], '15')===0)
                                                        $nort_east_rate_tin = 0;
                                                    $packing_rate_ltr = ($value['booking_rate']-($value['base_empty_tin_rates']+$nort_east_rate_tin))/15;
                                                }
                                                else
                                                {
                                                    $packing_rate_ltr = ($value['booking_rate']-$value['base_empty_tin_rates'])/15;
                                                }
                                                
                                                $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                                $skurate1 = $sku_rate;
                                                
                                            }
                                            else
                                            { 
                                                $packing_rate_ltr = (($value['booking_rate']-$value['base_empty_tin_rates'])/15)*$l_to_kg_rate;

                                                
                                                $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                            }
                                            $sku_rate = $sku_rate+$empty_tin_charge;

                                            $sku_rate = $sku_rate+(($sku_rate*$insurance_percentage)/100);
                                            if($v)
                                            {
                                                $sku_rate_total =  ($sku_rate*$v);
                                                $sku_rate_dispaly = $sku_rate.'*'.$v.' = '.$sku_rate_total;
                                            }
                                            else
                                            {
                                               $sku_rate_dispaly = $sku_rate.'*0'.' = 0'; 
                                            } 
                                            $sku_total_with_gst = ($sku_rate_total*00.0)+$sku_rate_total;
                                            $html_response .= '<tr>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['category_name'].' '.$value['brand_name'].' '.$value['name'].'*'.$value['packing_items_qty'].' </td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['weight'].'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['hsn'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.round($sku_rate,3).'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.round($sku_total_with_gst,2).'</td>
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
                                        if($freight_charge)
                                        {

                                        $html_response .= '<tr>
                                          <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$sno.'</td>
                                          <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Freight to Driver</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"> </td> 
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; font-weight:bold;  line-height:20px;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"> </td> 
                                          <td  style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">'.$freight_charge.'</td>
                                        </tr>';
                                        $total_invoice_amount = $total_invoice_amount+$freight_charge;
                                        }
                                        
                                        //echo "<pre>"; print_r($hsns); die;
                    $html_response .= '<tr>
                                          <td colspan="" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" valign="top">Items Total </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000; " valign="top">'.number_format($total_invoice_weight,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_qty,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" colspan="" valign="top">Taxable Amount </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_amount,2).'</td>
                                        </tr>';
                    $html_response .= '</table>'; 
                    $state_id = $booking_info['state_id'];
                    if($state_id==29)
                    {
                      $SGST = 2.5;
                      $CGST = 2.5;
                      $IGST  = 0.00;
                      $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                      $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                      $igst_amount = 0;
                    }
                    else
                    {
                      $SGST = 0.00;
                      $CGST = 0.00;
                      $IGST = 5.0;
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
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$cgst_amount.'</td>
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

                                              $state_id = $booking_info['state_id'];
                                              if($state_id==29)
                                              {
                                                $SGST = 2.5;
                                                $CGST = 2.5;
                                                $IGST  = 0.00;
                                                $sgst_amount = round(((($hsn_amount*$SGST)/100)),2);
                                                $cgst_amount = round(((($hsn_amount*$CGST)/100)),2);
                                                $igst_amount = 0;
                                              }
                                              else
                                              {
                                                $SGST = 0.00;
                                                $CGST = 0.00;
                                                $IGST = 5.0;
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

                                            $state_id = $booking_info['state_id'];
                                              if($state_id==29)
                                              {
                                                $SGST = 2.5;
                                                $CGST = 2.5;
                                                $IGST  = 0.00;
                                                $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                                                $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                                                $igst_amount = 0;
                                              }
                                              else
                                              {
                                                $SGST = 0.00;
                                                $CGST = 0.00;
                                                $IGST = 5.0;
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
                                            <p style="margin:0px; padding:0px;">2. All disputes subject to '.$booking_info['production_unit'].' Jurisdiction only.</p>
                                            <p style="margin:0px; padding:0px;">3. I/We hereby certify that food/foods mentioned in this invoice is/are warranted to be of the nature and quality which it/these purports/purported to be.</p></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">SGST </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$sgst_amount.' </td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">IGST </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$igst_amount.'</td>
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
                                            Freight to Drive: '.number_format($freight_charge,2).' 
                                           </td>
                                          <td  colspan="5" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-size:10px; font-weight:bold" valign="top"></td>
                                          <td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-size:14px;" valign="top"></td>
                                        </tr>
                                        <tr>
                                          <td colspan="6" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:15px;"  valign="top"></td>
                                          <td  colspan="2" style="width: 152px; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">For Grand Total</td>'; 
                                           $gst = ((round($total_invoice_amount,2))*5)/100;
                                           $gross_toatl  = round($total_invoice_amount,2)+$gst;
                                           $amount_in_words = $this->convert_number(round($gross_toatl,2));
                                            $html_response .= '<td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">'.round($gross_toatl,2).'</td>
                                        </tr> 
                                        <tr>
                                          <td  colspan="6" rowspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; font-weight:bold; border-collapse:collapse;  " valign="top">Amount in Words : '.$amount_in_words.' </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                                        </tr>                                 
                    </table>';   

                    $Compnaycondition = array('id'=> $compnay_id);
                    $compnayinfo = $this->booking_model->CompnayInfo($Compnaycondition); 
                    $product_unit_name = $compnayinfo['name'];
                    $product_unit_address = $compnayinfo['address'].'<br>Tel. No(s) :'.$compnayinfo['phone'];
                    $product_unit_gst = $compnayinfo['gstn'];
                    $product_unit_pan = $compnayinfo['pan'];
                    $product_unit_cin = $compnayinfo['cin'];
                    $our_bank = $compnayinfo['bank_details'];

                    $header_html = '<div style="margin: 0px; padding: 0px; height: 100%; width: 100%;">
                          <table width="100%" border="0" align="center" cellpadding="0" bgcolor="#ffffff" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px;  padding:20px; font-size:14px; font-family:arial, verdana, tahoma">
                            <tr>
                              <td style="margin:0px; padding:20px; text-align:center; border-collapse:collapse; background:#ffffff;" valign="top">
                              <table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto;  padding:0px;">
                                  <tr>
                                    <td colspan="3" style="margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><h5 style="font-size:20px; line-height:38px; color:#202020; font-weight:bold; margin:0px; padding:0px;">'.$product_unit_name.'</h5>
                                      <p style="font-size:12px; color:#202020; margin:0px; padding:0px;">'.$product_unit_address.'</p></td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:12px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"><strong>PI</strong></td>
                                  </tr>
                                  <tr>
                                    <td style=" margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$product_unit_gst.'</p></td>
                                    <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PAN No. : '.$product_unit_pan.'</p></td>
                                    <td style="white-space:nowrap; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">CIN No. : '.$product_unit_cin.'</p></td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:10px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"> REVERSE CHARGE :  </td>
                                  </tr>
                                  <tr>
                                    <td style=" width:200px;margin:0px; padding:10px 0 5px 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PI No: '.implode(',',$invoice_nos).'</p></td>
                                    <td colspan="2" style="margin:0px; padding:10px 10px 5px 10px; line-height:20px;  text-align:center; border-collapse:collapse;" valign="top"><p style="font-size:14px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PERFORMA INVOICE</p></td>
                                    <td style="width:200px;margin:0px; padding:10px 0 5px 0; font-size:12px;  font-weight:bold; text-align:right; border-collapse:collapse;" valign="top"> Date: '.strtoupper(date('d M Y')).' </td>
                                  </tr>
                                  <tr>
                                    <td style="width:50%; margin:0px; padding:5px 10px 10px 0; line-height:20px; border-top:1px solid #000000;  border-bottom:1px solid #000000;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Invoice To</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.', '.$party_city_name.'</p>
                                      <p style="font-size:12px; font-weight:normal; color:#202020; margin:0px; padding:0px;">'.$party_city_name.', '.$party_state_name.'</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.' 
                                        '.$party_city_name.', State :'.$party_state_name.', PIN</p>
                                      <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$party_gst_no.' </p></td>
                                    <td  style="margin:0px; border-top:1px solid #000000; border-left:1px solid #000000;  border-bottom:1px solid #000000;   padding:5px 0px 10px 10px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Ship To</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">As Invoiced to</p></td>
                                  </tr>
                                  <tr>
                                    <td style="margin:0px; padding:5px 10px 10px 0; line-height:20px; text-align:left; border-collapse:collapse;" valign="top" colspan="4"><table width="100%" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Transport: </td>
                                          <td colspan="3" style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Supply Place: </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$party_city_name.' '.$party_state_name.' </td>
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
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$broker_name.'</td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:10px 5px 0 0; line-height:20px; font-size:14px;  text-align:left; border-collapse:collapse; font-weight:bold;" valign="top">e WayBill No. </td>
                                          <td colspan="2" style="margin:0px; padding:10px 5px 0 0; font-size:14px;  line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                          <td colspan="3" style="margin:0px; padding:10px 5px 0 0; line-height:20px;  text-align:left; font-size:10px; border-collapse:collapse;" valign="top">'.$our_bank.'</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                              </table>
                              </td>
                            </tr>
                          </table>
                          <div>';
                    $footerHtml = '<table  style="width:100%; ">
                                <tr>
                                  <td style="vertical-align:bottom; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"><span </span></td>
                                  <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:12px;" valign="top">For '.$product_unit_name.'  
                                  <br><br><br>
                                    <p style="display:block; margin:0px; padding:0px 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:11px;">Authorised Signatory
                                    <br>*This is system generated PI, no signature required. <br> PI is subject to change at the time of invoice.</p>
                                  </td>
                                </tr>
                              </table>';
                            //echo $html_response; die;
                echo $header_html.$html_response.$footerHtml;
            }
        }

    }


    public function preview1()
    { 
          
        //echo 2032.1+((2032.1*.0)/100); die;
         //echo "<pre>"; print_r($_POST); die;
        $_POST['bargains'] = array(790);

        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];  

        $bargain_ids_array  =  $_POST['bargains'];


        $bargain_id = $bargain_ids_array[0];
        $condition = array('booking_id' => $bargain_id);
        $booking_info = $this->booking_model->GetBookingInfoByIdPI($bargain_id);

        $conditions = array('booking_skus.bargain_id'=>$bargain_id);
        $bargain_ids = implode(',',$bargain_ids_array);
        $skus = $this->booking_model->GetAllSkupi($bargain_ids);
        //echo "<pre>"; print_r($booking_info); die;
        $booking_id = 'DATA/'.$booking_info['booking_id'];
        $invoice_date = strtoupper(date('d M Y',strtotime($booking_info['created_at'])));
        $party_name = $booking_info['party_name'];
        $party_city_name = $booking_info['city_name'];
        $party_state_name = $booking_info['state_name'];
        $party_gst_no = $booking_info['gst_no'];
        $broker_name = ($booking_info['broker_name']) ? $booking_info['broker_name'] : 'Direct';
 
        $booking_info['rate'] = 1756.2;
        $skus_pi_data = array();
          $html_response = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">
                                <tr>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. </td>
                                  <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Item Name </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Wt.(Kg) </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">HSN </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">UOM </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty. </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">GST %</td>
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
                                $invoice_nos = array() ;
                                foreach ($skus as $key => $value) { 
                                    $invoice_nos[$value['bargain_id']] = 'DATA/'.$value['bargain_id']; 
                                    $sku_rate = 0;
                                    $v = $value['quantity'];
                                    $insurance_percentage = 0;
                                    if($booking_info['insurance'])
                                        $insurance_percentage = $value['insurance_percentage'];
                                    $mt = 0;
                                    $mt1 = '';
                                    $approx_weight=0.02;
                                    $l_to_kg_rate = round((1/.91),2); 
                                    $empty_tin_charge = ($value['empty_tin_rate']*$value['packing_items_qty']); 
                                    if($value['packaging_type']!=1)
                                    {
                                        $packing_rate_ltr = ($booking_info['rate']-$booking_info['tin_rate'])/15;
                                        $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                        
                                    }
                                    else
                                    { 
                                        $packing_rate_ltr = (($booking_info['rate']-$booking_info['tin_rate'])/15)*$l_to_kg_rate;
                                        $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                    }
                                    $sku_rate = $sku_rate+$empty_tin_charge;
                                    $sku_rate = $sku_rate+(($sku_rate*$insurance_percentage)/100);
                                    if($v)
                                    {
                                        $sku_rate_total =  ($sku_rate*$v);
                                        $sku_rate_dispaly = $sku_rate.'*'.$v.' = '.$sku_rate_total;
                                    }
                                    else
                                    {
                                       $sku_rate_dispaly = $sku_rate.'*0'.' = 0'; 
                                    } 
                                    $sku_total_with_gst = round((($sku_rate_total*.05)+$sku_rate_total),2);
                                    $html_response .= '<tr>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$sno.'</td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['category_name'].' '.$value['brand_name'].' '.$value['name'].'*'.$value['packing_items_qty'].' </td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['weight'].'</td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['hsn'].'</td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">NUMBER</td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">5.00 </td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.number_format($sku_rate,2).'</td>
                                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.number_format($sku_total_with_gst,2).'</td>
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
                                    );
                                }  

                                $updated_skus_pi_data = array();
                                if($skus_pi_data)
                                {
                                    foreach ($skus_pi_data as $skus_pi_data_key => $skus_pi_data_value) {
                                        $updated_skus_pi_data[$skus_pi_data_key] = $skus_pi_data_value;
                                        $updated_skus_pi_data[$skus_pi_data_key]['pi_number'] = 20;
                                    }
                                    $this->booking_model->AddPiSkuHistory($updated_skus_pi_data);
                                } 
                                echo "<pre>"; print_r($updated_skus_pi_data); die;
            $html_response .= '<tr>
                                  <td colspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" valign="top">Items Total </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000; " valign="top">'.number_format($total_invoice_weight,2).'</td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_qty,2).'</td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" colspan="2" valign="top">Taxable Amount </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_amount,2).'</td>
                                </tr>';
            $html_response .= '</table>'; 
            $amount_in_words = $this->convert_number($total_invoice_amount);
            $state_id = $booking_info['state_id'];
            if($state_id==29)
            {
              $SGST = 2.5;
              $CGST = 2.5;
              $IGST  = 0.00;
              $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
              $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
              $igst_amount = 0;
            }
            else
            {
              $SGST = 0.00;
              $CGST = 0.00;
              $IGST = 5.0;
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
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$cgst_amount.'</td>
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

                                      $state_id = $booking_info['state_id'];
                                      if($state_id==29)
                                      {
                                        $SGST = 2.5;
                                        $CGST = 2.5;
                                        $IGST  = 0.00;
                                        $sgst_amount = round(((($hsn_amount*$SGST)/100)),2);
                                        $cgst_amount = round(((($hsn_amount*$CGST)/100)),2);
                                        $igst_amount = 0;
                                      }
                                      else
                                      {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST = 5.0;
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
                                    $state_id = $booking_info['state_id'];
                                      if($state_id==29)
                                      {
                                        $SGST = 2.5;
                                        $CGST = 2.5;
                                        $IGST  = 0.00;
                                        $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                                        $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                                        $igst_amount = 0;
                                      }
                                      else
                                      {
                                        $SGST = 0.00;
                                        $CGST = 0.00;
                                        $IGST = 5.0;
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
                                    <p style="margin:0px; padding:0px;">2. All disputes subject to '.$booking_info['production_unit'].' Jurisdiction only.</p>
                                    <p style="margin:0px; padding:0px;">3. I/We hereby certify that food/foods mentioned in this invoice is/are warranted to be of the nature and quality which it/these purports/purported to be.</p></td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">SGST </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$sgst_amount.' </td>
                                </tr>
                                <tr>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">IGST </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$igst_amount.'</td>
                                </tr>
                                <tr>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Forwarding </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
                                </tr>
                                <tr>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">Insurance </td>
                                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">0.00 </td>
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
                                  <td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-size:14px;" valign="top">0.00</td>
                                </tr>
                                <tr>
                                  <td colspan="6" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:15px;"  valign="top"></td>
                                  <td  colspan="2" style="width: 152px; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">For Grand Total</td>
                                  <td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">'.number_format($total_invoice_amount,2).'</td>
                                </tr>                                  
            </table>';    
            $product_unit_name = 'Babulal Edible Oils Pvt. Ltd.';
            $product_unit_address = '20-21 and 22 Old Industrial Area, Alwar, STATE - Rajasthan - 301001(INDIA)<br>Tel. No(s) :' ;
            $product_unit_gst = '08AAICB7230N1ZC';
            $product_unit_pan = 'AAICB7230N';
            $product_unit_cin = 'U15490RJ2019PTC066820';
            $our_bank = "Our Bank. : PNB, Jaipur A/c No. 2987008700014769 IFS Code PUNB0298700 <br>
                        PNB, Alwar A/c No. 0013008700011443 IFS Code PUNB0001300 <br>
                        Payment will be accepted only Through Electronic Mode <br>
                        Fssai L. No. 10019013002056";
            if($booking_info['production_unit']=='jaipur')
            {
                $product_unit_name = 'Shree Hari Agro Industries Limited ';
                $product_unit_address = 'Village + Post Mansar Khedi, Tehshil - Bassi, Jaipur, STATE - Rajasthan - 303301 (INDIA) <br>Tel. No(s) : , Fax No. : 01429216501' ;
                $product_unit_gst = '08AADCS7756H1ZY';
                $product_unit_pan = 'AADCS7756H';
                $product_unit_cin = 'U15142RJ1995PLC067473';
                $our_bank = 'KOTAK MAHINDRA BANK A/C 9313706265 RTGS KKBK0000271,<br>FSSAI LICENSE NO. 10012013000228';
            } 
            $header_html = '<div style="margin: 0px; padding: 0px; height: 100%; width: 100%;">
                  <table width="100%" border="0" align="center" cellpadding="0" bgcolor="#ffffff" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px;  padding:20px; font-size:14px; font-family:arial, verdana, tahoma">
                    <tr>
                      <td style="margin:0px; padding:20px; text-align:center; border-collapse:collapse; background:#ffffff;" valign="top">
                      <table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto;  padding:0px;">
                          <tr>
                            <td colspan="3" style="margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><h5 style="font-size:20px; line-height:38px; color:#202020; font-weight:bold; margin:0px; padding:0px;">'.$product_unit_name.'</h5>
                              <p style="font-size:12px; color:#202020; margin:0px; padding:0px;">'.$product_unit_address.'</p></td>
                            <td style="margin:0px; padding:0 0 0 0; font-size:12px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"><strong>PI</strong></td>
                          </tr>
                          <tr>
                            <td style=" margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$product_unit_gst.'</p></td>
                            <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PAN No. : '.$product_unit_pan.'</p></td>
                            <td style="white-space:nowrap; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">CIN No. : '.$product_unit_cin.'</p></td>
                            <td style="margin:0px; padding:0 0 0 0; font-size:10px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"> REVERSE CHARGE :  </td>
                          </tr>
                          <tr>
                            <td style=" width:200px;margin:0px; padding:10px 0 5px 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PI: '.implode(',',$invoice_nos).'</p></td>
                            <td colspan="2" style="margin:0px; padding:10px 10px 5px 10px; line-height:20px;  text-align:center; border-collapse:collapse;" valign="top"><p style="font-size:14px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PERFORMA INVOICE</p></td>
                            <td style="width:200px;margin:0px; padding:10px 0 5px 0; font-size:12px;  font-weight:bold; text-align:right; border-collapse:collapse;" valign="top"> Date: '.strtoupper(date('d M Y')).' </td>
                          </tr>
                          <tr>
                            <td style="width:50%; margin:0px; padding:5px 10px 10px 0; line-height:20px; border-top:1px solid #000000;  border-bottom:1px solid #000000;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Invoice To</p>
                              <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.', '.$party_city_name.'</p>
                              <p style="font-size:12px; font-weight:normal; color:#202020; margin:0px; padding:0px;">'.$party_city_name.', '.$party_state_name.'</p>
                              <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.' 
                                '.$party_city_name.', State :'.$party_state_name.', PIN</p>
                              <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$party_gst_no.' </p></td>
                            <td  style="margin:0px; border-top:1px solid #000000; border-left:1px solid #000000;  border-bottom:1px solid #000000;   padding:5px 0px 10px 10px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Ship To</p>
                              <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">As Invoiced to</p></td>
                          </tr>
                          <tr>
                            <td style="margin:0px; padding:5px 10px 10px 0; line-height:20px; text-align:left; border-collapse:collapse;" valign="top" colspan="4"><table width="100%" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                                <tr>
                                  <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Transport: </td>
                                  <td colspan="3" style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                  <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Supply Place: </td>
                                  <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$party_city_name.' '.$party_state_name.' </td>
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
                                  <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$broker_name.'</td>
                                </tr>
                                <tr>
                                  <td  style="margin:0px; padding:10px 5px 0 0; line-height:20px; font-size:14px;  text-align:left; border-collapse:collapse; font-weight:bold;" valign="top">e WayBill No. </td>
                                  <td colspan="2" style="margin:0px; padding:10px 5px 0 0; font-size:14px;  line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                  <td colspan="3" style="margin:0px; padding:10px 5px 0 0; line-height:20px;  text-align:left; font-size:10px; border-collapse:collapse;" valign="top">'.$our_bank.'</td>
                                </tr>
                              </table></td>
                          </tr>
                      </table>
                      </td>
                    </tr>
                  </table>
                  <div>';
            $footerHtml = '<table  style="width:100%; ">
                        <tr>
                          <td style="vertical-align:bottom; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"><span </span></td>
                          <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:12px;" valign="top">For '.$product_unit_name.'  
                          <br><br><br>
                            <p style="display:block; margin:0px; padding:0px 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:11px;">Authorised Signatory
                            <br>*This is system generated pi, no signature required.</p>
                          </td>
                        </tr>
                      </table>';
                    //echo $html_response; die;


                      echo $header_html.$html_response.$footerHtml; die;
    }
    public function invoice()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142;
        //$_POST['bargains'] = array(214);
        //$_POST['freight'] = array(1=>434,2=>45664);
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];
        $admin_name = $admin_info['name'];  
        
        $skus_pi_data = array();

        $freights = $_POST['freight'];
        include(FCPATH."mpdf1/mpdf.php");
        $pi_invoice = array();
        if($freights)
        {
            foreach ($freights as $freights_key => $freight) { 
                $freight_charge = ($freight) ? $freight : 0.000;
                $compnay_id = $freights_key;
                $bargain_ids_array  =  $_POST['bargains'];
                $bargain_id = $bargain_ids_array[0];
                $condition = array('booking_id' => $bargain_id);
                $booking_info = $this->booking_model->GetBookingInfoByIdPI($bargain_id);

                $conditions = array('booking_skus.bargain_id'=>$bargain_id);
                $bargain_ids = implode(',',$bargain_ids_array);
                $skus = $this->booking_model->GetAllSkupibycomany($bargain_ids,$compnay_id);
                //echo "<pre>"; print_r($skus); die;

                $booking_id = 'DATA/'.$booking_info['booking_id'];
                $invoice_date = strtoupper(date('d M Y',strtotime($booking_info['created_at'])));
                $party_name = $booking_info['party_name'];
                $party_id = $booking_info['party_id'];
                $party_city_name = $booking_info['city_name'];
                $party_state_name = $booking_info['state_name'];
                $party_gst_no = $booking_info['gst_no'];
                $broker_name = ($booking_info['broker_name']) ? $booking_info['broker_name'] : 'Direct';
            
                $party_id = $booking_info['party_id'];
                


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
                                        $skus_pi_data = array();
                                        foreach ($skus as $key => $value) { 

                                            $booked_by = $booking_info['booked_by'];
                                            $booking_id_barg = $booking_info['booking_id']; 

                                            $invoice_nos_pi[] = $value['booking_id'];
                                            $sku_nos_pi[] = $value['id'];
                                            $invoice_nos[$value['bargain_id']] = 'DATA/'.$value['bargain_id']; 
                                            $sku_rate = 0;
                                            $insurance_percentage = 0;
                                            if($booking_info['insurance'])
                                                $insurance_percentage = $value['insurance_percentage'];
                                            $v = $value['quantity'];
                                            $mt = 0;
                                            $mt1 = '';
                                            $approx_weight=0.02;
                                            $l_to_kg_rate = 1/.91;
                                            if(strtolower($value['category_name'])=='vanaspati')
                                                $l_to_kg_rate = 1/.897;
                                            $empty_tin_charge = ($value['empty_tin_rate']*$value['packing_items_qty']); 
                                            $nort_east_rate_tin = 0;
                                            if($value['packaging_type']!=1)
                                            {
                                                if(($booking_info['state_id']==4 || $booking_info['state_id']==22 || $booking_info['state_id']== 23 || $booking_info['state_id']==24|| $booking_info['state_id']==25|| $booking_info['state_id']==30|| $booking_info['state_id']==33) && $value['base_rate']==0) 
                                                {
                                                    $nort_east_rate_tin = 31.85;
                                                    if(strpos($value['name'], '15')===0)
                                                        $nort_east_rate_tin = 0;
                                                    $packing_rate_ltr = ($value['booking_rate']-($value['base_empty_tin_rates']+$nort_east_rate_tin))/15;
                                                }
                                                else
                                                {
                                                    $packing_rate_ltr = ($value['booking_rate']-$value['base_empty_tin_rates'])/15;
                                                }
                                                
                                                $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                                
                                            }
                                            else
                                            { 
                                                $packing_rate_ltr = (($value['booking_rate']-$value['base_empty_tin_rates'])/15)*$l_to_kg_rate;

                                                
                                                $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                            }
                                            $sku_rate = $sku_rate+$empty_tin_charge;
                                            $sku_rate = $sku_rate+(($sku_rate*$insurance_percentage)/100);
                                            if($v)
                                            {
                                                $sku_rate_total =  ($sku_rate*$v);
                                                $sku_rate_dispaly = $sku_rate.'*'.$v.' = '.$sku_rate_total;
                                            }
                                            else
                                            {
                                               $sku_rate_dispaly = $sku_rate.'*0'.' = 0'; 
                                            } 
                                            $sku_total_with_gst = ($sku_rate_total*00.0)+$sku_rate_total;
                                            $html_response .= '<tr>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['category_name'].' '.$value['brand_name'].' '.$value['name'].'*'.$value['packing_items_qty'].' </td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['weight'].'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['hsn'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.round($sku_rate,3).'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.round($sku_total_with_gst,2).'</td>
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
                                                'booking_id' => $value['booking_id'],
                                            );
                                        }  
                                        if($freight_charge)
                                        {

                                            $html_response .= '<tr>
                                              <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$sno.'</td>
                                              <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Freight to Driver</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"></td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"> </td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"></td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; font-weight:bold;  line-height:20px;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"> </td> 
                                              <td  style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">'.$freight_charge.'</td>
                                            </tr>';
                                            $total_invoice_amount = $total_invoice_amount+$freight_charge; 
                                        }
                                        
                                        //echo "<pre>"; print_r($hsns); die;
                    $html_response .= '<tr>
                                          <td colspan="" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" valign="top">Items Total </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000; " valign="top">'.number_format($total_invoice_weight,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_qty,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" colspan="" valign="top">Taxable Amount </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_amount,2).'</td>
                                        </tr>';
                    $html_response .= '</table>'; 
                    $state_id = $booking_info['state_id'];
                    if($state_id==29)
                    {
                      $SGST = 2.5;
                      $CGST = 2.5;
                      $IGST  = 0.00;
                      $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                      $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                      $igst_amount = 0;
                    }
                    else
                    {
                      $SGST = 0.00;
                      $CGST = 0.00;
                      $IGST = 5.0;
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
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$cgst_amount.'</td>
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

                                              $state_id = $booking_info['state_id'];
                                              if($state_id==29)
                                              {
                                                $SGST = 2.5;
                                                $CGST = 2.5;
                                                $IGST  = 0.00;
                                                $sgst_amount = round(((($hsn_amount*$SGST)/100)),2);
                                                $cgst_amount = round(((($hsn_amount*$CGST)/100)),2);
                                                $igst_amount = 0;
                                              }
                                              else
                                              {
                                                $SGST = 0.00;
                                                $CGST = 0.00;
                                                $IGST = 5.0;
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

                                            $state_id = $booking_info['state_id'];
                                              if($state_id==29)
                                              {
                                                $SGST = 2.5;
                                                $CGST = 2.5;
                                                $IGST  = 0.00;
                                                $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                                                $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                                                $igst_amount = 0;
                                              }
                                              else
                                              {
                                                $SGST = 0.00;
                                                $CGST = 0.00;
                                                $IGST = 5.0;
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
                                            <p style="margin:0px; padding:0px;">2. All disputes subject to '.$booking_info['production_unit'].' Jurisdiction only.</p>
                                            <p style="margin:0px; padding:0px;">3. I/We hereby certify that food/foods mentioned in this invoice is/are warranted to be of the nature and quality which it/these purports/purported to be.</p></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">SGST </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$sgst_amount.' </td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">IGST </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$igst_amount.'</td>
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
                                            Freight to Drive: '.number_format($freight_charge,2).' 
                                           </td>
                                          <td  colspan="5" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-size:10px; font-weight:bold" valign="top"></td>
                                          <td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-size:14px;" valign="top"></td>
                                        </tr>
                                        <tr>
                                          <td colspan="6" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:15px;"  valign="top"></td>
                                          <td  colspan="2" style="width: 152px; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">For Grand Total</td>'; 
                                           $gst = ((round($total_invoice_amount,2))*5)/100;
                                           $gross_toatl  = round($total_invoice_amount,2)+$gst;
                                           $amount_in_words = $this->convert_number(round($gross_toatl,2));
                                            $html_response .= '<td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">'.round($gross_toatl,2).'</td>
                                        </tr> 
                                        <tr>
                                          <td  colspan="6" rowspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; font-weight:bold; border-collapse:collapse;  " valign="top">Amount in Words : '.$amount_in_words.' </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                                        </tr>                                 
                    </table>';   

                    $Compnaycondition = array('id'=> $compnay_id);
                    $compnayinfo = $this->booking_model->CompnayInfo($Compnaycondition); 
                    $product_unit_name = $compnayinfo['name'];
                    $product_unit_address = $compnayinfo['address'].'<br>Tel. No(s) :'.$compnayinfo['phone'];
                    $product_unit_gst = $compnayinfo['gstn'];
                    $product_unit_pan = $compnayinfo['pan'];
                    $product_unit_cin = $compnayinfo['cin'];
                    $our_bank = $compnayinfo['bank_details'];
                    $pi_invoice['booking_id'] = implode(',',array_unique($invoice_nos_pi));
                    $pi_invoice['sku_ids'] = implode(',', $sku_nos_pi); 
                    $pi_invoice['company_id'] =$freights_key; 
                    $pi_invoice['created_by'] =$admin_id;  
                    $pi_invoice['pi_amount'] =$gross_toatl;  
                    $pi_invoice['total_weight_pi'] =number_format($total_invoice_weight,2);  
                    $pi_invoice['party_id'] =$party_id; 
 
                    $pi_invoice['created_by'] =$booked_by; 
                    //$pi_invoice['booking_id'] =$booking_id_barg;  


                    $pi_invoice_number = $this->booking_model->AddPiHistory($pi_invoice);

                    $updated_skus_pi_data = array();
                    if($skus_pi_data)
                    {
                        foreach ($skus_pi_data as $skus_pi_data_key => $skus_pi_data_value) {
                            $updated_skus_pi_data[$skus_pi_data_key] = $skus_pi_data_value;
                            $updated_skus_pi_data[$skus_pi_data_key]['pi_number'] = $pi_invoice_number;
                            $updated_skus_pi_data[$skus_pi_data_key]['party_id'] = $party_id;


                            $remark = "PI Generated  Amount @ ".$gross_toatl." and Weight @ ".number_format($total_invoice_weight,2);
                            $remarkdata = array('booking_id' => $skus_pi_data_value['booking_id'],'remark' => $remark,'remark_type'=> 'Bargain PI Generated','updated_by' => $admin_id);
                            $this->booking_model->AddRemark($remarkdata);

                        }
                        $this->booking_model->AddPiSkuHistory($updated_skus_pi_data);
                    }



                    $pi_invoice_condition = array('id' =>$pi_invoice_number); 

                    $header_html = '<div style="margin: 0px; padding: 0px; height: 100%; width: 100%;">
                          <table width="100%" border="0" align="center" cellpadding="0" bgcolor="#ffffff" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px;  padding:20px; font-size:14px; font-family:arial, verdana, tahoma">
                            <tr>
                              <td style="margin:0px; padding:20px; text-align:center; border-collapse:collapse; background:#ffffff;" valign="top">
                              <table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto;  padding:0px;">
                                  <tr>
                                    <td colspan="3" style="margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><h5 style="font-size:20px; line-height:38px; color:#202020; font-weight:bold; margin:0px; padding:0px;">'.$product_unit_name.'</h5>
                                      <p style="font-size:12px; color:#202020; margin:0px; padding:0px;">'.$product_unit_address.'</p></td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:12px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"><strong>PI</strong></td>
                                  </tr>
                                  <tr>
                                    <td style=" margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$product_unit_gst.'</p></td>
                                    <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PAN No. : '.$product_unit_pan.'</p></td>
                                    <td style="white-space:nowrap; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">CIN No. : '.$product_unit_cin.'</p></td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:10px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"> REVERSE CHARGE :  </td>
                                  </tr>
                                  <tr>
                                    <td style=" width:200px;margin:0px; padding:10px 0 5px 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PI: '.$pi_invoice_number.'</p></td>
                                    <td colspan="2" style="margin:0px; padding:10px 10px 5px 10px; line-height:20px;  text-align:center; border-collapse:collapse;" valign="top"><p style="font-size:14px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PERFORMA INVOICE</p></td>
                                    <td style="width:200px;margin:0px; padding:10px 0 5px 0; font-size:12px;  font-weight:bold; text-align:right; border-collapse:collapse;" valign="top"> Date: '.strtoupper(date('d M Y')).' </td>
                                  </tr>
                                  <tr>
                                    <td style="width:50%; margin:0px; padding:5px 10px 10px 0; line-height:20px; border-top:1px solid #000000;  border-bottom:1px solid #000000;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Invoice To</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.', '.$party_city_name.'</p>
                                      <p style="font-size:12px; font-weight:normal; color:#202020; margin:0px; padding:0px;">'.$party_city_name.', '.$party_state_name.'</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.' 
                                        '.$party_city_name.', State :'.$party_state_name.', PIN</p>
                                      <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$party_gst_no.' </p></td>
                                    <td  style="margin:0px; border-top:1px solid #000000; border-left:1px solid #000000;  border-bottom:1px solid #000000;   padding:5px 0px 10px 10px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Ship To</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">As Invoiced to</p></td>
                                  </tr>
                                  <tr>
                                    <td style="margin:0px; padding:5px 10px 10px 0; line-height:20px; text-align:left; border-collapse:collapse;" valign="top" colspan="4"><table width="100%" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Transport: </td>
                                          <td colspan="3" style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Supply Place: </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$party_city_name.' '.$party_state_name.' </td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Vehicle No: </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Credit Days: </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Cust. Ref.: '.implode(',',$invoice_nos).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">GR / LR No. </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">GR/LR Date: </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Broker: </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$broker_name.'</td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:10px 5px 0 0; line-height:20px; font-size:14px;  text-align:left; border-collapse:collapse; font-weight:bold;" valign="top">e WayBill No. </td>
                                          <td colspan="2" style="margin:0px; padding:10px 5px 0 0; font-size:14px;  line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                          <td colspan="3" style="margin:0px; padding:10px 5px 0 0; line-height:20px;  text-align:left; font-size:10px; border-collapse:collapse;" valign="top">'.$our_bank.'</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                              </table>
                              </td>
                            </tr>
                          </table>
                          <div>';
                    $footerHtml = '<table  style="width:100%; ">
                                <tr>
                                  <td style="vertical-align:bottom; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"><span >Page {PAGENO} of {nbpg}</span></td>
                                  <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:12px;" valign="top">For '.$product_unit_name.'  
                                  <br><br><br>
                                    <p style="display:block; margin:0px; padding:0px 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:11px;">Authorised Signatory
                                    <br>*This is system generated PI, no signature required. <br> PI is subject to change at the time of invoice.</p>
                                  </td>
                                </tr>
                              </table>';
                            //echo $html_response; die; 

                            $mpdf=new mPDF('utf-8','A4','0','0','0','0','130','25','0','0'); 
                            $mpdf->SetHTMLHeader($header_html);
                            $mpdf->SetHTMLFooter($footerHtml);
                            $mpdf->WriteHTML($html_response);
                            $f_name = str_replace('/','-',$booking_info['party_name']);

                            $f_name = preg_replace('/[^A-Za-z0-9\-]/', '', $f_name);

                            $f_name = str_replace(' ','-',$f_name).time().'-'.$freights_key.'-PI.pdf';

                            $invpice_name = FCPATH.'invoices/pi/'.$f_name; 
                            $pdf_file_name  = base_url().'invoices/pi/'.$f_name;
                            $mpdf->Output($invpice_name,'F'); 
                            $update_pi_data['invoice_file'] = $f_name;
                            $this->booking_model->UpdatePiHistory($update_pi_data,$pi_invoice_condition);  
                            $updatedata = array('pi_id' => $pi_invoice_number);
                            $condition_sku_update = array('sku_ids' => $pi_invoice['sku_ids']);
                            $this->booking_model->UpdateBookingSkuPiStatus($updatedata,$condition_sku_update);
                            
                            $sendor_party_name =  $product_unit_name; 
                            $message_params = urlencode($booking_info['party_name'].'~'.$sendor_party_name.'~'.$admin_name); 
                            $curl_watsappapi = curl_init();
                            curl_setopt_array($curl_watsappapi, array( 
                            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$admin_mobile.'&TID=8909629&P='.$message_params.'&PATH='.$pdf_file_name,
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

                            echo $response = curl_exec($curl_watsappapi);
                            curl_close($curl_watsappapi);
            }
        } 
        
        
    }

    public function download()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142; 
        //$_POST['bargains'] = array(214);
        //$_POST['freight'] = array(1=>434,2=>45664);
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];
        $admin_name = $admin_info['name'];  
        
        $freights = $_POST['freight'];
        include(FCPATH."mpdf1/mpdf.php");
        $invoices = array();
        if($freights)
        {
            foreach ($freights as $freights_key => $freight) { 
                $freight_charge = ($freight) ? $freight : 0.000;
                $compnay_id = $freights_key;
                $bargain_ids_array  =  $_POST['bargains'];
                $bargain_id = $bargain_ids_array[0];
                $condition = array('booking_id' => $bargain_id);
                $booking_info = $this->booking_model->GetBookingInfoByIdPI($bargain_id);

                $conditions = array('booking_skus.bargain_id'=>$bargain_id);
                $bargain_ids = implode(',',$bargain_ids_array);
                $skus = $this->booking_model->GetAllSkupibycomany($bargain_ids,$compnay_id);
                //echo "<pre>"; print_r($skus); die;
                $booking_id = 'DATA/'.$booking_info['booking_id'];
                $invoice_date = strtoupper(date('d M Y',strtotime($booking_info['created_at'])));
                $party_name = $booking_info['party_name'];
                $party_city_name = $booking_info['city_name'];
                $party_state_name = $booking_info['state_name'];
                $party_gst_no = $booking_info['gst_no'];
                $broker_name = ($booking_info['broker_name']) ? $booking_info['broker_name'] : 'Direct';
         


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
                                        $invoice_nos = array() ;
                                        foreach ($skus as $key => $value) { 
                                            $invoice_nos[$value['bargain_id']] = 'DATA/'.$value['bargain_id']; 
                                            $sku_rate = 0;
                                            $insurance_percentage = 0;
                                            if($booking_info['insurance'])
                                                $insurance_percentage = $value['insurance_percentage'];
                                            $v = $value['quantity'];
                                            $mt = 0;
                                            $mt1 = '';
                                            $approx_weight=0.02;
                                            $l_to_kg_rate = 1/.91;
                                            if(strtolower($value['category_name'])=='vanaspati')
                                                $l_to_kg_rate = 1/.897;
                                            $empty_tin_charge = ($value['empty_tin_rate']*$value['packing_items_qty']); 
                                            $nort_east_rate_tin = 0;
                                            if($value['packaging_type']!=1)
                                            {
                                                if(($booking_info['state_id']==4 || $booking_info['state_id']==22 || $booking_info['state_id']== 23 || $booking_info['state_id']==24|| $booking_info['state_id']==25|| $booking_info['state_id']==30|| $booking_info['state_id']==33) && $value['base_rate']==0) 
                                                {
                                                    $nort_east_rate_tin = 31.85;
                                                    if(strpos($value['name'], '15')===0)
                                                        $nort_east_rate_tin = 0;
                                                    $packing_rate_ltr = ($value['booking_rate']-($value['base_empty_tin_rates']+$nort_east_rate_tin))/15;
                                                }
                                                else
                                                {
                                                    $packing_rate_ltr = ($value['booking_rate']-$value['base_empty_tin_rates'])/15;
                                                }
                                                
                                                $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                                
                                            }
                                            else
                                            { 
                                                $packing_rate_ltr = (($value['booking_rate']-$value['base_empty_tin_rates'])/15)*$l_to_kg_rate;

                                                
                                                $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                            }
                                            $sku_rate = $sku_rate+$empty_tin_charge;
                                            $sku_rate = $sku_rate+(($sku_rate*$insurance_percentage)/100);
                                            if($v)
                                            {
                                                $sku_rate_total =  ($sku_rate*$v);
                                                $sku_rate_dispaly = $sku_rate.'*'.$v.' = '.$sku_rate_total;
                                            }
                                            else
                                            {
                                               $sku_rate_dispaly = $sku_rate.'*0'.' = 0'; 
                                            } 
                                            $sku_total_with_gst = ($sku_rate_total*00.0)+$sku_rate_total;
                                            $html_response .= '<tr>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['category_name'].' '.$value['brand_name'].' '.$value['name'].'*'.$value['packing_items_qty'].' </td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['weight'].'</td>
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['hsn'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.round($sku_rate,3).'</td> 
                                              <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.round($sku_total_with_gst,2).'</td>
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
                                        if($freight_charge)
                                        {

                                        $html_response .= '<tr>
                                          <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$sno.'</td>
                                          <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Freight to Driver</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"> </td> 
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; font-weight:bold;  line-height:20px;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top"> </td> 
                                          <td  style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">'.$freight_charge.'</td>
                                        </tr>';
                                        $total_invoice_amount = $total_invoice_amount+$freight_charge;
                                        }
                                        
                                        //echo "<pre>"; print_r($hsns); die;
                    $html_response .= '<tr>
                                          <td colspan="" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" valign="top">Items Total </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000; " valign="top">'.number_format($total_invoice_weight,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_qty,2).'</td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; border-top:1px solid #000000;" colspan="" valign="top">Taxable Amount </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; border-top:1px solid #000000;" valign="top">'.number_format($total_invoice_amount,2).'</td>
                                        </tr>';
                    $html_response .= '</table>'; 
                    $state_id = $booking_info['state_id'];
                    if($state_id==29)
                    {
                      $SGST = 2.5;
                      $CGST = 2.5;
                      $IGST  = 0.00;
                      $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                      $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                      $igst_amount = 0;
                    }
                    else
                    {
                      $SGST = 0.00;
                      $CGST = 0.00;
                      $IGST = 5.0;
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
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$cgst_amount.'</td>
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

                                              $state_id = $booking_info['state_id'];
                                              if($state_id==29)
                                              {
                                                $SGST = 2.5;
                                                $CGST = 2.5;
                                                $IGST  = 0.00;
                                                $sgst_amount = round(((($hsn_amount*$SGST)/100)),2);
                                                $cgst_amount = round(((($hsn_amount*$CGST)/100)),2);
                                                $igst_amount = 0;
                                              }
                                              else
                                              {
                                                $SGST = 0.00;
                                                $CGST = 0.00;
                                                $IGST = 5.0;
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

                                            $state_id = $booking_info['state_id'];
                                              if($state_id==29)
                                              {
                                                $SGST = 2.5;
                                                $CGST = 2.5;
                                                $IGST  = 0.00;
                                                $sgst_amount = round(((($total_invoice_amount*$SGST)/100)),2);
                                                $cgst_amount = round(((($total_invoice_amount*$CGST)/100)),2);
                                                $igst_amount = 0;
                                              }
                                              else
                                              {
                                                $SGST = 0.00;
                                                $CGST = 0.00;
                                                $IGST = 5.0;
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
                                            <p style="margin:0px; padding:0px;">2. All disputes subject to '.$booking_info['production_unit'].' Jurisdiction only.</p>
                                            <p style="margin:0px; padding:0px;">3. I/We hereby certify that food/foods mentioned in this invoice is/are warranted to be of the nature and quality which it/these purports/purported to be.</p></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">SGST </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$sgst_amount.' </td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top">IGST </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top">'.$igst_amount.'</td>
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
                                            Freight to Drive: '.number_format($freight_charge,2).' 
                                           </td>
                                          <td  colspan="5" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-size:10px; font-weight:bold" valign="top"></td>
                                          <td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-size:14px;" valign="top"></td>
                                        </tr>
                                        <tr>
                                          <td colspan="6" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:15px;"  valign="top"></td>
                                          <td  colspan="2" style="width: 152px; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">For Grand Total</td>'; 
                                           $gst = ((round($total_invoice_amount,2))*5)/100;
                                           $gross_toatl  = round($total_invoice_amount,2)+$gst;
                                           $amount_in_words = $this->convert_number(round($gross_toatl,2));
                                            $html_response .= '<td  colspan="1" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:13px;" valign="top">'.round($gross_toatl,2).'</td>
                                        </tr> 
                                        <tr>
                                          <td  colspan="6" rowspan="2" style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; font-weight:bold; border-collapse:collapse;  " valign="top">Amount in Words : '.$amount_in_words.' </td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse; font-weight:bold; " colspan="2" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; border-collapse:collapse;" valign="top"></td>
                                        </tr>                                 
                    </table>';   

                    $Compnaycondition = array('id'=> $compnay_id);
                    $compnayinfo = $this->booking_model->CompnayInfo($Compnaycondition); 
                    $product_unit_name = $compnayinfo['name'];
                    $product_unit_address = $compnayinfo['address'].'<br>Tel. No(s) :'.$compnayinfo['phone'];
                    $product_unit_gst = $compnayinfo['gstn'];
                    $product_unit_pan = $compnayinfo['pan'];
                    $product_unit_cin = $compnayinfo['cin'];
                    $our_bank = $compnayinfo['bank_details'];

                    $header_html = '<div style="margin: 0px; padding: 0px; height: 100%; width: 100%;">
                          <table width="100%" border="0" align="center" cellpadding="0" bgcolor="#ffffff" cellspacing="0" style="border-collapse:collapse; border:0px; margin:0px;  padding:20px; font-size:14px; font-family:arial, verdana, tahoma">
                            <tr>
                              <td style="margin:0px; padding:20px; text-align:center; border-collapse:collapse; background:#ffffff;" valign="top">
                              <table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto;  padding:0px;">
                                  <tr>
                                    <td colspan="3" style="margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><h5 style="font-size:20px; line-height:38px; color:#202020; font-weight:bold; margin:0px; padding:0px;">'.$product_unit_name.'</h5>
                                      <p style="font-size:12px; color:#202020; margin:0px; padding:0px;">'.$product_unit_address.'</p></td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:12px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"><strong>PI</strong></td>
                                  </tr>
                                  <tr>
                                    <td style=" margin:0px; padding:0 0 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$product_unit_gst.'</p></td>
                                    <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PAN No. : '.$product_unit_pan.'</p></td>
                                    <td style="white-space:nowrap; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">CIN No. : '.$product_unit_cin.'</p></td>
                                    <td style="margin:0px; padding:0 0 0 0; font-size:10px;  font-weight:bold; text-align:center; border-collapse:collapse;" valign="top"> REVERSE CHARGE :  </td>
                                  </tr>
                                  <tr>
                                    <td style=" width:200px;margin:0px; padding:10px 0 5px 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PI No: '.implode(',',$invoice_nos).'</p></td>
                                    <td colspan="2" style="margin:0px; padding:10px 10px 5px 10px; line-height:20px;  text-align:center; border-collapse:collapse;" valign="top"><p style="font-size:14px; font-weight:bold; color:#202020; margin:0px; padding:0px;">PERFORMA INVOICE (For Verification)</p></td>
                                    <td style="width:200px;margin:0px; padding:10px 0 5px 0; font-size:12px;  font-weight:bold; text-align:right; border-collapse:collapse;" valign="top"> Date: '.strtoupper(date('d M Y')).' </td>
                                  </tr>
                                  <tr>
                                    <td style="width:50%; margin:0px; padding:5px 10px 10px 0; line-height:20px; border-top:1px solid #000000;  border-bottom:1px solid #000000;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Invoice To</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.', '.$party_city_name.'</p>
                                      <p style="font-size:12px; font-weight:normal; color:#202020; margin:0px; padding:0px;">'.$party_city_name.', '.$party_state_name.'</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">'.$party_name.' 
                                        '.$party_city_name.', State :'.$party_state_name.', PIN</p>
                                      <p style="font-size:10px; font-weight:bold; color:#202020; margin:0px; padding:0px;">GSTIN '.$party_gst_no.' </p></td>
                                    <td  style="margin:0px; border-top:1px solid #000000; border-left:1px solid #000000;  border-bottom:1px solid #000000;   padding:5px 0px 10px 10px; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top" colspan="2"><p style="font-size:12px; font-weight:bold; color:#202020; margin:0px; padding:0px;">Ship To</p>
                                      <p style="font-size:13px; font-weight:bold; color:#202020; margin:0px; padding:0px;">As Invoiced to</p></td>
                                  </tr>
                                  <tr>
                                    <td style="margin:0px; padding:5px 10px 10px 0; line-height:20px; text-align:left; border-collapse:collapse;" valign="top" colspan="4"><table width="100%" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse; border:0px; text-align:center; margin:0px auto; font-size:12px;  padding:0px;">
                                        <tr>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Transport: </td>
                                          <td colspan="3" style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"></td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">Supply Place: </td>
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$party_city_name.' '.$party_state_name.' </td>
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
                                          <td  style="margin:0px; padding:0 5px 0 0; line-height:20px;  text-align:left; border-collapse:collapse;" valign="top">'.$broker_name.'</td>
                                        </tr>
                                        <tr>
                                          <td  style="margin:0px; padding:10px 5px 0 0; line-height:20px; font-size:14px;  text-align:left; border-collapse:collapse; font-weight:bold;" valign="top">e WayBill No. </td>
                                          <td colspan="2" style="margin:0px; padding:10px 5px 0 0; font-size:14px;  line-height:20px;  text-align:left; border-collapse:collapse;" valign="top"> </td>
                                          <td colspan="3" style="margin:0px; padding:10px 5px 0 0; line-height:20px;  text-align:left; font-size:10px; border-collapse:collapse;" valign="top">'.$our_bank.'</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                              </table>
                              </td>
                            </tr>
                          </table>
                          <div>';
                    $footerHtml = '<table  style="width:100%; ">
                                <tr>
                                  <td style="vertical-align:bottom; margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse; font-weight:bold; font-size:10px;"><span >Page {PAGENO} of {nbpg}</span></td>
                                  <td style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:12px;" valign="top">For '.$product_unit_name.'  
                                  <br><br><br>
                                    <p style="display:block; margin:0px; padding:0px 5px 0 5px; line-height:20px;  text-align:right; font-weight:bold; border-collapse:collapse; font-size:11px;">Authorised Signatory
                                    <br>*This is system generated PI, no signature required. <br> PI is subject to change at the time of invoice.</p>
                                  </td>
                                </tr>
                              </table>';
                            //echo $html_response; die; 

                            $mpdf=new mPDF('utf-8','A4','0','0','0','0','130','25','0','0'); 
                            $mpdf->SetHTMLHeader($header_html);
                            $mpdf->SetHTMLFooter($footerHtml);
                            $mpdf->WriteHTML($html_response);
                            $f_name = str_replace('/','-',$booking_info['party_name']);

                            $f_name = str_replace(' ','-',$f_name).time().'-'.$freights_key.'-PI.pdf';
                            $invpice_name = FCPATH.'invoices/pi/'.$f_name; 
                            $pdf_file_name  = base_url().'invoices/pi/'.$f_name;
                            $mpdf->Output($invpice_name,'F'); 
                            $invoices[] = $invpice_name;
            }
            //echo "<pre>"; print_r($invoices); die;
            $f_name = str_replace('/','-',$booking_info['party_name']);
            $f_name = str_replace(' ','-',$f_name).time();

            $zipname = FCPATH.$f_name.".zip";
            $zip = new ZipArchive;
            $zip->open($zipname, ZipArchive::CREATE);

            if($invoices)
            {
                foreach ($invoices as $key => $value) {
                    $file_names = explode('/', $value);
                    $file_name = end($file_names);
                    $zip->addFile($value,$file_name);
                }
            }
            $zip->close();
            /*header('Content-Type: application/zip');
            header("Content-Disposition: attachment; filename='$zipname'");
            header('Content-Length: ' . filesize($zipname));
            header("Location: $zipname");*/
            echo base_url().'performainvoice/downloadfile/'.$f_name.".zip"; die;
        } 
        echo 0; die;
    }
    public function downloadfile($filename){  

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
        header('Content-Length: ' . filesize($filename));

        flush();
        readfile($filename);
        // delete file
        unlink($filename); 
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


    public function skulist()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142;
        //$_POST['bargains'] = array(214);
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];  

        $companycondtition = array();
        $compnies = $this->booking_model->CompnayList($companycondtition);


        $bargain_ids_array  =  $_POST['bargains'];


        $bargain_id = $bargain_ids_array[0];
        $condition = array('booking_id' => $bargain_id);
        $booking_info = $this->booking_model->GetBookingInfoByIdPI($bargain_id);

        $conditions = array('booking_skus.bargain_id'=>$bargain_id);
        $bargain_ids = implode(',',$bargain_ids_array);
        $skus = $this->booking_model->GetAllSkupi($bargain_ids);
        //echo "<pre>"; print_r($skus); die;
        $booking_id = 'DATA/'.$booking_info['booking_id'];
        $invoice_date = strtoupper(date('d M Y',strtotime($booking_info['created_at'])));
        $party_name = $booking_info['party_name'];
        $party_city_name = $booking_info['city_name'];
        $party_state_name = $booking_info['state_name'];
        $party_gst_no = $booking_info['gst_no'];
        $broker_name = ($booking_info['broker_name']) ? $booking_info['broker_name'] : 'Direct';
 


          $html_response = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">
                <tr>
                  <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. </td>
                  <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Item Name </td>
                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Wt.(MT) </td>
                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty (NOS) </td>
                  <td  style="margin:0px; font-weight:bold; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Company</td>
                </tr>';
                $total_invoice_amount = 0;
                $total_invoice_weight = 0;
                $total_invoice_qty = 0;                                 
                $sno = 1;
                $sku_rate_total  = 0;
                $total_amount = 0; 
                $hsns = array() ;
                $invoice_nos = array() ;
                foreach ($skus as $key => $value) { 
                    $invoice_nos[$value['bargain_id']] = 'DATA/'.$value['bargain_id']; 
                    $sku_rate = 0;
                    $insurance_percentage = 0;
                    if($booking_info['insurance'])
                        $insurance_percentage = $value['insurance_percentage'];
                    $v = $value['quantity'];
                    $mt = 0;
                    $mt1 = '';
                    $approx_weight=0.02;
                    $l_to_kg_rate = 1/.91;
                    $empty_tin_charge = ($value['empty_tin_rate']*$value['packing_items_qty']); 
                    if($value['packaging_type']!=1)
                    {
                        $packing_rate_ltr = ($booking_info['rate']-$booking_info['empty_tin_rate'])/15;
                        $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                        
                    }
                    else
                    { 
                        $packing_rate_ltr = (($booking_info['rate']-$booking_info['empty_tin_rate'])/15)*$l_to_kg_rate;
                        $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                    }
                    $sku_rate = $sku_rate+$empty_tin_charge;
                    $sku_rate = $sku_rate+(($sku_rate*$insurance_percentage)/100);
                    if($v)
                    {
                        $sku_rate_total =  ($sku_rate*$v);
                        $sku_rate_dispaly = $sku_rate.'*'.$v.' = '.$sku_rate_total;
                    }
                    else
                    {
                       $sku_rate_dispaly = $sku_rate.'*0'.' = 0'; 
                    } 
                    $sku_total_with_gst = ($sku_rate_total*00.0)+$sku_rate_total;
                    $html_response .= '<tr>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['category_name'].' '.$value['brand_name'].' '.$value['name'].'*'.$value['packing_items_qty'].' </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['weight'].'</td>
                     
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td>  
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">
                        <select name="compnay['.$value['id'].']" class="form=control">';
                            $production_unit = $booking_info['production_unit'];
                            if($compnies)
                            {
                                
                                foreach ($compnies as $key => $value) {
                                    $selelected = '';
                                    if(strtolower($production_unit)=='jaipur' && $value['id']==2)
                                        $selelected = 'selected';
                                    $html_response .= '<option value="'.$value['id'].'" '.$selelected.'>'.$value['name'].'</option>';
                                }
                            }
                        $html_response .= '</select>
                      </td>
                    </tr> ';
                    $sno++; 
                }  
            $html_response .= '</table>';
            echo $html_response; die;
            
    }

    public function savecompnay()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142;
        //$_POST['bargains'] = array(214);

        $companies = $_POST['compnay'];
        $insertdata = array();
        if($companies)
        {
            foreach ($companies as $key => $value) {
                $condition = array('id' => $key);
                $updatedata = array('company_id' => $value);
                $this->booking_model->UpdateBookingSku($updatedata,$condition);
            }
            echo 1;
        }
        echo 0;
    }
    public function getcompnaylist()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142;
        //$_POST['bargains'] = array(214);
        $bargain_ids_array  =  $_POST['bargains'];
        $bargain_ids = implode(',',$bargain_ids_array);
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];  

        $companycondtition = array();
        $companies = $this->booking_model->SkuCompnayList($bargain_ids);
        //echo "<pre>"; print_r($compnies); die;
        $html_response = '';
        if($companies)
        {
            $html_response = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">
                <tr>
                    <td>Compnay Name</td>
                    <td>Weight (MT)</td>
                    <td>Freight</td>
                </tr>';

            foreach ($companies as $key => $value) {

                $html_response .= '<tr>
                    <td>'.$value['name'].'</td>
                    <td>'.$value['total_weight'].'</td>
                    <td><input type="text" name="freight['.$value['id'].']" value="" class="form-control freight_input"></td>
                </tr>';
            }
            $html_response .= '</table>';
        }
        echo $html_response;
    }

    function pi_history()
    {
        $booking_id = base64_decode($_POST['booking_id']);
        $results = $this->booking_model->GetPiHistoryEnabled($booking_id); 
        $html_response = "";
        if($results)
        {

            $html_response = "<table class='table table-striped table-bordered table-hover'>";
            $html_response .= "<thead><tr ><th rowspan='2' style='vertical-align: middle;'>S.No.</th><th rowspan='2' style='vertical-align: middle;'>Company Name</th><th rowspan='2' style='vertical-align: middle;'>Weight</th><th rowspan='2' style='vertical-align: middle;'>Bargain No.</th><th rowspan='2' style='vertical-align: middle;'>PI</th><th colspan='2' style='text-align:center'>Dispatch Via</th><th rowspan='2' style='vertical-align: middle; border-left: 1px solid #ddd;'>Deviation</th></tr><tr><th style='text-align: center'>Truck No.</th><th  style='text-align: center'>Date</th></tr></thead>
            <input  type='hidden' name='booking_id' value='".$booking_id."'>
            ";
            $sn = 1;
            $total_weight = 0;
            foreach ($results as $key => $value) {

                $html_response .= "<tr><td>".$sn."</td><td><input  type='hidden' name='booking_ids[]' value='".$value['booking_id']."'>".$value['company_name']."</td><td>".$value['total_weight_pi']."</td><td>".$value['bargain_ids']."</td><td><a download href='".base_url()."/invoices/pi/".$value['invoice_file']."'><img src='".base_url()."assets/img/pdf-bl.png' width='30' title='Download PI'></a></td><td><input class='form-control' type='text' name='dispatch[".$value['id']."][truck_no]' value='".$value['truck_number']."' placeholder='Truck Number' style='width: 125px;'></td><td><input type='text' class='form-control dispatch_date' name='dispatch[".$value['id']."][dispatch_date]' value='".$value['dispatch_date']."' placeholder='Dispatch Date' style='width: 125px;'></td><td><a href='javascript:void(0)' rel='".$value['id']."' class='deviation'>Deviation</a></td></tr>";
                $sn++;
                $total_weight = $total_weight+$value['total_weight_pi'];
            }
            $html_response .= "<tr><td></td><td>Total</td><td colspan='9'>".$total_weight."</td></tr>";
            $html_response .= "</table>";
        }
        echo $html_response; die;
    }

    function save_dispatch()
    {   

        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];  

        //echo "<pre>"; print_r($_POST); die;
        $booking_id = $_POST['booking_id'];
        $dispatch = $_POST['dispatch'];
        if($dispatch)
        {
            $pi_id_array = array();
            $booking_ids1 = $_POST['booking_ids'];
            foreach ($dispatch as $key => $value) {
                //echo "<pre>"; print_r($value); 
                $update_data = array('truck_number' => $value['truck_no'],'dispatch_date' => $value['dispatch_date']);
                $condition = array('id' => $key);
                $pi_id_array[] = $key; 
                $results = $this->booking_model->UpdatePiHistory($update_data,$condition);


                if($booking_ids1)
                {
                    foreach ($booking_ids1 as $key => $booking_ids_value1) {
                        $booking_id_array1 = explode(',', $booking_ids_value1);
                        if($booking_id_array1)
                        {
                            foreach ($booking_id_array1 as $key => $booking_ids_value2) {                             
                                $remark = "PI Dispatched  Truck Number @ ".$value['truck_no']." and Dispatch Date @ ".$value['dispatch_date'];
                                $remarkdata = array('booking_id' => $booking_ids_value2,'remark' => $remark,'remark_type'=> 'Bargain PI Dispatched','updated_by' => $admin_id);
                                $this->booking_model->AddRemark($remarkdata);
                            }
                        }
                    }
                }
            }
            $pi_ids = implode(',',$pi_id_array);
            $update_data = array('pi_ids' => $pi_ids);
            $condition = array('id' => $booking_id);
            echo $this->booking_model->UpdateBooking($update_data,$condition);

            $booking_ids = $_POST['booking_ids'];

            if($booking_ids)
            {
                foreach ($booking_ids as $key => $booking_ids_value) {
                    $booking_id_array = explode(',', $booking_ids_value);
                    if($booking_id_array)
                    {
                        foreach ($booking_id_array as $booking_id_key => $booking_id_value) {
                            $condition = array('booking_booking.id'=>$booking_id_value);
                            $value = $this->booking_model->checkflags($condition);

                            $dispatch_status = 0;
                            $pi_status = 0;
                            $remaining_pi_skus = $value['not_pi_sku'];
                            $withouttruck_pi = $value['withouttruck'];
                            $withtruck_pi = $value['withtruck'];
                            $lock_status = $value['is_lock'];
                            if($lock_status && $remaining_pi_skus==0)
                            {
                                if($lock_status == 2){
                                    $sku_msg = ' with no SKU';
                                    $dispactch_msg = ' Partially Dispatched';
                                    $dispatch_status = 2;
                                    $pi_status = 2;
                                }
                                if($lock_status == 1){
                                    $sku_msg = '';
                                    $dispactch_msg = ' Dispatched';
                                    $dispatch_status = 1;
                                    $pi_status = 1;
                                }
                                if($withouttruck_pi==0 || $withtruck_pi==0)
                                {
                                    if($withouttruck_pi ==0)
                                    {
                                         
                                        $title = $lock_msg.$sku_msg.$dispactch_msg;
                                        //echo '<img style="height: 35px" src="'.base_url('assets/img/full-truck.png').'" title="'.$title.'">';

                                        $dispatch_status = 1;
                                        $pi_status = 1;
                                    }
                                    else
                                    {
                                        $dispactch_msg = ' and Dispatch Pending';
                                        $title = $lock_msg.$sku_msg.$dispactch_msg;
                                        //echo '<span title="'.$title.'">PI Completed</span>';
                                        $dispatch_status = 2;
                                        $pi_status = 1;
                                    }
                                }
                                else
                                {
                                    $dispatch_status = 2;
                                    $pi_status = 1;
                                   $dispactch_msg = ' and Partial Dispatched';
                                   $title = $lock_msg.$sku_msg.$dispactch_msg;
                                   //echo '<img style="height: 35px;" src="'.base_url('assets/img/half-truck.png').'" title="'.$title.'">';
                                }
                            }
                            else
                            {
                                $sku_msg = ' and PI pending';
                                $dispatch_status = 0;
                                $pi_status = 0;
                                if($withouttruck_pi==0 || $withtruck_pi==0)
                                {
                                    if($withouttruck_pi == 0)
                                    {
                                        //This case will not occur in any condition
                                        if($lock_status=2)
                                        {
                                            $dispatch_status = 1; 
                                            $pi_status = 2;
                                        }
                                        $dispactch_msg = ' Partially Dispatched';
                                        //echo '<img style="height: 35px;" src="'.base_url('assets/img/half-truck.png').'" title="SKU Pending for PI and No PI pending for dispatch">';
                                    }
                                    else
                                    { 
                                        $dispatch_status = 0; 
                                        $pi_status = 2;
                                        $dispactch_msg = ' and Dispatch Pending';
                                        $title = $lock_msg.$sku_msg.$dispactch_msg;
                                        //echo '<span title="'.$title.'">PI</span>';
                                    }
                                }
                                else
                                {
                                    $dispatch_status = 2; 
                                    $pi_status = 2;
                                    $dispactch_msg = ' and Partially Dispatched';
                                    $title = $lock_msg.$sku_msg.$dispactch_msg;
                                    //echo '<img style="height: 35px;" src="'.base_url('assets/img/half-truck.png').'" title="'.$title.'">';
                                }
                            }

                            $updatedata_bar = array('dispatch_status' => $dispatch_status,'pi_status' => $pi_status);
                            $this->booking_model->UpdateBooking($updatedata_bar,$condition);
                        }
                    }
                }
            }

        }
    }

    

    function save_deviation()
    {
        //echo "<pre>"; print_r($_POST); die;

        $pi_id = $_POST['pi_id'];
        $pi_sku_ids = $_POST['pi_sku_id'];
        $brands  =  $_POST['brand'];
        $categories  =  $_POST['category'];
        $products  =  $_POST['product'];
        $deviations  =  $_POST['deviation'];
        $qtys  =  $_POST['qty'];

        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];

        if($pi_sku_ids)
        {
            $insertdata = array();
            foreach ($pi_sku_ids as $key => $value) {
                //echo "<pre>"; print_r($value); 
                $insertdata[] = array(
                    'pi_sku_id' => $value,
                    'brand_id' => $brands[$key],
                    'category_id' => $categories[$key],
                    'product_id' => $products[$key],
                    'pi_id' => $pi_id,
                    'quantity' => $qtys[$key],
                    'deviation' => $deviations[$key],
                    'created_by' => $admin_id,
                ); 
            } 
            echo $results = $this->pi_model->Adddeviation($insertdata);
        }
    }


    public function piskulist()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142;
        //$_POST['bargains'] = array(214);
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];  

        


        $pi_id = $_POST['pi_id']; 
        $condition = array('pi_sku_history.pi_number' => $pi_id);
        $skus = $this->pi_model->GetPiSkus($condition);
        //print_r($skus); die;
 


          $html_response = '<table border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">
                <tr>
                  <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. </td>
                  <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Item Name </td>
                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty (NOS) </td>
                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty Actual Dispatched </td> 
                </tr>
                <input type="hidden" name="pi_id" value="'.$pi_id.'">
                ';
                $sno = 1;
                foreach ($skus as $key => $value) { 
                    $condition = array('pi_deviation.pi_id' => $pi_id,'pi_deviation.pi_sku_id' => $value['id']);
                    $devitationskus = $this->pi_model->GetPiSkuDeviation($condition); 
                    $qty =  $value['quantity'];
                    if($devitationskus)
                        $qty = $devitationskus['deviation'];

                    $html_response .= '<tr>

                        <input type="hidden" name="pi_sku_id[]" value="'.$value['id'].'">
                        <input type="hidden" name="brand[]" value="'.$value['brand_id'].'">
                        <input type="hidden" name="category[]" value="'.$value['category_id'].'">
                        <input type="hidden" name="product[]" value="'.$value['product_id'].'">
                        <input type="hidden" name="qty[]" value="'.$value['quantity'].'">


                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['category_name'].' '.$value['brand_name'].' '.$value['name'].'*'.$value['packing_items_qty'].' </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td>
                     
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">
                      <input type="text" name="deviation[]" value="'.$qty.'"></td>   
                    </tr> ';
                    $sno++; 
                }  
            $html_response .= '</table>';
            echo $html_response; die;            
    }


    public function deviationlist()
    { 
        //echo "<pre>"; print_r($_POST); die;
        //$bargain_id = 142;
        //$_POST['bargains'] = array(214);
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $admin_mobile = $admin_info['mobile'];  

        


        $pi_id = $_POST['pi_id']; 
        $condition = array('pi_sku_history.pi_number' => $pi_id);
        $skus = $this->pi_model->GetPiSkus($condition);
        //print_r($skus); die;
 


          $html_response = '<table border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">
                <tr>
                  <td  style="width:30px;margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. </td>
                  <td width="220px" style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:left; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Item Name </td>
                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty (NOS) </td>
                  <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;  text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000;" valign="top">Qty Actual Dispatched </td> 
                </tr>
                <input type="hidden" name="pi_id" value="'.$pi_id.'">
                ';
                $sno = 1;
                foreach ($skus as $key => $value) { 
                    $condition = array('pi_deviation.pi_id' => $pi_id,'pi_deviation.pi_sku_id' => $value['id']);
                    $devitationskus = $this->pi_model->GetPiSkuDeviation($condition); 
                    $qty =  $value['quantity'];
                    if($devitationskus)
                        $qty = $devitationskus['deviation'];

                    $html_response .= '<tr>

                        <input type="hidden" name="pi_sku_id[]" value="'.$value['id'].'">
                        <input type="hidden" name="brand[]" value="'.$value['brand_id'].'">
                        <input type="hidden" name="category[]" value="'.$value['category_id'].'">
                        <input type="hidden" name="product[]" value="'.$value['product_id'].'">
                        <input type="hidden" name="qty[]" value="'.$value['quantity'].'">


                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000; border-left:1px solid #000000; width:30px;" valign="top">'.$sno.'</td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:left; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['category_name'].' '.$value['brand_name'].' '.$value['name'].'*'.$value['packing_items_qty'].' </td>
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">'.$value['quantity'].'</td>
                     
                      <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;  text-align:center; border-collapse:collapse;  border-right:1px solid #000000;" valign="top">
                      '.$qty.'</td>   
                    </tr> ';
                    $sno++; 
                }  
            $html_response .= '</table>';
            echo $html_response; die;            
    }
}