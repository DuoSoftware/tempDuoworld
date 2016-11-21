// 'use strict';

var p_entry_module = angular.module("platformEntryModule", ["ui.router","uiMicrokernel","ngMaterial","ngMessages" ,"vcRecaptcha"]);

p_entry_module.run(['$templateCache', function($templateCache){
	$templateCache.put("forgotpassword.html","<!-- forgotpassword-partial.html -->\r\n<md-card id=\"partial-animation\" class=\"commonContentShell md-whiteframe-12dp\" layout=\"column\" style=\"max-width:425px;margin: 0 auto\" layout-align=\"start center\">\r\n	<section id=\"cygilContainer\" layout=\"column\" layout-align=\"center center\">\r\n		<img src=\"loginDuo.png\"/>\r\n	</section>\r\n	<section id=\"formContainer\" layout=\"column\" layout-align=\"start start\">\r\n		<form name=\"forgotpasswordForm\" ng-submit=\"submitForgotPasswordDetails()\" autocomplete=\"off\"> 	\r\n			<md-input-container md-no-float class=\"md-block\">\r\n				<input required id=\"email\" type=\"email\" name=\"email\" placeholder=\"registered email address\" ng-model=\"forgotpassworddetails.email\">\r\n			</md-input-container>							\r\n			<div class=\"md-actions\" layout=\"column\" layout-align=\"center center\">\r\n				<md-button class=\"md-raised md-primary\" style=\"width:100%\" type=\"submit\"><span class=\"loginBtnLabel\">reset password</span></md-button>\r\n			</div>\r\n		</form>\r\n		<div class=\"secondaryAction\" layout=\"column\" layout-align=\"center center\">\r\n			<span ng-click=\"switchEntryView(\'signin\')\">go back?</span>\r\n		</div>\r\n	</section>\r\n</md-card>");
	$templateCache.put("signin.html","<!-- signin-partial.html -->\r\n<md-card id=\"partial-animation\"  class=\"commonContentShell md-whiteframe-12dp\" layout=\"column\" style=\"max-width:425px;margin: 0 auto\" layout-align=\"center center\">\r\n	<section id=\"cygilContainer\" layout=\"column\" layout-align=\"center center\">\r\n		<img src=\"loginDuo.png\"/>\r\n	</section>\r\n	<section id=\"formContainer\" layout=\"column\" layout-align=\"center center\">\r\n		<form name=\"signinForm\" ng-submit=\"submitSigninDetails()\" autocomplete=\"off\"> 	\r\n			<div layout=\"row\" layout-align=\"start start\" style=\"padding: 6px;background:#5ac55f;border-radius: 3px;\" ng-if=\"activated == \'true\'\">\r\n		\r\n				<img src=\"checked.png\" style=\"width:30px;height:30px;\"></img>\r\n				<span style=\"padding-left: 20px;color:white;font-weight:700;font-size: 16px; margin: 5px;\">Your acount is activated.</span>\r\n	\r\n			</div>\r\n			<div layout=\"row\" layout-align=\"start start\" style=\"padding: 6px;background:#FF5252;border-radius: 3px;\" ng-if=\"activated == \'false\'\">\r\n				<img src=\"error.png\" style=\"width:30px;height:30px;\"></img>\r\n				<span style=\"padding-left: 20px;color:white;font-weight:700;font-size: 16px; margin: 5px;\">Your acount was not activated. Please try again later.</span>\r\n			</div>\r\n			\r\n			<div ng-if=\"activated == \'true\' || !activated\">\r\n				<md-input-container md-no-float class=\"md-block\">\r\n					<input required id=\"username\" type=\"email\" name=\"username\" placeholder=\"email\" ng-model=\"signindetails.Username\">\r\n					<div ng-messages=\"signinForm.username.$error\">\r\n						<div ng-message=\"required\">your registered email is required !</div>\r\n					</div>\r\n				</md-input-container>				\r\n				<md-input-container md-no-float class=\"md-block\">\r\n					<input required id=\"password\" type=\"password\" name=\"password\" placeholder=\"password\" ng-model=\"signindetails.Password\">\r\n					<div ng-messages=\"signinForm.password.$error\">\r\n						<div ng-message=\"required\">pleas enter a valid password.</div>\r\n					</div>\r\n				</md-input-container>			\r\n				<div class=\"md-actions\" layout=\"column\" layout-align=\"center center\">\r\n					<md-button class=\"md-raised md-primary\" style=\"width:100%\" type=\"submit\" ng-disabled=\"signinForm.$invalid\"><span class=\"loginBtnLabel\">sign in</span></md-button>\r\n				</div>\r\n			</div>\r\n		</form>\r\n		<div class=\"socialActions\" layout=\"column\" layout-align=\"center center\">\r\n			<span>or signin with your social account<span>\r\n		</div>\r\n		<div class=\"socialBtnContainer\" layout=\"row\" layout-align=\"center center\">\r\n			<md-button md-no-ink class=\"md-primary twitter_btn\" ng-click=\"social_signin(\'/signup/twitterRegister/?connect=twitter\')\">\r\n				<md-icon md-svg-src=\"twitter-box.svg\" style=\"fill:#60b8fd;\"></md-icon>\r\n				Twitter\r\n			</md-button>\r\n			<md-button md-no-ink class=\"md-primary google_btn\" ng-click=\"social_signin(\'/signup/googlePlusRegister\')\">\r\n				<md-icon md-svg-src=\"google-plus-box.svg\" style=\"fill:#f13c37;\"></md-icon>\r\n				Google +\r\n			</md-button>\r\n		</div>\r\n        <div class=\"secondaryAction\" layout=\"column\" layout-align=\"center center\" ng-if=\"activated == \'true\' || !activated\">\r\n			<span ng-click=\"switchEntryView(\'forgotpassword\')\">unable to access your account? </span>\r\n		</div>\r\n		<div class=\"secondaryAction\" layout=\"column\" layout-align=\"center center\" ng-if=\"activated == \'true\' || !activated\">\r\n			<span ng-click=\"switchEntryView(\'signup\')\">not a member? <i>Sign up</i></span>\r\n		</div>\r\n		</div>\r\n	</section>\r\n</md-card>\r\n");
	$templateCache.put("signUp.html","<!-- signin-partial.html -->\r\n<md-card id=\"partial-animation\" class=\"commonContentShell md-whiteframe-12dp\" layout=\"column\" layout-align=\"start center\" style=\"max-width:425px;margin: 0 auto\" ng-switch=\"signupsuccess\">\r\n	<section id=\"cygilContainer\" layout=\"column\" layout-align=\"center center\">\r\n		<img src=\"loginDuo.png\"/>\r\n	</section>\r\n	<section id=\"formContainer\" layout=\"column\" layout-align=\"start start\" ng-switch-when=\"false\">\r\n		<form name=\"signupForm\" ng-submit=\"submitSignupDetails(signupdetails)\" autocomplete=\"off\">\r\n			\r\n			<md-input-container md-no-float class=\"md-block\">\r\n				<input required id=\"name\" type=\"text\" name=\"userName\" placeholder=\"name*\" ng-model=\"signupdetails.Name\">\r\n				<div ng-messages=\"signupForm.userName.$error\">\r\n					<div ng-message=\"required\">your name is required.</div>\r\n				</div>\r\n			</md-input-container> 	\r\n			<md-input-container md-no-float class=\"md-block\">\r\n				<input required=\"\" id=\"email\" type=\"email\" name=\"userEmail\" placeholder=\"email*\" ng-model=\"signupdetails.EmailAddress\" ng-pattern=\"/^.+@.+\\..+$/\" ng-disabled=\"isinvited\">\r\n				<div ng-messages=\"signupForm.userEmail.$error\" role=\"alert\">\r\n					<div ng-message-exp=\"[\'required\',\'pattern\']\">a valid email address is required.</div>\r\n				</div>\r\n			</md-input-container>\r\n	\r\n			\r\n			<md-input-container md-no-float class=\"md-block\">\r\n				<input password-strength-indicator required id=\"password\" type=\"password\" name=\"userPassword\" placeholder=\"password*\" ng-model=\"signupdetails.Password\" ng-pattern=\"/^[^<>;\\/?,\']+$/\">\r\n				<div ng-messages=\"signupForm.userPassword.$error\" multiple>\r\n					<div ng-message=\"required\" class=\"my-message\">A valid password is required.</div>\r\n					<div ng-message=\"pattern\" class=\"my-message\">password cannot contain ? < > ; / , \' \")</div>\r\n				</div>\r\n			</md-input-container>\r\n			\r\n			<div layout=\"row\" layout-align=\"center center\">\r\n				<div vc-recaptcha\r\n					 theme=\"\'light\'\"\r\n					 key=\"model.key\"\r\n					 on-create=\"setWidgetId(widgetId)\"\r\n					 on-success=\"setResponse(response)\"\r\n					 on-expire=\"cbExpiration()\"></div>\r\n			</div>	\r\n			<div class=\"socialActions\" layout=\"column\" layout-align=\"start start\" ng-init=\"signupdetails.Terms = false\">\r\n				<md-checkbox ng-model=\"signupdetails.Terms\" aria-label=\"Checkbox 1\" style=\"margin:10px;font-size: 14px;\">\r\n					 Do you agree to the <i style=\"color: rgb(63,81,181)\" ng-click=\"switchToTermsAndConditions();$event.stopPropagation()\">Terms and Conditions</i>?\r\n				</md-checkbox>\r\n			</div>\r\n			<div class=\"md-actions\" layout=\"column\" layout-align=\"center center\">\r\n				<md-button class=\"md-raised md-primary\" style=\"width:100%\" type=\"submit\" ng-disabled=\"signupForm.$invalid\"><span class=\"loginBtnLabel\">sign up</span></md-button>\r\n			</div>\r\n		</form>\r\n		<!--div class=\"socialActions\" layout=\"column\" layout-align=\"center center\">\r\n			<span>or signup with your social account<span>\r\n		</div>\r\n		<div class=\"socialBtnContainer\" layout=\"row\" layout-align=\"center center\">\r\n			<md-button md-no-ink class=\"md-primary facebook_btn\">\r\n				<md-icon md-svg-src=\"facebook-box.svg\" style=\"fill:#2e79db;\"></md-icon>\r\n				Facebook\r\n			</md-button>\r\n			<md-button  md-no-ink class=\"md-primary twitter_btn\">\r\n				<md-icon md-svg-src=\"twitter-box.svg\" style=\"fill:#60b8fd;\"></md-icon>\r\n				Twitter\r\n			</md-button>\r\n			<md-button md-no-ink class=\"md-primary google_btn\">\r\n				<md-icon md-svg-src=\"google-plus-box.svg\" style=\"fill:#f13c37;\"></md-icon>\r\n				Google +\r\n			</md-button>\r\n		</div-->\r\n		<div class=\"secondaryAction\" layout=\"column\" layout-align=\"center center\">\r\n			<span ng-click=\"switchEntryView(\'signin\')\">already a member? <i>Sign in</i></span>\r\n		</div>\r\n	</section>\r\n	<section id=\"successContainer\" layout=\"column\" layout-align=\"center center\" ng-switch-when=\"true\">\r\n		<img src=\"success.png\" style=\"width:100px; height:100px; opacity:0.2;\"/>\r\n		<span style=\"font-size:18px; width:330px; text-align:center; padding:20px 0px 10px 0px;\">You have been successfully registered to DuoWorld.</span>\r\n		<span style=\"font-size:16px; width:330px; text-align:center; padding:10px 0px 10px 0px; color:rgb(63,81,181);\" ng-click=\"locateUserEmail()\">Please check your email to get started.</span>\r\n	</section>\r\n</md-card>");
}]);

