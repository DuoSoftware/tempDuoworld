var mainModule = angular.module("loginTransaction", ["ngMaterial"]);

mainModule.controller("mambatiFrameworkLogin-ctrl", function ($scope,$mdDialog,$http) {

	$scope.IsBusy = false;

	$scope.AuthenticateUser = function(event){
		$scope.IsBusy = true;

		$http({
			url: "authenticate.php",
			dataType: "json",
			method: "POST",
			headers: {
				"Access-Control-Allow-Origin": "*",
				"Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
				"Access-Control-Allow-Headers": "Content-Type, X-Requested-With",
				"Content-Type": "text/json"
			}
		}).success(
		function (data, status, headers, config) {
			$scope.IsBusy = false;
			$mdDialog.show(
				$mdDialog.alert()
				.parent(angular.element(document.querySelector('#popupContainer')))
				.clickOutsideToClose(true)
				.title('Login Denied!')
				.textContent(data.Message)
				.ariaLabel('Alert Dialog Demo')
				.ok('Okay')
				.targetEvent(event)
				);
		}).error(function (data, status, headers, config) {
			$scope.IsBusy = false;
			$mdDialog.show(
				$mdDialog.alert()
				.parent(angular.element(document.querySelector('#popupContainer')))
				.clickOutsideToClose(true)
				.title('Login error')
				.textContent('There is some issue with the service. Please try again.')
				.ariaLabel('Alert Dialog Demo')
				.ok('Okay')
				.targetEvent(event)
				);
		});

		/*$http.post(
			"authenticate.php",
			$scope.user, 
			{
				headers: {
					"Access-Control-Allow-Origin": "*",
					"Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
					"Access-Control-Allow-Headers": "Content-Type, X-Requested-With",
					"Content-Type": "application/x-www-form-urlencoded"
				}
			}).
		success(function (data, status, headers, config) {
			$scope.IsBusy = false;
			$mdDialog.show(
				$mdDialog.alert()
				.parent(angular.element(document.querySelector('#popupContainer')))
				.clickOutsideToClose(true)
				.title('Login Denied!')
				.textContent(data.Message)
				.ariaLabel('Alert Dialog Demo')
				.ok('Okay')
				.targetEvent(event)
				);
		}).
		error(function (data, status, headers, config) {
			$scope.IsBusy = false;
			$mdDialog.show(
				$mdDialog.alert()
				.parent(angular.element(document.querySelector('#popupContainer')))
				.clickOutsideToClose(true)
				.title('Login error')
				.textContent('There is some issue with the service. Please try again.')
				.ariaLabel('Alert Dialog Demo')
				.ok('Okay')
				.targetEvent(event)
				);
});*/
}
});
