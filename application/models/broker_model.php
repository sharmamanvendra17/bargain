<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Broker_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 

	function GetBrokers(){
		$this->db->select(array('brokers.id','brokers.name','brokers.mobile','brokers.address','brokers.city_id','brokers.state_id','brokers.zipcode','brokers.pan_card','states.name as state_name','city.name as city_name'));
	    $this->db->from('brokers');   
	    $this->db->join('states', 'states.id = brokers.state_id','left');
	    $this->db->join('city', 'city.id = brokers.city_id','left');
	    $this->db->order_by('brokers.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function GetBrokerbyId($vendor_id){
		$this->db->select(array('brokers.id','brokers.name','brokers.mobile','brokers.address','brokers.city_id','brokers.state_id','brokers.zipcode','brokers.pan_card','states.name as state_name','city.name as city_name'));
	    $this->db->from('brokers');   
	    $this->db->join('states', 'states.id = brokers.state_id','left');
	    $this->db->join('city', 'city.id = brokers.city_id','left'); 
	    $this->db->where('brokers.id',$vendor_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function GetUserbyId($condition){
		$this->db->select('*');
	    $this->db->from('brokers');   
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	} 


	function UpdateBroker($update_data,$condition){
      $this->db->where($condition);
      return $this->db->update('brokers', $update_data);
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

	 

	function AddBroker($insertdata)
	{
		return $this->db->insert('brokers',$insertdata);
	}	 


	function DeleteBroker($condition){
	  $this->db->where($condition);
	  return $this->db->delete('brokers');
	}
}