<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ==================================
// = MORESODA SERVER IDENTIFICATION =
// ==================================

// ==================================
// = MORESODA SERVER IDENTIFICATION =
// ==================================

if($_SERVER['HTTP_HOST']  ==  'LOCAL-HOST-NAME'){ // ATTENTION
	$active_group = 'local';
}elseif($_SERVER['HTTP_HOST']  == 'PREVIEW-DOMAIN.moresoda-preview.co.uk'){ // ATTENTION
	$active_group = 'preview';
}else{
	$active_group = 'live';
}
$active_record = TRUE;

$db['expressionengine']['hostname'] = "localhost";
$db['expressionengine']['username'] = "LIVE-USER"; // ATTENTION
$db['expressionengine']['password'] = "LIVE-PASSWORD"; // ATTENTION
$db['expressionengine']['database'] = "LIVE-DATABASE"; // ATTENTION
$db['expressionengine']['dbdriver'] = "mysql";
$db['expressionengine']['dbprefix'] = "exp_";
$db['expressionengine']['pconnect'] = FALSE;
$db['expressionengine']['swap_pre'] = "exp_";
$db['expressionengine']['db_debug'] = TRUE;
$db['expressionengine']['cache_on'] = FALSE;
$db['expressionengine']['autoinit'] = FALSE;
$db['expressionengine']['char_set'] = "utf8";
$db['expressionengine']['dbcollat'] = "utf8_general_ci";
$db['expressionengine']['cachedir'] = APPPATH . "cache/db_cache/";

$db['preview']['hostname'] = "localhost";
$db['preview']['username'] = "moreprev";
$db['preview']['password'] = "12qwaszx";
$db['preview']['database'] = "moresoda-preview_co_uk_PREVIEW-DB"; // ATTENTION
$db['preview']['dbdriver'] = "mysql";
$db['preview']['dbprefix'] = "exp_";
$db['preview']['pconnect'] = FALSE;
$db['preview']['swap_pre'] = "exp_";
$db['preview']['db_debug'] = TRUE;
$db['preview']['cache_on'] = FALSE;
$db['preview']['autoinit'] = FALSE;
$db['preview']['char_set'] = "utf8";
$db['preview']['dbcollat'] = "utf8_general_ci";
$db['expressionengine']['cachedir'] = APPPATH . "cache/db_cache/";

$db['local']['hostname'] = "localhost";
$db['local']['username'] = "root";
$db['local']['password'] = "root";
$db['local']['database'] = "LOCAL-DATABASE"; // ATTENTION
$db['local']['dbdriver'] = "mysql";
$db['local']['dbprefix'] = "exp_";
$db['local']['pconnect'] = FALSE;
$db['local']['swap_pre'] = "exp_";
$db['local']['db_debug'] = TRUE;
$db['local']['cache_on'] = FALSE;
$db['local']['autoinit'] = FALSE;
$db['local']['char_set'] = "utf8";
$db['local']['dbcollat'] = "utf8_general_ci";
$db['expressionengine']['cachedir'] = APPPATH . "cache/db_cache/";
