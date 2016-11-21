<?php
    $apiRootFolder = defined("API_MANAGER_ROOT") ? API_MANAGER_ROOT: "flighthackapis";
    $allApisFolder = $_SERVER["DOCUMENT_ROOT"]."/". $apiRootFolder; 
    $apiFolder = $splitParts[2];
    
    //IMPORTANT!!!: check functional scope here

    $modifiedRequestUri = str_replace("/apis/","/$apiRootFolder/", $relativePath);
    
    $_SERVER["REQUEST_URI"] = $modifiedRequestUri;
    //echo $_SERVER["REQUEST_URI"];
    chdir ("$allApisFolder/$apiFolder");
    //echo getcwd();
    $_SERVER["SCRIPT_NAME"] = "/$apiRootFolder/$apiFolder/index.php";
    //echo $_SERVER["SCRIPT_NAME"];
    $_SERVER["SCRIPT_FILENAME"] = getcwd() . "/index.php";
    //echo $_SERVER["SCRIPT_FILENAME"];

    $_SERVER["DW_APPKEY"] = $requestObj->appKey;
    require_once ($_SERVER["SCRIPT_FILENAME"]);
    exit();
?>