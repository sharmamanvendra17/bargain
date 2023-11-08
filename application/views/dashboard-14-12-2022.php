<?php include 'header.php'; ?>
<section id="middle">
    <header id="page-header">
        <h1>Dashboard</h1>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong>Dashboard</strong>
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
                <?php } ?> 
                <span id="modal_trigger"></span>
                <div class="row">
                    <div class="col-md-12"> 
                        <div id="exTab1" class="container"> 
                            <ul  class="nav nav-pills">
                                <li class="active">
                              <a  href="#1a" data-toggle="tab">15Days</a>
                                </li>
                                <li><a href="#2a" data-toggle="tab">30Days</a>
                                </li>
                                <li><a href="#3a" data-toggle="tab">More Than Month</a>
                                </li> 
                            </ul>

                                <div class="tab-content clearfix">
                                    <div class="tab-pane active" id="1a"> 
                                        <h3>Past 15 days Report</h3>
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
                                        <?php //echo "<pre>"; print_r($fifteendays);
                                        $tot_sum_report = $fifteendays['tot_sum_report'];
                                        $locked = $fifteendays['locked'];
                                        $sum_report = $fifteendays['sum_report'];
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
                                                    <?php echo ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($locked)
                                        {  foreach ($locked as $key => $locked_value) { 
                                            ?>
                                            <div class="row summary_report" >
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Locked</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $locked_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($locked_value['weight']) ? $locked_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($sum_report)
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
                                                        <?php echo $sum_report_value['weight']; ?>
                                                    </div>
                                                </div> 
                                            <?php } 
                                        }  ?>
                                    </div>
                                    <div class="tab-pane" id="2a">
                                        <h3>Past 1 Month Report</h3>
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
                                        <?php //echo "<pre>"; print_r($fifteendays);
                                        $tot_sum_report = $onemonth['tot_sum_report'];
                                        $locked = $onemonth['locked'];
                                        $sum_report = $onemonth['sum_report'];
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
                                                    <?php echo ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0;; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($locked)
                                        {  foreach ($locked as $key => $locked_value) { 
                                            ?>
                                            <div class="row summary_report" >
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Locked</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $locked_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($locked_value['weight']) ? $locked_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($sum_report)
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
                                                        <?php echo $sum_report_value['weight']; ?>
                                                    </div>
                                                </div> 
                                            <?php } 
                                        }  ?>
                                    </div>
                                    <div class="tab-pane" id="3a">
                                        <h3>More than 1 Month Report</h3>
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
                                        <?php //echo "<pre>"; print_r($fifteendays);
                                        $tot_sum_report = $moremonth['tot_sum_report'];
                                        $locked = $moremonth['locked'];
                                        $sum_report = $moremonth['sum_report'];
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
                                                    <?php echo ($tot_sum_report_value['weight']) ? $tot_sum_report_value['weight'] : 0;; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($locked)
                                        {  foreach ($locked as $key => $locked_value) { 
                                            ?>
                                            <div class="row summary_report" >
                                                <div class="col-md-3 summary_report_col">
                                                    <strong>Locked</strong>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo $locked_value['bargain_count']; ?>
                                                </div>
                                                <div class="col-md-3 summary_report_col">
                                                    <?php echo ($locked_value['weight']) ? $locked_value['weight'] : 0; ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        if($sum_report)
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
                                                        <?php echo $sum_report_value['weight']; ?>
                                                    </div>
                                                </div> 
                                            <?php } 
                                        }  ?>
                                    </div> 
                                </div>
                        </div>
                    </div>
                    <?php  if($bookings) { ?>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <h3>SKU Pending bargains</h3>
                            <table class="table table-striped table-bordered table-hover" id="">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Bargain No</th>
                                        <th>Party Name</th>
                                        <th>Place</th>   
                                        <th>Brand</th>   
                                        <th>Product</th>   
                                        <th>Quantity</th>  
                                        <th>Rate (15Ltr Tin)</th>  
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
                                        
                                        foreach ($bookings as $key => $value) { ?>
                                            <tr class="odd gradeX">
                                                <td><?php echo $count; ?></td>
                                                <td><span title="<?php echo $value['admin_name']; ?>">DATA/<?php echo $value['booking_id']; ?></span></td>
                                                <td>
                                                    <?php if($value['status']==0) { ?>
                                                        <a title="Edit Brgain" href="<?php echo base_url('booking/edit').'/'.base64_encode($value['id']);?>" class="">
                                                    <?php  } ?>
                                                    <?php echo $value['party_name']; ?>
                                                    <?php if($value['status']==0) { ?>
                                                        </a>
                                                    <?php }  ?>
                                                </td>
                                                <td><?php echo $value['city_name']; ?></td> 

                                                <td><?php echo $value['brand_name']; ?></td> 
                                                <td><?php echo $value['category_name']; ?></td> 

                                                <td><?php echo $value['quantity']; ?></td>  
                                                <td><?php echo $value['rate']; ?></td> 
                                                 
                                                <td><?php echo date("d-m-Y", strtotime($value['created_at'])); ?></td> 
                                                <?php //if($logged_role != 1) { ?>
                                                <td><?php 
                                                $app_status = 0;
                                                $lock_img = '/un-lock.png';
                                                if($value['is_lock'])
                                                    $lock_img = '/lock.png';
                                                if($value['status']==3)  
                                                { ?>
                                                    <span class="btn btn-danger "  data-status="3" rel="<?php echo base64_encode($value['id']); ?>" style="cursor:none"> <img style="height: 17px;width: 17px;" src="<?php echo base_url('assets/img').$lock_img; ?>"> Rejected</span>
                                                <?php } elseif($value['status']==2) { 
                                                    $app_status = 1; ?>
                                                    <span class="btn btn-default <?php echo ($logged_role == 4) ? 'update_status_reject' : 'style="cursor:none"'; ?>" data-status="3" rel="<?php echo base64_encode($value['id']); ?>" ><img style="height: 17px;width: 17px;" src="<?php echo base_url('assets/img').$lock_img; ?>"> Approved</span>
                                                <?php } else { if($value['is_lock'])  { $app_status = 1; } ?>
                                                <span class="btn btn-danger <?php echo ($logged_role == 4) ? 'update_status_reject' : 'style="cursor:none"'; ?>"  data-status="3" rel="<?php echo base64_encode($value['id']); ?>" > <img style="height: 17px;width: 17px;" src="<?php echo base_url('assets/img').$lock_img; ?>"> Approval Pending</span>
                                                <?php } ?> 
                                                
                                                </td> 
                                                <?php //} ?>
                                                <td> 
                                                    <?php 

                                                    if($value['is_lock'] && $value['is_mail'] ) { ?>
                                                        <!--<a href="<?php echo base_url('booking/downloadreport').'/'.base64_encode($value['booking_id']); ?>" rel="<?php echo $value['booking_id']; ?>" class="btn btn-default btn_report detail">Report</a>-->
                                                        <a href="javascript:void(0)" rel="<?php echo $value['booking_id']; ?>" data-lock="<?php echo $value['is_lock']; ?>" class="btn btn-default detail btn_report1" data-production_unit="<?php echo $value['production_unit']; ?>" data-status="<?php echo $app_status; ?>">Report</a>
                                                    <?php } else { if($logged_role==1) {                        
                                                    
                                                    if(!$value['is_lock']){
                                                    ?>
                                                    
                                                    <a href="<?php echo base_url('booking/sku').'/'.base64_encode($value['booking_id']); ?>" rel="<?php echo $value['booking_id']; ?>"  class="btn btn-default detail">Add SKU</a>
                                                    <?php 
                                                    
                                                    }else { // if mail not sents?>
                                                        <a href="javascript:void(0)" data-lock="<?php echo $value['is_close']; ?>" rel="<?php echo $value['booking_id']; ?>" class="btn btn-default detail btn_report1" data-production_unit="<?php echo $value['production_unit']; ?>" data-status="<?php echo $app_status; ?>" data-party="<?php echo $value['party_id']; ?>">Send Mail</a>
                                                    <?php }
                                                    
                                                    
                                                    
                                                    }  else { ?>
                                                        <a href="javascript:void(0)" data-lock="<?php echo $value['is_close']; ?>" rel="<?php echo $value['booking_id']; ?>"  data-party="<?php echo $value['party_id']; ?>" class="btn btn-default detail btn_report1" data-production_unit="<?php echo $value['production_unit']; ?>" data-status="<?php echo $app_status; ?>" data-party="<?php echo $value['party_id']; ?>">Report</a>
                                                    <?php }  } ?>
                                                </td> 
                                            </tr>
                                    <?php $count++; } } ?>  
                                </tbody>
                            </table> 
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
 
