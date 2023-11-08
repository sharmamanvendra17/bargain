<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	  

	function GetTransactions(){
		$this->db->select(array('transaction_history.user_id','transaction_history.order_id','transaction_history.MerchantTxnId','transaction_history.TxnId','transaction_history.AuthIdCode','transaction_history.amt','transaction_history.invoice_id','orders.firstname','orders.lastname'));
	    $this->db->from('transaction_history'); 
	    $this->db->join('orders', 'orders.id = transaction_history.order_id','left'); 
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}	


	function GetTransactions1($condition){
		$this->db->select(array('transaction_history.user_id','transaction_history.order_id','transaction_history.MerchantTxnId','transaction_history.TxnId','transaction_history.AuthIdCode','transaction_history.amt','transaction_history.invoice_id','orders.firstname','orders.lastname'));
	    $this->db->from('transaction_history'); 
	    $this->db->join('orders', 'orders.id = transaction_history.order_id','left'); 
	    $this->db->where($condition);
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}	

	function GetProductByProductId($product_id){
		$this->db->select('*');
	    $this->db->from('product');  
	    $this->db->where('product_id',$product_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
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

	function userlogin($logindata) {
		$this->db->select('*');
	    $this->db->from('admin');  
	    $this->db->where($logindata);  
	    $query = $this->db->get(); 
	    //return $query->num_rows();
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
    	return $this->db->update('product', $update_data);
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

	function UpdateShippingToken($condition,$updatedata) 
	{
      	$this->db->where($condition);
    	return $this->db->update('shipping_config', $updatedata);
      //echo $this->db->last_query(); die;
  	}
}