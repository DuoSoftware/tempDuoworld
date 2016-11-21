<?php

	header('Content-Type: application/json');
	$perfFile;

	$authObj = json_decode($_COOKIE["authData"]);
	$username = $authObj->Username;

	isDevTenant();
	
	if (isAdminUser($username))
		$perfFile = TENANT_MEDIA_FOLDER . "/apppermisson/all.json";
	else {
		$perfFile = (isGlobalMode() ?  USER_MEDIA_FOLDER : TENANT_MEDIA_FOLDER) . "/apppermisson/$username.json";

		if (!file_exists($perfFile)){
			if (isDevTenant()) $perfFile = "data/default.dev.json";
			else $perfFile = "data/default.json";
		}
	}
	
	$contents =  json_decode(file_get_contents($perfFile));

	$allApps = array();
	foreach ($contents as $k)
	if ($k!==null){
	    array_push($allApps, $k);
	}

	if (defined(PAYMENT_KEY)){
		require_once ("./paymentmanager.php");
		 
		$payMan = new AppPaymentManager();
		$subscribedAppList = $payMan->GetSubscribedApps();
		$appsToBeChecked = array();

		foreach ($allApps as $app){
			$appId = $app->ApplicationID;
			if (!isset($subscribedAppList->$appId))
				array_push($appsToBeChecked, $appId);
		}

		$appsToBeDisbled = $payMan->FilterPaidApps($appsToBeChecked);

		foreach ($allApps as $app){
			$appId = $app->ApplicationID;
			if (isset($appsToBeDisbled->$appId))
				$app->disabled = true;
		}
	}

	if (file_exists("./functions/allapps_filter.php")){
		require_once("./functions/allapps_filter.php");
		$allApps = filter_apps($allApps);
	}

	echo json_encode($allApps);
?>