<?php include 'footer.php'; ?>  
<script type="text/javascript">  
    $(window).on("load", function () { 
        var penidng_bargains = '<?php echo count($bookings); ?>';
        if(penidng_bargains>0)
        {
            $('.alert_message').html("<span style='color:red'>You have "+penidng_bargains+" bargains pending to add sku </span>");
            $('#myModal').modal('show');    
        }
    });
</script>
    
 
<style type="text/css">
#exTab2 h3 {
  color : white;
  background-color: #428bca;
  padding : 5px 15px;
}

/* remove border radius for the tab */

#exTab1 .nav-pills > li > a {
  border-radius: 0; 
    border: 1px solid #ccc;
    margin-right: 10px;
}

/* change border radius for the tab , apply corners on top*/

#exTab3 .nav-pills > li > a {
  border-radius: 4px 4px 0 0 ;
}

#exTab3 .tab-content {
  color : white;
  background-color: #428bca;
  padding : 5px 15px;
}
</style>

<script type="text/javascript">
    $(document).ready(function(){ 
        var penidng_bargains = '<?php echo count($bookings); ?>';
        if(penidng_bargains>0)
        {
            $('.alert_message').html("<span style='color:red'>You have "+penidng_bargains+" bargains pending to add sku </span>");
            $('#myModal').modal('show');    
        }
        
    });
</script>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Alert</h4>
            </div>
            <div class="modal-body">
                <span class="alert_message"></span>
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>