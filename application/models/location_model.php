<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}

	function AddState($insertdata)
	{
		return $this->db->insert('states',$insertdata);
	}

	function GetStates(){  
		$this->db->select('*');
	    $this->db->from('states'); 
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function GetStateById($state_id){  
		$this->db->select('*');
	    $this->db->from('states'); 
	    $this->db->where('id',$state_id); 
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function UpdateState($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('states', $updatedata);
	}

	function DeleteState($condition){
	  $this->db->where($condition);
	  return $this->db->delete('states');
	}



	function AddDisctrict($insertdata)
	{
		return $this->db->insert('districts',$insertdata);
	}

	function GetDisctrict(){  
		$this->db->select(array('districts.id','districts.name','districts.state_id','states.name as state_name'));
	    $this->db->from('districts'); 
	    $this->db->join('states', 'states.id = districts.state_id','left'); 
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function GetDisctrictById($district_id){  
		$this->db->select(array('districts.id','districts.name','districts.state_id','states.name as state_name'));
	    $this->db->from('districts'); 
	    $this->db->join('states', 'states.id = districts.state_id','left'); 
	    $this->db->where('districts.id',$district_id); 
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function UpdateDisctrict($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('districts', $updatedata);
	}

	function DeleteDisctrict($condition){
	  $this->db->where($condition);
	  return $this->db->delete('districts');
	}





	function GetCities(){  
		$this->db->select(array('city.id','city.name','city.state_id','states.name as state_name'));
	    $this->db->from('city'); 
	    $this->db->join('states', 'states.id = city.state_id','left'); 
	    $this->db->order_by('city.name','ASC');
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function GetCityById($city_id){  
		$this->db->select(array('city.id','city.name','city.state_id','states.name as state_name'));
	    $this->db->from('city'); 
	    $this->db->join('states', 'states.id = city.state_id','left'); 
	    $this->db->where('city.id',$city_id); 
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function AddCity($insertdata)
	{
		return $this->db->insert('city',$insertdata);
	}

	function UpdateCity($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('city', $updatedata);
	}

	function DeleteCity($condition){
	  $this->db->where($condition);
	  return $this->db->delete('city');
	}
}