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

$route['default_controller'] = "home";  
$route['404_override'] = '';

$route['useragreement'] = 'home/useragreement';

//countries
$route['ind'] = 'home/index';
$route['id'] = 'home/index';
$route['th'] = 'home/index';
$route['bangladesh'] = 'home/index';
$route['cambodia'] = 'home/index';
$route['china'] = 'home/index';
$route['jp'] = 'home/index';
$route['myanmar'] = 'home/index';
$route['nepal'] = 'home/index';
$route['ph'] = 'home/index';
$route['korea'] = 'home/index';
$route['srilanka'] = 'home/index';
$route['taiwan'] = 'home/index';
$route['vietnam'] = 'home/index';
$route['can'] = 'home/index';
$route['usa'] = 'home/index';
$route['aus'] = 'home/index';
$route['russia'] = 'home/index';
$route['uae'] = 'home/index';
$route['uk'] = 'home/index';

$route['category/(:any)/search']  = 'books/category_search/$1';
$route['category/(:any)/search/(:any)/(:any)']  = 'books/category_search/$1/$2/$3';
$route['category/(:any)']         = 'home/index/$1';

// Authentication
$route[LOGIN_URL]				= 'users/login';
$route[REGISTER_URL]            = 'users/register';
$route['hot-this-week']         = 'home/hot_this_week';
$route['users/login']           = '';
$route['users/register']        = '';
$route['logout']				= 'users/logout';
$route['forgot_password']		= 'users/forgot_password';
$route['reset_password/(:any)/(:any)']	= "users/reset_password/$1/$2";

// Contexts
$route[SITE_AREA .'/([a-z_]+)/(:any)/(:any)/(:any)/(:any)/(:any)']		= "$2/$1/$3/$4/$5/$6";
$route[SITE_AREA .'/([a-z_]+)/(:any)/(:any)/(:any)/(:any)']		= "$2/$1/$3/$4/$5";
$route[SITE_AREA .'/([a-z_]+)/(:any)/(:any)/(:any)']		= "$2/$1/$3/$4";
$route[SITE_AREA .'/([a-z_]+)/(:any)/(:any)'] 		= "$2/$1/$3";
$route[SITE_AREA .'/([a-z_]+)/(:any)']				= "$2/$1/index";
$route[SITE_AREA .'/content']				= "control/content/index";
$route[SITE_AREA .'/reports']				= "control/reports/index";
$route[SITE_AREA .'/developer']				= "control/developer/index";
$route[SITE_AREA .'/settings']				= "settings/index";

$route[SITE_AREA]	= 'control/home';

// Activation
$route['activate']		        = 'users/activate';
$route['activate/(:any)']		= 'users/activate/$1';
$route['resend_activation']		= 'users/resend_activation';


$route['(\d{13})/?(:any)?']		= 'books/details/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */