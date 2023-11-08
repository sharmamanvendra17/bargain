<?php include APPPATH.'views/header.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script> 
<!--<script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>-->
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
            <div class="panel-heading" style="display:none;">
                <div class="row" style="display:none">

                        <div class="col-md-8">   
                            <span class="title elipsis">
                                <strong><?php echo $title; ?></strong> <!-- panel title -->
                            </span> 
                        </div>
                        <div class="col-md-4">
                            <span class="title elipsis header_add" >
                                <div class="form-group cal" style="margin-bottom:0px!important"> 
                                    <input class="form-control" type="text" name="bagainnumber" id="bagainnumber" value="<?php echo (isset($_POST['bagainnumber'])) ? $_POST['bagainnumber'] : ''; ?>" placeholder="Search By Purchase Number" />
                                </div>
                                <button type="submit" name="bargain_search" class="btn btn-default booking_submit " value="bargain_search">Go</button>  
                            </span>               

                        </div>

                </div>
             
                
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
                <?php } //echo "<pre>"; print_r($_POST); ?> 
                <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="booking_number" id="booking_number" value="0">                              
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">               
                                        <label for="name">Party Name</label> 
                                        <select class="form-control" id="party" name="party" >
                                            <option value="">Select Party</option>
                                            <?php if($users) { 
                                                foreach ($users as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('party'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product</label> 
                                        <select class="form-control" id="product" name="product" >
                                            <option value="">Select Product</option>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('product'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="booking_date_from">Inventory Date (From) </label>
                                        <input class="form-control" type="text" id="booking_date_from" name="booking_date_from"  value="<?php if(isset($_POST['booking_date_from']) && !empty($_POST['booking_date_from']) ) { echo $_POST['booking_date_from']; } else { echo date('d-m-Y'); } ?>" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="booking_date_to">Inventory Date (To)</label>
                                        <input class="form-control" type="hidden" id="booking_date_to" name="booking_date_to" value="<?php if(isset($_POST['booking_date_to']) && !empty($_POST['booking_date_to']) ) { echo $_POST['booking_date_to']; } else { echo date('d-m-Y'); } ?>"/>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="employee">Employee (Makers)</label>
                                        <?php if($logged_role==4 || $logged_role==2|| $logged_role==5) { ?>
                                        <select class="form-control" id="employee" name="employee" >
                                            <option value="">Select Employee</option>
                                            <?php if($employees) { 
                                                foreach ($employees as $key => $employee) { ?>
                                            <option value="<?php echo $employee['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$employee['id']) { echo "selected"; } }; ?>><?php echo $employee['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <?php } else { ?>
                                            <select class="form-control" id="employee" name="employee" style="display:none;">
                                                <option value="">Select Employee</option>
                                            </select>
                                        <?php }  ?>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="broker">Broker</label> 
                                        <?php if($logged_role==4 || $logged_role==2|| $logged_role==5) { ?>
                                        <select class="form-control" id="broker" name="broker" >
                                            <option value="">Select Broker</option>
                                            <?php if($brokers) { 
                                                foreach ($brokers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"  <?php if(isset($_POST['broker'])) { if($_POST['broker']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <?php } else { ?>
                                            <select class="form-control" id="broker" name="broker" style="display:none;">
                                                <option value="">Select Broker</option>
                                            </select>
                                        <?php }  ?> 
                                    </div>
                                </div>
                            </div>                              
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search Inventory</button>  
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
                        <?php if($inventories) {  ?>
                        <div class="phppot-container"> 
                            <h3>FFA Trend </h3>
                            <div>
                                <canvas id="line-chart" ></canvas>
                            </div>
                            <h3><?php echo ($product_id==9) ? 'Cloud Point' : 'Pungency'; ?> Trend </h3>
                            <div>
                                <canvas id="line-chart-pungency" ></canvas>
                            </div>
                            <h3>Color Trend </h3>
                            <div>
                                <canvas id="line-chart-color" ></canvas>
                            </div>                            
                        </div>
                        <?php } ?>
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

<?php

$datasets = array();
$data_values= array();
if($inventories) { 
    $j = 0; 
    foreach ($inventories as $key_parties => $value_parties) { 
        //$i = 1;
		$i = 0;
        if($value_parties)
        {
            $data_values = array();
			//$data_values[0]['x'] = $_POST['booking_date_from'];
			//$data_values[0]['y'] = 0;
            foreach ($value_parties as $key => $value) {
                   // $party_name = $value['supplier_name'];
                    $data_values[$i]['x']=date('d-m-Y', strtotime($value['created_at']));
                    $data_values[$i]['y']= ($value['lab_result_ffa']) ? $value['lab_result_ffa'] : 0;
                    $i++;
                    
            }
            $data_values_1 =  json_encode($data_values,JSON_UNESCAPED_SLASHES);
            $data_values_1 = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $data_values_1); 
            $datasets[$j]['data'] = $data_values_1;
            $datasets[$j]['label'] = $key_parties;
            $datasets[$j]['borderColor'] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            $datasets[$j]['fill'] = false; 
            $j++;
        }
    }    
} 
$datasets_json = json_encode($datasets,JSON_UNESCAPED_SLASHES);
$datasets_json = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $datasets_json);
$datasets_json = str_replace(']"', "]",(str_replace('"[', "[", $datasets_json))); 


/* pungency */
$datasets_pungency = array();
$data_values_pungency= array();
if($inventories) { 
    $j =0; 
    foreach ($inventories as $key_parties => $value_parties) { 
        $i = 0; 
        if($value_parties)
        {
             $data_values_pungency = array();
			 //$data_values_color[0]['x'] = $_POST['booking_date_from'];
			//$data_values_color[0]['y'] = 0;
            foreach ($value_parties as $key => $value) {
                   // $party_name = $value['supplier_name'];
                    $data_values_pungency[$i]['x']=date('d-m-Y', strtotime($value['created_at']));
                    $data_values_pungency[$i]['y']= ($value['lab_result_pungency']) ? $value['lab_result_pungency'] : 0;
                    $i++;
                    
            }
            $data_values_1_pungency =  json_encode($data_values_pungency,JSON_UNESCAPED_SLASHES);
            $data_values_1_pungency = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $data_values_1_pungency); 
            $datasets_pungency[$j]['data'] = $data_values_1_pungency;
            $datasets_pungency[$j]['label'] = $key_parties;
            $datasets_pungency[$j]['borderColor'] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            $datasets_pungency[$j]['fill'] = false; 
            $j++;
        }
    }    
} 
$datasets_pungency_json = json_encode($datasets_pungency,JSON_UNESCAPED_SLASHES);
$datasets_pungency_json = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $datasets_pungency_json);
$datasets_pungency_json = str_replace(']"', "]",(str_replace('"[', "[", $datasets_pungency_json)));


/* color */
$datasets_color = array();
$data_values_color= array();
$dates = array();
if($inventories) { 
    $j =0; 
    foreach ($inventories as $key_parties => $value_parties) { 
        $i = 0; 
        if($value_parties)
        {
            $data_values_color = array();
			//$data_values_color[0]['x'] = $_POST['booking_date_from'];;
			//$data_values_color[0]['y'] = 0;
            foreach ($value_parties as $key => $value) {
                    //$party_name = $value['supplier_name'];
                    $data_values_color[$i]['x']=date('d-m-Y', strtotime($value['created_at']));
                    $data_values_color[$i]['y']= ($value['lab_result_color']) ? $value['lab_result_color'] : 0;
                    $i++;
                    //$dates[] = $_POST['booking_date_from'];
                    $dates[] = date('d-m-Y', strtotime($value['created_at']));
                    
            }
            $data_values_1_color =  json_encode($data_values_color,JSON_UNESCAPED_SLASHES);
            $data_values_1_color = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $data_values_1_color); 
            $datasets_color[$j]['data'] = $data_values_1_color;
            $datasets_color[$j]['label'] = $key_parties;
            $datasets_color[$j]['borderColor'] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            $datasets_color[$j]['fill'] = false; 
            $j++;
        }
    }    
} 
$datasets_color_json = json_encode($datasets_color,JSON_UNESCAPED_SLASHES);
$datasets_color_json = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $datasets_color_json);
$datasets_color_json = str_replace(']"', "]",(str_replace('"[', "[", $datasets_color_json)));


?>

<?php asort($dates);
$dates  = array_values(array_unique($dates)); ?>




<script>
        const xValues1 = <?php echo json_encode($dates); ?>;
        new Chart(document.getElementById("line-chart"), {
            type : 'line', 
            data : {
                labels: xValues1, 
                datasets :  <?php echo stripcslashes($datasets_json); ?> 
            },
            options: {
                legend: {display: true},
                scales: { 
                  yAxes: [{ticks: {min: 0, max:3}}],
                }
            } 
        });

        new Chart(document.getElementById("line-chart-pungency"), {
            type : 'line',
            data : { 
                labels: xValues1, 
                datasets :  <?php echo stripcslashes($datasets_pungency_json); ?> 
            }, 
            options: {
                legend: {display: true},
                scales: { 
                  yAxes: [{ticks: {min: 0, max:1}}],
                }
            } 
        });
        new Chart(document.getElementById("line-chart-color"), {
            type : 'line',
            data : { 
                labels: xValues1, 
                datasets :  <?php echo stripcslashes($datasets_color_json); ?> 
            }, 
            options: {
                legend: {display: true},
                scales: { 
                  yAxes: [{ticks: {min: 10, max:50}}],
                }
            } 
        });
    </script>
<?php include APPPATH.'views/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#booking_date_from,#booking_date_to").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
    var category_id = $("#category").val();  
    var product_id = '<?php echo $product_id; ?>';  
    if(category_id.trim()!='')
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>purchase/purchaseproduct/getproductlist',
            data: { 'category_id': category_id},
            success: function(msg){ 
                $("#product").html(msg);
                $("#product").val(product_id);
            }
        });
    }
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
                $("#product").val(product_id);
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

    $('.updtae_purchase_form_status').change(function(){
        var status_value = $( '.updtae_purchase_form_status:checked' ).val();  
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
         var status_value = $('.updtae_purchase_form_status:checked').val(); 
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
                <input type="radio" name="status" class="updtae_purchase_form_status" value="1" checked> Reject
                <input type="radio" name="status" class="updtae_purchase_form_status"  value="2"> Cancel
                <input type="radio" name="status" class="updtae_purchase_form_status"  value="4"> Approve
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
