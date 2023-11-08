<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pi_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}

	function Adddeviation($insertdata)
	{
		return $this->db->insert_batch('pi_deviation',$insertdata);
		//echo $this->db->last_query(); die;
	}

	function GetPiSkus($condition)
	{  
		$this->db->select('pi_sku_history.*');
		$this->db->select('products.name');
		$this->db->select('products.packaging_type');
		$this->db->select('products.packing_items');
		$this->db->select('products.packing_items_qty');
		$this->db->select('brands.name as brand_name');
		$this->db->select('category.category_name as category_name');
		$this->db->from('pi_sku_history');  
		$this->db->join('products','products.id= pi_sku_history.product_id','LEFT');
		$this->db->join('brands','brands.id=pi_sku_history.brand_id');
		$this->db->join('category','category.id= pi_sku_history.category_id and category.brand_id= pi_sku_history.brand_id');
		$this->db->where($condition);  
		$query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $query->result_array();
	}

	function GetPiSkuDeviation($condition)
	{  
		$this->db->select('pi_deviation.*'); 
		$this->db->from('pi_deviation');   
		$this->db->where($condition);  
		$this->db->order_by('pi_deviation.created_at','DESC');  
		$query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $query->row_array();
	}

	function GetPIHistoryCount($condition)
	{  
		$condition_date = "";
		$cur_date = date('Y-m-d');
		if(!isset($condition['pinumber'])  || empty($condition['pinumber']))
		{
			if($condition['booking_date_from']!='' && $condition['booking_date_to']=='')
				$condition_date = "`pi_history.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
			elseif($condition['booking_date_from']!='' && $condition['booking_date_to']!='')
				$condition_date = "`pi_history.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$condition['booking_date_to']." 23:54:54.969999'";
		}

		$this->db->select('pi_history.*');
		$this->db->select('company.name as company_name');
		$this->db->select('GROUP_CONCAT(booking_booking.booking_id) as bargain_ids');
		//$this->db->select('GROUP_CONCAT(vendors.name) as vendors_name');
		$this->db->select('vendors.name as vendors_name');
		$this->db->from('pi_history');  
		$this->db->join('company','company.id=pi_history.company_id'); 
		$this->db->join('booking_booking ',"find_in_set ( booking_booking.id , pi_history.booking_id ) <> 0"); 
		$this->db->join('vendors','vendors.id=pi_history.party_id');
		if($condition['party_id'] && !empty($condition['party_id']))
	    	$this->db->where('booking_booking.party_id',$condition['party_id']); 
	    if($condition['unit'] && !empty($condition['unit']))
	    	$this->db->where('booking_booking.production_unit',$condition['unit']); 

	    if($condition['rejected'] && !empty($condition['rejected']))
	    	$this->db->where('pi_history.status',1); 
	    if($condition['pinumber'] && !empty($condition['pinumber']))
	    	$this->db->where('pi_history.id',$condition['pinumber']); 

	    if($condition_date!='')
	    	$this->db->where($condition_date); 
		$this->db->group_by('pi_history.id');
        $query = $this->db->get();   
        return $query->num_rows(); 
	}

	function GetPIHistory($condition,$perPage=20,$pageNo=1)
	{
		//echo "<pre>"; print_r($condition); die;
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;
		$condition_date = "";
		$cur_date = date('Y-m-d');
		if(!isset($condition['pinumber'])  || empty($condition['pinumber']))
		{
			if($condition['booking_date_from']!='' && $condition['booking_date_to']=='')
				$condition_date = "`pi_history.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
			elseif($condition['booking_date_from']!='' && $condition['booking_date_to']!='')
				$condition_date = "`pi_history.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$condition['booking_date_to']." 23:54:54.969999'";
		}

		$this->db->select('pi_history.*');
		$this->db->select('company.name as company_name');
		$this->db->select('GROUP_CONCAT(booking_booking.booking_id) as bargain_ids');
		//$this->db->select('GROUP_CONCAT(vendors.name) as vendors_name');
		$this->db->select('vendors.name as vendors_name');

		$this->db->select('pidev.pi_deviation');

		$this->db->from('pi_history');  
		$this->db->join('company','company.id=pi_history.company_id'); 
		$this->db->join('booking_booking ',"find_in_set ( booking_booking.id , pi_history.booking_id ) <> 0"); 
		$this->db->join('vendors','vendors.id=pi_history.party_id');

		//$this->db->join('pi_deviation','pi_deviation.pi_id=pi_history.id','left');

		$this->db->join('(Select `pi_deviation`.`pi_id`,  count(pi_deviation.id) as pi_deviation FROM pi_deviation group by `pi_deviation`.`pi_id`) pidev ','pidev.pi_id=pi_history.id','left');

		if($condition['party_id'] && !empty($condition['party_id']))
	    	$this->db->where('booking_booking.party_id',$condition['party_id']); 
	    if($condition['unit'] && !empty($condition['unit']))
	    	$this->db->where('booking_booking.production_unit',$condition['unit']); 

	    if($condition['rejected'] && !empty($condition['rejected']))
	    	$this->db->where('pi_history.status',1); 
	    if($condition['pinumber'] && !empty($condition['pinumber']))
	    	$this->db->where('pi_history.id',$condition['pinumber']); 

	    if($condition_date!='')
	    	$this->db->where($condition_date); 
		$this->db->group_by('pi_history.id');
		$this->db->order_by('pi_history.id','DESC');
		$this->db->limit($perPage, $startFromRecord);
        $query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $query->result_array();
	}


	function GetSecondaryPIHistoryCount($condition)
	{ 
		$condition_date = "";
		$cur_date = date('Y-m-d');
		if(!isset($condition['pinumber'])  || empty($condition['pinumber']))
		{
			if($condition['booking_date_from']!='' && $condition['booking_date_to']=='')
				$condition_date = "`pi_history_secondary_booking`.`created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
			elseif($condition['booking_date_from']!='' && $condition['booking_date_to']!='')
				$condition_date = "`pi_history_secondary_booking`.`created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$condition['booking_date_to']." 23:54:54.969999'";
		}
		$this->db->select('pi_history_secondary_booking .*'); 
		$this->db->select('GROUP_CONCAT(secondary_booking.secondary_booking_id) as bargain_ids');
		$this->db->from('pi_history_secondary_booking ');  
		$this->db->join('secondary_booking ',"find_in_set ( secondary_booking.id , pi_history_secondary_booking .booking_id ) <> 0"); 
		if($condition['party_id'] && !empty($condition['party_id']))
	    	$this->db->where('secondary_booking.supply_from',$condition['party_id']); 
	    

	    if($condition['rejected'] && !empty($condition['rejected']))
	    	$this->db->where('pi_history_secondary_booking.status',1); 
	    if($condition['pinumber'] && !empty($condition['pinumber']))
	    	$this->db->where("pi_history_secondary_booking.id",$condition['pinumber']);


	    if($condition_date!='')
	    	$this->db->where($condition_date); 
		$this->db->group_by('pi_history_secondary_booking.id');
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->num_rows(); 
	}

	function GetSecondaryPIHistory($condition,$perPage=20,$pageNo=1)
	{
		//echo "<pre>"; print_r($condition); die;
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;
		$condition_date = "";
		$cur_date = date('Y-m-d');
		if(!isset($condition['pinumber'])  || empty($condition['pinumber']))
		{
			if($condition['booking_date_from']!='' && $condition['booking_date_to']=='')
				$condition_date = "`pi_history_secondary_booking`.`created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
			elseif($condition['booking_date_from']!='' && $condition['booking_date_to']!='')
				$condition_date = "`pi_history_secondary_booking`.`created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$condition['booking_date_to']." 23:54:54.969999'";
		}

		$this->db->select('pi_history_secondary_booking.*'); 
		$this->db->select('GROUP_CONCAT(secondary_booking.secondary_booking_id) as bargain_ids');
		//$this->db->select('GROUP_CONCAT(vendors.name) as vendors_name');
		$this->db->select('vendors.name as vendors_name');
		$this->db->select('vendors.cnf');
		$this->db->from('pi_history_secondary_booking');   
		$this->db->join('secondary_booking',"find_in_set ( secondary_booking.id , pi_history_secondary_booking.booking_id ) <> 0"); 
		$this->db->join('vendors','vendors.id=secondary_booking.supply_from');
		if($condition['party_id'] && !empty($condition['party_id']))
	    	$this->db->where('secondary_booking.supply_from',$condition['party_id']); 
	    

	    if($condition['rejected'] && !empty($condition['rejected']))
	    	$this->db->where('pi_history_secondary_booking.status',1); 
	    if($condition['pinumber'] && !empty($condition['pinumber']))
	    	$this->db->where('pi_history_secondary_booking.id',$condition['pinumber']); 

	    if($condition_date!='')
	    	$this->db->where($condition_date); 
		$this->db->group_by('pi_history_secondary_booking.id');
		$this->db->order_by('pi_history_secondary_booking.id','DESC');
		$this->db->limit($perPage, $startFromRecord);
        $query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $query->result_array();
	}


	function GetPiInfo($condition)
	{  
		$this->db->select('pi_history.truck_number'); 
		$this->db->select('pi_history.dispatch_date'); 
		$this->db->from('pi_history');   
		$this->db->where($condition);  
		$query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $query->row_array();
	}


	function GetSecondaryPiInfo($condition)
	{  
		$this->db->select('pi_history_secondary_booking.party_id'); 
		$this->db->select('pi_history_secondary_booking.created_at'); 
		$this->db->from('pi_history_secondary_booking');   
		$this->db->where($condition);  
		$query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $query->row_array();
	}


	function Deletecnf_sales_history($stock_date,$party_id){
	  $this->db->where("stock_date >= ", $stock_date);
	  $this->db->where("party_id", $party_id);
	  return $this->db->delete('cnf_sales_history');
	}
}