/*
	VISCO ASSETS CMS
	By Vasily Lukyanenko
	2017

*/

/* GLOBAL FUNCTIONS */

Array.prototype.makeUnique = function(){
   var u = {}, a = [];
   for(var i = 0, l = this.length; i < l; ++i){
      if(u.hasOwnProperty(this[i])) {
         continue;
      }
      a.push(this[i]);
      u[this[i]] = 1;
   }
   return a;
}

document.addEventListener("contextmenu", function(e){
   e.preventDefault();
}, false);

var hostname = 'http://' + window.location.hostname + '/';

/* APP */

var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap', 'ngAnimate', 'ngCookies']);


// CONFIG 
app.config(function($routeProvider) {    
	
	$routeProvider
    .when('/home', {
        templateUrl : 'templates/home.php',
		controller: 'homeCtrl'
    })
	 .when('/cat/:catid/:page', {
        templateUrl : 'templates/cat.php',
		controller: 'catCtrl'
    })
  	.when('/about/', {
        templateUrl : "templates/about.html",
		controller: 'aboutCtrl'
    }) 	
	.otherwise({redirectTo:'/home'});
});

// DIRECTIVES
app.directive("alerts", function($rootScope) {	 
    return {
        templateUrl : hostname + 'templates/alert.html'
    };
});

app.directive("search", function($rootScope) {	 
    return {
        templateUrl : hostname + 'templates/search.html'
    };
});

app.directive("menu", function($rootScope) {	 
    return {
        templateUrl : hostname + 'templates/menu.html'
    };
});

app.directive("pagination", function($rootScope) {	 
    return {
        templateUrl : hostname + 'templates/pagination.html'
    };
});

app.directive("preview", function($rootScope) {	 
    return {
        templateUrl : hostname + 'templates/preview.html'
    };
});

// FILTERS

app.filter('orderObjectBy', function() {
  return function(items, field, reverse) {
    var filtered = [];
    angular.forEach(items, function(item) {
      filtered.push(item);
    });
    filtered.sort(function (a, b) {
      return (a[field] > b[field] ? 1 : -1);
    });
    if(reverse) filtered.reverse();
    return filtered;
  };
});


// CONTROLLERS
	// ABOUT
app.controller("aboutCtrl", function($scope){
	
});

	// MAIN MENU
app.controller("menuCtrl", function($scope, $rootScope, vault){
	$scope.getCat = function() {
		vault.getCat();
	}
	
	$rootScope.libType = 1;
	$scope.getCat();
	
	$scope.changeTab = function(t) {$scope.type = t;}	

	$scope.showHideMenu = function(id, show) {
		if(angular.isUndefined($rootScope.menuItemActive)) {$rootScope.menuItemActive = [];}
		if(angular.isUndefined($rootScope.menuItemShow)) {$rootScope.menuItemShow = [];}
		
		$rootScope.menuItemShow[id] = !$rootScope.menuItemShow[id];		
		
		if($rootScope.menuItemActive[id]) {$rootScope.menuItemShow[id] = show;}	
		$rootScope.menuItemActive[id] = false;
	}
});

	// MSG
app.controller("msgCtrl", function ($scope, $rootScope, vault) {
	$rootScope.msg = {};
});

app.controller("searchCtrl", function ($scope, $rootScope, vault) {
	$rootScope.msg = {};
});

	// LOGIN
app.controller("loginCtrl", function($scope, $rootScope, vault){
	$scope.badUserName = false;
	$scope.badUserPassword = false;
	
	$scope.userName = '';
	$scope.userPassword = '';
	
	vault.showMessage('Please enter your credentials!', 'warning');
	$scope.sendMessage = function(m, t) {vault.showMessage(m, t)}
	
	$scope.signIn = function() {
		$scope.badUserName = $scope.userName == '';
		$scope.badUserPassword = $scope.userPassword == '';
		
		if(!$scope.badUserName && !$scope.badUserPassword) {
			var u = $scope.userName;
			var p = $scope.userPassword;
			
			vault.signIn(u, p);
		}
		else {
			vault.showMessage('Please enter correct your credentials!', 'error');
		}
	}
});

	// HOME
