<?php
session_start();
include_once("src/Google_Client.php");
include_once("src/contrib/Google_Oauth2Service.php");
######### edit details ##########
$clientId = '41795866501-rocerat1gakc2pro6h8okl7821mvb9r0.apps.googleusercontent.com'; //Google CLIENT ID
$clientSecret = 'r2C312ZgbZp5w_FerKW6yzAS'; //Google CLIENT SECRET
$redirectUrl = 'http://developer.duoworld.com/signup/googlePlusRegister';  //return url (url to script)
$homeUrl = 'http://duoworld.com';  //return to home

##################################

$gClient = new Google_Client();
$gClient->setApplicationName('Login to DuoWorld.com');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectUrl);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>
