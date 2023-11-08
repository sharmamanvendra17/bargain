<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}


	function joiningmonth($logindata) {
		$this->db->select("TIMESTAMPDIFF(MONTH, `joining_date`, now()) as month");
	    $this->db->from('admin');  
	    $this->db->where($logindata);  
	    $query = $this->db->get(); 
	    //return $query->num_rows();
	    //echo $this->db->last_query(); die;
		$row = $query->row_array(); 
		if($query->num_rows())
			return $row['month'];  
		else
			return '';
	}

	function check_user_distance($condition) {
		$this->db->select('id');
		$this->db->select('date(tracking_date) as created_at');
	    $this->db->from('employee_locations');  
	    $this->db->where($condition);  
	    $this->db->order_by('tracking_date','DESC');  
	    $query = $this->db->get();   
	    return $query->row_array();  
	}

	function userlogin($logindata) {
		$this->db->select('*');
	    $this->db->from('admin');  
	    $this->db->where($logindata);  
	    $query = $this->db->get(); 
	    //return $query->num_rows();
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}

	function UpdateUser($update_data,$condition) 
	{
      	$this->db->where($condition);
    	return $this->db->update('admin', $update_data);
      //echo $this->db->last_query(); die;
  	}


  	function userpersonalogin($logindata) {
		$this->db->select('*');
	    $this->db->from('admin');  
	    if(isset($logindata['parent_user_id']))
	    {
		    $parent_user_id =  $logindata['parent_user_id'];
		    unset($logindata['parent_user_id']); 
		    $this->db->where("find_in_set ( $parent_user_id, admin.persona_user )");
		}
	    $this->db->where($logindata);	    
	    $query = $this->db->get(); 
	    //return $query->num_rows();
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}
}