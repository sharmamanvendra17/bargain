<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}
	  

	function GetAllOrders($paymnet_status=''){
		$this->db->select(array('orders.id as order_id','orders.user_id','transaction_history.user_id as transaction_history_user_id','transaction_history.order_id as transaction_history_order_id','transaction_history.MerchantTxnId','transaction_history.TxnId','transaction_history.AuthIdCode','transaction_history.amt','transaction_history.invoice_id','orders.firstname','orders.lastname','orders.address','orders.address2','orders.city','orders.state','orders.country','orders.phone','orders.postcode','orders.sub_total','orders.grand_total','orders.shipping_charge','orders.discount','orders.gst','orders.coupon_employee_email','orders.order_history','orders.payment_status','orders.self_pick_up','orders.self_pick_up_address','orders.created_at'));
	    $this->db->from('orders'); 
	    $this->db->join('transaction_history', 'transaction_history.order_id=orders.id','left');  
	    if($paymnet_status)
	    	$this->db->where('orders.payment_status',$paymnet_status);
	    $this->db->order_by('orders.created_at','DESC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	}

	function GetOrderDetail($order_id){
		$this->db->select(array('orders.id as order_id','orders.user_id','transaction_history.user_id as transaction_history_user_id','transaction_history.order_id as transaction_history_order_id','transaction_history.MerchantTxnId','transaction_history.TxnId','transaction_history.AuthIdCode','transaction_history.amt','transaction_history.invoice_id','orders.firstname','orders.lastname','orders.address','orders.address2','orders.city','orders.state','orders.country','orders.phone','orders.postcode','orders.sub_total','orders.grand_total','orders.shipping_charge','orders.discount','orders.gst','orders.coupon_employee_email','orders.order_history','orders.payment_status','orders.self_pick_up','orders.self_pick_up_address','orders.created_at'));
	    $this->db->from('orders'); 
	    $this->db->join('transaction_history', 'transaction_history.order_id=orders.id','left'); 
	    $this->db->where('orders.id',$order_id);
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	}
	 
	function product_detail($id){
		//return $q = $this->db->select('*')->from('product')->where('product_id',$id)->get()->row();

		$this->db->select(array('product_packing.id as packing_id','product_packing.product_id as p_id','product_packing.price','product_packing.packing','product_packing.product_image','product.product_name','product.product_id','product.hsn','product_packing.sku'));
	    $this->db->from('product_packing');
	    $this->db->join('product', 'product.product_id = product_packing.product_id','right');
	    $this->db->where('product_packing.id',$id);
	    $query = $this->db->get(); 
	    //echo $this->db->last_query();
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    } 

	}
}