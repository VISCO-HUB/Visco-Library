<div class="hidden-xs">
	<div class="row">
		<ul class="nav nav-tabs">
			<li ng-class="{active: libType==1}" class="offset-center"><a href="" ng-click="libType=1">Models</a></li>
			<li ng-class="{active: libType==2}" class="offset-center"><a href="" ng-click="libType=2">Textures</a></li>
		</ul>
		<br>
		<br>
		<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 col-xlg-4 col-xlg-offset-4 padding-0" search></div>
	</div>
	<div ng-repeat="lib in homePreviews | orderObjectBy:'sort'" ng-if="lib.type==libType"  class="home-showcase row">
		<h3><span><a href="#/models/{{lib.id}}/1">{{lib.name}}</a></span></h3>
		<ul>
			<li ng-repeat="cat in lib.child" ng-if="cat.name && $index < 6" ng-class="{'hidden-sm hidden-md': $index > 2, 'hidden-lg': $index > 3}" class="col-xlg-2 col-lg-3 col-md-4 col-sm-4 col-xs-12 visible-xlg img-responsive" style="position:relative"> <a href="#/models/{{cat.id}}/1"> <img ng-src="{{cat.previews[0]}}" style="opacity: 0;"> <img ng-animate-swap="activeImage[cat.id]" ng-src="{{activeImage[cat.id]}}" class="swap-animation">
				<dl date="{{cat.sort}}">
					{{cat.name}}
				</dl>
				</a> 
				</li>
		</ul>
	</div>
</div>
