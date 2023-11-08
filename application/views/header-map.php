<?php @date_default_timezone_set("Asia/Kolkata"); 
//echo @date_default_timezone_get(); die;
$admin_info = $this->session->userdata('admin');
$admin_role = $admin_info['role']; 
?>
<!doctype html>
<html lang="en-US">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title><?php echo $title; ?></title>
        <meta name="description" content="" />
        <meta name="Author" content="Dorin Grigoras [www.stepofweb.com]" />
        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800&amp;subset=latin,latin-ext,cyrillic,cyrillic-ext" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/css/essentials.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/css/layout.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>assets/css/color_scheme/green.css" rel="stylesheet" type="text/css" id="color_scheme" />
        <link href="<?php echo base_url(); ?>assets/css/layout-datatables.css" rel="stylesheet" type="text/css" />

    </head>
    <body>


        <!-- WRAPPER -->
        <div id="wrapper">
            <aside id="aside">
                <nav id="sideNav" class="map_navigation">
                    <?php $admin_info = $this->session->userdata('admin');  
                        $allow_rate = $admin_info['allow_rate'];
                        $admin_role = $admin_info['role']; 
                        $business_role = $admin_info['business_role'];
                        $pi_making_access = $admin_info['pi_making_access'];  ?> 
                    <ul class="nav nav-list">
                        <?php $current_year = date('Y');
                        $current_month = date('m');
                        $current_day = cal_days_in_month(CAL_GREGORIAN, $month, $current_year);;
                        if($current_month==$month && $current_year== $year)
                            $current_day = str_pad(date('d'), 2, "0", STR_PAD_LEFT); 

                        //echo $current_month; die;
                        for ($i=1; $i <= $current_day; $i++) {  ?>
                        <!--<li>
                            <a class="dashboard" href="javascript:void(0)">
                               <?php echo $i.'/'.$month.'/'.$year;  ?>
                            </a>
                        </li> -->
                        <?php } ?>

                        <?php if($locations_month) 
                        { ?>
                            <li>
                                <a class="" href="<?php echo base_url(); ?>target_report/map/<?php echo $userid.'/'.$month.'/'.$year.'/'.urlencode($state_id); ?>">
                                    <?php echo date('F', mktime(0, 0, 0, $month, 10)); ?>
                                </a>
                                <a class="expand">
                                    <span>+</span>
                                </a>
                                <?php foreach ($locations_month as $key => $locations_months) { ?>
                                    <ul>
                                        <?php foreach ($locations_months as $locations_months_key => $locations_months_value) {  ?>
                                            <li><a href="<?php echo base_url(); ?>target_report/map/<?php echo $userid.'/'.$month.'/'.$year.'/'.urlencode($state_id).'/'.date('d',strtotime($locations_months[0]['tracking_date'])).'/'.$locations_months_value['latitude'].'/'.$locations_months_value['longitude']; ?>"><?php $address = explode('###', $locations_months_value['address']);
                                            echo $address[0]; ?></a></li> 
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </li>
                            <?php
                            foreach ($locations_month as $key => $locations_months) { ?>
                                <li>
                                <a class="" href="<?php echo base_url(); ?>target_report/map/<?php echo $userid.'/'.$month.'/'.$year.'/'.urlencode($state_id).'/'.date('d',strtotime($locations_months[0]['tracking_date'])); ?>">
                                   <?php echo date('d-m-Y', strtotime($locations_months[0]['tracking_date'])); ?>
                                </a>
                                <a class="expand">
                                    <span>+</span>
                                </a>
                                <ul>
                                    <?php foreach ($locations_months as $locations_months_key => $locations_months_value) {  ?>
                                    <li><a href="<?php echo base_url(); ?>target_report/map/<?php echo $userid.'/'.$month.'/'.$year.'/'.urlencode($state_id).'/'.date('d',strtotime($locations_months[0]['tracking_date'])).'/'.$locations_months_value['latitude'].'/'.$locations_months_value['longitude']; ?>"><?php 
                                    $address = explode('###', $locations_months_value['address']);

                                    echo $address[0]; ?></a></li> 
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php }
                        } ?>

                    </ul> 
                </nav>
                <span id="asidebg">
                    <!-- aside fixed background -->
                </span>
            </aside>
            <!-- /ASIDE -->


            <!-- HEADER -->
            <header id="header">
                <!-- Mobile Button -->
                <button id="mobileMenuBtn"></button>
                <!-- Logo -->
                <span class="logo pull-left">
                    <?php $admin_info = $this->session->userdata('admin');  
                        $admin_role = $admin_info['role'];
                        if($admin_role ==1)
                            echo "MAKER"; 
                        elseif ($admin_role ==5)  
                            echo "VIEWER"; 
                        elseif ($admin_role ==6)  
                            echo "SECONDARY MAKER";
                        elseif ($admin_role ==7)  
                            echo "ACCOUNTS"; 
                        else
                            echo "ADMIN"; 
                        ?>
                </span> 
                <span class="header_target" style="">
                    <?php
                    $admin_info = $this->session->userdata('admin');  
                        $admin_role = $admin_info['role']; 
                        if($admin_role==1)
                            echo getemplyeetargetreport(); 
                        if($admin_role==4)
                            echo getemplyeetargetreportadmin(); ?>
                    </span>
                <nav>
                    <!-- OPTIONS LIST -->
                    <ul class="nav pull-right">
                        <!-- USER OPTIONS -->
                        <li class="dropdown pull-left">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img class="user-avatar" alt="" src="<?php echo base_url(); ?>assets/images/noavatar.jpg" height="34" /> 
                            <span class="user-name">
                            <span class="hidden-xs">
                            <?php echo ucwords($admin_info['name']); ?> <i class="fa fa-angle-down"></i>
                            </span>
                            </span>
                            </a>
                            <ul class="dropdown-menu hold-on-click"> 
                                <li>
                                    <!-- logout -->
                                    <a href="<?php echo base_url(); ?>logout"><i class="fa fa-power-off"></i> Log Out</a>
                                </li>
                            </ul>
                        </li>
                        <!-- /USER OPTIONS -->
                    </ul>
                    <!-- /OPTIONS LIST -->
                </nav>
            </header>
            <!-- /HEADER -->