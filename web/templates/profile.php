<script type="text/ng-template" id="groups">
	<div class="btn-group margin-10-2">
		<span class="label label-simple">{{dispgrp}}</span>		
	</div>
</script>

<ul class="nav nav-tabs">
  <li ng-class="{'active': tab=='profile'}"><a href="#/profile/profile">Main</a></li>
  <li ng-class="{'active': tab=='favorites'}"><a href="#/profile/favorites">Favorites</a></li>
</ul><br>
<div ng-show="tab=='profile'">
<div class="pull-left text-center">
	<label>
		<input type="file" nv-file-select="" uploader="uploader1" class="hidden">
		<img ng-src="{{avatar}}" class="profile-avatar pointer"><br><br>
		<button class="btn btn-primary btn-xs" ng-click="clearAvatar()">Clear</button>
	</label>
</div>
<div class="pull-left margin-left-20">
	<table class="profile-table">
		<tbody>
			<tr>
				<td>Name: </td>
				<td><strong>{{profile.name}}</strong></td>
			</tr>
			<tr>
				<td>Login: </td>
				<td><strong>{{profile.user}}</strong></td>
			</tr>
			<tr>
				<td>Account:</td>
				<td>
					<strong><span ng-show="profile.rights == -1" class="label label-info">Guest</span>
					<span ng-show="profile.rights == 0" class="label label-primary">User</span>
					<span ng-show="profile.rights == 1" class="label label-warning">Moderator</span>
					<span ng-show="profile.rights == 2" class="label label-danger">Super Admin</span></strong>
				</td>
			</tr>
			<tr>
				<td>Group: </td>
				<td>
					<span ng-show="profile.grpname.length" ng-repeat="dispgrp in profile.grpname" ng-include="'groups'"></span> 					
					<span ng-show="!profile.grpname.length" ng-init="dispgrp='None'" ng-include="'groups'"></span> 	
				</td>
			</tr>
			<tr ng-show="false">
				<td>Office: </td>
				<td><strong>{{profile.office}}</strong></td>
			</tr>
			<tr>
				<td>Downloads: </td>
				<td><span class="label label-success">{{profile.downloads ? profile.downloads : 0}}</span></td>
			</tr>
			<tr>
				<td>Notifications: </td>
				<td>
				<span class="btn-group btn-group-xs" role="group">
        			<button class="btn btn-default width-40px" ng-class="{'btn-success': profile.notification==1}" type="button" ng-click="profileChangeParam('notification', 1)"> ON </button>
					<button class="btn btn-default width-40px"  ng-class="{'btn-danger': profile.notification!=1}" type="button" ng-click="profileChangeParam('notification', 0)"> OFF </button>
				</span>
				</td>
			</tr>				
		</tbody>
	</table>
</div>
</div>
<div ng-show="tab=='favorites'">
<ul class="nav nav-pills">
  <li ng-class="{'active': favtab==1}"><a ng-click="favtab=1" href="">Models</a></li>
  <li ng-class="{'active': favtab==2}"><a  ng-click="favtab=2" href="">Textures</a></li>
</ul>
<div ng-show="favtab==1">
<hr>
<a href="" class="inline-block" ng-click="favNewCollection(1)"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> New Collection</a> &nbsp;&nbsp;&nbsp;
<a href="" class="inline-block" ng-click="openSharedLink()"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> Open Shared Link</a>
<hr>
<div ng-repeat="fav in favorites" class="col-xxlg-2 col-xlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-6 col-xxs-12 text-center flex">		
		<div class="favorite">
			
				<div class="favorite-images relative" ng-href="" ng-href="#/favorite/{{fav.id}}">
					<a ng-href="#/favorite-collection/{{fav.id}}">
						<span ng-repeat="p in fav.products track by $index" ng-if="$index<9" ng-style="{'background-image': 'url(' + getProdImages(p.previews, 0, true) + ')'}"></span>
						<span ng-repeat="p in (9 - count(fav.products)) | range"></span>
					</a>
					<button type="button" class="btn btn-danger button-fixed button-fav-delete" ng-click="favDeleteCollection(fav.id, fav.name, 1)" uib-tooltip="Delete"> &nbsp;&nbsp;</button>
					<button type="button" class="btn btn-default btn-warning button-fixed button-fav-edit" ng-click="favRenameCollection(fav.id, fav.name, 1)" uib-tooltip="Rename"> &nbsp;&nbsp;</button> 
				</div>				
			
			<div class="favorite-title"><a ng-href="#/favorite-collection/{{fav.id}}">{{shortenName(fav.name, 16)}} ({{count(fav.products)}})</a></div>
		</div>
	
	</div>
</div>

</div>