AuthApp.controller('dashboardCtrl',['$scope','$http','$routeParams','$interval', function($scope, $http, $routeParams,$interval){
		$scope.pageClass = 'page-AuthConfig';
		$scope.pageName  = 'dashboard';
		$scope.ctr="dashboardCtrl";
		$scope.Sucess =[];
        $scope.Error=[];
        $scope.SucessDataRate=[];
        $scope.ErrorDataRate=[];
		$scope.chart = new CanvasJS.Chart("chartContainer", {
        theme: 'theme1',
        title:{
            text: "Number of calls made to the Server"              
        },
        axisY: {
            title: "Number of Calls",
            labelFontSize: 16,
        },
        axisX: {
        	title:"Time",
            labelFontSize: 16
        },
        data: [{
                type: "line",
                xValueType: "dateTime",
                name: "Sucessful calls",
                dataPoints: $scope.Sucess
            },
            {
                type: "line",
                xValueType: "dateTime",
                name: "Failed calls",
                dataPoints: $scope.Error
            }]
    	});

    	$scope.chart2 = new CanvasJS.Chart("chartTransfer", {
        theme: 'theme1',
        title:{
            text: "Data Transfer Rate"              
        },
        axisY: {
            title: "Data in Bytes",
            labelFontSize: 16,
        },
        axisX: {
        	title:"Time",
            labelFontSize: 16,
        },
        data: [{
                type: "line",
                xValueType: "dateTime",
                name: "Sucessful calls",
                dataPoints: $scope.SucessDataRate
            },
            {
                type: "line",
                xValueType: "dateTime",
                name: "Failed calls",
                dataPoints: $scope.ErrorDataRate
            }]
    	});
    $scope.OldSucessVal=0;
    $scope.OldFailedVal=0;

	$scope.UpdateChart=function(){
		var s =new Date()
    	$http({
				method: 'GET',
				url: getURI()+'/stat/GetStatus/Success',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(data, status, headers, config){
				if($scope.DataSucess!=null){
					$scope.Sucess.push({x:s.getTime(),y:data.NumberOfCalls- $scope.DataSucess.NumberOfCalls})
					$scope.SucessDataRate.push({x:s.getTime(),y:data.TotalSize- $scope.DataSucess.TotalSize})		
				}else{
					$scope.Sucess.push({x:s.getTime(),y:0})
					$scope.SucessDataRate.push({x:s.getTime(),y:0})
				}
				$scope.DataSucess=data	
				$scope.OldSucessVal=data.NumberOfCalls
			}).error(function(data, status, headers, config){
				console.log(data);
			});
		$http({
				method: 'GET',
				url: getURI()+'/stat/GetStatus/Error',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(data, status, headers, config){
				if($scope.DataError!=null){
					$scope.Error.push({x:s.getTime(),y:data.NumberOfCalls- $scope.DataError.NumberOfCalls})
					$scope.ErrorDataRate.push({x:s.getTime(),y:data.TotalSize- $scope.DataError.TotalSize})
				}else{
					$scope.Error.push({x:s.getTime(),y:0}) 
					$scope.ErrorDataRate.push({x:s.getTime(),y:0}) 					
				}
				$scope.DataError=data
				$scope.OldFailedVal=data.NumberOfCalls
			}).error(function(data, status, headers, config){
				console.log(data);
			});
		$scope.chart.render();
		$scope.chart2.render();
	}
    $scope.UpdateChart();
    //$interval($scope.UpdateChart,10000);
    $scope.changeChartType = function(chartType) {
        $scope.chart.options.data[0].type = chartType;
        $scope.chart.render(); 
    }	
}]);
