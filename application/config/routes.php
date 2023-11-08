<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
 


/* admin pane start */

$route['default_controller'] = "login/index";
$route['rate/cnfrate'] = "cnfrate";
$route['rate/copy'] = "cnfrate/GetBookingSkusRates";

$route['rate/cnfratepdf'] = "cnfrate/RatePDF";
$route['rate/cnfratepdfwhatsapp'] = "cnfrate/RatePDFwhatsapp";


$route['dashboard'] = "login/dashboard";
$route['logout'] = "login/logout";
$route['changepassword'] = "login/changepassword";

$route['users'] = "users/index";
$route['users/activate/(:any)'] = "users/activate";

$route['products'] = "product/index";
$route['edit_product/(:any)'] = "product/edit_product/";

 



$route['paid_orders'] = "order_admin/paid_orders";
$route['unpaid_orders'] = "order_admin/unpaid_orders";
$route['order/(:any)'] = "order_admin/order";
$route['reports'] = "report_admin/index";

$route['states'] = "location/states";
$route['districts'] = "location/districts";
$route['city'] = "location/city";


/* End of file routes.php */
/* Location: ./application/config/routes.php */