<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

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
        $this->load->model('login_model');   
        $this->load->library('dynamic');                

    }

    public function index(){
        $this->dynamic->alreadyLogIn();
        //echo hash('sha256', 'dviaappSB39Mmo'); die;
        $data['title'] = "Report"; 
        $data['title'] = "Shail Booking";
        if (!empty($_POST)) {
            //echo "<pre>"; print_r($_POST);
            $code = $this->input->post('code'); 
            $this->form_validation->set_rules('code', 'Enter Code','required'); 
            if ($this->form_validation->run() == false) {
            }
            else {  
                unset($_SESSION['store']);
                unset($_SESSION['search__report_data']);
                $this->session->unset_userdata('admin'); 
                $code = strtoupper($_POST['code']);
                $keystring = 'dviaapp'.trim($code).'de';
              
                $key = MD5($keystring);  //hash('sha256', $keystring); die;
                $url = "http://mail.dil.in/webapi/createuser/signupXgenMail.jsp"; 

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'http://mail.dil.in/webapi/createuser/signupXgenMail.jsp',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{ flag  : "dviaapp", reqotp   : '.trim($code).', source    : "de" , encr     : false , key     : '.$key.' }',
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: text/plain',
                    'Cookie: JSESSIONID=43ED727691A89BAC76B6327FE8341E28'
                  ),
                ));

                $curl_response = curl_exec($curl);

                curl_close($curl);
                //echo $response;
                $response =  json_decode($curl_response); 
                
                if($response->status)
                {
                    $email = $response->Linfo->Lname;
                    $logindata = array(
                        'username' =>$email, 
                        );
                    //print_r($logindata);
                    $result = $this->login_model->userlogin($logindata); 
                    if(count($result))
                    {  

                        $_SESSION['store'] = trim($_POST['store']);
                        $this->session->set_userdata('admin', $result); 
                        if($result['role']==4 || $result['role']==5) 
                          redirect('booking/report');   
                        else
                            redirect('booking'); 
                    }
                    else {
                        //echo "<br> 1111 <br>";
                        $this->session->set_flashdata('err_msg','User not registered.');
                        redirect('/');
                    }
                }
                else
                {
                    $this->session->set_flashdata('err_msg','Unauthorised Access.');
                    redirect('/');  
                }

                
            }
        }
        $this->load->view('login',$data);
    } 
    public function dashboard(){ 
        $this->dynamic->alreadynotLogIn();
        $data['title'] = "Dashboard";
        $admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));
        $data['bookings'] = array();
        if($role==1)
            $data['bookings'] = $this->booking_model->GetBargainalert();


        /* 15 days data */
        $group_by  = array('status');
        $age  = 15;
        $status  = '';
        $sum_report_15 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by); 
        $tot_sum_report_15 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by=array()); 

        $group_by  = array('is_lock');
        $locked_15 = $this->booking_model->GetBookingSummaryLockedDashboard($age,$status,$group_by);

        $data['fifteendays']= array('sum_report' => $sum_report_15,'tot_sum_report' => $tot_sum_report_15,'locked' => $locked_15);
        /* 15 days data ends */


        /* 30 days data */
        $group_by  = array('status');
        $age  = 30;
        $status  = '';
        $sum_report_30 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by); 
        $tot_sum_report_30 = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by=array()); 

        $group_by  = array('is_lock');
        $locked_30 = $this->booking_model->GetBookingSummaryLockedDashboard($age,$status,$group_by);
        $data['onemonth']= array('sum_report' => $sum_report_30,'tot_sum_report' => $tot_sum_report_30,'locked' => $locked_30);
        /* 30 days data ends */

        /* more than 1month data */
        $group_by  = array('status');
        $age  = 0;
        $status  = '';
        $sum_report_month = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by); 
        $tot_sum_report_month = $this->booking_model->GetBookingSummarySumReportDashboard($age,$status,$group_by=array()); 

        $group_by  = array('is_lock');
        $locked_month = $this->booking_model->GetBookingSummaryLockedDashboard($age,$status,$group_by);
        $data['moremonth']= array('sum_report' => $sum_report_month,'tot_sum_report' => $tot_sum_report_month,'locked' => $locked_month);
        /* more than 1month data */

        //echo "<pre>"; print_r($data['15days']['sum_report']); die;

        $this->load->view('dashboard',$data);
    }

    function logout()
    { 
        unset($_SESSION['store']);
        unset($_SESSION['search__report_data']);
        $this->session->unset_userdata('admin'); 
        redirect('/');
    } 
}