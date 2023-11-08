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
                            	<div class="row">
                        			<div class="col-md-4">
		                                <div class="form-group">
		                                    <label for="name">Party Name</label> 
		                                    <select class="form-control" id="party" name="party" required="">
		                                        <option value="">Select Party</option>
		                                        <?php if($users) { 
		                                            foreach ($users as $key => $value) { ?>
		                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
		                                        <?php } } ?>
		                                    </select>
		                                    <span class="txt-danger"><?php echo form_error('party'); ?></span>
		                                </div>
		                            </div>
		                            <div class="col-md-4">
		                                <div class="form-group">
		                                    <label for="name">Brand </label> 
		                                    <select class="form-control" id="brand" name="brand" required="">
		                                        <option value="">Select Category</option>
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
		                                    <label for="name">Category </label> 
		                                    <select class="form-control" id="category" name="category" required="">
		                                        <option value="">Select Category</option>
		                                        <?php if($brands) { 
		                                            foreach ($brands as $key => $value) { ?>
		                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['brand'])) { if($_POST['brand']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
		                                        <?php } } ?>
		                                    </select>
		                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
		                                </div>
		                            </div>
		                        </div>
                                <div class="row">
                        			<div class="col-md-4">
		                                <div class="form-group">
		                                    <label for="name">Products </label> 
		                                    <select class="form-control" id="product" name="product" required="">
		                                        <option value="">Select Product</option>
		                                        
		                                    </select>
		                                    <span class="txt-danger"><?php echo form_error('product'); ?></span>
		                                </div>
		                            </div>
		                            <div class="col-md-4">
		                                <div class="form-group">
		                                    <label for="quantity">Qunatity</label> 
		                                    <input type="text" class="form-control" id="quantity" name="quantity" required="" value="<?php if(isset($_POST['quantity'])) echo $_POST['quantity']; ?>">
		                                    <span class="txt-danger"><?php echo form_error('quantity'); ?></span>
		                                </div>
		                            </div>
		                            <div class="col-md-4">   
		                                <div class="form-group">
		                                    <label for="rate">Rate</label> 
		                                    <input type="text" class="form-control" id="hsn" name="rate"  value="<?php if(isset($_POST['rate'])) echo $_POST['rate']; ?>" required>
		                                    <span class="txt-danger"><?php echo form_error('rate'); ?></span>
		                                </div> 
		                            </div>
		                        </div>
		                         <div class="row">
                        			<div class="col-md-4 col-md-offset-4">
                                		<button type="submit" class="btn btn-default">Save</button> 
                                	</div>
                                </div>
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
    $("#category").change(function(){
        var category_id = $(this).val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getproduct',
            data: { 'category_id': category_id},
            success: function(msg){
                $("#product").html(msg);
            }
        });
    });
});
</script>