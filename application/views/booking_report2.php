<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
<style type="text/css">
    .panel-body .table th {
    text-align: center;
    vertical-align: top;
}
.table_report { border: none; }
.table_report td { font-weight: normal; }
</style>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1> 
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <form action="<?php echo base_url('booking/report/'); ?>" class="" method="post" id="addbooking">
            <div class="panel-heading">
                <div class="row">

                        <div class="col-md-8">   
                            <span class="title elipsis">
                                <strong><?php echo $title; ?></strong> <!-- panel title -->
                            </span> 
                        </div>
                        <div class="col-md-4">
                            <span class="title elipsis header_add" style="display:none;">
                                <div class="form-group cal" style="margin-bottom:0px!important"> 
                                    <div class="controls input-append date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                                        <input size="16" type="text" value="" class="form-control calc_b" readonly>
                                        <span class="add-on"><i class="icon-remove"></i></span>
                                        <span class="add-on"><i class="icon-th"></i></span>
                                    </div>
                                    <input type="hidden" name="booking_date" id="dtp_input1" value="" /><br/>
                                </div> &nbsp;&nbsp;
                                <a href="javascript:void(0)" class="reset btn">Reset</a>
                            </span>               

                        </div>

                </div>
             
                
            </div>
            <div class="panel-body">
                <?php if($this->session->flashdata('err_msg')) { ?>
                    <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                        <?php echo $this->session->flashdata('err_msg'); ?>
                    </div>
                <?php } ?>
                <?php if($this->session->flashdata('suc_msg')) { ?>
                    <div class="alert alert-success noborder text-center weight-400 nomargin noradius">
                        <?php echo $this->session->flashdata('suc_msg'); ?>
                    </div>
                <?php } //echo "<pre>"; print_r($_POST); ?> 
                <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="booking_number" id="booking_number" value="0">                              
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">               
                                        <label for="name">Party Name</label> 
                                        <select class="form-control" id="party" name="party" >
                                            <option value="">Select Party</option>
                                            <?php if($users) { 
                                                foreach ($users as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('party'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Brand </label> 
                                        <select class="form-control" id="brand" name="brand" >
                                            <option value="">Select Brand</option>
                                            <?php if($brands) { 
                                                foreach ($brands as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['brand'])) { if($_POST['brand']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('brand'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product</label> 
                                        <select class="form-control" id="category" name="category" >
                                            <option value="">Select Product</option>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">Booking Date (From) </label>
                                        <input class="form-control" type="text" id="booking_date_from" name="booking_date_from"  value="<?php if(isset($_POST['booking_date_from'])) { echo $_POST['booking_date_from']; } else { echo date('d-m-Y'); } ?>" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">Booking Date (To)</label>
                                        <input class="form-control" type="hidden" id="booking_date_to" name="booking_date_to" value="<?php if(isset($_POST['booking_date_to'])) { echo $_POST['booking_date_to']; } else { echo date('d-m-Y'); } ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <?php if($logged_role==4 || $logged_role==2|| $logged_role==5) { ?>
                                        <label for="rate">Employee (Makers)</label>
                                        <select class="form-control" id="employee" name="employee" >
                                            <option value="">Select Employee</option>
                                            <?php if($employees) { 
                                                foreach ($employees as $key => $employee) { ?>
                                            <option value="<?php echo $employee['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$employee['id']) { echo "selected"; } }; ?>><?php echo $employee['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <?php } else { ?>
                                            <select class="form-control" id="employee" name="employee" style="display:none;">
                                                <option value="">Select Employee</option>
                                            </select>
                                        <?php }  ?>
                                    </div>
                                </div> 
                            </div>  
                            <div class="row" >
                                <div class="col-md-8">
                                    <div class="form-group"> 
                                        <label for="rate">Status</label>
                                        <input class="" type="radio" id="" name="status"  value=""   <?php echo (!isset($_POST['status']) || $_POST['status']=='') ? 'checked' : ''; ?> />All
                                        <input class="" type="radio" id="" name="status"  value="2" <?php echo (isset($_POST['status']) && $_POST['status']=='2') ? 'checked' : ''; ?> />Approved
                                        <input class="" type="radio" id="" name="status"  value="0" <?php echo (isset($_POST['status']) && $_POST['status']=='0') ? 'checked' : ''; ?> />Pending
                                        <input class="" type="radio" id="" name="status"  value="3" <?php echo (isset($_POST['status']) && $_POST['status']=='3') ? 'checked' : ''; ?> />Rejected

                                        <input class="" type="radio" id="" name="status"  value="lock" <?php echo (isset($_POST['status']) && $_POST['status']=='lock') ? 'checked' : ''; ?> />Locked

                                        <input class="" type="radio" id="" name="status"  value="partial_lock" <?php echo (isset($_POST['status']) && $_POST['status']=='partial_lock') ? 'checked' : ''; ?> />Partial Locked
                                        
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">Unit</label>
                                        <input class="" type="radio" id="" name="production_unit"  value=""   <?php echo (!isset($_POST['production_unit']) || $_POST['production_unit']=='') ? 'checked' : ''; ?> />All
                                        <input class="" type="radio" id="" name="production_unit"  value="alwar" <?php echo (isset($_POST['production_unit']) && $_POST['production_unit']=='alwar') ? 'checked' : ''; ?> />Alwar
                                        <input class="" type="radio" id="" name="production_unit"  value="jaipur" <?php echo (isset($_POST['production_unit']) && $_POST['production_unit']=='jaipur') ? 'checked' : ''; ?> />Jaipur
                                    </div>
                                </div>
                            </div>
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search Bargain</button> 
                                        <input type="submit" class="btn btn-default" name="summary_submit" value="Search Summary"> 
                                    </div>                                  
                                </div> 
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-4">
                                    <?php if($bookings) { 
                                        $params_report = "?party=$_POST[party]&brand=$_POST[brand]&product=$_POST[category]&from=$_POST[booking_date_from]&to=$_POST[booking_date_to]&type=brand&status=$_POST[status]&employee=$_POST[employee]&production_unit=$_POST[production_unit]"; ?>
                                        <a style="display: inline-block;float: right;" target="_blank" class="rept_btn btn btn-default" href="<?php echo base_url('booking/report_print').$params_report; ?>">
                                            
                                            <img src="<?php echo base_url('assets/img/pdf.png'); ?>" height="20" width="20">

                                        </a>
                                        <a style="display: inline-block;float: right;" target="_blank" class="rept_btn btn btn-default" href="<?php echo base_url('booking/report_print_excel').$params_report; ?>"><img src="<?php echo base_url('assets/img/xls.png'); ?>" height="20" width="20"> </a>
                                    <?php } ?>
                                </div>                               
                            </div>
                        
                    </div>
                </div> 
                
               
                    <?php $total_weight = 0; ?>
                    <?php if($search_summary == 0) { ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Bargain No</th>
                                <th>Party Name</th> 
                                <th>Brand</th>   
                                <th>Product</th>
                                 
                                <th>Tins (Qty)</th>  
                                <th>Rate (15Ltr Tin)</th>  
                                <th>Unit</th>  
                                <th>Date</th>  
                                <?php //if($logged_role != 1) { ?>
                                <th>Status</th> 
                                <?php //} ?>  
                                <th>Action</th> 
                            </tr>
                        </thead>
                        <tbody class="booking_records">
                            <?php $total_weight = 0; if($bookings) { 
                                $i=1;
                                $count = 1;
                                $cur_page =1;
                                if(isset($limit))
                                    $con_li = $limit;
                                if($this->uri->segment(3)!='')
                                    $cur_page = $this->uri->segment(3);
                                $count = ($cur_page-1)*$con_li+1;
                                foreach ($bookings as $key => $value) { ?>
                                    <tr class="odd gradeX">
                                        <td><?php echo $count; ?></td>
                                        <td><span title="<?php echo $value['admin_name']; ?>">DATA/<?php echo $value['booking_id']; ?></span></td>
                                        <td>
                                            <?php if($value['status']==0 && (($logged_role!=5 && $logged_role!=7 )|| $logged_in_id==$value['team_lead_id'])) { ?>
                                                <a title="Edit Brgain" href="<?php echo base_url('booking/edit').'/'.base64_encode($value['id']);?>" class="">
                                            <?php  } ?>
                                            <?php echo $value['party_name'] .' - '.$value['city_name']; ?>
                                            <?php if($value['status']==0) { ?>
                                                </a>
                                            <?php }  ?>
                                        </td> 
                                        <td><?php echo $value['brand_name']; ?></td> 
                                        <td><?php echo $value['category_name']; ?></td> 
                                        <td><?php echo $value['quantity']; 
                                            if(strtolower($value['production_unit'])=='jaipur' && $value['total_weight_net']>0)
                                                echo ' ('.round($value['total_weight_net'],2).' MT)';
                                            else
                                                echo ' ('.round($value['weight'],2).' MT)'; ?></td>  
                                        <td><?php echo $value['rate']; ?><br>

                                            <?php 
                                            $loose_rate_per_kg = round(($value['loose_oil_rate']/13.65),3);
                                            if(strtolower($value['category_name'])=='vanaspati')
                                                $loose_rate_per_kg = round(($value['loose_oil_rate']/13.455),3);
                                            


                                            if($value['state_id']==4 || $value['state_id']==22 || $value['state_id']==23 || $value['state_id']==24 || $value['state_id']==25 || $value['state_id']==30 || $value['state_id']==33)
                                            {
                                                $loose_rate_per_kg = $loose_rate_per_kg-5;
                                            }
                                            echo '<strong>('.$loose_rate_per_kg.'/kg)</strong>'; ?>
                                        </td> 
                                        <td><?php echo $value['production_unit']; ?></td>
                                        <td title="<?php echo date("h:i:s", strtotime($value['created_at'])); ?>"><?php echo date("d-m-y", strtotime($value['created_at'])); ?></td>
                                        
                                        <?php //if($logged_role != 1) { ?>
                                        <td>
                                            <?php 
                                        $app_status = 0;
                                        $lock_img = '/un-lock.png';
										$lock_status = 0;
                                        
										if($value['is_lock']==1){
                                            $lock_img = '/lock.png';
											$lock_status = 1;
										}
                                        if($value['is_lock']==2){
                                            $lock_img = '/lock-yellow.png';
											$lock_status = 2;
										}

                                        if($value['status']==3)  
                                        { ?>
                                            <span title="<?php echo $value['reject_remark']; ?>" class="btn btn-danger <?php echo ($logged_role == 4) ? 'update_status_reject' : 'style="cursor:none"'; ?>"  data-status="0" rel="<?php echo base64_encode($value['id']); ?>" > <img style="height: 17px;width: 17px;" src="<?php echo base_url('assets/img').$lock_img; ?>" > Rejected</span>
                                        <?php } elseif($value['status']==2) { 
                                            $app_status = 1; ?>
                                            <span class="btn btn-default <?php echo ($logged_role == 4 || (($logged_in_id==$value['team_lead_id']) || $logged_role == 5)) ? 'update_status_reject' : 'style="cursor:none"'; ?>" data-status="3" lock-status="<?php echo $lock_status; ?>" rel="<?php echo base64_encode($value['id']); ?>" ><img style="height: 17px;width: 17px;" src="<?php echo base_url('assets/img').$lock_img; ?>"> 
                                                <?php //echo ($value['pi_ids']) ? "PI" : "Approved"; 
                                                    if($value['pi_ids'])
                                                    {
                                                        $remaining_pi_skus = $value['not_pi_sku'];
                                                        $withouttruck_pi = $value['withouttruck'];
                                                        $withtruck_pi = $value['withtruck'];
                                                        if($lock_status && $remaining_pi_skus==0)
                                                        {
                                                            if($withouttruck_pi==0 || $withtruck_pi==0)
                                                            {
                                                                if($withouttruck_pi ==0)
                                                                {
                                                                    echo '<img style="height: 35px" src="'.base_url('assets/img/full-truck.png').'" title="Bargain Fully Dispateched and weight pending">';
                                                                }
                                                                else
                                                                {
                                                                    echo "PI Completed";
                                                                }
                                                            }
                                                            else
                                                            {
                                                                echo '<img style="height: 35px;" src="'.base_url('assets/img/half-truck.png').'" title="PI pending for dispatch">';
                                                            }
                                                        }
                                                        else
                                                        {
                                                            if($withouttruck_pi==0 || $withtruck_pi==0)
                                                            {
                                                                if($withouttruck_pi ==0)
                                                                {
                                                                    echo '<img style="height: 35px;" src="'.base_url('assets/img/half-truck.png').'" title="SKU Pending for PI and No PI pending for dispatch">';
                                                                }
                                                                else
                                                                {
                                                                    echo "PI";
                                                                }
                                                            }
                                                            else
                                                            {
                                                                echo '<img style="height: 35px;" src="'.base_url('assets/img/half-truck.png').'" title="SKU Pending for PI and PI pending for dispatch">';
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        echo "Approved"; 
                                                    }

                                                 ?>

                                            </span>
                                        <?php } else { if($value['is_lock']==1)  { $app_status = 1; } ?>
                                        <span class="btn btn-danger <?php echo ($logged_role == 4) ? 'update_status_reject' : 'style="cursor:none"'; ?>"  data-status="3" lock-status="<?php echo $lock_status; ?>" rel="<?php echo base64_encode($value['id']); ?>" > <img style="height: 17px;width: 17px;" src="<?php echo base_url('assets/img').$lock_img; ?>"> Approval Pending</span>
                                        <?php } ?>



                                        <?php /*if($value['status']==3)  
                                        { ?>
                                            <span class="btn btn-danger "  data-status="3" rel="<?php echo base64_encode($value['id']); ?>" style="cursor:none">Rejected</span>
                                        <?php }
                                        elseif($value['status']==2 && ($value['is_lock']==0 || $value['is_lock']==2))
                                        { ?>
                                         <span class="btn btn-default <?php echo ($logged_role == 4) ? 'update_status_reject' : 'style="cursor:none"'; ?>" data-status="3" rel="<?php echo base64_encode($value['id']); ?>" >Approved</span>
                                        <?php $app_status = 1;
                                        }
                                        else 
                                        {
                                            if($value['is_lock']==1)
                                            {  $app_status = 1;  ?> 
                                                <span class="btn btn-default <?php echo ($logged_role == 4) ? 'update_status_reject' : 'style="cursor:none"'; ?>"  data-status="3" rel="<?php echo base64_encode($value['id']); ?>">Locked</span>
                                            <?php } else  
                                            {
                                                if($value['status']==3)
                                                { ?>
                                                    <span class="btn btn-danger "  data-status="3" rel="<?php echo base64_encode($value['id']); ?>" style="cursor:none">Rejected</span>
                                                <?php }
                                                else
                                                { ?>
                                                    <span class="btn btn-danger <?php echo ($logged_role == 4) ? 'update_status_reject' : 'style="cursor:none"'; ?>"  data-status="3" rel="<?php echo base64_encode($value['id']); ?>" >Approval Pending</span>
                                                <?php }
                                            }
                                        } */
                                        /*
                                        if($value['is_lock']) {
                                            if($value['status']==0 && $logged_role == 1)
                                                echo '<span class="btn btn-danger"  rel="'.base64_encode($value['id']).'" style="cursor:none">Check Pending</span>'; 
                                            elseif($value['status']==0 && $logged_role == 2 )
                                                echo '<span class="btn btn-danger update_status" rel="'.base64_encode($value['id']).'" data-status="1" ><a href="javascript:void(0)" >Check Pending</a></span>'; 
                                            elseif($value['status']==1  && $logged_role == 2)
                                                echo '<span class="btn btn-warning"  rel="'.base64_encode($value['id']).'" style="cursor:none" >Checked</span>';
                                            elseif($value['status']==1  && $logged_role == 3)
                                                echo '<div class="approval_status_section '.$value['id'].'"><span  rel="'.base64_encode($value['id']).'" class="btn btn-success update_status_reject" rel="'.base64_encode($value['id']).'" data-status="2"><a href="javascript:void(0)" >Approve</a></span> <span class="btn btn-danger update_status_reject" rel="'.base64_encode($value['id']).'" data-status="3"><a href="javascript:void(0)" >Reject</a></span></div>';
                                            elseif($value['status']==1  && $logged_role == 1)//maker and checked
                                                echo '<span class="btn btn-warning" style="cursor:auto"  rel="'.base64_encode($value['id']).'" >Checked</span>';
                                            elseif($value['status']==2)
                                                echo '<span class="btn btn-success show_details1"  rel="'.base64_encode($value['id']).'" >Approved</span>'; 
                                            elseif($value['status']==3)
                                                echo '<span class="btn btn-danger show_details1"   rel="'.base64_encode($value['id']).'" >Rejected</span>'; 
                                        } else  { echo  "SKU Pending"; } */
                                            ?>
                                        </td>
                                        <?php //} ?>  
                                        <td> 
                                             <?php 
                                             $view_approval_status = 0;
                                             if($logged_in_id==$value['team_lead_id'])
                                                    $view_approval_status = 1;
                                            if($value['is_lock']==1 && $value['is_mail'] ) { ?>
                                                <!--<a href="<?php echo base_url('booking/downloadreport').'/'.base64_encode($value['booking_id']); ?>" rel="<?php echo $value['booking_id']; ?>" class="btn btn-default btn_report detail">Report</a>-->
                                                <a href="javascript:void(0)" rel="<?php echo $value['booking_id']; ?>" data-lock="<?php echo $value['is_lock']; ?>" class="btn btn-default detail btn_report1" data-production_unit="<?php echo $value['production_unit']; ?>" data-status="<?php echo $app_status; ?>"  data-view_approval_status="<?php echo $view_approval_status; ?>" >Report</a>
                                            <?php } else { if($logged_role==1) {                        
                                            
                                            if($value['is_lock']!=1){
                                            ?>
                                            
                                            <a href="<?php echo base_url('booking/sku').'/'.base64_encode($value['booking_id']); ?>" rel="<?php echo $value['booking_id']; ?>"  class="btn btn-default detail">Add SKU</a>
                                            <?php 
                                            
                                            }else { // if mail not sents?>
                                                <a href="javascript:void(0)" data-lock="<?php echo $value['is_close']; ?>" rel="<?php echo $value['booking_id']; ?>" class="btn btn-default detail btn_report1" data-production_unit="<?php echo $value['production_unit']; ?>" data-status="<?php echo $app_status; ?>" data-party="<?php echo $value['party_id']; ?>">Send Mail</a>
                                            <?php }
                                            
                                            
                                            
                                            }  else {  
                                                ?>
                                                <a href="javascript:void(0)" data-lock="<?php echo $value['is_close']; ?>" rel="<?php echo $value['booking_id']; ?>"  data-party="<?php echo $value['party_id']; ?>" class="btn btn-default detail btn_report1" data-production_unit="<?php echo $value['production_unit']; ?>" data-status="<?php echo $app_status; ?>" data-party="<?php echo $value['party_id']; ?>" data-view_approval_status="<?php echo $view_approval_status; ?>" >Report</a>


                                            <?php }  } ?>
                                        </td> 
                                    </tr>
                            <?php $count++; } } ?> 
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <?php echo $links; ?>
                            </td>
                        </tr>
                    </table>
                </div>
                    <?php } else { 
                        $params = "?party=$_POST[party]&brand=$_POST[brand]&product=$_POST[category]&from=$_POST[booking_date_from]&to=$_POST[booking_date_to]&type=brand&status=$_POST[status]&employee=$_POST[employee]&production_unit=$_POST[production_unit]";
                        $params_place = "?party=$_POST[party]&brand=$_POST[brand]&product=$_POST[category]&from=$_POST[booking_date_from]&to=$_POST[booking_date_to]&type=place&status=$_POST[status]&employee=$_POST[employee]&production_unit=$_POST[production_unit]";

                        $params_product = "?party=$_POST[party]&brand=$_POST[brand]&product=$_POST[category]&from=$_POST[booking_date_from]&to=$_POST[booking_date_to]&type=combo&status=$_POST[status]&employee=$_POST[employee]&production_unit=$_POST[production_unit]";
                        ?> 

                    <div class="row summary_report">
                                <div class="col-md-3 summary_report_col">
                                </div>
                                <div class="col-md-3 summary_report_col">
                                    <strong>Number Of Bargains</strong>
                                </div>
                                <div class="col-md-3 summary_report_col">
                                    <strong>Weight (MT)</strong>
                                </div>
                    </div>
                    <?php
                    if($tot_sum_report)
                    {  foreach ($tot_sum_report as $key => $tot_sum_report_value) { 
                        ?>
                        <div class="row summary_report">
                            <div class="col-md-3 summary_report_col">
                                <strong>Total</strong>
                            </div>
                            <div class="col-md-3 summary_report_col">
                                <?php echo $tot_sum_report_value['bargain_count']; ?>
                            </div>
                            <div class="col-md-3 summary_report_col">
                                <?php echo ($tot_sum_report_value['weight']) ? round($tot_sum_report_value['weight'],2) : 0;; ?>
                            </div>
                        </div>
                    <?php } }

                    if($locked)
                    {  foreach ($locked as $key => $locked_value) { 
                        ?>
                        <div class="row summary_report"  <?php echo (isset($_POST['status']) && ($_POST['status']=='lock' || $_POST['status']=='')) ? '"' : 'style="display:none;"'; ?>>
                            <div class="col-md-3 summary_report_col">
                                <strong>Locked</strong>
                            </div>
                            <div class="col-md-3 summary_report_col">
                                <?php echo $locked_value['bargain_count']; ?>
                            </div>
                            <div class="col-md-3 summary_report_col">
                                <?php echo ($locked_value['weight']) ? round($locked_value['weight'],2) : 0; ?>
                            </div>
                        </div>
                    <?php } }

                    if($sum_report)
                    { 
                        if(isset($_POST['status']) && ($_POST['status']!='lock'))
                        {
                        foreach ($sum_report as $key => $sum_report_value) { ?>

                            <div class="row summary_report">
                                <div class="col-md-3 summary_report_col">
                                    <strong><?php 
                                    if($sum_report_value['status']==0)
                                       echo "Pending";
                                     elseif($sum_report_value['status']==2)
                                       echo "Approved";
                                     else
                                        echo "Rejected";
                                     ?></strong>
                                </div>
                                <div class="col-md-3 summary_report_col">
                                    <?php echo $sum_report_value['bargain_count']; ?>
                                </div>
                                <div class="col-md-3 summary_report_col">
                                    <?php echo round($sum_report_value['weight'],2); ?>
                                </div>
                            </div> 
                        <?php } }
                    } ?>

                    <h4>Summary Based on Packing Type (Bulk/Consumer) </h4>
                    <div class="row summary_report">
                        <div class="col-md-3 summary_report_col">
                        </div> 
                        <div class="col-md-3 summary_report_col">
                            <strong>SKU Weight (MT)</strong>
                        </div>
                    </div>  
                    <?php
                    if($product_packing_type_summary)
                    {  foreach ($product_packing_type_summary as $key => $product_packing_type_value) { 
                        ?>
                        <div class="row summary_report">
                            <div class="col-md-3 summary_report_col">
                                <strong><?php echo ($key) ? 'Consumer' : 'Bulk'; ?></strong>
                            </div> 
                            <div class="col-md-3 summary_report_col">
                                <?php echo ($product_packing_type_value['weight']) ? round($product_packing_type_value['weight'],2) : 0;; ?>
                            </div>
                        </div>
                    <?php } } else { ?>
                        <div class="row summary_report">
                            <div class="col-md-12 ">
                                No Record Found
                            </div> 
                        </div>
                    <?php } ?>

                    <h4>Summary Based on Product 
                        <?php if($bookings_product) { ?>
                            <a class="btn btn-default"  style="display: inline-block;float: right;" target="_blank" href="<?php echo base_url('booking/summary_print').$params_product; ?>">Print</a>
                        <?php } ?>
                    </h4> 
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr>
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">S.No</th>     
                                <th rowspan="2"style="text-align:left; vertical-align: middle;">Product</th>
                                <th rowspan="2"style="text-align:left; vertical-align: middle;">Qty (Tins/Cartons)</th>
                                <th rowspan="2"style="text-align:left; vertical-align: middle;">Weight (MT)</th>
                                <th style="text-align:center;" colspan="2">Avg Rate / Loose(kg)</th> 
                            </tr>
                            <tr>
                                <th>Others</th>
                                <th>North East</th>
                            </tr>
                        </thead>
                        <tbody class="">
                            <?php $weight_total_summary = 0; $sn = 1; if($bookings_product) {
                                foreach ($bookings_product as $key => $bookings_brand_product1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product1['weight'];
                                    ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>   
                                    <td><?php echo $bookings_brand_product1['category_name']; ?></td> 
                                    <td><?php echo $bookings_brand_product1['quantity']; ?></td> 
                                    <td><?php echo round($bookings_brand_product1['weight'],2); ?></td>
                                    <td style="text-align:center;" >
                                        <?php 
                                        if($bookings_brand_product1['avg_rate_other']) 
                                        {                                         
                                            echo $avg_rate =  round($bookings_brand_product1['avg_rate_other'],2);

                                            $loose_kg = (strtolower($bookings_brand_product1['category_name'])=='vanaspati') ? 0.897 : .91;
                                            echo ' / ';
                                            $avg_rate_loose =  round($bookings_brand_product1['avg_rate_other_loose'],2);
                                            //echo $bookings_brand_product1['avg_rate_other_loose'].'/';
                                            $loose_rate = (($avg_rate_loose)/$loose_kg)/15;
                                            echo round($loose_rate,2);

                                            //echo '/'.$bookings_brand_product1['avg_rate_other_loose_1']; 
                                            //echo '/'.$bookings_brand_product1['avg_rate_other_loose_qty'];
                                        }
                                        ?>   
                                    </td>
                                    <td style="text-align:center;" >

                                        <?php if($bookings_brand_product1['avg_rate_aasam'])
                                        {
                                       
                                            echo $avg_rate =  round($bookings_brand_product1['avg_rate_aasam'],2);

                                            $loose_kg = (strtolower($bookings_brand_product1['category_name'])=='vanaspati') ? 0.897 : .91;
                                            echo ' / ';
                                            $avg_rate_loose =  round($bookings_brand_product1['avg_rate_aasam_loose'],2);
                                            $loose_rate = ((($avg_rate_loose)/$loose_kg)-$bookings_brand_product1['freight_rate'])/15;
                                            echo round($loose_rate,2); 
                                        }
                                         ?> 
                                    </td> 
                                </tr>
                            <?php $sn++; }?>
                            <tr style="color:red;"><td colspan="2"></td><td>Total</td><td ><?php echo round($weight_total_summary,2); ?></td><td colspan="2"></td></tr>
                            <?php  } else { ?>
                            <tr><td colspan="4">No Record Found</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                    <h4>Summary Based on Brand and Product 
                        <?php /*if($bookings_brand_product) { ?>
                            <a class="btn btn-default"  style="display: inline-block;float: right;" target="_blank" href="<?php echo base_url('booking/summary_print').$params; ?>">Print</a>
                        <?php } */?>
                    </h4> 
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr>
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">S.No</th>    
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Brand</th>   
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Product</th>
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Qty (Tins/Cartons)</th>
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Weight (MT)</th>
                                <th style="text-align:center;" colspan="2">Avg Rate / Loose(kg)</th> 
                            </tr>
                            <tr>
                                <th>Others</th>
                                <th>North East</th>
                            </tr>
                        </thead>
                        <tbody class="">
                            <?php $weight_total_summary = 0; $sn = 1; if($bookings_brand_product) {
                                foreach ($bookings_brand_product as $key => $bookings_brand_product1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product1['weight'];
                                    ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>  
                                    <td><?php echo $bookings_brand_product1['brand_name']; ?></td> 
                                    <td><?php echo $bookings_brand_product1['category_name']; ?></td> 
                                    <td><?php echo $bookings_brand_product1['quantity']; ?></td> 
                                    <td><?php echo round($bookings_brand_product1['weight'],2); ?></td>
                                    <td style="text-align:center;" >
                                        <?php //echo round($bookings_brand_product1['avg_rate'],2); 
                                        if($bookings_brand_product1['avg_rate_other'])
                                        {
                                            echo $avg_rate =  round($bookings_brand_product1['avg_rate_other'],2);
                                            $loose_kg = (strtolower($bookings_brand_product1['category_name'])=='vanaspati') ? 0.897 : .91;
                                            echo ' / ';
                                            $avg_rate_loose =  round($bookings_brand_product1['avg_rate_other_loose'],2);
                                            $loose_rate = (($avg_rate_loose)/$loose_kg)/15;
                                            echo round($loose_rate,2);
                                        } ?>   
                                    </td> 
                                    <td style="text-align:center;" >
                                        <?php //echo round($bookings_brand_product1['avg_rate'],2); 
                                        if($bookings_brand_product1['avg_rate_aasam'])
                                        {
                                            echo $avg_rate =  round($bookings_brand_product1['avg_rate_aasam'],2);
                                            $loose_kg = (strtolower($bookings_brand_product1['category_name'])=='vanaspati') ? 0.897 : .91;
                                            echo ' / ';
                                            $avg_rate_loose =  round($bookings_brand_product1['avg_rate_aasam_loose'],2);
                                            $loose_rate = ((($avg_rate_loose)/$loose_kg)-$bookings_brand_product1['freight_rate'])/15;
                                            echo round($loose_rate,2); 
                                        } ?>   
                                    </td> 
                                </tr>
                            <?php $sn++; }?>
                            <tr style="color:red;"><td colspan="3"></td><td>Total</td><td ><?php echo round($weight_total_summary,2); ?></td><td colspan="2"></td></tr>
                            <?php  } else { ?>
                            <tr><td colspan="5">No Record Found</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
               
                    <h4>Summary Based on State 
                        <?php if($bookings_brand_product_place) { ?>
                            <a class="btn btn-default"  style="display: inline-block;float: right;" target="_blank" href="<?php echo base_url('booking/summary_print').$params_place; ?>">Print</a>
                        <?php } ?>
                    </h4>
                     <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr>
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">S.No</th>    
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">State</th>  
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Brand</th>   
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Product</th>
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Qty (Tins/Cartons)</th>
                                <th rowspan="2" style="text-align:left; vertical-align: middle;">Weight (MT)</th>
                                <th style="text-align:center;" colspan="2">Avg Rate / Loose(kg)</th> 
                            </tr>
                            <tr>
                                <th>Others</th>
                                <th>North East</th>
                            </tr>
                        </thead>
                        <tbody class="">
                            <?php $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight']; ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>  
                                    <td><?php echo $bookings_brand_product_place1['state_name']; ?></td>
                                    <td><?php echo $bookings_brand_product_place1['brand_name']; ?></td>
                                    <td><?php echo $bookings_brand_product_place1['category_name']; ?></td> 
                                    <td><?php echo $bookings_brand_product_place1['quantity']; ?></td> 
                                    <td><?php echo round($bookings_brand_product_place1['weight'],2); ?></td> 
                                    <td>
                                        <?php 
                                        if($bookings_brand_product_place1['avg_rate_other'])
                                        {
                                            echo $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                            $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                            echo ' / ';
                                            $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_other_loose'],2);
                                            $loose_rate = (($avg_rate_loose)/$loose_kg)/15;
                                            echo round($loose_rate,2);
                                        } ?>                                            
                                    </td> 
                                    <td>
                                        <?php 
                                        if($bookings_brand_product_place1['avg_rate_aasam'])
                                        {
                                            echo $avg_rate =  round($bookings_brand_product_place1['avg_rate_aasam'],2);

                                            $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                            echo ' / ';
                                            $avg_rate_loose =  round($bookings_brand_product_place1['avg_rate_aasam_loose'],2);
                                            $loose_rate = ((($avg_rate_loose)/$loose_kg)-$bookings_brand_product_place1['freight_rate'])/15;
                                            echo round($loose_rate,2);
                                        } ?>                                            
                                    </td> 
                                </tr>
                            <?php $sn++; } ?>
                            <tr style="color:red;"><td colspan="4"></td><td>Total</td><td ><?php echo round($weight_total_summary,2); ?></td><td colspan="2"></td></tr>
                            <?php } else { ?>
                                <tr><td colspan="6">No Record Found</td></tr>
                            <?php } ?>
                        </tbody>
                    </table></div>
                    <?php } ?>
                </div>
                <!--
                <div>
                    <span><strong>Total Ordered Weight : <?php echo $total_weight; ?> in Kg (<?php echo $total_weight/1000; ?> In Ton)</strong></span>
                </div>-->
                 
            </div>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#booking_date_from,#booking_date_to").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
    var number_of_packings = 0;
    $(".add_more").click(function(){
        var packing_added = $(".packaging").find(".row").length; 
        if(number_of_packings>packing_added)
        {
            var packing_list ="<option value=''>Select Packed In</option>";
            var category_id = $("#category").val(); 
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>product/getproduct',
                data: { 'category_id': category_id},
                success: function(msg){ 
                    packing_list = msg;
                    $('.packaging').append('<div class="row more_added_products"><div class="col-md-4"> <div class="form-group"> <label for="name">Packed In</label> <select class="form-control product_packing" id="" name="product[]" required>'+packing_list+'</select></div></div><div class="col-md-4"> <div class="form-group"> <label for="quantity">Quantity</label> <input type="text" class="form-control quantity_packed" id="" name="quantity[]" required value=""> <span class="txt-danger"></span> </div></div><div class="col-md-4"> <div class="form-group"><label class="btn-block">&nbsp;</label> <span class="remove btn btn-default">Remove</span> </div></div></div>');
                }
            }); 
        }    
        else
        {
            alert("only "+number_of_packings+" packaging are available.");
        }    
    }); 
    
    $(document).on('change', '.product_packing', function(){
        var packing_in = $(this).val();
        var match_count = 0;
        $(".product_packing").each(function(){  
            if($(this).val()==packing_in)
            {
                match_count++;
            }            
        });  
        if(match_count>=2)
        {
            alert("Already selected");
            $(this).val('');
        }
    });

    $(document).on('keyup', '.rate', function(){
         if (this.value.match(/[^0-9.]/g, '')) { 
          this.value = this.value.replace(/[^0-9.]/g, '');      
        } 
    });

    $(document).on('keyup', '.quantity_packed', function(){
        
        var category_id = $("#category").val();
        var category_name = $("#category :selected").text().toLowerCase();  
        var category_name = category_name.trim();
        if(category_id=='')
        {
            alert("Please select product"); 
            this.value = '';
            return false;
        }

         if (this.value.match(/[^0-9]/g, '')) { 
          this.value = this.value.replace(/[^0-9]/g, '');      
        }  

        var l_to_kg = .910; 
        if(category_name=='vanaspati')
            var l_to_kg = .897;
        var total_weight_kg= (((this.value)*15)*l_to_kg);
        var mt =  total_weight_kg/1000;
        var mt_rond = mt.toFixed(2);;
        $("#weight").val(mt);
        $('.Total_Weight_MT').text(mt_rond+' MT');
    });
    $(document).on('click', '.remove', function(){
        $(this).parent().parent().parent().remove();
    });

    $(".show_terms").click(function(){
        $('.payment_terms_section').hide();
        $('.dispatch_delivery_terms_section').hide();
        var payment_terms = $(this).attr('data-payment-term');
        var dispatch_delivery_terms = $(this).attr('data-dispatch_delivery-term');

        if(payment_terms.trim()!='')
        {
            $('.payment_terms').text(payment_terms);
            $('.payment_terms_section').show();
        }
        if(dispatch_delivery_terms.trim()!='')
        {
            $('.dispatch_delivery_terms').text(dispatch_delivery_terms);
            $('.dispatch_delivery_terms_section').show();
        }

        $('#myModalTerms').modal('show');
    });
    $(".reset").click(function(){
        $("#dtp_input1").val('');
        $(".calc_b").val('');
    });
    $("#brand").change(function(){
        var brand_id = $(this).val(); 
        $("#quantity").val('');
        $(".Total_Weight_MT").text('');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getcategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
                $("#category").html(msg);
            }
        });
    });
    $("#category").change(function(){
        var category_id = $(this).val(); 
        var brand_id = $('#brand').val(); 
        $("#quantity").val('');
        $(".Total_Weight_MT").text('');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getproductlist',
            data: { 'category_id': category_id},
            success: function(msg){

                var response = msg.split("__");
                $(".product_packing").html(response[0]);
                number_of_packings = response[1];
                $('.more_added_products').remove();
            }
        });


        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getrate',
            data: { 'category_id': category_id,'brand_id': brand_id},
            success: function(rate){
                //alert(rate);
                $("#rate").val(rate);
            }
        });
    }); 
    $(document).ready(function(){ 
         
        //$(".show_details").click(function(){
        $(document).on('click', '.show_details', function(){
            var booking_id = $(this).attr('rel');  
            var deccoded_booking_id = atob(booking_id);
            var ele =  $(this);
            $("#divLoading").css({display: "block"});
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>booking/details',
                data: { 'booking_id': booking_id},
                success: function(msg){
                    $("#divLoading").css({display: "none"});
                    //alert(msg);
                    $("#UpdateTime").text(msg);
                    $('#DetailModal').modal('show'); 
                }
            });
        });

        
        $(document).on('click', '.ApproveAllkBtn', function(){
            $("#ApproveRemarkall").val(''); 
            $('#DetailModal').modal('hide');
            $("#ApproveAllremark").modal('show');
        });
        $(document).on('click', '.invoice_generate', function(){
            var booking_id = $(this).attr('rel');
            var production_unit = $('input[name="production_unit"]:checked').val(); 
            var ApproveRemark = $("#ApproveRemarkall").val();
            //alert(booking_id); alert(production_unit);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>booking/GetBookingInfoDetailsPdf',
                data: { 'booking_id': booking_id,'production_unit': production_unit,'ApproveRemark': ApproveRemark},
                success: function(msg){  
                     alert("invoice mailed successfully");
                     $("#ApproveRemarkall").val('');
                     $("#ApproveAllremark").modal('hide');
                     $('#DetailModal').modal('hide'); 
                }
            });
        });   

        $(document).on('click', '.approve_btn', function(){
            var booking_id = $(this).attr('rel'); 
            if(booking_id!='')
            {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>booking/approve_order',
                    data: { 'booking_id': booking_id},
                    success: function(msg){ 
                        alert("Order apporoved successfully.");
                        location.reload();
                    }
                });
            }
        });
        $(document).on("click", ".send_mail_plant_status", function(event){ 
            event.preventDefault();
            var party_id = $(this).attr('rel');
            var data_production_unit = $(this).attr('data-production_unit');
            $('.send_mail_plant').attr('rel',party_id);
            $('.send_mail_plant').attr('data-production_unit',data_production_unit);
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>booking/getbargainweight/',
                data: {'party_id':party_id}, 
                dataType: "html",
                success: function(weight_bargains){ 
                    //$('.LockMailModal_Weight').tetx(weight+" MT");
                    var weight_bargain_array =  weight_bargains.split("__");
                    var weight = weight_bargain_array[0];
                    var bargains = weight_bargain_array[1];
                    var pop_content;
                    if(weight==0)
                    {
                        alert("There is no pending bargains to send mail.");
                        return false;
                    }
                    pop_content = "<p>Total Locked Bargains: <strong>"+weight+" MT</strong></p>";
                    if(parseFloat(weight)<9)
                    {
                        pop_content = pop_content+"<p>Your locked bargain is still less than Truck load.</p>";
                        if(bargains>1)
                        {
                            pop_content = pop_content+"<p>Add more SKU in pending bargains.</p>";
                            pop_content = pop_content+"<p><a class='btn btn-warning' href='<?php echo base_url(); ?>booking'>Add SKU</a></p>";

                        }
                    }
                    else
                    {
                       if(bargains>1)
                       {
                            pop_content = pop_content+"<br> Do you want to add more SKU in another bargin before sending for dispatch to plant. ";
                            pop_content = pop_content+"<br><a href='<?php echo base_url(); ?>booking'>Add SKU</a>";
                        }
                    }
                    $(".LockMailModalContent").html(pop_content);
                    $("#DetailModal").modal('hide'); 
                  $("#LockMailModal").modal('show');  
                }
            }); 
        });

        $(document).on("click", ".send_mail_plant", function(event){
            event.preventDefault();
            party_id = $(this).attr('rel');
            var mailremark = $("#mailremark").val();
            var production_untit = $('.send_mail_plant').attr('data-production_unit');
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>booking/sendmail_plant_lock_bulk/'+party_id,
                data: {'reamrk' : mailremark,'production_untit' : production_untit}, 
                dataType: "html",
                success: function(data){ 
                    //alert("Mail sent"); 
                    $('.mail_sent').text("Mail sent successfully.");
                    $(".close_btn").hide();
                    $(".homaepagelink").show();
                    $("#LockMailModal").modal('hide');
                    $("#SuccessmailModal").modal('show');
                }
            });
        });
        $(document).on('click', '.mail_btn', function(){
            var booking_id = $(this).attr('rel'); 
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>booking/sendmail_plant',
                data: { 'booking_id': booking_id},
                success: function(msg){ 
                    alert("Mail sent successfully.");
                }
            });
        });
        $(document).on('click', '.detail', function(){
            var booking_id = $(this).attr('rel');

            var view_approval_status = $(this).attr('data-view_approval_status');

            var data_status = $(this).attr('data-status'); 
             
            var user_role = "<?php echo $logged_role; ?>";
            var data_production_unit = $(this).attr('data-production_unit');
            var data_party = $(this).attr('data-party');
            $('.send_mail_plant_status').attr('rel',data_party);
            $('.send_mail_plant_status').attr('data-production_unit',data_production_unit);
            //alert(booking_id);
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>booking/GetBookingInfoDetails',
                data: { 'booking_id': booking_id},
                success: function(msg){ 

                    $("#divLoading").css({display: "none"}); 
                    //$('.invoice_generate').attr('rel',booking_id);
                    var href= "<?php echo base_url('booking/downloadreport').'/'; ?>"+btoa(booking_id);
                    $('.btn_report').attr('href',href);
                    $(".BookingInfoDetails").html(msg);

                    if(user_role==2 || user_role==3 || user_role==5)
                    {

                        //$('.mail_btn').attr('rel',btoa(booking_id));
                        $('.mail_btn').attr('rel',booking_id);
                        $('.mail_btn').text('Send mail to '+data_production_unit+' plant');
                        $('.approve_btn').text('Approve');
                        $('.approve_btn').attr('rel',booking_id);
                        $(".approve_btn").css({"cursor": "pointer"}); 
                        if(data_status==1)
                        {
                            $(".approve_btn").css({"cursor": "none"}); 
                            $('.approve_btn').text('Approved');
                            $('.approve_btn').attr('rel','');
                        }   

                        if(user_role==5 && view_approval_status==1)                     
                        {
                            $('.approve_btn').show();
                        }
                        else if(user_role==5 && view_approval_status==0)
                        {
                            $('.approve_btn').hide();
                        }
                    }

                    $('#DetailModal').modal('show'); 
                }
            });
        });     
        $(document).on('click', '.update_status_reject', function(){
            //$("#unlock_radio").trigger("change");
            $("#unlock_radio").trigger('click');
            $("#remark").val('');
            $("#v_ramark").html('');
            $(".reject_status_radio").show();
            $(".bargainer_list").hide();
            $("#bargainer").val('');
            $("#divLoading").css({display: "block"});
            var booking_id = $(this).attr('rel');  
            var data_status = $(this).attr('data-status');  
            if(data_status==3)
            {
                $('.reject_status_label').text('Unlock');
                var btn = '<span class="btn btn-danger update_status" rel="'+booking_id+'" data-status="3"><a class="reject_status_label" style="color:#fff;" href="javascript:void(0)" >Unlock</a></span>';
            }
            else if(data_status==0)
            {
                var btn = '<span class="btn btn-danger update_status" rel="'+booking_id+'" data-status="0"><a class="reject_status_label" style="color:#fff;" href="javascript:void(0)" >Unapprove</a></span>';
                $(".update_radio").hide();
                $( '#unapproved_radio' ).click();
                $(".reject_status_label").text("Unapprove");
                $(".unapproved_radio_span").show();
            }
            else
            {
                var btn = '<span class="btn btn-success update_status" rel="'+booking_id+'" data-status="2"><a href="javascript:void(0)" >Approve</a></span>'; 
            }
            $('.submit_reject').html(btn);
            $('#myModal').modal('show');
            $("#divLoading").css({display: "none"});
        }); 
        $(document).on('click', '.reject_status_radio', function(){
            var reject_status_label =  $(".reject_status_radio:checked").parent().text();
            $('.reject_status_label').text(reject_status_label);

            if($(".reject_status_radio:checked").val()==5)
            {
                $(".bargainer_list").show();
            }
            else
            {
                $(".bargainer_list").hide();
            }

        });

        $(document).on('click', '.update_status', function(){
            var status = $('input[name="reject_status"]:checked').val();
            var remark = $("#remark").val();
			var lock_status = $(this).attr('lock-status');
            if(status==3 || status==2 || status==0 || status==5 || status==4)
            {
                if(remark.trim()=='')
                {
                    $("#v_ramark").html('<span style="color:red">Please Enter Remark</span>');
                    return false;
                }
                else
                { 
                    $("#v_ramark").html('');
                }
            }
            else
            {    
                if (!confirm("Are you sure ?")){
                  //return false;
                }
            } 
            var bargainer = 0;
            if(status==5)
            {
                var bargainer = $("#bargainer").val();

                if(bargainer.trim()=='')
                {
                    $("#v_bargainer").html('<span style="color:red">Please Select Maker</span>');
                    return false;
                }
                else
                { 
                    $("#v_bargainer").html('');
                }
            }
            var booking_id = $(this).attr('rel');  
            var deccoded_booking_id = atob(booking_id);
            var ele =  $(this);
            $("#divLoading").css({display: "block"});
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>booking/updatestatus',
                data: { 'booking_id': booking_id,'status': status,'remark': remark,'bargainer': bargainer},
                success: function(msg){
                    $("#divLoading").css({display: "none"});
                    if(msg)
                    {
                       $("#remark").val('');
                        ele.unbind('click'); 
                        ele.removeClass( "btn-danger update_status" );
                        
                        if(status==1)
                        {
                            ele.addClass( "btn-warning" );
                            ele.text("Checked"); 
                        }
                        else if(status==2)
                        { 
                            //ele.closest('.approval_status_section').html("<span class='btn btn-success'>Approved</span>");
                            $('.remark'+deccoded_booking_id).text(remark);
                            $('#myModal').modal('hide');
                            $('.'+deccoded_booking_id).html("<span class='btn btn-success show_details' rel='"+booking_id+"'>Approved</span>"); 
                        }
                        else if(status==3)
                        {
                            $('.remark'+deccoded_booking_id).text(remark);
                            $('#myModal').modal('hide');
                            $('.'+deccoded_booking_id).html("<span class='btn btn-danger show_details' rel='"+booking_id+"'>Rejected</span>"); 
                            //ele.closest('.approval_status_section').html("<span class='btn btn-danger'>Rejected</span>"); 
                        }
                        location.reload();
                    }
                }
            });
        });
    });
});
</script>
 
 
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script type="text/javascript">
    $('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });
</script>
<?php $total_weight_ton = $total_weight/1000; ?>
<script type="text/javascript">
    var total_weight_ton = '<?php echo round($total_weight_ton,3);?>'
    $('#total_weight').html("<b>Total Ordered Weight : "+total_weight_ton+" Ton </b><br>");
</script>



<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
    $(document).ready(function(){
        $("#party").select2(); 
        $("#broker").select2(); 
        //$("#bargainer").select2(); 
        $("#employee").select2(); 
        
    });
    $(document).ready(function() {
      $(window).keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
      });
    });
