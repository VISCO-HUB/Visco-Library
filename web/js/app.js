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

var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap']);


// CONFIG 
app.config(function($routeProvider) {    
	
	$routeProvider
    .when('/home', {
        templateUrl : 'templates/home.php',
		controller: 'homeCtrl'
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
	
	$scope.type = 1;
	$scope.getCat();
	
	$scope.changeTab = function(t) {$scope.type = t;}	
});

	// MSG
app.controller("msgCtrl", function ($scope, $rootScope, vault) {
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
app.controller("homeCtrl", function ($scope, vault) {
	
});

// AUTO RUN
app.run(function($rootScope, $location, $routeParams, vault) {
    $rootScope.$watch(function() { 
        return $location.path(); 
    },
     function(a){
			
		// INIT
		$rootScope.goLogin = function() {
			window.location = hostname + 'login/';
		}
		
		$rootScope.goHome = function() {
			window.location = hostname;			
		}
		
		$rootScope.setAuth = function(u) {
			$rootScope.auth = u;
		}
		
		$rootScope.msg = {};
		
		$rootScope.deleteMsg = function() {$rootScope.msg = {};};
		
		$rootScope.singOut = function() {
			if(!confirm('Do you really want to sign out?')){
				return false;
			}
			vault.signOut()
		};
				
		$rootScope.globals = {};
		
		$rootScope.count = function(o) {
			var count = 0;
			
			if(!o) {return 0;}
			for(var prop in o) {
				if(o.hasOwnProperty(prop))
					count = count + 1;
			}
			return count;
		}
		
		vault.getCat();
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
				
	return {
		showMessage: showMessage,
		deleteMessage: deleteMessage,
		signIn: signIn,
		signOut: signOut,				
		getGlobal: getGlobal,
		getCat: getCat
	};
});


