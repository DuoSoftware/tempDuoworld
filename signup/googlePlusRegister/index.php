<?php
include_once("config.php");

//print_r($_GET);die;

if(isset($_REQUEST['code'])){
	$gClient->authenticate();
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($redirectUrl, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
	$gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
	$userProfile = $google_oauthV2->userinfo->get();
	$_SESSION['google_data'] = $userProfile; // Storing Google User Data in Session
	header("location: account.php");
	$_SESSION['token'] = $gClient->getAccessToken();
} else {
	$authUrl = $gClient->createAuthUrl();
}

if(isset($authUrl)) {
	//if needed button can be shown image must be added
	//echo '<a href="'.$authUrl.'"><img src="images/glogin.png" alt=""/></a>';
    header("Location:".$authUrl);
    //echo $authUrl;
} else {
	echo '<a href="logout.php?logout">Logout</a>';
}

?>