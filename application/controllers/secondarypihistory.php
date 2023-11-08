<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secondarypihistory extends CI_Controller {

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
        $this->load->model(array('pi_model','secondarybooking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));   
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();     
    }  
    public function removepitest(){ 
        echo 1;
    }
    public function removepi(){ 
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['logged_role'] = $role;
        $data['logged_role'] = $role;
        $pi_id = base64_decode($_POST['pi_id']);
        $unlock = trim($_POST['unlock']);
        $booking_id = base64_decode(trim($_POST['booking_id']));
        $remark = trim($_POST['remark']);
        if($pi_id)
        {
            $updatedata = array('remark' => $remark,'status' => 1,'pi_removed_time'=> date('Y-m-d H:i:s'),'removed_by' => $admin_id);
            $condition = array('id' => $pi_id);
            $updated = $this->secondarybooking_model->UpdateSecondaryPiHistory($updatedata,$condition);
            if($updated)
            {
                $updatedata_sku = array('pi_id' => 0); 
                $condition_sku = array('pi_id' => $pi_id);
                $this->secondarybooking_model->UpdateSeconadryBookingSku($updatedata_sku,$condition_sku);  
                $this->secondarybooking_model->UpdateBooking($updatedata_sku,$condition_sku);   
                $condition = array('pi_number' => $pi_id);
                $this->secondarybooking_model->pi_sku_historysecondary_bookingremove($condition);

                

            }
            echo $updated;
        }
    }
    public function index(){    
        //unset($_SESSION['search__secondary_pi_report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Secondary Booking PI Report"; 
        $data['pis'] = array();
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
        if(!empty($_POST) || isset($_SESSION['search__secondary_pi_report_data']))
        //if(!empty($_POST))
        {
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__secondary_pi_report_data'] = $_POST;
            else
                $_POST = $_SESSION['search__secondary_pi_report_data']; 
            $party_id = $_POST['supply_from']; 
            $booking_date_from = date('Y-m-d',strtotime($_POST['booking_date_from']));
            $booking_date_to = date('Y-m-d',strtotime($_POST['booking_date_to']));              
            $role = $this->session->userdata('admin')['role'];
            $rejected = (isset($_POST['rejected'])) ? $_POST['rejected'] : '';
            $pinumber = (isset($_POST['pinumber'])) ? $_POST['pinumber'] : ''; 

            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            } 
            //echo "<pre>"; print_r($_POST); die;
            //$this->session->set_userdata('search__pi_data', $_POST); 
            
            $this->load->library("pagination");

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 

            $config = array();
            $config["base_url"] = base_url() . "secondarypihistory/index/";
            $condition = array(
                'party_id' => $party_id,
                'brand_id' => $brand_id,
                'category_id' => $category_id,
                'booking_date_from' => $booking_date_from,
                'booking_date_to' => $booking_date_to,
                'booked_by' => $booked_by,
                'booking_status' => $booking_status,  
                'rejected' => $rejected,
                'pinumber' => $pinumber,
            );
            $total_rows =  $this->pi_model->GetSecondaryPIHistoryCount($condition);
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
            $data['pis'] = $this->pi_model->GetSecondaryPIHistory($condition,$limit,$page);
            //echo "<pre>"; print_r($data['pis']); die;
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

        //echo "<pre>"; print_r($data); die;
        $this->load->view('secondary_pi_report',$data);
    }
 
    public function report_pdf(){    
        //unset($_SESSION['search__pihistory_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Performa Invoice Report"; 
        $data['pis'] = array();
        $party_id = $_REQUEST['party'];
        $booking_date_from = date('Y-m-d',strtotime($_REQUEST['from']));
        $booking_date_to = date('Y-m-d',strtotime($_REQUEST['to'])); 
        $booking_status = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : '';
        $role = $this->session->userdata('admin')['role'];
        $rejected = (isset($_REQUEST['rejected'])) ? $_REQUEST['rejected'] : '';
        $pinumber = (isset($_REQUEST['pinumber'])) ? $_REQUEST['pinumber'] : '';
         
        $unit = $_REQUEST['production_unit']; 

        $booked_by = '';
        $condition = array();
        if($role==1) //maker
        {
            $booked_by = $this->session->userdata('admin')['id'];
        } 
        //echo "<pre>"; print_r($_POST); die;
        //$this->session->set_userdata('search__pi_data', $_POST); 
        
        $this->load->library("pagination");

        $limit = 200000000000;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 

        $config = array();
        $config["base_url"] = base_url() . "pihistory/index/";
        $condition = array(
            'party_id' => $party_id, 
            'booking_date_from' => $booking_date_from,
            'booking_date_to' => $booking_date_to,
            'booked_by' => $booked_by, 
            'rejected' => $rejected,
            'pinumber' => $pinumber,
        );
        $total_rows =  $this->pi_model->GetSecondaryPIHistoryCount($condition);
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
        $pis = $this->pi_model->GetSecondaryPIHistory($condition,$limit,$page);
        //echo "<pre>"; print_r($pis); die;
        $html_print .= '';
        if($pis)
        {
            $html_print .= '
            <table  style="overflow:wrap; table-layout:fixed; border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                <tr>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:100px"><strong>PI Number</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Party Name</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Bargain No.</strong></td> 
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Total Weight (MT)</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Amount</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>PI Date</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Status</strong></td>
                </tr>
            ';
            foreach ($pis as $key => $value) {
                /*$html_print .= '<tr>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:8%;"><strong>'.$value['id'].'</strong></td>
                    <td style="word-wrap:break-all; text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>'.$value['vendors_name'].'</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>'.$value['bargain_ids'].'</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>'.$value['company_name'].'</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>'.round($value['total_weight_pi'],2).'</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>'.number_format($value['pi_amount'],2).'</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.date("d-m-Y H:i:s", strtotime($value['created_at'])).'</td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Status</strong></td>
                </tr>'; */
                $bg_color = '';
                if($value['status']==1)
                    $bg_color = 'red';
                $html_print .= '<tr style="background-color:'.$bg_color.';">';
                $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.$value['id'].'</td>';
                $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.$value['vendors_name'].'</td>';
                $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px; word-wrap:break-all;">'.$value['bargain_ids'].'</td>'; 
                $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.round($value['total_weight_pi'],2).'</td>';
                $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.number_format($value['pi_amount'],2).'</td>';
                $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.date("d-m-Y H:i:s", strtotime($value['created_at'])).'</td>';
                if($value['status']==0)
                {
                    $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">';
                    if($value['truck_number'])
                        $html_print .= 'Truck No. '.$value['truck_number'].'<br> Dispatch Date : '.$value['dispatch_date'];
                    $html_print .= '</td>'; 
                }
                else
                {
                    $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.$value['remark'].'</td>';
                }
                $html_print .= '</tr>';
            }
            $html_print .= '</table>';
            $header = '
                <h3 style="text-align:center;">Performa Invoice Report Secondary Booking '.$_REQUEST['from'].' to '.$_REQUEST['to'].'</h3>';
            /* $header = '    <table  style="table-layout:fixed; overflow:wrap;border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;"> 
                <tr>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:8%;"><strong>PI Number</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Party Name</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Bargain No.</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Comapny Name</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Total Weight (MT)</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Amount</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>PI Date</strong></td>
                    <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Status</strong></td>
                </tr>
                </table>';*/
            $footer = '{PAGENO} of {nbpg}';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4-L','0','0','10','10','16','15','0','0'); 
            $mpdf->SetHTMLHeader($header); 
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Performa Invoice Report Secondary Booking '.$_REQUEST['from'].' to '.$_REQUEST['to'].'.pdf'; 
            $mpdf->Output($f_name,'I');
        }
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

    public function convert_number($number)
    { 
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
        100000             => 'Lakh',
        10000000          => 'Crore',
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
            case $number < 100000:
                $thousands  = $number / 1000; 
                if($thousands >= 21)
                {
                  $tens   = ((int) ($thousands / 10)) * 10; 
                  $units  = $thousands % 10; 
                  $string = $dictionary[$tens];
                  if ($units) {
                      $string .= ' '.$dictionary[$units];
                  }
                }
              $remainder = $number % 1000;
              @$string .= $dictionary[$thousands] . ' ' . $dictionary[1000]; 
              if ($remainder) {
                  $string .= $conjunction . $this->convert_number($remainder);
              }
              break;
            case $number < 10000000:
                $thousands  = $number / 100000; 
                if($thousands >= 21)
                {
                  $tens   = ((int) ($thousands / 10)) * 10; 
                  $units  = $thousands % 10; 
                  $string = $dictionary[$tens];
                  if ($units) {
                      $string .= ' '.$dictionary[$units];
                  }
                }
              $remainder = $number % 100000;
              @$string .= $dictionary[$thousands] . ' ' . $dictionary[100000]; 
              if ($remainder) {
                  $string .= $conjunction . $this->convert_number($remainder);
              }
              break;
          default:
               // echo log($number, 100000); die;
               $baseUnit = pow(10000000, floor(log($number, 10000000)));
              $numBaseUnits = (int) ($number / $baseUnit);
              $remainder = $number % $baseUnit; 
              $string = $this->convert_number($numBaseUnits) . ' ' . $dictionary[$baseUnit];
              if ($remainder) {

                  $string .= $remainder < 10000000 ? $conjunction : $separator;

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
                            if($compnies)
                            {
                                foreach ($compnies as $key => $value) {
                                    $html_response .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
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
        $results = $this->booking_model->GetPiHistory($booking_id); 
        $html_response = "";
        if($results)
        {

            $html_response = "<table class='table table-striped table-bordered table-hover'>";
            $html_response .= "<thead><tr ><th rowspan='2' style='vertical-align: middle;'>S.No.</th><th rowspan='2' style='vertical-align: middle;'>Company Name</th><th rowspan='2' style='vertical-align: middle;'>Weight</th><th rowspan='2' style='vertical-align: middle;'>Bargain No.</th><th rowspan='2' style='vertical-align: middle;'>PI</th><th colspan='2' style='text-align:center'>Dispatch Via</th></tr><tr><th>Truck No.</th><th>Date</th></tr></thead>
            <input  type='hidden' name='booking_id' value='".$booking_id."'>
            ";
            $sn = 1;
            $total_weight = 0;
            foreach ($results as $key => $value) {
                $html_response .= "<tr><td>".$sn."</td><td>".$value['company_name']."</td><td>".$value['total_weight_pi']."</td><td>".$value['bargain_ids']."</td><td><a download href='".base_url()."/invoices/pi/".$value['invoice_file']."'><img src='".base_url()."assets/img/pdf-bl.png' width='30' title='Download PI'></a></td><td><input class='form-control' type='text' name='dispatch[".$value['id']."][truck_no]' value='".$value['truck_number']."' placeholder='Truck Number' style='width: 125px;'></td><td><input type='text' class='form-control dispatch_date' name='dispatch[".$value['id']."][dispatch_date]' value='".$value['dispatch_date']."' placeholder='Dispatch Date' style='width: 125px;'></td></tr>";
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
        $booking_id = $_POST['booking_id'];
        $dispatch = $_POST['dispatch'];
        if($dispatch)
        {
            $pi_id_array = array();
            foreach ($dispatch as $key => $value) {
                //echo "<pre>"; print_r($value); 
                $update_data = array('truck_number' => $value['truck_no'],'dispatch_date' => $value['dispatch_date']);
                $condition = array('id' => $key);
                $pi_id_array[] = $key; 
                $results = $this->booking_model->UpdatePiHistory($update_data,$condition);
            }
            $pi_ids = implode(',',$pi_id_array);
            $update_data = array('pi_ids' => $pi_ids);
            $condition = array('id' => $booking_id);
            echo $this->booking_model->UpdateBooking($update_data,$condition);
        }
    }




    function accounting_calculation()
    { 
        $this->load->model(array('secondarybooking_model','vendor_model','rate_model','pi_model','booking_model','testmodel','category_model')); 
      $pi_id = base64_decode($_POST['pi_id']); 
      $condition = array('pi_history_secondary_booking.id' => $pi_id);
      $piinfo = $this->pi_model->GetSecondaryPiInfo($condition);
      
      $current_month = date('m');
      $current_year = date('Y'); 
      $vendor_id = $piinfo['party_id'];       
      $start_dates = strtotime(date('Y-m', strtotime($piinfo['created_at'])));
      $delete_start_date = date("Y-m-t", $start_dates);

      $this->pi_model->Deletecnf_sales_history($delete_start_date,$vendor_id);
      // cnf_sales_history 

      $end_date = strtotime(date("Y-m")); 
      $_POST['status'] = "sku";

      while($start_dates < $end_date)
      {
        //echo date('Y m', $start_dates), PHP_EOL; echo "<br>";
        $_POST['year'] = date('Y', $start_dates);
        $_POST['month'] = date('m', $start_dates); 
        $start_dates = strtotime("+1 month", $start_dates);
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
          }
          //echo "<pre>"; print_r($data['skulists']); die;
          //$data['bargains'] = $this->booking_model->current_month_buy($vendor_id); 
          //$data['secondary'] = $this->booking_model->current_month_sale($vendor_id); 
        }  
      } 
      echo 1; 
    }


    public function add_accounts_history($party,$month,$status){ 
        $party_id = $party;  
        //echo $month;

        $month_year = $month;
        $month_array = explode('-', $month_year);
        $post_month = $month_array[1];
        $post_year = $month_array[0];
        $month2 = $post_month-1;
        $year2 = $post_year;
        $post_month = $month2;
        $post_month1 = $month_array[1];
        if($month2==0)
        {
            $post_month = 12;
            $previous_month= 12;
            $post_year =$post_year-1;
        } 
        $stock_date  = $post_year."-".sprintf("%02d", $post_month1);
        //echo $stock_date; die;
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
 
}