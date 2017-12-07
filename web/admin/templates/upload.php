<h1>Upload Models</h1>
<hr>
<div nv-file-drop="" uploader="uploader" filters="queueLimit, customFilter, zipFilter">
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
				<td ng-show="uploader.isHTML5"><div class="progress" style="margin-bottom: 0;">
						<div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
					</div></td>
				<td class="text-center">
					<span ng-show="item.isSuccess && !item.isUploading"><i class="glyphicon glyphicon-ok"></i></span> 
					<span ng-show="item.isCancel && !item.isUploading"><i class="glyphicon glyphicon-ban-circle"></i></span> 
					<span ng-show="item.isError && !item.isUploading"> <i class="glyphicon glyphicon-remove"></i></span> 
					<span ng-show="item.isReplace && !item.isUploading"><i class="glyphicon glyphicon-refresh"></i> </span>
					<div ng-show="item.isUploading" class="loader-container"><div class="loader" style="position: static;"></div></div>
				</td>
				<td nowrap><button type="button" class="btn btn-success btn-xs" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess" ng-show="!item.isReplace"> <span class="glyphicon glyphicon-upload"></span> Upload </button>
					<button type="button" class="btn btn-danger btn-xs" ng-click="replaceUpload(item)" ng-disabled="item.isReady || item.isUploading || item.isSuccess" ng-show="item.isReplace"> <span class="glyphicon glyphicon-refresh"></span> Replace </button>
					<button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading || item.isReplace"> <span class="glyphicon glyphicon-ban-circle"></span> Cancel </button>
					<button type="button" class="btn btn-danger btn-xs" ng-click="item.remove()"> <span class="glyphicon glyphicon-trash"></span> Clear </button></td>
			</tr>
		</tbody>
	</table>
	<div>
		<div> Queue progress:
			<div class="progress" style="">
				<div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
			</div>
		</div>
		<button type="button" class="btn btn-success btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length" ng-show="true"> <span class="glyphicon glyphicon-upload"></span> Upload All </button>
		<button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading"> <span class="glyphicon glyphicon-ban-circle"></span> Cancel All </button>
		<button type="button" class="btn btn-danger btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length"> <span class="glyphicon glyphicon-trash"></span> Clear All </button>
	</div>
</div>
<div ng-show="uploader.isHTML5">
	<div class="well drop-zone" nv-file-over="" uploader="uploader"> <span><i style="font-size: 2em;transform: translateY(30%);" class="glyphicon glyphicon-upload" aria-hidden="true"></i> Click here or drop files!</span>
		<input type="file" nv-file-select="" uploader="uploader" multiple/>
		<br/>
	</div>
</div>
