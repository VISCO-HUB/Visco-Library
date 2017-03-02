<div ng-show="product.product">
	<div class="col-md-12 col-lg-8 col-sm-12 col-xs-12">
	<div class="prod-gallery-frame"><img src="img/loading.gif" ng-src="{{productGallery[currItem]}}" class="img-responsive margin-auto" fade-in></div>
	<br>
		<ul class="prod-gallery" ng-show="productGalleryPreviews.length > 1">
			<li ng-repeat="item in productGalleryPreviews" ng-style="{'background-image': 'url(' + item + ')'}" ng-click="showItem($index)" ng-class="{active: $index == currItem}"></li>
		</ul>
		<br>
		<ul class="nav nav-tabs">
			  <li ng-class="{'active': tabinfo=='desc'}"><a href="" ng-click="changeTabInfo('desc')">Description</a></li>
			  <li ng-class="{'active': tabinfo=='comments'}"><a href="" ng-click="changeTabInfo('comments')">Comments (0)</a></li>
		</ul>
		<br>
		<div ng-show="tabinfo=='desc'">
			<span ng-show="product.product.overview" class="first-capitalize">{{product.product.overview}}</span>
			<div ng-show="!product.product.overview" class="text-muted text-center">No description...</div>
		</div>
		<div ng-show="tabinfo=='comments'">
			{{product.comments}}
			<div ng-show="!product.comments" class="text-muted text-center">No comments...</div>
		</div>
	</div>
	<div class="col-md-12 col-lg-4 col-sm-12 col-xs-12 fontsize-12">
		<h3 class="capitalize">{{product.product.name}}</h3>
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-rating" uib-tooltip="Rate this item"> &nbsp;&nbsp;</button>&nbsp;
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-heart" uib-tooltip="Add to favorite"> &nbsp;&nbsp;</button>&nbsp;
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-download" uib-tooltip="Download item"> &nbsp;&nbsp;</button>&nbsp;
		<hr>
		<ul class="nav nav-tabs nav-justified">
			  <li ng-class="{'active': tabinfo2 =='info'}"><a href="" ng-click="changeTabInfo2('info')">Info</a></li>
			  <li ng-class="{'active': tabinfo2 =='files'}"><a href="" ng-click="changeTabInfo2('files')">Files</a></li>
		</ul>
		<br>
		<div ng-show="tabinfo2=='files'">
			<div class="text-muted text-center">This section under construction...</div>
		</div>
		<div ng-show="tabinfo2=='info'" class="text-muted">
			<div class="capitalize"><strong>Date:</strong> {{tm(product.product.date)}}</div>
			<div class="capitalize"><strong>Downloads:</strong> {{product.product.downloads}}</div>
			<div class="capitalize"><strong>Rating:</strong> {{product.product.rating.split(';').length - 1}}</div>
			<br>
			<div class="capitalize"><strong>Manufacturer:</strong> {{isNA(product.product.manufacturer)}}</div>		
			<div class="capitalize"><strong>Project:</strong> {{isNA(product.product.project)}}</div>
			<div class="capitalize"><strong>Client:</strong> {{isNA(product.product.client)}}</div>		
			<div><strong>Modeller:</strong> {{isNA(product.product.modeller)}}</div>		
			<div class="capitalize" ng-show="product.product.custom1"><strong>Custom1:</strong> {{isNA(product.product.custom1)}}</div>
			<div><strong>Uploaded By:</strong> {{isNA(product.product.uploadedby)}}</div>
	
			<hr>
			<div><strong>Polys:</strong> {{product.product.polys}}</div>
			<div><strong>Renderer:</strong> {{product.product.render}}</div>
			<div><strong>Dimension: </strong>{{product.product.dim}}</div>
			<div><strong>Units:</strong> {{product.product.units}}</div>
			<div><strong>Format:</strong> {{product.product.format}}</div>
			<br>
			<div class="capitalize"><strong>Unwrapped:</strong> {{yesNo(product.product.unwrap)}}</div>
			<div class="capitalize"><strong>Lights:</strong> {{yesNo(product.product.lights)}}</div>
			<div class="capitalize"><strong>Baked Textures:</strong> {{yesNo(product.product.baked)}}</div>
			<div class="capitalize"><strong>Rigged:</strong> {{yesNo(product.product.rigged)}}</div>
			<div class="capitalize"><strong>Game Engine Ready:</strong> {{yesNo(product.product.gameengine)}}</div>
			<div class="capitalize"><strong>Lods:</strong> {{yesNo(product.product.lods)}}</div>
			<br>
			<div><strong>Tags:</strong> {{product.product.tags}}</div>
		</div>
		<hr>
		<table class="dropup margin-0" tooltip-placement="right" ng-show="auth.browser=='MXS'" width="100%">
			<tr>
			<td>
				<button type="button" class="btn btn-primary btn-block" ng-click="placeModel(product.product.id)"> 
					{{placename}} 
				</button>
			</td>
			<td width="30">
			<button type="button" class="btn btn-primary dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu width-100" aria-labelledby="dropdownMenu1">										
				<li><a href="" ng-click="changePlace(2)" >Open Model</a></li>
				<li><a href="" ng-click="changePlace(1)" >X-Ref Model</a></li>
				<li><a href="" ng-click="changePlace(0)" >Merge Model</a></li>
			</ul>
			</td>
			</tr>
		</table>
		
	</div>
</div>

<div ng-show="product.responce == 'PRODINFONOACCESS'">You have no access to view this content!</div>
<div ng-show="product.responce == 'PRODINFOOFF'"> This model disabled!</div>
<img ng-repeat="img in productGallery" ng-src="{{img}}" class="hidden">