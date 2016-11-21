<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

/**
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */

/* Load required lib files. */
require_once('oauth/twitteroauth.php');
//require_once ("/payapi/duoapi/objectstoreproxy.php");
require_once('crypt.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");
//require_once (ROOT_PATH . "/dwcommon.php");
//require_once (ROOT_PATH .'/include/flight/Flight.php');

session_start();

class StripeAPI{
    protected  $consumer_key	 = 'Y4ZWzeC6dE4TU2SiEvdqOdGvI';
    protected  $consumer_secret	 = 'e4rjLOuEz5fQe3zUroVYLWQ2otVfNmyriOFcVU3wNghyeNEJBT';
    protected  $oauth_callback	 = 'http://developer.duoworld.com/signup/twitterRegister/callback.php';

    function __construct() {

        if(empty($_SESSION['status'])){
            $this->login_twitter();
        }
    }

    function login_twitter(){
        if ($this->consumer_key === '' || $this->consumer_secret === '') {
            echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
            // exit;
        }


        /* Build an image link to start the redirect process. */
        //echo $content = '<a href="./?connect=twitter"><img src="./images/lighter.png" alt="Sign in with Twitter"/></a>';

        //GET METHOD : http://localhost/twitterRegister/?connect=twitter
        if(isset($_GET['connect']) && $_GET['connect']=='twitter'){

            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret);// Key and Sec
            $request_token = $connection->getRequestToken($this->oauth_callback);// Retrieve Temporary credentials.

            $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
            $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];


            switch ($connection->http_code) {
                case 200:    $url = $connection->getAuthorizeURL($token); // Redirect to authorize page.
                    header('Location: ' . $url);
                    break;
                default:
                    echo 'Could not connect to Twitter. Refresh the page or try again later.';
            }
        }
    }

    function twitter_callback(){
        $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
        $_SESSION['access_token'] = $access_token;
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);

        if (200 == $connection->http_code) {
            echo $_SESSION['status'] = 'verified';
            header('Location: ./index.php?connected');
        } else {
            header('Location: ./destroy.php?2');
        }
    }



    function  register(){
        if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
            header('Location: ./destroy.php?3');
        }
        $access_token = $_SESSION['access_token'];

        $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

        /* If method is set change API call made. Test is called by default. */
        $content = $connection->get('account/verify_credentials',["include_email" => "true"]);

        $arr = (object) array_merge((array)  array('id_str' =>$content->id_str) ,(array)  array('screen_name' =>$content->screen_name) , (array)  array('screen_name' =>$content->screen_name) , (array)  array('email' =>$content->email),(array) $access_token,(array) array('domain' => $_SERVER['HTTP_HOST']), (array) array('consumer_key' => $this->consumer_key), (array) array('consumer_secret' => $this->consumer_secret), (array) array('authority' => 'twitter'));
 	//var_dump($arr);
        //$arr = array('profile' => $content, 'oauth' => $access_token, 'domain' => $_SERVER['HTTP_HOST'], 'consumer_key' => $this->consumer_key, 'consumer_secret' => $this->consumer_secret );
        $data=json_encode((array)$arr);
        
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
        /*$client = ObjectStoreClient::WithNamespace("com.duosoftware.com","twitter_accounts","ignore");



        $ifExist=$client->get()->byKey($content->email);


        //var_dump($content->email);
        if($ifExist==[]){

            $password=randomPassword();
            $password=Encrypt($password,$this->consumer_secret);
            $resArray=array('email'=>$content->email,'password'=>$password);
            $client->store()->byKeyField('email')->andStoreArray($resArray);

            // set post fields
            $post = [
                'EmailAddress'=> $content->email,
                'Name'=> $content->name,
                'Password'=> $password,
                'ConfirmPassword'=> $password,
                'Active'=> false
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_URL, SVC_AUTH_URL."/UserActivation/");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            // execute!
            $response = curl_exec($ch);
            var_dump($response);
            // close the connection, release resources used
            curl_close($ch);

            // do anything you want with your response
            //var_dump($response);


        }
        else{
            $username=$ifExist->email;
            $password=Decrypt($ifExist->password,$this->consumer_secret);
            $curl = curl_init();
            curl_setopt ($curl, CURLOPT_URL,  SVC_AUTH_URL.'/Login/'.$username.'/'.$password.'/duoworld.com');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $payinfo=curl_exec ($curl);
            curl_close ($curl);

            var_dump($payinfo);


        }*/

//echo $content->name;echo $content->location;echo $content->followers_count;echo $content->friends_count;
//echo $content->friends_count;echo "<img src='{$content->profile_image_url}'/>";echo "<a href='./destroy.php'>LogOut</a>";


    }


}


global $twitter_obj;

if(isset($_REQUEST['connected']) && isset($_SESSION['status'])){
    $twitter_obj = New StripeAPI();
    $twitter_obj->register();
}else{
    $twitter_obj = New StripeAPI();
}

