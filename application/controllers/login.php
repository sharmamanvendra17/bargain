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
        $this->load->model('distributor_model');   
        $this->load->model('Secondarybooking_model');   
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
                
                $login_code_string = $_POST['code'];
                $login_code_explode =  explode('-', $_POST['code']);
                $persona_email  = "";
                if(count($login_code_explode)==2)
                {
                    $persona_email  = strtolower($login_code_explode[0]);
                    $code = strtoupper($login_code_explode[1]);
                }
                else
                {
                    $code = strtoupper($login_code_explode[0]);
                }
                
                $keystring = 'dviaapp'.trim($code).'de';
                $parent_user_id = '';
                $parent_user_name = '';
                $key = MD5($keystring);  //hash('sha256', $keystring); die;
                $url = "https://mail.dil.in/webapi/createuser/signupXgenMail.jsp"; 

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://mail.dil.in/webapi/createuser/signupXgenMail.jsp',
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

                        if($persona_email)
                        {
                            $parent_user_id = $result['id'];
                            $parent_user_name = $result['name'];
                            $logindata = array(
                                'username' =>$persona_email, 
                                'parent_user_id' =>$parent_user_id,
                                );
                            $result = $this->login_model->userpersonalogin($logindata); 
                            if(!count($result))
                            {
                                $this->session->set_flashdata('err_msg','User not registered or Unauthorised');
                                redirect('/');
                            }
                        }

                        if($result['status']==1 || ($persona_email))
                        {
                            $condition = array('email' => $email);
                            $distance_info  = $this->login_model->check_user_distance($condition); 
                            //echo "<pre>"; print_r($distance_info); die;
                            $yestrdatdate = date('d-m-Y',strtotime("-1 days"));
                            $insertnewdata = 1;
                            $start_date = "2023-02-01";
                            $end_date_date = date("Y-m-d",strtotime("-1 days"));
                            //echo "Sdasda";
                            if($distance_info)
                            {
                               // echo $distance_info['created_at']; die;
                               // echo $end_date_date; echo "---"; 
                                //echo "string";
                                $last_insert_date = date('Y-m-d', strtotime($distance_info['created_at'])); 
                                if($last_insert_date == $end_date_date)
                                {
                                    $insertnewdata = 0;
                                }
                                else
                                {
                                    $start_date = date('Y-m-d', strtotime('+1 day', strtotime($last_insert_date)));
                                }
                            } 
                            if($insertnewdata)
                            {
                                $this->load->model('target_model');
                                $eid  = $email;
                                $source = "mo";
                                $flag = "gul";
                                $fd =  $start_date;
                                $td = $end_date_date;
                                $plainTextBytes = utf8_encode($flag.$eid.$fd.$td.$source);
                                $key = base64_encode($plainTextBytes);
                                $requestBody  = array(
                                                'sFlag' => $flag,
                                                'eid' => $eid,
                                                'fd' => $fd,
                                                'td' => $td,
                                                'source' => $source,
                                                'key' => $key,
                                            );
                                //echo "<pre>"; print_r($requestBody); 
                                $requestdata =  json_encode($requestBody); 
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                  CURLOPT_URL => 'https://mail.dil.in/webapi/track/track.jsp',
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_ENCODING => '',
                                  CURLOPT_MAXREDIRS => 10,
                                  CURLOPT_TIMEOUT => 0,
                                  CURLOPT_FOLLOWLOCATION => true,
                                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                  CURLOPT_CUSTOMREQUEST => 'POST',
                                  CURLOPT_POSTFIELDS =>$requestdata,
                                  CURLOPT_HTTPHEADER => array(
                                    'Content-Type: application/json'
                                  ),
                                ));

                                $curl_response = curl_exec($curl);
                                curl_close($curl);
                                $response = json_decode($curl_response,true);
                                //echo "<pre>"; print_r($response); die;
                                if($response)
                                {
                                    $response_status = $response['status'];
                                    if($response_status)
                                    {
                                        $locations = $response['message']; 

                                        if($locations)
                                        {
                                            foreach ($locations as $location_key => $location) {
                                                $useradd = '';
                                                $lat = '';
                                                $lon = '';
                                                if(isset($location['useradd']))
                                                    $useradd = $location['useradd'];
                                                if(isset($location['lat']))
                                                    $lat = $location['lat'];
                                                if(isset($location['lon']))
                                                    $lon = $location['lon'];
                                                $timezone = '+5.5';
                                                $location_date = date("Y-m-d H:i:s", strtotime($location['date']) + 3600*($timezone));
                                                $insertdata[] = array(
                                                    'user_id' => $result['id'],
                                                    'email' => $result['username'],
                                                    'latitude' => $lat,
                                                    'longitude' => $lon,
                                                    'address' => $useradd,
                                                    'tracking_date' => $location_date,
                                                    'addressType' => $location['addressType'],
                                                    'attendanceStartStop' => $location['attendanceStartStop'],
                                                );
                                            } 
                                        }
                                    } 
                                    $this->target_model->addtracking($insertdata);
                                } 
                            }

                            $_SESSION['store'] = trim($_POST['store']);
                            $result['parent_user_id'] = $parent_user_id;
                            $result['parent_user_name'] = $parent_user_name;
                            $this->session->set_userdata('admin', $result);  
                            addlog('Logged In Account');

                            if($result['business_role']==1 || $result['business_role']==3)
                            {
                                if($result['role']==4 || $result['role']==5) 
                                  redirect('booking/report');   
                                else
                                    redirect('booking'); 
                            }
                            else
                            {

                                redirect('purchase'); 
                            }
                        }
                        else
                        {
                            $this->session->set_flashdata('err_msg','Account is deactivated.');
                            redirect('/login');
                        }
                    }
                    else {
                        //echo "<br> 1111 <br>";
                        $this->session->set_flashdata('err_msg','User not registered.');
                        redirect('/');
                    }
                }
                else
                { 
                    
                    //$code = strtoupper($_POST['code']);
                    $keystring = 'dviaapp'.trim($code).'de';
                  
                    $key = MD5($keystring);  //hash('sha256', $keystring); die;
                    $url = "https://mail.datamail.in/webapi/createuser/signupXgenMail.jsp"; 

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $url,
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
                        //echo "<pre>";
                        // print_r($result); die;
                        if(count($result))
                        {  
                            if($persona_email)
                            {
                                $parent_user_id = $result['id'];
                                $parent_user_name = $result['name'];
                                $logindata = array(
                                    'username' =>$persona_email, 
                                    'parent_user_id' =>$parent_user_id,
                                    );
                                $result = $this->login_model->userpersonalogin($logindata); 
                                if(!count($result))
                                {
                                    $this->session->set_flashdata('err_msg','User not registered or Unauthorised.');
                                    redirect('/');
                                }
                            }

                            if($result['status']==1)
                            {
                                $condition = array('email' => $email);
                                $distance_info  = $this->login_model->check_user_distance($condition); 
                                //echo "<pre>"; print_r($distance_info); die;
                                $yestrdatdate = date('d-m-Y',strtotime("-1 days"));
                                $insertnewdata = 1;
                                $start_date = "2023-02-01";
                                $end_date_date = date("Y-m-d",strtotime("-1 days"));
                                //echo "Sdasda";
                                if($distance_info)
                                {
                                   // echo $distance_info['created_at']; die;
                                   // echo $end_date_date; echo "---"; 
                                    //echo "string";
                                    $last_insert_date = date('Y-m-d', strtotime($distance_info['created_at'])); 
                                    if($last_insert_date == $end_date_date)
                                    {
                                        $insertnewdata = 0;
                                    }
                                    else
                                    {
                                        $start_date = date('Y-m-d', strtotime('+1 day', strtotime($last_insert_date)));
                                    }
                                } 
                                if($insertnewdata)
                                {
                                    $this->load->model('target_model');
                                    $eid  = $email;
                                    $source = "mo";
                                    $flag = "gul";
                                    $fd =  $start_date;
                                    $td = $end_date_date;
                                    $plainTextBytes = utf8_encode($flag.$eid.$fd.$td.$source);
                                    $key = base64_encode($plainTextBytes);
                                    $requestBody  = array(
                                                    'sFlag' => $flag,
                                                    'eid' => $eid,
                                                    'fd' => $fd,
                                                    'td' => $td,
                                                    'source' => $source,
                                                    'key' => $key,
                                                );
                                    //echo "<pre>"; print_r($requestBody); 
                                    $requestdata =  json_encode($requestBody); 
                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                      CURLOPT_URL => 'https://mail.dil.in/webapi/track/track.jsp',
                                      CURLOPT_RETURNTRANSFER => true,
                                      CURLOPT_ENCODING => '',
                                      CURLOPT_MAXREDIRS => 10,
                                      CURLOPT_TIMEOUT => 0,
                                      CURLOPT_FOLLOWLOCATION => true,
                                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                      CURLOPT_CUSTOMREQUEST => 'POST',
                                      CURLOPT_POSTFIELDS =>$requestdata,
                                      CURLOPT_HTTPHEADER => array(
                                        'Content-Type: application/json'
                                      ),
                                    ));

                                    $curl_response = curl_exec($curl);
                                    curl_close($curl);
                                    $response = json_decode($curl_response,true);
                                    //echo "<pre>"; print_r($response); die;
                                    if($response)
                                    {
                                        $response_status = $response['status'];
                                        if($response_status)
                                        {
                                            $locations = $response['message']; 

                                            if($locations)
                                            {
                                                foreach ($locations as $location_key => $location) {
                                                    $useradd = '';
                                                    $lat = '';
                                                    $lon = '';
                                                    if(isset($location['useradd']))
                                                        $useradd = $location['useradd'];
                                                    if(isset($location['lat']))
                                                        $lat = $location['lat'];
                                                    if(isset($location['lon']))
                                                        $lon = $location['lon'];
                                                    $timezone = '+5.5';
                                                    $location_date = date("Y-m-d H:i:s", strtotime($location['date']) + 3600*($timezone));
                                                    $insertdata[] = array(
                                                        'user_id' => $result['id'],
                                                        'email' => $result['username'],
                                                        'latitude' => $lat,
                                                        'longitude' => $lon,
                                                        'address' => $useradd,
                                                        'tracking_date' => $location_date,
                                                        'addressType' => $location['addressType'],
                                                        'attendanceStartStop' => $location['attendanceStartStop'],
                                                    );
                                                } 
                                            }
                                        } 
                                        $this->target_model->addtracking($insertdata);
                                    } 
                                }

                                $_SESSION['store'] = trim($_POST['store']);
                                $result['parent_user_id'] = $parent_user_id;
                                $result['parent_user_name'] = $parent_user_name;
                                $this->session->set_userdata('admin', $result);  
                                addlog('Logged In Account');

                                if($result['business_role']==1 || $result['business_role']==3)
                                {
                                    if($result['role']==4 || $result['role']==5) 
                                      redirect('booking/report');   
                                    else
                                        redirect('booking'); 
                                }
                                else
                                {

                                    redirect('purchase'); 
                                }
                            }
                            else
                            {
                                $this->session->set_flashdata('err_msg','Account is deactivated.');
                                redirect('/login');
                            }
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
        }
        $this->load->view('login',$data);
    } 
    public function dashboard(){ 
        //echo "<pre>"; print_r($this->session->userdata('admin')); die;
        $this->dynamic->alreadynotLogIn();
        addlog('Visit Dashboard');
        $data['title'] = "Dashboard";
        $admin_info = $this->session->userdata('admin');  
        $admin_id = $admin_info['id']; 
        $role = $this->session->userdata('admin')['role'];
        $business_role = $this->session->userdata('admin')['business_role']; 
        $userid = $this->session->userdata('admin')['id'];
        $data['logged_in_id'] = $userid;
        $data['logged_role'] = $role;
        $data['business_role'] = $business_role;
        $this->load->model(array('booking_model','brand_model','vendor_model','category_model','broker_model','admin_model'));
        $data['bookings'] = array();

        $data['moremonth']= array();
        $data['bookings']= array();
        $data['fifteendays']= array();
        $data['onemonth']= array(); 

        if($role!=6)
        {

            if($role==1)
            {
                $data['bookings'] = $this->booking_model->GetBargainalert();
            }


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


            /* Secondary maker dashboard start */
        }
        

         
        $data['top_distributers'] = array();
        $data['need_attenstion'] = array();
        $data['total_distributers'] = 0;
        $data['total_active_distributors'] = 0;
        $data['month_active_distributors'] = 0;
        $data['more_month_active_distributors'] = 0;
        if($role!=6)
        {
            $conditions_data = array();
            if($role==1)
                $conditions_data = array('maker' => $admin_id); 
            $total_distributer_count= $this->distributor_model->MakerDistributors($conditions_data); 
            $total_distributers = $total_distributer_count['total_distributor'];
            $data['total_distributers'] = $total_distributers;
            $conditions_data = array();
            if($role==1)
                $conditions_data = array('maker' => $admin_id); 
            $data['total_active_distributors'] = $this->Secondarybooking_model->TotalorderCount($conditions_data);
           
            $conditions_data = array('time' =>'30');
            if($role==1)
                $conditions_data = array('maker' => $admin_id,'time' =>'30'); 
            $data['month_active_distributors'] = $this->Secondarybooking_model->TotalorderCount($conditions_data);

            $conditions_data = array('time' =>'0');
            if($role==1)
                $conditions_data = array('maker' => $admin_id,'time' =>'0'); 
            $data['more_month_active_distributors'] = $this->Secondarybooking_model->TotalorderCount($conditions_data);

            $conditions_data = array('top' =>'5');
            if($role==1)
                $conditions_data = array('maker' => $admin_id,'top' => 5); 
            $data['top_distributers'] = $this->Secondarybooking_model->TotalorderCount($conditions_data); 
            $conditions_data = array();
            if($role==1)
                $conditions_data = array('maker' => $admin_id); 
            $data['need_attention'] = $this->Secondarybooking_model->need_attention($conditions_data); 
        }
        
        //echo $total_distributers; die;
        /* Secondary maker dashboard end */
        //echo "<pre>"; print_r($data['need_attenstion']); die;
        $data['total_assigned_sd'] = 0;
        if($role==6)
        {
            $conditions = array('admin.id' =>$admin_id);
           $data['total_assigned_sd'] = $this->booking_model->total_assigned_sd($conditions); 
        }
        if($role==1)
        {
         $conditions = array('admin.team_lead_id'=> $admin_id,'admin.id' =>$admin_id);
           $data['total_assigned_sd'] = $this->booking_model->total_assigned_sd($conditions); 
        }
        if($role!=6 && $role!=1)
        {
            $conditions = array();
            $data['total_assigned_sd'] = $this->booking_model->total_assigned_sd($conditions);
        }
        //echo "<pre>"; print_r($data['total_assigned_sd']); die;
        $this->load->view('dashboard',$data);
    }

    function logout()
    { 
        addlog('Loggout From Account');
        unset($_SESSION);
        unset($_SESSION['store']);
        unset($_SESSION['search__report_data']);
        unset($_SESSION['search__secondary_report_data']);
        unset($_SESSION['search__pihistory_data']);        
        $this->session->unset_userdata('admin');  
        session_destroy();
        redirect('/');
    } 
}