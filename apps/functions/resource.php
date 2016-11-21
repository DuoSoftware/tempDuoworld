<?php
  checkPermissionForResource($requestObj->appKey);

  function system_extension_mime_types() {
      # Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
      $out = array();
      $file = fopen('data/mime.types', 'r');
      while(($line = fgets($file)) !== false) {
          $line = trim(preg_replace('/#.*/', '', $line));
          if(!$line)
              continue;
          $parts = preg_split('/\s+/', $line);
          if(count($parts) == 1)
              continue;
          $type = array_shift($parts);
          foreach($parts as $part)
              $out[$part] = $type;
      }
      fclose($file);
      return $out;
  }

  function system_extension_mime_type($file) {
      static $types;
      if(!isset($types))
          $types = system_extension_mime_types();
      $ext = pathinfo($file, PATHINFO_EXTENSION);
      if(!$ext)
          $ext = $file;
      $ext = strtolower($ext);
      return isset($types[$ext]) ? $types[$ext] : null;
  }

  
  $relativePath = str_replace("/apps/$requestObj->appKey","", $_SERVER["REQUEST_URI"]);
  $fullPath = rawurldecode(TENANT_MEDIA_FOLDER . "/apps/$requestObj->appKey/resources$relativePath");
  if (!file_exists($fullPath)) {
    $isOk = false;
    $splitParts = explode("/", trim($relativePath));
    if (sizeof($splitParts) > 1 ){
      $resMapping = array("data"=>"data.php", "uimicrokernel"=>"uimicrokernel.php", "apis"=>"apis.php");

      foreach ($resMapping as $mKey=>$mValue)
        if (strcmp($splitParts[1], $mKey) ==0) {
          require_once("functions/$mValue");
          break;
        }
    }
    
      require_once ("./serviceframework/executor.php");
    
    if (!$isOk){
      $executor = new ServiceExecutor();
      $isOk = $executor->Execute();
    }
    
    if (!$isOk) sendJsonResponse("404 - Resource not found [$relativePath]", false, 404);
  }
  else {
    if (is_dir($fullPath)){
      $defaultFiles = array("index.html", "index.htm", "index.php", "default.html", "default.htm", "default.php");
      
      foreach ($defaultFiles as $defFile){
        $defFileName = $fullPath . "/".$defFile;
        if (file_exists($defFileName)){
          header ("Location: $_SERVER[REQUEST_URI]/$defFile");
          exit();
        }
      }

      sendJsonResponse("404 - Resource not found", false, 404);
    }else{
      header('Content-Type: '. system_extension_mime_type($fullPath));
      header("Cache-Control: no-transform,public,max-age=86400,s-maxage=86400");
      echo file_get_contents($fullPath);        
    }

  }
?>
