<h1>Tools</h1>	
<hr>

<ul class="nav nav-tabs">
  <li ng-class="{active:show == 'tabBackups'}"><a href="" ng-click="show='tabBackups'">Backups</a></li>
  <li ng-class="{active:show == 'tabTags'}"><a href="" ng-click="show='tabTags'">Tags</a></li>
  <li ng-class="{active:show == 'tabMissingModels'}"><a href="" ng-click="show='tabMissingModels'">Missing Models</a></li>
  <li ng-class="{active:show == 'tabMissingModelsPreview'}"><a href="" ng-click="show='tabMissingModelsPreview'">Missing Models Preview</a></li>
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
<div class="alert alert-warning">Warning! This operation can be carried out within 20 minutes!<br>Do not close the page!</div><br><br>
<button type="submit" disabled class="btn btn-primary" ng-click="tagsRefresh('models')">Refresh</button>
</div>

<div ng-show="show == 'tabBackups'">

<h2><small>Backup Database:</small></h2>
<div class="alert alert-warning">Warning! Do not leave backup files for a long time is not safe!</div><br><br>
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
</div>
</div>
	
<div ng-show="show == 'tabMissingModels'">
<h2><small>Missing Models:</small></h2>

<div class="alert alert-warning">If you have found missing models, please backup these files, delete and then upload again! <br>To back up file please use Windows file explorer. <br>To delete files press "Delete" button in browser.</div>


<div ng-show="!loadingData">
Choose Library:<br>
	
<div class="btn-group dropup">
	<button type="button" class="btn dropdown-toggle btn-default" ng-class="{'btn-success': findIn.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{findIn.name ? findIn.name : 'None'}} <span class="caret"></span></button>
	<ul class="dropdown-menu">
		<li role="presentation" ng-repeat="lib in categories"><a href="" ng-click="selectFindLib(lib.id, lib.name)">{{lib.name}}</a></li>
		
	</ul>
</div>	
<button class="btn btn-primary" ng-click="findMissingModels()">Find</button>
</div>
<hr>

<div ng-show="loadingData" class="loader-container"><div class="loader"></div> Loading...</div>

<div ng-show="missingModels.missing_count==0 && !loadingData">Missing Models not found!</div>
<div class="table-responsive" ng-show="missingModels.missing_count && !loadingData">

Found {{missingModels.missing_count}} models!

<table class="table table-hover">
	<thead>
		<tr>
			<th>#</th><th>Path</th><th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<tr ng-repeat="miss in missingModels.missing">
			<td>{{$index + 1}}.</td>
			<td><input type="text" readonly class="form-control" id="usr" ng-value="miss"></td>
			<td><a href="" ng-click="delMissingModel(miss)">Delete</a></td>
		</tr>
	</tbody>
</table>


</div>

</div>	


<div ng-show="show == 'tabMissingModelsPreview'">
<h2><small>Excess Models Preview:</small></h2>

<div class="alert alert-warning">Warning! This operation can be carried out within 20 minutes!<br>Do not close the page!</div>

<div ng-show="!loadingData">

<button class="btn btn-primary" ng-click="findMissingModelsPreview()">Find Excess Preview</button>
</div>
<hr>

<div ng-show="loadingData" class="loader-container"><div class="loader"></div> Loading...</div>

<div ng-show="missingModelsPreview.missing_count==0 && !loadingData">Missing Models Preview not found!</div>
<div class="table-responsive" ng-show="missingModelsPreview.missing_count && !loadingData">

Found {{missingModelsPreview.missing_count}} models Preview!
<br><br>
<button class="btn btn-danger" ng-click="delMissingModelsPreview(missingModelsPreview.missing)">Delete {{missingModelsPreview.missing_count}}  Previews</button>


</div>


</div>	