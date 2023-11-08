<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
		function updatelosseratekg($loose_oil_rate_kg,$id)
		{
			$update_data = array('loose_oil_rate_kg' => $loose_oil_rate_kg);
			$CI =& get_instance();
			$CI->db->where('booking_id',$id);
    		$CI->db->update('booking_booking', $update_data);

		}
		function dispatchtarget($dispetched_month,$employee,$state_id){
			$CI =& get_instance();
			//$employee = $conditions['employee'];
			$condition_emp = "";
			$condition_state = "";
			if($employee)
			{
				$condition_emp = "and ( booking_booking.admin_id = $employee )"; 
			}

			if($employee)
			{
				$condition_state = "and vendors.state_id IN (".$state_id.")"; 
			}
			//$data_query_dispetch = 'select booking_booking.brand_id, brands.name as brand_name, SUM(pi_history.total_weight_pi) as total_dispateched_weight, SUM( pi_history.pi_amount) as total_dispatched_amount from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status =0 and (pi_history.dispatch_date like "%'.$dispetched_month.'%" ) '.$condition_emp;	
			$data_query_dispetch = ' select category.category_name, booking_booking.category_id,booking_booking.brand_id, brands.name as brand_name,SUM(pi_sku_history.weight)  as total_dispateched_weight, SUM(pi_sku_history.amount)  as total_dispatched_amount from pi_history
				LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number
				LEFT JOIN `booking_booking` ON `booking_booking`.`id`= `pi_sku_history`.`booking_id` 
				LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` 
				LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id` 
				LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id` 
				LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status=0 '.$condition_state.' and ( pi_history.dispatch_date like  "%'.$dispetched_month.'%" ) '.$condition_emp; 

				$query_dispetch = $CI->db->query($data_query_dispetch);     
			    return $pi_dispetch = $query_dispetch->row_array();   
	    } 
	function schemewinner($weight,$schemeid){
		$CI =& get_instance();


		//$data_query = "SELECT scheme_detail.reward_image,scheme_detail.reward_name,scheme_detail.target_dispatched_ton ,  DATEDIFF(scheme.`to_date`, scheme.`from_date`) AS scheme_days, DATEDIFF(now(), scheme.`from_date`) AS completed_days, (scheme_detail.target_dispatched_ton/ ( DATEDIFF(scheme.`to_date`, scheme.`from_date`)))*(DATEDIFF(now(), scheme.`from_date`)) as total_estimated_weight FROM `scheme_detail` join  scheme on scheme.id = scheme_detail.scheme_id where (scheme_detail.target_dispatched_ton/ ( DATEDIFF(scheme.`to_date`, scheme.`from_date`)))*(DATEDIFF(now(), scheme.`from_date`)) < $weight order by total_estimated_weight DESC limit 0,1;";

		$data_query = "SELECT scheme_detail.reward_image,scheme_detail.reward_name,scheme_detail.target_dispatched_ton ,  DATEDIFF(scheme.`to_date`, scheme.`from_date`) AS scheme_days, if( (DATEDIFF(now(), scheme.`from_date`)) <= DATEDIFF(scheme.`to_date`, scheme.`from_date`), DATEDIFF(now(), scheme.`from_date`),  DATEDIFF(scheme.`to_date`, scheme.`from_date`)) AS completed_days, (scheme_detail.target_dispatched_ton/ ( DATEDIFF(scheme.`to_date`, scheme.`from_date`)))*( if( (DATEDIFF(now(), scheme.`from_date`)) <= DATEDIFF(scheme.`to_date`, scheme.`from_date`), DATEDIFF(now(), scheme.`from_date`),  DATEDIFF(scheme.`to_date`, scheme.`from_date`)) ) as total_estimated_weight FROM `scheme_detail` join  scheme on scheme.id = scheme_detail.scheme_id where (scheme_detail.target_dispatched_ton/ ( DATEDIFF(scheme.`to_date`, scheme.`from_date`)))*( if( (DATEDIFF(now(), scheme.`from_date`)) <= DATEDIFF(scheme.`to_date`, scheme.`from_date`), DATEDIFF(now(), scheme.`from_date`),  DATEDIFF(scheme.`to_date`, scheme.`from_date`)) ) < $weight and scheme.id = $schemeid order by total_estimated_weight DESC limit 0,1;";
		$query = $CI->db->query($data_query);
		return $row = $query->row_array();
		 
	}
	function getrate($brand_id,$category_id){
    	$CI =& get_instance(); 
    	$CI->db->select('rate');
		$CI->db->from('rate_master');
		$CI->db->where('brand_id',$brand_id);
		$CI->db->where('category_id',$category_id);
		$CI->db->order_by('created_at','DESC');
		$CI->db->limit(1); 
		$query = $CI->db->get();
		if($query->num_rows() > 0 )
	    {
	    	$row = $query->row_array(); 
	      	return $row['rate']  ;
	    } 
	    else
	    {
	    	return '';
	    }
    }

    function getlooserate($brand_id,$category_id){
    	$CI =& get_instance(); 
    	$CI->db->select('rate_master.rate');
    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.base_rate = 1 and e2.state_id = 29 order by e2.id desc limit 1) as base_empty_tin_rate");
		$CI->db->from('rate_master');
		$CI->db->where('rate_master.brand_id',$brand_id);
		$CI->db->where('rate_master.category_id',$category_id);
		$CI->db->order_by('created_at','DESC');
		$CI->db->limit(1); 
		$query = $CI->db->get();
		//echo $CI->db->last_query(); die;
		if($query->num_rows() > 0 )
	    {
	    	$row = $query->row_array(); 
	      	return round(((  ($row['rate']-$row['base_empty_tin_rate'])/15 )/.910),2);
	    } 
	    else
	    {
	    	return '';
	    }
    }
    function getlooseratevansapati($brand_id,$category_id){
    	$CI =& get_instance(); 
    	$CI->db->select('rate_master.rate');
    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.base_rate = 1 and e2.state_id = 29 order by e2.id desc limit 1) as base_empty_tin_rate");
		$CI->db->from('rate_master');
		$CI->db->where('rate_master.brand_id',$brand_id);
		$CI->db->where('rate_master.category_id',$category_id);
		$CI->db->order_by('created_at','DESC');
		$CI->db->limit(1); 
		$query = $CI->db->get();
		//echo $CI->db->last_query(); die;
		if($query->num_rows() > 0 )
	    {
	    	$row = $query->row_array(); 
	      	return round(((  ($row['rate']-$row['base_empty_tin_rate'])/15 )/.897),2);
	    } 
	    else
	    {
	    	return '';
	    }
    }
 

    function getskurate($brand_id,$category_id,$base_sku_id,$sku_id,$weight,$weight_type,$type='',$fixed_rate=''){

    	if($fixed_rate)
    	{
    		$CI =& get_instance(); 
	    	$CI->db->select('fixed_rate');
	    	$CI->db->from('products');
	    	$CI->db->where('products.brand_id',$brand_id);
			$CI->db->where('products.category_id',$category_id);
			$CI->db->where('products.id',$sku_id);
			$query = $CI->db->get(); 
			//echo $CI->db->last_query(); die;
			if($query->num_rows() > 0 )
		    {
		    	$row = $query->row_array(); 
		    	return round($row['fixed_rate'],2);
		    }
		    else
		    {
		    	return '';
		    }
    	}
    	else
    	{
	    	$CI =& get_instance(); 
	    	$CI->db->select('rate');
	    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.base_rate = 1 and e2.state_id = 29 order by e2.id desc limit 1) as base_empty_tin_rate");
	    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.product_id = $sku_id and e2.state_id = 29 order by e2.id desc limit 1) as empty_tin_rate");
			$CI->db->from('rate_master'); 
			$CI->db->where('rate_master.brand_id',$brand_id);
			$CI->db->where('rate_master.category_id',$category_id);
			$CI->db->order_by('rate_master.created_at','DESC');
			$CI->db->limit(1); 
			$query = $CI->db->get(); 
			//echo $CI->db->last_query(); die;
			if($query->num_rows() > 0 )
		    {
		    	$row = $query->row_array(); 
		      	$approx_weight=0.02;
		      	$l_to_kg_rate = 1/.91;
		      	if($type)
	               $l_to_kg_rate = 1/.897; 
	           	$empty_tin_charge = $row['empty_tin_rate'];
		      	if($weight_type!=1)
	            { 
		      		$packing_rate_ltr = ($row['rate']-$row['base_empty_tin_rate'])/15;
		      	}
				else
				{  
					$packing_rate_ltr = (($row['rate']-$row['base_empty_tin_rate'])/15)*$l_to_kg_rate;
				}
				//echo $packing_rate_ltr; die;
				if($weight_type==3)
				{
					$weight = $weight/1000;
				}
		      	$sku_rate = round(($weight*$packing_rate_ltr),2);
		      	$sku_rate = $sku_rate+$empty_tin_charge;
		      	return $sku_rate;
		    } 
		    else
		    {
		    	return '';
		    }
		}
    }


    function getskurate_test($brand_id,$category_id,$base_sku_id,$sku_id,$weight,$weight_type,$type='',$fixed_rate=''){

    	if($fixed_rate)
    	{
    		$CI =& get_instance(); 
	    	$CI->db->select('fixed_rate');
	    	$CI->db->from('products');
	    	$CI->db->where('products.brand_id',$brand_id);
			$CI->db->where('products.category_id',$category_id);
			$CI->db->where('products.id',$sku_id);
			$query = $CI->db->get(); 
			//echo $CI->db->last_query(); die;
			if($query->num_rows() > 0 )
		    {
		    	$row = $query->row_array(); 
		    	echo round($row['fixed_rate'],2); die;
		    	return round($row['fixed_rate'],2);
		    }
		    else
		    {
		    	return '';
		    }
    	}
    	else
    	{
	    	$CI =& get_instance(); 
	    	$CI->db->select('rate');
	    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.base_rate = 1 and e2.state_id = 29 order by e2.id desc limit 1) as base_empty_tin_rate");
	    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.product_id = $sku_id and e2.state_id = 29 order by e2.id desc limit 1) as empty_tin_rate");
			$CI->db->from('rate_master'); 
			$CI->db->where('rate_master.brand_id',$brand_id);
			$CI->db->where('rate_master.category_id',$category_id);
			$CI->db->order_by('rate_master.created_at','DESC');
			$CI->db->limit(1); 
			$query = $CI->db->get(); 
			echo $CI->db->last_query(); die;
			if($query->num_rows() > 0 )
		    {
		    	$row = $query->row_array(); 
		    	echo "<pre>"; print_r($row); die;
		      	$approx_weight=0.02;
		      	$l_to_kg_rate = 1/.91;
		      	if($type)
	               $l_to_kg_rate = 1/.897; 
	           	$empty_tin_charge = $row['empty_tin_rate'];
		      	if($weight_type!=1)
	            { 
		      		$packing_rate_ltr = ($row['rate']-$row['base_empty_tin_rate'])/15;
		      	}
				else
				{  
					$packing_rate_ltr = (($row['rate']-$row['base_empty_tin_rate'])/15)*$l_to_kg_rate;
				}
				//echo $packing_rate_ltr; die;
				if($weight_type==3)
				{
					$weight = $weight/1000;
				}
		      	$sku_rate = round(($weight*$packing_rate_ltr),2);
		      	$sku_rate = $sku_rate+$empty_tin_charge;
		      	echo $sku_rate; die;
		      	return $sku_rate;
		    } 
		    else
		    {
		    	return '';
		    }
		}
    }

    function getskurate1($brand_id,$category_id,$base_sku_id,$sku_id,$weight,$weight_type,$type=''){
    	$CI =& get_instance(); 
    	$CI->db->select('rate');
    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.base_rate = 1 and e2.state_id = 29 order by e2.id desc limit 1) as base_empty_tin_rate");
    	$CI->db->select("(select e2.rate from empty_tin_rates as e2 where `e2`.`brand_id` = $brand_id and e2.category_id = $category_id and e2.product_id = $sku_id and e2.state_id = 29 order by e2.id desc limit 1) as empty_tin_rate");
		$CI->db->from('rate_master'); 
		$CI->db->where('rate_master.brand_id',$brand_id);
		$CI->db->where('rate_master.category_id',$category_id);
		$CI->db->order_by('rate_master.created_at','DESC');
		$CI->db->limit(1); 
		$query = $CI->db->get(); 
		echo $CI->db->last_query(); die;
		if($query->num_rows() > 0 )
	    {
	    	$row = $query->row_array(); 
	      	$approx_weight=0.02;
	      	$l_to_kg_rate = 1/.91;
	      	if($type)
               $l_to_kg_rate = 1/.897; 
           	$empty_tin_charge = $row['empty_tin_rate'];
	      	if($weight_type!=1)
            { 
	      		$packing_rate_ltr = ($row['rate']-$row['base_empty_tin_rate'])/15;
	      	}
			else
			{  
				$packing_rate_ltr = (($row['rate']-$row['base_empty_tin_rate'])/15)*$l_to_kg_rate;
			}
			//echo $packing_rate_ltr; die;
			if($weight_type==3)
			{
				$weight = $weight/1000;
			}
	      	$sku_rate = round(($weight*$packing_rate_ltr),2);
	      	$sku_rate = $sku_rate+$empty_tin_charge;
	      	return $sku_rate;
	    } 
	    else
	    {
	    	return '';
	    }
    }


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

		/*$CI =& get_instance(); 
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
		} */
		$condition_emp = "and ( booking_booking.admin_id = $admin_id )"; 
		$CI =& get_instance();
		$dispetched_month = date('m-Y');// $conditions['month'].'-'.$conditions['year'];	
		$data_query_dispetch = ' select SUM(pi_sku_history.weight)  as total_dispateched_weight, SUM(pi_sku_history.amount)  as total_dispatched_amount from pi_history
			LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number
			LEFT JOIN `booking_booking` ON `booking_booking`.`id`= `pi_sku_history`.`booking_id`  
			LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status=0  and ( pi_history.dispatch_date like  "%'.$dispetched_month.'%" ) '.$condition_emp;
		$query = $CI->db->query($data_query_dispetch);
		$row = $query->row_array();
		if($row)
		{
			$total_bargain_weight = $row['total_dispateched_weight']; 
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


		/*$CI =& get_instance();
		$data_query = "SELECT sum(`total_weight`) as total_bargain_weight FROM `booking_booking` right JOIN (Select DISTINCT(user_id) from targets where targets.month = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and targets.year = YEAR(CURRENT_DATE - INTERVAL 0 MONTH)) as t ON booking_booking.admin_id = t.user_id WHERE booking_booking.`status` <> 3 and YEAR( booking_booking.`created_at`) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( booking_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and booking_booking.`total_weight` > 0";
		$query = $CI->db->query($data_query);
		$row = $query->row_array();
		if($row)
		{
			$total_bargain_weight = $row['total_bargain_weight']; 
		}*/


		$CI =& get_instance();
		$dispetched_month = date('m-Y');// $conditions['month'].'-'.$conditions['year'];	
		$data_query_dispetch = ' select SUM(pi_sku_history.weight)  as total_dispateched_weight, SUM(pi_sku_history.amount)  as total_dispatched_amount from pi_history
			LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number
			LEFT JOIN `booking_booking` ON `booking_booking`.`id`= `pi_sku_history`.`booking_id`  
			LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status=0  and ( pi_history.dispatch_date like  "%'.$dispetched_month.'%" ) ';
		$query = $CI->db->query($data_query_dispetch);
		$row = $query->row_array();
		if($row)
		{
			$total_bargain_weight = $row['total_dispateched_weight']; 
		}
		//echo "<pre>"; print_r($row); die;

		$CI =& get_instance();
		$data_query1 = "SELECT count(`id`) as total_visited FROM `employee_locations` right JOIN (Select DISTINCT(user_id) from targets where targets.month = MONTH(CURRENT_DATE - INTERVAL 0 MONTH) and targets.year = YEAR(CURRENT_DATE - INTERVAL 0 MONTH)) as t ON employee_locations.user_id = t.user_id WHERE employee_locations.`address` LIKE '%###%' and YEAR( employee_locations.`tracking_date`) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( employee_locations.tracking_date) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		$query = $CI->db->query($data_query1);
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
 	

 	function GetBookingsummary($state_id,$conditions){ 
 	 	
 		$CI =& get_instance(); 
		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;
		$visit_condition = "";
		$month = $conditions['year'].'-'.$conditions['month'];	
		$dispetched_month = $conditions['month'].'-'.$conditions['year'];	
		$employee = $conditions['employee'];
		$condition_emp = "";
		if($employee)
		{
			$condition_emp = "and ( booking_booking.admin_id = $employee )"; 
		}

		$admin_info = $CI->session->userdata('admin');  
		$admin_role = $admin_info['role'];
		$loggedin_id = $admin_info['id'];
		if($conditions['report_type']==1 && $admin_role==5)
		{
			$condition_emp .= " and admin.performance_viewer = $loggedin_id";
		}
		if($conditions['temalead']!='' && $admin_role==4)
		{
			$condition_emp .= " and admin.performance_viewer = ".$conditions['temalead'];
		}
		
		$condition_production_unit = "";
		if(isset($conditions['production_unit']) && !empty($conditions['production_unit']))
		{
			$condition_production_unit  = " and booking_booking.production_unit = '".$conditions['production_unit']."'"; 
		}
		
		$booking_date_range_condition = 'booking_booking.created_at like "%'.$month.'%"';

		if(isset($conditions['booking_date_from']) && !empty(trim($conditions['booking_date_from'])))
		{
			$booking_date_range_condition = "( DATE_FORMAT(STR_TO_DATE(`booking_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";
		}

		$data_query_booking = 'SELECT category.category_name, booking_booking.category_id,booking_booking.brand_id, brands.name as brand_name, vendors.state_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id` LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id` LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id`  where booking_booking.status <> 3 and vendors.state_id='.$state_id.' and ( '.$booking_date_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.' GROUP by booking_booking.category_id order by category.category_name asc';		
		$query_booking = $CI->db->query($data_query_booking);     
	    $booking_response = $query_booking->result_array();  


	    $pi_date_range_condition = 'pi_history.created_at like "%'.$month.'%" ';
		if(isset($conditions['booking_date_from']) && !empty(trim($conditions['booking_date_from'])))
		{
			$pi_date_range_condition = "( DATE_FORMAT(STR_TO_DATE(`pi_history`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";
		}
		
		$data_query_pi = 'select category.category_name, booking_booking.category_id,booking_booking.brand_id, brands.name as brand_name,  SUM(pi_sku_history.weight) as total_weight_pi, SUM( pi_sku_history.amount) as total_amount_pi from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON `booking_booking`.`id`= `pi_sku_history`.`booking_id`  LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id` LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where booking_booking.status <> 3 and pi_history.status = 0 and vendors.state_id='.$state_id.' and ( '.$pi_date_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.' GROUP by booking_booking.category_id order by category.category_name asc';		
		$query_pi = $CI->db->query($data_query_pi);     
	    $pi_response = $query_pi->result_array();  

	    //$data_query_dispetch = 'select category.category_name, booking_booking.category_id,booking_booking.brand_id, brands.name as brand_name, SUM(pi_history.total_weight_pi) as total_dispateched_weight, SUM( pi_history.pi_amount) as total_dispatched_amount from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id` LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id` LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status=0 and vendors.state_id='.$state_id.' and ( pi_history.dispatch_date like "%'.$dispetched_month.'%" ) '.$condition_emp.' GROUP by booking_booking.category_id order by category.category_name asc';
		
		$dispatch_date_range_condition = 'pi_history.dispatch_date like "%'.$dispetched_month.'%" ';
		if(isset($conditions['booking_date_from']) && !empty(trim($conditions['booking_date_from'])))
		{
			$dispatch_date_range_condition = "( DATE_FORMAT(STR_TO_DATE(`pi_history`.`dispatch_date`,'%d-%m-%Y'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";
		}
	     
		 $data_query_dispetch = ' select category.category_name, booking_booking.category_id,booking_booking.brand_id, brands.name as brand_name,SUM(pi_sku_history.weight)  as total_dispateched_weight, SUM(pi_sku_history.amount)  as total_dispatched_amount from pi_history
LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number
LEFT JOIN `booking_booking` ON `booking_booking`.`id`= `pi_sku_history`.`booking_id` 
LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` 
LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id` 
LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id` 
LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status=0 and vendors.state_id='.$state_id.' and ( '.$dispatch_date_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.' GROUP by booking_booking.category_id order by category.category_name asc'; 

		$query_dispetch = $CI->db->query($data_query_dispetch);     
	    $pi_dispetch = $query_dispetch->result_array();  

	    //echo "<pre>"; print_r($booking_response);  print_r($pi_response);   print_r($pi_dispetch); die;
	    $unset_array = array();
	    

	    $reponse = array();
	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['category_id'], array_column($pi_response, 'category_id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_weight_pi'] =   0 ;
	    			$booking_response[$key]['total_amount_pi'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['total_weight_pi'] = $pi_response[$key_exists]['total_weight_pi'];
	    			$booking_response[$key]['total_amount_pi'] = $pi_response[$key_exists]['total_amount_pi'];	 
	    			//unset($pi_response[$key_exists]);		 
	    			$unset_array[] = $key_exists;
	    		}
	    	}
	    } 

	    if(count($unset_array))
	    {
	    	foreach ($unset_array as $key => $value) { 
	    		unset($pi_response[$value]);
	    	}
	    } 
	     //echo "<pre>"; print_r($pi_response); die;
	    if($pi_response)
	    {
	    	foreach ($pi_response as $key => $value) {
	    		$pi_response[$key]['total_weight'] =   0 ;
	    		$pi_response[$key]['total_amount'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_response,$booking_response);
	    }

	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['category_id'], array_column($pi_dispetch, 'category_id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_dispateched_weight'] =   0 ;
	    			$booking_response[$key]['total_dispatched_amount'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['total_dispateched_weight'] = $pi_dispetch[$key_exists]['total_dispateched_weight'];
	    			$booking_response[$key]['total_dispatched_amount'] = $pi_dispetch[$key_exists]['total_dispatched_amount'];	 
	    			//unset($pi_response[$key_exists]);		 
	    			$unset_array[] = $key_exists;
	    		}
	    	}
	    } 
	    if($unset_array)
	    {
	    	foreach ($unset_array as $key => $value) { 
	    		unset($pi_dispetch[$value]);
	    	}
	    } 
	     //echo "<pre>"; print_r($pi_response); die;
	    if($pi_dispetch)
	    {
	    	foreach ($pi_dispetch as $key => $value) {
	    		$pi_dispetch[$key]['total_weight'] =   0 ;
	    		$pi_dispetch[$key]['total_amount'] =   0 ; 
	    		$pi_dispetch[$key]['total_weight_pi'] =   0 ;
	    		$pi_dispetch[$key]['total_amount_pi'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_dispetch,$booking_response);
	    } 
	    return $booking_response; 
	}

	function GetTotalBookingsummary($conditions){ 
 	 	
 		$CI =& get_instance(); 
		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;
		$visit_condition = "";
		$month = $conditions['year'].'-'.$conditions['month'];	
		$dispetched_month = $conditions['month'].'-'.$conditions['year'];	
		$employee = $conditions['employee'];
		$condition_emp = "";
		if($employee)
		{
			$condition_emp = "and ( booking_booking.admin_id = $employee )"; 
		}

		$data_query_booking = 'SELECT booking_booking.brand_id, brands.name as brand_name, vendors.state_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  where booking_booking.status <> 3  and ( booking_booking.created_at like "%'.$month.'%" ) '.$condition_emp.' GROUP by booking_booking.brand_id';		
		$query_booking = $CI->db->query($data_query_booking);     
	    $booking_response = $query_booking->result_array();  


	    $data_query_pi = 'select booking_booking.brand_id, brands.name as brand_name,  SUM(pi_history.total_weight_pi) as total_weight_pi, SUM( pi_history.pi_amount) as total_amount_pi from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`   where booking_booking.status <> 3 and pi_history.status = 0  and ( pi_history.created_at like "%'.$month.'%"  ) '.$condition_emp.'  GROUP by booking_booking.brand_id';		
		$query_pi = $CI->db->query($data_query_pi);     
	    $pi_response = $query_pi->result_array();  

	    $data_query_dispetch = 'select booking_booking.brand_id, brands.name as brand_name, SUM(pi_history.total_weight_pi) as total_dispateched_weight, SUM( pi_history.pi_amount) as total_dispatched_amount from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status =0 and (pi_history.dispatch_date like "%'.$dispetched_month.'%" ) '.$condition_emp.' GROUP by booking_booking.brand_id';		
		$query_dispetch = $CI->db->query($data_query_dispetch);     
	    $pi_dispetch = $query_dispetch->result_array();  

	    //echo "<pre>"; print_r($booking_response);  print_r($pi_response);   print_r($pi_dispetch); die;
	    $unset_array = array();
	    

	    $reponse = array();
	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['brand_id'], array_column($pi_response, 'brand_id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_weight_pi'] =   0 ;
	    			$booking_response[$key]['total_amount_pi'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['total_weight_pi'] = $pi_response[$key_exists]['total_weight_pi'];
	    			$booking_response[$key]['total_amount_pi'] = $pi_response[$key_exists]['total_amount_pi'];	 
	    			//unset($pi_response[$key_exists]);		 
	    			$unset_array[] = $key_exists;
	    		}
	    	}
	    } 

	    if(count($unset_array))
	    {
	    	foreach ($unset_array as $key => $value) { 
	    		unset($pi_response[$value]);
	    	}
	    } 
	     //echo "<pre>"; print_r($pi_response); die;
	    if($pi_response)
	    {
	    	foreach ($pi_response as $key => $value) {
	    		$pi_response[$key]['total_weight'] =   0 ;
	    		$pi_response[$key]['total_amount'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_response,$booking_response);
	    }

	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['brand_id'], array_column($pi_dispetch, 'brand_id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_dispateched_weight'] =   0 ;
	    			$booking_response[$key]['total_dispatched_amount'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['total_dispateched_weight'] = $pi_dispetch[$key_exists]['total_dispateched_weight'];
	    			$booking_response[$key]['total_dispatched_amount'] = $pi_dispetch[$key_exists]['total_dispatched_amount'];	 
	    			//unset($pi_response[$key_exists]);		 
	    			$unset_array[] = $key_exists;
	    		}
	    	}
	    } 
	    if($unset_array)
	    {
	    	foreach ($unset_array as $key => $value) { 
	    		unset($pi_dispetch[$value]);
	    	}
	    } 
	     //echo "<pre>"; print_r($pi_response); die;
	    if($pi_dispetch)
	    {
	    	foreach ($pi_dispetch as $key => $value) {
	    		$pi_dispetch[$key]['total_weight'] =   0 ;
	    		$pi_dispetch[$key]['total_amount'] =   0 ; 
	    		$pi_dispetch[$key]['total_weight_pi'] =   0 ;
	    		$pi_dispetch[$key]['total_amount_pi'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_dispetch,$booking_response);
	    } 
	    return $booking_response; 
	}


	function GetTotalBookingsummary1($conditions){ 
 	 	//echo "<<pre>"; print_r($conditions); die;
 		$CI =& get_instance(); 
		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;
		$visit_condition = "";
		$month = $conditions['year'].'-'.$conditions['month'];	
		$dispetched_month = $conditions['month'].'-'.$conditions['year'];	
		$employee = $conditions['employee'];
		$condition_emp = "";
		$condition_emp1 = "";
		if($employee)
		{
			$condition_emp = "and ( booking_booking.admin_id = $employee )"; 
			$condition_emp1 = "and ( admin_sku.admin_id = $employee )"; 
		}



		$admin_info = $CI->session->userdata('admin');  
		$admin_role = $admin_info['role'];
		$loggedin_id = $admin_info['id'];
		if($conditions['report_type']==1 && $admin_role==5)
		{
			$condition_emp .= " and admin.performance_viewer = $loggedin_id";
			$condition_emp1 .= " and admin.performance_viewer = $loggedin_id";
		}
		if($conditions['temalead']!='' && $admin_role==4)
		{
			$condition_emp .= " and admin.performance_viewer = ".$conditions['temalead'];
			$condition_emp1 .= " and admin.performance_viewer = ".$conditions['temalead'];
		}
		$condition_production_unit = "";
		if(isset($conditions['production_unit']) && !empty($conditions['production_unit']))
		{
			$condition_production_unit  = " and booking_booking.production_unit = '".$conditions['production_unit']."'"; 
		}

		$booking_date_range_condition = 'booking_booking.created_at like "%'.$month.'%"';

		if(isset($conditions['booking_date_from']) && !empty(trim($conditions['booking_date_from'])))
		{
			$booking_date_range_condition = "( DATE_FORMAT(STR_TO_DATE(`booking_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";
		}



		$data_query_booking = 'SELECT category.category_name, booking_booking.category_id,booking_booking.brand_id, brands.name as brand_name, vendors.state_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id` LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where booking_booking.status <> 3  and ( '.$booking_date_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.' GROUP by booking_booking.category_id order by category.category_name asc';
		 //echo $data_query_booking; die;			
		$query_booking = $CI->db->query($data_query_booking);     
	    $booking_response = $query_booking->result_array();  



	    $pi_date_range_condition = 'pi_history.created_at like "%'.$month.'%" ';
		if(isset($conditions['booking_date_from']) && !empty(trim($conditions['booking_date_from'])))
		{
			$pi_date_range_condition = "( DATE_FORMAT(STR_TO_DATE(`pi_history`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";
		}

	    $data_query_pi = 'select category.category_name,booking_booking.category_id, booking_booking.brand_id, brands.name as brand_name,  SUM(pi_sku_history.weight) as total_weight_pi, SUM(pi_sku_history.amount) as total_amount_pi from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON `booking_booking`.`id`= `pi_sku_history`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id`  LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where booking_booking.status <> 3 and pi_history.status = 0  and ( '.$pi_date_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.'  GROUP by booking_booking.category_id order by category.category_name asc';




	    //echo $data_query_pi; die;	
		$query_pi = $CI->db->query($data_query_pi);     
	    $pi_response = $query_pi->result_array();  


	    $dispatch_date_range_condition = 'pi_history.dispatch_date like "%'.$dispetched_month.'%" ';
		if(isset($conditions['booking_date_from']) && !empty(trim($conditions['booking_date_from'])))
		{
			$dispatch_date_range_condition = "( DATE_FORMAT(STR_TO_DATE(`pi_history`.`dispatch_date`,'%d-%m-%Y'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";
		}

	    $data_query_dispetch = 'select category.category_name,booking_booking.category_id,booking_booking.brand_id, brands.name as brand_name, SUM(pi_sku_history.weight) as total_dispateched_weight, SUM( pi_sku_history.amount) as total_dispatched_amount from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON `booking_booking`.`id`= `pi_sku_history`.`booking_id`  LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  LEFT JOIN `category` ON `category`.`id`=`booking_booking`.`category_id` LEFT JOIN `admin` ON `admin`.`id`=`booking_booking`.`admin_id` where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status =0 and ( '.$dispatch_date_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.' GROUP by booking_booking.category_id order by category.category_name asc';	
		$query_dispetch = $CI->db->query($data_query_dispetch);     
	    $pi_dispetch = $query_dispetch->result_array();  

	    //echo "<pre>"; print_r($booking_response);  print_r($pi_response);   print_r($pi_dispetch); die;
	    $unset_array = array();
	    

	    $reponse = array();
	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['category_id'], array_column($pi_response, 'category_id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_weight_pi'] =   0 ;
	    			$booking_response[$key]['total_amount_pi'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['total_weight_pi'] = $pi_response[$key_exists]['total_weight_pi'];
	    			$booking_response[$key]['total_amount_pi'] = $pi_response[$key_exists]['total_amount_pi'];	 
	    			//unset($pi_response[$key_exists]);		 
	    			$unset_array[] = $key_exists;
	    		}
	    	}
	    } 

	    if(count($unset_array))
	    {
	    	foreach ($unset_array as $key => $value) { 
	    		unset($pi_response[$value]);
	    	}
	    } 
	     //echo "<pre>"; print_r($pi_response); die;
	    if($pi_response)
	    {
	    	foreach ($pi_response as $key => $value) {
	    		$pi_response[$key]['total_weight'] =   0 ;
	    		$pi_response[$key]['total_amount'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_response,$booking_response);
	    }

	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['category_id'], array_column($pi_dispetch, 'category_id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_dispateched_weight'] =   0 ;
	    			$booking_response[$key]['total_dispatched_amount'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['total_dispateched_weight'] = $pi_dispetch[$key_exists]['total_dispateched_weight'];
	    			$booking_response[$key]['total_dispatched_amount'] = $pi_dispetch[$key_exists]['total_dispatched_amount'];	 
	    			//unset($pi_response[$key_exists]);		 
	    			$unset_array[] = $key_exists;
	    		}
	    	}
	    } 
	    if($unset_array)
	    {
	    	foreach ($unset_array as $key => $value) { 
	    		unset($pi_dispetch[$value]);
	    	}
	    } 
	     //echo "<pre>"; print_r($pi_response); die;
	    if($pi_dispetch)
	    {
	    	foreach ($pi_dispetch as $key => $value) {
	    		$pi_dispetch[$key]['total_weight'] =   0 ;
	    		$pi_dispetch[$key]['total_amount'] =   0 ; 
	    		$pi_dispetch[$key]['total_weight_pi'] =   0 ;
	    		$pi_dispetch[$key]['total_amount_pi'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_dispetch,$booking_response);
	    } 
	    return $booking_response; 
	}

 	function makerstotalerformance($state_id,$conditions)
    {
    	$CI =& get_instance(); 
    	$condition = "YEAR( booking_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( booking_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		if(isset($conditions) && !empty($conditions))
		{ 
			$month = $conditions['year'].'-'.$conditions['month'];
			//$condition = "( booking_booking.created_at like '%$month%' )";
			$condition = "( DATE_FORMAT(STR_TO_DATE(`booking_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";
			$employee = $conditions['employee'];
			if($employee)
			{
				$condition .= "and ( booking_booking.admin_id = $employee )";
			}

			$admin_info = $CI->session->userdata('admin');  
			$admin_role = $admin_info['role'];
			$loggedin_id = $admin_info['id'];
			if($conditions['report_type']==1 && $admin_role==5)
			{
				$condition .= " and admin.performance_viewer = $loggedin_id";
			}
			if($conditions['temalead']!='' && $admin_role==4)
			{
				$condition .= " and admin.performance_viewer = ".$conditions['temalead'];
			}

			if(isset($conditions['production_unit']) && !empty($conditions['production_unit']))
			{
				$condition  .= " and booking_booking.production_unit = '".$conditions['production_unit']."'"; 
			}
		}


    	
		$CI->db->select('sum(total_weight ) as total_weight ');
		$CI->db->select('sum(`rate`*`quantity`) as total_amount');
		$CI->db->from('booking_booking ');
		$CI->db->join("vendors","vendors.id=booking_booking.party_id","left");
		$CI->db->join("admin","admin.id=booking_booking.admin_id","left");
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

		
    	$CI =& get_instance();   
    	$condition = "YEAR( secondary_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( secondary_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		if(isset($conditions) && !empty($conditions))
		{
			$month = $conditions['year'].'-'.$conditions['month'];
			$condition = "( secondary_booking.created_at like '%$month%'  )";
			$condition = "(DATE_FORMAT(STR_TO_DATE(`secondary_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' ) ";
			//echo $condition; die;
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

			$admin_info = $CI->session->userdata('admin');  
			$admin_role = $admin_info['role'];
			$loggedin_id = $admin_info['id'];
			 
			if($conditions['report_type']==1 && $admin_role==5)
			{
				$condition .= " and admin.performance_viewer = $loggedin_id";
			}
			if($conditions['temalead']!='' && $admin_role==4)
			{
				$condition .= " and admin.performance_viewer = ".$conditions['temalead'];
			}

		}    
		$CI->db->select('sum(secondary_booking_skus.weight ) as total_weight ');
		$CI->db->select('sum(`secondary_booking_skus`.`rate`*`secondary_booking_skus`.`quantity`*`products`.`packing_items_qty`) as total_amount');
		$CI->db->from('secondary_booking');
		//$CI->db->from('secondary_booking_skus');
		$CI->db->join("secondary_booking_skus","secondary_booking_skus.secondary_booking_id=secondary_booking.id","left");
		$CI->db->join("vendors","vendors.id=secondary_booking.supply_from","left");
		$CI->db->join("products","products.id=secondary_booking_skus.product_id","left");
		$CI->db->join("admin","admin.id=secondary_booking.admin_id","left");
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



    

    