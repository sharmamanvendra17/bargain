<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	  

	function GetAllEmployee(){
		$this->db->select('*');
	    $this->db->from('employee');  
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function GetEmployeeinfo($condition){
		$this->db->select('*');
	    $this->db->from('employee'); 
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}

	function UpdateEmployee($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('employee', $updatedata);
	} 

	function AddEmployee($insertdata)
	{
		return $this->db->insert('employee',$insertdata);
	}

	function DeleteEmployee($condition){
	  $this->db->where($condition);
	  return $this->db->delete('employee');
	}
}