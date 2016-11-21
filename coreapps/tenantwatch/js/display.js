AuthApp.controller('serverCtrl',function($scope, $agent, $http, $routeParams,$v6urls){
		
		$scope.ctr="ServerconfigCtrl"
		$scope.displayGroupType = pageDisplayGroup;
		
		$scope.displayData = {};
		$scope.logInfo = [];

		$agent.onAgentStatInfo(function(e,statObj){
			var data = statObj.data;

			for (var datum in data){
				if ($scope.displayData[datum]){
					var valueObj = $scope.displayData[datum][0];
					if (valueObj.key){
						if (valueObj.values.length == 10) valueObj.values.splice(0, 1); 
						valueObj.values.push([Date.parse(statObj.data.SystemTime), data[datum]]);
					}
				}
			}
		});

		var countKeys=0;
		$agent.onAgentLogInfo(function (e, data){
			countKeys++;
			try{
				$scope.logInfo.push({key:countKeys, value: data.data.Output})
			}
			catch(e){

			}
		});

	    $agent.onDisplayInfo(function(e,data){
			var tmpData = [];
			var dispData = {};
			var isDispData = false;

			for (var tIndex in data){
				if (data[tIndex].displayType != "output"){
					if (data[tIndex].contents.length > 0)
						tmpData.push(data[tIndex]);
				}
				else tmpData.push(data[tIndex]);

				
				if (data[tIndex].displayType == "info"){
					isDispData = true;
					for (var cIndex in data[tIndex].contents){
						var cObject = data[tIndex].contents[cIndex];
						var objKey = cObject.name;
						dispData[objKey] = [{key : cObject.name, values:[]}];
					}
				}
			}
	      	
	     	$scope.displayInfo = tmpData;
	      	if (isDispData)
	      		$scope.displayData = dispData;
	    });

	    $scope.menuSelected = function(obj){
	      if (obj.displayType && obj.displayId){
	        $agent.on(obj.displayId);
	        $agent.getDisplayInfo(obj.displayType, obj.displayId)
	        $scope.displayEntity = obj.displayId;
	      }
	      else alert ("no information to display");
	    };

	    $scope.getType = function(obj){
	      var outData = typeof obj;
	      return outData;
	    };

	    $scope.getKeys = function(obj){
	      var outData = [];
	      if (!obj) return;
	      for (var k in obj) outData.push(k);
	      return outData;
	    };


		$scope.updateSettings = function(type, fileName, content)  {
			var jsonObj = {};
			for (pIndex in content)
				jsonObj[content[pIndex].key]  =content[pIndex].value;

			if (type =="global")
				$agent.saveGlobalConfig($scope.displayEntity, fileName, jsonObj);      
			else
				$agent.saveAgentConfig($scope.displayEntity, fileName, jsonObj);      

		};

	    $scope.menuSelected(pageDisplayItem);
});