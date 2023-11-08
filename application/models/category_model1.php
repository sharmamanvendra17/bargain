<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	  


	function CheckCategory($condition){
		$this->db->select('*'); 
		 $this->db->from('category'); 
	    $this->db->where($condition);
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->num_rows(); 
	}



	function GetCategories(){
		$this->db->select(array('category.id','category.brand_id','category.category_name','category.is_enable','category.sort_order','category.hsn','category.product_href','brands.name as brand_name'));
	    $this->db->from('category'); 
	    $this->db->join('brands', 'brands.id = category.brand_id','left');
	    $this->db->order_by('category.category_name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function GetCategories1(){
		//$this->db->select(array('category.brand_id','category.category_name','category.is_enable','category.sort_order','category.hsn','category.product_href', GROUP_CONCAT('category.id' SEPARATOR ",")));
		$this->db->select(array('GROUP_CONCAT(category.id SEPARATOR ",") as category_ids','GROUP_CONCAT(category.brand_id SEPARATOR ",") as brand_id','category.category_name','category.alias_name'));
	    $this->db->from('category');
	    //$this->db->group_by('category.category_name');
	    $this->db->group_by('category.alias_name');
	    $this->db->order_by('category.sort_order');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}	

	function GetCategoryByCategoryId($category_id){
		$this->db->select('*');
	    $this->db->from('category');  
	    $this->db->where('id',$category_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}

	function GetAllPackaging(){
		$this->db->select('*');
	    $this->db->from('package');    
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function GetProducts(){ 
		$this->db->select(array('products.id','products.category_id','products.name','products.is_enable','products.sku','products.name','products.product_type','products.loose_rate','brands.name as brand_name','category.category_name as category_name'));
	    $this->db->from('products'); 
	    $this->db->join('category', 'category.id = products.category_id','left');  
	    $this->db->join('brands', 'brands.id = category.brand_id','left');     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function GetProductsbycategpry_id($category_id){ 
		$this->db->select(array('products.id','products.category_id','products.name','products.is_enable','products.sku','products.name','products.loose_rate','products.product_type','brands.name as brand_name','category.category_name as category_name'));
	    $this->db->from('products'); 
	    $this->db->join('category', 'category.id = products.category_id','left');  
	    $this->db->join('brands', 'brands.id = category.brand_id','left'); 
	    $this->db->where('products.category_id',$category_id);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function GetProductByProductId($product_id){ 
		$this->db->select(array('products.for_rate','products.id','products.category_id','products.name','products.is_enable','products.sku','products.name','products.loose_rate','products.weight','products.product_type','brands.id as brand_id','brands.name as brand_name','category.category_name as category_name'));
	    $this->db->from('products'); 
	    $this->db->join('category', 'category.id = products.category_id','left');  
	    $this->db->join('brands', 'brands.id = category.brand_id','left'); 
	    $this->db->where('products.id',$product_id);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	}
	
	function Productinfobyid($product_id){ 
		$this->db->select('*');
	    $this->db->from('products'); 
	    $this->db->where('id',$product_id);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	}


	function CheckPackagingEnable($packing_id) {
		$this->db->select('*');
	    $this->db->from('product_packing');  
	    $this->db->where('id',$packing_id);  
	    $query = $this->db->get(); 
	    return $query->num_rows();
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}

	 


	function UpdateCategory($update_data,$condition) 
	{
      	$this->db->where($condition);
    	return $this->db->update('category', $update_data);
      //echo $this->db->last_query(); die;
  	}


  	function UpdatePackaging($update_data,$condition) 
	{
      	$this->db->where($condition);
    	return $this->db->update('product_packing', $update_data);
      //echo $this->db->last_query(); die;
  	}

  	function GetProductPackagingByProductId($product_id){
		$this->db->select('*');
	    $this->db->from('product_packing'); 
	    $this->db->where('product_id',$product_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}	


	function GetProductPackagingToenable($product_id){
		$this->db->select('*');
	    $this->db->from('product_packing'); 
	    $this->db->where('product_id',$product_id);
	    $this->db->where('is_enable',0); 	  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	}

	function AddCategory($insertdata)
	{
		return $this->db->insert('category',$insertdata);
	}	 


	function DeleteCategory($condition){
	  $this->db->where($condition);
	  return $this->db->delete('category');
	}

	function GetCategory($condition){
		$this->db->select('*');
	    $this->db->from('category'); 
	    $this->db->where($condition); 
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
		return $this->db->insert('products',$insertdata);
	}

	function UpdateProduct($update_data,$condition) 
	{
      	$this->db->where($condition);
    	return $this->db->update('products', $update_data);
      	//echo $this->db->last_query(); die;
  	}

  	function DeleteProduct($condition){
	  $this->db->where($condition);
	  return $this->db->delete('products');
	}
}