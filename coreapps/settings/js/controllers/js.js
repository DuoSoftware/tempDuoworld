var app = angular.module('mainApp', [ 'draganddrop','ngMaterial', 'ngAnimate', 'ui.router', 'directivelibrary', 'ngMessages' ,'uiMicrokernel','cloudcharge'])

.config(function($stateProvider, $urlRouterProvider) {

	$urlRouterProvider.otherwise('/main/users');

	$stateProvider
	// HOME STATES AND NESTED VIEWS ========================================

	.state('main', {
		url: '/main',
		templateUrl: 'partials/main.html'
	})

	.state('main.users', {
		url: '/users',
		templateUrl: 'partials/users.html',
		controller: 'usersCtrl'
	})

	.state('main.pendingUsers', {
		url: '/pendingUsers',
		templateUrl: 'partials/pendingUsers.html',
		controller: 'usersCtrl'
	})
	
	.state('main.groups', {
		url: '/groups',
		templateUrl: 'partials/groups.html',
		controller: 'groupsCtrl'
	})

	.state('main.shareApps', {
		url: '/shareApps',
		templateUrl: 'partials/shareApps.html',
		controller: 'shareAppsCtrl'
	})

	.state('main.share',{
		url: '/share?appId?appNamee',
		templateUrl: 'partials/share.html',
		controller: 'shareCtrl'
	})

	.state('main.myTenants',{
		url: '/myTenants',
		templateUrl: 'partials/viewMyTenants.html',
		controller: 'viewMyTenantsCtrl',
		params: {
			'pageNo': '0'
		}
	})

	.state('mainViewMyTenant', {
		url: '/mainViewMyTenant',
		templateUrl: 'partials/mainViewMyTenant.html',
		controller: 'AddCtrl'
	})

	.state('mainViewMyTenant.registration', {
		url: '/registration',
		templateUrl: 'partials/registrationMyTenants.html',
	})

	.state('mainViewMyTenant.add', {
		url: '/add',
		templateUrl: 'partials/addMyTenants.html'
	});

})

app.controller('AppCtrl', function ($scope, $rootScope, $mdDialog, $location, $state, $timeout, $q,$http, notifications) {

	console.log('V 1.0.13');

	$scope.changeTab = function(ind){
		$rootScope.selectedPage = ind;
		switch (ind) {
			case 0:
			$location.url("/main/users");

			break;
			case 1:
			$location.url("/main/pendingUsers");

			break;
			case 2:
			$location.url("/main/groups");

			break;
			case 3:
			$location.url("/main/shareApps");

			break;
			case 4:
			$location.url("/main/myTenants");

			break;

		}
	}; 
	
	$scope.getCatLetter=function(catName){
		try{
			var catogeryLetter = "/img/material alperbert/avatar_tile_"+catName.charAt(0).toLowerCase()+"_28.png";
		}catch(exception){}
		return catogeryLetter;
	}; 

})//END OF AppCtrl

