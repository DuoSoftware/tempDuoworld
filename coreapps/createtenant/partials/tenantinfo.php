<div><md-toolbar style="background:#4CAF50">
    <div class="md-toolbar-tools">
        <h2 class="md-flex">Tenant Informations</h2>
    </div>
</md-toolbar>
<form name="tenantRegForm" ng-submit="formSubmit()" novalidate>
    <md-content layout-padding style="padding: 18px;" layout="column" layout-sm="column">
            <div layout="row" layout-sm="column">
                <div flex layout="column" style="margin-top:3%">
                    <md-input-container style="width:80%">
                        <label>Tenant Name</label>
                        <input type="text" name="txt_tenantname" ng-model="tenant.name" required>
                    </md-input-container>
                    <md-input-container style="width:80%">
                        <label>Company Name</label>
                        <input type="text" name="txt_companyname" ng-model="tenant.company" required="" />
                    </md-input-container>  
                    <div ng-show="tenantRegForm.$submitted || tenantRegForm.txt_companyname.$touched">
                        <div ng-show="tenantRegForm.txt_companyname.$error.required"></div>
                    </div>
                    <label 
                            style="font:normal normal normal 15.3333330154419px/normal Arial;color:#bdbdbd;padding:3px">
                            Tenant Type
                    </label>
                        <md-radio-group layout="row" ng-model="tenant.type" required>
                            <md-radio-button value="dev">Developer</md-radio-button>
                            <md-radio-button value="com">Company</md-radio-button>
                        </md-radio-group>
                </div>
                <div flex>
                    <div id="box" 
                         style="border:#E0E0E0 2px dashed;max-width:400px;max-height:300px;min-height:300px;object-fit: cover;padding:5px" 
                         layout flex layout-align="center center" ng-click="uploadImage()">
                        <p ng-if="prev_img == '' || prev_img == null " style="text-align: center;">
                            <span style="font:normal normal normal 30px/normal Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif;color:#bdbdbd;"> 
                                Click Here to Change<br/> Tenant Image
                            </span>
                        </p>
                        <img src="{{prev_img}}" />
                    </div>
                    <input id="profile-image-upload" file-model="myFile" 
                           onchange="angular.element(this).scope().file_changed(this)" type="file"
                           style="position:absolute;left:-9999px" />
                </div>
            </div>
            <div layout="column">
                <label 
                    style="font:normal normal normal 15.3333330154419px/normal Arial;color:#bdbdbd;padding:3px">
                    Access Level
                </label>
                <md-radio-group layout="row" ng-model="tenant.accessLevel" required style="margin-bottom:10px">
                    <md-radio-button value="public">Public</md-radio-button>
                    <md-radio-button value="private">Private</md-radio-button>
                </md-radio-group>
                <md-input-container style="width:80%">
                    <md-select name="dpd_model" placeholder="Business Model" ng-model="tenant.businessModel" required>
                        <md-option ng-value="model.value" ng-repeat="model in businessModels">{{model.name}}</md-option>
                    </md-select>
                    <div ng-show="tenantRegForm.$submitted || tenantRegForm.dpd_model.$touched">
                        <div ng-show="tenantRegForm.dpd_model.$error.required"></div>
                    </div>
                </md-input-container>
            </div>
    </md-content>
    <div layout="row" layout-align="end end" style="background:#FAFAFA;width:100%;padding:5px 13px">
        <md-button type="submit" style="margin:5px" ng-disabled="tenantRegForm.$invalid">Next</md-button>
    </div> 
</form>
</div>
