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
   if(!$(e.target).is('input') && !$(e.target).is('img')) {
		e.preventDefault();
   }
}, false);


var hostname = window.location.protocol + '//' + window.location.hostname + '/';

var getUrlVars = function (url) {
	var vars = {};
	var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}

/* APP */

var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap', 'ngAnimate', 'ngCookies', 'angularFileUpload']);

// CONFIG 
app.config(['$routeProvider', function($routeProvider) {
	
	$routeProvider
    .when('/home', {
        templateUrl : 'templates/home.php',
		controller: 'homeCtrl'
    })
	.when('/models/:catid/:page', {
        templateUrl : 'templates/models.php',
		controller: 'modelsCtrl'
    })
	.when('/model/:id', {
        templateUrl : 'templates/model.php',
		controller: 'modelCtrl'
    })
	.when('/user/:user', {
        templateUrl : 'templates/user.php',
		controller: 'userCtrl'
    })
	.when('/profile/:tab', {
        templateUrl : 'templates/profile.php',
		controller: 'profileCtrl'
    })
	.when('/favorite-collection/:id', {
        templateUrl : 'templates/favorite-collection.php',
		controller: 'favoriteCollectionCtrl'
    })
	.when('/favorite-share/:shareid', {
        templateUrl : 'templates/favorite-share.php',
		controller: 'favoriteShareCtrl'
    })
	.when('/search/:query/:catid/:global/:page', {
        templateUrl : 'templates/search.php',
		controller: 'searchCtrl'
    })
  	.when('/about/', {
        templateUrl : 'templates/about.html',
		controller: 'aboutCtrl'
    }) 	
	.otherwise({redirectTo:'/home'});
}]);

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

