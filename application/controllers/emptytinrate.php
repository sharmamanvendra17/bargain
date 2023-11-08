<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emptytinrate extends CI_Controller {

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
        $this->load->model('category_model');      
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    } 
    public function add_rate($brand_id,$category_id){  
        ///echo "<pre>"; print_r($_POST); die;
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Empty Tin Rate";
        $this->load->model('vendor_model');  
        $category_condition = array('category.id' => base64_decode($category_id));
        $data['info'] = $this->category_model->GetCategorInfo($category_condition); 
        //echo "<pre>"; print_r($data['info']); die;
        $data['states'] = $this->vendor_model->GetStates(); 
        $data['products'] =array();// $this->category_model->GetProductsbycategpry_id(base64_decode($category_id));
        //echo "<pre>"; print_r($data['products']); die;
        $data['skus'] = array();
        //echo "<pre>"; print_r($data); die;
        $this->load->view('emptytinrate_add',$data);
    }

    public function getskus(){ 
        $brand_id = $_POST['brand_id'];
        $category_id = $_POST['category_id'];
        $state_id = $_POST['state_id'];
        $products = $this->category_model->GetProductsbycategpry_id($category_id);
        $condition = array('brand_id'=>$brand_id,'category_id'=>$category_id,'state_id'=>$state_id);
        $skusrate  = $this->category_model->Emtpytinrates($condition);
        
        $res = '';
        $insurance_rate = 0.00; 
        if($products) 
        {
            
            $i = 1;            
            foreach ($products as $key => $value) {
                
                $bs_rst = (strtolower(str_replace(' ','',$value['name']))=='15ltrtin') ? 1 : 0;
                $rate = 0;
                if(array_key_exists($value['id'],$skusrate))
                {
                    $rate = $skusrate[$value['id']]['rate'];
                    $insurance_rate = $skusrate[$value['id']]['insurance'];
                }
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
                $res .='<input  class="form-control rate" type="text"  name="rate[]" value="'.$rate.'"></div></div>';
                $res .= '</div>';
                $type = 0; //new
                if($rate>0)
                    $type = 1; //old
                $res .='<input type="hidden"  name="type[]" value="'.$type.'">';
                $i++;
            }
            $res .='<div class="col-md-12" style="padding:0;"><div class="form-group">
                                    <label for="name">Insurance (%)</label> 
                                    <input type="text" class="form-control" id="insurance" name="insurance" required="" value="'.$insurance_rate.'"> 
                                </div></div>';
        } 

           echo $res;  
    }
    public function save_rate(){  
        //echo "<pre>"; print_r($_POST); die;
        $inserdata = array();
        $added = 0;
        if(isset($_POST['product']) && count($_POST['product']))
        {   
            $products = $_POST['product'];
            $rates = $_POST['rate'];
            $types = $_POST['type'];
            $base_rates = $_POST['base_rates'];
            $insurance = $_POST['insurance'];

            if($_POST['state']==4)
                $states = array(4,25,24,23,22,33,30,3);
            elseif ($_POST['state']==5)  
                $states = array(5);
            else 
                $states = array(1,2,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,26,27,28,29,31,32,34,35,36,37); 

            foreach ($products as $key => $value) { 
                $delete_condition =  array(
                    'brand_id' => $_POST['brand_id'],
                    'category_id' => $_POST['category_id'], 
                    'state_id' => $_POST['state'],
                );
                $type = $types[$key];
                $created_at = date("Y-m-d H:i:s");
                if($type==0)
                    $created_at = date("2022-10-01 00:00:00");
                foreach ($states as $state_key => $state_value) { 
                    $inserdata[] = array(
                        'brand_id' => $_POST['brand_id'],
                        'category_id' => $_POST['category_id'],
                        'product_id' => $value,
                        'rate' => $rates[$key],
                        'base_rate' => $base_rates[$key],
                        'state_id' => $state_value,
                        'insurance' => $insurance,
                        'created_at' => $created_at
                    );
                    
                }
            }
            //$this->category_model->DeleteEmptyRates($delete_condition);
            $added =  $this->category_model->AddEmptyRates($inserdata);
        } 
        echo $added; die;
    }


    public function getrate(){  
        $product_id = $_POST['product_id'];
        $smallpack = $_POST['smallpack'];

        $tintype = $_POST['tintype'];
        $brand = $_POST['brand'];
        $category = $_POST['category'];
        $weight = $_POST['weight'];
        $weight_type = $_POST['weight_type'];
        $state_id = 29;
            //if($smallpack)
            //$state_id = 4;
        $condition = array(
            'product_id' => $product_id,
            'state_id' => $state_id,
            'tintype' => $tintype,
            'brand' => $brand,
            'category' => $category,
            'weight' => $weight,
            'weight_type' => $weight_type,
        );
        echo $this->category_model->Emtpytinratesku($condition);
    }
    /* end */
}