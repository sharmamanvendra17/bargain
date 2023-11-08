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
            <form action="" class="" method="post" id="addbooking"> 
                <div class="col-md-4">
                    <span class="title elipsis header_add" style="display: none;">
                        <div class="form-group cal"> 
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
                <?php if($logged_role==6 || $logged_role==4) { ?>
                <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="booking_number" id="booking_number" value="0">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">                                        
                                        <label for="supply_from">Supply From</label> 
                                        <select class="form-control" id="supply_from" name="supply_from" required> 
                                            <?php if($super_disributers) { ?>  
                                            <option value="">Select Supply From</option>
                                            <?php foreach ($super_disributers as $key => $super_disributer) { ?>
                                            <option value="<?php echo $super_disributer['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$super_disributer['id']) { echo "selected"; } }; ?>><?php echo $super_disributer['name'].' - '.$super_disributer['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('supply_from'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="supply_to">Supply To </label> 
                                        <select class="form-control" id="supply_to" name="supply_to" required>
                                            <option value="">Select Supply To</option>
                                            <?php if($disributers) { 
                                                foreach ($disributers as $key => $disributer) { ?>
                                            <option value="<?php echo $disributer['id']; ?>" <?php if(isset($_POST['brand'])) { if($_POST['brand']==$disributer['id']) { echo "selected"; } }; ?>><?php echo $disributer['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('supply_to'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4"> 
                                        <div class="form-group">
                                            <?php $dt = date("Y-m-d"); ?>
                                            <label for="quantity">Delivery Date</label>  
                                            <input type="text" class="form-control" id="delivery_date" name="delivery_date"  value="<?php if(isset($_POST['delivery_date'])) { echo $_POST['delivery_date']; } else { echo  date("d-m-Y", strtotime( "$dt +7 day")); } ?>" required>
                                            
                                        </div>
                                </div>
                            </div>  
                            <input type="hidden" name="total_weight_input" id="total_weight_input_id" value="">
                            <div class="sku_list">
                                <?php $res = "" ;
                                if($skulists)
                                {
                                    $i = 1;            
                                    foreach ($skulists as $key => $value) { ?>
                                        
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php if($i==1) { ?>
                                                    <label for="name">Brand</label>
                                                    <?php } ?>
                                                    <select class="form-control" id="" name="brand[]">
                                                        <option value="<?php echo $value['brand_id']; ?>"><?php echo  $value['brand_name']; ?></option>';
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php if($i==1) { ?>
                                                    <label for="name">Category</label>
                                                    <?php } ?>
                                                    <select class="form-control" id="" name="category[]">
                                                        <option value="<?php echo $value['category_id']; ?>"><?php echo  $value['category_name']; ?></option>';
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php if($i==1) { ?>
                                                        <label for="name">Product</label>
                                                    <?php } ?>
                                                    <select class="form-control product_packing" id="" name="product[]">
                                                        <?php $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty']; ?>
                                                        <option value="<?php echo$value['id']; ?>"><?php echo $value['name'].$packing_items_qty; ?></option>';
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                <?php    $v = '';
                                                    $mt = 0;
                                                    $mt1 = ''; 
                                                    $placeholder = "Number of cartons";
                                                    if($value['packing_items_qty']==1)
                                                        $placeholder = "Number of tins"; ?>
                                                    <?php if($i==1) { ?>
                                                        <label for="name">Quantity</label>
                                                    <?php } ?>
                                                    <input type="hidden" class="packing_weight" name="packing_weight[]" value="<?php echo $mt; ?>">
                                                    <input type="hidden" class="packing_type" name="packing_type[]" value="<?php echo $value['packaging_type']; ?>">
                                                    <input type="hidden" class="packed_items_quantity" name="packed_items_quantity[]" value="<?php echo $value['packing_items']; ?>">
                                                    <input type="text" class="form-control quantity_packed" id="" name="quantity[]"  value="<?php echo $v; ?>" placeholder="<?php echo $placeholder; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2 packing_weight_input_section">
                                                <div class="form-group">
                                                    <?php if($i==1) { ?>
                                                        <label for="name">Weight (MT)</label>
                                                    <?php } ?>
                                                    <input type="text" class="form-control packing_weight_input" id="" name=""  value="<?php echo $mt1; ?>" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php if($i==1) { ?>
                                                        <label for="name">Rate per piece</label>
                                                    <?php } ?>
                                                    <input type="text" class="form-control" id="" name="rate[]"  value="0" >
                                                    <span class="packing_rate_show" style="color:red;">1500Rs.</span>
                                                </div>
                                            </div>
                                        </div> 
                                <?php $i++; } }  ?>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="sales_executive">Remak</label> 
                                    <div class="form-group"> 
                                        <textarea rows="2" class="form-control" name="remark" id="remark" placeholder="Remark"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="sales_executive">Payment Terms</label> 
                                    <div class="form-group"> 
                                        <select class="form-control" name="payment_term" id="payment_term" required>
                                            <option value="">Select Payment Term</option>
                                            <option value="Advance">Advance</option>
                                            <option value="On Delivery">On Delivery</option> 
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row total_weight_show">
                                <div class="col-md-8 ">   
                                </div>
                                <div class="col-md-4 text-center">                
                                    Added SKU Weight <br><span class="weightValue total_weight_input"></span>
                                </div>
                            </div> 
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                         <button type="submit" class="btn btn-default booking_submit" value="Save">Save Order</button> 
                                    </div>                                  
                                </div>                                
                            </div>
                        
                    </div>
                </div> 
                <?php } ?>
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
                                            <a href="javascript:void(0)" data-lock="0" rel="<?php echo $value['id']; ?>" data-party="69" class="btn btn-default detail btn_report1" data-production_unit="alwar" data-status="0" data-view_approval_status="0"><?php echo ($value['status']) ? '<span class="approved"><img src="'.base_url().'assets/img/check.svg" style="height:14px;"></span>' : ''; ?> Send Order</a>

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
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script type="text/javascript">
$("#delivery_date").flatpickr({ 
    minDate: "today",
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        $("#supply_from").select2(); 
        $("#supply_to").select2(); 
    });
    $(document).on('change', '#supply_from', function(){  
        var vendor_id = $(this).val();
        $(".loader").show();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/GetBookingSkus',
            data: { 'vendor_id': vendor_id},
            success: function(msg){ 
                $(".sku_list").html(msg);
                $(".loader").hide();
                if(msg)
                {
                    $('.total_weight_input').text('');
                    $('.total_weight_show').show();
                }
            }
        }); 

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/GetDistributors',
            data: { 'vendor_id': vendor_id},
            success: function(msg){ 
                $('#supply_to').html(msg);
            }
        }); 
    });
    $(document).on('blur', '.rate', function(){  
        if (this.value.match(/[^0-9.]/g, '')) { 
          this.value = this.value.replace(/[^0-9]/g, '');      
        }
        var amount = this.value;  
    });
    $(document).on('blur', '.quantity_packed', function(){  
        var category_id = $("#category").val();
        var category_name = $("#category :selected").text().toLowerCase(); 
        var category_name = category_name.trim();  
        if(category_id=='')
        {
            alert("Please select product"); 
            this.value = '';
            return false;
        }
        var total_weight_input = 0; 
        if (this.value.match(/[^0-9]/g, '')) { 
          this.value = this.value.replace(/[^0-9]/g, '');      
        }

        var available_qty = parseInt($(this).next().text());
        var input_qty = parseInt($(this).val()); 
        if(available_qty<input_qty)
        {
            this.value = 0;
            $(this).siblings('.packing_weight').val(0);
            $(this).closest(".col-md-2").next().find(".packing_weight_input").val(0); 
            total_weight_input = 0 ;
            alert("sorry You have exceeded the quantity limit");
        }


        var packing_type = $(this).parent().find('.packing_type').val();
        var packed_items_quantity = $(this).parent().find('.packed_items_quantity').val(); 

        var packing_rate = $(this).parent().find('.packing_rate').val(); 
        var packing_rate_total = parseFloat(packing_rate)*$(this).val();
        $(this).closest('.col-md-4').next('.col-md-4').find('.packing_rate_show').text(''+packing_rate+'*'+$(this).val()+' = '+packing_rate_total+' Rs.');

        var l_to_kg = 1;
        var approx_weight=0.02;             
        if(packing_type!=1)
        {
            var l_to_kg = .91;
            approx_weight = .0182; 
        } 
        if((l_to_kg==.91 && category_name=='vanaspati'))
        {
            var l_to_kg = .897; 
            approx_weight = .01794;  
        } 
        var total_weight_kg= (((this.value)*packed_items_quantity)*l_to_kg);

        var mt =  (total_weight_kg/1000);
        var mt_rond = mt.toFixed(2);  
        if(parseFloat(mt)>0)
        {
            var mt_t= mt.toFixed(4);
            //$(this).next().text(mt_t+' MT');
        }
        else
        {
            //$(this).next().text('');
        }
        $(this).siblings('.packing_weight').val(mt);
        $(this).closest(".col-md-2").next().find(".packing_weight_input").val(mt);

        //$(this).next().focus();
        $('.packing_weight').each(function(i, obj) {  
            if(obj.value)
            {  
                total_weight_input = total_weight_input+parseFloat(obj.value);
            }
        });  
        total_weight_input1 =  total_weight_input.toFixed(4); 
        $('.total_weight_input').text(total_weight_input1+' MT');           
        $('#total_weight_input_id').val(total_weight_input1);  


    }); 

    $(document).on("submit", "#addbooking", function(event){
        event.preventDefault();         
        var supply_from = $("#supply_from").val();
        var party_name = $("#supply_from :selected").text();

        var rates = $(".rate");
        var count = 1;
        var val_flag = 0;
        $.each(rates, function(key,val) {
            // console.log(val);
            //alert(key); 
           var rate_value = $(this).val();
           var quantity_value = $(".quantity"+count).val(); 
           if((rate_value.trim()=='' && quantity_value.trim()=='') || (rate_value.trim()!='' && quantity_value.trim()!='') )
            {
                
            }
            else
            {
                alert("Please wnter weight and rate both");
                $(this).focus();
                return false;
            }
           count++;
        }); 
        var supply_to = $("#supply_to").val();
        var supply_to_name = $("#supply_to :selected").text(); 
        $('.booking_submit').attr('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>secondarybooking/add_booking/',
            data: $("#addbooking").serializeArray(), 
            dataType: "html",
            success: function(data){
                
                $(".total_weight_input").text('');
                $("#booking_number").val(0); 
                $('#addbooking').trigger("reset");
                $(".sku_list").html('');
                $("#total_weight_input").val(0);
                $('.border_booked_message').html("Order booked for <strong>"+party_name+"</strong> with secondary bargain id <strong>#DATA/SEC/"+data+"</strong>");
                $("#BookingSuccessModal").modal('show');                 
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
});
</script>

<div id="BookingSuccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="modal-title">Secondary Order Booked</h4>
            </div>
            <div class="modal-body border_booked_message">
                
            </div>
            <div class="modal-footer"> 
                <!--<button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>-->
                <a class="btn btn-default" href="<?php echo base_url('secondarybooking/'); ?>">Close</a>
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
<div class="loader"><img src="https://sales.datagroup.in/assets/img/hug.gif"> </div>