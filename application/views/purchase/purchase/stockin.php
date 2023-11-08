<?php include APPPATH.'views/header.php'; ?>
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
            <form action="" class="" method="post" id="addinventory">  
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
                <?php if($logged_role==11 || $logged_role==10  || $logged_role==9 || $logged_role==4) { ?>
                <div class="row">
                    <div class="col-md-12">                         
                            <div class="row">  
                                <input type="hidden" name="booking_number" id="booking_number" value="<?php echo $booking_info['id']; ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">                                        
                                        <label for="name">Supplier</label> 
                                        <select class="form-control" id="party" name="party"  required1> 
                                            <?php if($users) { 
                                                foreach ($users as $key => $value) { ?>
                                            <option value="<?php echo $value['id']; ?>" <?php echo ($booking_info['party_id']==$value['id']) ? 'selected' : ''; ?> ><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                        <span class="txt-danger v_party"><?php echo form_error('party'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gr_lr_no">GR/LR No.</label>  
                                        <input type="text" class="form-control" id="gr_lr_no" name="gr_lr_no"   value="<?php if(isset($_POST['gr_lr_no'])) echo $_POST['gr_lr_no']; ?>"> 
                                        <span class="txt-danger v_gr_lr_no"><?php echo form_error('gr_lr_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gr_lr_date">Date</label>  
                                        <input type="text" class="form-control" id="gr_lr_date" name="gr_lr_date"   value="<?php if(isset($_POST['gr_lr_date'])) echo $_POST['gr_lr_date']; ?>"> 
                                        <span class="txt-danger v_gr_lr_date"><?php echo form_error('gr_lr_date'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="vendor_invoice_number">Vendor Invoice No.</label>  
                                        <input type="text" class="form-control" id="vendor_invoice_number" name="vendor_invoice_number"   value="<?php if(isset($_POST['vendor_invoice_number'])) echo $_POST['vendor_invoice_number']; ?>"> 
                                        <span class="txt-danger v_vendor_invoice_number"><?php echo form_error('vendor_invoice_number'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="vendor_invoice_date">Vendor Invoice Date</label>  
                                        <input type="text" class="form-control" id="vendor_invoice_date" name="vendor_invoice_date"   value="<?php if(isset($_POST['vendor_invoice_date'])) echo $_POST['vendor_invoice_date']; ?>"> 
                                        <span class="txt-danger v_vendor_invoice_date"><?php echo form_error('vendor_invoice_date'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="vehicle_number">Vehicle Number</label>  
                                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number"  required1 value="<?php if(isset($_POST['vehicle_number'])) echo $_POST['vehicle_number']; ?>"> 
                                        <span class="txt-danger v_vehicle_number"><?php echo form_error('vehicle_number'); ?></span>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ordered_qty">Ordered QTY (MT)</label> 
                                        <input type="text" class="form-control" id="ordered_qty" name="ordered_qty"  value="<?php echo $booking_info['weight']; ?>"  required1 readonly> 
                                        <span class="txt-danger v_ordered_qty"><?php echo form_error('ordered_qty'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="balance_qty">Balance QTY (MT)</label> 
                                        <input type="text" class="form-control" id="balance_qty" name="balance_qty"  value="<?php echo round(($booking_info['weight']-$inventory_total_weight),2); ?>" readonly> 
                                        <span class="txt-danger v_balance_qty"><?php echo form_error('balance_qty'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bill_weight">Bill QTY (MT)</label> 
                                        <input type="text" class="form-control" id="bill_weight" name="bill_weight"  value="<?php if(isset($_POST['bill_weight'])) echo $_POST['bill_weight']; ?>"  required1 > 
                                        <span class="txt-danger v_bill_weight"><?php echo form_error('bill_weight'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="unit_numbers">Box/Bags/Tankers</label> 
                                        <input type="text" class="form-control" id="unit_numbers" name="unit_numbers"  value="<?php if(isset($_POST['unit_numbers'])) echo $_POST['unit_numbers']; ?>"  required1 > 
                                        <span class="txt-danger v_unit_numbers"><?php echo form_error('unit_numbers'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="unit_numbers">ERP Sr. No.</label> 
                                        <input type="text" class="form-control" id="erp_sr_no" name="erp_sr_no"  value="<?php if(isset($_POST['erp_sr_no'])) echo $_POST['erp_sr_no']; ?>"  required1 > 
                                        <span class="txt-danger v_erp_sr_no"><?php echo form_error('erp_sr_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="unit_numbers">Code</label> 
                                        <input type="text" class="form-control" id="code" name="code"  value="<?php if(isset($_POST['code'])) echo $_POST['code']; ?>"  required1 > 
                                        <span class="txt-danger v_code"><?php echo form_error('code'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="sales_executive">Remark</label> 
                                    <div class="form-group"> 
                                        <textarea class="form-control" name="remark" id="remark" placeholder="Remark" rows="3"></textarea>
                                    </div>
                                </div> 
                            </div>
                            <div class="row" >
                                <div class="col-md-8">
                                    <div class="form-group"> 
                                        <label for="rate">Status</label> 
                                        <input class="" type="radio" id="" name="status"  value="1" checked />In Transist
                                        <input class="" type="radio" id="" name="status"  value="2"  />In Factory                                        
                                    </div>
                                </div>
                                 
                            </div>
                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <label class="btn-block"></label>
                                         <button type="submit" class="btn btn-default booking_submit" value="Save">Save</button> 
                                    </div>                                  
                                </div>                                
                            </div>
                        
                    </div>
                </div> 
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer" id="datatable_sample" >
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>ERP Sr. No.</th>
                                <th>Code</th>
                                <th>Gr/LR No</th>
                                <th>Gr/LR Date</th>
                                <th>Vendor Invoice No.</th>
                                <th>Vendor Invoice Date</th>   
                                <th>Vehicle Number</th>  
                                <th>Weight (MT)</th> 
                                <th>Box/Bag</th>  
                                <th>Supplier</th>   
                                <th>Added By</th>   
                                <th>Status</th>  
                                <th>Created at</th>   
                                <th>Action</th>   
                            </tr>
                        </thead>
                        <tbody class="booking_records">
                            <?php $total_weight = 0; if($inventories) { 
                                $i=1;
                                $count = 1;
                                $cur_page =1;
                                if(isset($limit))
                                    $con_li = $limit;
                                if($this->uri->segment(5)!='')
                                    $cur_page = $this->uri->segment(5);
                                $count = ($cur_page-1)*$con_li+1;
                                foreach ($inventories as $key => $value) { ?>
                                    <tr class=""> 
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo ($value['erp_sr_no']) ? $value['erp_sr_no'] : '-'; ?></td> 
                                        <td><?php echo ($value['code']) ? $value['code'] : '-'; ?></td> 
                                        <td><span title="<?php echo $value['added_by']; ?>"><?php echo $value['gr_lr_no']; ?></span></td> 
                                        <td><?php echo date('d-m-Y', strtotime($value['gr_lr_date']) ); ?> </td> 

                                        <td><?php echo $value['vendor_invoice_number']; ?></td> 
                                        <td><?php echo date('d-m-Y', strtotime($value['vendor_invoice_date']) ); ?></td> 

                                        <td><?php echo $value['vehicle_number']; ?></td>  
                                        <td><?php echo $value['bill_weight']; ?></td> 
                                        <td><?php echo $value['unit_numbers']; ?></td>
                                        <td><?php echo $value['supplier_name']; ?></td> 
                                        <td><?php echo $value['added_by'];?></td>
                                        <td><a title="Edit Purchase" href="javascript:void(0)" data-erp_sr_no="<?php echo $value['erp_sr_no']; ?>" data-code="<?php echo $value['code']; ?>" data-purchase_id="<?php echo base64_encode($value['purchase_id']); ?>" data-id="<?php echo $value['id'];?>" data-gr_lr_no="<?php echo $value['gr_lr_no'];?>" data-gr_lr_date="<?php echo $value['gr_lr_date'];?>" data-vendor_invoice_number="<?php echo $value['vendor_invoice_number'];?>" data-vendor_invoice_date="<?php echo $value['vendor_invoice_date'];?>" data-vehicle_number="<?php echo $value['vehicle_number'];?>" data-bill_weight="<?php echo $value['bill_weight'];?>" data-unit_numbers="<?php echo $value['unit_numbers'];?>"  data-remark="<?php echo $value['remark'];?>" data-party_id="<?php echo $value['party_id'];?>" data-inventory_status="<?php echo $value['inventory_status'];?>"  class="editinventory"> <?php echo ($value['inventory_status']==1) ? "<img src='".base_url('/assets/images/truck.gif')."' height='40px'>" : "<img src='".base_url('/assets/images/factory.gif')."' height='40px'>";?></a></td>
                                        <td><?php echo date('d-m-Y', strtotime($value['created_at'])); ?></td> 
                                        <td>
                                            <span class="btn btn-<?php echo ($value['debit_note_id']) ? 'success' : 'danger'; ?>  <?php echo ($logged_role == 9 ||$logged_role == 10 || $logged_role == 4 ) ? 'add_debit_note' : ''; ?>"  rel="<?php echo base64_encode($value['purchase_id']); ?>" data-inventory="<?php echo base64_encode($value['id']); ?>" data-debit-id="<?php echo ($value['debit_note_id']) ? base64_encode($value['debit_note_id']) : ''; ?>"   data-debit-remark="<?php echo $value['debit_remark']; ?>" data-debit-amount="<?php echo $value['debit_amount']; ?>" ><?php echo ($value['debit_note_id']) ? 'Debit Note' : 'Add Debit Note'; ?></span>
                                        </td>
                                    </tr>
                            <?php $count++; } } ?>  
                        </tbody>
                    </table> 
                </div>
                <!--
                <div>
                    <span><strong>Total Ordered Weight : <?php echo $total_weight; ?> in Kg (<?php echo $total_weight/1000; ?> In Ton)</strong></span>
                </div>-->
                 
            </div>
            </form>
        </div>
    </div>
</section>

<?php include APPPATH.'views/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#gr_lr_date,#vendor_invoice_date,#update_gr_lr_date,#update_vendor_invoice_date").flatpickr({ 
    //minDate: "today",
    dateFormat: "d-m-Y",
}); 
</script>
<script>
    $(document).ready(function(){ 
        
        $(document).on('click', '.editinventory', function(){
            var id  = $(this).attr('data-id');
            var purchase_id  = $(this).attr('data-purchase_id'); 
            var gr_lr_no  = $(this).attr('data-gr_lr_no');
            var gr_lr_date  = $(this).attr('data-gr_lr_date');
            var vendor_invoice_number  = $(this).attr('data-vendor_invoice_number');
            var vendor_invoice_date  = $(this).attr('data-vendor_invoice_date');
            var vehicle_number  = $(this).attr('data-vehicle_number');
            var bill_weight  = $(this).attr('data-bill_weight');
            var unit_numbers  = $(this).attr('data-unit_numbers');
            var remark  = $(this).attr('data-remark');
            var party_id  = $(this).attr('data-party_id'); 
            var inventory_status  = $(this).attr('data-inventory_status');  
            var code  = $(this).attr('data-code');  
            var erp_sr_no  = $(this).attr('data-erp_sr_no');  


            var ordered_qty  = $("#ordered_qty").val();  
            var balance_qty  = $("#balance_qty").val();  


            $("#update_gr_lr_date,#update_vendor_invoice_date").flatpickr({ 
                //minDate: "today",
                dateFormat: "d-m-Y",
            }); 

            $("#update_purchase_id").val(purchase_id);
            $("#update_inventory_id").val(id);
            $("#update_party").val(party_id);
            $("#update_gr_lr_no").val(gr_lr_no); 
            $("#update_gr_lr_date").val(gr_lr_date); 
            $("#update_vendor_invoice_number").val(vendor_invoice_number); 
            $("#update_vendor_invoice_date").val(vendor_invoice_date); 
            $("#update_vehicle_number").val(vehicle_number); 
            $("#update_bill_weight").val(bill_weight); 
            $("#update_entered_bill_weight").val(bill_weight);             
            $("#update_unit_numbers").val(unit_numbers); 
            $("#update_remark").val(remark); 
            $("#update_erp_sr_no").val(erp_sr_no); 
            $("#update_code").val(code); 
            $("input[name=update_status][value=" + inventory_status + "]").prop('checked', true);


            $("#update_ordered_qty").val(ordered_qty);
            $("#update_balance_qty").val(balance_qty);


            $('#update_inventory_modal').modal('show');
            
        });
        $(document).on('click', '.add_debit_note', function(){
            $("#remark").val('');
            $("#v_ramark").html(''); 
            $("#divLoading").css({display: "block"});
            var purchase_id = $(this).attr('rel');   
            var inventory_id = $(this).attr('data-inventory'); 
            var debit_amount = $(this).attr('data-debit-amount'); 
            var debit_id = $(this).attr('data-debit-id');  
            var debit_remark = $(this).attr('data-debit-remark');  
            $("#debit_note_purchase_id").val(purchase_id);
            $("#debit_note_inventory_id").val(inventory_id);

            $("#debit_amount").val(debit_amount);
            $("#debit_note_remark").val(debit_remark);
            $("#debit_note_id").val(debit_id);
            $("#debit_note_inventory_id").val(inventory_id);
            if(debit_id.trim()=='')
                $(".debit_note_modal_btn").text("Save");
            else
                $(".debit_note_modal_btn").text("Update");
            $('#debit_note_modal').modal('show');
        });

        $("#bill_weight").blur(function () {  
            var balance_qty = parseFloat($("#balance_qty").val());
            var ordered_qty = parseFloat($("#ordered_qty").val()); 
            var bill_weight = parseFloat($("#bill_weight").val()); 
            var  balance_qty = isNaN(balance_qty) ? 0 : balance_qty; 
            var  bill_weight = isNaN(bill_weight) ? 0 : bill_weight; 
            remain_balance_qty = balance_qty;
            if(balance_qty==0)
            {
                //balance_qty = ordered_qty;
            }

             var max_weight =  (balance_qty+(ordered_qty*.04));
             
            var bill_weight = bill_weight;
            var total_bill_weight = ordered_qty-balance_qty;
             
            if(bill_weight>max_weight){
                alert("Weight is exceeded you can enter only "+max_weight+" (MT)");
                $("#bill_weight").val(max_weight);
            }                      
        });
        $("#update_bill_weight").blur(function () {  
            var balance_qty = parseFloat($("#balance_qty").val());
            var ordered_qty = parseFloat($("#ordered_qty").val()); 
            var bill_weight = parseFloat($("#update_bill_weight").val()); 
            var entered_bill_weight = parseFloat($("#update_entered_bill_weight").val()); 
            var  balance_qty = isNaN(balance_qty) ? 0 : balance_qty; 
            var  balance_qty = balance_qty+entered_bill_weight;
            var  bill_weight = isNaN(bill_weight) ? 0 : bill_weight; 
            if(balance_qty==0)
            {
                balance_qty = ordered_qty;
            }

            var max_weight =  (balance_qty+(ordered_qty*.04));
             
            var bill_weight = bill_weight;
            var total_bill_weight = ordered_qty-balance_qty; 
            if(bill_weight>max_weight || total_bill_weight==max_weight){
                alert("Weight is exceeded you can enter only "+max_weight+" (MT)");
                $("#bill_weight").val(max_weight);
            }            
        });

        $("#bill_weight,#update_bill_weight").keyup(function () { 
            if (this.value.match(/[^0-9.]/g, '')) { 
              this.value = this.value.replace(/[^0-9.]/g, '');      
            } 
        });
        $("#unit_numbers,#update_unit_numbers").keyup(function () { 
            if (this.value.match(/[^0-9]/g, '')) { 
              this.value = this.value.replace(/[^0-9]/g, '');      
            } 
        }); 

        $(document).on("submit", "#addinventory", function(event){
            event.preventDefault();
             
            var party_id = $("#party").val();  
            var gr_lr_no = $("#gr_lr_no").val();
            var gr_lr_date = $("#gr_lr_date").val();
            var vendor_invoice_number = $("#vendor_invoice_number").val();            
            var vendor_invoice_date = $("#vendor_invoice_date").val();
            var vehicle_number = $("#vehicle_number").val();
            var ordered_qty = $("#ordered_qty").val();
            var balance_qty = $("#balance_qty").text(); 
            var bill_weight = $("#bill_weight").val();
            var unit_numbers = $("#unit_numbers").val();  

            var flag = 0;            
             
            if(party_id.trim()=='')
            {
                $(".v_party_id").html("Please Select Part");
                flag = 1;
            } 
            else
            {
                $(".v_party_id").html("");
            } 

             
            if(gr_lr_no.trim()=='')
            {
                $(".v_gr_lr_no").html("Please enter GR/LR No.");
                flag = 1;
            } 
            else
            {
                $(".v_gr_lr_no").html("");
            } 

            if(gr_lr_date=='')
            {
                $(".v_gr_lr_date").html("Please select gr/lr date");
                flag = 1;
            } 
            else
            {
                $(".v_gr_lr_date").html("");
            }
            if(vendor_invoice_number=='')
            {
                $(".v_vendor_invoice_number").html("Please select Vendor Invoice No.");
                flag = 1;
            } 
            else
            {
                $(".v_vendor_invoice_number").html("");
            }

            if(vendor_invoice_date.trim()=='')
            {
                $(".v_vendor_invoice_date").html("Please enter Vendor Invoice Date");
                flag = 1;
            } 
            else
            {
                $(".v_vendor_invoice_date").html("");
            }
            if(vehicle_number=='')
            {
                $(".v_vehicle_number").html("Please enter Vehicle Number");
                flag = 1;
            } 
            else
            {
                $(".v_vehicle_number").html("");
            }
            if(unit_numbers.trim()=='')
            {
                $(".v_unit_numbers").html("Please enter Box/Bags/Tankers");
                flag = 1;
            } 
            else
            {
                $(".v_unit_numbers").html("");
            }

            if(bill_weight.trim()=='')
            {
                $(".v_bill_weight").html("Please enter Bill QTY");
                flag = 1;
            } 
            else
            { 
                if( parseFloat(bill_weight)<=0)
                {
                    $(".v_bill_weight").html("Please enter Bill QTY");
                    flag = 1;
                }
                else
                {
                    $(".v_bill_weight").html("");
                }
            } 
            if(flag==1)
            {
                return false;
            } 
            //$('.booking_submit').attr('disabled', 'disabled'); 
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>purchase/purchase/add_inventory/',
                data: $("#addinventory").serializeArray(), 
                dataType: "html",
                success: function(data){          
                    $('.border_booked_message_title').html("Inventory Added");              
                    $('.border_booked_message').html("Inventory Added");
                    $("#BookingSuccessModal").modal({backdrop: 'static', keyboard: false,show:true});
                    $('#addbooking').trigger("reset");  
                }
            }); 
        });


        $(document).on("submit", "#debit_note_form", function(event){
            event.preventDefault();
            var remark = $('#debit_note_remark').val();  
            var debit_amount = $('#debit_amount').val(); 
            var flag = 0;
            if(remark.trim()=='')
            {
                $('.v_remark').text('Please Enter remark');
                flag = 1;
            }
            else
            {
                $('.v_remark').text('');
            } 
            if(debit_amount.trim()=='')
            {
                $('.v_debit_amount').text('Please Enter sattelment amount');
                flag = 1;
            }
            else
            {
                $('.v_debit_amount').text('');
            } 
            if(flag == 1)
            {
                return false;
            }
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>purchase/purchase/add_debit_note/',
                data: $("#debit_note_form").serializeArray(), 
                dataType: "html",
                success: function(data){   
                    if(data==1)
                    {
                        alert("Debit note added successfully");
                        location.reload();
                    }
                    else
                    {
                        alert("try again");
                    }
                }
            });
        });


        $(document).on("submit", "#updateinventory", function(event){
            event.preventDefault();
             
            var party_id = $("#update_party").val();  
            var gr_lr_no = $("#update_gr_lr_no").val();
            var gr_lr_date = $("#update_gr_lr_date").val();
            var vendor_invoice_number = $("#update_vendor_invoice_number").val();            
            var vendor_invoice_date = $("#update_vendor_invoice_date").val();
            var vehicle_number = $("#update_vehicle_number").val();
            var ordered_qty = $("#ordered_qty").val();
            var balance_qty = $("#balance_qty").text(); 
            var bill_weight = $("#update_bill_weight").val();
            var unit_numbers = $("#update_unit_numbers").val();  

            var flag = 0;            
             
            if(party_id.trim()=='')
            {
                $(".v_update_party").html("Please Select Part");
                flag = 1;
            } 
            else
            {
                $(".v_update_party").html("");
            } 

             
            if(gr_lr_no.trim()=='')
            {
                $(".v_update_gr_lr_no").html("Please enter GR/LR No.");
                flag = 1;
            } 
            else
            {
                $(".v_update_gr_lr_no").html("");
            } 

            if(gr_lr_date=='')
            {
                $(".v_update_gr_lr_date").html("Please select gr/lr date");
                flag = 1;
            } 
            else
            {
                $(".v_update_gr_lr_date").html("");
            }
            if(vendor_invoice_number=='')
            {
                $(".v_update_vendor_invoice_number").html("Please select Vendor Invoice No.");
                flag = 1;
            } 
            else
            {
                $(".v_update_vendor_invoice_number").html("");
            }

            if(vendor_invoice_date.trim()=='')
            {
                $(".v_update_vendor_invoice_date").html("Please enter Vendor Invoice Date");
                flag = 1;
            } 
            else
            {
                $(".v_update_vendor_invoice_date").html("");
            }
            if(vehicle_number=='')
            {
                $(".v_update_vehicle_number").html("Please enter Vehicle Number");
                flag = 1;
            } 
            else
            {
                $(".v_update_vehicle_number").html("");
            }
            if(unit_numbers.trim()=='')
            {
                $(".v_update_unit_numbers").html("Please enter Box/Bags/Tankers");
                flag = 1;
            } 
            else
            {
                $(".v_update_unit_numbers").html("");
            }

            if(bill_weight.trim()=='')
            {
                $(".v_update_bill_weight").html("Please enter Bill QTY");
                flag = 1;
            } 
            else
            { 
                if( parseFloat(bill_weight)<=0)
                {
                    $(".v_update_bill_weight").html("Please enter Bill QTY");
                    flag = 1;
                }
                else
                {
                    $(".v_update_bill_weight").html("");
                }
            } 
            if(flag==1)
            {
                return false;
            } 
            //$('.booking_submit').attr('disabled', 'disabled'); 
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>purchase/purchase/update_inventory/',
                data: $("#updateinventory").serializeArray(), 
                dataType: "html",
                success: function(data){       
                    $('.border_booked_message_title').html("Inventory Updated");            
                    $('.border_booked_message').html("Inventory Updated");
                    $("#update_inventory_modal").modal('hide');
                    $("#BookingSuccessModal").modal('show'); 
                }
            }); 
        });
    });
</script>

<div id="BookingSuccessModal" data-bs-backdrop='static'  class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="modal-title border_booked_message_title">Inventory Added</h4>
            </div>
            <div class="modal-body border_booked_message">
                
            </div>
            <div class="modal-footer"> 
                <!--<button type="submit" class="btn btn-default" data-dismiss="modal">Close</button>-->
                <a class="btn btn-default" href="<?php echo  current_url();; ?>">Close</a>
            </div>
        </form>
    </div>

  </div>
</div>


<!-- Modal -->
<div id="debit_note_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form method="POST" id="debit_note_form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Debit Note</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="debit_note_id" id="debit_note_id" value="">
                <input type="hidden" name="debit_note_purchase_id" id="debit_note_purchase_id" value="">
                <input type="hidden" name="debit_note_inventory_id" id="debit_note_inventory_id" value="">                
                <div class="row"> 
                    <div class="col-md-12">
                        <div class="form-group">                                        
                            <label for="name">Debit Amount</label> 
                            <input type="text" class="form-control" name="debit_amount" id="debit_amount">
                            <span class="txt-danger v_debit_amount"></span>
                        </div>
                    </div> 
               
                
                    <div class="col-md-12">
                        <div class="form-group">                                        
                            <label for="name">Remark</label>
                            <textarea class="form-control" name="remark" id="debit_note_remark"></textarea>
                            <span class="txt-danger v_remark"></span>
                        </div>
                    </div> 
                </div> 
            </div>
            <div class="modal-footer">
                <span class="submit_reject"></span>
                <button type="submit" class="btn btn-success debit_note_modal_btn" >Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
  </div>
</div>



<!-- Modal update form -->
<div id="update_inventory_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update Inventory</h4>
            </div>  
            <form action="" class="" method="post" id="updateinventory">
                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12"> 
                                <input type="hidden" name="inventory_id" id="update_inventory_id" value="">
                                <input type="hidden" name="purchase_id" id="update_purchase_id" value="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">                                        
                                            <label for="name">Supplier</label> 
                                            <select class="form-control" id="update_party" name="party" required1=""> 
                                                <?php if($users) { 
                                                    foreach ($users as $key => $value) { ?>
                                                <option value="<?php echo $value['id']; ?>" <?php echo ($booking_info['party_id']==$value['id']) ? 'selected' : ''; ?> ><?php echo $value['name'].' - '.$value['city_name']; ?></option>
                                                <?php } } ?>
                                            </select>
                                            <span class="txt-danger v_update_party"></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="gr_lr_no">GR/LR No.</label>  
                                            <input type="text" class="form-control" id="update_gr_lr_no" name="gr_lr_no" value=""> 
                                            <span class="txt-danger v_update_gr_lr_no"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="gr_lr_date">Date</label>  
                                            <input type="text" class="form-control flatpickr-input" id="update_gr_lr_date" name="gr_lr_date" value="" readonly> 
                                            <span class="txt-danger v_update_gr_lr_date"></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vendor_invoice_number">Vendor Invoice No.</label>  
                                            <input type="text" class="form-control" id="update_vendor_invoice_number" name="vendor_invoice_number" value=""> 
                                            <span class="txt-danger v_update_vendor_invoice_number"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vendor_invoice_date">Vendor Invoice Date</label>  
                                            <input type="text" class="form-control flatpickr-input" id="update_vendor_invoice_date" name="vendor_invoice_date" value="" readonly> 
                                            <span class="txt-danger v_update_vendor_invoice_date"></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vehicle_number">Vehicle Number</label>  
                                            <input type="text" class="form-control" id="update_vehicle_number" name="vehicle_number" required1="" value=""> 
                                            <span class="txt-danger v_update_vehicle_number"></span>
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ordered_qty">Ordered QTY (MT)</label> 
                                            <input type="text" class="form-control" id="update_ordered_qty" name="ordered_qty" value="1600.00" required1="" readonly> 
                                            <span class="txt-danger v_ordered_qty"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="balance_qty">Balance QTY (MT)</label> 
                                            <input type="text" class="form-control" id="update_balance_qty" name="balance_qty" value="390.88" readonly> 
                                            <span class="txt-danger v_balance_qty"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="bill_weight">Bill QTY (MT)</label> 
                                            <input type="hidden" class="form-control" id="update_entered_bill_weight" name="entered_bill_weight" value=""> 
                                            <input type="text" class="form-control" id="update_bill_weight" name="bill_weight" value="" required1=""> 
                                            <span class="txt-danger v_update_bill_weight"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="unit_numbers">Box/Bags/Tankers</label> 
                                            <input type="text" class="form-control" id="update_unit_numbers" name="unit_numbers" value="" required1=""> 
                                            <span class="txt-danger v_update_unit_numbers"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="unit_numbers">ERP Sr. No.</label> 
                                            <input type="text" class="form-control" id="update_erp_sr_no" name="erp_sr_no" value="" required1=""> 
                                            <span class="txt-danger v_update_erp_sr_no"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="unit_numbers">Code</label> 
                                            <input type="text" class="form-control" id="update_code" name="code" value="" required1=""> 
                                            <span class="txt-danger v_update_code"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sales_executive">Remark</label> 
                                        <div class="form-group"> 
                                            <textarea class="form-control" name="remark" id="update_remark" placeholder="Remark" rows="3"></textarea>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row" >
                                    <div class="col-md-8">                                       
                                        <label for="rate">Status&nbsp;</label> 
                                        <input class="" type="radio" id="" name="update_status"  value="1" checked /> In Transist
                                        <input class="" type="radio" id="" name="update_status"  value="2"  /> In Factory                                        
                                    </div>                                         
                                </div>     
                            </div>
                        </div> 
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success booking_submit" value="Save">Update</button> 
                    <button type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">Cancel</button> 
                </div> 
            </form>
    </div>
  </div>
</div>