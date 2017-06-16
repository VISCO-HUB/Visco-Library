<h1>Messages</h1>
<hr>
<div class="message" id="message">
	<div class="text-center" ng-show="!currentMessage.length">Select message from list to view content.</div>
	<img ng-src="{{currentImg}}" ng-show="currentImg" class="pull-left">
	<h3 ng-show="currentSubject.length">{{currentSubject}}</h3>
	<div ng-bind-html="currentMessage"></div>	
</div>

<div class="table-responsive" style="clear: both">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>
			<th>Subject</th>
			<th>Status</th>
			<th></th>
			<th>Date</th>
			<th>By User</th>
			<th>Actions</th>
		</tr>
		<tr ng-repeat="msg in messages.messages" >
			<td>{{$index + 1}}.</td>
			<td>
				<img src="img/bug.svg" class="icon-bug" ng-show="msg.bug==1"> <a href="" ng-click="setCurrentMessage(msg)" ng-class="{'text-bug': msg.bug==1}"><span ng-style="{'color':  msg.fixedbug==1 ? '#34ca34' : ''}">{{msg.subject}}</span></a>								
			</td>
			<td>
				<span ng-show="msg.viewed!=1" class="label label-success pointer text-blink">Unread</span> 			
				<span ng-show="msg.viewed==1" class="label label-default">Read</span> 			
			</td>
			<td>
				<div >
					<span ng-show="msg.fixedbug==1" class="label label-success pointer"  ng-click="msgSetParam('fixedbug', 0, msg.id)">{{msg.bug==1 ? 'Fixed' : 'Done'}}</span> 
					<span ng-show="msg.fixedbug!=1" class="label label-danger pointer text-blink" ng-click="msgSetParam('fixedbug', 1, msg.id)">{{msg.bug==1 ? 'Not Fixed' : 'Queue'}}</span> 
				</div>
		
			</td>
			<td>{{tm(msg.date)}} </td>
			<td>{{msg.user}}</td>
			<td><a href="" ng-click="msgDelete(msg.id, msg.subject)">Delete</a></td>			
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
