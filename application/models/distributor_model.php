<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distributor_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 

	function GetDistributorsmaker($condition){
		$this->db->select(array('distributors.id','distributors.name','distributors.mobile','distributors.address','distributors.city_id','distributors.state_id','distributors.zipcode','distributors.gst_no','distributors.vendor_id','states.name as state_name','city.name as city_name','vendors.name as vendor_name','c.name as vendor_city_name'));
	    $this->db->from('distributors');   
	    $this->db->join('states', 'states.id = distributors.state_id','left');
	    $this->db->join('city', 'city.id = distributors.city_id','left');
	    $this->db->join('vendors', 'vendors.id = distributors.vendor_id','left');
	    $this->db->join('city c', 'c.id = vendors.city_id','left');
	    $this->db->where($condition);  
	    $this->db->order_by('distributors.name','ASC');  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	} 

	function GetDistributors(){
		$this->db->select(array('distributors.id','distributors.name','distributors.mobile','distributors.address','distributors.city_id','distributors.state_id','distributors.zipcode','distributors.gst_no','distributors.vendor_id','states.name as state_name','city.name as city_name','vendors.name as vendor_name','c.name as vendor_city_name'));
	    $this->db->from('distributors');   
	    $this->db->join('states', 'states.id = distributors.state_id','left');
	    $this->db->join('city', 'city.id = distributors.city_id','left');
	    $this->db->join('vendors', 'vendors.id = distributors.vendor_id','left');
	    $this->db->join('city c', 'c.id = vendors.city_id','left');
	    $this->db->order_by('distributors.name','ASC');  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
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
	function GetUserbyId($condition){
		$this->db->select('*');
	    $this->db->from('distributors');   
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	} 

	function AddDistributor($insertdata)
	{
		return $this->db->insert('distributors',$insertdata);
	}	 
	function DeleteDistributor($condition){
	  $this->db->where($condition);
	  return $this->db->delete('distributors');
	}

	function GetDistributorbyId($vendor_id){
		$this->db->select(array('distributors.id','distributors.email','distributors.name','distributors.mobile','distributors.address','distributors.city_id','distributors.state_id','distributors.zipcode','distributors.gst_no','distributors.vendor_id','states.name as state_name','city.name as city_name','vendors.name as vendor_name'));
	    $this->db->from('distributors');   
	    $this->db->join('states', 'states.id = distributors.state_id','left');
	    $this->db->join('city', 'city.id = distributors.city_id','left');
	    $this->db->join('vendors', 'vendors.id = distributors.vendor_id','left');
	    $this->db->where('distributors.id',$vendor_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function UpdateDistributor($update_data,$condition){
      $this->db->where($condition);
      return $this->db->update('distributors', $update_data);
      echo $this->db->last_query(); die;
    }

    function GetDistributorsbystate($condition){
		$this->db->select(array('distributors.id as distributor_id','distributors.name','distributors.mobile','distributors.address','distributors.city_id','distributors.state_id','distributors.zipcode','distributors.gst_no','distributors.vendor_id','states.name as state_name','city.name as city_name','vendors.name as vendor_name','c.name as vendor_city_name'));
		$this->db->select('vendors.id');
	    $this->db->from('distributors');   
	    $this->db->join('states', 'states.id = distributors.state_id','left');
	    $this->db->join('city', 'city.id = distributors.city_id','left');
	    $this->db->join('vendors', 'find_in_set ( vendors.id ,  distributors.vendor_id ) ','left');
	    $this->db->join('city c', 'c.id = vendors.city_id','left'); 
	    if(isset($condition['state_ids']))
	    {
	    	$state_ids = $condition['state_ids'];
	    	$this->db->where("distributors.state_id IN ($state_ids)");
	    	unset($condition['state_ids']);
	    }
	    if(isset($condition['distributors.vendor_id']) && !empty($condition['distributors.vendor_id']))
	    {
	    	$vendor_id = $condition['distributors.vendor_id']; 
	    	$this->db->where("( find_in_set ( $vendor_id, distributors.vendor_id ) <> 0)"); 
	    	unset($condition['distributors.vendor_id']);
	    }

	    if($condition && count($condition))
	    {
	    	$this->db->where($condition);
	    } 
	    $this->db->order_by('distributors.name','ASC');  
	    $query = $this->db->get();  
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	} 



	function GetDistributorsbystatemakers($condition){
		$this->db->select(array('distributors.id as distributor_id','distributors.name','distributors.mobile','distributors.address','distributors.city_id','distributors.state_id','distributors.zipcode','distributors.gst_no','distributors.vendor_id','states.name as state_name','city.name as city_name','vendors.name as vendor_name','c.name as vendor_city_name'));
		$this->db->select('vendors.id');
	    $this->db->from('distributors');   
	    $this->db->join('states', 'states.id = distributors.state_id','left');
	    $this->db->join('city', 'city.id = distributors.city_id','left');
	    $this->db->join('vendors', 'find_in_set ( vendors.id ,  distributors.vendor_id ) ','left');
	    $this->db->join('city c', 'c.id = vendors.city_id','left'); 
	    if(isset($condition['distributors.state_id']))
	    {
	    	$state_ids = $condition['distributors.state_id'];
	    	$this->db->where("distributors.state_id IN ($state_ids)");
	    	unset($condition['distributors.state_id']);
	    }
	    if(isset($condition['distributors.vendor_id']) && !empty($condition['distributors.vendor_id']))
	    {
	    	$vendor_id = $condition['distributors.vendor_id']; 
	    	$this->db->where("( find_in_set ( $vendor_id, distributors.vendor_id ) <> 0)"); 
	    	unset($condition['distributors.vendor_id']);
	    }

	    if($condition && count($condition))
	    {
	    	$this->db->where($condition);
	    } 
	    $this->db->order_by('distributors.name','ASC');  
	    $query = $this->db->get();  
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	}

	function GetDistributorslist($condition){
		$this->db->select('distributors.*');
		$this->db->select('city.name as city_name');		
	    $this->db->from('distributors');  
	    $this->db->where($condition);
	    $this->db->join('states', 'states.id = distributors.state_id','left');
	    $this->db->join('city', 'city.id = distributors.city_id','left');
	    $this->db->order_by('distributors.name','ASC');  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	} 

	function MakerDistributors($condition){ 
		$this->db->select('count(id) as total_distributor');
	    $this->db->from('distributors');   
	    if(isset($condition['maker']))
	    	$this->db->where('maker_id',$condition['maker']); 
	    $query = $this->db->get();  
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();  
	      return $row;  
	    }
	}


	function GetSuppliers($condition){
		$this->db->select('vendors.*');
	    $this->db->from('vendors'); 
	    $this->db->select('city.name as city_name'); 
	    $this->db->where($condition);   
	    $this->db->join('admin', 'find_in_set ( admin.state_id ,  vendors.state_id ) ','left');
	    $this->db->join('city', 'city.id = vendors.city_id','left');
	    $this->db->order_by('name','ASC');  
	    $this->db->group_by('vendors.id');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->result_array();
	}


	function GetSuppliersbystate(){

		$admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $state_id = $admin_info['state_id'];  

		$this->db->select('vendors.*');
	    $this->db->from('vendors'); 
	    $this->db->select('city.name as city_name');   
	    $this->db->join('city', 'city.id = vendors.city_id','left');
	    if($state_id)
	    	$this->db->where("vendors.state_id IN ($state_id)");
	    $this->db->order_by('name','ASC');  
	    $this->db->group_by('vendors.id');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->result_array();
	}
	
}