<?php include 'header.php'; ?>
<style type="text/css">
    .panel-body .table th {
    text-align: center;
    vertical-align: top;
}
.table_report { border: none; }
.table_report td { font-weight: normal; }
</style>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1> 
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <form action="" class="" method="post" id="emptytinrate"> 
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
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="category_id" value="<?php  echo base64_decode($this->uri->segment(4)); ?>">
                                <input type="hidden" name="brand_id" value="<?php  echo base64_decode($this->uri->segment(3)); ?>">
                                <div class="form-group">
                                    <select class="form-control" id="" name="">
                                        <option><?php echo $info['brand_name']; ?></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" id="" name="">
                                        <option><?php echo $info['category_name']; ?></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                        <select class="form-control" id="state" name="state" required>
                                            <option value="">Select State</option>
                                            <?php if($states) { 
                                                foreach ($states as $key => $value) { ?>
                                                <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['state'])) { if($_POST['state']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                </div>
                            </div>
                        </div>

                        <div class="packing">
                            <?php if($products) 
                            {
                                $res = '';
                                $i = 1;            
                                foreach ($products as $key => $value) {
                                    $bs_rst = (strtolower(str_replace(' ','',$value['name']))=='15ltrtin') ? 1 : 0;
                                    $res .= '<input type="hidden" name="base_rates[]" value="'.$bs_rst.'">';
                                    $res .= '<div class="row"><div class="col-md-6"><div class="form-group">';
                                    if($i==1)
                                        $res .= '<label for="name">Packed In</label>';
                                    $res .= '<select class="form-control" id="" name="product[]">';
                                    $packing_items_qty = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                                    $res .= '<option value="'.$value['id'].'">'.$value['name'].$packing_items_qty.'</option>';
                                    $res .= '</select></div></div><div class="col-md-6"><div class="form-group">';
                                    if($i==1)
                                        $res .= '<label for="name">Rate</label>';
                                    $res .='<input  class="form-control rate" type="text"  name="rate[]" value=""></div></div>';

                                    $res .= '</div>';
                                    $res .= '<input type="hidden" name="type[]" >';
                                    $i++;
                                }
                               echo $res;  
                            } ?>
                        </div>    
                        <button type="submit" class="btn btn-default save_rates">Save</button> 
                    </div>
                </div>                  
            </div>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script>
$(document).ready(function(){  
    var category_id = "<?php  echo base64_decode($this->uri->segment(4)); ?>";
    var brand_id = "<?php  echo base64_decode($this->uri->segment(3)); ?>";
    $(document).on('keyup', '.rate', function(){
        var state_id = $("#state").val(); 
        if(state_id=='')
        {
            alert("Select State First");
        }
         if (this.value.match(/[^0-9.]/g, '')) { 
          this.value = this.value.replace(/[^0-9.]/g, '');      

        } 
    });  
    $("#state").change(function(){
        var state_id = $(this).val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>emptytinrate/getskus',
            data: { 'state_id': state_id,'category_id': category_id,'brand_id': brand_id},
            success: function(msg){
                $(".packing").html(msg);
            }
        });
    });   
    $(document).on("click", ".save_rates", function(event){ 
        event.preventDefault();
        var state_id = $("#state").val(); 
        if(state_id=='')
        {
            alert("Select state first");
            return false;
        }
        $(".loader").show();
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>emptytinrate/save_rate/',
            data: $("#emptytinrate").serializeArray(), 
            dataType: "html",
            success: function(data){
                $(".loader").hide(); 
                if(data)
                {
                    alert("rates addedd successfully");
                }
                else
                {
                    alert("Something went wrong try again");
                }
            }
        });
    });
});
</script>
 
 



<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
 
<!-- Trigger the modal with a button -->
 
<div id="BookingSuccessModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Success</h4>
            </div>
            <div class="modal-body "> 
                    <div class="order_booked"></div>
                    <div class="row booked_sku_info"> 

                    </div>
            </div>
            <div class="modal-footer"> 
                <a style="display: none;" href="<?php echo base_url('booking'); ?>" class="homaepagelink btn btn-default">Close</a>
                <button type="submit" class="btn btn-default close_btn" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>
<div id="LockMailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body "> 
                <div class="LockMailModalContent"></div>
                <div class="form-group">
                        <label>Remark Form Dispatch Team / Plant</label>
                </div>
                <textarea name="remark" id="mailremark"  class="form-control" placeholder=" tentative dispatch date etc"> </textarea>
            </div>
            <div class="modal-footer"> 
                <a href="javascript:void(0)" class="btn btn-default send_mail_plant"  rel="" >Send Mail</a>
                <a style="display: none;" href="<?php echo base_url('booking'); ?>" class="homaepagelink btn btn-default">Close</a>
                <button type="submit" class="btn btn-default close_btn" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>