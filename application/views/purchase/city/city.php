<?php include APPPATH.'views/header.php'; ?>
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1> 
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <div class="panel-heading">
                <span class="title elipsis">
                    <strong><?php echo $title; ?></strong> <!-- panel title -->
                </span> 
                <span class="title elipsis header_add">
                    <a href="<?php echo base_url();?>purchase/purchasecity/add">Add New City</a>
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
                            <th>Name</th>  
                            <th>Status</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($cities) { 
                            $i=1;
                            foreach ($cities as $key => $value) { ?>
                                <tr class="odd gradeX">
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $value['name']; ?></td>  
                                    <td><?php echo $value['state_name']; ?></td>   
                                    <td><a href="<?php echo base_url();?>purchase/purchasecity/edit_city/<?php echo base64_encode($value['id']) ?>">Edit</a> </</td> 
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
            url: '<?php echo base_url();?>purchase/purchasecategory/updateStatus',
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