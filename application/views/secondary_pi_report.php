<?php include 'header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
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
            <form action="<?php echo base_url('secondarypihistory/index/'); ?>" class="" method="post" id="addbooking">
            <div class="panel-heading">
                <div class="row">
                        <?php 
                        $params_report = "";
                        if($pis) { 
                            $rejected =0;
                            if(isset($_POST['rejected']))
                                $rejected = $_POST['rejected'];
                            $params_report = "?party=$_POST[supply_from]&from=$_POST[booking_date_from]&to=$_POST[booking_date_to]&pinumber=$_POST[pinumber]&rejected=$rejected";
                        } ?>
                        <div class="col-md-8">   
                            <span class="title elipsis">
                                <strong><?php echo $title; ?></strong> <!-- panel title -->
                            </span> 
                        </div>
                        <div class="col-md-4">
                            <span class="title elipsis header_add" >
                                <div class="form-group cal" style="margin-bottom:0px!important"> 
                                    <input class="form-control" type="text" name="pinumber" id="pinumber" value="<?php echo (isset($_POST['pinumber'])) ? $_POST['pinumber'] : ''; ?>" placeholder="Search By PI Number" />
                                </div>  
                            </span> 
                        </div>

                </div>
             
                
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
                <?php } //echo "<pre>"; print_r($_POST); ?> 
                <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="booking_number" id="booking_number" value="0">                              
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">                                        
                                        <label for="supply_from">Supply From</label> 
                                        <select class="form-control" id="supply_from" name="supply_from" >
                                            <option value="">Select Supply From</option>
                                            <?php if($super_disributers) { 
                                                foreach ($super_disributers as $key => $super_disributer) { ?>
                                            <option value="<?php echo $super_disributer['id']; ?>" <?php if(isset($_POST['supply_from'])) { if($_POST['supply_from']==$super_disributer['id']) { echo "selected"; } }; ?>><?php echo $super_disributer['name'].' - '.$super_disributer['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger"><?php echo form_error('supply_from'); ?></span>
                                    </div>
                                </div>   
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">PI Date (From) </label>
                                        <input class="form-control" type="text" id="booking_date_from" name="booking_date_from"  value="<?php if(isset($_POST['booking_date_from'])) { echo $_POST['booking_date_from']; } else { echo date('d-m-Y'); } ?>" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label for="rate">PI Date (To)</label>
                                        <input class="form-control" type="hidden" id="booking_date_to" name="booking_date_to" value="<?php if(isset($_POST['booking_date_to'])) { echo $_POST['booking_date_to']; } else { echo date('d-m-Y'); } ?>"/>
                                    </div>
                                </div> 
                            </div>   
                            <div class="row" > 
                                <div class="col-md-6"> 
                                    <label for="rate">Status</label> 
                                    <input class="" type="checkbox" id="" name="rejected"  value="1"   <?php echo (isset($_POST['rejected']) && $_POST['rejected']=!'') ? 'checked' : ''; ?> />Rejected
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-2">
                                  
                                </div>
                            </div>
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                        <button type="submit" class="btn btn-default booking_submit" value="Search">Search Secondary PI</button>  
                                    </div>                                  
                                </div> 
                                <div class="col-md-4">
                                </div>                
                                <div class="col-md-4">
                                    <?php if($pis) { ?>
                                    <a style="display: inline-block;float: right;" target="_blank" class="rept_btn btn btn-default" href="<?php echo base_url('secondarypihistory/report_pdf').$params_report; ?>"><img src="<?php echo base_url('assets/img/pdf.png'); ?>" height="20" width="20"> </a>
                                    <?php } ?>
                                </div>                         
                            </div>
                        
                    </div>
                </div> 
                </form>
               
                    <?php $total_weight = 0; ?> 
                <div class="table-responsive">
                    <form method="POST" id="pi_form">
                    <table class="table table-striped table-bordered table-hover" id="">
                        <thead>
                            <tr> 
                                <th>PI Number</th>
                                <th>Party Name</th>
                                <th>Bargain No.</th>    
                                <th>Total Weight (MT)</th>                                 
                                <th>Amount</th>  
                                <th>PI Date</th>  
                                <th>Status</th>     
                            </tr>
                        </thead>
                        <tbody class="booking_records"> 
                                <?php if($pis) { 
                                    $i=1;
                                    $count = 1;
                                    $cur_page =1;
                                    if(isset($limit))
                                        $con_li = $limit;
                                    if($this->uri->segment(3)!='')
                                        $cur_page = $this->uri->segment(3);
                                    $count = ($cur_page-1)*$con_li+1;
                                    foreach ($pis as $key => $value) { ?>
                                        <tr class="odd gradeX <?php echo ($value['status']==1) ? "danger" : ""; ?>" style="text-align: center; vertical-align: middle;"> 
                                            <td><a  target="_blank" href="<?php echo base_url().'/invoices/secondary/'.$value['invoice_file'] ?>"> <?php echo $value['id']; ?></a></td>
                                            <td><?php echo $value['vendors_name']; ?> </td>
                                            <td><?php echo $value['bargain_ids']; ?></td>   
                                            <td><?php echo round($value['total_weight_pi'],2); ?></td>
                                            <td><?php echo number_format($value['pi_amount'],2); ?></td> 
                                            <td title="<?php echo date("h:i:s", strtotime($value['created_at'])); ?>"><?php echo date("d-m-Y", strtotime($value['created_at'])); ?></td>
                                             
                                            <td>
                                                <?php if($value['dispatch_date']) { ?>
                                                    <img title="<?php echo $value['truck_number']; ?>&#013;<?php echo $value['dispatch_date']; ?>" src="<?php echo base_url(); ?>assets/img/truck.png" style="height: 40px;"> 
                                                <?php } elseif($value['status']==0) { ?>
                                                    <?php $clss= ""; if($logged_role==4 || $logged_role==5)
                                                            $clss= "removepimodal btn";
                                                     ?>
                                                    <span class="<?php echo $clss; ?> btn-warning btn-xs" data-cnf="<?php echo $value['cnf']; ?>"  rel="<?php echo base64_encode($value['id']); ?>" data-id="<?php echo base64_encode($value['booking_id']); ?>">Remove</span>

                                                    <br>
                                                    <span class="btn-danger btn-xs tax_invoice" title="<?php echo $value['remark']; ?>" rel="<?php echo base64_encode($value['id']); ?>" data-id="<?php echo base64_encode($value['bargain_ids']); ?>" data-vendor="<?php echo base64_encode($value['party_id']); ?>">Tax</span>

                                                <?php } else { ?>
                                                    <span class="btn-danger btn-xs invoice1" title="<?php echo $value['remark']; ?>" rel="<?php echo base64_encode($value['id']); ?>" data-id="<?php echo base64_encode($value['bargain_ids']); ?>" data-vendor="<?php echo base64_encode($value['party_id']); ?>">Removed</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                <?php $count++; } } ?>  
                        </tbody>
                    </table>
                    </form>
                    <table>
                        <tr>
                            <td>
                                <?php echo $links; ?>
                            </td>
                        </tr>
                    </table>
                </div>  
                </div>
                <!--
                <div>
                    <span><strong>Total Ordered Weight : <?php echo $total_weight; ?> in Kg (<?php echo $total_weight/1000; ?> In Ton)</strong></span>
                </div>-->
                 
            </div>
            </form>

            <form id="pi_info">
                <input type="hidden" id="booking_id" name="booking_id" value="">
                <input type="hidden" id="vendor_id" name="vendor_id" value="">
                <input type="hidden" id="pi_number" name="pi_number" value="">
            </form> 
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>


<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>

<script type="text/javascript">
$("#booking_date_from,#booking_date_to").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#supply_from").select2(); 
        $("#supply_to").select2(); 
    });


    $(document).on("keyup",".freight_input",function () { 
            if (this.value.match(/[^0-9.]/g, '')) { 
              this.value = this.value.replace(/[^0-9.]/g, '');      
            } 
        });

    $(document).on("click", ".removepimodal", function(event){ 
        var pi_id = $(this).attr('rel');
        var booking_id = $(this).attr('data-id');
        var cnf = $(this).attr('data-cnf');
        $('.removepi_btn').attr('rel',pi_id);
        $('.removepi_btn').attr('data-id',booking_id);
        $('.removepi_btn').attr('data-cnf',cnf);
        $("#myModal").modal("show");
    }); 
    $(document).on("click", ".removepi", function(event){         
        var pi_id = $(this).attr('rel');
        var cnf = $(this).attr('data-cnf');
        //alert(cnf);
        var booking_id = $(this).attr('data-id');
        var unlock =0; 
        var remark = $("#remark").val();
        if(remark.trim()=='')
        {
            $("#v_ramark").html('<span style="color:red">Please Enter Remark</span>');
            return false;
        }
        else
        { 
            $("#v_ramark").html('');
        }
        $(".loader").show();  
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>secondarypihistory/removepi/',
            data: { 'pi_id' :pi_id, 'unlock' :unlock,'remark': remark,'booking_id': booking_id }, 
            dataType: "html",
            success: function(data){  
                $(".loader").hide();  
                if(data)
                {  
                    if(cnf==1)
                    {
                        //location.reload();
                        $(".manage_claculation").show();  
                        $("#myModal").modal('hide');
                        $("#piremoval_modal") .modal('show');
                        $.ajax({
                            type: "POST",
                            url: '<?php echo base_url();?>secondarypihistory/accounting_calculation/',
                            data: { 'pi_id' :pi_id}, 
                            dataType: "html",
                            success: function(data){   
                                $(".manage_claculation").hide();
                                $("#piremoval_modal").modal('hide'); 
                                if(data)
                                { 
                                    alert("PI Removed Successfully");
                                    location.reload();
                                }                         
                                else
                                {
                                   alert("Try Again");
                                }
                            }   
                        }); 
                    }
                    else
                    {
                        alert("PI Removed Successfully");
                        location.reload();
                    }
                }                         
                else
                {
                    alert("Try Again");
                }
            }   
        }); 
    }); 
    $(document).on('change', '#supply_from', function(){  
        var vendor_id = $(this).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/GetDistributors',
            data: { 'vendor_id': vendor_id},
            success: function(msg){
                $("#supply_to").html(msg); 
            }
        }); 
    });
    $(document).on('click', '.tax_invoice', function(){  
        var vendor_id = $(this).attr('data-vendor');
        var booking_id = $(this).attr('data-id');
        var pi_number = $(this).attr('rel');
        $("#booking_id").val(booking_id);
        $("#vendor_id").val(vendor_id);
        $("#pi_number").val(pi_number);
        $(".loader").show();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/invoiceskus',
            data: { 'booking_id': booking_id,'vendor_id': vendor_id,'pi_number': pi_number},
            success: function(msg){ 
                $(".sku_list").html(msg);
                $(".loader").hide();
                $("#skus_modal").modal('show');  
            }
        }); 
    });

    $(document).on("click", ".add_frieght", function(event){ 
        event.preventDefault();
        $(".loader").show(); 
        $(".skus_weight").text($('#total_invoice_weight').val());
        $('#freightnmodal').modal('show');
        $(".loader").hide();
    });
    $(document).on("click", ".preview", function(event){ 
        event.preventDefault();
            var dataString = $("#pi_info, #Freight_form").serialize(); 
            $(".loader").show();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>secondarybooking/taxinvoicepreview/',
                data: dataString, 
                dataType: "html",
                success: function(data){ 
                    $(".loader").hide();
                    $('.piresponse').html(data);                        
                    $('#PreviewPI').modal('show'); 
                    $('body').removeClass('modal-open');
                }
            });      
    });

    $(document).on("click", ".invoice_download", function(event){ 
        event.preventDefault();
            var dataString = $("#pi_info, #Freight_form").serialize();
            $(".loader").show();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>secondarybooking/taxinvoicedownload/',
                data: dataString, 
                dataType: "html",
                success: function(data){ 
                    $(".loader").hide();
                    if(data==0)
                    {
                        alert("Something went wrong");
                    }
                    else
                    {
                        window.open(data, '_blank'); 
                    }
                }
            });      
    });

    $(document).on("click", ".invoice", function(event){ 
        event.preventDefault();
        $('.invoice').attr('disabled', 'disabled');
        $('.invoice').prop("disabled",true); 
            var dataString = $("#pi_info, #Freight_form").serialize(); 
            $(".loader").show();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>secondarybooking/taxinvoice/',
                data: dataString, 
                dataType: "html",
                success: function(data){ 
                    $(".loader").hide();
                    $('.piresponse').html('');
                    $('#PreviewPI').modal('hide');
                    $('#invoicedPI').modal('show');
                }
            });      
    });

    $(document).on('click', '.invoice1', function(){  
        var vendor_id = $(this).attr('data-vendor');
        var booking_id = $(this).attr('data-id');
        $(".loader").show();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>secondarybooking/skus',
            data: { 'booking_id': booking_id,'vendor_id': vendor_id},
            success: function(msg){ 
                $(".sku_list").html(msg);
                $(".loader").hide();
                $("#skus_modal").modal('show');  
            }
        }); 
    });    



      
