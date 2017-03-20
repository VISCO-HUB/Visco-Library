<h1>Comments</h1>	
<hr>
<div ng-show="commentsList.comments.length">	
<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th>User</th>
			<th>Date</th>
			<th>Comment</th>
			<th width="70px">Actions</th>
		</tr>
		<tr ng-repeat="cmt in commentsList.comments" >
			<td>{{($index + 1) + (commentsList.perpage * (currentPage - 1))}}.</td>
			<td>{{cmt.user}}</td>
			<td>{{tm(cmt.date)}}</td>
			<td>{{cmt.comment}}</td>
			<td><a href="" ng-click="commentDelete(cmt.id)">Delete</a></td>		
		</tr>
	</table>
</div>	
<div class="row text-center">
	<ul uib-pagination total-items="commentsList.totalitems" ng-model="currentPage" max-size="5" class="pagination-sm" items-per-page="commentsList.perpage" boundary-links="true" force-ellipses="true" ng-click="changePage()">
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
<div ng-show="!commentsList.comments.length"><h3 class="text-center">No comments yet.</h3></div>