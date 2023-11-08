<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
    <section id="middle">
        <header id="page-header">
            <h1><?php echo $title; ?></h1> 
        </header>
        <div id="content" class="padding-20">
            <div id="panel-1" class="panel panel-default">
                <div class="panel-heading">
                    <span class="title elipsis">
                        <strong><?php echo $title; ?></strong>
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
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" class="" method="post" enctype='multipart/form-data'>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Brand</label> 
                                            <select class="form-control" id="brand" name="brand" required>
                                                    <option value="">Select Brand</option>
                                                    <?php if($brands) { 
                                                        foreach ($brands as $key => $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['brand'])) { if($_POST['brand']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                                    <?php } } ?>
                                                </select>
                                                <span class="txt-danger"><?php echo form_error('brand'); ?></span> 
                                        </div>
                                    </div> 
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pan">Category</label> 
                                            <select class="form-control" id="category" name="category" required>
                                                <option value="">Select Category</option>
                                            </select>
                                            <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="state">State </label> 
                                            <select class="form-control" id="state" name="state" required="">
                                                <option value="">Select State</option>
                                                <?php if($states) { 
                                                    foreach ($states as $key => $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['state'])) { if($_POST['state']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                                <?php } } ?>
                                            </select>
                                            <span class="txt-danger"><?php echo form_error('state'); ?></span>
                                        </div> 
                                    </div>
                                </div>
                                <div class="row">                                     
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="city">Scheme For </label> 
                                            <select class="form-control" id="scheme_for" name="scheme_for" required="">
                                                <option value="">Select Scheme for</option>
                                                <option value="1">Supplier</option>
                                                <option value="2">Broker</option>
                                                <option value="3">Distributer</option>
                                                <option value="4">Maker</option>
                                                <option value="5">Secondary Maker</option>
                                            </select>
                                            <span class="txt-danger"><?php echo form_error('scheme_for'); ?></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-4">
                                        <div class="form-group"> 
                                            <label for="">Scheme Date (From) </label>
                                            <input class="form-control" type="text" id="from_date" name="from_date"  value="<?php if(isset($_POST['from_date'])) { echo $_POST['from_date']; } else { echo date('d-m-Y'); } ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group"> 
                                            <label for="">Scheme Date (To)</label>
                                            <input class="form-control" type="text" id="to_date" name="to_date" value="<?php if(isset($_POST['to_date'])) { echo $_POST['to_date']; } else { echo date('d-m-Y'); } ?>"/>
                                        </div>
                                    </div>
                                </div> 
                                <button type="submit" class="btn btn-default">Save</button> 
                                <a href="<?php echo base_url('schemes') ?>" class="btn btn-default">Cancel</a> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#from_date,#to_date").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
     $("#brand,#category,#scheme_for").select2();
    $("#brand").change(function(){
        var brand_id = $(this).val(); 
        $("#quantity").val('');
        $(".Total_Weight_MT").text('');
        $(".Total_Weight_MT_net").text('');
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getactivecategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
                $("#category").html(msg); 
            }
        });
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
});
</script>