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
                            <form action="" class="" method="post">
                                

                                <div class="form-group">
                                    <label for="name">Name</label> 
                                    <input type="hidden" class="form-control" id="employee_id" name="employee_id" required="" value="<?php echo $admin['id']; ?>">
                                    <input type="text" class="form-control" id="name" name="name" required="" value="<?php echo $admin['name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="username">Email</label> 
                                    <input type="text" class="form-control" id="username" name="username" required="" value="<?php echo $admin['username']; ?>">
                                    <span class="txt-danger"><?php echo form_error('username'); ?></span>
                                </div>
                                <?php $selected_states = explode(',', $admin['state_id']); ?>
                                <div class="form-group">
                                    <label for="state">Working Area</label> 
                                    <select class="form-control" id="state" name="state[]"  multiple>
                                        <option value="">Select State</option>
                                        <?php if($states) { 
                                            foreach ($states as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"<?php  if(in_array($value['id'], $selected_states)) { echo "selected"; } ; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('state'); ?></span>
                                </div> 
                                <div class="form-group">
                                    <label for="role">Role </label> 
                                    <select class="form-control" id="role" name="role" required="">
                                        <option value="">Select Role</option>
                                        <?php
                                        foreach ($roles as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if($admin['role']==$value['id']) echo "selected"; ?>><?php echo  ucwords($value['role_name']); ?> </option>
                                        <?php } ?> 
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('role'); ?></span>
                                </div>   

                                <div class="form-group teamlead_section" style="<?php echo ($admin['role']==1) ? '' : 'display: none;'; ?>">
                                    <label for="role">Bragain Approver</label> 
                                    <select class="form-control" id="teamlead" name="teamlead" >
                                        <option value="">Select Bragain Approver</option>
                                        <?php foreach ($checkers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo ($value['id']==$admin['team_lead_id']) ? 'selected' : ''; ?> ><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('teamlead'); ?></span>
                                </div>   
                                <?php $authorized_viewers = explode(',',$admin['unauthorized_viewers']); 
                                    //echo "<pre>"; print_r($authorized_viewers); ?>
                                <div class="form-group Unauthorized_section" style="<?php echo ($admin['role']==1) ? '' : 'display: none;'; ?>">
                                    <label for="viewer">Unauthorized Bragain Viewer</label> 
                                    <select class="form-control" id="viewer" name="viewer[]" multiple> 
                                        <?php
                                        foreach ($viewers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo (in_array($value['id'], $authorized_viewers)) ? 'selected' : ''; ?>><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('viewer'); ?></span>
                                </div>    
                                <div class="form-group"> 
                                    <input type="checkbox"  id="allow_rate" name="allow_rate" value="1" <?php echo ($admin['allow_rate']==1) ? "checked" : ""; ?> > Allow Rate Entry Access (This will update master rate)
                                </div> 
                                <div class="form-group"> 
                                    <input type="checkbox"  id="allow_rate_booking" name="allow_rate_booking" value="1" <?php echo ($admin['allow_rate_booking']==1) ? "checked" : ""; ?> > Allow Allow Rate Change While Booking
                                </div>                            
                                <button type="submit" class="btn btn-default">Save</button> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>
<script>
$(document).ready(function(){
    $('#teamlead').removeAttr('required');
    $("#role").change(function(){
        var role = $(this).val();
        if(role==1)
        {
            $('#teamlead').attr('required',"required");
            $('.teamlead_section').show();
        }
        else
        {
            $('#teamlead').removeAttr('required');
            $('.teamlead_section').hide();
        }
    });  
    $("#teamlead").change(function(){
        var teamlead = $(this).val();
        if(teamlead)
        {
            $('#viewer').attr('required',"required");
            $('#teamlead').attr('required',"required");
            $('.Unauthorized_section').show();
        }
        else
        { 
            $('#teamlead').removeAttr('required');
            $('.Unauthorized_section').hide();
        }
    });
});
</script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
    $(document).ready(function(){ 
        $("#viewer").select2(); 
        $("#state").select2(); 
    });
</script>