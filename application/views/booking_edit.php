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
                <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="booking_number" id="booking_number" value="<?php echo $booking_info['id']; ?>">
                                <input type="hidden" name="" id="bargain_number" value="<?php echo $booking_info['booking_id']; ?>">                             
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">                                        
                                        <label for="name">Party Name</label> 
                                        <select class="form-control" id="party" name="party" required>
                                            <option value="">Select Party</option>
                                            <?php if($users) { 
                                                foreach ($users as $key => $value) { ?>
                                            <option data-state_id="<?php echo $value['state_id']; ?>" value="<?php echo $value['id']; ?>" <?php  if($booking_info['party_id']==$value['id']) { echo "selected"; }  ?>><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('party'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Brand</label> 
                                        <input type="hidden" name="previous_brand" value="<?php  echo $booking_info['brand_id']; ?>">
                                        <select class="form-control" id="brand" name="brand" required>
                                            <option value="">Select Brand</option>
                                            <?php if($brands) { 
                                                foreach ($brands as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php  if($booking_info['brand_id']==$value['id']) { echo "selected"; } ; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('brand'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product</label> 
                                        <input type="hidden" name="previous_product" value="<?php  echo $booking_info['category_id']; ?>">
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="">Select Product</option>
                                            <?php if($categories) { 
                                                foreach ($categories as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if($booking_info['category_id']==$value['id']) { echo "selected"; }  ?>><?php echo $value['category_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-4">
                                    <input type="hidden" name="previous_rate" value="<?php  echo $booking_info['rate']; ?>">
                                    <div class="form-group">
                                        <label for="rate">Rate (15Ltr Tin)</label> 
                                        <input type="text" class="form-control rate" id="rate" name="rate"  value="<?php if(isset($booking_info['rate'])) echo $booking_info['rate']; ?>" required  <?php echo ($allow_rate_booking || ($logged_role!=1 && $logged_role!=6) ) ? '' : 'readonly'; ?>>
                                        <span class="rate_date" style="color:#1a73e8;"></span>
                                        <span class="txt-danger"><?php echo form_error('rate'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" name="previous_qty" value="<?php  echo $booking_info['quantity']; ?>">
                                        <input type="hidden" name="previous_weight" value="<?php  echo $booking_info['total_weight']; ?>">
                                        <input type="hidden" name="net_weight" id="net_weight" value="<?php if(isset($booking_info['total_weight_net'])) echo $booking_info['total_weight_net']; ?>">

                                        <label for="quantity">Quantity (15 Ltr Tin)</label> 
                                        <input type="hidden" name="weight" id="weight" value="<?php if(isset($booking_info['total_weight'])) echo $booking_info['total_weight']; ?>">
                                        <input type="text" class="form-control quantity_packed" id="quantity" name="quantity" required value="<?php if(isset($booking_info['quantity'])) echo $booking_info['quantity']; ?>">
                                        <span class="Total_Weight_MT">
                                            <?php echo (isset($booking_info['total_weight']) && $booking_info['total_weight']) ? 'Gross : '.$booking_info['total_weight'].'MT' : '';  ?>
                                        </span>
                                        <br>
                                        <span class="Total_Weight_MT_net"><?php echo (isset($booking_info['total_weight_net']) && $booking_info['total_weight_net']) ? 'Net : '.$booking_info['total_weight_net'].'MT' : '';  ?></span>

                                        <span class="txt-danger"><?php echo form_error('quantity'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-4" style="min-height:130px;"> 
                                        <div class="form-group">
                                            <?php $dt = date("Y-m-d"); ?>
                                            <label for="quantity">Shipment Date</label>  
                                            <input type="text" class="form-control" id="shipment_date" name="shipment_date"  value="<?php if(isset($booking_info['shipment_date'])) echo date('d-m-Y', strtotime($booking_info['shipment_date'])); ?>" required>
                                            
                                        </div>
                                </div>
                                <?php    $north_east_sates = array(4,25,24,23,22,33,30,3); ?>
                                <div class="col-md-4 noth_east_blocks" <?php echo (in_array($booking_info['state_id'], $north_east_sates)) ? '' : 'style="display:none"'; ?> >
                                    <div class="form-group">
                                        <label for="quantity_numbers">Qunatity In Numbers</label>  
                                        <input type="text" class="form-control noth_east_inputs" id="quantity_numbers" name="quantity_numbers"  value="<?php echo (isset($booking_info['qunatity_in_numbers'])) ? $booking_info['qunatity_in_numbers'] : ''; ?>" <?php echo (in_array($booking_info['state_id'], $north_east_sates)) ? 'required' : ''; ?>> 
                                        <span class="txt-danger"><?php echo form_error('quantity_numbers'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 noth_east_blocks" <?php echo (in_array($booking_info['state_id'], $north_east_sates)) ? '' : 'style="display:none"'; ?>>
                                    <div class="form-group">
                                        <label for="small_pack_rate">Small Pack Rate</label>  
                                        <input type="text" class="form-control noth_east_inputs" id="small_pack_rate" name="small_pack_rate"  value="<?php echo (isset($booking_info['small_pack_rate']) && $booking_info['small_pack_rate']> 0) ? $booking_info['small_pack_rate'] : ''; ?>" <?php echo (in_array($booking_info['state_id'], $north_east_sates)) ? 'required' : ''; ?>> 
                                        <span class="txt-danger"><?php echo form_error('small_pack_rate'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="broker">Broker</label> 
                                        <select class="form-control" id="broker" name="broker" >
                                            <option value="">Select Broker</option>
                                            <?php if($brokers) { 
                                                foreach ($brokers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if($booking_info['broker_id']==$value['id']) { echo "selected"; } ; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('broker'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sales_executive">Sales Executive</label> 
                                        <select class="form-control" id="sales_executive" name="sales_executive" required>
                                            <option value="">Select Sales Executive</option>
                                            <?php if($makers) { 
                                                foreach ($makers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo ($booking_info['sales_executive_id']==$value['id']) ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('sales_executive'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sales_executive">Order Recieved Via</label> 
                                        <select class="form-control" id="order_recieved" name="order_recieved" required>
                                            <option value="" >Select Order Recieved Via</option> 
                                            <option value="Telephone" <?php echo ($booking_info['order_recieved_via']=='Telephone') ? 'selected' : ''; ?>>Telephone</option> 
                                            <option value="Whatsapp" <?php echo ($booking_info['order_recieved_via']=='Whatsapp') ? 'selected' : ''; ?>>Whatsapp</option> 
                                            <option value="Email" <?php echo ($booking_info['order_recieved_via']=='Email') ? 'selected' : ''; ?>>Email</option> 
                                            <option value="Letter Head" <?php echo ($booking_info['order_recieved_via']=='Letter Head') ? 'selected' : ''; ?>>Letter Head</option> 
                                            <option value="In Person" <?php echo ($booking_info['order_recieved_via']=='In Person') ? 'selected' : ''; ?>>In Person</option> 
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('order_recieved'); ?></span>
                                        <span class="txt-danger">* Please enter details in remark.</span>
                                    </div>
                                </div>
                            </div>  
                            <div class="row" >
                                <div class="col-md-12">
                                    <label for="sales_executive">Remak</label> 
                                    <div class="form-group"> 
                                        <textarea class="form-control" name="remark" id="remark" placeholder="Remark"><?php echo $booking_info['remark']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <!--<input type="checkbox"  id="insurance" name="insurance" value="1" <?php echo ($booking_info['insurance']>0) ? 'checked' : ''; ?>> Insurace Included in price --->
                                        <input type="checkbox"  id="insurance" name="insurance" value="1" <?php echo ($booking_info['insurance']>0) ? 'checked' : ''; ?> > Add Insurace in price
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="ex_factory" name="ex_factory" value="1" <?php echo ($booking_info['is_for']) ? 'checked' : ''; ?>> Price ex-factory
                                    </div>
                                </div>
                                <div class="form-group"> 
                                        <label class="btn-block"><strong>Production Unit</strong></label>
                                        <input type="radio" name="production_unit" value="alwar" <?php echo ($booking_info['production_unit']=='alwar'  || $booking_info['production_unit']=='' || is_null($booking_info['production_unit'])) ? 'checked' : ''; ?>> Alwar
                                        <input type="radio" name="production_unit" value="jaipur" <?php echo ($booking_info['production_unit']=='jaipur') ? 'checked' : ''; ?>> Jaipur
                                    </div>
                                </div>
                            </div>
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                         <button type="submit" class="btn btn-default booking_submit" value="Save">Save Bargain</button> 
                                    </div>                                  
                                </div>                                
                            </div>
                        
                    </div>
                </div> 
                <?php $total_weight = 0; ?> 
                 
            </div>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#shipment_date").flatpickr({ 
    minDate: "today",
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
    
    $(document).on('change', '#party', function(){
        $('.noth_east_blocks').hide();
        $(".noth_east_inputs").prop('required',false);
        var party_id = $(this).val(); 
        var state_id = $(this).find('option:selected').attr('data-state_id'); 
        var brand = $("#brand").val();
        var category = $("#category").val();
        if(party_id !='' && brand !='' && category !='')
        {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>booking/getinsurance',
                data: { 'party_id': party_id,'brand_id': brand,'category_id': category},
                success: function(msg){ 
                    if(msg!='0.00')
                    {
                        $("#insurance").prop('checked',true);
                    }
                    else
                    {
                        $("#insurance").prop('checked',false);
                    }
                }
            });
        }
        if(party_id.trim()!='')
        { 
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>vendors/get_vendorForinfo',
                data: { 'party_id': party_id},
                success: function(rate){
                    //alert(rate);
                    var obj = rate.split("_");
                    var for_rate = parseFloat(obj[0]); 
                    var tax_included = obj[1]; 
                    var freight_included = obj[2];   
                    if(for_rate>0)
                    {
                        $("#ex_factory").removeAttr('checked');
                    }
                    else
                    {
                        $("#ex_factory").attr("checked",true);
                        $("#ex_factory").prop("checked","checked");
                    }

                    /*if(insurance_included==0)
                    {
                        $("#insurance").removeAttr('checked');
                    }
                    else
                    {
                        $("#insurance").attr("checked",true);
                        $("#insurance").prop("checked","checked");
                    } */
                }
            });
        }


        if(state_id.trim()=='4' || state_id.trim()=='25' || state_id.trim()=='24' || state_id.trim()=='23' || state_id.trim()=='22' || state_id.trim()=='33' || state_id.trim()=='30' || state_id.trim()=='3') 
        {
            $('.noth_east_blocks').show();
            $(".noth_east_inputs").prop('required',true);
        }
        else
        {
            $("#quantity_numbers").val('');
            $("#small_pack_rate").val('');
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

    $(document).on('keyup', '#quantity_numbers', function(){
         if (this.value.match(/[^0-9]/g, '')) { 
          this.value = this.value.replace(/[^0-9.]/g, '');      
        } 
    });
    $(document).on('keyup', '#small_pack_rate', function(){
         if (this.value.match(/[^0-9.]/g, '')) { 
          this.value = this.value.replace(/[^0-9.]/g, '');      
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

        /*var l_to_kg = .910; 
        if(category_name=='vanaspati')
            var l_to_kg = .897; */

        /*old start */
        var l_to_kg = .910; 
        if(category_name=='vanaspati')
            var l_to_kg = .897;
        var total_weight_kg_net= (((this.value)*15)*l_to_kg);
        /*old end */


        var l_to_kg = 14.950; 
        if(category_name=='vanaspati')
            var l_to_kg = 13.455;
        var total_weight_kg= (((this.value))*l_to_kg);
        //var total_weight_kg= (((this.value)*15)*l_to_kg);
        var mt =  total_weight_kg/1000;
        var mt_rond = mt.toFixed(2);

        var mt_net =  total_weight_kg_net/1000;
        var net_mt_rond = mt_net.toFixed(3);

        $("#weight").val(mt);
        $("#net_weight").val(mt_net);
        $('.Total_Weight_MT').text(" Gross : "+mt_rond+' MT');
        $('.Total_Weight_MT_net').html("Net : "+net_mt_rond+' MT');
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
        $(".Total_Weight_MT_net").text('');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getcategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
                $("#category").html(msg);
                $("#party").trigger("change");
            }
        });
    });
    $("#category").change(function(){
        var category_name = $('#category :selected').text();
        if(category_name.toLowerCase()=='vanaspati' || category_name.toLowerCase()=='soya refined oil' || category_name.toLowerCase()=='sro 13.500 ' || category_name.toLowerCase()=='sro jaipur')
        { 
            $("input[name=production_unit][value='jaipur']").prop('checked', true);
        }
        else
        {
            $("input[name=production_unit][value='alwar']").prop('checked', true);
        }
        var category_id = $(this).val(); 
        var brand_id = $('#brand').val(); 
        $("#quantity").val('');
        $(".Total_Weight_MT").text('');
        $(".Total_Weight_MT_net").text('');
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getproductlist',
            data: { 'category_id': category_id},
            success: function(msg){
                $("#party").trigger("change");
                var response = msg.split("__");
                $(".product_packing").html(response[0]);
                number_of_packings = response[1];
                $('.more_added_products').remove();
            }
        });


        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getrate_booking',
            data: { 'category_id': category_id,'brand_id': brand_id},
            success: function(rate){
                //alert(rate); 
                var obj = rate.split("_");
                var rate_ltr = obj[0];
                var category_name  = $("#category :selected").text().toLowerCase();
                $("#rate").val(obj[0]);
                var is_ex_rate = obj[1]; 
                var insurance_included = obj[2]; 
                var update_date = obj[3]; 

                if(category_name=='vanaspati')
                {
                    var rate_kg = (obj[0]/.897).toFixed(2);
                }
                else
                {
                    var rate_kg = (obj[0]/.91).toFixed(2);
                } 
                if(update_date)
                {
                    $('.rate_date').html('Rate Updated On : '+update_date+ "<br>"+" Rate (15 KG Tin) : "+rate_kg);
                }
                else
                {
                    $('.rate_date').text('');
                }
                if(is_ex_rate==0)
                {
                    $("#ex_factory").removeAttr('checked');
                }
                else
                {
                    $("#ex_factory").attr("checked",true);
                    $("#ex_factory").prop("checked","checked");
                }

                
            }
        });

        var party_id = $('#party').val(); 
        if(party_id.trim()!='')
        { 
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>vendors/get_vendorForinfo',
                data: { 'party_id': party_id},
                success: function(rate){
                    //alert(rate);
                    var obj = rate.split("_");
                    var for_rate = parseFloat(obj[0]); 
                    var tax_included = obj[1]; 
                    var freight_included = obj[2];   
                    if(for_rate>0)
                    {
                        $("#ex_factory").removeAttr('checked');
                    }
                    else
                    {
                        $("#ex_factory").attr("checked",true);
                        $("#ex_factory").prop("checked","checked");
                    }

                    /*if(insurance_included==0)
                    {
                        $("#insurance").removeAttr('checked');
                    }
                    else
                    {
                        $("#insurance").attr("checked",true);
                        $("#insurance").prop("checked","checked");
                    } */
                }
            });
        }
    }); 
    $(document).ready(function(){
        $(document).on("submit", "#addbooking", function(event){
            event.preventDefault();
            var activeElement  = $(document.activeElement).val();
            if((activeElement=='popup'))
            {
                $("#RemarkModal").modal('show'); return false;
            }
            var party_id = $("#party").val();
            var party_name = $("#party :selected").text();
            var insurance = $("#insurance").val();
            var broker = $("#broker").val();
            var is_for = $("#is_for").val(); 

            var bargain_number = $("#bargain_number").val();

            var quantity = $("#quantity").val(); 
            if(quantity<=0)
            {
                alert("Please enter quantity");
                return false;
            } 
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>booking/update_booking/',
                data: $("#addbooking").serializeArray(), 
                dataType: "html",
                success: function(data){ 
                    
                    $('.border_booked_message').html("Order updated for <strong>"+party_name+"</strong> with bargain id <strong>#DATA/"+bargain_number+"</strong>");
                    $("#BookingSuccessModal").modal('show');
                    setTimeout(
                      function() 
                      {
                        //window.location.href = "<?php echo base_url('booking'); ?>";
                      }, 1000);
                        
                    
                }
            });
        });
         
        //$(".show_details").click(function(){
        $(document).on('click', '.show_details', function(){
            var booking_id = $(this).attr('rel');
            return false;  
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
            var data_lock = $(this).attr('data-lock');
            var data_status = $(this).attr('data-status'); 
            var user_role = "<?php echo $logged_role; ?>";
            var data_production_unit = $(this).attr('data-production_unit');

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
        $(document).on('click', '.update_status_reject', function(){
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
                <?php if($logged_role == 3 || $logged_role == 2) { ?>
                 
                <span class="approve_btn btn btn-default" rel="">Approve</span>
                <span class="mail_btn btn btn-default" rel="" style="display: none">Send</span>
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
                <h4 class="modal-title">Order Booked</h4>
            </div>
            <div class="modal-body border_booked_message">
                
            </div>
            <div class="modal-footer"> 
                <a href="<?php echo base_url('booking') ?>" class="btn btn-default" >Close</a> 
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
