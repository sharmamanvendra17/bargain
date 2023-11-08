<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

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
        $this->load->model('login_model');   
        $this->load->library('dynamic');                

    }
    public function index(){   
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));

        /* 15 days data */
        $group_by  = array('status');
        $age  = 15;
        $status  = '';
        $sum_report_15 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by); 
        $tot_sum_report_15 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by=array()); 

        $group_by  = array('is_lock');
        $locked_15 = $this->booking_model->GetBookingSummaryLockedDashboard($age,$status,$group_by);

        $fifteendays= array('sum_report' => $sum_report_15,'tot_sum_report' => $tot_sum_report_15,'locked' => $locked_15);
        /* 15 days data ends */


        /* 30 days data */
        $group_by  = array('status');
        $age  = 30;
        $status  = '';
        $sum_report_30 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by); 
        $tot_sum_report_30 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by=array()); 

        $group_by  = array('is_lock');
        $locked_30 = $this->booking_model->GetBookingSummaryLockedDashboard($age,$status,$group_by);
        $onemonth= array('sum_report' => $sum_report_30,'tot_sum_report' => $tot_sum_report_30,'locked' => $locked_30);
        /* 30 days data ends */

        /* more than 1month data */
        $group_by  = array('status');
        $age  = 30;
        $status  = '';
        $sum_report_month = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by); 
        $tot_sum_report_month = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by=array()); 

        $group_by  = array('is_lock');
        $locked_month = $this->booking_model->GetBookingSummaryLockedDashboard($age,$status,$group_by);
        $moremonth= array('sum_report' => $sum_report_month,'tot_sum_report' => $tot_sum_report_month,'locked' => $locked_month);
        /* more than 1month data */

        //echo "<pre>"; print_r($data); die;
        $response = "";
        if($fifteendays)
        {
            $response .= "<table style='width:100%; border:1px solid #000; border-collapse: collapse;'><tr><th colspan='3' style='border:1px solid #000; border-collapse: collapse;'>Report Past 15 days</th></tr>";
            $response .= "<tr><th style='border:1px solid #000; border-collapse: collapse;'></th><th style='border:1px solid #000; border-collapse: collapse;'>Number Of Bargains</th> <th style='border:1px solid #000; border-collapse: collapse;'>Weight (MT)</th></tr>";

            $tot_sum_report = $fifteendays['tot_sum_report'];
            $locked = $fifteendays['locked'];
            $sum_report = $fifteendays['sum_report']; 
            //echo "<pre>"; print_r($tot_sum_report); die;                
            if($tot_sum_report)
            {  
                foreach ($tot_sum_report as $key => $tot_sum_report_value) { 
                    $weight = ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0;   
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">Total</th><th style="border:1px solid #000; border-collapse: collapse;">'.$tot_sum_report_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                } 
            }
            if($locked)
            {  
                foreach ($locked as $key => $locked_value) 
                { 
                    $weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">Locked</th><th style="border:1px solid #000; border-collapse: collapse;">'.$locked_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                }
            }
            if($sum_report)
            {  
                foreach ($sum_report as $key => $sum_report_value) { 
                    $weight = ($locked_value['weight']) ? $sum_report_value['weight'] : 0;  
                    if($sum_report_value['status']==0)
                        $st  =  "Pending";
                    elseif($sum_report_value['status']==2)
                        $st  = "Approved";
                    else
                        $st  = "Rejected"; 
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">'.$st.'</th><th style="border:1px solid #000; border-collapse: collapse;">'.$sum_report_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                }
            }
        }

        if($onemonth)
        {
            $response .= "<table style='margin-top:25px; width:100%; border:1px solid #000; border-collapse: collapse;'><tr><th colspan='3' style='border:1px solid #000; border-collapse: collapse;'>Report Past 1 Month</th></tr>";
            $response .= "<tr><th style='border:1px solid #000; border-collapse: collapse;'></th><th style='border:1px solid #000; border-collapse: collapse;'>Number Of Bargains</th> <th style='border:1px solid #000; border-collapse: collapse;'>Weight (MT)</th></tr>";

            $tot_sum_report = $onemonth['tot_sum_report'];
            $locked = $onemonth['locked'];
            $sum_report = $onemonth['sum_report']; 
            //echo "<pre>"; print_r($tot_sum_report); die;                
            if($tot_sum_report)
            {  
                foreach ($tot_sum_report as $key => $tot_sum_report_value) { 
                    $weight = ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0;   
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">Total</th><th style="border:1px solid #000; border-collapse: collapse;">'.$tot_sum_report_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                } 
            }
            if($locked)
            {  
                foreach ($locked as $key => $locked_value) 
                { 
                    $weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">Locked</th><th style="border:1px solid #000; border-collapse: collapse;">'.$locked_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                }
            }
            if($sum_report)
            {  
                foreach ($sum_report as $key => $sum_report_value) { 
                    $weight = ($locked_value['weight']) ? $sum_report_value['weight'] : 0;  
                    if($sum_report_value['status']==0)
                        $st  =  "Pending";
                    elseif($sum_report_value['status']==2)
                        $st  = "Approved";
                    else
                        $st  = "Rejected"; 
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">'.$st.'</th><th style="border:1px solid #000; border-collapse: collapse;">'.$sum_report_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                }
            }
        }

        if($moremonth)
        {
            $response .= "<table style='margin-top:25px; width:100%; border:1px solid #000; border-collapse: collapse;'><tr><th colspan='3'  style='border:1px solid #000; border-collapse: collapse;'>Report More than 1 Month</th></tr>";
            $response .= "<tr><th style='border:1px solid #000; border-collapse: collapse;'></th><th style='border:1px solid #000; border-collapse: collapse;'>Number Of Bargains</th> <th style='border:1px solid #000; border-collapse: collapse;'>Weight (MT)</th></tr>";

            $tot_sum_report = $moremonth['tot_sum_report'];
            $locked = $moremonth['locked'];
            $sum_report = $moremonth['sum_report']; 
            //echo "<pre>"; print_r($tot_sum_report); die;                
            if($tot_sum_report)
            {  
                foreach ($tot_sum_report as $key => $tot_sum_report_value) { 
                    $weight = ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0;   
                    $response .= '<tr style="border:1px solid #000; border-collapse: collapse;"><th style="border:1px solid #000; border-collapse: collapse;">Total</th><th style="border:1px solid #000; border-collapse: collapse;">'.$tot_sum_report_value['bargain_count'].'</th> <th tyle="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                } 
            }
            if($locked)
            {  
                foreach ($locked as $key => $locked_value) 
                { 
                    $weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">Locked</th><th style="border:1px solid #000; border-collapse: collapse;">'.$locked_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                }
            }
            if($sum_report)
            {  
                foreach ($sum_report as $key => $sum_report_value) { 
                    $weight = ($locked_value['weight']) ? $sum_report_value['weight'] : 0;  
                    if($sum_report_value['status']==0)
                        $st  =  "Pending";
                    elseif($sum_report_value['status']==2)
                        $st  = "Approved";
                    else
                        $st  = "Rejected"; 
                    $response .= '<tr><th style="border:1px solid #000; border-collapse: collapse;">'.$st.'</th><th style="border:1px solid #000; border-collapse: collapse;">'.$sum_report_value['bargain_count'].'</th> <th style="border:1px solid #000; border-collapse: collapse;">'.$weight.'</th></tr>';
                }
            }
        }
        include 'mailer/email.php'; 
        $from = "webmaster@dil.in";
        $from_name = "Bargain";
        $subject   = 'Bargain Daily Report'; 
        $email = 'ajay@data.in'; 
        $cc = 'deepak@data.in';
        $bcc = 'ss@datagroup.in'; 
        echo smtpmailer($email, $from,$from_name,$subject, $response,"",$cc,$bcc); 
    }

    public function bargain_report()
    {
        $this->load->model('booking_model');
        $booking_date_from = date('Y-m-d',strtotime("-1 days"));
        $booking_date_to = date('Y-m-d',strtotime("-1 days"));;
        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $booked_by = "";
        $group_by  = array('brand_id','category_id');
        $employee = "";
        $unit = "";
        $booking_pending_days = 0;
        $broker = "";
        $bagainnumber = "";
        $result =  $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$booking_pending_days,$broker,$bagainnumber);
        $params = date('d/m/Y',strtotime("-1 days"))." "; 
         
        if($result)
        {
            foreach ($result as $key => $value) {
                if($value['avg_rate_other'])
                {
                    $loose_kg = (strtolower($value['category_name'])=='vanaspati') ? 0.897 : .91;
                    $avg_rate_loose =  round($value['avg_rate_other_loose'],2);
                    $loose_rate = (($avg_rate_loose)/$loose_kg)/15;
                }
                if($value['avg_rate_aasam'])
                {
                    $loose_kg = (strtolower($value['category_name'])=='vanaspati') ? 0.897 : .91;
                    $avg_rate_loose =  round($value['avg_rate_aasam_loose'],2);
                    $loose_rate = ((($avg_rate_loose)/$loose_kg)-$value['freight_rate'])/15;
                } 
                $params .= $value['brand_name']." ".$value['category_name']." ".round($value['weight'],2)." (MT) "." loose rate ".round($loose_rate,2)." ";
            }
        }  
        $mobile_numbar = "9829099922,9828066666,9828077777,7792047479,9799356333,9649646362"; 
        //$mobile_numbar = "7792047479,9649646362"; 
        $whatsapp_message = urlencode($params); 
                $curl_url = 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbar.'&TID=1022013997&P='.$whatsapp_message;                
               
       $curl_watsappapi = curl_init();
        curl_setopt_array($curl_watsappapi, array( 
        CURLOPT_URL => $curl_url,
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

    public function dispatch_report()
    {
        $this->load->model('booking_model'); 
        $date_from = date('d-m-Y',strtotime("-1 days"));
        $date_to = date('d-m-Y',strtotime("-1 days"));        
        $condition = array(
                "DATE_FORMAT(STR_TO_DATE(`pi_history`.`dispatch_date`,'%d-%m-%Y'),'%d-%m-%Y') >="  =>$date_from,
                "DATE_FORMAT(STR_TO_DATE(`pi_history`.`dispatch_date`,'%d-%m-%Y'),'%d-%m-%Y')  <= " =>$date_to,
            );
        $result = $this->booking_model->GetDispatchedSummaryBrandProduct($condition);
        //echo "<pre>"; print_r($result); die;
        $params = date('d/m/Y',strtotime("-1 days"))." ";
        if($result)
        {
            foreach ($result as $key => $value) {
                $params .= $value['brand_name']." ".$value['category_name']." ".round($value['total_dispatch'],2)." (MT) ";
            }
        }
        $mobile_numbar = "9829099922,9828066666,9828077777,7792047479,9799356333,9649646362";
        //$mobile_numbar = "7792047479,9649646362"; 
        $whatsapp_message = urlencode($params); 
                $curl_url = 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbar.'&TID=1022013999&P='.$whatsapp_message;                
               
       $curl_watsappapi = curl_init();
        curl_setopt_array($curl_watsappapi, array( 
        CURLOPT_URL => $curl_url,
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

    public function alert(){   
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));

        $alerts = $this->booking_model->GetBargainalert();
        //echo "<pre>"; print_r($alerts); die;      

        $users = array();
        if($alerts)
        {
            $res="";
            $res.="<table style='margin-top:25px; width:100%; border:1px solid #000; border-collapse: collapse;'>
                <thead>
                    <tr>
                        <th style='border:1px solid #000; border-collapse: collapse;'>S.No.</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Bargain No</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Employee  Name</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Party Name</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Place</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Brand</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Product</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Quantity</th> 
                        <th style='border:1px solid #000; border-collapse: collapse;'>Weight</th> 
                        <th style='border:1px solid #000; border-collapse: collapse;'>Rate (15Ltr Tin)</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Production Unit</th> 
                        <th style='border:1px solid #000; border-collapse: collapse;'>Delivery Date</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>Remark</th> 
                        <th style='border:1px solid #000; border-collapse: collapse;'>Date</th>
                        <th style='border:1px solid #000; border-collapse: collapse;'>status</th> 
                    </tr>
                </thead>";
                $s_no = 1;
            foreach ($alerts as $key => $value) {

                $users[$value['admin_id']]['email'] = $value['username'];
                $users[$value['admin_id']]['name'] = $value['admin_name'];
                $users[$value['admin_id']]['data'][] = $value;
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
                $res.="<tr>
                        <td style='text-align:center; border:1px solid #000; border-collapse: collapse;'>".$s_no."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>DATA/".$value['booking_id']."</td> 
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['admin_name']."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['party_name']."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['city_name']."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['brand_name']."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['category_name']."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['quantity']."</td> 
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['weight']."</td> 
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['rate']." (".$ex.")</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['production_unit']."</td> 
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".date("d-m-Y", strtotime($value['shipment_date']))."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['remark']."</td> 
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".date("d-m-Y", strtotime($value['created_at']))."</td>
                        <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$b_status."</td> 
                    </tr>";
                    $s_no++;
            }
            $res.="<table>";
            //echo "<pre>"; print_r($users); die;
            include 'mailer/email.php'; 
            $from = "webmaster@dil.in";
            $from_name = "Bargain";
            $subject   = 'Older than 5 day pending bargains report'; 

            $email = 'ajay@data.in'; 
            $cc = 'deepak@data.in'; 
            $bcc = 'ss@datagroup.in'; 
            smtpmailer($email, $from,$from_name,$subject, $res,"",$cc,$bcc); 
            //echo $this->alert_user($users);
            $data = $users;
            if($data)
            { 
                foreach ($data as $key => $data_alerts) { 

                    $user_name = $data_alerts['name'];
                    $user_email = $data_alerts['email'];
                    $alerts = $data_alerts['data'];

           
                    if($alerts)
                    {
                        $res=" Hello ".$user_name.",<br>";
                        $res.="<table style='margin-top:25px; width:100%; border:1px solid #000; border-collapse: collapse;'>
                            <thead>
                                <tr>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>S.No.</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Bargain No</th> 
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Party Name</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Place</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Brand</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Product</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Quantity</th> 
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Weight</th> 
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Rate (15Ltr Tin)</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Production Unit</th> 
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Delivery Date</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Remark</th> 
                                    <th style='border:1px solid #000; border-collapse: collapse;'>Date</th>
                                    <th style='border:1px solid #000; border-collapse: collapse;'>status</th> 
                                </tr>
                            </thead>";
                            $s_no = 1;
                        foreach ($alerts as $key => $value) {                        
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
                            $res.="<tr>
                                    <td style='text-align:center; border:1px solid #000; border-collapse: collapse;'>".$s_no."</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>DATA/".$value['booking_id']."</td>  
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['party_name']."</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['city_name']."</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['brand_name']."</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['category_name']."</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['quantity']."</td> 
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['weight']."</td> 
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['rate']." (".$ex.")</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['production_unit']."</td> 
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".date("d-m-Y", strtotime($value['shipment_date']))."</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$value['remark']."</td> 
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".date("d-m-Y", strtotime($value['created_at']))."</td>
                                    <td style='text-align:center;border:1px solid #000; border-collapse: collapse;'>".$b_status."</td> 
                                </tr>";
                                $s_no++;
                        }
                        $res.="<table>";
                        //echo "<pre>"; print_r($res); die;
                        
                        $from = "webmaster@dil.in";
                        $from_name = "Bargain";
                        $subject   = 'Older than 5 day pending bargains report';
                        smtpmailer($user_email, $from,$from_name,$subject, $res,"",$cc=''); 
                    }
                }
            }
        }        
    } 



    public function purchase_report(){   
        $this->load->model(array('purchase/purchase_model'));

        $result = $this->purchase_model->GetPurchasealert();
        //echo "<pre>"; print_r($result); die; 
        $params = date('d/m/Y')." : "; 
        if($result)
        {
            foreach ($result as $key => $value) {
                 
                $param_array[] = round($value['qunatity'],2)." (MT) ".$value['product_name']." @ ".round($value['average_rate'],2);
            }
            $params .= implode(', ', $param_array);
        }  
        else
        {
            $params .= "No data in purchase";
        }
        //echo  $params; die;
        
        //$mobile_numbar = "7792047479,9649646362"; 
        $mobile_numbar = "9783307841,9828066666,9783301284,7792047479,9649646362"; 
        $whatsapp_message = urlencode($params); 
                $curl_url = 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=919462570495&T='.$mobile_numbar.'&TID=1022523064&P='.$whatsapp_message;                
               
       $curl_watsappapi = curl_init();
        curl_setopt_array($curl_watsappapi, array( 
        CURLOPT_URL => $curl_url,
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


    public function whatsapp_message(){   
        $postdata = file_get_contents('php://input');

        $date = date('d-m-Y');
        $log_file = "api-logs/logs".$date.'.log';
        $log_file = fopen($log_file,"a");  
        fwrite($log_file, PHP_EOL .'================================================================================='.PHP_EOL.date('H:i').' Request data => '. PHP_EOL); 
        fwrite($log_file, print_r($postdata, true)); 



        $postdata_array = json_decode($postdata,true);
        $receivedate = urldecode($postdata_array['Receivedate']);
		$str = strlen($postdata_array['From']);
		if($str>10)
        	$mobile_number = substr($postdata_array['From'],2,$str);
		else
			$mobile_number = $postdata_array['From'];
        $sender_number = $postdata_array['To'];
        $message = str_replace('+',' ',$postdata_array['Text']);
        $send_receive_flag = 2;

        $media_type = "";
        $content_type = "";
        $recieved_file = "";
        $caption = '';
        $Endusername = '';
        if(isset($postdata_array['Media_type']))
            $media_type = $postdata_array['Media_type'];
        if(isset($postdata_array['Content_type']))
            $content_type = urldecode($postdata_array['Content_type']);
        if(isset($postdata_array['Media_data']))
            $recieved_file = $postdata_array['Media_data'];
        if(isset($postdata_array['Caption']))
            $caption = $postdata_array['Caption'];
        if(isset($postdata_array['Endusername']))
            $Endusername = $postdata_array['Endusername'];
        

        $this->load->model('booking_model'); 
        $whatsapp_data = array('mobile_number' => $mobile_number,'message' => $message,'sender_number'=> $sender_number,'receiving_time'=> $receivedate,'send_receive_flag' => $send_receive_flag,'media_type' => $media_type,'content_type' => $content_type,'recieved_file' => $recieved_file,'caption' => $caption,'Endusername' => $Endusername);
        //echo "<pre>"; print_r($whatsapp_data); die;
        $this->booking_model->AddWhatsappLog($whatsapp_data);
        echo json_encode(array('status' => 1, 'message' => 'Data inserted successfully'));
    }


    public function whatsapp_message_old_data(){   
        $postdata = file_get_contents('php://input');
        $postdata_array = json_decode($postdata,true);
        $receivedate = urldecode($postdata_array['Receivedate']);
        $str = strlen($postdata_array['From']);
        if($str>10)
            $mobile_number = substr($postdata_array['From'],2,$str);
        else
            $mobile_number = $postdata_array['From'];
        $sender_number = $postdata_array['To'];
        $message = str_replace('+',' ',$postdata_array['Text']);
        $send_receive_flag = 2;

        $media_type = $postdata_array['media_type'];
        $content_type = $postdata_array['content_type'];
        $recieved_file = $postdata_array['media_data'];
        


        $this->load->model('booking_model'); 
        $whatsapp_data = array('mobile_number' => $mobile_number,'message' => $message,'sender_number'=> $sender_number,'receiving_time'=> $receivedate,'send_receive_flag' => $send_receive_flag,'media_type' => $media_type,'content_type' => $content_type,'recieved_file' => $recieved_file);
        //echo "<pre>"; print_r($whatsapp_data); die;
        $this->booking_model->AddWhatsappLog($whatsapp_data);
        echo json_encode(array('status' => 1, 'message' => 'Data inserted successfully'));
    }
}