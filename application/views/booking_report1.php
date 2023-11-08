<?php include 'header.php'; ?>

<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<style type="text/css">
    .panel-body .table th {
    text-align: center;
    vertical-align: top;
}
.table_report { border: none; }
.table_report td { font-weight: normal; }
</style>
    <section id="middle">
        <header id="page-header">
            <h1><?php echo $title; ?></h1> 
        </header>
        <div id="content" class="padding-20">
            <div id="panel-1" class="panel panel-default">
                <div class="panel-heading">
                    <span class="title elipsis">
                        <strong><?php echo $title; ?></strong>
                    </span>  
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
                    <?php }   ?>
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" class="" method="post">
                            	<div class="row">
                        			<div class="col-md-4">
		                                <div class="form-group">
		                                    <label for="name">Party Name</label> 
		                                    <select class="form-control" id="party" name="party">
		                                        <option value="">Select Party</option>
		                                        <?php if($users) {  
		                                            foreach ($users as $key => $value) { ?>
		                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
		                                        <?php } } ?>
		                                    </select>
		                                    <span class="txt-danger"><?php echo form_error('party'); ?></span>
		                                </div>
		                            </div>
		                            <div class="col-md-4">
		                                <div class="form-group">
		                                    <label for="name">Brand </label> 
		                                    <select class="form-control" id="brand" name="brand">
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
		                                    <select class="form-control" id="category" name="category">
		                                        <option value="">Select Product</option> 
		                                    </select>
		                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
		                                </div>
		                            </div>
		                        </div>
                                <div class="row">
                        			<div class="col-md-4">
		                                <div class="form-group">
		                                    <label for="name">Packed In</label> 
		                                    <select class="form-control" id="product" name="product">
		                                        <option value="">Select Packed In</option>
		                                        
		                                    </select>
		                                    <span class="txt-danger"><?php echo form_error('product'); ?></span>
		                                </div>
		                            </div> 
		                            <div class="col-md-4">
		                                <div class="form-group"> 
				                        	<label for="rate">Booking Date (From) <?php echo date('Y-m-d');  ?></label>
					                        <div class="controls input-append date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
							                    <input size="16" type="text" value="<?php echo date('Y-m-d'); ?>" class="form-control calc_b" readonly>
							                    <span class="add-on"><i class="icon-remove"></i></span>
							                    <span class="add-on"><i class="icon-th"></i></span>
							                </div>
							                <input type="hidden" id="dtp_input2" name="booking_date_from"  value="<?php echo date('Y-m-d'); ?>" /><br/>
					                    </div>
		                            </div>
		                            <div class="col-md-4">
		                                <div class="form-group"> 
				                        	<label for="rate">Booking Date (To)</label>
					                        <div class="controls input-append date form_date1" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input3" data-link-format="yyyy-mm-dd">
							                    <input size="16" type="text" value="<?php echo date('Y-m-d'); ?>" class="form-control calc_b" readonly>
							                    <span class="add-on"><i class="icon-remove"></i></span>
							                    <span class="add-on"><i class="icon-th"></i></span>
							                </div>
							                <input type="hidden" id="dtp_input3" name="booking_date_to" value="<?php echo date('Y-m-d'); ?>"/><br/>
					                    </div>
		                            </div> 
		                        </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?php $statuses = array('1' => 'Checked','2' => 'Approved','3' => 'Rejected')  ?>
                                        <label for="name">Status </label> 
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Select Status</option>
                                            <?php
                                            foreach ($statuses as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>" <?php echo ($booking_status==$key) ? 'selected' : ''; ?>><?php echo $value; ?></option> 
                                        <?php } ?> 
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('status'); ?></span>
                                    </div>
                                </div> 
		                         <div class="row">
                        			<div class="col-md-4 col-md-offset-4">
                                		<button type="submit" class="btn btn-default">Get Report</button> 
                                        <?php  if($bookings) { ?><a id="print" class="btn btn-default" href="javascript:void(0)">Print </a><?php } ?>
                                	</div> 
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center"><b>Results From <?php echo $booking_date_from; ?> to <?php echo $booking_date_to; ?></b></div>
                    </div>
                    <div id="printMe">
                        <div id="total_weight"></div> <br>
                        <div class="table-responsive">
                            <?php $excel_report = array();  if($bookings) {  if($categories) {  
                                ?>
                            <table class="table table-striped table-bordered table-hover" id="">
                                <thead>                        
                                    <tr>
                                        <?php 
                                        $weight_ton_x=0; 
                                        $Packaging_cost = 0;
                                        $Average_price =0;

                                          $total_weight_ordered = 0;  foreach ($distinct_categories as $key => $category) { 
                                            $is_vanaspati = 0;
                                            $category_weight = 0;
                                            $category_loose_rate = 0;
                                            $category_price = 0;
                                            
                                            $bulk_category_weight = 0;
                                            $bulk_category_loose_rate = 0;
                                            $bulk_category_price = 0;


                                            $consumer_category_weight = 0;
                                            $consumer_category_loose_rate = 0;
                                            $consumer_category_price = 0; 
                                            $bulkprice_m = 0;
                                            $consumerprice_m = 0;
                                            ?>
                                            <th style="vertical-align: top; text-align: center;"><?php echo $category['alias_name']; ?>
                                                <table class="table table-striped table-bordered table-hover table_report" id="" style="background-color:transparent; border:none;">
                                                    <tr>
                                                        <?php
                                                        $b =0;
                                                        $barnd_ids = $category['brand_id'];

                                                         


                                                        $category_ids = $category['category_ids'];
                                                        //$products =  getbrandisbycategoryname($category['category_name'])
                                                        //$products =  getproductsbycategory($category['id']);
                                                        $brands = explode(',',$barnd_ids); 
                                                        $products =  getproductsbybrandid($barnd_ids,$category_ids);
                                                        if($products) {
                                                            //echo "<pre>"; print_r($products);
                                                            foreach ($products as $key => $value) { 
                                                                $brand_total = 0; 
                                                                $loose_rate = 0; 
                                                                $total_price = 0;


                                                                $bulk_brand_total = 0; 
                                                                $bulk_total_price = 0;
                                                                $bulk_loose_rate = 0;



                                                                $consumer_brand_total = 0;
                                                                $consumer_loose_rate = 0;
                                                                $consumer_total_price = 0;
                                                                ?>
                                                            
                                                                <?php //echo $value['name']; ?>
                                                                
                                                                    
                                                                        <?php 
                                                                        if($brands) {

                                                                            foreach ($brands as $key => $brand_v) { 
                                                                                $brand = getbrad_info($brand_v);
                                                                                ?>
                                                                            
                                                                                <?php //echo $brand['name']; ?>
                                                                                <?php
                                                                                //$weight =  getweightbyproductid($value['id'],$brand['id']); 
                                                                                $weight =  getweightbyproductid($value['id'],$brand['id'],$booking_date_from,$booking_date_to,$party_id,$value['category_id'],$booking_status); 
                                                                                //echo "<pre>"; print_r($weight);
                                                                                if($weight['total_weight']>0) 
                                                                                { ?>
                                                                                        <?php  
                                                                                        if(strtolower($category['category_name'])=="vanaspati")
                                                                                        {
                                                                                            $product_type = $weight['product_type']; 
                                                                                            $is_vanaspati = 1;
                                                                                            if($product_type==0)
                                                                                            {
                                                                                                $weight_brand =  $weight['total_weight'];
                                                                                                $brand_total = $brand_total+$weight_brand;
                                                                                                $loose_rate = $loose_rate+ $weight['total_loose_rate'];
                                                                                                $total_price = $total_price+ $weight['total_price']; 


                                                                                                $bulk_weight_brand = $weight['total_weight'];
                                                                                                $bulk_brand_total = $bulk_brand_total+$weight_brand;
                                                                                                $bulk_loose_rate = $bulk_loose_rate+ $weight['total_loose_rate'];
                                                                                                $bulk_total_price = $bulk_total_price+ $weight['total_price'];
                                                                                                
                                                                                            }

                                                                                            else
                                                                                            {
                                                                                                $weight_brand =  $weight['total_weight'];
                                                                                                $brand_total = $brand_total+$weight_brand;
                                                                                                $loose_rate = $loose_rate+ $weight['total_loose_rate'];
                                                                                                $total_price = $total_price+ $weight['total_price'];

                                                                                                $consumer_weight_brand = $weight['total_weight'];
                                                                                                $consumer_brand_total = $consumer_brand_total+$weight_brand;
                                                                                                $consumer_loose_rate = $consumer_loose_rate+ $weight['total_loose_rate'];
                                                                                                $consumer_total_price = $consumer_total_price+ $weight['total_price']; 
                                                                                            }
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $weight_brand =  $weight['total_weight'];
                                                                                            $brand_total = $brand_total+$weight_brand;
                                                                                            $loose_rate = $loose_rate+ $weight['total_loose_rate'];
                                                                                            $total_price = $total_price+ $weight['total_price'];
                                                                                        } 
                                                                                } ?>
                                                                            
                                                                        <?php } } ?>
                                                                    
                                                                
                                                            
                                                        <?php 
                                                        $category_weight = $category_weight+$brand_total;
                                                        $category_loose_rate = $category_loose_rate+$loose_rate;
                                                        $category_price = $category_price+$total_price;

                                                        $bulk_category_weight = $bulk_category_weight+$bulk_brand_total;
                                                        $bulk_category_loose_rate = $bulk_category_loose_rate+$bulk_loose_rate;
                                                        $bulk_category_price = $bulk_category_price+$bulk_total_price;

                                                        $consumer_category_weight = $consumer_category_weight+$consumer_brand_total;
                                                        $consumer_category_loose_rate = $consumer_category_loose_rate+$consumer_loose_rate;
                                                        $consumer_category_price = $consumer_category_price+$consumer_total_price;


                                                        } } ?>
                                                    
                                                    <?php if($category_weight>0) { ?>
                                                    <!--<tr><td>Total Weight (Kg) : <?php echo $category_weight;?> Kg</td></tr> -->
                                                    <tr><td>

                                                    <?php if($is_vanaspati==1) { ?>
                                                    <table  class="table table-striped table-bordered table-hover table_report" id="" style="background-color:transparent; border:none;">
                                                        <tr><th style="border-top :0;border-bottom :0;border-left :0">Bulk</th><th>Consumer</th></tr>
                                                        <tr><td style="border-top :0;border-bottom :0;border-left :0">
                                                        <?php if($bulk_category_weight>0) { $bulk_1 =  ($bulk_category_weight/1000); echo round($bulk_1,3);  } ?></td><td><?php if($consumer_category_weight>0) { $consum_1 =  ($consumer_category_weight/1000);  echo round($consum_1,3); }  ?></td></tr>
                                                        <tr>
                                                            <td style="border-top :0;border-bottom :0;border-left :0">
                                                                <?php 
                                                                    if($bulk_category_weight>0) { 
                                                                        //echo "Total Loose Rate : ".$bulk_category_loose_rate."<br>";
                                                                        $bulk_Packaging_cost = round(($bulk_category_loose_rate/$bulk_category_weight),2);
                                                                        //echo "Average Loose Rate packing cost : ".$bulk_Packaging_cost." per kg <br>";
                                                                        $Average_price_bulk = round(($bulk_category_price/$bulk_category_weight),2);
                                                                        //echo "Average Price : ".$Average_price_bulk." per kg <br>";
                                                                        //echo "Actual Total Rate : ".($bulk_category_price-$bulk_category_loose_rate)." per kg <br>";
                                                                        $bulkprice_m = ((float)$Average_price_bulk-(float)$bulk_Packaging_cost);
                                                                        //echo "Loose Rate per kg : ".($Average_price_bulk-$bulk_Packaging_cost)." per kg <br>";
                                                                        echo "Loose Rate per kg : ".round($bulkprice_m,3)." per kg <br>";
                                                                        
                                                                    } ?>
                                                            </td>
                                                            <td style="border-top :0;border-bottom :0;border-left :0">
                                                                <?php 
                                                                    if($consumer_category_weight>0) { 
                                                                        //echo "Total Loose Rate : ".$consumer_category_loose_rate."<br>";
                                                                        $consumer_Packaging_cost = round(($consumer_category_loose_rate/$consumer_category_weight),2);
                                                                        //echo "Average Loose Rate packing cost : ".$consumer_Packaging_cost." per kg <br>";
                                                                        $Average_price_consumer = round(($consumer_category_price/$consumer_category_weight),2);
                                                                       // echo "Average Price : ".$Average_price_consumer." per kg <br>";
                                                                        //echo "Actual Total Rate : ".($consumer_category_price-$consumer_category_loose_rate)." per kg <br>";
                                                                         $consumerprice_m = $Average_price_consumer-$consumer_Packaging_cost;
                                                                        //echo "Loose Rate per kg : ".($Average_price_consumer-$consumer_Packaging_cost)." per kg <br>";
                                                                        echo "Loose Rate per kg : ".round($consumerprice_m,3)." per kg <br>";
                                                                    } ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <?php  
                                                     } ?>
                                                    Total Weight (Ton) :
                                                    <?php if($category_weight>0) 
                                                    {
                                                        $weight_ton_x = ($category_weight/1000);
                                                        echo round($weight_ton_x,3);
                                                    }
                                                        

                                                        $total_weight_ordered = $total_weight_ordered+$weight_ton_x;

                                                        ?> Ton

                                                    </td></tr>
                                                    <!--<tr><td>Total Loose Rate : <?php echo $category_loose_rate;?> </td></tr>
                                                    <tr><td>Average Loose Rate(Packaging Cost) : <?php if($category_weight>0)  echo $Packaging_cost =  round(($category_loose_rate/$category_weight),2);?> per Kg</td></tr>
                                                    <tr><td>Average Price : <?php if($category_weight>0) echo $Average_price = round(($category_price/$category_weight),2);?> per Kg</td></tr>
                                                    <tr><td> Actual Total Rate : <?php if($category_weight>0) echo $category_price-$category_loose_rate;?></td></tr> -->
                                                    <tr><td style="border-top :0;border-bottom :0;border-left :0"> Loose Rate per kg : 
                                                    <?php 
                                                        if($category_weight>0) 
                                                        {
                                                            $cost_1 =  $Average_price-$Packaging_cost;
                                                            echo round($cost_1,3);
                                                        } ?>

                                                    </td></tr>
                                                    <?php 
                                                 
                                                    }
                                                    if($category['alias_name']=='Vanaspati')
                                                    {
                                                        $excel_report['header']['Vanaspati_Bulk']['weight'] =  $bulk_category_weight/1000;
                                                        $excel_report['header']['Vanaspati_Bulk']['rate'] =  $bulkprice_m;

                                                        $excel_report['header']['Vanaspati_Consumer']['weight'] = $consumer_category_weight/1000;
                                                        $excel_report['header']['Vanaspati_Consumer']['rate'] =  $consumerprice_m;

                                                        $excel_report['header']['vanaspati_average']['weight'] = $weight_ton_x;
                                                        $excel_report['header']['vanaspati_average']['rate'] = (float)$Average_price-(float)$Packaging_cost;
                                                    }
                                                    else
                                                    {
                                                        $excel_report['header'][$category['alias_name']]['weight'] = $weight_ton_x;
                                                        $excel_report['header'][$category['alias_name']]['rate'] = (float)$Average_price-(float)$Packaging_cost;
                                                    }
                                                    $weight_ton_x = '';
                                                     $Average_price = '';
                                                     $Packaging_cost ='';
                                                     $excel_report['total_weight'] = $total_weight_ordered;
                                                    ?>
                                                </table>
                                            </th> 
                                        <?php } ?>
                                    </tr>                       
                                </thead>
                            </table>  
                            <?php } }  //echo "<pre>"; print_r($excel_report); 
                              $this->session->set_userdata('excel_report', $excel_report);    ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="datatable_sample">
    	                    <thead>
    	                        <tr>
    	                            <th>S.No</th>
    	                            <th>Bargain No</th>
    	                            <th>Party Name</th>
    	                            <th>Place</th>                            
    	                            <th>Brand name</th>
    	                            <th>Category Name</th> 
    	                            <th>Product Name</th> 
    	                            <th>Quantity</th> 
                                    <th>Rate(Without insurance)</th>
                                    <th>Rate(With insurance)</th>
                                    <th>Rate(FOR)</th>
                                    <th>Booked By</th>
    	                            <th>Date/Time</th> 
                                    <th>Status</th>
                                    <th>Remark</th> 
    	                            <th>Action</th> 
    	                        </tr>
    	                    </thead>
    	                    <tbody>
    	                        <?php if($bookings) { 
    	                            $i=1;
    	                            foreach ($bookings as $key => $value) { ?>
    	                                <tr class="odd gradeX">
    	                                    <td><?php echo $i; ?></td>
    	                                    <td>SHAIL/<?php echo $value['booking_id']; ?></td>
    	                                    <td><?php echo $value['party_name']; ?></td>
    	                                    <td><?php echo $value['city_name']; ?></td>
    	                                    <td><?php echo $value['brand_name']; ?></td>
    	                                    <td><?php echo $value['category_name']; ?></td>
    	                                    <td><?php echo $value['product_name']; ?></td>
    	                                    <td><?php echo $value['quantity']; ?></td>
                                            <td><?php echo $price = $value['rate']; ?></td>
                                            <td>
                                                <?php if($value['insurance']!='0.00') { 
                                                     $price = (($value['rate']*$value['insurance'])/100)+$value['rate']; 
                                                            $price1 =  round($price,2); 
                                                            //echo $dec = ltrim(($price - floor($price)),"0.");  echo "<br>";
                                                            //echo $dec =round(($price - floor($price)),2);  echo "<br>";
                                                            $float_number_array = explode('.', $price1);
                                                            if(count($float_number_array)>1)
                                                            {
                                                                $float_number = $float_number_array[1];
                                                                if(strlen($float_number)>1)
                                                                {
                                                                     $first_float =  substr($float_number, 0, 1); 
                                                                    //echo  substr($float_number, 0, 1); echo "<br>";
                                                                    $last_float =  substr($float_number, -1); 
                                                                    if($last_float>=3 && $last_float<=7)
                                                                    {
                                                                        $new_float = $first_float.'5';
                                                                    }
                                                                    if($last_float>0 && $last_float<3)
                                                                    {
                                                                        $new_float = $first_float.'0';
                                                                    }
                                                                    if($last_float>7 && $last_float<=9)
                                                                    {
                                                                        //echo $first_float.'--'.$last_float; echo "<br>";
                                                                        $new_float = ((int)$first_float+1).'0';
                                                                    }
                                                                    if($float_number=='98' || $float_number=='99' )
                                                                    {
                                                                        echo ($float_number_array[0]+1).'.00';
                                                                    }
                                                                    else
                                                                    {
                                                                        echo $float_number_array[0].'.'.$new_float;
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    echo $price1.'0'; 
                                                                }
                                                            } else
                                                            {
                                                                 echo $price1.'.00'; 
                                                            }

                                                    } ?>
                                            </td>
                                            <td><?php  if($value['for_total']!='0.00') { echo $value['for_total']; } else { echo "0.00"; } ?></td>
                                            <td><?php echo $value['admin_name']; ?></td>
    	                                    <td><?php echo $value['created_at']; ?></td>
                                            <td><?php                                                
                                                    
                                                if($value['status']==0 && $logged_role == 1)
                                                    echo '<span class="btn btn-danger">Check Pending</span>'; 
                                                elseif($value['status']==0 && $logged_role == 2 )
                                                    echo '<span class="btn btn-danger update_status" rel="'.base64_encode($value['id']).'" data-status="1" ><a href="javascript:void(0)" >Check Pending</a></span>'; 
                                                elseif($value['status']==1  && $logged_role == 2)
                                                    echo '<span class="btn btn-warning" style="cursor:auto" rel="'.base64_encode($value['id']).'" >Checked</span>';
                                                elseif($value['status']==1  && $logged_role == 3)
                                                    echo '<div class="approval_status_section '.$value['id'].'"><span class="btn btn-success update_status_reject" rel="'.base64_encode($value['id']).'" data-status="2"><a href="javascript:void(0)" >Approve</a></span> <span class="btn btn-danger update_status_reject" rel="'.base64_encode($value['id']).'" data-status="3"><a href="javascript:void(0)" >Reject</a></span></div>';
                                                elseif($value['status']==1  && $logged_role == 1)//maker and checked
                                                    echo '<span class="btn btn-warning" style=cursor:auto" rel="'.base64_encode($value['id']).'" >Checked</span>';
                                                elseif($value['status']==2)
                                                    echo '<span class="btn btn-success show_details" rel="'.base64_encode($value['id']).'" >Approved</span>'; 
                                                elseif($value['status']==3)
                                                    echo '<span class="btn btn-danger show_details" rel="'.base64_encode($value['id']).'" >Rejected</span>'; 
                                                ?>
                                            </td>
                                            <td class="remark<?php echo $value['id']; ?>"><?php echo $value['remark']; ?></td>
                                            <td>
                                                <?php if($logged_in_id==$value['admin_id'] && ( ($value['status']==0 && $logged_role == 1) || ($value['status']==1 && $logged_role == 2) || ($value['status']==2 && $logged_role == 3) ) ) { ?>
                                                <a href="<?php echo base_url(); ?>booking/edit/<?php echo base64_encode($value['id']); ?>">Edit</a> &nbsp; <a href="<?php echo base_url(); ?>booking/delete/<?php echo base64_encode($value['id']); ?>"  onClick="if(confirm('Are you want to sure delete this order?')) return true; else return false">Delete</a>
                                                <?php } ?>
                                            </td>
    	                                </tr>
    	                        <?php $i++; } } ?> 
    	                    </tbody>
    	                </table>
                    </div>
	                
                </div>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>
<script>
$(document).ready(function(){
    $("#brand").change(function(){
        var brand_id = $(this).val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getcategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
				//alert(msg);
                $("#category").html(msg);
            }
        });
    });
    $("#category").change(function(){
        var category_id = $(this).val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getproduct',
            data: { 'category_id': category_id},
            success: function(msg){
				//alert(msg);
                $("#product").html(msg);
            }
        });
    });
});
</script>
 

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script type="text/javascript">
    $('.form_datetime').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    }); 
    $('.form_date').datetimepicker({ 
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });
    $('.form_date1').datetimepicker({ 
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });
</script>
<script type="text/javascript">
    var total_weight_ordered = '<?php echo round($total_weight_ordered,3);?>'
    $('#total_weight').html("<b>Total Ordered Weight : "+total_weight_ordered+" Ton </b><br>");
