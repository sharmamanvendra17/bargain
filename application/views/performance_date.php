<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
             <div class="panel-heading">
                <div class="row">

                        <div class="col-md-8">   
                            <span class="title elipsis">
                                <strong><?php echo $title; ?></strong> 
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
                                    <input type="hidden" name="booking_date" id="dtp_input1" value=""><br>
                                </div> &nbsp;&nbsp;
                                <a href="javascript:void(0)" class="reset btn">Reset</a>
                            </span>               

                        </div>

                </div>
             
                
            </div>
            <div class="panel-body">
                    <form action="" class="" method="post" id="addbooking">
                        <div class="row"> 
                            <div class="col-md-4" <?php echo  (($logged_role==6 || $logged_role ==1)) ? 'style="display:none;"' : ''; ?>>                           
                                <div class="form-group">
                                    <label for="name">Employee </label> 
                                    <select class="form-control" id="employee" name="employee" >
                                        <option value="">Select Employee</option>
                                        <?php if($users) { 
                                            foreach ($users as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Start Date</label> 
                                     <input class="form-control" type="text" id="booking_date_from" name="booking_date_from"  value="<?php if(isset($_POST['booking_date_from']) && !empty($_POST['booking_date_from']) ) { echo $_POST['booking_date_from']; } else { echo date("d-m-Y", strtotime("first day of this month")); } ?>" />
                                    <span class="txt-danger"><?php echo form_error('year'); ?></span>
                                </div> 
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">End Date</label> 
                                    <input class="form-control" type="hidden" id="booking_date_to" name="booking_date_to" value="<?php if(isset($_POST['booking_date_to']) && !empty($_POST['booking_date_to']) ) { echo $_POST['booking_date_to']; } else { echo date("d-m-Y", strtotime("last day of this month")); } ?>"/>
                                    <span class="txt-danger"><?php echo form_error('month'); ?></span>
                                </div>
                            </div>
                            <?php if($logged_role==5) { ?>
                            <div class="col-md-12">
                                <div class="form-group"> 
                                    <label for="rate">Report</label>
                                    <input class="" type="radio" id="" name="report_type"  value="1"   <?php echo (!isset($_POST['report_type']) || $_POST['report_type']==1) ? 'checked' : ''; ?> />My Team
                                    <input class="" type="radio" id="" name="report_type"  value="2" <?php echo (isset($_POST['report_type']) && $_POST['report_type']==2) ? 'checked' : ''; ?> />All 
                                </div>
                            </div>
                            <?php } ?>
                        </div> 
                        <div class="row">
                            <?php if($logged_role==4) { ?>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">Team Lead</label> 
                                        <select name="temalead" id="temalead" class="form-control">
                                            <option value="">Select Team Lead</option>
                                            <?php if($team_leads)
                                            {
                                                foreach ($team_leads as $key => $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['temalead'])) { if($_POST['temalead']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>                                
                            <?php } ?>
                            <div class="col-md-4">
                                <div class="form-group"><label for="rate">&nbsp;</label> </div>
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
                                    <button type="submit" class="btn btn-default booking_submit" value="Search">Search History</button>  
                                </div>                                  
                            </div> 
                            <div class="col-md-4">
                            </div>                             
                        </div>
                        </form>    

                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <h4>Total Summary (Category Wise) For <span class="selected_employee"></span></h4>
                            <?php $total_bargain_weight_inline = 0;
                                $total_bargain_amount_inline = 0;

                                $total_pi_weight_inline = 0;
                                $total_pi_amount_inline = 0;

                                $total_pi_dispetch_weight_inline = 0;
                                $total_pi_dispetch_amount_inline = 0;  

                            $booking_summary =  GetTotalBookingsummary1($postdata);  ?>
                            <table class="table accordionTable table-striped table-bordered table-hover ">
                                <thead>
                                  <tr>
                                    <th rowspan="2" >Brand Name</th>
                                    <th rowspan="2">Category Name</th>
                                    <th  colspan="2">Bargain Booked</th>
                                    <th  colspan="2">Primary PI</th>
                                    <th  colspan="2">Dispatched</th> 
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <th colspan="2"></th>
                                    <th>Ton</th>
                                    <th>Amount</th>
                                    <th>Ton</th>
                                    <th>Amount</th>
                                    <th>Ton</th>
                                    <th>Amount</th> 
                                  </tr>
                                    <?php 
                                    $brand_total_weight = 0;
                                    $brand_total_amount = 0;

                                    $brand_pi_total_weight = 0;
                                    $brand_pi_total_amount = 0;

                                    $brand_dispateched_total_weight = 0;
                                    $brand_dispateched_total_amount = 0;
                                    if($booking_summary) {
                                    foreach ($booking_summary as $booking_summary_key => $booking_summary_value) {
                                        $brand_total_weight = $brand_total_weight+$booking_summary_value['total_weight'];
                                        $brand_total_amount = $brand_total_amount+$booking_summary_value['total_amount'];


                                        $brand_pi_total_weight = $brand_pi_total_weight+$booking_summary_value['total_weight_pi'];
                                        $brand_pi_total_amount = $brand_pi_total_amount+$booking_summary_value['total_amount_pi'];


                                        $brand_dispateched_total_weight = $brand_dispateched_total_weight+$booking_summary_value['total_dispateched_weight'];
                                        $brand_dispateched_total_amount = $brand_dispateched_total_amount+$booking_summary_value['total_dispatched_amount'];

                                     ?>
                                    <tr>
                                        <td><?php echo $booking_summary_value['brand_name']; ?></td>
                                        <td><?php echo $booking_summary_value['category_name']; ?></td>
                                        <td><?php echo ($booking_summary_value['total_weight']) ? round($booking_summary_value['total_weight'],2) : 0;?></td>
                                        <td><?php echo number_format($booking_summary_value['total_amount'],2);?></td>

                                        <td><?php echo ($booking_summary_value['total_weight_pi']) ? round($booking_summary_value['total_weight_pi'],2) : 0;?></td>
                                        <td><?php echo number_format($booking_summary_value['total_amount_pi'],2);?></td>

                                        <td><?php echo ($booking_summary_value['total_dispateched_weight']) ? round($booking_summary_value['total_dispateched_weight'],2) : 0;?></td>
                                        <td><?php echo number_format($booking_summary_value['total_dispatched_amount'],2);?></td> 
                                    </tr> 
                                    <?php } } ?> 
                                    <tr>
                                        <td colspan="2"><strong>Total</strong></td>
                                        <td><strong><?php echo ($brand_total_weight) ? round($brand_total_weight,2) : 0;?></strong></td>
                                        <td><strong><?php echo number_format($brand_total_amount,2);?></strong></td>

                                        <td><strong><?php echo ($brand_pi_total_weight) ? round($brand_pi_total_weight,2) : 0;?></strong></td>
                                        <td><strong><?php echo number_format($brand_pi_total_amount,2);?></strong></td>

                                        <td><strong><?php echo ($brand_dispateched_total_weight) ? round($brand_dispateched_total_weight,2) : 0;?></strong></td>
                                        <td><strong><?php echo number_format($brand_dispateched_total_amount,2);?></strong></td> 
                                    </tr> 
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>           
                <div class="row">
                    <div class="col-md-12">  
                        <nav class='animated saleAccordion bounceInDown'>
                            <ul>
                                <?php if($response) {
									$total_state_weight = 0.00;
									$total_state_amount = 0.00;

                                    $total_state_pi_weight = 0.00;
                                    $total_state_pi_amount = 0.00;


									$sec_total_state_weight = 0.00;
									$sec_total_state_amount = 0.00;

                                    $sec_pi_total_state_weight = 0.00;
                                    $sec_pi_total_state_amount = 0.00;


                                    $pi_total_weight_dispatched = 0.00;
                                    $pi_total_amount_dispatched = 0.00;

                                    $pi_total_state_weight_dispatched = 0.00;
                                    $pi_total_state_amount_dispatched = 0.00;
                                foreach ($response as $key => $value) { 
                                    $state_info = $value['state'];
                                    $maker_info = $value['makers'];
                                    $secondry_maker_info = $value['secondry_makers'];
                                    ?>
                                <li class='sub-menu'><a href='#settings'><?php echo $state_info['name']; ?><div class='fa fa-caret-down right'></div></a>
                                    <ul>
                                        <li class='next-sub-menu'><a href='#settings'>Makers <?php if( count($maker_info)) { ?><div class='fa fa-caret-down right'></div><?php } ?> <span class="text-right" style="float: right;padding-right: 25px;"><?php echo makerstotalerformance($state_info['id'],$postdata); ?></span></a>
                                            <?php $booking_summary =  GetBookingsummary($state_info['id'],$postdata); ?>
                                                <ul>
                                                    <li>
                                                        <div class="table-responsive">
                                                        <table class="table accordionTable table-striped table-bordered table-hover ">
                                                            <thead>
                                                              <tr>
                                                                <th rowspan="2">Employee Name</th>
                                                                <th  colspan="2">Bargain Booked</th>
                                                                <th  colspan="2">Primary PI</th>
                                                                <th  colspan="2">Dispatched</th>
                                                                <th  colspan="2">Monthly Effort</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <tr>
                                                                <th></th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Sales(%)</th>
                                                                <th>Visit(%)</th>
                                                              </tr>
                                                            <?php if($maker_info) {
															$display = 1;	
                                                            $total_bargain_weight_inline = 0;
                                                            $total_bargain_amount_inline = 0;

                                                            $total_pi_weight_inline = 0;
                                                            $total_pi_amount_inline = 0;

                                                            $total_pi_dispetch_weight_inline = 0;
                                                            $total_pi_dispetch_amount_inline = 0; 
                                                            foreach ($maker_info as $key => $maker_info_value) { 

                                                            $role = $maker_info_value['role'];
                                                            $maker_state = $maker_info_value['state_id'];
                                                            $maker_state_ids = explode(',', $maker_info_value['state_id']);
                                                            $current_state = $state_info['id'];

                                                            if(($role==6 && ($maker_info_value['total_weight']!=0 || $maker_info_value['pi_total_amount']!=0  ) ) || ($role==1))
                                                            {

															$total_state_weight = $total_state_weight + $maker_info_value['total_weight'];
															$total_state_amount = $total_state_amount + $maker_info_value['total_amount'];

                                                            $total_state_pi_weight = $total_state_pi_weight + $maker_info_value['pi_total_weight'];
                                                            $total_state_pi_amount = $total_state_pi_amount + $maker_info_value['pi_total_amount'];


                                                            $pi_total_state_weight_dispatched = $pi_total_state_weight_dispatched + $maker_info_value['total_dispateched_weight'];
                                                            $pi_total_state_amount_dispatched = $pi_total_state_amount_dispatched + $maker_info_value['total_dispatched_amount'];

                                                            $pi_total_weight_dispatched = $pi_total_weight_dispatched+$maker_info_value['total_dispateched_weight'];
                                                            $pi_total_amount_dispatched = $pi_total_amount_dispatched+$maker_info_value['total_dispatched_amount'];

                                                            $total_bargain_weight_inline = $total_bargain_weight_inline+$maker_info_value['total_weight'];
                                                            $total_bargain_amount_inline = $total_bargain_amount_inline+$maker_info_value['total_amount'];

                                                            $total_pi_weight_inline = $total_pi_weight_inline+$maker_info_value['pi_total_weight'];
                                                            $total_pi_amount_inline = $total_pi_amount_inline+$maker_info_value['pi_total_amount'];

                                                            $total_pi_dispetch_weight_inline = $total_pi_dispetch_weight_inline+$maker_info_value['total_dispateched_weight'];
                                                            $total_pi_dispetch_amount_inline = $total_pi_dispetch_amount_inline+$maker_info_value['total_dispatched_amount'];


															 ?> 
                                                                <tr>
                                                                    <td><?php echo $maker_info_value['name']; ?></td>
                                                                    <td><?php echo ($maker_info_value['total_weight']) ? round($maker_info_value['total_weight'],2) : 0;?></td>
                                                                    <td><?php echo number_format($maker_info_value['total_amount'],2);?></td>

                                                                    <td><?php echo ($maker_info_value['pi_total_weight']) ? round($maker_info_value['pi_total_weight'],2) : 0;?></td>
                                                                    <td><?php echo number_format($maker_info_value['pi_total_amount'],2);?></td>

                                                                    <td><?php echo ($maker_info_value['total_dispateched_weight']) ? round($maker_info_value['total_dispateched_weight'],2) : 0;?></td>
                                                                    <td><?php echo number_format($maker_info_value['total_dispatched_amount'],2);?></td>


                                                                    <td><?php echo number_format($maker_info_value['per_target'],2);?></td>
                                                                    <td><?php echo number_format($maker_info_value['per_target_visit'],2);?></td>
                                                                </tr> 
                                                            <?php } 
                                                        } } ?>
                                                            <tr>
                                                                    <td><strong>Total</strong></td>
                                                                    <td><strong><?php echo $total_bargain_weight_inline; ?></strong></td>
                                                                    <td><strong><?php echo $total_bargain_amount_inline; ?></strong></td>
                                                                    <td><strong><?php echo $total_pi_weight_inline; ?></strong></td>
                                                                    <td><strong><?php echo $total_pi_amount_inline; ?></strong></td>
                                                                    <td><strong><?php echo $total_pi_dispetch_weight_inline; ?></strong></td>
                                                                    <td><strong><?php echo $total_pi_dispetch_amount_inline; ?></strong></td>
                                                                    <td colspan="2"></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        </div>
                                                    </li>   
                                                    <li>
                                                        <h4>Brand Wise Summary </h4>
                                                        <div class="table-responsive">
                                                            <table class="table accordionTable table-striped table-bordered table-hover ">
                                                            <thead>
                                                              <tr>
                                                                <th rowspan="2">Brand Name</th>
                                                                <th rowspan="2">Category Name</th>
                                                                <th  colspan="2">Bargain Booked</th>
                                                                <th  colspan="2">Primary PI</th>
                                                                <th  colspan="2">Dispatched</th> 
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <tr>
                                                                <th colspan="2"></th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Ton</th>
                                                                <th>Amount</th> 
                                                              </tr>
                                                                <?php 
                                                                $brand_total_weight = 0;
                                                                $brand_total_amount = 0;

                                                                $brand_pi_total_weight = 0;
                                                                $brand_pi_total_amount = 0;

                                                                $brand_dispateched_total_weight = 0;
                                                                $brand_dispateched_total_amount = 0;
                                                                if($booking_summary) {
                                                                foreach ($booking_summary as $booking_summary_key => $booking_summary_value) {
                                                                    $brand_total_weight = $brand_total_weight+$booking_summary_value['total_weight'];
                                                                    $brand_total_amount = $brand_total_amount+$booking_summary_value['total_amount'];


                                                                    $brand_pi_total_weight = $brand_pi_total_weight+$booking_summary_value['total_weight_pi'];
                                                                    $brand_pi_total_amount = $brand_pi_total_amount+$booking_summary_value['total_amount_pi'];


                                                                    $brand_dispateched_total_weight = $brand_dispateched_total_weight+$booking_summary_value['total_dispateched_weight'];
                                                                    $brand_dispateched_total_amount = $brand_dispateched_total_amount+$booking_summary_value['total_dispatched_amount'];

                                                                 ?>
                                                                <tr>
                                                                    <td><?php echo $booking_summary_value['brand_name']; ?></td>
                                                                    <td><?php echo $booking_summary_value['category_name']; ?></td>
                                                                    <td><?php echo ($booking_summary_value['total_weight']) ? round($booking_summary_value['total_weight'],2) : 0;?></td>
                                                                    <td><?php echo number_format($booking_summary_value['total_amount'],2);?></td>

                                                                    <td><?php echo ($booking_summary_value['total_weight_pi']) ? round($booking_summary_value['total_weight_pi'],2) : 0;?></td>
                                                                    <td><?php echo number_format($booking_summary_value['total_amount_pi'],2);?></td>

                                                                    <td><?php echo ($booking_summary_value['total_dispateched_weight']) ? round($booking_summary_value['total_dispateched_weight'],2) : 0;?></td>
                                                                    <td><?php echo number_format($booking_summary_value['total_dispatched_amount'],2);?></td> 
                                                                </tr> 
                                                                <?php } } ?> 
                                                                <tr>
                                                                    <td colspan="2"><strong>Total</strong></td>
                                                                    <td><strong><?php echo ($brand_total_weight) ? round($brand_total_weight,2) : 0;?></strong></td>
                                                                    <td><strong><?php echo number_format($brand_total_amount,2);?></strong></td>

                                                                    <td><strong><?php echo ($brand_pi_total_weight) ? round($brand_pi_total_weight,2) : 0;?></strong></td>
                                                                    <td><strong><?php echo number_format($brand_pi_total_amount,2);?></strong></td>

                                                                    <td><strong><?php echo ($brand_dispateched_total_weight) ? round($brand_dispateched_total_weight,2) : 0;?></strong></td>
                                                                    <td><strong><?php echo number_format($brand_dispateched_total_amount,2);?></strong></td> 
                                                                </tr> 
                                                            </tbody>
                                                            </table>
                                                        </div>

                                                    </li>
                                                </ul>
                                                
                                        </li> 
                                        <li class='next-sub-menu'> <a href='#settings'>Secondary Makers <?php if( count($secondry_maker_info)) { ?><div class='fa fa-caret-down right'></div><?php  } ?><span class="text-right" style="float: right;padding-right: 25px;"><?php echo secondarymakerstotalerformance($state_info['id'],$postdata); ?></span></a>
                                                <?php if($secondry_maker_info) { ?>
                                                <ul>
                                                    <li>
                                                        <div class="table-responsive">
                                                        <table class="table accordionTable table-striped table-bordered table-hover ">
                                                            <thead>
                                                              <tr>
                                                                <th rowspan="2">Employee Name</th>
                                                                <th  colspan="2">Bargain Booked</th>
                                                                <th  colspan="2">Secondary PI</th> 
                                                                <th  colspan="2">Monthly Effort</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <tr>
                                                                <th></th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Sales(%)</th>
                                                                <th>Visit</th>
                                                              </tr>
                                                            <?php if($secondry_maker_info) { 
                                                            foreach ($secondry_maker_info as $key => $secondry_maker_value) { 
                                                                $role = $secondry_maker_value['role'];
                                                                $secondary_maker_state = $secondry_maker_value['state_id'];
                                                                $secondary_maker_state_ids = explode(',', $secondry_maker_value['state_id']);
                                                                $current_state = $state_info['id'];


                                                                if(($role==1 && ($secondry_maker_value['total_weight']!=0   ) ) || ($role==6 && in_array($current_state, $secondary_maker_state_ids)) )
                                                                {

														$sec_total_state_weight = $sec_total_state_weight + $secondry_maker_value['total_weight'];
														$sec_total_state_amount = $sec_total_state_amount + $secondry_maker_value['total_amount'];


                                                        $sec_pi_total_state_weight = $sec_pi_total_state_weight + $secondry_maker_value['pi_total_weight'];
                                                        $sec_pi_total_state_amount = $sec_pi_total_state_amount + $secondry_maker_value['pi_total_amount'];

															?> 
                                                                <tr>
                                                                    <td><?php echo $secondry_maker_value['name']; ?></td>
                                                                    <td><?php echo ($secondry_maker_value['total_weight']) ? round($secondry_maker_value['total_weight'],2) : 0;?></td>
                                                                    <td><?php echo number_format($secondry_maker_value['total_amount'],2);?></td>

                                                                    <td><?php echo ($secondry_maker_value['total_weight']) ? round($secondry_maker_value['pi_total_weight'],2) : 0;?></td>
                                                                    <td><?php echo number_format($secondry_maker_value['pi_total_amount'],2);?></td>

                                                                    <td><?php echo number_format($secondry_maker_value['per_target'],2);?></td>
                                                                    <td>-</td>
                                                                </tr> 
                                                            <?php } } } ?>
                                                            </tbody>
                                                        </table>
                                                        </div>
                                                    </li>   
                                                </ul> 
                                                <?php } ?> 
                                        </li>
                                    </ul>
                                </li>
                                
                                <?php  						
								} ?>

                                <hr>
								<div class="table-responsive">
                                    <h4>Total Summary For <span class="selected_employee"></span></h4>  
                                        <table class="table accordionTable table-striped table-bordered table-hover ">
                                            <thead>
                                              <tr>
                                                <th colspan="2" width="33%">Primary Bargain</th>
                                                <th colspan="2" width="33%">Primary PI</th>
                                                <th colspan="2" width="33%">Dispatched</th>
                                                <th  colspan="2" width="34%">Secondary Bargain</th>
                                                 <th colspan="2" width="33%">Secondary PI</th>
                                              </tr>
                                            </thead>
                                            <tbody>
                                              <tr>

                                                <th width="16.5%">Ton</th>
                                                <th width="16.5%">Amount</th>
                                                
                                                <th width="16.5%">Ton</th>
                                                <th width="16.5%">Amount</th>
                                                
                                                <th width="17%">Ton</th>
                                                <th width="17%">Amount</th>
                                                
                                                <th width="17%">Ton</th>
                                                <th width="17%">Amount</th>
                                                
                                                <th width="17%">Ton</th>
                                                <th width="17%">Amount</th>
                                                
                                              </tr>
                                               <tr>
                                                    <td><?php echo ($total_state_weight) ? $total_state_weight : 0; ?></td>
                                                    <td><?php echo number_format($total_state_amount,2); ?></td>
                                                    

                                                    <td><?php echo ($total_state_pi_weight) ? $total_state_pi_weight : 0; ?></td>
                                                    <td><?php echo number_format($total_state_pi_amount,2); ?></td>
                                                    

                                                    <td><?php echo ($pi_total_weight_dispatched) ? $pi_total_weight_dispatched : 0; ?></td>
                                                    <td><?php echo number_format($pi_total_amount_dispatched,2); ?></td>
                                                    

                                                    <td><?php echo ($sec_total_state_weight) ? $sec_total_state_weight : 0; ?></td>
                                                    <td><?php echo number_format($sec_total_state_amount,2); ?></td>

                                                    
                                                    <td><?php echo ($sec_total_state_weight) ? $sec_pi_total_state_weight : 0; ?></td>
                                                    <td><?php echo number_format($sec_pi_total_state_amount,2); ?></td>
                                                    
                                                </tr> 
                                            </tbody>
                                        </table>
                                </div>
								<?php } ?> 
                            </ul>
                        </nav>
                    </div>                    
                </div>                   
                </div>
                <!--
                <div>
                    <span><strong>Total Ordered Weight : 0 in Kg (0 In Ton)</strong></span>
                </div>-->
                 
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
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        var selected_employee = $("#employee :selected").text();
        if(selected_employee=='Select Employee')
        {
            var selected_employee = "All States";
        }
        $(".selected_employee").text(selected_employee);
        $("#employee").select2();  
    });
    
     
     
});
</script>





