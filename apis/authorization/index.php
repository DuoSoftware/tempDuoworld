<?php
	/*
	----------------------------------
	--  User Authorization Service  --
	--        Version 1.0.7         --
	----------------------------------
	*/

    	//skip the security token validation.
    	define ("SKIP_AUTH", true);

 	require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");
	require_once(ROOT_PATH . "/dwcommon.php");
 	require_once (ROOT_PATH .'/include/flight/Flight.php');
	require_once ("./userauth.php");

	new UserAuthorization();
 	Flight::start();

	header('Access-Control-Allow-Headers: Content-Type');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');  
?>
