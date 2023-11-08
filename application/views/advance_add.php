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
                            <form action="" class="" method="post">
                                <div class="form-group">
                                    <label for="name">Party</label> 
                                    <select class="form-control" id="party" name="party" required="">
                                        <option value="">Select Party</option>
                                        <?php if($parties) { 
                                            foreach ($parties as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name'].' '.$value['city_name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('party'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="name">Type</label> 
                                    <select class="form-control" id="type" name="type" required="">
                                        <option value="">Select Type</option>
                                        <option value="advance">Advance</option>
                                        <option value="bg">BG</option>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('type'); ?></span>
                                </div>
                                <div class="form-group expiry_date_block" style="display: none;">
                                    <label for="expiry_date">BG Expiry Date</label> 
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" required="" value="">
                                    <span class="txt-danger"><?php echo form_error('expiry_date'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="amount">Amount</label> 
                                    <input type="text" class="form-control" id="amount" name="amount" required="" value="<?php if(isset($_POST['amount'])) echo $_POST['amount']; ?>">
                                    <span class="txt-danger"><?php echo form_error('amount'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="verified_by">Verified By</label> 
                                    <select class="form-control" id="verified_by" name="verified_by" required="">
                                        <option value="">Select Verified By</option>
                                        <?php if($viewers) { 
                                            foreach ($viewers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['verified_by'])) { if($_POST['verified_by']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('verified_by'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="company">Amount deposited Company</label> 
                                    <select class="form-control" id="company" name="company" required="">
                                        <option value="">Select Company</option>
                                        <?php if($compnies) { 
                                            foreach ($compnies as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['company'])) { if($_POST['company']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('company'); ?></span>
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
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#expiry_date").flatpickr({ 
    minDate: "today",
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        $("#party,#verified_by,#company").select2();  
    });
    $("#type").change(function(){
        var type = $(this).val();  
        $('.expiry_date_block').hide();
        if(type=='bg')
        {
            $('.expiry_date_block').show();
        }
    });
});
</script>