var app = angular.module('Fsignup', ['uiMicrokernel', 'ngMaterial', 'ngMessages']);

app.controller('MainCtrl', ['$scope', '$mdToast', '$animate', '$http', '$objectstore', '$v6urls', function ($scope, $mdToast, $animate, $http, $objectstore, $v6urls) {
    var delay = 2000;
    $scope.User_Name = "";
    $scope.User_Email = "";
    $scope.submit = function () {
        if ($scope.user.password == $scope.user.confirmPassword) {
            var SignUpBtn = document.getElementById("mySignup").disabled = true;
            var fullname = $scope.user.firstName + " " + $scope.user.lastName;
            $scope.user = {
                "EmailAddress": $scope.user.email,
                "Name": fullname,
                "Password": $scope.user.password,
                "ConfirmPassword": $scope.user.confirmPassword,
                "Active":false
            };
            console.log($scope.user);

            $http({
                method: 'POST',
                url: $v6urls.auth + "/UserRegistation/",
                data: angular.toJson($scope.user),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }

            }).success(function (data, status, headers, config) {
                $scope.User_Name = data.Name;
                $scope.User_Email = data.EmailAddress;
                //setting the name of the profile
                var userDetails = {
                    name: fullname,
                    phone: '',
                    email: $scope.user.EmailAddress,
                    company: "",
                    country: "",
                    zipcode: "",
                    bannerPicture: 'fromObjectStore',
                    id: "admin@duosoftware.com"
                };
                if (!data.Active) {
                    console.log(data);

                    //setting the userdetails
                    var client = $objectstore.getClient("duosoftware.com", "profile", true);
                    client.onError(function (data) {
                        $mdToast.show({
                            position: "bottom right",
                            template: "<md-toast>Successfully created your profile,Please check your Email for verification!</md-toast>"
                        });
                    });
                    client.onComplete(function (data) {
                        $mdToast.show({
                            position: "bottom right",
                            template: "<md-toast>Successfully created your profile,Please check your Email for verification!</md-toast>"
                        });
                    });
                    client.update(userDetails, {
                        KeyProperty: "email"
                    });


                    setTimeout(function () {

                        //location.href = "http://duoworld.duoweb.info/successpage";
                        $scope.showFailure = false;
                        $scope.showRegistration = false;
                        $scope.showSuccess = true;
                    }, delay);
                } else {

                    $mdToast.show({
                        position: "bottom right",
                        template: "<md-toast>There is a problem in registering or you have already been registered!!</md-toast>"
                    });

                    setTimeout(function () {

                        $scope.showFailure = true;
                        $scope.showRegistration = false;
                        $scope.showSuccess = false;

                        //location.href="http://duoworld.duoweb.info/signup/";
                    }, 3000);


                }


            }).error(function (data, status, headers, config) {

                $mdToast.show({
                    position: "bottom right",
                    template: "<md-toast>Please Try again !!</md-toast>"
                });
                setTimeout(function () {
                    //location.href = "http://duoworld.duoweb.info/login.php?r=http://dw.duoweb.info/s.php#/duoworld-framework/dock";
                    $scope.showFailure = true;
                    $scope.showRegistration = false;
                    $scope.showSuccess = false;
                }, 4000)

            });


        } else {
            $scope.user.password = "";
            $scope.user.confirmPassword = "";
            $mdToast.show({
                position: "bottom right",
                template: "<md-toast>Passwords did not match!</md-toast>"
            });
        }

    }

    $scope.directDuoworlLogin = function () {
        location.href =window.location.protocol +"//"+window.location.host + "/login.php";
    }
    $scope.directDuoworlMain = function () {
        location.href = window.location.protocol +"//"+window.location.host;
    }
    $scope.directDuoworlSignUp = function () {
        location.href = window.location.protocol +"//"+window.location.host + "/signup/"
    }
}])
