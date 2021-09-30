<?php
function doDefine($name, $value){
	if(!defined($name)){ define($name, $value); }
}

error_reporting(E_ALL);
ini_set('display_errors', 'On');

//'real' paths
doDefine('REAL_PATH', realpath(dirname(__FILE__)).'/');	//	/home/customer/www/littlemanharley.com/
doDefine('HTMLS_PATH', REAL_PATH.'htmls/');

//setup logging
doDefine('EOL', "\r\n");
doDefine('DEBUG_LOG', REAL_PATH.'logs/myDebug.log');
doDefine('DEBUG_TIMESTAMP', 'D M d H:i:s');
date_default_timezone_set('America/Los_Angeles');
if(!function_exists('logDebug')){
	function logDebug($text1, $text2=false){
		if($text2){//log an error
			error_log('['.date(DEBUG_TIMESTAMP).'] DAVERROR '.$text2.PHP_EOL, 3, DEBUG_LOG);
		}else{//log a debug
			error_log('['.date(DEBUG_TIMESTAMP).'] '.$text1.PHP_EOL, 3, DEBUG_LOG);
		}
	}
}

//paths from www/
doDefine('CSS_URL_PATH', '/css/');
doDefine('JS_URL_PATH', '/js/');

//get base site-config
$site_config = parse_ini_file(REAL_PATH.'site_config.ini');
if(!$site_config){
	echo 'no site config';
	exit;
}
doDefine('SITE_NUMBER', $site_config['site_number']);

//database defaults
doDefine("DB_HOST", $site_config['db_host']);
doDefine('DB_NAME', $site_config['db_name']);
doDefine('DB_USER', $site_config['db_user']);
doDefine('DB_PASS', $site_config['db_pass']);

//create the one and only instance of the DbFactory
require_once('classes/factories/DbFactory.php');
$dbFactory = DbFactory::getInstance();

if(!function_exists('ourautoload')){
	function ourautoload($classname){
		if(file_exists(REAL_PATH."classes/factories/{$classname}.php") && $classname !== 'DbFactory'){
			require_once("classes/factories/{$classname}.php");
		}
		if(file_exists(REAL_PATH."classes/core/{$classname}.php")){
			require_once("classes/core/{$classname}.php");
		}
		if(file_exists(REAL_PATH."classes/gateways/{$classname}.php")){
			require_once("classes/gateways/{$classname}.php");
		}
		if(file_exists(REAL_PATH."classes/{$classname}.php")){
			require_once("classes/{$classname}.php");
		}
	}
}
spl_autoload_register('ourautoload');

//create the factories to be injected into classes and functions
$configFactory			= new ConfigFactory;
$pictureFactory			= new PictureFactory;
$userFactory			= new UserFactory;
$videoFactory			= new VideoFactory;

//start session AFTER loading all the classes
//logDebug('starting session');
session_start();
$alreadyLoggedIn = (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true);

//prettymuch every page needs this...
$config = $configFactory->newInstance();

//logDebug('config complete');