//Platform entry view route configuration - Start
p_entry_module.config(['$stateProvider','$urlRouterProvider', function($sp, $urp){
	$urp.otherwise('/signin');
	$sp
	.state('signin', {
		url: '/signin?activated',
		templateUrl: 'signin.html',
		controller: 'platformEntry-signin-ctrl'
	})
	.state('signup', {
		url: '/signup?email&code',
		templateUrl: 'signUp.html',
		controller: 'platformEntry-signup-ctrl'
	})
	.state('forgotpassword', {
		url:'/forgotpassword',
		templateUrl: 'forgotpassword.html',
		controller: 'platformEntry-forgotpassword-ctrl'
	})
	.state('termsAndConditions', {
		url:'/termsAndConditions',
		templateUrl: 'termsAndConditions.html',
		controller: 'platformEntry-termsAndConditions-ctrl'
	})
}]);
//Platform entry view route configuration - End

//Sign in view Controller - Start
p_entry_module.controller("platformEntry-signin-ctrl", 
                          ["$window","$scope","$stateParams","$http","$state","$location","$mdDialog","$helpers",
                           function ($window,$scope,$stateParams,$http,$state,$location,$mdDialog,$helpers){

	if($stateParams.activated) {
		//console.log($stateParams.activated);
        $scope.activated = $stateParams.activated;
    }					   
	
	$scope.signindetails = {};

	var authorizationSuccessFull = function(sectoken){
		$window.location.href = "/s.php?securityToken=" + sectoken;
	};

	var clearSigninDetails = function(){
		$scope.signindetails.Password = ""
	};

	var displaySigninCredentialsError = function(message){
	    $mdDialog.show(
	      $mdDialog.alert()
	        .parent(angular.element(document.body))
	        .clickOutsideToClose(true)
	        .title('Logon Denied')
	        .textContent(message)
	        .ariaLabel('Signin error.')
	        .ok('Got it!')
	    );
	};

    var displaySigninSubmissionProgression = function(){
    	$mdDialog.show({
	      template: 
	      	'<md-dialog ng-cloak>'+
    		'	<md-dialog-content>'+
      		'		<div class="loadInidcatorContainer" layout="row" layout-align="start center">'+
      		'			<md-progress-circular class="md-accent" md-mode="indeterminate" md-diameter="40"></md-progress-circular>'+
      		'			<span>Authenticating, please wait...</span>'+
      		'		</div>'+
    		'	</md-dialog-content>'+
			'</md-dialog>',
	      parent: angular.element(document.body),
	      clickOutsideToClose:false
	    });
    };

	$scope.submitSigninDetails = function(){
		var payload = angular.toJson($scope.signindetails);

		console.log(payload);

	    displaySigninSubmissionProgression();

		$http({
			method: 'POST',
			url: '/apis/authorization/userauthorization/login',
			headers: {'Content-Type':'application/json'},
			data: payload
		})
		.success(function(data){
			$mdDialog.hide();
			if(data.Success === false){
				displaySigninCredentialsError(data.Message);
				clearSigninDetails();
			}else{
				authorizationSuccessFull(data.Data.SecurityToken);
			}
			console.log(data);
		})
		.error(function(data){
			console.log(data);
			$mdDialog.hide();
		});
	};
	
	$scope.switchEntryView = function(stateinchange){
		$state.go(''+stateinchange+'');
	};
	
	$scope.social_signin = function(url) {
		$window.location.href = url;
	}

}]);
//Sign in view Controller - End

