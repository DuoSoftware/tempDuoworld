<div><md-toolbar style="background:#00BCD4">
    <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="Back" style="5px" ng-click="goBack()">
            <ng-md-icon icon="keyboard_arrow_left" style="line-height:66px;" size="30"></ng-md-icon>
        </md-button>
        <h2 class="md-flex">Tenant Configurations</h2>
    </div>
</md-toolbar>
<md-content ng-controller="tenantConfigCtrl" layout="column" layout-padding style="padding: 18px;">
<!--    company section-->
    <section id="price-plan" style="overflow-x:auto" ng-show="tenant.type  == 'com'">
        <div class="package-container" layout="row" layout-align="center center">
            <div class="package" ng-repeat="plan in companyPricePlans" ng-class="{'active' : $index == selectedIndex}"
                 ng-click="selectPackage($index,plan)">
                <p class="name">{{plan.name}}</p>
                <span class="price">${{plan.price}}</span>
                <ul>
                    <li><ng-md-icon icon="done" style="fill:#33c4b6;margin-right: 3px;"></ng-md-icon> 
                        {{plan.DataDown}} / down </li>
                    <li><ng-md-icon icon="done" style="fill:#33c4b6;margin-right: 3px;"></ng-md-icon> 
                        {{plan.DataUp}} / up </li>
                    <li><ng-md-icon icon="done" style="fill:#33c4b6;margin-right: 3px;"></ng-md-icon>                                                     {{plan.NumberOfUsers}} / users</li>
                </ul>
            </div>
        </div>
    </section>
<!--     end company section-->
    
<!--     developer section-->
    <section id="price-plan" style="overflow-x:auto" ng-show="tenant.type  == 'dev'">
        <div class="package-container" layout="row" layout-align="center center">
            <div class="package" ng-repeat="plan in devPricePlan" ng-class="{'active' : $index == selectedIndex}"
                 ng-click="selectPackage($index,plan)">
                <p class="name">{{plan.name}}</p>
                <span class="price">${{plan.price}}</span>
                <ul>
                    <li><ng-md-icon icon="done" style="fill:#33c4b6;margin-right: 3px;"></ng-md-icon> 
                        {{plan.DataDown}} / down </li>
                    <li><ng-md-icon icon="done" style="fill:#33c4b6;margin-right: 3px;"></ng-md-icon> 
                        {{plan.DataUp}} / up </li>
                    <li><ng-md-icon icon="done" style="fill:#33c4b6;margin-right: 3px;"></ng-md-icon>                                                     {{plan.appCount}} / Apps</li>
                </ul>
            </div>
        </div>
    </section>
<!--     end developer section-->
</md-content>
<div layout="row" layout-align="end end" style="background:#FAFAFA;width:100%;padding:5px 13px">
    <md-button ng-click="formSubmit($event)" style="margin:5px">Creat Tenant</md-button>
</div> 
</div>