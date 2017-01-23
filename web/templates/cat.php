<div ng-show="!isHome" class="row">
	<div class="col-xs-12 col-xxs-12 col-lg-8 col-lg-offset-2 col col-md-12 col-xlg-4 col-xlg-offset-4 col-xxlg-4 col-xxlg-offset-4 padding-0" search></div>
</div>
<br>
<div class="row">
<div ng-repeat="prod in products.products" class="col-xxlg-2 col-xlg-3 col-lg-4 col-md-6 col-sm-6 col-xs-6 col-xxs-12 text-center flex">
	<div class="card col-xxs-12 col-xs-12 resp">
		<div class="card-content col-xxs-12 col-xs-12 disable-hover-xxs">
			<div class="text-center margin-bottom-5 relative"> 
				<a href="#prod/{{prod.id}}"><img ng-src="{{getProdImages(prod.previews, 1, true)}}" class="text-center col-xxs-12"></a>
				<button type="button" class="btn btn-default custom-button-gray button-fixed button-zoom" ng-click="showBigPreview($event, true, prod.previews)"> &nbsp;&nbsp;
						
				</button>
			</div>
			<div class="text-center">{{prod.name}}</div>
			<div class="card-hidden visible-xxs">
				<div class="text-left"><small><strong>Render</strong>: {{prod.render}}</small></div>	
				<div class="text-left margin-bottom-5"><small><strong>Format:</strong> {{prod.format}}</small></div>
				<table width="100%" border="0">			
						<tr>
							<td><button type="button" class="btn btn-primary btn-block">Place to scene</button></td>
							<td width="34px">
								<button type="button" class="btn btn-default custom-button-gray btn-block button-fixed button-heart">&nbsp;</button>
							</td>
						</tr>
				</table>								
			</div>
		</div>
	</div>
</div>
</div>

<div pagination></div>
