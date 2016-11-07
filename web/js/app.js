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

// DIRECTIVES
app.directive("alerts", function($rootScope) {
    return {
        templateUrl : 'templates/alert.html'
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
	$scope.section = 'cat';

	vault.getGlobal();
	vault.catGet();
	
	$scope.addLibrary = function(type) {
		var n = prompt('Please enter the name!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the name (A-z, numbers, spaces)!', 'warning');
		
			return false;
		}
		
		vault.adminCatAdd(n, 0, type);
	}
	
	$scope.adminAddCat = function(parentid, type) {		
		if($scope.level[parentid] > 1) {return false;}
		
		var n = prompt('Please enter the name!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the name (A-Z, numbers, spaces)!', 'warning');
		
			return false;
		}
		
		vault.adminCatAdd(n, parentid, type);
	}
	
	$scope.adminGlobalChangePath = function() {
		var n = prompt('Please enter library path!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter library path!', 'warning');
		
			return false;
		}
		
		vault.adminGlobalsChange(n);		
	}
	
	$scope.libType = function(type) {
		var t = '';
		
		switch(type)
		{			
			case '1': t = 'Models';
			break;
			case '2': t = 'Textures';
			break;
			default: t = 'Unknown';
			break;
		}
		
		return t;
	}
	
	$scope.adminChangeDesc = function(id) {
		var n = prompt('Please enter description!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter library path!', 'warning');
		
			return false;
		}
		
		$scope.adminCatSetParam('description', n, id);
	}
	
	$scope.adminSubCatRename = function(id) {
		if(id == $scope.adminCatEditId) {return false;}
		
		$scope.adminChangeName(id);
	}
	
	$scope.adminSubCatDel = function(id) {
		if(id == $scope.adminCatEditId) {return false;}
		
		$scope.adminCatDel(id);
		
		//subCatEditID=adminCatEditId
	}
	
	$scope.adminChangeName = function(id) {				
		var n = prompt('Please enter the name!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the name!', 'warning');
		
			return false;
		}
		
		vault.adminChangeName(n, id);		
	}	
	
	$scope.libDel = function(id, name) {				
		if(!name) {name = '';}
		if(!confirm('Do you really want to delete category ' + name + '?')){
			return false;
		}
		
		vault.adminCatDel(id);
	}
	
	$scope.changeSort = function(id, sort) {
		vault.adminCatSort(id, sort);
	}
	
	$scope.adminCatDel = function(id, name) {
		if(id == $scope.adminCatEditId) {return false;}
		
		if(!name) {name = '';}
		if(!confirm('Do you really want to delete category ' + name + '?')){
			return false;
		}
		
		vault.adminCatDel(id);
	}
	
	$scope.isAdminCatEdit = false;
	
	$scope.level = {};	
	$scope.subCatEditID = -1;		
	$scope.adminCatEditId = -1;
	
	$scope.adminCatEdit = function(id) {
		$scope.adminCatEditId = id;
		$scope.subCatEditID  = id;
		$scope.level[id] = 0;
		$scope.isAdminCatEdit = true;
		$rootScope.deleteMsg();
	}
	
	$scope.isSubCatActive = function(id) {
		return $scope.subCatEditID == id;
	}
	
	$scope.subCatEdit = function(id) {
		$scope.subCatEditID = id;
	}
	
	$scope.adminCatSetParam = function(param, value, id) {
	
		vault.adminCatSetParam(param, value, id);
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
		
		$rootScope.count = function(o) {
			var count = 0;
			
			if(!o) {return 0;}
			for(var prop in o) {
				if(o.hasOwnProperty(prop))
					count = count + 1;
			}
			return count;
		}
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
			case 'RENOK': s.success = 'Category renamed success!';
			break;
			case 'RENBAD': s.error = 'Category can\'t be renamed!';
			break;
			case 'CATDELBAD': s.warning = 'Category can\'t be removed!';
			break;
			case 'CATDELOK': s.success = 'Category deleted success!';
			break;
			case 'CATDELWARN': s.warning = 'Sorry! This category contains the files, remove them first!';
			break;
			case 'USERBAD': s.warning = 'Please enter correct credentials!';
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
			case 'SETTINGBAD': s.error = 'Setting not changed!';
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
	
	
	var catGet = function(parentid) {
		if(parentid == null) {parentid = 0;}
		
		var json = {'parentid': parentid};
		
		HttpPost('CATGET', json).then(function(r){						
			
			$rootScope.categories = r.data;
			responceMessage(r.data);
			console.log(r.data)
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
		
	var adminCatAdd = function(name, parentid, type) {
		if(parentid == null) parentid = '0';
		
		var json = {'parentid': parentid, 'name': name, 'type': type};
		
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
			console.log(r.data)
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var adminCatSetParam = function(param, value, id) {
		var json = {'param': param, 'value': value, 'id': id};
		
		HttpPost('CATSETPARAM', json).then(function(r){									
			console.log(r.data)
			catGet();		
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
	
	var adminChangeName = function(name, id) {
		if(name == null) name = '';
			
		var json = {'name': name, 'id': id};
		
		HttpPost('CATRENAME', json).then(function(r){									
			catGet();
			console.log(r.data)	
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var adminCatSort = function(id, sort) {		
		var json = {'id': id, 'sort': sort};
		
		HttpPost('CATSORT', json).then(function(r){									
			catGet();
			console.log(r.data)	
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
		adminGlobalsChange: adminGlobalsChange,
		adminCatSetParam: adminCatSetParam,
		adminChangeName: adminChangeName,
		adminCatSort: adminCatSort
	};
});


