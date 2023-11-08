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
                    <?php $admin_info = $this->session->userdata('admin');  
                        $allow_rate = $admin_info['allow_rate'];
                        $admin_role = $admin_info['role']; 
                        $business_role = $admin_info['business_role'];
                        $parent_user_name = $admin_info['parent_user_name'];
                        $parent_user_id = $admin_info['parent_user_id'];
                        $pi_making_access = $admin_info['pi_making_access'];  ?> 
                    <ul class="nav nav-list">
                        <li>
                            <a class="dashboard" href="<?php echo base_url('dashboard'); ?>">
                                <i class="main-icon fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>  
                        <?php if($admin_role!=7) { ?>
                            <?php if($business_role==1 || $business_role==3) { ?>
                            <li>
                                <a class="dashboard" href="javascript:void(0)">
                                    <i class="main-icon fa fa-dashboard"></i> <span>Sales Operation</span>
                                </a>
                                <ul>   
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
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Suppliers</span>
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
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>schemes">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Scheme Management</span>
                                                </a>
                                            </li> 
                                        </ul>
                                    </li>
                                    <?php } ?>
                                    <?php if(($admin_role==4 || $admin_role==5 ) || ($admin_role==1 && $pi_making_access == 1 )) { ?>
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>performainvoice">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Performa Invoice</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if($admin_role!=6) { ?> 
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>distributors">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Distributors</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <li>
                                        <a class="dashboard" href="javascript:void(0)">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Bookings</span>
                                        </a>
                                        <ul>
                                            <?php if($admin_role!=5 && $admin_role!=6) { ?>  
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>booking"><i class="main-icon fa fa-dashboard"></i> <span>Booking</span></a> 
                                            </li>
                                            <?php } ?>
                                            <?php if($admin_role==6 || $admin_role==4) { ?> 
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>secondarybooking"><i class="main-icon fa fa-dashboard"></i><span>Secondray Booking</span></a> 
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </li>

                                    <li>
                                        <a class="dashboard" href="javascript:void(0)">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Reports</span>
                                        </a>
                                        <ul>
                                            <?php if($admin_role!=6) { ?> 
                                            <li>
                                                <a href="<?php echo base_url(); ?>booking/report" title="Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Report</span>
                                                </a>
                                            </li>
                                            <?php }  ?>
                                            <li>
                                                <a href="<?php echo base_url(); ?>secondarybooking/report" title="Secondray Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Secondray Report</span>
                                                </a>
                                            </li>

                                            <?php if($admin_role==4 || $admin_role==5) { ?>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>pihistory" title="PI Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>PI Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>secondarypihistory" title="Secondary PI Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Secondary PI Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>accounting" title="Accounting Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Accounting Report</span>
                                                </a>
                                            </li> 
                                            <?php } ?>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>target_report" title="Target Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Target Report</span>
                                                </a>
                                            </li> 
                                            
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>performance" title="Performance Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Performance Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>booking/state_city_report" title="State City Dispatch Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>State City Dispatch Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>scheme_report" title="Report Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Scheme Report</span>
                                                </a>
                                            </li>
                                            <?php if($admin_role==4 || $admin_role==5) { ?>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>dispatch_report" title="Dispatched QTY Report">
                                                    <i class="main-icon fa fa-dashboard"></i> <span> Dispatched QTY Report</span>
                                                </a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </li>



                                    <?php if($admin_role==4) { ?>
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>admins">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Team Management</span>
                                        </a>
                                    </li> 
                                    <?php } ?>
                                    <?php if($admin_role==1 || $admin_role==4 || $admin_role==5) { ?>
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>targets">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Target Management</span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if($admin_role==4 || $allow_rate==1) { ?>
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>rate">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Rate Master</span>
                                        </a>
                                    </li> 
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>rate/cnfrate">
                                            <i class="main-icon fa fa-dashboard"></i> <span>C&F Rate Master</span>
                                        </a>
                                    </li> 
                                    <?php } ?>
                                    <?php if($admin_role==4) { ?>
                                        <li>
                                            <a class="dashboard" href="<?php echo base_url(); ?>messages" title="Messages">
                                                <i class="main-icon fa fa-dashboard"></i> <span>Whatsapp Messages</span>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li> 
                            <?php } if($business_role==2 || $business_role==3) { ?>    
                            <li>
                                <a class="dashboard" href="javascript:void(0)">
                                    <i class="main-icon fa fa-dashboard"></i> <span>Purchase Operation</span>
                                </a>
                                <ul>                      
                                    <?php if($admin_role==4 || $admin_role==10 || $admin_role==9) { ?>
                                    <li>
                                        <a class="dashboard" href="javascript:void(0)">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Master</span>
                                        </a>
                                        <ul> 
                                            <?php if($admin_role==4 || $admin_role==10 || $admin_role==9) { ?>
                                            <li> 
                                                <a class="dashboard" href="<?php echo base_url(); ?>purchase/purchasecity"> 
                                                    <i class="main-icon fa fa-dashboard"></i> <span>City</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>purchase/purchasecategory">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Category</span>
                                                </a>
                                            </li>
                                            <li> 
                                                <a class="dashboard" href="<?php echo base_url(); ?>purchase/purchaseproduct"> 
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Products</span>
                                                </a>
                                            </li>
                                            <?php } ?>
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>purchase/purchasevendors">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Vendors</span>
                                                </a>
                                            </li>  
                                            <li>
                                                <a class="dashboard" href="<?php echo base_url(); ?>purchase/purchasebrokers">
                                                    <i class="main-icon fa fa-dashboard"></i> <span>Broker Management</span>
                                                </a>
                                            </li> 
                                        </ul>
                                    </li>
                                    <?php } if(($admin_role==4 || $admin_role==10 || $admin_role==9) && (($business_role==2 || $business_role==3))) { ?>
                                    <li>
                                        <a class="dashboard" href="javascript:void(0)">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Bookings</span>
                                        </a>
                                        <ul>
                                            <li>
                                                <a href="<?php echo base_url(); ?>purchase/purchase"><i class="main-icon fa fa-dashboard"></i>Orders</a> 
                                            </li>
                                        </ul>
                                    </li>
                                    <?php } if($business_role==2 || $business_role==3 ) { ?>
                                    <li>
                                        <a class="dashboard" href="javascript:void(0)">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Reports</span>
                                        </a>
                                        <ul>
                                            <?php if($admin_role==4 || $admin_role==10 || $admin_role==9 || $admin_role==11) { ?>
                                            <li>
                                                <a href="<?php echo base_url(); ?>purchase/purchase/report"><i class="main-icon fa fa-dashboard"></i>Report</a> 
                                            </li>
                                            <li>
                                                <a href="<?php echo base_url(); ?>purchase/purchase/inventoryreport"><i class="main-icon fa fa-dashboard"></i>Inventory Report</a> 
                                            </li>
                                            <?php }  ?>
                                            <li>
                                                <a href="<?php echo base_url(); ?>purchase/labreport"><i class="main-icon fa fa-dashboard"></i>Lab Report</a> 
                                            </li>
                                            <?php if($admin_role==4) { ?>
                                                <li>
                                                    <a class="dashboard" href="<?php echo base_url(); ?>purchase/trendreport" title="Trend Report">
                                                        <i class="main-icon fa fa-dashboard"></i> <span> Trend Report</span>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li> 
                                    <?php }  ?>
                                </ul>
                            </li>       
                            <?php } ?>  
                        <?php } else { ?>
                            <li>
                                <a class="dashboard" href="<?php echo base_url(); ?>performainvoice">
                                    <i class="main-icon fa fa-dashboard"></i> <span>Performa Invoice</span>
                                </a>
                            </li>
                            <li>
                                <a class="dashboard" href="javascript:void(0)">
                                    <i class="main-icon fa fa-dashboard"></i> <span>Reports</span>
                                </a>
                                <ul>
                                    <li>
                                        <a href="<?php echo base_url(); ?>booking/report">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Report</span>
                                        </a>
                                    </li> 
                                    <li>
                                        <a href="<?php echo base_url(); ?>secondarybooking/report">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Secondray Report</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url(); ?>pihistory">
                                            <i class="main-icon fa fa-dashboard"></i> <span>PI Report</span>
                                        </a>
                                    </li> 
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>secondarypihistory">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Secondary PI Report</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>accounting">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Accounting Report</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>target_report">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Target Report</span>
                                        </a>
                                    </li> 
                                    
                                    <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>performance">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Performance Report</span>
                                        </a>
                                    </li>
                                   
                                </ul>
                            </li>
                             <li>
                                        <a class="dashboard" href="<?php echo base_url(); ?>targets">
                                            <i class="main-icon fa fa-dashboard"></i> <span>Target Management</span>
                                        </a>
                                    </li>
                        <?php } ?>
                        <li>
                            <a href="<?php echo base_url(); ?>logout" class="dashboard"><i class="main-icon fa fa-power-off"></i></i> <span>Logout</span>
                            </a>                                     
                        </li> 
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
                            <?php echo ($parent_user_id) ? ucwords($parent_user_name." as ".$admin_info['name']) : ucwords($admin_info['name']); ?> <i class="fa fa-angle-down"></i>
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