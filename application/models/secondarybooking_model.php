<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secondarybooking_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	}

	function UpdateSeconadryBookingSku($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('secondary_booking_skus', $updatedata); 
	}

	function UpdateSecondaryPiHistory($updatedata,$condition)	
	{
		$this->db->where($condition);
    	$this->db->update('pi_history_secondary_booking', $updatedata); 
    	//echo $this->db->last_query(); die;
	}
	function AddPiHistory($insertdata)
	{
		$this->db->insert('pi_history_secondary_booking',$insertdata); 
		return $this->db->insert_id();
	}
	function UpdateSecondaryBookingSkuPiStatus($updatedata,$condition)	
	{
 
		if($condition['sku_ids'])
		{
			$ids = $condition['sku_ids'];
			$this->db->where("id IN ($ids)");
    		return $this->db->update('secondary_booking_skus', $updatedata); 
    		//echo $this->db->last_query(); 
    	}
	}

	function TotalorderCount($condition){ 
		$this->db->select('count(secondary_booking.id) as total_order');
		if(isset($condition['top']))
	    	$this->db->select('sum(secondary_booking.total_weight) as total_weight');
		$this->db->select('secondary_booking.supply_to');
		$this->db->select('distributors.name as distributor_name');
	    $this->db->from('secondary_booking');   
	    $this->db->join('distributors', 'distributors.id = secondary_booking.supply_to','left');  
	    if(isset($condition['maker']))
	    {
	    	$userid = $condition['maker'];
	    	$this->db->where("(distributors.maker_id = $userid)");  
	    }
	    if(isset($condition['top']))
	    {
	    	$this->db->limit($condition['top']);
	    	$this->db->order_by('total_order','DESC');  
	    }

	    if(isset($condition['time']))
	    {
	    	$age = $condition['time'];
	    	if($age=='30')
	    	{
	    		$age_value = "MONTH(secondary_booking.created_at) = MONTH(CURRENT_DATE()) AND YEAR(secondary_booking.created_at) = YEAR(CURRENT_DATE())";
				$this->db->where($age_value);
		    	
			}
			else
			{		
				$d2 = date('Y-m-d', strtotime('-30 days'));
				$age_value = "secondary_booking.created_at >= '$d2'";
				//echo $age_value; die;
				$this->db->where($age_value);
			}
	    }
	    if(isset($condition['top']))
	    {
	    	$d2 = date('Y-m-d', strtotime('-30 days'));
			$age_value = "secondary_booking.created_at >= '$d2'";
			//echo $age_value; die;
			$this->db->where($age_value);
	    }
	    $this->db->group_by('secondary_booking.supply_to');
	    $query = $this->db->get();  
	    if(isset($condition['top']))
	    {
	    	//echo $this->db->last_query(); die;
	    }
	     
	    if(isset($condition['top']))
	    {
	    	 

	    	return $query->result_array();  
	    }
	    else
	    { 
	    	return $query->num_rows(); 
	    } 
	}

	function updatebargiansmail($id)	
	{
		$this->db->where('id',$id);
		$this->db->set('is_mail',1);
    	return $this->db->update('secondary_booking');
    	//echo $this->db->last_query(); die;
	}

	function GetVendorSkus($condition)
	{
		$this->db->select('booking_booking.party_id');
		$this->db->select('products.*');
		$this->db->select('booking_skus.product_id');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');
        $this->db->from('booking_skus');
        $this->db->join('booking_booking', 'booking_booking.booking_id = booking_skus.bargain_id','left'); 
        $this->db->join('products', 'products.id = booking_skus.product_id','left'); 
        $this->db->join('category', 'category.id = booking_skus.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_skus.brand_id','left'); 
        $this->db->where($condition); 
        $this->db->group_by('booking_skus.product_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get();    
        return $results =  $query->result_array();
	}

	function getlast_booking_id($book_chek_date){   
		$select_query =  "SELECT CASE WHEN MONTH(created_at)>=4 THEN concat(YEAR(created_at), '-',YEAR(created_at)+1) ELSE concat(YEAR(created_at)-1,'-', YEAR(created_at)) END AS financial_year,MAX(secondary_booking_id) as booking_id FROM secondary_booking GROUP BY financial_year having financial_year = (CASE WHEN MONTH('$book_chek_date')>=4 THEN concat(YEAR('$book_chek_date'), '-',YEAR('$book_chek_date')+1) ELSE concat(YEAR('$book_chek_date')-1,'-', YEAR('$book_chek_date')) END)"; 

		$select_query =  "SELECT MAX(secondary_booking_id) as booking_id FROM secondary_booking"; 

		$query = $this->db->query($select_query);
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row['booking_id'];  
	    }
	}
	function AddBooking($insertdata)
	{
		$this->db->insert('secondary_booking',$insertdata);
		return $booking_number =  $this->db->insert_id(); 
	}

	function AddSKU($skudata)
	{ 
        return $this->db->insert('secondary_booking_skus',$skudata);
	}

 
 
	function GetBookingList($condition,$perPage=20, $pageNo=1){ 
		//echo date('Y-m-d');  die;
		//echo "<pre>"; print_r($condition); die;
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		$date_range = "`secondary_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('secondary_booking.pi_id','secondary_booking.total_weight as weight','secondary_booking.secondary_booking_id as booking_id','secondary_booking.id','secondary_booking.supply_from','secondary_booking.rate','secondary_booking.created_at','secondary_booking.admin_id','vendors.name as supply_from','distributors.name as party_name','distributors.city_id as city_id','city.name as city_name','admin.name as admin_name'));
		$this->db->select('secondary_booking.status');
		$this->db->select('secondary_booking.remark');
		$this->db->select('pi_history_secondary_booking.invoice_file');
	    $this->db->from('secondary_booking'); 
	    $this->db->join('vendors', 'vendors.id = secondary_booking.supply_from','left');  
	    $this->db->join('distributors', 'distributors.id = secondary_booking.supply_to','left');  
	    $this->db->join('city', 'city.id = distributors.city_id','left'); 
	    $this->db->join('admin', 'admin.id = secondary_booking.admin_id','left');
	    $this->db->join('pi_history_secondary_booking', 'pi_history_secondary_booking.id = secondary_booking.pi_id','left');
	    $this->db->where($date_range); 
	    if($role==1 || $role==6) //maker
        { 
            $this->db->where('admin_id' , $userid); 
        }
        elseif ($role==2) {  //checker
            $this->db->where("(admin.team_lead_id = $userid OR admin_id = $userid)");  
        }
        elseif ($role==3) 
        {
        	$this->db->where("secondary_booking.status <> ",0); 
        }  
	    $this->db->limit($perPage, $startFromRecord); 
        $this->db->order_by('secondary_booking.id','DESC');  

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
		$date_range = "`secondary_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('secondary_booking.total_weight as weight','secondary_booking.secondary_booking_id as booking_id','secondary_booking.id','secondary_booking.supply_from','secondary_booking.rate','secondary_booking.created_at','secondary_booking.admin_id','distributors.name as party_name','distributors.city_id as city_id','city.name as city_name','admin.name as admin_name'));
		$this->db->select('secondary_booking.status');
		$this->db->select('secondary_booking.remark');
	    $this->db->from('secondary_booking'); 
	    $this->db->join('distributors', 'distributors.id = secondary_booking.supply_to','left');  
	    $this->db->join('city', 'city.id = distributors.city_id','left'); 
	    $this->db->join('admin', 'admin.id = secondary_booking.admin_id','left');
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
        	$this->db->where("secondary_booking.status <> ",0); 
        } 
        $this->db->order_by('secondary_booking.id','DESC');   
	    $query = $this->db->get();  
	   	return $query->num_rows();
	}


	function GetBookingInfoById($booking_id) { 
		//echo date('Y-m-d');  die;
		$cur_date = date('Y-m-d');
		$condition = "`secondary_booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		$this->db->select(array('vendors.invoice_prefix','vendors.other_info','vendors.bank_details as vendor_bank_details','pi_history_secondary_booking.invoice_file','secondary_booking.pi_id','secondary_booking.supply_from as party_id','secondary_booking.status','secondary_booking.is_mail','secondary_booking.payment_term','secondary_booking.remark','secondary_booking.delivery_date','secondary_booking.total_weight','secondary_booking.secondary_booking_id','secondary_booking.id','secondary_booking.supply_from','distributors.name as distributor_name','secondary_booking.rate','secondary_booking.created_at','vendors.name as party_name','vendors.city_id as city_id','city.name as city_name','c1.name as distributor_city_name','admin.name as sales_executive_name','admin.username as email','states.name as state_name','vendors.address as vendor_address','vendors.gst_no as vendor_gst_no','vendors.zipcode as vendor_zipcode','vendors.mobile as vendor_mobile','s1.name as distributor_state_name','distributors.address as distributors_address','distributors.gst_no as distributors_gst_no','distributors.zipcode as distributors_zipcode','distributors.mobile as distributors_mobile','distributors.email as distributors_email','vendors.email as vendors_email','admin.mobile as sales_executive_mobile','a2.mobile as maker_mobile','a2.username as maker_email','distributors.state_id as distributors_state_id','vendors.cnf'));
		$this->db->select('secondary_booking.status');
	    $this->db->from('secondary_booking'); 
	    $this->db->join('vendors', 'vendors.id = secondary_booking.supply_from','left');  
	    $this->db->join('distributors', 'distributors.id = secondary_booking.supply_to','left');  
	    $this->db->join('city', 'city.id = vendors.city_id','left');  
	    $this->db->join('states', 'states.id = vendors.state_id','left');  
	    $this->db->join('city c1', 'c1.id = distributors.city_id','left');   
	    $this->db->join('states s1', 's1.id = distributors.state_id','left');   
	    $this->db->join('admin', 'admin.id = secondary_booking.admin_id','left');
	    $this->db->join('admin a2', 'a2.id = admin.team_lead_id','left');

	    $this->db->join('pi_history_secondary_booking', 'pi_history_secondary_booking.id = secondary_booking.pi_id','left');

	    $this->db->where('secondary_booking.id',$booking_id); 
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
	function GetSkuinfo($condition)
	{
		$this->db->select('secondary_booking_skus.rate');
		$this->db->select('secondary_booking_skus.weight');
		$this->db->select('secondary_booking_skus.quantity');
		$this->db->select('secondary_booking_skus.id');

		$this->db->select('secondary_booking_skus.secondary_bargain_id');
		$this->db->select('secondary_booking_skus.secondary_booking_id');

		$this->db->select('products.name');
		$this->db->select('brands.name as brand_name');
		$this->db->select('category.category_name');
		$this->db->select('category.hsn');
		$this->db->select('products.packing_items_qty');

		$this->db->select('secondary_booking_skus.brand_id');
		$this->db->select('secondary_booking_skus.category_id');
		$this->db->select('secondary_booking_skus.product_id');

		$this->db->select('admin.id as booked_by');

        $this->db->from('secondary_booking_skus');
        $this->db->join('products', 'products.id = secondary_booking_skus.product_id','left');
        $this->db->join('brands', 'brands.id = secondary_booking_skus.brand_id','left');
        $this->db->join('category', 'category.id = secondary_booking_skus.category_id','left'); 

        $this->db->join('secondary_booking', 'secondary_booking.id = secondary_booking_skus.secondary_booking_id','left');
        $this->db->join('admin', 'admin.id = secondary_booking.admin_id','left');

        $this->db->where($condition); 
        $query = $this->db->get();  
        return $query->result_array();
    }

    function UpdateBooking($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('secondary_booking', $updatedata);
    	//echo $this->db->last_query(); die;
	}


	function CountSecondaryBooking($condition){  
		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];

		$cur_date = date('Y-m-d');
		if($condition['booking_date_from']!='' && $condition['booking_date_to']=='')
			$condition_date = "`secondary_booking.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($condition['booking_date_from']!='' && $condition['booking_date_to']!='')
			$condition_date = "`secondary_booking.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$condition['booking_date_to']." 23:54:54.969999'";

		$this->db->select(array('secondary_booking.total_weight as weight','secondary_booking.secondary_booking_id as booking_id','secondary_booking.id','secondary_booking.supply_from','secondary_booking.rate','secondary_booking.created_at','secondary_booking.admin_id','distributors.name as party_name','distributors.city_id as city_id','city.name as city_name','admin.name as admin_name'));
		$this->db->select('secondary_booking.status');
		$this->db->select('secondary_booking.remark');
	    $this->db->from('secondary_booking'); 
	    $this->db->join('distributors', 'distributors.id = secondary_booking.supply_to','left');  
	    $this->db->join('city', 'city.id = distributors.city_id','left'); 
	    $this->db->join('admin', 'admin.id = secondary_booking.admin_id','left');
	    if($condition_date!='')
	    	$this->db->where($condition_date);
	    
	    if(isset($condition['supply_from']) && !empty($condition['supply_from']))
	    	$this->db->where('secondary_booking.supply_from',trim($condition['supply_from']));
	    if(isset($condition['supply_to']) && !empty($condition['supply_to']))
	    	$this->db->where('secondary_booking.supply_to',trim($condition['supply_to']));
	    if(isset($condition['employee']) && !empty($condition['employee']))
	    	$this->db->where('secondary_booking.admin_id',trim($condition['employee']));
	    

		if($role==1 || $role==6) //maker
        { 
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

        $this->db->order_by('secondary_booking.id','DESC');   
	    $query = $this->db->get();   
	   	return $query->num_rows();
	}
	function GetReportBooking($perPage=20, $pageNo=1,$condition){ 
		//echo "<pre>"; print_r($condition);
		$startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;

		//echo date('Y-m-d');  die;
		
		//@date_default_timezone_set("Asia/Kolkata");
		//$condition = ""; 
		$cur_date = date('Y-m-d');
		if($condition['booking_date_from']!='' && $condition['booking_date_to']=='')
			$condition_date = "`secondary_booking.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($condition['booking_date_from']!='' && $condition['booking_date_to']!='')
			$condition_date = "`secondary_booking.created_at` BETWEEN '".$condition['booking_date_from']." 00:00:00.000000' AND '".$condition['booking_date_to']." 23:54:54.969999'";


		$role = $this->session->userdata('admin')['role'];
        $userid = $this->session->userdata('admin')['id'];
        
		$this->db->select(array('secondary_booking.pi_id','secondary_booking.total_weight as weight','secondary_booking.secondary_booking_id as booking_id','secondary_booking.id','secondary_booking.supply_from','secondary_booking.rate','secondary_booking.created_at','secondary_booking.admin_id','vendors.name as supply_from','distributors.name as party_name','distributors.city_id as city_id','city.name as city_name','admin.name as admin_name'));
		$this->db->select('secondary_booking.status');
		$this->db->select('secondary_booking.remark');
		$this->db->select('pi_history_secondary_booking.invoice_file');
	    $this->db->from('secondary_booking'); 
	    $this->db->join('vendors', 'vendors.id = secondary_booking.supply_from','left');  
	    $this->db->join('distributors', 'distributors.id = secondary_booking.supply_to','left');  
	    $this->db->join('city', 'city.id = distributors.city_id','left'); 
	    $this->db->join('admin', 'admin.id = secondary_booking.admin_id','left');
	    $this->db->join('pi_history_secondary_booking', 'pi_history_secondary_booking.id = secondary_booking.pi_id','left');
 		if($condition_date!='')
	    	$this->db->where($condition_date);
	    
	    if(isset($condition['supply_from']) && !empty($condition['supply_from']))
	    	$this->db->where('secondary_booking.supply_from',trim($condition['supply_from']));
	    if(isset($condition['supply_to']) && !empty($condition['supply_to']))
	    	$this->db->where('secondary_booking.supply_to',trim($condition['supply_to']));
	    if(isset($condition['employee']) && !empty($condition['employee']))
	    	$this->db->where('secondary_booking.admin_id',trim($condition['employee']));
	    

		if($role==1 || $role==6) //maker
        { 
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
           
        $this->db->limit($perPage, $startFromRecord);
		$this->db->order_by('secondary_booking.id','DESC');  
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


	function GetVendorProductscnfrate($condition)
	{
		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf'); 
		$this->db->select('booking_booking.party_id'); 
		$this->db->select('booking_booking.brand_id'); 
		$this->db->select('booking_booking.category_id'); 
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');

		$this->db->select('rate_master.rate as rate');

        $this->db->from('booking_booking');  
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('category', 'category.id = booking_booking.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_booking.brand_id','left'); 

        $this->db->join('rate_master', 'rate_master.category_id = category.id and rate_master.brand_id = brands.id','left'); 
        
        $this->db->where($condition); 
        $this->db->group_by('booking_booking.category_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();
	}


	function GetVendorProductsprecnfrate($condition)
	{
		$inner_condition = ""; 
		if(isset($condition['party_id']))
		{
			$inner_condition = " where vendor_id =  ".$condition['party_id'];
		}
		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf'); 
		$this->db->select('booking_booking.party_id'); 
		$this->db->select('booking_booking.brand_id'); 
		$this->db->select('booking_booking.category_id'); 
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');

		$this->db->select('p1.rate as rate');
		$this->db->select('p1.comission_amount');
		$this->db->select('p1.explaination_formula');
		$this->db->select('p1.gst_pecentage');
        $this->db->from('booking_booking');  
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('category', 'category.id = booking_booking.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_booking.brand_id','left'); 

        $this->db->join("(select max(id) as p_id,party_rate_master.brand_id, party_rate_master.category_id, party_rate_master.vendor_id , party_rate_master.rate, party_rate_master.comission_amount, party_rate_master.gst_pecentage, party_rate_master.explaination_formula from party_rate_master  ".$inner_condition." GROUP by vendor_id,category_id) p",  'p.vendor_id = booking_booking.party_id and p.category_id = category.id and p.brand_id = brands.id','left');

         $this->db->join('party_rate_master as p1', 'p.p_id = p1.id','left'); 

        $this->db->where($condition); 
        $this->db->group_by('booking_booking.category_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();
	}
	function GetVendorProducts($condition)
	{
		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf'); 
		$this->db->select('booking_booking.party_id'); 
		$this->db->select('booking_booking.brand_id'); 
		$this->db->select('booking_booking.category_id'); 
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');
        $this->db->from('booking_booking');  
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('category', 'category.id = booking_booking.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_booking.brand_id','left'); 
        
        $this->db->where($condition); 
        $this->db->group_by('booking_booking.category_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get();    
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();
	}

	function GetVendorSkusWithcnfrate($condition)
	{
		$this->db->select('city.name as party_city_name');
		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf');
		$this->db->select('vendors.mobile as party_mobile');
		$this->db->select('cnf_rate_master.gst_percentage');
		//$this->db->select('cnf_rate_master.explaination_formula');
		$this->db->select('cnf_rate_master.rate');
		$this->db->select('cnf_rate_master.created_at as cnf_rate_date');
		$this->db->select('booking_booking.party_id');
		$this->db->select('products.*');
		$this->db->select('booking_skus.product_id');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');
        $this->db->from('booking_skus');
        $this->db->join('booking_booking', 'booking_booking.booking_id = booking_skus.bargain_id','left'); 
        $this->db->join('products', 'products.id = booking_skus.product_id','left'); 
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('city', 'city.id = vendors.city_id','left'); 
        $this->db->join('category', 'category.id = booking_skus.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_skus.brand_id','left'); 
        $this->db->join('cnf_rate_master', 'cnf_rate_master.product_id = products.id and cnf_rate_master.vendor_id =booking_booking.party_id and cnf_rate_master.id=(select id from cnf_rate_master as cnf where cnf.vendor_id =booking_booking.party_id and cnf.product_id = products.id order by  cnf.id desc limit 1  )','left'); 
        $this->db->where($condition); 
        $this->db->group_by('booking_skus.product_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get();
		//echo $this->db->last_query(); die;  
        return $results =  $query->result_array();
	}

	function Copycnfrates($condition)
	{
		$this->db->select('e1.rate as base_empty_tin_rates');

		$this->db->select('empty_tin_rates.base_rate');
		$this->db->select('empty_tin_rates.rate as empty_tin_rate');
		$this->db->select('empty_tin_rates.insurance as insurance_percentage');


		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf');
		$this->db->select('rate_master.rate');
		$this->db->select('rate_master.created_at as cnf_rate_date');
		$this->db->select('booking_booking.party_id');
		$this->db->select('products.*');
		$this->db->select('booking_skus.product_id');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');

		$this->db->select('vendors.state_id as state_id');
		$this->db->select('vendors.name as vendor_name');

		$this->db->select('products.packaging_type');
		$this->db->select('products.packing_items');

        $this->db->from('booking_skus');
        $this->db->join('booking_booking', 'booking_booking.booking_id = booking_skus.bargain_id','left'); 
        $this->db->join('products', 'products.id = booking_skus.product_id','left'); 
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('category', 'category.id = booking_skus.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_skus.brand_id','left'); 
        $this->db->join('rate_master', 'rate_master.brand_id = booking_skus.brand_id  and  rate_master.category_id = booking_skus.category_id  and rate_master.id=(select id from rate_master as r1 where r1.brand_id =booking_skus.brand_id and r1.category_id = booking_skus.category_id order by  r1.id desc limit 1  )','left');  

        $this->db->join('empty_tin_rates', 'empty_tin_rates.brand_id = booking_skus.brand_id and empty_tin_rates.category_id = booking_skus.category_id and empty_tin_rates.product_id = booking_skus.product_id and empty_tin_rates.state_id = vendors.state_id and empty_tin_rates.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.category_id = booking_skus.category_id and e2.product_id = booking_skus.product_id and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at order by e2.created_at desc limit 1  ) ','left');


        $this->db->join('empty_tin_rates e1', 'e1.brand_id = booking_booking.brand_id and e1.category_id = booking_booking.category_id and e1.state_id = vendors.state_id and e1.base_rate = 1 and e1.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.base_rate = 1 and e2.category_id = booking_skus.category_id   and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at order by e2.created_at desc limit 1  ) ','left');
        $this->db->where($condition); 
        $this->db->group_by('booking_skus.product_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();
	}


	function Copycnfratesmasterforrate($condition)
	{
		$this->db->select('e1.rate as base_empty_tin_rates');

		$this->db->select('empty_tin_rates.base_rate');
		$this->db->select('empty_tin_rates.rate as empty_tin_rate');
		$this->db->select('empty_tin_rates.insurance as insurance_percentage');


		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf');
		$this->db->select('party_rate_master.rate');
		$this->db->select('party_rate_master.created_at as cnf_rate_date');
		$this->db->select('booking_booking.party_id');
		$this->db->select('products.*');
		$this->db->select('booking_skus.product_id');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');

		$this->db->select('vendors.state_id as state_id');
		$this->db->select('vendors.name as vendor_name');

		$this->db->select('products.packaging_type');
		$this->db->select('products.packing_items');

        $this->db->from('booking_skus');
        $this->db->join('booking_booking', 'booking_booking.booking_id = booking_skus.bargain_id','left'); 
        $this->db->join('products', 'products.id = booking_skus.product_id','left'); 
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('category', 'category.id = booking_skus.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_skus.brand_id','left'); 
        $this->db->join('party_rate_master', 'party_rate_master.brand_id = booking_skus.brand_id  and  party_rate_master.category_id = booking_skus.category_id  and party_rate_master.id=(select id from party_rate_master as r1 where r1.brand_id =booking_skus.brand_id and r1.category_id = booking_skus.category_id and booking_booking.party_id = party_rate_master.vendor_id order by  r1.id desc limit 1  )','left');  

        $this->db->join('empty_tin_rates', 'empty_tin_rates.brand_id = booking_skus.brand_id and empty_tin_rates.category_id = booking_skus.category_id and empty_tin_rates.product_id = booking_skus.product_id and empty_tin_rates.state_id = vendors.state_id and empty_tin_rates.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.category_id = booking_skus.category_id and e2.product_id = booking_skus.product_id and e2.state_id = vendors.state_id  order by e2.created_at desc limit 1  ) ','left');


        $this->db->join('empty_tin_rates e1', 'e1.brand_id = booking_booking.brand_id and e1.category_id = booking_booking.category_id and e1.state_id = vendors.state_id and e1.base_rate = 1 and e1.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.base_rate = 1 and e2.category_id = booking_skus.category_id   and e2.state_id = vendors.state_id  order by e2.created_at desc limit 1  ) ','left');
        $this->db->where($condition); 
        $this->db->group_by('booking_skus.product_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();
	}

	function Copycnfratesmaster($condition)
	{
		$this->db->select('e1.rate as base_empty_tin_rates');

		$this->db->select('empty_tin_rates.base_rate');
		$this->db->select('empty_tin_rates.rate as empty_tin_rate');
		$this->db->select('empty_tin_rates.insurance as insurance_percentage');


		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf');
		$this->db->select('party_rate_master.rate');
		$this->db->select('party_rate_master.created_at as cnf_rate_date');
		$this->db->select('booking_booking.party_id');
		$this->db->select('products.*');
		$this->db->select('booking_skus.product_id');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');

		$this->db->select('vendors.state_id as state_id');
		$this->db->select('vendors.name as vendor_name');

		$this->db->select('products.packaging_type');
		$this->db->select('products.packing_items');

        $this->db->from('booking_skus');
        $this->db->join('booking_booking', 'booking_booking.booking_id = booking_skus.bargain_id','left'); 
        $this->db->join('products', 'products.id = booking_skus.product_id','left'); 
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('category', 'category.id = booking_skus.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_skus.brand_id','left'); 
        $this->db->join('party_rate_master', 'party_rate_master.brand_id = booking_skus.brand_id  and  party_rate_master.category_id = booking_skus.category_id  and party_rate_master.id=(select id from party_rate_master as r1 where r1.brand_id =booking_skus.brand_id and r1.category_id = booking_skus.category_id and booking_booking.party_id = party_rate_master.vendor_id order by  r1.id desc limit 1  )','left');  

        $this->db->join('empty_tin_rates', 'empty_tin_rates.brand_id = booking_skus.brand_id and empty_tin_rates.category_id = booking_skus.category_id and empty_tin_rates.product_id = booking_skus.product_id and empty_tin_rates.state_id = vendors.state_id and empty_tin_rates.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.category_id = booking_skus.category_id and e2.product_id = booking_skus.product_id and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at order by e2.created_at desc limit 1  ) ','left');


        $this->db->join('empty_tin_rates e1', 'e1.brand_id = booking_booking.brand_id and e1.category_id = booking_booking.category_id and e1.state_id = vendors.state_id and e1.base_rate = 1 and e1.id=(select e2.id from empty_tin_rates as e2 where `e2`.`brand_id` = `booking_skus`.`brand_id` and e2.base_rate = 1 and e2.category_id = booking_skus.category_id   and e2.state_id = vendors.state_id and e2.created_at < booking_booking.created_at order by e2.created_at desc limit 1  ) ','left');
        $this->db->where($condition); 
        $this->db->group_by('booking_skus.product_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        return $results =  $query->result_array();
	}

	function GetBookingInfoById1($booking_id) { 
		//echo date('Y-m-d');  die;
		 
		$this->db->select('secondary_booking.*');
	    $this->db->from('secondary_booking');  

	    $this->db->where('secondary_booking.id',$booking_id); 
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get();  
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array();
	      //echo "<pre>";  print_r($row); die;
	      return $row;  
	    }
	}
	function Bookedskus($condition) {  
		$this->db->select('secondary_booking_skus.*');
	    $this->db->from('secondary_booking_skus'); 
	    $this->db->where($condition); 
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get();  
	    $res = array(); 
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      foreach ($row as $key => $value) {
	       	$res[$value['product_id']] = $value;
	       } 
	    }
	    return $res;
	}

	function Bookedskuslist($condition) {  
		$this->db->select('secondary_booking_skus.*');
		$this->db->select('products.name');
		$this->db->select('products.packaging_type');
		$this->db->select('products.packing_items');
		$this->db->select('products.loose_rate');
		$this->db->select('products.packing_items_qty');
		$this->db->select('category.hsn');
		$this->db->select('category.category_name as category_name');
		$this->db->select('category.hsn');
	    $this->db->from('secondary_booking_skus'); 
	    $this->db->join('products','products.id= secondary_booking_skus.product_id','LEFT');
        $this->db->join('category','category.id= secondary_booking_skus.category_id and category.brand_id= secondary_booking_skus.brand_id');
        $this->db->join('brands','brands.id=secondary_booking_skus.brand_id'); 
	    $this->db->where($condition); 
	    //$this->db->where("(booking.created_at >= " . now() . ")");
	    $query = $this->db->get();  
	    //echo $this->db->last_query(); die;
	    $res = array(); 
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array();
	      foreach ($row as $key => $value) {
	       	$res[$value['product_id']] = $value;
	       } 
	    }
	    return $res;
	}

	function DeleteSecondarySKU($condition){
	  $this->db->where($condition);
	  return $this->db->delete('secondary_booking_skus');
	}

	/*   ==============  page End =============== */

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
	
	function DeleteSKU($condition){
	  $this->db->where($condition);
	  return $this->db->delete('booking_skus');
	}
	function UpdateBookingBooking($updatedata,$condition)	
	{
		$this->db->where($condition);
    	return $this->db->update('booking_booking', $updatedata);
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

	


	

	function DeleteBooking($condition){
	  $this->db->where($condition);
	  return $this->db->delete('booking');
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
	        	else
	        	{
	            	$this->db->where("booking_booking.status ",$status); 
	            	//$this->db->where("booking_booking.is_lock ",0);
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
			$age_value = "created_at BETWEEN DATE_SUB(NOW(), INTERVAL $age DAY) AND NOW()";
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

		$age_value = "created_at < DATE_SUB(NOW(), INTERVAL 5 DAY)";
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

	function AddPiSkuHistorysecondary_booking($insertdata)
	{
		return $this->db->insert_batch('pi_sku_historysecondary_booking',$insertdata);  
	}


	function pi_sku_historysecondary_bookingremove($condition){
	  $this->db->where($condition);
	  return $this->db->delete('pi_sku_historysecondary_booking');
	}


	function need_attention($condition){ 
		$this->db->select('count(secondary_booking.id) as total_order');
		$this->db->select('sum(secondary_booking.total_weight) as total_weight1');
		$this->db->select('secondary_booking.supply_to');
		$this->db->select('distributors.name as distributor_name');
	    $this->db->from('secondary_booking');   
	    $this->db->join('distributors', 'distributors.id = secondary_booking.supply_to','left');  
	    if(isset($condition['maker']))
	    {
	    	$userid = $condition['maker'];
	    	$this->db->where("(distributors.maker_id = $userid)");  
	    }
	    $this->db->order_by('total_order','DESC');    
    	$d2 = date('Y-m-d', strtotime('-30 days'));
		$age_value = "secondary_booking.created_at >= '$d2'";
		//echo $age_value; die;
		$this->db->where($age_value); 
		$this->db->having("total_weight1 <= 0.2");
	    $this->db->group_by('secondary_booking.supply_to');
	    $this->db->order_by('total_weight1','ASC');
	    $query = $this->db->get();  
	    //echo $this->db->last_query(); die;	        
	    return $query->result_array();  
	}


	function GetVendorSkusWithcnfprevoiusrate($condition,$created_date)
	{

		$this->db->select('cnf_rate_master.rate');
		$this->db->select('cnf_rate_master.created_at as cnf_rate_date');

		$this->db->select('city.name as party_city_name');
		$this->db->select('vendors.name as party_name');
		$this->db->select('vendors.cnf');
		$this->db->select('vendors.mobile as party_mobile');
		$this->db->select('cnf_rate_master.gst_percentage');
		//$this->db->select('cnf_rate_master.explaination_formula');
		
		$this->db->select('booking_booking.party_id');
		$this->db->select('products.*');
		$this->db->select('booking_skus.product_id');
		$this->db->select('category.category_name');
		$this->db->select('brands.name as brand_name');
        $this->db->from('booking_skus');
        $this->db->join('booking_booking', 'booking_booking.booking_id = booking_skus.bargain_id','left'); 
        $this->db->join('products', 'products.id = booking_skus.product_id','left'); 
        $this->db->join('vendors', 'vendors.id = booking_booking.party_id','left'); 
        $this->db->join('city', 'city.id = vendors.city_id','left'); 
        $this->db->join('category', 'category.id = booking_skus.category_id','left'); 
        $this->db->join('brands', 'brands.id = booking_skus.brand_id','left'); 
        $this->db->join('cnf_rate_master', 'cnf_rate_master.product_id = products.id and cnf_rate_master.vendor_id =booking_booking.party_id and cnf_rate_master.id=(select id from cnf_rate_master as cnf where cnf.vendor_id =booking_booking.party_id and cnf.product_id = products.id and cnf.created_at <= "'.$created_date.'"  order by  cnf.id desc limit 1  )','left'); 
        $this->db->where($condition); 
        $this->db->group_by('booking_skus.product_id'); 
        $this->db->order_by('brands.name','ASC'); 
        $this->db->order_by('category.category_name','ASC'); 
        $query = $this->db->get();
		//echo $this->db->last_query(); die;  
        return $results =  $query->result_array();
	}



	function GetInvoiceSkuinfo($condition)
	{
		$this->db->select('pi_sku_historysecondary_booking.amount');
		$this->db->select('pi_sku_historysecondary_booking.weight');
		$this->db->select('pi_sku_historysecondary_booking.quantity');
		$this->db->select('pi_sku_historysecondary_booking.id');
 
		$this->db->select('pi_sku_historysecondary_booking.booking_id');

		$this->db->select('products.name');
		$this->db->select('brands.name as brand_name');
		$this->db->select('category.category_name');
		$this->db->select('category.hsn');
		$this->db->select('products.packing_items_qty');

		$this->db->select('pi_sku_historysecondary_booking.brand_id');
		$this->db->select('pi_sku_historysecondary_booking.category_id');
		$this->db->select('pi_sku_historysecondary_booking.product_id');

		$this->db->select('admin.id as booked_by');

        $this->db->from('pi_sku_historysecondary_booking');
        $this->db->join('products', 'products.id = pi_sku_historysecondary_booking.product_id','left');
        $this->db->join('brands', 'brands.id = pi_sku_historysecondary_booking.brand_id','left');
        $this->db->join('category', 'category.id = pi_sku_historysecondary_booking.category_id','left'); 

        $this->db->join('secondary_booking', 'secondary_booking.id = pi_sku_historysecondary_booking.booking_id','left');
        $this->db->join('admin', 'admin.id = secondary_booking.admin_id','left');
        $this->db->where($condition); 
        $query = $this->db->get();  
        return $query->result_array();
    }



    function AddSecondaryTaxInvoice($insertdata)
	{
		$this->db->insert('invoice_secondary_booking',$insertdata); 
		return $this->db->insert_id();
	}

	function GetlatestInvoiceNumber($prev_financial_year){    
		$select_query =  "SELECT MAX(id) as invoice_id FROM invoice_secondary_booking where financial_year =  '$prev_financial_year'"; 
		$query = $this->db->query($select_query);
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      $invoice_id = $row['invoice_id'];  
	      return $invoice_id;
	    }
	    else
	    {
	    	return 0;
	    }
	}

	function updateSecondaryTaxInvoice($updatedata,$condition)	
	{
		$this->db->where($condition); 
    	$this->db->update('invoice_secondary_booking',$updatedata);
    	//echo $this->db->last_query(); die;
	}
}