app.controller("homeCtrl", function ($scope, $rootScope, vault, $interval) {
	$rootScope.isHome = true;
	$scope.getHomeProd = function(type, id){
		vault.getHomeProd(type);
	}
	
	$scope.activeImage = {}
	$scope.activeIndex = {}
	
	vault.getHomeProd($rootScope.libType);
	
	$scope.rnd = function(min, max) {
		return Math.floor(Math.random() * (max - min) ) + min;
	}
	
	
	$scope.$watchCollection('homePreviews', function () {
		var v = $rootScope.homePreviews;
		if(!v) {return false;}
		
		angular.forEach(v, function (value, key) {					
			angular.forEach(value, function (value2, key2) {				
				if(value2.name != undefined) {				
					if(!$scope.activeIndex[value2.id]){$scope.activeImage[value2.id] = value2.previews[0];}
					
					$interval(function(){
						if($scope.activeIndex[value2.id] > value2.previews.length-1 || !$scope.activeIndex[value2.id]){$scope.activeIndex[value2.id] = 0}
						
						var i = $scope.activeIndex[value2.id];
						$scope.activeImage[value2.id] = value2.previews[i];			
						$scope.activeIndex[value2.id]++;
					},  $scope.rnd(3800, 6500));
				}
			});
		});
	});
	

});

app.controller("catCtrl", function ($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.isHome = false;
	
	$scope.page = $routeParams.page;
	$scope.catid = $routeParams.catid;
			
	$scope.getProducts = function(page, perpage, id, filter){				
		vault.getProducts(page, perpage, id, filter);
	};
	
	
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
		
	if(!$cookieStore.get('perpage-home')) {		
		$cookieStore.put('perpage-home', 24);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage-home');
	
	$scope.getProducts($scope.page, $rootScope.perpage, $scope.catid, $rootScope.catFilter);
	
	$scope.changePage = function() {							
		$location.path('/cat/' + $scope.catid + '/' + $scope.currentPage);		
	}
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage-home', p);
		$rootScope.perpage = p;		
		
		$scope.getProducts($scope.page, $rootScope.perpage, $scope.catid, $rootScope.catFilter);
	}
});

// AUTO RUN
app.run(function($rootScope, $location, $routeParams, vault) {
   
   $rootScope.menuItemActive = [];
   
   $rootScope.goLogin = function() {
		window.location = hostname + 'login/';
	}
	
	$rootScope.goHome = function() {
		window.location = hostname;			
	}
	
	$rootScope.setAuth = function(u) {
		$rootScope.auth = u;
	}

	$rootScope.deleteMsg = function() {$rootScope.msg = {};};
	
	$rootScope.singOut = function() {
		if(!confirm('Do you really want to sign out?')){
			return false;
		}
		vault.signOut()
	};
	
	$rootScope.getProdImages = function(imgs, size, main) {
		var out = [];
		var a = imgs.split(';');
		var sizes = ['60x60', '200x200', '600x600'];
		
		if(!size) {size = 0}
		
		
		angular.forEach(a, function(item){
			out.push('images/' + item + '_' + sizes[size] + '.jpg');
		});
		
		if(main) return out[0];
		return out;
	}
	
	$rootScope.count = function(o) {
		var count = 0;
		
		if(!o) {return 0;}
		for(var prop in o) {
			if(o.hasOwnProperty(prop))
				count = count + 1;
		}
		return count;
	}
	
	$rootScope.toggleOverlayMenu = function(){
		$rootScope.showOverlayMenu = !$rootScope.showOverlayMenu;
	}
	
	$rootScope.isActiveMenu = function(id) {
		if($rootScope.activeMenuId && $rootScope.activeMenuId.indexOf(id) != -1) {
			return true;
		}
		return false;
	}
	
	$rootScope.setActiveMenu = function() {
		angular.forEach($rootScope.categories, function(item, key){				
			var a = $rootScope.isActiveMenu(item.id);				
			$rootScope.menuItemActive[item.id] = a;
			angular.forEach(item.child, function(item2, key2){
				var b = $rootScope.isActiveMenu(item2.id);
				$rootScope.menuItemActive[item2.id] = b;
				
				angular.forEach(item2.child, function(item3, key3){
					var c = $rootScope.isActiveMenu(item3.id);
					$rootScope.menuItemActive[item3.id] = c;
				});
			});
		});			
	}
	
	$rootScope.addCrumb = function(name, url) {
		$rootScope.breadcrumbs.push({'url' : url, 'name': name });
	}
	
	$rootScope.bigPreview = '';
	$rootScope.bigPreviewPos = {'x': 0, 'y': 0};
	
	$rootScope.showBigPreview = function($event, show, previews) {
		$rootScope.bigPreview = '';
		if(show == true) {
			$rootScope.bigPreview = $rootScope.getProdImages(previews, 2, true);
		}		
	}
	
	
   $rootScope.$watch(function() { 
        return $location.path(); 
    },
     function(a){
			
		// INIT
				
		$rootScope.msg = {};
						
		$rootScope.globals = {};
				
		$rootScope.showOverlayMenu = false;
			
		if(!$rootScope.categories) {vault.getCat();}
				
		$rootScope.breadcrumbs = [];
		$rootScope.menuItem = [];				
		$rootScope.activeMenuId = [];
    });
});

