<?php
	require_once(ROOT_PATH . "/data/SchemaValidator.php");
	
	class StorageManager{

		private $namespace;
		private $class;
		private $headers;
		private $securityToken;
		private $reqBody;

		public function Store(){

			$canRate = false;
			
			if($_SERVER["REQUEST_METHOD"]=="POST") {
				$validateRes = $this->validatePost();
				if (isset($validateRes)) return $validateRes;
				$canRate = true;
			} else if($_SERVER["REQUEST_METHOD"]=="DELETE") {
				$this->validateDelete();
				$canRate = true;
			}
			

			if (defined("ENABLE_RATING") && $canRate){
				require_once(ROOT_PATH. "/payapi/duoapi/ratingengine/ratingengine.php");
				$rateEngine = new RatingEngine();
				$bodySize = strlen($this->reqBody);
				if ($rateEngine->ValidateStorage($this->namespace, $this->class, $bodySize))
					$rateEngine->RateStorage($this->namespace, $this->class, $bodySize);
			}

			if (strcmp(STORAGE_MECHANISM,"OS") == 0) {
            	$whatToSend;

            	if (isset($_GET["searchAdd"])) $whatToSend  = true;
            	else if (isset($_GET["searchRemove"])) $whatToSend  = false;

            	$osRes = $this->callOs();

            	if (isset($whatToSend)){
            		$this->sendToSearchService($whatToSend);
            	}
				
				return $osRes;
			}
			else return $this->callBigQuery();
		}

		private function validateDelete(){
			$req = json_decode($this->reqBody);
			if (!isset($req)){
				$req = new stdClass();
				$req->Object = new stdClass();
				$req->Object->ID = $this->reqBody;
				$req->Paramters = new stdClass();
				$req->Paramters->KeyProperty = "ID";
				$this->reqBody = json_encode($req);
			} else {
				if (!((isset($req->Objects) || isset($req->Object)) && isset($req->Parameters))) {
					$obj = $req;
					$req = new stdClass();
					$req->Object = $obj;
					$validator = new SchemaValidator();

					$keyField = $validator->GetKeyField("common", $this->class);
					
					if (isset($keyField)){
						$req->Parameters = new stdClass();
						$req->Parameters->KeyProperty = $keyField;
					}
					
					$this->reqBody = json_encode($req);

				}
			}
		}

		private function validatePost(){
			$validator = new SchemaValidator();
			$result = $validator->validate("common", $this->class, $this->reqBody);
			
			if ($result->success) {
				$this->reqBody = $result->result;
				return NULL;
			}
			else 
				return '{"success":false, "errorLog":' . json_encode($result->errorLog) . ', "errorCode" : 0, "errorMessage": "Validation Error"}';
		}

		private function callOs(){
			$relativeUrl = str_replace(STORAGE_REL_URL,'',$_SERVER["REQUEST_URI"]);
			$parts = explode('/', $relativeUrl);

			switch(sizeof($parts)){
				case 1:
					$relativeUrl = $this->namespace . "/" . $relativeUrl;	
					break;
				case 2:
					if (!strpos($parts[0], "."))$relativeUrl = $this->namespace . "/" . $relativeUrl;	
					break;
			}
			
			$log;

			if(isset($this->headers["log"])) $log=$this->headers["log"];

			//echo SVC_OS_URL ."/" .$relativeUrl;

			$ch=curl_init();
		  	$headerArray = array('securityToken:'.$this->securityToken);
		  	if (isset($log)) array_push($headerArray, 'log:' . $log);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
			curl_setopt($ch, CURLOPT_URL, SVC_OS_URL . "/". $relativeUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER["REQUEST_METHOD"]);

			if($_SERVER["REQUEST_METHOD"]!="GET"){
				curl_setopt($ch, CURLOPT_POST, count($this->reqBody));
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->reqBody);
			}

			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			$result = new stdClass();

			if ($httpcode == 500){
				$result->success = false;
				$result->errorCode ="0";
				$resObj = json_decode($data);

				if(!isset($resObj)){
					$result->errorLog = array($data);	
					$result->errorMessage = "Unknown Internal Server Error";
				}else{
					$result->errorLog = $resObj->Stack;	
					$result->errorMessage = $resObj->Message;
				}

				$result = json_encode($result);

			} else {
				if ($data[0] == '[' || $data[0] =='{') $result = '{"success":true, "result":'. $data . '}';
				else $result = '{"success":false, "errorLog":[], "errorCode" : 0, "errorMessage": "'. $data . '"}'; 				
			}

			return $result;
		}

		private function callBigQuery(){
			$result = new stdClass();
			$result->success = false;
			$result->errorLog = array("BigQuery Not Implemented!!!!");
			return $result;
		}

		private function sendToSearchService($isAdd){
			require_once (ROOT_PATH .'/payapi/duoapi/objectstoreproxy.php');
			require_once(ROOT_PATH . "/apis/search/search.php");
			
			$searchObj = new searchService(false);
			
			$res;

			$encObj = json_decode($this->reqBody);

			$propVaue = $encObj->Parameters->KeyProperty;

			$addObj = new stdClass();
			$addObj->id = $encObj->Object->$propVaue;
			$addObj->class = $this->class;
			$addObj->object = $encObj->Object;
			$addObj->tags = [];

			if ($isAdd) $res = $searchObj->addObject($addObj, "search" . $this->namespace);
			else $res = $searchObj->removeObject($addObj, "search" . $this->namespace);

			return $res;
		}

		function __construct($secToken, $namespace, $class, $headers, $reqBody){
			$this->securityToken = $secToken;
			$this->namespace = $namespace;
			$this->class = $class;
			$this->headers = $headers;
			$this->reqBody = $reqBody;
		}
	}
?>
