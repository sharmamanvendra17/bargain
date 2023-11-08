<?php include 'header.php'; ?>


            <!-- 
                MIDDLE 
            -->
            <section id="middle">


                <!-- page title -->
                <header id="page-header">
                    <h1>Report Management</h1> 
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
                                <strong>Report </strong> <!-- panel title -->
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
                                    <form action="" class="form-inline" method="post">
                                      <div class="form-group">
                                        <label for="start_date">Start Date :</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php if(isset($_POST['start_date'])) { echo $_POST['start_date']; } ?>">
                                      </div>
                                      <div class="form-group">
                                        <label for="end_date">End Date: </label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php if(isset($_POST['end_date'])) { echo $_POST['end_date']; } ?>">
                                      </div> 
                                      <button type="submit" class="btn btn-default">Search</button>
                                      <button type="submit" class="btn btn-default" name="download">Download</button>
                                    </form>
                                </div>
                            </div>
                            <form method="post" action="">
                                <table class="table table-striped table-bordered table-hover" id="datatable_sample">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>MerchantTxnId</th>
                                            <th>TxnId</th>
                                            <th>AuthIdCode</th>
                                            <th>Amount</th> 
                                            <th>Order Number (Payment Gateway)</th> 
                                            <th>Name</th> 
                                            <th>Invoice Number</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(isset($reports)) { 
                                            $i=1;
                                            foreach ($reports as $key => $value) { ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $value['MerchantTxnId']; ?></td>
                                                <td><?php echo $value['TxnId']; ?></td>
                                                <td><?php echo $value['AuthIdCode']; ?></td>
                                                <td><?php echo $value['amt']; ?></td>
                                                <td><?php echo $value['order_id']; ?></td>
                                                <td><?php echo $value['firstname'].' '.$value['lastname']; ?></td>
                                                <td><?php echo ($value['invoice_id'] ? $value['invoice_id'] : "Not Generated(Cancelled)"); ?></td>
                                            </tr>
                                        <?php $i++; } } ?>
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