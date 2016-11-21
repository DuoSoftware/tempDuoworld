'use strict';
var uri = "http://duoworld.sossgrid.com"
var port ="3048"

// var uri = "http://irangaduosoftwarecom.space.duoworld.com"
// var port ="3000"

function getURI() {
        return uri+":"+port
}

var pageDisplayItem = {};
var pageDisplayGroup = "";

var AuthApp = angular.module('AuthApp', ['ngRoute','ui.bootstrap','uiMicrokernel', 'nvd3ChartDirectives']);

AuthApp.config(function($routeProvider){
		$routeProvider
			.when('/',{
				templateUrl: 'partials/dashboard.html',
				controller: 'dashboardCtrl',
			})
			.when('/config/:servId',{
				templateUrl: 'partials/display.html',
				controller: 'serverCtrl'
			})
			.when('/servers/:servId',{
				templateUrl: 'partials/display.html',
				controller: 'serverCtrl'
			})
	});
