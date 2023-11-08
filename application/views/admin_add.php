<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
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
                                    <input type="text" class="form-control" id="name" name="name" required="" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="username">Email</label> 
                                    <input type="text" class="form-control" id="username" name="username" required="" value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>">
                                    <span class="txt-danger"><?php echo form_error('username'); ?></span>
                                </div>
 
                                <div class="form-group">
                                    <label for="confirm_password">Mobile</label> 
                                    <input type="text" class="form-control" id="mobile" name="mobile" required="" value="<?php if(isset($_POST['mobile'])) echo $_POST['mobile']; ?>">
                                    <span class="txt-danger"><?php echo form_error('mobile'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="name">Joining Date</label> 
                                    <input type="text" class="form-control" id="joining_date" name="joining_date"  value="<?php if(isset($_POST['joining_date'])) echo $_POST['joining_date']; ?>">
                                    <span class="txt-danger"><?php echo form_error('joining_date'); ?></span>
                                </div>
                                <div class="form-group">
                                    <?php $business_role = array(1=>'Sales',2=>'Purchase',3=>'Both'); ?>
                                    <label for="business_role">Business Role </label> 
                                    <select class="form-control" id="business_role" name="business_role" required="">
                                        <option value="">Select Buiness</option>
                                        <?php if($business_role) 
                                        {
                                            foreach ($business_role as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                            <?php }
                                        }?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('business_role'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="state">Working Area</label> 
                                    <select class="form-control" id="state" name="state[]"  multiple>
                                        <option value="">Select State</option>
                                        <?php if($states) { 
                                            foreach ($states as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['state'])) { if($_POST['state']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
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
                                            <option value="<?php echo $value['id']; ?>"><?php echo  ucwords($value['role_name']); ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('role'); ?></span>
                                </div>  
                                <div class="form-group maker_section" style="display: none;">
                                    <label for="maker">Maker</label> 
                                    <select class="form-control" id="maker" name="maker" >
                                        <option value="">Select Maker</option>
                                        <?php
                                        foreach ($makers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('maker'); ?></span>
                                </div>
                                <div class="form-group maker_section" style="display: none;">
                                    <label for="maker">Super Distributor</label> 
                                    <select class="form-control" id="vendor" name="vendor[]" multiple>
                                        <option value="">Select Super Distributor</option>
                                        <?php
                                        foreach ($vendors as $key => $vendors) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('maker'); ?></span>
                                </div>

                                <div class="form-group performance_viewer_section" style="display: none;">
                                    <label for="performance_viewer">Team Lead</label> 
                                    <select class="form-control" id="performance_viewer" name="performance_viewer" >
                                        <option value="">Select Team Lead</option>
                                        <?php
                                        foreach ($checkers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('performance_viewer'); ?></span>
                                </div> 

                                <div class="form-group teamlead_section" style="display: none;">
                                    <label for="role">Bragain Approver</label> 
                                    <select class="form-control" id="teamlead" name="teamlead" >
                                        <option value="">Select Bragain Approver</option>
                                        <?php
                                        foreach ($checkers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('teamlead'); ?></span>
                                </div> 
                                <div class="form-group Unauthorized_section" style="display: none;">
                                    <label for="viewer">Unauthorized Bragain Viewer</label> 
                                    <select class="form-control" id="viewer" name="viewer[]" multiple> 
                                        <?php
                                        foreach ($viewers as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('viewer'); ?></span>
                                </div>

                                <div class="form-group dispetcher_access" style="display:none;">
                                    <?php $plants = array('All','Jaipur','Alwar'); ?>
                                    <label for="dispetcher_access">PI Access</label> 
                                    <select class="form-control" id="dispetcher_access" name="dispetcher_access" >
                                        <option value="">Select PI Access</option>
                                        <?php if($plants) 
                                        {
                                            foreach ($plants as $key => $value) { ?>
                                                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                            <?php }
                                        }?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('dispetcher_access'); ?></span>
                                </div>

                                <div class="rate_box"> 
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="allow_rate" name="allow_rate" value="1"> Allow Rate Entry Access (This will update master rate)
                                    </div>  
                                    <div class="form-group"> 
                                        <input type="checkbox"  id="allow_rate_booking" name="allow_rate_booking" value="1" > Allow Rate Change While Booking
                                    </div> 
                                    <div class="form-group teamlead_section" style="display: none;"> 
                                        <input type="checkbox"  id="pi_making_access" name="pi_making_access" value="1"  > Allow PI Making Access 
                                    </div>

                                </div>
                                <div class="form-group"> 
                                        <input type="checkbox"  id="rate_whatsapp" name="rate_whatsapp" value="1"> Rate List On Whatsapp
                                </div> 
                                <div class="persona_section">
                                    <div class="form-group">
                                        <input type="checkbox"  id="persona_user_allow" name="persona_user_allow" value="1"> Allow Persona Access  ( This person will have access to see bargains and add sku. ) 
                                    </div>
                                    <div class="form-group persona_user_list" style="display: none;">
                                        <select class="form-control" name="persona_user[]" id="persona_user" multiple >
                                            <option value="">Select persona user</option>
                                            <?php
                                            foreach ($makers as $key => $value) { ?>
                                                <option value="<?php echo $value['id']; ?>"  ><?php echo $value['name'].' - '.$value['username']; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#joining_date").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script>
$(document).ready(function(){
    $('#teamlead').removeAttr('required');

    
    $("#state").change(function(){
        var state = $(this).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>admins/GetSuperDistributers',
            data: { 'state': state},
            success: function(msg){ 
                $("#vendor").html(msg);
            }
        }); 
    });
    $("#role").change(function(){
        var role = $(this).val();
        if(role==1)
        {
            
            $('.performance_viewer_section').show();
            $('.dispetcher_access').hide();
            $('.rate_box').show();
            $('.maker_section').hide();
            //$('#teamlead').attr('required',"required");
            $('.teamlead_section').show();
            $('.persona_section').show();
        }
        else if(role==6)
        {
            $('.performance_viewer_section').show();
            $('.dispetcher_access').hide();
            $('.rate_box').hide();
            $('.maker_section').show(); 
            $('.Unauthorized_section').hide(); 
            $('#teamlead').removeAttr('required');
            $('.teamlead_section').hide();
            $('.persona_section').hide();
        }
        else if(role==8)
        {
            $('.performance_viewer_section').hide();
            $('.maker_section').hide();
            $('.rate_box').show();
            $('#teamlead').removeAttr('required');
            $('.teamlead_section').hide();
            $('.dispetcher_access').show();
            $('.persona_section').hide();
        }
        else
        {
            $('.performance_viewer_section').hide();
            $('.dispetcher_access').hide();
            $('.maker_section').hide();
            $('.rate_box').show();
            $('#teamlead').removeAttr('required');
            $('.teamlead_section').hide();
            $('.persona_section').hide();
        }
    }); 
    $("#teamlead").change(function(){
        var teamlead = $(this).val();
        if(teamlead)
        {
            //$('#viewer').attr('required',"required");
            $('#teamlead').attr('required',"required");
            $('.Unauthorized_section').show();
        }
        else
        { 
            $('#teamlead').removeAttr('required');
            $('.Unauthorized_section').hide();
        }
    });
    $("#persona_user_allow").click(function(){

        if($('#persona_user_allow').is(':checked'))
        { 
            $('.persona_user_list').show();
        }
        else
        {  
            $('.persona_user_list').hide();
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
        $("#vendor").select2(); 
        $("#performance_viewer").select2(); 
        $("#persona_user").select2();     
    });
</script>