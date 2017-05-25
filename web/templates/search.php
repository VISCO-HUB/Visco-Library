<div ng-show="!isHome" class="row">
	<div class="col-xs-12 col-xxs-12 col-lg-8 col-lg-offset-2 col col-md-12 col-xlg-6 col-xlg-offset-3 col-xxlg-4 col-xxlg-offset-4 padding-0" search></div>
</div>
<br>
<div class="row">
	<div ng-repeat="prod in products.result" class="col-xxlg-2 col-xlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-6 col-xxs-12 text-center flex">
		<div modelcard></div>
	</div>
</div>
<h3 ng-show="!products.result.length && products.currpage" class="text-center">Sorry. Nothing found{{searchFilter.cat.name.length ? ' in ' + searchFilter.cat.name : '...'}}</h3>
<h3 ng-show="!products.currpage" class="text-center">Searching...</h3>
<div pagination></div>

