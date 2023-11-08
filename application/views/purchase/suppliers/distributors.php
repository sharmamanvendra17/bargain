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
                <span class="title elipsis header_add">
                    <a href="<?php echo base_url();?>purchase/purchasesuppliers/add">Add New Supplier</a>
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
                <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="datatable_sample">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Party Name</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>City</th> 
                                        <th>State</th> 
                                        <th>GST</th>
                                        <th>Assigned Supplier</th> 
                                        <th>Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($users) { 
                                        $i=1;
                                        foreach ($users as $key => $value) { ?>
                                        <tr class="odd gradeX">
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $value['name']; ?></td>
                                            <td><?php echo $value['mobile']; ?></td>
                                            <td><?php echo $value['address']; ?></td>
                                            <td><?php echo $value['city_name']; ?></td>
                                            <td><?php echo $value['state_name']; ?></td>
                                            <td><?php echo $value['gst_no']; ?></td>
                                            <td><?php echo $value['vendor_name'].' - '.$value['vendor_city_name']; ?></td>
                                            <td><a href="<?php echo base_url(); ?>purchase/purchasesuppliers/edit_distributor/<?php echo base64_encode($value['distributor_id']);?>"> Edit</a> &nbsp;

                                            <!--<a href="<?php echo base_url(); ?>distributors/delete_distributor/<?php echo base64_encode($value['distributor_id']);?>" onClick="if(confirm('Are you want to sure delete this Distributor?')) return true; else return false"> Delete</a>-->
                                            </td>
                                        </tr>
                                    <?php $i++; } } ?> 
                                </tbody>
                            </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include APPPATH.'views/footer.php'; ?>
