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
	<div ng-repeat="lib in homePreviews" ng-if="lib.type==libType"  class="home-showcase row">
		<h3><span><a href="#/cat/{{lib.id}}/1">{{lib.name}}</a></span></h3>
		<ul>
			<li ng-repeat="cat in lib" ng-if="cat.name" class="col-lg-3 col-md-3 col-sm-6 col-xs-12 img-responsive" style="position:relative"> <a href="#/cat/{{cat.id}}/1"> <img ng-src="{{cat.previews[0]}}" style="opacity: 0;"> <img ng-animate-swap="activeImage[cat.id]" ng-src="{{activeImage[cat.id]}}" class="swap-animation">
				<dl>
					{{cat.name}}
				</dl>
				</a> </li>
		</ul>
	</div>
</div>
