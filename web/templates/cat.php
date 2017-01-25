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
				<button type="button" class="btn btn-default custom-button-gray button-fixed button-zoom" ng-click="showBigPreview($event, true, prod.previews)"> &nbsp;&nbsp;</button>
				<a href="admin/#/models-edit/{{prod.id}}/1" class="btn btn-default btn-danger button-fixed button-admin-edit" uib-tooltip="Edit" ng-show="auth.rights>=1"> &nbsp;&nbsp;</a>
			</div>
			<div class="text-center">{{prod.name}}</div>
			<div class="card-hidden visible-xxs">
				<div class="text-left"><small><strong>Render</strong>: {{prod.render}}</small></div>	
				<div class="text-left margin-bottom-5"><small><strong>Format:</strong> {{prod.format}}</small></div>
				<table width="100%" border="0">			
						<tr>
							<td>

								
								<div class="btn-group dropup btn-block margin-0" tooltip-placement="right" ng-show="auth.browser=='MXS'">
									<button type="button" class="btn btn-primary" style="width: 80.3%"  ng-click="placeModel(prod.id)">{{place ? 'X-Ref Model' : 'Merge Model'}}</button>
									<button type="button" class="btn btn-primary dropdown-toggle width-20" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								  </button>
									<ul class="dropdown-menu width-100" aria-labelledby="dropdownMenu1">
										<li><a href="" ng-click="changePlace(0)" >Merge Model</a></li>        			
										<li><a href="" ng-click="changePlace(1)" >X-Ref Model</a></li> 
									</ul>
							</div>
																
								<button type="button" class="btn btn-primary btn-block margin-0" ng-show="auth.browser!='MXS'" ng-class="{'disabled': auth.rights < 0}" ng-click="placeModel(prod.id)">Download</button>
							</td>
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
