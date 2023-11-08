<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testmodel extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}

	function getallbookingupdate(){
		$this->db->select('booking_booking.id'); 
		$this->db->select('booking_booking.brand_id'); 
		$this->db->select('booking_booking.category_id'); 
		$this->db->select('booking_booking.party_id'); 
		$this->db->select('booking_booking.rate'); 
		$this->db->select('vendors.state_id'); 
		$this->db->from('booking_booking'); 
		$this->db->join('vendors','vendors.id = booking_booking.party_id','left'); 
	    $this->db->order_by('booking_booking.id','DESC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->result_array(); 
	}
}