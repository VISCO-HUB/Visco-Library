<div ng-show="!isHome" class="row">
	<div class="col-xs-12 col-xxs-12 col-lg-8 col-lg-offset-2 col col-md-12 col-xlg-6 col-xlg-offset-3 col-xxlg-4 col-xxlg-offset-4 padding-0" search></div>
</div>
<br>
<div class="row" ng-show="products.products.length">
	<div ng-repeat="prod in products.products" class="col-xxlg-2 col-xlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-6 col-xxs-4 text-center">
		<div modelcard></div>
	</div>
</div>

<h3 ng-show="!products.products.length" class="text-center">Category is empty...</h3>
<div pagination></div>
