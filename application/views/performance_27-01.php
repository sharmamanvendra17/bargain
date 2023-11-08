<?php include 'header.php'; ?>
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
             <div class="panel-heading">
                <div class="row">

                        <div class="col-md-8">   
                            <span class="title elipsis">
                                <strong><?php echo $title; ?></strong> 
                            </span> 
                        </div>
                        <div class="col-md-4">
                            <span class="title elipsis header_add" style="display:none;">
                                <div class="form-group cal" style="margin-bottom:0px!important"> 
                                    <div class="controls input-append date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input1" data-link-format="yyyy-mm-dd">
                                        <input size="16" type="text" value="" class="form-control calc_b" readonly="">
                                        <span class="add-on"><i class="icon-remove"></i></span>
                                        <span class="add-on"><i class="icon-th"></i></span>
                                    </div>
                                    <input type="hidden" name="booking_date" id="dtp_input1" value=""><br>
                                </div> &nbsp;&nbsp;
                                <a href="javascript:void(0)" class="reset btn">Reset</a>
                            </span>               

                        </div>

                </div>
             
                
            </div>
            <div class="panel-body">
                    <form action="" class="" method="post" id="addbooking">
                        <div class="row">  
                            <div class="col-md-4">                           
                                <div class="form-group">
                                    <label for="name">Emplyee </label> 
                                    <select class="form-control" id="employee" name="employee" >
                                        <option value="">Select  Employee</option>
                                        <?php if($users) { 
                                            foreach ($users as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Year</label> 
                                    <select class="form-control" id="year" name="year" required="">
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
                                    <select class="form-control" id="month" name="month" required="">
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
                                    <label class="btn-block"></label>
                                    <button type="submit" class="btn btn-default booking_submit" value="Search">Search History</button>  
                                </div>                                  
                            </div> 
                            <div class="col-md-4">
                            </div>                             
                        </div>
                        </form>               
                <div class="row">
                    <div class="col-md-12">  
                      

                        <nav class='animated saleAccordion bounceInDown'>
                            <ul>
                                <?php if($response) {
                                foreach ($response as $key => $value) { 
                                    $state_info = $value['state'];
                                    $maker_info = $value['makers'];
                                    $secondry_maker_info = $value['secondry_makers'];
                                    ?>
                                <li class='sub-menu'><a href='#settings'><?php echo $state_info['name']; ?><div class='fa fa-caret-down right'></div></a>
                                    <ul>
                                        <li class='next-sub-menu'><a href='#settings'>Makers <?php if( count($maker_info)) { ?><div class='fa fa-caret-down right'></div><?php } ?> <span class="text-right" style="float: right;padding-right: 25px;"><?php echo makerstotalerformance($state_info['id'],$postdata); ?></span></a>
                                                <ul>
                                                    <li>
                                                        <div class="table-responsive">
                                                        <table class="table accordionTable table-striped table-bordered table-hover ">
                                                            <thead>
                                                              <tr>
                                                                <th rowspan="2">Employee Name</th>
                                                                <th  colspan="2">Bargain Booked</th>
                                                                <th  colspan="2">Montly Effort</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <tr>
                                                                <th></th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Sales(%)</th>
                                                                <th>Visit</th>
                                                              </tr>
                                                            <?php if($maker_info) { 
                                                            foreach ($maker_info as $key => $maker_info_value) { ?> 
                                                                <tr>
                                                                    <td><?php echo $maker_info_value['name']; ?></td>
                                                                    <td><?php echo ($maker_info_value['total_weight']) ? $maker_info_value['total_weight'] : 0;;?></td>
                                                                    <td><?php echo number_format($maker_info_value['total_amount'],2);?></td>
                                                                    <td>-</td>
                                                                    <td>-</td>
                                                                </tr> 
                                                            <?php } } ?>
                                                            </tbody>
                                                        </table>
                                                        </div>
                                                    </li>   
                                                </ul>
                                        </li> 
                                        <li class='next-sub-menu'> <a href='#settings'>Secondary Makers <?php if( count($secondry_maker_info)) { ?><div class='fa fa-caret-down right'></div><?php  } ?><span class="text-right" style="float: right;padding-right: 25px;"><?php echo secondarymakerstotalerformance($state_info['id'],$postdata); ?></span></a>
                                                <?php if($secondry_maker_info) { ?>
                                                <ul>
                                                    <li>
                                                        <div class="table-responsive">
                                                        <table class="table accordionTable table-striped table-bordered table-hover ">
                                                            <thead>
                                                              <tr>
                                                                <th rowspan="2">Employee Name</th>
                                                                <th  colspan="2">Montly Effort</th>
                                                                <th  colspan="2">Montly Effort</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <tr>
                                                                <th></th>
                                                                <th>Ton</th>
                                                                <th>Amount</th>
                                                                <th>Sales(%)</th>
                                                                <th>Visit</th>
                                                              </tr>
                                                            <?php if($secondry_maker_info) { 
                                                            foreach ($secondry_maker_info as $key => $secondry_maker_value) { ?> 
                                                                <tr>
                                                                    <td><?php echo $secondry_maker_value['name']; ?></td>
                                                                    <td><?php echo ($secondry_maker_value['total_weight']) ? $secondry_maker_value['total_weight'] : 0;;?></td>
                                                                    <td><?php echo number_format($secondry_maker_value['total_amount'],2);?></td>
                                                                    <td>-</td>
                                                                    <td>-</td>
                                                                </tr> 
                                                            <?php } } ?>
                                                            </tbody>
                                                        </table>
                                                        </div>
                                                    </li>   
                                                </ul> 
                                                <?php } ?>
                                        </li>
                                    </ul>
                                </li>
                                <?php  } } ?> 
                            </ul>
                        </nav>
                    </div>                    
                </div>                   
                </div>
                <!--
                <div>
                    <span><strong>Total Ordered Weight : 0 in Kg (0 In Ton)</strong></span>
                </div>-->
                 
            </div>
        </div>
</section>
<?php include 'footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        $("#employee").select2();  
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
});
</script>





