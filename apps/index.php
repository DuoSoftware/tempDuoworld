<?php
header("Access-Control-Allow-Origin: *");
define("AUTH_FAIL_RESPONSE_JSON", true);
define("PAYMENT_KEY","PAYMENT_GATWAY");

require_once("../include/config.php");
require_once($_SERVER["DOCUMENT_ROOT"]. "/dwcommon.php");

function setProfile($profile="PROXY", $isDebug=false, $isSecure=true){
	if ($isDebug){
		ini_set('display_errors', 1); 
		error_reporting(E_ALL);
	}

	if (!$isSecure){
		define("SKIP_AUTH", true);
		if (!defined("APP_DISABLE_JWT"))
			define("APP_DISABLE_JWT", true);
	}

	switch ($profile){
		case "PROXY":
			break;
		case "STORAGE":
			if (!defined("SKIP_AUTH"))
				define("SKIP_AUTH", true);
			break;
		case "STORAGE-AUTH":
			break;
	}
}

if (!defined("STORAGE_PROFILE"))
	define("STORAGE_PROFILE", "STORAGE-AUTH");

setProfile(STORAGE_PROFILE, false, true);

if (strcmp(STORAGE_PROFILE, "PROXY") == 0) require_once("forward.php");

require_once("environ.php");

if (!defined("MAIN_DOMAIN")) define ("MAIN_DOMAIN", $mainDomain);
define ("TENANT_MEDIA_FOLDER", MEDIA_PATH . "/" . (isGlobalMode() ? $mainDomain :DuoWorldCommon::GetHost()));

if (isGlobalMode()) define ("USER_MEDIA_FOLDER", MEDIA_PATH . "/" . DuoWorldCommon::GetHost());

$requestObj = new stdClass();
$requestObj->relativePath = str_replace('/apps','',$_SERVER["REQUEST_URI"]);
extractAllParams($requestObj);
$tmpAppKey = substr($requestObj->relativePath, 1);
if (strpos($tmpAppKey, "/") != FALSE)
	$tmpAppKey = substr($tmpAppKey, 0, strpos($tmpAppKey, "/"));

$requestObj->appKey = $tmpAppKey;

if ($requestObj->relativePath =="/") {
	$requestObj->appMode = false;
	if (isset($requestObj->getParams)) serveByGetParam($requestObj); 
	else require_once("functions/allapps.php");
}
else {
	$requestObj->appMode = true;
	if ($_SERVER['REQUEST_METHOD'] == "POST") require_once("functions/localinstall.php");
	else {
		if (file_exists(TENANT_MEDIA_FOLDER . "/apps/$requestObj->appKey")){
			if (isset($requestObj->getParams)) serveByGetParam($requestObj); 
			else {
				if (checkPermissionToOpen($requestObj->appKey))
					require_once("functions/resource.php");
			}			
		}
		else sendJsonResponse("Application not installed", $succes=false, $code=401);
		
	}
}

?>