app.directive("modelcard", function($rootScope) {	 
    return {
        templateUrl : hostname + 'templates/model-card.html'
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

app.directive("comments", function($rootScope) {	 
    return {
        templateUrl : hostname + 'templates/comments.html'
    };
});

app.directive("lightbox", function ($rootScope) {
	return {
		templateUrl : hostname + 'templates/lightbox.html'		
    };
});

app.directive("webglplayer", function ($rootScope) {
	return {
		templateUrl : hostname + 'templates/webgl-player.html'		
    };
});

app.directive("feedback", function ($rootScope) {
	return {
		templateUrl : hostname + 'templates/feedback.html'		
    };
});

app.directive("quickfavorites", function ($rootScope) {
	return {
		templateUrl : hostname + 'templates/quick-favorites.html'		
    };
});

app.directive('iframeOnload', [function(){
return {
    scope: {
        callBack: '&iframeOnload'
    },
    link: function(scope, element, attrs){
        element.on('load', function(){
            return scope.callBack();
        })
    }
}}]);

app.directive('watchChange', function() {
    return {
        scope: {
            onchange: '&watchChange'
        },
        link: function(scope, element, attrs) {
            element.on('input', function() {
                scope.onchange();
            });
        }
    };
});

app.directive('dropFile', function() {
    return {
        restrict: 'A',
		scope: {
            data: '@dropFile'			
        },
        link: function(scope, element, attrs) {
	
			element.on('dragover', function(event) {
				
				window.external.text = "DRAG=" + scope.data;
					//event.preventDefault();
					
			});			
        }
    };
});

app.directive("keepScrollPos", function($route, $window, $timeout, $location, $anchorScroll) {

    // cache scroll position of each route's templateUrl
    var scrollPosCache = {};

    // compile function
    return function(scope, element, attrs) {

        scope.$on('$routeChangeStart', function() {
            // store scroll position for the current view
            if ($route.current) {
                scrollPosCache[$route.current.loadedTemplateUrl] = [ $window.pageXOffset, $window.pageYOffset ];
            }
        });

        scope.$on('$routeChangeSuccess', function() {
            // if hash is specified explicitly, it trumps previously stored scroll position
            if ($location.hash()) {
                $anchorScroll();

            // else get previous scroll position; if none, scroll to the top of the page
            } else {
                var prevScrollPos = scrollPosCache[$route.current.loadedTemplateUrl] || [ 0, 0 ];
                $timeout(function() {
                    $window.scrollTo(prevScrollPos[0], prevScrollPos[1]);
                }, 0);
            }
        });
    }
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

app.filter('range', function(){
	return function(n) {
		var res = [];
		for (var i = 0; i < n; i++) {
			res.push(i);
		}
		return res;
	};
});

app.filter('encode', function() {
	return function(s) {
		return btoa(s);
	}
});

app.filter('decode', function() {
	return function(s) {
		return atob(s);
	}
});

app.filter('br', function() {
	return function(s) {
		if(!s) {return '';}
		return s.split('|').join('\n').split('\\n').join('\n');
	}
});

app.filter('rmdir', function() {
	return function(s) {
		if(!s) {return '';}
		return s.split('\\').pop();
	}
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

	// SEARCH

app.controller("fastSearchCtrl", function ($scope, $rootScope, $location, $routeParams, vault) {
	$rootScope.msg = {};
	$scope.catid = $routeParams.catid;
	$scope.global = $routeParams.global;
	

	//$rootScope.globalQuery = '';
});
	
	// PROFILE

app.controller("profileCtrl", function ($scope, $rootScope, $location, $timeout, $routeParams, vault, FileUploader) {
	$rootScope.isHome = false;
	$scope.tab = $routeParams.tab;
	$scope.favtab = 1;
				
	$rootScope.addCrumb($scope.tab, '');		
		
	$scope.changeTab = function(tab) {
		$scope.tab = tab;
	}
		
	var uploader1 = $scope.uploader1 = new FileUploader({
		url: hostname + 'vault/uploadavatar.php'
	});
	
	uploader1.onAfterAddingFile = function(fileItem) {
        uploader1.uploadAll();
		//console.info('onAfterAddingFile', fileItem);
    };
	
	uploader1.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
        //console.info('onWhenAddingFileFailed', item, filter, options);
		vault.showMessage('Allowed only *.jpg files!', 'error');
	};
	
	uploader1.onCompleteItem = function(fileItem, response, status, headers) {
		//console.info('onCompleteItem', fileItem, response, status, headers);
		
		vault.getAuth();
		vault.showMessage('Avatar changed success!', 'success');
	};
	
	uploader1.onErrorItem = function(fileItem, response, status, headers) {
        //console.info('onErrorItem', fileItem, response, status, headers);
	};
			
	uploader1.filters.push({
		name: 'imageFilter',
		fn: function(item /*{File|FileLikeObject}*/, options) {
			var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
			z = '|jpg|jpeg|'.indexOf(type) !== -1;
			return z;
		}
	});
	
	$rootScope.avatar = 'img/noavatar.svg';
	
	$scope.getAvatar = function(a) {
		$rootScope.avatar = a.avatar;
	}
	
	$scope.getAvatar($rootScope.auth);
	
	$scope.clearAvatar = function() {
		vault.clearAvatar();
	}
	
	$scope.profileChangeParam = function(p, v) {
		vault.profileChangeParam(p, v);
	}
	
	$rootScope.favorites = {};
		
	
	$rootScope.favGet(1);
	vault.getAuth();
});

app.controller("quickFavCtrl", function ($scope, $rootScope, $location, $timeout, $routeParams, vault) {

	$scope.status = {};
	
		
});

app.controller("favoriteCollectionCtrl", function ($scope, $rootScope, $location, $timeout, $routeParams, vault) {
		
	$rootScope.isHome = false;
	
	$rootScope.addCrumb('Favorites', '#/profile/favorites');
	$rootScope.addCrumb('View Collection', '');
	
	$scope.collectionid = $routeParams.id;
		
	$scope.favGetCollection = function(id){
		vault.favGetCollection(id);		
	}
	
	$scope.favShareCollection = function(id, status) {
		vault.favShareCollection(id, status);
	}
	
	$scope.favGetCollection($scope.collectionid);
	
	$("input[type='text']").on("click", function () {
		$(this).select();
	});
});

app.controller("favoriteShareCtrl", function ($scope, $rootScope, $location, $timeout, $routeParams, vault) {
		
	$rootScope.isHome = false;
	
	$rootScope.addCrumb('Favorites', '#/profile/favorites');
	$rootScope.addCrumb('Shared', '');
	
	$scope.shareid = $routeParams.shareid;
		
	$scope.favGetShared = function(id){
		vault.favGetShared(id);		
	}
		
	
	$scope.favGetShared($scope.shareid);
});


app.controller("userCtrl", function ($scope, $rootScope, $location, $timeout, $routeParams, vault) {
	$rootScope.isHome = false;
				
	$rootScope.addCrumb('User Profile', '');		
	
	$scope.user = $routeParams.user;
	
	$rootScope.userProfile = {};
	
	$scope.getUserProfile = function() {
		vault.getUserProfile($scope.user);
	}
	
	$scope.getUserProfile();
		
});

	
app.controller("searchCtrl", function ($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	
	
	$rootScope.isHome = false;
	
	$scope.page = $routeParams.page;
	$scope.catid = $routeParams.catid;
	$scope.global = $routeParams.global;
	$rootScope.searchIn.cattype = $scope.catid > 0 ? 2 : 1;
	$rootScope.globalQuery = atob($routeParams.query);
	
	$rootScope.menuItemActive = {};
	
	$rootScope.breadcrumbs = [];
	$rootScope.addCrumb('Search', '#/search/');
			
	$scope.searchProducts = function(page, perpage, global, query){				
		var type = $rootScope.libType;	
		if(!$scope.catid) {$scope.catid = -1}
	
		var filter = {};
		if($rootScope.searchFilter != undefined) {filter = $rootScope.searchFilter};
		filter.cat = {'id': $scope.catid};
		filter.global = global;
			
		vault.searchProducts(type, page, perpage, query, filter);
	};
		
	$timeout(function(){
		$scope.currentPage = $scope.page;	
	}, 50);
		
	if(!$cookieStore.get('perpage-home')) {		
		$cookieStore.put('perpage-home', 24);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage-home');
	
	$scope.searchProducts($scope.page, $rootScope.perpage, $scope.global, $rootScope.globalQuery);
	
	$scope.changePage = function() {							
		$rootScope.goSearch($scope.catid, $scope.currentPage)
		//$location.path('/search/' + btoa($rootScope.globalQuery) + '/' + $scope.currentPage);		
	}
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage-home', p);
		$rootScope.perpage = p;		
		
		$scope.searchProducts($scope.page, $rootScope.perpage, $scope.global, $rootScope.globalQuery);
	}	
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
	$rootScope.searchFilter = {};
	$rootScope.searchIn.cattype = 1;
	$rootScope.searchHolder = 'Search in All Libraries...';
	$rootScope.globalQuery = '';
	
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
			angular.forEach(value.child, function (value2, key2) {				
				if(value2.name != undefined) {				
					if(!$scope.activeIndex[value2.id]){$scope.activeImage[value2.id] = value2.previews[value2.previews.length-1];}
					
					$interval(function(){
						if($scope.activeIndex[value2.id] > value2.previews.length-1 || !$scope.activeIndex[value2.id]){$scope.activeIndex[value2.id] = 0}
						
						var i = $scope.activeIndex[value2.id];
						$scope.activeImage[value2.id] = value2.previews[i];			
						$scope.activeIndex[value2.id]++;
					},  $scope.rnd(3000, 7000));					
				}
			});
		});
	});

});

app.controller("modelCtrl", function ($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.isHome = false;
	$scope.id = $routeParams.id;
	$rootScope.libType = 1;
	$rootScope.fileList = {};
	
	vault.sendCommandMXS('SHOW_DROP_TEX');
	
	$scope.getProduct = function(id, type){				
		vault.getProduct(id, type);
	};
	
	$scope.getFileList = function(id, type){
		vault.getFileList(id, type);
	}
	
	$scope.downloadItem = function(id, type, file) {
		if(!confirm('Do you really want download?')){
			return false;
		}
	
		$rootScope.download = hostname + 'vault/download.item.php?id=' + id +'&type=' + type + '&file="' + file + '"&time=' + new Date().getTime();
		console.log($rootScope.download)
		$timeout(function() {			
			$rootScope.$apply(function(){$rootScope.download = ''});
			
		}, 1000);
	}
	
	$scope.dragTimer;
	
	$(document).on('dragenter', function(e){		
		$timeout.cancel($scope.dragTimer);
		$scope.dragTimer = $timeout(function(){
			vault.showMessage('This file will be downloaded to local drive cash folder. Don\'t forget to move it to the proper place and relink.', 'warning');
		}, 150);		
	});


	$scope.imgSize = function(index) {
		s = $rootScope.fileList.files.imgsize[index];
		return s[0] + 'x' + s[1] + ' px';
	}
		
	$scope.getDim = function(dim, units) {
		if(!dim || !units) {return '';}
		var p = '';
		switch(units) {
			case 'Meters': p = 'm';
			break;
			case 'Millimeters': p = 'mm';
			break;
			case 'Kilometers': p = 'km';
			break;
			case 'Centimeters': p = 'cm';
			break;
		}
		
		return dim.split(' x ').join(p + ' x ') + p;				
	}	
		
	$scope.getProduct($scope.id, 1);
		
	$scope.tabinfo = 'desc';
	$scope.changeTabInfo = function(s) {
		$scope.tabinfo = s;		
		
		switch(s) {
			case 'files': $scope.getFileList($scope.id, 1);
			break;
		}
	}
	
	$scope.rateProduct = function(id, type) {
		vault.rateProduct(id, type);
	}
	
	$scope.favoriteProduct = function(id, type) {
		vault.favoriteProduct(id, type);
	}
		
	$rootScope.favGet($rootScope.libType);
});

app.controller("modelsCtrl", function ($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.isHome = false;
	$rootScope.globalQuery = '';
	$rootScope.libType = 1;
		
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
		$location.path('/models/' + $scope.catid + '/' + $scope.currentPage);		
	}
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage-home', p);
		$rootScope.perpage = p;		
		
		$scope.getProducts($scope.page, $rootScope.perpage, $scope.catid, $rootScope.catFilter);
	}
		
	if(!$cookieStore.get('place-mode')) {		
		$cookieStore.put('place-mode', 0);	
	};
	
	
	$scope.checkPending = function(id) {		
		vault.checkPending(id, $scope.page, $rootScope.perpage, $rootScope.catFilter, 1, $scope.catid);
	}
	
	$rootScope.favGet($rootScope.libType);
});

