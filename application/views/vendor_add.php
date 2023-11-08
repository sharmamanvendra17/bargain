<?php include 'header.php'; ?>
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
                                    <label for="mobile">Mobile</label> 
                                    <input type="text" class="form-control" id="mobile" name="mobile" required="" value="<?php if(isset($_POST['mobile'])) echo $_POST['mobile']; ?>">
                                    <span class="txt-danger"><?php echo form_error('mobile'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label> 
                                    <input type="text" class="form-control" id="email" name="email" required="" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>">
                                    <span class="txt-danger"><?php echo form_error('email'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label> 
                                    <textarea class="form-control" id="address" name="address" required=""><?php if(isset($_POST['address'])) echo $_POST['address']; ?></textarea>
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
                                    <label for="bank_details">Bank Details</label> 
                                    <textarea class="form-control" id="bank_details" name="bank_details"><?php if(isset($_POST['bank_details'])) echo $_POST['bank_details']; ?></textarea>
                                    <span class="txt-danger"><?php echo form_error('bank_details'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="other_info">Other Info</label> 
                                    <textarea class="form-control" id="other_info" name="other_info"><?php if(isset($_POST['other_info'])) echo $_POST['other_info']; ?></textarea>
                                    <span class="txt-danger"><?php echo form_error('other_info'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="invoice_prefix">Invoice Prefix</label> 
                                    <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" value="<?php if(isset($_POST['invoice_prefix'])) echo $_POST['invoice_prefix']; else echo  ''; ?>">
                                    <span class="txt-danger"><?php echo form_error('invoice_prefix'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="loose_rate">FOR cost per KG (excluding tax)</label> 
                                    <input type="text" class="form-control" id="for_rate" name="for_rate" required="" value="<?php if(isset($_POST['for_rate'])) echo $_POST['for_rate']; else echo 0.00; ?>">
                                    <span class="txt-danger"><?php echo form_error('for_rate'); ?></span>
                                </div>
                                <div class=""> 
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="tax_included" name="tax_included" value="1"> Tax Include
                                        <!--<input type="checkbox"  id="freight_included" name="freight_included" value="1"> Freight Include-->
                                    </div>                               
                                </div>
                                <div class="form-group">
                                    <label for="employee">Assign Team (Maker)</label> 
                                    <select class="form-control" id="employee" name="employee" required="">
                                        <option value="">Select Maker</option>
                                        <?php if($employees) { 
                                            foreach ($employees as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                                </div>
                                <div class="rate_box"> 
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="cnf" name="cnf" value="1"> C&F
                                    </div>                              
                                </div>
                                <button type="submit" class="btn btn-default">Save</button> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>
<script>
$(document).ready(function(){
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