<?php include APPPATH.'views/header.php'; ?>  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1> 
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
            <form action="<?php echo base_url('messages'); ?>" class="" method="post" id=""> 
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
                    <?php } //echo "<pre>"; print_r($_POST); ?> 
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="row"> 
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="channel">Channel</label> 
                                        <input class="" type="radio" id="" name="channel"  value="" <?php echo ((isset($_POST['channel']) && $_POST['channel']=='') || !isset($_POST['channel'])) ? 'checked' : ''; ?> /> All
                                        <input class="" type="radio" id="" name="channel"  value="918764216255" <?php echo ( (isset($_POST['channel']) && $_POST['channel']=='918764216255') ) ? 'checked' : ''; ?> /> ashokawa
                                        <input class="" type="radio" id="" name="channel"  value="918764183760" <?php echo ( (isset($_POST['channel']) && $_POST['channel']=='918764183760') ) ? 'checked' : ''; ?> /> scooterwa
                                        <input class="" type="radio" id="" name="channel"  value="919462570495" <?php echo ( (isset($_POST['channel']) && $_POST['channel']=='919462570495') ) ? 'checked' : ''; ?> /> dataxgenwa
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Party</label> 
                                        <select name="party" id="party" class="form-control">
                                            <option value="">Select Party</option>
                                            <?php if($parties) { 
                                                foreach ($parties as $key => $value) { ?>
                                            <option data-state_id="<?php echo $value['state_id']; ?>"  value="<?php echo $value['mobile']; ?>" <?php if(isset($_POST['party'])) { if($_POST['party']==$value['mobile']) { echo "selected"; } }; ?>><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                            <?php } } ?> 
                                        </select>                                       
                                        <span class="txt-danger"><?php echo form_error('category'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Employee</label> 
                                        <select name="employee" id="employee" class="form-control">
                                            <option value="">Select Employee</option>
                                            <?php if($employess) { 
                                                foreach ($employess as $key => $value) { ?>
                                            <option value="<?php echo $value['mobile']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$value['mobile']) { echo "selected"; } }; ?>><?php echo $value['name']; ?></option>
                                            <?php } } ?> 
                                        </select> 
                                        <span class="txt-danger"><?php echo form_error('mobile'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label for="booking_date_from">Date (From)</label>
                                        <span class="clear" style="float: right;">Clear</span>
                                        <input class="form-control" type="text" id="date_from" name="date_from"  value="<?php if(isset($_POST['date_from']) && !empty($_POST['date_from']) ) { echo $_POST['date_from']; } else { echo ''; } ?>" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label for="booking_date_to">Date (To)</label>
                                        <span class="clear" style="float: right;">Clear</span>
                                        <input class="form-control" type="hidden" id="date_to" name="date_to" value="<?php if(isset($_POST['date_to']) && !empty($_POST['date_to']) ) { echo $_POST['date_to']; } else { echo ''; } ?>"/>
                                    </div>
                                </div> 
                            </div>                          
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search Messages</button>  
                                    </div>                                  
                                </div> 
                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>                               
                            </div>                            
                        </div>
                    </div>  
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Type</th> 
                                    <th>Party/ Employee Name</th>
                                    <th>Mobile Number</th> 
                                    <th>Message</th>                                    
                                    <th>Time</th>    
                                </tr>
                            </thead>
                            <tbody class="booking_records">
                                <?php if($messages) { 
                                    $i=1;
                                    $count = 1;
                                    $cur_page =1;
                                    if(isset($limit))
                                        $con_li = $limit;
                                    if($this->uri->segment(3)!='')
                                        $cur_page = $this->uri->segment(3);
                                    $count = ($cur_page-1)*$con_li+1;
                                    foreach ($messages as $key => $value) { ?>
                                        <tr class="odd gradeX">
                                            <td><?php echo $count; ?></td>
                                            <td>
                                                <?php if($value['send_receive_flag'] == 1){ echo 'TX'; $img = "right-arrow.png"; }else{ echo 'RX'; $img = "left-arrow.png"; } ?>
                                                <span><img src="<?php echo base_url() ?>assets/images/<?php echo $img; ?>" alt="" width="25" height="25"/></span>
                                                <?php 
                                                    if($value['sender_number']=='918764216255')
                                                    { 
                                                        echo "(ashokawa)";
                                                    }
                                                    if($value['sender_number']=='918764183760')
                                                    { 
                                                        echo "(scooterwa)";
                                                    }
                                                    if($value['sender_number']=='919462570495')
                                                    { 
                                                        echo "(dataxgenwa)";
                                                    }
                                                ?>
                                            </td> 
                                            <td><?php echo ($value['employee_name']) ? $value['employee_name'] : ($value['vendor_name'] ? $value['vendor_name'] : $value['Endusername']  ); ?></td> 
                                            <td><?php echo $value['mobile_number']; ?></td>  
                                            <td><?php echo $value['message']; ?>            
                                            <?php if($value['content_type'])
                                            {
                                                echo ($value['caption']) ? $value['caption'].'<br>' : '';
                                                echo "<a target='_blank' href='".base_url('messages/view/')."/".$value['id']."'>View</a>";  
                                            } ?>

                                            <?php if($value['file'])
                                            { 
                                                echo "<a target='_blank' href='".$value['file']."'>View</a>";  
                                            } ?>


                                            </td> 
                                            <td><?php echo date('d-m-Y H:i:s', strtotime($value['receiving_time'])); ?></td> 
                                        </tr>
                                <?php $count++; } } ?> 
                                <?php if($links) { ?>
                                <tr>
                                    <td colspan="7">
                                        <?php echo $links; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>  
                    </div> 
                </div>
            </form>
        </div>
    </div>
</section>

<?php include APPPATH.'views/footer.php'; ?> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">     
const $flatpickr = $("#date_from,#date_to").flatpickr({  
    dateFormat: "d-m-Y",
}); 
$(".clear").click(function() {
   $("#date_from,#date_to").flatpickr().clear(); 
})
</script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
    $(document).ready(function(){
        $("#party,#employee").select2();  
    }); 
</script> 