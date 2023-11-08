<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target_report extends CI_Controller {

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
        $this->load->model('admin_model');   
        $this->load->model('target_model');  
        $this->load->model('vendor_model');  
        $this->load->library('dynamic');    
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role']; 
    } 
    public function map(){  
        $this->dynamic->alreadynotLogIn();  
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;
        $data['logged_role'] = $logged_role;
        $data['title'] = "Location Map";
        $userid = $this->uri->segment(3);
        $month = urldecode($this->uri->segment(4));
        $year = urldecode($this->uri->segment(5));
        $state_id = urldecode($this->uri->segment(6));
        $day = urldecode($this->uri->segment(7));

        $latitude = urldecode($this->uri->segment(8));
        $longitude = urldecode($this->uri->segment(9));

        $condition = array('role'=> 1);         

        $s = 0 ;

        if((isset($_POST) && !empty($_POST)))
        {
            $userid =  $_POST['employee'];
            $month =  $_POST['month'];
            $year =  $_POST['year'];
            $state_id =  $_POST['state']; 
            
            $condition = array('role'=> 1);        
            if($role==1)
            {
                $condition = array('id'=> $userid,'status' => 1);
                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role' => 6, 'team_lead_id' => $userid);
            }
            else
            {
                $condition = array('role'=> 1,'status' => 1);
                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role'=> 6);
            }
        }
        else
        {
            if($userid=='')
                $s = 1;
        }
        $data['locations']  = array();
        $data['locations1'] = array();
        if($s==0)
        {
            $_POST['employee']  = $userid; 
            $_POST['month']  = $month; 
            $_POST['year']  = $year; 
            $_POST['state']  = $state_id; 

            $data['users'] =$this->admin_model->allemployess($condition);

           
            $condition = array('user_id' => $userid); 
            $condition_month = array('user_id' => $userid); 
            if($year !='')
            {
                $monthyear =   $year."-".$month;
                $condition_month['tracking_date'] = $monthyear;
                if(trim($day)!='')
                    $monthyear =   $year."-".$month."-".$day;
                $condition['tracking_date'] = $monthyear;
            } 
            if($state_id!='')
            {
                $condition['state'] = $state_id;
                $condition_month['state'] = $state_id;
            }
            //echo "<pre>"; print_r($condition); die;
            //echo "<pre>"; print_r($condition); die;
            $locations_month  = $this->target_model->getuserlocations($condition_month);
            $locations  = $this->target_model->getuserlocations($condition);
            
            $data['locations_month'] = $locations_month; 
            if($locations)
            {
                $locations_array= array();
                foreach ($locations as $key => $value) {
                    $locations_array[$value['user_id']][]= $value; 
                }
                $locations_array = array_values($locations_array); 
                $data['locations'] = $locations_array; 
            }

            if($locations_month)
            {
                $locations_month_array= array();
                foreach ($locations_month as $key => $value) {
                    $key_id = date('dmY', strtotime($value['tracking_date']));
                    $locations_month_array[$key_id][]= $value; 
                } 
                $data['locations_month'] = $locations_month_array; 
            }

            //echo "<pre>"; print_r($data['locations_month']); die;
        } 
        $data['userid']= $userid;
        $data['month']= $month;
        $data['year']= $year;
        $data['state_id']= $state_id;
        $data['day_data']= $day;
        $data['latitude']= $latitude;
        $data['longitude']= $longitude;

        $states= $this->vendor_model->GetStates();
        $data['states'] =$states; 
        //echo "<pre>"; print_r($data['locations']); die;
        $this->load->view('map',$data);
        //echo "<pre>"; print_r($responses); die;
    }
    public function index(){  

        if((isset($_POST) && !empty($_POST) && isset($_POST['downloadpdf']) && $_POST['downloadpdf']=='downloadpdf') )
        {
            $this->dynamic->alreadynotLogIn(); 
            $data['title'] = "Target Report";
            $admin_info = $this->session->userdata('admin');  
            $admin_role = $admin_info['role'];
            $admin_id = $admin_info['id'];   
            $role = $this->session->userdata('admin')['role'];
            $userid = $this->session->userdata('admin')['id']; 
            $logged_in_id = $userid;
            $logged_role = $role;  

            $data['since_months'] = "";
            $makers = array();
            $data['result'] = array();
            $data['logged_role'] = $logged_role;
            $postdata = array();
            $postdata_secondary = array();
            $response = array();
            $states= $this->vendor_model->GetStates();
            $data['results'] = array() ;

            if(!isset($_SERVER['HTTP_REFERER'])) {
                unset($_SESSION['search_target_report_data']);
            }

            if(isset($_SESSION['search_target_report_data']) && (!isset($_POST)))
                $_POST = $_SESSION['search_target_report_data']; 
            if((isset($_POST) && !empty($_POST)) || isset($_SESSION['search_target_report_data']) )
            {
                if(isset($_POST) && !empty($_POST)) 
                { 
                    $_SESSION['search_target_report_data'] = $_POST;
                }
                else
                { 
                    $_POST = $_SESSION['search_target_report_data']; 
                }
                $conditions = $_POST; 
                //$data['results']   = $this->target_model->getEmployeetargets($conditions); 
                //echo "<pre>"; print_r($_POST); die;
                $data['since_months'] = "";
                if(isset($conditions['employee']))
                {
                    $logindata = array(
                        'id' =>$conditions['employee'], 
                        );
                    //print_r($logindata);
                    $this->load->model('login_model');   
                    $since_months = $this->login_model->joiningmonth($logindata); 
                    $data['since_months'] = $since_months;
                    //print_r($result); die;
                }


                $this->load->library("pagination");
                $limit = 2000000;
                if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                    $limit = $conditions_data['limit'];
                } 

                $config = array();
                $config["base_url"] = base_url() . "target_report/index/";
                $total_rows =  $this->target_model->countEmployeetargets($conditions);
                $config["total_rows"] = $total_rows;
                // Number of items you intend to show per page.
                $config["per_page"] = 2000000;
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
                //echo "<pre>"; print_r($data["links"]); die;
                //echo $booking_status; 
                $data['results']   = $this->target_model->getEmployeetargets($conditions,$limit,$page); 
            } 
             
            $data['states'] =$states; 
            $data['response'] =$response; 
            $condition = array('role'=> 1);        
            if($role==1)
            {
                $condition = array('id'=> $userid);

                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role' => 6, 'team_lead_id' => $userid);

            }
            else if($role==6)
            {
                $condition = array('id'=> $userid); 
            }
            else
            {
                 
                $condition = array('role'=> 1);
                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role'=> 6);
            }
            //echo "<pre>"; print_r($condition); die;
            $data['users'] =$this->admin_model->allemployess($condition);  
            //echo "<pre>"; print_r($data['results']); die;
            $data['team_leads']   = array();
            if($role==4)
                $data['team_leads'] =$this->admin_model->Getteamleads(); 
            $result = $this->load->view('target_report_pdf',$data,true);

            include(FCPATH."mpdf1/mpdf.php");
            $mpdf=new mPDF('utf-8','A4-L','0','0','0','0','10','0','0','0'); 
            $monthName = date('F', mktime(0, 0, 0, $_POST['month'], 10));

            $report_header = 'Target Report '.$monthName.' '.$_POST['year'];
            $header = '<h3 style="text-align:center;">'.$report_header.'</h3>';
            $mpdf->SetHTMLHeader($header);                        
            $mpdf->WriteHTML($result);
            $f_name = $report_header.'.pdf';
            $invpice_name = FCPATH.'/'.$f_name; 
            $mpdf->Output($f_name,'D'); 
        }
        if((isset($_POST) && !empty($_POST) && isset($_POST['downloadexcel']) && $_POST['downloadexcel']=='downloadexcel') )
        {

            $this->dynamic->alreadynotLogIn(); 
            $data['title'] = "Target Report";
            $admin_info = $this->session->userdata('admin');  
            $admin_role = $admin_info['role'];
            $admin_id = $admin_info['id'];   
            $role = $this->session->userdata('admin')['role'];
            $userid = $this->session->userdata('admin')['id']; 
            $logged_in_id = $userid;
            $logged_role = $role;  

            $data['since_months'] = "";
            $makers = array();
            $data['result'] = array();
            $data['logged_role'] = $logged_role;
            $postdata = array();
            $postdata_secondary = array();
            $response = array();
            $states= $this->vendor_model->GetStates();
            $data['results'] = array() ;

            if(!isset($_SERVER['HTTP_REFERER'])) {
                unset($_SESSION['search_target_report_data']);
            }

            if(isset($_SESSION['search_target_report_data']) && (!isset($_POST)))
                $_POST = $_SESSION['search_target_report_data']; 
            $results = array();
            if((isset($_POST) && !empty($_POST)) || isset($_SESSION['search_target_report_data']) )
            {
                if(isset($_POST) && !empty($_POST)) 
                { 
                    $_SESSION['search_target_report_data'] = $_POST;
                }
                else
                { 
                    $_POST = $_SESSION['search_target_report_data']; 
                }
                $conditions = $_POST; 
                //$data['results']   = $this->target_model->getEmployeetargets($conditions); 
                //echo "<pre>"; print_r($_POST); die;
                $data['since_months'] = "";
                if(isset($conditions['employee']))
                {
                    $logindata = array(
                        'id' =>$conditions['employee'], 
                        );
                    //print_r($logindata);
                    $this->load->model('login_model');   
                    $since_months = $this->login_model->joiningmonth($logindata); 
                    $data['since_months'] = $since_months;
                    //print_r($result); die;
                }


                $this->load->library("pagination");
                $limit = 2000000;
                if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                    $limit = $conditions_data['limit'];
                } 

                $config = array();
                $config["base_url"] = base_url() . "target_report/index/";
                $total_rows =  $this->target_model->countEmployeetargets($conditions);
                $config["total_rows"] = $total_rows;
                // Number of items you intend to show per page.
                $config["per_page"] = 2000000;
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
                //echo "<pre>"; print_r($data["links"]); die;
                //echo $booking_status; 
                $results   = $this->target_model->getEmployeetargets($conditions,$limit,$page); 
            } 
             
            $data['states'] =$states; 
            $data['response'] =$response; 
            $condition = array('role'=> 1);        
            if($role==1)
            {
                $condition = array('id'=> $userid);

                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role' => 6, 'team_lead_id' => $userid);

            }
            else if($role==6)
            {
                $condition = array('id'=> $userid); 
            }
            else
            {
                 
                $condition = array('role'=> 1);
                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role'=> 6);
            }
            //echo "<pre>"; print_r($condition); die;
            $data['users'] =$this->admin_model->allemployess($condition);  
            //echo "<pre>"; print_r($data['results']); die;
                

            $monthName = date('F', mktime(0, 0, 0, $_POST['month'], 10));
            $report_header = 'Target Report '.$monthName.' '.$_POST['year']; 
            
            $this->load->library('excel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            $i = 1;
            $row = 1;
            $col= 'A';
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'S.No.');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Name');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Joining date');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Joining Months');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'State Name)');$col++;
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Target (MT)');$col++; 
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Bargain (MT)');$col++;
            if((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')) { 
                $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Dispatched (MT)');$col++; 
            }
            $percent_name = (isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers') ? 'Dispatched' : 'Bargain';
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$percent_name.' %');$col++; 
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Target Visits');$col++; 
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Visited');$col++; 
            $objPHPExcel->getActiveSheet()->setCellValue($col.$row,'Visits %');$col++;
            $row++; 
            if($results)
            {
                $count = 1; 
                foreach ($results as $key => $value) {
                    $col= 'A';
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$count);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['employee_name']);$col++;
                    $joining_date =  '';
                    if($value['joining_date'])
                        $joining_date = date('d-m-Y',strtotime($value['joining_date']));
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$joining_date);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['joining_month']);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['state_name']);$col++;
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($value['total_target_weight'],2));$col++; 
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($value['bargain_total_weight'],2));$col++; 

                    if((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')) {
                        $month_post = $_POST['month'].'-'.$_POST['year'];
                        $dispatched  =  dispatchtarget($month_post,$value['user_id'],$value['state_ids']);
                        $dispatched_persentage = ($value['total_target_weight']>0) ? (round(($dispatched['total_dispateched_weight']*100)/($value['total_target_weight']),2)) : 0; 
                        $dispatched_target_Acheived =  ($dispatched['total_dispateched_weight']) ? $dispatched_persentage : 0; 
                        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($dispatched['total_dispateched_weight'],2));$col++; 
                        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($dispatched_target_Acheived,2));$col++; 
                    }
                    else
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($value['per_target'],2));$col++; 
                    }


                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['total_target_visits']);$col++; 
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,$value['total_visited']);$col++; 
                    $objPHPExcel->getActiveSheet()->setCellValue($col.$row,round($value['per_target_visit'],2));$col++;
                    $count++;
                    $row++;
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$col.$row)->getAlignment()->applyFromArray(
                array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
            );

            $fileName = $report_header.'.xls';
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Pragma: no-cache");
            header("Expires: 0");
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            //$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
            $objWriter->save('php://output');   
        }
        else
        {
            $this->dynamic->alreadynotLogIn(); 
            $data['title'] = "Target Report";
            $admin_info = $this->session->userdata('admin');  
            $admin_role = $admin_info['role'];
            $admin_id = $admin_info['id'];   
            $role = $this->session->userdata('admin')['role'];
            $userid = $this->session->userdata('admin')['id']; 
            $logged_in_id = $userid;
            $logged_role = $role;  

            $data['since_months'] = "";
            $makers = array();
            $data['result'] = array();
            $data['logged_role'] = $logged_role;
            $postdata = array();
            $postdata_secondary = array();
            $response = array();
            $states= $this->vendor_model->GetStates();
            $data['results'] = array() ;

            if(!isset($_SERVER['HTTP_REFERER'])) {
                unset($_SESSION['search_target_report_data']);
            }

            if(isset($_SESSION['search_target_report_data']) && (!isset($_POST)))
                $_POST = $_SESSION['search_target_report_data']; 
            if((isset($_POST) && !empty($_POST)) || isset($_SESSION['search_target_report_data']) )
            {
                if(isset($_POST) && !empty($_POST)) 
                { 
                    $_SESSION['search_target_report_data'] = $_POST;
                }
                else
                { 
                    $_POST = $_SESSION['search_target_report_data']; 
                }
                $conditions = $_POST; 
                //$data['results']   = $this->target_model->getEmployeetargets($conditions); 
                //echo "<pre>"; print_r($_POST); die;
                $data['since_months'] = "";
                if(isset($conditions['employee']))
                {
                    $logindata = array(
                        'id' =>$conditions['employee'], 
                        );
                    //print_r($logindata);
                    $this->load->model('login_model');   
                    $since_months = $this->login_model->joiningmonth($logindata); 
                    $data['since_months'] = $since_months;
                    //print_r($result); die;
                }


                $this->load->library("pagination");
                $limit = 20;
                if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                    $limit = $conditions_data['limit'];
                } 

                $config = array();
                $config["base_url"] = base_url() . "target_report/index/";
                $total_rows =  $this->target_model->countEmployeetargets($conditions);
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
                //echo "<pre>"; print_r($data["links"]); die;
                //echo $booking_status; 
                $data['results']   = $this->target_model->getEmployeetargets($conditions,$limit,$page); 
                //echo "<pre>"; print_r($data["results"]); die;
            } 
             
            $data['states'] =$states; 
            $data['response'] =$response; 
            $condition = array('role'=> 1);        
            if($role==1)
            {
                $condition = array('id'=> $userid);

                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role' => 6, 'team_lead_id' => $userid);

            }
            else if($role==6)
            {
                $condition = array('id'=> $userid); 
            }
            else
            {
                 
                $condition = array('role'=> 1);
                if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                     $condition = array('role'=> 6);
            }
            //echo "<pre>"; print_r($condition); die;
            $data['users'] =$this->admin_model->allemployess($condition);  
            //echo "<pre>"; print_r($data['results']); die;
            $data['team_leads']   = array();
            if($role==4)
                $data['team_leads'] =$this->admin_model->Getteamleads(); 
        }
        $this->load->view('target_report',$data);
    }  

    public function getusers()
    {   
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;  
        $view_reoprt = $_POST['view_reoprt'];


        if($role==1)
        {
            $condition = array('id'=> $userid);
            if($view_reoprt=='secondarymakers')
                $condition = array('role' => 6, 'team_lead_id' => $userid);
        }
        else
        {
            $condition = array('role'=> 1);
            if($view_reoprt=='secondarymakers')
                $condition = array('role'=> 6);
        }
        $users =$this->admin_model->allemployess($condition);  
        $response = "";
        if($role!=1 ||  $view_reoprt=='secondarymakers')
        {
            $response = "<option value=''>Select  Employee</option>";
        }
        if($users)
        {
            foreach ($users as $key => $value) { 
                $response .= '<option value="'.$value['id'].'" >'.$value['name'].' '.$value['username'].'</option>';
            }
        }
        echo $response;
    }



    public function pdfdownload(){  
        $this->dynamic->alreadynotLogIn(); 
        $data['title'] = "Target Report";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id'];   
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
        $logged_in_id = $userid;
        $logged_role = $role;  

        $data['since_months'] = "";
        $makers = array();
        $data['result'] = array();
        $data['logged_role'] = $logged_role;
        $postdata = array();
        $postdata_secondary = array();
        $response = array();
        $states= $this->vendor_model->GetStates();
        $data['results'] = array() ;

        if(!isset($_SERVER['HTTP_REFERER'])) {
            unset($_SESSION['search_target_report_data']);
        }

        if(isset($_SESSION['search_target_report_data']) && (!isset($_POST)))
            $_POST = $_SESSION['search_target_report_data']; 
        if((isset($_POST) && !empty($_POST)) || isset($_SESSION['search_target_report_data']) )
        {
            if(isset($_POST) && !empty($_POST)) 
            { 
                $_SESSION['search_target_report_data'] = $_POST;
            }
            else
            { 
                $_POST = $_SESSION['search_target_report_data']; 
            }
            $conditions = $_POST; 
            //$data['results']   = $this->target_model->getEmployeetargets($conditions); 
            //echo "<pre>"; print_r($_POST); die;
            $data['since_months'] = "";
            if(isset($conditions['employee']))
            {
                $logindata = array(
                    'id' =>$conditions['employee'], 
                    );
                //print_r($logindata);
                $this->load->model('login_model');   
                $since_months = $this->login_model->joiningmonth($logindata); 
                $data['since_months'] = $since_months;
                //print_r($result); die;
            }


            $this->load->library("pagination");
            $limit = 2000000;
            if (isset($conditions_data['limit']) && is_numeric($conditions_data['limit'])) {
                $limit = $conditions_data['limit'];
            } 

            $config = array();
            $config["base_url"] = base_url() . "target_report/index/";
            $total_rows =  $this->target_model->countEmployeetargets($conditions);
            $config["total_rows"] = $total_rows;
            // Number of items you intend to show per page.
            $config["per_page"] = 2000000;
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
            //echo "<pre>"; print_r($data["links"]); die;
            //echo $booking_status; 
            $data['results']   = $this->target_model->getEmployeetargets($conditions,$limit,$page); 
        } 
         
        $data['states'] =$states; 
        $data['response'] =$response; 
        $condition = array('role'=> 1);        
        if($role==1)
        {
            $condition = array('id'=> $userid);

            if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                 $condition = array('role' => 6, 'team_lead_id' => $userid);

        }
        else if($role==6)
        {
            $condition = array('id'=> $userid); 
        }
        else
        {
             
            $condition = array('role'=> 1);
            if(isset($_POST) && !empty($_POST) && isset($_POST['view_reoprt']) && !empty($_POST) && $_POST['view_reoprt']=='secondarymakers')
                 $condition = array('role'=> 6);
        }
        //echo "<pre>"; print_r($condition); die;
        $data['users'] =$this->admin_model->allemployess($condition);  
        //echo "<pre>"; print_r($data['results']); die;
        $data['team_leads']   = array();
        if($role==4)
            $data['team_leads'] =$this->admin_model->Getteamleads(); 
        $result = $this->load->view('target_report_pdf',$data,true);

        include(FCPATH."mpdf1/mpdf.php");
        $mpdf=new mPDF('utf-8','A4-L','0','0','0','0','10','0','0','0'); 
        $header = '<h3 style="text-align:center;">Target Report</h3>';
        $mpdf->SetHTMLHeader($header);                        
        $mpdf->WriteHTML($result);
        $f_name = 'dsdsad.pdf';
        $invpice_name = FCPATH.'/'.$f_name; 
        $mpdf->Output($f_name,'I'); die;

        echo "<pre>"; print_r($result); die;
    } 

}
