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
            <form action="<?php echo base_url('purchase/labreport/'); ?>" class="" method="post" id="addbooking"> 
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
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Product</label> 
                                        <select class="form-control" id="product" name="product" >
                                            <option value="">Select Product</option>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('product'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label for="booking_date_from">Inventory Date (From) </label>
                                        <input class="form-control" type="text" id="booking_date_from" name="booking_date_from"  value="<?php if(isset($_POST['booking_date_from']) && !empty($_POST['booking_date_from']) ) { echo $_POST['booking_date_from']; } else { echo date('d-m-Y'); } ?>" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label for="booking_date_to">Inventory Date (To)</label>
                                        <input class="form-control" type="hidden" id="booking_date_to" name="booking_date_to" value="<?php if(isset($_POST['booking_date_to']) && !empty($_POST['booking_date_to']) ) { echo $_POST['booking_date_to']; } else { echo date('d-m-Y'); } ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">ERP Sr. No.</label> 
                                        <input type="text" name="erp_sr_no" class="form-control" value="<?php if(isset($_POST['erp_sr_no']) && !empty($_POST['erp_sr_no']) ) { echo $_POST['erp_sr_no']; } ?>" placeholder="Search by ERP Sr. No.">
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Code</label> 
                                        <input type="text" name="code" class="form-control" value="<?php if(isset($_POST['code']) && !empty($_POST['code']) ) { echo $_POST['code']; } ?>" placeholder="Search by Code">
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Purchase Number</label> 
                                        <input type="text" name="purchase_number" class="form-control" value="<?php if(isset($_POST['purchase_number']) && !empty($_POST['purchase_number']) ) { echo $_POST['purchase_number']; } ?>" placeholder="Search by Purchase Number">
                                        <span class="txt-danger"><?php echo form_error('purchase_number'); ?></span>
                                    </div>
                                </div>
                            </div>                              
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search Report</button>  
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
                                <th>Gr/LR Date</th>
                                <th>Code</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>FFA</th>
                                <th>Pungency / Cloud Point</th>
                                <th>Colour</th>
                                <th>Oil %</th>
                                <th>Moisture</th>
                                <th>Sand & Silica</th>
                                <th>Created at</th>   
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="booking_records">
                            <?php  $total_weight = 0; if($inventories) { 
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
                                        <td><?php echo ($value['erp_sr_no']) ? $value['erp_sr_no'] : '-'; ?></td>
                                        <td><?php echo ($value['gr_lr_date']) ? date('d-m-Y', strtotime($value['gr_lr_date'])) : '-'; ?></td> 
                                        <td><?php echo ($value['code']) ? $value['code'] : '-'; ?></td> 
                                        <td><?php echo $value['category_name']; ?></td> 
                                        <td><?php echo $value['product_name']; ?></td>
                                        <td style="color:red"><?php echo ($value['lab_result_ffa']) ? $value['lab_result_ffa'] : '-'; ?></td>
                                        <td style="color:red"><?php echo ($value['lab_result_pungency']) ? $value['lab_result_pungency'] : '-'; ?></td>
                                        <td style="color:red"><?php echo ($value['lab_result_color']) ? $value['lab_result_color'] : '-'; ?></td>
                                        <td style="color:red"><?php echo ($value['lab_result_oil_percentage']) ? $value['lab_result_oil_percentage'] : '-'; ?>
                                        <td style="color:red"><?php echo ($value['lab_result_moisture']) ? $value['lab_result_moisture'] : '-'; ?></td>
                                        <td style="color:red"><?php echo ($value['lab_result_sand']) ? $value['lab_result_sand'] : '-'; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($value['created_at'])); ?></td>  
                                        <td><a href="javascript:void(0)" data-lab_result_id="<?php echo $value['lab_result_id']; ?>" data-ffa="<?php echo $value['lab_result_ffa']; ?>" data-oil_percentage="<?php echo $value['lab_result_oil_percentage']; ?>" data-pungency="<?php echo $value['lab_result_pungency']; ?>" data-moisture="<?php echo $value['lab_result_moisture']; ?>" data-remark="<?php echo $value['lab_result_remark']; ?>" data-color="<?php echo $value['lab_result_color']; ?>" data-sand="<?php echo $value['lab_result_sand']; ?>" data-smell="<?php echo $value['lab_result_smell']; ?>"    data-purchase_inventory_id="<?php echo $value['id']; ?>"  data-erp_sr_no="<?php echo $value['erp_sr_no']; ?>"  data-code="<?php echo $value['code']; ?>"   data-purchase_id="<?php echo $value['purchase_id']; ?>" data-product-id="<?php echo $value['product_id']; ?>" data-product="<?php echo strtolower($value['product_name']); ?>" data-category="<?php echo strtolower($value['category_name']); ?>"  class="btn btn-<?php echo ($value['lab_result_id']) ? 'default'  : 'danger'; ?> update_report"><?php echo ($admin_role==12) ? 'Update' : 'Lab Report';?></a></td>
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

        $("#ffa,#pungency,#oil_percentage,#moisture,#color").keyup(function () { 
            if (this.value.match(/[^0-9.]/g, '')) { 
              this.value = this.value.replace(/[^0-9.]/g, '');      
            } 
        });
        
    });
    $(document).ready(function() {
        var category_id = $("#category").val();  
        var product_id = '<?php echo $product_id; ?>'; 
        if(category_id.trim()!='')
        {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>purchase/labreport/getproductlist',
                data: { 'category_id': category_id},
                success: function(msg){ 
                    $("#product").html(msg);
                    $("#product").val(product_id);
                }
            });
        }
      $(window).keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
      });

        $("#category").change(function(){
            $('.custom_attributes').hide();
            $('.custom_attributes_input').val();
            var category_id = $(this).val();   
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>purchase/labreport/getproductlist',
                data: { 'category_id': category_id},
                success: function(msg){ 
                    $("#product").html(msg);
                }
            });
        }); 
        $(document).on('click', '.update_report', function(){
            var category = $(this).attr('data-category');
            var product = $(this).attr('data-product');
            var code = $(this).attr('data-code');
            var product_id = $(this).attr('data-product-id');

            var lab_result_id = $(this).attr('data-lab_result_id');
            var ffa = $(this).attr('data-ffa');
            var pungency = $(this).attr('data-pungency');
            var moisture = $(this).attr('data-moisture');
            var color = $(this).attr('data-color');
            var remark = $(this).attr('data-remark');
            var smell = $(this).attr('data-smell');
            var sand = $(this).attr('data-sand');
            var oil_percentage = $(this).attr('data-oil_percentage'); 

            var erp_sr_no = $(this).attr('data-erp_sr_no');
            var purchase_id = $(this).attr('data-purchase_id');
            var purchase_inventory_id = $(this).attr('data-purchase_inventory_id');
            $("#lab_result_id").val(lab_result_id);
            $("#purchase_inventory_id").val(purchase_inventory_id);
            $("#code").val(code);
            $("#erp_sr_no").val(erp_sr_no);
            $(".purchase_code").text("Lab Results #"+code);


            /*
            $('.oil_cake').hide();
            $('.musturad_seed').hide();
            $('.musturad_oil').hide();
            $('.oil_Pungency').hide();

            $('#ffa').val(ffa);
            $('#pungency').val(pungency);
            $('#oil_percentage').val(oil_percentage);
            $('#moisture').val(moisture);
            $('#color').val(color);
            $('#remark').val(remark);
            $('#smell').val(smell);
            $('#sand').val(sand); */

            /*if(category.toLowerCase()=='oil' )
            {
                if(product_id==9 )
                {
                    $('.oil_Pungency_label').text('Cloud point (%)');
                    $('#pungency').attr('placeholder','Cloud point in percentage'); 
                }
                if(product_id==5 )
                {
                    $('.oil_Pungency_label').text('Pungency (%)');
                    $('#pungency').attr('placeholder','Pungency in percentage'); 
                }                
                $('.musturad_oil').show(); 
                if(product_id==7 || product_id==12 )
                {
                    $('.oil_Pungency').hide();
                }
            }
            if(category.toLowerCase()=='seed')
            {
                $('#oil_percentage').val('');
                $('.musturad_seed').show();
            }
            if(category.toLowerCase()=='oil cake')
            {
                //$('#oil_percentage').val(42);
                $('.oil_cake').show();
            }*/
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>purchase/labreport/getreportattributes',
                data: { 'product_id': product_id,'inventory_id' :purchase_inventory_id},
                success: function(msg){ 
                    $(".form_attributes").html(msg);
                }
            }); 
            $('#update_inventory_modal').modal('show');
        });
        $(document).on("submit", "#updatelabreport", function(event){
            event.preventDefault();             
            var code = $("#code").val();   

            var flag = 0;            
              
            //$('.booking_submit').attr('disabled', 'disabled'); 
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>purchase/labreport/updatelabreport/',
                data: $("#updatelabreport").serializeArray(), 
                dataType: "html",
                success: function(data){    
                    if(data)
                    {
                        $('#update_inventory_modal').modal('hide');
                        $('.border_booked_message').text('Lab result for code #'+code+' added successfully ');
                        $("#BookingSuccessModal").modal({backdrop: 'static', keyboard: false,show:true});
                    }
                    else
                    {
                        alert("something went wrong");
                    }
                }
            }); 
        });
    });
