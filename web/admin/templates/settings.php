<h1>Settings</h1>	
<hr>

<ul class="nav nav-tabs">
  <li ng-class="{active:show == 'tabGlobal'}" ng-click="show='tabGlobal'"><a href="">Global</a></li>
  
  <!--<li ng-class="{active:show == 'tab2'}"><a href="" ng-click="show='tab2'">Mail</a></li>
  <li ng-class="{active:show == 'tab3'}"><a href="" ng-click="show='tab3'">System</a></li>
  <li ng-class="{active:show == 'tab4'}"><a href="" ng-click="show='tab4'">View</a></li>-->

</ul>


<h2><small>Global Status:</small></h2>
<div class="btn-group" data-toggle="buttons">
	<button type="button" class="btn" ng-class="globals.status == 1 ? 'btn-success' : 'btn-default'" ng-click="globSetParam('status', '1')">&nbsp;ON&nbsp;</button>
	<button type="button" class="btn" ng-class="globals.status == 0 ? 'btn-danger' : 'btn-default'" ng-click="globSetParam('status', '0')">OFF</button>
</div>
<br><br>
<div class="form-group">
	<input type="text" class="form-control" disabled placeholder="{{globals.message}}">				
</div>

<button type="submit" class="btn btn-primary" ng-click="globSetParam('message', null, globals.message)">Change</button>
<hr>

<div ng-show="show == 'tabGlobal'">
<h2><small>Global Path:</small></h2>
<div class="form-group">
	<input type="text" class="form-control" disabled placeholder="{{globals.path}}">				
</div>
<mark class="text-muted">Example: \\visco.local\data\Library\</mark><br><br>
<button type="submit" class="btn btn-primary" ng-click="globalsPath()">Change</button>
</div>

	