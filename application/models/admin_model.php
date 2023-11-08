<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 

	function Getteamleads(){
		$this->db->select('DISTINCT(admin.performance_viewer) as user_id');     
		$this->db->select('a.*');  
		$this->db->join('admin a','a.id = admin.performance_viewer','left');     
	    $this->db->from('admin'); 
	    $this->db->where('admin.performance_viewer is NOT NULL');   
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get();  
	    return $query->result_array(); 
	} 

	function allemployess($condition){
		$this->db->select('admin.*');     
	    $this->db->from('admin'); 
	    if(isset($condition['admin.role']) && !empty($condition['admin.role']))
	    {
	    	//echo $condition['admin.role']; die;
	    	$this->db->where('admin.role IN ('.$condition['admin.role'].')');  
	    	unset($condition['admin.role']);
	    }
	    $this->db->where($condition);   
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    return $query->result_array(); 
	} 
	function GetAdmin(){
		$this->db->select('admin.*');
		$this->db->select('roles.role_name');
	    $this->db->join('roles','roles.id = admin.role');   
	    $this->db->from('admin');   
	    $this->db->order_by('name','ASC');  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function GetAddminbyId($admin_id){
		$this->db->select('*');
	    $this->db->from('admin');   
	    $this->db->where('id',$admin_id);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      //echo "<pre>"; print_r($row); die;
	      return $row;  
	    }
	} 

	function GetUserbyId($condition){
		$this->db->select('*');
	    $this->db->from('vendors');   
	    $this->db->where($condition);  
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->row_array(); 
	      return $row;  
	    }
	} 


	function UpdateAdmin($update_data,$condition){
      $this->db->where($condition);
      return $this->db->update('admin', $update_data);
    }

    function GetAllCheckers(){  
		$this->db->select('*');
	    $this->db->from('admin'); 
	    $this->db->where('role' ,2); 
	    $this->db->or_where('role' ,5);  
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}
	function GetAllViewers($condition = array()){  
		$this->db->select('*');
	    $this->db->from('admin'); 
	    $this->db->where('role' ,5);    
	    $this->db->where($condition);
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}

	function GetRoles(){  
		$this->db->select('*');
	    $this->db->from('roles');   
	    $this->db->order_by('role_name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}
     
	function GetEmployees(){
		$this->db->select('*');
	    $this->db->from('employee');   
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    if($query->num_rows() > 0 )
	    {
	      $row = $query->result_array(); 
	      return $row;  
	    }
	}

	function AddAdmin($insertdata)
	{
		return $this->db->insert('admin',$insertdata);
	}	 


	function DeleteAdmin($condition){
	  $this->db->where($condition);
	  return $this->db->delete('admin');
	}

	function UpdateEmployee($updatedata,$condition){
      $this->db->where($condition);
      return $this->db->update('employee', $updatedata);
    }

    function GetAllPurchaseMakers(){ 
    	$admin_info = $this->session->userdata('admin');  
        $role = $admin_info['role'];
        $admin_id = $admin_info['id'];  
		$this->db->select('*');
	    $this->db->from('admin'); 
	    //$this->db->where('role' ,1); 
	    $this->db->where("(admin.business_role=2 OR admin.business_role=3)");  
	    if($role==5)
        {
        	//$this->db->where('team_lead_id' ,$admin_id); 
            //$condition = array('team_lead_id', $admin_id);
        }
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}

    function GetAllMakers(){  
		$this->db->select('*');
	    $this->db->from('admin'); 
	    $this->db->where('role' ,1);  
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}
	function GetAllMaker($condition){  
		$this->db->select('*');
	    $this->db->from('admin'); 

	    if(isset($condition) && !empty($condition['state_id']))
	    {
	    	$state_id = $condition['state_id'];
	    	$this->db->where("( find_in_set ( $state_id, admin.state_id ) OR  admin.state_id is NULL OR  admin.state_id ='' )"); 
	    	unset($condition['state_id']);
	    }

	    if($condition)
	    {
	    	$this->db->where($condition);
	    }
	    $this->db->where('role' ,1);  
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get();  
	    $row = $query->result_array(); 
	    return $row; 
	}

	function GetSecondaryMakers($condition){  
		$this->db->select('*');
	    $this->db->from('admin');
	    if(isset($condition) && !empty($condition['state_id']))
	    {
	    	$state_id = $condition['state_id'];
	    	$this->db->where("( find_in_set ( $state_id, admin.state_id ) OR  admin.state_id is NULL OR  admin.state_id ='' )"); 
	    	unset($condition['state_id']);
	    }
	    $this->db->where($condition);
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}

	function GetAllMaker21($condition){  
		$this->db->select('admin.*');
		$this->db->select('sum(b2.total_weight ) as total_weight ');
		$this->db->select('sum(`b2`.`rate`*`b2`.`quantity`) as total_amount');
	    $this->db->from('admin'); 

	    if(isset($condition) && !empty($condition['state_id']))
	    {
	    	$state_id = $condition['state_id'];
	    	$this->db->where("( find_in_set ( $state_id, admin.state_id ) OR  admin.state_id is NULL OR  admin.state_id ='' )"); 
	    	$this->db->join("booking_booking","booking_booking.admin_id=admin.id","left");
	    	$this->db->join("vendors","vendors.id=booking_booking.party_id","left"); 
	    	$this->db->join("booking_booking as b2","b2.admin_id=admin.id and vendors.state_id=$state_id","left");

	    	unset($condition['state_id']);

	    }

	    if($condition)
	    {
	    	$this->db->where($condition);
	    }
	    $this->db->where('role' ,1);  
	    $this->db->group_by('admin.id');
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get();  
	    echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}


	function GetBookingsummary($state_id,$conditions){ 

		$admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $loggedin_id = $admin_info['id']; 


 	 	 //echo "<pre>"; print_r($conditions); die;
		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;
		$visit_condition = "";
		$month = $conditions['year'].'-'.$conditions['month'];	
		$dispetched_month = $conditions['month'].'-'.$conditions['year'];	

		$employee = $conditions['employee'];
		$condition_emp = "";
		$employee_condition = "";
		$employee_condition1 = "";
		if(isset($conditions) && !empty($conditions))
		{
			$visit_condition = " and tracking_date like '%".$month."%'";  

			$target_month_year =  $conditions['month'].'-'.$conditions['year'];

			$target_cond = " and targent_month_year like '%".$target_month_year."%'";  
		}
		if($employee)
		{
			$employee_condition = "and ( admin.id = $employee )";
			$employee_condition1 = "and ( admin.id = $employee )";
			$condition_emp = "and ( booking_booking.admin_id = $employee )"; 
		}
		else
		{
			$employee_condition = "and ( (find_in_set($state_id,admin.state_id) OR admin.state_id is NUll OR  admin.state_id = '')  OR booking_booking.state_id=$state_id )  ";
			$employee_condition1 = "and ( (find_in_set($state_id,admin.state_id) OR admin.state_id is NUll OR  admin.state_id = '')  OR admin_sku.state_id=$state_id )  ";
			if($conditions['report_type']==1 && $admin_role==5)
			{
				$employee_condition .= " and admin.performance_viewer = $loggedin_id";
				$employee_condition1 .= " and admin.performance_viewer = $loggedin_id";
			}

			if($conditions['temalead']!='' && $admin_role==4)
			{
				$employee_condition .= " and admin.performance_viewer = ".$conditions['temalead'];
				$employee_condition1 .= " and admin.performance_viewer = ".$conditions['temalead'];
			}



			
			 
		}

		$data_query_booking = 'SELECT (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit , states.name as state_name, (COALESCE(booking_booking.total_weight,0)*100)/(targets.weight) as per_target , booking_booking.state_id, booking_booking.total_weight, booking_booking.total_amount, admin.* from admin left join (SELECT booking_booking.brand_id, booking_booking.admin_id, brands.name as brand_name, vendors.state_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  where vendors.state_id = '.$state_id.' and  booking_booking.status <> 3 and vendors.state_id='.$state_id.' and ( booking_booking.created_at like "%'.$month.'%" ) '.$condition_emp.' GROUP by booking_booking.admin_id) booking_booking on booking_booking.admin_id = admin.id left join  targets on targets.user_id = admin.id and targets.state_ids = '.$state_id.$target_cond.'  left join states on states.id = '.$state_id.'

			left join (SELECT 
			CASE 
			WHEN address LIKE "%andaman%" THEN "Andaman and Nicobar Islands" 
			WHEN address LIKE "%andhra%" THEN "Andhra Pradesh" 
			WHEN address LIKE "%arunachal%" THEN "Arunachal Pradesh" 
			WHEN address LIKE "%assam%" THEN "Assam" 
			WHEN address LIKE "%bihar%" THEN "Bihar"
			WHEN address LIKE "%chandigarh%" THEN "Chandigarh" 
			WHEN address LIKE "%chattisgarh%" THEN "Chattisgarh" 
			WHEN address LIKE "%dadra%" THEN "Dadra and Nagar Haveli" 
			WHEN address LIKE "%daman%" THEN "Daman and Diu" 
			WHEN address LIKE "%delhi%" THEN "Delhi" 
			WHEN address LIKE "%goa%" THEN "Goa" 
			WHEN address LIKE "%Gujrat%" THEN "Gujrat"
			WHEN address LIKE "%haryana%" THEN "Haryana" 
			WHEN address LIKE "%himachal%" THEN "Himachal Pradesh" 
			WHEN address LIKE "%jammu%" THEN "Jammu and Kashmir" 
			WHEN address LIKE "%jharkhand%" THEN "Jharkhand"
			WHEN address LIKE "%karnataka%" THEN "Karnataka" 
			WHEN address LIKE "%kerala%" THEN "Kerala" 
			WHEN address LIKE "%lakshadweep%" THEN "Lakshadweep" 
			WHEN address LIKE "%madhya%" THEN "Madhya Pradesh" 
			WHEN address LIKE "%maharashtra%" THEN "Maharashtra" 
			WHEN address LIKE "%manipur%" THEN "Manipur" 
			WHEN address LIKE "%meghalaya%" THEN "Meghalaya" 
			WHEN address LIKE "%mizoram%" THEN "Mizoram" 
			WHEN address LIKE "%nagaland%" THEN "Nagaland" 
			WHEN address LIKE "%odisha%" THEN "Odisha" 
			WHEN address LIKE "%poducherry%" THEN "Poducherry" 
			WHEN address LIKE "%punjab%" THEN "Punjab" 
			WHEN address LIKE "%rajasthan%" THEN "Rajasthan" 
			WHEN address LIKE "%sikkim%" THEN "Sikkim" 
			WHEN address LIKE "%tamil%" THEN "Tamil Nadu" 
			WHEN address LIKE "%telangana%" THEN "Telangana" 
			WHEN address LIKE "%tripura%" THEN "Tripura" 
			WHEN address LIKE "%uttar pradesh%" THEN "Uttar Pradesh" 
			WHEN address LIKE "%uttarakhand%" THEN "Uttarakhand" 
			WHEN address LIKE "%west%" THEN "West Bengal" 
			WHEN address LIKE "%ladakh%" THEN "Ladakh"  


			END  As state , 
			COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like "%###%" '.$visit_condition.' 
			GROUP BY user_id, state ) visits on visits.user_id = admin.id and states.name = visits.state 

		 where (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC'; 
	
 
		$query_booking = $this->db->query($data_query_booking);     
	    $booking_response = $query_booking->result_array();  


	    //$data_query_pi = 'SELECT  booking_booking.state_id,booking_booking.brand_id, booking_booking.brand_name,  booking_booking.total_weight_pi,booking_booking.total_amount_pi, admin.* from admin left join (select vendors.state_id,booking_booking.admin_id,booking_booking.brand_id, brands.name as brand_name,  SUM(pi_history.total_weight_pi) as total_weight_pi, SUM( pi_history.pi_amount) as total_amount_pi from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`)  LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`   where booking_booking.status <> 3 and pi_history.status = 0 and vendors.state_id='.$state_id.' and ( pi_history .created_at like "%'.$month.'%"  ) '.$condition_emp.'  GROUP by booking_booking.admin_id) booking_booking on booking_booking.admin_id = admin.id where (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC';
	    $data_query_pi = 'SELECT  admin_sku.state_id,  SUM(admin_sku.total_weight_pi) as total_weight_pi,SUM(admin_sku.total_amount_pi) as total_amount_pi, admin.* from admin left join (select vendors.state_id,booking_booking.admin_id,  SUM(pi_sku_history.weight) as total_weight_pi, SUM(pi_sku_history.amount) as total_amount_pi from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON  `booking_booking`.`id` = `pi_sku_history`.`booking_id`    LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`   where booking_booking.status <> 3 and pi_history.status = 0 and vendors.state_id='.$state_id.' and ( pi_history .created_at like "%'.$month.'%"  ) '.$condition_emp.'  GROUP by booking_booking.admin_id) admin_sku on admin_sku.admin_id = admin.id where (role = 6 OR role=1 )'.$employee_condition1.'  Group by admin.id ORDER BY `name` ASC';
		$query_pi = $this->db->query($data_query_pi);    
		//echo $data_query_pi; die; 
	    $pi_response = $query_pi->result_array();  
	    
	    //$data_query_dispetch = ' SELECT booking_booking.state_id,booking_booking.brand_id, booking_booking.brand_name,booking_booking.total_dispateched_weight, booking_booking.total_dispatched_amount, admin.* from admin left join (select  vendors.state_id, booking_booking.admin_id,booking_booking.brand_id, brands.name as brand_name, SUM(pi_history.total_weight_pi) as total_dispateched_weight, SUM( pi_history.pi_amount) as total_dispatched_amount from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status =0 and vendors.state_id='.$state_id.' and ( pi_history.dispatch_date like "%'.$dispetched_month.'%" ) '.$condition_emp.' GROUP by booking_booking.admin_id) booking_booking on booking_booking.admin_id = admin.id where (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC';	

	    $data_query_dispetch = ' SELECT admin_sku.state_id,SUM(admin_sku.total_dispateched_weight) as total_dispateched_weight, SUM(admin_sku.total_dispatched_amount) as total_dispatched_amount, admin.* from admin left join (select vendors.state_id,pi_history.id as pid, booking_booking.admin_id, SUM(pi_sku_history.weight) as total_dispateched_weight, SUM(pi_sku_history.amount) as total_dispatched_amount from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON  `booking_booking`.`id` = `pi_sku_history`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status =0 and vendors.state_id='.$state_id.' and ( pi_history.dispatch_date like "%'.$dispetched_month.'%"  '.$condition_emp.'  ) GROUP by pid,booking_booking.admin_id) admin_sku on admin_sku.admin_id = admin.id where (admin.role = 6 OR admin.role=1 ) '.$employee_condition1.'  Group by admin.id ORDER BY `name` ASC'; 

		$query_dispetch = $this->db->query($data_query_dispetch);   
		//echo $data_query_dispetch; die;   

	    $pi_dispetch = $query_dispetch->result_array();  
	   
	   	

	    $unset_array = array();
	    

	    $reponse = array();
	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['id'], array_column($pi_response, 'id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['pi_total_weight'] =   0 ;
	    			$booking_response[$key]['pi_total_amount'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['pi_total_weight'] = $pi_response[$key_exists]['total_weight_pi'];
	    			$booking_response[$key]['pi_total_amount'] = $pi_response[$key_exists]['total_amount_pi'];	 
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
	    
	    if($pi_response)
	    {
	    	foreach ($pi_response as $key => $value) {
	    		$pi_response[$key]['pi_total_weight'] =   $value['total_weight_pi']; ;
	    		$pi_response[$key]['pi_total_amount'] =   $value['total_amount_pi']; ; ;
	    		$pi_response[$key]['total_weight'] =   0 ;
	    		$pi_response[$key]['total_amount'] =   0 ;  
	    		$pi_response[$key]['per_target'] =   0 ;
	    		$pi_response[$key]['per_target_visit'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_response,$booking_response);
	    }



	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['id'], array_column($pi_dispetch, 'id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_dispateched_weight'] =   0 ;
	    			$booking_response[$key]['total_dispatched_amount'] =   0 ;  
	    			$pi_response[$key]['per_target'] =   0 ;
	    			$pi_response[$key]['per_target_visit'] =   0 ; 
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
	   
	    if($pi_dispetch)
	    {
	    	foreach ($pi_dispetch as $key => $value) {
	    		$pi_dispetch[$key]['total_weight'] =   0 ;
	    		$pi_dispetch[$key]['total_amount'] =   0 ; 
	    		$pi_dispetch[$key]['pi_total_weight'] =   0 ;
	    		$pi_dispetch[$key]['pi_total_amount'] =   0 ;  
	    		$pi_response[$key]['per_target'] =   0 ;
	    		$pi_response[$key]['per_target_visit'] =   0 ; 
	    	}
	    	$booking_response = array_merge($pi_dispetch,$booking_response);
	    } 
	    if($state_id==29)
		{
			//echo "<pre>"; print_r($booking_response); die;
		}
	     //echo "<pre>"; print_r($booking_response); die;
	   // echo "<pre>";print_r($booking_response); die;
	    //echo "<pre>"; print_r($booking_response);  print_r($pi_response);   print_r($pi_dispetch); die;
	   // echo "<pre>";print_r($booking_response); die;
	    return $booking_response; 
	}

	function GetAllMaker2($state_id,$conditions){ 

		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;
		$visit_condition = "";
		

		$condition = "and YEAR( booking_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( booking_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		$employee_condition = "";
		$employee_condition_target  = "";
		if(isset($conditions) && !empty($conditions))
		{ 
			$month = $conditions['year'].'-'.$conditions['month'];
			$condition = "and ( booking_booking.created_at like '%$month%' )";


			$visit_condition = " and tracking_date like '%".$month."%'"; 

			$employee = $conditions['employee'];
			if($employee)
			{
				$condition .= "and ( booking_booking.admin_id = $employee )";
				$employee_condition = "and ( admin.id = $employee )";
				$employee_condition_target  = "and ( targets.user_id = $employee )";
			}

			$target_month_year =  $conditions['month'].'-'.$conditions['year'];
			
		}

		$condition_target = "  targets.targent_month_year = '$target_month_year' and (find_in_set($state_id,targets.state_ids) OR targets.state_ids is NUll OR  targets.state_ids = '' ) ".$employee_condition_target;

		$condition1 = "and YEAR( pi_history .created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( pi_history .created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		$employee_condition = "";
		if(isset($conditions) && !empty($conditions))
		{ 
			$month = $conditions['year'].'-'.$conditions['month'];
			$condition1 = "and ( pi_history .created_at like '%$month%' )";

			$employee = $conditions['employee'];
			if($employee)
			{
				$condition1 .= "and ( booking_booking.admin_id = $employee )";
				$employee_condition = "and ( admin.id = $employee )";
			}
			
		}

		$data_query = 'select (COALESCE(visits.visit,0)*100)/(t3.distributor_visits) as per_target_visit ,  t4.total_dispateched_weight,t4.total_dispatched_amount, (COALESCE(t.total_weight,0)*100)/(t3.weight) as per_target , admin.*, t.total_weight , t.total_amount , t1.total_weight as pi_total_weight , t1.total_amount as pi_total_amount  from admin left join (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  where booking_booking.status <> 3 and   vendors.state_id='.$state_id.' '.$condition.'  GROUP by booking_booking.admin_id ) t on t.admin_id = admin.id

			left join ( select booking_booking.admin_id, vendors.state_id, SUM(pi_history.total_weight_pi) as total_weight, SUM( pi_history.pi_amount) as total_amount from pi_history LEFT JOIN `booking_booking` ON `booking_booking`.`id`=`pi_history`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3 and pi_history.status = 0 and vendors.state_id='.$state_id.' '.$condition1.' GROUP by booking_booking.admin_id ) t1 on t1.admin_id = admin.id
			Left join  (select * from targets where '.$condition_target.' )  t3 on t3.user_id = t.admin_id
			Left join  ( select booking_booking.admin_id, vendors.state_id, SUM(pi_history.total_weight_pi) as total_dispateched_weight, SUM( pi_history.pi_amount) as total_dispatched_amount from pi_history LEFT JOIN `booking_booking` ON `booking_booking`.`id`=`pi_history`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and  booking_booking.status <> 3 and pi_history.status = 0 and vendors.state_id='.$state_id.' '.$condition1.' GROUP by booking_booking.admin_id )  t4 on t4.admin_id =  admin.id


left join states on states.id = '.$state_id.'
left join (SELECT 
CASE 
WHEN address LIKE "%andaman%" THEN "Andaman and Nicobar Islands" 
WHEN address LIKE "%andhra%" THEN "Andhra Pradesh" 
WHEN address LIKE "%arunachal%" THEN "Arunachal Pradesh" 
WHEN address LIKE "%assam%" THEN "Assam" 
WHEN address LIKE "%bihar%" THEN "Bihar"
WHEN address LIKE "%chandigarh%" THEN "Chandigarh" 
WHEN address LIKE "%chattisgarh%" THEN "Chattisgarh" 
WHEN address LIKE "%dadra%" THEN "Dadra and Nagar Haveli" 
WHEN address LIKE "%daman%" THEN "Daman and Diu" 
WHEN address LIKE "%delhi%" THEN "Delhi" 
WHEN address LIKE "%goa%" THEN "Goa" 
WHEN address LIKE "%Gujrat%" THEN "Gujrat"
WHEN address LIKE "%haryana%" THEN "Haryana" 
WHEN address LIKE "%himachal%" THEN "Himachal Pradesh" 
WHEN address LIKE "%jammu%" THEN "Jammu and Kashmir" 
WHEN address LIKE "%jharkhand%" THEN "Jharkhand"
WHEN address LIKE "%karnataka%" THEN "Karnataka" 
WHEN address LIKE "%kerala%" THEN "Kerala" 
WHEN address LIKE "%lakshadweep%" THEN "Lakshadweep" 
WHEN address LIKE "%madhya%" THEN "Madhya Pradesh" 
WHEN address LIKE "%maharashtra%" THEN "Maharashtra" 
WHEN address LIKE "%manipur%" THEN "Manipur" 
WHEN address LIKE "%meghalaya%" THEN "Meghalaya" 
WHEN address LIKE "%mizoram%" THEN "Mizoram" 
WHEN address LIKE "%nagaland%" THEN "Nagaland" 
WHEN address LIKE "%odisha%" THEN "Odisha" 
WHEN address LIKE "%poducherry%" THEN "Poducherry" 
WHEN address LIKE "%punjab%" THEN "Punjab" 
WHEN address LIKE "%rajasthan%" THEN "Rajasthan" 
WHEN address LIKE "%sikkim%" THEN "Sikkim" 
WHEN address LIKE "%tamil%" THEN "Tamil Nadu" 
WHEN address LIKE "%telangana%" THEN "Telangana" 
WHEN address LIKE "%tripura%" THEN "Tripura" 
WHEN address LIKE "%uttar pradesh%" THEN "Uttar Pradesh" 
WHEN address LIKE "%uttarakhand%" THEN "Uttarakhand" 
WHEN address LIKE "%west%" THEN "West Bengal" 
WHEN address LIKE "%ladakh%" THEN "Ladakh"  


END  As state , 
COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like "%###%" '.$visit_condition.' 
GROUP BY user_id, state ) visits on visits.user_id = t3.user_id and states.name = visits.state 

		 where (role = 6 OR role=1 ) '.$employee_condition.' ORDER BY `name` ASC'; 
		$query = $this->db->query($data_query);    
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}

	function GetSecondaryMakers2($state_id,$conditions){  
		//echo "<pre>"; print_r($conditions); die;

		$admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $loggedin_id = $admin_info['id']; 

		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;

		$condition = "and YEAR( secondary_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( secondary_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		$employee_condition = "";
		$employee_condition_target  = "";
		if(isset($conditions) && !empty($conditions))
		{
			$month = $conditions['year'].'-'.$conditions['month'];
			$condition = "and ( secondary_booking.created_at like '%$month%' )";
			$employee = $conditions['employee'];
			$employee_sec  = "";

			if(isset($conditions['employee_sec']) && !empty($conditions['employee_sec']))
				$employee_sec  = $conditions['employee_sec'];
			if($employee)
			{
				if($employee_sec)
					$condition .= "and ( secondary_booking.admin_id IN ($employee_sec) )";
				else
					$condition .= "and ( secondary_booking.admin_id IN ($employee) )";
				$employee_condition = "and ( admin.id = $employee OR admin.team_lead_id = $employee)";

				

				$employee_condition_target  = "and ( targets.user_id = $employee )";
			}
			$target_month_year =  $conditions['month'].'-'.$conditions['year'];
			
		}
		if($conditions['report_type']==1 && $admin_role==5)
		{
			$employee_condition .= " and admin.performance_viewer = $loggedin_id";
		}
		

		if($conditions['temalead']!='' && $admin_role==4)
		{
			$employee_condition .= " and admin.performance_viewer = ".$conditions['temalead'];
		}

		//$data_query = 'select admin.*, t.total_weight , t.total_amount  from admin left join (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking_skus.weight) as total_weight, SUM(`secondary_booking_skus`.`rate`*`secondary_booking_skus`.`quantity`*`products`.`packing_items_qty`) as total_amount FROM (`secondary_booking_skus`) LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`secondary_booking_skus`.`secondary_booking_id` LEFT JOIN `products` ON `products`.`id`=`secondary_booking_skus`.`product_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where vendors.state_id='.$state_id.'  '.$condition.' GROUP by secondary_booking.admin_id ) t on t.admin_id = admin.id where (find_in_set('.$state_id.',admin.state_id) or admin.state_id = "" or admin.state_id IS NULL) and role = 6 '.$employee_condition.' ORDER BY `name` ASC';

		$condition_target = "  targets.targent_month_year = '$target_month_year' and (find_in_set($state_id,targets.state_ids) OR targets.state_ids is NUll OR  targets.state_ids = '' ) ".$employee_condition_target;

		$data_query = 'select (COALESCE(t.total_weight,0)*100)/(t3.weight) as per_target , COALESCE(t1.total_weight, 0) as pi_total_weight , t1.total_amount as pi_total_amount, admin.*, t.total_weight , t.total_amount  from admin left join (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking_skus.weight) as total_weight, SUM(`secondary_booking_skus`.`rate`*`secondary_booking_skus`.`quantity`*`products`.`packing_items_qty`) as total_amount FROM (`secondary_booking_skus`) LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`secondary_booking_skus`.`secondary_booking_id` LEFT JOIN `products` ON `products`.`id`=`secondary_booking_skus`.`product_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where vendors.state_id='.$state_id.'  '.$condition.' GROUP by secondary_booking.admin_id ) t on t.admin_id = admin.id

		left join ( select secondary_booking.admin_id, vendors.state_id, SUM(pi_history_secondary_booking.total_weight_pi) as total_weight, SUM( pi_history_secondary_booking.pi_amount) as total_amount from pi_history_secondary_booking LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`pi_history_secondary_booking`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where  pi_history_secondary_booking.status = 0 and vendors.state_id='.$state_id.' '.$condition.' GROUP by secondary_booking.admin_id ) t1 on t1.admin_id = admin.id

		Left join  (select * from targets where '.$condition_target.' )  t3 on t3.user_id = t.admin_id

		 where  (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC';



		$query = $this->db->query($data_query);    
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}


	function GetAllMakersSecondary($condition= array()){  
		$this->db->select('*');
	    $this->db->from('admin'); 
	    $this->db->where("(role = 1 OR role = 6)");  
	    //$this->db->or_where('role' ,6);  
	    if($condition)
	    	$this->db->where($condition);  
	    $this->db->order_by('name','ASC');
	    $query = $this->db->get(); 
	   	//echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}



	function GetSecondaryMakersbyadminid($condition){  
		$this->db->select('GROUP_CONCAT(id) as secondary_makers');
	    $this->db->from('admin');	     
	    $this->db->where($condition); 
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->row_array(); 
	    return $row; 
	}


	function GetSecondaryMakersbyadminid2($id){  
		$this->db->select('GROUP_CONCAT(id) as secondary_makers');
	    $this->db->from('admin');	     
	    $this->db->where('id',$id); 
	    $query = $this->db->get(); 
	    //echo $this->db->last_query(); die;
	    $row = $query->row_array(); 
	    return $row; 
	}

	public function updateStatus($id, $status,$table) {
        $status = ($status == '1') ? '0' : '1';

        if($status==0)
        {
        	$this->db->set('deactivation_date', date('Y-m-d'));
        }
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        if ($this->db->update($table)) {
            return true;
        } else {
            return false;
        }
    }




    function GetBookingsummary_date($state_id,$conditions){ 

		$admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $loggedin_id = $admin_info['id']; 


 	 	 //echo "<pre>"; print_r($conditions); die;
		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;
		$visit_condition = "";
		$month = $conditions['year'].'-'.$conditions['month'];	
		$dispetched_month = $conditions['month'].'-'.$conditions['year'];	

		$employee = $conditions['employee'];
		$condition_emp = "";
		$employee_condition = "";
		$employee_condition1 = "";
		$condition_production_unit = "";
		if(isset($conditions) && !empty($conditions))
		{

			
			if(isset($conditions['production_unit']) && !empty($conditions['production_unit']))
			{
				$condition_production_unit  = " and booking_booking.production_unit = '".$conditions['production_unit']."'"; 
			}

			//$visit_condition = " and tracking_date like '%".$month."%'";  
			$visit_condition = " and ( DATE_FORMAT(`tracking_date`,'%Y-%m-%d') BETWEEN '". date('Y-m-d',strtotime($conditions['booking_date_from'])) ."' AND '". date('Y-m-d',strtotime($conditions['booking_date_to']))."'  ) ";  
			$target_month_year =  $conditions['month'].'-'.$conditions['year'];

			$target_cond = " and targent_month_year like '%".$target_month_year."%'";  
		}
		if($employee)
		{
			$employee_condition = "and ( admin.id = $employee )";
			$employee_condition1 = "and ( admin.id = $employee )";
			$condition_emp = "and ( booking_booking.admin_id = $employee )"; 
		}
		else
		{
			$employee_condition = "and ( (find_in_set($state_id,admin.state_id) OR admin.state_id is NUll OR  admin.state_id = '')  OR booking_booking.state_id=$state_id )  ";
			$employee_condition1 = "and ( (find_in_set($state_id,admin.state_id) OR admin.state_id is NUll OR  admin.state_id = '')  OR admin_sku.state_id=$state_id )  ";
			if($conditions['report_type']==1 && $admin_role==5)
			{
				$employee_condition .= " and admin.performance_viewer = $loggedin_id";
				$employee_condition1 .= " and admin.performance_viewer = $loggedin_id";
			}

			if($conditions['temalead']!='' && $admin_role==4)
			{
				$employee_condition .= " and admin.performance_viewer = ".$conditions['temalead'];
				$employee_condition1 .= " and admin.performance_viewer = ".$conditions['temalead'];
			}



			
			 
		}

		$booking_date_range_condition = "( DATE_FORMAT(STR_TO_DATE(`booking_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";

		$data_query_booking = 'SELECT (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit , states.name as state_name, (COALESCE(booking_booking.total_weight,0)*100)/(targets.weight) as per_target , booking_booking.state_id, booking_booking.total_weight, booking_booking.total_amount, admin.* from admin left join (SELECT booking_booking.brand_id, booking_booking.admin_id, brands.name as brand_name, vendors.state_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  where vendors.state_id = '.$state_id.' and  booking_booking.status <> 3 and vendors.state_id='.$state_id.' and ( '.$booking_date_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.' GROUP by booking_booking.admin_id) booking_booking on booking_booking.admin_id = admin.id left join  targets on targets.user_id = admin.id and targets.state_ids = '.$state_id.$target_cond.'  left join states on states.id = '.$state_id.'

			left join (SELECT 
			CASE 
			WHEN address LIKE "%andaman%" THEN "Andaman and Nicobar Islands" 
			WHEN address LIKE "%andhra%" THEN "Andhra Pradesh" 
			WHEN address LIKE "%arunachal%" THEN "Arunachal Pradesh" 
			WHEN address LIKE "%assam%" THEN "Assam" 
			WHEN address LIKE "%bihar%" THEN "Bihar"
			WHEN address LIKE "%chandigarh%" THEN "Chandigarh" 
			WHEN address LIKE "%chattisgarh%" THEN "Chattisgarh" 
			WHEN address LIKE "%dadra%" THEN "Dadra and Nagar Haveli" 
			WHEN address LIKE "%daman%" THEN "Daman and Diu" 
			WHEN address LIKE "%delhi%" THEN "Delhi" 
			WHEN address LIKE "%goa%" THEN "Goa" 
			WHEN address LIKE "%Gujrat%" THEN "Gujrat"
			WHEN address LIKE "%haryana%" THEN "Haryana" 
			WHEN address LIKE "%himachal%" THEN "Himachal Pradesh" 
			WHEN address LIKE "%jammu%" THEN "Jammu and Kashmir" 
			WHEN address LIKE "%jharkhand%" THEN "Jharkhand"
			WHEN address LIKE "%karnataka%" THEN "Karnataka" 
			WHEN address LIKE "%kerala%" THEN "Kerala" 
			WHEN address LIKE "%lakshadweep%" THEN "Lakshadweep" 
			WHEN address LIKE "%madhya%" THEN "Madhya Pradesh" 
			WHEN address LIKE "%maharashtra%" THEN "Maharashtra" 
			WHEN address LIKE "%manipur%" THEN "Manipur" 
			WHEN address LIKE "%meghalaya%" THEN "Meghalaya" 
			WHEN address LIKE "%mizoram%" THEN "Mizoram" 
			WHEN address LIKE "%nagaland%" THEN "Nagaland" 
			WHEN address LIKE "%odisha%" THEN "Odisha" 
			WHEN address LIKE "%poducherry%" THEN "Poducherry" 
			WHEN address LIKE "%punjab%" THEN "Punjab" 
			WHEN address LIKE "%rajasthan%" THEN "Rajasthan" 
			WHEN address LIKE "%sikkim%" THEN "Sikkim" 
			WHEN address LIKE "%tamil%" THEN "Tamil Nadu" 
			WHEN address LIKE "%telangana%" THEN "Telangana" 
			WHEN address LIKE "%tripura%" THEN "Tripura" 
			WHEN address LIKE "%uttar pradesh%" THEN "Uttar Pradesh" 
			WHEN address LIKE "%uttarakhand%" THEN "Uttarakhand" 
			WHEN address LIKE "%west%" THEN "West Bengal" 
			WHEN address LIKE "%ladakh%" THEN "Ladakh"  


			END  As state , 
			COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like "%###%" '.$visit_condition.' 
			GROUP BY user_id, state ) visits on visits.user_id = admin.id and states.name = visits.state 

		 where (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC'; 
	
 
		$query_booking = $this->db->query($data_query_booking);     
	    $booking_response = $query_booking->result_array();  


	    //$data_query_pi = 'SELECT  booking_booking.state_id,booking_booking.brand_id, booking_booking.brand_name,  booking_booking.total_weight_pi,booking_booking.total_amount_pi, admin.* from admin left join (select vendors.state_id,booking_booking.admin_id,booking_booking.brand_id, brands.name as brand_name,  SUM(pi_history.total_weight_pi) as total_weight_pi, SUM( pi_history.pi_amount) as total_amount_pi from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`)  LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`   where booking_booking.status <> 3 and pi_history.status = 0 and vendors.state_id='.$state_id.' and ( pi_history .created_at like "%'.$month.'%"  ) '.$condition_emp.'  GROUP by booking_booking.admin_id) booking_booking on booking_booking.admin_id = admin.id where (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC';

	    $data_query_pi_range_condition = "( DATE_FORMAT(STR_TO_DATE(`pi_history`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";

	    $data_query_pi = 'SELECT  admin_sku.state_id,  SUM(admin_sku.total_weight_pi) as total_weight_pi,SUM(admin_sku.total_amount_pi) as total_amount_pi, admin.* from admin left join (select vendors.state_id,booking_booking.admin_id,  SUM(pi_sku_history.weight) as total_weight_pi, SUM(pi_sku_history.amount) as total_amount_pi from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON  `booking_booking`.`id` = `pi_sku_history`.`booking_id`    LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`   where booking_booking.status <> 3 and pi_history.status = 0 and vendors.state_id='.$state_id.' and ( '.$data_query_pi_range_condition.' ) '.$condition_emp.' '.$condition_production_unit.'  GROUP by booking_booking.admin_id) admin_sku on admin_sku.admin_id = admin.id where (role = 6 OR role=1 )'.$employee_condition1.'   Group by admin.id ORDER BY `name` ASC';
		$query_pi = $this->db->query($data_query_pi);    
		//echo $data_query_pi; die; 
	    $pi_response = $query_pi->result_array();  
	    
	    //$data_query_dispetch = ' SELECT booking_booking.state_id,booking_booking.brand_id, booking_booking.brand_name,booking_booking.total_dispateched_weight, booking_booking.total_dispatched_amount, admin.* from admin left join (select  vendors.state_id, booking_booking.admin_id,booking_booking.brand_id, brands.name as brand_name, SUM(pi_history.total_weight_pi) as total_dispateched_weight, SUM( pi_history.pi_amount) as total_dispatched_amount from pi_history LEFT JOIN `booking_booking` ON find_in_set( `booking_booking`.`id`, `pi_history`.`booking_id`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` LEFT JOIN `brands` ON `brands`.`id`=`booking_booking`.`brand_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> "" ) and booking_booking.status <> 3 and pi_history.status =0 and vendors.state_id='.$state_id.' and ( pi_history.dispatch_date like "%'.$dispetched_month.'%" ) '.$condition_emp.' GROUP by booking_booking.admin_id) booking_booking on booking_booking.admin_id = admin.id where (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC';	

	    $data_query_dispetch = " SELECT admin_sku.state_id,SUM(admin_sku.total_dispateched_weight) as total_dispateched_weight, SUM(admin_sku.total_dispatched_amount) as total_dispatched_amount, admin.* from admin left join (select vendors.state_id,pi_history.id as pid, booking_booking.admin_id, SUM(pi_sku_history.weight) as total_dispateched_weight, SUM(pi_sku_history.amount) as total_dispatched_amount from pi_history LEFT JOIN pi_sku_history ON pi_history.id = pi_sku_history.pi_number LEFT JOIN `booking_booking` ON  `booking_booking`.`id` = `pi_sku_history`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id`  where ( pi_history.dispatch_date IS NOT NULL and pi_history.dispatch_date <> '' ) and booking_booking.status <> 3 ".$condition_production_unit." and pi_history.status =0 and vendors.state_id=".$state_id." and ( 
	    	( DATE_FORMAT(STR_TO_DATE(`pi_history`.`dispatch_date`,'%d-%m-%Y'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )".$condition_emp.") GROUP by pid,booking_booking.admin_id) admin_sku on admin_sku.admin_id = admin.id where (admin.role = 6 OR admin.role=1 ) ".$employee_condition1."  Group by admin.id ORDER BY `name` ASC"; 

		$query_dispetch = $this->db->query($data_query_dispetch);   
		//echo $data_query_dispetch; die;   

	    $pi_dispetch = $query_dispetch->result_array();  
	   
	   	

	    $unset_array = array();
	    

	    $reponse = array();
	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['id'], array_column($pi_response, 'id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['pi_total_weight'] =   0 ;
	    			$booking_response[$key]['pi_total_amount'] =   0 ;  
	    		}
	    		else
	    		{
	    			 
	    			$booking_response[$key]['pi_total_weight'] = $pi_response[$key_exists]['total_weight_pi'];
	    			$booking_response[$key]['pi_total_amount'] = $pi_response[$key_exists]['total_amount_pi'];	 
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
	    
	    if($pi_response)
	    {
	    	foreach ($pi_response as $key => $value) {
	    		$pi_response[$key]['pi_total_weight'] =   $value['total_weight_pi']; ;
	    		$pi_response[$key]['pi_total_amount'] =   $value['total_amount_pi']; ; ;
	    		$pi_response[$key]['total_weight'] =   0 ;
	    		$pi_response[$key]['total_amount'] =   0 ;  
	    		$pi_response[$key]['per_target'] =   0 ;
	    		$pi_response[$key]['per_target_visit'] =   0 ;  
	    	}
	    	$booking_response = array_merge($pi_response,$booking_response);
	    }



	    if($booking_response)
	    { 
	    	$unset_array= array();
	    	foreach ($booking_response as $key => $value) {
	    		$key_exists = array_search($value['id'], array_column($pi_dispetch, 'id'));
	    		if($key_exists===FALSE)
	    		{
	    			$booking_response[$key]['total_dispateched_weight'] =   0 ;
	    			$booking_response[$key]['total_dispatched_amount'] =   0 ;  
	    			$pi_response[$key]['per_target'] =   0 ;
	    			$pi_response[$key]['per_target_visit'] =   0 ; 
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
	   
	    if($pi_dispetch)
	    {
	    	foreach ($pi_dispetch as $key => $value) {
	    		$pi_dispetch[$key]['total_weight'] =   0 ;
	    		$pi_dispetch[$key]['total_amount'] =   0 ; 
	    		$pi_dispetch[$key]['pi_total_weight'] =   0 ;
	    		$pi_dispetch[$key]['pi_total_amount'] =   0 ;  
	    		$pi_response[$key]['per_target'] =   0 ;
	    		$pi_response[$key]['per_target_visit'] =   0 ; 
	    	}
	    	$booking_response = array_merge($pi_dispetch,$booking_response);
	    } 
	    if($state_id==29)
		{
			//echo "<pre>"; print_r($booking_response); die;
		}
	     //echo "<pre>"; print_r($booking_response); die;
	   // echo "<pre>";print_r($booking_response); die;
	    //echo "<pre>"; print_r($booking_response);  print_r($pi_response);   print_r($pi_dispetch); die;
	   // echo "<pre>";print_r($booking_response); die;
	    return $booking_response; 
	}



	function GetSecondaryMakers_date($state_id,$conditions){  
		//echo "<pre>"; print_r($conditions); die;

		$admin_info = $this->session->userdata('admin');  
        $admin_role = $admin_info['role'];
        $loggedin_id = $admin_info['id']; 

		$current_year = date("Y");
		$current_month = sprintf("%02d", date("m"));
		$target_month_year = $current_month.'-'.$current_year;

		$condition = "and YEAR( secondary_booking.created_at) = YEAR(CURRENT_DATE - INTERVAL 0 MONTH) AND MONTH( secondary_booking.created_at) = MONTH(CURRENT_DATE - INTERVAL 0 MONTH)";
		$employee_condition = "";
		$employee_condition_target  = "";
		if(isset($conditions) && !empty($conditions))
		{
			$month = $conditions['year'].'-'.$conditions['month'];
			$condition = "and ( secondary_booking.created_at like '%$month%' )";

			$condition = " and ( DATE_FORMAT(STR_TO_DATE(`secondary_booking`.`created_at`,'%Y-%m-%d'),'%Y-%m-%d') BETWEEN '".date('Y-m-d',strtotime($conditions['booking_date_from']))."' AND '".date('Y-m-d',strtotime($conditions['booking_date_to']))."' )";



			$employee = $conditions['employee'];
			$employee_sec  = "";

			if(isset($conditions['employee_sec']) && !empty($conditions['employee_sec']))
				$employee_sec  = $conditions['employee_sec'];
			if($employee)
			{
				if($employee_sec)
					$condition .= "and ( secondary_booking.admin_id IN ($employee_sec) )";
				else
					$condition .= "and ( secondary_booking.admin_id IN ($employee) )";
				$employee_condition = "and ( admin.id = $employee OR admin.team_lead_id = $employee)";

				

				$employee_condition_target  = "and ( targets.user_id = $employee )";
			}
			$target_month_year =  $conditions['month'].'-'.$conditions['year'];
			
		}
		if($conditions['report_type']==1 && $admin_role==5)
		{
			$employee_condition .= " and admin.performance_viewer = $loggedin_id";
		}
		

		if($conditions['temalead']!='' && $admin_role==4)
		{
			$employee_condition .= " and admin.performance_viewer = ".$conditions['temalead'];
		}

		//$data_query = 'select admin.*, t.total_weight , t.total_amount  from admin left join (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking_skus.weight) as total_weight, SUM(`secondary_booking_skus`.`rate`*`secondary_booking_skus`.`quantity`*`products`.`packing_items_qty`) as total_amount FROM (`secondary_booking_skus`) LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`secondary_booking_skus`.`secondary_booking_id` LEFT JOIN `products` ON `products`.`id`=`secondary_booking_skus`.`product_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where vendors.state_id='.$state_id.'  '.$condition.' GROUP by secondary_booking.admin_id ) t on t.admin_id = admin.id where (find_in_set('.$state_id.',admin.state_id) or admin.state_id = "" or admin.state_id IS NULL) and role = 6 '.$employee_condition.' ORDER BY `name` ASC';

		$condition_target = "  targets.targent_month_year = '$target_month_year' and (find_in_set($state_id,targets.state_ids) OR targets.state_ids is NUll OR  targets.state_ids = '' ) ".$employee_condition_target;

		$data_query = 'select (COALESCE(t.total_weight,0)*100)/(t3.weight) as per_target , COALESCE(t1.total_weight, 0) as pi_total_weight , t1.total_amount as pi_total_amount, admin.*, t.total_weight , t.total_amount  from admin left join (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking_skus.weight) as total_weight, SUM(`secondary_booking_skus`.`rate`*`secondary_booking_skus`.`quantity`*`products`.`packing_items_qty`) as total_amount FROM (`secondary_booking_skus`) LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`secondary_booking_skus`.`secondary_booking_id` LEFT JOIN `products` ON `products`.`id`=`secondary_booking_skus`.`product_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where vendors.state_id='.$state_id.'  '.$condition.' GROUP by secondary_booking.admin_id ) t on t.admin_id = admin.id

		left join ( select secondary_booking.admin_id, vendors.state_id, SUM(pi_history_secondary_booking.total_weight_pi) as total_weight, SUM( pi_history_secondary_booking.pi_amount) as total_amount from pi_history_secondary_booking LEFT JOIN `secondary_booking` ON `secondary_booking`.`id`=`pi_history_secondary_booking`.`booking_id` LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where  pi_history_secondary_booking.status = 0 and vendors.state_id='.$state_id.' '.$condition.' GROUP by secondary_booking.admin_id ) t1 on t1.admin_id = admin.id

		Left join  (select * from targets where '.$condition_target.' )  t3 on t3.user_id = t.admin_id

		 where  (role = 6 OR role=1 )'.$employee_condition.' ORDER BY `name` ASC';



		$query = $this->db->query($data_query);    
	    //echo $this->db->last_query(); die;
	    $row = $query->result_array(); 
	    return $row; 
	}
}