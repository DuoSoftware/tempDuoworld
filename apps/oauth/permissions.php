<md-card class="commonContentShell md-whiteframe-12dp" layout="column" layout-align="start center">
	<section id="formContainer" layout="column" layout-align="center center">

			<h1>Request for permission</h1>		
			<text style="color:grey">The '{{appObj.Name}}' Application is requesting the following information.<text>	
			<p>Description : {{appObj.Description}}</p>
			<div class="md-actions" layout="row" layout-align="center space-between" style="padding:20px">
				<img ng-src="{{profilePicUrl}}" err-src="/apps/oauth/APP_SHELL_MY_ACCOUNT.png" style="height: 64px;width: 64px;border-radius: 100%;" />
				<md-icon md-svg-src="/apps/oauth/ic_forward_24px.svg" style="width:50px;" ></md-icon>
				<img ng-src="{{appIconUrl}}" err-src="standard.png" style="height: 64px;width: 64px;border-radius: 100%;" />
			</div>
			<div ng-show="scopeVisible">
				<h2>Scope</h2>
				
				<div ng-show = "dataShow">
					<h3>Data Scope</h3>
					<p ng-repeat="datum in scopeObj.scope.data"> {{datum}}</p>
				</div>

				<div ng-show = "functionShow">
					<h3>Functional Scope</h3>
					<p ng-repeat="datum in scopeObj.scope.functions"> {{datum}}</p>
				</div>
			</div>
			
			<div class="md-actions" layout="row" layout-align="center space-between">
				<md-button class="md-raised md-primary" style="background:#FF5252" type="submit" flex ng-click="reject()"><span class="loginBtnLabel">Reject</span></md-button>
				<md-button class="md-raised md-primary" style="background:#4CAF50" type="submit" flex ng-click="approve()"><span class="loginBtnLabel">Accept</span></md-button>
			</div>
			<!--p>?php echo $authObj->Username; ?</p-->
			
	</section>
</md-card>