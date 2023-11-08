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
                    <form action="" class="" method="post" id="addbooking">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rate">Vendor Type</label> 
                                <input class="party_type" type="radio" id="" name="cnf"  value="1" <?php echo ((isset($_POST['cnf']) && $_POST['cnf']=='1') || !isset($_POST['cnf'])) ? 'checked' : ''; ?> />C&F
                                <input class="party_type" type="radio" id="" name="cnf"  value="0" <?php echo (isset($_POST['cnf']) && $_POST['cnf']=='0') ? 'checked' : ''; ?> />Non C&F
                            </div>
                        </div> 
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>  
                    </div>
                    <div class="row">
                        <div class="col-md-4">                           
                            <div class="form-group">
                                <label for="name">Vendors </label> 
                                <select class="form-control" id="vendor" name="vendor" required>
                                    <option value="">Select  Vendor</option>
                                    <?php if($vendors) { 
                                        foreach ($vendors as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['vendor'])) { if($_POST['vendor']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                    <?php } } ?>
                                </select>
                                <span class="txt-danger"><?php echo form_error('vendor'); ?></span>
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Year</label> 
                                <select class="form-control" id="year" name="year" required>
                                    <option value="">Select Year</option>
                                    <?php   $current_year = date('Y');
                                            $start_year = 2022; 
                                            for ($i=$start_year; $i <=$current_year ; $i++) { ?>
                                               <option value="<?php echo $i; ?>" <?php if((isset($_POST['year']) && $_POST['year']==$i) || (!isset($_POST['year']) && $current_year==$i)) {  echo "selected"; }  ?> ><?php echo $i; ?></option>
                                            <?php } ?> 
                                </select>
                                <span class="txt-danger"><?php echo form_error('year'); ?></span>
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Month</label> 
                                <select class="form-control" id="month" name="month" required>
                                    <option value="">Select Month</option>
                                    <?php   $month = 1;
                                            $current_month = date('m');
                                            for ($i=$month; $i <=12 ; $i++) { ?>
                                               <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>" <?php if((isset($_POST['month']) && $_POST['month']==$i) || (!isset($_POST['month']) && $current_month==$i)) {  echo "selected"; }  ?>><?php echo date("F",mktime(0, 0, 0, $i, 10)); ?></option>
                                            <?php } ?> 
                                </select>
                                <span class="txt-danger"><?php echo form_error('month'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rate">Status</label> 
                                <input class="" type="radio" id="" name="status"  value="amount" <?php echo ((isset($_POST['status']) && $_POST['status']=='amount') || !isset($_POST['status'])) ? 'checked' : ''; ?> />Amount
                                <input class="" type="radio" id="" name="status"  value="sku" <?php echo (isset($_POST['status']) && $_POST['status']=='sku') ? 'checked' : ''; ?> />SKU
                            </div>
                        </div> 
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>  
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
                        <?php if($_POST['status']=='amount') { ?>
                        <div class="row text-right">
                            <div class="col-md-12"> 
                                <span style="color: red;">Note : Amount shown are without GST</span> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"> 
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Brand</th>
                                            <th>Product</th> 
                                            <th>Opening (1<sup>st</sup> <?php echo date("F",mktime(0, 0, 0, $_POST['month'], 10)); ?>) </th>
                                            <th>PIs</th>
                                            <th>Secondory</th>
                                            <th>Closing</th>                                     
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sno = 1;
                                        $total_opening = 0;
                                        $total_bargains = 0;
                                        $total_secondary = 0;
                                        $total_closing = 0;
                                        foreach ($skulists as $key => $value) { ?>
                                        <tr>
                                            <td><?php echo $sno; ?></td>
                                            <td><?php echo $value['brand_name']; ?></td>
                                            <td><?php echo $value['category_name']; ?></td>  
                                            <td><?php echo number_format($value['opening_amount'],2); ?></td>
                                            <td><?php echo number_format($value['bargain_amount'],2); ?></td>
                                            <td><?php echo  number_format($value['secondary_amount'],2); ?></td>
                                            <td><?php //echo ($value['opening_weight']+$value['buy_weight'])-$value['sale_weight']; ?> 
                                            <?php echo  number_format($value['closing_amount'],2); ?> </td>  

                                        </tr>
                                        <?php $sno++; 
                                        $total_opening = $total_opening+$value['opening_amount'];
                                        $total_bargains = $total_bargains+$value['bargain_amount'];
                                        $total_secondary = $total_secondary+$value['secondary_amount'];
                                        $total_closing = $total_closing+$value['closing_amount'];                              
                                        } ?>
                                    </tbody>
                                </table>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr> 
                                            <th>Total Opening</th>
                                            <th>Total PIs</th>
                                            <th>Total Secondory</th>
                                            <th>Total Closing</th>                                     
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo number_format($total_opening,2); ?></td>
                                            <td><?php echo number_format($total_bargains,2); ?></td>
                                            <td><?php echo number_format($total_secondary,2); ?></td>
                                            <td><?php echo number_format($total_closing,2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if($_POST['status']=='sku') { ?>
                        <div class="row">
                            <div class="col-md-12"> 
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Brand</th>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Opening (MT) (1<sup>st</sup> <?php echo date("F",mktime(0, 0, 0, $_POST['month'], 10)); ?>) <br><span style="color:red">Amount</span></th>
                                            <th>PIs (MT) <br><span style="color:red">Amount</span></th>
                                            <th>Secondory (MT) <br><span style="color:red">Amount</span></th>
                                            <th>Closing (MT) <br><span style="color:red">Amount</span></th>                                     
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $sno = 1;
                                        $total_opening = 0;
                                        $total_bargains = 0;
                                        $total_secondary = 0;
                                        $total_closing = 0; 
                                        foreach ($skulists as $key => $value) { ?>
                                        <tr>
                                            <td><?php echo $sno; ?></td>
                                            <td><?php echo $value['brand_name']; ?></td>
                                            <td><?php echo $value['category_name']; ?></td>
                                            <td><?php echo $value['name']; echo ($value['packing_items_qty']>1) ? '*'.$value['packing_items_qty'] : ''; ?></td>
                                            <td><?php echo $value['opening_weight'].' ('.$value['opening_qty'].')'; ?> <?php echo ' <br><span style="color:red">'.number_format($value['opening_amount'],2); ?></span></td>
                                            <td><?php echo $value['buy_weight'].' ('.$value['buy_qty'].')'; ?>   <?php echo ' <br><span style="color:red">'.number_format($value['bargain_amount'],2); ?></span></td>
                                            <td><?php echo  $value['sale_weight'].' ('.$value['sale_qty'].')'; ?> <?php echo ' <br><span style="color:red">'.number_format($value['secondary_amount'],2); ?></span></td>
                                            <td><?php //echo ($value['opening_weight']+$value['buy_weight'])-$value['sale_weight']; ?> 
                                            <?php $cl_amount = ($value['secondary_amount']-($value['opening_amount']+$value['bargain_amount'])); ?>
                                            <?php if($value['closing_qty']) { ?>
                                                <a href="javascript:void(0)" class="getstockdetail" data-party="<?php echo $_POST['vendor']; ?>" data-party="<?php echo $_POST['vendor']; ?>" data-product-info="<?php echo $value['brand_name']." ".$value['category_name']." ".$value['name']; ?>" data-party="<?php echo $_POST['vendor']; ?>" data-product="<?php echo $value['product_id']; ?>" data-closing_qty="<?php echo $value['closing_qty']; ?>"> 
                                            <?php } ?>

                                            <?php echo round($value['closing_weight'],3).' ('.$value['closing_qty'].')'; ?> <?php 
                                                if($cl_amount<0)
                                                  echo ' <br><span style="color:red">'.number_format($cl_amount,2).'</span>'; 
                                                else
                                                    echo ' <br><span style="color:green">'.number_format($cl_amount,2).'</span>';
                                            ?>

                                            <?php if($value['closing_qty']) { ?>
                                                </a>
                                            <?php } ?>
                                        </td>  
                                        </tr>
                                        <?php $sno++; 
                                        $total_opening = $total_opening+$value['opening_weight'];
                                        $total_bargains = $total_bargains+$value['buy_weight'];;
                                        $total_secondary = $total_secondary+$value['sale_weight'];;
                                        $total_closing = $total_closing+$value['closing_weight'];;                                
                                        } ?>
                                    </tbody>
                                </table>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Total Opening (MT)</th>
                                            <th>Total PIs (MT)</th>
                                            <th>Total Secondory Bargains (MT)</th>
                                            <th>Total Closing (MT)</th>                                     
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo $total_opening; ?> </td>
                                            <td><?php echo $total_bargains; ?> </td>
                                            <td><?php echo $total_secondary; ?> </td>
                                            <td><?php echo $total_closing; ?> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                        <?php } ?>
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
    
    $(document).on('change', '.party_type', function(){  
        var party_type = $('input[name="cnf"]:checked').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>accounting/party_list',
            data: { 'party_type': party_type},
            success: function(msg){
                $("#vendor").html(msg);
                $("#vendor").select2();  
            }
        });
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



    $(document).on("click", ".getstockdetail", function(event){
        event.preventDefault();         
        var party = $(this).attr('data-party');  
        var product = $(this).attr('data-product');   
        var closing_qty = $(this).attr('data-closing_qty');
        var product_info = $(this).attr('data-product-info');
        
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>accounting/getdispatchlist/'+party+'/'+product+'/'+closing_qty+'/',
            data: {}, 
            dataType: "html",
            success: function(data){
                $(".stockresult").html(data);
                $(".product_info_header").html(product_info);
                $("#Stockdetailmodal").modal('show');
            }
        });
    });
});
</script>



<!-- Modal -->
<div id="Stockdetailmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <strong><span class="product_info_header"></span></strong>
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
                <div class="stockresult">
                     
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