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
                    <span class="title elipsis header_add">
                        <a target="_blank" href="<?php echo base_url(); ?>vendors/logs/<?php echo base64_encode($vendor['id']); ?>">Log</a>
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
                                    <input type="text" class="form-control" id="name" name="name" required="" value="<?php echo $vendor['name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="gst">GST Number</label> 
                                    <input type="text" class="form-control" id="gst" name="gst" required="" value="<?php echo $vendor['gst_no']; ?>">
                                    <span class="txt-danger"><?php echo form_error('gst'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="mobile">Mobile</label> 
                                    <input type="text" class="form-control" id="mobile" name="mobile" required="" value="<?php echo $vendor['mobile']; ?>">
                                    <span class="txt-danger"><?php echo form_error('mobile'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label> 
                                    <input type="text" class="form-control" id="email" name="email" required="" value="<?php echo $vendor['email']; ?>">
                                    <span class="txt-danger"><?php echo form_error('email'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea  class="form-control" id="address" name="address" required=""><?php echo $vendor['address']; ?></textarea>  
                                    <span class="txt-danger"><?php echo form_error('address'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="state">State </label> 
                                    <select class="form-control" id="state" name="state" required="">
                                        <option value="">Select State</option>
                                        <?php if($states) { 
                                            foreach ($states as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"<?php  if($vendor['state_id']==$value['id']) { echo "selected"; } ; ?>><?php echo $value['name']; ?></option>
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
                                    <input type="text" class="form-control" id="zipcode" name="zipcode"  value="<?php echo $vendor['zipcode']; ?>">
                                    <span class="txt-danger"><?php echo form_error('zipcode'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="bank_details">Bank Details</label>
                                    <textarea  class="form-control" id="bank_details" name="bank_details"><?php echo $vendor['bank_details']; ?></textarea>  
                                    <span class="txt-danger"><?php echo form_error('bank_details'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="other_info">Other Info</label>
                                    <textarea  class="form-control" id="other_info" name="other_info"><?php echo $vendor['other_info']; ?></textarea>  
                                    <span class="txt-danger"><?php echo form_error('other_info'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="invoice_prefix">Invoice Prefix</label> 
                                    <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix"  value="<?php echo $vendor['invoice_prefix']; ?>">
                                    <span class="txt-danger"><?php echo form_error('invoice_prefix'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="loose_rate">FOR cost per KG (excluding tax)</label> 
                                    <input type="text" class="form-control" id="for_rate" name="for_rate" required="" value="<?php echo $vendor['for_rate']; ?>">
                                    <span class="txt-danger"><?php echo form_error('for_rate'); ?></span>
                                </div>
                                <div class=""> 
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="tax_included" name="tax_included" value="1" <?php echo ($vendor['tax_included']) ? 'checked' : ''; ?>> Tax Include
                                        <!--<input type="checkbox"  id="freight_included" name="freight_included" value="1" <?php echo ($vendor['freight_included']) ? 'checked' : ''; ?>> Freight Include-->
                                    </div>                               
                                </div>
                                <div class="form-group">
                                    <label for="employee">Assign Team (Maker)</label> 
                                    <select class="form-control" id="employee" name="employee" required="">
                                        <option value="">Select Maker</option>
                                        <?php if($employees) { 
                                            foreach ($employees as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php  if($vendor['employee_id']==$value['id']) { echo "selected"; } ; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                                </div>
                                <div class="rate_box" <?php echo ($logged_role==4) ? '' : 'style="display:none;"';  ?>> 
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="cnf" name="cnf" value="1" <?php echo ($vendor['cnf']==1) ? "checked" : ""; ?>> C&F
                                    </div>                              
                                </div>
                                <button type="submit" class="btn btn-default">Update</button> 
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
    var city_id = '<?php echo $vendor['city_id']; ?>';
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