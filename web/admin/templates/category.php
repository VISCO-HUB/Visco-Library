<script type="text/ng-template" id="groups">
	<div class="btn-group margin-10-2">
		<span class="label label-simple">{{dispgrp.name}}</span>		
	</div>
</script>

<h1>Libraries</h1>
<hr>
<div class="table-responsive">
	<table class="table table-hover">
		<tr class="active">
			<th width="30px">#</th>				
			<th width="120px">Library</th>		
			<th>Type</th>					
			<th>Status</th>
			<th>Download</th>
			<th>Groups</th>
			<th ng-show="auth.rights==2" width="60px">Sort</th>
			<th width="150px">Actions</th>
		</tr>
		<tr ng-repeat="cat in categories | orderObjectBy:'sort'" ng-if="cat.editors.length && cat.editors.split(';').indexOf(auth.user)!=-1 || auth.rights==2">
			<td>{{$index + 1}}.</td>
			<td><a href="#/category-edit/{{cat.id}}">{{cat.name}}</a></td>
			<td>{{libType(cat.type)}}</td>
			<td>
				<span ng-show="cat.status==0" class="label label-default pointer" ng-click="catSetParam('status', '1', cat.id)">Disabled</span> 
				<span ng-show="cat.status==1" class="label label-success pointer" ng-click="catSetParam('status', '0', cat.id)">Enabled</span>
			</td>
			<td>
				<span ng-show="cat.candl!=1" class="label label-default pointer" ng-click="catSetParam('candl', '1', cat.id)">No</span> 
				<span ng-show="cat.candl==1" class="label label-success pointer" ng-click="catSetParam('candl', '0', cat.id)">Yes</span>
			</td>
			<td>						
				<span uib-dropdown dropdown-append-to-body="true">
				  <a href id="link-dropdown" uib-dropdown-toggle class="href-clear">
					<span ng-show="cat.groups.length" ng-repeat="dispgrp in cat.groups" ng-include="'groups'"></span> 					
					<span ng-show="!cat.groups.length" ng-init="dispgrp.name='All&nbsp;'" ng-include="'groups'"></span> 				
				  </a>
				  <ul class="dropdown-menu"  uib-dropdown-menu aria-labelledby="link-dropdown" ng-show="auth.rights == 2">
						<li ng-repeat="g in userFilterList.grp" ng-init="c = checkGroup(g.id, cat.groups)">											
							<a href ng-click="togglePermission(cat.id, g.id)">
								<span class="glyphicon" ng-class="{'glyphicon glyphicon-check': c,  'glyphicon-unchecked': !c}" aria-hidden="true"></span>&nbsp;&nbsp;
								{{g.name}}							
							</a>
						</li>
				  </ul>
				</span>	
			</td>			
			<td ng-show="auth.rights==2">
				<span class="glyphicon text-gray glyphicon-triangle-bottom pointer" aria-hidden="true" ng-click="changeSort(cat.id, -1)"></span>&nbsp;&nbsp;
				<span class="glyphicon text-gray glyphicon-triangle-top pointer" aria-hidden="true" ng-click="changeSort(cat.id, 1)"></span>
			</td>
			<td>
				<a href="#/category-edit/{{cat.id}}">Edit</a><span ng-show="auth.rights==2"> &nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="" ng-click="libDel(cat.id, cat.name)">Delete</a></span>
			</td>					
		</tr>
	</table>
</div>

<div class="btn-group dropup" tooltip-placement="right" uib-tooltip="Creates specific library type. After creation you can't change library type." ng-show="auth.rights==2">
	<button type="button" class="btn btn-primary" data-toggle="dropdown" >Add Library</button>
	<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	<span class="caret"></span>
	<span class="sr-only">Toggle Dropdown</span>
  </button>
	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
		<li><a href="" ng-click="addLibrary(1)" >Model Library</a></li>        			
		<li><a href="" ng-click="addLibrary(2)" >Texture Library</a></li> 
	</ul>
</div>





	