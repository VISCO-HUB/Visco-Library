<script type="text/ng-template" id="tags">
	<div class="btn-group margin-10-2" ng-if="tag.length > 1">
		<button type="button" class="btn btn-default btn-xs" disabled>{{tag}}</button>
		<button type="button" class="btn btn-default btn-xs" ng-click="removeTag(tag)" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>	
	</div>
</script>

<div ng-show="product.responce"><h3 class="text-center">You have no access!</h3></div>

<div ng-show="product.info">

<div class="row">
	<a class="btn btn-default pull-right" href="/#/model/{{product.info.id}}">View</a>
	<a class="btn btn-default pull-right" href="" ng-show="auth.browser=='MXS'" ng-click="openModel(product.info.id)">Open</a>
</div>

<h1>Edit: {{product.info.name}}</h1>
<hr>

<h2><small>Status:</small></h2>
<div class="btn-group" data-toggle="buttons">
	<button type="button" class="btn" ng-class="product.info.status == 1 ? 'btn-success' : 'btn-default'" ng-click="prodSetParam('status', '1')">&nbsp;ON&nbsp;</button>
	<button type="button" class="btn" ng-class="product.info.status == 0 ? 'btn-danger' : 'btn-default'" ng-click="prodSetParam('status', '0')">OFF</button>
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
	</<br>
	<br>
	<span class="label label-success" ng-show="product.exist">Directory Exist</span> <span class="label label-danger" ng-show="!product.exist">Directory Not Exist!</span> </div>
<hr>
<h2><small>Preview:</small></h2>
<img ng-src="{{product.previews[pid]}}" class="img-responsive admin-preview"> <br>

<br><br>
<div class="dropdown clr">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{product.previewNames[pid]}} <span class="caret"></span></button> 
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
		<li ng-repeat="name in product.previewNames"><a href="" ng-click="choosePreview($index)">{{name}}</a></li>
	</ul>
</div>

<div class="btn-group">
	<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions <span class="caret"></span></button>
	<ul class="dropdown-menu">
		<li role="presentation"><a href="" ng-click="setMainPreview(product.previewNames[pid])"><span class="glyphicon glyphicon-picture"></span> Set As Main</a></li>
		<li role="presentation" class="divider"></li>
		<li role="presentation"><a href="" ng-click="removePreview(product.previewNames[pid])"><span class="glyphicon glyphicon-remove-circle text-danger"></span> Delete Preview</a></li>
	</ul>
</div>
<br><br>

<hr>
<h2><small>Upload New Preview:</small></h2>

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
		<td width="30%">Format: </td>
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
		<td>Modeller: </td>
		<td>{{product.info.modeller}}</td>
	</tr>
	<tr>
		<td>Manufacturer:</td>
		<td>{{product.info.manufacturer}}</td>
	</tr>
	<tr>
		<td>Client:</td>
		<td>{{product.info.client}}</td>
	</tr>
	<tr>
		<td>Project: </td>
		<td>{{product.info.project}}</td>
	</tr>
	<tr>
		<td>Render: </td>
		<td>{{product.info.render}}</td>
	</tr>
	<tr>
		<td>Unwrap: </td>
		<td>{{yesno(product.info.unwrap)}}</td>
	</tr>
	<tr>
		<td>Game Engine Ready: </td>
		<td>{{yesno(product.info.gameengine)}}</td>
	</tr>
	<tr>
		<td>Custom1: </td>
		<td>{{product.info.custom1}}</td>
	</tr>
	<tr>
		<td>Lights: </td>
		<td>{{yesno(product.info.lights)}}</td>
	</tr>
	<tr>
		<td>Lods: </td>
		<td>{{yesno(product.info.lods)}}</td>
	</tr>
	<tr>
		<td>Baked: </td>
		<td>{{yesno(product.info.baked)}}</td>
	</tr>
	<tr>
		<td>Rigged: </td>
		<td>{{yesno(product.info.rigged)}}</td>
	</tr>
	<tr>
		<td>Uploaded By: </td>
		<td>{{product.info.uploadedby}}</td>
	</tr>
</table>
</div>