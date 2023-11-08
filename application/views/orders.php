<?php include 'header.php'; ?>


            <!-- 
                MIDDLE 
            -->
            <section id="middle">


                <!-- page title -->
                <header id="page-header">
                    <h1>Order Management</h1> 
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
                                <strong>Orders </strong> <!-- panel title -->
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
                                <table class="table table-striped table-bordered table-hover" id="datatable_sample">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Invoice Id</th>
                                            <th>Order ID</th>
                                            <th>Name</th>
                                            <th>Amount</th> 
                                            <th>Txn Id</th> 
                                            <th>Paymnet Staus</th> 
                                            <th>Date</th> 
                                            <th>Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(isset($orders)) { 
                                            $i=1;
                                            foreach ($orders as $key => $value) { ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo "ASHOKA".str_pad($value['invoice_id'],5,0,STR_PAD_LEFT); ?></td>
                                                <td><?php echo "HARI-".str_pad($value['order_id'],5,0,STR_PAD_LEFT); ?></td>
                                                <td><?php echo $value['firstname'].' '.$value['lastname']; ?></td>
                                                <td><?php echo $value['amt']; ?></td>
                                                <td><?php echo $value['TxnId']; ?></td>
                                                <td><?php echo $value['payment_status']; ?></td>
                                                <td><?php echo $value['created_at']; ?></td>
                                                <td><a href="<?php echo base_url(); ?>admin/order/<?php echo base64_encode($value['order_id']); ?>">View</a></td>
                                            </tr>
                                        <?php $i++; } } ?>
                                    </tbody>    
                                </table> 

                        </div>
                        <!-- /panel content -->
                    </div>
                    <!-- /PANEL -->

                </div>
            </section>
            <!-- /MIDDLE -->
<?php include 'footer.php'; ?>