//Sign up view Controller - Start
p_entry_module.controller("platformEntry-signup-ctrl", 
                          ["$window","$scope","$http","$state","$stateParams","$location","$mdDialog","$mdMedia", "vcRecaptchaService", "notifications", 
                           function ($window,$scope,$http,$state,$stateParams,$location,$mdDialog,$mdMedia,vcRecaptchaService, notifications){

	//recaptcha
	$scope.response = null;
	$scope.widgetId = null;
	$scope.model = {
		key: '6LeMjiITAAAAAAtRk0u9p6EOE5RJWxCnG4d3KPvr'
	};
	$scope.setResponse = function (response) {
		console.info('Response available');
		$scope.response = response;
	};
	$scope.setWidgetId = function (widgetId) {
		console.info('Created widget ID: %s', widgetId);
		$scope.widgetId = widgetId;
	};
	$scope.cbExpiration = function() {
		console.info('Captcha expired. Resetting response object');
		$scope.response = null;
	 }; 
						  
	$scope.signupdetails = {};

	$scope.signupsuccess = false;
    $scope.isinvited = false;
        
    if($stateParams.email && $stateParams.code) {
        $scope.isinvited = true;
        $scope.signupdetails.EmailAddress = $stateParams.email;
    }

	var getUserEmailDomain = function(){
		var val = $scope.signupdetails.EmailAddress;
		return val.substr(val.indexOf("@") + 1);
	};

	var displaySignupDetailsError = function(message){
	    $mdDialog.show(
	      $mdDialog.alert()
	        .parent(angular.element(document.body))
	        .clickOutsideToClose(true)
	        .title('Incorrect registration details !')
	        .textContent(''+message+'')
	        .ariaLabel('Signup error.')
	        .ok('Got it!')
	    );
	};

	var displaySignupSubmissionProgression = function(){
    	$mdDialog.show({
	      template: 
	      	'<md-dialog ng-cloak>'+
    		'	<md-dialog-content>'+
      		'		<div class="loadInidcatorContainer" layout="row" layout-align="start center">'+
      		'			<md-progress-circular class="md-accent" md-mode="indeterminate" md-diameter="40"></md-progress-circular>'+
      		'			<span>Submitting your details, please wait...</span>'+
      		'		</div>'+
    		'	</md-dialog-content>'+
			'</md-dialog>',
	      parent: angular.element(document.body),
	      clickOutsideToClose:false
	    });
    };

	var addConfirmPasswordString = function(){
		$scope.signupdetails['ConfirmPassword'] = $scope.signupdetails.Password;
	};	
                               
    var registerUser = function(payload) {
		return $http({
			method: 'POST',
			url: '/apis/authorization/userauthorization/userregistration',
			headers: {'Content-Type':'application/json'},
			data: payload
		});
	}	

	var joinToTenant = function(email, token) {
		return $http({
			method: 'GET',
			url: '/apis/usertenant/tenant/request/accept/' + email + '/' + token,
			headers: {'Content-Type':'application/json'},
		});
	}
	
	$scope.submitSignupDetails = function(){

		addConfirmPasswordString();

		var payload = angular.copy($scope.signupdetails);

		console.log(payload);
		
		if($scope.signupdetails.Terms == true)
		{
			delete payload.Terms;
			
			displaySignupSubmissionProgression();
            
            registerUser(payload).success(function(response) { // register a user
				if(response.Success) { // user registration success
					if($scope.isinvited) { //user invited for a tenant
						joinToTenant(payload.EmailAddress, $stateParams.code).success(function(response) { // bind user to the invited tennt
							$mdDialog.hide();
							if(response.Success) // invitation accepted
								$scope.signupsuccess = true; // signup process and tenant request acceptation compeleted
							else
								displaySignupDetailsError(response.Message); // display signup error
						}).error(function(error) {
							console.log(error);
						});
					} else {
						$mdDialog.hide();
						$scope.signupsuccess = true; // signup process compeleted 
					}
				} else {
					$mdDialog.hide();
					displaySignupDetailsError(response.Message);
				}
			}).error(function(error) {
				console.log(error);
			});
			
            /*
			$http({
				method: 'POST',
				url: '/apis/authorization/userauthorization/userregistration',
				headers: {'Content-Type':'application/json'},
				data: payload
			})
			.success(function(data){
				$mdDialog.hide();
				console.log(data);
				if(data.Success === false){
					displaySignupDetailsError(data.Message);	
				}else{
					$scope.signupsuccess = true;
				}

			})
			.error(function(data){
				$mdDialog.hide();
				console.log(data);
				$scope.signupsuccess = false;
			});
			*/
		}else{
			notifications.toast("Please agree to the terms and conditions", "error" );
		}
	};

	$scope.switchEntryView = function(stateinchange){
		$state.go(''+stateinchange+'');
	};
	
	$scope.switchToTermsAndConditions = function(){
		var newStr = window.location.origin+ window.location.pathname;
		console.log(newStr + "#/termsAndConditions");
		window.open(newStr + "#/termsAndConditions" ,'_blank');
	};
}]);
//Sign up view Controller - End

