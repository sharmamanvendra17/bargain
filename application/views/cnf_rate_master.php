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
                <div class="row">
                        <div class="col-md-12">
                            
                                <div class="form-group">
                                    <label for="name">C&F Vendors </label> 
                                    <select class="form-control" id="vendor" name="vendor" required="">
                                        <option value="">Select C&F Vendor</option>
                                        <?php if($vendors) { 
                                            foreach ($vendors as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['vendor'])) { if($_POST['vendor']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('brand'); ?></span>
                                </div>   
                                <div class="sku_list">
                                </div> 
                            </form>
                        </div>
                    </div>
                <div class="table-responsive"> 
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
<div id="divLoading" style="display: none;"><span>Please wait....</span></div>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        $("#vendor").select2();  
    });

    $("body").on("click",".whatapp",function(e){ 
            e.preventDefault();
            var vendor_id = $("#vendor").val();  
            $("#divLoading").css({display: "block"});
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>cnfrate/whatsappusers',
                data: {'vendor_id' :vendor_id},
                success: function(msg){ 
                    $("#divLoading").css({display: "none"});
                    if(msg==0)
                    {
                        alert("Please Enter Dispatch Details");
                    }
                    else
                    {
                        $(".userdetails").html(msg);
                        $("#WhatappDetailModal").modal('show');                        
                    }
                }
            });
        });
    $(document).on('change', '#vendor', function(){  
        var vendor_id = $(this).val();
        $(".copy_from_rate").hide();
        $(".comisson").hide();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>cnfrate/GetBookingSkus',
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


    $("body").on("click",".whatsapppdf",function(e){ 
            //console.log($('input[name="users"]:checked').serialize());
            e.preventDefault();
            var numbers = Array();
            var vendor_id = $("#vendor").val();    
            //alert(JSON.stringify($("input:checkbox[name=users]:checked")));
            var flag = 0;
            $("input[name='users']:checked").each(function(){ 
                flag = 1;
                numbers.push($(this).val());
            });
            if(flag==0)
            {
                alert("Please select at least one mobile number.");
                return false;
            }
            //alert(numbers); 
            $("#divLoading").css({display: "block"});
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>rate/cnfratepdfwhatsapp/',
                data: {'numbers' : numbers,'vendor_id' : vendor_id},
                success: function(msg){ 
                    $("#divLoading").css({display: "none"});
                    // $("#DetailModal").modal('hide'); 
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
        $("#divLoading").show();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>rate/copy',
            data: { 'vendor_id': vendor_id,'comisson': comisson,'gst_precentage': gst_precentage},
            success: function(msg){
                $("#divLoading").hide();
               $(".sku_list").html(msg);                
            }
        });   
    });
    $(document).on("submit", "#addcnfrate", function(event){
        event.preventDefault();    
        $("#divLoading").show();     
        var vendor = $("#vendor").val();   
        $('.booking_submit').attr('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>cnfrate/add_rate/',
            data: $("#addcnfrate").serializeArray(), 
            dataType: "html",
            success: function(data){
                $("#divLoading").hide();
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
        $("#divLoading").show();  
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
                            $("#divLoading").hide();
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


<div id="WhatappDetailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
                <h4 class="modal-title">Notify CNF rate on whatsapp</h4>
            </div>
            <div class="modal-body"> 
                <div class="table-responsive userdetails">
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)"  class="whatsapppdf btn btn-default">Send On Whatsapp</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
  </div>
</div>