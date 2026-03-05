<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array();

$autoload['libraries'] = array('database','session','form_validation','Tcpdf_lib');

$autoload['drivers'] = array();

$autoload['helper'] = array('url','form','html','common');

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array('Login_model','Master_model','Main_model','Api_Model','Report_model');
