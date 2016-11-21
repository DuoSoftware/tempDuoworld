<?php

checkPermissionToUninstall($requestObj->appKey);

function recurse_rmdir($dir) {
  $files = array_diff(scandir($dir), array('.','..'));
  foreach ($files as $file) 
    (is_dir("$dir/$file")) ? recurse_rmdir("$dir/$file") : unlink("$dir/$file");
  
  return rmdir($dir);
}

$appKey = $requestObj->appKey;

$perfFolder = TENANT_MEDIA_FOLDER . "/apppermisson/";
$appFolder = TENANT_MEDIA_FOLDER . "/apps/$appKey";

foreach (new DirectoryIterator($perfFolder) as $file) {
  if ($file->isFile()) {
      $userFile =  $file->getPath() . "/". $file->getFilename();
      $userAppObj = json_decode(file_get_contents($userFile));
      if (isset($userAppObj->$appKey)){
          unset($userAppObj->$appKey);
          file_put_contents($userFile, json_encode($userAppObj));
      }
  }
}

if (file_exists($appFolder))
  recurse_rmdir($appFolder);

sendJsonResponse("Application successfully uninstalled");
?>
