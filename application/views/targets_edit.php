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
                    <form action="" class="" method="post" id="addtarget"> 
                        <input type="hidden" name="target_id" value="<?php echo $info['id']; ?>">
                    <div class="row">
                        <div class="col-md-4">                           
                            <div class="form-group">
                                <label for="name">Team </label> 
                                <select class="form-control" id="employee" name="employee" required="">
                                    <option value="">Select  Employee</option>
                                    <?php if($employees) { 
                                        foreach ($employees as $key => $employee) { ?>
                                        <option value="<?php echo $employee['id']; ?>" <?php if(isset($info['user_id'])) { if($info['user_id']==$employee['id']) { echo "selected"; } } ?>><?php echo $employee['name'].' - '.$employee['username']; ?></option>
                                    <?php } } ?>
                                </select>
                                <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <?php $targent_month_year = $info['targent_month_year'];
                                    if($targent_month_year)
                                    {
                                        $targent_month_year_array =  explode('-', $targent_month_year); 
                                        $targent_month = $targent_month_year_array[0];
                                        $targent_year = $targent_month_year_array[1];
                                    }

                                 ?>
                                <label for="name">Month</label> 
                                <select class="form-control" id="month" name="month" required="">
                                    <option value="">Select Month</option>
                                    <?php   $month = 1;
                                            $current_month = date('m');
                                            for ($i=$month; $i <=12 ; $i++) { ?>
                                               <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>" <?php if((isset($targent_month) && $targent_month==$i)) {  echo "selected"; }  ?>><?php echo date("F",mktime(0, 0, 0, $i, 10)); ?></option>
                                            <?php } ?> 
                                </select>
                                <span class="txt-danger"><?php echo form_error('month'); ?></span>
                            </div>
                        </div>  
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="name">Year</label> 
                                <select class="form-control" id="to_year" name="to_year" required="">
                                    <option value="">Select Year</option>
                                    <?php   $end_year = date('Y', strtotime('+1 year'));
                                            $start_year = 2022;// date('Y'); 
                                            for ($i=$start_year; $i <=$end_year ; $i++) { ?>
                                               <option value="<?php echo $i; ?>" <?php if((isset($targent_year) && $targent_year==$i) ) {  echo "selected"; }  ?> ><?php echo $i; ?></option>
                                            <?php } ?> 
                                </select>
                                <span class="txt-danger"><?php echo form_error('to_year'); ?></span>
                            </div> 
                        </div>
                    </div> 
                    <div class="row">  
                        <div class="col-md-4">
                            <label for="weight">Weight (MT) (Monthly)</label> 
                            <input type="text" class="form-control" placeholder="Weight Per Month (MT)" name="weight" id="weight" value="<?php echo $info['weight']; ?>">
                            <span class="txt-danger"><?php echo form_error('weight'); ?></span>
                        </div>
                        <div class="col-md-4">
                            <label for="distributor_visit">Visits (Monthly)</label> 
                            <input type="text" class="form-control" placeholder="Number Of Visits Per Month" name="distributor_visit" value="<?php echo $info['distributor_visits']; ?>">
                            <span class="txt-danger"><?php echo form_error('distributor_visit'); ?></span>
                        </div>
                        <?php $state_ids = $info['state_ids'];
                                $state_ids_array = array();
                                    if($state_ids)
                                    {
                                        $state_ids_array =  explode(',', $state_ids);  
                                    }

                                 ?>
                        <div class="col-md-4">
                            <label for="state">Working Area</label> 
                            <select class="form-control" id="state" name="state[]" multiple>
                                <?php if($states) { 
                                    foreach ($states as $key => $value) { ?>
                                    <option value="<?php echo $value['id']; ?>" <?php echo (in_array($value['id'], $state_ids_array)) ? 'selected' : ''; ?> ><?php echo $value['name']; ?></option>
                                <?php } } ?>
                            </select> 
                            <span class="txt-danger"><?php echo form_error('state'); ?></span>
                        </div> 
                        <div class="col-md-4" style=" display: none;">
                            <label for="retailer_visit">Visit Retailer</label> 
                            <input type="text" class="form-control" placeholder="Number Of Visit Retailer" name="retailer_visit" id="retailer_visit" value="0">
                            <span class="txt-danger"><?php echo form_error('retailer_visit'); ?></span>
                        </div>  
                    </div>
                    <div class="row">                               
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label class="btn-block"></label>
                                <button type="submit" class="btn btn-default booking_submit" value="Search">Save Target</button>  
                            </div>                                  
                        </div> 
                        <div class="col-md-4">
                        </div>                             
                    </div>
                    </form>  
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
         $("#state").select2(); 
        $("#employee").select2();  
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
});
</script>