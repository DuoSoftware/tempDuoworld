<?php

require_once($_SERVER["DOCUMENT_ROOT"]. "/payapi/duoapi/objectstoreproxy.php");
require_once($_SERVER["DOCUMENT_ROOT"]. "/include/duoapi/cloudcharge.php");
 
class AppPaymentManager {

    private $cloudCharge;

    public function AllowInstall($tenant, $appKey, $price, $stripeToken){
        if (!defined(PAYMENT_KEY))
            return true;

        $result;
        if ($this->isSubscriptionApp($appKey)){
            if ($this->isAlreadyInstalled($appKey))
                $result = $this->cloudCharge->app()->reinstall($appKey);
            else
                $result = $this->cloudCharge->app()->subscribe($appKey, $price, $stripeToken);
        } else 
            $result = $this->cloudCharge->app()->purchase($appKey, $price, $stripeToken);
        
        $canInstall = false;
        if (isset($result))
        if (isset($result->status)){
            $canInstall= $result->status;
            if (is_string($canInstall)){
                $canInstall = trim(strtolower($canInstall));
                $canInstall = ($canInstall === "true") ? true: false;
            } if (is_int($canInstall)){
                $canInstall = ($canInstall === 0) ? false: true;
            }
                
        }
        
        if ($canInstall)
            $this->setRecord($tenant, $appKey, "appinstallation");

        return $canInstall;
    }

    private function isAlreadyInstalled($appKey){
        $client = ObjectStoreClient::WithNamespace(DuoWorldCommon::GetHost(),"application","123");
		$appObj =  $client->get()->byKey($appKey);

        if (isset($appObj)){
            if (isset($appObj->ApplicationID))
                return true;
        }

        return false;
    }

    public function AllowUninstall($tenant, $appKey){ //call this method inside the app marketplace service, and the shell
        if (!defined(PAYMENT_KEY))
            return;

        $result;
        if ($this->isSubscriptionApp($appKey))
            $result = $this->cloudCharge->app()->uninstall($appkey);
        else {
            $result = new stdClass();
            $result->status = true;
        }

        $canUninstall = false;
        if (isset($result))
            if (isset($result->status))
                $canUninstall= $result->status;
        
        if ($canUninstall)
            $this->setRecord($tenant, $appKey, "appuninstallation");
    }

    public function VerifyInstall($tenant, $appKey){
        if (!defined(PAYMENT_KEY))
            return true;

        if ($this->IsFreeApp($appKey)) return true;
        else return $this->verifyRecord($tenant, $appKey,"appinstallation");
    }

    public function IsFreeApp($appKey){
        $client = ObjectStoreClient::WithNamespace(MAIN_DOMAIN,"appstoreapps","123");
        $appObj =  $client->get()->byKey($appKey);

        if (!isset($appObj->price)) return true;
        else {
            if ($appObj->price === 0) return true;
            else if (is_string($appObj->price)){
                if (trim($appObj->price) === "" || trim($appObj->price) === "0") return true;
            }
        }

        return false;
    }

    public function FilterPaidApps($appKeys){
        $appList = array();

        $filter = "SELECT * FROM appstoreapps where appKey in (";
        $isFirst = true;
        foreach ($appKeys as $oneAppKey) {
            if (!$isFirst) $filter .= ",";
            else $isFirst = false;
            $filter .= "'$oneAppKey'";
        }
        $filter .= ")";
        
        $client = ObjectStoreClient::WithNamespace(MAIN_DOMAIN,"appstoreapps","123");
        $allApps =  $client->get()->byFiltering($filter);

        foreach ($allApps as $appObj){
            $isfree = false;
            if (!isset($appObj["price"])) $isfree = true;
            else {
                if ($appObj["price"] === 0) $isfree = true;
                else if (is_string($appObj["price"])){
                    if (trim($appObj["price"]) === "" || trim($appObj["price"]) === "0") $isfree = true;
                }
            }

            if (!$isfree)
                array_push($appList, $appObj["appKey"]);
        }

        return $appList;
    }

    public function VerifyUninstall($tenant, $appKey){
        if (!defined(PAYMENT_KEY))
            return true;
        
        if ($this->IsFreeApp($appKey)) return true;
        return $this->verifyRecord($tenant, $appKey,"appuninstallation");
    }

    public function GetSubscribedApps(){
        $outData = new stdClass();
        $allApps = $this->cloudCharge->app()->withinSubscriptionPeriod();

        if (isset($allApps))
        if (isset($allApps->data))
        foreach ($allApps->data as $plan){
            $appKey = $plan->plan;
            $outData->$appKey = true;
        }
        
        return $outData;
    }

    public function VerifySubscription($tenant, $appKey){
        if (!defined(PAYMENT_KEY))
            return true;
            
            if ($this->isSubscriptionApp($appKey)){
                $allApps = $this->cloudCharge->app()->withinSubscriptionPeriod();
                
                if (isset($allApps))
                if (isset($allApps->data))
                foreach ($allApps->data as $plan)
                if (strcmp($plan->plan, $appKey) == 0)
                    return true;
                
                return false;
            } else return true;
            
    }

    private function isSubscriptionApp($appKey){
        $client = ObjectStoreClient::WithNamespace($GLOBALS['mainDomain'],"appsubscriptions","123");
		$subObj =  $client->get()->byKey($appKey);

        if (isset($subObj)){
            if (isset($subObj->paymentMethod))
                if (strcmp(strtolower($subObj->paymentMethod),"subscription") == 0)
                    return true;
        }

        return false;
    }

    private function setRecord($tenant, $appKey, $class){
        $appObj = new stdClass();
        $appObj->id = "$tenant.$appKey";
        $appObj->tenantId = $tenant;
        $appObj->appKey = $appKey;
        $appObj->allow = true;

        $client = ObjectStoreClient::WithNamespace($GLOBALS['mainDomain'],$class,"123");
		$client->store()->byKeyField("id")->andStore($appObj);
    }

    private function verifyRecord($tenant, $appKey, $class){
        $client = ObjectStoreClient::WithNamespace($GLOBALS['mainDomain'],$class,"123");
		$res = $client->get()->byKey("$tenant.$appKey");

        if (isset($res->id))
            return true;
        
        return false;
    }

    function __construct() {
        $this->cloudCharge = new CloudCharge();
    }


}

?>