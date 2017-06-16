<script type="text/ng-template" id="sortIcon">
	<span class="glyphicon" ng-class="reverse ? 'glyphicon-triangle-bottom' : 'glyphicon-triangle-top'" aria-hidden="true"></span>
</script>


<script type="text/ng-template" id="groups">
	<div class="btn-group margin-10-2">
		<span class="label label-simple">{{dispgrp}}</span>		
	</div>
</script>

<ul class="nav nav-tabs">
  <li ng-class="{'active': tab=='users'}"><a href="" ng-click="changeTab('users')">Users</a></li>
  <li ng-class="{'active': tab=='groups'}"><a href="" ng-click="changeTab('groups')">Groups</a></li>
</ul>

<div ng-show="tab=='groups'">
<br>
<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th>Group</th>
			<th>Actions</th>
		</tr>
		<tr ng-repeat="group in userFilterList.grp">
			<td>{{$index + 1}}.</td>
			<td>{{group.name}}</td>
			<td><a href="" ng-click="usersRenameGroup(group.id, group.name)">Rename</a> | <a href="" ng-click="usersDelGroup(group.id, group.name)">Delete</a></td>
		</tr>
	</table>	
	<button class="btn btn-primary" ng-click="usersAddGroup()">New Group</button>
</div>		

</div>

<div ng-show="tab=='users'">
<br>
<div class="jumbotron row" style="padding: 10px;">

<h4>Access rights</h4>
<table class="table" style="margin-bottom: 0;">
	<tr>
		<td width="100px"><span class="label label-info">Guest</span></td><td>View</td>
	</tr>
	<tr>
		<td><span class="label label-primary">User</span></td><td>Merge</td>
	</tr>
	<tr>
		<td><span class="label label-warning">Moderator</span></td><td>Limited admin rights</td>
	</tr>
	<tr>
		<td><span class="label label-danger">Super Admin</span></td><td>All permissions</td>
	</tr>
</table>

</div>

<div class="row hidden-xs" ng-show="false">
	<div class="dropdown clr padding-5">By Group:
		<div class="btn-group">
			<button type="button" class="btn btn-default">{{users.filter.grp}}</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
				<li><a href="" ng-click="changeFilter({'grp': 'All'})">All</a></li>
				<li class="divider"></li>
				<li ng-repeat="sub in userFilterList.grp"> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'grp': sub.id})">{{sub.name}}</a> </li>
			</ul>
		</div>
	</div>
	<div class="dropdown clr padding-5">By Status:
		<div class="btn-group">
			<button type="button" class="btn btn-default"> <span ng-show="users.filter.status=='All'">All</span> <span ng-show="users.filter.status!='All'">{{users.filter.status == '1' ? 'Enabled' : 'Disabled'}}</span></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
				<li><a href="" ng-click="changeFilter({'status': 'All'})">All</a></li>
				<li class="divider"></li>
				<li> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'status': '1'})">Enabled</a></li>
				<li> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'status': '0'})">Disabled</a></li>
			</ul>
		</div>
	</div>
	<div class="dropdown clr padding-5">By Rights:
		<div class="btn-group">
			<button type="button" class="btn btn-default"> <span ng-show="users.filter.rights=='All'">All</span> <span ng-show="users.filter.rights=='-1'">Guest</span> <span ng-show="users.filter.rights=='0'">User</span> <span ng-show="users.filter.rights=='1'">Moderator</span> <span ng-show="users.filter.rights=='2'">Super Admin</span> </button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
				<li><a href="" ng-click="changeFilter({'rights': 'All'})">All</a></li>
				<li class="divider"></li>
				<li> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'rights': '-1'})">Guest</a></li>
				<li> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'rights': '0'})">User</a></li>
				<li> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'rights': '1'})">Moderator</a></li>
				<li> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'rights': '2'})">Super Admin</a></li>
			</ul>
		</div>
	</div>
</div>
<br>
<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th ng-click="orderByParam('user')" class="pointer">User
				<span ng-include="'sortIcon'" ng-show="orderUsers=='user'"></span>
			</th>
			<th  ng-click="orderByParam('name')" class="pointer">Name
				<span ng-include="'sortIcon'" ng-show="orderUsers=='name'"></span>
			</th>
			<th  ng-click="orderByParam('grp')" class="pointer">Group
				<span ng-include="'sortIcon'" ng-show="orderUsers=='grp'"></span>
			</th>			
			<th  ng-click="orderByParam('status')" class="pointer">Status
				<span ng-include="'sortIcon'" ng-show="orderUsers=='status'"></span>
			</th>
			<th  ng-click="orderByParam('rights')" class="pointer">Rights
				<span ng-include="'sortIcon'" ng-show="orderUsers=='rights'"></span>
			</th>
		</tr>
		<tr ng-repeat="user in users.users | orderBy:orderUsers:reverse" >
			<td>{{$index + 1}}.</td>
			<td>{{user.user}}</td>
			<td>{{user.name}}</td>
			<td>
			    <span uib-dropdown dropdown-append-to-body="true">
				  <a href id="link-dropdown" uib-dropdown-toggle class="href-clear">
					<span ng-show="user.grpname.length" ng-repeat="dispgrp in user.grpname" ng-include="'groups'"></span> 										
					<span ng-show="!user.grpname.length" ng-init="dispgrp='None'" ng-include="'groups'"></span> 					
				  </a>
				  <ul class="dropdown-menu"  uib-dropdown-menu aria-labelledby="link-dropdown">
						<li ng-repeat="g in userFilterList.grp">							
							<a href ng-click="usersToggleGroup(user.id, g.id)">
								<span ng-init="c = user.grp && user.grp.indexOf(g.id) != -1 " class="glyphicon" ng-class="{'glyphicon glyphicon-check': c,  'glyphicon-unchecked': !c}" aria-hidden="true"></span>&nbsp;&nbsp;
								{{g.name}}							
							</a>
						</li>
				  </ul>
				</span>						
			</td>
			<td>
			<span ng-show="user.status==0" class="label label-default pointer" ng-click="usersSetParam('status', '1', user.id)">Disabled</span> 
			<span ng-show="user.status==1" class="label label-success pointer" ng-click="usersSetParam('status', '0', user.id)">Enabled</span></td>
			<td>
			<span ng-show="user.rights==-1" class="label label-info pointer" ng-click="usersSetParam('rights', '0', user.id)">Guest</span> 
			<span ng-show="user.rights==0" class="label label-primary pointer" ng-click="usersSetParam('rights', '1', user.id)">User</span> 
			<span ng-show="user.rights==1" class="label label-warning pointer" ng-click="usersSetParam('rights', '2', user.id)">Moderator</span> 
			<span ng-show="user.rights==2" class="label label-danger pointer" ng-click="usersSetParam('rights', '-1', user.id)">Super Admin</span></td>
		</tr>
	</table>
</div>
<div ng-show="false">
<div class="row text-center">
	<ul uib-pagination total-items="users.totalitems" ng-model="currentPage" max-size="5" class="pagination-sm" items-per-page="users.perpage" boundary-links="true" force-ellipses="true" ng-click="changePage()">
	</ul>
</div>
<hr>
<div class="dropdown dropup clr pull-right"> Show:
	<div class="btn-group">
		<button type="button" class="btn btn-default">{{perpage}}</button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
		<ul class="dropdown-menu" style="min-width: 100%;" role="menu" aria-labelledby="dropdownMenu">
			<li ng-repeat="i in [50, 100, 150, 200, 250]"><a href="" ng-click="changePerPage(i)">{{i}}</a></li>
		</ul>
	</div>
</div>
</div>
</div>
