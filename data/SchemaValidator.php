<?php
	require_once(ROOT_PATH . "/data/config.php");

	class SchemaValidator {

		public function GetKeyField($namespace,$class){
			$schema = $this->getSchema($namespace, $class);
			if (!isset($schema)) return NULL;

			foreach($schema->entity as $sField=>$validators)
				if (isset($validators->isPrimary)) return $sField;

			return NULL;
		}

		public function Validate($namespace, $class, $json){
			$schema = $this->getSchema($namespace, $class);

			if (!isset($schema) || !isset($json)) return $this->getResponse(true, NULL, $json, NULL);
			else return $this->startEngine($schema, $json);
		}

		private function getResponse($isSuccess, $errorLog, $resultObj, $priKey){
			$res = new stdClass();

			$res->success = $isSuccess;
			if (isset($errorLog)) $res->errorLog = $errorLog;
			if (isset($resultObj)) $res->result = $resultObj;
			if (isset($priKey)) $res->keyField = $priKey;
			
			return $res;
		}

		private function  startEngine($schema, $json){
			$req = json_decode($json);
			$obj;

			if (!isset($req)){

				$req = new stdClass();
				$req->Query = new stdClass();
				$req->Query->Type = "Query";
				$req->Query->Paramters = $json;
				//var_dump($req);
				return $this->getResponse(true, NULL, json_encode($req), NULL);
			}

			if (isset($req->Query)) return $this->getResponse(true, NULL, $json, NULL);

			//req=Object;req=Object,Parameters;req=inside object
			if (isset($req->Object) && isset($req->Parameters)){
				$obj = $req->Object;
			}else{
				$obj = $req;
				$req = new stdClass();
				$req->Object = $obj;
				$req->Paramters = new stdClass();
			}

			

			$result = array();
			$isStatic = (strcmp($schema->type, "static") == 0);
			$isModified = false;
			$keyField;

			foreach($schema->entity as $sField=>$validators){
				
				if (isset($validators->isPrimary)) $keyField = $sField;

				if (isset($validators->default)){
					if (!isset($obj->$sField)) 
						{$obj->$sField = $validators->default; $isModified=true;}
				}
				else{
					if (isset($validators->isNull))
					if ($validators->isNull == false)
					if (!isset($obj->$sField)) 
						{array_push($result, "FieldName $sField not found in object");continue;}
				}

				if (isset($obj->$sField)){
					$dataType = $validators->dataType;
					if (isset($dataType)){

						if (!$this->validateDataType($obj->$sField, $dataType))
							{array_push($result, "Invalid Data type for field $sField, expected $dataType");continue;}

						$extension = $this->getExtension($dataType);

						if (isset($extension)){
							$extRes = $extension->Validate($sField, $obj->$sField, $validators);
							if (isset($extRes)) {array_push($result, $extRes);continue;}
						}
						else 
							{array_push($result, "Invalid Validator ($vKey : $vValue) for field $key");continue;}
					} 
					else {array_push($result, "Data type not defined for field $sField"); continue;}

				}
			}

			if ($isStatic)
			foreach ($obj as $field=>$value)
			if (!isset($schema->entity->$field))
		 		array_push($result, "Invalid additional field $field in object");
			
			if (sizeof($result) != 0) return $this->getResponse(false, $result, NULL, NULL);
			
			if (isset($keyField)){
				if (!isset($req->Parameters)) {$req->Parameters = new stdClass(); $isModified=true;}
				if (!isset($req->Parameters->KeyProperty)) {$req->Parameters->KeyProperty = $keyField; $isModified=true;}
			}
			if ($isModified) $json = json_encode($req);
			return $this->getResponse(true, NULL, $json, isset($keyField)? $keyField : NULL);
		}

		private function validateDataType($field, $dataType){
			
			$outerType; $innerType;
			$isArray=false; $isObject=false; $isObjectArray=false;

			if (strpos($dataType,'(') !== false) {
				$outerType = trim(substr($field, 0, strpos(strpos($dataType,'('))));
				$innerType = trim(substr($field, strpos(strpos($dataType,'(')), strlen($dataType) - 1));
			}
			else $outerType = $innerType = $dataType;

			switch($innerType){
				case "string":
					if (!is_string($field)) return false;
					break;
				case "number":
					if (!is_numeric($field)) return false;
					break;
				case "boolean":
					if (!is_bool($field)) return false;
					break;
				case "dateTime":
					if (!is_dateTime($field)) return false;
					break;
				case "object":
					break;
				case "array":
					break;
			}
			
			return true;
		}

		private function is_dateTime($date, $format = 'Y-m-d'){
		    $dt = DateTime::createFromFormat($format, $date);
		    return $dt && $dt->format($format) == $date;
		}

		private function getExtension($vKey){
			$validatorFile = VALIDATOR_LOCATION . "/$vKey.php";
			if (!file_exists($validatorFile)) return NULL;

			require_once($validatorFile);
			$class = $vKey."Validator";
			return new $class();
		}

		private function getSchema($namespace, $class){
			$schema;
			$sFileName= SCHEMA_LOCATION . "/" . strtolower($namespace) . "/" . strtolower($class) . ".json";

			if (file_exists($sFileName)){
				$contents = file_get_contents($sFileName);
				$schema = json_decode($contents);
			}

			return isset($schema) ? $schema : NULL;
		}
	}

?>