// SERVICES

app.service('vault', function($http, $rootScope, $timeout, $interval, $templateCache) {
		
	var showMessage = function(m, t) {
		$rootScope.msg = {};
		$rootScope.msg[t] = m;
	}
	
	var deleteMessage = function() {
		$rootScope.msg = {};
	}
	
	var responceMessage = function(r) {
		if(!r.responce) return false;
		
		$rootScope.msg = {};
		var s = {};
		switch(r.responce)
		{			
			case 'USERBAD': s.warning = 'Please enter correct credentials!';
			break;
			case 'USEROK': s.success = 'Logged success!';
			break;
			case 'SIGNEDOUT': s.success = 'You are signed out!';
			break;
			case 'ERROR': s.error = 'MySQL Error!';
			break;
			case 'RESTRICTED': s.error = 'This user is not allowed!';
			break;
			case 'NORIGHTS': s.error = 'You have no rights for make changes!';
			break;
		}
		
		$rootScope.msg = s;
	}
	
	// SIMPLIFY POST PROCEDURE
	var HttpPost = function(query, json) {		
		return $http({
			url: hostname + 'vault/handle.php?query=' + query + '&time=' + new Date().getTime(),
			method: "POST",
			data: json
		});
	}
	
	var httpGet = function(query) {		
		return $http.get(hostname + 'vault/handle.php?query=' + query + '&time=' + new Date().getTime());
	}
	
	var signIn = function(u, p) {
		var json = {'user': u, 'pwd': p};
		
		$templateCache.removeAll();
		HttpPost('SIGNIN', json).then(function(r){						
			var m = r.data.responce;
			
			if(m == 'USEROK') {
				$timeout(function(){							
					$rootScope.goHome();
				}, 200);
			}

			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});					
	}
	
	var signOut = function() {
		$templateCache.removeAll();
		
		httpGet('SIGNOUT').then(function(r){						
			responceMessage(r.data);
						
			$timeout(function(){
				window.location = hostname + 'login/';
			}, 200);			
		},
		function(r){
			responceMessage(r);
		});
	}
			
	var getGlobal = function() {
		httpGet('GLOBALGET').then(function(r){									
			$rootScope.globals = r.data;
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var getCat = function() {
		httpGet('CATGET').then(function(r){									
			$rootScope.categories = r.data;
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var getHomeProd = function(type) {
		var json = {'type': type};
		
		HttpPost('HOMEGET', json).then(function(r){						
			console.log(r.data);
			$rootScope.homePreviews = r.data;
						
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});	
	}
	
	var getProducts = function(page, perpage, id, filter) {
			
		var json = {'page': page, 'perpage': perpage, 'filter': filter, 'id': id};
		
		HttpPost('PRODGET', json).then(function(r){						
			console.log(r.data)
			$rootScope.products = r.data;									
						
			$rootScope.activeMenuId = [];
			$rootScope.breadcrumbs = [];
						
			if(r.data.pathway) {
				angular.forEach(r.data.pathway, function(item) {
					$rootScope.addCrumb(item.name, ('#/cat/' + item.id + '/1'));
					$rootScope.activeMenuId.push(item.id);
				});				
			}
			
			$rootScope.setActiveMenu();
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	return {
		showMessage: showMessage,
		deleteMessage: deleteMessage,
		signIn: signIn,
		signOut: signOut,				
		getGlobal: getGlobal,
		getCat: getCat,
		getHomeProd: getHomeProd,
		getProducts: getProducts
	};
});


