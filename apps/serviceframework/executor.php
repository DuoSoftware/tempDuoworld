<?php
require_once ("./serviceframework/common.php");

class ServiceExecutor {

	private function handleServiceError($errno, $errstr, $errfile, $errline){
		$message = new stdClass();
		$message->no = $errno;
		$message->message = $errstr;
		$message->file = $errfile;
		$message->line = $errline;

		echo json_encode(DwFramework::ReturnMessage($message, false),JSON_PRETTY_PRINT);
		exit();
	}

	private function handleServiceException($exception){
		echo json_encode(DwFramework::ReturnMessage($exception->getMessage(), false),JSON_PRETTY_PRINT);
		exit();
	}

	private function getPathInfo($exeInfo){
		require_once(ROOT_PATH . "/payapi/duoapi/objectstoreproxy.php");

		$client = ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),"projectsettings","123");
		$desc = $client->get()->byKey($exeInfo->appKey);
		
		$defVal;
		if (isset($desc->data))
			if (isset($desc->data->services))
				$defVal = $desc->data->services;

		if (!isset($defVal))
			$defVal = array();

		return $defVal;
	}
	
	private function getExecutionInfo(){
		$currentPath = str_replace(str_replace("\\","/", strtolower($_SERVER["DOCUMENT_ROOT"])), "", str_replace("\\","/", strtolower(dirname(__FILE__))));
		$currentPath = str_replace("/serviceframework","" , $currentPath);  

		$relativeUrl = str_replace($currentPath, "", $_SERVER["REQUEST_URI"]);
		if ($relativeUrl[0] === '/' && $relativeUrl[1] === '/')
			$relativeUrl = substr($relativeUrl,1);

		$parts = explode("/", $relativeUrl);

		if (sizeof($parts) > 1){
			$appKey = $parts[1];
			$rest = str_replace("/$appKey", "", $relativeUrl);

			$outData = new stdClass();
			$outData->appKey = trim($appKey);
			$outData->rest = trim($rest);
			$outData->fullPath = $relativeUrl;
			return $outData;
		}
	}

	function executePhpService($exeInfo, $pathInfo, $vars){
		$istrue = false;
		$fileToInclude = TENANT_MEDIA_FOLDER . "/apps/$exeInfo->appKey/resources". ($pathInfo->phpFile[0] == "/" ? $pathInfo->phpFile : "/". $pathInfo->phpFile);
		if (file_exists($fileToInclude)){
			require_once(ROOT_PATH . "/payapi/duoapi/objectstoreproxy.php");
			require_once(ROOT_PATH . "/payapi/duoapi/cebproxy.php");
			require_once(ROOT_PATH . "/apps/serviceframework/common.php");

            set_error_handler(array($this, 'handleServiceError'));
            set_exception_handler(array($this, 'handleServiceException'));

			require_once ($fileToInclude);

			$methodInfo = explode(".", $pathInfo->function);

			if (sizeof($methodInfo) == 2){
				$serviceObj = new $methodInfo[0]();
				header ("Content-type: application/json");
				$outData = call_user_func_array(array($serviceObj, $methodInfo[1]), $vars);
				if (!isset($outData))
					$outData = null;
				
				echo json_encode($outData, JSON_PRETTY_PRINT);
				$istrue = true;
			} else sendJsonResponse("unable to execute method in the php file", false, 500);					
		}else sendJsonResponse("unable to execute service ($fileToInclude not found on server)", false, 500);
		
		return $istrue;
	}

	private function getVars($exeInfo, $allPaths){
			$validPath;
			$validVars;

			foreach ($allPaths as $path)
			if (strcmp($path->method, $_SERVER["REQUEST_METHOD"]) ==0){
				$pathParts = explode("/", $path->path);
				$currentParts = explode("/", $exeInfo->rest);

				if (sizeof($pathParts) != sizeof($currentParts))
					continue;

				$isValid = true;
				$variables = array();
				$foundVariable = false;
				$foundConstant = false;

				for ($i=1;$i<sizeof($pathParts);$i++){
					$pathPart = $pathParts[$i];

					if ($pathPart[0] == '@' || $pathPart[0] == '$'){
						$foundVariable = true;

						if ($foundConstant) {
							//$variable = array(substr($pathPart, 1) => $currentParts[$i]);
							$variable = $currentParts[$i];
							array_push($variables, $variable);
						} else {
							$isValid = false;
							break;
						}

					} else {
						$foundConstant = true;

						if (strcmp($pathParts[$i], $currentParts[$i]) !=0){
							$isValid = false;
							break;
						}
					}
				}

				if ($isValid){
					$validPath = $path;
					$validVars = $variables;
					break;
				}
			}

		$outData = new stdClass();
		$outData->validPath = $validPath;
		$outData->validVars = $validVars;

		return $outData;
	}

	public function Execute(){
		$exeInfo = $this->getExecutionInfo();
		$allPaths = $this->getPathInfo($exeInfo);
		$vars = $this->getVars($exeInfo, $allPaths);
		$istrue = $this->executePhpService($exeInfo, $vars->validPath, $vars->validVars);;

		if (!$istrue) exit();

		return $istrue;
	}
}

?>