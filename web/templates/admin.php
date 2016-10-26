<?php

	INCLUDE '../vault/config.php';
	INCLUDE '../vault/lib.php';
	
	AUTH::ADMIN();		
?>


<div class="container">

<div class="col-sm-3 col-md-3 col-lg-3"> <br>
	<div class="list-group"> 
		<a href="" class="list-group-item" ng-class="{active: adminSection=='global'}" ng-click="adminSection='global'">Global</a> 
		<a href="" class="list-group-item" ng-class="{active: adminSection=='cat'}" ng-click="adminSection='cat';">Categories</a> 		
	</div>
</div>

<div>

<div class="col-sm-9 col-md-9 col-lg-9"> 

	<!-- CATEGORIES -->
	<div ng-show="adminSection=='global'">
		<h1>Global</h1>	
		<hr>
		<h2><small>Global Path:</small></h2>
		<div class="form-group">
			<input type="text" class="form-control" disabled placeholder="{{globals.path}}">				
		</div>
		<mark class="text-muted">Example: \\visco.local\data\Library\</mark><br><br>
		<button type="submit" class="btn btn-primary" ng-click="adminGlobalChangePath()">Change</button>
		<hr>		
	</div>
	
	
	<!-- CAT -->
	<div ng-show="adminSection=='cat'">
		<h1>Categories</h1>
		<hr>
		<div class="table-responsive">
			<table class="table table-hover">
				<tr>
					<th width="30px">#</th>
					<th>Library</span></th>					
					<th>Status</th>
					<th width="150px">Actions</th>
				</tr>
				<tr ng-repeat="cat in categories" >
					<td>{{$index + 1}}.</td>
					<td><a href="">{{cat.name}}</a></td>
					<td><span ng-show="!cat.status" class="label label-danger">Disabled</span> <span ng-show="cat.status" class="label label-success">Enabled</span></td>
					<td>
						<a href="" ng-click="adminCatEdit(cat.id, cat.name)">Edit</a> &nbsp;&nbsp;|&nbsp;&nbsp;
						<a href="" ng-click="adminCatDel(cat.id, cat.name)">Delete</a>
					</td>					
				</tr>
			</table>
		</div>
		<button class="btn btn-primary" ng-click="adminAddCat()">Add Library</button>	
	</div>	
	
</div>