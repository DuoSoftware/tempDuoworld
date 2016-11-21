<?php 
  require ($_SERVER['DOCUMENT_ROOT'] . "/dwcommon.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>DuoWorld TenantWatch</title>
    
    <link rel="stylesheet" href="./css/layout.css" type="text/css" media="screen">
    <link rel="stylesheet" href="./css/bootstrap.min.css" type="text/css" media="screen">

    <script src="./js/jquery/jquery-1.11.2.js" type="text/javascript"></script>
    <script src="./js/jquery/hideshow.js" type="text/javascript"></script>
    <script src="./js/jquery/canvasjs.min.js" type="text/javascript" ></script>

    <script src="/uimicrokernel/socket.io-1.2.0.js"></script>
    <script src="/uimicrokernel/angular.min.js"></script>
    <script src="/uimicrokernel/uimicrokernel.js"></script>

    <script src="./js/angular/angular-route.min.js"></script>
    <script src="./js/angular/ui-bootstrap-tpls-0.12.0.min.js"></script>

    <script src="./js/d3/d3.js"></script>
    <script src="./js/d3/nv.d3.js"></script>
    <script src="./js/d3/angularjs-nvd3-directives.min.js"></script>
    
    <script src="js/globals.js"></script>
    <script src="js/main.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/display.js"></script>

    <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
  <body ng-app="AuthApp" ng-controller="mainController">
    <header id="header">
      <hgroup>
        <h1 class="site_title">
          <img class="site_title" src="./images/logo.png" style="padding-top:8px;"/>
        </h1>
        <h2 class="section_title">{{pageName}}</h2>
      </hgroup>
    </header>
    <!-- end of header bar -->
    <section id="secondary_bar">
      <div class="user">
        <p>{{currentUser}}</p>
    </section>
    <!-- end of secondary bar -->
    <aside id="sidebar" class="column">
      <hr>
        <div ng-repeat="item in ClusterInfo">
          <h3>{{item.caption}}</h3>
            <ul class="toggle">
              <li ng-repeat="subItem in item.subitems" class="icn_new_article">
                <a href="" ng-click="select(item.group, subItem)">{{subItem.caption}}</a>
              </li>
            </ul>
        </div>     

      <footer>
        <hr>
        <!--p><strong>Copyright © 2015 Duo Software (Pvt) Ltd</strong></p-->
      </footer>
    </aside>
    <!-- end of sidebar -->
    <section id="main" class="column">
      <div id="viewContainer" ng-view=""></div>
    </section>
  </body>
</html>