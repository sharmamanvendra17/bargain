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
                <span class="title elipsis header_add">
                    <a href="<?php echo base_url();?>rate/pdf" target="_blank"><img src="<?php echo base_url(); ?>assets/img/pdf-bl.png" title="Download SKU Rate PDF" width="30"> SKU Rate PDF</a>
                    &nbsp;&nbsp;
                    <a href="javascript:void(0)"  class="users"> <img src="<?php echo base_url(); ?>assets/img/whatsappIcon.png" title="Whatsapp SKU Rate PDF" width="30"></a>

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
                                    <select class="form-control" id="brand" name="brand" required>
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
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option> 
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                </div>  
                                <div class="form-group">
                                    <label for="loose_rate">Base Rate</label> 
                                    <input type="text" class="form-control" id="base_rate" name="base_rate" required value="<?php if(isset($_POST['base_rate'])) echo $_POST['base_rate']; ?>">
                                    <span class="txt-danger"><?php echo form_error('base_rate'); ?></span>
                                </div> 

                                <div class="form-group">
                                    <label for="name">Packaging </label> 
                                    <select class="form-control" id="packaging" name="packaging" required>
                                        <option value="">Select Packaging</option>
                                        <?php if($packaging) { 
                                            foreach ($packaging as $key => $value) { ?>
                                            <option value="<?php echo $value['weight']; ?>" loose="<?php echo $value['is_loose']; ?>" tin-type="<?php echo $value['packing']; ?>" tin-rate="<?php echo $value['tin_rate']; ?>" small-pack="<?php echo $value['small_pack']; ?>" data-id="<?php echo $value['packing_type']; ?>" <?php if(isset($_POST['brand'])) { if($_POST['brand']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['packaging']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('packaging'); ?></span>
                                    <span class="formaula" style="color:red;"></span>
                                </div>

                                <div class="form-group">
                                    <label for="loose_rate">Rate (15 Ltr Tin)</label> 
                                    <input type="text" class="form-control" id="rate" name="rate" required value="<?php if(isset($_POST['rate'])) echo $_POST['rate']; ?>">
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
<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8; display: none;">
<p style="position: absolute; color: White; top: 50%; left: 45%;">
please wait...
<img src="<?php echo base_url();?>assets/images/loaders/4.gif">
</p>
</div>
<script>
$(document).ready(function(){

    $("#packaging").change(function(){
        var base_rate = $("#base_rate").val(); 
        var weight = $(this).val();
        var brand = $("#brand").val();
        var category = $("#category").val();
        var category_name = $("#category option:selected").text();
        var category_name = category_name.toLowerCase();
        var category_name = category_name.trim(); 
        if(category=='' || brand =="" || base_rate=='')
        {
            alert("Please select Brand Category and enter base rate");
            $(this).val('');
            return false;
        }
        if(weight!='')
        {
            var weight_type = $(":selected",this).attr('data-id');
            var product_id = $(":selected",this).attr('tin-rate');
            var smallpack = $(":selected",this).attr('small-pack'); 
            var tintype = $(":selected",this).attr('tin-type'); 
            var loose = $(":selected",this).attr('loose'); 
            
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>emptytinrate/getrate',
                data: { 'tintype': tintype, 'brand': brand,'category': category,'weight_type': weight_type,'weight': weight, 'product_id': product_id,'smallpack' : smallpack},
                success: function(emptytinrate){
					//alert(emptytinrate);  
                    var emptytinrate = parseFloat(emptytinrate);
                        if(loose==1)
                            emptytinrate = 0;
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo base_url(); ?>emptytinrate/getrate',
                            data: { 'tintype': 'tin', 'brand': brand,'category': category,'weight_type': weight_type,'weight': 15,'product_id': 104,'smallpack' : 0},
                            success: function(emptytinrate_add){ 
							
                                var emptytinrate_add = parseFloat(emptytinrate_add);
                                var conversion_factor = 1;
                                if(weight_type==1 && category_name !='vanaspati')
                                    conversion_factor = .91;
                                if(weight_type==1 && category_name =='vanaspati')
                                    conversion_factor = .897;

                                var smallpack_rate = 0;
                                if(smallpack==1)
                                {
                                    smallpack_rate = emptytinrate-60;   
                                    //emptytinrate = -(emptytinrate-60);
                                    var rate_15_ltr =  ((((base_rate-emptytinrate+smallpack_rate)*conversion_factor)/weight)*15)+emptytinrate_add;
                                    var formaula =  "(((("+base_rate+"-"+emptytinrate+"+"+smallpack_rate+")*"+conversion_factor+")/"+weight+")*15)+"+emptytinrate_add; 
                                }
                                else
                                {
                                   var rate_15_ltr =  ((((base_rate-(emptytinrate+smallpack_rate))*conversion_factor)/weight)*15)+emptytinrate_add;
                                    var formaula =  "(((("+base_rate+"-("+emptytinrate+"+"+smallpack_rate+"))*"+conversion_factor+")/"+weight+")*15)+"+emptytinrate_add; 
                                }
                                
                                //alert(rate_15_ltr1);
                                $(".formaula").html(formaula);
                                $("#rate").val(rate_15_ltr.toFixed(2));
                            }
                        });  
                }
            });
        }
        else
        {
            $(".formaula").html('');
            $("#rate").val('');
        }        
    });

    $(".users").click(function(e){ 
        e.preventDefault();
        $("#divLoading").css({display: "block"});
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>rate/rateusers',
            data: {},
            success: function(msg){ 
                $("#divLoading").css({display: "none"});
                $("#DetailModal").modal('show');
                $(".userdetails").html(msg);
            }
        });
    });

    $("body").on("click",".whatsapppdf",function(e){ 
        //console.log($('input[name="users"]:checked').serialize());
        e.preventDefault();
        var numbers = Array();
        //alert(JSON.stringify($("input:checkbox[name=users]:checked")));
        var flag = 0;
        $("input[name='users']:checked").each(function(){ 
            flag = 1;
            numbers.push($(this).val());
        });
        if(flag==0)
        {
            alert("Please select at least one mobile number.");
            return false;
        }
        //alert(numbers); 
        $("#divLoading").css({display: "block"});
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>rate/whatsapppdf',
            data: {'numbers' : numbers},
            success: function(msg){ 
                $("#divLoading").css({display: "none"});
                $("#DetailModal").modal('hide');
                alert("PDF Whatssapp successfully");
            }
        });
    });

    
    $(".checkall").click(function(e){ 
        e.preventDefault();
        $(".user_select").prop("checked",true);
    });

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

<div id="DetailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
                <h4 class="modal-title">These numbers will be notified via whatsapp</h4>
            </div>
            <div class="modal-body"> 
                <div class="table-responsive userdetails">
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)"  class="whatsapppdf btn btn-default">Send Whatsapp</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
  </div>
</div>