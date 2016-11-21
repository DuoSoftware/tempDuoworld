<?php
	if (sizeof($splitParts) >= 3){
		$splitParts[3] = $splitParts[2];
		$splitParts[2] = DuoWorldCommon::GetHost();

		$namespace = $splitParts[2];
		$class = $splitParts[3];
		
		$requestMapping = array ("r" => "GET", "w" => "POST", "d" => "DELETE");
		function validateAllowWithRequestType($allow){
			
			for ($i=0; $i<strlen($allow); $i++) {
				$mappedRequest = $allow[$i];
				if (isset($mappedRequest))
				if (strcmp ($_SERVER["REQUEST_METHOD"], $requestMapping[$mappedRequest]) ==0){
					return true;
				}
			}

			return false;
		}

		function validateDataScope($appKey, $class){
			$saveClass = null;
			if (isset($_COOKIE["APP_JWT_$appKey"])){
				$splitData = explode(".", $_COOKIE["APP_JWT_$appKey"]);
				$scopeObj = json_decode(base64_decode($splitData[1]));
				
				if (isset($scopeObj->scope))
				if (isset($scopeObj->scope->data)){

					for ($i=0;$i<sizeof($scopeObj->scope->data);$i++) {
						$classData = $scopeObj->scope->data[$i];
						
						if (is_object($classData)){
							$allVars = get_object_vars($classData);
							if (sizeof ($allVars) ==1){
								$tmpClass = array_keys($allVars)[0];

								if (strcmp ($tmpClass, $class) == 0){
									$classInfoObj = $classData->$tmpClass; 
									$isAllowed = true;

									if (isset($classInfoObj->allow)) //parent level allowing
										$isAllowed = validateAllowWithRequestType($classInfoObj->allow);
									
									if (isset($classInfoObj->allowIds)){ //child level allowing
										
									}

									if (!$isAllowed){
										sendJsonResponse("Access denied in JWT token", false, 403);
										exit();
									}

									if (isset ($classInfoObj->localClass)) $saveClass = $classInfoObj->localClass;
									else $saveClass = $tmpClass;
									
									break;
								}
							}
						}else if (is_string($classData)){
							if (strcmp ($classData, $class) == 0){
									$saveClass = $class;
									break;
							}
						}
					}
				}
			}
			return $saveClass;
		}

		$class = validateDataScope($requestObj->appKey, $class); 
		if (isset($class)){
			define ("STORAGE_REL_URL", str_replace($relativePath,"",$_SERVER["REQUEST_URI"]) . "/data/");
			require_once($_SERVER["DOCUMENT_ROOT"]. "/data/DataManager.php");

			$dataMan = new DataManager();
			$dataMan->SetNamespaceClass($namespace, $class);
			$response = $dataMan->Process();
			
			echo is_string($response) ? $response : json_encode($response);
			
		}else
			sendJsonResponse("Application not authorized to access this class", false, 403);
	}else{
		sendJsonResponse("Insufficient parameters passed to data service", false, 401);
	}
	
	$isOk = true;

?>