<h1>Users</h1>
<hr>
<div class="row hidden-xs">
	<div class="dropdown clr padding-5">By Group:
		<div class="btn-group">
			<button type="button" class="btn btn-default">{{users.filter.grp}}</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
				<li><a href="" ng-click="changeFilter({'grp': 'All'})">All</a></li>
				<li class="divider"></li>
				<li ng-repeat="sub in userFilterList.grp"> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'grp': sub.grp})">{{sub.grp}}</a> </li>
			</ul>
		</div>
	</div>
	<div class="dropdown clr padding-5">By Office:
		<div class="btn-group">
			<button type="button" class="btn btn-default">{{users.filter.office}}</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
				<li><a href="" ng-click="changeFilter({'office': 'All'})">All</a></li>
				<li class="divider"></li>
				<li ng-repeat="sub in userFilterList.office"> <a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'office': sub.office})">{{sub.office}}</a> </li>
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
<br>
<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th>User</th>
			<th>Name</th>
			<th>Group</th>
			<th>Office</th>
			<th>Status</th>
			<th>Rights</th>
		</tr>
		<tr ng-repeat="user in users.users" >
			<td>{{$index + 1}}.</td>
			<td>{{user.user}}</td>
			<td>{{user.name}}</td>
			<td>{{user.grp}}</td>
			<td>{{user.office}}</td>
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
<div class="row text-center">
	<ul uib-pagination total-items="products.totalitems" ng-model="currentPage" max-size="5" class="pagination-sm" items-per-page="users.perpage" boundary-links="true" force-ellipses="true" ng-click="changePage()">
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