//Password Change View Controller - Start
p_entry_module.controller("platformEntry-forgotpassword-ctrl", ["$window","$scope","$http","$state","$location","$mdDialog","$mdMedia", "notifications",function ($window,$scope,$http,$state,$location,$mdDialog,$mdMedia, notifications){
	console.log('this is the new forgotpassword controller !');

	$scope.forgotpassworddetails = {};

	var displayResetPasswordProgression = function(){
    	$mdDialog.show({
	      template: 
	      	'<md-dialog ng-cloak>'+
    		'	<md-dialog-content>'+
      		'		<div class="loadInidcatorContainer" layout="row" layout-align="start center">'+
      		'			<md-progress-circular class="md-accent" md-mode="indeterminate" md-diameter="40"></md-progress-circular>'+
      		'			<span>Resetting, please wait...</span>'+
      		'		</div>'+
    		'	</md-dialog-content>'+
			'</md-dialog>',
	      parent: angular.element(document.body),
	      clickOutsideToClose:false
	    });
    };

	var displayResetPasswordError = function(){
	    $mdDialog.show(
	      $mdDialog.alert()
	        .parent(angular.element(document.body))
	        .clickOutsideToClose(true)
	        .title('Password reset error !')
	        .textContent('')
	        .ariaLabel(' error.')
	        .ok('Got it!')
	    );
	};

    $scope.submitForgotPasswordDetails = function(){

    	displayResetPasswordProgression();

        $http({
			method: 'GET',
			url: '/apis/authorization/userauthorization/forgotpassword/' + $scope.forgotpassworddetails.email,
			headers: {'Content-Type':'application/json'},
		})
		.success(function(data){
			$mdDialog.hide();
			if(data.Success === false){
				notifications.toast(data.Message, "error" );
			}else{
				
				notifications.toast("The password was resetted, a new password was sent to your email.", "success" );
				$scope.switchEntryView('signin');
			}
			
		})
		.error(function(data){
			$mdDialog.hide();
			console.log(data);
		});
    }

	$scope.switchEntryView = function(stateinchange){
		$state.go(''+stateinchange+'');
	};
}]);
//Password Change View Controller - End

