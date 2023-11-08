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
                                    <label for="name">Product Name</label> 
                                    <input type="text" class="form-control" id="name" name="name" required value="<?php echo $product['name'];?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="name">Brand </label> 
                                    <select class="form-control" id="brand" name="brand" required>
                                        <option value="">Select Brand</option>
                                        <?php if($brands) { 
                                            foreach ($brands as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if($product['brand_id']==$value['id']) { echo "selected"; } ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('brand'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="name">Category </label> 
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php if($brands) { 
                                            foreach ($brands as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['brand'])) { if($_POST['brand']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="weight">Weight Unit</label> 
                                    <select class="form-control" name="weight_unit" id="weight_unit">
                                        <option value="">Select Unit</option>
                                        <option value="1"  <?php echo ($product['packaging_type']==1) ? 'selected' : '';?>>KG</option>
                                        <option value="2"  <?php echo ($product['packaging_type']==2) ? 'selected' : '';?>>LT</option>
                                        <option value="3" <?php echo ($product['packaging_type']==3) ? 'selected' : '';?>>ML</option>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('weight_unit'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="weight">Item Qty in carton/Tin</label> 
                                    <input type="text" class="form-control" id="quantity" name="quantity" required value="<?php echo $product['packing_items_qty']; ?>">
                                    <span class="txt-danger"><?php echo form_error('quantity'); ?></span>
                                </div>
                                 
                                <div class="form-group">
                                    <label for="weight">Weight in a piece (Oil excluded packing)</label> 
                                    <input type="text" class="form-control" id="weight" name="weight" required value="<?php echo $product['weight'];?>">
                                    <span class="txt-danger"><?php echo form_error('weight'); ?></span>
                                </div>  
                                <div class="form-group">
                                <?php $wt_unit = ($product['packaging_type']==3) ? 'in Grams' : 'in KG';?>
                                    <label for="packing_weight">Net Weight in a piece packing (only packing weight <span class="wt_unit"><?php echo $wt_unit;?></span>)</label> 
                                    <input type="text" class="form-control" id="packing_weight" name="packing_weight" required value="<?php echo $product['packing_weight'];?>">
                                    <span class="txt-danger"><?php echo form_error('packing_weight'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="loose_rate">Empty Tin Rate</label> 
                                    <input type="text" class="form-control" id="loose_rate" name="loose_rate" required value="<?php echo $product['loose_rate'];?>">
                                    <span class="txt-danger"><?php echo form_error('loose_rate'); ?></span>
                                </div>
                                <div class="form-group" style="display:none;">
                                    <label for="loose_rate">FOR Rate</label> 
                                    <input type="text" class="form-control" id="for_rate" name="for_rate" required value="<?php echo $product['for_rate'];?>">
                                    <span class="txt-danger"><?php echo form_error('for_rate'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="loose_rate">Type</label> 
                                    <select class="form-control" id="product_type" name="product_type" required>
                                        <option value="0" <?php echo ($product['product_type']==0) ? 'selected' : '' ?>>Bulk</option>
                                        <option value="1"  <?php echo ($product['product_type']==1) ? 'selected' : '' ?>>Consumer</option>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('product_type'); ?></span>
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
        var category_id = '<?php echo $product['category_id']; ?>';
        var brand_id = $('#brand').val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getcategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
                $("#category").html(msg);
                $("select#category option").each(function(){
                  if ($(this).val() == category_id)
                    $(this).attr("selected","selected");
                });
            }
        });


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