<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends CI_Controller {

    function __construct()
    { 
        parent::__construct();
        $this->load->library(array('session','user_agent'));
        $this->load->helper(array(
            'form',
            'url',
            'common'));
        $this->load->library('form_validation');
        $this->load->library('pagination'); 
        $this->load->model(array('purchase/purchase_model','purchase/purchase_brand_model','purchase/purchase_vendor_model','purchase/purchase_category_model','purchase/purchase_broker_model','admin_model'));      
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();          
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];  
    }

    public function checkpendingorder(){  
        $supplier = $_POST['party'];
        $condition  = array('party_id' => $supplier,'status ' => 0);
        echo $pendingorder = $this->purchase_model->checkpendingorder($condition);  
    }
    public function index(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Purchase Order"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $allow_rate_booking = $this->session->userdata('admin')['allow_rate_booking'];        
        $data['brokers'] = $this->purchase_broker_model->GetBrokers();  
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->purchase_vendor_model->GetUsersByState($states_ids);  
        $condition = array('pur_category.status' => 1)      ;
        $data['categories'] = $this->purchase_category_model->GetCategories($condition); 
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['allow_rate_booking'] = $allow_rate_booking;    

        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $config = array();
        $config["base_url"] = base_url() . "purchase/purchase/index/";
        $condition = array();
        $total_rows =  $this->purchase_model->CountBookingList($condition);
        $config["total_rows"] = $total_rows;
        // Number of items you intend to show per page.
        $config["per_page"] = $limit;
        // Use pagination number for anchor URL.
        $config['use_page_numbers'] = TRUE;
        //Set that how many number of pages you want to view.
        $config['num_links'] = 5;
        $config['uri_segment'] = 4; 
        /*$config["per_page"] = $limit;
        $config['use_page_numbers'] = TRUE; */
        $this->pagination->initialize($config);
        if ($this->uri->segment(4)) {
            $page = ($this->uri->segment(4));
        } else {
            $page = 1;
        }
        $data["links"] = $this->pagination->create_links();
        //echo "<pre>"; print_r($data["links"]); die;
        $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
        $data["total_page_count"] = ceil($config["total_rows"] / $limit);
        $page_no = ceil($config["total_rows"] / $limit);
        $data['total_page_no'] = $page_no;
        $data['current_page_no'] = $page;
        $data['limit'] = $limit;
        $data['bookings'] = $this->purchase_model->GetBookingList($condition,$limit,$page); 

        $this->load->view('purchase/purchase/index',$data);
    }

    public function add_purchase_order(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
         
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $category = $this->input->post('category');  
            $product = $this->input->post('product'); 
            $rate = $this->input->post('rate');    
            $net_rate = $this->input->post('net_rate');           
            $weight = $this->input->post('weight');   
            $delivery_date = $this->input->post('delivery_date'); 
            $booking_number = 0; 
            $broker = ($_POST['broker']!='') ? $this->input->post('broker') : NULL; 
            $ex_factory = (isset($_POST['ex_factory'])) ? $this->input->post('ex_factory') : 0;


            //$quality_condition = $this->input->post('quality_condition');  
            $ffa = $this->input->post('ffa'); 
            $sand = $this->input->post('sand'); 
            $pungency = $this->input->post('pungency'); 
            $oil_percentage = $this->input->post('oil_percentage'); 


            $mandi_expenses = $this->input->post('mandi_expenses'); 
            $moisture = $this->input->post('moisture'); 
            $freight = $this->input->post('freight'); 
            //$supplier = $this->input->post('supplier'); 

            $remark = $this->input->post('remark');  
            $payment_terms = $this->input->post('payment_terms');  
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            $new_purchase_id  = $this->purchase_model->getlast_purchase_id($book_chek_date);      
            $dt = date("Y-m-d");       
            $delivery_date = date("d-m-Y", strtotime( "$dt + $delivery_date day"));


            $broker_percentage = 0;

            if($broker)
            {
                $condition = array('broker_id' => $broker, 'category_id'=> $category);
                $brokerage_rates = $this->purchase_broker_model->GetBrokerbrokerage($condition);
                if($brokerage_rates)
                {
                    $broker_percentage = $brokerage_rates['brokerage_rate'];
                }
            }

            $insertdata = array(
                'purchase_id' =>$new_purchase_id+1,
                'party_id' =>$party,
                'category_id' =>$category, 
                'product_id' =>$product,
                'rate' =>$rate, 
                'net_rate' =>$net_rate,
                'weight' =>$weight, 
                'delivery_date' => date('Y-m-d', strtotime($delivery_date)),
                //'quality_condition' =>$quality_condition, 
                'broker_id' =>$broker, 
                'broker_percentage' => $broker_percentage,
                'remark' =>$remark,
                'payment_terms' => $payment_terms,
                'admin_id' =>$admin_id,                 
                'ex_factory' =>(trim($ex_factory)) ? trim($ex_factory) : 0, 
                'freight' =>(trim($freight)) ? trim($freight) : 0,
                //'supplier' =>$supplier,
            );


            $insertdata['mandi_expenses'] = 0;
            $insertdata['oil_percentage'] = 0;
            $insertdata['moisture'] = 0;
            $insertdata['ffa'] = 0;
            $insertdata['pungency'] = 0; 


            $insertdata['mandi_expenses'] = (isset($_POST['mandi_expenses']) && trim($_POST['mandi_expenses'])>0 ) ? trim($_POST['mandi_expenses']) : 0;
            $insertdata['oil_percentage'] = (isset($_POST['oil_percentage']) && trim($_POST['oil_percentage'])>0 ) ? trim($_POST['oil_percentage']) : 0;
            $insertdata['moisture'] = (isset($_POST['moisture']) && trim($_POST['moisture'])>0 ) ? trim($_POST['moisture']) : 0;
            $insertdata['ffa'] = (isset($_POST['ffa']) && trim($_POST['ffa'])>0 ) ? trim($_POST['ffa']) : 0;
            $insertdata['pungency'] = (isset($_POST['pungency']) && trim($_POST['pungency'])>0 ) ? trim($_POST['pungency']) : 0;
            $insertdata['sand'] = (isset($_POST['sand']) && trim($_POST['sand'])>0 ) ? trim($_POST['sand']) : 0; 

            //echo "<pre>"; print_r($insertdata); die; 
            echo $result = $this->purchase_model->AddOrder($insertdata);
        }
    }

    public function update_purchase_order(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
         
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $category = $this->input->post('category');  
            $product = $this->input->post('product'); 
            $rate = $this->input->post('rate');    
            $net_rate = $this->input->post('net_rate');           
            $weight = $this->input->post('weight');   
            $delivery_date = $this->input->post('delivery_date'); 
            $booking_number = 0; 
            $broker = ($_POST['broker']!='') ? $this->input->post('broker') : NULL; 
            $ex_factory = (isset($_POST['ex_factory'])) ? $this->input->post('ex_factory') : 0;


            //$quality_condition = $this->input->post('quality_condition');  
            $ffa = $this->input->post('ffa'); 
            $sand = $this->input->post('sand');
            $pungency = $this->input->post('pungency'); 
            $oil_percentage = $this->input->post('oil_percentage'); 


            $mandi_expenses = $this->input->post('mandi_expenses'); 
            $moisture = $this->input->post('moisture'); 
            $freight = $this->input->post('freight'); 
            //$supplier = $this->input->post('supplier'); 

            $remark = $this->input->post('remark');  
            $payment_terms = $this->input->post('payment_terms');  
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            //$new_purchase_id  = $this->purchase_model->getlast_purchase_id($book_chek_date);      
            $dt = date("Y-m-d");       
            $delivery_date = date("d-m-Y", strtotime( "$dt + $delivery_date day"));


            $broker_percentage = 0;

            if($broker)
            {
                $condition = array('broker_id' => $broker, 'category_id'=> $category);
                $brokerage_rates = $this->purchase_broker_model->GetBrokerbrokerage($condition);
                if($brokerage_rates)
                {
                    $broker_percentage = $brokerage_rates['brokerage_rate'];
                }
            }

            $insertdata = array(
                //'purchase_id' =>$new_purchase_id+1,
                'party_id' =>$party,
                'category_id' =>$category, 
                'product_id' =>$product,
                'rate' =>$rate,
                'net_rate' =>$net_rate,
                'weight' =>$weight, 
                'delivery_date' => date('Y-m-d', strtotime($delivery_date)),
                //'quality_condition' =>$quality_condition, 
                'broker_id' =>$broker, 
                'broker_percentage' => $broker_percentage,                
                'remark' =>$remark,
                'payment_terms' => $payment_terms,
                'admin_id' =>$admin_id,                 
                'ex_factory' =>(trim($ex_factory)) ? trim($ex_factory) : 0,              
                'freight' =>(trim($freight)) ? trim($freight) : 0,
                //'supplier' =>$supplier,
            );

            $insertdata['mandi_expenses'] = (isset($_POST['mandi_expenses']) && trim($_POST['mandi_expenses'])>0 ) ? trim($_POST['mandi_expenses']) : 0;
            $insertdata['oil_percentage'] = (isset($_POST['oil_percentage']) && trim($_POST['oil_percentage'])>0 ) ? trim($_POST['oil_percentage']) : 0;
            $insertdata['moisture'] = (isset($_POST['moisture']) && trim($_POST['moisture'])>0 ) ? trim($_POST['moisture']) : 0;
            $insertdata['ffa'] = (isset($_POST['ffa']) && trim($_POST['ffa'])>0 ) ? trim($_POST['ffa']) : 0;
            $insertdata['pungency'] = (isset($_POST['pungency']) && trim($_POST['pungency'])>0 ) ? trim($_POST['pungency']) : 0;
            $insertdata['sand'] = (isset($_POST['sand']) && trim($_POST['sand'])>0 ) ? trim($_POST['sand']) : 0; 
  


            $condition = array('id' => $_POST['booking_number']);
            //echo "<pre>"; print_r($insertdata); die; 
            echo $result = $this->purchase_model->UpdateBooking($insertdata,$condition);
            if($result)
            {
                $rate = $this->input->post('rate');           
                $weight = $this->input->post('weight');   
                $ffa = $this->input->post('ffa'); 
                $pungency = $this->input->post('pungency'); 
                $oil_percentage = $this->input->post('oil_percentage'); 
                $mandi_expenses = $this->input->post('mandi_expenses'); 
                $moisture = $this->input->post('moisture'); 
                $freight = $this->input->post('freight'); 

                $ffa_remark = (trim($ffa)>0) ? " and ffa @ ".round($_POST['previous_ffa'],2).":".round($_POST['ffa'],2) : '';
                $pungency_remark = (trim($pungency)>0 || trim($_POST['previous_pungency'])>0) ? " and pungency @ ".round($_POST['previous_pungency'],2).":".round($_POST['pungency'],2) : '';
                $oil_percentage_remark = (trim($oil_percentage)>0 || trim($_POST['previous_oil_percentage'])>0) ? " and oil percentage @ ".round($_POST['previous_oil_percentage'],2).":".round($_POST['oil_percentage'],2) : '';
                $mandi_expenses_remark = (trim($mandi_expenses)>0 || trim($_POST['previous_mandi_expenses'])>0) ? " and mandi expenses @ ".round($_POST['previous_mandi_expenses'],2).":".round($_POST['mandi_expenses'],2) : '';
                $moisture_remark = (trim($moisture)>0 || trim($_POST['previous_moisture'])>0) ? " and moisture @ ".round($_POST['previous_moisture'],2).":".round($_POST['moisture'],2) : '';
                $freight_remark = (trim($freight)>0 || trim($_POST['previous_freight'])>0) ? " and freight @ ".round($_POST['previous_freight'],2).":".round($_POST['freight'],2) : '';

                $remark = "Bargain Updated Rate @ ".$_POST['previous_rate'].":".$_POST['rate']." and weight @ ".round($_POST['previous_weight'],2).":".round($_POST['weight'],2).$ffa_remark.$pungency_remark.$oil_percentage_remark.$mandi_expenses_remark.$moisture_remark.$freight_remark ;
                    $remarkdata = array('booking_id' => $_POST['booking_number'],'remark' => $remark,'remark_type'=> 'Bargain Update','updated_by' => $admin_id);
                //echo "<pre>"; print_r($remark_data); die;
                $this->purchase_model->AddRemark($remarkdata);  
            }
        }
    }


    public function updatestatus(){  
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $time = date('Y-m-d H:i:s');
        $update_data = array('status' => $_POST['status'],'update_remark' => trim($_POST['remark']),'approve_reject_time' => $time );
        if($_POST['status']==2)
        {
            $update_data['settlement_amount'] = trim($_POST['sattelment_amount']);
        }
        $condition  = array('id' => base64_decode($_POST['update_status_bookig_id']));
        //echo "<pre>"; print_r($update_data); die;
        $result= $this->purchase_model->UpdateBooking($update_data,$condition);
        if($result)
        {
            //$remark_type  =  ($_POST['status']==1) ?  'rejected' : 'cancelled';
            if($_POST['status']==1)
                $remark_type  = 'Rejected';
            if($_POST['status']==2)
                $remark_type  = 'Cancelled';
            if($_POST['status']==3)
                $remark_type  = 'Completed';
            if($_POST['status']==4)
                $remark_type  = 'Approved';

            $remark_data = array('booking_id'=>base64_decode($_POST['update_status_bookig_id']),'remark' => trim($_POST['remark']),'remark_type' => $remark_type,'updated_by' => $admin_id); 
            //echo "<pre>"; print_r($remark_data); die;
            $this->purchase_model->AddRemark($remark_data);  



            if($_POST['status']==4)
            {
                $condition = array('purchase_order.id' =>  base64_decode($_POST['update_status_bookig_id']));

                $info =  $this->purchase_model->getpurchaseinfo($condition); 
                if($info['broker_mobile'])
                    $mobile_numbar = $info['broker_mobile'];
                else
                    $mobile_numbar = $info['maker_mobile'];
                $weight = $info['weight'];
                $party_name = $info['party_name'];
                $product_name = $info['product_name'];
                $rate = $info['rate'];
                $is_ex_factory = $info['ex_factory'];
                $ex_factory = ($is_ex_factory) ? 'Ex Factory' : 'FOR';

                $whatsapp_message = urlencode($weight."~".$product_name."~".$rate."~".$ex_factory); 
                $curl_url = 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbar.'&TID=1022032605&P='.$whatsapp_message; 
                
               
               $curl_watsappapi = curl_init();
                curl_setopt_array($curl_watsappapi, array( 
                CURLOPT_URL => $curl_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS =>'',
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                  ),
                ));

                $response = curl_exec($curl_watsappapi); 
                curl_close($curl_watsappapi);
            }
        }
        echo $result;
    }

    public function inventory(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $booking_id = base64_decode($this->uri->segment(4));
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById1($booking_id);
        $condition_balance_inventory = array('pur_inventory.purchase_id'=>$booking_id);
        $data['inventory_total_weight'] = $this->purchase_model->GetBookingBalanceInventory($condition_balance_inventory);
        //echo "<pre>"; print_r($data['inventory_total_weight']); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Purchase Inventory #DATA/".$booking_id; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $allow_rate_booking = $this->session->userdata('admin')['allow_rate_booking'];        
        
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->purchase_vendor_model->GetUsersByState($states_ids);   

        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $config = array();
        $config["base_url"] = base_url() . "purchase/purchase/inventory/".base64_encode($booking_id)."/";
        $condition = array('pur_inventory.purchase_id' => $booking_id); 
        $total_rows =  $this->purchase_model->CountBookingList($condition);
        $config["total_rows"] = $total_rows;
        // Number of items you intend to show per page.
        $config["per_page"] = $limit;
        // Use pagination number for anchor URL.
        $config['use_page_numbers'] = TRUE;
        //Set that how many number of pages you want to view.
        $config['num_links'] = 2;
        /*$config['uri_segment'] = 4; 
        $config["per_page"] = $limit;
        $config['use_page_numbers'] = TRUE; */
        $this->pagination->initialize($config);
        if ($this->uri->segment(5)) {
            $page = ($this->uri->segment(5));
        } else {
            $page = 1;
        }
        $data["links"] = $this->pagination->create_links();
        //echo "<pre>"; print_r($data["links"]); die;
        $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
        $data["total_page_count"] = ceil($config["total_rows"] / $limit);
        $page_no = ceil($config["total_rows"] / $limit);
        $data['total_page_no'] = $page_no;
        $data['current_page_no'] = $page;
        $data['limit'] = $limit;
        $data['inventories'] = $this->purchase_model->GetInventoryList($condition,$limit,$page); 
        //echo "<pre>"; print_r($data["inventories"]); die;
        $this->load->view('purchase/purchase/stockin',$data);
    }


    public function add_inventory(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
         
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $bargain_id = $this->input->post('booking_number'); 
            $party_id = $this->input->post('party'); 
            $gr_lr_no = $this->input->post('gr_lr_no');  
            $gr_lr_date = $this->input->post('gr_lr_date'); 
            $vendor_invoice_number = $this->input->post('vendor_invoice_number');           
            $vendor_invoice_date = $this->input->post('vendor_invoice_date');   
            $vehicle_number = $this->input->post('vehicle_number');  
            $bill_weight = $this->input->post('bill_weight'); 
            $unit_numbers = $this->input->post('unit_numbers'); 
            $remark = $this->input->post('remark'); 
            $status = $this->input->post('status'); 
            $erp_sr_no = $this->input->post('erp_sr_no'); 
            $code = $this->input->post('code'); 
            $insertdata = array(
                'purchase_id' =>$bargain_id,
                'party_id' =>$party_id,
                'gr_lr_no' =>trim($gr_lr_no), 
                'gr_lr_date' => date('Y-m-d', strtotime($gr_lr_date)),
                'vendor_invoice_number' =>$vendor_invoice_number, 
                'vendor_invoice_date' => date('Y-m-d', strtotime($vendor_invoice_date)), 
                'vehicle_number' =>trim($vehicle_number), 
                'bill_weight' => $bill_weight,
                'unit_numbers' => $unit_numbers, 
                'remark' =>trim($remark), 
                'added_by' =>$admin_id,
                'inventory_status' => trim($status),      
                'erp_sr_no' =>$erp_sr_no,
                'code' =>$code,
            );

            //echo "<pre>"; print_r($insertdata); die; 
            $result = $this->purchase_model->AddInventory($insertdata);
            if($result)
            {
                $remark_data = array('booking_id'=>$bargain_id,'remark' => "Inventory Added ".$bill_weight."(MT) vehicle number ".$vehicle_number." and gr_lr_no ".trim($gr_lr_no),'remark_type' => "Inventory Added",'updated_by' => $admin_id); 
                //echo "<pre>"; print_r($remark_data); die;
                $this->purchase_model->AddRemark($remark_data);  
            }
            echo $result;
        }
    }


    public function update_inventory(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
         
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $bargain_id= $this->input->post('purchase_id'); 
            $inventory_id = $this->input->post('inventory_id'); 
            $party_id = $this->input->post('party'); 
            $gr_lr_no = $this->input->post('gr_lr_no');  
            $gr_lr_date = $this->input->post('gr_lr_date'); 
            $vendor_invoice_number = $this->input->post('vendor_invoice_number');           
            $vendor_invoice_date = $this->input->post('vendor_invoice_date');   
            $vehicle_number = $this->input->post('vehicle_number');  
            $bill_weight = $this->input->post('bill_weight'); 
            $unit_numbers = $this->input->post('unit_numbers'); 
            $status = $this->input->post('update_status'); 
            $remark = $this->input->post('remark'); 
            $erp_sr_no = $this->input->post('erp_sr_no'); 
            $code = $this->input->post('code'); 
            $insertdata = array( 
                'party_id' =>$party_id,
                'gr_lr_no' =>trim($gr_lr_no), 
                'gr_lr_date' => date('Y-m-d', strtotime($gr_lr_date)),
                'vendor_invoice_number' =>$vendor_invoice_number, 
                'vendor_invoice_date' => date('Y-m-d', strtotime($vendor_invoice_date)), 
                'vehicle_number' =>trim($vehicle_number), 
                'bill_weight' => $bill_weight,
                'unit_numbers' => $unit_numbers, 
                'remark' =>trim($remark),   
                'inventory_status' => trim($status),
                'erp_sr_no' =>$erp_sr_no,
                'code' =>$code,
            );
            $condition = array('id' => $inventory_id);

            //echo "<pre>"; print_r($insertdata); die; 
            $result = $this->purchase_model->updateInventry($insertdata,$condition);
            if($result)
            { 
                $remark_data = array('booking_id'=>base64_decode($bargain_id),'remark' => "Inventory Updated ".$bill_weight."(MT) vehicle number ".$vehicle_number." and gr_lr_no ".trim($gr_lr_no),'remark_type' => "Inventory Updated",'updated_by' => $admin_id); 
                //echo "<pre>"; print_r($remark_data); die;
                $this->purchase_model->AddRemark($remark_data);  
            }
            echo $result;
        }
    }

    
    public function report(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Purchase Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search_purchase_report_data']); die;
        $data["links"] = '';
        if(!empty($_POST) || isset($_SESSION['search_purchase_report_data']))
        //if(!empty($_POST))
        {
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search_purchase_report_data'] = $_POST;
            else
                $_POST = $_SESSION['search_purchase_report_data']; 

            if(isset($_POST['bagainnumber']) && !empty($_POST['bagainnumber']))
            {
                $_POST['party'] = '';
                $_POST['product'] = '';
                $_POST['category'] = '';
                $_POST['booking_date_from'] = '';
                $_POST['booking_date_to'] = '';
                $_POST['employee'] = '';
                $_POST['broker'] = '';
                $_POST['status'] = '';
            }
            else
            {
                $_POST['bagainnumber'] = '';
            }

            $party_id = $_POST['party'];
            $product_id = $_POST['product'];
            $category_id = $_POST['category']; 
            $booking_date_from = date('Y-m-d',strtotime($_POST['booking_date_from']));
            $booking_date_to = date('Y-m-d',strtotime($_POST['booking_date_to'])); 
            $booking_status = (isset($_POST['status'])) ? $_POST['status'] : '';
            $role = $this->session->userdata('admin')['role'];

            $employee = $_POST['employee'];  
            $broker = $_POST['broker']; 
            $bagainnumber = $_POST['bagainnumber'];

            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            } 
            //echo "<pre>"; print_r($_POST); die;
            //$this->session->set_userdata('search__report_data', $_POST); 
            
            $this->load->library("pagination");

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 

            $config = array();
            $config["base_url"] = base_url() . "purchase/purchase/report/";
            $total_rows =  $this->purchase_model->CountBooking($party_id,$product_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$employee,$bagainnumber,$broker);
            $config["total_rows"] = $total_rows;
            // Number of items you intend to show per page.
            $config["per_page"] = 20;
            // Use pagination number for anchor URL.
            $config['use_page_numbers'] = TRUE;
            //Set that how many number of pages you want to view.
            $config['num_links'] = 5;
            $config['uri_segment'] = 4; 
            /*$config["per_page"] = $limit;
            $config['use_page_numbers'] = TRUE; */
            $this->pagination->initialize($config); 
            if ($this->uri->segment(4)) {
                $page = ($this->uri->segment(4));
            } else {
                $page = 1;
            }
            $data["links"] = $this->pagination->create_links();
            $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
            $data["total_page_count"] = ceil($config["total_rows"] / $limit);
            $page_no = ceil($config["total_rows"] / $limit);
            $data['total_page_no'] = $page_no;
            $data['current_page_no'] = $page;
            $data['limit'] = $limit;
            //echo "<pre>"; print_r($data);  print_r($config); die;
            //echo $booking_status;
            $data['bookings'] = $this->purchase_model->GetReportBooking($party_id,$product_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$bagainnumber,$broker);
            //echo "<pre>"; print_r($data); die;

        } 
        
        //echo "<pre>"; print_r($data['bookings']); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers();  
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->purchase_vendor_model->GetUsersByState($states_ids);  
        $condition = array('pur_category.status' => 1)      ;

        $data['categories'] = $this->purchase_category_model->GetCategories($condition); 
        //echo "<pre>"; print_r($data); die;
        $data['booking_date_from'] = $booking_date_from;
        $data['booking_date_to'] = $booking_date_to;

        $data['party_id'] = $party_id;
        $data['booking_status'] = $booking_status; 
        $data['category_id'] = $category_id;
        $data['product_id'] = $product_id;  
        $data['logged_in_id'] = $admin_id;
        $data['logged_role'] = $role;
        $data['employees'] = array();
        if($role==4 || $role==5)
        { 
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers();
        }
        if($role==2)
        {
            $condition = array('team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers($condition);
        }
        //echo "<pre>"; print_r($data['employees']); die;
        $this->load->view('purchase/purchase/purchase_report',$data);

    }

    public function inventoryreport(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Purchase Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search_purchase_report_data']); die;
        $data["links"] = '';
        $data['inventories'] = array();
        if(!empty($_POST) || isset($_SESSION['search_purchase_inventory_report_data']))
        //if(!empty($_POST))
        { 

            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search_purchase_inventory_report_data'] = $_POST;
            else
                $_POST = $_SESSION['search_purchase_inventory_report_data']; 

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 
            $product_id = $_POST['product']; 
            $config = array();
            $config["base_url"] = base_url() . "purchase/purchase/inventoryreport/";
            $condition = array(); 
            if(isset($_POST['party']) && !empty($_POST['party']))
                $condition['purchase_order.party_id'] = $_POST['party'];
            if(isset($_POST['category']) && !empty($_POST['category']))
                $condition['purchase_order.category_id'] = $_POST['category'];
            if(isset($_POST['product']) && !empty($_POST['product']))
                $condition['purchase_order.product_id'] = $_POST['product'];
            if(isset($_POST['employee']) && !empty($_POST['employee']))
                $condition['purchase_order.admin_id'] = $_POST['employee'];
            if(isset($_POST['broker']) && !empty($_POST['broker']))
                $condition['purchase_order.broker_id'] = $_POST['broker']; 

            if(isset($_POST['booking_date_from']) && !empty($_POST['booking_date_from']))
                $condition['booking_date_from'] = date('Y-m-d',strtotime($_POST['booking_date_from'])); 
            if(isset($_POST['booking_date_to']) && !empty($_POST['booking_date_to']))
                $condition['booking_date_to'] = date('Y-m-d',strtotime($_POST['booking_date_to'])); 


            if(isset($_POST['erp_sr_no']) && !empty($_POST['erp_sr_no']))
                $condition['pur_inventory.erp_sr_no'] = $_POST['erp_sr_no']; 
            if(isset($_POST['code']) && !empty($_POST['code']))
                $condition['pur_inventory.code'] = $_POST['code']; 
            //echo "<pre>"; print_r($_POST); die;

            $total_rows =  $this->purchase_model->CountInventoryReportList($condition);
            $config["total_rows"] = $total_rows;
            // Number of items you intend to show per page.
            $config["per_page"] = $limit;
            // Use pagination number for anchor URL.
            $config['use_page_numbers'] = TRUE;
            //Set that how many number of pages you want to view.
            $config['num_links'] = 2;
            $config['uri_segment'] = 4; 
            /*$config["per_page"] = $limit;
            $config['use_page_numbers'] = TRUE; */
            $this->pagination->initialize($config); 
            if ($this->uri->segment(4)) {
                $page = ($this->uri->segment(4));
            } else {
                $page = 1;
            }
            $data["links"] = $this->pagination->create_links();
            //echo "<pre>"; print_r($data["links"]); die;
            $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
            $data["total_page_count"] = ceil($config["total_rows"] / $limit);
            $page_no = ceil($config["total_rows"] / $limit);
            $data['total_page_no'] = $page_no;
            $data['current_page_no'] = $page;
            $data['limit'] = $limit;
            $data['inventories'] = $this->purchase_model->GetInventoryReportList($condition,$limit,$page); 

        }         
        //echo "<pre>"; print_r($data['bookings']); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers();  
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->purchase_vendor_model->GetUsersByState($states_ids);  
        $condition = array('pur_category.status' => 1)      ;

        $data['categories'] = $this->purchase_category_model->GetCategories($condition); 
        //echo "<pre>"; print_r($data); die;
        $data['booking_date_from'] = $booking_date_from;
        $data['booking_date_to'] = $booking_date_to;

        $data['party_id'] = $party_id;
        $data['booking_status'] = $booking_status; 
        $data['category_id'] = $category_id;
        $data['product_id'] = $product_id;  
        $data['logged_in_id'] = $admin_id;
        $data['logged_role'] = $role;
        $data['employees'] = array();
        if($role==4 || $role==5)
        { 
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers();
        }
        if($role==2)
        {
            $condition = array('team_lead_id' => $admin_id);
            $data['employees'] = $this->admin_model->GetAllPurchaseMakers($condition);
        }
        //echo "<pre>"; print_r($data['employees']); die;
        $this->load->view('purchase/purchase/inventoryreport',$data);
    }


    public function add_debit_note(){  
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        
        $debit_id = trim($_POST['debit_note_id']);
        
        $insertdata = array(
                            'purchase_id' => base64_decode(trim($_POST['debit_note_purchase_id'])),
                            'inventory_id' => base64_decode(trim($_POST['debit_note_inventory_id'])),
                            'remark' => trim($_POST['remark']),
                            'amount' => trim($_POST['debit_amount']),
                            'added_by' => $admin_id
                        );
        //echo "<pre>"; print_r($update_data); die;
        if($debit_id=='')
        {
            $result= $this->purchase_model->AddDebditNote($insertdata);
            if($result)
            {
                $condition = array('id' => base64_decode(trim($_POST['debit_note_inventory_id'])));
                $updatedata = array('debit_note_id' => $result);
                $result= $this->purchase_model->updateInventry($updatedata,$condition);

                //$remark_type  =  ($_POST['status']==1) ?  'rejected' : 'cancelled';
                $remark_type  = 'Debit Note';
                $remark_data = array('booking_id'=>base64_decode(trim($_POST['debit_note_purchase_id'])),'remark' => trim($_POST['remark']).'@'.trim($_POST['debit_amount']),'remark_type' => $remark_type,'updated_by' => $admin_id); 
                //echo "<pre>"; print_r($remark_data); die;
                $this->purchase_model->AddRemark($remark_data);  
            }
        }
        else
        {
            $condition = array('id' => base64_decode($debit_id));
            $result= $this->purchase_model->UpdateDebditNote($insertdata,$condition);
            if($result)
            {
                 
                $remark_type  = 'Debit Note updated';
                $remark_data = array('booking_id'=>base64_decode(trim($_POST['debit_note_purchase_id'])),'remark' => trim($_POST['remark']).'@'.trim($_POST['debit_amount']),'remark_type' => $remark_type,'updated_by' => $admin_id); 
                //echo "<pre>"; print_r($remark_data); die;
                $this->purchase_model->AddRemark($remark_data);  
            }

        }
        echo $result;
    }

    /* ====== end ================== */
    

    public function GetBookingInfoDetailsMail($booking_id){ 
        //$_POST['booking_id'] = 682;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
        $condition = array();


        $bargain_id = $booking_id;
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->purchase_model->GetSkuinfo($condition); 

        $party_name = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $broker_name = $booking_info['broker_name'];
        $sales_executive_name = $booking_info['sales_executive_name'];
        $bargain_number = 'DATA/'.$booking_info['booking_id'];
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $production_unit = $booking_info['production_unit'];
        $ordered_total_weight = $booking_info['total_weight'];
        $sku_total_weight = '';//$booking_info['total_weight_input']; 
        $sku_total_weight = '';//$booking_info['total_weight_input']; 
        $insurance = '';//($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
        //echo "<pre>"; print_r($skus); die;
        $item_total = 0;
        if($skus)
        {
            $sr = 1;
            foreach ($skus as $key => $value) {
                $item_total = $item_total+$value['quantity'];
            }
        }


        $result = '
            <h2 style="text-align:center">'.$party_name.'</h2>
            <table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                <thead>
                    <tr>
                        <th class="text-center">Orderd Date </th>
                        <th class="text-center">Broker </th>
                        <th class="text-center">Sales Executive</th>
                    </tr>
                    <tr>
                        <td> '.$order_date.'</td>
                        <td>'.$broker_name.'</td>
                        <td>'.$sales_executive_name.'</td>
                    </tr>
                    <tr>
                        <th class="text-center">Bargain Number</th>
                        <th class="text-center">Dispatch Date</th>
                        <th class="text-center">Production Unit</th>
                    </tr>
                    <tr>
                        <td>'.$bargain_number.'</td>
                        <td>'.$dispatch_date.'</td>
                        <td>'.$production_unit.'</td>
                    </tr>
                    <tr>
                        <th class="text-center">Orderd Total Weight</th>
                        <th class="text-center">SKU Total Weight</th>
                        <th class="text-center">Total Items </th>
                    </tr>
                    <tr>
                        <td>'.$ordered_total_weight.'</td>
                        <td>'.$sku_total_weight.'</td>
                        <td>'.$item_total.'</td>
                    </tr>
                    <tr>
                        <th class="text-center">Insurace Included in price </th>
                        <th class="text-center">Price ex-factory</th>
                        <th class="text-center"></th>
                    </tr>
                    <tr>
                        <td>'.$insurance.'</td>
                        <td>'.$is_for.'</td>
                        <td></td>
                    </tr>'; 
                    $result .= '<tr>
                        <th class="text-left" colspan="3">Remark</th> 
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left">'.$remark.'</td> 
                    </tr>'; 
                $result .= '</thead>
            </table>';

        return  ''.$result; die;

        
    }

    public function GetBookingInfoDetails(){ 
        //$_POST['booking_id'] = 682;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
        $condition = array();


        $bargain_id = $_POST['booking_id']; 
        $condition = array('purchase_order.id' => $bargain_id);
        $booking_info = $this->purchase_model->GetBookingInfoById($bargain_id);       


        //echo "string"; die;
        $party_name = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $broker_name = $booking_info['broker_name'];
        $sales_executive_name = $booking_info['sales_executive_name'];
        $bargain_number = 'DATA/'.$booking_info['purchase_id'];
        $delivery_date = $booking_info['delivery_date'];
        $dt = date('d-m-Y', strtotime($booking_info['delivery_date']));
        $dispatch_date =date('d-m-Y', strtotime($booking_info['delivery_date']));
        $production_unit = '';//$booking_info['production_unit'];
        $ordered_total_weight = $booking_info['weight'];
        
        $is_for = ($booking_info['ex_factory']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 

       
        $category_name = $booking_info['category_name']; 
        $product_name = $booking_info['product_name']; 

        $category_id = $booking_info['category_id']; 
        $product_id = $booking_info['product_id'];

        $condition_rouduct = array('pur_products.id'=> $product_id);
        $attributes = $this->purchase_category_model->Getallattributesbyproduct_id($condition_rouduct);


        $l1_value = "";
        $l2_value = "";
        $l3_value = "";  
        $l1 = "FFA";
        $l2 = "";
        $l3 = "";
        if($category_id==2)
        {
            $l1 = "Oil Percentage";
            $l2 = "Moisture";
            $l1_value = $booking_info['oil_percentage']; 
            $l2_value = $booking_info['moisture']; 
        }
        else
        { 
            $l1_value = $booking_info['ffa']; 
        }
        if($category_id==7)
        {
            $l1 = "Oil Percentage";
            $l2 = "Moisture";
            $l3 = "Sand & Silica";
            $l1_value = $booking_info['oil_percentage']; 
            $l2_value = $booking_info['moisture'];
            $l3_value = $booking_info['sand']; 
        }

        if($product_id==5 || $product_id==9)
        { 
            $l2 = "Pungency";
            $l2_value = $booking_info['pungency']; 
        }  
        if($product_id==9)
            $l2 = "Cloud Point";

        $rate = $booking_info['rate']; 
        //echo "<pre>"; print_r($skus); die;
        
        $comments = $this->purchase_model->GetBookingRemarks($booking_info['id']);
        $labhistory_condition = array('pur_inventory.purchase_id'=>$booking_info['id']);
        $labhistory = $this->purchase_model->GetBookingLabHistory($labhistory_condition); 
        //echo "<pre>"; print_r($labhistory); die;

        $result = '
            <h2 style="text-align:center">'.$party_name.'<br> (Purchase Number DATA/'.$bargain_id.')</h2>
            <table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                <thead>
                    <tr>
                        <th class="text-center">Orderd Date </th>
                        <th class="text-center">Broker </th>
                        <th class="text-center">Sales Executive</th>
                    </tr>
                    <tr>
                        <td> '.$order_date.'</td>
                        <td>'.$broker_name.'</td>
                        <td>'.$sales_executive_name.'</td>
                    </tr>
                    <tr>
                        <th class="text-center">Bargain Number</th>
                        <th class="text-center">Est. Receiving Date</th>
                        <th class="text-center">Orderd Total Weight</th>
                    </tr>
                    <tr>
                        <td>'.$bargain_number.'</td>
                        <td>'.$dispatch_date.'</td>
                        <td>'.$ordered_total_weight.'</td>
                    </tr>
                    
                    <tr>
                        <th class="text-center">Category</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">Rate</th>
                    </tr>
                    <tr>
                        <td>'.$category_name.'</td>
                        <td>'.$product_name.'</td>
                        <td>'.$rate.'</td>
                    </tr>
                    <tr>
                        <th class="text-left" colspan="3">Quality Conditons</th> 
                    </tr>
                    <tr> 
                        <td colspan="3">
                        <table class="table table-striped table-bordered table-hover text-center">
                            <tr>';
                            $attribute_data="";
                            if($attributes)
                            {
                                foreach ($attributes as $attributes_key => $attributes_value) {
                                    $result .= '<th class="text-center">'.$attributes_value['name'].'</th>';
                                    $attribute_data .= '<td class="text-center">'.$booking_info[$attributes_value['alias']].'</td>';
                                }
                            }
                            $result .= '</tr>
                            <tr>'.$attribute_data.'</tr>
                        </table>
                        </td> 
                    </tr>

                    ';
                    
                    /*$result .= '
                    <tr> 
                        <th class="text-center">'.$l1.'</th>
                        <th class="text-center">'.$l2.'</th>
                        <th class="text-center">'.$l3.'</th>
                    </tr>
                    <tr> 
                        <td>'.$l1_value.'</td>                        
                        <td>'.$l2_value.'</td>
                        <td>'.$l3_value.'</td>
                    </tr>'; 
                    */

                    $result .= '
                    <tr>
                        <th class="text-left" colspan="3">Price ex-factory : '.$is_for.'</th> 
                    </tr>';
                    $result .= '
                    <tr>
                        <th class="text-left" colspan="3">Remark</th> 
                    </tr>
                    <tr>
                        <td colspan="3" class="text-left" style="text-align:left;">'.$remark.'</td> 
                    </tr>'; 
                $result .= '</thead>
            </table>';
            if($labhistory)
            {
                $result .= '<hr><h5>Lab Report</h5><div class="table-responsive"><table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                    <thead>
                        <tr>
                            <th class="text-center">Sr.No.</th>
                            <th class="text-center">Erp Sr No</th>
                            <th class="text-center">Code</th>
                            <th class="text-center">Weight (MT)</th>';
                            if($l1)
                                $result .= '<th class="text-center">'.$l1.'</th>';
                            if($l2)
                                $result .= '<th class="text-center">'.$l2.'</th>';
                            if($l3)
                                $result .= '<th class="text-center">'.$l3.'</th>';
                            $result .= '<th class="text-center">Colour</th>
                            <th class="text-center">Remark</th>
                        </tr>
                    </thead>';
                    $sr = 1;

                    foreach ($labhistory as $key => $value) { 

                        $l1_value = "";
                        $l2_value = "";  
                        $l3_value = "";  
                        if($category_id==2)
                        { 
                            $l1_value = $value['oil_percentage']; 
                            $l2_value = $value['moisture']; 
                        }
                        else
                        { 
                            $l1_value = $value['ffa']; 
                        }

                        if($product_id==5 || $product_id==9)
                        {                              
                            $l2_value = $value['pungency']; 
                        }   

                        if($category_id==7)
                        { 
                            $l1_value = $value['oil_percentage']; 
                            $l2_value = $value['moisture']; 
                            $l3_value = $value['sand']; 
                        }

                        $result .= '<tr>
                            <td>'.$sr.'</td>
                            <td>'.$value['erp_sr_no'].'</td>
                            <td>'.$value['code'].'</td>
                            <td>'.$value['bill_weight'].'</td>';
                            if($l1)
                                $result .= '<td>'.$l1_value.'</td>';
                            if($l2)
                                $result .= '<td>'.$l2_value.'</td>';
                            if($l3)
                                $result .= '<td>'.$l3_value.'</td>';
                            $result .= '<td>'.$value['color'].'</td>
                            <td>'.$value['remark'].'</td>
                        </tr>';
                        $sr++;
                    } 
                $result .= '</table></div>';
            } 
            if($comments)
            {

                $result .= '<hr><h5>Comments History</h5><div class="table-responsive"><table class="table table-striped table-bordered table-hover text-center" style="text-align:center">
                    <thead>
                        <tr>
                            <th class="text-center">Sr.No.</th>
                            <th class="text-center">Comment Type</th>
                            <th class="text-center">Comment</th>
                            <th class="text-center">Update By</th>
                            <th class="text-center">Updated On</th>
                        </tr>
                    </thead>';  
                    $sr = 1;
                    foreach ($comments as $key => $value) {
                        $result .= '<tr>
                            <td>'.$sr.'</td>
                            <td>'.$value['remark_type'].'</td>
                            <td>'.$value['remark'].'</td>
                            <td>'.$value['updated_by_name'].'</td>
                            <td>'.date('d-m-Y H:i:s', strtotime($value['created_at'])).'</td>
                        </tr>';
                        $sr++;
                    } 
                $result .= '</table></div>';
            }

        echo $result; die;

        
    }

    public function GetBookingInfoDetailsPdf(){ 
        //$_POST['booking_id'] = 682;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
        $condition = array();
        $remark = $_POST['ApproveRemark'];
        $this->purchase_model->UpdateAproveStatusAll($_POST['booking_id']); 

        if(trim($remark))
        {
            $remark_data = array('booking_id'=>$_POST['booking_id'],'remark' => trim($remark),'remark_type' => 'apprval'); 
            $this->purchase_model->AddRemark($remark_data);  
        }

        $bookings = $this->purchase_model->GetBookingInfoDetailsPdf($_POST['booking_id']); 
        //echo "<pre>"; print_r($bookings); die;
        $remarks = $this->purchase_model->GetBookingRemarks($_POST['booking_id']); 


        $party_name = $bookings[0]['party_name'];
        $city_name = $bookings[0]['city_name'];
        $booking_date = date("d-m-Y", strtotime($bookings[0]['created_at']));
        //echo "<pre>"; print_r($bookings); die;
        //$result = '<table class="table table-striped table-bordered table-hover" id="datatable_sample"><thead><tr><th>S.No</th><th>Brand name</th><th>Category Name</th><th>Product Name</th><th>Quantity</th><th>Rate(Without ins.)</th><th>Insurance</th><th>Rate(FOR With ins.)</th><th>Broker</th><th>Booked by</th><th>Date/Time</th><th>Status</th><th>Remark</th><th>Terms</th><th>Action</th></tr></thead><tbody>';

        $result = ' 
            <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <style>
            body {font-family: Poppins; }
            .table-border td {
              border: none;
              border-collapse: collapse;
            }
            .table-border th {
              border: none;
              border-collapse: collapse;
            }
            </style>  
                    <table colspan style="width:750px;margin:0 auto;">
                         
                        <tr>
                            <td colspan="2" style="padding: 0px 40px;">
                                <table style="width:100%;">
                                    <tr>
                                        <td colspan="" style="width:60%">
                                        <p style="font-size: 20px;font-family: Poppins;text-align: left;font-weight: 600;color: #404041;line-height:22px;margin: 0;">'.$party_name.'</p>
                                        <p style="font-size: 18px;font-family: Poppins;text-align: left;font-weight: 600;color: #404041;line-height:22px;margin: 0;">'.$city_name.'</p>
                                        </td>
                                        <td colspan="" style="padding: 10px 0px 10px 10px;background: #f2fbfe;">
                                        <p style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;">Bargain No : <span style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;padding-left: 20px;">#SHAIL/'.$_POST['booking_id'].'</span></p>
                                        <p style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;">Booking Date : <span style="font-size: 14px;font-family: Poppins;text-align: left;font-weight: 400;color: #404041;line-height:22px;margin: 0;">'.$booking_date.'</span></p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <tr style="">
                            <td colspan="2" style="padding: 0px 20px;">
                                <table class="table-border" colspan style="width:100%;margin-top:10px;border-collapse: collapse;">
                                    <thead>
                                    <tr>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">S.No.</p></th>
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Brand Name</p></th>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Product</p></th>
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Packed In</p></th>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Qty.</p></th>
                                        <th style="background:#0294d8;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Rate</p></th>
                                        <th style="background:#047bc0;padding:10px 5px;"><p style="font-size:14px;font-family: Poppins;text-align:center;font-weight: 400;color: #fff;line-height:22px;">Total</p></th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>';

                                        $total_weight = 0; 
                                        if($bookings) { 
                                            $i = 1;
                                            $total = 0;
                                            foreach ($bookings as $key => $value) {
                                                $price = $value['rate'];
                                                $total = $total+($value['quantity']*$value['rate']);
                                        $result .= '<tr  style="background:#d9f3fd;">
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$i.'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['brand_name'].'</p></td>
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['category_name'].'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['product_name'].'</p></td>
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['quantity'].'</p></td>
                                            <td style="padding:10px 5px;background: #ceeffc;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['rate'].'</p></td>
                                            <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #404041;line-height:22px;">'.$value['quantity']*$value['rate'].'</p></td>
                                                        </tr>';
                                                $i++;
                                            }
                                        }
                                        $result .= '</tbody>
                                    <tr  style="background:#404041;border-top: 3px solid #fff;">
                                        <td colspan="6" style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:right;color: #fff;line-height:22px;">Total Amount:</p></td>
                                        <td style="padding:10px 5px;"><p style="font-size:12px;font-family: Poppins;font-weight: 600;text-align:center;color: #fff;line-height:22px;">'.$total.'</p></td>
                                    </tr>
                                    
                                </table>
                            </td>               
                        </tr>';
                        if($remarks)
                        {
                        $result .= '<tr>
                            <td colspan="2" style="padding: 0px 20px;"> 
                                <p style="font-size: 14px;font-family: Poppins;font-weight: 600;color: #404041;line-height:32px;margin-top: 22px;margin-bottom: 0px;border-bottom:1px solid #0294d8;width:200px;">Remark </p>
                                 <ol>';
                                foreach ($remarks as $key => $remark) {
                                    $result .= '<li>'.$remark['remark'].'</li>';
                                }  
                            $result .= '</ol></td>
                        </tr>';
                        }
                        $result .= '
                    </table>'; 
            //echo APPPATH."third_party/mpdf/mpdf.php"; die;
            //echo $result; die;
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','0','0','38','18','0','0'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px;">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                <h2 style="font-size: 40px;font-family: Poppins;text-align: right;font-weight: 800;color: #404041;line-height:22px;">Booking Details</h2>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '<table  style="width:100%; ">
                        <tr >
                            <td  colspan="2"  style="background-image: url('.base_url('assets/images/footer-line.png').');background-size: 100% 87px; height: 87px; background-repeat: no-repeat;"> <span style="display: block;color: #fff;padding-top: 25px;padding-left: 21px;">{PAGENO} out of {nbpg}</span> 
                           
                            </td>
                        </tr></table>';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($result);
            $f_name = 'dsdsad.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'F');
            include 'mailer/email.php'; 
            $from = "webmaster@dil.in";
            $from_name = "Bargain Invoice";
            $subject   = 'Bargain Invoice'; 

            if($_POST['production_unit']=='alwar')
                $email = 'manvendra.s@bharatsync.com';
            else
                $email = 'rohittak@dil.in';
            smtpmailer($email, $from,$from_name,$subject, "test",$invpice_name);
            //echo $result; die;
    }
    public function BookingList(){  
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $condition = array();
        if($role==1) //maker
        {
            $condition = array('admin_id' => $userid);
        }
        elseif ($role==2) { //checker
            $condition = array('admin.team_lead_id' => $userid);
        } 

        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $config = array();
        $config["base_url"] = base_url() . "booking/index/";
        $total_rows =  $this->purchase_model->CountBookingList($condition);
        $config["total_rows"] = $total_rows;
        // Number of items you intend to show per page.
        $config["per_page"] = $limit;
        // Use pagination number for anchor URL.
        $config['use_page_numbers'] = TRUE;
        //Set that how many number of pages you want to view.
        $config['num_links'] = 2;
        /*$config['uri_segment'] = 4; 
        $config["per_page"] = $limit;
        $config['use_page_numbers'] = TRUE; */
        $this->pagination->initialize($config);
        if ($this->uri->segment(3)) {
            $page = ($this->uri->segment(3));
        } else {
            $page = 1;
        }
        $data["links"] = $this->pagination->create_links();
        $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
        $data["total_page_count"] = ceil($config["total_rows"] / $limit);
        $page_no = ceil($config["total_rows"] / $limit);
        $data['total_page_no'] = $page_no;
        $data['current_page_no'] = $page;
        $data['limit'] = $limit;



        $bookings = $this->purchase_model->GetBookingList($condition,$limit,$page); 
        $i = 1;
        $response ='';
        if($bookings)
        {
            foreach ($bookings as $key => $value) { 
                $response .='<tr class="odd gradeX"><td>'.$i.'</td><td><span title="'.$value['admin_name'].'">DATA/'.$value['booking_id'].'</span></td>

                <td>'.$value['party_name'].'</td>

                <td>'.$value['city_name'].'</td><td>'.$value['brand_name'].'</td><td>'.$value['category_name'].'</td><td>'.$value['quantity'].'</td><td>'.$value['rate'].'</td><td>'.date("d-m-Y", strtotime($value['created_at'])).'</td><td>';
                if($value['status']==0) { 
                    $response .='<a href="'.base_url().'booking/edit/'.base64_encode($value['id']).'" class="btn btn-default detail">Edit</a>';
                }
                if($value['is_lock']) { 
                                                
                $response .='<a href="javascript:void(0)" rel="'.$value['booking_id'].'" class="btn btn-default detail btn_report1" data-production_unit="'.$value['production_unit'].'">Report</a>';
                } else { if($role==1) { 
                $response .='<a href="'.base_url().'booking/sku/'.base64_encode($value['booking_id']).'" rel="'.$value['booking_id'].'"  class="btn btn-default detail">Add SKU</a>';
             }  } 

                $response .='</td></tr>';
                $i++;
            }
            $response .='<tr><td>'.$data["links"].'</td></tr>';
        }
        else
        {
            $response .='<tr class="odd gradeX"><td colspan="7">No Record Found</td></tr>';
        }
        echo $response;
    }
   

    public function approve_order()
    {
         
        $bargain_id = $_POST['booking_id']; 
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];  

        $condition = array('booking_id' => $bargain_id);
        //$data['booking_info'] = $this->purchase_model->GetBookingInfoById(base64_decode($bargain_id));
        $updatedata = array('status'=>2);
        $result = $this->purchase_model->UpdateBooking($updatedata,$condition);
        if($result)
        {

            $condition = array('purchase_order.booking_id' => $bargain_id);
            $info =  $this->purchase_model->getpurchaseinfo($condition);
            if($info['broker_mobile'])
                $mobile_numbar = $info['broker_mobile'];
            else
                $mobile_numbar = $info['maker_mobile'];
            $weight = $info['weight']. '(MT)';
            $party_name = $info['party_name'];
            $product_name = $info['product_name'];
            $rate = $info['rate'];
            $is_ex_factory = $info['ex_factory'];
            $ex_factory = ($is_ex_factory) ? 'Ex Factory' : 'FOR';

            $whatsapp_message = urlencode($weight."~".$product_name."~".$rate."~".$ex_factory); 
            $curl_url = 'https://dlr.dil.in/notify.jsp?TYPE=WAB&F=918764216255&T='.$mobile_numbar.'&TID=1022032605&P='.$whatsapp_message;
            
           
           $curl_watsappapi = curl_init();
            curl_setopt_array($curl_watsappapi, array( 
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>'',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
              ),
            ));

            $response = curl_exec($curl_watsappapi);
            curl_close($curl_watsappapi);
        }
        echo $result;
    }

    public function sendmail_plant()
    {
        $from = "webmaster@dil.in";
        $from_name = "Bargain Invoice";
        $subject   = 'Bargain Invoice';  
        $bargain_id = $_POST['booking_id'];
        
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array(); 
        $condition = array('booking_id' => $bargain_id);
        //$data['booking_info'] = $this->purchase_model->GetBookingInfoById(base64_decode($bargain_id));
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->purchase_category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->purchase_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->purchase_category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers(); 
        $data['brands'] = $this->purchase_brand_model->GetAllBrand();
        $data['users'] = $this->purchase_vendor_model->GetUsers();

        $data['categories'] = $this->purchase_category_model->GetCategories();

        $data['distinct_categories'] = $this->purchase_category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
         

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);
        $mail_message = "Hello , <br><br> Order <strong>#DATA/".$booking_info['booking_id']."</strong> is locked for <strong>".$booking_info['party_name']." - ".$booking_info['city_name']."</strong> <br><br> You can check order details in attached file";

        ///return $fileName.'______'.$mail_message;


        $email = 'manvendra.s@bharatsync.com'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'rohittak@dil.in';
         
        $file_name = $fileName;
        $mail_mesage = $mail_message; 
        $attach_file = FCPATH.'/'.$file_name; 
        include 'mailer/t.php'; 
        unlink($attach_file);
    }


    public function sendmail_plant_lock($bargain_id)
    {
        $from = "webmaster@dil.in";
        $from_name = "Bargain Invoice";
        $subject   = 'Bargain Invoice';  
        //$bargain_id = $_POST['booking_id'];
        
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array(); 
        $condition = array('booking_id' => $bargain_id);
        //$data['booking_info'] = $this->purchase_model->GetBookingInfoById(base64_decode($bargain_id));
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById($bargain_id);
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->purchase_category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->purchase_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->purchase_category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers(); 
        $data['brands'] = $this->purchase_brand_model->GetAllBrand();
        $data['users'] = $this->purchase_vendor_model->GetUsers();

        $data['categories'] = $this->purchase_category_model->GetCategories();

        $data['distinct_categories'] = $this->purchase_category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
         

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);
        $mail_message = "Hello , <br><br> Order <strong>#DATA/".$booking_info['booking_id']."</strong> is locked for <strong>".$booking_info['party_name']." - ".$booking_info['city_name']."</strong> <br><br> You can check order details in attached file";

        ///return $fileName.'______'.$mail_message;


        $email = 'manvendra.s@bharatsync.com'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'rohittak@dil.in';

        /*$email = 'vopsales@dil.in'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'pankajkumar.gupta@datafoods.com';*/
         
        $file_name = $fileName;
        $mail_mesage = $mail_message; 
        $attach_file = FCPATH.'/'.$file_name; 
        include 'mailer/t.php'; 
        unlink($attach_file);
    }

    public function sendmail_plant_lock_bulk($party_id)
    {
        
        //$party_id = $_POST['party_id'];
        $from = "webmaster@dil.in";
        $from_name = "Bargain Invoice";
        $subject   = 'Bargain Invoice';  
        //$bargain_id = $_POST['booking_id'];
        
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 

        $condition  = array('party_id'=>$party_id,'is_mail'=>0,'is_lock'=>1,'status'=>2);
        $bargains = $this->purchase_model->getpenidngbargainInfo($condition);
        //echo "<pre>"; print_r($bargains); die;
        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        

        if($bargains)
        {
            $bargain_loop = 1;
            $i = 1;
            $row = 11;
            $party_name = '';
            $party_city = '';
            $bargaind_ids = array();
            $bargaind_numbers = array();
            $fileName = "";
            foreach ($bargains as $key => $value) {
                $col= 'B';
                $booking_info = $value;
                $condition = array('booking_skus.booking_id' => $booking_info['id']); 
                $bargaind_ids[] =$booking_info['id'];
                $bargaind_numbers[] ='#DATA/'.$booking_info['booking_id'];
                $party_name = $booking_info['party_name'];
                $city_name = $booking_info['city_name'];

                $skus = $this->purchase_model->GetSkuinfo($condition);  
                $data['logged_in_id'] = $userid;
                $data['logged_role'] = $role;
                $data['bargain_id'] = $booking_info['id'];
                $data['category_name'] = $booking_info['category_name']; 
                $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
                $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
                $remark = $booking_info['remark']; 
                
                

                if($bargain_loop==1)
                {
                    $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
                    $fileName = $booking_info['party_name'].'.xls'; 
                    $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
                    $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);
                    $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);
                }
                $bargain_loop++;

                if($skus)
                { 
                
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

                    $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                    $row++;
                    $item_total = 0;
                    if($skus)
                    { 
                        $sr_no = 1;
                        foreach ($skus as $key => $value) {
                            $sku_col = 'B';
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$sr_no);
                            $sku_col++;
                            $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$value['name'].$packing);
                            $sku_col++;
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$value['quantity']);
                            $sku_col++;
                            $objPHPExcel->getActiveSheet()->setCellValue($sku_col.$row,$value['weight']);
                            $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                            $row++;
                            $sr_no++;
                            $item_total = $item_total+$value['quantity'];
                        }

                        $total_col = 'C';
                        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$total_col++;
                        $objPHPExcel->getActiveSheet()->setCellValue($total_col.$row,$item_total);$total_col++;
                        $objPHPExcel->getActiveSheet()->setCellValue($total_col.$row,$booking_info['total_weight_input']);
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1); 
                        $row = ($row-$sr_no)-8;  
                        $header_row_start = $row;
                        $header_row = $row;
                        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Orderd Date : '.$order_date);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Broker : '.$booking_info['broker_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Sales Executive : '.$booking_info['sales_executive_name']);
                        $row++; $header_row++;
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Bargain Number : DATA/'.$booking_info['booking_id']);
                        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Dispatch Date : '.$dispatch_date);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Production Unit : '.$booking_info['production_unit']);
                        $row++;$header_row++;

                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Brand: '.$booking_info['brand_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Product : '.$booking_info['category_name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Rate : '.$booking_info['rate']);
                        $row++;$header_row++;

                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Orderd Total Weight : '.$booking_info['total_weight']);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'SKU Total Weight : '.$booking_info['total_weight_input']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,'Total Items : '.$item_total);
                        $row++;$header_row++;


                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Insurace Included in price  : '.$insurance);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,'Price ex-factory : '.$is_for); 
                        $row++;$header_row++;
                        $objPHPExcel->getActiveSheet()->mergeCells('C'.$row.':E'.$row);
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,'Remark  : '.$remark); 
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$header_row_start.':E'.$header_row)->applyFromArray($styleArray1);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$header_row_start.':E'.$header_row)->applyFromArray($BorderstyleArray);


                        $objPHPExcel->getActiveSheet()->getStyle('C'.$header_row.':E'.$header_row)->getAlignment()->applyFromArray(
                            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
                        );
                        $row = ($row+$sr_no)+13;

                    }
                }


                $row++;
            }
        }

         
        //echo $fileName; die;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);  
        
        
        $order_ids = implode(',', $bargaind_numbers);

        $mailremark  = '';
        $mail_message = "Hello , <br><br> Order <strong>".$order_ids."</strong> is locked for <strong>".$party_name." - ".$city_name."</strong> <br><br> You can check order details in attached file";

        if(isset($_POST['reamrk']))
        {
            $mail_message  .= '<br> Remark : '.trim($_POST['reamrk']); 
        }

        ///return $fileName.'______'.$mail_message;


       
        

        $email = 'vopsales@dil.in'; 
        if($booking_info['production_unit']=='alwar')
            $email = 'pankajkumar.gupta@datafoods.com';
         
        $file_name = $fileName;
        $mail_mesage = $mail_message; 
        $attach_file = FCPATH.'/'.$file_name; 
        include 'mailer/t.php';  
        //unlink($attach_file); 
        $this->purchase_model->updatebargiansmail($bargaind_ids);
    }

    public function add_skus(){ 
        //echo "<pre>"; print_r($_POST); die;
        if(!empty($_POST))
        {
            $id = $this->input->post('id'); 
            $booking_id = base64_decode($this->input->post('booking_id')); 
            $category = $this->input->post('category'); 
            $brand = $this->input->post('brand'); 
            $products = $_POST['product'];

            $quantities = $_POST['quantity']; 
            $packing_weight = $_POST['packing_weight'];

            if($products)
            {
                $added = 1;
                if($_POST['update_data'])
                {
                    $weight_update =  $this->input->post('update_weight'); 
                    $condition_booking = array('id' => $id);
                    $update_data_booking = array('brand_id' => $brand,'category_id' => $category,'total_weight' => $weight_update);
                    $this->purchase_model->UpdateBookingBooking($update_data_booking,$condition_booking); 
                    $condition_all = array('booking_id' => $id);
                    $this->purchase_model->DeleteSKU($condition_all); 
                } 
                $update_data_booking_weight = array('total_weight_input' => $_POST['total_weight_input'],'remaining_weight' => $_POST['remaining_weight'],'is_lock' => $_POST['flag'],'production_unit' => $_POST['production_unit']);
                $condition_booking_weight = array('id' => $id);
                $this->purchase_model->UpdateBookingBooking($update_data_booking_weight,$condition_booking_weight); 

                foreach ($products as $key => $product) {                    
                    
                        $condition = array( 
                            'booking_id' => $id,
                            'bargain_id' => $booking_id,
                            'product_id' => $product, 
                        );
                        $skudata = array(
                            'brand_id' => $brand,
                            'category_id' => $category, 
                            'booking_id' => $id,
                            'bargain_id' => $booking_id,
                            'product_id' => $product,
                            'weight' => $packing_weight[$key],
                            'quantity' => ($quantities[$key]) ? $quantities[$key] : NULL,
                        ); 
                    if($quantities[$key])
                    {
                        $flag = $this->purchase_model->AddSKU($condition,$skudata); 
                        if(!$flag)
                            $added = 0;
                        
                        if($_POST['flag']){
                            //$this->sendmail_plant_lock($booking_id);
                        }
                    }
                    else
                    {
                        $this->purchase_model->DeleteSKU($condition); 
                    }             
                } 
                     
                //echo $added;

                if($_POST['flag']==1 && $added)
                { 
                    //$this->sendmail_plant_lock($booking_id);
                }
                echo $added; die;
            }
        }
    }
    

    public function update_booking(){ 
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category');  
            $rate = $this->input->post('rate');                  
            $quantity = $this->input->post('quantity');  
            $weight = $this->input->post('weight');   
            $booking_date = $this->input->post('booking_date'); 
            $booking_number = 0;
            $today_cur_date =  date("dm"); 
            $book_chek_date = date("Y-m-d")." 00:00:00.000000"; 
            $broker = ($_POST['broker']!='') ? $this->input->post('broker') : NULL; // $this->input->post('broker');   
            $insurance = (isset($_POST['insurance'])>0) ? $this->input->post('insurance') : 0;
            $is_for = (isset($_POST['ex_factory'])) ? $this->input->post('ex_factory') : 0;

            $remark = $this->input->post('remark'); 
            $sales_executive = $this->input->post('sales_executive'); 
            $shipment_date = $this->input->post('shipment_date'); 
                
           
            $insertdata = array( 
                'party_id' =>$party,
                'brand_id' =>$brand,
                'category_id' =>$category, 
                'quantity' =>$quantity,
                //'weight' =>$quantity*15,
                'rate' =>$rate,
                'broker_id' =>$broker,
                'insurance' =>$insurance,
                'is_for' =>$is_for,
                //'admin_id' =>$admin_id,
                'total_weight' => $weight,
                'production_unit' => $_POST['production_unit'],
                'remark' =>$remark,
                'sales_executive_id' =>$sales_executive,
                'shipment_date' =>date('Y-m-d', strtotime($shipment_date)),
            );
            $condition = array('id' => $_POST['booking_number']);
            echo $result = $this->purchase_model->UpdateBooking($insertdata,$condition);
        }
    }

    public function booked_sku_info()
    { 
        $condition = array('booking_skus.booking_id' => $_POST['id']);
        $skus = $this->purchase_model->GetSkuinfo($condition);
        //echo "<pre>"; print_r($skus); die;
        $res = "<table class='table table-striped table-bordered table-hover dataTable no-footer'><tr><td>S.No.</td><td>Name</td><td>Quantity</td></tr>";
        if($skus)
        {
            $i = 1;
            foreach ($skus as $key => $value) {
                $qty = '';
                if($value['packing_items_qty'])
                    $qty = '*'.$value['packing_items_qty'];
                $res .= "<tr><td>".$i."</td><td>".$value['name'].$qty."</td><td>".$value['quantity']."</td></tr>";
                $i++;
            }
        }
        else
        {
            $res .= "<tr><td colspan='3'>No Recod Found</td>";
        }
        $res .= "</table>";
        $res .= "<span style='color:red'><strong>Note</strong> If you lock this order. This will not change </span>";
        echo $res;
    }

    public function sku($bargain_id){   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById(base64_decode($bargain_id));
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->purchase_category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_id' => $data['booking_info']['id']);
        $data['skus'] = $this->purchase_model->GetAllSkus($condition);
 
        $data['products'] = $this->purchase_category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);
        //echo "<pre>"; print_r($data['products']); die;

        //echo "<pre>"; print_r($data['skus']); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers(); 
        $data['brands'] = $this->purchase_brand_model->GetAllBrand();
        $data['users'] = $this->purchase_vendor_model->GetUsers();

        $data['categories'] = $this->purchase_category_model->GetCategories();

        $data['distinct_categories'] = $this->purchase_category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name'];
        
        $data['bookings'] = array();
        $this->load->view('sku',$data);

    }

    public function createexcel($bargain_id)
    { 
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById(base64_decode($bargain_id));
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->purchase_category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->purchase_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->purchase_category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers(); 
        $data['brands'] = $this->purchase_brand_model->GetAllBrand();
        $data['users'] = $this->purchase_vendor_model->GetUsers();

        $data['categories'] = $this->purchase_category_model->GetCategories();

        $data['distinct_categories'] = $this->purchase_category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
         

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save($fileName);
        $mail_message = "Hello , <br><br> Order <strong>#DATA/".$booking_info['booking_id']."</strong> is locked for <strong>".$booking_info['party_name']." - ".$booking_info['city_name']."</strong> <br><br> Please  login with given link and check the order. <a href='".base_url()."booking'>Login</a> <br><br> You can check order details in attached file";
        return $fileName.'______'.$mail_message;
        //redirect(base_url().$fileName);  
        //echo "string"; die;
    }
    public function downloadreport($bargain_id)
    {
        $this->load->library('excel');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Add SKU"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        
        $condition = array();
        $condition = array('booking_id' => $bargain_id);
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById(base64_decode($bargain_id));
        $booking_info = $data['booking_info'];
        //echo "<pre>"; print_r($data['booking_info']); die;
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['brand_categories'] = $this->purchase_category_model->GetCategory($condition);
        //echo "<pre>"; print_r($data['categories']); die;
        $condition = array('booking_skus.booking_id' => $booking_info['id']);
        $skus = $this->purchase_model->GetSkuinfo($condition);  
 
        $data['products'] = $this->purchase_category_model->GetProductsbycategpry_id($data['booking_info']['category_id']);


        //echo "<pre>"; print_r($skus); die;
        $data['brokers'] = $this->purchase_broker_model->GetBrokers(); 
        $data['brands'] = $this->purchase_brand_model->GetAllBrand();
        $data['users'] = $this->purchase_vendor_model->GetUsers();

        $data['categories'] = $this->purchase_category_model->GetCategories();

        $data['distinct_categories'] = $this->purchase_category_model->GetCategories1();
        //print_r($data['distinct_categories']); die;
        //echo "<pre>"; print_r($data['skus']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['bargain_id'] = $bargain_id;
        $data['category_name'] = $data['booking_info']['category_name']; 

        $insurance = ($booking_info['insurance']>0) ?  'Yes' : 'No'; 
        $is_for = ($booking_info['is_for']) ?  'Yes' : 'No'; 
        $remark = $booking_info['remark']; 
         

        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );

        $BorderstyleArray = array(
             'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  ),
              )
        );


        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            )
        );
        $styleArray2 = array(
            'font'  => array(
                'color' => array('rgb' => '00ffa5'),
                'size'  => 15,
            )
        );
        $styleArraythick = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
            )
          )
        );
        
        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ); 
        $ms = 'M/S '.$booking_info['party_name'].' - '.$booking_info['city_name'];
        $objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
        $objPHPExcel->getActiveSheet()->setCellValue('B2',$ms);

        $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray3);

        // set Header
        $i = 1;
        $row = 11;
        $col= 'B';
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Packing');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity(Tins/Cartons)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weignt (MT)');$col++;

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':E'.$row)->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $row++;
        $item_total = 0;
        if($skus)
        { 
            $sr_no = 1;
            foreach ($skus as $key => $value) {
                $col = 'B';
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sr_no);
                $col++;
                $packing = ($value['packing_items_qty']==1 || $value['packing_items_qty']=='' || is_null($value['packing_items_qty'])) ? '' : '*'.$value['packing_items_qty'];
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['name'].$packing);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);
                $col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
                $row++;
                $sr_no++;
                $item_total = $item_total+$value['quantity'];
            }
        }
        $col = 'C';
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$row,'Total');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$item_total);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($BorderstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row.':E'.$row)->applyFromArray($styleArray1);

        $row++;

        $order_date = date('d-m-Y', strtotime($booking_info['created_at']));
        $objPHPExcel->getActiveSheet()->setCellValue('C4','Orderd Date : '.$order_date);
        $objPHPExcel->getActiveSheet()->setCellValue('D4','Broker : '.$booking_info['broker_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E4','Sales Executive : '.$booking_info['sales_executive_name']);

        $objPHPExcel->getActiveSheet()->setCellValue('C5','Bargain Number : DATA/'.$booking_info['booking_id']);
        $dispatch_date = date('d-m-Y', strtotime($booking_info['shipment_date']));
        $objPHPExcel->getActiveSheet()->setCellValue('D5','Dispatch Date : '.$dispatch_date);
        $objPHPExcel->getActiveSheet()->setCellValue('E5','Production Unit : '.$booking_info['production_unit']);


        $objPHPExcel->getActiveSheet()->setCellValue('C6','Brand: '.$booking_info['brand_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('D6','Product : '.$booking_info['category_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('E6','Rate : '.$booking_info['rate']);


        $objPHPExcel->getActiveSheet()->setCellValue('C7','Orderd Total Weight : '.$booking_info['total_weight']);
        $objPHPExcel->getActiveSheet()->setCellValue('D7','SKU Total Weight : '.$booking_info['total_weight_input']);
        $objPHPExcel->getActiveSheet()->setCellValue('E7','Total Items : '.$item_total);



        $objPHPExcel->getActiveSheet()->setCellValue('C8','Insurace Included in price  : '.$insurance);
        $objPHPExcel->getActiveSheet()->setCellValue('D8','Price ex-factory : '.$is_for); 

        $objPHPExcel->getActiveSheet()->mergeCells('C9:E9');
        $objPHPExcel->getActiveSheet()->setCellValue('C9','Remark  : '.$remark);
        

        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($styleArray1);
        $objPHPExcel->getActiveSheet()->getStyle('C4'.':E9')->applyFromArray($BorderstyleArray);


        $objPHPExcel->getActiveSheet()->getStyle('C9:E9')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $fileName = $booking_info['party_name'].'-'.$booking_info['booking_id'].'.xls'; 
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save('php://output');
        //redirect(base_url().$fileName);  
        //echo "string"; die;
    }

    public function add(){ 
        $data['title'] = "New Booking";

        if(!empty($_POST))
        {
            //echo "<pre>"; print_r($_POST); die; 
            $party = $this->input->post('party'); 
            $brand = $this->input->post('brand'); 
            $category = $this->input->post('category'); 
            $product = $this->input->post('product'); 
            $quantity = $this->input->post('quantity'); 
            $rate = $this->input->post('rate');   
            $booking_date = $this->input->post('booking_date');   

            $this->form_validation->set_rules('party', 'Party Name','required');
            $this->form_validation->set_rules('brand', 'Brand','required');
            $this->form_validation->set_rules('category', 'Category','required');
            $this->form_validation->set_rules('product', 'Product','required');
            $this->form_validation->set_rules('quantity', 'Quantity','required');


            if ($this->form_validation->run() == false) {
            }
            else { 
                $product_info = $this->purchase_category_model->Productinfobyid($product);
                $loose_rate = $product_info['loose_rate'];
                $weight = $product_info['weight'];
                //$for_rate = $product_info['for_rate'];
                $vendor_condition = array('id' =>$party);
                $vendor_info = $this->purchase_vendor_model->GetUserbyId($vendor_condition);
                //echo "<pre>"; print_r($vendor_info); die;
                $for_rate = $vendor_info['for_rate'];

                $total_loose_rate = $loose_rate*$quantity;
                $total_weight = $weight*$quantity;
                $total_price = $rate*$quantity;
                $total_for_price = 0;
                if($is_for==0)
                    $total_for_price = $for_rate*$quantity;

                $insurance_amount = (($total_price*$insurance)/100)+$total_price;

                
                $insertdata = array('party_id' =>$party,'brand_id' =>$brand,'category_id' =>$category,'product_id' =>$product,'quantity' =>$quantity,'rate' =>$rate,'loose_rate' =>$loose_rate,'total_loose_rate' =>$total_loose_rate,'total_weight' =>$total_weight,'total_price' =>$total_price,'for_total' => $total_for_price);
                if($booking_date!='')
                     $insertdata['created_at'] = $booking_date.': 00'; 
                $result = $this->purchase_model->AddBooking($insertdata);
                if($result)
                    $this->session->set_flashdata('suc_msg','Booked successfully.');  
                else
                    $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                redirect('booking');
            }
        } 


        $data['brands'] = $this->purchase_brand_model->GetAllBrand();
        $data['users'] = $this->purchase_vendor_model->GetUsers();
        //$data['categories'] = $this->purchase_category_model->GetCategories();
        $this->load->view('booking_add',$data);
    }

    public function edit30052023(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $booking_id = base64_decode($this->uri->segment(3));
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Bulk Order Booking"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
         
        
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById1($booking_id);

        //echo "<pre>"; print_r($data['booking_info']); die;

        $condition = array('is_lock' => 1);
        if($role==1) //maker
        {
            $condition = array('admin_id' => $userid);
        }
        elseif ($role==2) { //checker
            $condition = array('admin.team_lead_id' => $userid);
        } 
        //echo "<pre>"; print_r($condition); die; 
        $data['brokers'] = $this->purchase_broker_model->GetBrokers(); 

        $data['brands'] = $this->purchase_brand_model->GetAllBrand();

        $data['users'] = $this->purchase_vendor_model->GetUsers();
        $data['makers'] = $this->admin_model->GetAllMakers();
        $brand_id = $data['booking_info']['brand_id'];
        $condition = array('brand_id' => $brand_id);
        $data['categories'] = $this->purchase_category_model->GetCategory($condition);
        $data['distinct_categories'] = $this->purchase_category_model->GetCategories1();
        //echo "<pre>"; print_r($data['categories']); die;
        //echo "<pre>"; print_r($data['users']); die;


        

        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;

        $this->load->view('booking_edit',$data);

    }

    public function edit(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $booking_id = base64_decode($this->uri->segment(4));
        $data['booking_info'] = $this->purchase_model->GetBookingInfoById1($booking_id);
        //echo "<pre>"; print_r($data['booking_info']); die;
        $product_id = $data['booking_info']['product_id'];
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Purchase Order"; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $allow_rate_booking = $this->session->userdata('admin')['allow_rate_booking'];        
        $data['brokers'] = $this->purchase_broker_model->GetBrokers();  
        $states_ids = $this->session->userdata('admin')['state_id']; 
        $data['users'] = $this->purchase_vendor_model->GetUsersByState($states_ids);  
        $condition = array('pur_category.status' => 1)      ;
        $data['categories'] = $this->purchase_category_model->GetCategories($condition); 

        $condition = array('pur_products.category_id' => $data['booking_info']['category_id'],'pur_products.status' => 1);
        $data['products'] = $this->purchase_category_model->GetProductsbycategpry_id($condition);
        $condition_rouduct = array('pur_products.id'=> $product_id);
        $data['attributes'] = $this->purchase_category_model->Getallattributesbyproduct_id($condition_rouduct);
        //echo "<pre>"; print_r($data['attributes']); die;
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['allow_rate_booking'] = $allow_rate_booking;    

        $limit = 20;
        if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
            $limit = $conditions_data['limit'];
        } 
        $config = array();
        $config["base_url"] = base_url() . "purchase/purchase/index/";
        $condition = array();
        $total_rows =  $this->purchase_model->CountBookingList($condition);
        $config["total_rows"] = $total_rows;
        // Number of items you intend to show per page.
        $config["per_page"] = $limit;
        // Use pagination number for anchor URL.
        $config['use_page_numbers'] = TRUE;
        //Set that how many number of pages you want to view.
        $config['num_links'] = 2;
        /*$config['uri_segment'] = 4; 
        $config["per_page"] = $limit;
        $config['use_page_numbers'] = TRUE; */
        $this->pagination->initialize($config);
        if ($this->uri->segment(4)) {
            $page = ($this->uri->segment(4));
        } else {
            $page = 1;
        }
        $data["links"] = $this->pagination->create_links();
        //echo "<pre>"; print_r($data["links"]); die;
        $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
        $data["total_page_count"] = ceil($config["total_rows"] / $limit);
        $page_no = ceil($config["total_rows"] / $limit);
        $data['total_page_no'] = $page_no;
        $data['current_page_no'] = $page;
        $data['limit'] = $limit;
        $data['bookings'] = $this->purchase_model->GetBookingList($condition,$limit,$page); 

        $this->load->view('purchase/purchase/edit',$data);
    }
    
    public function delete(){
        $data['title'] = "Order Edit";
        $booking_id = base64_decode($this->uri->segment(3));
        if($booking_id)
        {
            $condition = array('id' =>$booking_id);
            $result = $this->purchase_model->DeleteBooking($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Booking deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('booking');  
    }

    public function report_28_10_2022(){   
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';
        if(!empty($_POST) || isset($_SESSION['search__report_data']))
        {
            //echo "<pre>"; print_r($_POST); die;
            //$this->session->set_userdata('search__report_data', $_POST); 
            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search__report_data'] = $_POST;
            else
                $_POST = $_SESSION['search__report_data'];
            $party_id = $_POST['party'];
            $brand_id = $_POST['brand'];
            $category_id = $_POST['category']; 
            $booking_date_from = $_POST['booking_date_from'];
            $booking_date_to = $_POST['booking_date_to'];
            $booking_status = (isset($_POST['status'])) ? $_POST['status'] : '';
            $role = $this->session->userdata('admin')['role'];
            $booked_by = '';
            $condition = array();
            if($role==1) //maker
            {
                $booked_by = $this->session->userdata('admin')['id'];
            }
            $this->load->library("pagination");

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 

            $config = array();
            $config["base_url"] = base_url() . "booking/report/";
            $total_rows =  $this->purchase_model->CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status);
            $config["total_rows"] = $total_rows;
            // Number of items you intend to show per page.
            $config["per_page"] = 20;
            // Use pagination number for anchor URL.
            $config['use_page_numbers'] = TRUE;
            //Set that how many number of pages you want to view.
            $config['num_links'] = 2;
            /*$config['uri_segment'] = 4; 
            $config["per_page"] = $limit;
            $config['use_page_numbers'] = TRUE; */
            $this->pagination->initialize($config);
            if ($this->uri->segment(3)) {
                $page = ($this->uri->segment(3));
            } else {
                $page = 1;
            }
            $data["links"] = $this->pagination->create_links();
            $data["links"] = str_replace('?p_id=', '/', $data["links"]); 
            $data["total_page_count"] = ceil($config["total_rows"] / $limit);
            $page_no = ceil($config["total_rows"] / $limit);
            $data['total_page_no'] = $page_no;
            $data['current_page_no'] = $page;
            $data['limit'] = $limit;
            //echo "<pre>"; print_r($data);  print_r($config); die;

            $data['bookings'] = $this->purchase_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page);
            //echo "<pre>"; print_r($data); die;
        } 
        
        //echo "<pre>"; print_r($data['bookings']); die;
        $data['categories'] = $this->purchase_category_model->GetCategories();
        $data['brands'] = $this->purchase_brand_model->GetAllBrand();
        $data['users'] = $this->purchase_vendor_model->GetUsers();
        $data['booking_date_from'] = $booking_date_from;
        $data['booking_date_to'] = $booking_date_to;

        $data['party_id'] = $party_id;
        $data['booking_status'] = $booking_status;
        $data['brand_id'] = $brand_id;
        $data['category_id'] = $category_id;
        $data['product_id'] = $product_id; 
        $data['distinct_categories'] = $this->purchase_category_model->GetCategories1(); 
        $data['logged_in_id'] = $admin_id;
        $data['logged_role'] = $role;
        $this->load->view('booking_report',$data);

    }

    


    public function report_print(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';

        $party_id = $_REQUEST['party'];
        $brand_id = $_REQUEST['brand'];
        $category_id = $_REQUEST['product']; 
        $booking_date_from = date('Y-m-d',strtotime($_REQUEST['from']));
        $booking_date_to = date('Y-m-d',strtotime($_REQUEST['to'])); 
        $booking_status = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : '';
        $role = $this->session->userdata('admin')['role'];

        $employee = $_REQUEST['employee']; 
        $unit = $_REQUEST['production_unit']; 

        $booked_by = '';
        $condition = array();
        if($role==1) //maker
        {
            $booked_by = $this->session->userdata('admin')['id'];
        }
        $limit = 2000000;
        $page = 1;
        $bookings = $this->purchase_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);
        $html_print = '';

            $html_print .= '
            <table  style="border-left:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">';
            if($bookings)
            {
                $sno = 1;
                foreach ($bookings as $key => $value) {
                    $bg_color = "#fff";
                    if($sno%2==0)
                        $bg_color = "#ece9e9";
                    $b_status = '';
                    if($value['status']==3)  
                    {
                        $b_status = "Rejected";
                        if($value['is_lock'])
                             $b_status =  $b_status.' (Locked)';
                    }
                    elseif($value['status']==2)
                    {
                        $b_status = "Approved"; 
                        if($value['is_lock'])
                             $b_status =  $b_status.' (Locked)';
                    }
                    else 
                    {
                        $b_status = "Approval Pending"; 
                        if($value['is_lock'])
                             $b_status =  $b_status.' (Locked)';
                    }
                    $ex = 'For';
                    if($value['is_for'])
                        $ex = 'Ex';
                        $html_print .= '<tr style="background-color:'.$bg_color.';">
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:60px;">'.$sno.'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:100px;">DATA/'.$value['booking_id'].'</td>


                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px;">'.$value['party_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px">'.$value['city_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['brand_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['category_name'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['quantity'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['rate'].' ('.$ex.')</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['weight'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['production_unit'].'</td>

                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.date("d-m-Y", strtotime($value['shipment_date'])).'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'.$value['remark'].'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px">'. date("d-m-Y", strtotime($value['created_at'])).'</td>
                                <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>'.$b_status.'</strong></td>
                            </tr>'; 
                            $sno++;
                } 

            }

            $html_print .= '</table>';




        include(FCPATH."mpdf1/mpdf.php");


        $mpdf=new mPDF('utf-8','A4-L','0','0','10','10','20','0','0','0'); 
        $header = '<table  style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr style="background-color:#c8c8c8;">
                                        <td colspan="14" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; width:60px; text-align:center"><h3>Bargain Report</h3></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:60px;"><strong>S.No</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;  width:100px;"><strong>Bargain No</strong></td>


                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Party Name</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:100px"><strong>Place</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Brand</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Product</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Quantity (Tins)</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Rate (15Ltr Tin)</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Weight (MT)</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Pr. Unit</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Delivery Date</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000; width:80px"><strong>Remark</strong></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Date</strong></td>
                                        <td style="text-align:center; border-right:1px solid #000; border-bottom:1px solid #000;width:80px"><strong>Status</strong></td>
                                    </tr>
                                </table>';
            $footer = '{PAGENO} of {nbpg}';
        //echo $footer; die;
        $mpdf->SetHTMLHeader($header); 
        $mpdf->SetHTMLFooter($footer);   
        $mpdf->WriteHTML($html_print);
        $f_name = 'Report'.'pdf';
        $invpice_name = FCPATH.'/'.$f_name; 
        $mpdf->Output($f_name,'I'); 
    }

    public function report_print_excel(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';

        $party_id = $_REQUEST['party'];
        $brand_id = $_REQUEST['brand'];
        $category_id = $_REQUEST['product']; 
        $booking_date_from = date('Y-m-d',strtotime($_REQUEST['from']));
        $booking_date_to = date('Y-m-d',strtotime($_REQUEST['to'])); 
        $booking_status = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : '';
        $role = $this->session->userdata('admin')['role'];

        $employee = $_REQUEST['employee']; 
        $unit = $_REQUEST['production_unit']; 
        $booked_by = '';
        $condition = array();
        if($role==1) //maker
        {
            $booked_by = $this->session->userdata('admin')['id'];
        }
        $limit = 2000000;
        $page = 1;
        $bookings = $this->purchase_model->GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$limit,$page,$employee,$unit);

        $styleArray1 = array(
            'font'  => array(
                'bold'  => true,
            ),
        );
         
        $styleArray = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
            ),
            // 'borders' => array(
              //    'allborders' => array(
                //      'style' => PHPExcel_Style_Border::BORDER_THIN
                  //),
              //)
        );
         

        $styleArray3 = array(
            'font'  => array(
                'color' => array('rgb' => 'FF0000'),
                'size'  => 15,
                'bold'  => true,
            )
        );

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        $col='A';
        $row = 2;
        $objPHPExcel->getActiveSheet()->getStyle('A:Z')->getAlignment()->applyFromArray(
            array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );  

        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');
        $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(10);$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Bargain No');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Party Name');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Place');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Brand');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Product');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Quantity (Tins)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Rate (15Ltr Tin)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Weight (MT)');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Production Unit');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Delivery Date');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Remark');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Date');$col++;
        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Status');

        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$col.'1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1','Bargain Report');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray3);
        $row++;
         

        if($bookings)
        {
            
            $sno = 1;
            foreach ($bookings as $key => $value) {
                $col='A';
                $bg_color = "#fff";
                if($sno%2==0)
                    $bg_color = "#ece9e9";
                $b_status = '';
                if($value['status']==3)  
                {
                    $b_status = "Rejected";
                    if($value['is_lock'])
                         $b_status =  $b_status.' (Locked)';
                }
                elseif($value['status']==2)
                {
                    $b_status = "Approved"; 
                    if($value['is_lock'])
                         $b_status =  $b_status.' (Locked)';
                }
                else 
                {
                    $b_status = "Approval Pending"; 
                    if($value['is_lock'])
                         $b_status =  $b_status.' (Locked)';
                }
                $ex = 'For';
                if($value['is_for'])
                    $ex = 'Ex';

                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$sno);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'DATA/'.$value['booking_id']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['party_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['city_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['brand_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['category_name']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['quantity']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['rate'].' ('.$ex.')');$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['weight']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['production_unit']);$col++;
                
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,date("d-m-Y", strtotime($value['shipment_date'])));$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['remark']);$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,date("d-m-Y", strtotime($value['created_at'])));$col++;
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$b_status);$col++; 
                $row++;
                $sno++;
            }
        }

        $fileName = 'Report.xls'; 
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
        $objWriter->save('php://output'); 
    }


    public function summary_print(){    
        //unset($_SESSION['search__report_data']);
        //echo "<pre>"; print_r($_POST); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Booking Report"; 
        $data['bookings'] = array();
        $booking_date_from = '';
        $booking_date_to = '';

        $party_id = '';
        $brand_id = '';
        $category_id = '';
        $product_id = '';
        $booking_status = '';
        $data['search_summary'] = 0;
        //echo "<pre>"; print_r($_SESSION['search__report_data']); die;
        $data["links"] = '';
        $booked_by = ''; 
        if($role==1) //maker
        {
            $booked_by = $this->session->userdata('admin')['id'];
        }
        $party_id = $_GET['party'];
        $brand_id = $_GET['brand'];
        $category_id = $_GET['product']; 
        $booking_date_from = date('Y-m-d',strtotime($_GET['from']));
        $booking_date_to = date('Y-m-d',strtotime($_GET['to']));   
        $booking_status = $_GET['status']; 
        $employee = $_GET['employee']; 
        $unit = $_GET['production_unit']; 
        $group_by  = array('status');
        $sum_report = $this->purchase_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,'',$unit);

        $tot_sum_report = $this->purchase_model->GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,1,$unit);

        $locked = $this->purchase_model->GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,'',array(),$employee,$unit);  

        //echo "<pre>"; print_r($data['locked']); die; 
        if($_GET['type']=='place')
        {
            $group_by  = array('place','brand_id','category_id');
            $bookings_brand_product_place = $this->purchase_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);

            $html_print = '
            <table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($tot_sum_report_value['weight'],2).'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                      $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($locked_weight,2).'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($sum_report_value['weight'],2).'</td>
                                        </tr>'; 
                                    } }  }
                                $html_print .= '</table>
            <table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                        <thead>
                            <tr><th colspan="8" style="text-align:center;border-bottom:1px solid #000;">Order summary based on state from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">S.No</th>    
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">State</th>  
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Brand</th>   
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Product</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Qty <br> (Tins/Cartons)</th>
                                <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align: middle;">Weight <br>  (MT)</th>
                                <th style="text-align:left; border-bottom:1px solid #000;"  colspan="2">Avg Rate / Loose(kg)</th>
                            </tr>
                            <tr>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                    $avg_rate_other = '';
                                    $avg_rate_aasam1 = '';
                                    if($bookings_brand_product_place1['avg_rate_other'])
                                    {
                                        $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                        $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                        $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                        $loose_rate = round($loose_rate,2);
                                        $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                    }

                                    if($bookings_brand_product_place1['avg_rate_aasam'])
                                    {
                                        $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);
                                        $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                        $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;

                                        

                                        $loose_rate_aasam = round($loose_rate_aasam,2);
                                        $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                    }



                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['state_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 
                                    
                                    <td style="text-align:left; border-bottom:1px solid #000;border-right:1px solid #000;">'.$avg_rate_other.'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td> 
                                </tr>';
                            $sn++; }
                            $html_print .= '<tr style="color:red;"><td colspan="4"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td></td></tr>';
                            } else { 
                                $html_print .= '<tr><td colspan="7">No Record Found</td></tr>';
                            }
                        $html_print .= '</tbody>
                    </table>';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }
        elseif ($_GET['type']=='brand') 
        {
            $group_by  = array('brand_id','category_id');        
            $bookings_brand_product_place = $this->purchase_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
            $html_print = '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['weight'].'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                        $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_weight.'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['weight'].'</td>
                                        </tr>'; 
                                        }   
                                    } } 
                                $html_print .= '</table><table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                        <thead>
                            <tr><th colspan="6" style="text-align:center;border-bottom:1px solid #000;">Order summary based on brand and product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                            
                            <tr>
                                <th style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">S.No</th>   
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Brand</th>   
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Product</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Qty (Tins/Cartons)</th>
                                <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Weight (MT)</th>
                                <th style="text-align:left; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                            </tr>
                        </thead>
                        <tbody class="">';
                            $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                    $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate'],2);
                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                    $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                    $loose_rate = round($loose_rate,2);

                                $html_print .= '<tr>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                     
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                    <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['weight'].'</td> 
                                    <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate.' / '.$loose_rate.'</td> 
                                </tr>';
                            $sn++; }
                            $html_print .= '<tr style="color:red;"><td colspan="3"></td><td>Total</td><td >'.$weight_total_summary.'</td><td></td></tr>';
                            } else { 
                                $html_print .= '<tr><td colspan="6">No Record Found</td></tr>';
                            }
                        $html_print .= '</tbody>
                    </table>';
                    include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }
        elseif ($_GET['type']=='product') 
        {
            $group_by  = array('category_name');      
            $bookings_brand_product_place = $this->purchase_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
            $html_print = '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['weight'].'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                        $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_weight.'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['weight'].'</td>
                                        </tr>'; 
                                        }   
                                    } } 
                                $html_print .= '</table><table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <thead>
                                        <tr><th colspan="6" style="text-align:center;border-bottom:1px solid #000;">Order summary based on product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">S.No</th>    
                                            <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Product</th>
                                            <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Qty (Tins/Cartons)</th>
                                            <th style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000;">Weight (MT)</th>
                                            <th style="text-align:left; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];

                                                $avg_rate =  round($bookings_brand_product_place1['avg_rate'],2);
                                                $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                                $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                                $loose_rate = round($loose_rate,2);

                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['weight'].'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate.' / '.$loose_rate.'</td> 
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="2"></td><td>Total</td><td >'.$weight_total_summary.'</td><td></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="6">No Record Found</td></tr>';
                                        }
                                    $html_print .= '</tbody>
                                </table>';
                    include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }
        else
        {
            
            $html_print = '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Number of bargains</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total Weight (MT)</td>
                                    </tr>';
                                    if($tot_sum_report)
                                    {  
                                    foreach ($tot_sum_report as $key => $tot_sum_report_value) {
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Total</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$tot_sum_report_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($tot_sum_report_value['weight'],2).'</td>
                                    </tr>';
                                    } }
                                    if($locked)
                                    {  
                                    foreach ($locked as $key => $locked_value) {
                                        $locked_weight = ($locked_value['weight']) ? $locked_value['weight'] : 0;
                                    $html_print .= '<tr>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">Locked</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$locked_value['bargain_count'].'</td>
                                        <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($locked_weight,2).'</td>
                                    </tr>';
                                    } }
                                    if($sum_report)
                                    {  
                                    foreach ($sum_report as $key => $sum_report_value) {
                                        //$sumtype = ($sum_report_value[status]==0) ? "Pending" : "Approved";
                                        if($sum_report_value['status']!=3)
                                        {
                                            if($sum_report_value['status']==0)
                                                $sumtype =  "Pending";
                                            elseif ($sum_report_value['status']==2) {
                                                $sumtype =  "Approved";
                                            }
                                            elseif ($sum_report_value['status']==3) {
                                                $sumtype =  "Rejected";
                                            }
                                            
                                        $html_print .= '<tr>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sumtype.'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sum_report_value['bargain_count'].'</td>
                                            <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($sum_report_value['weight'],2).'</td>
                                        </tr>'; 
                                        }   
                                    } } 
                                $html_print .= '</table>';
                        $group_by  = array('category_name');      
                        $bookings_brand_product_place = $this->purchase_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);
                        $html_print .= '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%;margin-top:20px;">
                                    <thead>
                                        <tr><th colspan="6" style="text-align:center;border-bottom:1px solid #000;">Order summary based on product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">S.No</th>    
                                            <th rowspan="2"  style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Product</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Qty <br> (Tins/Cartons)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle; ">Weight <br> (MT)</th>
                                            <th colspan="2"  style="text-align:center; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                        <tr>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                                $avg_rate_other = '';
                                                $avg_rate_aasam1 = '';
                                                
                                                if($bookings_brand_product_place1['avg_rate_other'])
                                                {
                                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                                    $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                                    $loose_rate = round($loose_rate,2);
                                                    $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                                }
                                                if($bookings_brand_product_place1['avg_rate_aasam'])
                                                {
                                                    $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);


                                                    $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                                    $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15; 
                                                    $loose_rate_aasam = round($loose_rate_aasam,2);
                                                    $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                                }

                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$avg_rate_other.'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td> 
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="2"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td colspan="2"></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="7">No Record Found</td></tr>';
                                        }
                                    $html_print .= '</tbody>
                                </table>';


                        $group_by  = array('brand_id','category_id');        
                        $bookings_brand_product_place = $this->purchase_model->GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by,$booking_status,$group_by,$employee,$unit);

                        $html_print .= '<table style="border:1px solid #000; margin:0;padding:0; border-collapse:collapse; width:100%; margin-top:20px; ">
                                    <thead>
                                        <tr><th colspan="7" style="text-align:center;border-bottom:1px solid #000;">Order summary based on brand and product from '.date('d-m-Y', strtotime($booking_date_from)) .' to '.date('d-m-Y', strtotime($booking_date_to)).'</th></tr> 
                                        
                                        <tr>
                                            <th rowspan="2" style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">S.No</th>   
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Brand</th>   
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Product</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Qty <br>(Tins/Cartons)</th>
                                            <th rowspan="2" style="text-align:left;  border-right:1px solid #000; border-bottom:1px solid #000; vertical-align:middle;">Weight  <br> (MT)</th>
                                            <th colspan="2" style="text-align:center; border-bottom:1px solid #000;">Avg Rate / Loose(kg)</th>
                                        </tr>
                                        <tr>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">Others</th>
                                            <th style="border-right:1px solid #000; border-bottom:1px solid #000;">North East</th>
                                        </tr>
                                    </thead>
                                    <tbody class="">';
                                        $weight_total_summary = 0; $sn = 1; if($bookings_brand_product_place) {
                                            foreach ($bookings_brand_product_place as $key => $bookings_brand_product_place1) { 
                                                $weight_total_summary = $weight_total_summary+$bookings_brand_product_place1['weight'];
                                                $avg_rate_other = '';
                                                $avg_rate_aasam1 = '';
                                                if($bookings_brand_product_place1['avg_rate_other'])
                                                {
                                                    $avg_rate =  round($bookings_brand_product_place1['avg_rate_other'],2);
                                                    $loose_kg = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 13.455 : 13.650; 
                                                    $loose_rate = ($avg_rate-$bookings_brand_product_place1['tin_rate'])/$loose_kg;
                                                    $loose_rate = round($loose_rate,2);
                                                    $avg_rate_other = $avg_rate.' / '.$loose_rate;
                                                }

                                                if($bookings_brand_product_place1['avg_rate_aasam'])
                                                {
                                                    $avg_rate_aasam =  round($bookings_brand_product_place1['avg_rate_aasam'],2);
                                                  

                                                    $loose_kg_aasam = (strtolower($bookings_brand_product_place1['category_name'])=='vanaspati') ? 0.897 : .91;
                                                    $loose_rate_aasam = ((($avg_rate_aasam-$bookings_brand_product_place1['tin_rate'])/$loose_kg_aasam)-$bookings_brand_product_place1['freight_rate'])/15;


                                                    $loose_rate_aasam = round($loose_rate_aasam,2);
                                                    $avg_rate_aasam1 = $avg_rate_aasam.' / '.$loose_rate_aasam;
                                                }
                                            $html_print .= '<tr>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$sn.'</td>  
                                                 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['brand_name'].'</td>
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['category_name'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$bookings_brand_product_place1['quantity'].'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.round($bookings_brand_product_place1['weight'],2).'</td> 
                                                <td style="text-align:left; border-right:1px solid #000; border-bottom:1px solid #000;">'.$avg_rate_other.'</td> 
                                                <td style="text-align:left; border-bottom:1px solid #000;">'.$avg_rate_aasam1.'</td>  
                                            </tr>';
                                        $sn++; }
                                        $html_print .= '<tr style="color:red;"><td colspan="3"></td><td>Total</td><td >'.round($weight_total_summary,2).'</td><td></td></tr>';
                                        } else { 
                                            $html_print .= '<tr><td colspan="6">No Record Found</td></tr>';
                                        }
                                    $html_print .= '</tbody>
                                </table>';
            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4','0','0','10','10','38','0','0','0'); 
                   // $mpdf=new mPDF('utf-8','A4','0','0','10','10'); 
            $header = '<table colspan style="width:750px;margin:0 auto;">
                        <tr style="display: flex;justify-content: space-between;align-items: center;">
                            <td style="padding: 20px 0px 20px 20px; ">
                                <img src="'.base_url('assets/images/').'/datagroup-logo.png" style="">
                            </td>
                            <td style="padding: 20px 20px 20px 0px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2">
                            <img src="'.base_url('assets/images/').'/header-line.png" style="width: 100%;">
                            </td>
                        </tr></table>';
            $footer = '{PAGENO} out of {nbpg}';
                        //echo $footer; die;
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);   
            $mpdf->WriteHTML($html_print);
            $f_name = 'Summary '.$_GET['from'].' to '.$_GET['to'].'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'I');
            //echo $html_print; die;
        }

    }

    


    public function details(){  
       
        $condition  = array('id' => base64_decode($_POST['booking_id']));
        $approve_reject_time= $this->purchase_model->Bookingdetils($condition);
        echo $approve_reject_time = date("d-m-Y H:i:s", strtotime($approve_reject_time));
    } 
    
    public function status_update(){    
        $data['title'] = "";
        $status =  $this->uri->segment(3);
        $category_id =  base64_decode($this->uri->segment(4)); 
        $update_data = array('is_enable' => $status);
        $condition  = array('id' => $category_id);
        $result= $this->purchase_category_model->UpdateCategory($update_data,$condition);
        if($result)
            $this->session->set_flashdata('suc_msg','Category updated successfully.');
        else
            $this->session->set_flashdata('err_msg','Something went wrong.');
        redirect('category');
    } 
 

    public function edit_category(){   
        $data['title'] = "Update Product";
        $category_id =  base64_decode($this->uri->segment(3));
        $data['product'] = $this->purchase_category_model->GetCategoryByCategoryId($category_id);
        $old_hsn  = $data['product']['hsn'];

        if($category_id)
        {
            $condition = array('id' => $category_id);
            if(!empty($_POST))
            { 
                $name = $this->input->post('name'); 
                $brand = $this->input->post('brand'); 
                $is_enable = $this->input->post('is_enable'); 
                $sort_order = $this->input->post('sort_order'); 
                $hsn = $this->input->post('hsn');   


                $this->form_validation->set_rules('name', 'Product Name','required');
                $this->form_validation->set_rules('brand', 'Brand','required');
                $this->form_validation->set_rules('is_enable', 'Enable','required');
                $this->form_validation->set_rules('sort_order', 'Sort Order','required');
                $this->form_validation->set_rules('hsn', 'HSN','required');


                if ($this->form_validation->run() == false) {
                }
                else { 
                    $updatedata = array('category_name' =>$name,'brand_id' =>$brand,'is_enable' =>$is_enable,'sort_order' =>$sort_order,'hsn' =>$hsn);
                    $result = $this->purchase_category_model->UpdateCategory($updatedata,$condition);
                    if($result)
                        $this->session->set_flashdata('suc_msg','Category updated successfully.');  
                    else
                        $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
                    redirect('category');
                }
            } 
            
            //echo "<pre>"; print_r($data['product']); die;
            $this->load->model('purchase_brand_model'); 
            $data['brands'] = $this->purchase_brand_model->GetAllBrand(); 
        }
        else
        {
            redirect('category');
        }
        $this->load->view('category_edit',$data);
    }  

     
 
    

    public function delete_category(){
        $data['title'] = "Category EDelete";
        $category_id = base64_decode($this->uri->segment(3));
        if($category_id)
        {
            $condition = array('id' =>$category_id);
            $result = $this->purchase_category_model->DeleteCategory($condition); 
            if($result)
                $this->session->set_flashdata('suc_msg','Category deleted successfully.');  
            else
                $this->session->set_flashdata('err_msg','Something went wrong, please try again.');
        }
        redirect('category');  
    }


    public function getbargainweight()
    {
        $party_id = $_POST['party_id'];
        $condition  = array('party_id'=>$party_id,'is_mail'=>0,'is_lock'=>1,'status'=>2);
        $weight = $this->purchase_model->getbargainweight($condition); 
        $pending_condition  = array('party_id'=>$party_id,'is_mail'=>0,'is_lock'=>0,'status <> '=>3,);
        $bargains = $this->purchase_model->getpenidngbargain($pending_condition); 

        //echo "<pre>"; print_r($bargains); die;
        echo $weight['weight'].'__'.$bargains;
        //echo "<pre>"; print_r($weight); die;
    }
}