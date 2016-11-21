<?php

function readResources($root, $dir){
          $arr = array();
          $files = array_diff(scandir($dir), array('.','..'));
          foreach ($files as $file) {
            if (is_dir("$dir/$file")) $arr = array_merge(readResources ($root, $arr, "$dir/$file"), $arr);
            else {
                $fileObj = new stdClass();
                $fileObj->id = str_replace($root. "/", "", "$dir/$file");
                $fileObj->data = file_get_contents("$dir/$file");
                array_push($arr, $fileObj);
            }
          }
          return $arr;
}

$appPath = TENANT_MEDIA_FOLDER . "/apps/$requestObj->appKey/";
switch ($requestObj->getParams->meta){
        case "desc":
            if (file_exists($appPath. "descriptor.json")){
                echo file_get_contents($appPath. "descriptor.json");
                header('Content-Type: application/json');
            }
            else sendJsonResponse("Unable to load descriptor for app : $requestObj->appKey", false, 401);
            break;
        case "res":
            if (file_exists($appPath . "resources")){
                echo json_encode(readResources($appPath . "resources", $appPath . "resources"));
                header('Content-Type: application/json');
            }
            else sendJsonResponse("Unable to load resources for app : $requestObj->appKey", false, 401);
            break;
        case "icon":
            if (file_exists($appPath. "icon.png")) echo file_get_contents($appPath. "icon.png");
            else echo file_get_contents("data/defaulticon.png");
            header('Content-Type: image/png');
            break;
        case "shares": //show shares for a particular app
            require_once("shares.php");
            break;
        case "bundle":
            if (file_exists($appPath."/resources/bundle.json")) echo file_get_contents($appPath."/resources/bundle.json");
            else echo "{\"appKey\":\"$requestObj->appKey\", \"apps\":[]}";
            header('Content-Type: application/json');
        	break;
        case "scope":
        	if (file_exists($appPath."scope.json")) echo file_get_contents($appPath."scope.json");
        	else echo "{\"appKey\":\"$requestObj->appKey\", \"scope\":{\"data\":[], \"functions\":[]}}";
        	header ('Content-Type: application/json');
        	break;
        default:
            sendJsonResponse("Unknown Metadata Operation",false,401);
            exit();
            break;
}

header("Cache-Control: no-transform,public,max-age=86400,s-maxage=86400");

?>