// AUTO RUN
app.run(function($rootScope, $location, $routeParams, $timeout, $cookieStore, vault) {
      
	vault.getGlobal();  
	  
	  
	$rootScope.oldLocation = '';  
   $rootScope.menuItemActive = [];
   $rootScope.searchFilter = {};
   $rootScope.searchIn = {};
  
	$rootScope.yesNo = function(s) {
		if(s == 0 || !s || s == undefined || s == null || s == 'No' || s == 'NO' || s == 'no' ) {return 'No';}
		return 'Yes';
	}
	
	$rootScope.isNA = function(s) {
		if(s == 0 || !s || s == undefined || s == null || s == '' || s.length == 0 || s == 'N/A') {return 'N/A';}
		return s;
	}
	
	$rootScope.tm = function(time) {
		var d = new Date(time * 1000);
		var s = d.getDate() + '.' + (d.getMonth()+1) + '.' + d.getFullYear();
		
		return s;
    }
   
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
		if(!imgs) {return '';}
		
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
	
	// PLACE 
	
	$rootScope.place = $cookieStore.get('place-mode');
	
	$rootScope.placename = '';
	$rootScope.getPlaceName = function(mode) {
		switch(mode) {
			case 1: $rootScope.placename = 'X-Ref Model';
			break;
			case 2: $rootScope.placename = 'Open Model';
			break;
			default: $rootScope.placename = 'Merge Model';
			break;
		}
	}

	$rootScope.getPlaceName($rootScope.place);
	
	$rootScope.changePlace = function(mode) {
		$rootScope.getPlaceName(mode);
		$rootScope.place = mode;
		$cookieStore.put('place-mode', mode);
	}
	
	$rootScope.placeModel = function(id) {		
		vault.placeModel(id, $rootScope.place);				
	}
	
	// DOWNLOAD
	
	$rootScope.download = '';	
	
	$rootScope.downloadUrl = function(id, type) {
		if(!confirm('Do you really want download?')){
			return false;
		}
	
		$rootScope.download = hostname + 'vault/download.php?id=' + id +'&type=' + type;
		
		$timeout(function() {			
			$rootScope.$apply(function(){$rootScope.download = ''});
			
		}, 1000);
	}
	
	$rootScope.prodError = {};
	
	$rootScope.webgl = '';
	$rootScope.webglMsg = function() {
		
	}
	
	$rootScope.webglUrl = function(item, title) {
	
		if(!item) {
			$rootScope.webgl = '';
			$rootScope.webglTitle = '';
			v = document.getElementById('webgl');
			if(v) {v.contentWindow.document.body.innerHTML='';}
			return false;
		}
		
		/*if(!confirm('Do you really want open 3D mode?')){
			return false;
		}*/
		$rootScope.webglTitle = title;
		$rootScope.webgl = hostname + 'webgl/?item=' + item;				
	}
	
	$rootScope.downloadMsg = function() {
		var t = $("#download").contents().find("body").html();		
		if(!t || !t.length) {return false;}
		console.log(t);
		var j = JSON.parse(t);	
		var v = getUrlVars($rootScope.download);
		var id = v['id'];
		var err = 0;
		switch(j.responce)
		{			
			case 'MODELBAD': err = 1;
			break;
			case 'MODELNOTEXIST': err = 1;
			break;
			case 'NORIGHTS': err = 2;
			break;
			case 'ITEMLBAD': alert('You have no access!');
			break;
			case 'ITEMNOTEXIST': alert('Sorry, this item not exist!\nContact with Administrator!');
			break;
		}
		$rootScope.$apply(function(){$rootScope.prodError[id] = err});
		
		$rootScope.download = '';		
	}
	
	// SEARCH

	$rootScope.doFastSearch = function(q) {				
		$rootScope.showResults = false;
				
		if(!q || q.length < 2) {
			$rootScope.fastSearch = {};			
			return false;
		}
		
		$rootScope.showResults = true;
		var filter = {};
		var id = $routeParams.catid ? $routeParams.catid : -1;
		filter.cat = {'id': id};
		console.log(filter)
		vault.fastSearch(q, $rootScope.libType, filter);
		
		$rootScope.bindSearchEvent();
	}
	
	$rootScope.onListEnter = function(s) {
		$rootScope.listEnter = s;
	}
	
	$(document).keydown(function(e){ 		
		
		if(e.which == 40){
			$rootScope.showResults = true;
			
			if($(".fast-result a.active").length != 0) {
				var storeTarget = $('.fast-result').find("a.active").next();
				$(".fast-result a.active").removeClass("active");
				storeTarget.addClass("active");
			}
			else {
				$('.fast-result').find("a:first").addClass("active");
			}
		}
		
		if(e.which == 38){
			if($(".fast-result a.active").length != 0) {
				var storeTarget = $('.fast-result').find("a.active").prev();
				$(".fast-result a.active").removeClass("active");
				storeTarget.addClass("active");
			}
			else {
				$('.fast-result').find("a:first").addClass("active");
			}
		}
	});
		
	$rootScope.bindSearchEvent = function() {
		// Angular not working with WebBrowser Component!
		$('#fast-search').unbind('input');
		$('#fast-search').bind('input', function(){
			var v = $(this).val();
			$rootScope.globalQuery = v;	
			$rootScope.doFastSearch(v);			
		});
				
		
		var v = $('#fast-search').val();
		$rootScope.globalQuery = v;
	}	
	
	$rootScope.showHideResults = function(show) {			
		$rootScope.showResults = show;
		if(show == false && $rootScope.listEnter) {
			$rootScope.showResults = true;
			
		} else {
			$rootScope.bindSearchEvent();
		}		
	}
	
	$rootScope.goSearch = function(catid, page) {
		$rootScope.bindSearchEvent();
				
		if(!$rootScope.globalQuery.length) return alert('Please enter search query!');
		
		if($rootScope.showResults == true) {
			if($(".fast-result a.active").length != 0) {
				var href = $('.fast-result').find("a.active").attr('href');
				$location.path(href);
				
				return false;
			}						
		}
		
		
		if(!catid) {catid = -1;}
		if(!page) {page = 1;}
		var global = 0;
		if($rootScope.searchIn.cattype == 2) {global = 1;}	
		if(global == 0) {catid = -1;}
		$location.path('/search/' + btoa($rootScope.globalQuery) + '/' + catid + '/' + global + '/' + page);
	}
	
	// FEEDBACK
	$rootScope.hideShowFeedback = function(x){		
		$rootScope.showFeedBack = x;	
	};
		
	
	$rootScope.feedBack = function(feed) {		
		vault.feedBack(feed.content, feed.subject, feed.bug);
	}
		
	// LIGHTBOX
	
	$rootScope.hideShowLightBox = function(x, img){
		$rootScope.showLightBox = x;
				
		if(!img) return false;		
		$rootScope.currItem = 0;		
		$rootScope.productGalleryPreviews = [];
		$rootScope.productGallery = [];			
		$rootScope.productGallery[0] = 'img/loading.gif';
			
		$timeout(function(){			
			$rootScope.productGallery = $rootScope.getProdImages(img, 2, false);
			$rootScope.productGalleryPreviews = $rootScope.getProdImages(img, 0, false);			
		}, 150);		
	};
	
	$rootScope.slideLightBox = function(i){
		
		var c = $rootScope.currItem;
		var l = $rootScope.productGallery.length - 1;
		
		var s = c + i;
		if(s > l) {s = 0;}
		if(s < 0) {s = l;}
		
		$rootScope.currItem = s;		
	};
	
	$rootScope.showItem = function(x){
		$rootScope.currItem = x;
	};
	
	$rootScope.keyLightBox = function(e){
		
		if(e.keyCode == 39) {			
			$rootScope.slideLightBox(1);
		}
		
		if(e.keyCode == 37) {
			$rootScope.slideLightBox(-1);
		}
		
		// ESC ACTION
		if(e.keyCode == 27) {
			$rootScope.showLightBox = false;
			$rootScope.showFeedBack = false;
			$rootScope.showQuickFavorites = false;
			$rootScope.webglUrl(null);
		}	

		$rootScope.$apply();		
	};
	
	var $doc = angular.element(document);

	$doc.on('keydown', $rootScope.keyLightBox);
	
	$rootScope.$on('$destroy',function(){
		$doc.off('keydown', $rootScope.keyLightBox);
	});
	
	// COMMENTS
	
	$rootScope.sendComment = function(id, txt, bug, item) {
		
		email = {
			'user': item.uploadedby,
			'preview': $rootScope.productGalleryMedium[0],
			'link': window.location.href,
			'name': item.name
		};
		
		vault.sendComment(id, txt, bug, $rootScope.libType, email);
	}
	
	$rootScope.delComment = function(id, prodid) {
		
		if(!confirm('Do you really want delete comment?')){
			return false;
		}
		
		vault.delComment(id, prodid, $rootScope.libType);
	}
	
	// MXS BROWSER ACTIONS
	
	$rootScope.mxsGoBack = function(){
		vault.sendCommandMXS('GOBACK');
	}

	$rootScope.mxsGoForward = function(){
		vault.sendCommandMXS('GOFORWARD');
	}

	$rootScope.mxsForceRefresh = function(){
		vault.sendCommandMXS('FORCEREFRESH');
	}	
	
	$rootScope.mxsClearIeCache = function(){
		vault.sendCommandMXS('CLEARCHACHE');
	}	
		
	$rootScope.constructionAlert = function() {
		alert('This option under construction!');
	}	
	
	$rootScope.getAuth = function() {
		vault.getAuth();
	}
	
	$rootScope.avatar = '';
	
	$rootScope.shortenName = function(n, s) {
		if(n.length > s) {
			return n.substr(0, s).trim() + '...';
		}
		
		return n;
	}
		
	// FAV
	
	$rootScope.openSharedLink = function() {
	
		var shareid = prompt('Please enter collection ID!', '');
		
		if(!shareid) {return false;}
		if(!shareid.length || shareid.match(/[^a-zA-Z0-9-_ ]/)) {
			vault.showMessage('Wrong collection ID!', 'warning');
			
			return false;
		}
		
		$location.path('/favorite-share/' + shareid);	
	}
	
	$rootScope.favDelCollectionItem = function(id, prodid, name) {
		if(!confirm('Do you really want to delete item \"' + name + '\"?')){
			return false;
		}
		
		vault.favDelCollectionItem(id, prodid);
	}
	
	$rootScope.hideShowQuickFavortites = function(x){	
		$rootScope.quickFavID = x;
		$rootScope.showQuickFavorites = x.id;	
		
		$rootScope.favGet($rootScope.libType);
	};
	
	$rootScope.favGet = function(type) {		
		vault.favGet(type);
	}	
		
	$rootScope.favNewCollection = function(type, prodid) {
		
		var name = prompt('Please enter collection name! Ex.: Cars', '');			
		
		if(!name || !name.length) {			
			vault.showMessage('Please enter the name!', 'warning');
		
			return false;
		}
				
		if(!name.length || name.match(/[^a-zA-Z0-9-_ ]/)) {
			vault.showMessage('Wrong collection name!', 'warning');
			
			return false;
		}
				
		vault.favNewCollection(name, type, prodid);
	}

	$rootScope.favDeleteCollection = function(id, name, type, getforid) {
		if(!confirm('Do you really want to delete collection \"' + name + '\"?')){
			return false;
		}
		$rootScope.lastFav = {};
		vault.favDeleteCollection(id, type, getforid);
	}
	
	$rootScope.favRenameCollection = function(id, name, type, getforid) {
		if(!confirm('Do you really want to rename collection \"' + name + '\"?')){
			return false;
		}
		
		var name = prompt('Please enter collection name! Ex.: Cars', '');
		
		if(!name || !name.length) {			
			vault.showMessage('Please enter the name!', 'warning');
		
			return false;
		}
		
		if(!name.length || name.match(/[^a-zA-Z0-9-_ ]/)) {
			vault.showMessage('Wrong collection name!', 'warning');
			
			return false;
		}
		
		vault.favRenameCollection(id, name, type, getforid);
	}	
		
	$rootScope.lastFav = {}	;
		
	// Quick Fav	
	$rootScope.favAddRemove = function(status, id, prodid, type, name){				
		$rootScope.lastFav.id = id;
		$rootScope.lastFav.name = name;
		
		if(status) {
			vault.favAddItem(id, prodid, type);
		} else {
			vault.favDelItem(id, prodid, type);
		}
	}		
	
	$rootScope.addLastFav = function(prodid) {
		
		if(!$rootScope.lastFav.id) {return false;}
		
		vault.favAddItem($rootScope.lastFav.id, prodid, $rootScope.libType);
	}
		
    $rootScope.$watch(function() { 
        return $location.path(); 
    },
     function(a){
			
		// INIT
		$rootScope.quickFavID = -1;		
		$rootScope.msg = {};						
		$rootScope.globals = {};				
		$rootScope.showOverlayMenu = false;			
		if(!$rootScope.categories) {vault.getCat();}				
		$rootScope.breadcrumbs = [];
		$rootScope.menuItem = [];				
		$rootScope.activeMenuId = [];
		$rootScope.activeMenuName = [];
		$rootScope.globalQuery = '';
		$rootScope.download = '';		
		$rootScope.fastSearch = {};	
		$rootScope.listEnter = false;
		$rootScope.showResults = false;
		$rootScope.prodError = {};		
		$rootScope.searchIn.cattype = 2;
		$rootScope.currItem = 0;
				
		$rootScope.showLightBox = false;
		$rootScope.showFeedBack = false;
		$rootScope.productGalleryPreviews = [];
		$rootScope.productGalleryMedium = [];
		$rootScope.productGallery = [];
		$rootScope.prod = {};
		$rootScope.comments = [];
		$rootScope.showQuickFavorites = false;	
				
		$rootScope.bindSearchEvent();
		
		$rootScope.webglUrl(null);
    });
});

