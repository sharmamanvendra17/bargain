<?php include 'header.php'; ?>


            <!-- 
                MIDDLE 
            -->
            <section id="middle">


                <!-- page title -->
                <header id="page-header">
                    <h1>Order Details</h1> 
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
                            <span style="text-align: right; float: right;" class="title elipsis">
                                <a href="javascript:void(0)" class="create_invoice" rel="<?php echo $order['order_id']; ?>">Invoice</a>
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
                                <div class="col-md-6">
                                    <h4><?php echo $order['firstname'].' '.$order['lastname']; ?></h4>
                                    <p class="nomargin">
                                        <?php echo $order['address']; ?><br>
                                        <?php echo $order['address2']; ?><br>
                                        <?php echo $order['city'].','.$order['state'].','.$order['country'].','.$order['postcode']; ?><br>
                                        <?php echo $order['phone']; ?><br>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="nomargin">
                                        <strong>Invoice Id : </strong><?php echo "ASHOKA".str_pad($order['invoice_id'],5,0,STR_PAD_LEFT); ?><br>
                                        <strong>Order  Id : </strong><?php echo "HARI-".str_pad($order['order_id'],5,0,STR_PAD_LEFT);  ?><br>
                                        <strong>Transaction Id : </strong><?php echo $order['TxnId']; ?><br> 
                                        <strong>Date : </strong><?php echo $order['created_at']; ?><br>
                                        <strong>Status : </strong><b><?php echo $order['payment_status']; ?></b><br> 
                                    </p>
                                </div>
                            </div>

                                <?php $order_items = json_decode($order['order_history']); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered table-hover" id="datatable_sample1">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Product Name</th>
                                                <th>Product Price</th>
                                                <th>Qty</th>  
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($order_items)) { 
                                                $i=1;
                                                foreach ($order_items as $key => $value) { ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $value->product_name.' | '.$value->product_packing; ?></td>
                                                    <td><?php echo $value->product_price; ?></td> 
                                                    <td><?php echo $value->product_qty; ?></td>
                                                </tr>
                                            <?php $i++; } } ?>
                                        </tbody>    
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <table class="table table-striped table-bordered table-hover" >
                                        <tr>
                                            <td>Subtotal</td>
                                            <td><?php echo $order['sub_total']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td> - <?php echo $order['discount']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping</td>
                                            <td><?php echo $order['shipping_charge']; ?></td>
                                        </tr>
                                        <?php if($order['state']=="Rajasthan")
                                        { ?> 
                                            <tr>
                                                <td>SGST</td>
                                                <td><?php echo number_format((float)($order['gst']/2), 2, '.', '');  ?></td>
                                            </tr>
                                            <tr>
                                                <td>CGST</td>
                                                <td><?php echo number_format((float)($order['gst']/2), 2, '.', '');  ?></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr>
                                                <td>IGST</td>
                                                <td><?php echo $order['gst']; ?></td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <td>Grand Total</td>
                                            <td><?php echo round($order['grand_total']); ?></td>
                                        </tr>
                                    </table>
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
<script>
$(document).ready(function(){
    $(".create_invoice").click(function(){
 
        var order_id = $(this).attr('rel');
        var dataString  = { order_id  : order_id };   
        $.ajax({
            type: "POST",
            url: "<?php echo base_url();?>index.php/admin/order_admin/create_pdf",
            data: dataString, 
            success: function(data){  
                //alert(data); 
                if(data)
                {
                   var valFileDownloadPath = 'http//:'+'your url';

                    window.open(data , '_blank');
                }
            } ,error: function(xhr, status, error) {
                alert(status);
            },
        });         
    });
});
</script>