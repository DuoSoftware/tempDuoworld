<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");
session_start();
if(!isset($_SESSION['google_data'])):header("Location:index.php");endif;
//$arr = array('profile' => $_SESSION['google_data'], 'oauth' => json_decode($_SESSION['token']), 'domain' =>$_SERVER['HTTP_HOST']);

$gdata= $_SESSION['google_data'];
$oauth = json_decode($_SESSION['token']);
$arr = (object) array_merge((array)  array('id' =>$gdata['id']) ,(array)  array('email' =>$gdata['email']) , (array) array('name' =>$gdata['name'])  ,(array) array('access_token' => $oauth->access_token),(array) array('domain' => $_SERVER['HTTP_HOST']), (array) array('authority' => 'googleplus'));

$data=json_encode((array)$arr);
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt ($ch, CURLOPT_URL, SVC_AUTH_URL."/ArbiterAuthorize/");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// execute!
$response = curl_exec($ch);
// echo $response;

$responseobj = json_decode($response);
if($responseobj)
    if(isset($responseobj->SecurityToken) && isset($responseobj->UserID))
        header("location: /s.php?securityToken=" . $responseobj->SecurityToken);
    else header("location: /");
else header("location: /");
// close the connection, release resources used
curl_close($ch);
exit();
?>

