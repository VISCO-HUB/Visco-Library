<div ng-show="product.product.name.length">
	<div class="col-md-12 col-lg-8 col-sm-12 col-xs-12">
	<div class="prod-gallery-frame">
		<img src="img/loading.gif" ng-src="{{productGallery[currItem]}}" ng-class="{'show': !showLightBox}" class="img-responsive margin-auto pointer" ng-click="hideShowLightBox(true, prod.previews)" fade-in>
	</div>
	<br>
		<ul class="prod-gallery" ng-show="productGalleryPreviews.length > 1 && !showLightBox">
			<li ng-repeat="item in productGalleryPreviews" ng-style="{'background-image': 'url(' + item + ')'}" ng-click="showItem($index)" ng-class="{'active': $index == currItem}"></li>
		</ul>
		<br>
		<ul class="nav nav-tabs">
			  <li ng-class="{'active': tabinfo=='desc'}"><a href="" ng-click="changeTabInfo('desc')">Description</a></li>
			  <li ng-class="{'active': tabinfo=='comments'}"><a href="" ng-click="changeTabInfo('comments')">Comments ({{comments.length}})</a></li>
		</ul>
		<br>
		<div ng-show="tabinfo=='desc'">
			<pre ng-show="prod.overview" class="first-capitalize decription">{{prod.overview.split('|').join('\n')}}</pre>
			<div ng-show="!prod.overview" class="text-muted text-center">No description...</div>
		</div>
		<div ng-show="tabinfo=='comments'">						
			<div comments></div>			
		</div>
	</div>
	<div class="col-md-12 col-lg-4 col-sm-12 col-xs-12 fontsize-12">
		<h3 class="capitalize">{{prod.name}}</h3>
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-rating" ng-class="{'highlight': product.userrate}" uib-tooltip="Rate model" ng-click="rateProduct(prod.id, libType)"> &nbsp;&nbsp;</button>&nbsp;
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-heart" uib-tooltip="Add to favorite" ng-click="hideShowQuickFavortites(prod)"> &nbsp;&nbsp;</button>&nbsp;
		<button type="button" class="btn btn-default custom-button-gray button-fixed button-download" uib-tooltip="Download model" ng-click="downloadUrl(prod.id, libType)" ng-class="{'disabled': auth.rights < 0}"> &nbsp;&nbsp;</button>&nbsp;
		<a ng-href="admin/#/models-edit/{{prod.id}}/1" class="btn btn-default custom-button-gray button-fixed button-edit" uib-tooltip="Edit" ng-show="auth.rights > 0"> &nbsp;&nbsp;</a>&nbsp;
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
			<div class="capitalize"><strong>Date:</strong> {{tm(prod.date)}}</div>
			<div class="capitalize"><strong>Downloads:</strong> {{prod.downloads}}</div>
			<div class="capitalize"><strong>Rating:</strong> {{product.rating}}</div>
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
			<div><strong>Dimension: </strong>{{prod.dim}}</div>
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
			<div><strong>Tags:</strong> {{prod.tags}}</div>
		</div>
		<hr>
		<button type="button" ng-click="downloadUrl(prod.id, libType)" class="btn btn-block margin-0" ng-show="auth.browser!='MXS'" ng-class="{'disabled': auth.rights < 0, 'btn-danger': prodError[prod.id], 'btn-primary': !prodError[prod.id]}">
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
<div ng-show="!product.product.name.length && !product.responce"><h3 class="text-center">Model not found!</h3></div>
<div ng-show="product.responce == 'PRODINFONOACCESS'"><h3 class="text-center">You have no access to view this content!</h3></div>
<div ng-show="product.responce == 'PRODINFOOFF'"><h3 class="text-center">This model disabled!M</h3></div>
<img ng-repeat="img in productGallery" src="{{img}}" class="hidden">