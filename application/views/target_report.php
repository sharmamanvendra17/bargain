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

                        </div>

                </div>
             
                
            </div>
            <?php if(isset($_SESSION['search_target_report_data']))
            {   
                        $_POST = $_SESSION['search_target_report_data'];   
            } ?>
            <div class="panel-body">
                    <form action="<?php echo base_url('target_report'); ?>" class="" method="post" id="">
                        <?php if($logged_role!=6) { ?>
                        <div class="row" <?php echo ($logged_role==6) ? 'style="display:none;"' : ''; ?>>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rate">View</label> 
                                    <input class="view_reoprt" type="radio" id="" name="view_reoprt"  value="makers" <?php echo ((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers') || !isset($_POST['view_reoprt'])) ? 'checked' : ''; ?> />Makers
                                    <input class="view_reoprt" type="radio" id="" name="view_reoprt"  value="secondarymakers" <?php echo ( (isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='secondarymakers') ) ? 'checked' : ''; ?> /> Secondary Makers
                                </div>
                            </div> 
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>  
                        </div> 
                        <?php } else { ?>
                            <input class="view_reoprt" type="hidden" id="" name="view_reoprt"  value="secondarymakers"  /> 
                        <?php } ?>
                        <div class="row"> 
                            <div class="col-md-3" >                           
                                <div class="form-group">
                                    <label for="name">Employee </label> 
                                    <select class="form-control" id="employee" name="employee" >
                                        <?php if(($logged_role!=1 && $logged_role!=6 ) || (isset($_POST['view_reoprt']) && $_POST['view_reoprt'] =='secondarymakers')) { ?>
                                        <option value="">Select  Employee</option>
                                        <?php } ?>
                                        <?php if($users) { 
                                            foreach ($users as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name'].' - '.$value['username']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <?php if($since_months) { ?>
                                        <span class="txt-danger"><strong>Working since <?php echo $since_months; ?> months.</strong></span>
                                    <?php } ?>
                                    <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Year</label> 
                                    <select class="form-control" id="year" name="year" >
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Month</label> 
                                    <select class="form-control" id="month" name="month" >
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">State</label> 
                                    <select class="form-control" id="state" name="state" >
                                        <option value="">Select State</option>
                                        <?php if($states) {
                                            foreach ($states as $key => $value) {  ?>
                                                <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['state'])) { if($_POST['state']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('state'); ?></span>
                                </div>
                            </div>
                        </div>  
                        <div class="row">
                            <?php if($logged_role==4) { ?>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">Team Lead</label> 
                                        <select name="temalead" id="temalead" class="form-control">
                                            <option value="">Select Team Lead</option>
                                            <?php if($team_leads)
                                            {
                                                foreach ($team_leads as $key => $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['temalead'])) { if($_POST['temalead']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>    
                            <?php } ?>                            
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rate">Report</label> <br>
                                        <input class="" type="radio" id="" name="report_type"  value="0" <?php echo ((isset($_POST['report_type']) && $_POST['report_type']=='0') || !isset($_POST['cnf'])) ? 'checked' : ''; ?> /> State Wise
                                        <input class="" type="radio" id="" name="report_type"  value="1" <?php echo (isset($_POST['report_type']) && $_POST['report_type']=='1') ? 'checked' : ''; ?> /> Consolidated
                                    </div>
                                </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="rate">Sort By</label> 
                                    <input class="party_type" type="radio" id="" name="sort_by"  value="employee_name" <?php echo ((isset($_POST['sort_by']) && $_POST['sort_by']=='employee_name') || !isset($_POST['cnf'])) ? 'checked' : ''; ?> /> Name
                                    <input class="party_type" type="radio" id="" name="sort_by"  value="per_target" <?php echo (isset($_POST['sort_by']) && $_POST['sort_by']=='per_target') ? 'checked' : ''; ?> /> Percentage Achieved
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
                        <?php if($results) { ?>
                            <div class="row">   
                                <div class="col-md-8"> </div>
                                <div class="col-md-4" style="text-align:right;">   
                                    <button type="submit" class="btn btn-default" value="downloadpdf" name="downloadpdf">Download PDF</button>
                                    <button type="submit" class="btn btn-default" value="downloadexcel" name="downloadexcel">Download Excel</button>
                                </div>
                            </div>
                        <?php } ?>                           
                        <div class="row">
                            <div class="col-md-12">   
                                <div class="table-responsive">
                                    <?php if($results) { ?>
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Name</th>
                                                <th>State Name</th> 
                                                <th>Target (MT)</th> 
                                                <th>Bargain (MT) </th>
                                                <?php if((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')) { ?>
                                                <th>Dispatched (MT) </th>
                                                <?php } ?>
                                                <th><?php echo (isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers') ? 'Dispatched' : 'Bargain' ?>  %</th>  
                                                <th>Target Visits </th>
                                                <th>Visited</th>
                                                <th>Visits %</th>                                     
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php   
                                            $count = 1;
                                            $cur_page =1;
                                            if(isset($limit))
                                                $con_li = $limit;
                                            if($this->uri->segment(3)!='')
                                                $cur_page = $this->uri->segment(3);
                                            $count = ($cur_page-1)*$con_li+1; 
                                            foreach ($results as $key => $value) { ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td title="<?php echo $value['mobile']; ?>"><?php echo $value['employee_name']; echo ($value['joining_month']) ? ' <strong > <span title=" Joining date : '.date('d-m-Y',strtotime($value['joining_date']) ).'"> ('.$value['joining_month'].') </span></strong>' : ''; ?></td>
                                                <td><?php echo $value['state_name']; ?></td>
                                                <td><?php echo round($value['total_target_weight'],2); ?></td>  
                                                <td><?php echo round($value['bargain_total_weight'],2); ?></td>
                                                <?php if((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')) { ?>
                                                <td>  
                                                <?php
                                                    $month_post = $_POST['month'].'-'.$_POST['year'];
                                                   $dispatched  =  dispatchtarget($month_post,$value['user_id'],$value['state_ids']);
                                                   $dispatched_target_Acheived =  ($dispatched['total_dispateched_weight']) ? $dispatched['total_dispateched_weight'] : 0;
                                                   echo round($dispatched_target_Acheived,2);
                                                    ?>
                                                </td>
                                                <td><?php $dispatched_persentage = ($value['total_target_weight']>0) ? (round(($dispatched['total_dispateched_weight']*100)/($value['total_target_weight']),2)) : 0; 
                                                    echo $dispatched_persentage;
                                                    //echo round($value['per_target'],2); ?></td>
                                                <?php } else { ?>
                                                <td><?php echo round($value['per_target'],2); ?></td>
                                                <?php } ?>
                                                <td><?php echo $value['total_target_visits']; ?></td>
                                                <td><?php echo ($value['total_visited']) ? "<a href='".base_url()."target_report/map/".$value['user_id']."/".urlencode($_POST['month']).'/'.urlencode($_POST['year']).'/'.urlencode($value['state_name'])."' target='_blank'>".$value['total_visited']."</a>" : $value['total_visited']; ?></td>
                                                <td><?php echo round($value['per_target_visit'],2); ?></td>   
                                            </tr>
                                            <?php $count++;  } ?>
                                        </tbody> 
                                    </table>
                                    <table>
                                        <tr>
                                            <td>
                                                <?php echo $links; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php } ?>                        
                                </div>
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
<?php include 'footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){    
    
    $("#employee,#state").select2();
    $(document).on('change', '.view_reoprt', function(){   
        var view_reoprt= $('input[name="view_reoprt"]:checked').val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>target_report/getusers/',
            data: { 'view_reoprt': view_reoprt},
            success: function(msg){ 
                $("#employee").html(msg);
            }
        }); 
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
});
</script>




 