<?php include 'header.php'; ?>
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
            <form action="" class="" method="post" id="addskus"> 
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
                        <strong>
                        <div class="row">  
                            <div class="col-md-4 text-center">
                                Ordered SKU Weight <br><span class="weightValue total_weight_ordered"><?php echo $booking_info['total_weight']; ?> MT</span> 
                            </div>  
                            <div class="col-md-4 text-center">                
                            Added SKU Weight <br><span class="weightValue total_weight_input"></span>
                            </div>  
                            <div class="col-md-4 text-center">                
                                Remaining SKU Weight <br><span class="weightValue remaining_weight_input"></span>
                            </div>
                        </div>
                        </strong>
                        <div class="row">  
                            <input type="hidden" name="" id="party_id"  value="<?php echo $booking_info['party_id']; ?>">

                            <input type="hidden" name="" id="booking_status"  value="<?php echo $booking_info['status']; ?>">
                            <input type="hidden" name="update_weight" id="update_weight"  value="0">
                            <input type="hidden" name="update_data" id="update_data"  value="0">
                            <input type="hidden" name="id" id="id" value="<?php echo $booking_info['id']; ?>">
                            <input type="hidden" name="booking_id" id="booking_id" value="<?php echo $bargain_id; ?>">
                            <input type="hidden" name="total_weight_input" id="total_weight_input_id" value="<?php echo ($booking_info['total_weight_input']) ? $booking_info['total_weight_input'] : 0; ?>">
                            <input type="hidden" name="remaining_weight" id="remaining_weight_id" value="<?php echo ($booking_info['remaining_weight']) ? $booking_info['remaining_weight'] : 0; ?>">
                            <input type="hidden" name="flag" id="flag" value="0">
                        </div>
                        <div class="row"> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Brand </label> 
                                    <select class="form-control" id="brand" name="brand" required>
                                        <option value="">Select Brand</option>
                                        <?php if($brands) { 
                                            foreach ($brands as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>" <?php echo ($booking_info['brand_id']==$value['id']) ? 'selected' : '' ; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('brand'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Product</label> 
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Product</option>
                                        <?php if($brand_categories){ 
                                            foreach ($brand_categories as $key => $brand_category) { ?>
                                            <option value="<?php echo $brand_category['id'];?>" <?php echo ($booking_info['category_id']==$brand_category['id']) ? 'selected' : '' ; ?>><?php echo $brand_category['category_name'];?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rate">Rate (15Ltr Tin)</label> 
                                    <input type="text" class="form-control rate" id="rate" name="rate"  value="<?php echo $booking_rate = $booking_info['rate']; ?>" required>
                                    <span class="txt-danger"><?php echo form_error('rate'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="packing">
                            <?php if($products) 
                            {
                                $res = '';
                                $i = 1;            
                                foreach ($products as $key => $value) {
                                    $res .= '<div class="row"><div class="col-md-4"><div class="form-group">';
                                    if($i==1)
                                    $res .= '<label for="name">Packed In</label>';
                                    $res .= '<select class="form-control product_packing" id="" name="product[]">';
                                    $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                                    $res .= '<option value="'.$value['id'].'">'.$value['name'].$packing_items_qty.'</option>';
                                    $res .= '</select></div></div><div class="col-md-4"><div class="form-group">';

                                    $v = '';
                                    $mt = 0;
                                    $mt1 = '';
                                    $approx_weight=0.02;
                                    $l_to_kg_rate = round((1/.91),2);
                                    if (array_key_exists($value['id'],$skus))
                                    {
                                        $v = $skus[$value['id']]['quantity'];
                                        $packing_type = $value['packaging_type'];
                                        $packed_items_quantity = $value['packing_items']; 
                                        $l_to_kg = 1; 
                                        $approx_weight=0.02;
                                        if($packing_type!=1)
                                        {
                                            $l_to_kg = .91; 
                                            $approx_weight = .0182; 
                                        }
                                        
                                        if(($l_to_kg ==.91 && strtolower($value['category_name'])=='vanaspati'))
                                        {
                                            $l_to_kg = .897; 
                                            $approx_weight = .01794; 
                                        }

                                        $total_weight_kg= (($v*$packed_items_quantity)*$l_to_kg);
                                        $mt =  ($total_weight_kg/1000);
                                        $mt_rond = round($mt,2);    
                                        $mt1 = round($mt,4).' MT';
                                    }
                                    else
                                    {
                                         
                                        $packing_type = $value['packaging_type'];
                                        $packed_items_quantity = $value['packing_items'];  
                                        if((strtolower($value['category_name'])=='vanaspati'))
                                        {
                                            $l_to_kg_rate = round((1/.897),2);  
                                        }
                                    }

                                    if($i==1)
                                    $res .= '<label for="quantity">Quantity</label>';
                                    $sku_rate = 0;
                                    $empty_tin_charge = ($value['loose_rate']*$value['packing_items_qty']);

                                    if($value['packaging_type']!=1)
                                    {
                                        $packing_rate_ltr = ($booking_info['rate']-$booking_info['tin_rate'])/15;
                                        $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                        
                                    }
                                    else
                                    { 
                                        $packing_rate_ltr = (($booking_info['rate']-$booking_info['tin_rate'])/15)*$l_to_kg_rate;
                                        $sku_rate = round(($value['packing_items']*$packing_rate_ltr),2);
                                    }
                                    $sku_rate = $sku_rate+$empty_tin_charge;
                                    if($v)
                                    {
                                        $sku_rate_total =  ($sku_rate*$v);
                                        $sku_rate_dispaly = $sku_rate.'*'.$v.' = '.$sku_rate_total;
                                    }
                                    else
                                    {
                                       $sku_rate_dispaly = $sku_rate.'*0'.' = 0'; 
                                    }

                                    $res .= '<input type="hidden" class="packing_weight" name="packing_weight[]" value="'.$mt.'"><input type="hidden" class="packing_type" name="packing_type[]" value="'.$value['packaging_type'].'"><input type="hidden" class="packed_items_quantity" name="packed_items_quantity[]" value="'.$value['packing_items'].'">

                                    <input type="hidden" class="form-control  packing_rate" id="" name=""  value="'.$sku_rate.'" readonly placeholder="Rate">
                                    ';
                                    $placeholder = "Number of cartons";
                                    if($value['packing_items_qty']==1)
                                        $placeholder = "Number of tins";
                                    $res .='<input type="text" class="form-control quantity_packed" id="" name="quantity[]"  value="'.$v.'" placeholder="'.$placeholder.'"></div></div><div class="col-md-4"><div class="form-group">';
                                    if($i==1)
                                    $res .= '<label for="quantity">Weight (MT)</label>';
                                    $res .='<input type="text" class="form-control  packing_weight_input" id="" name=""  value="'.$mt.'" readonly>';
                                    

                                    $res .='<span class="packing_rate_show" style="color:red;">'.$sku_rate_dispaly.' Rs.</span>';
                                    $res .='</div></div></div>';
                                    $i++;
                                }
                               echo $res;  
                            } ?>
                        </div>  

                        <strong>
                        <div class="row">  
                            <div class="col-md-4 text-center">
                                Ordered SKU Weight <br><span class="weightValue total_weight_ordered"><?php echo $booking_info['total_weight']; ?> MT</span> 
                            </div>  
                            <div class="col-md-4 text-center">                
                                Added SKU Weight <br><span class="weightValue total_weight_input"></span>
                            </div>  
                            <div class="col-md-4 text-center">                
                                Remaining SKU Weight <br><span class="weightValue remaining_weight_input"></span>
                            </div>
                        </div>
                        </strong>
                        <?php
                        $total_weight_input = $booking_info['total_weight_input'];
                        $disabled = 'disabled';  
                        $approx_total_weight_ordered_min = $booking_info['total_weight']-$approx_weight;
                        $approx_total_weight_ordered_max = $booking_info['total_weight']+$approx_weight;
                        if(($total_weight_input<$approx_total_weight_ordered_max && $total_weight_input>$approx_total_weight_ordered_min) && $booking_info['status']==2)
                        {
                            $disabled = '';
                        } 
                        if($booking_info['is_lock']==0) {
                        ?> 
                        <hr>
                        <div class="row"> 
                            <div class="col-md-4">
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
                                    <button type="submit" class="btn btn-default booking_submit" value="Save">Save SKU's</button> 
                                    <input type="submit" class="btn btn-danger lock_order" value="Lock & Mail Order" <?php echo $disabled; ?> name="lock"> 
                                    <br>
                                    <span class="txt-danger" style="padding-top: 10px; display: inline-block;">** Bargain is <?php echo  ($booking_info['is_lock']) ? 'locked.' :  'not locked yet.';   ?></span>
                                </div>                                  
                            </div>                                  
                        </div>  
                        <?php } else { ?> 
                            <div class="row" style="padding-top: 20px;">                               
                                <div class="col-md-4">
                                    <span style="color: green;">** Bargain is <?php echo  ($booking_info['is_lock']) ? 'locked.' :  'not locked yet.';   ?></span>
                                </div>
                            </div>                    
                        <?php } ?>
                    </div>
                </div>                  
            </div>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script>
$(document).ready(function(){
    var booking_status;
    booking_status = "<?php echo  $booking_info['status']; ?>";     
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
    $(document).on('keyup', '.rate', function(){
         if (this.value.match(/[^0-9.]/g, '')) { 
          this.value = this.value.replace(/[^0-9.]/g, '');      

        } 
    });  
    $("#brand").change(function(){
        var brand_id = $(this).val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getcategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
                $("#category").html(msg);
            }
        });
    });   
    setTimeout(function () {
        var category_id = '<?php echo $booking_info['category_id']; ?>'; 
        $('#category').val(category_id);
        var brand_id = '<?php echo $booking_info['brand_id']; ?>';
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getproductlisting',
            data: { 'category_id': category_id},
            success: function(msg){ 
                //$(".packing").html(msg);
            }
        });
    }, 0);
    $("#category").change(function(){
        var category_id = $(this).val(); 
        var brand_id = $('#brand').val(); 
        var id = $('#id').val(); 
        $('.total_weight_input').text('');
        $('.remaining_weight_input').text('');

        $('#update_data').val(1);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getproductlisting',
            data: { 'category_id': category_id,'id': id},
            success: function(msg){ 
                $(".packing").html(msg);
            }
        });

        var category_id = $("#category").val();
        var category_name = $("#category :selected").text().toLowerCase();  
        var category_name = category_name.trim();
        var l_to_kg = .910; 
        if(category_name=='vanaspati')
            var l_to_kg = .897;
        var w = '<?php echo $booking_info['quantity']; ?>'
        var total_weight_kg= (((w)*15)*l_to_kg);
        var mt =  total_weight_kg/1000;
        var mt_rond = mt.toFixed(2);
        $('.total_weight_ordered').text(mt_rond+' MT');
        $('#update_weight').val(mt_rond);

    });
    
    $(document).on("click", ".send_mail_plant", function(event){
        event.preventDefault();
        party_id = $(this).attr('rel');
        var mailremark = $("#mailremark").val();
        var production_untit = $('input[name="production_unit"]:checked').val();
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>booking/sendmail_plant_lock_bulk/'+party_id,
            data: {'reamrk' : mailremark,'production_untit' : production_untit}, 
            dataType: "html",
            success: function(data){ 
                //alert("Mail sent");
                $('.order_booked').text("Mail sent successfully.");
                $(".close_btn").hide();
                $(".homaepagelink").show();
                $("#LockMailModal").modal('hide');
                $("#BookingSuccessModal").modal('show');
            }
        });
    });
    $(document).on("submit", "#addskus", function(event){
        event.preventDefault();
        $("#flag").val(0);
        var activeElement  = $(document.activeElement).val(); 
        if(activeElement=='Lock & Mail Order'){
            $('.lock_order').attr('disabled', 'disabled');
            $("#flag").val(1);
        }
        var party_id = $("#party_id").val(); 
        var id = $("#id").val(); 

        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>booking/add_skus/',
            data: $("#addskus").serializeArray(), 
            dataType: "html",
            success: function(data){ 
                if(data!=0)
                { 
                    $('.order_booked').text("SKUs Added successfully.");
                    if(activeElement=='Lock & Mail Order'){ 
                        
                        $.ajax({
                            type: "POST",
                            url: '<?php echo base_url();?>booking/getbargainweight/',
                            data: {'party_id':party_id}, 
                            dataType: "html",
                            success: function(weight_bargains){ 
                                //$('.LockMailModal_Weight').tetx(weight+" MT");
                                var weight_bargain_array =  weight_bargains.split("__");
                                var weight = weight_bargain_array[0];
                                var bargains = weight_bargain_array[1];
                                var pop_content;
                                pop_content = "<p>Total Locked Bargains: <strong>"+weight+" MT</strong></p>";
                                if(parseFloat(weight)<9)
                                {
                                    pop_content = pop_content+"<p>Your locked bargain is still less than Truck load.</p>";
                                    if(bargains>1)
                                    {
                                        pop_content = pop_content+"<p>Add more SKU in pending bargains.</p>";
                                        pop_content = pop_content+"<p><a class='btn btn-warning' href='<?php echo base_url(); ?>booking'>Add SKU</a></p>";

                                    }
                                }
                                else
                                {
                                   if(bargains>1)
                                   {
                                        pop_content = pop_content+"<br> Do you want to add more SKU in another bargin before sending for dispatch to plant. ";
                                        pop_content = pop_content+"<br><a href='<?php echo base_url(); ?>booking'>Add SKU</a>";
                                    }
                                }
                                $(".LockMailModalContent").html(pop_content);
                              $("#LockMailModal").modal('show');  
                            }
                        });
                        $('.send_mail_plant').attr('rel',party_id);
                        $(".homaepagelink").show();
                        $(".close_btn").hide();
                    }
                    else
                    {
                        $(".close_btn").show();
                        $(".homaepagelink").hide();
                        $("#BookingSuccessModal").modal('show');
                    }
                    
                }
                else
                {
                    $('.order_booked').text("Something went wrong");
                    $("#BookingSuccessModal").modal('show');
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
        $("#broker").select2(); 
    });
    $(document).ready(function() {
        var total_weight_ordered = '<?php echo $booking_info['total_weight']; ?>'; 
        total_weight_input = 0;
        $('.packing_weight').each(function(i, obj) {                 
            if(this.value)
            {
                total_weight_input = total_weight_input+parseFloat(this.value);
            }
        }); 
        total_weight_input =  total_weight_input.toFixed(4);
        var remaining_weight = (total_weight_ordered-total_weight_input).toFixed(4);
        $('.total_weight_input').text(total_weight_input+' MT');
        var remaining_weight_kg = -((remaining_weight*1000).toFixed(2));
        var remaining_weight1 = -(parseFloat(remaining_weight));
        $('.remaining_weight_input').text(remaining_weight1+' MT ('+remaining_weight_kg+' Kg)');
      $(window).keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
      });
      
        $(document).on('blur', '.quantity_packed', function(){ 
            var category_id = $("#category").val();
            var category_name = $("#category :selected").text().toLowerCase(); 
            var category_name = category_name.trim(); 
            var booking_status = $("#booking_status").val();
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
                $(this).next().text(mt_t+' MT');
            }
            else
            {
                $(this).next().text('');
            }
            $(this).siblings('.packing_weight').val(mt);
            $(this).closest(".col-md-4").next().find(".packing_weight_input").val(mt);

            //$(this).next().focus();
            $('.packing_weight').each(function(i, obj) {  
                if(obj.value)
                {  
                    total_weight_input = total_weight_input+parseFloat(obj.value);
                }
            });  
            total_weight_input1 =  total_weight_input.toFixed(4); 
            $('.total_weight_input').text(total_weight_input1+' MT');
            var remaining_weight = (total_weight_ordered-total_weight_input1).toFixed(4);

            var remaining_weight_kg = -((remaining_weight*1000).toFixed(2));
            var remaining_weight1 = -(parseFloat(remaining_weight));
            $('.remaining_weight_input').text(remaining_weight1+' MT ('+remaining_weight_kg+' Kg)'); 



            var approx_total_weight_ordered_min = parseFloat(total_weight_ordered)-parseFloat(approx_weight);
            var approx_total_weight_ordered_max = parseFloat(total_weight_ordered)+parseFloat(approx_weight); 
            //alert(approx_total_weight_ordered_min+' - '+approx_total_weight_ordered_max+' - '+total_weight_input1); 
            $('.lock_order').attr('disabled', 'disabled');
            //alert(booking_status);
            if((parseFloat(total_weight_input1)>parseFloat(approx_total_weight_ordered_max)))
            { 
                this.value = 0;
                $(this).siblings('.packing_weight').val(0);
                $(this).closest(".col-md-4").next().find(".packing_weight_input").val(0);
                $(this).next().text('');
                total_weight_input = 0 ;
                alert("sorry You have exceeded the weight limit");
                $('.packing_weight').each(function(i, obj) {   
                        total_weight_input = total_weight_input+parseFloat(this.value);                    
                });
                total_weight_input1 =  total_weight_input.toFixed(4);
                $('.total_weight_input').text(total_weight_input1+' MT');
                var remaining_weight = (total_weight_ordered-total_weight_input1).toFixed(4);
                var remaining_weight_kg = -((remaining_weight*1000).toFixed(2));
                var remaining_weight1 = -(parseFloat(remaining_weight));
                $('.remaining_weight_input').text(remaining_weight1+' MT ('+remaining_weight_kg+' Kg)'); 
            }
            else
            { 
                if((parseFloat(total_weight_input1)<parseFloat(approx_total_weight_ordered_max) && parseFloat(total_weight_input1)>parseFloat(approx_total_weight_ordered_min))  && booking_status==2)
                {   
                    $('.lock_order').removeAttr('disabled');
                }   
            }
            $('#total_weight_input_id').val(total_weight_input1);
            $('#remaining_weight_id').val(remaining_weight);
        }); 
    });
</script>
<!-- Trigger the modal with a button -->
 
<div id="BookingSuccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Success</h4>
            </div>
            <div class="modal-body "> 
                    <div class="order_booked"></div>
                    <div class="row booked_sku_info"> 

                    </div>
            </div>
            <div class="modal-footer"> 
                <a style="display: none;" href="<?php echo base_url('booking'); ?>" class="homaepagelink btn btn-default">Close</a>
                <button type="submit" class="btn btn-default close_btn" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>
<div id="LockMailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body "> 
                <div class="LockMailModalContent"></div>
                <div class="form-group">
                        <label>Remark Form Dispatch Team / Plant</label>
                </div>
                <textarea name="remark" id="mailremark"  class="form-control" placeholder=" tentative dispatch date etc"> </textarea>
            </div>
            <div class="modal-footer"> 
                <a href="javascript:void(0)" class="btn btn-default send_mail_plant"  rel="" >Send Mail</a>
                <a style="display: none;" href="<?php echo base_url('booking'); ?>" class="homaepagelink btn btn-default">Close</a>
                <button type="submit" class="btn btn-default close_btn" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>