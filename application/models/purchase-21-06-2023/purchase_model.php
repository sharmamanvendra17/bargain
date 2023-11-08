<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}

	function GetBookingInfoById1($purchase_id) { 
		//echo date('Y-m-d');  die;
		$cur_date = date('Y-m-d');
		$condition = "`purchase_order.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('purchase_order.*','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','category.category_name as category_name','brokers.id as broker_id','brokers.name as broker_name','admin.name as sales_executive_name'));
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');    
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');  
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');

	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');

	    $this->db->where('purchase_order.id',$purchase_id); 
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

	function checkpendingorder($condition){   
		$this->db->select('id');
        $this->db->from('purchase_order');
        $this->db->where($condition); 
        $query = $this->db->get();  
        return $query->num_rows();
	}

	function getlast_purchase_id($book_chek_date){  

		$select_query =  "SELECT MAX(purchase_id) as purchase_id FROM purchase_order"; 

		$query = $this->db->query($select_query);
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row['purchase_id'];  
	    }
	}
	function AddOrder($insertdata)
	{
		$this->db->insert('purchase_order',$insertdata);  
		$booking_number =  $this->db->insert_id();
		$this->db->select('purchase_id');
        $this->db->from('purchase_order');
        $this->db->where('id',$booking_number); 
        $query = $this->db->get();  
        $row = $query->row_array();
        return $row['purchase_id'];
		//echo $this->db->last_query(); die;
	}


	function GetBookingList($condition,$perPage=20, $pageNo=1){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`purchase_order.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('purchase_order.weight','purchase_order.purchase_id','purchase_order.id','purchase_order.party_id','purchase_order.product_id','purchase_order.category_id','purchase_order.rate','purchase_order.created_at','purchase_order.admin_id','pur_vendors.name as party_name','pur_vendors.city_id as city_id','city.name as city_name','pur_products.product_name','pur_category.category_name as category_name','pur_brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('purchase_order.status');
		$this->db->select('purchase_order.remark');
	    $this->db->from('purchase_order'); 
	    $this->db->join('pur_vendors', 'pur_vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');  
	    $this->db->join('pur_products', 'pur_products.id = purchase_order.product_id','left');  
	    $this->db->join('pur_category', 'pur_category.id = purchase_order.category_id','left');   
	    $this->db->join('pur_brokers', 'pur_brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
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
        	$this->db->where("purchase_order.status <> ",0); 
        }
        //echo "<pre>"; print_r($condition); die;
        if(isset($condition['is_lock']))
        {
        	$this->db->where("purchase_order.is_lock ",1); 
        }
	    	//if($condition)
	    	//$this->db->where($condition); 

	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    //$this->db->group_by('purchase_order.purchase_id'); 
	    $this->db->limit($perPage, $startFromRecord); 
        $this->db->order_by('purchase_order.id','DESC');  

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
		$date_range = "`purchase_order.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('purchase_order.weight','purchase_order.purchase_id','purchase_order.id','purchase_order.party_id','purchase_order.product_id','purchase_order.category_id','purchase_order.rate','purchase_order.created_at','purchase_order.admin_id','pur_vendors.name as party_name','pur_vendors.city_id as city_id','city.name as city_name','pur_products.product_name','pur_category.category_name as category_name','pur_brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('purchase_order.status');
		$this->db->select('purchase_order.remark');
	    $this->db->from('purchase_order'); 
	    $this->db->join('pur_vendors', 'pur_vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');  
	    $this->db->join('pur_products', 'pur_products.id = purchase_order.product_id','left');  
	    $this->db->join('pur_category', 'pur_category.id = purchase_order.category_id','left');   
	    $this->db->join('pur_brokers', 'pur_brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
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
        	$this->db->where("purchase_order.status <> ",0); 
        }
        
        $this->db->order_by('purchase_order.id','DESC');  
	    $query = $this->db->get();  
	   	return $query->num_rows();
	}
	/* ===========End ========= */


	function AddInvoice($insertdata)
	{
		return $this->db->insert('invoice',$insertdata);
		//echo $this->db->last_query(); die;
	}
	function AddRemark($insertdata)
	{
		return $this->db->insert('pur_order_remarks',$insertdata);
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
    	return $this->db->update('purchase_order', $updatedata);
	}
	
	function getbookingid($book_chek_date,$party_id){  
		 
		$dtae_book =  date("Y-m-d", strtotime($book_chek_date));
		$select_query =  "select MAX(purchase_id) as purchase_id, created_at from purchase_order where date(created_at)='$dtae_book' and party_id=$party_id"; 
		$query = $this->db->query($select_query);
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0)
	    {
	    	$row = $query->row_array();
	    	if(!is_null($row['purchase_id']))
	    	{
	    		return $row['purchase_id']-1;
	    	}
	    	else
	    	{
	    		$select_query =  "SELECT CASE WHEN MONTH(created_at)>=4 THEN concat(YEAR(created_at), '-',YEAR(created_at)+1) ELSE concat(YEAR(created_at)-1,'-', YEAR(created_at)) END AS financial_year,MAX(purchase_id) as purchase_id FROM purchase_order GROUP BY financial_year having financial_year = (CASE WHEN MONTH('$book_chek_date')>=4 THEN concat(YEAR('$book_chek_date'), '-',YEAR('$book_chek_date')+1) ELSE concat(YEAR('$book_chek_date')-1,'-', YEAR('$book_chek_date')) END)"; 

				$query = $this->db->query($select_query);
			    //echo $this->db->last_query(); die;
			    if($query->num_rows() > 0 )
			    {
			      $row = $query->row_array();
			      //echo "<pre>";  print_r($row); die;
			      return $row['purchase_id'];  
			    }
	    	}
	    } 
	}


	function GetBookingInfoById($purchase_id) { 
		//echo date('Y-m-d');  die;
		$cur_date = date('Y-m-d');
		$condition = "`purchase_order.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('purchase_order.*','pur_vendors.name as party_name','pur_vendors.city_id as city_id','city.name as city_name','pur_category.category_name as category_name','pur_products.product_name as product_name','pur_brokers.id as broker_id','pur_brokers.name as broker_name','admin.name as sales_executive_name'));
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('pur_vendors', 'pur_vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');    
	    $this->db->join('pur_category', 'pur_category.id = purchase_order.category_id','left');  
	    $this->db->join('pur_products', 'pur_products.id = purchase_order.product_id','left');  
	    $this->db->join('pur_brokers', 'pur_brokers.id = purchase_order.broker_id','left');

	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');

	    $this->db->where('purchase_order.purchase_id',$purchase_id); 
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


	

	

	function GetBooking($condition){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('SUM(booking.quantity) as quantity','booking.payment_terms','booking.dispatch_delivery_terms','booking.for_total','booking.is_for','booking.purchase_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.rate','booking.loose_rate','booking.total_loose_rate','booking.total_weight','booking.total_price','booking.created_at','booking.insurance','booking.insurance_amount','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.product_type as product_type','brokers.name as broker_name','admin.name as admin_name'));
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
    	return $this->db->update('purchase_order', $updatedata);
    	//echo $this->db->last_query(); die;
	}

	function DeleteBooking($condition){
	  $this->db->where($condition);
	  return $this->db->delete('booking');
	}

	function GetReportBooking($party_id,$product_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$perPage=20, $pageNo=1,$employee='',$bagainnumber='',$broker=''){ 

		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;

		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('purchase_order.*','pur_vendors.name as party_name','pur_vendors.city_id as city_id','city.name as city_name','pur_products.product_name as product_name','pur_category.category_name as category_name','pur_brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('purchase_order.status');
		$this->db->select('purchase_order.remark');
	    $this->db->from('purchase_order'); 
	    $this->db->join('pur_vendors', 'pur_vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');    
	    $this->db->join('pur_category', 'pur_category.id = purchase_order.category_id','left');
	    $this->db->join('pur_products', 'pur_products.id = purchase_order.product_id','left');
	    $this->db->join('pur_brokers', 'pur_brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 
	    
	    if($party_id)
	    	$this->db->where('purchase_order.party_id',$party_id);
	    if($product_id)
	    	$this->db->where('purchase_order.product_id',$product_id);
	    if($category_id)
	    	$this->db->where('purchase_order.category_id',$category_id);
	   
	    if($bagainnumber=='')
	   	{
		    if($condition!='')
		    $this->db->where($condition); 
		}
		if($bagainnumber)
	    	$this->db->where('purchase_order.purchase_id',$bagainnumber);
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);  
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("purchase_order.is_lock",1); 
        }
        if($status!='')
        {
        	if($status=='lock')
        	{
        		$this->db->where("purchase_order.is_lock ",1); 
        	}
        	elseif($status=='mailed')
        	{
            	$this->db->where("purchase_order.is_mail ",1);
        	}
            else
            {
            	$this->db->where("purchase_order.status ",$status);
            	//$this->db->where("purchase_order.is_lock ",0);
            }
        }
        if($employee!='')
    	{ 
    		$this->db->where("purchase_order.admin_id",$employee); 
    	}

    	if($broker!='')
    	{ 
    		$this->db->where("purchase_order.broker_id",$broker); 
    	}

    	if($role==5) //maker
        { 
        	$this->db->where("find_in_set($userid, admin.unauthorized_viewers)=0"); 
        }
 
        $this->db->limit($perPage, $startFromRecord);
		$this->db->order_by('purchase_order.id','DESC');  
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
        
		$this->db->select(array('booking.for_total','booking.is_for','booking.purchase_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.quantity','booking.rate','booking.insurance','booking.total_price','booking.created_at','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.loose_rate as loose_rate','products.product_type as product_type','admin.name as admin_name'));
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

	

 

	function CheckBooking($book_chek_date){ 
		//echo date('Y-m-d');  die;
		$select_query =  "SELECT CASE WHEN MONTH(created_at)>=4 THEN concat(YEAR(created_at), '-',YEAR(created_at)+1) ELSE concat(YEAR(created_at)-1,'-', YEAR(created_at)) END AS financial_year,MAX(purchase_id) FROM booking GROUP BY financial_year having financial_year = (CASE WHEN MONTH('$book_chek_date')>=4 THEN concat(YEAR('$book_chek_date'), '-',YEAR('$book_chek_date')+1) ELSE concat(YEAR('$book_chek_date')-1,'-', YEAR('$book_chek_date')) END)"; 

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



	function GetBookingInfoDetails($purchase_id){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking.quantity','booking.payment_terms','booking.dispatch_delivery_terms','booking.for_total','booking.is_for','booking.purchase_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.rate','booking.loose_rate','booking.total_loose_rate','booking.total_weight','booking.total_price','booking.created_at','booking.insurance','booking.insurance_amount','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.product_type as product_type','brokers.name as broker_name','admin.name as admin_name'));
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
	    $this->db->where('booking.purchase_id',$purchase_id);  
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



	

	function GetBookingRemarks($purchase_id) {  
		 
		$this->db->select('booking_remarks.*');
	    $this->db->from('booking_remarks');  
	    $this->db->where('purchase_id',$purchase_id); 
	    $query = $this->db->get(); 
	    return $row = $query->result_array();  
	}


	function GetBookingInfoDetailsPdf($purchase_id){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('booking.quantity','booking.payment_terms','booking.dispatch_delivery_terms','booking.for_total','booking.is_for','booking.purchase_id','booking.id','booking.party_id','booking.brand_id','booking.category_id','booking.product_id','booking.rate','booking.loose_rate','booking.total_loose_rate','booking.total_weight','booking.total_price','booking.created_at','booking.insurance','booking.insurance_amount','booking.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','products.name as product_name','products.weight as product_weight','products.product_type as product_type','brokers.name as broker_name','admin.name as admin_name'));
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
	    $this->db->where('booking.purchase_id',$purchase_id);  
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

	function UpdateAproveStatusAll($purchase_id)	
	{
		$updatedata = array('status'=> 2);
		$this->db->where('purchase_id',$purchase_id); 
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






    function CountBooking($party_id,$product_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$employee='',$bagainnumber='',$broker=''){ 
		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('purchase_order.*','pur_vendors.name as party_name','pur_vendors.city_id as city_id','city.name as city_name','pur_products.product_name as product_name','pur_category.category_name as category_name','pur_brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('purchase_order.status');
		$this->db->select('purchase_order.remark');
	    $this->db->from('purchase_order'); 
	    $this->db->join('pur_vendors', 'pur_vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = pur_vendors.city_id','left');    
	    $this->db->join('pur_category', 'pur_category.id = purchase_order.category_id','left');
	    $this->db->join('pur_products', 'pur_products.id = purchase_order.product_id','left');
	    $this->db->join('pur_brokers', 'pur_brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 
	    
	    if($party_id)
	    	$this->db->where('purchase_order.party_id',$party_id);
	    if($product_id)
	    	$this->db->where('purchase_order.product_id',$product_id);
	    if($category_id)
	    	$this->db->where('purchase_order.category_id',$category_id);
	   
	    if($bagainnumber=='')
	   	{
		    if($condition!='')
		    $this->db->where($condition); 
		}
		if($bagainnumber)
	    	$this->db->where('purchase_order.purchase_id',$bagainnumber);
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);  
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("purchase_order.is_lock",1); 
        }
        if($status!='')
        {
        	if($status=='lock')
        	{
        		$this->db->where("purchase_order.is_lock ",1); 
        	}
        	elseif($status=='mailed')
        	{
            	$this->db->where("purchase_order.is_mail ",1);
        	}
            else
            {
            	$this->db->where("purchase_order.status ",$status);
            	//$this->db->where("purchase_order.is_lock ",0);
            }
        }
        if($employee!='')
    	{ 
    		$this->db->where("purchase_order.admin_id",$employee); 
    	}
    	if($broker!='')
    	{ 
    		$this->db->where("purchase_order.broker_id",$broker); 
    	}
    	if($role==5) //maker
        { 
        	$this->db->where("find_in_set($userid, admin.unauthorized_viewers)=0"); 
        }

        

		$this->db->order_by('purchase_order.id','ASC');  
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->num_rows(); 
	}



	function GetBookingSummary($party_id,$product_id
		,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$group_by,$employee='',$unit=''){   
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('category.freight_rate','category.tin_rate','count(purchase_order.id) as bargain_count','purchase_order.shipment_date','purchase_order.production_unit','purchase_order.is_lock','purchase_order.remaining_weight','purchase_order.is_close','SUM(purchase_order.total_weight) as weight', 'SUM(purchase_order.quantity) as quantity','purchase_order.purchase_id','purchase_order.id','purchase_order.party_id','purchase_order.brand_id','purchase_order.category_id','purchase_order.created_at','purchase_order.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','states.name as state_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name','vendors.state_id'));

		 
		$this->db->select("AVG( if( `vendors`.`state_id` <> 4 AND  `vendors`.`state_id` <> 22 AND  `vendors`.`state_id` <> 23 AND  `vendors`.`state_id` <> 24 AND  `vendors`.`state_id` <> 25 AND  `vendors`.`state_id` <> 30 AND  `vendors`.`state_id` <> 33, `purchase_order`.`rate`, purchase_order.avg_condition )) as avg_rate_other");
		$this->db->select("AVG( if( `vendors`.`state_id` = 4 OR  `vendors`.`state_id` = 22 OR  `vendors`.`state_id` = 23 OR  `vendors`.`state_id` = 24 OR  `vendors`.`state_id` = 25 OR  `vendors`.`state_id` = 30 OR  `vendors`.`state_id` = 33 , `purchase_order`.`rate`, purchase_order.avg_condition )) as avg_rate_aasam");
		$this->db->select('purchase_order.status');
		$this->db->select('purchase_order.remark');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('states', 'states.id = vendors.state_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 	

 		if($employee!='')
    	{ 
    		$this->db->where("purchase_order.admin_id",$employee); 
    	}
	    
	    if($party_id)
	    	$this->db->where('purchase_order.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('purchase_order.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('purchase_order.category_id',$category_id);
	   
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
	        		$this->db->where("purchase_order.is_lock ",1); 
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	$this->db->where("purchase_order.is_lock ",0);
	            }
	        } 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
            if($status!='')
	        {
	        	if($status=='lock')
	        	{
	        		$this->db->where("purchase_order.is_lock ",1); 
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	$this->db->where("purchase_order.is_lock ",0);
	            }
	        }
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		//$this->db->where("purchase_order.status <> ",0); 
        	}
        	else
        	{ 
        		//$this->db->where("purchase_order.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("purchase_order.is_lock ",1); 
        		}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	$this->db->where("purchase_order.is_lock ",0);
	            }
        	}
        	//$this->db->where("purchase_order.is_lock",1); 
        } 
        else
        {
        	if($status!='')
	        {
	        	if($status=='lock')
	        	{
	        		$this->db->where("purchase_order.is_lock ",1); 
	        	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	$this->db->where("purchase_order.is_lock ",0);
	            }
	        }
        }
		$this->db->where("purchase_order.status <>",3);
		if($unit) 
        { 
        	$this->db->where("purchase_order.production_unit",$unit); 
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
					$this->db->group_by('purchase_order.'.$group_by_value);  
					$this->db->order_by('brands.name','ASC');
				}
			}
		}
		else
		{
			$this->db->order_by('purchase_order.id','DESC'); 
		}
		

		  
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}


	


	function GetBookingSummarySumReport($party_id,$brand_id,$category_id,$booking_date_from,$booking_date_to,$booked_by='',$status='',$group_by,$employee='',$rejected='',$unit=''){   
		$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from!='' && $booking_date_to=='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('count(purchase_order.id) as bargain_count','SUM(purchase_order.total_weight) as weight', 'SUM(purchase_order.quantity) as quantity')); 
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 
	    if($employee!='')
    	{ 
    		$this->db->where("purchase_order.admin_id",$employee); 
    	}
	    if($party_id)
	    	$this->db->where('purchase_order.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('purchase_order.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('purchase_order.category_id',$category_id);
	   
	    if($condition!='')
	    $this->db->where($condition); 
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
            if($status!='')
            {
            	//$this->db->where("purchase_order.status ",$status); 
            	if($status=='lock')
            	{
	        		$this->db->where("purchase_order.is_lock ",1); 
            	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	//$this->db->where("purchase_order.is_lock ",0);
	            }
            }
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");
            if($status!='')
        	{  
            	if($status=='lock')
            	{
	        		$this->db->where("purchase_order.is_lock ",1); 
            	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	//$this->db->where("purchase_order.is_lock ",0);
	            }
	        }
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		//$this->db->where("purchase_order.status <> ",0); 
        	}
        	else
        	{ 
        		//$this->db->where("purchase_order.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("purchase_order.is_lock ",1); 
        		}
	        	else
	        	{
	            	$this->db->where("purchase_order.status ",$status); 
	            	//$this->db->where("purchase_order.is_lock ",0);
	        	}
        	}
        	//$this->db->where("purchase_order.is_lock",1); 
        } 
        else
        {
        	if($status=='')
        	{ 
        		//$this->db->where("purchase_order.status <> ",0); 
        		//$this->db->where("purchase_order.is_lock ",0);
        	}
        	else
        	{ 
        		//$this->db->where("purchase_order.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("purchase_order.is_lock ",1); 
        		}
	        	else
	        	{
	            	$this->db->where("purchase_order.status ",$status); 
	            	//$this->db->where("purchase_order.is_lock ",0);
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
					$this->db->group_by('purchase_order.'.$group_by_value);  
					$this->db->order_by('brands.name','ASC');
				}
			}
			$this->db->order_by('purchase_order.status','ASC'); 
		}
		else
		{
			$this->db->order_by('purchase_order.id','DESC'); 
		}
		if($rejected)
		{
			$this->db->where("purchase_order.status <>",3); 
		}
		 
		if($unit) 
        { 
        	$this->db->where("purchase_order.production_unit",$unit); 
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
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`purchase_order.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('count(purchase_order.id) as bargain_count','SUM(purchase_order.total_weight) as weight', 'SUM(purchase_order.quantity) as quantity')); 
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 	
 		if($employee!='')
    	{ 
    		$this->db->where("purchase_order.admin_id",$employee); 
    	}
	    
	    if($party_id)
	    	$this->db->where('purchase_order.party_id',$party_id);
	    if($brand_id)
	    	$this->db->where('purchase_order.brand_id',$brand_id);
	    if($category_id)
	    	$this->db->where('purchase_order.category_id',$category_id);
	   
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
             
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("purchase_order.is_lock",1); 
        } 
		$this->db->where("purchase_order.is_lock ",1);
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
					$this->db->group_by('purchase_order.'.$group_by_value);  
					$this->db->order_by('brands.name','ASC');
				}
			}
		}
		else
		{
			$this->db->order_by('purchase_order.id','DESC'); 
		}
		if($unit) 
        { 
        	$this->db->where("purchase_order.production_unit",$unit); 
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
        
		$this->db->select(array('SUM(purchase_order.total_weight_input) as weight', 'SUM(purchase_order.quantity) as quantity')); 
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 	  
		$this->db->where('purchase_order.party_id',$condition['party_id']); 
		$this->db->where('purchase_order.is_mail',$condition['is_mail']); 
		$this->db->where('purchase_order.is_lock',$condition['is_lock']);
	    $this->db->where('purchase_order.status',$condition['status']);
	   
	     

		if($role==1) //maker
        { 
            $this->db->where('purchase_order.admin_id' , $userid);   
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
             
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("purchase_order.is_lock",1); 
        } 

		$this->db->group_by('purchase_order.party_id');  
		 

		  
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
		$this->db->from('purchase_order'); 
		$this->db->where($condition); 
		$query = $this->db->get(); 
		//echo $this->db->last_query(); die;
		return $query->num_rows();
	}


	function getpenidngbargainInfo($condition) { 
		//echo date('Y-m-d');  die;
		 
		$this->db->select(array('purchase_order.remark','purchase_order.shipment_date','purchase_order.production_unit','purchase_order.is_lock','purchase_order.is_close','purchase_order.remaining_weight','purchase_order.total_weight_input','purchase_order.total_weight','purchase_order.is_for','purchase_order.purchase_id','purchase_order.id','purchase_order.party_id','purchase_order.brand_id','purchase_order.category_id','purchase_order.quantity','purchase_order.rate','purchase_order.insurance','purchase_order.created_at','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.id as broker_id','brokers.name as broker_name','admin.name as sales_executive_name'));
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');  
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');

	    $this->db->join('admin', 'admin.id = purchase_order.sales_executive_id','left');

	    if(isset($condition['party_id']))
	    	$this->db->where('purchase_order.party_id',$condition['party_id']); 
	    if(isset($condition['is_mail']))
	    	$this->db->where('purchase_order.is_mail',$condition['is_mail']); 
	    if(isset($condition['is_lock']))
	    	$this->db->where('purchase_order.is_lock',$condition['is_lock']); 
	    if(isset($condition['status']))
	    	$this->db->where('purchase_order.status',$condition['status']);
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
    	return $this->db->update('purchase_order');
    	//echo $this->db->last_query(); die;
	}




	function GetBookingSummarySumReportDashboard($age,$status,$group_by){ 

		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$this->db->select(array('count(purchase_order.id) as bargain_count','SUM(purchase_order.total_weight) as weight', 'SUM(purchase_order.quantity) as quantity')); 
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 
	     
	     

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid); 
            if($status!='')
            {
            	if($status=='lock')
            	{
	        		$this->db->where("purchase_order.is_lock ",1); 
            	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	//$this->db->where("purchase_order.is_lock ",0);
	            }
            }
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");
            if($status!='')
        	{  
            	if($status=='lock')
            	{
	        		$this->db->where("purchase_order.is_lock ",1); 
            	}
	        	elseif($status=='mailed')
	        	{
	            	$this->db->where("purchase_order.is_mail ",1);
	        	}
	            else
	            {
	            	$this->db->where("purchase_order.status ",$status);
	            	//$this->db->where("purchase_order.is_lock ",0);
	            }
	        }
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	if($status=='')
        	{ 
        		//$this->db->where("purchase_order.status <> ",0); 
        	}
        	else
        	{ 
        		//$this->db->where("purchase_order.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("purchase_order.is_lock ",1); 
        		}
	        	else
	        	{
	            	$this->db->where("purchase_order.status ",$status); 
	            	//$this->db->where("purchase_order.is_lock ",0);
	        	}
        	}
        	//$this->db->where("purchase_order.is_lock",1); 
        } 
        else
        {
        	if($status=='')
        	{ 
        		//$this->db->where("purchase_order.status <> ",0); 
        		//$this->db->where("purchase_order.is_lock ",0);
        	}
        	else
        	{ 
        		//$this->db->where("purchase_order.status  ",$status); 
        		if($status=='lock')
        		{
	        		$this->db->where("purchase_order.is_lock ",1); 
        		}
	        	else
	        	{
	            	$this->db->where("purchase_order.status ",$status); 
	            	//$this->db->where("purchase_order.is_lock ",0);
	        	}
        	}
        }
        if($age)
        {
			$age_value = "created_at BETWEEN DATE_SUB(NOW(), INTERVAL $age DAY) AND NOW()";
			$this->db->where($age_value);
		}

		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				 
				$this->db->group_by('purchase_order.'.$group_by_value); 
			}
				 
		} 
		$this->db->order_by('purchase_order.status','ASC'); 
		 

		  
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
        
		$this->db->select(array('count(purchase_order.id) as bargain_count','SUM(purchase_order.total_weight) as weight', 'SUM(purchase_order.quantity) as quantity')); 
		$this->db->select('purchase_order.status');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
 	
 		  
		//if($booked_by)
			//$this->db->where('booking.admin_id',$booked_by);

		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);   
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
             
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("purchase_order.is_lock",1); 
        } 
		$this->db->where("purchase_order.is_lock ",1);

		if($age)
        {
			$age_value = "created_at BETWEEN DATE_SUB(NOW(), INTERVAL $age DAY) AND NOW()";
			$this->db->where($age_value);
		}

		if($group_by)
		{
			//cho "<pre>"; print_r($group_by); die;
			foreach ($group_by as $key => $group_by_value) { 
				 
					$this->db->group_by('purchase_order.'.$group_by_value);   
				 
			}
		}
		$this->db->order_by('purchase_order.status','ASC');
		 

		  
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
        
		$this->db->select(array('admin.username','admin.name as admin_name','purchase_order.is_for','purchase_order.is_mail','purchase_order.shipment_date','purchase_order.production_unit','purchase_order.is_lock','purchase_order.remaining_weight','purchase_order.is_close','purchase_order.total_weight as weight', 'purchase_order.quantity','purchase_order.purchase_id','purchase_order.id','purchase_order.party_id','purchase_order.brand_id','purchase_order.category_id','purchase_order.rate','purchase_order.created_at','purchase_order.admin_id','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','brands.name as brand_name','category.category_name as category_name','brokers.name as broker_name','admin.name as admin_name'));
		$this->db->select('purchase_order.status');
		$this->db->select('purchase_order.remark');
	    $this->db->from('purchase_order'); 
	    $this->db->join('vendors', 'vendors.id = purchase_order.party_id','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('brands', 'brands.id = purchase_order.brand_id','left');  
	    $this->db->join('category', 'category.id = purchase_order.category_id','left');   
	    $this->db->join('brokers', 'brokers.id = purchase_order.broker_id','left');
	    $this->db->join('admin', 'admin.id = purchase_order.admin_id','left');
	    
 	 
		if($role==1) //maker
        { 
            $this->db->where('admin_id' , $userid);   
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
             
            //$this->db->where("purchase_order.is_lock",1); 
        }
        elseif ($role==3) 
        {
        	 
        	//$this->db->where("purchase_order.is_lock",1); 
        } 
		$this->db->where("purchase_order.is_lock ",0);
		$this->db->where("purchase_order.status ",2);

		$age_value = "created_at < DATE_SUB(NOW(), INTERVAL 5 DAY)";
		$this->db->where($age_value); 
		$this->db->order_by('purchase_order.status','ASC');
		 

		  
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