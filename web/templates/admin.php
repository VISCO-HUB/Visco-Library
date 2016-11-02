<?php

	INCLUDE '../vault/config.php';
	INCLUDE '../vault/lib.php';
	
	AUTH::ADMIN();		
?>

<div class="container">

<div class="col-sm-3 col-md-3 col-lg-3"> <br>
	<div class="list-group"> 
		<a href="" class="list-group-item" ng-class="{active: section=='global'}" ng-click="section='global'">Global</a> 
		<a href="" class="list-group-item" ng-class="{active: section=='cat'}" ng-click="section='cat';">Categories</a> 		
	</div>
</div>

<div>

<div class="col-sm-9 col-md-9 col-lg-9"> 

	<!-- CATEGORIES -->
	<div ng-show="section=='global'">
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
	<div ng-show="section=='cat'">
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
					<td><a href="" ng-click="adminCatEdit(cat.id)">{{cat.name}}</a></td>
					<td>
						<span ng-show="cat.status==0" class="label label-danger pointer" ng-click="adminCatSetParam('status', '1', cat.id); isAdminCatEdit=false;">Disabled</span> 
						<span ng-show="cat.status==1" class="label label-success pointer" ng-click="adminCatSetParam('status', '0', cat.id); isAdminCatEdit=false;">Enabled</span>
					</td>
					<td>
						<a href="" ng-click="adminCatEdit(cat.id)">Edit</a> &nbsp;&nbsp;|&nbsp;&nbsp;
						<a href="" ng-click="adminCatDel(cat.id, cat.name)">Delete</a>
					</td>					
				</tr>
			</table>
		</div>
		
		<div class="btn-group dropup">
			<button type="button" class="btn btn-primary" data-toggle="dropdown">Add Library</button>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		  </button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" tooltip-placement="left" uib-tooltip="Creates specific library type. After creation you can't change library type.">
				<li><a href="" ng-click="adminAddCat(1)" >Model Library</a></li>        			
				<li><a href="" ng-click="adminAddCat(2)" >Texture Library</a></li> 
			</ul>
		</div>
		
	</div>		
</div>

<div class="overlay" ng-show="overlay">
</div>

<script type="text/ng-template" id="treeList">
	<a href="" ng-click="subCatEdit(subcat.id)" ng-class="{active: isSubCatActive(subcat.id)}">{{subcat.name}} <i ng-show="level[subcat.id] < 2">({{count(subcat.child)}})</i></a> 
	<span class="pull-right">
		<span ng-show="subcat.status==0" class="label label-danger pointer" ng-click="adminCatSetParam('status', '1', subcat.id);">OFF</span> 
		<span ng-show="subcat.status==1" class="label label-success pointer" ng-click="adminCatSetParam('status', '0', subcat.id);">ON</span>
	</span>
	
    <ul ng-if="subcat.child">
        <li ng-repeat="subcat in subcat.child" ng-include="'treeList'" ng-init="level[subcat.id]=2">           
        </li>
    </ul>
</script>

<div ng-show="isAdminCatEdit; overlay=isAdminCatEdit" class="lightbox">	
	
	<span class="lightbox-close" ng-click="isAdminCatEdit=false"></span>
	
	<h1>Edit Library: {{categories[adminCatEditId].name}}</h1>
	<div alerts></div>
	<h2><small>Status:</small></h2>
	<div class="btn-group" data-toggle="buttons">
		<button type="button" class="btn" ng-class="categories[adminCatEditId].status == 1 ? 'btn-success' : 'btn-default'" ng-click="adminCatSetParam('status', '1', adminCatEditId)">&nbsp;ON&nbsp;</button>
		<button type="button" class="btn" ng-class="categories[adminCatEditId].status == 0 ? 'btn-danger' : 'btn-default'" ng-click="adminCatSetParam('status', '0', adminCatEditId)">OFF</button>
	</div>
	<hr>
	<h2><small>Name:</small></h2>
	<div class="form-group">
		<input type="text" class="form-control" disabled placeholder="{{categories[adminCatEditId].name}}"><br>		
		<mark class="text-muted">Note: Folder will rename automatically!</mark><br><br>
		<button type="submit" class="btn btn-primary" ng-click="adminChangeName(adminCatEditId)">Change</button>
	</div>
	<hr>
	<h2><small>Description:</small></h2>
	<div class="form-group">		
		<textarea class="form-control" cols="20" rows="2" disabled>{{categories[adminCatEditId].desc}}</textarea><br>
		<button type="submit" class="btn btn-primary" ng-click="adminChangeDesc(adminCatEditId)">Change</button>
	</div>
	<hr>
	<h2><small>Hierarchy:</small></h2>			
	<div class="col-sm-12 col-md-12col-lg-12">		
		<div class="admin-cat-hierarchy col-sm-4 col-md-4 col-lg-4">
			<a href="" ng-click="subCatEdit(adminCatEditId)" ng-class="{active: isSubCatActive(categories[adminCatEditId].id)}">{{categories[adminCatEditId].name}} <i>({{count(categories[adminCatEditId].child)}})</i></a>
			<ul>
				<li ng-repeat="subcat in categories[adminCatEditId].child" ng-include="'treeList'" class="no" ng-init="level[subcat.id]=1;"> </li>
			</ul>  				
		</div>
	</div>
	<button class="btn btn-primary" ng-click="adminAddCat(subCatEditID)" ng-class="{disabled: level[subCatEditID] > 1}">Add</button> 
	<button class="btn btn-danger" ng-class="{disabled: subCatEditID == adminCatEditId}" ng-click="adminSubCatDel(subCatEditID)">Delete</button>
	<button class="btn btn-warning" ng-class="{disabled: subCatEditID == adminCatEditId}" ng-click="adminSubCatRename(subCatEditID)">Rename</button>
</div>