<script type="text/ng-template" id="tags">
	<a href="#/search/{{tag | encode}}/-1/1/1" class="btn-group margin-2-2 href-clear">		
		<span class="label label-simple">{{tag}}</span>	
	</a>
</script>

<div ng-show="product.product.name.length">
	<div class="col-md-12 col-lg-8 col-sm-12 col-xs-12">
	<div class="prod-gallery-frame">
		<img src="img/loading.gif" ng-src="{{productGallery[currItem]}}" ng-class="{'show': !showLightBox}" class="img-responsive margin-auto pointer" ng-click="hideShowLightBox(true, prod.previews)" fade-in>		
	</div>
	<br>
		<ul class="prod-gallery" ng-show="productGalleryPreviews.length > 1 && !showLightBox">
			<li ng-repeat="item in productGalleryPreviews" ng-style="{'background-image': 'url(' + item + ')'}" ng-click="showItem($index)" ng-class="{'active': $index == currItem}"></li>
			<li ng-style="{'background-image': 'url(' + productGalleryPreviews[0] + ')'}" ng-show="prod.webgl && auth.browser!='MXS'" ng-click="webglUrl(prod.webgl, prod.name)">
				<div style="background-image:url('/img/3d2.svg'); background-size: contain; height: 20px; width: 20px; bottom: 0" ></div>
			</li>
		</ul>
		<br>
		<ul class="nav nav-tabs">
			  <li ng-class="{'active': tabinfo=='desc'}"><a href="" ng-click="changeTabInfo('desc')">Description</a></li>
			  <li ng-class="{'active': tabinfo=='files'}"><a href="" ng-click="changeTabInfo('files')">Files</a></li>
			  <li ng-class="{'active': tabinfo=='comments'}"><a href="" ng-click="changeTabInfo('comments')">Comments ({{comments.length}})</a></li>
		</ul>
		<br>
		<div ng-show="tabinfo=='desc'">
			<pre ng-show="prod.overview" class="first-capitalize decription" ng-bind-html="prod.overview | br | linky:'_blank':{rel: 'nofollow'}"></pre>
			<div ng-show="!prod.overview" class="text-muted text-center">No description...</div>
		</div>
		<div ng-show="tabinfo=='comments'">						
			<div comments></div>			
		</div>
		<div ng-show="tabinfo=='files'">
			<div ng-show="!fileList.responce" class="text-center text-muted"><br>Loading...</div>
			<div class="file-list" ng-show="fileList.responce == 'OK'">
				<h4>Files</h4>
					
				<table class="table table-striped">
					<tr>
						<td>#</td>
						<td>Name</td>
					</tr>	
					<tr ng-repeat="file in fileList.files.file">
						<td>
							<a href ng-click="downloadItem(prod.id, libType, file)" class="file-list-href" uib-tooltip="Download"><span class="glyphicon glyphicon-download-alt inline" aria-hidden="true"></span></a>
						</td>
						<td class="wrap">
							{{file}}
						</td>
					</tr>
				</table>
						
					
				<div ng-show="fileList.files.img.length">
					<h4>Images</h4>
					<table class="table table-striped">
						<tr>
							<td>#</td>
							<td>Preview</td>
							<td>Name</td>
						</tr>
						<tr ng-repeat="img in fileList.files.img">
							<td>
								<a href ng-click="downloadItem(prod.id, libType, (img | rmdir))" class="file-list-href" uib-tooltip="Download"><span class="glyphicon glyphicon-download-alt inline" aria-hidden="true"></span></a>
							</td>
							<td>								
								<img drop-file="{{fileList.path + key + '\\' + img}}" ng-src="vault/r.php?p={{fileList.path + key + '\\' + img}}" uib-tooltip="{{imgSize($index)}}" title="{{img | rmdir}}"></span>
							</td>
							<td class="wrap">
								{{img | rmdir}}
							</td>
						</tr>
					</table>
				</div>				
			</div>
		</div>
	</div>
	<div class="col-md-12 col-lg-4 col-sm-12 col-xs-12 fontsize-12">
		<h3 class="capitalize">{{prod.name}}</h3>
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-rating" ng-class="{'highlight': product.userrate}" uib-tooltip="Rate model" ng-click="rateProduct(prod.id, libType)"> &nbsp;&nbsp;</button>&nbsp;
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-heart" uib-tooltip="Add to favorite" ng-click="hideShowQuickFavortites(prod)"> &nbsp;&nbsp;</button>&nbsp;
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-download" uib-tooltip="Download model" ng-click="downloadUrl(prod.id, libType)" ng-show="auth.rights >= 0 && product.candl"> &nbsp;&nbsp;</button>&nbsp;
		<a ng-href="admin/#/models-edit/{{prod.id}}/1" class="btn btn-default custom-button-gray button-fixed button-edit" uib-tooltip="Edit" ng-show="auth.rights > 0"> &nbsp;&nbsp;</a>&nbsp;
		<br><br>
		<hr>		
				
		<div class="text-muted">
			<div class="capitalize"><strong>Date:</strong> {{tm(prod.date)}}</div>
			<div class="capitalize"><strong>Downloads:</strong> {{prod.downloads}}</div>
			<div class="capitalize"><strong>Rating:</strong> <a>{{product.rating}}</a></div>
			<br>
			<div class="capitalize"><strong>Manufacturer:</strong> {{isNA(prod.manufacturer)}}</div>		
			<div class="capitalize"><strong>Project:</strong> {{isNA(prod.project)}}</div>
			<div class="capitalize"><strong>Client:</strong> {{isNA(prod.client)}}</div>		
			<div><strong>Modeller:</strong> {{isNA(prod.modeller)}}</div>		
			<div class="capitalize" ng-show="prod.custom1"><strong>Custom1:</strong> {{isNA(prod.custom1)}}</div>
			<div><strong>Uploaded By:</strong> 
				<a ng-href="#/user/{{prod.uploadedby}}" ng-show="prod.uploadedby">{{prod.uploadedby}}</a>
				<span ng-show="!prod.uploadedby">{{isNA(prod.uploadedby)}}</span>
			</div>
	
			<hr>
			<div><strong>Polys:</strong> {{prod.polys}}</div>
			<div><strong>Renderer:</strong> {{prod.render}}</div>
			<div><strong>Dimension: </strong>{{getDim(prod.dim, prod.units)}}</div>
			<div><strong>Units:</strong> {{prod.units}}</div>
			<div><strong>Format:</strong> {{prod.format}}</div>
			<br>
			<div class="capitalize"><strong>Unwrapped:</strong> {{yesNo(prod.unwrap)}}</div>
			<div class="capitalize"><strong>Lights:</strong> {{yesNo(prod.lights)}}</div>
			<div class="capitalize"><strong>Baked Textures:</strong> {{yesNo(prod.baked)}}</div>
			<div class="capitalize"><strong>Rigged:</strong> {{yesNo(prod.rigged)}}</div>
			<div class="capitalize"><strong>Game Engine Ready:</strong> {{yesNo(prod.gameengine)}}</div>
			<div class="capitalize"><strong>Lods:</strong> {{yesNo(prod.lods)}}</div>
			<br>
			<div><strong>Tags:</strong><br><span ng-repeat="tag in prod.tags" ng-include="'tags'">{{tag}}</span></div>
		</div>
		<hr>
		<button type="button" ng-click="downloadUrl(prod.id, libType)" class="btn btn-block margin-0" ng-show="auth.browser!='MXS' && (auth.rights >= 0 && product.candl)" ng-class="{ 'btn-danger': prodError[prod.id], 'btn-primary': !prodError[prod.id]}">
			<span ng-show="!prodError[prod.id]">Download</span>
			<span ng-show="prodError[prod.id]==1">File Not Found!</span>
			<span ng-show="prodError[prod.id]==2">No Access!</span>
		</button>

		<table class="dropup margin-0" tooltip-placement="right" ng-show="auth.browser=='MXS'" width="100%">
			<tr>
			<td>
				<button type="button" class="btn btn-primary btn-block" ng-click="placeModel(prod.id)"> 
					{{placename}} 
				</button>
			</td>
			<td width="30">
			<div class="dropdown">
			  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Dropdown Example
			  <span class="caret"></span></button>
			  <ul class="dropdown-menu">
				<li><a href="#">HTML</a></li>
				<li><a href="#">CSS</a></li>
				<li><a href="#">JavaScript</a></li>
			  </ul>
			</div>
			</td>
			</tr>
		</table>
		
	</div>
</div>
<div ng-show="!product.product.name.length && !product.responce"><h3 class="text-center">Loading...</h3></div>
<div ng-show="product.responce == 'PRODINFONOTEXIST'"><h3 class="text-center">Model not found!</h3></div>
<div ng-show="product.responce == 'PRODINFONOACCESS'"><h3 class="text-center">You have no access to view this content!</h3></div>
<div ng-show="product.responce == 'PRODINFOOFF'"><h3 class="text-center">This model disabled!</h3></div>
<img ng-repeat="img in productGallery" ng-src="{{img}}" class="hidden">