app.controller('usersCtrl', function ($scope, $mdDialog, $location, $state, $timeout, $q, $http, notifications, $auth, $v6urls , uiInitilize ) {

	var baseUrl = "http://" + window.location.hostname;
	$scope.showGlobalProgress = true;
	$scope.adduser=[];
	$scope.EmailAddress={};

	$scope.emaill=[];
	function loaduser(){
		$scope.searchText="";
		$http.get(baseUrl + "/devportal/project/share/getusers")
		.success(function(data) 
		{
			$scope.adduser=data;
			console.log(data);
			$scope.showGlobalProgress = false;
			$scope.emailadd=[];
			for(var i=0; i<$scope.adduser.length; i++){
				console.log($scope.adduser[i].EmailAddress);
				
				if($scope.emailadd.indexOf($scope.adduser[i]) == -1){
					
					$scope.emailadd.push($scope.adduser[i].EmailAddress);
					
					console.log($scope.emailadd);
				}
				
			}
			
			$scope.EmailAddress={
				email:$scope.emailadd
			};
			
			console.log($scope.EmailAddress);
			
				//getUser's more details of json
				getuserdatabulk();
			});
		
	}
	
	function getuserdatabulk(){
	         //This holds the UI logic for the collapse cards
	         $scope.toggles = {};
	         $scope.toggleOne = function($index) {
	         	$scope.toggles = uiInitilize.openOne(this.users, $index);
	         };
	         
	         $http({
	         	method : 'POST',
	         	url : baseUrl + "/apis/profile/userprofile/getuserdatabulk",
	         	data : $scope.EmailAddress
	         }).then(function(response) {
	         	console.log(response);
	         	console.log(response.data);
	         	$scope.users=response.data;
	         	
	         }, function(response) {
	         	console.log(response);
	         });
	     }
	     
	     loaduser();

	     $scope.enterInviteUser=function(text,ev){
	     	console.log(text);
	     	var typeEmail=text.toLowerCase();
	     	if(typeEmail!=="" && typeEmail!==undefined ){
	     		$scope.checkUserAlredyIn=false;
	     		for(var j=0; j<$scope.adduser.length; j++){
	     			var emailAddress = $scope.adduser[j].EmailAddress;
	     			console.log(emailAddress);
	     			if(emailAddress==typeEmail){
	     				$scope.checkUserAlredyIn=true;
	     				notifications.toast("This user already invited", "error");
	     				break;
	     			}else{
	     				$scope.checkUserAlredyIn=false;
	     			}
	     		}

	     		if(!$scope.checkUserAlredyIn){
	     			var atpos = typeEmail.indexOf("@");
	     			var dotpos = typeEmail.lastIndexOf(".");
	     			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=typeEmail.length) {
	     				notifications.alertDialog("Alert" , "Please enter valid e-mail address");
                    //return false;
                    //break;
                }
                else{
                	$http.get( $v6urls.auth + "/tenant/AddUser/"+typeEmail+"/user")
                	.success(function(data){
                		$scope.user=data;
                		console.log(data);

                		if(data=="true"){
                			console.log(data);
                			notifications.toast("Successfully Invited", "success",3000);
                			loaduser();
                		}
                		else{
                			$mdDialog.show(
                				$mdDialog.alert()
                				.parent(angular.element(document.querySelector('#popupContainer')))
                				.clickOutsideToClose(true)
                				.title('Alert')
                				.content('This Email does not exeist')
                				.ariaLabel('Alert Dialog Demo')
                				.ok('Got it!')
                				.targetEvent(ev)
                				);
                		}

                	});
                }
            }

        }
        else{
        	notifications.toast("Please Enter Email Address", "error");
        }

    };

    $auth.checkSession();

    function loadOwernTenantDetails(){
    	$http.get("/auth/GetSession/"+$auth.getSecurityToken()+"/"+window.location.hostname)
    	.success(function(data){
    		$scope.owernUser=data;
    		console.log($scope.owernUser);
    	}).error(function(){
    		console.log(data);
    	});
    }
    
    loadOwernTenantDetails();

    $scope.removeInviteUser=function(data, ev){
    	console.log(data);
    	var confirm = $mdDialog.confirm()
    	.title('Would you like to delete this User?')
    	.content('This user is deleting')
    	.ariaLabel('Lucky day')
    	.targetEvent(ev)
    	.ok('Please do it!')
    	.cancel('Sounds like a scam');

    	$mdDialog.show(confirm).then(function() {
    		console.log(data);
    		$scope.showGlobalProgress = true;

    		if(data.Email==$scope.owernUser.Email){
    			console.log(data.Email);
    			console.log($scope.owernUser.Email);
    			$scope.showGlobalProgress = false;
    			notifications.alertDialog("Alert", "System will not authorized to delete the tenant owner");
    		}
    		else{
    			
    			$http.get($v6urls.auth + "/tenant/RemoveUser/"+ data.Email) 
    			.success(function(data) 
    			{
    				$scope.removedInviteUser=data;
    				console.log(data);
    				loaduser();
    				$scope.showGlobalProgress = false;
    				notifications.toast("Successfully User Deleted", "success",3000);
    			});
    			
    			$http.get("/apis/usercommon/updateSharedAppsAndGroups/"+ data.Email) 
    			.success(function(data) 
    			{
    				console.log(data);
    			});
    			
    		}
    	});

    };

    //GetPendingTenantRequest....................................................................
    function GetPendingTenantRequests(){
    	$http.get($v6urls.auth + "/tenant/GetPendingTenantRequest")
    	.success(function(data){
    		if(data!=undefined){
    			$scope.getPendingTenant=data;
    			console.log($scope.getPendingTenant);
    		}
    		else{
    			$scope.getPendingTenant=[];
    		}

    	}).error(function(){
    		console.log(data);
    	});
    }
    
    GetPendingTenantRequests();

    //acceptRequest.................................................................
    $scope.acceptPendingrequest=function(pending,ev){
    	console.log(pending);

    	$http.get($v6urls.auth + "/tenant/AcceptRequest/"+pending.Email+"/"+pending.Code+"")
    	.success(function(data){
    		console.log(data);
    		GetPendingTenantRequests();
    	}).error(function(){
    		console.log(data);
    	});

    }


})

