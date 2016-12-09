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
		<a href="" class="list-group-item" ng-class="{active: section=='upload'}" ng-click="section='upload';">Upload</a> 		
	</div>
</div>

<div>

<div class="col-sm-9 col-md-9 col-lg-9"> 

	<!-- UPLOAD -->

	<div ng-show="section=='upload'">
		<h1>Upload</h1>
		<hr>
		<div ng-controller="uploadCtrl" nv-file-drop="" uploader="uploader" filters="queueLimit, customFilter, zipFilter">	
	
		<div style="margin-bottom: 40px">

			<p>File count: {{ uploader.queue.length }} / 10</p>

                    <table class="table">
                        <thead>
                            <tr>
                                <th width="50%">Name</th>
                                <th ng-show="uploader.isHTML5">Size</th>
                                <th ng-show="uploader.isHTML5">Progress</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="item in uploader.queue">
                                <td><strong>{{ item.file.name }}</strong></td>
                                <td ng-show="uploader.isHTML5" nowrap>{{ item.file.size/1024/1024|number:2 }} MB</td>
                                <td ng-show="uploader.isHTML5">
                                    <div class="progress" style="margin-bottom: 0;">
                                        <div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span ng-show="item.isSuccess"><i class="glyphicon glyphicon-ok"></i></span>
                                    <span ng-show="item.isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>
                                    <span ng-show="item.isError"><i class="glyphicon glyphicon-remove"></i></span>
                                    <span ng-show="item.isReplace"><i class="glyphicon glyphicon-refresh"></i></span>
                                </td>
                                <td nowrap>
                                    <button type="button" class="btn btn-success btn-xs" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess" ng-show="!item.isReplace">
                                        <span class="glyphicon glyphicon-upload"></span> Upload
                                    </button>
									<button type="button" class="btn btn-danger btn-xs" ng-click="replaceUpload(item)" ng-disabled="item.isReady || item.isUploading || item.isSuccess" ng-show="item.isReplace">
                                        <span class="glyphicon glyphicon-refresh"></span> Replace
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading">
                                        <span class="glyphicon glyphicon-ban-circle"></span> Cancel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-xs" ng-click="item.remove()">
                                        <span class="glyphicon glyphicon-trash"></span> Clear
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                        <div>
                            Queue progress:
                            <div class="progress" style="">
                                <div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length" ng-show="true">
                            <span class="glyphicon glyphicon-upload"></span> Upload All
                        </button>
                        <button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading">
                            <span class="glyphicon glyphicon-ban-circle"></span> Cancel All
                        </button>
                        <button type="button" class="btn btn-danger btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length">
                            <span class="glyphicon glyphicon-trash"></span> Clear All
                        </button>
                    </div>

                </div>

	<div ng-show="uploader.isHTML5">                        
                        <div class="well drop-zone" nv-file-over="" uploader="uploader">
                            <span>Click here or drop files!</span>
							  <input type="file" nv-file-select="" uploader="uploader" multiple/><br/>
                        </div>                       
                    </div>

                    <!-- Example: nv-file-select="" uploader="{Object}" options="{Object}" filters="{String}" -->                    
		</div>
	</div>

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
					<th>Library</th>		
					<th>Type</th>					
					<th>Status</th>
					<th>Sort</th>
					<th width="150px">Actions</th>
				</tr>
				<tr ng-repeat="cat in categories | orderObjectBy:'sort'" >
					<td>{{$index + 1}}.</td>
					<td><a href="" ng-click="adminCatEdit(cat.id)">{{cat.name}}</a></td>
					<td>{{libType(cat.type)}}</td>
					<td>
						<span ng-show="cat.status==0" class="label label-danger pointer" ng-click="adminCatSetParam('status', '1', cat.id); isAdminCatEdit=false;">Disabled</span> 
						<span ng-show="cat.status==1" class="label label-success pointer" ng-click="adminCatSetParam('status', '0', cat.id); isAdminCatEdit=false;">Enabled</span>
					</td>
					<td>
						<span class="glyphicon glyphicon-triangle-bottom pointer" aria-hidden="true" ng-click="changeSort(cat.id, -1)"></span>&nbsp;&nbsp;
						<span class="glyphicon glyphicon-triangle-top pointer" aria-hidden="true" ng-click="changeSort(cat.id, 1)"></span>
					</td>
					<td>
						<a href="" ng-click="adminCatEdit(cat.id)">Edit</a> &nbsp;&nbsp;|&nbsp;&nbsp;
						<a href="" ng-click="libDel(cat.id, cat.name)">Delete</a>
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
				<li><a href="" ng-click="addLibrary(1)" >Model Library</a></li>        			
				<li><a href="" ng-click="addLibrary(2)" >Texture Library</a></li> 
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
	<h2><small>Library Type:</small></h2>
	<input type="text" class="form-control" disabled placeholder="{{libType(categories[adminCatEditId].type)}}">
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
	<button class="btn btn-primary" ng-click="adminAddCat(subCatEditID, categories[adminCatEditId].type)" ng-class="{disabled: level[subCatEditID] > 1}">Add</button> 
	<button class="btn btn-danger" ng-class="{disabled: subCatEditID == adminCatEditId}" ng-click="adminSubCatDel(subCatEditID)">Delete</button>
	<button class="btn btn-warning" ng-class="{disabled: subCatEditID == adminCatEditId}" ng-click="adminSubCatRename(subCatEditID)">Rename</button>
</div>