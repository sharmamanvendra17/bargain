<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distributor_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 

	function GetSuppliersList($condition){
		$this->db->select('pur_suppliers.*');
	    $this->db->from('pur_suppliers'); 
	    $this->db->select('city.name as city_name'); 
	    $this->db->where($condition);    
	    $this->db->join('city', 'city.id = pur_suppliers.city_id','left');
	    $this->db->order_by('name','ASC');   
	    $query = $this->db->get();  
	    return $query->result_array();
	}

	function GetDistributorsmaker($condition){
		$this->db->select(array('pur_suppliers.id','pur_suppliers.name','pur_suppliers.mobile','pur_suppliers.address','pur_suppliers.city_id','pur_suppliers.state_id','pur_suppliers.zipcode','pur_suppliers.gst_no','pur_suppliers.vendor_id','states.name as state_name','city.name as city_name','pur_vendors.name as vendor_name','c.name as vendor_city_name'));
	    $this->db->from('pur_suppliers');   
	    $this->db->join('states', 'states.id = pur_suppliers.state_id','left');
	    $this->db->join('city', 'city.id = pur_suppliers.city_id','left');
	    $this->db->join('pur_vendors', 'pur_vendors.id = pur_suppliers.vendor_id','left');
	    $this->db->join('city c', 'c.id = pur_vendors.city_id','left');
	    $this->db->where($condition);  
	    $this->db->order_by('pur_suppliers.name','ASC');  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	} 

	function GetDistributors(){
		$this->db->select(array('pur_suppliers.id','pur_suppliers.name','pur_suppliers.mobile','pur_suppliers.address','pur_suppliers.city_id','pur_suppliers.state_id','pur_suppliers.zipcode','pur_suppliers.gst_no','pur_suppliers.vendor_id','states.name as state_name','city.name as city_name','pur_vendors.name as vendor_name','c.name as vendor_city_name'));
	    $this->db->from('pur_suppliers');   
	    $this->db->join('states', 'states.id = pur_suppliers.state_id','left');
	    $this->db->join('city', 'city.id = pur_suppliers.city_id','left');
	    $this->db->join('pur_vendors', 'pur_vendors.id = pur_suppliers.vendor_id','left');
	    $this->db->join('city c', 'c.id = pur_vendors.city_id','left');
	    $this->db->order_by('pur_suppliers.name','ASC');  
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
	    $this->db->from('pur_suppliers');   
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
		return $this->db->insert('pur_suppliers',$insertdata);
	}	 
	function DeleteDistributor($condition){
	  $this->db->where($condition);
	  return $this->db->delete('pur_suppliers');
	}

	function GetDistributorbyId($vendor_id){
		$this->db->select(array('pur_suppliers.id','pur_suppliers.email','pur_suppliers.name','pur_suppliers.mobile','pur_suppliers.address','pur_suppliers.city_id','pur_suppliers.state_id','pur_suppliers.zipcode','pur_suppliers.gst_no','pur_suppliers.vendor_id','states.name as state_name','city.name as city_name','pur_vendors.name as vendor_name'));
	    $this->db->from('pur_suppliers');   
	    $this->db->join('states', 'states.id = pur_suppliers.state_id','left');
	    $this->db->join('city', 'city.id = pur_suppliers.city_id','left');
	    $this->db->join('pur_vendors', 'pur_vendors.id = pur_suppliers.vendor_id','left');
	    $this->db->where('pur_suppliers.id',$vendor_id);  
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
      return $this->db->update('pur_suppliers', $update_data);
      echo $this->db->last_query(); die;
    }

    function GetDistributorsbystate($condition){
		$this->db->select(array('pur_suppliers.id as distributor_id','pur_suppliers.name','pur_suppliers.mobile','pur_suppliers.address','pur_suppliers.city_id','pur_suppliers.state_id','pur_suppliers.zipcode','pur_suppliers.gst_no','pur_suppliers.vendor_id','states.name as state_name','city.name as city_name','pur_vendors.name as vendor_name','c.name as vendor_city_name'));
		$this->db->select('pur_vendors.id');
	    $this->db->from('pur_suppliers');   
	    $this->db->join('states', 'states.id = pur_suppliers.state_id','left');
	    $this->db->join('city', 'city.id = pur_suppliers.city_id','left');
	    $this->db->join('pur_vendors', 'find_in_set ( pur_vendors.id ,  pur_suppliers.vendor_id ) ','left');
	    $this->db->join('city c', 'c.id = pur_vendors.city_id','left'); 
	    if(isset($condition['state_ids']))
	    {
	    	$state_ids = $condition['state_ids'];
	    	$this->db->where("pur_suppliers.state_id IN ($state_ids)");
	    	unset($condition['state_ids']);
	    }
	    if(isset($condition['pur_suppliers.vendor_id']) && !empty($condition['pur_suppliers.vendor_id']))
	    {
	    	$vendor_id = $condition['pur_suppliers.vendor_id']; 
	    	$this->db->where("( find_in_set ( $vendor_id, pur_suppliers.vendor_id ) <> 0)"); 
	    	unset($condition['pur_suppliers.vendor_id']);
	    }

	    if($condition && count($condition))
	    {
	    	$this->db->where($condition);
	    } 
	    $this->db->order_by('pur_suppliers.name','ASC');  
	    $query = $this->db->get();  
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	} 



	function GetDistributorsbystatemakers($condition){
		$this->db->select(array('pur_suppliers.id as distributor_id','pur_suppliers.name','pur_suppliers.mobile','pur_suppliers.address','pur_suppliers.city_id','pur_suppliers.state_id','pur_suppliers.zipcode','pur_suppliers.gst_no','pur_suppliers.vendor_id','states.name as state_name','city.name as city_name','pur_vendors.name as vendor_name','c.name as vendor_city_name'));
		$this->db->select('pur_vendors.id');
	    $this->db->from('pur_suppliers');   
	    $this->db->join('states', 'states.id = pur_suppliers.state_id','left');
	    $this->db->join('city', 'city.id = pur_suppliers.city_id','left');
	    $this->db->join('pur_vendors', 'find_in_set ( pur_vendors.id ,  pur_suppliers.vendor_id ) ','left');
	    $this->db->join('city c', 'c.id = pur_vendors.city_id','left'); 
	     
	    if(isset($condition['pur_suppliers.vendor_id']) && !empty($condition['pur_suppliers.vendor_id']))
	    {
	    	$vendor_id = $condition['pur_suppliers.vendor_id']; 
	    	$this->db->where("( find_in_set ( $vendor_id, pur_suppliers.vendor_id ) <> 0)"); 
	    	unset($condition['pur_suppliers.vendor_id']);
	    }

	    if($condition && count($condition))
	    {
	    	$this->db->where($condition);
	    } 
	    $this->db->order_by('pur_suppliers.name','ASC');  
	    $query = $this->db->get();  
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	}

	function GetDistributorslist($condition){
		$this->db->select('pur_suppliers.*');
		$this->db->select('city.name as city_name');		
	    $this->db->from('pur_suppliers');  
	    $this->db->where($condition);
	    $this->db->join('states', 'states.id = pur_suppliers.state_id','left');
	    $this->db->join('city', 'city.id = pur_suppliers.city_id','left');
	    $this->db->order_by('pur_suppliers.name','ASC');  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();  
	      return $row;  
	    }
	} 

	function MakerDistributors($condition){ 
		$this->db->select('count(id) as total_distributor');
	    $this->db->from('pur_suppliers');   
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
		$this->db->select('pur_vendors.*');
	    $this->db->from('pur_vendors'); 
	    $this->db->select('city.name as city_name'); 
	    $this->db->where($condition);   
	    $this->db->join('admin', 'find_in_set ( admin.state_id ,  pur_vendors.state_id ) ','left');
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');
	    $this->db->order_by('name','ASC');  
	    $this->db->group_by('pur_vendors.id');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->result_array();
	}


	function GetSuppliersbystate(){

		$admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id = $admin_info['id']; 
        $state_id = $admin_info['state_id'];  

		$this->db->select('pur_vendors.*');
	    $this->db->from('pur_vendors'); 
	    $this->db->select('city.name as city_name');   
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');
	    if($state_id)
	    	$this->db->where("pur_vendors.state_id IN ($state_id)");
	    $this->db->order_by('name','ASC');  
	    $this->db->group_by('pur_vendors.id');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->result_array();
	}
	
}