<?php

session_start();
require_once('index.php');
global $twitter_obj;
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./destroy.php?1');
}

$connection = $twitter_obj->twitter_callback();

//save new access tocken array in session



