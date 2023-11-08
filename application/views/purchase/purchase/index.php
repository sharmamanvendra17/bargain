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
            <form action="" class="" method="post" id="addbooking">  
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
                <?php if($logged_role==9 || $logged_role==4) { ?>
                <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="booking_number" id="booking_number" value="0">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">                                        
                                        <label for="name">Supplier</label> 
                                        <select class="form-control" id="party" name="party"  required1>
                                            <option value="">Select Party</option>
                                            <?php if($users) { 
                                                foreach ($users as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger v_party"><?php echo form_error('party'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category">Category </label> 
                                        <select class="form-control" id="category" name="category"  required1>
                                            <option value="">Select Category</option>
                                            <?php if($categories) { 
                                                foreach ($categories as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['category'])) { if($_POST['category']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['category_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger v_category"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product">Product</label> 
                                        <select class="form-control" id="product" name="product"  required1>
                                            <option value="">Select Product</option>
                                        </select>
                                        <span class="txt-danger v_product"><?php echo form_error('product'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight">Loose Weight (MT)</label>  
                                        <input type="text" class="form-control" id="weight" name="weight"  required1 value="<?php if(isset($_POST['weight'])) echo $_POST['weight']; ?>"> 
                                        <span class="txt-danger v_weight"><?php echo form_error('weight'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Loose Rate (MT)</label> 
                                        <input type="text" class="form-control rate" id="rate" name="rate"  value="<?php if(isset($_POST['rate'])) echo $_POST['rate']; ?>"  required1>
                                        <span class="rate_date"></span>
                                        <span class="txt-danger v_rate"><?php echo form_error('rate'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Loose Net Rate (MT)</label> 
                                        <input type="text" class="form-control" id="net_rate" name="net_rate"  readonly> 
                                        <span class="txt-danger "><?php echo form_error('rate'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4" style="display: none;">
                                    <div class="form-group">
                                        <label for="quality_condition">Quality Condition</label> 
                                        <input type="text" class="form-control" id="quality_condition" name="quality_condition"  value="<?php if(isset($_POST['quality_condition'])) echo $_POST['quality_condition']; ?>" > 
                                        <span class="txt-danger v_quality_condition"><?php echo form_error('quality_condition'); ?></span>
                                    </div>
                                </div> 
                                <div class="form_attributes"></div>   
                                <?php /* ?>                            
                                <div class="col-md-4 musturad_oil oil_cake custom_attributes" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="ffa">FFA (%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="ffa" name="ffa"  value="<?php echo (isset($_POST['ffa'])) ? $_POST['ffa']: 1; ?>" placeholder="FFA in percentage"> 
                                        <span class="txt-danger v_ffa"><?php echo form_error('ffa'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 musturad_oil custom_attributes oil_Pungency" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="ffa" class="oil_Pungency_label">Pungency (%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="pungency" name="pungency"  value="<?php echo (isset($_POST['pungency'])) ? $_POST['pungency'] : 25; ?>" placeholder="Pungency in percentage"> 
                                        <span class="txt-danger v_pungency"><?php echo form_error('ffa'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 musturad_seed oil_cake custom_attributes" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="oil_percentage">Oil % (30%-45%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="oil_percentage" name="oil_percentage"  value="<?php if(isset($_POST['oil_percentage'])) echo $_POST['oil_percentage']; ?>" placeholder="Oil Percentage"> 
                                        <span class="txt-danger v_oil_percentage"><?php echo form_error('oil_percentage'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-4 oil_cake custom_attributes" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="ffa">Sand & Silica (%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="sand" name="sand"  value="<?php echo (isset($_POST['sand'])) ? $_POST['sand']: 1; ?>" placeholder="Sand & Silica in percentage"> 
                                        <span class="txt-danger v_sand"><?php echo form_error('sand'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-4 musturad_seed custom_attributes oil_cake" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="moisture">Moisture (6%-15%)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="moisture" name="moisture"  value="<?php if(isset($_POST['moisture'])) echo $_POST['moisture']; ?>" placeholder="Moisture"> 
                                        <span class="txt-danger v_moisture"><?php echo form_error('moisture'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 musturad_seed custom_attributes" style="display: none;"> 
                                    <div class="form-group"> 
                                        <label for="mandi_expenses">Mandi Expenses (MT)</label>
                                        <input type="text" class="form-control custom_attributes_input" id="mandi_expenses" name="mandi_expenses"  value="<?php if(isset($_POST['mandi_expenses'])) echo $_POST['mandi_expenses']; ?>" placeholder="Mandi Expenses"> 
                                        <span class="txt-danger v_mandi_expenses"><?php echo form_error('mandi_expenses'); ?></span>
                                    </div>
                                </div>      
                                <?php */ ?>


                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        <?php $dt = date("Y-m-d"); ?>
                                        <label for="delivery_date">Delivery Days</label>  
                                        <input type="text" class="form-control" id="delivery_date1" name="delivery_date"  value="<?php if(isset($_POST['delivery_date'])) { echo $_POST['delivery_date']; } else { echo  8; } ?>"  required1>
                                        <span class="txt-danger v_delivery_date"><?php echo form_error('delivery_date'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="broker">Broker</label> 
                                        <select class="form-control" id="broker" name="broker" >
                                            <option value="">No Broker</option>
                                            <?php if($brokers) { 
                                                foreach ($brokers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['broker'])) { if($_POST['broker']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger v_broker"><?php echo form_error('broker'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <label for="sales_executive">Remark</label> 
                                    <div class="form-group"> 
                                        <textarea class="form-control" name="remark" id="remark" placeholder="Remark"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4" style="display:none;">
                                    <label for="sales_executive">Payment Terms</label> 
                                    <div class="form-group"> 
                                        <textarea class="form-control" name="payment_terms" id="payment_terms" placeholder="Patyment Terms"></textarea>
                                    </div>
                                </div> 
                            </div>
                            <div class="row" > 
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="ex_factory" name="ex_factory" value="1" > Price ex-factory
                                    </div>
                                </div> 
                                <div class="col-md-4 freight_row" style="display:none;">
                                    <div class="form-group"> 
                                        <label for="freight">Freight (MT) </label>
                                        <input type="text" class="form-control " id="freight" name="freight"  value="<?php if(isset($_POST['freight'])) echo $_POST['freight']; ?>" placeholder="Freight"> 
                                        <span class="txt-danger v_freight"><?php echo form_error('freight'); ?></span>
                                    </div>
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
                                <th>Purchase No</th>
                                <th>Party Name</th>
                                <th>Place</th>   
                                <th>Category</th>   
                                <th>Product</th>   
                                <th>Weight (MT)</th>  
                                <th>Rate</th>  
                                <th>Date</th> 
                                <?php //if($logged_role != 1) { ?>
                                <th>Status</th>
                                <th>Action</th>
                                <?php //} ?> 
                            </tr>
                        </thead>
                        <tbody class="booking_records">
                            <?php $total_weight = 0; if($bookings) { 
                                $i=1;
                                $count = 1;
                                $cur_page =1;
                                if(isset($limit))
                                    $con_li = $limit;
                                if($this->uri->segment(4)!='')
                                    $cur_page = $this->uri->segment(4);
                                $count = ($cur_page-1)*$con_li+1;
                                foreach ($bookings as $key => $value) { ?>
                                    <tr class="odd gradeX">
                                        <td><?php echo $count; ?></td>
                                        <td><span title="<?php echo $value['admin_name']; ?>">DATA/<?php echo $value['purchase_id']; ?></span></td>
                                        <td>
                                            <?php if($value['status']==0) { ?>
                                                <a title="Edit Brgain" href="<?php echo base_url('purchase/purchase/edit').'/'.base64_encode($value['id']);?>" class="">
                                            <?php  } ?>
                                            <?php echo $value['party_name']; ?>
                                            <?php if($value['status']==0) { ?>
                                                </a>
                                            <?php }  ?>
                                        </td>
                                        <td><?php echo $value['city_name']; ?></td> 

                                        <td><?php echo $value['category_name']; ?></td> 
                                        <td><?php echo $value['product_name']; ?></td> 

                                        <td><?php echo $value['weight']; ?></td>  
                                        <td><?php echo $value['rate']; ?></td> 
                                         
                                        <td><?php echo date("d-m-Y", strtotime($value['created_at'])); ?></td> 
                                        <?php //if($logged_role != 1) { ?>
                                        <td><?php 
                                                $app_status = 0;                                        
                                                
                                                if($value['status']==2)  
                                                { ?>
                                                    <span class="btn btn-danger "  data-status="3" rel="<?php echo base64_encode($value['id']); ?>" style="cursor:none">Cancelled</span>
                                                <?php } elseif($value['status']==1) {  ?>
                                                    <span class="btn btn-warning <?php echo ($logged_role == 9 ||$logged_role == 10 || $logged_role == 4 ) ? 'update_status_reject1' : 'style="cursor:none"'; ?>" data-status="3" rel="<?php echo base64_encode($value['id']); ?>" >Rejected</span>
                                                <?php } elseif($value['status']==4 ) {  ?>
                                                    <span class="btn btn-default <?php echo ($logged_role == 9 ||$logged_role == 10 || $logged_role == 4 ) ? 'update_status_reject' : 'style="cursor:none"'; ?>" data-status="3" rel="<?php echo base64_encode($value['id']); ?>" >Approved</span>
                                                <?php } elseif($value['status']==0) {  ?>
                                                <span class="btn btn-danger <?php echo ($logged_role == 9 ||$logged_role == 10 || $logged_role == 4 ) ? 'update_status_reject' : 'style="cursor:none"'; ?>"  data-status="3" rel="<?php echo base64_encode($value['id']); ?>" >Pending</span>
                                                <?php } ?> 
                                            </td> 
                                        <td>
                                            <a href="javascript:void(0)" rel="<?php echo $value['purchase_id']; ?>"  class="btn btn-default detail btn_report1"  data-party="<?php echo $value['party_id']; ?>" >Report</a>
                                            <?php if($value['status']==4)  { ?>
                                                <a href="<?php echo base_url('purchase/purchase/inventory/').'/'.base64_encode($value['id']); ?>"  class="btn btn-default">Inventory</a>
                                            <?php } ?>
                                        </td>
                                        <?php //} ?> 
                                    </tr>
                            <?php $count++; } } ?> 
                            <tr>
                                <td colspan="10">
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

<?php include APPPATH.'views/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#delivery_date").flatpickr({ 
    minDate: "today",
    dateFormat: "d-m-Y",
}); 
</script>
<script>
    function net_rate()
    {
        var rate = parseFloat($("#rate").val());
        var  rate = isNaN(rate) ? 0 : rate;
        var freight = 0;
        if($("#ex_factory").is(":checked"))
        {
            var freight = parseFloat($("#freight").val());
            var  freight = isNaN(freight) ? 0 : freight;
        }        
        var mandi_expenses = parseFloat($("#mandi_expenses").val());
        var  mandi_expenses = isNaN(mandi_expenses) ? 0 : mandi_expenses; 
        var net_rate = parseFloat(rate)+parseFloat(freight)+parseFloat(mandi_expenses);
        $("#net_rate").val(net_rate);
    }
    //$("#rate,#mandi_expenses,#freight").blur(function () { 
    $(document).on("blur","#rate,#mandi_expenses,#freight",function () { 
        net_rate();
    });
    $(document).ready(function(){ 
        $(document).on("keyup","#rate,#weight,#ffa,#pungency,#oil_percentage,#moisture,#mandi_expenses,#freight",function () { 
            if (this.value.match(/[^0-9.]/g, '')) { 
              this.value = this.value.replace(/[^0-9.]/g, '');      
            } 
        });

        $(document).on("blur",".custom_attributes_input",function () { 
            var default_value = $(this).attr('default_value');
            var min_value = parseFloat($(this).attr('min_value'));
            var max_value = parseFloat($(this).attr('max_value')); 
            if(min_value!="" && max_value!=""){
                if(this.value<min_value || this.value>max_value)
                this.value  = default_value
            }            
        }); 

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
                }
            });
        });
        $("#product").change(function(){
            $('.custom_attributes').hide();
            $('.custom_attributes_input').val();
            var category = $('#category :selected').text();
            var product = $('#product :selected').text();
            var attributes = $('#product :selected').attr('data-attributes'); 
            var product_id = $('#product').val(); 
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>purchase/purchaseproduct/getattributes',
                data: { 'product_id': product_id},
                success: function(msg){ 
                    $(".form_attributes").html(msg);
                }
            });


            /*if(category.toLowerCase()=='oil' )
            {
                if(product_id==9 )
                {
                    $('.oil_Pungency_label').text('Cloud point (%)');
                    $('#pungency').attr('placeholder','Cloud point in percentage');
                    $('#pungency').val(3);
                }
                if(product_id==5 )
                {
                    $('.oil_Pungency_label').text('Pungency (%)');
                    $('#pungency').attr('placeholder','Pungency in percentage');
                    $('#pungency').val(25);
                }                
                $('.musturad_oil').show();
                $("#mandi_expenses").val(0)
                if(product_id==7 || product_id==12 )
                {
                    $('.oil_Pungency').hide();
                }
            }
            if(category.toLowerCase()=='seed')
            {
                $('#oil_percentage').val(42);
                $('.musturad_seed').show();
            }
            if(category.toLowerCase()=='oil cake')
            {
                $('#oil_percentage').val(42);
                $('.oil_cake').show();
            } */
        });
        $(document).on("click", "#ex_factory", function(event){ 
            $(".freight_row").hide();
            if($("#ex_factory").is(":checked"))
            {
                $(".freight_row").show();
            }
            else
            {
                $('#freight').val(0);
            }
            net_rate();
        });
        $(document).on("submit", "#addbooking", function(event){
            event.preventDefault();
             
            var party_id = $("#party").val();
            var supplier = $("#supplier").val();
            var category = $("#category").val();
            var product = $("#product").val();

             


            var rate = $("#rate").val();
            var party_name = $("#party :selected").text(); 
            var broker = $("#broker").val();
            var ex_factory = $("#ex_factory").val(); 
            var weight = $("#weight").val(); 
            var flag = 0;
            
            
            if($("#ex_factory").is(":checked"))
            {
                var freight= $("#freight").val(); 
                if(freight.trim()=='')
                {
                    $(".v_freight").html("Please enter freight");
                    flag = 1;
                } 
                else
                {
                    $(".v_freight").html("");
                }
            } 

            if(party_id=='')
            {
                $(".v_party").html("Please select party");
                flag = 1;
            } 
            else
            {
                $(".v_party").html("");
            }
            if(category=='')
            {
                $(".v_category").html("Please select category");
                flag = 1;
            } 
            else
            {
                $(".v_category").html("");
            }

            if(rate.trim()=='')
            {
                $(".v_rate").html("Please enter rate");
                flag = 1;
            } 
            else
            {
                $(".v_rate").html("");
            }
            if(product=='')
            {
                $(".v_product").html("Please select product");
                flag = 1;
            } 
            else
            {
                $(".v_product").html("");
            }
            if(weight.trim()<=0)
            {
                $(".v_weight").html("Please enter weight");
                flag = 1;
            } 
            else
            {
                $(".v_weight").html("");
            } 
            if(flag==1)
            {
                return false;
            } 

            //$('.booking_submit').attr('disabled', 'disabled');

            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>purchase/purchase/checkpendingorder/',
                data: $("#addbooking").serializeArray(), 
                dataType: "html",
                success: function(pendingorders){ 
                    if(pendingorders>0)
                    {
                        if (!confirm("one order is already pending with this supplier. Do you want to add new purchase order ?")){
                          return false;
                        } 
                    }
                    $.ajax({
                        type: "POST",
                        url: '<?php echo base_url();?>purchase/purchase/add_purchase_order/',
                        data: $("#addbooking").serializeArray(), 
                        dataType: "html",
                        success: function(data){ 
                            $("#booking_number").val(0); 
                            
                            $('.border_booked_message').html("Order booked for <strong>"+party_name+"</strong> with purchase id <strong>#DATA/"+data+"</strong>");
                            $("#BookingSuccessModal").modal('show');
                            $('#addbooking').trigger("reset"); 
                            $("#party").select2("val", "");
                            $("#broker").select2("val", "");                    
                            $("#party").select2({ 
                                allowClear: true
                            });
                            $("#broker").select2({ 
                                allowClear: true
                            });                            
                        }
                    });
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

        $('input[name=status]').change(function(){
            var status_value = $( 'input[name=status]:checked' ).val(); 
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
             var status_value = $('input[name=status]:checked').val(); 
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


<div id="BookingSuccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="modal-title">Purchase Order Booked</h4>
            </div>
            <div class="modal-body border_booked_message">
                
            </div>
            <div class="modal-footer"> 
                <!--<button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>-->
                <a class="btn btn-default" href="<?php echo base_url('purchase/purchase'); ?>">Close</a>
            </div>
        </form>
    </div>

  </div>
</div>


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
                <input type="radio" name="status" value="1" checked> Reject
                <input type="radio" name="status"  value="2"> Cancel
                <?php if($logged_role==4 || $logged_role==10) { ?>
                <input type="radio" name="status" class="updtae_purchase_form_status"  value="4"> Approve
                <?php } ?>
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