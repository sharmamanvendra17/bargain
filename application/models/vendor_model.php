<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 

	function GetUsersByState($states_ids){
		$this->db->select(array('vendors.id','vendors.name','vendors.mobile','vendors.address','vendors.city_id','vendors.state_id','vendors.zipcode','vendors.gst_no','vendors.employee_id','states.name as state_name','city.name as city_name','employee.name as employee_name'));
	    $this->db->from('vendors');   
	    $this->db->join('states', 'states.id = vendors.state_id','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');
	    $this->db->join('employee', 'employee.id = vendors.employee_id','left');
	    if($states_ids)
	    	$this->db->where("vendors.state_id IN ($states_ids)");
	    $this->db->order_by('vendors.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	}

	
	function GetUsers($condition = array()){
		$this->db->select(array('vendors.status','vendors.id','vendors.name','vendors.mobile','vendors.address','vendors.city_id','vendors.state_id','vendors.zipcode','vendors.gst_no','vendors.employee_id','states.name as state_name','city.name as city_name','admin.name as employee_name'));
	    $this->db->from('vendors');   
	    $this->db->join('states', 'states.id = vendors.state_id','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');
	    $this->db->join('admin', 'admin.id = vendors.employee_id','left');
	    $this->db->where($condition);
	    $this->db->order_by('vendors.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function GetCnfVendor($condition){
		$this->db->select(array('vendors.id','vendors.name','vendors.mobile','vendors.address','vendors.city_id','vendors.state_id','vendors.zipcode','vendors.gst_no','vendors.employee_id','states.name as state_name','city.name as city_name','admin.name as employee_name'));
	    $this->db->from('vendors');   
	    $this->db->join('states', 'states.id = vendors.state_id','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');
	    $this->db->join('admin', 'admin.id = vendors.employee_id','left');
	    $this->db->where($condition);
	    $this->db->order_by('vendors.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function GetVendorbyId($vendor_id){ 
		$this->db->select(array('vendors.other_info','vendors.invoice_prefix','vendors.bank_details','vendors.tax_included','vendors.freight_included','vendors.cnf','vendors.email','vendors.for_rate','vendors.id','vendors.name','vendors.mobile','vendors.address','vendors.city_id','vendors.state_id','vendors.zipcode','vendors.gst_no','vendors.employee_id','states.name as state_name','city.name as city_name','employee.name as employee_name'));
	    $this->db->from('vendors');   
	    $this->db->join('states', 'states.id = vendors.state_id','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');
	    $this->db->join('employee', 'employee.id = vendors.employee_id','left');
	    $this->db->where('vendors.id',$vendor_id);  
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
	    $this->db->from('vendors');   
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	} 


	function UpdateVendor($update_data,$condition){
      $this->db->where($condition);
      return $this->db->update('vendors', $update_data);
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

	function GetEmployees(){
		$this->db->select('*');
	    $this->db->from('employee');   
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function AddVendor($insertdata)
	{
		return $this->db->insert('vendors',$insertdata);
	}	 


	function DeleteVendor($condition){
	  $this->db->where($condition);
	  return $this->db->delete('vendors');
	}

	function GetUsersByids($vendors_ids){
		$this->db->select(array('vendors.id','vendors.name','vendors.mobile','vendors.address','vendors.city_id','vendors.state_id','vendors.zipcode','vendors.gst_no','vendors.employee_id','states.name as state_name','city.name as city_name','employee.name as employee_name'));
	    $this->db->from('vendors');   
	    $this->db->join('states', 'states.id = vendors.state_id','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');
	    $this->db->join('employee', 'employee.id = vendors.employee_id','left');
	    if($vendors_ids)
	    	$this->db->where("vendors.id IN ($vendors_ids)");
	    $this->db->order_by('vendors.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	}

	function GetUsersByVendor($vendors_id){ 

		$this->db->select('a1.mobile');
	    $this->db->from('admin');    
	    $this->db->join('admin a1', 'a1.id = admin.team_lead_id','left');
	    $this->db->where("FIND_IN_SET($vendors_id,admin.vendor_id) <> 0");  
	    $this->db->group_by('a1.mobile');    
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
		//echo "<pre>"; print_r($row); die;
		return $row;  
	}

	function GetUsersViewersshowactive(){ 

		$this->db->select('admin.mobile');
		$this->db->select('admin.name');
	    $this->db->from('admin');    
	    $this->db->where("role",5); 
	    $this->db->where("status",1);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
		//echo "<pre>"; print_r($row); die;
		return $row;  
	}

	function GetVendormobileshowwhatsapp($vendors_id){ 

		$this->db->select('admin.mobile');
		$this->db->select('admin.name');
	    $this->db->from('vendors');   
	    $this->db->join('admin', 'vendors.employee_id = admin.id','left');
	    $this->db->where("vendors.id",$vendors_id);   
	    $this->db->where("vendors.status",1);   
	    $this->db->where("admin.status",1);   
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
		//echo "<pre>"; print_r($row); die;
		return $row;  
	}


	function GetVendormobileshow($vendors_id){ 

		$this->db->select('admin.mobile');
		$this->db->select('admin.name');
	    $this->db->from('vendors');   
	    $this->db->join('admin', 'vendors.employee_id = admin.id','left');
	    $this->db->where("vendors.id",$vendors_id);   
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
		//echo "<pre>"; print_r($row); die;
		return $row;  
	}

	function GetUsersViewersshow(){ 

		$this->db->select('admin.mobile');
		$this->db->select('admin.name');
	    $this->db->from('admin');    
	    $this->db->where("role",5);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
		//echo "<pre>"; print_r($row); die;
		return $row;  
	}

	function GetVendormobile($vendors_id){ 

		$this->db->select('admin.mobile');
	    $this->db->from('vendors');   
	    $this->db->join('admin', 'vendors.employee_id = admin.id','left');
	    $this->db->where("vendors.id",$vendors_id);   
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->row_array(); 
		//echo "<pre>"; print_r($row); die;
		return $row;  
	}

	function GetUsersViewers(){ 

		$this->db->select('admin.mobile');
	    $this->db->from('admin');    
	    $this->db->where("role",5);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
		//echo "<pre>"; print_r($row); die;
		return $row;  
	}


	function GetStatesbyuser($user){
		$this->db->select('states.*');
	    $this->db->from('admin');   
	    $this->db->join("states","( find_in_set ( states.id , admin.state_id ) OR  admin.state_id is NULL OR  admin.state_id ='' "); 
	    $this->db->where("admin.id",$user);
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}


	function GetStatesbyuservendor($user){
		$this->db->select('DISTINCT(vendors.state_id) as id');
		$this->db->select('states.name');
	    $this->db->from('booking_booking');   
	    $this->db->join("vendors "," vendors.id = booking_booking.party_id"); 
	    $this->db->join("states "," states.id = vendors.state_id"); 
	    $this->db->where("booking_booking.admin_id",$user);
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}


	function GetStatesbyuservendorsecondary($user){
		$this->db->select('DISTINCT(vendors.state_id) as id');
		$this->db->select('states.name');
	    $this->db->from('secondary_booking');   
	    $this->db->join("vendors "," vendors.id = secondary_booking.supply_from"); 
	    $this->db->join("states "," states.id = vendors.state_id"); 
	    $this->db->where("secondary_booking.admin_id",$user);
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	public function updateStatus($id, $status) {
        $status = ($status == '1') ? '0' : '1';
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        if ($this->db->update('vendors')) {
            return true;
        } else {
            return false;
        }
    }


    function AddVendorTransaction($insertdata)
	{
		return $this->db->insert('vendors_transactions',$insertdata);
	}


	function CountUsersTransactions($condition){   		 
		$this->db->select('vendors_transactions.*');
	    $this->db->from('vendors_transactions'); 
	    $this->db->join('states', 'states.id = vendors_transactions.state_id','left');
	    $this->db->join('city', 'city.id = vendors_transactions.city_id','left');
	    $this->db->join('admin', 'admin.id = vendors_transactions.employee_id','left');
	    $this->db->where($condition);    
	    $query = $this->db->get(); 
	    ///echo $this->db->last_query(); die;
	   	return $query->num_rows();
	}

	function GetUsersTransactions($condition,$perPage=20, $pageNo=1){  
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;		 
		$this->db->select('vendors_transactions.*');
		$this->db->select('states.name as state_name');
		$this->db->select('city.name as city_name');
		$this->db->select('admin.name as employee_name'); 
		$this->db->select('a1.name as updated_by_name'); 
	    $this->db->from('vendors_transactions'); 
	    $this->db->join('states', 'states.id = vendors_transactions.state_id','left');
	    $this->db->join('city', 'city.id = vendors_transactions.city_id','left');
	    $this->db->join('admin', 'admin.id = vendors_transactions.employee_id','left');
	    $this->db->join('admin as a1', 'a1.id = vendors_transactions.updated_by','left');
	    $this->db->where($condition);  
	    $this->db->limit($perPage, $startFromRecord);  
        $this->db->order_by('vendors_transactions.id','DESC');  
	    $query = $this->db->get(); 
	   	return $row = $query->result_array();;
	}	 
}