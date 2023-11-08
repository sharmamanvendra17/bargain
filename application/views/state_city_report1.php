<?php include 'header.php'; //echo "<pre>"; print_r($_POST); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <!--<div class="panel-heading">
                <div class="row">
                    <div class="col-md-8">   
                        <span class="title elipsis">
                            <strong><?php echo $title; ?></strong> 
                        </span> 
                    </div>
                    <div class="col-md-4">    
                    </div>
                </div>            
            </div> -->
            <div class="panel-body">
                    <form action="" class="" method="post" id="">
                        <div class="row"> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">State</label> 
                                    <select class="form-control" id="state" name="state" required>
                                        <option value="">Select State</option>
                                        <?php if($states) {
                                            foreach ($states as $key => $value) {  ?>
                                                <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['state'])) { if($_POST['state']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('state'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3" >                           
                                <div class="form-group">
                                    <label for="name">City</label> 
                                    <select class="form-control" id="city" name="city">
                                        <option value="">Select City</option>
                                    </select> 
                                    <span class="txt-danger"><?php echo form_error('city'); ?></span>
                                </div>
                            </div>   
                            <div class="col-md-3">
                                <div class="form-group"> 
                                    <label for="rate">Date (From) </label>
                                    <input class="form-control" type="text" id="booking_date_from" name="from_date"  value="<?php if(isset($_POST['from_date']) && !empty($_POST['from_date']) ) { echo $_POST['from_date']; } else { echo date('d-m-Y'); } ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group"> 
                                    <label for="rate">Date (To)</label>
                                    <input class="form-control" type="hidden" id="booking_date_to" name="to_date" value="<?php if(isset($_POST['to_date']) && !empty($_POST['to_date']) ) { echo $_POST['to_date']; } else { echo date('d-m-Y'); } ?>"/>
                                </div>
                            </div>                          
                        </div>                         
                        <div class="row" >
                            <div class="col-md-8">
                                <div class="form-group"> 
                                    <label for="rate">Type</label>
                                    <input class="" type="radio" id="" name="report_type"  value="party"   <?php echo ( (!isset($_POST['report_type']) || $_POST['report_type']=='') || (isset($_POST['report_type']) && $_POST['report_type']=='party'))  ? 'checked' : ''; ?> />Party Wise
                                    <input class="" type="radio" id="" name="report_type"  value="employee" <?php echo (isset($_POST['report_type']) && $_POST['report_type']=='employee') ? 'checked' : ''; ?> />Team Wise
                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                 
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
                        <?php $parties_array = array(); if($result_type=='party') { ?>
                        <div class="row">
                            <div class="col-md-12">  
                                <?php   if( $results ) { ?>
                                <nav class='animated saleAccordion bounceInDown'> 
                                    <ul>
                                        <?php  $party_no = 1; if($results) { 
                                            foreach ($results as $key => $value) {
                                                $parties_array[] = $value['party_id'];
                                            $detailed_res = $value['detailed_res']; ?>
                                        <li class='next-sub-menu'><a href='#settings'><span class="accordionHeader"><?php echo $party_no.'. '.$value['party_name']; ?> -  <?php echo $value['city_name']; ?></span><div class='fa fa-caret-down right'></div><span class="text-right" style="float: right;padding-right: 25px;"><?php echo round($value['total_dispatch'],2); ?> (MT)</span></a>
                                                <ul>
                                                    <li>
                                                        <div class="table-responsive"> 
                                                            <table class="table accordionTable table-striped table-bordered table-hover ">
                                                                <thead>
                                                                  <tr>
                                                                    <th>S.No.</th>
                                                                    <th>Brand Name</th>
                                                                    <th>Category Name</th> 
                                                                    <th>Dispatched (MT)</th> 
                                                                  </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                                                        $sno = 1;
                                                                        foreach ($detailed_res as $key => $detailed_value) { ?>
                                                                    <tr> 
                                                                        <td class="text-center"><?php echo $sno; ?></td>
                                                                        <td class="text-center"><?php echo $detailed_value['brand_name'] ?></td>
                                                                        <td class="text-center"><?php echo $detailed_value['category_name'] ?></td>        
                                                                        <td class="text-center"><?php echo round($detailed_value['total_dispatch'],2) ?></td>
                                                                    </tr>
                                                                    <?php $sno++; } ?>
                                                                </tbody>
                                                            </table>   
                                                        </div>
                                                    </li>
                                                </ul>
                                        </li>
                                        <?php $party_no++; } } ?>
                                    </ul> 
                                </nav>
                                <?php }  ?>
                            </div> 
                            <div class="col-md-12">
                            <?php if(isset($_POST['state']) && !empty($_POST['state'])) { 
                                $parties = implode(',', $parties_array); ?>
                                <button rel="<?php echo $parties; ?>" type="button" class="btn btn-default show_all_parties" value="Show All Parties">Show All Parties</button>
                            <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </form>
                        <?php if($result_type=='employee') { ?>
                        <div class="row">
                            <div class="col-md-12">  
                                <?php  $sn = 1; if( $results ) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td>S.No.</td> 
                                                <td>Name</td> 
                                                <td>Mobile Number</td> 
                                                <td>Dispatch</td>  
                                            </tr>
                                            <?php foreach ($results as $key => $value) { ?>
                                            <tr>
                                                <td><?php echo $sn; ?></td> 
                                                <td>
                                                    <?php echo ($value['status']) ? $value['name'] : '<span class="txt-danger">'.$value['name'].'</span>'; echo ($value['role']==1) ? ' (Maker)' : ' (Secondary)'; ?> 
                                                    <?php if($value['joining_date']) { ?>-<span class="txt-danger"><strong> <?php echo $value['joining_month']; ?></strong></span>
                                                    <?php } ?>
                                                </td> 
                                                <td><?php echo $value['mobile']; ?></td> 
                                                <td><?php echo ($value['total_dispateched_weight']) ? round($value['total_dispateched_weight'],2) : round($value['total_dispateched_weight1'],2);  ?></td>  
                                            </tr>
                                            <?php $sn++; } ?>
                                        </table>
                                    </div>
                                <?php }  ?>
                            </div> 
                            <div class="col-md-12">
                            <?php if(isset($_POST['state']) && !empty($_POST['state'])) { 
                                $parties = implode(',', $parties_array); ?>
                                <button rel="<?php echo $parties; ?>" type="button" class="btn btn-default show_all_parties" value="Show All Parties">Show All Parties</button>
                            <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    <form> 
                        <div class="row">
                            <div class="col-md-12 all_party_response">
                            </div>
                        </div>
                    </form>                    
            </div>
                <!--
                <div>
                    <span><strong>Total Ordered Weight : 0 in Kg (0 In Ton)</strong></span>
                </div>-->
        </div>
    </div>
</section>
<?php  include 'footer.php';   ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){    
    
    var state_id = $('#state').val(); 
    if(state_id!='')
    {
        var city_id = '<?php echo $_POST['city']; ?>';
        var state_id = $('#state').val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>vendors/getcity',
            data: { 'state_id': state_id},
            success: function(msg){
                $("#city").html(msg);
                $("select#city option").each(function(){
                    if ($(this).val() == city_id)
                        $(this).attr("selected","selected");
                });
            }
        });
        $("#city").val(city_id);
    }
    $("#city,#state").select2();
    $("#booking_date_from,#booking_date_to").flatpickr({  
        dateFormat: "d-m-Y",
    }); 
    $("#state").change(function(){
        var state_id = $(this).val();  
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>vendors/getcity',
            data: { 'state_id': state_id},
            success: function(msg){ 
                $("#city").html(msg);
            }
        });
    });
    $(document).on('click', '.show_all_parties', function(){
        var state_id = $('#state').val(); 
        var city_id = $('#city').val(); 
        var parties = $(this).attr('rel');  
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>booking/getallcompnies',
            data: { 'state': state_id,'city': city_id,'parties': parties},
            success: function(response){  
                //alert(response);
                $('.all_party_response').html(response);
            }
        });
    });
});
</script>



 

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