app.controller('groupsCtrl', function ($scope, $mdDialog, $location, $state, $timeout, $apps, $q,$http ,uiInitilize ) {
	
	$scope.toggles = {};
	$scope.toggleOne = function($index) {
		$scope.toggles = uiInitilize.openOne(this.getallgroup, $index);
	};
	
	$scope.loading = true;
	var baseUrl = "http://" + window.location.hostname;

	function loadGroupData(){
		$scope.ctrl.searchText="";
		$http.get(baseUrl + "/apis/usercommon/getAllGroups") 
		.success(function(data) 
		{
			if (data) {
				$scope.getallgroup = data;
				console.log(data);
				$scope.loading = false;
			}
		}).error(function(){
			
			alert ("Oops! There was a error");
		});
		
	}

	loadGroupData(function(data){
		$scope.getallgroup = data;
	});

// .............................
$scope.createGroup = function(ev){
	$mdDialog.show({
		controller: 'DialogCreateGroupController',
		templateUrl: 'partials/createGroup.html',
		parent: angular.element(document.body),
		targetEvent: ev,
		clickOutsideToClose:true
	})
	.then(function(answer) {
		loadGroupData(function(data){
			$scope.getallgroup = data;
		});
		
	}, function() {
		$scope.status = 'You cancelled the dialog.';
	});
};

$scope.addNewUsersToGroup = function(getusers, ev){
	console.log(getusers);
	$mdDialog.show({
		controller: 'DialogaddNewUsersToGroupController',
		templateUrl: 'partials/addNewUsersToGroup.html',
		parent: angular.element(document.body),
		targetEvent: ev,
		locals: { getusers: getusers },
		clickOutsideToClose:true
	})
	.then(function(answer) {
		loadGroupData();		
	}, function() {
		$scope.status = 'You cancelled the dialog.';
	});
};

$scope.removeGroup = function(data, ev){
	     //console.log(data);
	     var confirm = $mdDialog.confirm()
	     .title('Would you like to delete this group?')
	     .content('This group is deleting')
	     .ariaLabel('Lucky day')
	     .targetEvent(ev)
	     .ok('Please do it!')
	     .cancel('Sounds like a scam');
	     
	     $mdDialog.show(confirm).then(function() {
	     	console.log( data);
	     	$scope.loading = true;
	     	$http.get(baseUrl + "/apis/usercommon/removeUserGroup/"+ data.groupId) 
	     	.success(function(data) 
	     	{
	     		$scope.deletegroup=data;
	     		console.log(data);
	     		
	     		loadGroupData(function(data){
	     			$scope.getallgroup = data;
	     		});
	     		
	     		$scope.loading = false;
	     		
	     	});
	     	
	     //	http://saumiduosoftwarecom.space.duoworld.com/apis/usercommon/updateSharedAppsAndGroups/2
	     $http.get("/apis/usercommon/updateSharedAppsAndGroups/"+ data.groupId) 
	     .success(function(data) 
	     {
	     	console.log(data);
	     });
	     
	     
	 }, function() {
	 	$scope.status = 'You decided to keep your debt.';
	 });
	     
	 };
	 
	 $scope.selected = [];
	 
	 $scope.toggle = function (user, list) {
	 	var idx = list.indexOf(user);
	 	if (idx > -1) list.splice(idx, 1);
	 	else list.push(user);
	 	console.log(list);
	 	
	 };
	 
	 $scope.exists = function (user, list) {
	 	return list.indexOf(user) > -1;
	 };
	 
	 $scope.deleteSelectedUser = function(data,ev){
	 	console.log(data);
	 	console.log($scope.selected);
	 	
	 	$scope.usersFromeGroup ={};
	 	$scope.usersFromeGroup.groupId =data.groupId;
	 	$scope.usersFromeGroup.users = $scope.selected;

	 	console.log($scope.usersFromeGroup);
	 	
	 	var confirm = $mdDialog.confirm()
	 	.title('Would you like to delete this selected user or users?')
	 	.content('users will be deleting..')
	 	.ariaLabel('Lucky day')
	 	.targetEvent(ev)
	 	.ok('yes')
	 	.cancel('No');
	 	
	 	$mdDialog.show(confirm).then(function() {
	 		$scope.loading = true;
	 		$http({
	 			method : 'POST',
	 			url : baseUrl + "/apis/usercommon/removeUserFromGroup",
	 			data : $scope.usersFromeGroup
	 		}).then(function(response) {
	 			console.log(response);
	 			$mdDialog.hide();
	 			loadGroupData();
	 			$scope.loading = false;
	 		}, function(response) {
	 			console.log(response);
	 		});
	 		
	 	}, function() {
	 		$scope.status = 'You decided to keep your debt.';
	 	});
	 	
	 };
	})

