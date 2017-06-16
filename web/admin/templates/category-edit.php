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

<script type="text/ng-template" id="permissions">
	<div class="btn-group margin-10-2" ng-if="permission">
		<button type="button" class="btn btn-default btn-xs" disabled>{{permission.name ? permission.name : 'All'}}</button>
		<button type="button" class="btn btn-default btn-xs" ng-click="togglePermission(catId, permission.id)" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>	
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
	<button type="submit" class="btn btn-primary" ng-click="catChangeName(catId)">Change</button>
</div>
<hr>
<h2><small>Description:</small></h2>
<div class="form-group">		
	<textarea class="form-control" cols="20" rows="2" disabled>{{categories[catId].desc}}</textarea><br>
	<button type="submit" class="btn btn-primary" ng-click="catChangeDesc(catId)">Change</button>
</div>
<div ng-show="auth.rights==2">
	<hr>
	<h2><small>Moderators:</small></h2>	
	<span ng-repeat="editor in categories[catId].editors.split(';') track by $index" ng-include="'editors'">{{editor}}</span> 
	<br>
	<br>
	
	<div class="btn-group dropup" tooltip-placement="right" uib-tooltip="Add moderators who may edit this library.">
		<button type="button" class="btn btn-primary" data-toggle="dropdown" >Add Moderator</button>
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	  </button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
			<li ng-repeat="user in userFilterList.moderators" ng-init="c = categories[catId].editors.split(';').indexOf(user.user) != -1">
				<a href="" ng-click="toggleEditor(catId, user.user)" >
					<span class="glyphicon" ng-class="{'glyphicon glyphicon-check': c, 'glyphicon-unchecked': !c}" aria-hidden="true"></span>&nbsp;&nbsp;
					{{user.user}}
				</a>
			</li>        					
		</ul>
	</div>
</div>
<div ng-show="auth.rights==2">
	<hr>
	<h2><small>Permissions:</small></h2>	
	<mark class="text-muted">Note: If not specified any group, access to the category will have all users!</mark><br><br>
	<span ng-init="permission='-1'" ng-include="'permissions'" ng-show="!categories[catId].groups.length">All</span> 
	<span ng-repeat="permission in categories[catId].groups" ng-include="'permissions'"></span> 
	<br>
	<br>
	
	<div class="btn-group dropup" tooltip-placement="right" uib-tooltip="Add the groups that will have access.">
		<button type="button" class="btn btn-primary" data-toggle="dropdown" >Add Group</button>
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	  </button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
			<li ng-repeat="grp in userFilterList.grp">			
				<a href="" ng-click="togglePermission(catId, grp.id)" ng-init="c = checkGroup(grp.id, categories[catId].groups)">
					<span class="glyphicon" ng-class="{'glyphicon glyphicon-check': c, 'glyphicon-unchecked': !c}" aria-hidden="true"></span>&nbsp;&nbsp;
					{{grp.name}}
				</a>
			</li>        					
		</ul>
	</div>
<h2><small>Allow Download:</small></h2>
<mark class="text-muted">Note: If set "NO", download can only administrators!</mark><br><br>
<div class="btn-group" data-toggle="buttons">
	<button type="button" class="btn" ng-class="categories[catId].candl==1 ? 'btn-success' : 'btn-default'" ng-click="catSetParam('candl', '1', catId)">&nbsp;YES&nbsp;</button>
	<button type="button" class="btn" ng-class="categories[catId].candl!=1 ? 'btn-danger' : 'btn-default'" ng-click="catSetParam('candl', '0', catId)">&nbsp;&nbsp;NO&nbsp;&nbsp;</button>
</div>	
</div>
<hr>
<h2><small>Categories:</small></h2>			
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