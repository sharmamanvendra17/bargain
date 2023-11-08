<?php

class Dynamic {

    private $CI;

    public function __construct() { 
        $this->CI = &get_instance();
        //echo $this->CI->uri->segment(1); die;
    }

    public function alreadyLogIn() {
        $application_session = $this->CI->session->userdata('admin');
        if (isset($application_session) && !empty($application_session)) {
            redirect(base_url('/dashboard'));
            //echo $this->uri->segment(1); die;
        } 
    } 

    public function alreadynotLogIn() {

        $application_session = $this->CI->session->userdata('admin');  
        //print_r($application_session); die;
        if ($application_session) {
            $username = $application_session['username'];
            //$role = $application_session['role'];
            $CI =& get_instance();   
            $CI->db->select('roles_privillages.privillage');  
            $CI->db->from('admin'); 
            $CI->db->join('roles_privillages', 'roles_privillages.role_id =  admin.role'); 
            $CI->db->where('admin.username',$username); 
            //echo $CI->db->last_query(); die;
            $query = $CI->db->get();  
            $user_privillages = $query->row_array(); 
            if($user_privillages && $user_privillages['privillage']!='all')
            {
                $privillages = explode(',', $user_privillages['privillage']);
                //print_r($privillages);
                $page_access =  $this->CI->uri->segment(1);
                // die;
                if(!in_array($page_access, $privillages) )
                { 
                    redirect(base_url('/dashboard'));
                } 
            }

        }
        else
        {
            redirect(base_url(''));
        }
    }

}
