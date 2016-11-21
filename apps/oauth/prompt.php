<?php

	function encrypt($pure_string, $encryption_key) {
	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
	    return $encrypted_string;
	}

	$appFile = TENANT_MEDIA_FOLDER . "/apps/$appKey/app.json";
	$scopeFile = TENANT_MEDIA_FOLDER . "/apps/$appKey/scope.json";
	$appStr =  file_get_contents($appFile);
	$appObj = json_decode($appStr);

	$scopeObj;
	if (file_exists($scopeFile))
		$scopeObj = json_decode(file_get_contents($scopeFile));
	else {
		$scopeObj = new stdClass();
		$scopeObj->appKey = $appKey;
		$scopeObj->scope = new stdClass();
		$scopeObj->scope->data = array();
		$scopeObj->scope->functions = array();
	}

	$reqUri = urlencode($_SERVER["REQUEST_URI"]);
	$authUrl = "/GetAuthCode/$_COOKIE[securityToken]/$appKey/tempUri";
	
	$authCode=AuthQuery($authUrl,[]);
	$cookieClass = new stdClass();

	$cookieClass->authCode = $authCode;
	$cookieClass->appKey = $appKey;
	$cookieClass->scope = $scopeObj;

	$authObj = json_decode($_COOKIE["authData"]);
	$cookieClass->username = $authObj->Username;

	$profilePicUrl = "/apis/media/user/profilepictures/profile.jpg";
	$appIconUrl = "/apps/$appKey?meta=icon";
	
	require_once($_SERVER["DOCUMENT_ROOT"]. "/payapi/duoapi/objectstoreproxy.php");
	$client = ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),"appapprovals","123");
	$res = $client->store()->byKeyField("username")->andStore($cookieClass);

?>
<html>
	<head>
		<title>Approve Application</title>
		<link rel="stylesheet" href="/bower_components/angular-material/angular-material.min.css">
		<link rel="stylesheet" href="/apps/oauth/platformentry-styles.css"> 
		
	</head>
	
	<body ng-app="mainApp" id="platformentry-container">
		
		<div id="viewContainer" layout="column" layout-align="center center" ui-view></div>
		
		<script type="text/javascript" src="/apps/oauth/script.js"></script>
		<script type="text/javascript">
			angular.module("mainApp",["ui.router","ngMaterial","ngMessages"])
			
			//Platform entry view route configuration - Start
			.config(['$stateProvider','$urlRouterProvider', function($sp, $urp){
				$urp.otherwise('/permissions');
				$sp
				.state('permissions', {
					url: '/permissions',
					templateUrl: '/apps/oauth/permissions.php',
					controller: 'mainController'
				})
				
				.state('rejected', {
					url: '/rejected',
					templateUrl: '/apps/oauth/rejected.php'
				})

			}])
			
			.controller("mainController", function($scope){
	
				$scope.appObj = <?php echo "$appStr;" ?>
				$scope.scopeObj = <?php echo json_encode($scopeObj). ";"; ?>
				$scope.dataShow = $scope.scopeObj.scope.data.length!=0 ? true: false;
				$scope.functionShow = $scope.scopeObj.scope.functions.length!=0 ? true: false;
				$scope.scopeVisible = $scope.dataShow | $scope.functionShow;
				$scope.profilePicUrl ="<?php echo $profilePicUrl;?>";
				$scope.appIconUrl = "<?php echo $appIconUrl;?>";

				$scope.approve = function(){
					location.href = "/apps/oauth/approve.php?r=" + window.location.href;
				}

				$scope.reject = function(){
					location.href = "#/rejected";
				}

			})
			
			.directive('errSrc', function () {
				return {
					link: function (scope, element, attrs) {
						element.bind('error', function () {
							if (attrs.src != attrs.errSrc) {
								attrs.$set('src', attrs.errSrc);
							}
						});

						attrs.$observe('ngSrc', function (value) {
							if (!value && attrs.errSrc) {
								attrs.$set('src', attrs.errSrc);
							}
						});
					}
				}
			});


		</script>
	</body>
</html>