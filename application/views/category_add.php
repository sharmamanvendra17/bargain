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
                                    <label for="name">Category Name</label> 
                                    <input type="text" class="form-control" id="name" name="name" required="" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="name">Brand </label> 
                                    <select class="form-control" id="brand" name="brand" required="">
                                        <option value="">Select Brand</option>
                                        <?php if($brands) { 
                                            foreach ($brands as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['brand'])) { if($_POST['brand']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('brand'); ?></span>
                                </div> 

                                <div class="form-group">
                                    <label for="name">Empty Tin Rate</label> 
                                    <input type="text" class="form-control" id="tin_rate" name="tin_rate" required value="<?php echo (isset($_POST['tin_rate'])) ? $_POST['tin_rate'] : 0; ?>">
                                    <span class="txt-danger"><?php echo form_error('tin_rate'); ?></span>
                                </div> 
                                
                                <div class="form-group">
                                    <label for="alias">Alias</label> 
                                    <select class="form-control" id="alias" name="alias" required="">
                                        <option value="">Select Alias</option>
                                        <?php
                                            if($alias)
                                            {
                                                foreach ($alias as $key => $value) { ?>
                                                    <option value="<?php echo $value['alias_name']; ?>"><?php echo $value['category_name']; ?>-<?php echo $value['alias_name']; ?></option>
                                                <?php }
                                            }
                                        ?>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group other_alias_name_s" style="display: none;"> 
                                    <input type="text" class="form-control" id="other_alias_name" name="other_alias_name" value="<?php if(isset($_POST['other_alias_name'])) echo $_POST['other_alias_name']; ?>">
                                    <span class="txt-danger"><?php echo form_error('other_alias_name'); ?></span>
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
<?php 
$package_html = '';
if($packages) {

    foreach ($packages as $key => $package) {
        $package_html .= '<option value="'.$package['package'].'">'.$package['package'].'</option>';
    }
} ?>
<script>
$(document).ready(function(){

    var max_fields = 100;
    var base_url = '<?php echo base_url();?>';
    var package_html = '<?php echo $package_html;?>';
    var add_input_button = $('.add_input_button');
    var field_wrapper = $('.field_wrapper');
    var input_count = 1;
    $("[id='other_alias_name']").keyup(function () { 
         if (this.value.match(/[^a-zA-Z]/g, '')) { 
          this.value = this.value.replace(/[^a-zA-Z]/g, '');      
        } 
    });
    $("#alias").change(function(){
        var alias = this.value; 
        if(alias=='other')
        {
            $("#other_alias_name").prop('required',true);
            $('.other_alias_name_s').show();
        }
        else
        {
            $("#other_alias_name").prop('required',false);
            $('.other_alias_name_s').hide();   
        }
         
    });

    // Add button dynamically
    $(add_input_button).click(function(){
        if(input_count < max_fields){
            input_count++;
            var new_field_html = '<div class="row"><div class="col-md-1"><div class="form-group" style="margin-top: 30px;"><span for="hsn">'+input_count+'</span></div></div><div class="col-md-3"><div class="form-group"><label for="hsn">Package</label><select name="package[]" id="package_'+input_count+'" class="form-control" required=""><option value="">Select Package</option>'+package_html+'</select></div></div><div class="col-md-3"><div class="row"><div class="col-md-6"><div class="form-group"><label for="weight">Weight</label><input type="text" class="form-control" id="weight_'+input_count+'" name="weight[]" required="" value=""></div></div><div class="col-md-6"><div class="form-group"><label for="weight">Unit</label><select name="unit[]" id="unit_'+input_count+'" class="form-control" required=""><option value="kg">Kg</option><option value="ltr">Ltr</option><option value="ml">Ml</option></select></div></div></div></div><div class="col-md-2"><div class="form-group"><label for="status">Status</label><select name="status[]" id="status_'+input_count+'" class="form-control" required=""><option value="0">Enable</option><option value="1">Disable</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="sku">SKU</label><input type="text" class="form-control" id="sku_'+input_count+'" name="sku[]" required="" value=""></div></div><div class="col-md-1"><div class="form-group" style="margin-top: 30px;"><a href="javascript:void(0);" class="remove_input_button" title="Remove field"><img src="'+base_url+'assets/images/remove-icon.png"> </a></div></div></div>';
            $(field_wrapper).append(new_field_html);
        }
    });
    // Remove dynamically added button
    $(field_wrapper).on('click', '.remove_input_button', function(e){
        e.preventDefault();
        $(this).closest('.row').remove();
        input_count--;
    });
});
</script>