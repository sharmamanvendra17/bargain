<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cnfrate extends CI_Controller {

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
        $this->load->model('secondarybooking_model'); 
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    }

    public function whatsappusers()
    {
        //echo "<pre>"; print_r($_POST); die;
        $vendor_id = $_POST['vendor_id'];

        $condition = array('id' => $vendor_id,'status' => 1);
        
        $vendor = $this->vendor_model->GetUserbyId($condition); 

        $users = $this->vendor_model->GetVendormobileshowwhatsapp($vendor_id); 
        $viewers = $this->vendor_model->GetUsersViewersshowactive();    
        //echo "<pre>"; print_r($users);print_r($viewers); die;
            $response= ' 
                <input type="hidden" id="vendor_id" name="notify_pi_id" value="'.$vendor_id.'"> 
                <table class="table table-striped table-bordered table-hover" id="datatable_sample">
                <thead>
                <tr>
                    <th>S.No.</th>
                    <th></th>
                    <th>Name</th>
                    <th>Mobile</th>
                </tr>
                </thead>
            ';
            $i = 1;
            if($vendor)
            {
                 
                    $response .=
                            "<tr>
                                <td>".$i."</td>
                                <td><input name='users' type='checkbox' class='user_select' value='".$vendor['mobile']."' checked ></td>
                                <td>".$vendor['name']."</td>
                                <td>".$vendor['mobile']."</td>
                            </tr>";
                    $i++; 
            }

            if($users)
            { 
                foreach ($users as $key => $value) {
                    $response .=
                            "<tr>
                                <td>".$i."</td>
                                <td><input name='users' type='checkbox' class='user_select' value='".$value['mobile']."' checked ></td>
                                <td>".$value['name']."</td>
                                <td>".$value['mobile']."</td>
                            </tr>";
                    $i++;
                }
            }
            if($viewers)
            { 
                foreach ($viewers as $key => $value) {
                    $response .=
                           "<tr>
                                <td>".$i."</td>
                                <td><input name='users' type='checkbox' class='user_select' value='".$value['mobile']."' checked ></td>
                                <td>".$value['name']."</td>
                                <td>".$value['mobile']."</td>
                            </tr>";
                    $i++;
                }
            } 
            $response .= "</table>";
            echo $response;
        
    }
    public function add_master_rate()
    {
        //echo "<pre>"; print_r($_POST); die;
        $brand = $_POST['master_brand'];
        $category = $_POST['master_category'];
        $rates = $_POST['master_rate'];
        $master_vendor = $_POST['master_vendor'];
        
        if($brand)
        {
            foreach ($brand as $key => $value) {
                $insert_data[] = array(
                    'vendor_id' => $master_vendor,
                    'brand_id' => $value,
                    'category_id' => $category[$key],
                    'rate' => $rates[$key],
                    'comission_amount' => trim($_POST['comisson']),
                    'gst_pecentage' => trim($_POST['tax']),
                    'explaination_formula' => trim($_POST['exlpaination']),
                );
            }
            echo $this->rate_model->AddcnfMasterRates($insert_data);
        }
    }
    public function GetBookingSkusRates(){  
        $vendor_id = $_POST['vendor_id'];
        $comission_amount = $_POST['comisson'];
        $tax_percentage = $_POST['gst_precentage'];
        $condition = array('booking_booking.party_id' => $vendor_id,'booking_booking.status <>' => 3); 
        $skulists = $this->secondarybooking_model->Copycnfratesmasterforrate($condition); 
        //echo "<pre>"; print_r($skulists); die;
        $res = "" ;
        if($skulists)
        {

            $i = 1;            
            foreach ($skulists as $key => $value) {  
                $insurance_percentage = $value['insurance_percentage'];
                $l_to_kg_rate = 1/.91;
                if(strtolower($value['category_name'])=='vanaspati')
                    $l_to_kg_rate = 1/.897;
                $empty_tin_charge = ($value['empty_tin_rate']*$value['packing_items_qty']); 
                $nort_east_rate_tin = 0;
                if($value['packaging_type']!=1)
                {
                    if(($value['state_id']==4 || $value['state_id']==22 || $value['state_id']== 23 || $value['state_id']==24|| $value['state_id']==25|| $value['state_id']==30|| $value['state_id']==33 || $value['state_id']==3) && $value['base_rate']==0) 
                    {
                        $nort_east_rate_tin = 31.85;
                        if(strpos($value['name'], '15')===0)
                            $nort_east_rate_tin = 0;
                        $packing_rate_ltr = ($value['rate']-($value['base_empty_tin_rates']+$nort_east_rate_tin))/15;
                    }
                    else
                    {
                        $packing_rate_ltr = ($value['rate']-$value['base_empty_tin_rates'])/15;
                    }
                    
                    $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                    
                }
                else
                { 
                    $packing_rate_ltr = (($value['rate']-$value['base_empty_tin_rates'])/15)*$l_to_kg_rate;

                    
                    $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                }
                $sku_rate = $sku_rate+$empty_tin_charge;
                $sku_rate = $sku_rate/$value['packing_items_qty'];
				
				if($value['fixed_rate_flag'] == 1)
					$sku_rate = $value['fixed_rate'];
                //=echo $value['brand_name'].' '.$value['category_name'].' '.$value['name'].$empty_tin_charge.' '.$sku_rate.'<br>';
                //$sku_rate = $sku_rate+(($sku_rate*$insurance_percentage)/100);
                $comisson = ($value['packing_items']/$value['packing_items_qty'])*$comission_amount;
                $sku_rate = $sku_rate+$comisson;
                $gst_amount = ($sku_rate*$tax_percentage)/100;
                $gst_amount = round($gst_amount,3);
                $sku_rate = $gst_amount+$sku_rate;
                $sku_rate = round($sku_rate,2);
                $res .= '<div class="row"><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Brand</label>';
                $res .= '<select class="form-control" id="" name="brand[]">';
                $res .= '<option value="'.$value['brand_id'].'">'.$value['brand_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Category</label>';
                $res .= '<select class="form-control" id="" name="category[]">';
                $res .= '<option value="'.$value['category_id'].'">'.$value['category_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Product</label>';
                $res .= '<select class="form-control product_packing" id="" name="product[]">';
                $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $res .= '<option value="'.$value['id'].'">'.$value['name'].$packing_items_qty.'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                $cnf_rate_date ='';
                if($value['cnf_rate_date'])
                {
                    $cnf_rate_date = date('d-m-Y H:i', strtotime($value['cnf_rate_date']));
                    $cnf_rate_date ='Rate Based On : '.$cnf_rate_date;
                }

                if($i==1)
                $res .= '<label for="quantity">Rate</label>';
                $res .='<input type="text" class="form-control rate" id="" name="rate[]"  value="'.$sku_rate.'"><span class="rate_date" style="color:#1a73e8;">'.$cnf_rate_date.'</span>';
                $res .='</div></div></div>';
                $i++;
            }
            $res .='<div class="col-md-12">
                                        <div class="form-group"> 
                                            <label class="btn-block"></label>
                                            <button type="submit" class="btn btn-default booking_submit" value="Save">Save Rate</button>
                                            <a class="btn btn-default whatapp" href="javascript:void(0)">Send PDf</a>
                                            <a target="_blank" class="btn btn-default" href="'.base_url().'cnfrate/DownloadRatePDF/'.$vendor_id.'">Create PDf</a>
                                        </div>                                  
                                    </div>';
            
        } //die;
        echo $res; die;
    }


    public function GetBookingSkus(){ 
        $vendor_id = $_POST['vendor_id'];
        $condition = array('booking_booking.party_id' => $vendor_id,'booking_booking.status <>' => 3); 
            
        $products = $this->secondarybooking_model->GetVendorProductsprecnfrate($condition); 

        $skulists = $this->secondarybooking_model->GetVendorSkusWithcnfrate($condition); 

        $res = "" ;
        $comission_amount = 3;
        $gst_pecentage = 5;
        if($products)
        {
            $res .= '<form  method="POST" id="add_master_rate"><input type="hidden" name="master_vendor" value="'.$vendor_id.'">';
            foreach ($products as $key => $product) {
                $comission_amount = $product['comission_amount'];
                $gst_pecentage = $product['gst_pecentage'];
                $explaination_formula = $product['explaination_formula'];
                $res .= '<div class="row"><div class="col-md-3"><div class="form-group"><select class="form-control" id="" name="master_brand[]"><option value="'.$product['brand_id'].'">'.$product['brand_name'].'</option></select></div></div><div class="col-md-3"><div class="form-group"><select class="form-control" id="" name="master_category[]"><option value="'.$product['category_id'].'">'.$product['category_name'].'</option></select></div></div><div class="col-md-3"><div class="form-group"><input type="text" class="form-control rate master_rates_input" id="" name="master_rate[]"  value="'.$product['rate'].'" placeholder="Rate 15 Ltr Tin" required></div></div></div>';
            }
            $res .= '<div class="row">
            <div class="col-md-12">
                                        <div class="form-group"> 
                                        <lable>Add Cost Per Ltr.</label>
                                            <input type="text" class="comisson form-control" name="comisson" id="comisson" value="'.$comission_amount.'" placeholder="Add Cost Per Ltr." >
                                        </div>                                  
                                    </div></div>';
            
            $res .= '<div class="row">
            <div class="col-md-12">
                                        <div class="form-group"> 
                                        <lable>Explaination (Cost per ltr) Formula </label>
                                            <textarea  class="tax form-control" name="exlpaination" id="exlpaination" placeholder="Explaination (Cost per ltr) Formula " >'.$explaination_formula.'</textarea>
                                        </div>                                  
                                    </div></div>';        

            $res .= '<div class="row">
            <div class="col-md-12">
                                        <div class="form-group"> 
                                        <lable>GST Percentage</label>
                                            <input type="text" class="tax form-control" name="tax" id="tax" value="'.$gst_pecentage.'" placeholder="GST Percentage" >
                                        </div>                                  
                                    </div></div>';                                    
            $res .= '<div class="col-md-12">
                                        <div class="form-group"> 
                                            <label class="btn-block"></label>
                                            <button type="submit" class="btn btn-default calculate_rate" value="Save">Calculate Rate</button> 
                                        </div>                                  
                                    </div>';
            $res .= '</form>';
        }
        if($skulists)
        {
            $i = 1;          
            $res .= '<form action="" class="" method="post" id="addcnfrate"><input type="hidden" name="master_vendor" value="'.$vendor_id.'">
            <input type="hidden" name="gst_rate" id="gst_rate" value=""><div class="updated_rate_sku">';
            foreach ($skulists as $key => $value) {
                
                $res .= '<div class="row"><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Brand</label>';
                $res .= '<select class="form-control" id="" name="brand[]">';
                $res .= '<option value="'.$value['brand_id'].'">'.$value['brand_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Category</label>';
                $res .= '<select class="form-control" id="" name="category[]">';
                $res .= '<option value="'.$value['category_id'].'">'.$value['category_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Product</label>';
                $res .= '<select class="form-control product_packing" id="" name="product[]">';
                $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $res .= '<option value="'.$value['id'].'">'.$value['name'].$packing_items_qty.'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                $cnf_rate_date ='';
                if($value['cnf_rate_date'])
                {
                    $cnf_rate_date = date('d-m-Y H:i', strtotime($value['cnf_rate_date']));
                    $cnf_rate_date ='Rate Updated On : '.$cnf_rate_date;
                }

                if($i==1)
                $res .= '<label for="quantity">Rate</label>';
                $res .='<input type="text" class="form-control rate" id="" name="rate[]"  value="'.$value['rate'].'" readonly><span class="rate_date" style="color:#1a73e8;">'.$cnf_rate_date.'</span>';
                $res .='</div></div></div>';
                $i++;
            }
            $res .='<div class="col-md-12">
                                        <div class="form-group"> 
                                            <label class="btn-block"></label>
                                            <button type="submit" class="btn btn-default booking_submit" value="Save">Save Rate</button>
                                            <a class="btn btn-default whatapp" href="javascript:void(0)">Send PDf</a>
                                            <a target="_blank" class="btn btn-default" href="'.base_url().'cnfrate/DownloadRatePDF/'.$vendor_id.'">Create PDf</a>
                                        </div>                                  
                                    </div></div>';
            $res .='</form>';
            
        } 
        echo $res; die;
    }
    
    public function GetBookingSkus1(){ 
        $vendor_id = $_POST['vendor_id'];
        $condition = array('booking_booking.party_id' => $vendor_id,'booking_booking.status <>' => 3); 
            
        //$skulists = $this->secondarybooking_model->GetVendorProducts($condition); 

        $skulists = $this->secondarybooking_model->GetVendorSkusWithcnfrate($condition); 

        $res = "" ;
        if($skulists)
        {
            $i = 1;          
            foreach ($skulists as $key => $value) {
                
                $res .= '<div class="row"><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Brand</label>';
                $res .= '<select class="form-control" id="" name="brand[]">';
                $res .= '<option value="'.$value['brand_id'].'">'.$value['brand_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Category</label>';
                $res .= '<select class="form-control" id="" name="category[]">';
                $res .= '<option value="'.$value['category_id'].'">'.$value['category_name'].'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                if($i==1)
                    $res .= '<label for="name">Product</label>';
                $res .= '<select class="form-control product_packing" id="" name="product[]">';
                $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $res .= '<option value="'.$value['id'].'">'.$value['name'].$packing_items_qty.'</option>';
                $res .= '</select></div></div><div class="col-md-3"><div class="form-group">';
                $cnf_rate_date ='';
                if($value['cnf_rate_date'])
                {
                    $cnf_rate_date = date('d-m-Y H:i', strtotime($value['cnf_rate_date']));
                    $cnf_rate_date ='Rate Updated On : '.$cnf_rate_date;
                }

                if($i==1)
                $res .= '<label for="quantity">Rate</label>';
                $res .='<input type="text" class="form-control rate" id="" name="rate[]"  value="'.$value['rate'].'"><span class="rate_date" style="color:#1a73e8;">'.$cnf_rate_date.'</span>';
                $res .='</div></div></div>';
                $i++;
            }
            $res .='<div class="col-md-12">
                                        <div class="form-group"> 
                                            <label class="btn-block"></label>
                                            <button type="submit" class="btn btn-default booking_submit" value="Save">Save Rate</button>
                                            <a class="btn btn-default whatapp" href="javascript:void(0)">Send PDf</a>
                                            <a target="_blank" class="btn btn-default" href="'.base_url().'cnfrate/DownloadRatePDF/'.$vendor_id.'">Create PDf</a>
                                        </div>                                  
                                    </div>';
            
        } 
        echo $res; die;
    }


    public function DownloadRatePDF($vendor_id){ 
        //$vendor_id = $_POST['vendor_id'];
        $condition = array('booking_booking.party_id' => $vendor_id,'booking_booking.status <>' => 3); 
        $skulists = $this->secondarybooking_model->GetVendorSkusWithcnfrate($condition); 
        //echo "<pre>"; print_r($skulists); die;

        $users = $this->vendor_model->GetUsersByVendor($vendor_id); 
        $res = "" ;
        if($skulists)
        {
            $res = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">

                    <tr>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. No.</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Brand</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Category</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Product</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Rate</td>
                    </tr>';
            $i = 1;            
            foreach ($skulists as $key => $value) {
                $partyname = $value['party_name'];
                $party_city_name = $value['party_city_name']; 
                $rate_gst_percentage = $value['gst_percentage']; 
                
                $res .='
                        <tr>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;    text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$i.'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['brand_name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['category_name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['rate'].'</td>
                        </tr>
                    ';
                    $i++;
            }
             
            $res .='</table>';
            $gst_show = "";
            if($rate_gst_percentage)
                $gst_show = "(Including GST ".$rate_gst_percentage."%)";
            $header = "<h4 style='text-align:center'>".$partyname."<br>Ex Depot ".$gst_show." - ".$party_city_name." Prices  As On ".date('d-m-Y')."</h4>";
            $footerHtml = ' <span style="text-align:right;">Note : The above rates are subject to reconfirmation</span> <br> Page {PAGENO} of {nbpg}';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','25','35','10','10'); 
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footerHtml);
            $mpdf->WriteHTML($res);
            $partyname = str_replace(' ', '-', $partyname);
            $file_name  =  $partyname.'-Rate-Sheet-'.date('d-m-y-h-i-s').'.pdf';
            $pdf_file_name  = base_url().'rates-cnf/'.$file_name;
            //$file_name  =  FCPATH.'rates-cnf/'.$file_name;
            $mpdf->Output($file_name,'I'); 

             
        }
        //echo $res; die;
    }
    public function RatePDF(){ 
        $vendor_id = $_POST['vendor_id'];
        $condition = array('booking_booking.party_id' => $vendor_id,'booking_booking.status <>' => 3); 
        $skulists = $this->secondarybooking_model->GetVendorSkusWithcnfrate($condition); 
        //echo "<pre>"; print_r($skulists); die;

        //$users = $this->vendor_model->GetUsersByVendor($vendor_id); 
        $users = $this->vendor_model->GetVendormobile($vendor_id); 
        $viewers = $this->vendor_model->GetUsersViewers();  
        //echo "<pre>"; print_r($users); die;
        $res = "" ;
        if($skulists)
        {
            $res = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">

                    <tr>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. No.</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Brand</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Category</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Product</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Rate</td>
                    </tr>';
            $i = 1;            
            foreach ($skulists as $key => $value) {
                $partyname = $value['party_name'];
                $partymobile = $value['party_mobile'];
                $party_city_name = $value['party_city_name']; 
                $rate_gst_percentage = $value['gst_percentage']; 
                $res .='
                        <tr>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;    text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$i.'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['brand_name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['category_name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['rate'].'</td>
                        </tr>
                    ';
                    $i++;
            }
            $res .='</table>';
            
            $gst_show = "";
            if($rate_gst_percentage)
                $gst_show = "(Including GST ".$rate_gst_percentage."%)";
            $header = "<h4 style='text-align:center'>".$partyname."<br>Ex Depot ".$gst_show." - ".$party_city_name." Prices  As On ".date('d-m-Y')."</h4>";
            $footerHtml = ' <span style="text-align:right;">Note : The above rates are subject to reconfirmation</span> <br> Page {PAGENO} of {nbpg}';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','25','35','10','10'); 
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footerHtml);
            $mpdf->WriteHTML($res);
            $partyname = str_replace(' ', '-', $partyname);
            $partyname = str_replace('&', '-', $partyname);
            $file_name  =  $partyname.'-Rate-Sheet-'.date('d-m-y-h-i-s').'.pdf';
            $pdf_file_name  = base_url().'rates-cnf/'.$file_name;
            $file_name  =  FCPATH.'rates-cnf/'.$file_name;
            $mpdf->Output($file_name,'F'); 

            $message_params = urlencode(date('d-M-Y',time()));
            $mobile_numbers = array();

            if($users)
            {
                foreach ($users as $key => $value) {
                   // 
                    //$mobile_numbers[] = $value['mobile'];
                    $mobile_numbers[] = $value;
                }
            }

            if($viewers)
            {
                foreach ($viewers as $key => $value) {
                    $mobile_numbers[] = $value['mobile'];
                }
            }

            $mobile_numbers[] = $partymobile; 
            //$mobile_numbers[] = '9828066666';
            //$mobile_numbers[] = '9828077777';
            $mobile_number = implode(',', $mobile_numbers); 
            $curl_watsappapi = curl_init(); 
            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_number.'&TID=9129259&P='.$message_params.'&PATH='.$pdf_file_name,
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
        //echo $res; die;
    }

    public function RatePDFwhatsapp(){ 
        $vendor_id = $_POST['vendor_id'];
        $condition = array('booking_booking.party_id' => $vendor_id,'booking_booking.status <>' => 3); 
        $skulists = $this->secondarybooking_model->GetVendorSkusWithcnfrate($condition); 
        //echo "<pre>"; print_r($_POST); die;

        //$users = $this->vendor_model->GetUsersByVendor($vendor_id); 
        $users = $this->vendor_model->GetVendormobile($vendor_id); 
        $viewers = $this->vendor_model->GetUsersViewers();  
        //echo "<pre>"; print_r($users); die;
        $res = "" ;
        if($skulists)
        {
            $res = '<table width="800" border="0"  cellpadding="0" cellspacing="0" style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center; margin:0px 20px; font-size:12px;  padding:0px;">

                    <tr>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Sr. No.</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Brand</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Category</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Product</td>
                        <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; font-weight:bold;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">Rate</td>
                    </tr>';
            $i = 1;            
            foreach ($skulists as $key => $value) {
                $partyname = $value['party_name'];
                $partymobile = $value['party_mobile'];
                $party_city_name = $value['party_city_name']; 
                $rate_gst_percentage = $value['gst_percentage']; 
                $res .='
                        <tr>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;    text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$i.'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['brand_name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['category_name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px; text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['name'].'</td>
                            <td  style="margin:0px; padding:0 5px 0 5px; line-height:20px;   text-align:center; border-collapse:collapse; border-bottom:1px solid #000000; border-right:1px solid #000000; border-top:1px solid #000000; border-right:1px solid #000000; border-left:1px solid #000000;" valign="top">'.$value['rate'].'</td>
                        </tr>
                    ';
                    $i++;
            }
            $res .='</table>';
            
            $gst_show = "";
            if($rate_gst_percentage)
                $gst_show = "(Including GST ".$rate_gst_percentage."%)";
            $header = "<h4 style='text-align:center'>".$partyname."<br>Ex Depot ".$gst_show." - ".$party_city_name." Prices  As On ".date('d-m-Y')."</h4>";
            $footerHtml = ' <span style="text-align:right;">Note : The above rates are subject to reconfirmation</span> <br> Page {PAGENO} of {nbpg}';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','25','35','10','10'); 
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footerHtml);
            $mpdf->WriteHTML($res);
            $partyname = str_replace(' ', '-', $partyname);
            $partyname = str_replace('&', '-', $partyname);
            $file_name  =  $partyname.'-Rate-Sheet-'.date('d-m-y-h-i-s').'.pdf';
            $pdf_file_name  = base_url().'rates-cnf/'.$file_name;
            $file_name  =  FCPATH.'rates-cnf/'.$file_name;
            $mpdf->Output($file_name,'F'); 

            $message_params = urlencode(date('d-M-Y',time()));
            $mobile_numbers = array();

            $mobile_numbers = $_POST['numbers']; 
              
            //$mobile_numbers[] = $partymobile; 
            //$mobile_numbers[] = '9828066666';
            //$mobile_numbers[] = '9828077777';
            //array_push($mobile_numbers,$partymobile);
            //echo "<pre>"; print_r($mobile_numbers); die;
            $mobile_number = implode(',', $mobile_numbers);  
            $curl_watsappapi = curl_init(); 
            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_number.'&TID=9129259&P='.$message_params.'&PATH='.$pdf_file_name,
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
        //echo $res; die;
    }

    public function index(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "C&F Rate Master";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role; 
        $condition = array('vendors.cnf' => 1); 
        $data['vendors'] = $this->vendor_model->GetCnfVendor($condition);  
    	$this->load->view('cnf_rate_master',$data);
	}
    public function add_rate(){ 
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;  
        $result = 0;
        if(isset($_POST) && !empty($_POST))
        {
            $vendor_id = $_POST['master_vendor'];
            $gst_rate = ($_POST['gst_rate']) ? $_POST['gst_rate'] : 5;
            $rates = $_POST['rate'];
            $products = $_POST['product'];
            $insertdata = array();
            foreach ($products as $key => $value) {
                if($rates[$key])
                {
                    $insertdata[] = array(
                        'vendor_id' =>$vendor_id,
                        'product_id' =>$products[$key],
                        'rate' =>$rates[$key],
                        'rate' =>$rates[$key],
                        'created_by' =>$userid,
                        'gst_percentage' =>$gst_rate,
                    );
                }
            }
            if($insertdata)
            {
                $result = $this->rate_model->AddcnfRates($insertdata);
            }
            echo $result; die;
        }
    }
}
