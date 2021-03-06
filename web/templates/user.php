<script type="text/ng-template" id="groups">
	<div class="btn-group margin-10-2">
		<span class="label label-simple">{{dispgrp}}</span>		
	</div>
</script>

<h3>{{userProfile.name}}</h3>
<div ng-show="userProfile">
<div class="pull-left text-center">
	<img ng-src="{{userProfile.avatar}}" class="profile-avatar">
</div>
<div class="pull-left margin-left-20">
	<table class="profile-table">
		<tbody>		
			<tr>
				<td>Login: </td>
				<td><strong>{{userProfile.user}}</strong></td>
			</tr>
			<tr>
				<td>Status:</td>
				<td>
					<strong>
					<span ng-show="userProfile.status != 1" class="label label-default">Banned</span>
					<span ng-show="userProfile.status == 1" class="label label-success">Enabled</span></strong>
				</td>
			</tr>
			<tr>
				<td>Account:</td>
				<td>
					<strong><span ng-show="userProfile.rights == -1" class="label label-info">Guest</span>
					<span ng-show="userProfile.rights == 0" class="label label-primary">User</span>
					<span ng-show="userProfile.rights == 1" class="label label-warning">Moderator</span>
					<span ng-show="userProfile.rights == 2" class="label label-danger">Super Admin</span></strong>
				</td>
			</tr>
			<tr>
				<td>Group: </td>
				<td>
					<span ng-show="userProfile.grpname.length" ng-repeat="dispgrp in userProfile.grpname" ng-include="'groups'"></span> 					
					<span ng-show="!userProfile.grpname.length" ng-init="dispgrp='None'" ng-include="'groups'"></span> 	
				</td>
			</tr>
			<tr ng-show="false">
				<td>Office: </td>
				<td><strong>{{userProfile.office}}</strong></td>
			</tr>
			<tr>
				<td>Downloads: </td>
				<td><span class="label label-success">{{userProfile.downloads ? userProfile.downloads : 0}}</span></td>
			</tr>
			<tr>
				<td>Notifications: </td>
				<td>{{yesNo(userProfile.notification)}}</td>
			</tr>
			<tr>
				<td>E-Mail: </td>
				<td><a ng-href="mailto:{{userProfile.email}}?Subject=Reply from Assets Library">{{userProfile.email}}</td>
			</tr>
		</tbody>
	</table>
</div>
</div>

<div ng-show="!userProfile"><h3 class="text-center">Profile not found!</h3></div>