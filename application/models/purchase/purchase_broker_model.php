<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_broker_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 

	function GetBrokers(){
		$this->db->select(array('pur_brokers.id','pur_brokers.name','pur_brokers.mobile','pur_brokers.address','pur_brokers.city_id','pur_brokers.state_id','pur_brokers.zipcode','pur_brokers.pan_card','states.name as state_name','city.name as city_name'));
	    $this->db->from('pur_brokers');   
	    $this->db->join('states', 'states.id = pur_brokers.state_id','left');
	    $this->db->join('city', 'city.id = pur_brokers.city_id','left');
	    $this->db->order_by('pur_brokers.name','ASC');  
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
		$this->db->select(array('pur_brokers.id','pur_brokers.name','pur_brokers.mobile','pur_brokers.address','pur_brokers.city_id','pur_brokers.state_id','pur_brokers.zipcode','pur_brokers.pan_card','states.name as state_name','city.name as city_name'));
	    $this->db->from('pur_brokers');   
	    $this->db->join('states', 'states.id = pur_brokers.state_id','left');
	    $this->db->join('city', 'city.id = pur_brokers.city_id','left'); 
	    $this->db->where('pur_brokers.id',$vendor_id);  
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
	    $this->db->from('pur_brokers');   
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
      return $this->db->update('pur_brokers', $update_data);
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
		$this->db->insert('pur_brokers',$insertdata);
		return $this->db->insert_id();
	}	 

	function AddBrokerageRates($insertdata)
	{
		return $this->db->insert_batch('pur_brokerage_rate',$insertdata); 
	}

	function DeleteBroker($condition){
	  $this->db->where($condition);
	  return $this->db->delete('pur_brokers');
	}

	function GetBrokerbrokerage_rate($condition){
		$this->db->select('*');
	    $this->db->from('pur_brokerage_rate'); 
	    $this->db->where($condition);   
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die; 
	    $result = array();

	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      foreach ($row as $key => $value) {
	      	$result[$value['category_id']] =  $value['brokerage_rate'];
	      }
	    }
	    return $result;  
	}

	function UpdateBrokerageRates($update_data,$condition){
      $this->db->where($condition);
      return $this->db->update('pur_brokerage_rate', $update_data);
      echo $this->db->last_query(); die;
    }

    function GetBrokerbrokerage($condition){
		$this->db->select('brokerage_rate');
	    $this->db->from('pur_brokerage_rate'); 
	    $this->db->where($condition);   
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die; 
	    return $row = $query->row_array(); ;  
	}
}