<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advance_model extends CI_Model {	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database(); 
	} 

	function CountAdvance($condition){   
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];		 
		$this->db->select('party_advance.id');   
	    $this->db->from('party_advance');  
	    $this->db->where($condition);  
	    $query = $this->db->get();  
	    return $query->num_rows();  
	}

	function GetAdvanceList($condition,$perPage=20, $pageNo=1){  
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];		 
		$this->db->select('party_advance.*');  
		$this->db->select('admin.name as added_by_name');  
		$this->db->select('a1.name as verified_by_name');
		$this->db->select('city.name as city_name');  
		$this->db->select('vendors.name as party_name');  
		$this->db->select('company.name as company_name');  
	    $this->db->from('party_advance');   
	    $this->db->join('admin', 'admin.id = party_advance.added_by','left');
	    $this->db->join('admin a1', 'a1.id = party_advance.verified_by','left');
	    $this->db->join('vendors', 'vendors.id = party_advance.party_id','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');	
	    $this->db->join('company', 'company.id = party_advance.company_id','left');     
	    $this->db->where($condition); 
	    $this->db->limit($perPage, $startFromRecord); 
        $this->db->order_by('party_advance.created_at','DESC'); 
	    $query = $this->db->get(); 
	   	//echo $this->db->last_query(); die;
	    return $row = $query->result_array();
	}


	function AddAdvance($insertdata)
	{
		$this->db->insert('party_advance',$insertdata);
		return $this->db->insert_id();
	}	


	function GetAdvanceInfo($condition){  
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];		 
		$this->db->select('party_advance.*');  
		$this->db->select('admin.name as added_by_name');  
		$this->db->select('a1.name as verified_by_name');
		$this->db->select('city.name as city_name');  
		$this->db->select('vendors.name as party_name');  
		$this->db->select('company.name as company_name');  
	    $this->db->from('party_advance');   
	    $this->db->join('admin', 'admin.id = party_advance.added_by','left');
	    $this->db->join('admin a1', 'a1.id = party_advance.verified_by','left');
	    $this->db->join('vendors', 'vendors.id = party_advance.party_id','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');	
	    $this->db->join('company', 'company.id = party_advance.company_id','left');     
	    $this->db->where($condition); 
	    $query = $this->db->get();  
	    return $row = $query->row_array();
	} 
}