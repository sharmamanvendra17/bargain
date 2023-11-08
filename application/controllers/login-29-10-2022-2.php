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
                        if($result['role']==4) 
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