</script>
<!-- Trigger the modal with a button -->


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title reject_status_label">Unlock</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">  
                        <?php if($logged_role==4)  { ?>
                        <span class="reject_radio_span update_radio">
                        <input type="radio" id="reject_radio" class="reject_status_radio" name="reject_status" value="3" > Reject
                        </span>
                        <span class="unapproved_radio_span update_radio">
                            <input type="radio" id="unapproved_radio" class="reject_status_radio" name="reject_status" value="0"> Unapprove
                        </span>
                        <span class="employee_radio_span update_radio">
                            <input type="radio" id="change_employee_radio" class="reject_status_radio" name="reject_status" value="5"> Change Bargainer
                        </span>
                        <?php } ?>
                        <span class="unlock_radio_span update_radio">
                            <input type="radio" id="unlock_radio" class="reject_status_radio" name="reject_status" value="4" checked> Unlock
                        </span>
                    </div>
                </div>
                <div class="row bargainer_list" style="display:none;">
                     
                        <div class="col-md-12">  
                            <select name="bargainer" id="bargainer" class="form-control">
                                <option value="">Select Bargainer</option> 
                                <?php if($employees) { 
                                    foreach ($employees as $key => $employee) { ?>
                                <option value="<?php echo $employee['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$employee['id']) { echo "selected"; } }; ?>><?php echo $employee['name']; ?></option>
                                <?php } } ?>
                            </select>                    
                            <span id="v_bargainer"></span>
                        </div> 
                </div>
                <div class="row">
                        <div class="col-md-12">  
                            <textarea class="form-control" name="remark" id="remark" placeholder="Enter Remark" required></textarea> 
                            <span id="v_ramark"></span>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="submit_reject"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>


