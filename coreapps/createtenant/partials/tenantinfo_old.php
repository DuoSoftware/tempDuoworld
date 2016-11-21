<!--
<div>
    <md-toolbar style="background:#4CAF50">
        <div class="md-toolbar-tools">
            <h2 class="md-flex">Tenant Informations</h2>
        </div>
    </md-toolbar>
<form name="tenantRegForm" ng-submit="formSubmit()" novalidate>
    <md-content layout-padding style="padding: 18px;" layout="column" layout-sm="column">
            <div layout="row" layout-sm="column">
                <div flex layout="column">
                    <md-input-container>
                        <label>Tenant Name</label>
                        <input type="text" name="txt_tenantname" ng-model="t.name" required>
                    </md-input-container> 
                    <label 
                           style="font:normal normal normal 15.3333330154419px/normal Arial;color:#bdbdbd;padding:3px">
                        Access Level
                    </label>
                    <md-radio-group layout="row" ng-model="t.accessLevel" required>
                        <md-radio-button ng-value="false">Public</md-radio-button>
                        <md-radio-button ng-value="true">Private</md-radio-button>
                    </md-radio-group>
                    <label 
                           style="font:normal normal normal 15.3333330154419px/normal Arial;color:#bdbdbd;padding:3px">
                        Tenant Type
                    </label>
                    <md-radio-group layout="row" ng-model="t.tType" required>
                        <md-radio-button value="dev.duoworld.com">Developer</md-radio-button>
                        <md-radio-button value="duoworld.com">Company</md-radio-button>
                    </md-radio-group>
    </md-content>
    <div layout="row" layout-align="end end" style="background:#FAFAFA;width:100%;padding:5px 13px">
        <md-button type="submit" style="margin:5px" ng-disabled="tenantRegForm.$invalid">Next</md-button>
    </div> 
</form>
</div>
-->

<div><md-toolbar style="background:#4CAF50">
    <div class="md-toolbar-tools">
        <h2 class="md-flex">Tenant Informations</h2>
    </div>
</md-toolbar>
<form name="tenantRegForm" ng-submit="formSubmit()" novalidate>
    <md-content layout-padding style="padding: 18px;">
        
        <md-input-container>
            <label>Tenant Name</label>
            <input type="text" name="txt_tenantname" ng-model="tenant.name" required>
        </md-input-container> 
        <label 
               style="font:normal normal normal 15.3333330154419px/normal Arial;color:#bdbdbd;padding:3px">
            Tenant Type
        </label>
        <md-radio-group layout="row" ng-model="tenant.type" required>
            <md-radio-button value="dev.duoweb.info">Developer</md-radio-button>
            <md-radio-button value="duoweb.info">Company</md-radio-button>
        </md-radio-group>
        <div layout="row" layout="column">
            <md-input-container flex=60>
                <label>Company Name</label>
                <input type="text" name="txt_tenantname" ng-model="tenant.company" required>
            </md-input-container>
            <div flex=5></div>
            <md-input-container flex=35>
              <label>Business Model</label>
              <md-select name="myModel" ng-model="tenant.businessModel" required>
                <md-option value="{{model.value}}" ng-repeat="model in businessModels">{{model.name}}</md-option>
              </md-select>
            </md-input-container>
        </div>
        <label 
               style="font:normal normal normal 15.3333330154419px/normal Arial;color:#bdbdbd;padding:3px">
            Access Level
        </label>
        <md-radio-group layout="row" ng-model="tenant.accessLevel" required>
            <md-radio-button ng-value="false">Public</md-radio-button>
            <md-radio-button ng-value="true">Private</md-radio-button>
        </md-radio-group>
    </md-content>
    <div layout="row" layout-align="end end" style="background:#FAFAFA;width:100%;padding:5px 13px">
        <md-button type="submit" style="margin:5px" ng-disabled="tenantRegForm.$invalid">Next</md-button>
    </div> 
</form>
</div>
