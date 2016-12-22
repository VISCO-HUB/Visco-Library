/* GLOBAL VARS */

var hostname = 'http://' + window.location.hostname + '/';
var previewSizes = {'small': 60, 'medium': 200, 'huge': 600};

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

var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ngCookies', 'ui.bootstrap', 'angularFileUpload']);


// CONFIG 
app.config(function($routeProvider) {    
	
	$routeProvider
    .when('/settings', {
        templateUrl : 'templates/settings.php',
		controller: 'settingsCtrl'
    })
    .when('/category', {
        templateUrl : "templates/category.php",
		controller: 'categoryCtrl'
    })
	.when('/upload', {
        templateUrl : "templates/upload.php",
		controller: 'uploadCtrl'
    })
	.when('/dashboard', {
        templateUrl : "templates/dashboard.php",
		controller: 'dashboardCtrl'
    })
	.when('/users', {
        templateUrl : "templates/users.php",
		controller: 'usersCtrl'
    })
	.when('/models/:page', {
        templateUrl : "templates/models.php",
		controller: 'modelsCtrl',
    })
	.when('/models-edit/:id/:page', {
        templateUrl : "templates/models-edit.php",
		controller: 'modelsEditCtrl',
    })
	.when('/category-edit/:id', {
        templateUrl : "templates/category-edit.php",
		controller: 'categoryEditCtrl'
    })
	.otherwise({redirectTo:'/dashboard'});
});

// DIRECTIVES
app.directive("alerts", function($rootScope) {
    return {
        templateUrl : 'templates/alert.html'
    };
});

