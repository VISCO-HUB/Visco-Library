<h1>Tags</h1>	
<hr>
<div class="row">
<form ng-submit="findTag(tagsList.filter.search)">
	<div class="col-sm-6 col-sm-offset-3">
		<div class="input-group">
		  <input type="text" class="form-control" placeholder="Search for..." ng-model="tagsList.filter.search">
		  <span class="input-group-btn">
			<button type="submit" class="btn btn-primary" type="button">Go!</button>
		  </span>
		</div>
	</div>
</form>
</div>
<br>
<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th>Tag</th>
			<th width="160px">Actions</th>
		</tr>
		<tr ng-repeat="tag in tagsList.tags" >
			<td>{{($index + 1) + (tagsList.perpage * (currentPage - 1))}}.</td>
			<td>{{tag.name}}</td>
			<td><a href="" ng-click="tagChange(tag.name)">Change</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="" ng-click="tagDelete(tag.name)">Delete</a></td>		
		</tr>
	</table>
</div>	
<div class="row text-center">
	<ul uib-pagination total-items="tagsList.totalitems" ng-model="currentPage" max-size="5" class="pagination-sm" items-per-page="tagsList.perpage" boundary-links="true" force-ellipses="true" ng-click="changePage()">
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