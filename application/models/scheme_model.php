<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheme_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 

	function GetSchemes($condition){

		$this->db->select('scheme.*');
		$this->db->select('states.name as state_name');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');
	    $this->db->from('scheme');   
	    $this->db->join('states', 'states.id = scheme.scheme_state','left');
	    $this->db->join('brands', 'brands.id = scheme.brand_id','left');
	    $this->db->join('category', 'category.id = scheme.category_id','left');
	    if($condition)
	   		$this->db->where($condition);
	    $this->db->order_by('scheme.id','DESC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    //echo "<pre>"; print_r($row); die;
	    return $row;  
	} 

	function AddScheme($insertdata)
	{
		return $this->db->insert('scheme',$insertdata);
	}

	function AddSchemeDetail($insertdata)
	{
		return $this->db->insert_batch('scheme_detail',$insertdata);
	}


	function GetSchemesDtails($condition){

		$this->db->select('scheme_detail.*'); 
		$this->db->from('scheme_detail');   
	    if($condition)
	   		$this->db->where($condition);
	    $this->db->order_by('scheme_detail.id','DESC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function UpdateSchemeDetail($updatedata,$condition)	
	{
		$this->db->where($condition);
    	 $this->db->update('scheme_detail', $updatedata); 
    	//echo $this->db->last_query(); die;
	}

	function deletedetail($condition){
	  $this->db->where($condition);
	  return $this->db->delete('scheme_detail');
	}


	function GetSchemesInfo($condition){

		$this->db->select('scheme.*'); 
		$this->db->from('scheme');   
	    if($condition)
	   		$this->db->where($condition);
	    $this->db->order_by('scheme.id','DESC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $row = $query->row_array();
	} 

	function UpdateScheme($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('scheme', $updatedata); 
    	//echo $this->db->last_query(); die;
	}

	public function updateStatus($id, $status) {
        $status = ($status == '1') ? '0' : '1';
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        if ($this->db->update('scheme')) {
            return true;
        } else {
            return false;
        }
    }


    function GetSchemesdata($condition){

		$this->db->select('scheme.*');
		$this->db->select('states.name as state_name');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');
	    $this->db->from('scheme');   
	    $this->db->join('states', 'states.id = scheme.scheme_state','left');
	    $this->db->join('brands', 'brands.id = scheme.brand_id','left');
	    $this->db->join('category', 'category.id = scheme.category_id','left');
	    if($condition)
	   		$this->db->where($condition);
	    $this->db->order_by('scheme.id','DESC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->row_array(); 
	    //echo "<pre>"; print_r($row); die;
	    return $row;  
	}


	function GetSchemeParties($condition){

		$this->db->select('sum(pi_sku_history.weight) as total_dispatch');
		$this->db->select('sum(pi_sku_history.net_weight) as total_net_dispatch');
		$this->db->select('pi_sku_history.party_id'); 
		$this->db->select('vendors.name as party_name'); 
	    $this->db->from('pi_sku_history');   
	    $this->db->join('pi_history', 'pi_history.id = pi_sku_history.pi_number','left');
	    $this->db->join('vendors', 'vendors.id = pi_sku_history.party_id','left'); 
	    if($condition)
	   		$this->db->where($condition);   
	   	$this->db->group_by('pi_sku_history.party_id');   
	   	$this->db->order_by('total_dispatch','DESC');   
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    //echo "<pre>"; print_r($row); die;
	    return $row;  
	} 
}