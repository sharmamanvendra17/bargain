<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking extends CI_Controller {

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
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));      
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();          
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
    }
    
    public function getinsurance()
    {
        $party_id = $_POST['party_id'];
        $brand_id = $_POST['brand_id'];
        $category_id = $_POST['category_id'];

        $condition = array(
            'vendors.id' =>$party_id,
            'empty_tin_rates.base_rate' =>1,
            'empty_tin_rates.brand_id' =>$brand_id,
            'empty_tin_rates.category_id' =>$category_id,
        );
        echo $this->booking_model->getinsurance($condition);
    }

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
        $data['booking_info'] = $this->booking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->booking_model->GetSkuinfo($condition); 

        $party_name = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $broker_name = $booking_info['broker_name'];
        $sales_executive_name = $booking_info['sales_executive_name'];
        $bargain_number = 'DATA/'.$booking_info['booking_id'];
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
                        <th class="text-center">Add Insurance in price </th>
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
        $data['booking_info'] = $this->booking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];

        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->booking_model->GetSkuinfo($condition); 

        $dispatchDates = $this->booking_model->BargainDispatchDates($booking_info['id']); 
        //echo "<pre>"; print_r($dispatchDates); die;
        $comments = $this->booking_model->GetBookingRemarks($booking_info['id']); 

        $party_name = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $broker_name = $booking_info['broker_name'];
        $sales_executive_name = $booking_info['sales_executive_name'];
        $bargain_number = 'DATA/'.$booking_info['booking_id'];
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));

        $truck_dispatch_date = $dispatchDates['dispatch_date'];

        $production_unit = $booking_info['production_unit'];
        $ordered_total_weight = $booking_info['total_weight'];
        $sku_total_weight = $booking_info['total_weight_input']; 
        $sku_total_weight = $booking_info['total_weight_input']; 
        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $small_pack_info ="";
        if(($booking_info['qunatity_in_numbers']>0) || ($booking_info['small_pack_rate']>0))
        {
            $small_pack_info = "<b>Small pack info :</b><br> Qunatity in numbers : ".$booking_info['qunatity_in_numbers']." <br> Small pack rate".$booking_info['small_pack_rate']." <br>";
        }

        $remark = $small_pack_info.$booking_info['remark']; 

        $brand_name = $booking_info['brand_name']; 
        $category_name = $booking_info['category_name']; 
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


        $result = '
            <h2 style="text-align:center">'.$party_name.'<br> (Bargain Number DATA/'.$bargain_id.')</h2>
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
                        <th class="text-center">Est. Dispatch Date</th>
                        <th class="text-center">Dispatch Date</th>
                    </tr>
                    <tr>
                        <td>'.$bargain_number.'</td>
                        <td>'.$dispatch_date.'</td>
                        <td>'.$truck_dispatch_date.'</td>
                    </tr>
                    <tr>

                        <th class="text-center">Production Unit</th>
                        <th class="text-center">Orderd Total Weight</th>
                        <th class="text-center">SKU Total Weight</th>
                    </tr>
                    <tr>                       
                        <td>'.$production_unit.'</td>
                        <td>'.$ordered_total_weight.'</td>
                        <td>'.$sku_total_weight.'</td> 
                    </tr>

                    <tr>
                        <th class="text-center">Total Items </th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Product</th>
                    </tr>
                    <tr>
                        <td>'.$item_total.'</td>
                        <td>'.$brand_name.'</td>
                        <td>'.$category_name.'</td>
                    </tr>

                    <tr>
                        <th class="text-center">Rate</th>
                        <th class="text-center">Add Insurance in price </th>
                        <th class="text-center">Price ex-factory</th> 
                    </tr>
                    <tr>
                        <td>'.$rate.'</td>
                        <td>'.$insurance.'</td>
                        <td>'.$is_for.'</td> 
                    </tr>'; 
                    $result .= '<tr>
                        <th class="text-left" colspan="3">Remark</th> 
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left" style="text-align: left;">'.$remark.'</td> 
                    </tr>'; 
                $result .= '</thead>
            </table>';
            $result .= '<div class="table-responsive"><table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
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
                        <td>'.round($value['weight'],2).'</td>
                    </tr>'; 
                    $sr++;
                }
                $result .= '<tr>
                        <td>Total</td>
                        <td></td>
                        <td>'.$item_total.'</td>
                        <td>'.$booking_info['total_weight_input'].'</td>
                    </tr>';
                    $tentative_weight = (($booking_info['total_weight_input']*1000)/.91);
                    $tentative_rate_per_ltr = ($rate/15);
                    $total_tentative_amount  = round(($tentative_rate_per_ltr*$tentative_weight),2);
                $result .= '<tr>
                        <td>Tentative Amount</td>
                        <td></td>
                        <td></td>
                        <td>'. number_format($total_tentative_amount,2).'</td>
                    </tr>';
            }
            $result .= '</table></div>';

            if($comments)
            {

                $result .= '<hr><h5>Comments History</h5><div class="table-responsive"><table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                    <thead>
                        <tr>
                            <th class="text-center">Sr.No.</th>
                            <th class="text-center">Comment Type</th>
                            <th class="text-center">Comment</th>
                            <th class="text-center">Update By</th>
                            <th class="text-center">Updated On</th>
                        </tr>
                    </thead>';  
                    $sr = 1;
                    foreach ($comments as $key => $value) {
                        $result .= '<tr>
                            <td>'.$sr.'</td>
                            <td>'.$value['remark_type'].'</td>
                            <td>'.$value['remark'].'</td>
                            <td>'.$value['updated_by_name'].'</td>
                            <td>'.date('d-m-Y H:i:s', strtotime($value['created_at'])).'</td>
                        </tr>';
                        $sr++;
                    } 
                $result .= '</table></div>';
            }

        echo $result; die;

        
    }

    public function GetBookingInfoDetailsPdf(){ 
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
        $remark = $_POST['ApproveRemark'];
        $this->booking_model->UpdateAproveStatusAll($_POST['booking_id']); 

        if(trim($remark))
        {
            $remark_data = array('booking_id'=>$_POST['booking_id'],'remark' => trim($remark),'remark_type' => 'apprval','updated_by' => $userid); 
            $this->booking_model->AddRemark($remark_data);  
        }

        $bookings = $this->booking_model->GetBookingInfoDetailsPdf($_POST['booking_id']); 
        //echo "<pre>"; print_r($bookings); die;
        $remarks = $this->booking_model->GetBookingRemarks($_POST['booking_id']); 


        $party_name = $bookings[0]['party_name'];
        $city_name = $bookings[0]['city_name'];
        $booking_date = date("d-m-Y", strtotime($bookings[0]['created_at']));
        //echo "<pre>"; print_r($bookings); die;
        //$result = '<table class="table table-striped table-bordered table-hover" id="datatable_sample"><thead><tr><th>S.No</th><th>Brand name</th><th>Category Name</th><th>Product Name</th><th>Quantity</th><th>Rate(Without ins.)</th><th>Insurance</th><th>Rate(FOR With ins.)</th><th>Broker</th><th>Booked by</th><th>Date/Time</th><th>Status</th><th>Remark</th><th>Terms</th><th>Action</th></tr></thead><tbody>';

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
                    <table colspan style="width:750px;margin:0 auto;">
                         
                        <tr>
                            <td colspan="2" style="padding: 0px 40px;">
                                <table style="width:100%;">
                                    <tr>
                                        <td colspan="" style="width:60%">
                                        <p style="font-size: 20px;font-family: Poppins;text-align: left;font-weight: 600;color: #404041;line-height:22px;margin: 0;">'.$party_name.'</p>
                                        <p style="font-size: 18px;font-family: Poppins;text-align: left;font-weight: 600;color: #404041;line-height:22px;margin: 0;">'.$city_name.'</p>
                                        </td>
                                        <td colspan="" style="padding: 10px 0px 10px 10px;background: #f2fbfe;">
                                        <p style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;">Bargain No : <span style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;padding-left: 20px;">#SHAIL/'.$_POST['booking_id'].'</span></p>
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
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Qty.</p></th>
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Rate</p></th>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Total</p></th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>';

                                        $total_weight = 0; 
                                        if($bookings) { 
                                            $i = 1;
                                            $total = 0;
                                            foreach ($bookings as $key => $value) {
                                                $price = $value['rate'];
                                                $total = $total+($value['quantity']*$value['rate']);
                                        $result .= '<tr  style="background:#d9f3fd;">
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$i.'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['brand_name'].'</p></td>
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['category_name'].'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['product_name'].'</p></td>
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['quantity'].'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['rate'].'</p></td>
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['quantity']*$value['rate'].'</p></td>
                                                        </tr>';
                                                $i++;
                                            }
                                        }
                                        $result .= '</tbody>
                                    <tr  style="background:#404041;border-top: 3px solid #fff;">
                                        <td colspan="6" style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:right;color: #fff;line-height:22px;">Total Amount:</p></td>
                                        <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #fff;line-height:22px;">'.$total.'</p></td>
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
                                foreach ($remarks as $key => $remark) {
                                    $result .= '<li>'.$remark['remark'].'</li>';
                                }  
                            $result .= '</ol></td>
                        </tr>';
                        }
                        $result .= '
                    </table>'; 
            //echo APPPATH."third_party/mpdf/mpdf.php"; die;
            //echo $result; die;
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','0','0','38','18','0','0'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px;">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                <h2 style="font-size: 40px;font-family: Poppins;text-align: right;font-weight: 800;color: #404041;line-height:22px;">Booking Details</h2>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '<table  style="width:100%; ">
                        <tr >
                            <td  colspan="2"  style="background-image: url('.base_url('assets/images/footer-line.png').');background-size: 100% 87px; height: 87px; background-repeat: no-repeat;"> <span style="display: block;color: #fff;padding-top: 25px;padding-left: 21px;">{PAGENO} out of {nbpg}</span> 
                           
                            </td>
                        </tr></table>';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($result);
            $f_name = 'dsdsad.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'F');
            include 'mailer/email.php'; 
            $from = "webmaster@dil.in";
            $from_name = "Bargain Invoice";
            $subject   = 'Bargain Invoice'; 

            if($_POST['production_unit']=='alwar')
                $email = 'manvendra.s@bharatsync.com';
            else
                $email = 'rohittak@dil.in';
            smtpmailer($email, $from,$from_name,$subject, "test",$invpice_name);
            //echo $result; die;
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
        $total_rows =  $this->booking_model->CountBookingList($condition);
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



        $bookings = $this->booking_model->GetBookingList($condition,$limit,$page); 
        $i = 1;
        $response ='';
        if($bookings)
        {
            foreach ($bookings as $key => $value) { 
                $response .='<tr class="odd gradeX"><td>'.$i.'</td><td><span title="'.$value['admin_name'].'">DATA/'.$value['booking_id'].'</span></td>

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
    public function index(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $allow_rate_booking = $this->session->userdata('admin')['allow_rate_booking'];
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
            $insurance = $this->input->post('insurance');   
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
                ///$insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate);
                $product_info = $this->category_model->Productinfobyid($product);
                $loose_rate = $product_info['loose_rate'];
                $weight = $product_info['weight'];
                $vendor_condition = array('id' =>$party);
                $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);
                //echo "<pre>"; print_r($vendor_info); die;
                $for_rate_per_kg = $vendor_info['for_rate'];
                $total_loose_rate = $loose_rate*$quantity;
                $total_weight = $weight*$quantity;
                $for_price = $for_rate_per_kg;
                $total_for_price = $for_rate;
                
                $insurance_amount = (($total_price*$insurance)/100)+$total_price;
                $rate1 = $rate;
                $total_for_price1 = 0;
                if($is_for==0)
                {
                    $for_rate = $for_rate_per_kg*$weight;                    
                    $total_for_price1 = $rate;
                    $rate1 = $rate-$for_rate;
                }

                $total_price = $rate1*$quantity;

                

                $today_cur_date =  date("dm"); 
                $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 

                if($booking_date!='')
                { 
                    $today_cur_date = date("dm", strtotime($booking_date));
                    $book_chek_date = $booking_date." 00:00:00.000000";

                }  
                $new_booking_id  = $this->booking_model->getlast_booking_id($book_chek_date);
                $booking_count = $this->booking_model->CheckBooking($book_chek_date);
                 
                    if(!$booking_count)
                        $new_booking_id =0;

                if($broker=='')
                    $broker = 0;
                if($insurance=='')
                    $insurance = 0.00;
                $insertdata = array('booking_id' =>$new_booking_id+1,'party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate1,'loose_rate' =>$loose_rate,'total_loose_rate' =>$total_loose_rate,'total_weight' =>$total_weight,'total_price' =>$total_price,'insurance' =>$insurance,'insurance_amount' =>$insurance_amount,'broker_id' =>$broker,'admin_id' =>$admin_id,'is_for' =>$is_for,'for_total' => $total_for_price1,'for_price' => $for_price,'dispatch_delivery_terms' => trim($dispatch_delivery_terms),'payment_terms' => trim($payment_terms));

                if($booking_date!='')
                     $insertdata['created_at'] = $booking_date.':00'; 
                $insertdata['status'] = 0;
                if ($role==2) { //checker
                   $insertdata['status'] = 1;
                }
                elseif ($role==3|| $role==4) { //approver
                   $insertdata['status'] = 2;
                }

                $result = $this->booking_model->AddBooking($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Booked successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('booking');
            }
        }  
        
        //echo "<pre>"; print_r($this->session->userdata('admin')['id']); die;
        
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
        //echo "<pre>"; print_r($condition); die;

        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $config = array();
        $config["base_url"] = base_url() . "booking/index/";
        $total_rows =  $this->booking_model->CountBookingList($condition);
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



        $data['bookings'] = $this->booking_model->GetBookingList($condition,$limit,$page); 

        $data['brokers'] = $this->broker_model->GetBrokers(); 

        $data['brands'] = $this->brand_model->GetAllBrand();

        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->vendor_model->GetUsersByState($states_ids);
        $data['makers'] = $this->admin_model->GetAllMakers();

        $data['categories'] = $this->category_model->GetCategories();

        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['users']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['allow_rate_booking'] = $allow_rate_booking;
        
        $this->load->view('booking',$data);

    }

    public function test()
    {

        echo $approval_status =   $this->booking_model->GetBargainapproved_whatsapp_status(543);

        die;
       $info =   $this->booking_model->GetBookingInfoById(726);
       $partname = $info['party_name'];
       $brand_name = $info['brand_name'];
       $category_name = $info['category_name'];
       $quantity = $info['quantity'];
       $rate = $info['rate'];
       $booking_id = $info['booking_id'];
       $maker_mobile = $info['maker_mobile'];
       $vendor_mobile = $info['vendor_mobile'];
       $whatsapp_message = urlencode($partname."~".$brand_name." ".$category_name."~".$quantity."~".$rate."~#DATA/".$booking_id); 
       $mobile_numbars = 7792047479;//($vendor_mobile) ? $vendor_mobile.','.$maker_mobile : $maker_mobile; 
       $curl_watsappapi = curl_init();
        curl_setopt_array($curl_watsappapi, array( 
        CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbars.'&TID=9153523&P='.$whatsapp_message,
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
       echo "<pre>"; print_r($response); die;
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
        
        $approval_status =   $this->booking_model->GetBargainapproved_whatsapp_status($bargain_id);


        $condition = array('booking_id' => $bargain_id);
        //$data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        $updatedata = array('status'=>2,'approved_whatsapp_status'=>1);
        $approved =   $this->booking_model->UpdateBooking($updatedata,$condition);
        if($approved)
        {

           $info =   $this->booking_model->GetBookingInfoById($bargain_id);

           
           $is_rate_quantity_changed = $info['is_rate_quantity_changed'];
           $partname = $info['party_name'];
           $brand_name = $info['brand_name'];
           $category_name = $info['category_name'];
           $quantity = $info['quantity'];
           $total_weight = $info['total_weight'];
           $rate = $info['rate'];
           $booking_id = $info['booking_id'];
           $maker_mobile = $info['maker_mobile'];
           $vendor_mobile = $info['vendor_mobile'];


           $remark = "Bargain Approved Rate @ ".$rate." and Qty @ ".$quantity." and weight @ ".$total_weight;
           $remarkdata = array('booking_id' => $info['id'],'remark' => $remark,'remark_type'=> 'Bargain Approved','updated_by' => $admin_id);
            $this->booking_model->AddRemark($remarkdata);

            $small_pack_info ="";
            $north_east_sates = array(4,25,24,23,22,33,30,3);
            if( (($info['qunatity_in_numbers']>0) || ($info['small_pack_rate']>0)) && in_array($info['state_id'], $north_east_sates) )
            {
                $small_pack_info = " - bargain calculated from Quantity in numbers ".$info['qunatity_in_numbers']." and  Small pack rate ".$info['small_pack_rate'];
            }
            $rate = $rate.$small_pack_info;
           $whatsapp_message = urlencode($partname."~".$brand_name." ".$category_name."~".$quantity."~".$rate."~#DATA/".$booking_id); 
           $mobile_numbars = ($vendor_mobile) ? $vendor_mobile.','.$maker_mobile : $maker_mobile; 
           if($is_rate_quantity_changed) 
                $mobile_numbars = $mobile_numbars.",9828066666,9828077777";
           $curl_url = 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbars.'&TID=9153523&P='.$whatsapp_message;
           if($approval_status)
                $curl_url = 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbars.'&TID=1021746973&P='.$whatsapp_message;

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

            $response = curl_exec($curl_watsappapi);
            curl_close($curl_watsappapi);
            //echo $response; 
            if($response==21)
            {
                $mobile_numbers =  str_replace(',',', ',$mobile_numbars);
                $remark = "Order by ".$partname." for ".$brand_name." ".$category_name." has been booked for ".$quantity."(15 Ltr tin) @".$rate." with bargain number #DATA/".$booking_id.".";
                if($approval_status)
                    $remark = "Revised order by ".$partname." for  ".$brand_name." ".$category_name." has been booked for ".$quantity."(15 Ltr tin) @".$rate." with bargain number #DATA/".$booking_id.".";
                $remarkdata = array('booking_id' => $info['id'],'remark' => $remark,'remark_type'=> 'Bargain Approval Whatsapp message sended to '.$mobile_numbers,'updated_by' => $admin_id);
                $this->booking_model->AddRemark($remarkdata);

                $sender_number = '918764216255'; 
                $mobile_number_array =  explode(',', $mobile_numbars);
                foreach ($mobile_number_array as $key => $value) {
                    $whatsapp_log_data = array('mobile_number' => $value,'message' => $remark,'bargain_number' => $booking_id,'sender_number'=> $sender_number,'receiving_time'=> date('Y-m-d H:i:s'));
                    $this->booking_model->AddWhatsappLog($whatsapp_log_data);
                }
                
            }
        }
        echo $approved;
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
        //$data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        $data['booking_info'] = $this->booking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->booking_model->GetSkuinfo($condition);  
 
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



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Add Insurance in price  : '.$insurance);
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
        //$data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        $data['booking_info'] = $this->booking_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->booking_model->GetSkuinfo($condition);  
 
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



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Add Insurance in price  : '.$insurance);
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
        $bargains = $this->booking_model->getpenidngbargainInfo($condition);
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

                $skus = $this->booking_model->GetSkuinfo($condition);  
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


                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Add Insurance in price  : '.$insurance);
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
        $this->booking_model->updatebargiansmail($bargaind_ids);
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
            $packing_weight_net = $_POST['packing_weight_net'];
            $update_ids = $_POST['update_id'];
            if($products)
            {
                $added = 1;
                if($_POST['update_data'])
                {
                    $weight_update =  $this->input->post('update_weight'); 
                    $condition_booking = array('id' => $id);
                    $update_data_booking = array('brand_id' => $brand,'category_id' => $category,'total_weight' => $weight_update);
                    $this->booking_model->UpdateBookingBooking($update_data_booking,$condition_booking); 
                    $condition_all = array('booking_id' => $id,'pi_id' => 0,'is_lock' => 0);
                    $this->booking_model->DeleteSKU($condition_all); 
                } 
                $update_data_booking_weight = array('total_weight_input' => $_POST['total_weight_input'],'remaining_weight' => $_POST['remaining_weight'],'is_lock' => $_POST['flag'],'production_unit' => $_POST['production_unit']);
                $condition_booking_weight = array('id' => $id);
                $this->booking_model->UpdateBookingBooking($update_data_booking_weight,$condition_booking_weight); 

                foreach ($products as $key => $product) {                    
                    
                        $condition = array( 
                            'booking_id' => $id,
                            'bargain_id' => $booking_id,
                            'product_id' => $product, 
                        );
                        if($update_ids[$key]>0)
                        {
                            $condition['id'] = $update_ids[$key];
                        }
                        $skudata = array(
                            'brand_id' => $brand,
                            'category_id' => $category, 
                            'booking_id' => $id,
                            'bargain_id' => $booking_id,
                            'product_id' => $product,
                            'weight' => $packing_weight[$key],
                            'net_weight' => $packing_weight_net[$key],
                            'quantity' => ($quantities[$key]) ? $quantities[$key] : NULL,
                        );
                        if($_POST['flag']==1)
                        {
                            $skudata['is_lock'] = 1;
                        } 
                        //echo "<pre>"; print_r($condition); die;
                    if($quantities[$key])
                    {
                        $flag = $this->booking_model->AddSKU($condition,$skudata); 
                        if(!$flag)
                            $added = 0;
                        
                        if($_POST['flag']){
                            //$this->sendmail_plant_lock($booking_id);
                        }
                    }
                    if($update_ids[$key]>0 && ($quantities[$key]==0 || trim($quantities[$key]=='') ))
                    {
                        $condition['pi_id'] = 0;
                        $condition['is_lock'] = 0;
                        $condition['id'] = $update_ids[$key];
                        $this->booking_model->DeleteSKU($condition); 
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
    public function add_booking(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        if($role==4)
        {
            $admin_id = $this->input->post('sales_executive');            
        }
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category');  
            $rate = $this->input->post('rate');                  
            $quantity = $this->input->post('quantity');  
            $weight = $this->input->post('weight');   
            $total_weight_net = $this->input->post('net_weight');  

            if(strtolower($_POST['production_unit'])=='jaipur')
               $weight = $total_weight_net;
            $booking_date = $this->input->post('booking_date'); 
            $booking_number = 0;
            $today_cur_date =  date("dm"); 
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            $broker = ($_POST['broker']!='') ? $this->input->post('broker') : NULL; // $this->input->post('broker');   
            $insurance = (isset($_POST['insurance'])>0) ? $this->input->post('insurance') : 0;
            $is_for = (isset($_POST['ex_factory'])) ? $this->input->post('ex_factory') : 0;

            $remark = $this->input->post('remark'); 
            $sales_executive = $this->input->post('sales_executive'); 
            $order_recieved = $this->input->post('order_recieved'); 
            $shipment_date = $this->input->post('shipment_date'); 

            $category_condition = array('category.id' =>$category);
            $category_info = $this->category_model->GetCategorInfo($category_condition); 

            $vendor_condition = array('id' =>$party);
            $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);



            $vendor_state = $vendor_info['state_id'];
            $empty_tin_rate_condition = array('empty_tin_rates.base_rate' => 1,'empty_tin_rates.brand_id' => $brand,'empty_tin_rates.category_id' => $category,'empty_tin_rates.state_id' => $vendor_state) ;
            $empty_tin_rate_info = $this->category_model->Emtpytinratebyid($empty_tin_rate_condition);
            $empty_tin_rate = $empty_tin_rate_info['rate'];
            $insurance_rate = $empty_tin_rate_info['insurance'];
            $loose_oil_rate =  $rate;
            if($is_for == 0 && $vendor_info['for_rate'])
            {
                
                if($vendor_info['tax_included'])
                {
                    $loose_oil_rate = (100*$loose_oil_rate)/(100+5); 
                } 
                $loose_oil_rate =  (($loose_oil_rate)-($vendor_info['for_rate']*13.650));
            }
            $loose_oil_rate =  $loose_oil_rate-$empty_tin_rate;
            //$loose_oil_rate =  $rate-$empty_tin_rate;
            if($booking_date!='')
            { 
                $today_cur_date = date("dm", strtotime($booking_date));
                $book_chek_date = $booking_date." 00:00:00.000000";

            }  
            $new_booking_id  = $this->booking_model->getlast_booking_id($book_chek_date);             
            $insurance_amount = 0.00;  
            $insurance_percentage = 0.00;
            if($insurance)
            {
                $insurance_amount = (($rate*$insurance_rate)/100);
                $insurance_amount = round($insurance_amount,2);
                $insurance_percentage = $insurance_rate;
            }

            $qunatity_in_numbers = (isset($_POST['quantity_numbers']) && $_POST['quantity_numbers']>0) ? $this->input->post('quantity_numbers') : 0;
            $small_pack_rate = (isset($_POST['small_pack_rate']) && $_POST['small_pack_rate'] >0) ? $this->input->post('small_pack_rate') : 0;


            $loose_rate_per_kg = round(($loose_oil_rate/13.65),3);
            if(strtolower($category_info['category_name'])=='vanaspati')
                $loose_rate_per_kg = round(($loose_oil_rate/13.455),3);
            


            if($vendor_info['state_id']==4 || $vendor_info['state_id']==22 || $vendor_info['state_id']==23 || $vendor_info['state_id']==24 || $vendor_info['state_id']==25 || $vendor_info['state_id']==30 || $vendor_info['state_id']==33|| $vendor_info['state_id']==3)
            {
                $loose_rate_per_kg = $loose_rate_per_kg-5;
            }


            $insertdata = array(
                'booking_id' =>$new_booking_id+1,
                'party_id' =>$party,
                'brand_id' =>$brand,
                'category_id' =>$category, 
                'quantity' =>$quantity,
                //'weight' =>$quantity*15,
                'rate' =>$rate,
                'loose_oil_rate' =>$loose_oil_rate,
                'broker_id' =>$broker,
                'insurance' =>$insurance_percentage,
                'insurance_amount' =>$insurance_amount,
                'is_for' =>$is_for,
                'admin_id' =>$admin_id,
                'total_weight' => $weight,
                'total_weight_net' => $total_weight_net,
                'production_unit' => $_POST['production_unit'],
                'remark' =>$remark,
                'sales_executive_id' =>$sales_executive,
                'shipment_date' =>date('Y-m-d', strtotime($shipment_date)),
                'order_recieved_via' =>$order_recieved,
                'qunatity_in_numbers' => $qunatity_in_numbers,
                'small_pack_rate' => $small_pack_rate,
                'loose_oil_rate_kg' => $loose_rate_per_kg
            );
            //echo "<pre>"; print_r($insertdata); die; 
            if($booking_date!='')
                 $insertdata['created_at'] = $booking_date.':00'; 
            $insertdata['status'] = 0;
            if ($role==2) { //checker
               $insertdata['status'] = 1;
            }
            elseif ($role==3) { //approver
               $insertdata['status'] = 2;
            }
            if($role==4)
            {
                $insertdata['super_admin_id'] = $admin_info['id'];;
            }
            $result = $this->booking_model->AddBooking($insertdata);

            if($result)
            {
                if($_POST['previous_rate']!==$rate)
                {
                    $remark = "Bargain saved Rate @ ".$_POST['previous_rate'].":".$rate." and Qty @ ".$quantity.":".$quantity." and weight @ ".round($weight,2).":".round($weight,2) ;
                    $remarkdata = array('booking_id' => $result['id'],'remark' => $remark,'remark_type'=> 'Bargain saved with rate change','updated_by' => $admin_id);
                    $this->booking_model->AddRemark($remarkdata);
                }
                echo $result['booking_id'];
            }
            else
            {
                echo 0;
            }

            //$this->add_accounts_history($party);
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
                $opening_amount = 0;
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

    public function update_booking(){ 
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
            $total_weight_net = $this->input->post('net_weight');  

            if(strtolower($_POST['production_unit'])=='jaipur')
               $weight = $total_weight_net;
           
            $booking_date = $this->input->post('booking_date'); 
            $booking_number = 0;
            $today_cur_date =  date("dm"); 
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            $broker = ($_POST['broker']!='') ? $this->input->post('broker') : NULL; // $this->input->post('broker');   
            $insurance = (isset($_POST['insurance'])>0) ? $this->input->post('insurance') : 0;
            $is_for = (isset($_POST['ex_factory'])) ? $this->input->post('ex_factory') : 0;

            $remark = $this->input->post('remark'); 
            $sales_executive = $this->input->post('sales_executive'); 
            $order_recieved = $this->input->post('order_recieved'); 
            $shipment_date = $this->input->post('shipment_date'); 
            

            $vendor_condition = array('id' =>$party);
            $vendor_info = $this->vendor_model->GetUserbyId($vendor_condition);
            $vendor_state = $vendor_info['state_id'];
            $empty_tin_rate_condition = array('empty_tin_rates.base_rate' => 1,'empty_tin_rates.brand_id' => $brand,'empty_tin_rates.category_id' => $category,'empty_tin_rates.state_id' => $vendor_state) ;
            $empty_tin_rate_info = $this->category_model->Emtpytinratebyid($empty_tin_rate_condition);
            $empty_tin_rate = $empty_tin_rate_info['rate'];
            $insurance_rate = $empty_tin_rate_info['insurance'];
            //$loose_oil_rate =  $rate-$empty_tin_rate;
            $loose_oil_rate =  $rate;
            if($is_for == 0 && $vendor_info['for_rate'])
            {
                
                if($vendor_info['tax_included'])
                {
                    $loose_oil_rate = (100*$loose_oil_rate)/(100+5); 
                } 
                $loose_oil_rate =  (($loose_oil_rate)-($vendor_info['for_rate']*13.650));
            }
            $loose_oil_rate =  $loose_oil_rate-$empty_tin_rate;
            
            $insurance_amount = 0.00;  
            $insurance_percentage = 0.00;
            if($insurance)
            {
                $insurance_amount = (($rate*$insurance_rate)/100);
                $insurance_amount = round($insurance_amount,2);
                $insurance_percentage = $insurance_rate;
            }

            $qunatity_in_numbers = (isset($_POST['quantity_numbers']) && $_POST['quantity_numbers']>0) ? $this->input->post('quantity_numbers') : 0;
            $small_pack_rate = (isset($_POST['small_pack_rate']) && $_POST['small_pack_rate'] >0) ? $this->input->post('small_pack_rate') : 0;


            $category_condition = array('category.id' =>$category);
            $category_info = $this->category_model->GetCategorInfo($category_condition); 

            $loose_rate_per_kg = round(($loose_oil_rate/13.65),3);
            if(strtolower($category_info['category_name'])=='vanaspati')
                $loose_rate_per_kg = round(($loose_oil_rate/13.455),3);



            if($vendor_info['state_id']==4 || $vendor_info['state_id']==22 || $vendor_info['state_id']==23 || $vendor_info['state_id']==24 || $vendor_info['state_id']==25 || $vendor_info['state_id']==30 || $vendor_info['state_id']==33|| $vendor_info['state_id']==3)
            {
                $loose_rate_per_kg = $loose_rate_per_kg-5;
            }

            $insertdata = array( 
                'party_id' =>$party,
                'brand_id' =>$brand,
                'category_id' =>$category, 
                'quantity' =>$quantity,
                //'weight' =>$quantity*15,
                'rate' =>$rate,
                'loose_oil_rate' =>$loose_oil_rate,
                'broker_id' =>$broker,
                'insurance' =>$insurance_percentage,
                'insurance_amount' =>$insurance_amount,
                'is_for' =>$is_for,
                //'admin_id' =>$admin_id,
                'total_weight' => $weight,
                'total_weight_net' => $total_weight_net,
                'production_unit' => $_POST['production_unit'],
                'remark' =>$remark,
                'sales_executive_id' =>$sales_executive,
                'shipment_date' =>date('Y-m-d', strtotime($shipment_date)),
                'order_recieved_via' =>$order_recieved,
                'qunatity_in_numbers' => $qunatity_in_numbers,
                'small_pack_rate' => $small_pack_rate,
                'loose_oil_rate_kg' => $loose_rate_per_kg
            );
            $condition = array('id' => $_POST['booking_number']);

            if($_POST['previous_rate']!==$_POST['rate'] || $_POST['previous_qty']!==$_POST['quantity'] || $_POST['previous_weight']!==$_POST['weight'] )
            {
                $insertdata['is_rate_quantity_changed'] = 1;
            }

            $booking_history_data  = array(
                'updated_by' =>$admin_id,
                'booking_id' => $_POST['booking_number'],
                'booking_rate' => $rate,
            );
            echo $result = $this->booking_model->UpdateBooking($insertdata,$condition);
            if($result)
            {
                $brand_remark ="";
                if($_POST['previous_product']!==$_POST['category'] || $_POST['previous_brand']!==$_POST['brand'])
                {
                    $condition_sku = array('booking_id' => $_POST['booking_number'],'pi_id' => 0);
                    $this->booking_model->DeleteSKU($condition_sku); 
                }

                if($_POST['previous_rate']!==$_POST['rate'] || $_POST['previous_qty']!==$_POST['quantity'] || $_POST['previous_weight']!==$_POST['weight'] )
                {
                    $remark = "Bargain Updated Rate @ ".$_POST['previous_rate'].":".$_POST['rate']." and Qty @ ".$_POST['previous_qty'].":".$_POST['quantity']." and weight @ ".round($_POST['previous_weight'],2).":".round($_POST['weight'],2) ;
                    $remarkdata = array('booking_id' => $_POST['booking_number'],'remark' => $remark,'remark_type'=> 'Bargain Update','updated_by' => $admin_id);
                    $this->booking_model->AddRemark($remarkdata);
                }
                $this->booking_model->UpdateBookingHistoryAdd($booking_history_data);
            }
            
        }
    }

    public function booked_sku_info()
    { 
        $condition = array('booking_skus.booking_id' => $_POST['id']);
        $skus = $this->booking_model->GetSkuinfo($condition);
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


    public function sku1($bargain_id){   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_id' => $data['booking_info']['id']);
        $data['skus'] = $this->booking_model->GetAllSkus($condition);
 
        $data['products'] = $this->category_model->GetProductsbycategpry_idactive($data['booking_info']['category_id']);
        //echo "<pre>"; print_r($data['skus']); die;

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

    public function sku($bargain_id){   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_id' => $data['booking_info']['id']);
        $data['skus'] = $this->booking_model->GetAllSkus($condition);
 
        $data['products'] = $this->category_model->GetProductsbycategpry_idactive($data['booking_info']['category_id']);
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
        $data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->booking_model->GetSkuinfo($condition);  
 
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



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Add Insurance in price  : '.$insurance);
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
        $data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->booking_model->GetSkuinfo($condition);  
 
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



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Add Insurance in price  : '.$insurance);
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
                $result = $this->booking_model->AddBooking($insertdata);
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
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        $allow_rate_booking = $this->session->userdata('admin')['allow_rate_booking'];
        
        $data['booking_info'] = $this->booking_model->GetBookingInfoById1($booking_id);

        //echo "<pre>"; print_r($data['booking_info']); die;

        $condition = array('is_lock' => 1);
        if($role==1) //maker
        {
            $condition = array('admin_id' => $userid);
        }
        elseif ($role==2) { //checker
            $condition = array('admin.team_lead_id' => $userid);
        } 
        //echo "<pre>"; print_r($condition); die; 
        $data['brokers'] = $this->broker_model->GetBrokers(); 

        $data['brands'] = $this->brand_model->GetAllBrand();

        $data['users'] = $this->vendor_model->GetUsers();
        $data['makers'] = $this->admin_model->GetAllMakers();
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['categories'] = $this->category_model->GetCategory($condition);
        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //echo "<pre>"; print_r($data['categories']); die;
        //echo "<pre>"; print_r($data['users']); die;


        

        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['allow_rate_booking'] = $allow_rate_booking;
        $this->load->view('booking_edit',$data);

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
                $result = $this->booking_model->UpdateBooking($insertdata,$condition);
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
        $data['booking_info'] = $this->booking_model->GetBookingInfoById($booking_id);
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
            $result = $this->booking_model->DeleteBooking($condition); 
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
            $total_rows =  $this->booking_model->CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status);
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

            $data['bookings'] = $this->booking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page);
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

    public function report(){    
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
        $data['total_dispatch'] = 0;
        $data['total_bargain'] = 0;
        if(!empty($_POST) || isset($_SESSION['search__report_data']))
        //if(!empty($_POST))
        {



            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__report_data'] = $_POST;
            else
                $_POST = $_SESSION['search__report_data']; 

            if(!isset($_POST['summary_submit']))
            {
                if(isset($_POST['bargain_search']) && !empty($_POST['bargain_search']))
                {
                    $_POST['party'] = '';
                    $_POST['brand'] = '';
                    $_POST['category'] = '';
                    $_POST['booking_date_from'] = '';
                    $_POST['booking_date_to'] = '';
                    $_POST['employee'] = '';
                    $_POST['broker'] = '';
                    $_POST['status'] = '';
                    $_POST['production_unit'] = '';
                    $_POST['pending'] = '';
                } 
                else
                {
                    $_POST['bagainnumber'] = '';
                }
            }
            else
            {
                if(isset($_POST['bagainnumber']) && !empty($_POST['bagainnumber']))
                {
                    $_POST['party'] = '';
                    $_POST['brand'] = '';
                    $_POST['category'] = '';
                    $_POST['booking_date_from'] = '';
                    $_POST['booking_date_to'] = '';
                    $_POST['employee'] = '';
                    $_POST['broker'] = '';
                    $_POST['status'] = '';
                    $_POST['production_unit'] = '';
                    $_POST['pending'] = '';
                }
                else
                {
                    $_POST['bagainnumber'] = '';
                }
            }
            //echo "<pre>"; print_r($_POST); die;
            $party_id = $_POST['party'];
            $brand_id = $_POST['brand'];
            $category_id = $_POST['category']; 
            if(!empty($_POST['booking_date_from']))
                $booking_date_from = date('Y-m-d',strtotime($_POST['booking_date_from']));
            else
                $booking_date_from = '';
            if(!empty($_POST['booking_date_to']))
                $booking_date_to = date('Y-m-d',strtotime($_POST['booking_date_to'])); 
            else
                $booking_date_to = '';
            $booking_status = (isset($_POST['status'])) ? $_POST['status'] : '';
            $role = $this->session->userdata('admin')['role'];
            $booking_pending_days = (isset($_POST['pending'])) ? $_POST['pending'] : '';

            $employee = $_POST['employee']; 
            $broker = $_POST['broker']; 
            $unit = $_POST['production_unit']; 
            $bagainnumber = $_POST['bagainnumber']; 

            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            }
            if(!isset($_POST['summary_submit']))
            {
                //echo "<pre>"; print_r($_POST); die;
                //$this->session->set_userdata('search__report_data', $_POST); 
                
                $this->load->library("pagination");

                $limit = 20;
                if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                    $limit = $conditions_data['limit'];
                } 

                $config = array();
                $config["base_url"] = base_url() . "booking/report/";
                $total_rows =  $this->booking_model->CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$employee,$unit,$booking_pending_days,$bagainnumber,$broker);

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
                $data['bookings'] = $this->booking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit,$booking_pending_days,$bagainnumber,$broker);

                $data['total_dispatch'] = $this->booking_model->GetBookingDispatch($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit,$booking_pending_days,$bagainnumber,$broker);
                $data['total_bargain'] = $this->booking_model->GetBookingBargains($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit,$booking_pending_days,$bagainnumber,$broker);

                //echo "<pre>"; print_r($data); die;
            }
            else
            {
                $data['search_summary'] = 1;
                //echo "<pre>"; print_r($_POST); die;
                $group_by  = array('category_name');
                //echo "<pre>"; print_r($group_by); die;
                $data['bookings_product'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$booking_pending_days,$broker,$bagainnumber);
                //echo "<pre>"; print_r($data['bookings_product']); die;
                $group_by  = array('brand_id','category_id');
                //echo "<pre>"; print_r($group_by); die;
                $data['bookings_brand_product'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$booking_pending_days,$broker,$bagainnumber);
                //echo "<pre>"; print_r($data['bookings_brand_product']); die;


                $group_by  = array('place','brand_id','category_id');
                $data['bookings_brand_product_place'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$booking_pending_days,$broker,$bagainnumber);

                //echo "<pre>"; print_r($data['bookings_brand_product_place']); die;

                $group_by  = array('status');
                $data['sum_report'] = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,'',$unit,$booking_pending_days,$broker,$bagainnumber);

                //echo "<pre>"; print_r($data['sum_report']); die;

                $data['tot_sum_report'] = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,'',$unit,$booking_pending_days,$broker,$bagainnumber); 

                $data['locked'] = $this->booking_model->GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,$unit,$booking_pending_days,$broker,$bagainnumber);


                $data['product_packing_type_summary'] = $this->booking_model->product_packing_type_summary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,'',$unit,$booking_pending_days,$broker,$bagainnumber);  


                $group_by  = array('broker','place');
                $data['bookings_broker_place'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$booking_pending_days,$broker,$bagainnumber);

                //echo "<pre>"; print_r($data['bookings_broker_place'] ); die;
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
        //echo "<pre>"; print_r($data); die;
        $data['brokers'] = $this->broker_model->GetBrokers(); 
        $this->load->view('booking_report',$data);

    }


    public function report_print(){  
        ini_set('max_execution_time', 0);  
        ini_set("memory_limit", "512M"); 
        set_time_limit(0);
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
        $bookings = $this->booking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);
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
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['quantity'].'</td>';
                                $loose_rate_per_kg = round(($value['loose_oil_rate']/13.65),3);
                                if($value['state_id']==4 || $value['state_id']==22 || $value['state_id']==23 || $value['state_id']==24 || $value['state_id']==25 || $value['state_id']==30 || $value['state_id']==33 || $value['state_id']==3)
                                {
                                    $loose_rate_per_kg = $loose_rate_per_kg-5;
                                }
                                $html_print .= '<td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['rate'].' ('.$ex.') <br><strong>'.$loose_rate_per_kg.'/Kg</strong></td>
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
        $f_name = 'Bargain Report From '.$_REQUEST['from'].' to '.$_REQUEST['to'].'.pdf';
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
        $bookings = $this->booking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);

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
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Rate (With Insurance) (15Ltr Tin)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Rate (Per Kg)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weight (MT)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Total Dispatch');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Pending Dispatch');$col++;
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
                $rate_with_insurance = '';
                if($value['insurance_amount'])
                    $rate_with_insurance = $value['rate']+$value['insurance_amount'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$rate_with_insurance);$col++;

                $loose_rate_per_kg = round(($value['loose_oil_rate']/13.65),3);
                if($value['state_id']==4 || $value['state_id']==22 || $value['state_id']==23 || $value['state_id']==24 || $value['state_id']==25 || $value['state_id']==30 || $value['state_id']==33 || $value['state_id']==3)
                {
                    $loose_rate_per_kg = $loose_rate_per_kg-5;
                }
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$loose_rate_per_kg);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($value['weight'],2));$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($value['total_dispatch'],2));$col++; 
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,(round($value['weight'],2)-round($value['total_dispatch'],2)));$col++; 
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
        $broker = $_GET['broker']; 
        $unit = $_GET['production_unit']; 
        $pending = $_GET['pending']; 
        $group_by  = array('status');
        $sum_report = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,'',$unit,$pending,$broker);

        $tot_sum_report = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,1,$unit,$pending,$broker);

        $locked = $this->booking_model->GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,$unit,$pending,$broker);  

        //echo "<pre>"; print_r($data['locked']); die; 
        if($_GET['type']=='place')
        {
            $group_by  = array('place','brand_id','category_id');
            $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$pending,$broker);

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
                                            elseif ($sum_report_value['status']==6) {
                                                $sumtype =  "Partial Rejected";
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
                            <tr><th colspan="10" style="text-align:center;border-bottom:1px solid #000;">Order summary based on state from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">S.No</th>    
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">State</th>  
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Brand</th>   
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Product</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Qty <br> (Tins/Cartons)</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Weight <br>  (MT)</th>

                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Locked Bulk Weight <br>  (MT)</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Locked Consumer Weight <br>  (MT)</th>


                                <th style="text-align:left; border-bottom:1px solid #000;"  colspan="2">Avg Rate / Loose(kg)</th>
                            </tr>
                            <tr>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                        $weight_bulk_total_summary = 0;
                        $weight_cunsomer_total_summary = 0;  
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                     $weight_bulk_total_summary = $weight_bulk_total_summary+$bookings_brand_product_place1['bulk_weight'];
                                    $weight_cunsomer_total_summary = $weight_cunsomer_total_summary+$bookings_brand_product_place1['consumer_weight'];

                                    $avg_rate_other = '';
                                    $avg_rate_aasam1 = '';
                                    if($bookings_brand_product_place1['avg_rate_other'])
                                    {
                                        $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                        $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                        
                                        $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_other_loose'],2);
                                        $loose_rate = ($avg_rate_loose)/$loose_kg;
                                        $loose_rate = round($loose_rate,2);
                                        $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                    }

                                    if($bookings_brand_product_place1['avg_rate_aasam'])
                                    {
                                        $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);
                                        $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                        $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;                                       

                                        $loose_rate_aasam = round($loose_rate_aasam,2);


                                        $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_aasam_loose'],2);
                                        $loose_rate = ((($avg_rate_loose)/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;
                                        $loose_rate_aasam = round($loose_rate,2);

                                        $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                    }



                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['state_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 

                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['bulk_weight'],2).'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['consumer_weight'],2).'</td> 
                                    
                                    <td style="text-align:left; border-bottom:1px solid #000;border-right:1px solid #000;">'.$avg_rate_other.'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td> 
                                </tr>';
                            $sn++; }
                            $html_print .= '<tr style="color:red;"><td colspan="4"></td><td>Total</td><td>'.round($weight_total_summary,2).'</td><td>'.round($weight_bulk_total_summary,2).'</td><td>'.round($weight_cunsomer_total_summary,2).'</td><td></td></tr>';
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
        elseif($_GET['type']=='broker')
        {
            $group_by  = array('broker','place');
            $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$pending,$broker);

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
                                            elseif ($sum_report_value['status']==6) {
                                                $sumtype =  "Partial Rejected";
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
                            <tr><th colspan="10" style="text-align:center;border-bottom:1px solid #000;">Order summary based on Broker state from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">S.No</th> 
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Broker</th>   
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">State</th>  
                                  
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Qty <br> (Tins/Cartons)</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Weight <br>  (MT)</th>

                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Locked Bulk Weight <br>  (MT)</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Locked Consumer Weight <br>  (MT)</th>


                                <th style="text-align:left; border-bottom:1px solid #000;"  colspan="2">Avg Rate / Loose(kg)</th>
                            </tr>
                            <tr>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                        $weight_bulk_total_summary = 0;
                        $weight_cunsomer_total_summary = 0;  
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                     $weight_bulk_total_summary = $weight_bulk_total_summary+$bookings_brand_product_place1['bulk_weight'];
                                    $weight_cunsomer_total_summary = $weight_cunsomer_total_summary+$bookings_brand_product_place1['consumer_weight'];

                                    $avg_rate_other = '';
                                    $avg_rate_aasam1 = '';
                                    if($bookings_brand_product_place1['avg_rate_other'])
                                    {
                                        $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                        $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                        
                                        $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_other_loose'],2);
                                        $loose_rate = ($avg_rate_loose)/$loose_kg;
                                        $loose_rate = round($loose_rate,2);
                                        $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                    }

                                    if($bookings_brand_product_place1['avg_rate_aasam'])
                                    {
                                        $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);
                                        $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                        $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;                                       

                                        $loose_rate_aasam = round($loose_rate_aasam,2);


                                        $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_aasam_loose'],2);
                                        $loose_rate = ((($avg_rate_loose)/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;
                                        $loose_rate_aasam = round($loose_rate,2);

                                        $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                    }



                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['broker_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['state_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 

                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['bulk_weight'],2).'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['consumer_weight'],2).'</td> 
                                    
                                    <td style="text-align:left; border-bottom:1px solid #000;border-right:1px solid #000;">'.$avg_rate_other.'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td> 
                                </tr>';
                            $sn++; }
                            $html_print .= '<tr style="color:red;"><td colspan="3"></td><td>Total</td><td>'.round($weight_total_summary,2).'</td><td>'.round($weight_bulk_total_summary,2).'</td><td>'.round($weight_cunsomer_total_summary,2).'</td><td></td></tr>';
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
            $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$pending,$broker);
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
                                            elseif ($sum_report_value['status']==6) {
                                                $sumtype =  "Partial Rejected";
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
            $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,$pending,$broker);
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
                                            elseif ($sum_report_value['status']==6) {
                                                $sumtype =  "Partial Rejected";
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
                                            elseif ($sum_report_value['status']==6) {
                                                $sumtype =  "Partial Rejected";
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
                        $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,'',$broker);
                        $html_print .= '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;margin-top:20px;">
                                    <thead>
                                        <tr><th colspan="8" style="text-align:center;border-bottom:1px solid #000;">Order summary based on product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">S.No</th>    
                                            <th rowspan="2"  style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Product</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Qty <br> (Tins/Cartons)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle; ">Weight <br> (MT)</th>

                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle; ">Locked Bulk Weight <br> (MT)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle; ">Locked Consumer Weight <br> (MT)</th>

                                            <th colspan="2"  style="text-align:center; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                        <tr>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                     $weight_bulk_total_summary = 0;
                                        $weight_cunsomer_total_summary = 0;
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                                $weight_bulk_total_summary = $weight_bulk_total_summary+$bookings_brand_product_place1['bulk_weight'];
                                                $weight_cunsomer_total_summary = $weight_cunsomer_total_summary+$bookings_brand_product_place1['consumer_weight'];

                                                $avg_rate_other = '';
                                                $avg_rate_aasam1 = '';
                                                
                                                if($bookings_brand_product_place1['avg_rate_other'])
                                                {
                                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                                    $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_other_loose'],2);
                                                    $loose_rate = ($avg_rate_loose)/$loose_kg;
                                                    $loose_rate = round($loose_rate,2);
                                                    $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                                }
                                                if($bookings_brand_product_place1['avg_rate_aasam'])
                                                {
                                                    $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);


                                                    $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;


                                                    $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_aasam_loose'],2);

                                                    $loose_rate_aasam = ((($avg_rate_loose)/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15; 
                                                    $loose_rate_aasam = round($loose_rate_aasam,2);
                                                    $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                                }

                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['bulk_weight'],2).'</td>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['consumer_weight'],2).'</td>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$avg_rate_other.'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td> 
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="2"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td >'.round($weight_bulk_total_summary,2).'</td><td >'.round($weight_cunsomer_total_summary,2).'</td><td colspan="2"></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="9">No Record Found</td></tr>';
                                        }
                                    $html_print .= '</tbody>
                                </table>';


                        $group_by  = array('brand_id','category_id');        
                        $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit,'',$broker);

                        $html_print .= '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%; margin-top:20px; ">
                                    <thead>
                                        <tr><th colspan="9" style="text-align:center;border-bottom:1px solid #000;">Order summary based on brand and product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">S.No</th>   
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Brand</th>   
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Product</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Qty <br>(Tins/Cartons)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Weight  <br> (MT)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle; ">Locked Bulk Weight <br> (MT)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle; ">Locked Consumer Weight <br> (MT)</th>
                                            <th colspan="2" style="text-align:center; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                        <tr>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                        $weight_bulk_total_summary = 0;
                                        $weight_cunsomer_total_summary = 0;
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                                $weight_bulk_total_summary = $weight_bulk_total_summary+$bookings_brand_product_place1['bulk_weight'];
                                                $weight_cunsomer_total_summary = $weight_cunsomer_total_summary+$bookings_brand_product_place1['consumer_weight'];

                                                $avg_rate_other = '';
                                                $avg_rate_aasam1 = '';
                                                if($bookings_brand_product_place1['avg_rate_other'])
                                                {
                                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650;

                                                    $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_other_loose'],2);

                                                    $loose_rate = ($avg_rate_loose)/$loose_kg;
                                                    $loose_rate = round($loose_rate,2);
                                                    $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                                }

                                                if($bookings_brand_product_place1['avg_rate_aasam'])
                                                {
                                                    $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);
                                                  

                                                    $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;

                                                    $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_aasam_loose'],2);

                                                    $loose_rate_aasam = ((($avg_rate_loose)/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;


                                                    $loose_rate_aasam = round($loose_rate_aasam,2);
                                                    $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                                }
                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td>

                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['bulk_weight'],2).'</td>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['consumer_weight'],2).'</td>

                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$avg_rate_other.'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td>  
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="3"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td >'.round($weight_bulk_total_summary,2).'</td><td >'.round($weight_cunsomer_total_summary,2).'</td><td></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="8">No Record Found</td></tr>';
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
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];  
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;

        $time = date('Y-m-d H:i:s');
        $update_data = array('status' => $_POST['status']);
        if($_POST['status']==4 || $_POST['status']==3 || $_POST['status']==2 || $_POST['status']==0)
        {
            $update_data['approve_reject_time'] = $time;
            $update_data['reject_remark'] = trim($_POST['remark']); 

            if($_POST['status']==0)
            {
                $update_data['is_lock'] = 0;
                $update_data['is_mail'] = 0;
            } 
            if($_POST['status']==4)
            {
                $update_data['is_lock'] = 2; //atleast one sku id locked
				if($_POST['lock_status'] == 0)
					$update_data['is_lock'] = 0; //if no sku id is locked
			   
                $update_data['is_mail'] = 0;
                unset($update_data['status']); 
            }
        }
        else
        {
            $update_data['check_time'] = $time;
        }
        $bargain_names = "";
        if($_POST['status']==5)
        {
            $BargainerId_condition = array('id' => base64_decode($_POST['booking_id']));
            $BargainerId = $this->booking_model->GetBargainerId($BargainerId_condition);
            $bargainer_name_condition = array('id' => $BargainerId['admin_id']);
            $previous_bargainer_name_res = $this->booking_model->GetBargainername($bargainer_name_condition);

            //echo "<pre>"; print_r($previous_bargainer_name_res); die;

            $new_bargainer_condition = array('id' => $_POST['bargainer']);
            $new_bargainer_name_res = $this->booking_model->GetBargainername($new_bargainer_condition);

            $bargain_names = "Bargain changed from ".$previous_bargainer_name_res['bargainer_name']." to ".$new_bargainer_name_res['bargainer_name']." <br>";

            unset($update_data['status']);
            $update_data['admin_id'] = $_POST['bargainer']; 
        } 
        $condition  = array('id' => base64_decode($_POST['booking_id']));
        //echo "<pre>";print_r($update_data); die;
        $result= $this->booking_model->UpdateBooking($update_data,$condition);
        if($result)
        {
            $remark_type  = 'Reject';
            if($_POST['status']==4)
                $remark_type  = 'Unlock';
            if($_POST['status']==0)
                $remark_type  = 'Unapprove';
            if($_POST['status']==5)
                $remark_type  = 'Change Bargainer ';
            if($_POST['status']==6)
                $remark_type  = 'Partial Rejected ';
            $remarkdata = array('booking_id' => base64_decode($_POST['booking_id']),'remark' => $bargain_names.trim($_POST['remark']),'remark_type'=> $remark_type,'updated_by' => $userid);
            $this->booking_model->AddRemark($remarkdata);

            if($_POST['status']==0 || $_POST['status']==4)
            {
                $skuupdate_data = array('is_lock'=>0);
                //$skuupdate_data = array('pi_id'=>0);
                $condition  = array('booking_id' => base64_decode($_POST['booking_id']),'pi_id'=>0);
                $this->booking_model->UpdateBookingSku($skuupdate_data,$condition);
            }
        }
        echo $result;
    }


    public function details(){  
       
        $condition  = array('id' => base64_decode($_POST['booking_id']));
        $approve_reject_time= $this->booking_model->Bookingdetils($condition);
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
        $weight = $this->booking_model->getbargainweight($condition);  
        $pending_condition  = array('party_id'=>$party_id,'is_mail'=>0,'is_lock'=>0,'status <> '=>3,);
        $bargains = $this->booking_model->getpenidngbargain($pending_condition); 

        //echo "<pre>"; print_r($bargains); die;
        $bargainweight = 0;
        if($weight['weight'])
            $bargainweight = $weight['weight']; 

        echo $bargainweight.'__'.$bargains;
        //echo "<pre>"; print_r($weight); die;
    }


    public function update_sku_status(){     
        $sku_id =  base64_decode($_POST['sku_id']); 
        $booking_id =  $_POST['booking_id']; 
        $update_data = array('is_lock' => 1);
        $condition  = array('id' => $sku_id);
        $result= $this->booking_model->UpdateBookingSku($update_data,$condition);
        if($result)
        {
             $update_data_booking = array('is_lock' => 2);
            $condition_booking  = array('id' => $booking_id);
            $this->booking_model->UpdateBooking($update_data_booking,$condition_booking); 
        }
        echo $result;
    } 



    public function state_city_report(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Dispatch report based on state city"; 
        $data['results'] = array(); 
        $data['summary'] = array();  
        $summary=  array(); 
        $data['result_type'] ='';
        if(isset($_POST) && !empty($_POST))
        {
            $state_id = $_POST['state']; 
            $city_id = $_POST['city']; 
            $from_date = date('Y-m-d', strtotime($_POST['from_date']));
            $to_date = date('Y-m-d', strtotime($_POST['to_date']));

            $condition_party = array(
                'STR_TO_DATE(pi_history.dispatch_date,"%d-%m-%Y") >= ' =>$from_date,
                'STR_TO_DATE(pi_history.dispatch_date,"%d-%m-%Y") <= ' =>$to_date, 
                'vendors.state_id' => $state_id, 
            );
            if($city_id)
            {
                $condition_party['vendors.city_id'] = $city_id;
            } 
            if(isset($_POST['report_type']) && trim($_POST['report_type']) =='employee')
            {
                $city_dispatch_conition = "";
                if($city_id)
                {
                    $city_dispatch_conition = " and vendors.city_id=".$city_id." ";
                }

                $data_query_dispetch = " SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month,admin_sku.city_id,admin_sku.state_id, SUM(admin_sku.total_dispateched_net_weight) as total_dispateched_net_weight, SUM(admin_sku.total_dispateched_weight) as total_dispateched_weight, SUM(admin_sku.total_dispatched_amount) as total_dispatched_amount,  SUM(t1.total_dispateched_net_weight) as total_dispateched_net_weight1, SUM(t1.total_dispateched_weight) as total_dispateched_weight1, SUM(t1.total_dispatched_amount) as total_dispatched_amount1, admin.* from admin left join (select vendors.city_id, vendors.state_id,pi_history.id as pid, booking_booking.admin_id, SUM(pi_sku_history.net_weight) as total_dispateched_net_weight, SUM(pi_sku_history.weight) as total_dispateched_weight, SUM(pi_sku_history.amount) as total_dispatched_amount from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON  `booking_booking`.`id` = `pi_sku_history`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> '' ) and booking_booking.status <> 3 and pi_history.status =0 and vendors.state_id=".$state_id." and (( DATE_FORMAT(STR_TO_DATE(`pi_history`.`dispatch_date`,'%d-%m-%Y'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($_POST['from_date']))."' AND '".date('Y-m-d',strtotime($_POST['to_date']))."' )) GROUP by booking_booking.admin_id) admin_sku on admin_sku.admin_id = admin.id 
                    left join ( 
                        select secondary_booking.admin_id, vendors.state_id, SUM(pi_history_secondary_booking.total_net_weight_pi) as total_dispateched_net_weight, SUM(pi_history_secondary_booking.total_weight_pi) as total_dispateched_weight, SUM( pi_history_secondary_booking.pi_amount) as total_dispatched_amount from pi_history_secondary_booking LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`pi_history_secondary_booking`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where pi_history_secondary_booking.status = 0 and vendors.state_id=".$state_id." and (( DATE_FORMAT(STR_TO_DATE(`pi_history_secondary_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($_POST['from_date']))."' AND '".date('Y-m-d',strtotime($_POST['to_date']))."' )) GROUP by secondary_booking.admin_id

                        ) t1 on t1.admin_id = admin.id
                    where (admin.role = 6 OR admin.role=1 )  and ( (find_in_set(".$state_id.",admin.state_id) OR admin.state_id is NUll OR admin.state_id = '') OR admin_sku.state_id=".$state_id." )   Group by admin.id ORDER BY `name` ASC";
                $query_dispetch = $this->db->query($data_query_dispetch);   
                $results = $query_dispetch->result_array(); 
                $data['result_type'] ='employee';
                //echo "<pre>"; print_r($results); die;
            }
            else
            {
                $data['result_type'] ='party';
                $results = $this->booking_model->GetBookingSummaryStateCity($condition_party); 
                $summary = $this->booking_model->GetBookingSummaryStateCitywithoutparty($condition_party); 
            }
            $data['results'] =$results; 
            $data['summary'] =$summary;
        } 
        else
        {
            $_POST['city'] = '';
        }
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $states= $this->vendor_model->GetStates();
        $data['states'] =$states; 
        //echo "<pre>"; print_r($data); die;
        $this->load->view('state_city_report',$data);

    }


    public function state_city_report_excel(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Dispatch report based on state city"; 
        $data['results'] = array(); 
        $data['summary'] = array();  
        $summary=  array(); 
        $data['result_type'] ='';
        if(isset($_POST) && !empty($_POST))
        {
            $state_id = $_POST['state']; 
            $city_id = $_POST['city']; 
            $from_date = date('Y-m-d', strtotime($_POST['from_date']));
            $to_date = date('Y-m-d', strtotime($_POST['to_date']));

            $condition_party = array(
                'STR_TO_DATE(pi_history.dispatch_date,"%d-%m-%Y") >= ' =>$from_date,
                'STR_TO_DATE(pi_history.dispatch_date,"%d-%m-%Y") <= ' =>$to_date, 
                'vendors.state_id' => $state_id, 
            );
            if($city_id)
            {
                $condition_party['vendors.city_id'] = $city_id;
            } 
            if(isset($_POST['report_type']) && trim($_POST['report_type']) =='employee')
            {
                $city_dispatch_conition = "";
                if($city_id)
                {
                    $city_dispatch_conition = " and vendors.city_id=".$city_id." ";
                }

                $data_query_dispetch = " SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month,admin_sku.city_id,admin_sku.state_id, SUM(admin_sku.total_dispateched_net_weight) as total_dispateched_net_weight, SUM(admin_sku.total_dispateched_weight) as total_dispateched_weight, SUM(admin_sku.total_dispatched_amount) as total_dispatched_amount,  SUM(t1.total_dispateched_net_weight) as total_dispateched_net_weight1, SUM(t1.total_dispateched_weight) as total_dispateched_weight1, SUM(t1.total_dispatched_amount) as total_dispatched_amount1, admin.* from admin left join (select vendors.city_id, vendors.state_id,pi_history.id as pid, booking_booking.admin_id, SUM(pi_sku_history.net_weight) as total_dispateched_net_weight, SUM(pi_sku_history.weight) as total_dispateched_weight, SUM(pi_sku_history.amount) as total_dispatched_amount from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON  `booking_booking`.`id` = `pi_sku_history`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> '' ) and booking_booking.status <> 3 and pi_history.status =0 and vendors.state_id=".$state_id." and (( DATE_FORMAT(STR_TO_DATE(`pi_history`.`dispatch_date`,'%d-%m-%Y'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($_POST['from_date']))."' AND '".date('Y-m-d',strtotime($_POST['to_date']))."' )) GROUP by booking_booking.admin_id) admin_sku on admin_sku.admin_id = admin.id 
                    left join ( 
                        select secondary_booking.admin_id, vendors.state_id, SUM(pi_history_secondary_booking.total_net_weight_pi) as total_dispateched_net_weight, SUM(pi_history_secondary_booking.total_weight_pi) as total_dispateched_weight, SUM( pi_history_secondary_booking.pi_amount) as total_dispatched_amount from pi_history_secondary_booking LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`pi_history_secondary_booking`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where pi_history_secondary_booking.status = 0 and vendors.state_id=".$state_id." and (( DATE_FORMAT(STR_TO_DATE(`pi_history_secondary_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($_POST['from_date']))."' AND '".date('Y-m-d',strtotime($_POST['to_date']))."' )) GROUP by secondary_booking.admin_id

                        ) t1 on t1.admin_id = admin.id
                    where (admin.role = 6 OR admin.role=1 )  and ( (find_in_set(".$state_id.",admin.state_id) OR admin.state_id is NUll OR admin.state_id = '') OR admin_sku.state_id=".$state_id." )   Group by admin.id ORDER BY `name` ASC";
                $query_dispetch = $this->db->query($data_query_dispetch);   
                $results = $query_dispetch->result_array(); 
                $data['result_type'] ='employee';
                //echo "<pre>"; print_r($results); die;
            }
            else
            {
                $data['result_type'] ='party';
                $results = $this->booking_model->GetBookingSummaryStateCity($condition_party); 
                $summary = $this->booking_model->GetBookingSummaryStateCitywithoutparty($condition_party); 
            }
            $data['results'] =$results; 
            $data['summary'] =$summary;
        } 
        else
        {
            $_POST['city'] = '';
        }
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $states= $this->vendor_model->GetStates();
        $data['states'] =$states; 
        //echo "<pre>"; print_r($data); die;
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $col='A';
        $row = 1;

        if(isset($_POST['report_type']) && trim($_POST['report_type']) =='employee')
        {
            $report_type = " Employee Wise";
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Employee Name');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Employee Role');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Employee Status');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Joining (Months)');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Mobile Number');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Total Dispatch (MT)');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Total Net Dispatch (MT)');$col++;
            if($results)
            {
                $row = 2;
                foreach ($results as $key => $value) {
                    $col='A'; 
                    $role = ($value['role']==1) ? 'Maker' : 'Secondary Maker';
                    $status = ($value['status']) ? 'Active' : ' Deactive';
                    $total_dispateched_weight = ($value['total_dispateched_weight']) ? round($value['total_dispateched_weight'],2) : round($value['total_dispateched_weight1'],2);
                    $total_dispateched_net_weight = ($value['total_dispateched_net_weight']) ? round($value['total_dispateched_net_weight'],2) : round($value['total_dispateched_net_weight1'],2); 
                    $joining_month =  ($value['joining_month']) ? $value['joining_month'] : '';

                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name']);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$role);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$status);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$joining_month);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['mobile']);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$total_dispateched_weight);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$total_dispateched_net_weight);$col++;
                    $row++;
                }
            }
        }
        else
        {
            $report_type = " Party Wise";
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Party Name');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Party City');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Total Dispatch (MT)');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Total Net Dispatch (MT)');$col++;
            if($results)
            {
                $row = 2;
                foreach ($results as $key => $value) {
                    $col='A';
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['party_name']);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['city_name']);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['total_dispatch']);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['total_net_dispatch']);$col++;
                    $row++;
                }
            }
        }

        $fileName = 'State City Dispatch Report '.$_POST['from_date'].' to '.$_POST['to_date'].$report_type. '.xls'; 
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save('php://output');  

    }


    public function getallcompnies(){     
        if(isset($_POST) && !empty($_POST))
        {
            $state_id = $_POST['state']; 
            $city_id = $_POST['city']; 
            $parties = $_POST['parties'];  

            $condition_party = array( 
                'parties' => $parties, 
                'vendors.state_id' => $state_id, 
            );
            if($city_id)
            {
                $condition_party['vendors.city_id'] = $city_id;
            }
            $results = $this->booking_model->getallcompnieswithlastdispatch($condition_party); 
            $response = '<div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td>S. No.</td>
                                        <td>Party Name</td>
                                        <td>Party Mobile</td>
                                        <td>Last Dispatch</td>
                                    </tr>
                                </thead><tbody>';
            if($results)
            {
                $sno = 1;
                foreach ($results as $key => $value) {
                    $response .= '<tr>
                                <td>'.$sno.'</td>
                                <td>'.$value['party_name'].'</td>
                                <td>'.$value['party_mobile'].'</td>
                                <td>'.$value['dispatch_date'].'</td>
                    </tr>';
                    $sno++;
                }
            }
            $response .= '</tbody></table></div>';
            echo $response;
        } 
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
}