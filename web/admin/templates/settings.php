<h1>Settings</h1>	
<hr>

<ul class="nav nav-tabs">
  <li ng-class="{active:show == 'tabGlobal'}" ng-click="show='tabGlobal'"><a href="">Global</a></li>
  <li ng-class="{active:show == 'tabTags'}"><a href="" ng-click="show='tabTags'">Tags</a></li>
  <li ng-class="{active:show == 'tabBackups'}"><a href="" ng-click="show='tabBackups'">Backups</a></li>
  <!--<li ng-class="{active:show == 'tab2'}"><a href="" ng-click="show='tab2'">Mail</a></li>
  <li ng-class="{active:show == 'tab3'}"><a href="" ng-click="show='tab3'">System</a></li>
  <li ng-class="{active:show == 'tab4'}"><a href="" ng-click="show='tab4'">View</a></li>-->

</ul>

<div ng-show="show == 'tabGlobal'">
<h2><small>Global Path:</small></h2>
<div class="form-group">
	<input type="text" class="form-control" disabled placeholder="{{globals.path}}">				
</div>
<mark class="text-muted">Example: \\visco.local\data\Library\</mark><br><br>
<button type="submit" class="btn btn-primary" ng-click="globalsChange()">Change</button>
</div>

<div ng-show="show == 'tabTags'">
<h2><small>Refresh Model Tags:</small></h2>
<mark class="text-muted">Warning! This operation can be carried out within 20 minutes!<br>Do not close the page!</mark><br><br>
<button type="submit" class="btn btn-primary" ng-click="tagsRefresh('models')">Refresh</button>
</div>

<div ng-show="show == 'tabBackups'">

<h2><small>Backup Database:</small></h2>
<mark class="text-muted">Warning! Do not leave backup files for a long time is not safe!</mark><br><br>
<button type="submit" class="btn btn-primary clr" ng-click="backupDatabase()">Create Backup</button>
<br><br>
<div class="table-responsive">
	<table class="table table-hover">
		<tr>
			<th>File</th><th>Date</th><th>Size</th><th>Actions</th>
		</tr>
		<tr ng-repeat="file in backupList">
			<td><a ng-href="{{file.path}}">{{file.name}}</td>
			<td>{{file.date}}</td>
			<td>{{file.size}}</td>
			<td><a href="" ng-click="delBackup(file.name)">Delete</a></td>
		</tr>
	</table>
<div>
</div>
	
	