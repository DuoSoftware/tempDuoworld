<?php

    //ini_set('display_errors', 1); 
    //error_reporting(E_ALL);

	require_once($_SERVER["DOCUMENT_ROOT"]. "/dwcommon.php");
	require_once($_SERVER["DOCUMENT_ROOT"]. "/include/config.php");

    if (!defined("STORAGE_PROFILE"))
        define("STORAGE_PROFILE", "STORAGE-AUTH");

    if (strcmp(STORAGE_PROFILE, "PROXY") == 0) require_once("../forward.php");

	require_once($_SERVER["DOCUMENT_ROOT"]. "/apps/environ.php");
    require_once($_SERVER["DOCUMENT_ROOT"]. "/payapi/duoapi/objectstoreproxy.php");


	define ("TENANT_MEDIA_FOLDER", MEDIA_PATH . "/" . DuoWorldCommon::GetHost());

	function decrypt($encrypted_string, $encryption_key) {
	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
	    return $decrypted_string;
	}

    
    $client = ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),"appapprovals","123");
    $authObj = json_decode($_COOKIE["authData"]);
    $approveObj = $client->get()->byKey($authObj->Username);
    $appKey = $approveObj->appKey;
	$appIconUrl = "http://$mainDomain/apps/$appKey?meta=icon";
	
    $appFile = TENANT_MEDIA_FOLDER . "/apps/$appKey/app.json";
    $appObj =  json_decode(file_get_contents($appFile));

    //echo $approveObj->authCode;
    $authUrl = "/AutherizeApp/$_COOKIE[securityToken]/" . $approveObj->authCode. "/$appKey/". $appObj->SecretKey;

    $sendScope = new stdClass();
    $sendScope->Object = new stdClass();

    $sendScope->Object->data = json_encode($approveObj->scope->scope->data);
    $sendScope->Object->functions = json_encode($approveObj->scope->scope->functions);

    $str=AuthQuery($authUrl,json_encode($sendScope));


    if (strcmp($str, "true") == 0) {
        if (!isset($_SESSION))session_start();
        $_SESSION["APP_AUTHORIZED_$appKey"] = true;
        header("Location: $_GET[r]");
        exit();
    }
?>
<html>
    <head>
        <title>Error approving application</title>
        <script src="/apps/oauth/angular.min.js" type="text/javascript"> </script>

        <script type="text/javascript">
            angular.module("mainApp",[]).controller("mainController", function($scope){
                $scope.errorMsg = <?php echo "'$str';"; ?>
                $scope.retry = function(){
                    location.href = <?php echo "'$_GET[r]';"; ?>
                }
            });
        </script>
    </head>
    
    <body ng-app="mainApp" ng-controller="mainController">
        <h1>Error granting permission for application {{errorMsg}}</h1>
		<img src="<?php echo $appIconUrl;?>"/>
        <div>
            <input type="button" value = "Retry" ng-click="retry()"/>
        </div>
    </body>
</html>