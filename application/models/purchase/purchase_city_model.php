<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_city_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 
	function AddCity($insertdata)
	{
		$this->db->insert('city',$insertdata);
		return $this->db->insert_id();
	}

	function GetCities($condition= array()){
		$this->db->select('city.*');
		$this->db->select('states.name as state_name');
	    $this->db->from('city');  
	    $this->db->join('states', 'states.id = city.state_id','left');
	    if($condition)
	    {
	    	$this->db->where($condition);  
	    }
	    $this->db->order_by('city.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}
	function GetStates($condition= array()){
		$this->db->select('states.*'); 
	    $this->db->from('states');   
	    if($condition)
	    {
	    	$this->db->where($condition);  
	    }
	    $this->db->order_by('states.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}	

	function GetCityInfo($city_id){
		$this->db->select('city.*');
		$this->db->select('states.name as state_name');
		$this->db->join('states', 'states.id = city.state_id','left');
	    $this->db->from('city');  
	    $this->db->where('city.id',$city_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->row_array(); 
	    return $row;  
	}
	function UpdateCity($update_data,$condition) 
	{
      	$this->db->where($condition);
    	return $this->db->update('city', $update_data);
      //echo $this->db->last_query(); die;
  	}
}