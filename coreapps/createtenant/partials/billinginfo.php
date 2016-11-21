<div><md-toolbar style="background:#FFCA28">
    <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="Back" style="5px" ng-click="goBack()">
            <ng-md-icon icon="keyboard_arrow_left" style="line-height:66px;" size="30"></ng-md-icon>
        </md-button>
        <h2 class="md-flex">Billing Informations</h2>
    </div>
</md-toolbar>
<form name="tenantBillingForm" ng-submit="formSubmit()" novalidate>
    <md-content layout-padding style="padding: 18px;">
        <div layout="row">
            <md-input-container flex=55>
                <label>Account Number</label>
                <input type="text" name="accountNum" ng-model="tenant.accNum" required>
            </md-input-container>
        </div>
        <div layout="row">
            <md-input-container flex=50>
                <label>Phone Number</label>
                <input type="text" name="mobileNum" ng-model="tenant.mobile" required>
            </md-input-container>
        </div>
        <md-input-container>
            <label>Billing Address</label>
            <textarea name="billAddr" ng-model="tenant.billAddr" required></textarea>
        </md-input-container>        
        <md-input-container>
            <label>Delivery Address</label>
            <textarea name="dlvrAddr" ng-model="tenant.deliveryAddr" required></textarea>
        </md-input-container>
    </md-content>
    <div layout="row" layout-align="end end" style="background:#FAFAFA;width:100%;padding:5px 13px">
        <md-button type="submit" style="margin:5px" ng-disabled="tenantBillingForm.$invalid">Register</md-button>
    </div> 
</form>
</div>
