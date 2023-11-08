<?php include 'header.php'; ?>
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
                                    <label for="name">Category </label> 
                                    <select class="form-control" id="category" name="category" required="">
                                        <option value="">Select Category</option> 
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                </div>  
                                <div class="form-group">
                                    <label for="loose_rate">Rate (15 Ltr Tin)</label> 
                                    <input type="text" class="form-control" id="rate" name="rate" required="" value="<?php if(isset($_POST['rate'])) echo $_POST['rate']; ?>">
                                    <span class="txt-danger"><?php echo form_error('rate'); ?></span>
                                </div> 
                                <div class="form-group"> 
                                    <input type="checkbox"  id="is_ex_rate" name="is_ex_rate" value="1"  checked> Ex Rate

                                    <input type="checkbox"  id="insurance" name="insurance" value="1"  style="margin-left: 20px;"> Insurace Included in price 

                                </div>  
                                <button type="submit" class="btn btn-default">Save</button> 
                            </form>
                        </div>
                    </div>
                <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="datatable_sample">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Brand</th>
                            <th>Category Name</th>
                            <th>Rate (15 Ltr Tin)</th>    
                            <th>Date</th> 
                            <th>Added By</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($rates) { 
                            $i=1;
                            foreach ($rates as $key => $value) { ?>
                                <tr class="odd gradeX">
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $value['brand_name']; ?></td>
                                    <td><?php echo $value['category_name']; ?></td>
                                    <td><?php echo $value['rate']; echo ($value['is_ex_rate']) ? ' (Ex)' : ' (FOR)';  echo ($value['insurance_included']) ? ' (Ins.)' : '';  ?></td>  
                                    <td><?php echo date('d-m-Y h:i:s', strtotime($value['created_at'])); ?></td>
                                    <td><?php echo $value['admin_name']; ?></td>
                                     
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
<script>
$(document).ready(function(){
    $("#brand").change(function(){
        var brand_id = $(this).val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getcategory',
            data: { 'brand_id': brand_id},
            success: function(msg){
                $("#category").html(msg);
            }
        });
    });
    $("#category").change(function(){
        var category_id = $(this).val(); 
        var brand_id = $('#brand').val();  
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>product/getrate',
            data: { 'category_id': category_id,'brand_id': brand_id},
            success: function(rate){
                //alert(rate);
                var obj = rate.split("_");
                $("#rate").val(obj[0]); 
                var is_ex_rate = obj[1]; 
                var insurance_included = obj[2]; 
                if(is_ex_rate==0)
                {
                    $("#is_ex_rate").removeAttr('checked');
                }
                else
                {
                    $("#is_ex_rate").attr("checked",true);
                    $("#is_ex_rate").prop("checked","checked");
                }

                if(insurance_included==0)
                {
                    $("#insurance").removeAttr('checked');
                }
                else
                {
                    $("#insurance").attr("checked",true);
                    $("#insurance").prop("checked","checked");
                } 
            }
        });
    });
});
</script>