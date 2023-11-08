<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
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
        $response = array();
        if($query->num_rows())
        {
        	$results =  $query->result_array();
        	foreach ($results as $key => $value) {
        		$response[$value['product_id']] = $value;
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
        if($query->num_rows())
        {
        	$this->db->where($condition);
    		return $this->db->update('booking_skus', $skudata);
        }
        else
        {
        	return $this->db->insert('booking_skus',$skudata);
			//echo $this->db->last_query(); die;
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
        $this->db->from('booking_booking');
        $this->db->where('id',$booking_number); 
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        $row = $query->row_array();
        return $row['booking_id'];
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
		$this->db->select(array('booking_booking.remark','booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.is_close','booking_booking.remaining_weight','booking_booking.total_weight_input','booking_booking.total_weight','booking_booking.is_for','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.quantity','booking_booking.rate','booking_booking.insurance','booking_booking.created_at','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.id as broker_id','brokers.name as broker_name','admin.name as sales_executive_name'));
	    $this->db->from('booking_booking'); 
	    $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
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
	}

	function DeleteBooking($condition){
	  $this->db->where($condition);
	  return $this->db->delete('booking');
	}

	function GetReportBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$perPage=20, $pageNo=1){ 

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
        
		$this->db->select(array('booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.remaining_weight','booking_booking.is_close','booking_booking.total_weight as weight', 'booking_booking.quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name'));
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
            if($status!='')
            	$this->db->where("booking_booking.status ",$status); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            if($status!='')
            	$this->db->where("booking_booking.status ",$status); 
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		$this->db->where("booking_booking.status <> ",0); 
        	}
        	else
        	{ 
        		$this->db->where("booking_booking.status  ",$status); 
        	}
        	//$this->db->where("booking_booking.is_lock",1); 
        }
        $this->db->limit($perPage, $startFromRecord);
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
	    $this->db->from('booking_remarks');  
	    $this->db->where('booking_id',$booking_id); 
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






    function CountBooking($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status=''){ 
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
            if($status!='')
            	$this->db->where("booking_booking.status ",$status); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            if($status!='')
            	$this->db->where("booking_booking.status ",$status); 
            $this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		$this->db->where("booking_booking.status <> ",0); 
        	}
        	else
        	{ 
        		$this->db->where("booking_booking.status  ",$status); 
        	}
        	$this->db->where("booking_booking.is_lock",1); 
        }

		$this->db->order_by('booking_booking.id','ASC');  
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    return $query->num_rows(); 
	}



	function GetBookingSummary($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$group_by){   
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking_booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('booking_booking.shipment_date','booking_booking.production_unit','booking_booking.is_lock','booking_booking.remaining_weight','booking_booking.is_close','SUM(booking_booking.total_weight) as weight', 'SUM(booking_booking.quantity) as quantity','booking_booking.booking_id','booking_booking.id','booking_booking.party_id','booking_booking.brand_id','booking_booking.category_id','booking_booking.rate','booking_booking.created_at','booking_booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name'));
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
            if($status!='')
            	$this->db->where("booking_booking.status ",$status); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            if($status!='')
            	$this->db->where("booking_booking.status ",$status); 
            //$this->db->where("booking_booking.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		$this->db->where("booking_booking.status <> ",0); 
        	}
        	else
        	{ 
        		$this->db->where("booking_booking.status  ",$status); 
        	}
        	//$this->db->where("booking_booking.is_lock",1); 
        } 
		
		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				if($group_by_value=='place')
				{
					$this->db->group_by('vendors.city_id');  
					$this->db->order_by('city.name','ASC'); 
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
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}
}