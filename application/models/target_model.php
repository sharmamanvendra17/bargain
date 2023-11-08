<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target_model extends CI_Model {
	
	function __construct()
	{
		// Call the Model constructor
	    parent::__construct();
		$this->load->database();
	} 
	function adddistance($insertdata)
	{ 
		return $this->db->insert_batch('employee_distances',$insertdata);
	}
	function addtracking($insertdata)
	{ 
		return $this->db->insert_batch('employee_locations',$insertdata);
	}
	function Add_Target($insertdata)
	{ 
		return $this->db->insert_batch('targets',$insertdata);
	}

  function Add_Data($insertdata)
  { 
    return $this->db->insert('targets',$insertdata);
  }

  public function Check_Target($condition) {   
    $this->db->select('targets.id');
    $this->db->where($condition); 
    $this->db->from('targets'); 
    $query = $this->db->get();  
    //echo $this->db->last_query(); die;
    return $query->row_array();
  }

  public function gettargetuser($year,$userid) {   
        $this->db->select('targets.*'); 

        $this->db->select('GROUP_CONCAT(states.name) as state_name'); 

        $this->db->select('admin.name'); 
        $this->db->select('admin.username as email'); 
        $this->db->like('targets.targent_month_year',$year); 
        $this->db->like('targets.user_id',$userid); 
        $this->db->from('targets'); 
        $this->db->join("admin","admin.id=targets.user_id","LEFT"); 
        $this->db->join('states', 'FIND_IN_SET ( states.id , targets.state_ids ) ' ,'LEFT');
        $this->db->order_by('targets.id','DESC');
        $this->db->group_by('targets.id');
        $this->db->group_by('states.id');
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        return $query->result_array();
    }

	public function gettarget($condition) {   
        $this->db->select('targets.*'); 

        $this->db->select('GROUP_CONCAT(states.name) as state_name'); 

        $this->db->select('admin.name'); 
        $this->db->select('admin.username as email'); 
        $this->db->where($condition); 
        $this->db->from('targets'); 
        $this->db->join("admin","admin.id=targets.user_id","LEFT"); 
        $this->db->join('states', 'FIND_IN_SET ( states.id , targets.state_ids ) ' ,'LEFT');
        $this->db->order_by('targets.month','ASC');
        $this->db->group_by('targets.id');
        //$this->db->group_by('states.id');
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        return $query->result_array();
    }	


    public function targetinfo($condition) {   
        $this->db->select('targets.*');  
        $this->db->from('targets');  
        $this->db->where($condition);  
        $query = $this->db->get(); 
        //echo $this->db->last_query(); die;
        return $query->row_array();
    }


    function UpdateTarget($update_data,$condition){
      $this->db->where($condition);
      return $this->db->update('targets', $update_data);
      //echo $this->db->last_query(); die;
    }



    function countEmployeetargets($condition){

      $admin_info = $this->session->userdata('admin');  
      $admin_role = $admin_info['role'];
      $admin_id = $admin_info['id'];   
      $role = $this->session->userdata('admin')['role'];
      $logged_in_id = $this->session->userdata('admin')['id'];  
      $logged_role = $role;  

      $outer_condition = "";
      $inner_condition = "";
      $visit_condition = "";
      $outer_condition_array = array();
      $inner_condition_array = array();
      $visit_condition_array = array();

      $team_lead_condtition = "";
      $groupby =  ",targets.state_ids ";
      if(isset($condition['report_type']) && !empty($condition['report_type']))
      { 
        if($condition['report_type']==1)
        {

          $groupby =  " ";
        }
      }
      if(isset($_POST['temalead']) && !empty($_POST['temalead']))
      {
        $team_lead_query = "select GROUP_CONCAT(admin.id) as team_makers from admin where admin.role = 1 and  admin.performance_viewer=".$_POST['temalead'];
        $team_lead_query_exec = $this->db->query($team_lead_query);    
        //echo $this->db->last_query(); die;
        $team_lead_query_res = $team_lead_query_exec->row_array();
        //echo "<pre>"; print_r($team_lead_query_res); die;
        $team_lead_condtition = " having targets.user_id IN ( ".$team_lead_query_res['team_makers'].") ";
        //echo "<pre>"; print_r($condition); die;
      }
      if(isset($condition['report_type']) && !empty($condition['report_type']))
      {
         
        if(isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')
        {
          if(isset($condition))
          {
            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "booking_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }


          } 
           //echo "<pre>"; print_r($inner_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  " and ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 


          if(isset($condition['employee']) && !empty($condition['employee']))
          {
            $data_query = "SELECT admin.role,   (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, targets.state_ids, SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 1
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$ordreby;
          }
          else
          {

            $data_query = "SELECT admin.role,   (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, targets.state_ids, SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."   group by booking_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$team_lead_condtition.$ordreby;         
          }
        }
        else
        {
          //echo "<pre>"; print_r($condition); die;
          if(isset($condition))
          {

            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            else
            {  
              if($logged_role==1)
              {
                $outer_condition_array[] = "admin.team_lead_id='".$logged_in_id."'";
              }            
            }

            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "secondary_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }
          } 
          

           //echo "<pre>"; print_r($outer_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  "  ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 
          //echo "<pre>"; print_r($outer_condition); die;

          if(isset($condition['employee']) && !empty($condition['employee']))
          {
            $data_query = "SELECT  admin.role, (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, targets.state_ids, SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where   ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 6
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 6 GROUP by targets.user_id ".$team_lead_condtition.$ordreby;
          }
          else
          {

            $data_query = "SELECT admin.role,   (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, targets.state_ids, SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where ".$inner_condition."   group by secondary_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 6 GROUP by targets.user_id ".$team_lead_condtition.$ordreby;         
          } 
        }
      }
      else
      {
        if(isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')
        {
          if(isset($condition))
          {
            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "booking_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }


          } 
           //echo "<pre>"; print_r($inner_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  " and ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 


          if(isset($condition['employee']) && !empty($condition['employee']))
          {
            $data_query = "SELECT  admin.role, (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 1
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$team_lead_condtition.$ordreby;
          }
          else
          {

            $data_query = "SELECT admin.role,   (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."   group by booking_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$team_lead_condtition.$ordreby;         
          }
        }
        else
        {
          //echo "<pre>"; print_r($condition); die;
          if(isset($condition))
          {

            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            else
            {  
              if($logged_role==1)
              {
                $outer_condition_array[] = "admin.team_lead_id='".$logged_in_id."'";
              }            
            }

            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "secondary_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }
          } 
          

           //echo "<pre>"; print_r($outer_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  "  ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 
          //echo "<pre>"; print_r($outer_condition); die;

          if(isset($condition['employee']) && !empty($condition['employee']))
          {
            $data_query = "SELECT  admin.role, (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where   ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 6
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 6 GROUP by targets.user_id,targets.state_ids ".$team_lead_condtition.$ordreby;
          }
          else
          {

            $data_query = "SELECT admin.role,   (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where ".$inner_condition."   group by secondary_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 6 GROUP by targets.user_id,targets.state_ids ".$team_lead_condtition.$ordreby;         
          } 
        }
      }
      // echo $data_query; die;
      $query = $this->db->query($data_query);    
      //echo $this->db->last_query(); die;
      $row = $query->num_rows(); 
      return $row; 
    }
    function getEmployeetargets($condition,$perPage=20, $pageNo=1){
      $startFromRecord = ($pageNo) ? $perPage * ($pageNo - 1) : $pageNo;

      $admin_info = $this->session->userdata('admin');  
      $admin_role = $admin_info['role'];
      $admin_id = $admin_info['id'];   
      $role = $this->session->userdata('admin')['role'];
      $logged_in_id = $this->session->userdata('admin')['id'];  
      $logged_role = $role;  

      $outer_condition = "";
      $inner_condition = "";
      $visit_condition = "";
      $outer_condition_array = array();
      $inner_condition_array = array();
      $visit_condition_array = array();
      $team_lead_condtition = "";
      $groupby =  ",targets.state_ids ";
      if(isset($condition['report_type']) && !empty($condition['report_type']))
      { 
        if($condition['report_type']==1)
        {

          $groupby =  " ";
        }
      }
      if(isset($_POST['temalead']) && !empty($_POST['temalead']))
      {
        $team_lead_query = "select GROUP_CONCAT(admin.id) as team_makers from admin where admin.role = 1 and  admin.performance_viewer=".$_POST['temalead'];
        $team_lead_query_exec = $this->db->query($team_lead_query);    
        //echo $this->db->last_query(); die;
        $team_lead_query_res = $team_lead_query_exec->row_array();
        //echo "<pre>"; print_r($team_lead_query_res); die;
        $team_lead_condtition = " having targets.user_id IN ( ".$team_lead_query_res['team_makers'].") ";
        //echo "<pre>"; print_r($condition); die;
      }
      if(isset($condition['report_type']) && !empty($condition['report_type']))
      {
         
        if(isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')
        {
          if(isset($condition))
          {
            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "booking_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }


          } 
           //echo "<pre>"; print_r($inner_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  " and ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 


          if(isset($condition['employee']) && !empty($condition['employee']))
          {
            $data_query = "SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role,   (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, targets.state_ids, SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 1
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$ordreby." limit ".$startFromRecord.",".$perPage;
          }
          else
          {

            $data_query = "SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role,   (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, GROUP_CONCAT(targets.state_ids) as state_ids , SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."   group by booking_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$team_lead_condtition.$ordreby." limit ".$startFromRecord.",".$perPage;         
          }
        }
        else
        {
          //echo "<pre>"; print_r($condition); die;
          if(isset($condition))
          {

            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            else
            {  
              if($logged_role==1)
              {
                $outer_condition_array[] = "admin.team_lead_id='".$logged_in_id."'";
              }            
            }

            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "secondary_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }
          } 
          

           //echo "<pre>"; print_r($outer_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  "  ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 
          //echo "<pre>"; print_r($outer_condition); die;

          if(isset($condition['employee']) && !empty($condition['employee']))
          {
            $data_query = "SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role, (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, targets.state_ids, SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where   ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 6
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 6 GROUP by targets.user_id ".$team_lead_condtition.$ordreby." limit ".$startFromRecord.",".$perPage;
          }
          else
          {

            $data_query = "SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role,   (COALESCE(SUM(visits.visit),0)*100)/(SUM(targets.distributor_visits)) as per_target_visit ,  COALESCE(SUM(visits.visit),0) as total_visited, GROUP_CONCAT(states.name) as state_name,admin.name as employee_name,admin.mobile, (COALESCE(SUM(booking.total_weight),0)*100)/(SUM(targets.weight)) as per_target ,  SUM(booking.total_weight) as bargain_total_weight, targets.state_ids, SUM(targets.weight)  as total_target_weight, SUM(targets.distributor_visits) as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where ".$inner_condition."   group by secondary_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 6 GROUP by targets.user_id ".$team_lead_condtition.$ordreby." limit ".$startFromRecord.",".$perPage;         
          } 
        }
      }
      else
      {
        if(isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers')
        {
          if(isset($condition))
          {
            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "booking_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }


          } 
           //echo "<pre>"; print_r($inner_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  " and ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 


          if(isset($condition['employee']) && !empty($condition['employee']))
          {
          	$data_query = "SELECT  TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role, (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 1
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$team_lead_condtition.$ordreby." limit ".$startFromRecord.",".$perPage;
          }
          else
          {

          	$data_query = "SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role,   (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT booking_booking.admin_id, vendors.state_id,booking_booking.party_id, SUM(booking_booking.total_weight) as total_weight, SUM(`booking_booking`.`rate`*`booking_booking`.`quantity`) as total_amount FROM (`booking_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`booking_booking`.`party_id` where booking_booking.status <> 3  ".$inner_condition."   group by booking_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 1 GROUP by targets.user_id".$groupby.$team_lead_condtition.$ordreby." limit ".$startFromRecord.",".$perPage;      		
        	}
        }
        else
        {
          //echo "<pre>"; print_r($condition); die;
          if(isset($condition))
          {

            if(isset($condition['employee']) && !empty($condition['employee']))
            {
              $outer_condition_array[] = "targets.user_id='".$condition['employee']."'";
              $inner_condition_array[] =  "admin_id=".$condition['employee']; 
            }
            else
            {  
              if($logged_role==1)
              {
                $outer_condition_array[] = "admin.team_lead_id='".$logged_in_id."'";
              }            
            }

            if(isset($condition['month']) && !empty($condition['month']))
              $outer_condition_array[] = "targets.month='".$condition['month']."'";
            if(isset($condition['year']) && !empty($condition['year']))
              $outer_condition_array[] = "targets.year='".$condition['year']."'";
            if(isset($condition['state']) && !empty($condition['state']))
              $outer_condition_array[] = "targets.state_ids='".$condition['state']."'";

            if( (isset($condition['month']) && !empty($condition['month'])) || (isset($condition['year']) && !empty($condition['year'])) )
            {
              $month_year  = $condition['year']."-".$condition['month'];
              $inner_condition_array[] =  "secondary_booking.created_at like '%".$month_year."%'"; 
              $visit_condition_array[] =  "tracking_date like '%".$month_year."%'"; 
            }

            if(isset($condition['sort_by']) && !empty($condition['sort_by']))
            { 
              if($condition['sort_by']=='per_target')
              {

                $ordreby =  "order by ".$condition['sort_by']." DESC";
              }
              else
              {
                $ordreby =  "order by ".$condition['sort_by'].",states.name asc";
              }
              
            }
          } 
          

           //echo "<pre>"; print_r($outer_condition_array); die;
          if(count($outer_condition_array))
            $outer_condition = implode(" and ", $outer_condition_array); 
          if(count($inner_condition_array))
          {
            $inner_condition = implode(" and ", $inner_condition_array); 
            $inner_condition =  "  ".$inner_condition;
          } 

          if(count($visit_condition_array))
          {
            $visit_condition = implode(" and ", $visit_condition_array);  
            $visit_condition =  " and ".$visit_condition;
          } 
          //echo "<pre>"; print_r($outer_condition); die;

          if(isset($condition['employee']) && !empty($condition['employee']))
          {
            $data_query = "SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role, (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where   ".$inner_condition."  group by vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id and   admin.role = 6
             left join (SELECT 
              CASE 
              WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
              WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
              WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
              WHEN address LIKE '%assam%' THEN 'Assam' 
              WHEN address LIKE '%bihar%' THEN 'Bihar'
              WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
              WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
              WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
              WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
              WHEN address LIKE '%delhi%' THEN 'Delhi' 
              WHEN address LIKE '%goa%' THEN 'Goa' 
              WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
              WHEN address LIKE '%haryana%' THEN 'Haryana' 
              WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
              WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
              WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
              WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
              WHEN address LIKE '%kerala%' THEN 'Kerala' 
              WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
              WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
              WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
              WHEN address LIKE '%manipur%' THEN 'Manipur' 
              WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
              WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
              WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
              WHEN address LIKE '%odisha%' THEN 'Odisha' 
              WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
              WHEN address LIKE '%punjab%' THEN 'Punjab' 
              WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
              WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
              WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
              WHEN address LIKE '%telangana%' THEN 'Telangana' 
              WHEN address LIKE '%tripura%' THEN 'Tripura' 
              WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
              WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
              WHEN address LIKE '%west%' THEN 'West Bengal' 
              WHEN address LIKE '%ladakh%' THEN 'Ladakh'  


              END  As state , 
              COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
              GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
              where  ".$outer_condition." and role = 6 GROUP by targets.user_id,targets.state_ids ".$team_lead_condtition.$ordreby." limit ".$startFromRecord.",".$perPage;
          }
          else
          {

            $data_query = "SELECT TIMESTAMPDIFF(MONTH, `joining_date`, now()) as joining_month, admin.joining_date, admin.role,   (COALESCE(visits.visit,0)*100)/(targets.distributor_visits) as per_target_visit ,  COALESCE(visits.visit,0) as total_visited, states.name as state_name,admin.name as employee_name,admin.mobile, (COALESCE(booking.total_weight,0)*100)/(targets.weight) as per_target ,  booking.total_weight as bargain_total_weight, targets.state_ids, targets.weight  as total_target_weight, targets.distributor_visits as total_target_visits,targets.user_id   from targets LEFT JOIN (SELECT secondary_booking.admin_id, vendors.state_id,secondary_booking.supply_from, SUM(secondary_booking.total_weight) as total_weight FROM (`secondary_booking`) LEFT JOIN `vendors` ON `vendors`.`id`=`secondary_booking`.`supply_from` where ".$inner_condition."   group by secondary_booking.admin_id, vendors.state_id  ) booking ON booking.admin_id =  targets.user_id and booking.state_id = targets.state_ids left join states on states.id = targets.state_ids left join admin on admin.id = targets.user_id 
            left join (SELECT 
            CASE 
            WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
            WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
            WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
            WHEN address LIKE '%assam%' THEN 'Assam' 
            WHEN address LIKE '%bihar%' THEN 'Bihar'
            WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
            WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
            WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
            WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
            WHEN address LIKE '%delhi%' THEN 'Delhi' 
            WHEN address LIKE '%goa%' THEN 'Goa' 
            WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
            WHEN address LIKE '%haryana%' THEN 'Haryana' 
            WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
            WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
            WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
            WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
            WHEN address LIKE '%kerala%' THEN 'Kerala' 
            WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
            WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
            WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
            WHEN address LIKE '%manipur%' THEN 'Manipur' 
            WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
            WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
            WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
            WHEN address LIKE '%odisha%' THEN 'Odisha' 
            WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
            WHEN address LIKE '%punjab%' THEN 'Punjab' 
            WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
            WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
            WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
            WHEN address LIKE '%telangana%' THEN 'Telangana' 
            WHEN address LIKE '%tripura%' THEN 'Tripura' 
            WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
            WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
            WHEN address LIKE '%west%' THEN 'West Bengal' 
            WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
            END  As state , 
            COUNT(*) as visit ,user_id FROM employee_locations a WHERE a.address like '%###%'   ".$visit_condition." 
            GROUP BY user_id, state ) visits on visits.user_id = targets.user_id and states.name = visits.state 
            where  ".$outer_condition." and role = 6 GROUP by targets.user_id,targets.state_ids ".$team_lead_condtition.$ordreby." limit ".$startFromRecord.",".$perPage;         
          } 
        }
      }
      // echo $data_query; die;
      $query = $this->db->query($data_query);    
      //echo $this->db->last_query(); die;
      $row = $query->result_array(); 
      return $row; 
    }



  public function getuserlocations($condition) {   
      /*$this->db->select('employee_locations .*');  
      $this->db->from('employee_locations');  

      if(isset($condition['tracking_date']))
      {
        $this->db->like('tracking_date',$condition['tracking_date']);  
        unset($condition['tracking_date']);
      }
      $this->db->where($condition); 
      $this->db->like('address','###','both');  
      $query = $this->db->get(); 
      //echo $this->db->last_query(); die;
      return $query->result_array(); */
      //echo "<pre>"; print_r($condition['tracking_date']); die;
      if( (isset($condition['tracking_date']) && !empty($condition['tracking_date'])) || (isset($condition['tracking_date']) && !empty($condition['tracking_date'])) )
      {             
        $visit_condition_array[] =  "tracking_date like '%".$condition['tracking_date']."%'"; 
      }
      if(isset($condition['user_id']) && !empty($condition['user_id']))
      {
        $visit_condition_array[] =  "user_id = '".$condition['user_id']."'"; 
      }
      if(isset($condition['state']) && !empty($condition['state']))
      {
        $states_array  = explode(' and ', $condition['state']);
        $state = $states_array[0];
        $visit_condition_array[] =  "address like '%".$state."%'"; 
      }
      $visit_condition =  "";
      if(count($visit_condition_array))
        {
          $visit_condition = implode(" and ", $visit_condition_array);  
          $visit_condition =  " and ".$visit_condition;
        } 
      $data_query = "select 
        CASE 
          WHEN address LIKE '%andaman%' THEN 'Andaman and Nicobar Islands' 
          WHEN address LIKE '%andhra%' THEN 'Andhra Pradesh' 
          WHEN address LIKE '%arunachal%' THEN 'Arunachal Pradesh' 
          WHEN address LIKE '%assam%' THEN 'Assam' 
          WHEN address LIKE '%bihar%' THEN 'Bihar'
          WHEN address LIKE '%chandigarh%' THEN 'Chandigarh' 
          WHEN address LIKE '%chattisgarh%' THEN 'Chattisgarh' 
          WHEN address LIKE '%dadra%' THEN 'Dadra and Nagar Haveli' 
          WHEN address LIKE '%daman%' THEN 'Daman and Diu' 
          WHEN address LIKE '%delhi%' THEN 'Delhi' 
          WHEN address LIKE '%goa%' THEN 'Goa' 
          WHEN address LIKE '%Gujrat%' THEN 'Gujrat'
          WHEN address LIKE '%haryana%' THEN 'Haryana' 
          WHEN address LIKE '%himachal%' THEN 'Himachal Pradesh' 
          WHEN address LIKE '%jammu%' THEN 'Jammu and Kashmir' 
          WHEN address LIKE '%jharkhand%' THEN 'Jharkhand'
          WHEN address LIKE '%karnataka%' THEN 'Karnataka' 
          WHEN address LIKE '%kerala%' THEN 'Kerala' 
          WHEN address LIKE '%lakshadweep%' THEN 'Lakshadweep' 
          WHEN address LIKE '%madhya%' THEN 'Madhya Pradesh' 
          WHEN address LIKE '%maharashtra%' THEN 'Maharashtra' 
          WHEN address LIKE '%manipur%' THEN 'Manipur' 
          WHEN address LIKE '%meghalaya%' THEN 'Meghalaya' 
          WHEN address LIKE '%mizoram%' THEN 'Mizoram' 
          WHEN address LIKE '%nagaland%' THEN 'Nagaland' 
          WHEN address LIKE '%odisha%' THEN 'Odisha' 
          WHEN address LIKE '%poducherry%' THEN 'Poducherry' 
          WHEN address LIKE '%punjab%' THEN 'Punjab' 
          WHEN address LIKE '%rajasthan%' THEN 'Rajasthan' 
          WHEN address LIKE '%sikkim%' THEN 'Sikkim' 
          WHEN address LIKE '%tamil%' THEN 'Tamil Nadu' 
          WHEN address LIKE '%telangana%' THEN 'Telangana' 
          WHEN address LIKE '%tripura%' THEN 'Tripura' 
          WHEN address LIKE '%uttar pradesh%' THEN 'Uttar Pradesh' 
          WHEN address LIKE '%uttarakhand%' THEN 'Uttarakhand' 
          WHEN address LIKE '%west%' THEN 'West Bengal' 
          WHEN address LIKE '%ladakh%' THEN 'Ladakh' 
          END  As state , a.address, a.latitude, a.user_id, a.longitude, a.tracking_date, admin.name as username,admin.mobile  FROM employee_locations a  left join admin on admin.id = a.user_id WHERE a.address like '%%'   ".$visit_condition." order by tracking_date ASC";

         $query = $this->db->query($data_query);    
      //echo $this->db->last_query(); die;
      $row = $query->result_array(); 
      return $row; 
  }
}