<div id="myModalTerms" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Terms</h4>
            </div>
            <div class="modal-body">
                <div class="payment_terms_section" style="display:none;">
                    <h3>Payment Terms</h3>
                    <div class="payment_terms"></div>
                </div>
                <div class="dispatch_delivery_terms_section" style="display:none;">
                    <h3>Dispatch/Delivery Terms</h3>
                    <div class="dispatch_delivery_terms"></div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="submit_reject"></span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>


<div id="DetailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body BookingInfoDetails"> 
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" rel="" class="btn btn-default btn_report detail">Download Report</a>
                <?php if($logged_role == 3 || $logged_role == 2 || $logged_role == 5) { ?>
                <span class="approve_btn btn btn-default" rel="">Approve</span>
                <span class="mail_btn btn btn-default" rel="" style="display: none;">Send</span>
                <?php } ?> 
                <?php if($logged_role == 1) { ?>
                  
                <span class="send_mail_plant_status btn btn-default" rel="">Send Mail to all locked bargains</span>
                <?php } ?> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>



<div id="RemarkModal1" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Remark</h4>
            </div>
            <div class="modal-body">
                <textarea class="form-control" name="remark" id="remark_close"></textarea>
                <span id="v_ramark"></span>
            </div>
            <div class="modal-footer">
                <span class="submit_reject"></span>
                <button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>

