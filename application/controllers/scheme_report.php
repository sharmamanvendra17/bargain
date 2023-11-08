<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheme_report extends CI_Controller {

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
        $this->load->model('scheme_model'); 
        $this->load->model('broker_model');  
        $this->load->model('brand_model');
        $this->load->model('category_model');
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();              

    }

    public function index(){ 
    	$data['title'] = "Scheme Report";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role; 
        $data['logged_role'] = $logged_role;
        $data['results'] = array();
        $data['schemeid'] = '';
        if(isset($_POST) && !empty($_POST) && $_POST['scheme_id']!='')
        {
            $scheme_id = $_POST['scheme_id'];    
            $data['schemeid'] = $scheme_id;        
            $condition = array('scheme.id'=> $scheme_id); 
            $scheme_info = $this->scheme_model->GetSchemesdata($condition);

            $state_id = $scheme_info['scheme_state'];
            $brand_id = $scheme_info['brand_id'];
            $category_id = $scheme_info['category_id'];
            $from_date = date('Y-m-d', strtotime($scheme_info['from_date']));
            $to_date = date('Y-m-d', strtotime($scheme_info['to_date']));

            $condition_party = array(
                'STR_TO_DATE(pi_history.dispatch_date,"%d-%m-%Y") >= ' =>$from_date,
                'STR_TO_DATE(pi_history.dispatch_date,"%d-%m-%Y") <= ' =>$to_date,
                'pi_sku_history.brand_id ' =>$brand_id,
                'pi_sku_history.category_id ' =>$category_id,
                'vendors.state_id' => $state_id
            );
            $data['results'] = $this->scheme_model->GetSchemeParties($condition_party); 
        }

        $condition = array('scheme.status'=> 1); 
    	$data['schemes'] = $this->scheme_model->GetSchemes($condition); 
        //echo "<pre>"; print_r($scheme_info); die;
    	$this->load->view('schemes_report',$data);

	} 
}