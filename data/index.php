<?php
	header("Access-Control-Allow-Origin: *");
	require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");
	require_once("./config.php");
	define("AUTH_FAIL_RESPONSE_JSON",true);
	require_once(ROOT_PATH . "/dwcommon.php");

	require_once("DataManager.php");

	define ("STORAGE_REL_URL", "/data/");
	$response = (new DataManager())->Process();
	echo is_string($response) ? $response : json_encode($response);

?>
