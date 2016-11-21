<?php
if (isGlobalMode()){
    sendJsonResponse("System Configured to use global tenant apps. Operation successfully completed!!!");
    exit();
}

checkPermissionToInstall($requestObj->getParams->install, $requestObj->appKey);

$logObj = new stdClass();
$logObj->log = array();
$logObj->success = true;

function zip($source, $destination, $logObj){
    if (!extension_loaded('zip') || !file_exists($source)) return false;

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) return false;

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true){
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file){
            $file = str_replace('\\', '/', $file);
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) continue;
        	
            //$file = str_replace($source, "", realpath($file)); //;
            if (is_dir($file) === true)
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            else if (is_file($file) === true)
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
        }
    }
    else if (is_file($source) === true)
        $zip->addFromString(basename($source), file_get_contents($source));
    
    array_push($logObj->log, "Successfully zipped contents!!!");

    return $zip->close();
}

 function postZip($zipName, $appKey, $tenant, $logObj){
	$zipContents = file_get_contents($zipName);
    $ch = curl_init();

    $currentHeaders = apache_request_headers();
    $forwardHeaders = array("Host: $tenant", "Content-Type: application/json");
    
    foreach ($currentHeaders as $key => $value)
        if (!(strcmp(strtolower($key), "host") ===0 || strcmp(strtolower($key),"content-type")===0))
            array_push($forwardHeaders, "$key : $value");

    $cookies = array();
	foreach ($_COOKIE as $key => $value)
	    if ($key != 'Array')
	        $cookies[] = $key . '=' . $value;
	
	curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookies));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $zipContents); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders); 
    curl_setopt($ch, CURLOPT_URL, "http://localhost/apps/$appKey");
    //echo "http://localhost/apps/$appKey";
    //exit();
    $data = curl_exec($ch);

    $postResponse = new stdClass();
    $postResponse->server = $tenant;
    $postResponse->response = json_decode($data);
    array_push($logObj->log, $postResponse);
    curl_close($ch);
}

//get descriptor
//remote install by calling the url

$appPath = TENANT_MEDIA_FOLDER . "/apps/$requestObj->appKey/";

if(file_exists($appPath)){
    $descriptor = file_get_contents(TENANT_MEDIA_FOLDER. "/apps/$requestObj->appKey/descriptor.json");
    $appType =  ((json_decode($descriptor)->type));

    if (strcmp($appType, "APPBUNDLE") ==0 ) {
        $bundledapps = json_decode(file_get_contents(TENANT_MEDIA_FOLDER . "/apps/$requestObj->appKey/resources/bundle.json"));

        foreach ($bundledapps as $bapp=>$bappV){
            $currentHeaders = apache_request_headers();
            $forwardHeaders = array("Host: ". DuoWorldCommon::GetHost(), "Content-Type: application/json");
            
            foreach ($currentHeaders as $key => $value)
                if (!(strcmp(strtolower($key), "host") ===0 || strcmp(strtolower($key),"content-type")===0))
                    array_push($forwardHeaders, "$key : $value");

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ". DuoWorldCommon::GetHost() , "AppKey: $bapp")); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders); 
            //curl_setopt($ch, CURLOPT_URL, "http://". $_SERVER["HTTP_HOST"] . "/apps/$bapp?install=$requestObj->getParams->install");
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ". (strcmp($_SERVER["HTTP_HOST"], DuoWorldCommon::GetHost()) ? "localhost" : DuoWorldCommon::GetHost()) , "AppKey: $bapp")); 
            curl_setopt($ch, CURLOPT_URL, "http://localhost/apps/$bapp?install=". $requestObj->getParams->install);
            $data = curl_exec($ch);

            $postResponse = new stdClass();
            $postResponse->server = $requestObj->getParams->install;
            $postResponse->response = json_decode($data);
            array_push($logObj->log, $postResponse);

            curl_close($ch);
        }
    }


    $tempFolder = TENANT_MEDIA_FOLDER . "/installtemp/tempuser/";
    if (!file_exists($tempFolder))
        mkdir($tempFolder, 0777, true);
    
    zip($appPath, "$tempFolder$requestObj->appKey.zip",$logObj);
    postZip("$tempFolder$requestObj->appKey.zip",$requestObj->appKey, $requestObj->getParams->install,$logObj);
    //exit();
    //unlink("$tempFolder$requestObj->appKey.zip");
}


header ("Content-type: application/json");
echo json_encode($logObj, JSON_PRETTY_PRINT);

?>
