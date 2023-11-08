<?php include APPPATH.'views/header.php'; ?>
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
            <form action="<?php echo base_url('purchase/purchase/inventoryreport/'); ?>" class="" method="post" id="addbooking">
            <div class="panel-heading">
                <div class="row" style="display:none">

                        <div class="col-md-8">   
                            <span class="title elipsis">
                                <strong><?php echo $title; ?></strong> <!-- panel title -->
                            </span> 
                        </div>
                        <div class="col-md-4">
                            <span class="title elipsis header_add" >
                                <div class="form-group cal" style="margin-bottom:0px!important"> 
                                    <input class="form-control" type="text" name="bagainnumber" id="bagainnumber" value="<?php echo (isset($_POST['bagainnumber'])) ? $_POST['bagainnumber'] : ''; ?>" placeholder="Search By Purchase Number" />
                                </div>
                                <button type="submit" name="bargain_search" class="btn btn-default booking_submit " value="bargain_search">Go</button>  
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
                                        <label for="name">Category</label> 
                                        <select class="form-control" id="category" name="category" >
                                            <option value="">Select Category</option>
                                            <?php if($categories) { 
                                                foreach ($categories as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['category'])) { if($_POST['category']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['category_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product</label> 
                                        <select class="form-control" id="product" name="product" >
                                            <option value="">Select Product</option>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('product'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="booking_date_from">Inventory Date (From) </label>
                                        <input class="form-control" type="text" id="booking_date_from" name="booking_date_from"  value="<?php if(isset($_POST['booking_date_from']) && !empty($_POST['booking_date_from']) ) { echo $_POST['booking_date_from']; } else { echo date('d-m-Y'); } ?>" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="booking_date_to">Inventory Date (To)</label>
                                        <input class="form-control" type="hidden" id="booking_date_to" name="booking_date_to" value="<?php if(isset($_POST['booking_date_to']) && !empty($_POST['booking_date_to']) ) { echo $_POST['booking_date_to']; } else { echo date('d-m-Y'); } ?>"/>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">ERP Sr. No.</label> 
                                        <input type="text" name="erp_sr_no" class="form-control" value="<?php if(isset($_POST['erp_sr_no']) && !empty($_POST['erp_sr_no']) ) { echo $_POST['erp_sr_no']; } ?>">
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Code</label> 
                                        <input type="text" name="code" class="form-control" value="<?php if(isset($_POST['code']) && !empty($_POST['code']) ) { echo $_POST['code']; } ?>">
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="employee">Employee (Makers)</label>
                                        <?php if($logged_role==4 || $logged_role==2|| $logged_role==5) { ?>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="broker">Broker</label> 
                                        <?php if($logged_role==4 || $logged_role==2|| $logged_role==5) { ?>
                                        <select class="form-control" id="broker" name="broker" >
                                            <option value="">Select Broker</option>
                                            <?php if($brokers) { 
                                                foreach ($brokers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"  <?php if(isset($_POST['broker'])) { if($_POST['broker']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <?php } else { ?>
                                            <select class="form-control" id="broker" name="broker" style="display:none;">
                                                <option value="">Select Broker</option>
                                            </select>
                                        <?php }  ?> 
                                    </div>
                                </div>
                            </div>                              
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search Inventory</button>  
                                    </div>                                  
                                </div> 
                                <div class="col-md-4">
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
                                <th>Purchase ID</th>
                                <th>ERP Sr. No.</th>
                                <th>Code</th>
                                <th>Gr/LR No</th>
                                <th>Gr/LR Date</th>
                                <th>Vendor Invoice No.</th>
                                <th>Vendor Invoice Date</th>   
                                <th>Vehicle Number</th>  
                                <th>Weight (MT)</th> 
                                <th>Box/Bag</th>  
                                <th>Supplier</th>   
                                <th>Added By</th>   
                                <th>Created at</th>   
                            </tr>
                        </thead>
                        <tbody class="booking_records">
                            <?php $total_weight = 0; if($inventories) { 
                                $i=1;
                                $count = 1;
                                $cur_page =1;
                                if(isset($limit))
                                    $con_li = $limit;
                                if($this->uri->segment(4)!='')
                                    $cur_page = $this->uri->segment(4);
                                $count = ($cur_page-1)*$con_li+1;
                                foreach ($inventories as $key => $value) { ?>
                                    <tr class="odd gradeX">
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo 'DATA/'.$value['purchase_id']; ?></td> 
                                        <td><?php echo $value['erp_sr_no']; ?></td> 
                                        <td><?php echo $value['code']; ?></td> 
                                        <td><span title="<?php echo $value['added_by']; ?>"><?php echo $value['gr_lr_no']; ?></span></td> 
                                        <td><?php echo date('d-m-Y',strtotime($value['gr_lr_date'])); ?></td> 

                                        <td><?php echo $value['vendor_invoice_number']; ?></td> 
                                        <td><?php echo date('d-m-Y',strtotime($value['vendor_invoice_date'])); ?></td> 

                                        <td><?php echo $value['vehicle_number']; ?></td>  
                                        <td><?php echo $value['bill_weight']; ?></td> 
                                        <td><?php echo $value['unit_numbers']; ?></td>
                                        <td><?php echo $value['supplier_name']; ?></td> 
                                        <td><?php echo $value['added_by']; ?></td> 
                                        <td><?php echo date('d-m-Y', strtotime($value['created_at'])); ?></td> 
                                        <?php //} ?> 
                                    </tr>
                            <?php $count++; } } ?> 
                            <tr>
                                <td colspan="12">
                                    <?php echo $links; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>  
                    </div> 
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

<?php include APPPATH.'views/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#booking_date_from,#booking_date_to").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
    var category_id = $("#category").val();  
    var product_id = '<?php echo $product_id; ?>';  
    if(category_id.trim()!='')
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>purchase/purchaseproduct/getproductlist',
            data: { 'category_id': category_id},
            success: function(msg){ 
                $("#product").html(msg);
                $("#product").val(product_id);
            }
        });
    }
    $("#category").change(function(){
        $('.custom_attributes').hide();
        $('.custom_attributes_input').val();
        var category_id = $(this).val();   
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>purchase/purchaseproduct/getproductlist',
            data: { 'category_id': category_id},
            success: function(msg){ 
                $("#product").html(msg);
                $("#product").val(product_id);
            }
        });
    }); 
    $(document).on('click', '.update_status_reject', function(){
        $("#remark").val('');
        $("#v_ramark").html('');
        if (!confirm("Are you sure ?")){
          return false;
        }
        $("#divLoading").css({display: "block"});
        var booking_id = $(this).attr('rel');   
        $("#update_status_bookig_id").val(booking_id);
        $('#update_status_modal').modal('show');
        $("#divLoading").css({display: "none"});
    });

    $('.updtae_purchase_form_status').change(function(){
        var status_value = $( '.updtae_purchase_form_status:checked' ).val();  
        if(status_value==2)
        {
            $('.sattelment_amount_section').show();
        }
        else
        {
            $('.sattelment_amount_section').hide();   
        }
    });

    $(document).on("submit", "#updtae_purchase_form", function(event){
        event.preventDefault();
         var status_value = $('.updtae_purchase_form_status:checked').val(); 
        var remark = $('#update_remark').val();  
        var sattelment_amount = $('#sattelment_amount').val(); 
        var flag = 0;
        if(remark.trim()=='')
        {
            $('.v_remark').text('Please Enter remark');
            flag = 1;
        }
        else
        {
            $('.v_remark').text('');
        }
        if(status_value==2)
        {
            if(sattelment_amount.trim()=='')
            {
                $('.v_sattelment_amount').text('Please Enter sattelment amount');
                flag = 1;
            }
            else
            {
                $('.v_sattelment_amount').text('');
            }
        }
        else
        {
            $('.v_sattelment_amount').text('');
        } 
        if(flag == 1)
        {
            return false;
        }
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>purchase/purchase/updatestatus/',
            data: $("#updtae_purchase_form").serializeArray(), 
            dataType: "html",
            success: function(data){  
                //alert(data);
                if(data==1)
                {
                    alert("Order Updated successfully");
                    location.reload();
                }
                else
                {
                    alert("try again");
                }
            }
        });
    });



    $(document).on('click', '.detail', function(){
        var booking_id = $(this).attr('rel');
        var user_role = "<?php echo $logged_role; ?>";
        var data_party = $(this).attr('data-party');
        //alert(booking_id);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>purchase/purchase/GetBookingInfoDetails',
            data: { 'booking_id': booking_id},
            success: function(msg){  
                $("#divLoading").css({display: "none"}); 
                //$('.invoice_generate').attr('rel',booking_id);
                
                $(".BookingInfoDetails").html(msg);

                 
                $('#DetailModal').modal('show'); 
            }
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
<div id="update_status_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="updtae_purchase_form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Purchase Order</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="update_status_bookig_id" id="update_status_bookig_id" value="">
                <input type="radio" name="status" class="updtae_purchase_form_status" value="1" checked> Reject
                <input type="radio" name="status" class="updtae_purchase_form_status"  value="2"> Cancel
                <input type="radio" name="status" class="updtae_purchase_form_status"  value="4"> Approve
                <textarea class="form-control" name="remark" id="update_remark"></textarea>
                <span class="v_remark"></span>
                <br>
                <div class="sattelment_amount_section" style="display:none;">
                    <input class="form-control" type="text" name="sattelment_amount" id="sattelment_amount"  value="" placeholder="Enter sattelment amount">
                    <span class="v_sattelment_amount"></span>
                </div>
            </div>
            <div class="modal-footer">
                <span class="submit_reject"></span>
                <button type="submit" class="btn btn-success" >Update</button>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>