app.controller('shareAppsCtrl', function ($scope, $mdDialog, $location, $state, $timeout, $apps, $q,$http) {
	$scope.showGlobalProgress = true;

	$apps.onAppsRetrieved(function(e,apps){
	 //here are the apps
	 $scope.app=apps.apps;
	 console.log(apps.apps);
	 $scope.showGlobalProgress = false;
	 
	 if($scope.app.length===0){
	 	$scope.showGlobalProgress = true;
	 }

	});

	$apps.getAppsForUser();

	$scope.applicationKey="";
	
	$scope.share=function(appId,appNamee){
		console.log(appId);
		console.log(appNamee);
		$state.go('main.share',{appId: appId , appNamee: appNamee});
	};
	
})

app.controller('shareCtrl', function ($scope, $http, $state, $auth, $objectstore, $v6urls, $apps, $mdDialog, $stateParams, $mdToast, notifications) {

//chips ctrl....................................................................
var baseUrl = "http://" + window.location.hostname;
$scope.loading=true;
$scope.getSharableuserAndGroup= [];	

function loadSharableObject() {
	$http.get(baseUrl + "/apis/usercommon/getSharableObjects")
	.success(function(data) 
	{
		console.log(data);
		$scope.loading=false;
		$scope.getSharableuserAndGroup = data;
		
	}).error(function(){

		alert ("Oops! There was a problem retrieving the User");
	});
}

loadSharableObject();

// chips ctrl CLOSE
$scope.selected=[];
$scope.loadUiShareData=[];

$scope.onDropOne = function (data, event) {
	console.log(data);
	
		// Get custom object data.
		var customObjectData= data['json/custom-object']; // {foo: 'bar'}
		
		$scope.setcustomObjectData={};
		$scope.setcustomObjectData.id=customObjectData.Id;
		$scope.setcustomObjectData.image="img/user.png";
		$scope.setcustomObjectData.name=customObjectData.Name;
		$scope.setcustomObjectData.type=customObjectData.Type;
		
		console.log($scope.setcustomObjectData);
		// Get other attached data.
		var uriList = data['text/uri-list'];
	   // console.log(uriList);
	   
	   for (ind in $scope.selected) 
	   {
	   	console.log($scope.selected[ind].id);
	   	
	   	if($scope.selected[ind].id == $scope.setcustomObjectData.id)
	   	{
	   		$scope.selected.splice(ind,1);
	   	}
	   }

	   if($scope.loadUiShareData.length !== 0){

	   	for(var j=0; j<$scope.loadUiShareData.length; j++){
	   		if($scope.setcustomObjectData.id !== $scope.loadUiShareData[j].id){
	   			$scope.selected.push($scope.setcustomObjectData);
	   			break;
	   		}
	   		else{
	   			$scope.selected.push($scope.setcustomObjectData);
	   			console.log($scope.selected);
	   			
	   		}
	   	}

	   } 
	   else {
	       //$scope.selected.push($scope.setcustomObjectData);
	       if($scope.selected.length ==0)
	       {
	       	$scope.selected.push($scope.setcustomObjectData);  
	       	console.log($scope.selected);
	       	
	       }
	       else{
	       	$scope.selected.push($scope.setcustomObjectData);  
	       	console.log($scope.selected);
	       }
	   }
	};
	
	$scope.appkey = $stateParams.appId;
	$scope.name = $stateParams.appNamee;
	console.log($scope.name);
	console.log($scope.appkey);
	
	$scope.shareUser = function(data) {
		console.log(data);

		$scope.shareData =data;
		console.log($scope.shareData);

	//This post method for save UI share data
	$http({
		method : 'POST',
		url : baseUrl + "/apis/usercommon/saveUiShareData/" +$scope.appkey,
		data : $scope.shareData
	}).then(function(response) {
		console.log(response);
		if(response){
			// $mdToast.show(
			// 	$mdToast.simple()
			// 	.textContent('Shared Users Suceesfully!')
			// 	.position($scope.getToastPosition())
			// 	.hideDelay(3000)
			// 	); 
			notifications.toast("Shared Successfully!", "success",3000);		
		}
		$mdDialog.hide();
	}, function(response) {
		console.log(response);
	});

};

//loadShare data according to app key         
function loadShareData() {
	$http.get(baseUrl + "/apis/usercommon/loadUiShareData/"+ $scope.appkey)
	.success(function(data) 
	{
		console.log(data);

		if(data.length !== 0){
			$scope.loadUiShareData=data;
			$scope.selected = $scope.loadUiShareData;
			console.log($scope.selected);
			console.log(data);
		}
		else{
			loadSharableObject();
			$scope.selected = [];
			$scope.loadUiShareData=[];
		    	//createAllUserAndGroup(data);
		    }

		}).error(function(){

			alert ("Oops! There was a problem retrieving the User");
		});
	}

	loadShareData();
	
	$scope.deleteApp= function(ev, data){
		console.log(data);
		for (ind in $scope.selected) 
		{
			console.log($scope.selected[ind].id);
			
			if($scope.selected[ind].id == data.id)
			{
				$scope.selected.splice(ind,1);
				console.log($scope.selected);
			}
		}
	};
	
})

