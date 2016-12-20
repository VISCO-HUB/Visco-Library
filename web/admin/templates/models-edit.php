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
	<input type="text" class="form-control" disabled placeholder="{{product.info.name}}"><br>		
	<mark class="text-muted">Note: Folder will rename automatically!</mark><br><br>
	<button type="submit" class="btn btn-primary" ng-click="catChangeName(catId)">Change</button>
</div>
<hr>
<h2><small>Preview:</small></h2>
<img ng-src="{{product.previews[pid]}}" class="img-responsive admin-preview">
<br>
<div class="dropdown clr">
	<div class="btn-group">
		<button type="button" class="btn btn-default">{{product.previewNames[pid]}}</button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
			<li ng-repeat="name in product.previewNames"><a href="" ng-click="choosePreview($index)">{{name}}</a></li>			
		</ul>
	</div>
</div>
<hr>
<h2><small>Overview:</small></h2>
<div class="form-group">		
	<textarea class="form-control" cols="20" rows="2" disabled>{{product.info.overview}}</textarea><br>
	<button type="submit" class="btn btn-primary" ng-click="catChangeDesc(catId)">Change</button>
</div>
<hr>
<h2><small>Info:</small></h2>
<table class="model-info">
		<tr>
			<td width="100px">Format: </td>
			<td>{{product.info.format}}</td>
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
			<td>Unwrap: </td>
			<td>{{yesno(product.info.unwrap)}}</td>
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
</table>







