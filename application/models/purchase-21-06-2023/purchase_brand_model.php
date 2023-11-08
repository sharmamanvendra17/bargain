<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_brand_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	  

	function GetAllBrand(){
		$this->db->select('*');
	    $this->db->from('pur_brands');  
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	/* ======= end  *====================== */

	function GetBrandinfo($condition){
		$this->db->select('*');
	    $this->db->from('pur_brands'); 
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}

	function UpdateBrand($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('pur_brands', $updatedata);
	} 

	function AddBrand($insertdata)
	{
		return $this->db->insert('pur_brands',$insertdata);
	}

	function DeleteBrand($condition){
	  $this->db->where($condition);
	  return $this->db->delete('pur_brands');
	}
}