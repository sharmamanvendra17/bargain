<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
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
                            <form action="" class="" method="post" enctype='multipart/form-data'>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="mobile">Scheme</label> 
                                            <select class="form-control" id="scheme" name="scheme" required=""> 
                                                <?php if($schemes) { 
                                                    foreach ($schemes as $key => $value) { ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['state'])) { if($_POST['scheme']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['brand_name'].'_'.$value['category_name'].'_'.$value['state_name']; ?></option>
                                                <?php } } ?>
                                            </select>
                                            <span class="txt-danger"><?php echo form_error('state'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php if($details) { $i = 0;
                                    foreach ($details as $key => $value) { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mobile">Dispatched Weight (MT)</label> 
                                            <input type="text" class="form-control" id="" name="target_dispatched_ton[]" required="" value="<?php echo $value['target_dispatched_ton']; ?>">
                                            <span class="txt-danger"><?php echo form_error('target_dispatched_ton'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="address">Reward</label> 
                                            <input type="text" class="form-control" id="" name="reward[]" required="" value="<?php echo $value['reward_name']; ?>">
                                            <span class="txt-danger"><?php echo form_error('reward'); ?></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="address">Image</label> 
                                            <input type="file" class="form-control" id="" name="scheme_image[]"  >
                                            <?php if($value['reward_image']){ ?>
                                                <img src="<?php echo base_url('/public/uploads/scheme_images').'/'.$value['reward_image']; ?>" style="width: 35px; height: 35px;">
                                            <?php } ?>
                                            <span class="txt-danger"><?php echo form_error('scheme_image'); ?></span>
                                            <input type="hidden" name="update_id[]" value="<?php echo $value['id']; ?>">
                                        </div>
                                    </div> 
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label> 
                                        <span rel ="<?php echo $value['id']; ?>" class="<?php echo ($i==0) ? 'addmore' : 'delete'; ?>  btn btn-danger form-control" ><?php echo ($i==0) ? '+' : '-'; ?></span>
                                        <input type="hidden" name="delete_id[]" value="">
                                        </div>
                                    </div>
                                </div> 
                                <?php $i++; } } else { ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="mobile">Dispatched Weight (MT)</label> 
                                            <input type="text" class="form-control" id="" name="target_dispatched_ton[]" required="" value="<?php echo set_value('target_dispatched_ton'); ?>"> 
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="address">Reward</label> 
                                            <input type="text" class="form-control" id="" name="reward[]" required="" value="<?php echo set_value('reward'); ?>">
                                            <span class="txt-danger"><?php echo form_error('reward'); ?></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="address">Image</label> 
                                            <input type="file" class="form-control" id="" name="scheme_image[]"  >
                                            <span class="txt-danger"><?php echo form_error('scheme_image'); ?></span>
                                            <input type="hidden" name="update_id[]" value="">
                                            <input type="hidden" name="delete_id[]" value="">
                                        </div>
                                    </div> 
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label for="mobile"></label> 
                                        <span class="addmore btn btn-danger form-control" >+</span>
                                        </div>
                                    </div>
                                </div> 
                                <?php } ?>
                                <div class="add_more_section"></div>
                                <button type="submit" class="btn btn-default">Save</button> 
                                <a href="<?php echo base_url('schemes') ?>" class="btn btn-default">Cancel</a> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#from_date,#to_date").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
    $(document).on('click', '.delete', function(){  
        var id = $(this).attr("rel");
        $(this).siblings().val(id); 
        $(this).closest('.row').hide();
    });
    $(document).on('click', '.remove', function(){   
        $(this).closest('.row').remove();
    });
    $(document).on('click', '.addmore', function(){  
        $(".add_more_section").append('<div class="row"> <div class="col-md-4"> <div class="form-group"> <label for="mobile">Dispatched Weight (MT)</label> <input type="text" class="form-control" id="" name="target_dispatched_ton[]" required="" value=""> <span class="txt-danger"></span> </div></div><div class="col-md-4"> <div class="form-group"> <label for="address">Reward</label> <input type="text" class="form-control" id="" name="reward[]" required="" value=""> <span class="txt-danger"></span> </div></div><div class="col-md-3"> <div class="form-group"> <label for="address">Image</label> <input type="file" class="form-control" id="" name="scheme_image[]"  ><input type="hidden" name="delete_id[]" value=""> <input type="hidden" name="update_id[]" value=""> <span class="txt-danger"></span> </div></div><div class="col-md-1"> <div class="form-group"> <label></label> <span class="remove btn btn-danger form-control" >-</span> </div></div></div>')
    });
});
</script>