<script type="text/javascript" src="https://sales.datagroup.in/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<script>
$('.sub-menu ul').hide();
$(".sub-menu > a").click(function () {
    
    if($(this).parent(".sub-menu").children("ul").css('display')=='block'){
        $(this).parent(".sub-menu").children("ul").css('display', 'block');
        $(this).find(".right").addClass("fa-caret-up");        
    }
    else{
        $('.sub-menu > a').parent(".sub-menu").children("ul").css('display', 'none');
        $('.sub-menu > a').removeClass("subMenuActive");
        $('.sub-menu > a').find(".right").removeClass("fa-caret-up");
        $('.sub-menu > a').find(".right").addClass("fa-caret-down");

    }
    
    $(this).parent(".sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActive");
    $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
});


$('.next-sub-menu ul').hide();
$(".next-sub-menu > a").click(function () {
    
    if($(this).parent(".next-sub-menu").children("ul").css('display')=='block'){
        $(this).parent(".next-sub-menu").children("ul").css('display', 'block');
        $(this).find(".right").addClass("fa-caret-up");
        
    }
    else{
        $('.next-sub-menu > a').parent(".next-sub-menu").children("ul").css('display', 'none');
        $('.next-sub-menu > a').removeClass("subMenuActive");
        $('.next-sub-menu > a').find(".right").removeClass("fa-caret-up");
        $('.next-sub-menu > a').find(".right").addClass("fa-caret-down");

    }
    
    $(this).parent(".next-sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActive");
    $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
});

$('.next-in-sub-menu ul').hide();
$(".next-in-sub-menu > a").click(function () {
    
    if($(this).parent(".next-in-sub-menu").children("ul").css('display')=='block'){
        $(this).parent(".next-in-sub-menu").children("ul").css('display', 'block');
        $(this).find(".right").addClass("fa-caret-up");
        
    }
    else{
        $('.next-in-sub-menu > a').parent(".next-in-sub-menu").children("ul").css('display', 'none');
        $('.next-in-sub-menu > a').removeClass("subMenuActive");
        $('.next-in-sub-menu > a').find(".right").removeClass("fa-caret-up");
        $('.next-in-sub-menu > a').find(".right").addClass("fa-caret-down");

    }
    
    $(this).parent(".next-in-sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActive");
    $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
});




/*$('.next-sub-menu ul').hide();
$(".next-sub-menu a").click(function () {
    $(this).parent(".next-sub-menu").children("ul").slideToggle("100");
    $(this).toggleClass("subMenuActiveNext");   
    $(this).find(".right").toggleClass("fa-caret-down");
});
*/



</script>
