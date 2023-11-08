<?php include APPPATH.'views/header.php'; ?>
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
                            <form action="" class="" method="post">
                                <div class="form-group">
                                    <label for="name">Name</label> 
                                    <input type="text" class="form-control" id="name" name="name" required="" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="gst">GST Number</label> 
                                    <input type="text" class="form-control" id="gst" name="gst" required="" value="<?php if(isset($_POST['gst'])) echo $_POST['gst']; ?>">
                                    <span class="txt-danger"><?php echo form_error('gst'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label> 
                                    <input type="text" class="form-control" id="email" name="email" required="" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>">
                                    <span class="txt-danger"><?php echo form_error('email'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="mobile">Mobile</label> 
                                    <input type="text" class="form-control" id="mobile" name="mobile" required="" value="<?php if(isset($_POST['mobile'])) echo $_POST['mobile']; ?>">
                                    <span class="txt-danger"><?php echo form_error('mobile'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label> 
                                    <input type="text" class="form-control" id="address" name="address" required="" value="<?php if(isset($_POST['address'])) echo $_POST['address']; ?>">
                                    <span class="txt-danger"><?php echo form_error('address'); ?></span>
                                </div>
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
                                <div class="form-group">
                                    <label for="city">City </label> 
                                    <select class="form-control" id="city" name="city" required="">
                                        <option value="">Select City</option>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('city'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="zipcode">Zip Code</label> 
                                    <input type="text" class="form-control" id="zipcode" name="zipcode"  value="<?php if(isset($_POST['zipcode'])) echo $_POST['zipcode']; ?>">
                                    <span class="txt-danger"><?php echo form_error('zipcode'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="employee">Assign Supplier</label> 
                                    <select class="form-control" id="supplier" name="supplier[]" required="" multiple>
                                        <option value="">Select Supplier</option>
                                        <?php if($suppliers) { 
                                            foreach ($suppliers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['supplier'])) { if($_POST['supplier']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('supplier'); ?></span>
                                </div>
                                <button type="submit" class="btn btn-default">Save</button> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include APPPATH.'views/footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        $("#supplier").select2();  
    });
    $("#state").change(function(){
        var state_id = $(this).val();  
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>distributors/getcity',
            data: { 'state_id': state_id},
            success: function(msg){ 
                $("#city").html(msg);
            }
        });
        /*$.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>distributors/getvendors',
            data: { 'state_id': state_id},
            success: function(suppliers){ 
                $("#supplier").html(suppliers);
            }
        });*/
    });
});
</script>