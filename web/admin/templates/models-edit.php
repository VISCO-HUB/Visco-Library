<script type="text/ng-template" id="tags">
	<div class="btn-group margin-10-2" ng-if="tag.length > 1">
		<button type="button" class="btn btn-default btn-xs" disabled>{{tag}}</button>
		<button type="button" class="btn btn-default btn-xs" ng-click="removeTag(tag)" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>	
	</div>
</script>

<div ng-show="product.responce"><h3 class="text-center">You have no access!</h3></div>

<div ng-show="product.info">

<div class="row">
	<a class="btn btn-default" ng-show="product.list.prev" href="#/models-edit/{{product.list.prev}}/1"><span class="glyphicon glyphicon-step-backward" aria-hidden="true"></span> Prev Model</a>
	<a class="btn btn-default" ng-show="product.list.next" href="#/models-edit/{{product.list.next}}/1">Next Model <span class="glyphicon glyphicon glyphicon-step-forward" aria-hidden="true"></span></a>

	<button ng-click="downloadUrl(product.info.id, type)" class="btn btn-primary pull-right">Download</button>
	<a class="btn btn-danger pull-right" href="" ng-click="prodDeleteFromEdit(product.info.id, product.info.name)">Delete</a>
	<a class="btn btn-info pull-right" href="/#/model/{{product.info.id}}">View</a>
	<a class="btn btn-default pull-right" href="" ng-show="auth.browser=='MXS'" ng-click="openModel(product.info.id)">Open</a>
	<a class="btn btn-default pull-right" href="" ng-show="auth.browser=='MXS'" ng-click="mergeModel(product.info.id)">Merge</a>
</div>

<h1>Edit: {{product.info.name}}</h1>
<hr>

<h2><small>Status:</small></h2>
<div class="btn-group" data-toggle="buttons">
	<button type="button" class="btn" ng-class="product.info.status == 1 ? 'btn-success' : 'btn-default'" ng-click="prodSetParam('status', '1')">&nbsp;ON&nbsp;</button>
	<button type="button" class="btn" ng-class="product.info.status == 0 ? 'btn-danger' : 'btn-default'" ng-click="prodSetParam('status', '0')">OFF</button>
</div>
<h2><small>Pending:</small></h2>
<div class="btn-group" data-toggle="buttons">	
	<button type="button" class="btn" ng-class="product.info.pending == 0 ? 'btn-success' : 'btn-default'" ng-click="prodSetParam('pending', '0')">&nbsp;NO&nbsp;</button>
	<button type="button" class="btn" ng-class="product.info.pending == 1 ? 'btn-warning' : 'btn-default'" ng-click="prodSetParam('pending', '1')">YES</button>
</div>
<hr>
<h2><small>Name:</small></h2>
<div class="form-group">
	<input type="text" class="form-control" disabled placeholder="{{product.info.name}}">
	<br>
	<mark class="text-muted">Note: Folder will rename automatically!</mark>
	<br>
	<br>
	<button type="submit" class="btn btn-primary" ng-click="productChangeName(product.info.catid, product.info.name)">Change</button>
</div>
<hr>
<h2><small>Path:</small></h2>
<div class="form-group">
	<input type="text" class="form-control" value="{{product.dir}}">	
	<br>
	<span class="label label-success" ng-show="product.exist">Directory Exist</span> <span class="label label-danger" ng-show="!product.exist">Directory Not Exist!</span> </div>
	
	<br>	
<hr>
<h2><small>Preview:</small></h2>
<div class="well well-lg">
	<div class="admin-preview" ng-style="{'background-image': 'url(' + product.previews[pid] + ')'}" >
		<div class="btn-group dropup" style="position: absolute; bottom: 0; left: 0;">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li role="presentation"><a href="" ng-click="setMainPreview(product.previewNames[pid])"><span class="glyphicon glyphicon-picture"></span> Set As Main</a></li>
				<li role="presentation" class="divider"></li>
				<li role="presentation"><a href="" ng-click="removePreview(product.previewNames[pid])"><span class="glyphicon glyphicon-remove-circle text-danger"></span> Delete Preview</a></li>
			</ul>
		</div>	
	</div>
	<br>
	<ul class="prod-gallery">
		<li ng-repeat="item in product.previews" ng-click="choosePreview($index)" 
			ng-style="{'background-image': 'url(' + item + ')'}" 
			ng-class="{'active': $index == pid}"
		></li>
	</ul>
	
	<hr>
	<div class="well well-lg" style="background-color: white;">
	<h2 style="margin: 0"><small>Upload New Preview:</small></h2>

	<div ng-show="uploaderImg.queue[0].progress">
	Upload progress:
	<div class="progress" style="margin-bottom: 0;">
		<div class="progress-bar" role="progressbar" ng-style="{ 'width': uploaderImg.queue[0].progress  + '%' }"></div>
	</div>

	</div>
	<br>
	<label>
	<span type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Upload New Preview</span>
	<input type="file" nv-file-select="" uploader="uploaderImg" class="hidden">
	</label>
	</div>