// SERVICES

app.service('vault', function($http, $rootScope, $timeout, $interval, $templateCache, $cookieStore) {
		
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
			case 'NORIGHTS': s.error = 'You have no rights view this content!';
			break;
			case 'MODELNOTEXIST': s.error = 'Error! Model not found. Please report about this model!';
			break;
			case 'COMMENTNOTEXT': s.warning = 'Message is required!';
			break;
			case 'COMMENTNOACCESS': s.error = 'You have no access to send comment!';
			break;
			case 'COMMENTOK': s.success = 'Comment successfully added!';
			break;
			case 'COMMENTDELOK': s.success = 'Comment deleted!';
			break;
			case 'COMMENTDELBAD': s.error = 'Error deleting comment!';
			break;
			case 'FEEDBACKBAD': s.warning = 'Please fill correct all fields!';
			break;
			case 'FEEDBACKOK': s.success = 'Feedback successfully sent!';
			break;
			case 'FAVNEWCOLLECTIONOK': s.success = 'Collection created!';
			break;
			case 'FAVNEWCOLLECTIONBAD': s.error = 'Error while create collection!';
			break;
			case 'FAVNEWCOLLECTIONEXIST': s.warning = 'Collection with this name already exist!';
			break;			
			case 'FAVDELOK': s.success = 'Collection deleted!';
			break;
			case 'FAVDELBAD': s.error = 'Error while delete collection!';
			break;
			case 'FAVRENBAD': s.error = 'Error while rename collection!';
			break;
			case 'FAVRENOK': s.success = 'Success collection renamed!';
			break;
			case 'FAVADDITEMOK': s.success = 'Item added to "' + r.name + '" collection!';
			break;
			case 'FAVREMITEMOK': s.warning = 'Item removed from "' + r.name + '" collection!';
			break;
			case 'FAVNEWCOLLECTIONITEMADDED': s.success = 'Item added to new collection!';
			break;
			case 'FAVSHAREON': s.success = 'Collection shared! Now you can copy the link!';
			break;
			case 'FAVSHAREOFF': s.warning = 'Collection closed for other users!';
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
		}).then(function(r) {					
				console.log(r.data);
				if(r.data.responce == 'RESTRICTED') {					 
					$cookieStore.put('old-location', window.location);
					$rootScope.goLogin(); 
					return false;
					
				}
				return r;
			}			
		);
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
					loc = $cookieStore.get('old-location');
					if(!loc) {
						$rootScope.goHome();						
					} else {												
						window.location = loc.href;
					}
				}, 200);
			}
			
			if(m == 'RESTRICTED') {
				$rootScope.goLogin();
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
				$cookieStore.put('old-location', '');
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
			console.log(r.data);
			$rootScope.categories = r.data;
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var getHomeProd = function(type) {
		var json = {'type': type};
		
		HttpPost('HOMEGET', json).then(function(r){						
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
			$rootScope.products = r.data;									
						
			$rootScope.activeMenuId = [];
			$rootScope.activeMenuName = [];
			$rootScope.breadcrumbs = [];
						
			if(r.data.pathway) {
				angular.forEach(r.data.pathway, function(item, key) {
					$rootScope.addCrumb(item.name, ('#/models/' + item.id + '/1'));
					$rootScope.activeMenuId.push(item.id);
					$rootScope.activeMenuName.push(item.name);
					
					if(key == r.data.pathway.length - 1) {
						$rootScope.searchHolder = 'Search in ' + item.name + '...';				
						$rootScope.searchFilter['cat'] = {'id': item.id, 'name': item.name};
					}
				});				
			}
			
			$rootScope.setActiveMenu();
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var searchProducts = function(type, page, perpage, query, filter) {
			
		var json = {'page': page, 'perpage': perpage, 'filter': filter, 'query': query, 'type': type};
		console.log(json)
		HttpPost('GLOBALSEARCH', json).then(function(r){						
			$rootScope.products = r.data;									
				
			$rootScope.breadcrumbs = [];
			$rootScope.addCrumb('Search in All Libraries ', '#/search/');
				
			var c = r.data.filter.cat.name;
			if(c) {
				$rootScope.searchHolder = 'Search in ' + c + '...'
				
				$rootScope.breadcrumbs = [];
				$rootScope.addCrumb($rootScope.searchHolder, '#/search/');
			};
			
			$rootScope.searchFilter = r.data.filter;
			$rootScope.searchIn.cattype = r.data.filter.global == true ? 1 : 2;
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var sendCommandMXS = function(cmd, value) {
		if(!value) {value = '';}
		window.external.text = cmd + '=' + value + '#' + new Date().getTime();
	}
	
	var checkPending = function(id, page, perpage, filter, type, catid) {
		var json = {'id': id, 'type': type};
		
		HttpPost('CHECKPENDING', json).then(function(r){						
			
			if(r.data.responce = "PENDINGOK") {getProducts(page, perpage, catid, filter);};
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var placeModel = function(id, mode) {
		var json = {'id': id};
		
		var cmd = '';
				
		switch(mode) {
			case 1: cmd = 'XREF_MODEL';
			break;
			case 2: cmd = 'OPEN_MODEL';
			break;
			default: cmd = 'MERGE_MODEL';
			break;
		}
		
		sendCommandMXS(cmd, id)			
	}
		
	var fastSearch = function(query, type, filter)
	{		
		var json = {'query': query, 'type': type, 'filter': filter};
		
		HttpPost('FASTSEARCH', json).then(function(r){
			$rootScope.fastSearch = r.data;
		});
	};
	
	var getProduct = function(id, type)
	{		
		var json = {'id': id, 'type': type};
			
		HttpPost('PRODINFO', json).then(function(r){						
			$rootScope.prod = r.data.product;
			$rootScope.product = r.data;
			$rootScope.activeMenuId = [];
			$rootScope.activeMenuName = [];
			$rootScope.breadcrumbs = [];
			$rootScope.comments = r.data.comments;
						
			if(r.data.pathway) {
				angular.forEach(r.data.pathway, function(item, key) {
					$rootScope.addCrumb(item.name, ('#/' +  r.data.type + '/' + item.id + '/1'));
					$rootScope.activeMenuId.push(item.id);
					$rootScope.activeMenuName.push(item.name);									
				});				
			}
			if(r.data.product) {
				$rootScope.productGallery = $rootScope.getProdImages(r.data.product.previews, 2);
				$rootScope.productGalleryMedium = $rootScope.getProdImages(r.data.product.previews, 1);
				$rootScope.productGalleryPreviews = $rootScope.getProdImages(r.data.product.previews, 0);				
			}
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var getFileList = function(id, type)
	{		
		var json = {'id': id, 'type': type};
			
		HttpPost('FILESLIST', json).then(function(r){						
			$rootScope.fileList = r.data;	
			console.log(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var rateProduct = function(id, type)
	{		
		var json = {'id': id, 'type': type};
			
		HttpPost('PRODRATE', json).then(function(r){						
				
			getProduct(id, type);
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var favoriteProduct = function(id, type)
	{		
		var json = {'id': id, 'type': type};
			
		alert('This option under construction!');
			
		HttpPost('PRODFAVORITE', json).then(function(r){						
						
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};

	var feedBack = function(txt, subject, bug)
	{		
		var json = {'txt': txt, 'subject': subject, 'bug': bug};
				
		HttpPost('FEEDBACK', json).then(function(r){						
			if(r.data.responce == "FEEDBACKOK") {
				$rootScope.showFeedBack = false;
			}
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}; 

	var getComments = function(id, type)
	{		
		var json = {'id': id, 'type': type};
		deleteMessage();
				
		HttpPost('GETCOMMENTS', json).then(function(r){						
						
			$rootScope.comments = r.data;
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};	
	
	var getAuth = function()
	{		
		var json = {'type': 'user'};
						
		HttpPost('GETAUTH', json).then(function(r){									
					
			$rootScope.auth = r.data;
			$rootScope.avatar = r.data.avatar;
			$rootScope.profile = r.data;
						
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var sendComment = function(id, txt, bug, type, email)
	{		
		var json = {'id': id, 'txt': txt, 'type': type, 'bug': bug, 'email': email};
		deleteMessage();
				
		HttpPost('SENDCOMMENT', json).then(function(r){						
				console.log(r.data)		
			getComments(id, type);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var delComment = function(id, prodid, type)
	{		
		var json = {'id': id, 'type': type};
		deleteMessage();
				
		HttpPost('DELCOMMENT', json).then(function(r){						
						
			getComments(prodid, type);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var clearAvatar = function(id, prodid, type)
	{		
		var json = {'type': 'avatar'};
		deleteMessage();
				
		HttpPost('DELAVATAR', json).then(function(r){						
						
			getAuth();
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var profileChangeParam = function(p, v)
	{		
		var json = {'param': p, 'value': v};
		deleteMessage();
				
		HttpPost('PROFILECHANGEPARAM', json).then(function(r){						
						
			getAuth();
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	};
	
	var getUserProfile = function(user) {
		var json = {'user': user};
		deleteMessage();
				
		HttpPost('GETUSERPROFILE', json).then(function(r){						
		
			$rootScope.userProfile = r.data.profile;
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	// FAV
	
	var favGet = function(type) {
		var json = {'type': type};
		deleteMessage();
				
		HttpPost('FAVGET', json).then(function(r){						
		
			$rootScope.favorites = r.data
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favNewCollection = function(name, type, prodid) {
		var json = {'name': name, 'type': type, 'prodid': prodid};
		deleteMessage();
				
		HttpPost('FAVNEWCOLLECTION', json).then(function(r){						
						
			favGet(type);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favDeleteCollection = function(id, type, getforid) {
		var json = {'id': id};
		deleteMessage();
				
		HttpPost('FAVDELCOLLECTION', json).then(function(r){						
			if(getforid) {
				favGetCollection(id);
			} else
			{
				favGet(type);
			}
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favRenameCollection = function(id, name, type, getforid) {
		var json = {'id': id, 'name': name};
		deleteMessage();
				
		HttpPost('FAVRENAMECOLLECTION', json).then(function(r){						
			if(getforid) {
				favGetCollection(id);
			} else
			{
				favGet(type);
			}
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favAdd = function(id, prodid, type) {
		var json = {'id': id, 'prodid': prodid};
		deleteMessage();
				
		HttpPost('FAVADDITEM', json).then(function(r){						
			favGet(type);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favAddItem = function(id, prodid, type) {
		var json = {'id': id, 'prodid': prodid};
		deleteMessage();
				
		HttpPost('FAVADDITEM', json).then(function(r){						
			favGet(type);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favDelItem = function(id, prodid, type) {
		var json = {'id': id, 'prodid': prodid};
		deleteMessage();
				
		HttpPost('FAVDELITEM', json).then(function(r){						
			favGet(type);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
		
	
	var favGetCollection = function(id) {
		var json = {'id': id};
		deleteMessage();
				
		HttpPost('FAVGETCOLLECTION', json).then(function(r){						
						
			$rootScope.favoriteCollection = r.data;
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favDelCollectionItem = function(id, prodid) {
		var json = {'id': id, 'prodid': prodid};
		deleteMessage();
				
		HttpPost('FAVDELITEM', json).then(function(r){						
				favGetCollection(id);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favShareCollection = function(id, status) {
		var json = {'id': id, 'status': status};
		deleteMessage();
				
		HttpPost('FAVSHARECOLLECTION', json).then(function(r){											
			favGetCollection(id);
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var favGetShared = function(shareid) {
		var json = {'shareid': shareid};
		deleteMessage();
				
		HttpPost('FAVGETSHARED', json).then(function(r){											
			$rootScope.favoriteCollection = r.data;
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	return {
		responceMessage: responceMessage,
		showMessage: showMessage,
		deleteMessage: deleteMessage,
		signIn: signIn,
		signOut: signOut,				
		getGlobal: getGlobal,
		getCat: getCat,
		getHomeProd: getHomeProd,
		getProducts: getProducts,
		placeModel: placeModel,
		fastSearch: fastSearch,
		searchProducts: searchProducts,
		checkPending: checkPending,
		getProduct: getProduct,
		rateProduct: rateProduct,
		favoriteProduct: favoriteProduct,
		feedBack: feedBack,
		sendComment: sendComment,
		delComment: delComment,
		sendCommandMXS: sendCommandMXS,
		getAuth: getAuth,
		clearAvatar: clearAvatar,
		getUserProfile: getUserProfile,
		favGet: favGet,
		favAddItem: favAddItem,
		favDelItem: favDelItem,
		favNewCollection: favNewCollection,
		favDeleteCollection: favDeleteCollection,
		favRenameCollection: favRenameCollection,
		favGetCollection: favGetCollection,
		favShareCollection: favShareCollection,
		favGetShared: favGetShared,
		favDelCollectionItem: favDelCollectionItem,
		profileChangeParam: profileChangeParam,
		getFileList: getFileList
	};
});


