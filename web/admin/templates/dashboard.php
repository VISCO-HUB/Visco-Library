<h1>Dashboard</h1>
<hr>
<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card blue">
		<div class="big-font">150</div>
		<div class="icon"><span class="glyphicon glyphicon glyphicon-lamp" aria-hidden="true"></span></div>
		<div>Models</div>
		<div class="bottom"><a href="#/models/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>

<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card green">
		<div class="big-font">300</div>
		<div class="icon"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></div>
		<div>Textures</div>
		<div class="bottom"><a href="#/textures/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>

<div class="col-md-6 col-sm-6 col-lg-6 clearfix">
	<div class="dashboard-card yellow">
		<div class="big-font">45</div>
		<div class="icon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></div>
		<div>Users</div>
		<div class="bottom" ng-show="auth.rights==2"><a href="#/users/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>

<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card red">
		<div class="big-font">144</div>
		<div class="icon"><span class="glyphicon glyphicon-comment" aria-hidden="true"></span></div>
		<div>Comments</div>
		<div class="bottom" ng-show="auth.rights==2"><a href="#/comments/1">More Info <span class="glyphicon glyphicon-circle-arrow-right"></span></a></div>
	</div>
</div>
<br style="clear: both"><br>
<h1>Models Downloads</h1>
<div id="chartContainer" style="height: 300px; width: 100%;">
<hr>