</script>
<!-- Trigger the modal with a button -->

 



<div id="BookingSuccessModal" data-bs-backdrop='static'  class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="modal-title border_booked_message_title border_booked_message">Lab result added successfully.</h4>
            </div> 
            <div class="modal-footer"> 
                <!--<button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>-->
                <a class="btn btn-default" href="<?php echo  current_url();; ?>">Close</a>
            </div>
        </form>
    </div>

  </div>
</div>
<!-- Modal update form -->
<div id="update_inventory_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title purchase_code"></h4>
            </div>  
            <form action="" class="" method="post" id="updatelabreport">
                <div class="modal-body">
                        <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="lab_result_id" id="lab_result_id" value="0">
                                <input type="hidden" name="code" id="code" value="">
                                <input type="hidden" name="purchase_inventory_id" id="purchase_inventory_id" value="">
                                <input type="hidden" name="erp_sr_no" id="erp_sr_no" value=""> 
                            </div>
                            <div class="row"> 
                                <div class="form_attributes"></div>


                                <?php /* ?><div class="col-md-4 musturad_oil custom_attributes oil_cake" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="ffa">FFA (%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="ffa" name="ffa"  value="" placeholder="FFA in percentage"> 
                                        <span class="txt-danger v_ffa"><?php echo form_error('ffa'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 musturad_oil custom_attributes oil_Pungency" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="ffa" class="oil_Pungency_label">Pungency (%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="pungency" name="pungency"  value="" placeholder="Pungency in percentage"> 
                                        <span class="txt-danger v_pungency"><?php echo form_error('ffa'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 musturad_seed custom_attributes oil_cake" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="oil_percentage">Oil % </label>
                                        <input type="text" class="form-control custom_attributes_input" id="oil_percentage" name="oil_percentage"  value="" placeholder="Oil Percentage"> 
                                        <span class="txt-danger v_oil_percentage"><?php echo form_error('oil_percentage'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 custom_attributes oil_cake" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="ffa">Sand & Silica (%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="sand" name="sand"  value="" placeholder="Sand & Silica in percentage"> 
                                        <span class="txt-danger v_ffa"><?php echo form_error('sand'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 musturad_seed custom_attributes oil_cake" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="moisture">Moisture</label>
                                        <input type="text" class="form-control custom_attributes_input" id="moisture" name="moisture"  value="" placeholder="Moisture"> 
                                        <span class="txt-danger v_moisture"><?php echo form_error('moisture'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <label for="sales_executive">Color</label> 
                                    <div class="form-group"> 
                                        <input type="text" class="form-control" name="color" id="color" placeholder="Color">
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <label for="sales_executive">Smell</label> 
                                    <div class="form-group"> 
                                        <input type="text" class="form-control" name="smell" id="smell" placeholder="Smell">
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <label for="sales_executive">Remark</label> 
                                    <div class="form-group"> 
                                        <textarea class="form-control" name="remark" id="remark" placeholder="Remark"></textarea>
                                    </div>
                                </div>
                                <?php */ ?>
                            </div> 
                    </div>
                </div>  
                </div>
                <div class="modal-footer">
                <?php if($admin_role==12){ ?>
                    <button type="submit" class="btn btn-success booking_submit" value="Save">Update</button> 
                 <?php } ?>   
                    <button type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">Cancel</button> 
                </div> 
            </form>
    </div>
  </div>
</div>