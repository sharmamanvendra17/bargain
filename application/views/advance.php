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
                    <a href="<?php echo base_url();?>advance/add">Add New Advance/BG</a>
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
                <form action="<?php echo base_url('advance'); ?>" class="" method="post" id="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">               
                            <label for="party">Party Name</label> 
                            <select class="form-control" id="party" name="party" >
                                <option value="">Select Party</option>
                                <?php if($parties) { 
                                    foreach ($parties as $key => $value) { ?>
                                <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                <?php } } ?>
                            </select>
                            <span class="txt-danger"><?php echo form_error('party'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="company">Deposited Company</label> 
                            <select class="form-control" id="company" name="company" >
                                <option value="">Select Deposited Company</option>
                                <?php if($compnies) { 
                                    foreach ($compnies as $key => $value) { ?>
                                <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['company'])) { if($_POST['company']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                <?php } } ?>
                            </select>
                            <span class="txt-danger"><?php echo form_error('company'); ?></span>
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="verified_by">Verified By</label> 
                            <select class="form-control" id="verified_by" name="verified_by" >
                                <option value="">Select Verified By</option>
                                <?php if($viewers) { 
                                    foreach ($viewers as $key => $value) { ?>
                                    <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['verified_by'])) { if($_POST['verified_by']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                <?php } } ?>
                            </select>
                            <span class="txt-danger"><?php echo form_error('verified_by'); ?></span>
                        </div>
                    </div>   
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="added_by">Added By</label> 
                            <select class="form-control" id="added_by" name="added_by" >
                                <option value="">Select Added By</option>
                                <?php if($added_by) { 
                                    foreach ($added_by as $key => $value) { ?>
                                    <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['added_by'])) { if($_POST['added_by']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                <?php } } ?>
                            </select>
                            <span class="txt-danger"><?php echo form_error('added_by'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"> 
                            <label class="btn-block"></label>
                            <button type="submit" class="btn btn-default" value="Search">Search </button>
                        </div>                                  
                    </div> 
                </div> 
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Party Name</th>
                                            <th>Type</th>
                                            <th>Amont</th>
                                            <th>Deposited In</th>
                                            <th>Verified By</th> 
                                            <th>Added By</th> 
                                            <th>Added date</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($users) { 
                                            $i=1;
                                            $count = 1;
                                            $cur_page =1;
                                            if(isset($limit))
                                                $con_li = $limit;
                                            if($this->uri->segment(3)!='')
                                                $cur_page = $this->uri->segment(3);
                                            $count = ($cur_page-1)*$con_li+1;
                                            foreach ($users as $key => $value) { ?>
                                            <tr class="odd gradeX">
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $value['party_name'].' - '.$value['city_name']; ?></td>
                                                <td class="text-center">
                                                    <?php echo strtoupper($value['advance_type']);
                                                        echo ($value['advance_type']=='bg') ?  '<br>'.date('d-m-Y',strtotime($value['expiry_date'])) : '';

                                                    ?>
                                                        
                                                </td>
                                                <td><?php echo $value['amount']; ?></td>
                                                <td><?php echo $value['company_name']; ?></td>
                                                <td><?php echo $value['verified_by_name']; ?></td>
                                                <td><?php echo $value['added_by_name']; ?></td> 
                                                <td><?php echo date('d-m-Y H:i:s', strtotime($value['created_at'])); ?></td> 
                                            </tr>
                                        <?php $count++; } } ?> 
                                        <tr>
                                            <td colspan="8">
                                                <?php echo $links; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){
    $(document).ready(function(){
        $("#party,#verified_by,#company,#added_by").select2();  
    });
});
</script>