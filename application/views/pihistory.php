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
.booking_records tr td { vertical-align:middle !important; }
</style>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1> 
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
             
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
                    <?php 
                    $params_report = "";
                    if($pis) { 
                        $rejected =0;
                        if(isset($_POST['rejected']))
                            $rejected = $_POST['rejected'];
                        $params_report = "?party=$_POST[party]&from=$_POST[booking_date_from]&to=$_POST[booking_date_to]&production_unit=$_POST[production_unit]&pinumber=$_POST[pinumber]&rejected=$rejected";
                    } ?>
                    <div class="col-md-12">  
                        <form action="<?php echo base_url('pihistory'); ?>" class="" method="post" id="addbooking">    
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-md-8">   
                                     
                                </div>
                                <div class="col-md-4">
                                    <span class="title elipsis header_add" >
                                        <div class="form-group cal" style="margin-bottom:0px!important"> 
                                            <input class="form-control" type="text" name="pinumber" id="pinumber" value="<?php echo (isset($_POST['pinumber'])) ? $_POST['pinumber'] : ''; ?>" placeholder="Search By PI Number" />
                                        </div>  
                                    </span> 
                                </div>
                            </div>
                        </div>          
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
                                <div class="col-md-4" style="display:none">
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
                                <div class="col-md-4" style="display:none">
                                    <div class="form-group">
                                        <label for="name">Product</label> 
                                        <select class="form-control" id="category" name="category" >
                                            <option value="">Select Product</option>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">PI Date (From) </label>
                                        <input class="form-control" type="text" id="booking_date_from" name="booking_date_from"  value="<?php if(isset($_POST['booking_date_from'])) { echo $_POST['booking_date_from']; } else { echo date('d-m-Y'); } ?>" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">PI Date (To)</label>
                                        <input class="form-control" type="hidden" id="booking_date_to" name="booking_date_to" value="<?php if(isset($_POST['booking_date_to'])) { echo $_POST['booking_date_to']; } else { echo date('d-m-Y'); } ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4" style="display:none">
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
                                <div class="col-md-6">
                                    <div class="form-group"> 
                                        <label for="rate">Production Unit</label> 
                                        <input class="" type="radio" id="" name="production_unit"  value=""   <?php echo (!isset($_POST['production_unit']) || $_POST['production_unit']=='') ? 'checked' : ''; ?> />All
                                        <input class="" type="radio" id="" name="production_unit"  value="alwar" <?php echo (isset($_POST['production_unit']) && $_POST['production_unit']=='alwar') ? 'checked' : ''; ?> />Alwar
                                        <input class="" type="radio" id="" name="production_unit"  value="jaipur" <?php echo (isset($_POST['production_unit']) && $_POST['production_unit']=='jaipur') ? 'checked' : ''; ?> />Jaipur
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-2">
                                    <label for="rate">Status</label> 
                                    <input class="" type="checkbox" id="" name="rejected"  value="1"   <?php echo (isset($_POST['rejected']) && $_POST['rejected']=!'') ? 'checked' : ''; ?> />Rejected
                                </div>
                            </div>
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search PI</button> 
                                         
                                    </div>                                  
                                </div> 
                                <div class="col-md-4">
                                </div>                
                                <div class="col-md-4">
                                    <?php if($pis) { ?>
                                    <a style="display: inline-block;float: right;" target="_blank" class="rept_btn btn btn-default" href="<?php echo base_url('pihistory/report_pdf').$params_report; ?>"><img src="<?php echo base_url('assets/img/pdf.png'); ?>" height="20" width="20"> </a>
                                    <?php } ?>
                                </div>              
                            </div>
                        </form>
                    </div>
                </div>  
                <div class="table-responsive">
                    <form method="POST" id="pi_form">
                    <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr> 
                                <th>PI Number</th>
                                <th>Party Name</th>
                                <th>Bargain No.</th>  
                                <th>Comapny Name</th>   
                                <th>Total Weight (MT)</th>                                 
                                <th>Amount</th>  
                                <th>PI Date</th>  
                                <th>Status</th>     
                            </tr>
                        </thead>
                        <tbody class="booking_records"> 
                                <?php if($pis) { 
                                    $i=1;
                                    $count = 1;
                                    $cur_page =1;
                                    if(isset($limit))
                                        $con_li = $limit;
                                    if($this->uri->segment(3)!='')
                                        $cur_page = $this->uri->segment(3);
                                    $count = ($cur_page-1)*$con_li+1;
                                    foreach ($pis as $key => $value) { ?>
                                        <tr class="odd gradeX <?php echo ($value['status']==1) ? "danger" : ""; ?>" style="text-align: center; vertical-align: middle;"> 
                                            <td><a  target="_blank" href="<?php echo base_url().'/invoices/pi/'.$value['invoice_file'] ?>"> <?php echo $value['id']; ?></a></td>
                                            <td><?php echo $value['vendors_name']; ?> </td>
                                            <td><?php echo $value['bargain_ids']; ?></td>  
                                            <td><?php echo $value['company_name']; ?></td> 
                                            <td><?php echo round($value['total_weight_pi'],2); ?></td>
                                            <td><?php echo number_format($value['pi_amount'],2); ?></td> 
                                            <td title="<?php echo date("h:i:s", strtotime($value['created_at'])); ?>"><?php echo date("d-m-Y", strtotime($value['created_at'])); ?></td>
                                             
                                            <td>
                                                <?php if($value['dispatch_date']) { ?>
                                                    <img title="<?php echo $value['truck_number']; ?>&#013;<?php echo $value['dispatch_date']; ?>" src="<?php echo base_url(); ?>assets/img/truck.png" style="height: 40px;"> 
                                                <?php } elseif($value['status']==0) { ?>
                                                    <?php $clss= ""; if($logged_role==4 || $logged_role==5)
                                                            $clss= "removepimodal btn";
                                                     ?>
                                                    <span class="<?php echo $clss; ?> btn-warning btn-xs" rel="<?php echo base64_encode($value['id']); ?>" data-id="<?php echo base64_encode($value['booking_id']); ?>">Remove</span>
                                                <?php } else { ?>
                                                    <span class="btn-danger btn-xs" title="<?php echo $value['remark']; ?>" rel="<?php echo base64_encode($value['id']); ?>">Removed</span>
                                                <?php } ?>
                                                <?php echo ($value['pi_deviation']) ? '<span class="deviation" rel="'.$value['id'].'">Deviation</span>' : ""; ?>
                                            </td>
                                        </tr>
                                <?php $count++; } } ?>  
                        </tbody>
                    </table>
                    </form>
                    <table>
                        <tr>
                            <td>
                                <?php echo $links; ?>
                            </td>
                        </tr>
                    </table>
                </div> 
                </div> 
                 
            </div>
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
    $(document).on("click", ".removepimodal", function(event){ 
        var pi_id = $(this).attr('rel');
        var booking_id = $(this).attr('data-id');
        $('.removepi_btn').attr('rel',pi_id);
        $('.removepi_btn').attr('data-id',booking_id);
        $("#myModal").modal("show");
    }); 
    $(document).on("click", ".removepi", function(event){         
        var pi_id = $(this).attr('rel');
        var booking_id = $(this).attr('data-id');
        var unlock =0;
        if($('#unlock_sku').is(":checked"))
            unlock =1;
        var remark = $("#remark").val();
        if(remark.trim()=='')
        {
            $("#v_ramark").html('<span style="color:red">Please Enter Remark</span>');
            return false;
        }
        else
        { 
            $("#v_ramark").html('');
        }
        $(".loader").show();  
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>pihistory/removepi/',
            data: { 'pi_id' :pi_id, 'unlock' :unlock,'remark': remark,'booking_id': booking_id }, 
            dataType: "html",
            success: function(data){  
                $(".loader").hide();  
                if(data)
                {
                    alert("PI Removed Successfully");
                    location.reload();
                }                         
                else
                {
                    alert("Try Again");
                }
            }   
        }); 
    }); 
});
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
        $(document).on("click", ".deviation", function(event){ 
            var pi_id  = $(this).attr('rel'); 
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>performainvoice/deviationlist/',
                data: { 'pi_id' :pi_id }, 
                dataType: "html",
                success: function(data){  
                    $(".loader").hide(); 
                    $('.pi_sku_list').html(data);
                    $('#deviation_modal').modal('show');
                    
                }
            });
        });
    });
</script>
<!-- Trigger the modal with a button -->


<div class="loader"><img src="<?php echo base_url('/assets/img/hug.gif'); ?>"> </div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title reject_status_label">Remove PI</h4>
            </div>
            <div class="modal-body"> 
                <span>
                    <input type="checkbox" class="" id="unlock_sku" name="unlock" value="1" > Unlock
                </span>
                <textarea class="form-control" name="remark" id="remark" placeholder="Enter Remark" required></textarea> 
                <span id="v_ramark"></span>
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default removepi removepi_btn" rel="" data-id="">Remove</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>

<div id="deviation_modal" class="modal fade" role="dialog"> 
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content"> 
         
            <div class="modal-header">
                <h4>PI Deviation<br> <small>If there is a dispatched quantity changed then the quantity mentioned in PI. Please notify below:</small></h4>
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
            </div>
            <div class="modal-body pi_sku_list"> 
            </div>
            <div class="modal-footer">   
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div> 
        
    </div>

  </div>
</div>