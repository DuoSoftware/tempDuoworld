<?php
	error_reporting (E_ALL);
	ini_set ('display_errors', 1); 
	
//	header ('Access-Control-Allow-Origin: *');

	ini_set ('xdebug.var_display_max_depth', 5);
	ini_set ('xdebug.var_display_max_children', 256);
	ini_set ('xdebug.var_display_max_data', 1024);	

	if (strpos($_SERVER["REQUEST_URI"],"/profilepic/") !==FALSE)
		define ("SKIP_AUTH", true);

 	require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");
	require_once (ROOT_PATH . "/dwcommon.php");
	define("AUTH_FAIL_RESPONSE_JSON",true);
 	require_once (ROOT_PATH .'/include/flight/Flight.php');

 	if (!defined("MAIN_DOMAIN"))
		define ("MAIN_DOMAIN", $mainDomain);
		
	require_once ("./mediaservice.php");
	new MediaService ();

 	Flight::start();
 	
 	header('Content-Type: application/json');
	header('Access-Control-Allow-Headers: Content-Type');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
?>
