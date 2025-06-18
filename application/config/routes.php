<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['register'] = 'auth/register';
$route['login'] = 'auth/login';
$route['projects'] = 'projects';
$route['add_projects'] = 'projects/create';
$route['update_projects/(:num)']['put'] = 'projects/update/$1';
$route['delete_projects/(:num)']['delete'] = 'projects/delete/$1';

$route['tasks/detail/(:num)'] = 'tasks/detail/$1';
$route['add_task/(:num)'] = 'tasks/create/$1';
$route['update_task/(:num)'] = 'tasks/update/$1';
$route['update_task_status/(:num)'] = 'tasks/update_status/$1';
$route['tasks/remarks/(:num)'] = 'tasks/add_remark/$1';
$route['delete_task/(:num)']['delete'] = 'tasks/delete/$1';

$route['summaryreport/(:num)'] = 'reports/projectsummary/$1';
$route['summaryreport/download/(:num)'] = 'reports/download_report/$1';

$route['status_history_report/(:num)'] = 'reports/status_history/$1';
$route['status_history_report/download/(:num)'] = 'reports/statushistorydownload/$1';

$route['dailyremarks_report/(:num)'] = 'reports/remarks_report/$1';
$route['dailyremarks_report/download/(:num)'] = 'reports/dailyremarksdownload/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
