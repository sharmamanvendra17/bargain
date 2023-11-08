<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rate_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}


	function GetMasterRates(){
		$this->db->select(array('rate_master.*','brands.name as brand_name','category.category_name as category_name','admin.name as admin_name'));

	    $this->db->from('rate_master');   
	    $this->db->join('category', 'category.id = rate_master.category_id','left');  
	    $this->db->join('brands', 'brands.id = rate_master.brand_id','left'); 
	    $this->db->join('admin', 'admin.id = rate_master.created_by','left'); 
	    $this->db->order_by('id','DESC'); 
	    //$this->db->order_by('category_id','DESC');     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function AddRates($insertdata)
	{
		return $this->db->insert('rate_master',$insertdata);
	}	
	function UpdateCategoryRates($update_data,$condition) 
	{
      	$this->db->where($condition);
    	return $this->db->update('category', $update_data); 
    	//echo $this->db->last_query();die;
  	}


  	function AddcnfRates($insertdata)
	{
		return $this->db->insert_batch('cnf_rate_master',$insertdata);
	}

	function AddcnfMasterRates($insertdata)
	{
		return $this->db->insert_batch('party_rate_master',$insertdata);
	}


	 

	function rate_master_packaging(){
		$this->db->select('*');
	    $this->db->from('rate_master_packaging');    
	    $query = $this->db->get();  
	    return $row = $query->result_array();  
	}
}