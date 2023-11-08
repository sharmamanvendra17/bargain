<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_vendor_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 
 

	function GetUsersByState($states_ids){
		$this->db->select(array('pur_vendors.id','pur_vendors.name','pur_vendors.mobile','pur_vendors.address','pur_vendors.city_id','pur_vendors.state_id','pur_vendors.zipcode','pur_vendors.gst_no','pur_vendors.employee_id','states.name as state_name','city.name as city_name','employee.name as employee_name'));
	    $this->db->from('pur_vendors');   
	    $this->db->join('states', 'states.id = pur_vendors.state_id','left');
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');
	    $this->db->join('employee', 'employee.id = pur_vendors.employee_id','left');
	    if($states_ids)
	    	$this->db->where("pur_vendors.state_id IN ($states_ids)");
	    $this->db->order_by('pur_vendors.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 
	
	function GetUsers(){ 
		$this->db->select(array('pur_vendors.*'));
		$this->db->select(array('states.name as state_name','city.name as city_name'));
	    $this->db->from('pur_vendors');   
	    $this->db->join('states', 'states.id = pur_vendors.state_id','left');
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left'); 
	    $this->db->order_by('pur_vendors.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 
 

	function GetVendorbyId($vendor_id){
		$this->db->select(array('pur_vendors.email','pur_vendors.id','pur_vendors.name','pur_vendors.mobile','pur_vendors.address','pur_vendors.city_id','pur_vendors.state_id','pur_vendors.zipcode','pur_vendors.gst_no','states.name as state_name','city.name as city_name'));
	    $this->db->from('pur_vendors');   
	    $this->db->join('states', 'states.id = pur_vendors.state_id','left');
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');
	    $this->db->where('pur_vendors.id',$vendor_id);  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function GetUserbyId($condition){
		$this->db->select('*');
	    $this->db->from('pur_vendors');   
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	} 


	function UpdateVendor($update_data,$condition){
      $this->db->where($condition);
      return $this->db->update('pur_vendors', $update_data);
      echo $this->db->last_query(); die;
    }


    function GetStates(){
		$this->db->select('*');
	    $this->db->from('states');   
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function GetCity($condition){
		$this->db->select('*');
	    $this->db->from('city'); 
	    $this->db->where($condition);   
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	 

	function AddVendor($insertdata)
	{
		return $this->db->insert('pur_vendors',$insertdata);
	}	 


	function DeleteVendor($condition){
	  $this->db->where($condition);
	  return $this->db->delete('pur_vendors');
	} 
}