<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	function getemplyeetargetreport()
    {
    	$current_month  = sprintf('%02d', date('m'));
    	$current_year  = date('Y');
    	$CI =& get_instance(); 
    	$admin_info = $CI->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id =  $admin_info['id']; 

        //echo " SELECT admin_id, SUM(booking_booking.total_weight) as total_weight,SUM(targets.weight) as total_target_weight FROM (`booking_booking`) left join targets on targets.user_id = booking_booking.admin_id where booking_booking.status <> 3 and booking_booking.admin_id = 59; "

        $CI =& get_instance(); 
		$CI->db->select('sum(weight ) as total_target_weight ');
		$CI->db->select('sum(distributor_visits) as total_target_visits ');
		$CI->db->from('targets '); 
		$CI->db->where('user_id',$admin_id); 
		$CI->db->where('year',$current_year); 
		$CI->db->where('month',$current_month); 
		$query = $CI->db->get();
		//echo $CI->db->last_query(); die;
		$row = $query->row_array();
		$total_target_weight = 0;
		$total_target_visits = 0;
		$total_bargain_weight = 0;
		if($row)
		{
			$total_target_weight = $row['total_target_weight'];
			$total_target_visits = $row['total_target_visits'];
		}

		$CI =& get_instance(); 
		$CI->db->select('sum(total_weight ) as total_bargain_weight'); 
		$CI->db->from('booking_booking'); 
		$CI->db->where('admin_id',$admin_id); 
		$CI->db->where("YEAR( booking_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( booking_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)");
		$CI->db->where('status <>',3); 
		$query = $CI->db->get(); 
		$row = $query->row_array();
		if($row)
		{
			$total_bargain_weight = $row['total_bargain_weight']; 
		}


		$CI =& get_instance(); 
		$CI->db->select('count(id ) as total_visited'); 
		$CI->db->from('employee_locations'); 
		$CI->db->where('user_id',$admin_id); 
		$CI->db->like('address ','###'); 
		$CI->db->where("YEAR( employee_locations.tracking_date) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( employee_locations.tracking_date) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)"); 
		$query = $CI->db->get(); 
		$row = $query->row_array();
		if($row)
		{
			$total_visited = $row['total_visited']; 
		}

		$res =   "";
		if($total_target_weight>0)
		{
			$target_percentage  = round( (($total_bargain_weight*100)/$total_target_weight),2);
			$res .=  "Target (MT) :  ".round($total_bargain_weight,2)." / ".$total_target_weight." (".$target_percentage ."%)";
		} 

		if($total_target_visits>0)
		{
			$target_percentage  = round( (($total_visited*100)/$total_target_visits),2);
			$res .= " || Visits  :  ".round($total_visited,2)." / ".$total_target_visits." (".$target_percentage ."%)";
		}

		if($res=='')
		{
			$res = "No target set yet";
		}
		echo $res; 
    }


    function getemplyeetargetreportadmin()
    {
    	$current_month  = sprintf('%02d', date('m'));
    	$current_year  = date('Y');
    	$CI =& get_instance(); 
    	$admin_info = $CI->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $admin_id =  $admin_info['id']; 

        //echo " SELECT admin_id, SUM(booking_booking.total_weight) as total_weight,SUM(targets.weight) as total_target_weight FROM (`booking_booking`) left join targets on targets.user_id = booking_booking.admin_id where booking_booking.status <> 3 and booking_booking.admin_id = 59; "

        $CI =& get_instance(); 
		$CI->db->select('sum(weight ) as total_target_weight ');
		$CI->db->select('sum(distributor_visits) as total_target_visits ');
		$CI->db->from('targets ');  
		$CI->db->where('year',$current_year); 
		$CI->db->where('month',$current_month); 
		$query = $CI->db->get();
		//echo $CI->db->last_query(); die;
		$row = $query->row_array();
		$total_target_weight = 0;
		$total_target_visits = 0;
		$total_bargain_weight = 0;
		if($row)
		{
			$total_target_weight = $row['total_target_weight'];
			$total_target_visits = $row['total_target_visits'];
		}

		$CI =& get_instance(); 
		$CI->db->select('sum(total_weight ) as total_bargain_weight'); 
		$CI->db->from('booking_booking');  
		$CI->db->where("YEAR( booking_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( booking_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)");
		$CI->db->where('status <>',3); 
		$query = $CI->db->get(); 
		$row = $query->row_array();
		if($row)
		{
			$total_bargain_weight = $row['total_bargain_weight']; 
		}


		$CI =& get_instance(); 
		$CI->db->select('count(id ) as total_visited'); 
		$CI->db->from('employee_locations');  
		$CI->db->like('address ','###'); 
		$CI->db->where("YEAR( employee_locations.tracking_date) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( employee_locations.tracking_date) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)"); 
		$query = $CI->db->get(); 
		$row = $query->row_array();
		if($row)
		{
			$total_visited = $row['total_visited']; 
		}

		$res =   "";
		if($total_target_weight>0)
		{
			$target_percentage  = round( (($total_bargain_weight*100)/$total_target_weight),2);
			$res .=  "Target (MT) :  ".round($total_bargain_weight,2)." / ".$total_target_weight." (".$target_percentage ."%)";
		} 

		if($total_target_visits>0)
		{
			$target_percentage  = round( (($total_visited*100)/$total_target_visits),2);
			$res .= " || Visits  :  ".round($total_visited,2)." / ".$total_target_visits." (".$target_percentage ."%)";
		}

		if($res=='')
		{
			$res = "No target set yet";
		}
		echo $res; 
    }
 	
 	function makerstotalerformance($state_id,$conditions)
    {
    	$condition = "YEAR( booking_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( booking_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		if(isset($conditions) && !empty($conditions))
		{ 
			$month = $conditions['year'].'-'.$conditions['month'];
			$condition = "( booking_booking.created_at like '%$month%' )";
			$employee = $conditions['employee'];
			if($employee)
			{
				$condition .= "and ( booking_booking.admin_id = $employee )";
			}
		}
    	$CI =& get_instance(); 
		$CI->db->select('sum(total_weight ) as total_weight ');
		$CI->db->select('sum(`rate`*`quantity`) as total_amount');
		$CI->db->from('booking_booking ');
		$CI->db->join("vendors","vendors.id=booking_booking.party_id","left");
		$CI->db->where($condition); 
		$CI->db->where("vendors.state_id",$state_id);
		$CI->db->where("booking_booking.status <>",3); 
		$query = $CI->db->get(); 
		$row = $query->row_array();
		$res = "";
		if($row['total_amount'])
		{
			$res .= "<span class='amount_x' style='padding-right: 20px;'>Amount : ".number_format($row['total_amount'],2)."</span>";
		}
		if($row['total_weight'])
		{
			$res .= "<span class='weight_x'>Weight : ".$row['total_weight']."</span>";
		}
		//echo $CI->db->last_query(); die;
		return $res;
		//return $row['quantity'].' '.$row['total_weight'];
	} 

	function secondarymakerstotalerformance($state_id,$conditions)
    {
		

    	$condition = "YEAR( secondary_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( secondary_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		if(isset($conditions) && !empty($conditions))
		{
			$month = $conditions['year'].'-'.$conditions['month'];
			$condition = "( secondary_booking.created_at like '%$month%'  )";

			$employee = $conditions['employee']; 
			if($employee)
			{
				if(isset($conditions['employee_sec']) && !empty($conditions['employee_sec']))
				{

					$employee_sec = $conditions['employee_sec'];
					$condition .= "and ( secondary_booking.admin_id IN ($employee_sec) )";
				}
				else
				{
					$condition .= "and ( secondary_booking.admin_id IN ($employee) )";
				}


				//$condition .= "and ( secondary_booking.admin_id = $employee)";
			}
		} 

    	$CI =& get_instance();   
		$CI->db->select('sum(secondary_booking_skus.weight ) as total_weight ');
		$CI->db->select('sum(`secondary_booking_skus`.`rate`*`secondary_booking_skus`.`quantity`*`products`.`packing_items_qty`) as total_amount');
		$CI->db->from('secondary_booking');
		//$CI->db->from('secondary_booking_skus');
		$CI->db->join("secondary_booking_skus","secondary_booking_skus.secondary_booking_id=secondary_booking.id","left");
		$CI->db->join("vendors","vendors.id=secondary_booking.supply_from","left");
		$CI->db->join("products","products.id=secondary_booking_skus.product_id","left");
		$CI->db->where($condition); 
		$CI->db->where("vendors.state_id",$state_id); 
		//$CI->db->where("secondary_booking.status",1); 
		$query = $CI->db->get();  
		$row = $query->row_array();
		$res = "";
		if($row['total_amount'])
		{
			$res .= "<span class='amount_x' style='padding-right: 20px;'>Amount : ".number_format($row['total_amount'],2)."</span>";
		}
		if($row['total_weight'])
		{
			$res .= "<span class='weight_x'>Weight : ".$row['total_weight']."</span>";
		}

		//echo $CI->db->last_query();
		return $res;
		//return $row['quantity'].' '.$row['total_weight'];
	} 

	function addlog($action)
    	{
    	 
    		$CI =& get_instance();  
    		$admin_info = $CI->session->userdata('admin');  
		$admin_role = $admin_info['role'];
		$admin_id = $admin_info['id']; 
		if (getenv('HTTP_CLIENT_IP'))
		  $ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
		  $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
		  $ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
		  $ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		 $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
		  $ipaddress = getenv('REMOTE_ADDR');
		else
		  $ipaddress = 'UNKNOWN';
		$req_ip =  $ipaddress;

		$log_data = array(
		  'user_id' => $admin_id,
		  'action' => $action,
		  'ip_address' => $req_ip
		);
		$CI->db->insert('log_history',$log_data);
	}

    function chekeremail($id)
    	{
    	 
    		$CI =& get_instance(); 
		$CI->db->select('username');
		$CI->db->from('admin');
		$CI->db->where('id', $id); 
		$query = $CI->db->get();
		$row = $query->row_array();
		return $row['username'];
	} 

     function getProductPacking($product_id)
    {
    	$tag1 = '';
    	$CI =& get_instance(); 
		$CI->db->select('*');
		$CI->db->from('product_packing');
		$CI->db->where('is_enable', 0);
		$CI->db->where_in('product_id', $product_id);
		$query = $CI->db->get();
		return $row = $query->result_array();
		 
    } 

    function getShipingCharge($id,$zone){
    	$CI =& get_instance(); 
    	$CI->db->select('*');
		$CI->db->from('shipping_rate');
		$CI->db->where($zone, $id);
		$query = $CI->db->get();

		if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    } 
    }

    function getCityname($city){
    	$CI =& get_instance(); 
    	$CI->db->select('*');
		$CI->db->from('city');
		$CI->db->where('id', $city);
		$query = $CI->db->get();

		if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row['name'];  
	    } 
    }

    function getStatename($state){
    	$CI =& get_instance(); 
    	$CI->db->select('*');
		$CI->db->from('states');
		$CI->db->where('id', $state);
		$query = $CI->db->get();

		if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row['name'];  
	    } 
    }

    function GetState(){
    	$CI =& get_instance(); 
    	$CI->db->select('*');
		$CI->db->from('states');
		$query = $CI->db->get();

		if($query->num_rows() > 0 )
	    {
	      return $row = $query->result_array();   
	    } 
    }

    function getproductsbycategory($category_id){
    	$CI =& get_instance(); 
    	$CI->db->select('*');
		$CI->db->from('products');
		$CI->db->where('category_id',$category_id);
		$query = $CI->db->get();

		if($query->num_rows() > 0 )
	    {
	      return $row = $query->result_array();   
	    } 
    }


    function getproductsbybrandid($brand_ids,$category_ids){
    	$CI =& get_instance(); 
    	$CI->db->select('*');
		$CI->db->from('products');
		$CI->db->where("brand_id IN ($brand_ids)");
		$CI->db->where("category_id IN ($category_ids)");
		$query = $CI->db->get();
		//echo $CI->db->last_query();
		if($query->num_rows() > 0 )
	    {
	      return $row = $query->result_array();   
	    } 
    }


    function getbrad_info($brand_id){
    	$CI =& get_instance(); 
    	$CI->db->select('*');
		$CI->db->from('brands');
		$CI->db->where_in('id',$brand_id);
		$query = $CI->db->get();

		if($query->num_rows() > 0 )
	    {
	      return $row = $query->row_array();   
	    } 
    }


    function getweightbyproductid($product_id,$brand_id,$booking_date_from='',$booking_date_to='',$party_id='',$category_id='',$status=''){

    	

    	$condition = "";
		$cur_date = date('Y-m-d');
		if($booking_date_from=='' && $booking_date_to=='')
			$condition = "`booking.created_at` BETWEEN '".$cur_date." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to=='')
			$condition = "`booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$cur_date." 23:54:54.969999'"; 
		elseif($booking_date_from!='' && $booking_date_to!='')
			$condition = "`booking.created_at` BETWEEN '".$booking_date_from." 00:00:00.000000' AND '".$booking_date_to." 23:54:54.969999'"; 
		
		
    	$CI =& get_instance(); 

    	$role = $CI->session->userdata('admin')['role'];
    	$booked_by = $CI->session->userdata('admin')['id'];
    	
    	$CI->db->select_sum('booking.total_weight');
    	$CI->db->select_sum('booking.total_loose_rate');
    	$CI->db->select_sum('booking.total_price');
    	$CI->db->select('products.product_type');
		$CI->db->from('booking');
		$CI->db->join('products', 'products.id = booking.product_id','left'); 
		$CI->db->join('admin', 'admin.id = booking.admin_id','left');
		$CI->db->where('booking.product_id',$product_id); 
		$CI->db->where('booking.brand_id',$brand_id); 
		if($party_id!='')
	    	$CI->db->where('booking.party_id',$party_id);
	   
	    if($category_id!='')
	    	$CI->db->where('booking.category_id',$category_id);
	    
	    if($role==1) //maker
	    {
	    	$CI->db->where('booking.admin_id',$booked_by);
	    	if($status!='')
            	$CI->db->where("booking.status ",$status); 
	    }
	    elseif ($role==2) { //checker
	    	$CI->db->where("(admin.team_lead_id = $booked_by OR admin_id = $booked_by)"); 
	    	if($status!='')
            	$CI->db->where("booking.status ",$status); 
	    }
	    elseif ($role==3) 
        {
        	//$CI->db->where("booking.status <> ",0); 
        	if($status=='')
        	{ 
        		$CI->db->where("booking.status <> ",0); 
        	}
        	else
        	{ 
        		$CI->db->where("booking.status  ",$status); 
        	}
        }
		$CI->db->where($condition); 
		$query = $CI->db->get();
		//echo $CI->db->last_query(); 
		if($query->num_rows() > 0 )
	    {
	      return $row = $query->row_array();   
	    } 
    }



    

    