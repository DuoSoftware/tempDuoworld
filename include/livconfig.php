<?php
$mainDomain="mambati.com";
$authURI="http://auth.mambati.com:3048/";
$objURI="http://obj.mambati.com:3000/";
$fullhost=strtolower($_SERVER['HTTP_HOST']);
$protacall="https";
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("MEDIA_PATH", "/var/media");
define("APPICON_PATH", "/var/www/html/devportal/appicons");
        //define("BASE_PATH", "/var/www/html/medialib");
        //define("STORAGE_PATH", BASE_PATH . "/media");
        //define("THUMB_PATH", BASE_PATH . "/thumbnails");
define("SVC_OS_URL", "http://obj.mambati.com:3000");
define("SVC_OS_BULK_URL", "http://obj.mambati.com:3001/transfer");
define("SVC_AUTH_URL", "http://auth.mambati.com:3048");
define("SVC_CEB_URL", "http://admin.dev.mambati.com:3500");

define("STORAGE_PROFILE", "PROXY");
define("SVC_MEDIA_URL", "dw-storage");
?>

    

