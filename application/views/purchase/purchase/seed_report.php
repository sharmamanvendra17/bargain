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
                        <div class="phppot-container"> 
                            <h3>Seed Rate Per MT Per Percentage</h3>
                            <div>
                                <canvas id="line-chart" ></canvas>
                            </div> 
                        </div>
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
$party_xaxis = array();
$value_p_index = 0;
$dates =array();
//echo "<pre>"; print_r($inventories);die;
if($inventories) { 
    $j = 0; 
    foreach ($inventories as $key_parties => $value_parties) { 
        //$i = 1;
		$i = 0;
        if($value_parties)
        {
            $data_values = array();
			$party_dates = array();
			//$data_values[0]['x'] = $_POST['booking_date_from'];
			//$data_values[0]['y'] = 0;
            foreach ($value_parties as $key => $value) {
                   // $party_name = $value['supplier_name'];
                    $data_values[$i]['x']=date('d-m-Y', strtotime($value['created']));
                    $data_values[$i]['y']= ($value['average_oil_rate']) ? round($value['average_oil_rate'],2) : 0;
                    $i++;
					
					$dates[] = date('d-m-Y', strtotime($value['created']));
                    $party_dates[] = date('d-m-Y', strtotime($value['created']));
                    
            }
            //$data_values_1 =  json_encode($data_values,JSON_UNESCAPED_SLASHES);
            //$data_values_1 = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $data_values_1); 
            //$datasets[$j]['data'] = $data_values_1;
            //$datasets[$j]['label'] = $key_parties;
            //$datasets[$j]['borderColor'] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            //$datasets[$j]['fill'] = false;
			
			$party_xaxis[$j]['d'] = $party_dates;
            $party_data_values[$j] = $data_values;
            $party_xaxis[$j]['label'] = $key_parties;  
            $j++;
        }
    } 
	
	   asort($dates);
	   $dates  = array_values(array_unique($dates));
	   //echo "<pre>"; print_r($party_xaxis);
       //echo "sdsadsa";
      // echo "<pre>"; print_r($party_data_values_color);
	   foreach($party_xaxis as $key_p => $value_p)
	   {
			$value_p_index = count($value_p['d']);
			$result = array_diff($dates,$value_p['d']);

			foreach($result as $result_key => $result_val)
			{
				$party_data_values[$key_p][$value_p_index]['x'] = $result_val;
                $party_data_values[$key_p][$value_p_index]['y']= "0";
				$value_p_index ++;
			}
            //echo "<pre>"; print_r($result);
            
			//asort();
            $key_values = array_column($party_data_values[$key_p], 'x'); 
            array_multisort($key_values, SORT_ASC, $party_data_values[$key_p]);

			$data_values_1 = json_encode($party_data_values[$key_p],JSON_UNESCAPED_SLASHES);
            $data_values_1 = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $data_values_1); 
            $datasets[$key_p]['data'] = $data_values_1;
            $datasets[$key_p]['label'] = $value_p['label'];
            $datasets[$key_p]['borderColor'] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            $datasets[$key_p]['fill'] = false;
	   }       
} 
$datasets_json = json_encode($datasets,JSON_UNESCAPED_SLASHES);
$datasets_json = preg_replace('/"([^"]+)"\s*:\s*/', '$1:', $datasets_json);
$datasets_json = str_replace(']"', "]",(str_replace('"[', "[", $datasets_json))); 

 //echo "<pre>"; print_r($data_values_1);

 
//echo "<pre>"; print_r($party_data_values_color);
?>




<script>
        const xValues = <?php echo json_encode($dates); ?>;
        new Chart(document.getElementById("line-chart"), {
            type : 'line', 
            data : {
                labels: xValues, 
                datasets :  <?php echo stripcslashes($datasets_json); ?> 
            },
              
        }); 
    </script>
<?php include APPPATH.'views/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#booking_date_from,#booking_date_to").flatpickr({  
    dateFormat: "d-m-Y",
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
 
