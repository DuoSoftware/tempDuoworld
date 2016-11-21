<?php
session_start();
// added in v4.0.0
require_once 'autoload.php';
require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;
// init app with app id and secret
FacebookSession::setDefaultApplication( '991729180953846','c0c9689305322cfd21810719af73a0ae' );
// login helper with redirect_uri
    $helper = new FacebookRedirectLoginHelper('http://'.$_SERVER['HTTP_HOST'].'/signup/facebookRegister/fbconfig.php' );
try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
}
// see if we have a session
if ( isset( $session ) ) {
  // graph api request for user data
  $request = new FacebookRequest( $session, 'GET', '/me' );
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject();
     	$fbid = $graphObject->getProperty('id');              // To Get Facebook ID
 	    $fbfullname = $graphObject->getProperty('name'); // To Get Facebook full name
	    $femail = $graphObject->getProperty('email');    // To Get Facebook email ID
	/* ---- Session Variables -----*/
	    $_SESSION['FBID'] = $fbid;           
        $_SESSION['FULLNAME'] = $fbfullname;
	    $_SESSION['EMAIL'] =  $femail;
    /* ---- header location after session ----*/

    //echo ($data);
    $arr = (object) array_merge((array)  array('access_token' => $session->getToken()), (array) array('authority' => 'FaceBook'), (array) array('user_id' => $fbid) ,(array) array('domain' => $_SERVER['HTTP_HOST']));
    $data=json_encode((array)$arr);

    echo ($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($ch, CURLOPT_URL, SVC_AUTH_URL."/ArbiterAuthorize/");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    // execute!
    $response = curl_exec($ch);

    $responseobj = json_decode($response);
    if($responseobj)
      if(isset($responseobj->SecurityToken) && isset($responseobj->UserID))
        header("location: /s.php?securityToken=" . $responseobj->SecurityToken);
      else header("location: /");
    else header("location: /");
    
    // close the connection, release resources used
    curl_close($ch);
    exit();


} else {
  $loginUrl = $helper->getLoginUrl(array('scope' => 'email'));
 header("Location: ".$loginUrl);
}
?>