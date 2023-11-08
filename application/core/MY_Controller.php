<?php

class MY_Controller extends CI_Controller {

    private $template = array(); 

    public function __construct() {
        

        parent::__construct();
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model')); 
    }    

    public function main_layout($view, $data) {
        //echo "string"; die;
        unset($this->template);         
        $this->template['header'] = $this->load->view('layout/header', $data, true); 
        $this->template['main'] = $this->load->view($view, $data, true); 
        $this->template['footer'] = $this->load->view('layout/footer', $data, true);
        $this->load->view('layout/main', $this->template);
    }  

} 