app.controller('DialogCreateGroupController',['$scope', '$http' ,'$mdDialog', '$objectstore',  '$auth', '$v6urls', '$mdToast','notifications' , function ($scope, $http ,$mdDialog, $objectstore,  $auth, $v6urls, $mdToast,notifications){
	
	$scope.getCatLetter=function(catName){
		try{
			var catogeryLetter = "/img/material alperbert/avatar_tile_"+catName.charAt(0).toLowerCase()+"_28.png";
		}catch(exception){}
		return catogeryLetter;
	};
	
	var baseUrl = "http://" + window.location.hostname;
	
	function loadGroupData(){
		$http.get(baseUrl + "/apis/usercommon/getAllGroups") 
		.success(function(data) 
		{
        // 		$scope.getallgroup = data;
        if (data) {
        	$scope.getallgroup = data;
        	console.log(data);
        }
    }).error(function(){
    	
    	alert ("Oops! There was a error");
    });
}
loadGroupData();

$auth.checkSession();

function loadOwernTenantDetails(){
	$http.get("/auth/GetSession/"+$auth.getSecurityToken()+"/"+window.location.hostname)
	.success(function(data){
		$scope.owernUser=data;
		console.log($scope.owernUser);
	}).error(function(){
		console.log(data);
	});
}

loadOwernTenantDetails();
$scope.loading=true;
function loadSharableObject() {
	$http.get(baseUrl + "/devportal/project/share/getusers")
	.success(function(data) 
	{
		console.log(data);
		$scope.getSharableuserAndGroup = data;
		for(var i=0; i<data.length; i++){
			console.log(data[i].EmailAddress);
			if(data[i].EmailAddress==$scope.owernUser.Email){
				console.log(data[i].EmailAddress);
				$scope.contact=[];
				$scope.contact.email=data[i].EmailAddress;
				$scope.contact.name=data[i].Name;
				$scope.contact.image="/img/material alperbert/avatar_tile_"+data[i].Name.charAt(0).toLowerCase()+"_28.png";
				$scope.selectedUsers.push($scope.contact);
				$scope.loading=false;
				data.splice(i,1);
				console.log(data);
				
	           //break;
	       }
	   } 
	   if(data.length > 0){
	   	createAllUserAndGroup(data);
	   	console.log(data);
	   }
	}).error(function(){
		notifications.alertDialog("Alert", "Oops! There was a problem retrieving the User");
	});
}

function createAllUserAndGroup(data){
	console.log(data);

	$scope.allContacts = data.map(function (c, index) {
		var contact = {
			name: c.Name,
			email: c.EmailAddress,
			image:"/img/material alperbert/avatar_tile_"+c.Name.charAt(0).toLowerCase()+"_28.png"
		};
		contact._lowername = contact.name.toLowerCase();
		return contact;
	});
	
// 	if ($scope.allContacts.length === 0 ){
// 		$scope.allContacts = 
// 		[{"_lowername":"administrator" ,"email":"admin@duoweb.info","image":"img/user.png","name":"Administrator"}]; 
// 	}
}

function querySearch (query) {
	return query ? $scope.allContacts.filter(createFilterFor(query)) : [];
}

function createFilterFor(query) {
	return function filterFn(contact) {
		return (contact._lowername.indexOf(angular.lowercase(query)) != -1);
	};
}

$scope.querySearch = querySearch;
$scope.allContacts = [];
$scope.contacts = [];
$scope.filterSelected = true;

loadSharableObject();

$scope.userGroup ={};

$scope.userGroup.groupId = "-999";
$scope.userGroup.groupname="";
$scope.userGroup.users = [];
$scope.userGroup.parentId = "";
$scope.selectedUsers = [];

if($scope.loading===false){
	$scope.check=true;
}

$scope.SaveGroup = function() {
	console.log($scope.selectedUsers);
	
		//post user group name and users
		$scope.userGroup.users = $scope.selectedUsers.map(function(obj,index){
			console.log(obj);
			obj.email._lowername = obj.email.toLowerCase();
			return obj.email;
		});
		
		$scope.textValidate=false;
		
		if($scope.userGroup.groupname!="" && $scope.userGroup.groupname !=undefined ){
			for(var j=0; j<$scope.getallgroup.length; j++){
				var groupname = $scope.getallgroup[j].groupname;
				var newname = $scope.userGroup.groupname;
				if(groupname==newname){
					$scope.textValidate=true;
					$scope.erromessage="Group Name already use. Try another Group Name";
					break;
				}else{
					$scope.textValidate=false;
				}
			}
			
			if(!$scope.textValidate){
				$scope.check=true;
				$http({
					method : 'POST',
					url : baseUrl + "/apis/usercommon/addUserGroup",
					data : $scope.userGroup
				}).then(function(response) {
					console.log(response);
					// $mdToast.show(
					// 	$mdToast.simple()
					// 	.textContent( data.Name + 'Successfully Group Added')
					// 	.position($scope.getToastPosition())
					// 	.hideDelay(3000)
					// 	); 
					notifications.toast("Group is added successfully", "success",3000);
					$mdDialog.hide();
				}, function(response) {
					console.log(response);
				});
			}
			
		}else{
			$scope.textValidate=true;
			$scope.erromessage="Enter Group Name";
		}
		
		console.log($scope.userGroup);
		
	};

	$scope.hide = function() {
		$mdDialog.hide();
	};

	$scope.cancel = function() {
		$mdDialog.cancel();
	};

}]);

