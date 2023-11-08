<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 


	function AddInvoice($insertdata)
	{
		return $this->db->insert('invoice',$insertdata);
		//echo $this->db->last_query(); die;
	}
	function AddRemark($insertdata)
	{
		return $this->db->insert('booking_remarks',$insertdata);
		//echo $this->db->last_query(); die;
	}
	function GetAllSkus($condition)
	{
		$this->db->select('*');
        $this->db->from('booking_skus');
        $this->db->where($condition); 
        $query = $this->db->get();
		//echo $this->db->last_query(); die;  
        $response = array();
        if($query->num_rows())
        {
        	$results =  $query->result_array();
        	foreach ($results as $key => $value) {
        		$response[$value['product_id']][] = $value;
        	}
        }
        return $response;
	}
	function AddSKU($condition,$skudata)
	{ 
        $this->db->select('id');
        $this->db->from('booking_skus');
        $this->db->where($condition); 
        $query = $this->db->get();  
        if(isset($condition['id']))
        {
	        if($query->num_rows())
	        {
	        	$this->db->where($condition);
	    		return $this->db->update('booking_skus', $skudata);
	    		//echo $this->db->last_query();
	        }
	    }
        else
        {
        	return $this->db->insert('booking_skus',$skudata);
			//echo $this->db->last_query(); 
        }		
	}
	function DeleteSKU($condition){
	  $this->db->where($condition);
	  return $this->db->delete('booking_skus');
	}
	function UpdateBookingBooking($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('booking_booking', $updatedata);
	}
	function AddBooking($insertdata)
	{
		$this->db->insert('booking_booking',$insertdata);
		$booking_number =  $this->db->insert_id();
		$this->db->select('booking_id');
		$this->db->select('id');
        $this->db->from('booking_booking');
        $this->db->where('id',$booking_number); 
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        $row = $query->row_array();
        return $row; //$row['booking_id'];
		//echo $this->db->last_query(); die;
	}
	function getbookingid($book_chek_date,$party_id){  
		 
		$dtae_book =  date("Y-m-d", strtotime($book_chek_date));
		$select_query =  "select MAX(booking_id) as booking_id, created_at from booking_booking where date(created_at)='$dtae_book' and party_id=$party_id"; 
		$query = $this->db->query($select_query);
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0)
	    {
	    	$row = $query->row_array();
	    	if(!is_null($row['booking_id']))
	    	{
	    		return $row['booking_id']-1;
	    	}
	    	else
	    	{
	    		$select_query =  "SELECT CASE WHEN MONTH(created_at)>=4 THEN concat(YEAR(created_at), '-',YEAR(created_at)+1) ELSE concat(YEAR(created_at)-1,'-', YEAR(created_at)) END AS financial_year,MAX(booking_id) as booking_id FROM booking_booking GROUP BY financial_year having financial_year = (CASE WHEN MONTH('$book_chek_date')>=4 THEN concat(YEAR('$book_chek_date'), '-',YEAR('$book_chek_date')+1) ELSE concat(YEAR('$book_chek_date')-1,'-', YEAR('$book_chek_date')) END)"; 

				$query = $this->db->query($select_query);
			    //echo $this->db->last_query(); die;
			    if($query->num_rows() > 0 )
			    {
			      $row = $query->row_array();
			      //echo "<pre>";  print_r($row); die;
			      return $row['booking_id'];  
			    }
	    	}
	    } 
	}


	function GetBookingInfoById($booking_id) { 
		//echo date('Y-m-d');  die;
		$cur_date = date('Y-m-d');
		$condition = "`booking_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking_booking.remark','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.remaining_weight','booking_booking.total_weight_input','booking_booking.total_weight_net','booking_booking.total_weight','booking_booking.is_for','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.quantity','booking_booking.rate','booking_booking.insurance','booking_booking.created_at','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.id as broker_id','brokers.name as broker_name','admin.name as sales_executive_name','category.tin_rate ','vendors.state_id as state_id','states.name as state_name','vendors.gst_no as gst_no','admin.mobile as maker_mobile','vendors.mobile as vendor_mobile'));
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('states', 'states.id = vendors.state_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');  
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');

	    $this->db->join('admin', 'admin.id = booking_booking.sales_executive_id','left');

	    $this->db->where('booking_booking.booking_id',$booking_id); 
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function GetBookingList($condition,$perPage=20, $pageNo=1){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.is_mail','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
	    $this->db->where($date_range); 
	    if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
        }
        elseif ($role==3) 
        {
        	$this->db->where("booking_booking.status <> ",0); 
        }
        //echo "<pre>"; print_r($condition); die;
        if(isset($condition['is_lock']))
        {
        	$this->db->where("booking_booking.is_lock ",1); 
        }
	    	//if($condition)
	    	//$this->db->where($condition); 

	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    //$this->db->group_by('booking_booking.booking_id'); 
	    $this->db->limit($perPage, $startFromRecord); 
        $this->db->order_by('booking_booking.id','DESC');  

	    $query = $this->db->get(); 
	   	//echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function CountBookingList($condition){  
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
	    $this->db->where($date_range); 
	    if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
        }
        elseif ($role==3) 
        {
        	$this->db->where("booking_booking.status <> ",0); 
        }
        //echo "<pre>"; print_r($condition); die;
        if(isset($condition['is_lock']))
        {
        	$this->db->where("booking_booking.is_lock ",1); 
        } 
        $this->db->order_by('booking_booking.id','DESC');  

	    $query = $this->db->get(); 
	   	return $query->num_rows();
	}

	function GetBooking($condition){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('SUM(booking.quantity) as quantity','booking.payment_terms','booking.dispatch_delivery_terms','booking.for_total','booking.is_for','booking.booking_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.rate','booking.loose_rate','booking.total_loose_rate','booking.total_weight','booking.total_price','booking.created_at','booking.insurance','booking.insurance_amount','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.product_type as product_type','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('booking.status');
		$this->db->select('booking.remark');
	    $this->db->from('booking'); 
	    $this->db->join('vendors', 'vendors.id = booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking.category_id','left');     
	    $this->db->join('products', 'products.id = booking.product_id','left');
	    $this->db->join('brokers', 'brokers.id = booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking.admin_id','left');
	    $this->db->where($date_range); 
	    if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
        }
        elseif ($role==3) 
        {
        	$this->db->where("booking.status <> ",0); 
        }

	    	//if($condition)
	    	//$this->db->where($condition); 

	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $this->db->group_by('booking.party_id');  
	    $this->db->group_by('booking.brand_id');  
	    $this->db->group_by('booking.category_id');  
	    $this->db->group_by('booking.product_id');  


	    $this->db->order_by('booking.id','ASC');  


	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	


	function UpdateBooking($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('booking_booking', $updatedata);
    	//echo $this->db->last_query(); die;
	}

	function DeleteBooking($condition){
	  $this->db->where($condition);
	  return $this->db->delete('booking');
	}

	function GetReportBooking22022023($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$perPage=20, $pageNo=1,$employee='',$unit=''){ 

		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;

		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('booking_booking.insurance_amount','SUM(`pi_history`.`total_weight_pi`) as total_weight_pi', 'GROUP_CONCAT(`pi_history`.`id`) as pi_ids','booking_booking.reject_remark','admin.unauthorized_viewers','booking_booking.loose_oil_rate','booking_booking.is_for','booking_booking.is_mail','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.remaining_weight','booking_booking.is_close','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name','admin.team_lead_id','vendors.state_id'));

		$this->db->select("if(pi_history.truck_number IS NULL OR pi_history.truck_number ='' , count(pi_history.id), 0) as withouttruck",false);

		$this->db->select("if(pi_history.truck_number IS NOT NULL, count(pi_history.id), 0) as withtruck",false);
		//$this->db->select("pih.withouttruck");
		//$this->db->select("pih.withouttruck");
		$this->db->select("count(booking_skus.id) as not_pi_sku");
		


		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark'); 
		$this->db->select('booking_booking.total_weight_net');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');

	    $this->db->join('booking_skus', 'booking_skus.booking_id = booking_booking.id and pi_id = 0 ','left');
 
 		$this->db->join('pi_history', 'FIND_IN_SET ( booking_booking.id , pi_history.booking_id ) and  pi_history.status=0' ,'LEFT');
	    
	    //$this->db->join("SELECT booking_id,if(pi_history.truck_number IS NULL OR pi_history.truck_number ='' , count(pi_history.id), 0) as withouttruck,if(pi_history.truck_number IS NOT NULL, count(pi_history.id), 0) as withtruck FROM `pi_history` WHERE pi_history.status=0 group by `booking_id`) as pih", 'FIND_IN_SET ( booking_booking.id , pih.booking_id )' ,'LEFT');

	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            //$this->db->where('admin_id' , $userid);  
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)"); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        }
        if($status!='')
        {
        	if($status=='lock')
        	{
        		$this->db->where("booking_booking.is_lock ",1); 
        	}
        	elseif($status=='partial_lock')
        	{
            	$this->db->where("booking_booking.is_lock ",2);
        	}
        	elseif($status=='mailed')
        	{
            	$this->db->where("booking_booking.is_mail ",1);
        	}
            else
            {
            	$this->db->where("booking_booking.status ",$status);
            	//$this->db->where("booking_booking.is_lock ",0);
            }
        }
        if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}

    	if($role==5) //maker
        { 
        	$this->db->where("( find_in_set ( $userid, admin.unauthorized_viewers ) = 0 OR  admin.unauthorized_viewers is NULL )"); 
        }

        if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }
        $this->db->limit($perPage, $startFromRecord);
		$this->db->order_by('booking_booking.id','DESC');  
		$this->db->group_by('booking_booking.id');  
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$perPage=20, $pageNo=1,$employee='',$unit=''){ 

		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;

		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('booking_booking.insurance_amount', 'pih.total_weight_pi', 'pih.pi_ids','booking_booking.reject_remark','admin.unauthorized_viewers','booking_booking.loose_oil_rate','booking_booking.is_for','booking_booking.is_mail','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.remaining_weight','booking_booking.is_close','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name','admin.team_lead_id','vendors.state_id'));

		 
		$this->db->select("pih.withouttruck");
		$this->db->select("pih.withtruck");
		$this->db->select("count(booking_skus.id) as not_pi_sku");
		


		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark'); 
		$this->db->select('booking_booking.total_weight_net');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');

	    $this->db->join('booking_skus', 'booking_skus.booking_id = booking_booking.id and pi_id = 0 ','left');
 
 		
	    
	    $this->db->join("(SELECT GROUP_CONCAT(`pi_history`.`id`) as pi_ids, SUM(`pi_history`.`total_weight_pi`) as total_weight_pi, booking_id,if(pi_history.truck_number IS NULL OR pi_history.truck_number ='' , count(pi_history.id), 0) as withouttruck,if(pi_history.truck_number IS NOT NULL, count(pi_history.id), 0) as withtruck FROM `pi_history` WHERE pi_history.status=0 group by `booking_id`) as pih", ' FIND_IN_SET ( booking_booking.id , pih.booking_id ) ' ,'LEFT',false);

	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            //$this->db->where('admin_id' , $userid);  
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)"); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        }
        if($status!='')
        {
        	if($status=='lock')
        	{
        		$this->db->where("booking_booking.is_lock ",1); 
        	}
        	elseif($status=='partial_lock')
        	{
            	$this->db->where("booking_booking.is_lock ",2);
        	}
        	elseif($status=='mailed')
        	{
            	$this->db->where("booking_booking.is_mail ",1);
        	}
            else
            {
            	$this->db->where("booking_booking.status ",$status);
            	//$this->db->where("booking_booking.is_lock ",0);
            }
        }
        if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}

    	if($role==5) //maker
        { 
        	$this->db->where("( find_in_set ( $userid, admin.unauthorized_viewers ) = 0 OR  admin.unauthorized_viewers is NULL )"); 
        }

        if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }
        $this->db->limit($perPage, $startFromRecord);
		$this->db->order_by('booking_booking.id','DESC');  
		$this->db->group_by('booking_booking.id');  
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function GetReport($party_id,$brand_id,$category_id,$product_id,$booking_date_from,$booking_date_to,$booked_by='',$status=''){ 
		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('booking.for_total','booking.is_for','booking.booking_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.quantity','booking.rate','booking.insurance','booking.total_price','booking.created_at','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.loose_rate as loose_rate','products.product_type as product_type','admin.name as admin_name'));
		$this->db->select('booking.status');
		$this->db->select('booking.remark');
	    $this->db->from('booking'); 
	    $this->db->join('vendors', 'vendors.id = booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking.category_id','left');     
	    $this->db->join('products', 'products.id = booking.product_id','left');
	    $this->db->join('admin', 'admin.id = booking.admin_id','left');
	    if($party_id)
	    	$this->db->where('booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking.category_id',$category_id);
	    if($product_id)
	    	$this->db->where('booking.product_id',$product_id);
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
            if($status!='')
            	$this->db->where("booking.status ",$status); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            if($status!='')
            	$this->db->where("booking.status ",$status); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		$this->db->where("booking.status <> ",0); 
        	}
        	else
        	{ 
        		$this->db->where("booking.status  ",$status); 
        	}
        }

		$this->db->order_by('booking.id','ASC');  
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function getlast_booking_id($book_chek_date){  
		//echo date('Y-m-d');  die;

		//SELECT CASE WHEN MONTH(created_at)>=4 THEN concat(YEAR(created_at), '-',YEAR(created_at)+1) ELSE concat(YEAR(created_at)-1,'-', YEAR(created_at)) END AS financial_year,MAX(booking_id) FROM booking GROUP BY financial_year having financial_year = (CASE WHEN MONTH(now())>=4 THEN concat(YEAR(now()), '-',YEAR(now())+1) ELSE concat(YEAR(now())-1,'-', YEAR(now())) END)


		$select_query =  "SELECT CASE WHEN MONTH(created_at)>=4 THEN concat(YEAR(created_at), '-',YEAR(created_at)+1) ELSE concat(YEAR(created_at)-1,'-', YEAR(created_at)) END AS financial_year,MAX(booking_id) as booking_id FROM booking_booking GROUP BY financial_year having financial_year = (CASE WHEN MONTH('$book_chek_date')>=4 THEN concat(YEAR('$book_chek_date'), '-',YEAR('$book_chek_date')+1) ELSE concat(YEAR('$book_chek_date')-1,'-', YEAR('$book_chek_date')) END)"; 

		$query = $this->db->query($select_query);
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row['booking_id'];  
	    }
	}

 

	function CheckBooking($book_chek_date){ 
		//echo date('Y-m-d');  die;
		$select_query =  "SELECT CASE WHEN MONTH(created_at)>=4 THEN concat(YEAR(created_at), '-',YEAR(created_at)+1) ELSE concat(YEAR(created_at)-1,'-', YEAR(created_at)) END AS financial_year,MAX(booking_id) FROM booking GROUP BY financial_year having financial_year = (CASE WHEN MONTH('$book_chek_date')>=4 THEN concat(YEAR('$book_chek_date'), '-',YEAR('$book_chek_date')+1) ELSE concat(YEAR('$book_chek_date')-1,'-', YEAR('$book_chek_date')) END)"; 

		$query = $this->db->query($select_query);
		return  $query->num_rows(); 
	}


	function Bookingdetils($condition) {  
		 
		$this->db->select('booking.approve_reject_time');
	    $this->db->from('booking');  
	    $this->db->where($condition); 
	    $query = $this->db->get(); 
	    $row = $query->row_array();
	    //echo $this->db->last_query(); die;
	    return $row['approve_reject_time'];
	}



	function GetBookingInfoDetails($booking_id){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking.quantity','booking.payment_terms','booking.dispatch_delivery_terms','booking.for_total','booking.is_for','booking.booking_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.rate','booking.loose_rate','booking.total_loose_rate','booking.total_weight','booking.total_price','booking.created_at','booking.insurance','booking.insurance_amount','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.product_type as product_type','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('booking.status');
		$this->db->select('booking.remark');
		$this->db->select('booking.booking_remark');
	    $this->db->from('booking'); 
	    $this->db->join('vendors', 'vendors.id = booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking.category_id','left');     
	    $this->db->join('products', 'products.id = booking.product_id','left');
	    $this->db->join('brokers', 'brokers.id = booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking.admin_id','left');
	    $this->db->where('booking.booking_id',$booking_id);  
	    $this->db->where($date_range); 
	    if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
        }
        elseif ($role==3) 
        {
        	$this->db->where("booking.status <> ",0); 
        }

	    	//if($condition)
	    	//$this->db->where($condition); 

	    //$this->db->where("(booking.created_at >= " . now() . ")"); 


	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}



	

	function GetBookingRemarks($booking_id) {  
		 
		$this->db->select('booking_remarks.*');
		$this->db->select('admin.name as updated_by_name');
	    $this->db->from('booking_remarks');  
	    $this->db->join('admin','admin.id=booking_remarks.updated_by','left');  
	    $this->db->where('booking_id',$booking_id); 
	    $this->db->order_by('created_at','DESC'); 
	    $query = $this->db->get(); 
	    return $row = $query->result_array();  
	}


	function GetBookingInfoDetailsPdf($booking_id){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking.quantity','booking.payment_terms','booking.dispatch_delivery_terms','booking.for_total','booking.is_for','booking.booking_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.rate','booking.loose_rate','booking.total_loose_rate','booking.total_weight','booking.total_price','booking.created_at','booking.insurance','booking.insurance_amount','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.product_type as product_type','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('booking.status');
		$this->db->select('booking.remark');
		$this->db->select('booking.booking_remark');
	    $this->db->from('booking'); 
	    $this->db->join('vendors', 'vendors.id = booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking.category_id','left');     
	    $this->db->join('products', 'products.id = booking.product_id','left');
	    $this->db->join('brokers', 'brokers.id = booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking.admin_id','left');
	    $this->db->where('booking.booking_id',$booking_id);  
	    $this->db->where('booking.status',2);
	    if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
        }
        elseif ($role==3) 
        {
        	$this->db->where("booking.status <> ",0); 
        }

	    	//if($condition)
	    	//$this->db->where($condition); 

	    //$this->db->where("(booking.created_at >= " . now() . ")"); 


	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function UpdateAproveStatusAll($booking_id)	
	{
		$updatedata = array('status'=> 2);
		$this->db->where('booking_id',$booking_id); 
		$this->db->where('status ',1);
    	return $this->db->update('booking', $updatedata);
	}


	function GetSkuinfo($condition)
	{
		$this->db->select('booking_skus.weight');
		$this->db->select('booking_skus.quantity');
		$this->db->select('products.name');
		$this->db->select('products.packing_items_qty');
        $this->db->from('booking_skus');
        $this->db->join('products', 'products.id = booking_skus.product_id','left');
        $this->db->where($condition); 
        $query = $this->db->get();  
        return $query->result_array();
    }






    function CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$employee='',$unit=''){ 
		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.remaining_weight','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
 
	    
	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);  
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        }
        if($status!='')
        {
        	if($status=='lock')
        	{
        		$this->db->where("booking_booking.is_lock ",1); 
        	}
        	elseif($status=='partial_lock')
        	{
            	$this->db->where("booking_booking.is_lock ",2);
        	}
        	elseif($status=='mailed')
        	{
            	$this->db->where("booking_booking.is_mail ",1);
        	}
            else
            {
            	$this->db->where("booking_booking.status ",$status);
            	//$this->db->where("booking_booking.is_lock ",0);
            }
        }
        if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}
    	if($role==5) //maker
        { 
        	//$this->db->where("find_in_set($userid, admin.unauthorized_viewers)=0"); 
        	$this->db->where("( find_in_set ( $userid, admin.unauthorized_viewers ) = 0 OR  admin.unauthorized_viewers is NULL )"); 
        }

        if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }

		$this->db->order_by('booking_booking.id','ASC');  
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->num_rows(); 
	}



	function GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$group_by,$employee='',$unit=''){   
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('category.freight_rate','category.tin_rate','count(booking_booking.id) as bargain_count','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.remaining_weight','booking_booking.is_close','SUM(booking_booking.total_weight) as weight', 'SUM(booking_booking.quantity) as quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','states.name as state_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name','vendors.state_id'));

		 
		$this->db->select("AVG( if( `vendors`.`state_id` <> 4 AND  `vendors`.`state_id` <> 22 AND  `vendors`.`state_id` <> 23 AND  `vendors`.`state_id` <> 24 AND  `vendors`.`state_id` <> 25 AND  `vendors`.`state_id` <> 30 AND  `vendors`.`state_id` <> 33, `booking_booking`.`rate`, booking_booking.avg_condition )) as avg_rate_other");
		$this->db->select("AVG( if( `vendors`.`state_id` = 4 OR  `vendors`.`state_id` = 22 OR  `vendors`.`state_id` = 23 OR  `vendors`.`state_id` = 24 OR  `vendors`.`state_id` = 25 OR  `vendors`.`state_id` = 30 OR  `vendors`.`state_id` = 33 , `booking_booking`.`rate`, booking_booking.avg_condition )) as avg_rate_aasam");


		//$this->db->select("AVG( if( `vendors`.`state_id` <> 4 AND  `vendors`.`state_id` <> 22 AND  `vendors`.`state_id` <> 23 AND  `vendors`.`state_id` <> 24 AND  `vendors`.`state_id` <> 25 AND  `vendors`.`state_id` <> 30 AND  `vendors`.`state_id` <> 33, `booking_booking`.`loose_oil_rate`, booking_booking.avg_condition )) as avg_rate_other_loose");

		$this->db->select("SUM( if( `vendors`.`state_id` <> 4 AND  `vendors`.`state_id` <> 22 AND  `vendors`.`state_id` <> 23 AND  `vendors`.`state_id` <> 24 AND  `vendors`.`state_id` <> 25 AND  `vendors`.`state_id` <> 30 AND  `vendors`.`state_id` <> 33, `booking_booking`.`loose_oil_rate`*`booking_booking`.`quantity`, booking_booking.avg_condition ))/SUM( if( `vendors`.`state_id` <> 4 AND  `vendors`.`state_id` <> 22 AND  `vendors`.`state_id` <> 23 AND  `vendors`.`state_id` <> 24 AND  `vendors`.`state_id` <> 25 AND  `vendors`.`state_id` <> 30 AND  `vendors`.`state_id` <> 33, `booking_booking`.`quantity`, booking_booking.avg_condition )) as avg_rate_other_loose");

		//$this->db->select("AVG( if( `vendors`.`state_id` = 4 OR  `vendors`.`state_id` = 22 OR  `vendors`.`state_id` = 23 OR  `vendors`.`state_id` = 24 OR  `vendors`.`state_id` = 25 OR  `vendors`.`state_id` = 30 OR  `vendors`.`state_id` = 33 , `booking_booking`.`loose_oil_rate`, booking_booking.avg_condition )) as avg_rate_aasam_loose");

		$this->db->select("SUM( if( `vendors`.`state_id` = 4 OR  `vendors`.`state_id` = 22 OR  `vendors`.`state_id` = 23 OR  `vendors`.`state_id` = 24 OR  `vendors`.`state_id` = 25 OR  `vendors`.`state_id` = 30 OR  `vendors`.`state_id` = 33 , `booking_booking`.`loose_oil_rate`*`booking_booking`.`quantity`, booking_booking.avg_condition ))/SUM( if( `vendors`.`state_id` = 4 OR  `vendors`.`state_id` = 22 OR  `vendors`.`state_id` = 23 OR  `vendors`.`state_id` = 24 OR  `vendors`.`state_id` = 25 OR  `vendors`.`state_id` = 30 OR  `vendors`.`state_id` = 33 , `booking_booking`.`quantity`, booking_booking.avg_condition )) as avg_rate_aasam_loose");



		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('states', 'states.id = vendors.state_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
 	

 		if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}
	    
	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
            if($status!='')
	        {
	        	if($status=='lock')
	        	{
	        		$this->db->where("booking_booking.is_lock ",1); 
	        	}
	        	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	$this->db->where("booking_booking.is_lock ",0);
	            }
	        } 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            if($status!='')
	        {
	        	if($status=='lock')
	        	{
	        		$this->db->where("booking_booking.is_lock ",1); 
	        	}
	        	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	$this->db->where("booking_booking.is_lock ",0);
	            }
	        }
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		//$this->db->where("booking_booking.status <> ",0); 
        	}
        	else
        	{ 
        		//$this->db->where("booking_booking.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("booking_booking.is_lock ",1); 
        		}
        		elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	$this->db->where("booking_booking.is_lock ",0);
	            }
        	}
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
        else
        {
        	if($status!='')
	        {
	        	if($status=='lock')
	        	{
	        		$this->db->where("booking_booking.is_lock ",1); 
	        	}
	        	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	$this->db->where("booking_booking.is_lock ",0);
	            }
	        }
        }
		$this->db->where("booking_booking.status <>",3);
		if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }
		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				if($group_by_value=='place')
				{
					$this->db->group_by('vendors.state_id');  
					$this->db->order_by('states.name','ASC'); 
				}
				elseif($group_by_value=='category_name')
				{
					$this->db->group_by('category.'.$group_by_value);  
				}
				else
				{
					$this->db->group_by('booking_booking.'.$group_by_value);  
					$this->db->order_by('brands.name','ASC');
				}
			}
		}
		else
		{
			$this->db->order_by('booking_booking.id','DESC'); 
		}
		

		  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function GetBookingInfoById1($booking_id) { 
		//echo date('Y-m-d');  die;
		$cur_date = date('Y-m-d');
		$condition = "`booking_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking_booking.sales_executive_id','booking_booking.remark','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.remaining_weight','booking_booking.total_weight_input','booking_booking.total_weight','booking_booking.total_weight_net','booking_booking.is_for','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.quantity','booking_booking.rate','booking_booking.insurance','booking_booking.created_at','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.id as broker_id','brokers.name as broker_name','admin.name as sales_executive_name'));
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');  
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');

	    $this->db->join('admin', 'admin.id = booking_booking.sales_executive_id','left');

	    $this->db->where('booking_booking.id',$booking_id); 
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$group_by,$employee='',$rejected='',$unit=''){   
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('count(booking_booking.id) as bargain_count','SUM(booking_booking.total_weight) as weight', 'SUM(booking_booking.quantity) as quantity')); 
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
 
	    if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}
	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
            if($status!='')
            {
            	//$this->db->where("booking_booking.status ",$status); 
            	if($status=='lock')
            	{
	        		$this->db->where("booking_booking.is_lock ",1); 
            	}
            	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	//$this->db->where("booking_booking.is_lock ",0);
	            }
            }
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");
            if($status!='')
        	{  
            	if($status=='lock')
            	{
	        		$this->db->where("booking_booking.is_lock ",1); 
            	}
            	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	//$this->db->where("booking_booking.is_lock ",0);
	            }
	        }
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		//$this->db->where("booking_booking.status <> ",0); 
        	}
        	else
        	{ 
        		//$this->db->where("booking_booking.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("booking_booking.is_lock ",1); 
        		}
        		elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	else
	        	{
	            	$this->db->where("booking_booking.status ",$status); 
	            	//$this->db->where("booking_booking.is_lock ",0);
	        	}
        	}
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
        else
        {
        	if($status=='')
        	{ 
        		//$this->db->where("booking_booking.status <> ",0); 
        		//$this->db->where("booking_booking.is_lock ",0);
        	}
        	else
        	{ 
        		//$this->db->where("booking_booking.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("booking_booking.is_lock ",1); 
        		}
        		elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	else
	        	{
	            	$this->db->where("booking_booking.status ",$status); 
	            	//$this->db->where("booking_booking.is_lock ",0);
	        	}
        	}
        }
		
		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				if($group_by_value=='place')
				{
					$this->db->group_by('vendors.state_id');  
					$this->db->order_by('states.name','ASC'); 
				}
				else
				{
					$this->db->group_by('booking_booking.'.$group_by_value);  
					$this->db->order_by('brands.name','ASC');
				}
			}
			$this->db->order_by('booking_booking.status','ASC'); 
		}
		else
		{
			$this->db->order_by('booking_booking.id','DESC'); 
		}
		if($rejected)
		{
			$this->db->where("booking_booking.status <>",3); 
		}
		 
		if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }
		  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function GetBookingSummaryLocked($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$group_by,$employee='',$unit=''){   
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('count(booking_booking.id) as bargain_count','SUM(booking_booking.total_weight) as weight', 'SUM(booking_booking.quantity) as quantity')); 
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
 	
 		if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}
	    
	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);   
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
             
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
		$this->db->where("booking_booking.is_lock ",1);
		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				if($group_by_value=='place')
				{
					$this->db->group_by('vendors.state_id');  
					$this->db->order_by('states.name','ASC'); 
				}
				else
				{
					$this->db->group_by('booking_booking.'.$group_by_value);  
					$this->db->order_by('brands.name','ASC');
				}
			}
		}
		else
		{
			$this->db->order_by('booking_booking.id','DESC'); 
		}
		if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }

		  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}



	function getbargainweight($condition){   


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('SUM(booking_booking.total_weight_input) as weight', 'SUM(booking_booking.quantity) as quantity')); 
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
 	  
		$this->db->where('booking_booking.party_id',$condition['party_id']); 
		$this->db->where('booking_booking.is_mail',$condition['is_mail']); 
		$this->db->where('booking_booking.is_lock',$condition['is_lock']);
	    $this->db->where('booking_booking.status',$condition['status']);
	   
	     

		if($role==1) //maker
        { 
            $this->db->where('booking_booking.admin_id' , $userid);   
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
             
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        } 

		$this->db->group_by('booking_booking.party_id');  
		 

		  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function getpenidngbargain($condition){   
		$this->db->select('id');
		$this->db->from('booking_booking'); 
		$this->db->where($condition); 
		$query = $this->db->get(); 
		//echo $this->db->last_query(); die;
		return $query->num_rows();
	}


	function getpenidngbargainInfo($condition) { 
		//echo date('Y-m-d');  die;
		 
		$this->db->select(array('booking_booking.remark','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.remaining_weight','booking_booking.total_weight_input','booking_booking.total_weight','booking_booking.is_for','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.quantity','booking_booking.rate','booking_booking.insurance','booking_booking.created_at','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.id as broker_id','brokers.name as broker_name','admin.name as sales_executive_name'));
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');  
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');

	    $this->db->join('admin', 'admin.id = booking_booking.sales_executive_id','left');

	    if(isset($condition['party_id']))
	    	$this->db->where('booking_booking.party_id',$condition['party_id']); 
	    if(isset($condition['is_mail']))
	    	$this->db->where('booking_booking.is_mail',$condition['is_mail']); 
	    if(isset($condition['is_lock']))
	    	$this->db->where('booking_booking.is_lock',$condition['is_lock']); 
	    if(isset($condition['status']))
	    	$this->db->where('booking_booking.status',$condition['status']);
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	function updatebargiansmail($condition)	
	{
		$this->db->where_in('id',$condition);
		$this->db->set('is_mail',1);
    	return $this->db->update('booking_booking');
    	//echo $this->db->last_query(); die;
	}




	function GetBookingSummarySumReportDashboard($age,$status,$group_by){ 

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$this->db->select(array('count(booking_booking.id) as bargain_count','SUM(booking_booking.total_weight) as weight', 'SUM(booking_booking.quantity) as quantity')); 
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
 
	     
	     

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
            if($status!='')
            {
            	if($status=='lock')
            	{
	        		$this->db->where("booking_booking.is_lock ",1); 
            	}
            	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	//$this->db->where("booking_booking.is_lock ",0);
	            }
            }
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");
            if($status!='')
        	{  
            	if($status=='lock')
            	{
	        		$this->db->where("booking_booking.is_lock ",1); 
            	}
            	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	//$this->db->where("booking_booking.is_lock ",0);
	            }
	        }
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		//$this->db->where("booking_booking.status <> ",0); 
        	}
        	else
        	{ 
        		//$this->db->where("booking_booking.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("booking_booking.is_lock ",1); 
        		}
        		elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	else
	        	{
	            	$this->db->where("booking_booking.status ",$status); 
	            	//$this->db->where("booking_booking.is_lock ",0);
	        	}
        	}
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
        else
        {
        	if($status=='')
        	{ 
        		//$this->db->where("booking_booking.status <> ",0); 
        		//$this->db->where("booking_booking.is_lock ",0);
        	}
        	else
        	{ 
        		//$this->db->where("booking_booking.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("booking_booking.is_lock ",1); 
        		}
        		elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	else
	        	{
	            	$this->db->where("booking_booking.status ",$status); 
	            	//$this->db->where("booking_booking.is_lock ",0);
	        	}
        	}
        }
        if($age)
        {
			$age_value = "booking_booking.created_at BETWEEN DATE_SUB(NOW(), INTERVAL $age DAY) AND NOW()";
			$this->db->where($age_value);
		}

		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				 
				$this->db->group_by('booking_booking.'.$group_by_value); 
			}
				 
		} 
		$this->db->order_by('booking_booking.status','ASC'); 
		 

		  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function GetBookingSummaryLockedDashboard($age,$status,$group_by){   

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('count(booking_booking.id) as bargain_count','SUM(booking_booking.total_weight) as weight', 'SUM(booking_booking.quantity) as quantity')); 
		$this->db->select('booking_booking.status');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
 	
 		  
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);   
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
             
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
		$this->db->where("booking_booking.is_lock ",1);

		if($age)
        {
			$age_value = "booking_booking.created_at BETWEEN DATE_SUB(NOW(), INTERVAL $age DAY) AND NOW()";
			$this->db->where($age_value);
		}

		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				 
					$this->db->group_by('booking_booking.'.$group_by_value);   
				 
			}
		}
		$this->db->order_by('booking_booking.status','ASC');
		 

		  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}
	
	function GetBargainalert(){   

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('admin.username','admin.name as admin_name','booking_booking.is_for','booking_booking.is_mail','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.remaining_weight','booking_booking.is_close','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');
	    
 	 
		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);   
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
             
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
		$this->db->where("booking_booking.is_lock ",0);
		$this->db->where("booking_booking.status ",2);

		$age_value = "booking_booking.created_at < DATE_SUB(NOW(), INTERVAL 5 DAY)";
		$this->db->where($age_value); 
		$this->db->order_by('booking_booking.status','ASC');
		 

		  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function GetAllSkupibycomany($bargain_ids,$company_id)
	{	
		$this->db->select('booking_booking.rate as booking_rate');
		$this->db->select('booking_skus.*');
		$this->db->select('products.name');


		$this->db->select('products.packaging_type');
		$this->db->select('products.packing_items');

		$this->db->select('products.loose_rate');
		$this->db->select('products.packing_items_qty');
		$this->db->select('category.hsn');
		$this->db->select('category.category_name as category_name');
		$this->db->select('category.hsn');
		$this->db->select('brands.name as brand_name');

		$this->db->select('empty_tin_rates.base_rate');
		$this->db->select('empty_tin_rates.rate as empty_tin_rate');
		$this->db->select('empty_tin_rates.insurance as insurance_percentage');

		$this->db->select('e1.rate as base_empty_tin_rates');

		$this->db->select('vendors.state_id as state_id');
		$this->db->select('vendors.name as vendor_name');

        $this->db->from('booking_skus');
        $this->db->join('booking_booking','booking_booking.booking_id= booking_skus.bargain_id','LEFT');
        $this->db->join('products','products.id= booking_skus.product_id','LEFT');
        $this->db->join('category','category.id= booking_skus.category_id and category.brand_id= booking_skus.brand_id');
        $this->db->join('brands','brands.id=booking_skus.brand_id');
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  

        $this->db->join('empty_tin_rates', 'empty_tin_rates.brand_id = booking_skus.brand_id and empty_tin_rates.category_id = booking_skus.category_id and empty_tin_rates.product_id = booking_skus.product_id and empty_tin_rates.state_id = vendors.state_id and empty_tin_rates.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.category_id = booking_skus.category_id and e2.product_id = booking_skus.product_id and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at order by e2.id desc limit 1  ) ','left');

        $this->db->join('empty_tin_rates e1', 'e1.brand_id = booking_booking.brand_id and e1.category_id = booking_booking.category_id and e1.state_id = vendors.state_id and e1.base_rate = 1 and e1.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.base_rate = 1 and e2.category_id = booking_skus.category_id   and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at order by e2.id desc limit 1  ) ','left');

        $this->db->where("booking_skus.booking_id IN ($bargain_ids)"); 
        $this->db->where("booking_skus.company_id = $company_id"); 
        $this->db->where("booking_skus.is_lock = 1"); 
        $this->db->where("booking_skus.pi_id",0); 
        $query = $this->db->get();  
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();;
	}

	function GetAllSkupi($bargain_ids)
	{	
		$this->db->select('booking_booking.rate as booking_rate');
		$this->db->select('booking_skus.*');
		$this->db->select('products.name');


		$this->db->select('products.packaging_type');
		$this->db->select('products.packing_items');

		$this->db->select('products.loose_rate');
		$this->db->select('products.packing_items_qty');
		$this->db->select('category.hsn');
		$this->db->select('category.category_name as category_name');
		$this->db->select('category.hsn');
		$this->db->select('brands.name as brand_name');

		$this->db->select('empty_tin_rates.base_rate');
		$this->db->select('empty_tin_rates.rate as empty_tin_rate');
		$this->db->select('empty_tin_rates.insurance as insurance_percentage');

		$this->db->select('vendors.state_id as state_id');
		$this->db->select('vendors.name as vendor_name');
		$this->db->select('admin.id as booked_by');
        $this->db->from('booking_skus');
        $this->db->join('booking_booking','booking_booking.booking_id= booking_skus.bargain_id','LEFT');
        $this->db->join('products','products.id= booking_skus.product_id','LEFT');
        $this->db->join('category','category.id= booking_skus.category_id and category.brand_id= booking_skus.brand_id');
        $this->db->join('brands','brands.id=booking_skus.brand_id');
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  

         $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');  

        $this->db->join('empty_tin_rates', 'empty_tin_rates.brand_id = booking_skus.brand_id and empty_tin_rates.category_id = booking_skus.category_id and empty_tin_rates.product_id = booking_skus.product_id and empty_tin_rates.state_id = vendors.state_id and empty_tin_rates.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.category_id = booking_skus.category_id and e2.product_id = booking_skus.product_id and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at order by e2.id desc limit 1  ) ','left');

        $this->db->where("booking_skus.booking_id IN ($bargain_ids)"); 
        $this->db->where("booking_skus.is_lock",1); 
        $this->db->where("booking_skus.pi_id",0); 
        $query = $this->db->get();  
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();;
	}

	function GetBookingInfoByIdPI($booking_id) { 
		//echo date('Y-m-d');  die;
		$cur_date = date('Y-m-d');
		$condition = "`booking_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking_booking.remark','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.remaining_weight','booking_booking.total_weight_input','booking_booking.total_weight','booking_booking.is_for','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.quantity','booking_booking.rate','booking_booking.insurance','booking_booking.created_at','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.id as broker_id','brokers.name as broker_name','admin.name as sales_executive_name','category.tin_rate ','vendors.state_id as state_id','states.name as state_name','vendors.gst_no as gst_no'));
		$this->db->select('booking_booking.status');
		$this->db->select('empty_tin_rates.rate as empty_tin_rate');
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('states', 'states.id = vendors.state_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');  
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');

	    $this->db->join('admin', 'admin.id = booking_booking.sales_executive_id','left');

	    $this->db->join('empty_tin_rates', 'empty_tin_rates.brand_id = booking_booking.brand_id and empty_tin_rates.category_id = booking_booking.category_id and empty_tin_rates.state_id = vendors.state_id and base_rate = 1  and empty_tin_rates.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_booking`.`brand_id` and e2.category_id = booking_booking.category_id and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at and base_rate = 1 order by e2.id desc limit 1  )','left');

	    $this->db->where('booking_booking.id',$booking_id); 
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function CompnayList($condition)
	{
		$this->db->select('*');
		$this->db->from('company'); 
        $this->db->where($condition); 
        $query = $this->db->get();  
        return $query->result_array();
	}

	function SkuCompnayList($bargain_ids)
	{
		$this->db->select('booking_skus.booking_id');	
		$this->db->select('SUM(booking_skus.weight) as total_weight');		
		$this->db->select('company.*');
		$this->db->select('booking_skus.id as sku_id');	
		$this->db->from('booking_skus');    
		$this->db->join('company', 'company.id = booking_skus.company_id','left');  
        $this->db->where("booking_skus.booking_id IN ($bargain_ids)");
        $this->db->where("booking_skus.is_lock",1); 
        $this->db->group_by('company_id'); 
        $query = $this->db->get();  
        //echo $this->db->last_query(); die;
        return $query->result_array();
	}

	function UpdateBookingSku($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('booking_skus', $updatedata); 
	}


	function CompnayInfo($condition)
	{
		$this->db->select('*');
		$this->db->from('company'); 
        $this->db->where($condition); 
        $query = $this->db->get();  
        return $query->row_array();
	}

	function UpdateBookingHistoryAdd($insertdata)
	{
		return $this->db->insert('booking_update_history',$insertdata);
		//echo $this->db->last_query(); die;
	}

	function product_packing_type_summary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$group_by,$employee='',$rejected='',$unit='')
	{
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
		$userid = $this->session->userdata('admin')['id'];

		$this->db->select('products.product_type');   
		$this->db->select('SUM(booking_skus.quantity) as quantity'); 
		$this->db->select('SUM(booking_skus.weight) as weight');  
		$this->db->from('booking_skus');  
	    $this->db->join('booking_booking', 'booking_booking.id = booking_skus.booking_id','left');  
	    $this->db->join('products', 'products.id = booking_skus.product_id','left');  

	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left');

	    if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}
	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		$this->db->where('booking_skus.is_lock',1);
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
            if($status!='')
            {
            	//$this->db->where("booking_booking.status ",$status); 
            	if($status=='lock')
            	{
	        		$this->db->where("booking_booking.is_lock ",1); 
            	}
            	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	//$this->db->where("booking_booking.is_lock ",0);
	            }
            }
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");
            if($status!='')
        	{  
            	if($status=='lock')
            	{
	        		$this->db->where("booking_booking.is_lock ",1); 
            	}
            	elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("booking_booking.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("booking_booking.status ",$status);
	            	//$this->db->where("booking_booking.is_lock ",0);
	            }
	        }
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		//$this->db->where("booking_booking.status <> ",0); 
        	}
        	else
        	{ 
        		//$this->db->where("booking_booking.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("booking_booking.is_lock ",1); 
        		}
        		elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	else
	        	{
	            	$this->db->where("booking_booking.status ",$status); 
	            	//$this->db->where("booking_booking.is_lock ",0);
	        	}
        	}
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
        else
        {
        	if($status=='')
        	{ 
        		//$this->db->where("booking_booking.status <> ",0); 
        		//$this->db->where("booking_booking.is_lock ",0);
        	}
        	else
        	{ 
        		//$this->db->where("booking_booking.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("booking_booking.is_lock ",1); 
        		}
        		elseif($status=='partial_lock')
	        	{
	            	$this->db->where("booking_booking.is_lock ",2);
	        	}
	        	else
	        	{
	            	$this->db->where("booking_booking.status ",$status); 
	            	//$this->db->where("booking_booking.is_lock ",0);
	        	}
        	}
        }
		
		$this->db->group_by('products.product_type');  
		if($rejected)
		{
			$this->db->where("booking_booking.status <>",3); 
		}
		 
		if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }
        $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }

	}

	function AddPiHistory($insertdata)
	{
		$this->db->insert('pi_history',$insertdata);
		return $this->db->insert_id();
	}
	function UpdatePiHistory($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('pi_history', $updatedata); 
    	//echo $this->db->last_query(); die;
	}
	function UpdateBookingSkuPiStatus($updatedata,$condition)	
	{
 
		if($condition['sku_ids'])
		{
			$ids = $condition['sku_ids'];
			$this->db->where("id IN ($ids)");
    		return $this->db->update('booking_skus', $updatedata); 
    		//echo $this->db->last_query(); 
    	}
	}


	function GetReportBookingPerforma($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$perPage=20, $pageNo=1,$employee='',$unit=''){ 

		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;

		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('admin.unauthorized_viewers','booking_booking.is_for','booking_booking.is_mail','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.remaining_weight','booking_booking.is_close','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name','admin.team_lead_id'));
		$this->db->select('booking_booking.status');
		$this->db->select('booking_booking.remark');

		$this->db->select('count(pi_history.id) as total_pi');

		$this->db->select("count(case when pi_history.truck_number is null then 1 else 0 end) AS total_trucks",false);

		$this->db->select('count(booking_skus.pi_id) as pending_sku_pi');

	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = booking_booking.brand_id','left');  
	    $this->db->join('category', 'category.id = booking_booking.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = booking_booking.broker_id','left');
	    $this->db->join('admin', 'admin.id = booking_booking.admin_id','left'); 	
 		$this->db->join('pi_history ','find_in_set (booking_booking.id , pi_history.booking_id ) and pi_history.status= 0','LEFT',false);

 		$this->db->join('booking_skus ','booking_skus.booking_id = booking_booking.id and booking_skus.pi_id = 0','LEFT',false);
 

	    if($party_id)
	    	$this->db->where('booking_booking.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('booking_booking.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('booking_booking.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);  
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("booking_booking.is_lock",1); 
        }
        if($status!='')
        {
        	if($status=='lock')
        	{
        		$this->db->where("booking_booking.is_lock ",1); 
        	}
        	elseif($status=='partial_lock')
        	{
            	$this->db->where("booking_booking.is_lock ",2);
        	}
        	elseif($status=='mailed')
        	{
            	$this->db->where("booking_booking.is_mail ",1);
        	}
            else
            {
            	$this->db->where("booking_booking.status ",$status);
            	//$this->db->where("booking_booking.is_lock ",0);
            }
        }
        if($employee!='')
    	{ 
    		$this->db->where("booking_booking.admin_id",$employee); 
    	}

    	if($role==5) //maker
        { 
        	//$this->db->where("find_in_set($userid, admin.unauthorized_viewers)=0"); 
        	$this->db->where("( find_in_set ( $userid, admin.unauthorized_viewers ) = 0 OR  admin.unauthorized_viewers is NULL )"); 
        }

        if($unit) 
        { 
        	$this->db->where("booking_booking.production_unit",$unit); 
        }
        $this->db->limit($perPage, $startFromRecord); 
		$this->db->group_by('booking_booking.id'); 
		$this->db->order_by('booking_booking.id','DESC');  
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}

	function GetPiHistoryEnabled($booking_id)
	{
		$this->db->select('pi_history.*');
		$this->db->select('company.name as company_name');
		$this->db->select('GROUP_CONCAT(booking_booking.booking_id) as bargain_ids');
		$this->db->from('pi_history'); 
		$this->db->join('company','company.id=pi_history.company_id'); 
		$this->db->join('booking_booking ',"find_in_set ( booking_booking.id , pi_history.booking_id )");
        $this->db->where("find_in_set ($booking_id, pi_history.booking_id ) "); 
        $this->db->where("pi_history.status",0);
        $this->db->group_by("pi_history.id");
        $query = $this->db->get();   
        return $query->result_array();
	}

	function GetPiHistory($booking_id)
	{
		$this->db->select('pi_history.*');
		$this->db->select('company.name as company_name');
		$this->db->select('GROUP_CONCAT(booking_booking.booking_id) as bargain_ids');
		$this->db->from('pi_history'); 
		$this->db->join('company','company.id=pi_history.company_id'); 
		$this->db->join('booking_booking ',"find_in_set ( booking_booking.id , pi_history.booking_id )");
        $this->db->where("find_in_set ($booking_id, pi_history.booking_id ) "); 
        $this->db->group_by("pi_history.company_id");
        $query = $this->db->get();   
        return $query->result_array();
	}
	function GetBargainids($booking_id)
	{
		$this->db->select('GROUP_CONCAT(booking_booking.booking_id) as bargain_id'); 
		$this->db->from('booking_booking');  
        $this->db->where_in('booking_booking.id',$booking_id); 
        $query = $this->db->get();   
        return $query->row_array();
	}

	function updatemultiplebooking($condition)	
	{
		$this->db->where_in('id',$condition);
		$this->db->set('is_lock',0);
    	return $this->db->update('booking_booking');
    	//echo $this->db->last_query(); die;
	}



	function total_assigned_sd($conditions)
	{
		$userid = $this->session->userdata('admin')['id'];
        //$userid = $userid;
        $role = $this->session->userdata('admin')['role'];

	 	$this->db->select('vendors.id '); 
		$this->db->from('vendors');  
		$this->db->join('admin ', ' FIND_IN_SET( vendors.id , admin.vendor_id ) <> 0 ');  
        $this->db->group_by('vendors.id'); 
        if($role==1)
        {
        	$team_lead_id = $conditions['admin.team_lead_id'];
        	$admin_id = $conditions['admin.id'];
        	$this->db->where("(`admin`.`team_lead_id` = '$team_lead_id' OR `admin`.`id` = '$admin_id')");
        }
        else
        {
        	$this->db->where($conditions);
        } 
        //$this->db->where($conditions);
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->num_rows();
	}


 

	function check_sales_history($party_id)
	{
		$data_query = "SELECT id FROM `cnf_sales_history` WHERE YEAR( cnf_sales_history.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( cnf_sales_history.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and cnf_sales_history.party_id = $party_id";
		$query = $this->db->query($data_query); 
	    return $query->num_rows();
	}

	function previous_month_stock($party_id)
	{
		$data_query = "SELECT *  FROM `cnf_sales_history` WHERE YEAR( cnf_sales_history.stock_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH( cnf_sales_history.stock_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and cnf_sales_history.party_id = $party_id";
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}
 

	function previous_month_buy($party_id)
	{
		$data_query = "SELECT brands.name as brand_name,category.category_name as category_name,products.name as product_name, pi_sku_history.party_id,pi_sku_history.brand_id,pi_sku_history.category_id,pi_sku_history.product_id,pi_sku_history.created_at,sum(pi_sku_history.quantity) as purchased_qty ,sum(pi_sku_history.weight) as purchased_weight ,sum(pi_sku_history.amount) as purchased_amount FROM pi_sku_history left join brands on brands.id =pi_sku_history.brand_id left join category on category.id =pi_sku_history.category_id left join products on products.id =pi_sku_history.product_id  WHERE YEAR( pi_sku_history.created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH( pi_sku_history.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and  pi_sku_history.party_id = $party_id and pi_sku_history.status = 0 GROUP by  pi_sku_history.product_id";
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}

	function previous_month_sale($party_id)
	{
		$data_query = "SELECT brands.name as brand_name,category.category_name as category_name,products.name as product_name, pi_sku_historysecondary_booking.party_id,pi_sku_historysecondary_booking.brand_id,pi_sku_historysecondary_booking.category_id,pi_sku_historysecondary_booking.product_id,pi_sku_historysecondary_booking.created_at,sum(pi_sku_historysecondary_booking.quantity) as saled_quantity,sum(pi_sku_historysecondary_booking.weight) as saled_weight,sum(pi_sku_historysecondary_booking.amount) as saled_amount FROM pi_sku_historysecondary_booking left join brands on brands.id =pi_sku_historysecondary_booking.brand_id left join category on category.id =pi_sku_historysecondary_booking.category_id left join products on products.id =pi_sku_historysecondary_booking.product_id WHERE YEAR( pi_sku_historysecondary_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH( pi_sku_historysecondary_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and  pi_sku_historysecondary_booking.party_id = $party_id GROUP by  pi_sku_historysecondary_booking.product_id";
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}

	function AddStock($insertdata)
	{
		return $this->db->insert_batch('cnf_sales_history ',$insertdata);
		//echo $this->db->last_query(); die;
	}



	function Getcnfaccountinghistory($condition)
	{

		$this->db->select('cnf_sales_history.*');  
		$this->db->select('brands.name as brand_name'); 
		$this->db->select('category.category_name as category_name'); 
		$this->db->select('products.name'); 
		$this->db->select('products.packing_items_qty');
		$this->db->from('cnf_sales_history');  
		$this->db->join('brands', 'brands.id = cnf_sales_history.brand_id','left');  
		$this->db->join('category', 'category.id = cnf_sales_history.category_id','left'); 
		$this->db->join('products', 'products.id = cnf_sales_history.product_id','left');   
        $this->db->where($condition);  
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->result_array();
	}

	function Getcnfaccountinghistoryamount($condition)
	{

		$this->db->select('cnf_sales_history.*'); 
		$this->db->select('SUM(cnf_sales_history.opening_weight) as opening_weight'); 
		$this->db->select('SUM(cnf_sales_history.buy_weight) as buy_weight'); 
		$this->db->select('SUM(cnf_sales_history.sale_weight) as sale_weight'); 
		$this->db->select('SUM(cnf_sales_history.closing_weight) as closing_weight'); 

		$this->db->select('SUM(cnf_sales_history.bargain_amount) as bargain_amount'); 
		$this->db->select('SUM(cnf_sales_history.secondary_amount) as secondary_amount'); 
		
		$this->db->select('SUM(cnf_sales_history.opening_amount) as opening_amount'); 
		$this->db->select('SUM(cnf_sales_history.closing_amount) as closing_amount'); 

		$this->db->select('brands.name as brand_name'); 
		$this->db->select('category.category_name as category_name'); 
		$this->db->select('products.name'); 
		$this->db->select('products.packing_items_qty');
		$this->db->from('cnf_sales_history');  
		$this->db->join('brands', 'brands.id = cnf_sales_history.brand_id','left');  
		$this->db->join('category', 'category.id = cnf_sales_history.category_id','left'); 
		$this->db->join('products', 'products.id = cnf_sales_history.product_id','left');   
        $this->db->where($condition);  
        $this->db->group_by('cnf_sales_history.category_id');  
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->result_array();
	}

	function GetPrimaryPi($condition)
	{
		$this->db->select('sum(pi_history.pi_amount) as total_purchased_pi_amount');  
		$this->db->from('pi_history ');     
        $this->db->where($condition);  
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->row_array();
	}

	function GetSecondaryPi($condition)
	{
		$this->db->select('sum(pi_history_secondary_booking.pi_amount) as total_secondary_pi_amount');  
		$this->db->from('pi_history_secondary_booking ');     
        $this->db->where($condition);  
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->row_array();
	}

	function add_first_sales_history($party_id)
	{
		$data_query = "SELECT coalesce(secondary.saled_quantity,0) as saled_quantity, COALESCE(secondary.saled_weight,0) as saled_weight, booking_skus.created_at,sum(booking_skus.quantity) as purchased_qty ,sum(booking_skus.weight) as purchased_weight,booking_skus.product_id FROM `booking_skus` join booking_booking on booking_booking.id =booking_skus.booking_id 
left join (SELECT secondary_booking_skus.created_at,sum(secondary_booking_skus.quantity) as saled_quantity ,sum(secondary_booking_skus .weight) as saled_weight,secondary_booking_skus.product_id FROM `secondary_booking_skus` join secondary_booking  on secondary_booking.id =secondary_booking_skus.secondary_booking_id WHERE YEAR( secondary_booking_skus.created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH( secondary_booking_skus.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and secondary_booking.supply_from = $party_id and secondary_booking_skus.pi_id != 0  GROUP by secondary_booking_skus.product_id) as secondary on booking_skus.product_id = secondary.product_id

WHERE YEAR( booking_skus.created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH( booking_skus.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and  booking_booking.party_id = $party_id and booking_skus.pi_id != 0 GROUP by  booking_skus.product_id;";
		$query = $this->db->query($data_query); 
	    return $query->num_rows();
	}




	function AddPiSkuHistory($insertdata)
	{
		return $this->db->insert_batch('pi_sku_history',$insertdata); 
	}


	function GetBargainhistory($condition)
	{
		$this->db->select('sum(pi_sku_history.amount) as total_pi_amount');  
		$this->db->select('sum(pi_sku_history.amount) as total_pi_amount');  
		$this->db->from('pi_sku_history ');     
        $this->db->where($condition);  
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->result_array();
	}

	function GetSecondaryHistory($condition)
	{
		$this->db->select('sum(pi_sku_history.amount) as total_pi_amount');  
		$this->db->from('pi_sku_history ');     
        $this->db->where($condition);  
        $query = $this->db->get();   
        //echo $this->db->last_query(); die;
        return $query->row_array();
	}


	function current_month_buy($party_id)
	{
		$data_query = "SELECT  brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_history.party_id,pi_sku_history.brand_id,pi_sku_history.category_id,pi_sku_history.product_id,pi_sku_history.created_at,sum(pi_sku_history.quantity) as purchased_qty ,sum(pi_sku_history.weight) as purchased_weight ,sum(pi_sku_history.amount) as purchased_amount FROM pi_sku_history  left join brands on brands.id =pi_sku_history.brand_id left join category on category.id =pi_sku_history.category_id left join products on products.id =pi_sku_history.product_id WHERE YEAR( pi_sku_history.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( pi_sku_history.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and  pi_sku_history.party_id = $party_id and pi_sku_history.status = 0  GROUP by  pi_sku_history.product_id";
		$query = $this->db->query($data_query);  
	    return $query->result_array();
	}

	function current_month_sale($party_id)
	{
		$data_query = "SELECT  brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_historysecondary_booking.party_id,pi_sku_historysecondary_booking.brand_id,pi_sku_historysecondary_booking.category_id,pi_sku_historysecondary_booking.product_id,pi_sku_historysecondary_booking.created_at,sum(pi_sku_historysecondary_booking.quantity) as saled_quantity,sum(pi_sku_historysecondary_booking.weight) as saled_weight,sum(pi_sku_historysecondary_booking.amount) as saled_amount FROM pi_sku_historysecondary_booking left join brands on brands.id =pi_sku_historysecondary_booking.brand_id left join category on category.id =pi_sku_historysecondary_booking.category_id left join products on products.id =pi_sku_historysecondary_booking.product_id WHERE YEAR( pi_sku_historysecondary_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( pi_sku_historysecondary_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and  party_id = $party_id GROUP by  pi_sku_historysecondary_booking.product_id";
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}



	

	function current_month_buy_amount($party_id)
	{

		$data_query = "SELECT brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_history.party_id,pi_sku_history.brand_id,pi_sku_history.category_id,pi_sku_history.product_id,pi_sku_history.created_at,sum(pi_sku_history.quantity) as purchased_qty ,sum(pi_sku_history.weight) as purchased_weight ,sum(pi_sku_history.amount) as purchased_amount FROM pi_sku_history left join brands on brands.id =pi_sku_history.brand_id left join category on category.id =pi_sku_history.category_id left join products on products.id =pi_sku_history.product_id  WHERE YEAR( pi_sku_history.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( pi_sku_history.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and  pi_sku_history.party_id = $party_id and pi_sku_history.status = 0 GROUP by  pi_sku_history.category_id";
		$query = $this->db->query($data_query);  
	    return  $query->result_array(); 
	}

	function current_month_sale_amount($party_id)
	{
		$data_query = "SELECT brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_historysecondary_booking.party_id,pi_sku_historysecondary_booking.brand_id,pi_sku_historysecondary_booking.category_id,pi_sku_historysecondary_booking.product_id,pi_sku_historysecondary_booking.created_at,sum(pi_sku_historysecondary_booking.quantity) as saled_quantity,sum(pi_sku_historysecondary_booking.weight) as saled_weight,sum(pi_sku_historysecondary_booking.amount) as saled_amount FROM pi_sku_historysecondary_booking left join brands on brands.id =pi_sku_historysecondary_booking.brand_id left join category on category.id =pi_sku_historysecondary_booking.category_id left join products on products.id =pi_sku_historysecondary_booking.product_id WHERE YEAR( pi_sku_historysecondary_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( pi_sku_historysecondary_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and  pi_sku_historysecondary_booking.party_id = $party_id GROUP by  pi_sku_historysecondary_booking.category_id";
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}



	function check_sales_history_month($party_id,$month_date)
	{
		$data_query = "SELECT id FROM `cnf_sales_history` WHERE cnf_sales_history.stock_date like '%$month_date%' and cnf_sales_history.party_id = $party_id"; 
		$query = $this->db->query($data_query); 
	    return $query->num_rows();
	}


	function past_month_buy_amount($party_id,$month_date)
	{

		$data_query = "SELECT brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_history.party_id,pi_sku_history.brand_id,pi_sku_history.category_id,pi_sku_history.product_id,pi_sku_history.created_at,sum(pi_sku_history.quantity) as purchased_qty ,sum(pi_sku_history.weight) as purchased_weight ,sum(pi_sku_history.amount) as purchased_amount FROM pi_sku_history left join brands on brands.id =pi_sku_history.brand_id left join category on category.id =pi_sku_history.category_id left join products on products.id =pi_sku_history.product_id  WHERE pi_sku_history.created_at like '%$month_date%' and  pi_sku_history.party_id = $party_id and pi_sku_history.status = 0 GROUP by  pi_sku_history.category_id"; 
		$query = $this->db->query($data_query);  
	    return  $query->result_array(); 
	}

	function past_month_sale_amount($party_id,$month_date)
	{
		$data_query = "SELECT brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_historysecondary_booking.party_id,pi_sku_historysecondary_booking.brand_id,pi_sku_historysecondary_booking.category_id,pi_sku_historysecondary_booking.product_id,pi_sku_historysecondary_booking.created_at,sum(pi_sku_historysecondary_booking.quantity) as saled_quantity,sum(pi_sku_historysecondary_booking.weight) as saled_weight,sum(pi_sku_historysecondary_booking.amount) as saled_amount FROM pi_sku_historysecondary_booking left join brands on brands.id =pi_sku_historysecondary_booking.brand_id left join category on category.id =pi_sku_historysecondary_booking.category_id left join products on products.id =pi_sku_historysecondary_booking.product_id WHERE pi_sku_historysecondary_booking.created_at like '%$month_date%' and  pi_sku_historysecondary_booking.party_id = $party_id GROUP by  pi_sku_historysecondary_booking.category_id";
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}


	function past_month_buy($party_id,$month_date)
	{
		$data_query = "SELECT  brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_history.party_id,pi_sku_history.brand_id,pi_sku_history.category_id,pi_sku_history.product_id,pi_sku_history.created_at,sum(pi_sku_history.quantity) as purchased_qty ,sum(pi_sku_history.weight) as purchased_weight ,sum(pi_sku_history.amount) as purchased_amount FROM pi_sku_history  left join brands on brands.id =pi_sku_history.brand_id left join category on category.id =pi_sku_history.category_id left join products on products.id =pi_sku_history.product_id WHERE pi_sku_history.created_at like '%$month_date%' and  pi_sku_history.party_id = $party_id and pi_sku_history.status = 0 GROUP by  pi_sku_history.product_id";
		$query = $this->db->query($data_query);  
	    return $query->result_array();
	}

	function past_month_sale($party_id,$month_date)
	{
		$data_query = "SELECT  brands.name as brand_name,category.category_name as category_name,products.name as name, pi_sku_historysecondary_booking.party_id,pi_sku_historysecondary_booking.brand_id,pi_sku_historysecondary_booking.category_id,pi_sku_historysecondary_booking.product_id,pi_sku_historysecondary_booking.created_at,sum(pi_sku_historysecondary_booking.quantity) as saled_quantity,sum(pi_sku_historysecondary_booking.weight) as saled_weight,sum(pi_sku_historysecondary_booking.amount) as saled_amount FROM pi_sku_historysecondary_booking left join brands on brands.id =pi_sku_historysecondary_booking.brand_id left join category on category.id =pi_sku_historysecondary_booking.category_id left join products on products.id =pi_sku_historysecondary_booking.product_id WHERE pi_sku_historysecondary_booking.created_at like '%$month_date%' and  party_id = $party_id GROUP by  pi_sku_historysecondary_booking.product_id"; 
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}

	function past_month_stock($party_id,$month_date)
	{
		$data_query = "SELECT *  FROM `cnf_sales_history` WHERE cnf_sales_history.stock_date like '%$month_date%'   and cnf_sales_history.party_id = $party_id";
		$query = $this->db->query($data_query); 
	    return $query->result_array();
	}


	function UpdatePiHistorySku($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('pi_sku_history', $updatedata); 
    	//echo $this->db->last_query(); die;
	}

	function pi_sku_history_bookingremove($condition){
	  $this->db->where($condition);
	  return $this->db->delete('pi_sku_history');
	}


	function getinsurance($condition)
	{
		$this->db->select('insurance');
        $this->db->from('vendors');
        $this->db->join('empty_tin_rates', 'empty_tin_rates.state_id = vendors.state_id','left');  
        $this->db->where($condition); 
        $this->db->order_by('empty_tin_rates.id','DESC'); 
        $this->db->limit(1); 
        $query = $this->db->get();
		//echo $this->db->last_query(); die;  
        $response = 0;
        if($query->num_rows())
        {
        	$results =  $query->row_array();
        	$response = $results['insurance'];
        }
        return $response;
	}


	function checkbookingstatus($booking_id)
	{ 
		$this->db->select('id');
        $this->db->from('booking_skus');  
        $this->db->where('status',1); 
        $query = $this->db->get();
		//echo $this->db->last_query(); die;   
        return $query->num_rows;
	}
}
