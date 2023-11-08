<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}

	function GetProductsAttributes(){
		$this->db->select('pur_product_attributes.*'); 
	    $this->db->from('pur_product_attributes');   
	    $this->db->order_by('pur_product_attributes.name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}


	
	function GetProducts(){
		$this->db->select('pur_products.*');
		$this->db->select('pur_category.category_name');
	    $this->db->from('pur_products');  
	    $this->db->join('pur_category','pur_category.id = pur_products.category_id');  
	    $this->db->order_by('pur_products.product_name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function AddProduct($insertdata)
	{
		//$this->db->insert('pur_products',$insertdata);
		//echo $this->db->last_query();die;
		return $this->db->insert('pur_products',$insertdata);
	}	 

	public function updateStatus($id, $status,$table) { 
        $status = ($status == '1') ? '0' : '1';
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        if ($this->db->update($table)) {
            return true;
        } else {
            return false;
        }
    }

    function GetProductByProductId($product_id){
		$this->db->select('*');
	    $this->db->from('pur_products');  
	    $this->db->where('id',$product_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}

	function UpdateProduct($update_data,$condition) 
	{
      	$this->db->where($condition);
    	return $this->db->update('pur_products', $update_data);
      //echo $this->db->last_query(); die;
  	}
}
