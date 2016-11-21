<?php
$allFile = TENANT_MEDIA_FOLDER . "/apppermisson/all.json";
$allApps = json_decode(file_get_contents($allFile));

$category;$type;

if (strpos($requestObj->getParams->scopeapps, ":") !==FALSE){
	list($category,$type) = explode(":", $requestObj->getParams->scopeapps);
}else $category = $requestObj->getParams->scopeapps;

$scopeArray = array();


foreach ($allApps as $app){

	$scopeAppFile = MEDIA_PATH . "/" . DuoWorldCommon::GetHost() . "/apps/".$app->ApplicationID ."/scope.json";

	if (file_exists($scopeAppFile)){
		$scopeObj = json_decode(file_get_contents($scopeAppFile));
		if (isset($scopeObj->scope)){
			if (isset($scopeObj->scope->$category)){
				$sObj = new stdClass();
				$sObj->appKey = $scopeObj->appKey;
				$sObj->scope = new stdClass();
				$sObj->scope->$category = $scopeObj->scope->$category;

				if (sizeof($scopeObj->scope->$category) > 0){
					if (isset($type)){
						for($i=0;$i<sizeof($scopeObj->scope->$category);$i++){
							$catObj = $scopeObj->scope->$category[$i];

							if (is_string($catObj)){
								if (strcmp($catObj, $type) ==0) array_push($scopeArray, $sObj);
							}else{
								if (isset($catObj->id))
									if (strcmp($catObj->id, $type) ==0) array_push($scopeArray, $sObj);
							}
						}
					}else array_push($scopeArray, $sObj);
					
				}
			}
		}
	}
}

header ("Content-Type: application/json");
echo json_encode($scopeArray, JSON_PRETTY_PRINT);

?>