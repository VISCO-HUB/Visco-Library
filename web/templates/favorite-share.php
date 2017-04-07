<div ng-show="favoriteCollection.type">
	<h3>{{favoriteCollection.name}}</h3>	
	<span>Shared by <a ng-href="/#user/{{favoriteCollection.user}}">{{favoriteCollection.user}}</a></span>
	<hr>
	<div class="row"  ng-show="count(favoriteCollection.products)">
		<div ng-repeat="prod in favoriteCollection.products" class="col-xxlg-2 col-xlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-6 col-xxs-12 text-center flex">
			<div modelcard></div>
		</div>
	</div>
	<h3 class="text-center" ng-show="!count(favoriteCollection.products)">Collection is empty...</h3>
</div>
<div ng-show="favoriteCollection.responce=='FAVGETSHAREBAD'"><h3 class="text-center">Shared collection not found!</h3></div>
<div ng-show="favoriteCollection.responce=='FAVGETSHAREOFF'"><h3 class="text-center">Shared collection "{{favoriteCollection.name}}" is closed!<br>Contact with user {{favoriteCollection.user}} to access this collection!</h3></div>
