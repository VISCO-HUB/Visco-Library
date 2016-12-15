<h1>Settings</h1>	
<hr>

<ul class="nav nav-tabs">
  <li ng-class="{active:show == 'tab1'}" ng-click="show='tab1'"><a href="">Global</a></li>
  <li ng-class="{active:show == 'tab2'}"><a href="" ng-click="show='tab2'">Mail</a></li>
  <li ng-class="{active:show == 'tab3'}"><a href="" ng-click="show='tab3'">System</a></li>
  <li ng-class="{active:show == 'tab4'}"><a href="" ng-click="show='tab4'">View</a></li>
</ul>

<div ng-show="show == 'tab1'">
<h2><small>Global Path:</small></h2>
<div class="form-group">
	<input type="text" class="form-control" disabled placeholder="{{globals.path}}">				
</div>
<mark class="text-muted">Example: \\visco.local\data\Library\</mark><br><br>
<button type="submit" class="btn btn-primary" ng-click="globalsChange()">Change</button>


	
	