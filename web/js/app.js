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

/* APP */

var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap']);


// CONFIG 
app.config(function($routeProvider) {    
	
	$routeProvider
    .when('/home', {
        templateUrl : 'templates/home.php',
		controller: 'homeCtrl'
    })
    .when('/admin', {
        templateUrl : "templates/admin.php",
		controller: 'adminCtrl'
    })
	.when('/login', {
        templateUrl : "templates/login.php",
		controller: 'loginCtrl'
    })
	.when('/about/', {
        templateUrl : "templates/about.html",
		controller: 'aboutCtrl'
    }) 	
	.otherwise({redirectTo:'/home'});
});


// CONTROLLERS
	// ABOUT
app.controller("aboutCtrl", function($scope){
	
});

	// LOGIN
app.controller("loginCtrl", function($scope, $rootScope, vault){
	$scope.badUserName = false;
	$scope.badUserPassword = false;
	
	$scope.userName = '';
	$scope.userPassword = '';

	vault.showMessage('Please enter your credentials!', 'warning');
	
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

	// ADMIN
app.controller("adminCtrl", function($scope, $rootScope, vault){
	$scope.adminSection = 'cat';
	
	vault.getGlobal();
	vault.catGet();
		
	$scope.adminAddCat = function() {		
		var libName = prompt('Please enter library name!', '');			
		
		if(!libName || !libName.length) {			
			vault.showMessage('Please enter library name!', 'warning');
		
			return false;
		}
		
		vault.adminCatAdd(libName);
	}
	
	$scope.adminGlobalChangePath = function() {
		var n = prompt('Please enter library path!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter library path!', 'warning');
		
			return false;
		}
		
		vault.adminGlobalsChange(n);		
	}	
	
	$scope.adminCatDel = function(id, name) {
		if(!confirm('Do you really want to delete category ' + name + '?')){
			return false;
		}
		
		vault.adminCatDel(id);
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
			$location.path("/login");
		}
		
		$rootScope.goHome = function() {
			$location.path("/");
		}
		
		$rootScope.setAuth = function(u) {
			$rootScope.auth = u;
		}
		
		$rootScope.msg = {};
		
		$rootScope.deleteMsg = function() {$rootScope.msg = {};};
		
		$rootScope.singOut = function() {vault.signOut()};
		
		$rootScope.categories = {};
		
		$rootScope.globals = {};
    });
});
// SERVICES

app.service('vault', function($http, $rootScope, $timeout, $interval, $templateCache) {
	
	var showMessage = function(m, t) {
		$rootScope.msg = {};
		$rootScope.msg[t] = m;
	}
	
	var responceMessage = function(r) {
		if(!r.responce) return false;
		
		$rootScope.msg = {};
		var s = {};
		switch(r.responce)
		{			
			case 'CATDELBAD': s.warn = 'Category can\'t be removed!';
			break;
			case 'CATDELOK': s.success = 'Category deleted success!';
			break;
			case 'USERBAD': s.warn = 'Please enter correct credentials!';
			break;
			case 'USEROK': s.success = 'Logged success!';
			break;
			case 'SIGNEDOUT': s.success = 'You are signed out!';
			break;
			case 'CATOK': s.success = 'Success category added!';
			break;
			case 'CATBAD': s.error = 'Error when creating category!';
			break;
			case 'SETTINGOK': s.success = 'Success setting changed!';
			break;
			case 'SETTINGBAD': s.error = 'Success not changed!';
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
			url: 'vault/handle.php?query=' + query + '&time=' + new Date().getTime(),
			method: "POST",
			data: json
		});
	}
	
	var httpGet = function(query) {		
		return $http.get('vault/handle.php?query=' + query + '&time=' + new Date().getTime());
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
				location.reload();
			}, 200);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	// ADMIN
	
	
	var catGet = function() {
		httpGet('CATGET').then(function(r){						
			
			$rootScope.categories = r.data;			
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
	
	var adminCatAdd = function(name, path, id) {
		if(path == null) path = '';
		if(id == null) id = '0';
		
		var json = {'id': id, 'path': path, 'name': name};
		
		HttpPost('CATADD', json).then(function(r){						
			catGet();
			console.log(r.data)		
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var adminCatDel = function(id) {				
		var json = {'id': id};
		
		HttpPost('CATDEL', json).then(function(r){						
			catGet();
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var adminGlobalsChange = function(path) {
		if(path == null) path = '';
				
		var json = {'path': path};
		
		HttpPost('GLOBALCHANGE', json).then(function(r){									
			getGlobal();
				
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
			
	return {
		showMessage: showMessage,
		signIn: signIn,
		signOut: signOut,
		catGet: catGet,
		adminCatDel: adminCatDel,
		adminCatAdd: adminCatAdd,
		getGlobal: getGlobal,
		adminGlobalsChange: adminGlobalsChange
	};
});


