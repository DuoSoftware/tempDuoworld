AuthApp.controller('mainController',function($scope, $http, $routeParams, $auth, $agent, $presence, $location){

    var isTestingMode = false;

    if (isTestingMode){
        $auth.onLoginResult(function(e,data){
          $scope.currentUser = $auth.getUserName();
          $presence.setOnline();
        });
        $auth.forceLogin("divani2","asdsdsdad","");
    } else{
        if ($auth.checkSession()){
          $scope.currentUser = $auth.getUserName();
          $presence.setOnline();
        }
    }

    $scope.ClusterInfo = [];
    $scope.displayInfo = [];
    $scope.contents = [];

    $scope.displayEntity = null;

    $agent.onClusterInfo(function(e,data){
    	console.log(data);
      $scope.ClusterInfo = data;

    });

    $agent.onDisplayInfo(function(e,data){
      $scope.displayInfo = data;
    });

    $presence.onStateChanged(function(e,data){
      $agent.getClusterInfo();
    });
    
    $scope.select = function (group, item){
    	if (pageDisplayItem && pageDisplayGroup === "servers")
    		$agent.off(pageDisplayItem.displayId);

    	pageDisplayGroup = group;
    	pageDisplayItem= item;

    	$location.url(group + "/" + item.displayId);
    };

   
    

});

