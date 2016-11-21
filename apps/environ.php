<?php

if (!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        foreach($_SERVER as $key=>$value) {
            if (substr($key,0,5)=="HTTP_") {
                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                $out[$key]=$value;
            }else{
                $out[$key]=$value;
            }
        }
        return $out;
    }
} 

function checkPermissionToOpen($appKey){

    if (!defined(PAYMENT_KEY))
        return true;

    if (!isset($_SESSION))
        session_start();

    if (isset($_SESSION["APP_VERIFIED_$appKey"]))
        return true;
    
    $isPermitted = true;
    if (isset($_SESSION["APP_EXPIRED_$appKey"]))
        $isPermitted = false;
    else{
        require_once("./paymentmanager.php");
        $payman = new AppPaymentManager();
        
        $isPermitted = $payman->VerifySubscription(DuoWorldCommon::GetHost(), $appKey);
    
        if ($isPermitted)
            $_SESSION["APP_VERIFIED_$appKey"] = true;
        else 
            $_SESSION["APP_EXPIRED_$appKey"] = true;
    }

    if (!$isPermitted){
        include ("./pages/expired.php");
        exit();
    }    

    return $isPermitted;
}

function checkPermissionToUninstall($appKey){

    if (isCoreApp($appKey)){
        sendJsonResponse("Cannot uninstall a core app", false, 401);
        exit();
    }

	$authObj = json_decode($_COOKIE["authData"]);
	$username = $authObj->Username;
    if (!isAdminUser($username)){
        sendJsonResponse("You do not have admin priviledges to uninstall the app : $appKey", false, 401);
        exit();        
    }

    if (!defined(PAYMENT_KEY))
        return true;
        
    require_once("./paymentmanager.php");
    $payman = new AppPaymentManager();
    $isPayAuthorized = $payman->VerifyUninstall(DuoWorldCommon::GetHost(), $appKey);

    $isPermitted = $isPayAuthorized;

    if (!$isPermitted){
        sendJsonResponse("You are not authorized to uninstall the app : $appKey", false, 401);
        exit();
    }

    return $isPermitted;
    
}

function checkPermissionToShare($appKey){
    $isPermitted = true;

	$authObj = json_decode($_COOKIE["authData"]);
	$username = $authObj->Username;
    $isPermitted = isAdminUser($username);
    
    if (!$isPermitted){
        sendJsonResponse("You are not authorized to share the app : $appKey", false, 401);
        exit();
    }

    return true;
}

function checkPermissionToInstall($tenantid, $appKey){

	$authObj = json_decode($_COOKIE["authData"]);
	$username = $authObj->Username;
    
    /*
    if (!isAdminUser($username)){
        sendJsonResponse("You do not have admin priviledges to install the app : $appKey", false, 401);
        exit();        
    }
    */

    if (!defined(PAYMENT_KEY))
        return true;
    
    $hasPermissionToInstall = true;
    $isMainDomain = true; 

    if (strcmp(MAIN_DOMAIN, DuoWorldCommon::GetHost()) !=0)
        $isMainDomain = false;

    if (isCoreApp($tenantid, $appKey))
        return true;

    require_once("./paymentmanager.php");
    $payman = new AppPaymentManager();
    $isPayAuthorized = $payman->VerifyInstall($tenantid, $appKey);

    $isPermitted = $isPayAuthorized && $isMainDomain && $hasPermissionToInstall;

    if (!$isPermitted){
        sendJsonResponse("You are not authorized to install the app : $appKey", false, 401);
        exit();
    }

    return true;
}

function isCoreApp($tenantid, $appKey){
    $permFile = $_SERVER["DOCUMENT_ROOT"]. "/apis/usertenant/appcodes.json";
    if (file_exists($permFile)){
        $coreAppCodes = file_get_contents($permFile);
        $coreAppObj = json_decode($coreAppCodes);
        
        if (is_object($coreAppObj)){
            $key;
            if (isDevTenant($tenantid)) $key = "Developer";
            else $key = "Company";

            foreach ($coreAppObj->$key as $coreApp) 
            if (strcmp($coreApp, $appKey) ==0) return true;
        }
    } else return true;

    return false;
}

function isScopeChanged($appKey, $jwtScopeObj){
    $scopeFile = TENANT_MEDIA_FOLDER . "/apps/$appKey/scope.json";
    $scopeObj = json_decode(file_get_contents($scopeFile));

    $dataArray = $scopeObj->scope->data;
    $funcArray = $scopeObj->scope->functions;
    $jwtDataArray = $jwtScopeObj->scope->data;
    $jwtFuncArray = $jwtScopeObj->scope->functions;

    if (!is_array($jwtDataArray)) $jwtDataArray = json_decode($jwtDataArray);
    if (!is_array($jwtFuncArray)) $jwtFuncArray = json_decode($jwtFuncArray);
    /*
    var_dump($dataArray);
    var_dump($funcArray);
    var_dump($jwtDataArray);
    var_dump($jwtFuncArray);
    exit();
    */
    $diffData = array_merge(array_diff($dataArray, $jwtDataArray), array_diff($jwtDataArray, $dataArray));
    $diffFunc  = array_merge(array_diff($funcArray, $jwtFuncArray), array_diff($jwtFuncArray, $funcArray));

    if (sizeof($diffData) > 0 || sizeof($diffFunc) > 0) return true;
    else return false;
}

