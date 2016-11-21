<?php
	if (sizeof($splitParts) == 3){
		if (strcmp($splitParts[2], "uimicrokernel.js") == 0){
			$mcObj = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/uimicrokernel/uimicrokernel.js");
			header("Content-Type: application/javascript");	
			$startPos = strpos($mcObj, "//##");
			$endPos = strpos($mcObj, "//###") + 5 ;

			$startStuff = substr($mcObj, 0, $startPos);
			$endStuff = substr($mcObj, $endPos);
            $middleStuff = "objectStore: p + \"//\" + host + \"/$appKey/data\",";
			echo "$startStuff$middleStuff$endStuff";

			$isOk = true;
		} else {
			$filePath = "$_SERVER[DOCUMENT_ROOT]$relativePath";
			if (file_exists($filePath)){
				header("Content-Type: application/javascript");	
				echo (file_get_contents($filePath));	
				$isOk = true;
			}
		}
	}
	
?>