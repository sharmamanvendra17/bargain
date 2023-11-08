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
                        <th class="text-center">Brand</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">Rate</th>
                    </tr>
                    <tr>
                        <td>'.$brand_name.'</td>
                        <td>'.$category_name.'</td>
                        <td>'.$rate.'</td>
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
            $remark_data = array('booking_id'=>$_POST['booking_id'],'remark' => trim($remark),'remark_type' => 'apprval'); 
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

        $data['users'] = $this->vendor_model->GetUsers();
        $data['makers'] = $this->admin_model->GetAllMakers();

        $data['categories'] = $this->category_model->GetCategories();

        $data['distinct_categories'] = $this->category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['users']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;

        $this->load->view('booking',$data);

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
        //$data['booking_info'] = $this->booking_model->GetBookingInfoById(base64_decode($bargain_id));
        $updatedata = array('status'=>2);
        echo  $this->booking_model->UpdateBooking($updatedata,$condition);
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

            if($products)
            {
                $added = 1;
                if($_POST['update_data'])
                {
                    $weight_update =  $this->input->post('update_weight'); 
                    $condition_booking = array('id' => $id);
                    $update_data_booking = array('brand_id' => $brand,'category_id' => $category,'total_weight' => $weight_update);
                    $this->booking_model->UpdateBookingBooking($update_data_booking,$condition_booking); 
                    $condition_all = array('booking_id' => $id);
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
                        $flag = $this->booking_model->AddSKU($condition,$skudata); 
                        if(!$flag)
                            $added = 0;
                        
                        if($_POST['flag']){
                            //$this->sendmail_plant_lock($booking_id);
                        }
                    }
                    else
                    {
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

            if($booking_date!='')
            { 
                $today_cur_date = date("dm", strtotime($booking_date));
                $book_chek_date = $booking_date." 00:00:00.000000";

            }  
            $new_booking_id  = $this->booking_model->getlast_booking_id($book_chek_date);             
           
            $insertdata = array(
                'booking_id' =>$new_booking_id+1,
                'party_id' =>$party,
                'brand_id' =>$brand,
                'category_id' =>$category, 
                'quantity' =>$quantity,
                //'weight' =>$quantity*15,
                'rate' =>$rate,
                'broker_id' =>$broker,
                'insurance' =>$insurance,
                'is_for' =>$is_for,
                'admin_id' =>$admin_id,
                'total_weight' => $weight,

                'remark' =>$remark,
                'sales_executive_id' =>$sales_executive,
                'shipment_date' =>date('Y-m-d', strtotime($shipment_date)),
            );
            //echo "<pre>"; print_r($insertdata); die; 
            if($booking_date!='')
                 $insertdata['created_at'] = $booking_date.':00'; 
            $insertdata['status'] = 0;
            if ($role==2) { //checker
               $insertdata['status'] = 1;
            }
            elseif ($role==3|| $role==4) { //approver
               $insertdata['status'] = 2;
            }
            echo $result = $this->booking_model->AddBooking($insertdata);
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

                'remark' =>$remark,
                'sales_executive_id' =>$sales_executive,
                'shipment_date' =>date('Y-m-d', strtotime($shipment_date)),
            );
            $condition = array('id' => $_POST['booking_number']);
            echo $result = $this->booking_model->UpdateBooking($insertdata,$condition);
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
 
        $data['products'] = $this->category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


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
        if(!empty($_POST) || isset($_SESSION['search__report_data']))
        //if(!empty($_POST))
        {
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__report_data'] = $_POST;
            else
                $_POST = $_SESSION['search__report_data']; 
            $party_id = $_POST['party'];
            $brand_id = $_POST['brand'];
            $category_id = $_POST['category']; 
            $booking_date_from = date('Y-m-d',strtotime($_POST['booking_date_from']));
            $booking_date_to = date('Y-m-d',strtotime($_POST['booking_date_to'])); 
            $booking_status = (isset($_POST['status'])) ? $_POST['status'] : '';
            $role = $this->session->userdata('admin')['role'];

            $employee = $_POST['employee']; 

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
                $total_rows =  $this->booking_model->CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$employee);
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
                $data['bookings'] = $this->booking_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee);
                //echo "<pre>"; print_r($data); die;
            }
            else
            {
                $data['search_summary'] = 1;
                //echo "<pre>"; print_r($_POST); die;
                $group_by  = array('brand_id','category_id');
                //echo "<pre>"; print_r($group_by); die;
                $data['bookings_brand_product'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee);
                $group_by  = array('place','brand_id','category_id');
                $data['bookings_brand_product_place'] = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee);

                //echo "<pre>"; print_r($data['bookings_brand_product_place']); die;

                $group_by  = array('status');
                $data['sum_report'] = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee);

                //echo "<pre>"; print_r($data['sum_report']); die;

                $data['tot_sum_report'] = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee); 

                $data['locked'] = $this->booking_model->GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee); 

                //echo "<pre>"; print_r($data['locked'] ); die;
            }
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

        $data['employees'] = $this->admin_model->GetAllMakers();
        //echo "<pre>"; print_r($data['employees']); die;
        $this->load->view('booking_report',$data);

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
        $group_by  = array('status');
        $sum_report = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee);

        $tot_sum_report = $this->booking_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee);

        $locked = $this->booking_model->GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee);  

        //echo "<pre>"; print_r($data['locked']); die; 
        if($_GET['type']=='place')
        {
            $group_by  = array('place','brand_id','category_id');
            $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee);

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
                                        $sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['weight'].'</td>
                                    </tr>'; 
                                    } } 
                                $html_print .= '</table>
            <table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                        <thead>
                            <tr><th colspan="7" style="text-align:center;border-bottom:1px solid #000;">Order summary based on city from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">S.No</th>    
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">City</th>  
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Brand</th>   
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Product</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Quantity(Tins/Cartons)</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Weight (MT)</th>
                                <th style="text-align:left; border-bottom:1px solid #000;">Average Rate</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['city_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['weight'].'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['avg_rate'],2).'</td> 
                                </tr>';
                            $sn++; }
                            $html_print .= '<tr style="color:red;"><td colspan="4"></td><td>Total</td><td >'.$weight_total_summary.'</td><td></td></tr>';
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
        else
        {
            $group_by  = array('brand_id','category_id');        
            $bookings_brand_product_place = $this->booking_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee);
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
                                        $sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['weight'].'</td>
                                    </tr>'; 
                                    } } 
                                $html_print .= '</table><table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                        <thead>
                            <tr><th colspan="6" style="text-align:center;border-bottom:1px solid #000;">Order summary based on brand and product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">S.No</th>   
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Brand</th>   
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Product</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Quantity(Tins/Cartons)</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Weight (MT)</th>
                                <th style="text-align:left; border-bottom:1px solid #000;">Average Rate</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                     
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['weight'].'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['avg_rate'],2).'</td> 
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

    }

    public function updatestatus(){  
        $time = date('Y-m-d H:i:s');
        $update_data = array('status' => $_POST['status']);
        if($_POST['status']==3 || $_POST['status']==2)
        {
            $update_data['approve_reject_time'] = $time;
            $update_data['reject_remark'] = trim($_POST['remark']); 
        }
        else
        {
            $update_data['check_time'] = $time;
        }


        $condition  = array('id' => base64_decode($_POST['booking_id']));
        echo $result= $this->booking_model->UpdateBooking($update_data,$condition);
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
        echo $weight['weight'].'__'.$bargains;
        //echo "<pre>"; print_r($weight); die;
    }
}