<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 


	function CountMessages($condition){  
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id']; 
		$this->db->select('whatsapp_messages.id'); 
	    $this->db->from('whatsapp_messages'); 

	    if(isset($condition['both_filter']))
	    {
	    	$this->db->where("(whatsapp_messages.mobile_number like '%".$condition['whatsapp_messages.mobile_number']."' OR whatsapp_messages.mobile_number like '%".$condition['party_mobile_number']."')"); 
	    	unset($condition['whatsapp_messages.mobile_number']);
	    	unset($condition['party_mobile_number']);
	    	unset($condition['both_filter']);        
	    } 
	    $this->db->where($condition);          
        $this->db->order_by('whatsapp_messages.id','DESC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	   	return $query->num_rows();
	}

	function GetMessages($condition,$perPage=20, $pageNo=1){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];		 
		$this->db->select('whatsapp_messages.*');  
		$this->db->select('admin.name as employee_name');  
		$this->db->select('vendors.name as vendor_name');  
	    $this->db->from('whatsapp_messages');   
	    $this->db->join('admin', 'admin.mobile = whatsapp_messages.mobile_number','left');
	    $this->db->join('vendors', 'vendors.mobile = whatsapp_messages.mobile_number','left');
	    if(isset($condition['both_filter']))
	    {
	    	$this->db->where("(whatsapp_messages.mobile_number like '%".$condition['whatsapp_messages.mobile_number']."' OR whatsapp_messages.mobile_number like '%".$condition['party_mobile_number']."')"); 
	    	unset($condition['whatsapp_messages.mobile_number']);
	    	unset($condition['party_mobile_number']);
	    	unset($condition['both_filter']);        
	    } 
	    $this->db->where($condition); 
	    $this->db->limit($perPage, $startFromRecord); 
        $this->db->order_by('whatsapp_messages.receiving_time','DESC'); 
	    $query = $this->db->get(); 
	   	//echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function GetMessageInfo($condition){ 
		$this->db->select('whatsapp_messages.*'); 
		$this->db->from('whatsapp_messages');   
		$this->db->where($condition);  
		$query = $this->db->get(); 
		return $query->row_array();
	}
}