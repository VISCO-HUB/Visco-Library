<h1>Tags</h1>	
<hr>

<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th>Tag</th>
			<th width="160px">Actions</th>
		</tr>
		<tr ng-repeat="tag in tagsList.tags" >
			<td>{{$index + 1}}.</td>
			<td>{{tag.name}}</td>
			<td><a href="" ng-click="tagChange(tag.name)">Change</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="" ng-click="tagDelete(tag.name)">Delete</a></td>		
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