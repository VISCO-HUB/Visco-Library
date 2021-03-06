<script type="text/ng-template" id="tags">
		
		<div class="dropdown dropdown-inline">
		  <a href="" class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
			{{tag}}
		  </a>
		  <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu1">
			<li role="presentation"><a role="menuitem" tabindex="-1" href="" ng-click="copyTag(tag)">Copy</a></li>
			<li role="presentation" class="divider"></li>
			<li role="presentation"><a role="menuitem" tabindex="-1" href="" ng-click="removeTag(prod.id, tag)">Remove</a></li>
		  </ul>
		</div>,
</script>

<h1>Models</h1>
<hr>
<div class="dropdown clr">
	<div class="btn-group">
		<button type="button" class="btn btn-default" data-toggle="dropdown">{{products.filter.cat.name}}</button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
			<li><a href="" ng-click="changeFilter({'catid': null, 'pending': null})">All</a></li>
			<li><a href="" ng-click="changeFilter({'catid': null, 'pending': 1})">Pending</a></li>
			<li class="divider"></li>
			<li class="dropdown-submenu" ng-repeat="cat in categories" ng-if="cat.type == 1 && (auth.rights==2 || cat.editors.split(';').indexOf(auth.user))!=-1"> 
				<a tabindex="-1" data-toggle="dropdown" href="" ng-click="changeFilter({'catid': cat.id, 'pending': null})">{{cat.name}}</a>			
				<ul class="dropdown-menu">
					<li class="dropdown-submenu" ng-repeat="sub1 in cat.child"> <a href="" ng-click="changeFilter({'catid': sub1.id, 'pending': null})">{{sub1.name}}</a>
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
			<th width="220px">Actions</th>
			<th>Tags</th>
		</tr>
		<tr ng-repeat="prod in products.products" >
			<td>{{$index + 1}}.</td>
			<td>
				<div class="is3d-small" ng-show="prod.webgl"></div>
				<a href="#/models-edit/{{prod.id}}/{{currentPage}}"><img ng-src="{{getMainPreview(prod.previews, 'small')}}"></a>
			</td>
			<td nowrap><a href="#/models-edit/{{prod.id}}/{{currentPage}}">{{prod.name}}</a></td>
			<td>
				<span ng-show="prod.status==0 && prod.pending!=1" class="label label-default pointer" ng-click="prodSetParam('status', '1', prod.id)">Disabled</span> 
				<span ng-show="prod.status==1 && prod.pending!=1" class="label label-success pointer" ng-click="prodSetParam('status', '0', prod.id)">Enabled</span>
				<span ng-show="prod.pending==1" class="label label-warning pointer" ng-click="prodSetParam('pending', '0', prod.id)">Pending</span>
			</td>
			<td> {{tm(prod.date)}} </td>
			<td><a href="#/models-edit/{{prod.id}}/{{currentPage}}">Edit</a> 
				|
				<span ng-show="auth.rights >= 1"><a href="" ng-click="prodDelete(prod.id, prod.name)" >Delete</a>
				|</span>
				<a href="/#/model/{{prod.id}}">View</a>
				<span ng-show="auth.browser=='MXS'">| <a href="" ng-click="openModel(prod.id)">Open</a></span>
				<span ng-show="auth.browser=='MXS'">| <a href="" ng-click="mergeModel(prod.id)">Merge</a></span>
				| <a href="" ng-click="downloadUrl(prod.id, type)">Download</a>
			</td>
			<td>
				<span ng-repeat="tag in prod.tags.split(',') track by $index" ng-include="'tags'">{{tag}}</span>
								
				<button class="btn btn-default btn-xs btn-tag" ng-click="addTag(prod.id)"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add</button>
				
				<button class="btn btn-default btn-xs btn-tag" ng-click="pasteTag(prod.id)"><span class="glyphicon glyphicon-paste" aria-hidden="true"></span> Paste</button>
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
