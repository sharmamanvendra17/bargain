<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Labreport extends CI_Controller {

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
    public function getreportattributes(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');  
        $product_id = $_POST['product_id'];
        $inventory_id = $_POST['inventory_id'];
        //$condition = array('pur_products.product_id' => $product_id,'pur_products.status' => 1);
        //$attributes = $this->category_model->Getattributesbyproduct_id($attributes);
        $condition_rouduct = array('pur_products.id'=> $product_id);
        $attributes = $this->purchase_category_model->Getallattributesbyproduct_id($condition_rouduct);

        $this->load->model(array('purchase/purchase_model')); 
        $condition =  array('pur_inventory.id' => $inventory_id);
        $purchase_info = $this->purchase_model->GetInventoryReportInfo($condition);

        
        $res = "";
        if($attributes)
        {
            foreach ($attributes as $key => $value) {
                if($value['alias']!='mandi_expenses')
                {
                    $col_name= 'lab_result_'.$value['alias'];
                    $attr_val =  ($purchase_info[$col_name]) ? $purchase_info[$col_name] : '';
                    $res .= '<div class="col-md-4">
                            <div class="form-group"> 
                                <label for="'.$value['name'].'">'.$value['name'].' '.$value['data_range'].'</label>
                                <input type="text" class="form-control custom_attributes_input" id="'.$value['alias'].'" name="'.$value['alias'].'"  value="'.$attr_val.'" placeholder="'.$value['name'].'" required>  
                                <span class="txt-danger v_'.$value['alias'].'"></span>
                            </div>
                        </div>';
                }
            }
        } 
        $res .= '<div class="col-md-4">
                    <label for="sales_executive">Color</label> 
                    <div class="form-group"> 
                        <input type="text" class="form-control" name="color" id="color" placeholder="Color" value='.$purchase_info['lab_result_color'].'>
                    </div>
                </div> 
                <div class="col-md-4">
                    <label for="sales_executive">Smell</label> 
                    <div class="form-group"> 
                        <input type="text" class="form-control" name="smell" id="smell" placeholder="Smell" value='.$purchase_info['lab_result_smell'].'>
                    </div>
                </div> 
                <div class="col-md-4">
                    <label for="sales_executive">Remark</label> 
                    <div class="form-group"> 
                        <textarea class="form-control" name="remark" id="remark" placeholder="Remark">'.$purchase_info['lab_result_remark'].'</textarea>
                    </div>
                </div> ';
        echo $res; die;
    } 

    public function getproductlist(){   
       // if (!$this->session->userdata('admin'))
          // redirect('/');  
        $category_id = $_POST['category_id'];
        $condition = array('pur_products.category_id' => $category_id,'pur_products.status' => 1);
        $products = $this->purchase_category_model->GetProductsbycategpry_id($condition);
        $res = "<option value=''>Select Product</option>";
        if($products)
        {
            foreach ($products as $key => $value) {
                $res .= '<option value="'.$value['id'].'">'.$value['product_name'].'</option>';
            }
        } 
        echo $res; die;
    } 
    public function index(){   
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $data['title'] = "Lab Report"; 
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
        if(!empty($_POST) || isset($_SESSION['search_lab_report_data']))
        //if(!empty($_POST))
        { 

            if(isset($_POST) && !empty($_POST)) 
                $_SESSION['search_lab_report_data'] = $_POST;
            else
                $_POST = $_SESSION['search_lab_report_data']; 

            $limit = 20;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 
            $product_id = $_POST['product']; 
            $config = array();
            $config["base_url"] = base_url() . "purchase/labreport/index/";
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

            if(isset($_POST['purchase_number']) && !empty($_POST['purchase_number']))
                $condition['purchase_order.purchase_id'] = $_POST['purchase_number']; 

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
         
        //echo "<pre>"; print_r($data['employees']); die;

        $this->load->view('purchase/lab/report',$data);
    }

    public function updatelabreport(){   
        $lab_result_id = trim($_POST['lab_result_id']);
        $code = trim($_POST['code']);
        $purchase_inventory_id = trim($_POST['purchase_inventory_id']);
        $erp_sr_no = trim($_POST['erp_sr_no']);
        $ffa = (isset($_POST['ffa']) && !empty(trim($_POST['ffa']))) ? trim($_POST['ffa']) : NULL;
        $pungency = (isset($_POST['pungency']) && !empty(trim($_POST['pungency']))) ? trim($_POST['pungency']) : NULL;
        $oil_percentage = (isset($_POST['oil_percentage']) && !empty(trim($_POST['oil_percentage']))) ? trim($_POST['oil_percentage']) : NULL;
        $moisture = (isset($_POST['moisture']) && !empty(trim($_POST['moisture']))) ? trim($_POST['moisture']) : NULL;
        $sand = (isset($_POST['sand']) && !empty(trim($_POST['sand']))) ? trim($_POST['sand']) : NULL;
        $color = (isset($_POST['color']) && !empty(trim($_POST['color']))) ? trim($_POST['color']) : NULL;
        $smell = (isset($_POST['smell']) && !empty(trim($_POST['smell']))) ? trim($_POST['smell']) : NULL;
        $remark = (isset($_POST['remark']) && !empty(trim($_POST['remark']))) ? trim($_POST['remark']) : NULL;
        $insert_data = array(
            'purchase_inventory_id' => $purchase_inventory_id,
            'erp_sr_no' => $erp_sr_no,
            'code' => $code,
            'ffa' => $ffa,
            'moisture' => $moisture,
            'oil_percentage' => $oil_percentage,
            'pungency' => $pungency,
            'sand' => $sand,
            'remark' => $remark,
            'color' => $color,
            'smell' => $smell,
        );
        //echo "<pre>"; print_r($insert_data); die;
        if($lab_result_id)
        {
            $condition = array('id' => $lab_result_id);
            echo $this->purchase_model->Updatelabreport($insert_data,$condition); 
        }
        else
        {
            echo $this->purchase_model->Addlabreport($insert_data); 
        }
    }
    
}