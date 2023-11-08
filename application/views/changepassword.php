<?php include 'header.php'; ?>


            <!-- 
                MIDDLE 
            -->
            <section id="middle">


                <!-- page title -->
                <header id="page-header">
                    <h1>Change Password</h1> 
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
                                <strong>Change Password </strong> <!-- panel title -->
                            </span>  

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
                            <div class="row">
                                <div class="col-md-12">
                                    <form action="" class="" method="post">
                                      <div class="form-group">
                                        <label for="start_date">Old Password</label>
                                        <input type="password" class="form-control" id="old_password" name="old_password" >
                                        <span class="txt-danger"><?php echo form_error('old_password'); ?></span>
                                      </div>
                                      <div class="form-group">
                                        <label for="end_date">New Password: </label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" >
                                        <span class="txt-danger"><?php echo form_error('new_password'); ?></span>
                                      </div>
                                      <div class="form-group">
                                        <label for="end_date">Confirm New Password: </label>
                                        <input type="password" class="form-control" id="Confirm_new_password" name="Confirm_new_password" >
                                        <span class="txt-danger"><?php echo form_error('Confirm_new_password'); ?></span>
                                      </div> 
                                      <button type="submit" class="btn btn-default">Save</button> 
                                    </form>
                                </div>
                            </div>

                        </div>
                        <!-- /panel content -->
                    </div>
                    <!-- /PANEL -->

                </div>
            </section>
            <!-- /MIDDLE -->
<?php include 'footer.php'; ?>