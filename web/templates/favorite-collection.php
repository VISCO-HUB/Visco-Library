<div ng-show="favoriteCollection.type">
	<h3>{{favoriteCollection.name}}</h3>
	<hr>
		<a href="" class="inline-block" ng-click="favDeleteCollection(favoriteCollection.id, favoriteCollection.name, favoriteCollection.type, true)"><span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span> Delete Collection</a> &nbsp;&nbsp;&nbsp;
		<a href="" class="inline-block" ng-click="favRenameCollection(favoriteCollection.id, favoriteCollection.name, favoriteCollection.type, true)"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Rename Collection</a>
		 &nbsp;&nbsp;&nbsp;
<a href="" class="inline-block" ng-click="openSharedLink()"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> Open Shared Link</a>
	<hr>
	<span>Share collection link:</span>
	<div class="input-group col-lg-4 col-xlg-3 col-md-5 col-sm-7 fav-share">		 
		<input type="text" class="form-control collectionid" ng-class="{'favorite-share': favoriteCollection.shared==1}" ng-model="favoriteCollection.shareid" readonly ng-disabled="favoriteCollection.shared!=1">
		<span class="input-group-btn">
        	<button class="btn btn-default width-60px" ng-class="{'btn-success': favoriteCollection.shared==1}" type="button" ng-click="favShareCollection(favoriteCollection.id, 1)"> ON </button>
			<button class="btn btn-default width-60px"  ng-class="{'btn-danger': favoriteCollection.shared!=1}" type="button" ng-click="favShareCollection(favoriteCollection.id, 0)"> OFF </button>
		</span>
	</div>
	<hr>
	<div class="row"  ng-show="favoriteCollection.products.length">
		<div ng-repeat="prod in favoriteCollection.products" class="col-xxlg-2 col-xlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-6 col-xxs-12 text-center flex">
			<div modelcard></div>
		</div>
	</div>
	<h3 class="text-center" ng-show="!favoriteCollection.products.length">Collection is empty...</h3>
</div>
<div ng-show="!favoriteCollection.type"><h3 class="text-center">Collection not found!</h3></div>
