<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Welcome';
$route['404_override'] = 'Welcome/get_long_url';
$route['translate_uri_dashes'] = FALSE;
$route['CustLogin'] = 'Login/customer_login';