app.directive('noClick', function() {
    return {
        restrict: 'A',
        link: function(scope, element) {
            element.click(function(e) {
                e.stopPropagation();
            });
        }
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

	// UPLOAD
app.controller('uploadCtrl', function($scope, FileUploader, vault, $rootScope) {
	
	$rootScope.addCrumb('Upload', '#/upload');
		
	var uploader = $scope.uploader = new FileUploader({
		url: hostname + 'vault/upload.php'
	});

	// FILTERS

	uploader.filters.push({
		name: 'customFilter',
		fn: function(item /*{File|FileLikeObject}*/, options) {
				return this.queue.length < 10;
			}
		},
		{
            name: 'zipFilter',
            fn: function(item /*{File|FileLikeObject}*/, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
                return '|x-zip-compressed|'.indexOf(type) !== -1;
            }
		}
	);
	
	uploader.onSuccessItem = function(fileItem, response, status, headers) {
		vault.deleteMessage();
		console.log(response);
		angular.forEach(uploader.queue, function(item, key) {
			if(fileItem.file.name == item.file.name) {					
				uploader.queue[key].isReplace = false;
			}
		});
		
		if(response.response == 'BADZIP') {
			vault.showMessage('Bad zip file \"' + response.name + '\"' , 'error');	
		}
				
		if(response.response == 'REPLACEFILE') {
			vault.showMessage('Some files already exist! Press the button replace for needed files!', 'warning');
				
			angular.forEach(uploader.queue, function(item, key) {
				  if(fileItem.file.name == item.file.name) {					
					uploader.queue[key].isReplace = true;
					uploader.queue[key].isSuccess = false;
					uploader.queue[key].isUploaded = false;
					uploader.queue[key].progress = 0;
					uploader.queue[key].url = hostname + 'vault/upload.php?replace=true';
				}
			});										
		}
    };
	
	$scope.replaceUpload = function(item) {		
		
		if(!confirm('Do you really want to update file: \"' + item.file.name + '\"?')){
			return false;
		}
		
		item.upload();
	}
	
	uploader.onErrorItem = function(fileItem, response, status, headers) {
            console.info('onErrorItem', fileItem, response, status, headers);
	};
 });
	// DASHBOARD
 app.controller('dashboardCtrl', function($scope, vault, $rootScope) {
	$rootScope.addCrumb('Dashboard', '#/dashboard');
	
	 CanvasJS.addColorSet("shaders",
		[//colorSet Array
			"#337AB7"               
		]);

	
	$scope.chart = new CanvasJS.Chart("chartContainer",
    {
		axisY: {
			lineThickness: 0.2,
			gridThickness: 0.2,
			tickThickness: 0.2
		},
		axisX: {
			lineThickness: 0.2,
			gridThickness: 0,
			tickThickness: 0.2
		},
		toolTip: {
			borderColor: "#FFF"
		},
		colorSet: "shaders",
		title:{
    
		},	
		data: [
			{        
				type: "splineArea",
			
				dataPoints: [
					{ x: new Date(2017, 00, 1), y: 1352 },
					{ x: new Date(2017, 01, 1), y: 1514 },
					{ x: new Date(2017, 02, 1), y: 1321 },
					{ x: new Date(2017, 03, 1), y: 1163 },
					{ x: new Date(2017, 04, 1), y: 950 },
					{ x: new Date(2017, 05, 1), y: 1201 },
					{ x: new Date(2017, 06, 1), y: 1186 },
					{ x: new Date(2017, 07, 1), y: 1281 },
					{ x: new Date(2017, 08, 1), y: 1438 },
					{ x: new Date(2017, 09, 1), y: 1305 },
					{ x: new Date(2017, 10, 1), y: 1480 },
					{ x: new Date(2017, 11, 1), y: 1291 }        
				]
			}            
		]
    });

    $scope.chart.render();
 });
 
 	// USERS
 app.controller('usersCtrl', function($scope, vault, $rootScope) {
	$rootScope.addCrumb('Users', '#/users');
	
 });
 
  	// MODELS
app.controller("modelsEditCtrl", function ($scope, $rootScope, $routeParams, vault) {
	vault.getGlobal();
	vault.catGet();
		
	$rootScope.section = '/models';
	$scope.type = 1;
		
	var id = $routeParams.id;
	var page = $routeParams.page;
		
	$rootScope.addCrumb('Models', '#/models/' + page);
	$rootScope.addCrumb('Edit Model', '');
	
	$rootScope.deleteMsg();
	
	$scope.productGet = function(){			
		vault.productInfo($scope.type, id);		
	};
	
	$scope.productGet();
		
	$scope.catSetParam = function(param, value, id) {	
		vault.catSetParam(param, value, id);
	}
	
	$scope.prodSetParam = function(param, value) {
		vault.prodSetParam(param, value, id, $scope.type);
	}
	
	$scope.getPreviews = function(p) {			
		$scope.previews = vault.getPreviews(p, 'huge'); ;
	}
			
	$scope.getPreviewNames = function(p) {			
		return p.split(';');
	}
	
	$scope.yesno = function(s) {
		if(s) {return 'Yes';}
		return 'No';
	}
	
	$scope.productChangeName = function(catid, oldname) {
		var n = prompt('Please enter new name!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the product name!', 'warning');
		
			return false;
		}
		
		vault.productChangeName(n, oldname, id, catid, $scope.type);
	}
	
	$scope.productChangeOverview = function() {
		var o = prompt('Please enter new overview!', '');			
		
		if(!o || !o.length) {			
			vault.showMessage('Please enter the product overview!', 'warning');
		
			return false;
		}
		
		vault.productChangeOverview(id, o, $scope.type);
	}
	
	$scope.removeTag = function(tag) {
		vault.removeTag(id, tag, $scope.type);
	}
	
	$scope.addTag = function() {
		var tags = prompt('Please add new tags separated by ","', '');			
		
		if(!tags || !tags.length) {			
			vault.showMessage('Please add tags!', 'warning');
		
			return false;
		}
		
		vault.addTag(id, tags, $scope.type)
	}
	
	$scope.setMainPreview = function(name) {
		vault.setMainPreview(id, name, $scope.type);
		$scope.pid = 0;
	}
	
	$scope.removePreview = function(name) {
		if(!confirm('Do you really want to delete preview ' + name + '?')){
			return false;
		}
		
		vault.removePreview(id, name, $scope.type);
		$scope.pid = 0;
	}
	
	
	$scope.pid = 0;
	$scope.choosePreview = function(i){$scope.pid = i;}
});
	
 app.controller('modelsCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.addCrumb('Models', '#/models');
	
	$scope.page = $routeParams.page;
	$rootScope.section = '/models';
	
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
	
	
	if(!$cookieStore.get('perpage')) {		
		$cookieStore.put('perpage', 50);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage');
	
	$scope.type = 1;
			
	$scope.productsGet = function(page, perpage, type, filter){				
		vault.productsGet(page, perpage, type, filter);
	};
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage', p);
		$rootScope.perpage = p;		
		
		$scope.productsGet($scope.page, $rootScope.perpage, $scope.type, $rootScope.modelFilter);
	}
	
	$scope.getMainPreview = function(p, size) {
		var a = vault.getPreviews(p, size);
		return a[0];
	}
	
	$scope.changeFilter = function(f) {
		if(!$rootScope.modelFilter) {$rootScope.modelFilter = {}};
		
		angular.forEach(f, function(value, key) {
			$rootScope.modelFilter[key] = value;
		});
		
		$scope.productsGet($scope.page, $rootScope.perpage, $scope.type, $rootScope.modelFilter);
	}
	
	
	$scope.productsGet($scope.page, $rootScope.perpage, $scope.type, $rootScope.modelFilter);
		
	vault.catGet();
	
	$scope.tm = function(time) {return vault.tm(time)};
	
	$scope.changePage = function() {							
		$location.path('/models/' + $scope.currentPage);		
	}

	$scope.prodSetParam = function(param, value, id) {
		vault.prodSetParam(param, value, id, $scope.type);
	}	
	
	$scope.prodDelete = function(id, name) {
	if(!confirm('Do you really want to delete "' + name + '"?')){
		return false;
	}
	
		vault.prodDelete(id, $scope.type, $scope.page, $rootScope.perpage, $rootScope.modelFilter);
	}
 });
 	// CATEGORY	
app.controller("categoryEditCtrl", function ($scope, $rootScope, $routeParams, vault) {
	vault.getGlobal();
	vault.catGet();
		
	$rootScope.section = '/category';
		
	var id = $routeParams.id;
	$scope.catId = id;
	$scope.subCatEditID = id;
	$scope.level = {};
	
	$rootScope.addCrumb('Categories', '#/category');
	$rootScope.addCrumb('Edit Library', '');
	
	$rootScope.deleteMsg();
		
	$scope.addCat = function(parentid, type) {		
		if($scope.level[parentid] > 1) {return false;}
		
		var n = prompt('Please enter the name!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the name (A-Z, numbers, spaces)!', 'warning');
		
			return false;
		}
		
		vault.catAdd(n, parentid, type);
	}
	
	
	$scope.catChangeDesc = function(id) {
		var n = prompt('Please enter description!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter library path!', 'warning');
		
			return false;
		}
		
		$scope.catSetParam('description', n, id);
	}
	
	$scope.subCatRename = function(id) {
		if(id == $scope.catId) {return false;}
		
		$scope.catChangeName(id);
	}
	
	$scope.subCatDel = function(id) {
		if(id == $scope.catId) {return false;}
		
		$scope.catDel(id);
	}
	
	$scope.catChangeName = function(id) {				
		var n = prompt('Please enter the name!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the name!', 'warning');
		
			return false;
		}
		
		vault.catChangeName(n, id);		
	}	
		
	$scope.changeSort = function(id, sort) {
		vault.catSort(id, sort);
	}
	
	$scope.catDel = function(id, name) {
		if(id == $scope.catId) {return false;}
		
		if(!name) {name = '';}
		if(!confirm('Do you really want to delete category ' + name + '?')){
			return false;
		}
		
		vault.catDel(id);
	}
	
	$scope.isSubCatActive = function(id) {
		return $scope.subCatEditID == id;
	}
	
	$scope.subCatEdit = function(id) {
		$scope.subCatEditID = id;
	}
	
	$scope.catSetParam = function(param, value, id) {
	
		vault.catSetParam(param, value, id);
	}
});
	// CATEGORY EDIT
