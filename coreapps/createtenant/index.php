<!doctype html>
<html ng-app="tenantCreateApp">

<head>
    <meta charset="UTF-8">
    <title>DuoWorld | Tenant Registration</title>
    <link rel="shortcut icon" href="../img/logos/title-logo.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="bower_components/angular-material/angular-material.min.css">
    <link rel="stylesheet" type="text/css" href="css/tenant.css">
    <style type="text/css">
        #box:focus {
            outline: none;
        }
    </style>
</head>

<body ng-controller="tenantCreateCtrl">    
    <div id="duoworld-framework-entry-container">
        <div class="ui-view-container" layout="column" layout-align="center center">
           <md-whiteframe style="min-width:50%" class="md-whiteframe-z3">
                <ui-view></ui-view>
            </md-whiteframe>
        </div>
    </div>
    <script type="text/javascript" src="bower_components/angular/angular.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-animate/angular-animate.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-aria/angular-aria.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-material/angular-material.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-material-icons/angular-material-icons.min.js"></script>
    <script type="text/javascript" src="js/uimicrokernel.js"></script>
    <script type="text/javascript">
        (function (app) {
            app.config(function($stateProvider, $urlRouterProvider, $httpProvider){
                
                $urlRouterProvider.otherwise("/registerTenant/tenantinfo");
                $stateProvider
                    .state('registerTenant',{
                        abstract:true,
                        url:'/registerTenant',
                        template:'<ui-view></ui-view>'
                    })
                    .state('registerTenant.tenantinfo',{
                        url:'/tenantinfo',
                        templateUrl: 'partials/tenantinfo.php',
                        controller:'tenantinfoCtrl'
                    })
                    .state('registerTenant.tenantconfig',{
                        url:'/tenantconfig',
                        templateUrl: 'partials/tenantconfig.php',
                        controller:'tenantConfigCtrl'
                    })                    
                    .state('registerTenant.billinginfo',{
                        url:'/billinginfo',
                        templateUrl: 'partials/billinginfo.php',
                        controller:'billingInfoCtrl'
                    })
            })
            
            app.controller('tenantCreateCtrl', function ($scope,$window,$mdToast) {
                
                $scope.myFile=null;
                
                $scope.tenant = {}

                $scope.selectPackage=function(package){
                    
                    $scope.tenant.Statistic=package;
                }
                
                $scope.showSimpleToast = function(msg,position,delay) {
                    $mdToast.show(
                      $mdToast.simple()
                        .content(msg)
                        .position(position)
                        .hideDelay(delay)
                    );
                }
                
                $scope.goBack = function() {
                    window.history.back();
                }
            });
            
            app.controller('tenantinfoCtrl',function($scope,$state,$http){

                $scope.businessModels=[
                    {
                        name : 'Business',
                        value : 'business'
                    },{
                        name : 'Communication',
                        value : 'communication'
                    },{
                        name : 'Entertainment',
                        value : 'entertainment'
                    },{
                        name : 'Health & Fitness',
                        value : 'health_fitness'
                    },{
                        name : 'Lifestyle',
                        value : 'lifestyle'
                    },{
                        name : 'News Services',
                        value : 'news'
                    },{
                        name : 'Social',
                        value : 'social'
                    },{
                        name : 'Education',
                        value : 'education'
                    },{
                        name : 'Shopping',
                        value : 'shopping'
                    },{
                        name : 'Sports',
                        value : 'sports'
                    },{
                        name : 'Medical',
                        value : 'medical'
                    },{
                        name : 'Transportation',
                        value : 'transpotation'
                    },{
                        name : 'Travel',
                        value : 'traval'
                    },{
                        name : 'Generic',
                        value : 'genaric'
                    }
                ]
                
                $scope.uploadImage = function(){
                    document.getElementById("profile-image-upload").click();
                }
                
                $scope.file_changed = function(element) {

                    var photofile = element.files[0];
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $scope.$apply(function() {
                            $scope.prev_img = e.target.result;
                        });
                    };
                    reader.readAsDataURL(photofile);
                };
                
                $scope.formSubmit=function(){
                    tenantNameAvailability(function(data){
                        if(data.hasOwnProperty('Error')){
                            $scope.showSimpleToast(data.Message,'bottom right',3000)
                            return;
                        }
                        if(data.hasOwnProperty('TenantID') && data.TenantID == "")
                            $state.go('registerTenant.tenantconfig');
                        else
                            $scope.showSimpleToast('Tenant name is already taken.','bottom right',3000)
                    }) 
                }
                
                function tenantNameAvailability(callback) {
                    $http({
                        method: "GET",
                        url: "checkTenantAvailality.php?tenantId=" + $scope.tenant.name + "&tenantType=" + $scope.tenant.type
                    }).success(function(data) {
                        callback(data);
                    }).error(function(data){
        
                    })
                }
                
            });
            
            app.controller('tenantConfigCtrl', function ($scope, $http, $window, $uploader, $state, $mdDialog, $request) {
                                
                $scope.companyPricePlans = [
                    {
                        id : "com-betatest-5550",
                        name:"Beta Test",
                        DataDown:"500MB",
                        DataUp:"500MB",
                        NumberOfUsers:"5",
                        price:"0",
                        Description: "desc"
                    },{
                        id: "com-silver-1121", 
                        name:"Silver",
                        DataDown:"10GB",
                        DataUp:"10GB",
                        NumberOfUsers:"20",
                        price:"20",
                        Description: "desc"
                    },{
                        id: "com-gold-3001",
                        name:"Gold",
                        DataDown:"3GB",
                        DataUp:"Unlimited",
                        NumberOfUsers:"Unlimited",
                        price:"100",
                        Description: "desc"
                    }
                ]
                
                $scope.devPricePlan = [
                    {
                        id:"dev-betatest-5530",
                        name:"Beta Test",
                        DataDown:"500MB",
                        DataUp:"500MB",
                        appCount:"3",
                        price:"0",
                        Description: "desc"
                    },{
                        id:"dev-silver-1111",
                        name:"Silver",
                        DataDown:"10GB",
                        DataUp:"10GB",
                        appCount:"100",
                        price:"20",
                        Description: "desc"
                    },{
                        id:"dev-gold-3001",
                        name:"Gold",
                        DataDown:"3GB",
                        DataUp:"Unlimited",
                        appCount:"Unlimited",
                        price:"100",
                        Description: "desc"
                    }
                ]
                
                $scope.selectPackage=function(index,package){
                    
                    $scope.tenant.package = package;
                    $scope.selectedIndex = index;
                }
                
                $scope.goBillingStep =function() {
                    $state.go('registerTenant.billinginfo'); 
                }
                
                $scope.goBilling =function() {
                    $state.go('registerTenant.billinginfo');
                }
                
                $scope.formSubmit = function(ev) {
                    var package =  $scope.tenant.package;
                    if(parseInt(package.price ) === 0) {
                        var fobj = formatObjects($scope.tenant);                        
                        $http({
                            method: "POST",
                            url: "tenantRegister.php",
                            data: fobj
                        }).success(function(data){
                            if(data.hasOwnProperty('Error')){
                                $scope.showSimpleToast(data.Message,'bottom right',3000)
                                return;
                            }else {
                                $scope.showSimpleToast(fobj.TenantID + " tenant successfully created.",'bottom right',3000)  
                                $window.location.href = 'http://'+ window.location.hostname + "/coreapps/mytenants";
                            }
                        }).error(function(data) {
                            console.log(data);
                            $scope.showSimpleToast("Please check your internet connection.",'bottom right',3000)
                        })                 
                    } else {
                        getAllCards(function(data) {
                            accounts = data;
                            if(accounts.length === 0){
                                showaddcardPopup(ev, package, $scope.tenant);
                            }else{
                                showMyAccountcardPopup(ev, package, $scope.tenant);
                            }
                        })
                    }
                }
                
                var getAllCards = function(func) {
                    var rel = "/account/get";
                    var results = null;
                    $request.get(rel, function(data) {
                        func(data);
                    });
                }
                
                var showaddcardPopup = function(ev, app, tenant) {
                    $mdDialog.show({
                        controller: "addCardController",
                        templateUrl: 'partials/dialog2.tmpl.php',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        locals: {appObject: app, tenantObj: tenant}
                    })
                    .then(function(answer) {
                        //$scope.status = 'You said the information was "' + answer + '".';
                    }, function() {
                        //$scope.status = 'You cancelled the dialog.';
                    });
                }
                
                var showMyAccountcardPopup = function(ev, app, tenant) {
                    $mdDialog.show({
                        controller: "showMyAccountController",
                        templateUrl: 'partials/myaccounts.php',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        locals: {appObject: app, tenantObj: tenant}
                    })
                    .then(function(answer) {
                        //$scope.status = 'You said the information was "' + answer + '".';
                    }, function() {
                        //$scope.status = 'You cancelled the dialog.';
                    });
                }
                
                function formatObjects(obj) {
                    return {
                        TenantID : obj.name.toLowerCase(),
                        Type : obj.type,
                        Name: obj.company,
                        Shell: "shell/index.html#/duoworld-framework/dock",
                        Statistic: obj.package,
                        Private: (obj.accessLevel == "private") ? true : false,
                        OtherData:
                            {
                                CompanyName: obj.company,
                                SampleAttributs: "Values",
                                catagory: obj.businessModel
                            }
                    };
                }
                
            });
            
            app.controller('billingInfoCtrl', function ($scope, $state, $http, $window, $uploader) {
                $scope.formSubmit = function() {
                    if($scope.tenant.name != ""){
                        var fobj = formatObjects($scope.tenant);                        
                        $http({
                            method: "POST",
                            url: "tenantRegister.php",
                            data: fobj.Tenant
                        }).success(function(data){
//                            $uploader.upload(fobj.Tenant.TenantID,"profilepictures", 
//                                    $scope.myFile, 
//                                    fobj.Tenant.TenantID);
//                            $uploader.onSuccess(function(e,data){
//                                console.log("successfull");
//                                $scope.showSimpleToast(fobj.Tenant.TenantID + " tenant successfully created.",'bottom right',3000)
//                            });
//
//                            $uploader.onError(function(e,data){
//                                console.log("Error occured");
//                            });
                            $scope.showSimpleToast(fobj.TenantID + " tenant successfully created.",'bottom right',3000)  
                            $window.location.href = 'http://'+ window.location.hostname + "/devstudio/workspace/My_Tenants";
                        }).error(function(data){
                            
                        }) 
                    }
                }
                
                function formatObjects(obj) {
                    return {
                        TenantID : obj.name + "." +obj.type,
                        Name: obj.company,
                        Shell: "shell/index.html#/duoworld-framework/dock",
                        Statistic: obj.package,
                        Private: (obj.accessLevel === "private") ? true : false ,
                        OtherData:
                            {
                                CompanyName: obj.company,
                                SampleAttributs: "Values",
                                catagory: obj.businessModel
                            }
                    };
                }
            });
            
            app.controller('addCardController', function ($scope, $mdDialog,$request, appObject) {
          
                $scope.account = {};
          
                var loadCards = function() {
                    $request.get("/account/get", function(accArray){
                        if(accArray.length != 0) {
                            $scope.account = accArray[0];
                        }else{
                            $scope.account.AccountCards = [];
                        }
                    });
                }
                
                loadCards();
				
                $scope.payinfoSubmit =function(card) {
                     if($scope.account.AccountCards.length === 0){
                         addAccount(card);
                     }else {
                        addCard(card);
                     }
				 }
				 
                var addCard = function(payment) {
                   $scope.account.AccountCards.push({
						"CardNo" : payment.CardNo,
						"Name": payment.Name,
						"CardType": "Master",
						"DeliveyAddress": payment.DeliveyAddress,
						"BillingAddress": payment.BillingAddress,
						"CSV": payment.CSV,
						"ExpiryYear": payment.ExpiryYear,
						"ExpiryMonth": payment.ExpiryMonth,
						"Active":true,
						"verified":true
						
					});
                     
                    storeDetails();
				 }
				 
                var addAccount = function(payment) {
					$scope.account.DeliveyAddress = payment.DeliveyAddress;
					$scope.account.BillingAddress = payment.BillingAddress;
					$scope.account.PhoneNumber = "0771234568";
					$scope.account.AccountCards.push({
						"CardNo" : payment.CardNo,
						"Name": payment.Name,
						"CardType": "Master",
						"DeliveyAddress": payment.DeliveyAddress,
						"BillingAddress": payment.BillingAddress,
						"CSV": payment.CSV,
						"ExpiryYear": payment.ExpiryYear,
						"ExpiryMonth": payment.ExpiryMonth,
						"Active":true,
						"verified":true
						
					})
                    
                    storeDetails();
				 }
                 
                var storeDetails = function(obj) {
                    $request.post("/account/false", $scope.account, function(data) {
						$mdDialog.show({
							controller: "showMyAccountController",
							templateUrl: 'partials/myaccounts.php',
							parent: angular.element(document.body),
                            locals: {appObject: appObject}
						})
						.then(function(answer) {
							//$scope.status = 'You said the information was "' + answer + '".';
						}, function() {
							//$scope.status = 'You cancelled the dialog.';
						});
                    })
                 }
				
                $scope.hide = function() {
                    $mdDialog.hide();
                };
                
                $scope.cancel = function() {
                    $mdDialog.cancel();
                };
                
                $scope.answer = function(answer) {
                    $mdDialog.hide(answer);
                };
            });
            
            app.controller('showMyAccountController', function ($scope, $mdDialog, $http, $request, appObject, tenantObj, $window, $mdToast) {
                
				$scope.accounts = [];
				$scope.selectedAccount = "";
				$scope.disablePayment = true;
				
				$scope.change = function()
				{
					if($scope.disablePayment == true)
					{
						$scope.disablePayment = false;
					}else
					{
						$scope.disablePayment = true;
					}
				}
				
                  $request.get("/account/get",function(data)
                  {
                        console.log(data);
                        $scope.accounts = data;
                  })
			  
			  $scope.selectAccount = function(account)
			  {
					$scope.selectedAccount = account;
					console.log($scope.selectedAccount);
			  }
			  			  
			  $scope.makePayment = function(card,account) {
                var paystrip = {
                    "AccountId": account[0].AccountId,
                    "Cards": card,
                    "Items": [{
                        "ItemRefID": appObject.id,
                        "ItemType": "Package",
                        "Description": appObject.Description,
                        "UnitPrice": parseFloat(appObject.price),
                        "UOM": "Unit",
                        "Qty": 1,
                        "Subtotal": parseFloat(appObject.price),
                        "Discount": 0,
                        "Tax": 0,
                        "TotalPrice": parseFloat(appObject.price),
                        "TaxDetails": ""
                    }]
                }
                             
                $request.post("/transaction/paystrip/", paystrip, function(result) {
                    if(result.hasOwnProperty('Error')){
                        $scope.showSimpleToast(result.Message,'bottom right',3000)  
                    }else {
                        var fobj = formatObjects(tenantObj);
                        $http({
                            method: "POST",
                            url: "tenantRegister.php",
                            data: fobj
                        }).success(function(data) {
                            if(data.hasOwnProperty('Error')){
                                $scope.showSimpleToast(data.Message,'bottom right',3000)
                                return;
                            }else {
                                $scope.showSimpleToast(fobj.TenantID + " tenant successfully created.",'bottom right',3000)  
                                $window.location.href = 'http://'+ window.location.hostname + "/devstudio/workspace/My_Tenants";
                            }
                        }).error(function(data) {
                        
                        })
                    }
                })
                
              }
              
              $scope.showSimpleToast = function(msg,position,delay) {
                    $mdToast.show(
                      $mdToast.simple()
                        .content(msg)
                        .position(position)
                        .hideDelay(delay)
                    );
                }

              $scope.payinfoSubmit = function()
                {
                    $scope.makePayment($scope.selectedAccount,$scope.accounts);
                    $scope.disablePayment = true;
                }
				
                $scope.showaddcardPopup = function(app,ev)
                {
                    $mdDialog.show({
                        controller: "addCardController",
                        templateUrl: 'partials/dialog2.tmpl.php',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose:true,
                        locals: {appObject: app, tenantObj: tenantObj}
                    })
                .then(function(answer) {
                    //$scope.status = 'You said the information was "' + answer + '".';
                }, function() {
                    //$scope.status = 'You cancelled the dialog.';
                });

                }
                
            function formatObjects(obj) {
                return {
                    TenantID : obj.name + "." +obj.type,
                    Name: obj.company,
                    Shell: "shell/index.html#/duoworld-framework/dock",
                    Statistic: obj.package,
                    Private: (obj.accessLevel ===  "private") ? true : false, 
                    OtherData:
                        {
                            CompanyName: obj.company,
                            SampleAttributs: "Values",
                            catagory: obj.businessModel
                        }
                };
            }
                
                $scope.hide = function() {
                    $mdDialog.hide();
                };
                $scope.cancel = function() {
                    $mdDialog.cancel();
                };
                $scope.answer = function(answer) {
                    $mdDialog.hide(answer);
                };
            });
            
            app.factory("$request", function($http) {
                
                function getHost() {
                    return "http://" + window.location.hostname + "/payapi";
                }
                
                function postRequest(rel, obj, func) {
                    var url = getHost() + rel;
                    $http.post(url, obj).success(function(data, status) {
                        func(data);
                    }).error(function(data, status) {
                        console.log(data);
                    });
                    
                }
                
                function getRequest(rel, func) {
                    var url = getHost() + rel;
                    $http.get(url).success(function(data, status) {
                        func(data);
                    }).error(function(data, status) {
                        console.log(data);
                    });
                }
                
                return {
                    get : getRequest,
                    post : postRequest
                }
            });
            
            app.filter('hideNumbers', function() {
                return function(input) {
                    return input.replace(/.(?=.{4})/g, 'x');
                };
            });
            

        })(angular.module('tenantCreateApp', ['ngMaterial','ui.router','ngAnimate','ngMdIcons','uiMicrokernel']))
    </script>
</body>

</html>
