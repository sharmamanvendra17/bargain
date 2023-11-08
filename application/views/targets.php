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

                <span class="title elipsis header_add">
                    <a href="<?php echo base_url();?>targets/add">Add New Target</a>
                </span>
                 

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
                    <div class="row">
                        <div class="col-md-4">                           
                            <div class="form-group">
                                <label for="name">Team </label> 
                                <select class="form-control" id="employee" name="employee" required>
                                    <option value="">Select  Employee</option>
                                    <?php if($employees) { 
                                        foreach ($employees as $key => $employee) { ?>
                                        <option value="<?php echo $employee['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$employee['id']) { echo "selected"; } } ?>><?php echo $employee['name'].' - '.$employee['username']; ?></option>
                                    <?php } } ?>
                                </select>
                                <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                            </div>
                        </div>  
                        <div class="col-md-4" style="display: none;">
                            <div class="form-group">
                                <label for="month">Month </label> 
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="year">Year</label> 
                                <select class="form-control" id="year" name="year" required="">
                                    <option value="">Select Year</option>
                                    <?php   $end_year = date('Y', strtotime('+1 year'));
                                            $start_year = 2022; 
                                            $current_year = date('Y');
                                            for ($i=$start_year; $i <=$end_year ; $i++) { ?>
                                               <option value="<?php echo $i; ?>" <?php if((isset($_POST['year']) && $_POST['year']==$i) || (!isset($_POST['year']) && $i==$current_year)) {  echo "selected"; }  ?> ><?php echo $i; ?></option>
                                            <?php } ?> 
                                </select>
                                <span class="txt-danger"><?php echo form_error('year'); ?></span>
                            </div> 
                        </div>
                    </div>  
                    <div class="row">                               
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label class="btn-block"></label>
                                <button type="submit" class="btn btn-default booking_submit" value="Search">Search Target</button>  
                            </div>                                  
                        </div> 
                        <div class="col-md-4">
                        </div>                             
                    </div>
                    </form>  
                    <?php if($targets) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Month Name</th>
                                        <th>Target (MT) (Monthly)</th> 
                                        <th>Visits (Monthly)</th>
                                        <th>States</th>
                                        <!--<th>Action</th>
                                        <th>Visit (Retailer)</th>         -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sno = 1; 
                                    foreach ($targets as $key => $value) { ?>
                                    <tr>
                                        <td><?php echo $sno; ?></td>
                                        <td><?php $target_month_year = $value['targent_month_year'];
                                                $target_month_year_array  = explode('-', $target_month_year);
                                                $month = $target_month_year_array[0];

                                         echo  date("F", mktime(0, 0, 0, $month, 10));; ?></td>
                                        <td><?php echo $value['weight']; ?></td>  
                                        <td><?php echo $value['distributor_visits']; ?></td>  
                                        <td><?php echo $value['state_name']; ?></td>  
                                        <!--<td><a href="<?php echo base_url('targets/edit'); ?>/<?php echo base64_encode($value['id']); ?>">Edit</a></td>  
                                        <td><?php echo $value['retailer_visits']; ?></td>--> 
                                    </tr>
                                    <?php $sno++; } ?>
                                </tbody>
                            </table>  
                        </div>
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