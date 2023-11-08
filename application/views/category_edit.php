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
                            <form action="<?php echo base_url('category/edit_category/').'/'.base64_encode($product['id']); ?>" class="" method="post">
                                <div class="form-group">
                                    <label for="name">Category Name</label> 
                                    <input type="text" class="form-control" id="name" name="name" required="" value="<?php echo $product['category_name'];?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="name">Brand </label> 
                                    <select class="form-control" id="brand" name="brand" required="">
                                        <option value="">Select Brand</option>
                                        <?php if($brands) { 
                                            foreach ($brands as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if($product['brand_id']==$value['id']) { echo "selected"; } ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('brand'); ?></span>
                                </div> 


                                <div class="form-group">
                                    <label for="name">Empty Tin Rate</label> 
                                    <input type="text" class="form-control" id="tin_rate" name="tin_rate" required value="<?php echo $product['tin_rate'];?>">
                                    <span class="txt-danger"><?php echo form_error('tin_rate'); ?></span>
                                </div> 
                                
                                <div class="form-group">
                                    <label for="alias">Alias</label> 
                                    <select class="form-control" id="alias" name="alias" required="">
                                        <option value="">Select Alias</option>
                                        <?php
                                            if($alias)
                                            {
                                                foreach ($alias as $key => $value) { ?>
                                                    <option value="<?php echo $value['alias_name']; ?>" <?php ?> <?php if($value['alias_name']==$product['alias_name']) { echo "selected"; }  ?> ><?php echo $value['category_name']; ?>-<?php echo $value['alias_name']; ?></option>
                                                <?php }
                                            }
                                        ?>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group other_alias_name_s" style="display: none;"> 
                                    <input type="text" class="form-control" id="other_alias_name" name="other_alias_name" value="<?php if(isset($_POST['other_alias_name'])) echo $_POST['other_alias_name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('other_alias_name'); ?></span>
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