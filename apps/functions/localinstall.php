<?php


	function recurse_rmdir($dir) {
	  $files = array_diff(scandir($dir), array('.','..'));
	  foreach ($files as $file) {
	    (is_dir("$dir/$file")) ? recurse_rmdir("$dir/$file") : unlink("$dir/$file");
	  }
	  return rmdir($dir);
	}

	function rmove($src, $dest){
	    if(!is_dir($src)) return false;

	    if(!is_dir($dest))
	    	if(!mkdir($dest)) return false;
	        
	    $i = new DirectoryIterator($src);
	    foreach($i as $f) {
	        if($f->isFile()) {
	            rename($f->getRealPath(), "$dest/" . $f->getFilename());
	        } else if(!$f->isDot() && $f->isDir()) {
	            rmove($f->getRealPath(), "$dest/$f");
	            unlink($f->getRealPath());
	        }
	    }
	    unlink($src);
	}

	function extractContents($requestObj, $logObj){
	
		$folder = TENANT_MEDIA_FOLDER . "/apps/". $requestObj->appKey;

		if (file_exists($folder))
			recurse_rmdir($folder);
		mkdir($folder, 0777, true);

		$zipFile = "$folder/publish.zip";
		$entityBody = file_get_contents('php://input');
		file_put_contents($zipFile, $entityBody);

		$zip = new ZipArchive;
		$res = $zip->open($zipFile);
		if ($res === TRUE) {
		    $zip->extractTo($folder);
		    $zip->close();
		    array_push($logObj->log, "Successfully extracted ZIP contents");
		} else {
			array_push($logObj->log, "Extracting ZIP contents Failed!!");
			$logObj->success = false;
		}

		unlink($zipFile);
	}


	function addToRegistry($requestObj, $logObj){
		$appKey = $requestObj->appKey;
		$folder = TENANT_MEDIA_FOLDER . "/apps/". $appKey;

		$perfFolder = TENANT_MEDIA_FOLDER . "/apppermisson/";
		if (!file_exists($perfFolder))
			mkdir($perfFolder, 0777, true);

		$perfFile = "$perfFolder/all.json";
		$perfContents;
		if (!file_exists($perfFile))
			$perfContents = new stdClass();
		else
			$perfContents = json_decode(file_get_contents($perfFile));

		if (isset($perfContents->$appKey))
			unset($perfContents->$appKey);

		$appFile = "$folder/app.json";
		$appObj = json_decode(file_get_contents($appFile));
		if (isset($appObj->secretKey))
			unset($appObj->secretKey);

		$perfContents->$appKey = $appObj;
		file_put_contents($perfFile, json_encode($perfContents));

		//new contents
		if (isset($_COOKIE["authData"])){
			$authData = json_decode($_COOKIE["authData"]);
			$userFile = "$perfFolder/" . $authData->Username . ".json";
			$userPerfFile = file_exists($userFile) ? $userFile : (strpos($perfFile, '.dev.') !== false ? "data/default.dev.json" : "data/default.json");
			$perfContents =  json_decode(file_get_contents($userPerfFile));
		
           	if (isset($perfContents->$appKey))
	                unset($perfContents->$appKey);

			$perfContents->$appKey = $appObj;
			file_put_contents($userFile, json_encode($perfContents));
		}

	}

	function addToObjectStore($requestObj, $logObj){
		require_once($_SERVER["DOCUMENT_ROOT"]. "/payapi/duoapi/objectstoreproxy.php");
		$appFile = TENANT_MEDIA_FOLDER . "/apps/". $requestObj->appKey . "/app.json";
		$appObj = json_decode(file_get_contents($appFile));
		$client = ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),"application","123");
		$res = $client->store()->byKeyField("ApplicationID")->andStore($appObj);
		
		array_push($logObj->log, $res);
	}

	function sendNotification($requestObj){
		require_once($_SERVER["DOCUMENT_ROOT"]. "/payapi/duoapi/cebproxy.php");

		$authObj = json_decode($_COOKIE["authData"]);
		$username = $authObj->Username;

		$cebProxy = new CebProxy($_COOKIE["securityToken"]);

		$emailObj = new stdClass();
		$emailObj->type = "email";
		$emailObj->to = $username;
		$emailObj->subject = "DuoWorld App Purchase";
		$emailObj->from = "DuoWorld <noreply@duoworld.com>";
		$emailObj->Namespace = DuoWorldCommon::GetHost();
		$emailObj->TemplateID = "T_Email_APPINSTALLATION";
		$emailObj->DefaultParams = array("@@CNAME@@"=> $username, "@@APPNAME@@"=> $requestObj->appKey);
		$emailObj->CustomParams = array("@@CNAME@@"=> $username, "@@APPNAME@@"=> $requestObj->appKey);

		$cebProxy->InvokeCommand("notification",$emailObj);

		/*
		Endpoint : http://admin.dev.duoworld.com:3500/command/notification
		Sample request body:
		{
		"type":"email",
		"to":"supun@duosoftware.com",
		"subject":"App Installation",
		"from":"DuoWorld <noreply@duoworld.com>",
		"Namespace": "com.duosoftware.com",
		"TemplateID": "T_Email_APPINSTALLATION",
		"DefaultParams":
		{ "@@CNAME@@": "Supun", "@@APPNAME@@": "Digin"	}
		,
		"CustomParams":
		{ "@@CNAME@@": "Supun", "@@APPNAME@@": "Digin"	}
		}
		*/

	}

	$logObj = new stdClass();
	$logObj->log = array();
	$logObj->success = true;

	extractContents($requestObj, $logObj);
	addToRegistry($requestObj, $logObj);
	addToObjectStore($requestObj, $logObj);
	sendNotification($requestObj);

	echo json_encode($logObj);
?>
