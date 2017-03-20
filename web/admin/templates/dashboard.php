<h1>Dashboard</h1>
<hr>
<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card blue">
		<div class="big-font">{{dashBoardInfo.mdl}}</div>
		<div class="icon"><span class="glyphicon glyphicon glyphicon-lamp" aria-hidden="true"></span></div>
		<div>Models</div>
		<div class="bottom"><a href="#/models/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card green">
		<div class="big-font">{{dashBoardInfo.tex}}</div>
		<div class="icon"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></div>
		<div>Textures</div>
		<div class="bottom"><a href="#/textures/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6 clearfix">
	<div class="dashboard-card yellow">
		<div class="big-font">{{dashBoardInfo.urs}}</div>
		<div class="icon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></div>
		<div>Users</div>
		<div class="bottom" ng-show="auth.rights==2"><a href="#/users/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card red">
		<div class="big-font">{{dashBoardInfo.cmt}}</div>
		<div class="icon"><span class="glyphicon glyphicon-comment" aria-hidden="true"></span></div>
		<div>Comments</div>
		<div class="bottom" ng-show="auth.rights==2"><a href="#/comments/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card color2">
		<div class="big-font">{{dashBoardInfo.space}}</div>
		<div class="icon"><span class="glyphicon glyphicon-hdd" aria-hidden="true"></span></div>
		<div>Free Space</div>
		<div class="bottom" ng-show="auth.rights==2"><a href="">Free: {{dashBoardInfo.free_space}} | Total: {{dashBoardInfo.total_space}}</a></div>
	</div>
</div>
<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card color3">
		<div class="big-font">{{dashBoardInfo.today}}</div>
		<div class="icon"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></div>
		<div>Today Downloaded</div>
		<div class="bottom" ng-show="auth.rights==2"><a href="" ng-click="changeTabRow1(4)">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>
<br style="clear: both">
<div ng-if="auth.rights==2">
	<hr>
	<h3>Model Downloads</h3>
	<ul class="nav nav-pills pull-right" id="tabs">
		<li ng-class="{'active': tabRow1==1}"><a href="" ng-click="changeTabRow1(1)">Month</a></li>
		<li ng-class="{'active': tabRow1==2}"><a href="" ng-click="changeTabRow1(2)">Top</a></li>
		<li ng-class="{'active': tabRow1==3}"><a href="" ng-click="changeTabRow1(3)">By User</a></li>
		<li ng-class="{'active': tabRow1==4}"><a href="" ng-click="changeTabRow1(4)">Log</a></li>
	</ul>
	<br style="clear: both">
	<br style="clear: both">
	<div ng-show="tabRow1==1">
		<canvas id="line" class="chart chart-line" chart-data="dataMonthDownload" chart-labels="labelsMonthDownload" chart-dataset-override="datasetOverride" chart-options="options" chart-colors="colors"> </canvas>
	</div>
	<div ng-show="tabRow1==2">
		<div class="table-responsive">
			<table class="table table-hover">
				<tr class="active">
					<th width="30px">#</th>
					<th></th>
					<th>Item</th>
					<th>Downloads</th>
				</tr>
				<tr ng-repeat="top in dashBoardInfo.graph_top" >
					<td>{{$index + 1}}.</td>
					<td><img ng-src="{{getMainPreview(top.previews, 'small')}}"></td>
					<td><a href="/#/model/{{top.id}}">{{top.name}}</a></td>
					<td>{{top.dwl}}</td>
				</tr>
			</table>
		</div>
	</div>
	<div ng-show="tabRow1==3">
		<canvas id="bar" class="chart-doughnut" chart-data="dataUserDownload" chart-labels="labelsUserDownload" chart-dataset-override="datasetOverride3" chart-options="options3" chart-colors="labelsUserColors"> </canvas>
		<div id="js-legend" class="chart-legend"></div>
	</div>
	<div ng-show="tabRow1==4">
		<div class="table-responsive">
			<table class="table table-hover">
				<tr class="active">
					<th width="30px">#</th>
					<th>Date</th>
					<th>User</th>
					<th>Item</th>
				</tr>
				<tr ng-repeat="log in donwloadLog.log" >
					<td>{{($index + 1) + (donwloadLog.perpage * (currentPage - 1))}}.</td>
					<td>{{tm(log.date)}}</td>
					<td>{{log.user}}</td>
					<td><a href="/#/model/{{log.prodid}}">{{log.prodname}}</a></td>
				</tr>
			</table>
		</div>
		<div class="row text-center">
			<ul uib-pagination total-items="donwloadLog.totalitems" ng-model="currentPage" max-size="5" class="pagination-sm" items-per-page="donwloadLog.perpage" boundary-links="true" force-ellipses="true" ng-click="changePage(currentPage)">
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
