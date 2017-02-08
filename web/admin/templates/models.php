<h1>Models</h1>
<hr>
<div class="dropdown clr">
	<div class="btn-group">
		<button type="button" class="btn btn-default">{{products.filter.cat.name}}</button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
			<li><a href="" ng-click="changeFilter({'catid': null, 'pending': null})">All</a></li>
			<li><a href="" ng-click="changeFilter({'catid': null, 'pending': 1})">Pending</a></li>
			<li class="divider"></li>
			<li class="dropdown-submenu" ng-repeat="cat in categories" ng-if="cat.type == 1 && (auth.rights==2 || cat.editors.split(';').indexOf(auth.user))!=-1"> <a tabindex="-1" data-toggle="dropdown" href="" no-click>{{cat.name}}</a>			
				<ul class="dropdown-menu">
					<li class="dropdown-submenu" ng-repeat="sub1 in cat.child"> <a href="" no-click>{{sub1.name}}</a>
						<ul class="dropdown-menu">
							<li ng-repeat="sub2 in sub1.child"><a href="" ng-click="changeFilter({'catid': sub2.id, 'pending': null})">{{sub2.name}}</a></li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>
<br>
<br>
<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th></th>
			<th>Name</th>
			<th>Status</th>
			<th>Date</th>
			<th width="150px">Actions</th>
		</tr>
		<tr ng-repeat="prod in products.products" >
			<td>{{$index + 1}}.</td>
			<td><img ng-src="{{getMainPreview(prod.previews, 'small')}}"></td>
			<td><a href="#/models-edit/{{prod.id}}/{{currentPage}}">{{prod.name}}</a></td>
			<td>
			<span ng-show="prod.status==0 && prod.pending!=1" class="label label-default pointer" ng-click="prodSetParam('status', '1', prod.id)">Disabled</span> 
			<span ng-show="prod.status==1 && prod.pending!=1" class="label label-success pointer" ng-click="prodSetParam('status', '0', prod.id)">Enabled</span>
			<span ng-show="prod.pending==1" class="label label-warning pointer" ng-click="prodSetParam('pending', '0', prod.id)">Pending</span></td>
			<td> {{tm(prod.date)}} </td>
			<td><a href="#/models-edit/{{prod.id}}/{{currentPage}}">Edit</a> 
				|
				<a href="" ng-click="prodDelete(prod.id, prod.name)">Delete</a>
				|
				<a href="/#/model/{{prod.id}}}" target="_blank">View</a></td>
			</td>
		</tr>
	</table>
</div>
<div class="row text-center">
	<ul uib-pagination total-items="products.totalitems" ng-model="currentPage" max-size="5" class="pagination-sm" items-per-page="products.perpage" boundary-links="true" force-ellipses="true" ng-click="changePage()">
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