window.directiveResources = {};

p_entry_module.service('notifications',['$mdToast','$mdDialog', function($mdToast,$mdDialog){
	this.toast = function(content,status, delay) {
			
			window.directiveResources.toastRef = $mdToast;
			
			if(!delay)
				delay = 2000;
			 $mdToast.show({
            	template: '<md-toast class="md-toast-'+status+'"><span flex>'+content+' </span> <md-button  style="margin-left: 20px !important;" onclick="(function(e){ window.directiveResources.toastRef.hide() })(event)">Close</md-button></md-toast>',
            	hideDelay: delay,
            	position: 'bottom right'
            });
		};
}])

//Password Strength Directive - Start
p_entry_module.directive('passwordStrengthIndicator',passwordStrengthIndicator);

function passwordStrengthIndicator() {
    return {
        restrict: 'A',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function (scope, element, attrs, ngModel) {

        	scope.strengthText = "";

            var strength = {
                measureStrength: function (p) {
                    var _passedMatches = 0;
                    var _regex = /[$@&+#-/:-?{-~!^_`\[\]]/g;
                    if (/[a-z]+/.test(p)) {
                        _passedMatches++;
                    }
                    if (/[A-Z]+/.test(p)) {
                        _passedMatches++;
                    }
                    if (_regex.test(p)) {
                        _passedMatches++;
                    }
                    return _passedMatches;
                }
            };

            var indicator = element.children();
            var dots = Array.prototype.slice.call(indicator.children());
            var weakest = dots.slice(-1)[0];
            var weak = dots.slice(-2);
            var strong = dots.slice(-3);
            var strongest = dots.slice(-4);

            element.after(indicator);

            var listener = scope.$watch('ngModel', function (newValue) {
                angular.forEach(dots, function (el) {
                    el.style.backgroundColor = '#EDF0F3';
                });
                if (ngModel.$modelValue) {
                    var c = strength.measureStrength(ngModel.$modelValue);
                    if (ngModel.$modelValue.length > 7 && c > 2) {
                        angular.forEach(strongest, function (el) {
                            el.style.backgroundColor = '#039FD3';
                            scope.strengthText = "is very strong";
                        });
                   
                    } else if (ngModel.$modelValue.length > 5 && c > 1) {
                        angular.forEach(strong, function (el) {
                            el.style.backgroundColor = '#72B209';
                            scope.strengthText = "is strong";
                        });
                    } else if (ngModel.$modelValue.length > 3 && c > 0) {
                        angular.forEach(weak, function (el) {
                            el.style.backgroundColor = '#E09015';
                            scope.strengthText = "is weak";
                        });
                    } else {
                        weakest.style.backgroundColor = '#D81414';
                        scope.strengthText = "is very weak";
                    }
                }
            });

            scope.$on('$destroy', function () {
                return listener();
            });
        },
        template: '<span id="password-strength-indicator"><span></span><span></span><span></span><span></span><md-tooltip>password strength {{strengthText}}</md-tooltip></span>'
    };
}
//Password Strength Directive - End


