<?php
require_once(ROOT_PATH. "/payapi/duoapi/ratingengine/ratingengine.php");

class RatingService {
    private $rateEngine;

    private function test(){
        echo "Rating Engine Service is Working!!!";
    }

    private function Process($tenantid, $route, $size, $criteria){
        $headers=null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $headers = json_decode(Flight::request()->getBody());
            if (!is_array($headers)) $headers = null;
        }

        if ($this->rateEngine->Validate($tenantid, $route, $size,$criteria))
            $this->rateEngine->Rate($tenantid, $route, $size, $criteria, $headers);

        $this->showSuccess("Rating successfully completed!!!");
    }

    private function CreateForUser($namespace, $userid){
        $obj = json_decode(Flight::request()->getBody());
        $this->rateEngine->CreateRulesForUser($namespace, $userid, $obj);
        $this->showSuccess("Successfully added rules for user $userid in tenant $namespace");
    }

    private function CreateForTenant($namespace){
        $obj = json_decode(Flight::request()->getBody());
        $this->rateEngine->CreateRulesForTenant($namespace, $obj);
        $this->showSuccess("Successfully added rules for tenant $namespace");
    }

    private function showSuccess($msg){
        header("Content-Type: application/json");
        echo "{\"success\": true, \"message\":\"$msg\"}";
    }

    function __construct(){       
        $this->rateEngine = new RatingEngine();
        Flight::route("GET /test",function(){$this->test();});

        Flight::route("POST /process/@tenantid/@route/@size/@criteria",function($tenantid,$route,$size, $criteria){$this->Process($tenantid, $route, $size, $criteria);});
        Flight::route("GET /process/@tenantid/@route/@size/@criteria",function($tenantid,$route,$size, $criteria){$this->Process($tenantid, $route, $size, $criteria);});

        Flight::route("POST /createforuser/@namespace/@userid",function($namespace, $userid){$this->CreateForUser($namespace, $userid);});
        Flight::route("POST /createfortenant/@namespace",function($namespace){$this->CreateForTenant($namespace);});

    }
}

?>
