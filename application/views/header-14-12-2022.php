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
                <nav id="sideNav">
                    <ul class="nav nav-list">
                        <li>
                            <a class="dashboard" href="<?php echo base_url('dashboard'); ?>">
                                <i class="main-icon fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <?php $admin_info = $this->session->userdata('admin'); 
                        $allow_rate = $admin_info['allow_rate'];
                        $admin_role = $admin_info['role']; ?> 
                        <?php if($admin_role==4 || $admin_role==5) { ?>
                        <li>
                            <a class="dashboard" href="javascript:void(0)">
                                <i class="main-icon fa fa-dashboard"></i> <span>Master</span>
                            </a>
                            <ul>
                                <li>
                                    <a class="dashboard" href="<?php echo base_url(); ?>brand">
                                        <i class="main-icon fa fa-dashboard"></i> <span>Brand</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dashboard" href="<?php echo base_url(); ?>category">
                                        <i class="main-icon fa fa-dashboard"></i> <span>Category</span>
                                    </a>
                                </li>
                                <li> 
                                    <a class="dashboard" href="<?php echo base_url(); ?>products"> 
                                        <i class="main-icon fa fa-dashboard"></i> <span>Products</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dashboard" href="<?php echo base_url(); ?>vendors">
                                        <i class="main-icon fa fa-dashboard"></i> <span>Vendors</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dashboard" href="#">
                                        <i class="main-icon fa fa-dashboard"></i> <span>Location Management</span>
                                    </a>
                                    <ul>
                                        <li><a href="<?php echo base_url(); ?>location/states">States</a></li>
                                        <!--<li><a href="<?php echo base_url(); ?>location/districts">District</a></li>-->
                                        <li><a href="<?php echo base_url(); ?>location/city">City</a></li>
                                    </ul>
                                </li>  
                                <li>
                                    <a class="dashboard" href="<?php echo base_url(); ?>brokers">
                                        <i class="main-icon fa fa-dashboard"></i> <span>Broker Management</span>
                                    </a>
                                </li> 
                            </ul>
                        </li>
                        <li>
                            <a class="dashboard" href="<?php echo base_url(); ?>performainvoice">
                                <i class="main-icon fa fa-dashboard"></i> <span>Performa Invoice</span>
                            </a>
                        </li>
                        <?php } if($admin_role==4) { ?>
                        <li>
                            <a class="dashboard" href="javascript:void(0)">
                                <i class="main-icon fa fa-dashboard"></i> <span>Sales Operation</span>
                            </a>
                            <ul>
                                <li>
                                    <a href="<?php echo base_url(); ?>booking">Bookings</a> 
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>booking/report">
                                <i class="main-icon fa fa-dashboard"></i> <span>Reports</span>
                            </a>
                        </li>
                        <li>
                            <a class="dashboard" href="<?php echo base_url(); ?>admins">
                                <i class="main-icon fa fa-dashboard"></i> <span>Team Management</span>
                            </a>
                        </li>
                        <li>
                            <a class="dashboard" href="<?php echo base_url(); ?>rate">
                                <i class="main-icon fa fa-dashboard"></i> <span>Rate Master</span>
                            </a>
                        </li>  
                        <li>
                            <a href="<?php echo base_url(); ?>logout" class="dashboard"><i class="main-icon fa fa-power-off"></i></i> <span>Logout</span>
                            </a>
                             
                        </li> 
                        <?php } else {  if($admin_role!=5) {  ?>
                        <li>
                            <a href="<?php echo base_url(); ?>booking">
                                <i class="main-icon fa fa-dashboard"></i> <span>Bookings</span>
                            </a>
                        </li> 
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url(); ?>booking/report">
                                <i class="main-icon fa fa-dashboard"></i> <span>Reports</span>
                            </a>
                        </li>
                        <?php if($allow_rate==1) { ?>
                        <li>
                            <a href="<?php echo base_url(); ?>rate">
                                <i class="main-icon fa fa-dashboard"></i> <span>Rates</span>
                            </a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url(); ?>logout" class="dashboard"><i class="main-icon fa fa-power-off"></i></i> <span>Logout</span>
                            </a>
                             
                        </li> 
                        <?php }  ?>
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
                    <?php echo ucwords($admin_info['name']); ?>
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