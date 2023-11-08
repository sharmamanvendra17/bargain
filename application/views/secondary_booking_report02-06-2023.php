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
            <form action="<?php echo base_url('secondarybooking/report/'); ?>" class="" method="post" id="addbooking">
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
                                        <label for="supply_from">Supply From</label> 
                                        <select class="form-control" id="supply_from" name="supply_from" >
                                            <option value="">Select Supply From</option>
                                            <?php if($super_disributers) { 
                                                foreach ($super_disributers as $key => $super_disributer) { ?>
                                            <option value="<?php echo $super_disributer['id']; ?>" <?php if(isset($_POST['supply_from'])) { if($_POST['supply_from']==$super_disributer['id']) { echo "selected"; } }; ?>><?php echo $super_disributer['name'].' - '.$super_disributer['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('supply_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="supply_to">Supply To </label> 
                                        <select class="form-control" id="supply_to" name="supply_to" >
                                            <option value="">Select Supply To</option>
                                            <?php if($disributers) { 
                                                foreach ($disributers as $key => $disributer) { ?>
                                            <option value="<?php echo $disributer['id']; ?>" <?php if(isset($_POST['supply_to'])) { if($_POST['supply_to']==$disributer['id']) { echo "selected"; } }; ?>><?php echo $disributer['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('supply_to'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <?php if($logged_role!=6) { ?>
                                        <label for="rate">Employee (Secondary Makers)</label>
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
                            </div>   
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search Bargain</button>  
                                    </div>                                  
                                </div> 
                                <div class="col-md-4">
                                </div>                             
                            </div>
                        
                    </div>
                </div> 
                
               
                    <?php $total_weight = 0; ?> 
                <div class="table-responsive">
                     <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Bargain No</th>
                                <th>Supply From</th>
                                <th>Supply To</th>
                                <th>Place</th>    
                                <th>Weight</th>   
                                <th>Date</th>  
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
                                        <td>
                                            <?php if($value['pi_id']==0) { ?>
                                                <a title="Edit Brgain" href="<?php echo base_url('secondarybooking/edit').'/'.base64_encode($value['id']);?>" class="">
                                                    <span title="<?php echo $value['admin_name']; ?>">DATA/SEC/<?php echo $value['booking_id']; ?></span></a>
                                            <?php  } else { ?> 
                                            <a target="_blank" href="<?php echo base_url('invoices/secondary').'/'.$value['invoice_file'];?>"  title="<?php echo $value['admin_name']; ?>">DATA/SEC/<?php echo $value['booking_id']; ?> 
                                                </a>
                                            <?php }  ?>
                                        </td>
                                        <td>
                                            <?php echo $value['supply_from']; ?>
                                        </td>
                                        <td>
                                           <?php echo $value['party_name']; ?>
                                        </td>
                                        <td><?php echo $value['city_name']; ?></td> 
                                        <td><?php echo $value['weight']; ?></td> 
                                        <td><?php echo date("d-m-Y", strtotime($value['created_at'])); ?></td>  
                                        <?php //} ?>
                                        <td>
                                            <a href="javascript:void(0)" data-lock="0" rel="<?php echo $value['id']; ?>" data-party="69" class="btn btn-default detail btn_report1" data-production_unit="alwar" data-status="0" data-view_approval_status="0"><?php echo ($value['status']) ? '<span class="approved"><img src="'.base_url().'assets/img/check.svg" style="height:14px;"></span>' : ''; ?>

                                            <?php echo ($value['pi_id']==0)  ? "Send Order" : "PI"; ?> </a>

                                        </td>
                                    </tr>
                            <?php $count++; } } ?> 
                            <tr>
                                <td colspan="9">
                                    <?php echo $links; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
<!--                    <table>
                        <tr>
                            <td>
                                <?php //echo $links; ?>
                            </td>
                        </tr>
                    </table>
                </div>  
 -->               </div>
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


<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>

<script type="text/javascript">
$("#booking_date_from,#booking_date_to").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#supply_from").select2(); 
        $("#supply_to").select2(); 
    });
    $(document).on('change', '#supply_from', function(){  
        var vendor_id = $(this).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/GetDistributors',
            data: { 'vendor_id': vendor_id},
            success: function(msg){
                $("#supply_to").html(msg); 
            }
        }); 
    }); 
    $(document).on('click', '.send_mail_vendor', function(){
        var booking_id = $(this).attr('rel');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/GetBookingInfoDetailsPdf',
            data: { 'booking_id': booking_id},
            success: function(msg){ 
                alert("Mail Sent successfully");
                location.reload();
            }
        });
    });
    $(document).on('click', '.detail', function(){
        var booking_id = $(this).attr('rel');
        var data_lock = $(this).attr('data-lock');
        var data_status = $(this).attr('data-status'); 
        var user_role = "<?php echo $logged_role; ?>";
        var data_party = $(this).attr('data-party');
        //$('.send_mail_plant_status').attr('rel',data_party);
        //$('.send_mail_plant_status').attr('data-production_unit',data_production_unit);
        //alert(booking_id);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/GetBookingInfoDetails',
            data: { 'booking_id': booking_id},
            success: function(msg){ 
                $("#divLoading").css({display: "none"}); 
                //$('.invoice_generate').attr('rel',booking_id);
                var href= "<?php echo base_url('booking/downloadreport').'/'; ?>"+btoa(booking_id);
                $('.btn_report').attr('href',href);
                $(".BookingInfoDetails").html(msg);

                if(user_role==2 || user_role==3)
                { 
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
                }
                if(data_lock==0)
                {
                    //$('.mail_btn').hide();
                }
                $('#DetailModal').modal('show'); 
            }
        });
    }); 
</script>

<div id="DetailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="BookingInfoDetails">
                <div class="modal-body"> 
                </div>
                <div class="modal-footer">
                    <span class="mail_btn btn btn-default" rel="">Send</span>
                    <?php if($logged_role == 3 || $logged_role == 2) { ?>
                     
                    <span class="approve_btn btn btn-default" rel="">Approve</span>
                    
                    <?php } ?>
                    <?php if($logged_role == 1) { ?>
                      
                    <span class="send_mail_plant_status btn btn-default" rel="" data-production_unit="">Send Mail to all locked bargains</span>
                    <?php } ?> 
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>

  </div>
</div>