<script type="text/ng-template" id="treeList">
	<a href="" ng-click="subCatEdit(subcat.id)" ng-class="{active: isSubCatActive(subcat.id)}">{{subcat.name}} <i ng-show="level[subcat.id] < 2">({{count(subcat.child)}})</i></a> 
	<span class="pull-right">
		<span ng-show="subcat.status==0" class="label label-danger pointer" ng-click="catSetParam('status', '1', subcat.id);">OFF</span> 
		<span ng-show="subcat.status==1" class="label label-success pointer" ng-click="catSetParam('status', '0', subcat.id);">ON</span>
	</span>
	
    <ul ng-if="subcat.child">
        <li ng-repeat="subcat in subcat.child" ng-include="'treeList'" ng-init="level[subcat.id]=2">           
        </li>
    </ul>
</script>

<script type="text/ng-template" id="editors">
	<div class="btn-group margin-10-2" ng-if="editor.length > 1">
		<button type="button" class="btn btn-default btn-xs" disabled>{{editor}}</button>
		<button type="button" class="btn btn-default btn-xs" ng-click="removeEditor(catId, editor)" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>	
	</div>
</script>

<h1>Edit Library: {{categories[catId].name}}</h1>
<h2><small>Library Type:</small></h2>
<input type="text" class="form-control" disabled placeholder="{{libType(categories[catId].type)}}">
<h2><small>Status:</small></h2>
<div class="btn-group" data-toggle="buttons">
	<button type="button" class="btn" ng-class="categories[catId].status == 1 ? 'btn-success' : 'btn-default'" ng-click="catSetParam('status', '1', catId)">&nbsp;ON&nbsp;</button>
	<button type="button" class="btn" ng-class="categories[catId].status == 0 ? 'btn-danger' : 'btn-default'" ng-click="catSetParam('status', '0', catId)">OFF</button>
</div>
<hr>
<h2><small>Name:</small></h2>
<div class="form-group">
	<input type="text" class="form-control" disabled placeholder="{{categories[catId].name}}"><br>		
	<mark class="text-muted">Note: Folder will rename automatically!</mark><br><br>
	<button type="submit" class="btn btn-primary" ng-click="catChangeName(catId)">Change</button>
</div>
<hr>
<h2><small>Description:</small></h2>
<div class="form-group">		
	<textarea class="form-control" cols="20" rows="2" disabled>{{categories[catId].desc}}</textarea><br>
	<button type="submit" class="btn btn-primary" ng-click="catChangeDesc(catId)">Change</button>
</div>
<hr>
<h2><small>Hierarchy:</small></h2>			
<div class="col-sm-12 col-md-12col-lg-12">		
	<div class="admin-cat-hierarchy col-sm-6 col-md-6 col-lg-6">
		<a href="" ng-click="subCatEdit(catId)" ng-class="{active: isSubCatActive(categories[catId].id)}">{{categories[catId].name}} <i>({{count(categories[catId].child)}})</i></a>
		<ul>
			<li ng-repeat="subcat in categories[catId].child" ng-include="'treeList'" class="no" ng-init="level[subcat.id]=1;"> </li>
		</ul>  				
	</div>
</div>
<button class="btn btn-primary" ng-click="addCat(subCatEditID, categories[catId].type)" ng-class="{disabled: level[subCatEditID] > 1}">Add</button> 
<button class="btn btn-danger" ng-class="{disabled: subCatEditID == catId}" ng-click="subCatDel(subCatEditID)">Delete</button>
<button class="btn btn-warning" ng-class="{disabled: subCatEditID == catId}" ng-click="subCatRename(subCatEditID)">Rename</button>
<hr>
<div ng-show="auth.rights==2">
	<h2><small>Editors:</small></h2>	
	<span ng-repeat="editor in categories[catId].editors.split(';') track by $index" ng-include="'editors'">{{editor}}</span> 
	<br>
	<br>
	
	<div class="btn-group dropup" tooltip-placement="right" uib-tooltip="Add admins who may edit this library.">
		<button type="button" class="btn btn-primary" data-toggle="dropdown" >Add Editor</button>
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	  </button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
			<li ng-repeat="user in users.users" ><a href="" ng-click="addEditor(catId, user.user)" >{{user.user}}</a></li>        					
		</ul>
	</div>
</div>