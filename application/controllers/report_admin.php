<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_admin extends CI_Controller {

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
        $this->load->model('report_model');     
        $this->load->model('product_model');                   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $this->load->library('dynamic');     
        $this->dynamic->alreadynotLogIn();       

    }

    public function index(){
    	$data['title'] = "Report";
    	if(!empty($_POST))
    	{
    		
    		$start_date = $_POST['start_date'];
    		$timestamp = strtotime($start_date); 
    		$start_date1 = date('Y-m-d', $timestamp); 
    		$end_date = $_POST['end_date'];
    		$timestamp1 = strtotime($end_date); 
    		$end_date1 = date('Y-m-d', $timestamp1); 
    		$condition = "`transaction_history.created_at` BETWEEN '".$start_date1." 00:00:00.000000' AND '".$end_date1." 23:54:54.969999'"; 
    		$reports = $this->report_model->GetTransactions1($condition);
    		$data['reports'] = $reports;
    		if(isset($_POST['download']))
    		{
    			$fileName = "Payment Report " . date('Y_m_d') . ".xls";
    			header("Content-Disposition: attachment; filename=\"$fileName\"");
				header("Content-Type: application/vnd.ms-excel");
				$flag = false;
			    /*foreach($reports as $row) {
			        if(!$flag) {
			            // display column names as first row
			            echo implode("\t", array_keys($row)) . "\n";
			            $flag = true;
			        }

			        echo implode("\t", array_values($row)) . "\n";
			        // filter data
			        array_walk($row, 'filterData'); 

			    } */
    		}
    	} 
    	
    	//echo "<pre>"; print_r($data['reports']); die;
    	$this->load->view('reports',$data);

	}

 

	public function createXLS() {
		// create file name
        $fileName = 'data-'.time().'.xlsx';  
		// load excel library
        $this->load->library('excel');
        $empInfo = $this->export->employeeList();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'First Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Last Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Email');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'DOB');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Contact_No');       
        // set Row
        $rowCount = 2;
        foreach ($empInfo as $element) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element['first_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $element['last_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element['email']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $element['dob']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $element['contact_no']);
            $rowCount++;
        }
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(ROOT_UPLOAD_IMPORT_PATH.$fileName);
		// download file
        header("Content-Type: application/vnd.ms-excel");
        redirect(HTTP_UPLOAD_IMPORT_PATH.$fileName);        
    }


	
	public function login(){    

		if (!empty($_POST)) {
			//echo "<pre>"; print_r($_POST);
            $email = $this->input->post('username');
            $password = $this->input->post('password');
            $this->form_validation->set_rules('username', 'Email Address','required');
            $this->form_validation->set_rules('password', 'Password','required');
            if ($this->form_validation->run() == false) {
            }
            else { 
                $logindata = array(
                    'username' =>$email,
                    'password' =>md5($password)
                    );
                $result = $this->product_model->userlogin($logindata); 
                if(count($result))
                {  
                    $this->session->set_userdata('admin', $result);  
                    redirect('product_manage'); 
                }
                else {
                    $this->session->set_flashdata('err_msg','Please check email address or password.');
                    redirect('/');
                }
            }
        }
    	$this->load->view('login');

	}
	public function status_update(){    
		$status =  $this->uri->segment(3);
		$product_id =  base64_decode($this->uri->segment(4)); 
		$update_data = array('is_enable' => $status);
		$condition  = array('product_id' => $product_id);
		$result= $this->product_model->UpdateProduct($update_data,$condition);
		if($result)
			$this->session->set_flashdata('suc_msg','Product updated successfully.');
		else
			$this->session->set_flashdata('err_msg','Something went wrong.');
		redirect('product_manage');
	} 

	public function status_update_packaging(){ 
		$status =  $this->uri->segment(3);
		$packing_id =  base64_decode($this->uri->segment(4)); 
		$product_id =  base64_decode($this->uri->segment(5)); 
		if($status==1)
		{
			$enable= $this->product_model->CheckPackagingEnable($packing_id);
			if($enable)
			{
				//$packages= $this->product_model->GetProductPackagingByProductId($product_id);
				$update_data_status = array('is_default' => 'N');
				$condition = array('product_id' => $product_id);
				$this->product_model->UpdatePackaging($update_data_status,$condition);
				$packaging = $this->product_model->GetProductPackagingToenable($product_id);
				$update_data_status = array('is_default' => 'Y');
				$condition = array('id' => $packaging['id']);
				$this->product_model->UpdatePackaging($update_data_status,$condition);

			}
		}
		$update_data = array('is_enable' => $status);
		$condition  = array('id' => $packing_id);
		$result= $this->product_model->UpdatePackaging($update_data,$condition);
		if($result)
			$this->session->set_flashdata('suc_msg','Product updated successfully.');
		else
			$this->session->set_flashdata('err_msg','Something went wrong.');
		redirect('product_manage');
	} 


	public function edit_product(){     
		$product_id =  base64_decode($this->uri->segment(3));
		if(!empty($_POST))
		{ 
			$packages = $_POST['package'];
			if(count($packages))
			{
				foreach ($packages as $key => $price) {
					$update_data = array('price' => $price);
					$condition  = array('id' => $key);
					$result= $this->product_model->UpdatePackaging($update_data,$condition);

				}
				$this->session->set_flashdata('suc_msg','Product updated successfully.');
			}
			else
				$this->session->set_flashdata('err_msg','Nothing to update.');
			redirect('/product_admin');
		}
		$data['packagings'] = $this->product_model->GetProductPackagingByProductId($product_id);
		$data['product'] = $this->product_model->GetProductByProductId($product_id);
		$this->load->view('packagings',$data);
	} 	

	function logout()
    {
        $this->session->unset_userdata('admin'); 
        redirect('/');
    }

    function blank()
    {
        $this->load->view('blank');
    }
}