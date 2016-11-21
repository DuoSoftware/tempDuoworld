<?php
	if (!function_exists('getallheaders')) {
	    function getallheaders() {
	    $headers = [];
	    foreach ($_SERVER as $name => $value) {
	        if (substr($name, 0, 5) == 'HTTP_') {
	            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
	        }
	    }
	    return $headers;
	    }
	}

	class DataManager {

		private $headers;
		private $securityToken;
		private $reqBody;

		private $namespace;
		private $class;


		private function getNamespaceClass(){
			$relativeUrl = str_replace(STORAGE_REL_URL,'',$_SERVER["REQUEST_URI"]);
			$urlPaths;
			if (strpos($relativeUrl, "?"))
				$urlPaths = substr($relativeUrl, 0, strpos($relativeUrl, "?"));
			else
				$urlPaths = $relativeUrl;

			$parts = explode('/', $urlPaths);

			switch(sizeof($parts)){
				case 1:
					$this->class = $parts[0];
					$this->namespace = DuoWorldCommon::GetHost();
					break;
				case 2:
					if (strpos($parts[0], ".")){
						$this->class = $parts[1];
						$this->namespace = $parts[0];
					}else{
						$this->class = $parts[0];
						$this->namespace = DuoWorldCommon::GetHost();
					}
					break;
				case 3:
					$this->class = $parts[1];
					$this->namespace = $parts[0];
					break;
				default:
					break;
			}

			return NULL;
		}

		private function getBody() {
		    $rawInput = fopen('php://input', 'r');
		    $tempStream = fopen('php://temp', 'r+');
		    stream_copy_to_stream($rawInput, $tempStream);
		    rewind($tempStream);
			$bodyData = stream_get_contents($tempStream);

			if (isset($this->headers["isEncrypted"]))
			if ($this->headers["isEncrypted"] === "true"){
				$bodyData = base64_decode($bodyData);
			}
				

		    return $bodyData;
		}

		private function checkPermission(){

			$allowed = false;
			if (isset($this->headers["securityToken"])) {$this->securityToken=$this->headers["securityToken"]; $allowed = true; }
			if (!isset($this->securityToken)) if(isset($_COOKIE["securityToken"])) {$this->securityToken=$_COOKIE["securityToken"]; $allowed = true;}
			if (!isset($this->securityToken)) if(isset($_GET["securityToken"])) {$this->securityToken=$_GET["securityToken"]; $allowed = true;}
			
			if ($allowed){
				//check security token
				if (!$allowed) return "SecurityToken not valid or expired";
 				if (strcmp(DuoWorldCommon::GetHost(),$this->namespace)==0) return ;
 				/*
				$authData = json_decode($_COOKIE["authData"]);
				$hosts = json_decode($authData->Otherdata->TenentsAccessible);
				foreach ($hosts as $host){
					if (strcmp($this->namespace, $host->TenantID) == 0)
						return;
				}
				*/
				return "User not authorized to access the tenant data";

			} else return "User not authenticated to use data services";
			
		}

		private function getResponse($isSuccess, $errorLog, $resultObj){
			$res = new stdClass();

			$res->success = $isSuccess;
			if (isset($errorLog)) $res->errorLog = $errorLog;
			if (isset($resultObj)) $res->result = $resultObj;
			
			return $res;
		}

		public function Process(){
			$isSuccess = true;
			$result;
			$errorLog = $arrayName = array();

			$this->headers = getallheaders();

			if (!(isset($this->namespace) && isset($this->class))){
				if ($this->getNamespaceClass() !== null)
					{array_push($errorLog, "Unable to determine namespace and class"); $isSuccess = false; return $this->getResponse($isSuccess, $errorLog, NULL);}
			}

			$permission = $this->checkPermission();
			if (isset($permission))
				{array_push($errorLog, $permission); $isSuccess = false; return $this->getResponse($isSuccess, $errorLog, NULL);}				

			if($_SERVER["REQUEST_METHOD"]!="GET") $this->reqBody = $this->getBody();

			require_once(ROOT_PATH . "/data/StorageManager.php");
			$storageManager = new StorageManager($this->securityToken, $this->namespace, $this->class, $this->headers, $this->reqBody);
			return $storageManager->Store();
		}

		public function SetNamespaceClass($namespace, $class){
			$this->namespace = $namespace;
			$this->class = $class;
		}

		function __construct(){
			header('Content-Type: application/json');
		}
	}
?>