app.controller('DialogaddNewUsersToGroupController',['$scope', '$http' ,'$mdDialog', '$objectstore',  '$auth', '$v6urls', 'getusers' , '$mdToast','notifications' , function ($scope, $http ,$mdDialog, $objectstore, $v6urls, getusers, $mdToast, notifications){	
	var baseUrl = "http://" + window.location.hostname;
	
	console.log(getusers);

	function loadSharableObject() {
		$http.get(baseUrl + "/devportal/project/share/getusers")
		.success(function(data) 
		{
			console.log(data);
			$scope.getSharableuserAndGroup = data;
			
			if(data.length > 0){
				createAllUserAndGroup(data);
				console.log(data);
			}
		}).error(function(){

			alert ("Oops! There was a problem retrieving the User");
		});
	}

	function createAllUserAndGroup(data){
		$scope.allContacts = data.map(function (c, index) {
			var contact = {
				name: c.Name,
				email: c.EmailAddress,
				image:"/img/material alperbert/avatar_tile_"+c.Name.charAt(0).toLowerCase()+"_28.png",
				type:c.Type
			};

			contact._lowername = contact.name.toLowerCase();
			return contact;
		});

		if ($scope.allContacts.length === 0 ){
			$scope.allContacts = 
			[{"_lowername":"administrator" ,"email":"admin@duoweb.info","image":"img/user.png","name":"Administrator"}]; 
		}
	}

	function querySearch (query) {
		return query ? $scope.allContacts.filter(createFilterFor(query)) : [];
	}

	function createFilterFor(query) {
		return function filterFn(contact) {
			return (contact._lowername.indexOf(angular.lowercase(query)) != -1);
		};
	}

	$scope.querySearch = querySearch;
	$scope.allContacts = [];
	$scope.contacts = [];
	$scope.filterSelected = true;

	loadSharableObject();
	
	$scope.addUsersTogroup ={};
	
	$scope.addUsersTogroup.groupId = getusers.groupId;
	$scope.addUsersTogroup.users = [];
	
	$scope.selectedUsers=[];
	
	$scope.AddUserToGroup = function(){
		
		console.log($scope.selectedUsers);
		
		for(var i=0; i < getusers.users.length; i++){
			console.log(getusers.users[i]);
			for(var s=0; s<$scope.selectedUsers.length;s++)
			{
				console.log($scope.selectedUsers[s].email);
				
				if(getusers.users[i] == $scope.selectedUsers[s].email)
				{
					$scope.selectedUsers.splice(s,1);
					console.log($scope.selectedUsers);
					break;
				}
				
			}
			
		}
		
			//post user group name and users
			$scope.addUsersTogroup.users = $scope.selectedUsers.map(function(obj,index){
				console.log(obj);
		//	obj.email._lowername = obj.email.toLowerCase();
		return obj.email;
	});
			
			console.log($scope.addUsersTogroup);
			
			$http({
				method : 'POST',
				url : baseUrl + "/apis/usercommon/addUserToGroup",
				data : $scope.addUsersTogroup
			}).then(function(response) {
				console.log(response.config.data.users);
				if(response.config.data.users.length!==0){
					notifications.toast("User or Users added successfully to the Group", "success",3000);
				}
				$mdDialog.hide();
			}, function(response) {
				console.log(response);
			});
			
		};
		
		$scope.hide = function() {
			$mdDialog.hide();
		};

		$scope.cancel = function() {
			$mdDialog.cancel();
		};

	}]);






