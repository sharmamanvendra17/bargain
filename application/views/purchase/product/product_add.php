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
                                    <label for="name">Product Name</label> 
                                    <input type="text" class="form-control" id="name" name="name" required value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="name">Category </label> 
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php if($categories) { 
                                            foreach ($categories as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['category'])) { if($_POST['category']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['category_name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                </div>  
                                <div class="form-group">
                                    <label for="Attributes">Attributes</label> <br>
                                    <?php if($attributes)
                                    {
                                        foreach ($attributes as $key => $value) { ?>
                                            <input type="checkbox" name="attributes[]" value="<?php echo $value['id']; ?>"> <?php echo $value['name']; ?>
                                        <?php }
                                    }

                                     ?>
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
<script>
$(document).ready(function(){
    $("#brand").change(function(){
        var brand_id = $(this).val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getcategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
                $("#category").html(msg);
            }
        });
    });
	$("#weight_unit").change(function(){
		$wt_unit = $("#weight_unit option:selected").text();
		if($wt_unit == 'LT')
		  $wt_unit = 'KG'
		else if($wt_unit == 'ML')
			$wt_unit = 'Grams'  
		
		$(".wt_unit").text('in '+$wt_unit);
	});
});
</script>