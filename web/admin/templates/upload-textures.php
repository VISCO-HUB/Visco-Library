<h1>Upload Textures</h1>
<hr>
<div class="well well-lg">
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label for="sel1">Select Library:</label>
				<select size="5" class="form-control" id="sel1">
					<option ng-repeat="lib in categories" ng-change="">{{lib.name}}</option>					
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label for="sel2">Select Category:</label>
				<select size="5" class="form-control" id="sel2">
					<option>1</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label for="sel3">Select Sub Category:</label>
				<select size="5" class="form-control" id="sel3">
					<option>1</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
				</select>
			</div>
		</div>
	</div>
	<h2><small>Seamless:</small></h2>
	<btn-trigger cls="" active="0" toggle="uploadTexToggleParam('seamless')"></btn-trigger>
	<h2><small>PBR:</small></h2>
	<btn-trigger cls="" active="0" toggle="uploadTexToggleParam('pbr')"></btn-trigger>
	
	<h2><small>Description:</small></h2>
	<div class="form-group">
		<textarea class="form-control" cols="20" rows="5" ng-model="product.info.overview"></textarea>
	
	</div>
	
	<div class="dropdown">
		<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Additional Info
		<span class="caret"></span></button>
		<ul class="dropdown-menu">
			<li><a href="#">Add Client</a></li>
			<li><a href="#">Add Project</a></li>
			<li><a href="#">Add Custom1</a></li>
			<li><a href="#">Add Manufacturer</a></li>
			<li><a href="#">Add Source</a></li>
		</ul>
	</div>
</div>
