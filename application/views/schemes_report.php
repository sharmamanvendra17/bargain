<?php include 'header.php'; ?>
<style type="text/css">
    table.table-bordered thead th, table.table-bordered tbody td { text-align: center; vertical-align: middle; }
</style>
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1>
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
             <div class="panel-heading">
                <div class="row">

                        <div class="col-md-8">   
                            <span class="title elipsis">
                                <strong><?php echo $title; ?></strong> 
                            </span> 
                        </div>
                        <div class="col-md-4">                                      

                        </div>

                </div>
             
                
            </div>
            <?php if(isset($_SESSION['search_target_report_data']))
            {   
                        $_POST = $_SESSION['search_target_report_data'];   
            } ?>
            <div class="panel-body">
                    <form action="" class="" method="post" id="addbooking">
                        <div class="row"> 
                            <div class="col-md-12" >                           
                                <div class="form-group">
                                    <label for="scheme_id">Scheme </label> 
                                    <select class="form-control" id="scheme_id" name="scheme_id" >
                                        <option value="">Select  Scheme</option> 
                                        <?php if($schemes) { 
                                            foreach ($schemes as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['scheme_id'])) { if($_POST['scheme_id']==$value['id']) { echo "selected"; } }; ?>><?php echo $value['brand_name'].'_'.$value['category_name'].'_'.$value['state_name']; ?></option>
                                        <?php } } ?>
                                    </select>
                                    <span class="txt-danger"><?php echo form_error('scheme_id'); ?></span>
                                </div>
                            </div>  
                        </div>                      

                        <div class="row">                               
                            <div class="col-md-4">
                                <div class="form-group"> 
                                    <label class="btn-block"></label>
                                    <button type="submit" class="btn btn-default" value="Search">Search</button>  
                                </div>                                  
                            </div> 
                            <div class="col-md-4">
                            </div>                             
                        </div>
                    </form>               
                <div class="row">
                    <div class="col-md-12">   
                        <div class="table-responsive">
                            <?php if($results) { ?>
                            <table class="table table-striped table-bordered table-hover text-center align-middle">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Name</th> 
                                        <th>Dispatched Weight (MT)</th>  
                                        <th>Scheme Days</th> 
                                        <th>Estimated Target Weight (MT)</th>        
                                        <th>Prize</th>        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php   
                                    $count = 1;
                                    $cur_page =1;
                                    $con_li = 100;
                                    if(isset($limit))
                                        $con_li = $limit;
                                    if($this->uri->segment(3)!='')
                                        $cur_page = $this->uri->segment(3);
                                    $count = ($cur_page-1)*$con_li+1;
                                    foreach ($results as $key => $value) { 
                                        //$winner_reponse =  schemewinner($value['total_dispatch'],$schemeid);
                                        $winner_reponse =  schemewinner($value['total_net_dispatch'],$schemeid);
                                        $winner_prize = "";
                                        $total_estimated_weight = "";
                                        $completed_days = "";
                                        $scheme_days = "";
                                        $target_dispatched_ton = "";
                                        $reward_image = "";
                                        if($winner_reponse)
                                        {
                                            $winner_prize = $winner_reponse['reward_name'];
                                            $total_estimated_weight = round($winner_reponse['total_estimated_weight'],2);
                                            $completed_days = $winner_reponse['completed_days'];
                                            $target_dispatched_ton = ' / '.$winner_reponse['target_dispatched_ton'];
                                            $scheme_days = ' / '.$winner_reponse['scheme_days'];
                                            $scheme_days_1 = $scheme_days;
                                            $completed_days_1 = $completed_days;
                                            $reward_image =$winner_reponse['reward_image'];
                                        }  
                                        ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td title=""><?php echo $value['party_name']; ?></td>
                                        <td><?php echo round($value['total_net_dispatch'],2); //echo round($value['total_dispatch'],2); ?></td> 
                                        <td><?php echo (isset($completed_days_1))  ? $completed_days_1 : '';?><?php echo (isset($scheme_days_1))  ? $scheme_days_1 : '';    ?></td>  
                                        <td><?php echo ($total_estimated_weight) ? $total_estimated_weight.$target_dispatched_ton : "Not Qualified";   ?></td> 
                                        <td style="text-align: center;">                
                                                <?php if($reward_image){ ?>
                                                    <img src="<?php echo base_url('/public/uploads/scheme_images').'/'.$reward_image; ?>" style="width: 65px; height: 35px;">
                                                <?php } ?>
                                                <div><?php echo ($winner_prize) ? $winner_prize : 'N/A';?></div>
                                        </td>  
                                    </tr>
                                    <?php $count++;  } ?>
                                </tbody> 
                            </table>
                            <table>
                                <tr>
                                    <td>
                                        <?php //echo $links; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php } ?>                        
                        </div>
                    </div>                    
                </div>                   
                </div>
                <!--
                <div>
                    <span><strong>Total Ordered Weight : 0 in Kg (0 In Ton)</strong></span>
                </div>-->
                 
            </div>
        </div>
</section>
<?php include 'footer.php'; ?>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){    
    
    $("#employee,#state").select2();
    $(document).on('change', '.view_reoprt', function(){   
        var view_reoprt= $('input[name="view_reoprt"]:checked').val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>target_report/getusers/',
            data: { 'view_reoprt': view_reoprt},
            success: function(msg){ 
                $("#employee").html(msg);
            }
        }); 
    });

    $(document).on('click', '.send_pdf', function(){  
        var vendor_id = $("#vendor").val(); 
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>rate/cnfratepdf/',
            data: { 'vendor_id': vendor_id},
            success: function(msg){ 
                if(msg==21)
                {
                    alert("PDF sent");
                }               
                else
                {
                   alert("try again"); 
                }
            }
        });   
    }); 
});
</script>




 