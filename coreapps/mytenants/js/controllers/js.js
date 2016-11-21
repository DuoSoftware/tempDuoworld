var app = angular.module('mainApp', ['ngMaterial', 'ngAnimate', 'ui.router', 'directivelibrary', 'ngMessages', 'uiMicrokernel','cloudcharge'])
	
	.config(function($stateProvider, $urlRouterProvider) {

	$urlRouterProvider.otherwise('/view');

	$stateProvider
	
	// HOME STATES AND NESTED VIEWS ========================================
  
	.state('view', {
		url: '/view',
		templateUrl: 'partials/view.html',
		controller: 'AppCtrl'
	})
	
	.state('main', {
		url: '/main',
		templateUrl: 'partials/main.html',
		controller: 'AddCtrl'
	})
	.state('main.registration', {
		url: '/registration',
		templateUrl: 'partials/registration.html'
	})
	.state('main.add', {
		url: '/add',
		templateUrl: 'partials/add.html'
	})

})


app.controller('AppCtrl', function ($scope,$rootScope ,$mdDialog,$http, uiInitilize, $v6urls, $auth, notifications) {
	
	$auth.checkSession();
	$rootScope.showGlobalProgress = true;
	$scope.submitted = false;
	$scope.items = []; //Tenant data
	
	function loadMyTenants(){
		$http.get($v6urls.auth + "/tenant/GetTenants/" + $auth.getSecurityToken())
		.success(function(data) 
		{
			for (i = 0, len = data.length; i<len; ++i){
                
				var tempType = data[i].TenantID.split('.');
				console.log(tempType);
				if(tempType[1] == "dev"){
					data[i].Type = "Developer Tenant";
					data[i].ImageUrl = "img/ic_desktop_mac_24px.svg";
				}else{
					data[i].Type = "Company Tenant";
					data[i].ImageUrl = "img/ic_business_24px.svg";
				}
			}
			
			$scope.items = uiInitilize.insertIndex(data);
			console.log(data);
			$rootScope.showGlobalProgress = false;
		}).error(function() 
		{
		    notifications.alertDialog("Error", "Error Retrieving My Tenants");
            $rootScope.showGlobalProgress = false;
		});
	}
	loadMyTenants();
	
		
	 //This holds the UI logic for the collapse cards
	 $scope.toggles = {};
	 $scope.toggleOne = function($index)
	 {
		$scope.toggles = uiInitilize.openOne($scope.items, $index);
	 }

	setInterval(function interval(){
		$scope.viewPortHeight = window.innerHeight;
		$scope.viewPortHeight = $scope.viewPortHeight+"px";
	 }, 100);
	 
	 $scope.viewTenant=function(data){
		window.open("http://" + data.TenantID , "_blank");
	}

	
})//END OF AppCtrl

  
app.controller('AddCtrl', function ($scope, $mdDialog, $window, $mdToast, notifications,$charge,$http,$rootScope, objectFormat, $mdMedia) {
	
	$scope.tenant = {}; //Form Object
	
	$rootScope.package = {};	
	$scope.showPricing = false;
	
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
        }]
	
	$scope.companyPricePlans = [
        {
            id : "personal_space",
            name:"Personal Space",
            numberOfUsers:"1",
			numberOfApps:"Unlimited",
			storage: "10 GB",
            price:"0",
			per: "/ Mo",
			Description: "desc"
        },
		{
            id : "mini_team",
            name:"We Are A Mini Team",
            numberOfUsers:"5",
			numberOfApps:"Unlimited",
			storage: "10 GB",
            price:"0",
			per: "/ Mo",
			Description: "desc"
        },
		{
            id : "world",
            name:"We Are the World",
            numberOfUsers:"Unlimited",
			numberOfApps:"Unlimited",
			storage: "10 GB",
            price:"4.99",
			per: "/ User",
			Description: "desc"
        }]
		
	$scope.devPricePlan = [
            {
                id:"dev-betatest-5530",
                name:"Beta Test",
                DataDown:"500MB",
                DataUp:"500MB",
                appCount:"3",
                price:"0",
                Description: "desc"
			}]
		
		
	$scope.openTermsAndConditions = function(ev)
	{
	    var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
			
		$mdDialog.show({
			  controller: 'termsCtrl',
			  templateUrl: 'partials/terms.html',
			  parent: angular.element(document.body),
			  targetEvent: ev,
			  fullscreen: useFullScreen,
			  clickOutsideToClose:true
		});
		
		$scope.$watch(function() {
		  return $mdMedia('xs') || $mdMedia('sm');
		}, function(wantsFullScreen) {
		  $scope.customFullscreen = (wantsFullScreen === true);
		});
	}
		
	$scope.selectPlan = function(ev, index, package) //This is the click event for adding a company tenant in add.html
    {
		$rootScope.package = package;
		$scope.selectedCard = index;

		notifications.startLoading("Checking tenant name Availability");

		tenantNameAvailability(function(data){
			if(data.hasOwnProperty('Error')){
				notifications.toast(data.Message,"error");
				notifications.finishLoading();
			}
			
			if(data.hasOwnProperty('TenantID') && data.TenantID == ""){ // If user name is available
				
				if(parseInt($rootScope.package.price ) === 0) { //Free company tenant
					notifications.startLoading("Please wait, adding a company tenant");
		            createTenant();
		        }else   //Paid company tenant
		        {
		            
        		   notifications.finishLoading();
		           notifications.startLoading("Checking for cards, please wait");
        		   
        		   $charge.payment().getAccounts().success(function(data) { //check for payment methods
        		        notifications.finishLoading();
                        if(Array.isArray(data) && data.length > 0) 
                            showCards(ev, data[0], package); //If user already has a account show cards
                        else
                            newCard(ev,null, package); //Else prompt to add a card
                    }).error(function(data) {
                        notifications.finishLoading();
                        notifications.alertDialog("Error", "Error Retrieving My Cards");
                        console.log(data);
                    })
                } //End of paid company tenant
			}
			else //if username is taken
			{
			   notifications.finishLoading();
			   notifications.toast("Sorry, Tenant name is already taken","error");
			}
		});// end of checking tenant name availability
			
    }
	
	$scope.goTenentInfo = function()
    {
		location.href = "#/main/registration";
		$scope.showPricing = false;			
    }
	
	
	$scope.submit = function(ev)
	{
		if($scope.editForm.$valid === true) 
		{
			if($scope.tenant.type && $scope.tenant.accessLevel && $scope.tenant.businessModel)//make sure all data is entered
			{
				if($scope.tenant.type == 'dev')
				{
				    $scope.submitted = true;
				    notifications.startLoading("Please wait, adding a developer tenant");
				    
					$rootScope.package =  $scope.devPricePlan[0];
		   
					$scope.tenant.name = $scope.tenant.name.toLowerCase(); //tenant name cannot contian upercase letters
					
					tenantNameAvailability(function(data){
						if(data.hasOwnProperty('Error')){
							notifications.toast(data.Message,"error");
							notifications.finishLoading();
						}
						
						if(data.hasOwnProperty('TenantID') && data.TenantID == "")
						{
							createTenant(); //if tenant name is available create a dev tenent
						}
						else
						{
						   notifications.finishLoading();
						   notifications.toast("Sorry, Tenant name is already taken","error");
						}
					}); // end of checking tenant name availability
					
				}else if($scope.tenant.type == 'com')
				{
					location.href = "#/main/add";
					$scope.showPricing = true;
				}
				
			}else{
				notifications.toast("Please fill all the details","error");
			}
		}else
		{
		    notifications.toast("Please fill all the details and agree to the terms and conditions","error", 3000);
		}
		
	}
	
	function tenantNameAvailability(callback) {
        $http({
            method: "GET",
            url: "/coreapps/createtenant/checkTenantAvailality.php?tenantId=" + $scope.tenant.name + "&tenantType=" + $scope.tenant.type
        }).success(function(data) {
            callback(data);
        }).error(function(data){
            notifications.finishLoading();
        })
    }
    
    function createTenant()
    {
        var fobj = objectFormat.format($scope.tenant);     
        
        $http({
            method: "POST",
            url: "/coreapps/createtenant/tenantRegister.php",
            data: fobj
        }).success(function(data){
            if(data.hasOwnProperty('Error')){
                 notifications.finishLoading();
            }else {
                notifications.toast(fobj.TenantID + " tenant successfully created.","success");
                location.href = "#/view";
                notifications.finishLoading();
                $scope.submitted = false;
               
            }
        }).error(function(data) {
            notifications.toast("Please check your internet connection.","error");
            notifications.finishLoading();
        })               
    }

	var newCard = function(ev,acc,package) {
			$mdDialog.show({
				controller: "addCardCtrl",
				templateUrl: 'partials/newCard.html',
				parent: angular.element(document.body),
				targetEvent: ev,
				clickOutsideToClose:false,
				locals: {cardObject: "", account: acc}
			}).then(function(account) {
			    if(account)
			    {   
			        showCards(ev, account, $rootScope.package);
			    }
            });
	}
		
	var showCards = function(ev,acc,package) {
		$mdDialog.show({
			controller: "myCardsCtrl",
			templateUrl: 'partials/myCards.html',
			parent: angular.element(document.body),
			targetEvent: ev,
			clickOutsideToClose:false,
			locals: {account: acc, package: package}
		}).then(function(response) {
		    if(response)
		    {   
		        if(response.purchase === true) // A user may either close this dialog to either purchase a tenant or open newCard dialog 
		        {
		            createTenant();
		        }else
		        {
		            newCard(response.event, response.account, response.app);
		        }
		    }
        });
	}
	
})//END OF AddCtrl

app.controller('termsCtrl',['$scope','$mdDialog', function ($scope,$mdDialog){

	$scope.cancel = function()
	{
		$mdDialog.hide();
	}
}]);