</div>
<hr>
<h2><small>Description:</small></h2>
<div class="form-group">
	<textarea class="form-control" cols="20" rows="5" ng-model="product.info.overview"></textarea>
	<br>
	<button type="submit" class="btn btn-primary" ng-click="productChangeOverview(product.info.overview)">Save Description</button>
	
</div>
<hr>
<h2><small>Tags:</small></h2>
<span ng-repeat="tag in product.info.tags.split(',') track by $index" ng-include="'tags'">{{tag}}</span> <br>
<br>
<button type="submit" class="btn btn-primary" ng-click="addTag()">Add Tags</button>
<hr>
<h2><small>Info:</small></h2>
<table class="model-info" width="100%">
	<tr>
		<td width="30%">Uploaded By: </td>
		<td>{{product.info.uploadedby}}</td>
		
	</tr>
	<tr>
		<td>Format: </td>
		<td>{{product.info.format}}</td>
		
	</tr>
	<tr>
		<td>Date: </td>
		<td>{{tm(product.info.date)}}</td>
		
	</tr>
	<tr>
		<td>Units: </td>
		<td>{{product.info.units}}</td>
		
	</tr>
	<tr>
		<td>Dimension: </td>
		<td>{{product.info.dim}}</td>
		
	</tr>
	<tr>
		<td>Polys: </td>
		<td>{{product.info.polys}}</td>
		
	</tr>
	<tr>
		<td>Render: </td>
		<td>{{product.info.render}}</td>
		
	</tr>
	<tr>
		<td>Modeller: </td>
		<td>{{product.info.modeller}}			
			<btn-edit ng-click="prodSetTextParam('modeller', product.info.modeller)"></btn-edit>
		</td>
		<td>
			
		</td>
	</tr>
	<tr>
		<td>Manufacturer:</td>
		<td>{{product.info.manufacturer}}
			<btn-edit ng-click="prodSetTextParam('manufacturer', product.info.manufacturer)"></btn-edit>
		</td>			
		<td>			
		</td>
	</tr>
	<tr>
		<td>Client:</td>
		<td>{{product.info.client}}
			<btn-edit ng-click="prodSetTextParam('client', product.info.client)"></btn-edit>
		</td>
		<td>			
		</td>
	</tr>
	<tr>
		<td>Project: </td>
		<td>{{product.info.project}}
			<btn-edit ng-click="prodSetTextParam('project', product.info.project)"></btn-edit>
		</td>
		<td>			
		</td>
	</tr>	
	<tr>
		<td>Custom1: </td>
		<td>{{product.info.custom1}}
			<btn-edit ng-click="prodSetTextParam('custom1', product.info.custom1)"></btn-edit>
		</td>
		<td>			
		</td>
	</tr>
	<tr>
		<td>Unwrap: </td>
		<td>
			<btn-trigger cls="'btn-xs'" active="product.info.unwrap" toggle="prodToggleParam('unwrap')"></btn-trigger>
		</td>
		
	</tr>
	<tr>
		<td>Game Engine Ready: </td>
		<td>
			<btn-trigger cls="'btn-xs'" active="product.info.gameengine" toggle="prodToggleParam('gameengine')"></btn-trigger>
		</td>
		
	</tr>	
	<tr>
		<td>Lights: </td>
		<td>
			<btn-trigger cls="'btn-xs'" active="product.info.lights" toggle="prodToggleParam('lights')"></btn-trigger>
		</td>
		
	</tr>
	<tr>
		<td>Lods: </td>
		<td>
			<btn-trigger cls="'btn-xs'" active="product.info.lods" toggle="prodToggleParam('lods')"></btn-trigger>
		</td>
		
	</tr>
	<tr>
		<td>Baked: </td>
		<td>
			<btn-trigger cls="'btn-xs'" active="product.info.baked" toggle="prodToggleParam('baked')"></btn-trigger>
		</td>
		
	</tr>
	<tr>
		<td>Rigged: </td>
		<td>
			<btn-trigger cls="'btn-xs'" active="product.info.rigged" toggle="prodToggleParam('rigged')"></btn-trigger>
		</td>
		
	</tr>	