</script>

 
<div class="loader"><img src="<?php echo base_url('/assets/img/hug.gif'); ?>"> </div>
<div class="loader manage_claculation"></div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title reject_status_label">Remove PI</h4>
            </div>
            <div class="modal-body">  
                <textarea class="form-control" name="remark" id="remark" placeholder="Enter Remark" required></textarea> 
                <span id="v_ramark"></span>
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default removepi removepi_btn" rel="" data-id="" data-cnf="" >Remove</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>


<div id="piremoval_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title reject_status_label">PI Removed Successfully</h4>
            </div>
            <div class="modal-body">  
                PI removed successfully. Please wait while are managing your accounting.
            </div>
            <div class="modal-footer">  
            </div>
        </form>
    </div>

  </div>
</div>
<div id="skus_modal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:1109px;">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title reject_status_label">SKU List</h4>
            </div>
            <div class="modal-body">  
                <div class="sku_list">                    
                </div>
            </div>
            <div class="modal-footer">  
                <div class="modal-footer"> 
                <button type="button" class="btn btn-default add_frieght" data-dismiss="modal">Add Freight</button>
                <button type="button" class="btn btn-default preview" data-dismiss="modal">Preview</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div> 
            </div>
        </form>
    </div>

  </div>
</div>



<div id="freightnmodal" class="modal fade" role="dialog"> 
  <div class="modal-dialog" style="width:1109px;">
    <!-- Modal content-->
    <div class="modal-content"  > 
        <form method="post" id="Freight_form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>                 
            </div>  
            <div class="modal-body freightlist">
                <table style="background:#ffffff; border-collapse:collapse;border:none; border-top:1px solid #000000;  border-bottom:1px solid #000000; text-align:center;font-size:12px;  padding:0px;" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>
                        <tr> 
                            <td>Weight (MT)</td>
                            <td>Freight</td>
                            <td>Vehicle Number</td>
                        </tr>
                        <tr> 
                            <td class="skus_weight"></td>
                            <td><input type="text" name="freight" value="" class="form-control freight_input"></td>
                            <td><input type="text" name="vehicle_number" value="" class="form-control vehicle_number"></td>
                        </tr>
                    </tbody>
                </table>
            </div>    
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default preview" data-dismiss="modal">Preview</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div> 
        </form>
    </div>

  </div>
</div>


<div id="PreviewPI" class="modal fade" style="position: absolute !important; overflow : visible;" role="dialog"> 
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 
            </div>
            <div class="modal-body">
                 <div class="piresponse"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default invoice_download" data-dismiss="modal">Download Invoice for verification</button> 
                <button type="button" class="btn btn-default invoice" data-dismiss="modal">Generate & Send Invoice on my whatsapp</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

  </div>
</div>



<div id="invoicedPI" class="modal fade" role="dialog"> 
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content"> 
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 
            </div>
            <div class="modal-body">
                Tax Invoice sent on your registered number whatsapp.
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div> 
    </div>
  </div>
</div>