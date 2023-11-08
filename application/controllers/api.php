<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

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
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));     
 
        

    }

    public function invoice()
    {
        $date_file = date('d-m-Y');
        $log_file = FCPATH."api-logs/logs".$date_file.'.log';
        $log_file = fopen($log_file,"a");  
        fwrite($log_file, PHP_EOL .'================================================================================='.PHP_EOL.date('H:i').' API REQUEST => '.$this->uri->segment(2). PHP_EOL);  
        fwrite($log_file, print_r($_POST, true)); 
        fwrite($log_file, print_r($_FILES, true)); 
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
		//header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, serverkey');

        $response = array();
        $code = SUCCESS_CODE;
        $status = false;
        $data = array();
        $message = "Something went wrong";

        $headers = $this->input->request_headers(); 
        $server_key = (isset($headers['Serverkey']) && !empty(trim($headers['Serverkey']))) ?  trim($headers['Serverkey']) : '';   

        $error_message = array();
        if (empty(trim($server_key))) {
            $error_message[] = 'Server Key is required field.';
        } 
        if (!empty($error_message)) {
            $code  = VALIDATION_ERROR_CODE;
            $response_data = $error_message;
            $message = "Validation Failed";
        }
        else
        {
            if($server_key!='3b69b7e5-a962-4d3e-87ba-395812b9716c')
            {
                $code  = UNAUTHORIZED_USER_CODE;
                $response_data = array();
                $message = "Unauthorised Server Key";
            }
            else
            {
                $bargain_numbers = (isset($_POST['bargain_numbers']) && !empty(trim($_POST['bargain_numbers']))) ?  trim($_POST['bargain_numbers']) : '';   
                $file = (isset($_FILES['file']) && $_FILES['file']['size']) ?  $_FILES['file']['size'] : '';   
                $dispatch_date = (isset($_POST['dispatch_date']) && !empty(trim($_POST['dispatch_date']))) ?  trim($_POST['dispatch_date']) : '';   
                $dispatch_weight = (isset($_POST['dispatch_weight']) && !empty(trim($_POST['dispatch_weight']))) ?  trim($_POST['dispatch_weight']) : '';  
                $amount = (isset($_POST['amount']) && !empty(trim($_POST['amount']))) ?  trim($_POST['amount']) : ''; 

                $invoice_no = (isset($_POST['invoice_no']) && !empty(trim($_POST['invoice_no']))) ?  trim($_POST['invoice_no']) : ''; 
                
                if (empty(trim($bargain_numbers))) {
                    $error_message[] = 'Bargain Numbers is required field.';
                } 

                if (empty(trim($invoice_no))) {
                    $error_message[] = 'Invoice No is required field.';
                }                 

                if (empty(trim($file))) {
                    $error_message[] = 'File is required field.';
                } 
                if (empty(trim($dispatch_date))) {
                    $error_message[] = 'Dispatch Date is required field.';
                } 
                if (empty(trim($dispatch_weight))) {
                    $error_message[] = 'Dispatch Weight is required field.';
                }
                if (empty(trim($amount))) {
                    $error_message[] = 'Amount is required field.';
                }  
                if (!empty($error_message)) {
                    $code  = VALIDATION_ERROR_CODE;
                    $response_data = $error_message;
                    $message = "Validation Failed";
                }
                else
                {
                    $insert_data = array(
                        'bargain_numbers' => $bargain_numbers, 
                        'dispatch_date' => date('Y-m-d',strtotime($dispatch_date)),
                        'dispatch_weight' => $dispatch_weight,
                        'amount' => $amount,
                        'invoice_no' => $invoice_no,
                    );

                    if ($_FILES['file']['size'] != 0) {
                        $upload_dir = './invoices/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir);
                        }
                        $config = array();
                        
                        $config['upload_path'] = $upload_dir;
                        $config['allowed_types'] = 'pdf';
                        $config['file_name'] = time() . $_FILES['file']['name'];
                        $config['overwrite'] = false;
                        //pr($config); die;
                        $this->load->library('upload', $config);
                        if (!$this->upload->do_upload('file')) { 
                            $error_message[] = $this->upload->display_errors(); 
                        } else {
                            $this->upload_data['file'] = $this->upload->data();  
                            $insert_data['file'] = $this->upload_data['file']['file_name']; 
                        }
                    } else {
                         $error_message[] = 'Invoice File is required';  
                    }
                    if (!empty($error_message)) {
                        $code  = VALIDATION_ERROR_CODE;
                        $response_data = $error_message;
                        $message = "Validation Failed";
                    }
                    else
                    {
                        if($this->booking_model->AddInvoice($insert_data))
                        {
                            $status = true;
                            $message = "Invoice Saved Successfully";
                        }   
                    }
                }
            }
        }
        $response['code'] = $code;
        $response['status'] = $status;
        $response['message'] = $message;
        $response['data'] = $error_message;
        echo json_encode($response);
       fwrite($log_file, PHP_EOL .'================================================================================='.PHP_EOL.date('H:i').' API Response => '.$this->uri->segment(2). PHP_EOL);  
       fwrite($log_file, print_r($response, true)); 
        die;
    }
}