function checkPermissionForResource($appKey){

    if (defined("APP_DISABLE_JWT"))
        if (APP_DISABLE_JWT == true)
            return true;

    $isPermitted = false;
    $canShowPrompt = false;
    $jwtData;

    if (!isset($_SESSION))
        session_start();

    if (isset($_SESSION["APP_AUTHORIZED_$appKey"])){
        $isPermitted = true;    
    } else {
        
          $str=AuthQuery("/Authorize/$_COOKIE[securityToken]/$appKey",[]);
          $obj=json_decode($str);

          if(isset($obj)){
            if (isset($obj->Error)) $canShowPrompt = true;
            else{
                
                $jwtData = $obj->Otherdata->JWT;    
                $splitData = explode(".", $jwtData);
                $jwtScopeObj = json_decode(base64_decode($splitData[1]));

                if (isScopeChanged($appKey, $jwtScopeObj)) $canShowPrompt = true;
                else {
                    setcookie("APP_JWT_$appKey", $jwtData, time() + (60 * 60 * 24), "/apps/$appKey/");
                    $_SESSION["APP_AUTHORIZED_$appKey"] = true;
                    $isPermitted = true;
                }
            }
          }else $canShowPrompt = true;
    }
	
    if ($canShowPrompt){
        include ("./oauth/prompt.php");
        exit();
    }

	if (!$isPermitted) {
        sendJsonResponse("Application Unauthorized (403)", false, 403);
		exit();
	}
}	


function extractAllParams($requestObj){
    foreach ($_GET as $pName=>$getParamValue){
        $param = $_GET[$pName];
        if (!isset($requestObj->getParams))
            $requestObj->getParams = new stdClass();

        $requestObj->getParams->$pName = $param;
        $requestObj->relativePath = str_replace("?$pName=$param",'',$requestObj->relativePath);
    }
}

function serveByGetParam($requestObj){

    $servePages = array("meta"=>"meta.php", 
        "scopeapps"=>"scopeapps.php", 
        "ins"=>"remoteinstall.php", 
        "install"=>"remoteinstall.php", 
        "share"=>"shares.php", 
        "unshare"=>"shares.php", 
        "uninst"=>"uninstall.php",
        "uninstall"=>"uninstall.php"
    );
    
    foreach($requestObj->getParams as $param=>$pValue)
    if (isset($servePages[$param])){
        require_once("functions/" . $servePages[$param]);
        return true;
    }

    sendJsonResponse("Unknown Operation",false,401);
    exit();
}

function sendJsonResponse($message, $succes=true, $code=200){
    $succes = $succes ? "true" : "false";
    echo "{\"success\":$succes, \"message\": \"$message\"}";
    header ("Content-Type: application/json");
    if ($code!=200)
        http_response_code($code);
}

function AuthQuery($authrequest,$postData){
    
    $headers = apache_request_headers();
    
    if(isset($_COOKIE["authData"])){
        $authData = json_decode($_COOKIE["authData"]);
        $headers["securityToken"] = $authData->SecurityToken;;
    }

    $sendHeaders = array();
    foreach ($headers as $hK=>$hV) array_push($sendHeaders, "$hK: $hV");
    
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER,$sendHeaders);
    curl_setopt($ch, CURLOPT_URL, SVC_AUTH_URL.$authrequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_ENCODING, '');

    if(count($postData)!=0){
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }

    $data = curl_exec($ch);
    return $data;
}

function isAdminUser($username){
    
    $authObj;
    if (isset($_SESSION["USER_TENANT_AUTH_$username"])){
        $authObj = json_decode($_SESSION["USER_TENANT_AUTH_$username"]);
    }else{
        $str=AuthQuery("/tenant/Autherized/".DuoWorldCommon::GetHost() ,[]);
        $authObj = json_decode($str);
        $_SESSION["USER_TENANT_AUTH_$username"] = $str;
    }

    if (isset($authObj))
        if (isset($authObj->SecurityLevel))
            if (strcmp(strtolower($authObj->SecurityLevel),"admin") == 0)
                return true;
    
    return false;
}

function isDevTenant(){   
    /*
    if (isset($_SESSION["TENANT_OBJECT"])){
        $tenObj = json_decode($_SESSION["TENANT_OBJECT"]);
    }else{
        $str=AuthQuery("/tenant/GetTenant/".DuoWorldCommon::GetHost() ,[]);
        $tenObj = json_decode($str);
        $_SESSION["TENANT_OBJECT"] = $str;
    }
    */
    return strpos($_SERVER["HTTP_HOST"], '.dev.') !== false;
}

function isGlobalMode(){
    if (defined ("APPS_GLOBAL_MODE"))
        return APPS_GLOBAL_MODE;
    return false;
}

?>