<?php include 'header.php'; ?>


            <!-- 
                MIDDLE 
            -->
            <section id="middle">


                <!-- page title -->
                <header id="page-header">
                    <h1>Update Product- <?php echo $product['product_name']; ?></h1> 
                </header>
                <!-- /page title -->


                <div id="content" class="padding-20">

                    <!-- 
                        PANEL CLASSES:
                            panel-default
                            panel-danger
                            panel-warning
                            panel-info
                            panel-success

                        INFO:   panel collapse - stored on user localStorage (handled by app.js _panels() function).
                                All pannels should have an unique ID or the panel collapse status will not be stored!
                    -->
                    <div id="panel-1" class="panel panel-default">
                        <div class="panel-heading">
                            <span class="title elipsis">
                                <strong><?php echo $product['product_name']; ?></strong> <!-- panel title -->
                            </span> 
                            <ul class="options pull-right list-inline">
                                <li><a href="<?php echo base_url(); ?>admin/">Back</a></li> 
                            </ul>

                        </div>

                        <!-- panel content -->
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
                            <form method="post" action="">
                                <table class="table table-striped table-bordered table-hover" id="datatable_sample1">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Product Name</th> 
                                            <th>Is Enable</th>
                                            <th style="width: 200px;">Selling Price</th> 
                                            <th style="width: 200px;">Mrp Price</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($packagings) { 
                                            $i=1;
                                            foreach ($packagings as $key => $value) { ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $value['packing']; ?></td> 
                                                <td><a href="<?php echo base_url();?>admin/status_update_packaging/<?php echo ($value['is_enable'] ? "0" : "1") ?>/<?php echo base64_encode($value['id']) ?>/<?php echo base64_encode($value['product_id']) ?>"><?php echo ($value['is_enable'] ? "No" : "Yes") ?></a></td>
                                                <td><input  type="text" name="package[<?php echo $value['id'];?>]" value="<?php echo $value['price']; ?>" class="form-control" required></</td> 
                                                <td><input  type="text" name="mrp[<?php echo $value['id'];?>]" value="<?php echo $value['mrp']; ?>" class="form-control" required></</td> 
                                            </tr>
                                        <?php $i++; } } ?>
                                        <tr>
                                            <td colspan="5" class="text-right"><input type="submit" name="submit" value="Update Price" class="btn btn-primary "></td>
                                        </tr>
                                    </tbody>  
                                </table>
                                </form>

                        </div>
                        <!-- /panel content -->
                    </div>
                    <!-- /PANEL -->

                </div>
            </section>
            <!-- /MIDDLE -->
<?php include 'footer.php'; ?>