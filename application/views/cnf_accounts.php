<?php include 'header.php'; ?>
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1> 
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <div class="panel-heading" style="height: 65px;">
                <span class="title elipsis">
                    <strong><?php echo $title; ?></strong> <!-- panel title -->
                </span>  

                <span class="copy_from_rate1 btn btn-default" style="float: right; display: none;" >Auto Fill</span>
                 

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
                    <form action="<?php echo base_url('accounting'); ?>" class="" method="post" id="addbooking">
                    <div class="row">
                        <div class="col-md-12">                           
                                <div class="form-group">
                                    <label for="name">C&F Vendors </label> 
                                    <select class="form-control" id="vendor" name="vendor" required="">
                                        <option value="">Select C&F Vendor</option>
                                        <?php if($vendors) { 
                                            foreach ($vendors as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['vendor'])) { if($_POST['vendor']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('vendor'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="name">Year</label> 
                                    <select class="form-control" id="year" name="year" required="">
                                        <option value="">Select Year</option>
                                        <?php   $current_year = date('Y');
                                                $start_year = 2022; 
                                                for ($i=$start_year; $i <=$current_year ; $i++) { ?>
                                                   <option value="<?php echo $i; ?>" <?php if(isset($_POST['year'])) { if($_POST['year']==$i) { echo "selected"; } } ?> ><?php echo $i; ?></option>
                                                <?php } ?> 
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('year'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="name">Month</label> 
                                    <select class="form-control" id="month" name="month" required="">
                                        <option value="">Select Month</option>
                                        <?php   $month = 1;
                                                for ($i=$month; $i <=12 ; $i++) { ?>
                                                   <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>" <?php if(isset($_POST['month'])) { if($_POST['month']==$i) { echo "selected"; } } ?>><?php echo date("F",mktime(0, 0, 0, $i, 10)); ?></option>
                                                <?php } ?> 
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('month'); ?></span>
                                </div>   
                                <div class="sku_list">
                                </div>  
                        </div> 
                    </div>
                    <div class="row">                               
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label class="btn-block"></label>
                                <button type="submit" class="btn btn-default booking_submit" value="Search">Search History</button>  
                            </div>                                  
                        </div> 
                        <div class="col-md-4">
                        </div>                             
                    </div>
                    </form>
                    <?php if($skulists) { ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Purchased</label>
                                <?php echo $primary_pi['total_purchased_pi_amount']; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Sale</label>
                                <?php echo $secondary_pi['total_secondary_pi_amount']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Brand</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="name">Category</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Product</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="name">Closing Qty</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="name">Opening Qty</label>
                            </div>
                        </div>
                        
                    </div>
                    <div class="table-responsive"> 
                        <?php  $res = "" ; $i = 1;
                        foreach ($skulists as $key => $value) {   ?>             
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control" id="" name="brand[]">';
                                            <option value="<?php echo $value['brand_id']; ?>"><?php echo $value['brand_name']; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select class="form-control" id="" name="category[]">
                                            <option value="<?php echo  $value['category_id']; ?>"><?php echo $value['category_name']; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control product_packing" id="" name="product[]">
                                            <?php $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty']; ?>
                                            <option value=""><?php echo  $value['name'].$packing_items_qty;?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group"> 
                                        <input type="text" class="form-control rate" id="" name="rate[]"  value="<?php echo $value['closing_qty']; ?>">
                                        <span class="rate_date" style="color:green;">
                                            Buy : <?php echo $value['buy_qty']; ?> (<?php echo $value['buy_weight']; ?>MT)</span><br>
                                        <span class="rate_date" style="color:red;">Sale : <?php echo $value['sale_qty']; ?> (<?php echo $value['sale_weight']; ?>MT)</span>
                                         
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group"> 
                                        <input type="text" class="form-control rate" id="" name="rate[]"  value="<?php echo $value['opening_qty']; ?>"> 
                                    </div>
                                </div>
                                
                            </div>
                            <?php $i++; }  ?>
                    </div>
                    <?php } ?>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        $("#vendor").select2();  
    });
    $(document).on('change', '#vendor1', function(){  
        var vendor_id = $(this).val();
        $(".copy_from_rate").hide();
        $(".comisson").hide();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>accounting/GetBookingSkus',
            data: { 'vendor_id': vendor_id},
            success: function(msg){
                $(".sku_list").html(msg);
                if(msg)
                {
                    $(".copy_from_rate").show();
                    $(".comisson").show();
                }
            }
        });   
    });
    $("[id='comisson']").keyup(function () { 
         if (this.value.match(/[^0-9.]/g, '')) { 
          this.value = this.value.replace(/[^0-9]/g, '');      
        } 
    });
    
    $(document).on('click', '.send_pdf', function(){  
        var vendor_id = $("#vendor").val(); 
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>rate/cnfratepdf/',
            data: { 'vendor_id': vendor_id},
            success: function(msg){ 
                if(msg==21)
                {
                    alert("PDF sent");
                }               
                else
                {
                   alert("try again"); 
                }
            }
        });   
    });

    $(document).on('click', '.copy_from_rate', function(){  
        var vendor_id = $("#vendor").val();
        var comisson = $("#comisson").val();
        var gst_precentage  = $("#tax").val(); 
        $("#gst_rate").val(gst_precentage);
        if(comisson=='')
        {
            alert("Please Enter Cost Per Ltr.");
            return false;
        } 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>rate/copy',
            data: { 'vendor_id': vendor_id,'comisson': comisson,'gst_precentage': gst_precentage},
            success: function(msg){
               $(".sku_list").html(msg);                
            }
        });   
    });
    $(document).on("submit", "#addcnfrate", function(event){
        event.preventDefault();         
        var vendor = $("#vendor").val();   
        $('.booking_submit').attr('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>cnfrate/add_rate/',
            data: $("#addcnfrate").serializeArray(), 
            dataType: "html",
            success: function(data){
                $(".booking_submit").removeAttr('disabled');
                if(data==1)
                {
                    alert("Rate Added Successfully");
                }
                else
                {
                    alert("Something went wrong try again");
                }
            }
        });
    });


    $(document).on("submit", "#add_master_rate", function(event){
        event.preventDefault();         
        var vendor = $("#vendor").val();   
        $('.calculate_rate').attr('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>cnfrate/add_master_rate/',
            data: $("#add_master_rate").serializeArray(), 
            dataType: "html",
            success: function(data){
                $(".calculate_rate").removeAttr('disabled');
                if(data==1)
                {
                    //alert("Master Rate Added Successfully");
                    var vendor_id = $("#vendor").val();
                    var comisson = $("#comisson").val();
                    var gst_precentage  = $("#tax").val(); 
                    $("#gst_rate").val(gst_precentage);
                    if(comisson=='')
                    {
                        alert("Please Enter Cost Per Ltr.");
                        return false;
                    } 
                    if(gst_precentage=='')
                    {
                        alert("Please Enter GST Percentage");
                        return false;
                    } 
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>rate/copy',
                        data: { 'vendor_id': vendor_id,'comisson': comisson,'gst_precentage': gst_precentage},
                        success: function(msg){
                           $(".updated_rate_sku").html(msg);                
                        }
                    }); 
                }
                else
                {
                    alert("Something went wrong try again");
                }
            }
        });
    });
});
</script>