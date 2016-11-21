<?php

checkPermissionToShare($requestObj->appKey);

$userFile;
$perfFile;

/*
function getUserFiles(){
	$authObj = json_decode($_COOKIE["authData"]);
	$username = $authObj->Username;
	$userFile = TENANT_MEDIA_FOLDER . "/apppermisson/$username.json";
	$perfFile = file_exists($userFile) ? $userFile : (strpos(DuoWorldCommon::GetHost(), '.dev.') == 0 ? "data/default.dev.json" : "data/default.json");
}
*/

function recurse_rmdir($dir) {
  $files = array_diff(scandir($dir), array('.','..'));
  foreach ($files as $file) 
    (is_dir("$dir/$file")) ? recurse_rmdir("$dir/$file") : unlink("$dir/$file");
  
  return rmdir($dir);
}

function getUserFolder(){
	$userFolder = (isGlobalMode() ?  USER_MEDIA_FOLDER : TENANT_MEDIA_FOLDER) . "/apppermisson/";
	if (!file_exists($userFolder))
		mkdir($userFolder, 0777, true);
	return $userFolder;
}

if (isset($requestObj->getParams->share)){ //share an app
	$appKey = $requestObj->appKey;

	$allFile = TENANT_MEDIA_FOLDER . "/apppermisson/all.json";
	$allApps =  json_decode(file_get_contents($allFile));
	$appObj = $allApps->$appKey;

	$shareUsers = explode(",", $requestObj->getParams->share);

	foreach ($shareUsers as $shareUser) {
		$userFile =  getUserFolder() . trim($shareUser) . ".json";
		$perfFile = file_exists($userFile) ? $userFile : (isDevTenant() ? "data/default.dev.json" : "data/default.json");
		$userAppObj = json_decode(file_get_contents($perfFile));
		if (!isset($userAppObj->$appKey)){
			$userAppObj->$appKey = $appObj;
			file_put_contents($userFile, json_encode($userAppObj));
		}
	}

	sendJsonResponse("$appKey is shared amoung ". $requestObj->getParams->share);
} 
else if (isset($requestObj->getParams->unshare)){ //unshare an app

	$appKey = $requestObj->appKey;
	$shareUsers = explode(",", $requestObj->getParams->unshare);
	
	foreach ($shareUsers as $shareUser) {
		$userFile =  getUserFolder() . trim($shareUser) . ".json";

		if (file_exists($userFile)){
			$userAppObj = json_decode(file_get_contents($userFile));
			if (isset($userAppObj->$appKey)){
				unset($userAppObj->$appKey);
				file_put_contents($userFile, json_encode($userAppObj));
			}
		}
	}
	sendJsonResponse("$appKey is unshared amoung ". $requestObj->getParams->unshare);
}
else { //get shares of apps
	$allFile = TENANT_MEDIA_FOLDER . "/apppermisson/all.json";
	$appKey = $requestObj->appKey;
	$filterUsers = strlen($appKey) <= 1 ? false: true;

	$outArray = array();
	if ($dh = opendir(getUserFolder())){
		while (($file = readdir($dh)) !== false){
			$path_parts = pathinfo($file);
			if (strcmp($path_parts['filename'], "all") == 0) continue;
			if (strcmp($path_parts['extension'], "json") == 0){
			  $userApps =  json_decode(file_get_contents(getUserFolder()."/$file"));
			  if (isset($userApps->$appKey))
			  	array_push($outArray, str_replace(".json", "", $file));			
			}
		}
		closedir($dh);
	}
	echo json_encode($outArray);
	header("Content-type: application/json");
}

?>