</table>
<br>
<div class="well well-lg">
	<h2 style="margin: 0" ng-click="isCollapsed = !isCollapsed"><small>Upload Interactive 3D Model (WebGL):</small></h2>
		<br>
		<iframe id="webgl" ng-src="{{webgl}}" ng-show="webgl.length" iframe-onload="webglMsg()" style="width: 100%" ng-style="webglStyle"></iframe>	
			
		<div ng-show="product.info.webgl">			
			Interactive Model ID: {{product.info.webgl}} <br><br>
			<button ng-show="!webgl.length && auth.browser!='MXS'" class="btn btn-primary" ng-click="webglUrl(product.info.webgl)">View Interactive Model</button>
			<button ng-show="webgl.length && auth.browser!='MXS'" class="btn btn-primary" ng-click="webglUrl(null)">Hide Interactive Model</button>
			<button class="btn btn-danger" ng-click="removeWebGLModel(product.info.webgl)">Delete Interactive Model</button>
		</div>
		<div ng-show="!product.info.webgl">
			<div ng-show="uploaderWebGl.queue[0].progress">
			Upload progress:
			<div class="progress" style="margin-bottom: 0;">
				<div class="progress-bar" role="progressbar" ng-style="{ 'width': uploaderWebGl.queue[0].progress  + '%' }"></div>
			</div>
			</div>
			<br>
			<label>
			<span type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Upload *.zip</span>
			<input type="file" nv-file-select="" uploader="uploaderWebGl" class="hidden">
			</label>
		</div>
</div>

<div class="well well-lg" ng-show="auth.rights==2">
	<h2 style="margin: 0"><small>Move To Category:</small></h2>
	<br>
	<div class="alert alert-warning"><b>Backup Database before moving the model!</b></div>
	<br>	
	
	Choose Category:<br>
	
	<div class="btn-group dropup">
		<button type="button" class="btn dropdown-toggle btn-default" ng-class="{'btn-success': moveToCat[0]}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{moveToCatName[0] ? moveToCatName[0] : 'None'}} <span class="caret"></span></button>
		<ul class="dropdown-menu">
			<li role="presentation" ng-repeat="lib in categories"><a href="" ng-click="selectMoveToCat(lib.id, lib.name, 0)">{{lib.name}}</a></li>
			
		</ul>
	</div>	
	&rarr;
	
	
	<div class="btn-group dropup">
		<button type="button" class="btn dropdown-toggle btn-default" ng-class="{'btn-success': moveToCat[1]}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{moveToCatName[1] ? moveToCatName[1] : 'None'}} <span class="caret"></span></button>
		<ul class="dropdown-menu">
			<li role="presentation" ng-repeat="cat in categories[moveToCat[0]].child"><a href="" ng-click="selectMoveToCat(cat.id, cat.name, 1)">{{cat.name}}</a></li>
			
		</ul>
	</div>	
	
	&rarr;	
	
	<div class="btn-group dropup">
		<button type="button" class="btn dropdown-toggle btn-default" ng-class="{'btn-success': moveToCat[2]}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{moveToCatName[2] ? moveToCatName[2] : 'None'}}  <span class="caret"></span></button>
		<ul class="dropdown-menu">
			<li role="presentation" ng-repeat="sub in categories[moveToCat[0]].child[moveToCat[1]].child"><a href="" ng-click="selectMoveToCat(sub.id, sub.name, 2)">{{sub.name}}</a></li>		
		</ul>
	</div>	
		
	<br><br>
	<button type="button" ng-disabled="!moveToCat[2]" class="btn dropdown-toggle btn-danger" ng-click="moveProduct(moveToCat[2])">Move model  {{moveToCat[2] ? 'to ' + moveToCatName[2] : ''}}</button>
	
	
</div>

</div>