<div id="BookingSuccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Success</h4>
            </div>
            <div class="modal-body">
                Order Booked.
            </div>
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>
<!-- Modal -->
<div id="ApproveAllremark" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Remark</h4>
            </div>
            <div class="modal-body">
                <input type="radio" class="production_unit" name="production_unit" checked value="alwar"> Alwar
                <input type="radio" class="production_unit" name="production_unit" value="jaipur"> Jaipur
                <textarea class="form-control" name="ApproveAllremark" id="ApproveRemarkall"></textarea>
                
            </div>
            <div class="modal-footer"> 
                <span class="invoice_generate btn btn-default" rel="">Approve All & Send Invoice</span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>


<div id="SuccessmailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Mail Sent</h4>
            </div>
            <div class="modal-body "> 
                    <div class="mail_sent"></div>
                    <div class="row booked_sku_info"> 

                    </div>
            </div>
            <div class="modal-footer"> 
                <a style="display: none;" href="<?php echo base_url('booking/report'); ?>" class="homaepagelink btn btn-default">Close</a>
                <button type="submit" class="btn btn-default close_btn" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>


<div id="LockMailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body "> 
                <div class="LockMailModalContent"></div>
                <div class="form-group">
                        <label>Remark Form Dispatch Team / Plant</label>
                </div>
                <textarea name="remark" id="mailremark"  class="form-control" placeholder=" tentative dispatch date etc"> </textarea>
            </div>
            <div class="modal-footer"> 
                <a href="javascript:void(0)" class="btn btn-default send_mail_plant"  rel="" >Send Mail</a>
                <a style="display: none;" href="<?php echo base_url('booking'); ?>" class="homaepagelink btn btn-default">Close</a>
                <button type="submit" class="btn btn-default close_btn" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>