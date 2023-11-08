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
                <span class="title elipsis header_add">
                    <a href="<?php echo base_url();?>brand/add">Add New Brand</a>
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
                                        <th>Brand Name</th> 
                                        <th>Status</th>
                                        <th>Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($categories) { 
                                        $i=1;
                                        foreach ($categories as $key => $value) { ?>
                                        <tr class="odd gradeX">
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $value['name']; ?></td> 
                                            <td  class="text-center">
                                                <?php if ($value['status'] == '1') { ?>
                                                    <a data-id="<?php echo $value['id']; ?>"  class="btn btn-sm btn-success changeStatus round" data-status="1"  title="Click to Mark as Active">Active</a>
                                                <?php } elseif ($value['status'] == '0') { ?>
                                                    <a data-id="<?php echo $value['id']; ?>" class="btn btn-sm btn-danger changeStatus round" data-status="0"  title="Click to Mark as Deactive">Deactive</a>
                                                <?php } ?>
                                            </td>
                                            <td><a href="<?php echo base_url(); ?>brand/edit/<?php echo base64_encode($value['id']); ?>">Edit</a> &nbsp; &nbsp; <a href="<?php echo base_url(); ?>brand/delete/<?php echo base64_encode($value['id']); ?>" onClick="if(confirm('Are you want to sure delete this brand?')) return true; else return false">Delete</a></td>
                                        </tr>
                                    <?php $i++; } } ?> 
                                </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>

<script type="text/javascript"> 
    $(document).on('click', '.changeStatus', function () {
        //alert("sdasda");
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-status');
        var status1;
        var ele;
        ele = $(this);
        status1  = status;
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>brand/updateStatus',
            data: {id: id, status: status},
            dataType: "html",
            success: function(response){
                if (response == 1) {
                    //switch (status) {
                    if(status1 =='0')
                    {
                      ele.attr('data-status', '1');
                      ele.html('Active');
                      ele.addClass('btn-success');
                      ele.removeClass('btn-danger');
                      alert('Status Changed, Markerd as Activated.'); 
                    }
                    else
                    {
                      ele.attr('data-status', '0');
                      ele.html('Deactive');
                      ele.addClass('btn-danger');
                      ele.removeClass('btn-success');
                      alert('Status Changed, Markerd as deactivated.'); 
                    } 
                }  
                else
                {
                    alert('An error is occured please try again.');
                } 
            }
        });
    });
</script>