<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	

	function GetRateBooking($condition){
		$this->db->select('*'); 
		 $this->db->from('rate_master'); 
	    $this->db->where($condition);
	    $this->db->order_by('id','DESC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->row_array(); 
	}


	function GetRate($condition){
		$this->db->select('*'); 
		 $this->db->from('category'); 
	    $this->db->where($condition);
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->row_array(); 
	}


	function GetCategoryAlias(){
		$this->db->select(array('category_name','alias_name'));
	    $this->db->from('category');  
	    $this->db->group_by('category_name');  
	    $this->db->group_by('alias_name');
	    $this->db->order_by('category_name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die; 
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
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
		$this->db->select(array('category.status','category.is_ex_rate','category.tin_rate','category.product_price','category.id','category.brand_id','category.alias_name','category.category_name','category.is_enable','category.sort_order','category.hsn','category.product_href','brands.name as brand_name'));
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
		$this->db->select(array('products.status','products.packing_items_qty','products.id','products.category_id','products.name','products.is_enable','products.sku','products.name','products.product_type','products.loose_rate','brands.name as brand_name','category.category_name as category_name'));
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
		$this->db->select(array('products.weight as product_weight','products.packing_items_qty','products.packaging_type','products.packing_items','products.id','products.category_id','products.name','products.is_enable','products.sku','products.name','products.loose_rate','products.product_type','brands.name as brand_name','category.category_name as category_name'));
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

	function GetProductsbycategpry_idactive($category_id){ 
		$this->db->select(array('products.gross_weight','products.weight as product_weight','products.packing_items_qty','products.packaging_type','products.packing_items','products.id','products.category_id','products.name','products.is_enable','products.sku','products.name','products.loose_rate','products.product_type','brands.name as brand_name','category.category_name as category_name'));
	    $this->db->from('products'); 
	    $this->db->join('category', 'category.id = products.category_id','left');  
	    $this->db->join('brands', 'brands.id = category.brand_id','left'); 
	    $this->db->where('products.category_id',$category_id);
	    $this->db->where('products.status',1);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function GetProductByProductId($product_id){ 
		$this->db->select(array('products.packing_weight','products.gross_weight','products.packing_items','products.packing_items_qty','products.packaging_type','products.for_rate','products.id','products.category_id','products.name','products.is_enable','products.sku','products.name','products.loose_rate','products.weight','products.product_type','brands.id as brand_id','brands.name as brand_name','category.category_name as category_name'));
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
		//$this->db->insert('category',$insertdata);
		//echo $this->db->last_query();die;
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
	    $this->db->order_by('category.category_name','ASC'); 
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

	function Emtpytinratebyid($condition){ 
		$this->db->select('empty_tin_rates.*');
	    $this->db->from('empty_tin_rates'); 
	    $this->db->order_by('empty_tin_rates.id','DESC'); 
	    $this->db->where($condition);     
	    $query = $this->db->get();   
	    return $query->row_array(); ;  
	}

	function Emtpytinrates($condition){ 
		$this->db->select('*');
	    $this->db->from('empty_tin_rates'); 
	    $this->db->where($condition);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $response = array();
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      foreach ($row as $key => $value) {
	      	$response[$value['product_id']] = $value;
	      }	      
	    }
	    return $response;  
	}
	function AddEmptyRates($insertdata)
	{
		 $this->db->insert_batch('empty_tin_rates',$insertdata);
		 $last_query = $this->db->last_query();
		 	$date_file = date('d-m-Y');
	        $log_file = FCPATH."api-logs/query_logs".$date_file.'.log';
	        $log_file = fopen($log_file,"a");  
	        fwrite($log_file, PHP_EOL .'================================================================================='.PHP_EOL.date('H:i').' insert_query => '. PHP_EOL);  
	        fwrite($log_file, print_r($last_query, true)); 
		 return true;
	} 
	function DeleteEmptyRates($condition){
	  $this->db->where($condition);
	  return $this->db->delete('empty_tin_rates');
	}

	function GetCategorInfo($condition){
		$this->db->select('category.*');
		$this->db->select('brands.name as brand_name');
		$this->db->join('brands', 'brands.id = category.brand_id','left');
	    $this->db->from('category');  
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->row_array(); 
	    return $row;  
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


    function Emtpytinratesku($condition){ 
		$this->db->select('rate');
	    $this->db->from('empty_tin_rates');  
	    $this->db->where("product_id = ( SELECT id from products where name LIKE '%".$condition['tintype']."%' and brand_id  = ".$condition['brand']." and category_id = ".$condition['category']." and weight = ".$condition['weight']." and packaging_type =".$condition['weight_type']." and status = 1 limit 1 )");  
	     $this->db->where("state_id",$condition['state_id']);
		$this->db->order_by('empty_tin_rates.id','DESC'); 
	    $this->db->limit(1);     
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows())
	    {
		    $row = $query->row_array(); 
		    return $row['rate'];
		}
		return 0;
	}
}