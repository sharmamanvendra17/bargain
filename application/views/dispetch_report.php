<?php include 'header.php'; ?>
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1>
    </header>
    <div id="content" class="padding-20">
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
        <div id="panel-2" class="panel panel-default">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong><?php echo $title; ?></strong>
                </span>
            </div>
            <div class="panel-body">  
                <form action="" class="" method="post" id="addbooking"> 
                    <div class="row">  
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label class="btn-block"><strong>Production Unit</strong></label>
                                <input class="" type="radio" id="" name="production_unit"  value=""   <?php echo (!isset($_POST['production_unit']) || $_POST['production_unit']=='') ? 'checked' : ''; ?> />All
                                <input class="" type="radio" id="" name="production_unit"  value="alwar" <?php echo (isset($_POST['production_unit']) && $_POST['production_unit']=='alwar') ? 'checked' : ''; ?> />Alwar
                                <input class="" type="radio" id="" name="production_unit"  value="jaipur" <?php echo (isset($_POST['production_unit']) && $_POST['production_unit']=='jaipur') ? 'checked' : ''; ?> />Jaipur
                            </div>
                        </div>  
                    </div>
                    <div class="row">                               
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label class="btn-block"></label>
                                <button type="submit" class="btn btn-default booking_submit" value="Search">Search Report</button>  
                            </div>                                  
                        </div> 
                        <div class="col-md-4">
                        </div>                             
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover ">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th>Quantity (Tins/Carton)</th>
                                <th>Total Pieces</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; if($results){
                            foreach ($results as $key => $value) { ?>
                            <tr>
                                <td><?php echo $i; ?></td>  
                                <td><?php echo $value['name']; ?></td>  
                                <td><?php echo number_format($value['total_cartons']); ?></td>
                                <td><?php echo number_format($value['total_qty']); ?></td>                                
                            </tr>
                            <?php $i++; } } ?>
                        </tbody>
                    </table>
                    <h4>Detailed Dispetched QTY Report </h4>
                    <table class="table table-striped table-bordered table-hover ">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th>Quantity (Tins/Carton)</th>
                                <th>Total Pieces</th> 
                                <th>Weight (MT)</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; $i = 1; if($results_details){
                            foreach ($results_details as $key => $results_detail) {
                                if($results_detail)
                                {
                                    foreach ($results_detail as $key => $value) {
                             ?>
                            <tr>
                                <td><?php echo $i; ?></td>  
                                <td><?php echo $value['product_name']; ?></td>  
                                <td><?php echo number_format($value['total_cartons']); ?></td>
                                <td><?php echo number_format($value['total_qty']); ?></td>
                                <td><?php echo $value['weight']; $total = $total+$value['weight']; ?></td>
                            </tr>
                            <?php $i++; } } } ?>
                            <tr>
                                <td colspan="4"><strong>Total</strong></td> 
                                <td><strong><?php echo $total; ?></strong></td> 
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div> 
            </div>
        </div> 
    </div>
</section> 
<?php include 'footer.php'; ?>  
 
 