app.controller("categoryCtrl", function ($scope, $rootScope, vault) {
	vault.getGlobal();
	vault.catGet();
	
	$rootScope.addCrumb('Categories', '#/category');
	
	$scope.addLibrary = function(type) {
		var n = prompt('Please enter the name!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the name (A-z, numbers, spaces)!', 'warning');
		
			return false;
		}
		
		vault.catAdd(n, 0, type);
	}
	
	
	$scope.adminChangeDesc = function(id) {
		var n = prompt('Please enter description!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter library path!', 'warning');
		
			return false;
		}
		
		$scope.adminCatSetParam('description', n, id);
	}
		
	$scope.libDel = function(id, name) {				
		if(!name) {name = '';}
		if(!confirm('Do you really want to delete category ' + name + '?')){
			return false;
		}
		
		vault.catDel(id);
	}
	
	$scope.changeSort = function(id, sort) {
		vault.catSort(id, sort);
	}
	
	$scope.catSetParam = function(param, value, id) {
	
		vault.catSetParam(param, value, id);
	}
});

	// MSG
app.controller("msgCtrl", function ($scope, $rootScope, vault) {
	
});

	// SETTINGS
app.controller("settingsCtrl", function ($scope, $rootScope, vault) {
	vault.getGlobal();
	
	$scope.show = 'tab1';
	
	$rootScope.addCrumb('Settings', '');
	
	$scope.globalsChange = function() {
		var n = prompt('Please enter library path!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter library path!', 'warning');
		
			return false;
		}
		
		vault.globalsChange(n);		
	}
});

