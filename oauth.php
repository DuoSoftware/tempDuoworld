<?php
require_once ("include/config.php");
require_once ("include/session.php");

if(!isset($_COOKIE["securityToken"])){
  //header("Location: s.php?r=http://".$_SERVER['HTTP_HOST'].'/oauth.php?Auth=2&Appid='.$_GET["Appid"].'&Uri'.$_GET["Uri"]);
  echo "Not Autherized";
  exit();
}

// Authorize Application
if(isset($_GET["Appid"]) && isset($_GET["Uri"]) && isset($_GET["Auth"])){
  $str=AuthQuery("/Authorize/".$_COOKIE["securityToken"]."/".$_GET["Appid"],[]);
  //echo $str
  $obj=json_decode($str);
  if(!isset($obj)){
      header("Location: ".$_GET["Uri"].'?Auth=0');
      exit();
  }else {
    header("Location: ".$_GET["Uri"].'?Auth=1&JWT='.$obj->Otherdata["JWT"].'&SecurityToken='.$obj->SecurityToken);
    exit();
  }
}

//Authendicate App
if(isset($_GET["Appid"])&&isset($_GET["Secret"])&&isset($_GET["Uri"])){

  $str=AuthQuery("/GetAuthCode/".$_COOKIE["securityToken"]."/".$_GET["Appid"]."/".$_GET["Uri"],[]);
  //echo $str
  $obj=json_decode($str);
  if($obj!="Application Not exist."){
      header("Location: ".$_GET["Uri"].'?Auth=0');
      exit();
  }else {
    //header("Location :".$_GET["Uri"].'?Auth=1&AuthCode='.$obj);
    //exit();
    ?>
    <!DOCTYPE html>
    <html>
      <head>
        <meta charset="utf-8">
        <title>Authrize Application</title>
      </head>
      <body>
          place the form to approve app POST it to this again
          <form action="">
              <input type="text" name="Authcode" value="Mickey"><br>
              <input type="text" name="Uri" value="<?php ?>"><br><br>
              <input type="submit" value="Submit">
          </form>
      </body>
    </html>
    <?php
  }
}

if(isset($_POST["Authcode"])&&isset($_POST["Appid"])&&isset($_POST["Secret"])&&isset($_POST["Uri"])){
  //$postData =$_POST["scop"]; // ScopBody
  $postData = getAppFunctions($_POST["Appid"]);

  $str=AuthQuery("/AutherizeApp/".$_COOKIE["securityToken"]."/".$_POST["Appid"]."/".$_POST["Uri"],$postData);
  //echo $str
  $obj=json_decode($str);
  if($obj!="true"){
      header("Location :".$_GET["Uri"].'?Auth=0');
      exit();
  }else {
    $str=AuthQuery("/Authorize/".$_COOKIE["securityToken"]."/".$_GET["Appid"],[]);
    //echo $str
    $obj=json_decode($str);
    if(!isset($obj)){
        header("Location: ".$_GET["Uri"].'?Auth=0');
        exit();
    }else {
      header("Location: ".$_GET["Uri"].'?Auth=1&JWT='.$obj->Otherdata["JWT"].'&SecurityToken='.$obj->SecurityToken);
      exit();
    }
    exit();
  }
}


function detectRequestBody() {
    $rawInput = fopen('php://input', 'r');
    $tempStream = fopen('php://temp', 'r+');
    stream_copy_to_stream($rawInput, $tempStream);
    rewind($tempStream);

    return stream_get_contents($tempStream);
}


function AuthQuery($authrequest,$postData){
  $headers = apache_request_headers();
  if(isset($_COOKIE["authData"])){
  	$authData = json_decode($_COOKIE["authData"]);
  	$SecurityToken=$authData->SecurityToken;
  	array_push($headers,'securityToken:'.$SecurityToken);
  }
  	$ch=curl_init();
  	curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
  	curl_setopt($ch, CURLOPT_URL, SVC_AUTH_URL.$authrequest);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	if(count($postData)!=0){
  		curl_setopt($ch, CURLOPT_POST, count($postData));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  	}
  	$data = curl_exec($ch);
    return $data;
}


function getAppFunctions($appId){
  $headers = apache_request_headers();

    $ch=curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_URL, "http://localhost/apps/$appId?meta=functions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $data = curl_exec($ch);

    $funcs;

    if (isset($data)){
      $dataObj = json_decode($data);
      
      if (is_object($dataObj))
        if (isset($dataObj->functions))
          $funcs = $dataObj->functions;
    }

    if (!isset($funcs)) $funcs = [];

    return json_encode($funcs);
}

?>