<script type="text/javascript" src="https://sales.datagroup.in/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<script>
$('.sub-menu ul').hide();
$(".sub-menu > a").click(function () {
    
    if($(this).parent(".sub-menu").children("ul").css('display')=='block'){
        $(this).parent(".sub-menu").children("ul").css('display', 'block');
        $(this).find(".right").addClass("fa-caret-up");        
    }
    else{
        $('.sub-menu > a').parent(".sub-menu").children("ul").css('display', 'none');
        $('.sub-menu > a').removeClass("subMenuActive");
        $('.sub-menu > a').find(".right").removeClass("fa-caret-up");
        $('.sub-menu > a').find(".right").addClass("fa-caret-down");

    }
    
    $(this).parent(".sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActive");
    $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
});


$('.next-sub-menu ul').hide();
$(".next-sub-menu > a").click(function () {
    
    if($(this).parent(".next-sub-menu").children("ul").css('display')=='block'){
        $(this).parent(".next-sub-menu").children("ul").css('display', 'block');
        $(this).find(".right").addClass("fa-caret-up");
        
    }
    else{
        $('.next-sub-menu > a').parent(".next-sub-menu").children("ul").css('display', 'none');
        $('.next-sub-menu > a').removeClass("subMenuActive");
        $('.next-sub-menu > a').find(".right").removeClass("fa-caret-up");
        $('.next-sub-menu > a').find(".right").addClass("fa-caret-down");

    }
    
    $(this).parent(".next-sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActive");
    $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
});

$('.next-in-sub-menu ul').hide();
$(".next-in-sub-menu > a").click(function () {
    
    if($(this).parent(".next-in-sub-menu").children("ul").css('display')=='block'){
        $(this).parent(".next-in-sub-menu").children("ul").css('display', 'block');
        $(this).find(".right").addClass("fa-caret-up");
        
    }
    else{
        $('.next-in-sub-menu > a').parent(".next-in-sub-menu").children("ul").css('display', 'none');
        $('.next-in-sub-menu > a').removeClass("subMenuActive");
        $('.next-in-sub-menu > a').find(".right").removeClass("fa-caret-up");
        $('.next-in-sub-menu > a').find(".right").addClass("fa-caret-down");

    }
    
    $(this).parent(".next-in-sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActive");
    $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
});




/*$('.next-sub-menu ul').hide();
$(".next-sub-menu a").click(function () {
    $(this).parent(".next-sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActiveNext");   
    $(this).find(".right").toggleClass("fa-caret-down");
});
*/



</script>