// AUTO RUN
app.run(function($rootScope, $location, $routeParams, vault) {
		
    $rootScope.$watch(function() { 
        return $location.path(); 
    },
     function(a, b){
		 
		$rootScope.section = a;
			
		// INIT
		$rootScope.goLogin = function() {
			$location.path("/login");
		}
		
		$rootScope.goHome = function() {
			$location.path("/login");
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
		
		$rootScope.categories = {};
		$rootScope.products = {};
		$rootScope.product = {};
		
		$rootScope.globals = {};
				
		$rootScope.libType = function(type) {
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
	
		$rootScope.breadcrumbs = [];
		
		$rootScope.addCrumb = function(name, url) {
			$rootScope.breadcrumbs.push({'url' : url, 'name': name });
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
			case 'REPLACEFILE': s.error = 'Some files already exist! Press replace for needed files!';
			break;
			case 'PRODBAD': s.error = 'Can\'t get items list!';
			break;
			case 'PRODNAMEINVALID': s.error = 'Invalid name! Allowed only A-Z, a-z, 0-9, minimum 4 symbols';
			break;
			case 'PODNAMEGBAD': s.error = 'Product can\'t be renamed!';
			break;
			case 'PRODNOTFOUNT': s.error = 'Product folder not found in file system! Please fix this issue!';
			break;
			case 'DELPREVIEWLAST': s.error = 'You can\'t delete main preview!';
			break;
			case 'DELPREVIEWOK': s.success = 'Success preview deleted!';
			break;
		}
		
		$rootScope.msg = s;
	}
	
	// SIMPLIFY POST PROCEDURE
	var HttpPost = function(query, json) {		
		return $http({
			url: '../vault/handle.php?query=' + query + '&time=' + new Date().getTime(),
			method: "POST",
			data: json
		});
	}
	
	var httpGet = function(query) {		
		return $http.get('../vault/handle.php?query=' + query + '&time=' + new Date().getTime());
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
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var getPreviews = function(p, size) {
		var a = p.split(';');
		var o = [];
		if(!size) {size = 'small'}
		
		s = previewSizes[size];
		
		angular.forEach(a, function(item) {
			o.push(hostname + 'images/' + item + '_' + s + 'x' + s + '.jpg');
		});
		
		return o;
	}
	
	var productsGet = function(page, perpage, type, filter) {
			
		var json = {'page': page, 'type': type, 'perpage': perpage, 'filter': filter};
		
		HttpPost('PRODGET', json).then(function(r){						
			console.log(r.data);
			$rootScope.products = r.data;						
			if(r.data.products) {$rootScope.product = r.data.products[0]};			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var productInfo = function(type, id) {
			
		var json = {'type': type, 'id': id};
		
		HttpPost('PRODINFO', json).then(function(r){						
			
			$rootScope.product = r.data;									
			if(r.data.info) {
				$rootScope.product.previews = getPreviews(r.data.info.previews, 'huge');				
				$rootScope.product.previewNames = r.data.info.previews.split(';');				
			}
			
			if($rootScope.products && r.data.info) {					
				angular.forEach($rootScope.products.products, function(value, key) {
					if(value.id == r.data.info.id) {
						$rootScope.products.products[key] = r.data.info;
					}
				});
			}
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var prodSetParam = function(param, value, id, type) {
		var json = {'param': param, 'value': value, 'id': id, 'type': type};
		
		HttpPost('PRODSETPARAM', json).then(function(r){												
			console.log(r.data)
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var productChangeName = function(name, oldname, id, catid, type) {
		var json = {'name': name, 'id': id, 'type': type, 'catid': catid, 'oldname': oldname};
		
		HttpPost('PRODSETNAME', json).then(function(r){												
			console.log(r.data)
			responceMessage(r.data);
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var productChangeOverview = function(id, overview, type) {		
		var json = {'id': id, 'type': type, 'overview': overview};
		
		HttpPost('PRODSETOVERVIEW', json).then(function(r){												

			responceMessage(r.data);
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var removeTag = function(id, tag, type) {
		var json = {'id': id, 'tag': tag, 'type': type};
		
		HttpPost('PRODREMOVETAG', json).then(function(r){												
			
			responceMessage(r.data);
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var addTag = function(id, tags, type) {
		var json = {'id': id, 'tags': tags, 'type': type};
		
		HttpPost('PRODADDTAGS', json).then(function(r){												
			
			responceMessage(r.data);
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var prodDelete = function(id, type, page, perpage, filter) {
		var json = {'id': id, 'type': type};
		
		HttpPost('PRODDELETE', json).then(function(r){												
			console.log(r.data)
			responceMessage(r.data);
			productsGet(page, perpage, type, filter)		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var setMainPreview = function(id, name, type) {
		var json = {'id': id, 'name': name, 'type': type};
		
		HttpPost('PRODSETMAINPREVIEW', json).then(function(r){												
			
			responceMessage(r.data);
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var removePreview = function(id, name, type) {
		var json = {'id': id, 'name': name, 'type': type};
		
		HttpPost('PRODDELPREVIEW', json).then(function(r){												
			console.log(r.data)
			responceMessage(r.data);
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var tm = function(time) {
		var d = new Date(time * 1000);
		var s = d.getDate() + '.' + (d.getMonth()+1) + '.' + d.getFullYear();
		
		return s;
    }
	
	var getGlobal = function() {
		httpGet('GLOBALGET').then(function(r){									
			$rootScope.globals = r.data;
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var catAdd = function(name, parentid, type) {
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
		
	var catDel = function(id) {				
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
		
	var catSetParam = function(param, value, id) {
		var json = {'param': param, 'value': value, 'id': id};
		
		HttpPost('CATSETPARAM', json).then(function(r){									
			console.log(r.data)
			catGet();		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var globalsChange = function(path) {
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
	
	var catChangeName = function(name, id) {
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
	
	var catSort = function(id, sort) {		
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
		deleteMessage: deleteMessage,
		signIn: signIn,
		signOut: signOut,
		catGet: catGet,
		catDel: catDel,
		catAdd: catAdd,
		getGlobal: getGlobal,
		globalsChange: globalsChange,
		catSetParam: catSetParam,
		catChangeName: catChangeName,
		catSort: catSort,
		productsGet: productsGet,
		productInfo: productInfo,
		prodSetParam: prodSetParam, 
		setMainPreview: setMainPreview,
		removePreview: removePreview,
		removeTag: removeTag,
		prodDelete: prodDelete,
		addTag: addTag,
		productChangeName: productChangeName,
		productChangeOverview: productChangeOverview,
		getPreviews: getPreviews,
		tm: tm
	};
});


