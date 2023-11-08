<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approve extends CI_Controller {

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
        $this->load->model('secondarybooking_model');  
    }
    public function index($booking_id){   
        $booking_id = base64_decode($booking_id);
        $condition = array('id'=>$booking_id);
        $updatedata = array('status'=>1,'approval_time'=>date('Y-m-d H:i:s'));
        if($this->secondarybooking_model->UpdateBooking($updatedata,$condition))
        {
            echo "approved suucessfullly";
        }
        else
        {
            echo "Something went wrong";
        }
    }
}