</script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
    $(document).ready(function(){
        $("#party").select2(); 
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


<script>
        function printDiv(divName){
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
 


 <script>
$(document).ready(function(){
    $("#print").click(function(){  
        $("#divLoading").css({display: "block"});
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>booking/booking_report',
            success: function(msg){  
                $("#divLoading").css({display: "none"});
                window.open(msg,'_blank' );
            }
        });
    });
});
    $(document).ready(function(){

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

        $(".update_status_reject").click(function(){
            $("#remark").val('');
            $("#v_ramark").html('');
            if (!confirm("Are you sure ?")){
              return false;
            }
            $("#divLoading").css({display: "block"});
            var booking_id = $(this).attr('rel');  
            var data_status = $(this).attr('data-status');  
            if(data_status==3)
                var btn = '<span class="btn btn-danger update_status" rel="'+booking_id+'" data-status="3"><a href="javascript:void(0)" >Reject</a></span>';
            else
                var btn = '<span class="btn btn-success update_status" rel="'+booking_id+'" data-status="2"><a href="javascript:void(0)" >Approve</a></span>';
            $('.submit_reject').html(btn);
            $('#myModal').modal('show');
            $("#divLoading").css({display: "none"});
        }); 
        $(document).on('click', '.update_status', function(){
            var status = $(this).attr('data-status'); 
            var remark = $("#remark").val();
            if(status==3 || status==2)
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
                  return false;
                }
            }
            var booking_id = $(this).attr('rel');  
            var deccoded_booking_id = atob(booking_id);
            var ele =  $(this);
            $("#divLoading").css({display: "block"});
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>booking/updatestatus',
                data: { 'booking_id': booking_id,'status': status,'remark': remark},
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
                    }
                }
            });
        });
    });
</script>


<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8; display: none;">
<p style="position: absolute; color: White; top: 50%; left: 45%;">
Loading, please wait...
<img src="<?php echo base_url();?>assets/images/loaders/4.gif">
</p>
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Remark</h4>
            </div>
            <div class="modal-body">
                <textarea class="form-control" name="remark" id="remark"></textarea>
                <span id="v_ramark"></span>
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
                <h4 class="modal-title">Update Time</h4>
            </div>
            <div class="modal-body">
                <span id="UpdateTime"></span>
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>