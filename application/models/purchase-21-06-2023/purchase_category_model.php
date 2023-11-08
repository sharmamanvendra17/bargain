<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_category_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	
	function GetCategories($condition= array()){
		$this->db->select('*');
	    $this->db->from('pur_category');  
	    if($condition)
	    {
	    	$this->db->where($condition);  
	    }
	    $this->db->order_by('pur_category.category_name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function AddCategory($insertdata)
	{
		//$this->db->insert('pur_category',$insertdata);
		//echo $this->db->last_query();die;
		return $this->db->insert('pur_category',$insertdata);
	}	 


	function DeleteCategory($condition){
	  $this->db->where($condition);
	  return $this->db->delete('pur_category');
	}

	function GetCategory($condition){
		$this->db->select('*');
	    $this->db->from('pur_category'); 
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
	    $this->db->from('pur_category');  
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

    function GetCategoryByCategoryId($category_id){
		$this->db->select('*');
	    $this->db->from('pur_category');  
	    $this->db->where('id',$category_id);  
	    $query = $this->db->get(); 
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
    	return $this->db->update('pur_category', $update_data);
      //echo $this->db->last_query(); die;
  	}

  	function GetProductsbycategpry_id($condition){ 
		$this->db->select(array('pur_products.id','pur_products.product_name','pur_category.category_name as category_name'));
	    $this->db->from('pur_products'); 
	    $this->db->join('pur_category', 'pur_category.id = pur_products.category_id','left');   
	    $this->db->where($condition);     
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}
}