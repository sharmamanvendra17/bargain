<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dispatch_report extends CI_Controller {

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
        $this->load->model(array('pi_model','booking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));   
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();     
    } 

    public function index(){
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role; 
        $data['title'] = "Dispatched QTY Report"; 
        $data['logged_role'] = $logged_role;
        $types = array('Basket','Bottle','Jar','Matka','Pouch','Tin'); 
        $response = array();
        $response_detail = array();
        $date_range = date('m-Y');
        $state_id = '';
        $production_unit = '';
        if(isset($_POST) && !empty($_POST))
        { 
            $date_range = $_POST['month'].'-' .$_POST['year'];
            $state_id = $_POST['state'];
            $production_unit = $_POST['production_unit'];
        }

        foreach ($types as $key => $value) {
            $response[]=  $this->booking_model->GetDispetchqtyreport($value,$date_range,$state_id,$production_unit);

             $response_detail[]=  $this->booking_model->GetDispetchqtydetailreport($value,$date_range,$state_id,$production_unit); 
        } 

        //echo "<pre>"; print_r($response_detail); die;
        $data['results'] = $response;
        $data['results_details'] = $response_detail;
        $states= $this->vendor_model->GetStates();
        $data['states'] =$states;
        $this->load->view('dispetch_report',$data);
    }
}