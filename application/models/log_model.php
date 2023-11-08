<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	function addlog($insertdata)
	{
		return $this->db->insert('log_history',$insertdata);
		//echo $this->db->last_query(); die;
	}
}