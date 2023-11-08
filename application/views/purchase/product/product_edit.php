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
                                    <input type="text" class="form-control" id="name" name="name" required value="<?php echo $product['product_name'];?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>   
                                <div class="form-group">
                                    <label for="name">Category </label> 
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php if($categories) { 
                                            foreach ($categories as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($product['category_id'])) { if($product['category_id']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['category_name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                </div>  
                                <div class="form-group">
                                    <label for="Attributes">Attributes</label> <br>
                                    <?php if($attributes)
                                    {
                                        $selected_product_attribute = array();
                                        $product_attribute = $product['attributes'];
                                        if($product_attribute)
                                            $selected_product_attribute = explode(',', $product_attribute); 
                                        foreach ($attributes as $key => $value) { ?>
                                            <input type="checkbox" name="attributes[]" value="<?php echo $value['id']; ?>" <?php echo (in_array($value['id'], $selected_product_attribute)) ? 'checked' : ''; ?> > <?php echo $value['name']; ?>
                                        <?php }
                                    }

                                     ?>
                                </div>
                                <button type="submit" class="btn btn-default">Update</button> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include APPPATH.'views/footer.php'; ?> 