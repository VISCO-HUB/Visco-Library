/* GLOBAL VARS */

var hostname = window.location.protocol + '//' + window.location.hostname + '/';
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
   if(!$(e.target).is('input') && !$(e.target).is('img')) {
		e.preventDefault();
   }
}, false)

/* APP */

var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ngCookies', 'ui.bootstrap', 'angularFileUpload', 'chart.js', 'ngAnimate', 'btorfs.multiselect']);


// CONFIG 
app.config(function($routeProvider, $sceProvider) {    
	
	 $sceProvider.enabled(false);
	
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
	.when('/dashboard/:page', {
        templateUrl : "templates/dashboard.php",
		controller: 'dashboardCtrl'
    })
	.when('/users/:page', {
        templateUrl : "templates/users.php",
		controller: 'usersCtrl'
    })
	.when('/models/:page', {
        templateUrl : "templates/models.php",
		controller: 'modelsCtrl',
    })
	.when('/msg/:page', {
        templateUrl : "templates/msg.php",
		controller: 'adminMsgCtrl',
    })	
	.when('/textures/:page', {
        templateUrl : "templates/textures.php",
		controller: 'texturesCtrl',
    })
	.when('/emailing', {
        templateUrl : "templates/emailing.php",
		controller: 'emailingCtrl',
    })
	.when('/tools', {
        templateUrl : "templates/tools.php",
		controller: 'toolsCtrl',
    })
	.when('/tags/:page', {
        templateUrl : "templates/tags.php",
		controller: 'tagsCtrl',
    })
	.when('/comments/:page', {
        templateUrl : "templates/comments.php",
		controller: 'commentsCtrl',
    })
	.when('/models-edit/:id/:page', {
        templateUrl : "templates/models-edit.php",
		controller: 'modelsEditCtrl',
    })
	.when('/category-edit/:id', {
        templateUrl : "templates/category-edit.php",
		controller: 'categoryEditCtrl'
    })
	.otherwise({redirectTo:'/dashboard/1'});
});

// DIRECTIVES
app.directive("alerts", function($rootScope) {
    return {
        templateUrl : 'templates/alert.html'
    };
});

app.directive("menu", function($rootScope) {
    return {
        templateUrl : hostname + '/admin/templates/menu.html'
    };
});

app.directive("btnEdit", function($rootScope) {
    return {
        template: '<button class="btn btn-primary btn-xs margin-left-15"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>'
    };
});

app.directive("btnTrigger", function($rootScope) {
    return {
		restrict: 'E',
		scope: {
			cls: '=',
			active: '=',
			toggle: '&'
		},
		controller: function($scope) {
			
		},
        templateUrl : hostname + '/admin/templates/btn-trigger.html'
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
app.controller('uploadCtrl', function($scope, FileUploader, vault, $rootScope, $http) {
	
	$rootScope.addCrumb('Upload', '#/upload');
		
	var uploader = $scope.uploader = new FileUploader({
		url: hostname + 'admin/vault/upload.php'
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
                var t = '|x-zip-compressed|'.indexOf(type) !== -1;
				if(!t) {alert('Supported only *.zip format!\n\nIf you uploaded zip archive and you see this message, you have a problem with the definition of this format. You need to reinstall 7Zip or edit the registry.\n\nDownload registry fix: ' + hostname + 'fix/zip-reg.zip')}
				return t;
            }
		}
	);
	
	uploader.onSuccessItem = function(fileItem, response, status, headers) {
		vault.deleteMessage();
		
		angular.forEach(uploader.queue, function(item, key) {
			if(fileItem.file.name == item.file.name) {					
				uploader.queue[key].isReplace = false;
			}
		});
		
		if(response.response == 'BADZIP') {
			vault.showMessage('Bad zip file \"' + response.name + '\"' , 'error');	
		}
		
		if(response.response == 'UPLOADCLOSED') {
			vault.showMessage('Uploading closed!<br><br>' + $rootScope.globals.message , 'error');
			fileItem.isError = true;
			fileItem.isSuccess = false;
		}
				
		if(response.response == 'REPLACEFILE') {
			vault.showMessage('Some files already exist. Click "Replace" button if you want to keep the new files or \"Clear\" button to delete from list.', 'warning');
			console.log(response);
			angular.forEach(uploader.queue, function(item, key) {
				  if(fileItem.file.name == item.file.name) {					
					uploader.queue[key].isReplace = true;
					uploader.queue[key].isSuccess = false;
					uploader.queue[key].isUploaded = false;
					uploader.queue[key].progress = 0;
					uploader.queue[key].url = hostname + 'admin/vault/upload.php?replace=true&dist=' + response.dist + '&name=' + response.name;
				}
			});										
		}
    };
	
	$scope.replaceUpload = function(item) {		
		
		if(!confirm('Do you really want to update file: \"' + item.file.name + '\"?')){
			return false;
		}
		
		item.isUploading = true;
		item.isCancel = false;
		
		console.log(item);
		
		
		$http({method: 'GET', url: item.url}).
            then(function success(response) {
                if(response.data.response == 'DONE') {
					item.isSuccess = true;
					item.isUploaded = true;
					item.isUploading = false;
					item.isReplace = false;
					
					vault.showMessage('File "' + item.file.name + '"  successfully replaced!', 'success');
				} else {
					item.isError = true;
				}				
		});
			
		
		//item.upload();
	}
	
	uploader.onErrorItem = function(fileItem, response, status, headers) {
            console.info('onErrorItem', fileItem, response, status, headers);
	};
 });
	// DASHBOARD
 app.controller('dashboardCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.addCrumb('Dashboard', '#/dashboard/1');
	$scope.page = $routeParams.page;
	$rootScope.section = '/dashboard';
	
	$scope.currentPage = 1;
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
	
	
	if(!$cookieStore.get('perpage')) {		
		$cookieStore.put('perpage', 50);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage');
	
	$scope.downloadLogGet = function(page, perpage, filter) {
		vault.downloadLogGet(page, perpage, filter);
	}
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage', p);
		$rootScope.perpage = p;		
		
		$scope.downloadLogGet($scope.page, $rootScope.perpage, $rootScope.downloadLogFilter);
	}
	
	$scope.downloadLogGet($scope.page, $rootScope.perpage, $rootScope.downloadLogFilter);
	
	$scope.changePage = function(p) {	
		$location.path('/dashboard/' + p);	
	}
	
	$rootScope.dataMonthDownload = [];
	$rootScope.labelsMonthDownload = [];
	$rootScope.labelsUserColors = ['#35A9E1', '#FF5555', '#FABB3C', '#4BACC6', '#F79646', '#2C4D75', '#C0504D', '#FABB3C', '#8064A2', '#4BACC6', '#F79646', '#2C4D75', '#C0504D'];
	$rootScope.dataUserDownload = [];
	$rootScope.labelsUserDownload = [];
	
	
	$rootScope.labelsSizeColors = ['#4BA871', '#9ED41C', '#35A9E1', '#F79646', '#FABB3C', '#C0504D',  '#3D2D48'];
	$rootScope.dataSize = [];
	$rootScope.labelsSize = [];
	$rootScope.labelsSizeDisc = [];
	
	if(!$rootScope.tabRow1) {
		$rootScope.tabRow1 = 1;
	}
	
	$scope.changeTabRow1 = function(i) {
		$rootScope.tabRow1 = i;
		$('html, body').animate({
        	scrollTop: ($('#tabs').offset().top) - 60
    	}, 500);
	}
	
	$scope.getDashBoardInfo = function() {
		vault.getDashBoardInfo();
	}
		
	$scope.colors = ['rgba(0,154,191,0.5)'];
	   
    $scope.datasetOverride = [
      {
        label: "Downloads",
        borderWidth: 3,
        hoverBackgroundColor: "rgba(255,99,132,0.4)",
        hoverBorderColor: "rgba(255,99,132,1)",
		pointRadius: 6   
      },
	  {}
    ];
		
	 $scope.datasetOverride2 = [
      {
        label: "Downloads",
        borderWidth: 3
      },
	  {}
    ];
	
	
	$scope.options2 = {
		scales:
        {
			reverse: true,           
			xAxes: [{
                display: false
            }]
        }
	};
	
	$scope.options3 = {
		responsive: true,
		maintainAspectRatio: true,
		segmentShowStroke: false,
		animateRotate: true,
		animateScale: false,
		percentageInnerCutout: 50,
		legend: {
			display: false,
			position: 'bottom',
			fullWidth: false
		}
	};
	
	$scope.options4 = {
		responsive: true,
		maintainAspectRatio: true,
		animateRotate: true,
		animateScale: false,
		percentageInnerCutout: 100
	};
	
	$scope.options = {
		scales: {
			reverse: true,
			yAxes: [
			{
			 ticks:				
				{
					beginAtZero:true
				}
			}
		  ]
		}
	};
		
	$scope.getDashBoardInfo();
  });
 
	//USERS
app.controller('usersCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.addCrumb('Users', '#/users');
	
	$scope.page = $routeParams.page;
	$rootScope.section = '/users';
	
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
	
	$scope.tab = 'users';
	$scope.changeTab = function(t) {		
		$scope.getData();
		$scope.tab = t;
	}
	
	if(!$cookieStore.get('perpage-users')) {		
		$cookieStore.put('perpage-users', 250);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage-users');

	$scope.usersGet = function(page, perpage, filter){				
		//vault.usersGet(page, perpage, filter);
		vault.usersGet(1, 100000, filter);
	};
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage-users', p);
		$rootScope.perpage = p;		
		
		$scope.usersGet($scope.page, $rootScope.perpage, $rootScope.usersFilter);
	}
	
	$scope.orderUsers = '';
	$scope.reverse = false;
		
	$scope.orderByParam = function(x) {
		$scope.reverse = !$scope.reverse;
		$scope.orderUsers = x;		
	};
	
	$scope.usersSetParam = function(param, value, id) {
		if($rootScope.auth.id == id) {
			alert('You can\'t change parameters for itself!');
			return false;
		}
			
		vault.usersSetParam(param, value, id);
	}
	
	$scope.usersGetFilter = function() {
		vault.usersGetFilter();
	}
	
	$scope.changeFilter = function(f) {
		if(!$rootScope.usersFilter) {$rootScope.usersFilter = {}};
		$location.path('/users/1');	
		
		angular.forEach(f, function(value, key) {
			$rootScope.usersFilter[key] = value;
		});
		
		$scope.usersGet($scope.page, $rootScope.perpage, $rootScope.usersFilter);
	}
	
	$scope.changePage = function() {							
		$location.path('/users/' + $scope.currentPage);		
	}
	
	
	$scope.usersAddGroup = function(){
		var name = prompt('Please enter new group name!', '');			
		
		if(!name || !name.length) {			
			vault.showMessage('Please enter the name!', 'warning');
		
			return false;
		}
		
		if(name.match(/[^a-zA-Z0-9-:()_ ]/)) {
			vault.showMessage('Group has wrong format!', 'warning');
			
			return false;
		}
		
		vault.usersAddGroup(name);
	}
	
	
	$scope.usersRenameGroup = function(id, oldname){
		var name = prompt('Rename group ' + oldname + ':', oldname);			
		
		if(!name || !name.length) {			
			vault.showMessage('Please enter the name!', 'warning');
		
			return false;
		}
		
		if(name.match(/[^a-zA-Z0-9-:()_ ]/)) {
			vault.showMessage('Group has wrong format!', 'warning');
			
			return false;
		}
		
		vault.usersRenameGroup(id, name);
	}
	
	$scope.usersGetGroups = function() {
		vault.usersGetGroups();
	}
			
	$scope.usersDelGroup = function(id, name) {
		if(!confirm('Do you really want to delete group: \"' + name + '\"?\n\nWARNING!\nThis action will delete this group permanently!')){
			return false;
		}
		
		vault.usersDelGroup(id, name);
	}
	
	$scope.usersToggleGroup = function(userid, groupid) {
		vault.usersToggleGroup(userid, groupid);
	}
	
	$scope.getData = function() {
		//$scope.usersGetFilter();
		$scope.usersGet($scope.page, $rootScope.perpage, $rootScope.usersFilter);
		//$scope.usersGetGroups();
	}

	$scope.getData();
	
});
 

	//TAGS
app.controller('tagsCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.addCrumb('Tags', '#/tags');
	
	$scope.page = $routeParams.page;
	$rootScope.section = '/tags';
	
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
	
	
	if(!$cookieStore.get('perpage')) {		
		$cookieStore.put('perpage', 50);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage');
	
	$scope.tagsGet = function(page, perpage, filter) {
		vault.tagsGet(page, perpage, filter);
	}
		
	$scope.findTag = function(t) {
		if(!$rootScope.tagsFilter) {$rootScope.tagsFilter = {}};
		
		
		$location.path('/tags/1');	
		$rootScope.tagsFilter.search = t;
		
		$scope.tagsGet($scope.page, $rootScope.perpage, $rootScope.tagsFilter);
	}
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage', p);
		$rootScope.perpage = p;		
		
		$scope.tagsGet($scope.page, $rootScope.perpage, $rootScope.tagsFilter);
	}
	
	$scope.tagDelete = function(t) {
		if(!confirm('Do you really want to delete tag: \"' + t + '\"?\n\nWARNING!\nThis action will delete this tag from all models and textures!\nYou will remove these tag permanently!')){
			return false;
		}
		
		vault.tagDelete(t, $scope.page, $rootScope.perpage, $rootScope.tagsFilter);
	}
	
	$scope.tagChange = function(tag) {
		if(!confirm('Do you really want to replace tag: \"' + tag + '\"?\n\nWARNING!\nThis action will replace this tag from all models and textures!\nYou will change these tag permanently!')){
			return false;
		}
		
		var newtag = prompt('Please enter new tag!', '');			
		
		if(!newtag || !newtag.length) {			
			vault.showMessage('Please enter the tag!', 'warning');
		
			return false;
		}
		
		if(newtag.match(/[^a-z0-9]/)) {
			vault.showMessage('Tag has wrong format!', 'warning');
			
			return false;
		}
		
		vault.tagChange(tag, newtag, $scope.page, $rootScope.perpage, $rootScope.tagsFilter);
	}
	
	$scope.tagsGet($scope.page, $rootScope.perpage, $rootScope.tagsFilter);
	
	$scope.changePage = function() {							
		$location.path('/tags/' + $scope.currentPage);		
	}
});

	//COMMENTS
app.controller('commentsCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.addCrumb('Comments', '#/comments/1');
	
	$scope.page = $routeParams.page;
	$rootScope.section = '/comments';
	
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
	
	
	if(!$cookieStore.get('perpage')) {		
		$cookieStore.put('perpage', 50);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage');
	
	$scope.commentsGet = function(page, perpage, filter) {
		vault.commentsGet(page, perpage, filter);
	}
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage', p);
		$rootScope.perpage = p;		
		
		$scope.commentsGet($scope.page, $rootScope.perpage, $rootScope.commentsFilter);
	}
	
	$scope.commentDelete = function(id) {
		if(!confirm('Do you really want to delete comment?')){
			return false;
		}
		
		vault.commentDelete(id, $scope.page, $rootScope.perpage, $rootScope.commentsFilter);
	}
	
	$scope.commentsGet($scope.page, $rootScope.perpage, $rootScope.commentsFilter);
	
	$scope.changePage = function() {							
		$location.path('/comments/' + $scope.currentPage);		
	}

});

	//EMAILING
app.controller('emailingCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.addCrumb('Emailing', '#/emailing');
	
	$scope.data = {
		userSelect: {},
		content: '',
		subject: '',
		templates: ['Default', 'Last Assets For 7 days'],
		currenttpl: -1,
		filter: {},
		force: false
	}
	$rootScope.users = {
		users: []
	}
	
	
	$scope.sendEmail = function(data) {
		var uCnt = data.userSelect.length ? data.userSelect.length : 'All';
		var uGrp = data.filter.grp ? data.filter.grp : -1;
		
		if(!data.content.length || !data.subject.length) {
			vault.showMessage('Please fill correct all data!', 'warning');			
			return false;
		}
		
		if(!confirm('Do you really want to send mail for ' + uCnt + ' users from group ' + uGrp + ' ?')){
			return false;
		}
		
		vault.sendEmail(data);
	}
		
	$scope.changeTemplate = function(id) {
		
		if($scope.data.content.length > 0) {
			if(!confirm('Do you really want to change template?\nThis action resets the message text!')){
				return false;
			}
		}
		
		$scope.data.currenttpl = id;		
				
		switch(id)
		{			
			case 0: {
				$scope.data.subject = 'Notification';
				$scope.data.content = '';				
			}
			break;	
			case 1: {
				$scope.data.subject = 'New Models';
				$scope.data.content = 'You got this letter because you were subscribed to the Visco Assets Library news!';									
				$scope.data.content += '\n[lastassets:7]';		
			}
			break;	
			default: {
				$scope.data.subject = '';
				$scope.data.content = '';										
			}			
			break;				
		}			
	}
	
	$scope.attachToMail = function(type) {
		
		var question = '';
		switch(type)
		{				
			case 'lastassets': question = 'Enter days for last models!';
			break;
			case 'img': question = 'Enter image url with "http://"!';
			break;
			case 'favorite': question = 'Enter favorite id!';
			break;
			default: return false;
			break;
		}
		
		var param = prompt(question, '');			
		if(!param) {return false;}
		if(!param.length) {			
			vault.showMessage('Please enter correct parameter!', 'warning');
		
			return false;
		}
		
		var error = false;
		
		switch(type)
		{				
			case 'lastassets': {
				if(param.match(/[^0-9]/)) {					
					error = true;
				}
			}
			break;
			break;
			case 'img': {
				error = true;
				if(param.match(/([a-z\-_0-9\/\:\.]*\.(jpg|jpeg|png|gif))/i)) {					
					error = false;
				}
			}
			break;
			case 'favorite': {
				if(param.match(/[^0-9A-Za-z]/)) {					
					error = true;
				}
			}
			break;			
		}
		
		if(error) {
			vault.showMessage('Please enter correct parameter!', 'warning');
			return false;
		}
		
		$scope.data.content += '\n[' + type + ':' + param + ']';
	}
		
	$scope.page = $routeParams.page;
	$rootScope.section = '/emailing';
		
	
	$scope.usersGet = function(filter){				
		vault.usersGet(1, 100000, filter);
	};
	
	$scope.changeFilter = function(f) {
		if(!$rootScope.usersFilter) {$rootScope.usersFilter = {}};
				
		angular.forEach(f, function(value, key) {
			$rootScope.usersFilter[key] = value;
		});
			
		$scope.data.userSelect = {};
		$scope.data.filter = $rootScope.usersFilter;
		$scope.usersGet($rootScope.usersFilter);
	}
	
	$scope.usersGet($rootScope.usersFilter);
	vault.usersGetFilter();
});


	//TEXTURES
app.controller('texturesCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore) {
	$rootScope.addCrumb('Textures', '#/textures');
	
	$scope.page = $routeParams.page;
	$rootScope.section = '/textures';
	
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
	
	
	if(!$cookieStore.get('perpage')) {		
		$cookieStore.put('perpage', 50);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage');

});
 
  	// MODELS
app.controller("modelsEditCtrl", function ($scope, $rootScope, $routeParams, vault, FileUploader, $window) {
	vault.catGet();
			
	$rootScope.section = '/models';
	$scope.type = 1;
		
	var id = $routeParams.id;
	var page = $routeParams.page;
	
	$scope.moveToCat = [];
	$scope.moveToCatName = [];
	
	$scope.selectMoveToCat = function(id, name, lvl) {
		
		$scope.moveToCat.length = lvl + 1;
		$scope.moveToCatName.length = lvl + 1;
		$scope.moveToCat[lvl] = id;
		$scope.moveToCatName[lvl] = name;
	}
	
	$rootScope.moveProductActive = false;
		
	$scope.moveProduct = function(cid) {
		if(!confirm('Do you really want to move model?')){
			return false;
		}
		
		$rootScope.moveProductActive = true;
		vault.moveProduct(id, cid, $scope.type);
	}
		
	$rootScope.addCrumb('Models', '#/models/' + page);
	$rootScope.addCrumb('Edit Model', '');
	
	$rootScope.deleteMsg();
	
	$scope.openModel = function(id) {
		vault.sendCommandMXS('OPEN_MODEL', id);
	}
	
	
	$scope.mergeModel = function(id) {
		vault.sendCommandMXS('MERGE_MODEL', id);
	}
	
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
	
	$scope.prodToggleParam = function(param) {
		vault.prodToggleParam(param, id, $scope.type);
	}
	
	$scope.prodSetTextParam = function(param, oldval) {
		var n = prompt('Please enter new value!', oldval);			
				
		vault.prodSetTextParam(param, n, id, $scope.type);
	}
	
	$scope.prodDeleteFromEdit = function(id, name) {
		if(!confirm('Do you really want to delete "' + name + '"?')){
			return false;
		}
	
		vault.prodDeleteFromEdit(id, $scope.type);				
	}
	
	$scope.getPreviews = function(p) {			
		$scope.previews = vault.getPreviews(p, 'huge'); ;
	}
			
	$scope.getPreviewNames = function(p) {			
		return p.split(';');
	}
	
	$scope.yesno = function(s) {
		if(s && s != 'No') {return 'Yes';}
		return 'No';
	}
	
	$scope.productChangeName = function(catid, oldname) {
		var n = prompt('Please enter new name!', oldname);			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter the product name!', 'warning');
		
			return false;
		}
		
		vault.productChangeName(n, oldname, id, catid, $scope.type);
	}
	
	$scope.productChangeOverview = function(o) {
		if(!confirm('Do you really want to change description?\n\n' + o)){
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
	
	
	
	// UPLOAD
	var uploaderImg = $scope.uploaderImg = new FileUploader({
		url: hostname + 'admin/vault/upload.preview.php?id=' + id + '&type=' + $scope.type
	});
	
	uploaderImg.onAfterAddingFile = function(fileItem) {
        uploaderImg.uploadAll();
		//console.info('onAfterAddingFile', fileItem);
    };
	
	uploaderImg.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
        //console.info('onWhenAddingFileFailed', item, filter, options);
		vault.showMessage('Allowed only *.jpg files!', 'error');
	};
	
	uploaderImg.onCompleteItem = function(fileItem, response, status, headers) {
		//console.info('onCompleteItem', fileItem, response, status, headers);
			
		if(response.response == 'DONE')	{
			vault.showMessage('Preview uploaded!', 'success');
		}
		
		if(response.response == 'FAILED')	{
			vault.showMessage('Error while uploading preview!', 'error');
		}
		
		if(response.response == 'MOVEERROR')	{
			vault.showMessage('Preview uploaded but not moved to preview folder!', 'error');
		}
		
		uploaderImg.clearQueue();
		console.log(response)	
			
		$scope.productGet();	
		
	};
	
	uploaderImg.onErrorItem = function(fileItem, response, status, headers) {
        //console.info('onErrorItem', fileItem, response, status, headers);
	};
			
	uploaderImg.filters.push({
		name: 'imageFilter',
		fn: function(item /*{File|FileLikeObject}*/, options) {
			var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
			z = '|jpg|jpeg|'.indexOf(type) !== -1;
			return z;
		}
	});
	
	// WEBGL
	
	$rootScope.webgl = '';
	
	$rootScope.webglUrl = function(item) {
	
		if(!item) {
			$rootScope.webgl = '';			
			v = document.getElementById('webgl');
			if(v) {v.contentWindow.document.body.innerHTML='';}
			return false;
		}
		
		/*if(!confirm('Do you really want open 3D mode?')){
			return false;
		}*/
		$rootScope.webgl = hostname + 'webgl/?item=' + item;				
	}
	
	$scope.webglStyle = {height: ($('#webgl').width()) + 'px'}
		
	
	$scope.removeWebGLModel = function(item) {
		if(!confirm('Do you really want to delete Interactive Model?')){
			return false;
		}
		$rootScope.webglUrl(null);
		vault.removeWebGLModel(id, $scope.type);
	}
	
	// UPLOAD WEBGL
	var uploaderWebGl = $scope.uploaderWebGl = new FileUploader({
		url: hostname + 'admin/vault/upload.webgl.php?id=' + id + '&type=' + $scope.type
	});
	
	uploaderWebGl.onAfterAddingFile = function(fileItem) {
        uploaderWebGl.uploadAll();
		//console.info('onAfterAddingFile', fileItem);
    };
	
	uploaderWebGl.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
        //console.info('onWhenAddingFileFailed', item, filter, options);
		vault.showMessage('Allowed only *.zip files!', 'error');
	};
	
	uploaderWebGl.onCompleteItem = function(fileItem, response, status, headers) {
		//console.info('onCompleteItem', fileItem, response, status, headers);
			console.log(response.response);
		if(response.response == 'DONE')	{
			vault.showMessage('Model uploaded!', 'success');
		}
		
		if(response.response == 'FAILED')	{
			vault.showMessage('Error while uploading Model!', 'error');
		}
		
		if(response.response == 'BADZIP')	{
			vault.showMessage('Error model has wrong format!', 'error');
		}
		
		if(response.response == 'MOVEERROR')	{
			vault.showMessage('Model uploaded but not moved to Model folder!', 'error');
		}
		
		uploaderWebGl.clearQueue();
		console.log(response)	
			
		$scope.productGet();	
		
	};
	
	uploaderWebGl.onErrorItem = function(fileItem, response, status, headers) {
        //console.info('onErrorItem', fileItem, response, status, headers);
	};
			
	uploaderWebGl.filters.push({
		name: 'webglFilter',
		fn: function(item /*{File|FileLikeObject}*/, options) {
			var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
			z = '|x-zip-compressed|'.indexOf(type) !== -1;
			return z;
		}
	});
	
	
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
	
	
	$scope.openModel = function(id) {
		vault.sendCommandMXS('OPEN_MODEL', id);
	}
	
	$scope.mergeModel = function(id) {
		vault.sendCommandMXS('MERGE_MODEL', id);
	}
	
	if(!$cookieStore.get('perpage')) {		
		$cookieStore.put('perpage', 50);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage');
	
	$scope.type = 1;
			
	$scope.productsGet = function(page, perpage, type, filter){				
		f = $cookieStore.get('modelFilter');
		if(!filter && f) {
			filter = f;
		}
		
		vault.productsGet(page, perpage, type, filter);
	};
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage', p);
		$rootScope.perpage = p;		
				
		$scope.productsGet($scope.page, $rootScope.perpage, $scope.type, $rootScope.modelFilter);
	}
		
	$scope.changeFilter = function(f) {
		if(!$rootScope.modelFilter) {$rootScope.modelFilter = {}};
		
		$location.path('/models/1');	
		angular.forEach(f, function(value, key) {
			$rootScope.modelFilter[key] = value;
		});
		
		$cookieStore.put('modelFilter', $rootScope.modelFilter);
		
		$scope.productsGet($scope.page, $rootScope.perpage, $scope.type, $rootScope.modelFilter);
	}
	
	
	$scope.productsGet($scope.page, $rootScope.perpage, $scope.type, $rootScope.modelFilter);
		
	vault.catGet();
	
	
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
 
	// MSG CTRL
	
	// MSG
app.controller("msgCtrl", function ($scope, $rootScope, vault) {
	$rootScope.msg = {};
});	
	
app.controller('adminMsgCtrl', function($scope, vault, $rootScope, $location, $routeParams, $timeout, $cookieStore, $sce) {
	
	$rootScope.addCrumb('Messages', '#/msg');
	
	$scope.page = $routeParams.page;
	$rootScope.section = '/msg';
	
	$timeout(function(){
		$scope.currentPage = $scope.page;
	}, 50);
	
	
	if(!$cookieStore.get('perpage')) {		
		$cookieStore.put('perpage', 50);	
	};
	
	$rootScope.perpage = $cookieStore.get('perpage');
	
	$scope.type = 1;
		
	$scope.msgGet = function(page, perpage, filter){				
		vault.msgGet(page, perpage, filter);
	};
	
	$scope.changePerPage = function(p) {		
		$cookieStore.put('perpage', p);
		$rootScope.perpage = p;		
		
		$scope.msgGet($scope.page, $rootScope.perpage,$rootScope.msgFilter);
	}
		
	
	$scope.msgGet($scope.page, $rootScope.perpage, $rootScope.msgFilter);
	
	
	$scope.changePage = function() {							
		$location.path('/msg/' + $scope.currentPage);		
	}

	$scope.msgSetParam = function(param, value, id) {
		vault.msgSetParam(param, value, id, $scope.page, $rootScope.perpage, $rootScope.msgFilter);
	}	
	
	$scope.msgDelete = function(id, name) {
		if(!confirm('Do you really want to delete "' + name + '"?')){
			return false;
		}
	
		$scope.currentMessage = {
			'msg': 'Message "' + name + '" deleted!',
			'subject': '',
			'img': null,
			'id': -1
		};
	
		vault.msgDelete(id, $scope.page, $rootScope.perpage, $rootScope.msgFilter);
	}
	
	$scope.renderHtml = function(html)
	{
		return $sce.trustAsHtml(html);
	};
	
	$scope.currentMessage = {
		'msg': '',
		'subject': '',
		'img': null,
		'id': -1
	};
		
	$scope.setCurrentMessage = function(msg) {		
		
		vault.msgSetParam('viewed', '1', msg.id, $scope.page, $rootScope.perpage, $rootScope.msgFilter);
		
		$('html, body').animate({
			scrollTop: ($('#message').offset().top)
		}, 50);
			
		
		var p = msg.img ? vault.getPreviews(msg.img, 'medium')[0] : null;
		
		$scope.currentMessage = {
			'msg': $scope.renderHtml(msg.msg),
			'subject': msg.subject,
			'img': p,
			'id': msg.id
		};		
	}
 });
	
 	// CATEGORY	
app.controller("categoryEditCtrl", function ($scope, $rootScope, $routeParams, vault) {
	vault.catGet();	
	vault.usersGetFilter();
		
	$rootScope.section = '/category';
		
	var id = $routeParams.id;
	$scope.catId = id;
	$scope.subCatEditID = id;
	$scope.level = {};
	
	$rootScope.addCrumb('Libraries', '#/category');
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
	
	$scope.checkGroup = function(find, groups) {
		var o = false;
		angular.forEach(groups, function(value, key) {
			if(value.id == find) {o = true;}
		});
		
		return o;
	}
	
	$scope.toggleEditor = function(id, user) {		
		vault.catToggleEditor(id, user);
	}
		
	$scope.togglePermission = function(id, grp) {		
		vault.catTogglePermission(id, grp);
	}
	
	$scope.removeEditor = function(id, user) {
		vault.catRemoveEditor(id, user);
	}
		
	$scope.catChangeDesc = function(id) {
		var n = prompt('Please enter description!', $rootScope.categories[id].desc);			
		
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
		name = '';
		if($rootScope.categories[id]) {name = $rootScope.categories[id].name};
		
		var n = prompt('Please enter the name!', name);			
		
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
	vault.catGet();
	vault.usersGetFilter();
	
	$rootScope.addCrumb('Libraries', '#/category');
	
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
	
	$scope.checkGroup = function(find, groups) {
		var o = false;
		angular.forEach(groups, function(value, key) {
			if(value.id == find) {o = true;}
		});
		
		return o;
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
	
	$scope.togglePermission = function(id, grp) {		
		vault.catTogglePermission(id, grp);
	}
});

	// SETTINGS
app.controller("settingsCtrl", function ($scope, $rootScope, vault) {
		
	$scope.show = 'tabGlobal';
	
	$rootScope.addCrumb('Settings', '');
	
	$scope.globalsPath = function() {
		var n = prompt('Please enter library path!', '');			
		
		if(!n || !n.length) {			
			vault.showMessage('Please enter library path!', 'warning');
		
			return false;
		}
		
		vault.globalsPath(n);		
	}
	
	$scope.globSetParam = function(param, value, def) {
		if(!value) {
			var value = prompt('Please enter value!', def);			
		
			if(!value || !value.length) {			
				vault.showMessage('Please enter the value!', 'warning');
			
				return false;
			}						
		}
		
		if(value.match(/[^a-zA-Z0-9-:()_ \!.\\]/)) {
			vault.showMessage('Wrong format!', 'warning');
			
			return false;
		}
		
		vault.globSetParam(param, value);
	}	
});


	// TOOLS
app.controller("toolsCtrl", function ($scope, $rootScope, vault) {
		
	vault.catGet();	
		
	$scope.show = 'tabBackups';
	
	$rootScope.addCrumb('Tools', '');
		
	
	$scope.tagsRefresh = function(t) {
		if(!confirm('Do you really want to refresh tags?\n\nWARNING!\nThis action will recalculate all tags for all models and textures!\nThis operation can be carried out within 20 minutes!')){
			return false;
		}		
		
		if(!confirm('After you click OK to begin the update. Do not close this page!')){
			return false;
		}
	
		vault.tagsRefresh(t);		
	}
	
	$scope.backupDatabase = function() {
		if(!confirm('Do you really want to create backup?')){
			return false;
		}		
			
		vault.backupDatabase();		
	}
	
	$scope.delBackup = function(file) {
		if(!confirm('Do you really want to delete backup "' + file + '"?')){
			return false;
		}		
			
		vault.delBackup(file);		
	}
	
	$scope.findIn = {}
	
	$scope.findMissingModels = function() {
		if(!$scope.findIn.id) {
			vault.showMessage('Please select library!', 'warning');
			return false;
		}
		
		vault.deleteMessage();
		
		$rootScope.loadingData = true;
		vault.findMissingModels($scope.findIn.id);
	}
	
	$scope.findMissingModelsPreview = function() {
				
		$rootScope.loadingData = true;
		vault.findMissingModelsPreview();
	}
	
	$scope.delMissingModelsPreview = function(miss) {
		if(!confirm('Do you really want to delete previews?')){
			return false;
		}
		
		if(!confirm('WARNING!\nThis action will delete this previews permanently!\Did you make a copy of the files?')){
			return false;
		}
		
		vault.delMissingModelsPreview(miss);
	}
		
	
	$scope.delMissingModel = function(path) {
		if(!confirm('Do you really want to delete this folder?')){
			return false;
		}
		
		if(!confirm('WARNING!\nThis action will delete this folder permanently!\Did you make a copy of the files?')){
			return false;
		}
		
		vault.delMissingModel(path);
	}
		
	$scope.selectFindLib = function(id, name) {
		$scope.findIn.id = id;
		$scope.findIn.name = name;
	}
	
	$rootScope.loadingData = false;
	
	
	vault.getBackupList();
	vault.getGlobal();
});


// AUTO RUN
app.run(function($rootScope, $location, $routeParams, vault, $timeout) {
	

	$rootScope.download = '';
	$rootScope.downloadUrl = function(id, type) {
		if(!confirm('Do you really want download?')){
			return false;
		}
	
		$rootScope.download = hostname + 'vault/download.php?id=' + id +'&type=' + type;
		console.log($rootScope.download);
		
		$timeout(function() {			
			$rootScope.$apply(function(){$rootScope.download = ''});
			
		}, 1000);
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
			case 'MODELBAD': alert('Wrong item!');
			break;
			case 'MODELNOTEXIST': alert('Item not exist!');
			break;
			case 'NORIGHTS': alert('You have no access!');
			break;
			case 'ITEMLBAD': alert('You have no access!');
			break;
			case 'ITEMNOTEXIST': alert('Sorry, this item not exist!');
			break;
		}
				
		$rootScope.download = '';		
	}
	
    $rootScope.$watch(function() { 
        return $location.path(); 
    },
     function(a, b){
		 
		$rootScope.section = a;
			
		// INIT
				
		
		$rootScope.showOverlayMenu = false;
		$rootScope.toggleOverlayMenu = function(){
			$rootScope.showOverlayMenu = !$rootScope.showOverlayMenu;
		}
		
		$rootScope.rnd = function(min, max) {
			return Math.floor(Math.random() * (max - min) ) + min;
		}
			
		$rootScope.months = {1: "Jan", 2:"Feb", 3: "Mar", 4: "Apr", 5: "May", 6: "Jun", 7:"Jul", 8: 'Aug', 9: 'Sep', 10: 'Oct', 11: 'Nov', 12: 'Dec'};
		
		$rootScope.tm = function(time) {return vault.tm(time)};
		
		$rootScope.goLogin = function() {
			$location.path("/login");
		}
		
		$rootScope.goHome = function() {
			$rootScope.oldLocation = window.location;
			window.location = hostname + 'login/';
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
		$rootScope.donwloadLog = {};
		
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
		
		// IMAGES
		
		$rootScope.getMainPreview = function(p, size) {
			var a = vault.getPreviews(p, size);
			return a[0];
		}
		
		// MESSAGES
		$rootScope.msgCnt = 0;
		vault.getMsgCnt();
		$rootScope.messages = [];
		
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
		
		vault.getGlobal();
    });
});

// SERVICES

app.service('vault', function($http, $rootScope, $timeout, $interval, $templateCache, $cookieStore, $window, $sce) {
	
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
			case 'TAGSEXIST': s.error = 'The same tag already exist! Enter another tag name!';
			break;
			case 'TAGSEXIST': s.error = 'The same tag already exist! Enter another tag name!';
			break;
			case 'TAGSREFRESHOK': s.success = 'Tags updated!';
			break;
			case 'TAGSREFRESHBAD': s.error = 'Error when updating tags!';
			break;
			case 'EMAILOK': s.success = 'Email send!';
			break;
			case 'EMAILERROR': s.error = 'An error occurred while sending email!';
			break;
			case 'EMAILNOUSERS': s.warning = 'You can\'t send email for selected users!';
			break;
			case 'GROUPSBAD': s.warning = 'Error while get groups!';
			break;
			case 'GROUPADDBAD': s.error = 'Error whire add group!';
			break;
			case 'GROUPSEXIST': s.error = 'Group already exist!';
			break;			
			case 'GROUPRENOK': s.success = 'Group renamed success!';
			break;
			case 'GROUPRENBAD': s.error = 'Error while renaming group!';
			break;
			case 'BACKUPERROR': s.error = 'Error while creating backup!';
			break;
			case 'BACKUPEXIST': s.warning = 'Backup already exist!';
			break;
			case 'BACKUPOK': s.success = 'Success! Backup created!';
			break;
			case 'BACKUPDELBAD': s.error = 'Error while deleting backup!';
			break;
			case 'BACKUPDELOK': s.success = 'Success! Backup deleted!';
			break;
			case 'MOVEBAD': s.error = 'Error! While moving the item!';
			break;
			case 'MOVEOK': s.success = 'Success! Item moved!';
			break;	
			case 'MISSINGDELBAD': s.error = 'Error while deleting folder!';
			break;
			case 'MISSINGDELOK': s.success = 'Success! Folder removed!';
			break;
			case 'MISSDELBAD': s.error = 'Error while deleting previews!';
			break;
			case 'MISSDELOK': s.success = 'Success! Previews removed!';
			break;			
		}
		
		$rootScope.msg = s;
	}
	
	// SIMPLIFY POST PROCEDURE
	var HttpPost = function(query, json) {			
		return $http({
			url: hostname + 'admin/vault/handle.php?query=' + query + '&time=' + new Date().getTime(),
			method: "POST",
			data: json
		}).then(function(r) {					
				console.log(r.data);
				if(r.data.responce == 'RESTRICTED') {$rootScope.goHome(); return false;}
				return r;
			}			
		);
	}
	
	var httpGet = function(query) {		
		return $http.get(hostname + 'admin/vault/handle.php?query=' + query + '&time=' + new Date().getTime());
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
				$rootScope.goHome();
			}, 300);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	// ADMIN
	
	var usersGetGroups = function() {
		var json = {'type': 'all'};
		
		HttpPost('GETGROUPS', json).then(function(r){						
			
			$rootScope.userFilterList.grp = r.data;
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var usersAddGroup = function(name) {
		var json = {'name': name};
		
		HttpPost('GROUPADD', json).then(function(r){						
		
			usersGetGroups();
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var usersToggleGroup = function(userid, groupid) {
		var json = {'userid': userid, 'groupid': groupid};
		
		HttpPost('GROUPTOGGLE', json).then(function(r){						
		
			//usersGet(1, 100000, null);
			
			usersInfo(userid);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var usersRenameGroup = function(id, name) {
		var json = {'id': id, 'name': name};
		
		HttpPost('GROUPRENAME', json).then(function(r){						
		
			usersGetGroups();
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var usersDelGroup = function(id, name) {
		var json = {'id': id, 'name': name};
		
		HttpPost('GROUPDEL', json).then(function(r){						
		
			usersGetGroups();
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	
	var catGet = function(parentid) {
		if(parentid == null) {parentid = 0;}
		
		var json = {'parentid': parentid};
		
		HttpPost('CATGET', json).then(function(r){						
			
			$rootScope.categories = r.data;
			usersGetFilter();
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
			
			$rootScope.products = r.data;						
			if(r.data.products) {
				
				if(r.data.pending > 0 && $rootScope.auth.rights == 2) {showMessage('Please check pending models!', 'warning')}
				$rootScope.product = r.data.products[0]
			};			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var tagsGet = function(page, perpage, filter) {
			
		var json = {'page': page, 'perpage': perpage, 'filter': filter};
		
		HttpPost('TAGSGET', json).then(function(r){						
			
			$rootScope.tagsList = r.data;									
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var commentsGet = function(page, perpage, filter) {
			
		var json = {'page': page, 'perpage': perpage, 'filter': filter};
		
		HttpPost('COMMENTSGET', json).then(function(r){						
			
			$rootScope.commentsList = r.data;									
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var commentDelete = function(id, page, perpage, filter) {
		var json = {'id': id};
		
		HttpPost('COMMENTSDEL', json).then(function(r){						
			
			
			commentsGet(page, perpage, filter);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var downloadLogGet = function(page, perpage, filter) {
			
		var json = {'page': page, 'perpage': perpage, 'filter': filter};
		
		HttpPost('DOWNLOADLOGGET', json).then(function(r){						
			
			$rootScope.donwloadLog = r.data;									
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var tagDelete = function(t, page, perpage, filter) {
		var json = {'tag': t};
		
		HttpPost('TAGSDEL', json).then(function(r){						
			
			
			tagsGet(page, perpage, filter);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var tagChange = function(oldtag, newtag, page, perpage, filter) {
		var json = {'tag': oldtag, 'newtag': newtag};
		
		HttpPost('TAGSCHANGE', json).then(function(r){						
						
			tagsGet(page, perpage, filter);
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var usersGet = function(page, perpage, filter) {
			
		var json = {'page': page, 'perpage': perpage, 'filter': filter};
		
		HttpPost('USERSGET', json).then(function(r){						
			$rootScope.users = r.data;	
			
			usersGetFilter();
						
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var usersInfo = function(id) {
			
		var json = {'id': id};
		
		HttpPost('USERISINFO', json).then(function(r){						
			
			$rootScope.theUser = r.data;									
			
			if($rootScope.users && r.data.info) {					
				angular.forEach($rootScope.users.users, function(value, key) {
					if(value.id == r.data.info.id) {
						$rootScope.users.users[key] = r.data.info;
					}
				});
			}
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var usersSetParam = function(param, value, id) {
		var json = {'param': param, 'value': value, 'id': id};
		
		HttpPost('USERSETPARAM', json).then(function(r){												
			
			responceMessage(r.data);			
			usersInfo(id);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var usersGetFilter = function(){
		var json = {};
		
		HttpPost('USERGETFILTER', json).then(function(r){												
			
			$rootScope.userFilterList = r.data.filter;
			responceMessage(r.data);						
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var productInfo = function(type, id) {
			
		var filter = $cookieStore.get('modelFilter');	
		var json = {'type': type, 'id': id, 'filter': filter};
		
		HttpPost('PRODINFO', json).then(function(r){						
			
			$rootScope.product = r.data;									
			if(r.data.info) {
				$rootScope.product.previews = getPreviews(r.data.info.previews, 'huge');				
				$rootScope.product.previewNames = r.data.info.previews.split(';');	
				if(r.data.info.overview) {$rootScope.product.info.overview = r.data.info.overview.split('|').join('\n').split('\\n').join('\n');}
				
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
			
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var globSetParam = function(param, value) {
		
		var json = {'param': param, 'value': value};
		
		HttpPost('GLOBSETPARAM', json).then(function(r){															
			getGlobal();	
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var prodToggleParam = function(param, id, type) {
		var json = {'param': param, 'id': id, 'type': type};
		
		HttpPost('PRODTOGGLEPARAM', json).then(function(r){												
			
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var prodSetTextParam = function(param, value, id, type) {
		var json = {'param': param, 'value': value, 'id': id, 'type': type};
		
		HttpPost('PRODSETTEXTPARAM', json).then(function(r){												
			
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
		
	
	var productChangeName = function(name, oldname, id, catid, type) {
		var json = {'name': name, 'id': id, 'type': type, 'catid': catid, 'oldname': oldname};
		
		HttpPost('PRODSETNAME', json).then(function(r){												
			
			responceMessage(r.data);
			productInfo(type, id);		
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var moveProduct = function(id, cid, type)	 {
		var json = {'id': id, 'cid': cid, 'type': type};
		
		HttpPost('PRODMOVE', json).then(function(r){												

			console.log(r.data);
			responceMessage(r.data);
			
			if(r.data.responce == 'MOVEEXIST') {
				
				m = 'Item already exist in destination category! Please rename this item! ';
				m += r.data.url;
				showMessage(m, 'warning');
			}
		
			$rootScope.moveProductActive = false;
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
	
	
	var removeWebGLModel = function(id, type) {
		var json = {'id': id, 'type': type};
		
		HttpPost('PRODREMOVEWEBGL', json).then(function(r){												
			
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
			
			responceMessage(r.data);
			productsGet(page, perpage, type, filter)		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var prodDeleteFromEdit = function(id, type) {
		var json = {'id': id, 'type': type};
		
		HttpPost('PRODDELETE', json).then(function(r){												
			
			responceMessage(r.data);
			
			setTimeout(function() {
				$window.history.back();
			},100);
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
			console.log(r);
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
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var catToggleEditor = function(id, user) {
		var json = {'id': id, 'user': user};
		
		HttpPost('CATTOGGLEEDITOR', json).then(function(r){						
			catGet();
						
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var catRemoveEditor = function(id, user) {
		var json = {'id': id, 'user': user};
		
		HttpPost('CATDELDITOR', json).then(function(r){						
			catGet();
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var catTogglePermission = function(id, grp) {
		var json = {'id': id, 'grp': grp};
		
		HttpPost('CATTOGGLEGRP', json).then(function(r){						
			catGet();
						
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
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
		
	var catSetParam = function(param, value, id) {
		var json = {'param': param, 'value': value, 'id': id};
		
		HttpPost('CATSETPARAM', json).then(function(r){									
			
			catGet();		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var globalsPath = function(path) {
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

	var tagsRefresh = function(t) {
					
		var json = {'type': t};
		
		HttpPost('TAGSREFRESH', json).then(function(r){									
			getGlobal();
			
				
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var getBackupList = function() {
		httpGet('GETBACKUPLIST').then(function(r){									
			$rootScope.backupList = r.data;
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var delBackup = function(file) {
		
		var json = {'file': file};
		
		HttpPost('DELETEBACKUP', json).then(function(r){									
			getGlobal();
			getBackupList();
				
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var backupDatabase = function() {
		
		var json = {'backup': 'yes'};
		
		HttpPost('ADMINBACKUPDB', json).then(function(r){									
			getGlobal();
			getBackupList();
				
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}	
	
	var delMissingModel = function(path) {
		var json = {'path': path};
		
		$rootScope.loadingDataDel = true;
		
		HttpPost('ADMINDELETEMISSING', json).then(function(r){									
						
			$rootScope.loadingDataDel = false;
							
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var findMissingModels = function(id) {
		var json = {'id': id};
		
		HttpPost('ADMINBFINDMISSING', json).then(function(r){									
			
			
			$rootScope.loadingData = false;
			$rootScope.missingModels = r.data;
					
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var findMissingModelsPreview = function(id) {
		var json = {'id': id};
		
		HttpPost('ADMINBFINDMISSINGPREVIEW', json).then(function(r){									
			
			
			$rootScope.loadingData = false;
			$rootScope.missingModelsPreview = r.data;
					
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var delMissingModelsPreview = function(miss) {
		var json = {'miss': miss};
		
		HttpPost('ADMINDELMISSINGPREVIEW', json).then(function(r){									
						
			$rootScope.missingModelsPreview = {};
								
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
			
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var sendEmail = function(data) {
					
		var json = data;
		
		HttpPost('SENDEMAIL', json).then(function(r){									
				
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
			
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	
	var getMsgCnt = function() {		
		var json = {'get': 'cnt'};
		
		HttpPost('MSGGETCNT', json).then(function(r){												
			
			
			$rootScope.msgCnt = r.data;
			responceMessage(r.data);			
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var msgGet = function(page, perpage, filter) {
		var json = {'page': page, 'perpage': perpage, 'filter': filter};
		
		HttpPost('MSGGET', json).then(function(r){									
			$rootScope.messages = r.data;						
			
			if(r.data.notview != undefined) {
				$rootScope.msgCnt['cnt'] = r.data.notview;
			}
				
			
			responceMessage(r.data);
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var msgSetParam = function(param, value, id, page, perpage, filter)
	{
		var json = {'param': param, 'value': value, 'id': id};
		
		HttpPost('MSGSETPARAM', json).then(function(r){															
			msgGet(page, perpage, filter);						
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var msgDelete = function(id, page, perpage, filter) {
		var json = {'id': id};
			
		HttpPost('MSGDELETE', json).then(function(r){												
			
			responceMessage(r.data);
			msgGet(page, perpage, filter)		
		},
		function(r){
			responceMessage(r);
		});
	}
	
	var sendCommandMXS = function(cmd, value) {
		if(!value) {value = '';}
		window.external.text = cmd + '=' + value + '#' + new Date().getTime();
	}
		
	var getDashBoardInfo = function() {
		var json = {'info': 'all'};
			
		HttpPost('DASHBOARDINFO', json).then(function(r){												
			$rootScope.dashBoardInfo = r.data;
			$rootScope.graphMotnData = [];
			var d = [];
			var l = [];
			if(r.data.graph_month) {
				
				angular.forEach(r.data.graph_month, function(item, key) {				
					d.push(item.cnt);
					l.push($rootScope.months[item.month]);
				});								
			}
			
			$rootScope.dataMonthDownload = [d];
			$rootScope.labelsMonthDownload = l;
								
			var d3 = [];
			var l3 = [];
			var c3 = [];
			if(r.data.graph_user) {				
				angular.forEach(r.data.graph_user, function(item, key) {															
					var dd = {};
					var r = $rootScope.rnd(0, 200);
					var g = $rootScope.rnd(0, 200);
					var b = $rootScope.rnd(0, 200);
					c3.push('rgba(' + r + ',' + g +  ',' + b +  ',0.5)');
					
					d3.push(item.dwl);
					l3.push(item.user);					
				});								
			}
						
			$rootScope.dataUserDownload = d3;
			$rootScope.labelsUserDownload = l3;
			//$rootScope.labelsUserColors = c3;
			
			
			var d4 = [];
			var l4 = [];
			var ll4 = [];
			var lll4 = [];
			
			
			if(r.data.graph_lib) {				
				angular.forEach(r.data.graph_lib, function(item, key) {																									
					d4.push(item.size);
					l4.push(item.name + ' (%)');					
					lll4.push(item.name);					
					ll4.push(item.disc_size);					
				});								
			}			
			
			$rootScope.dataSize = d4;
			$rootScope.labelsSize = l4;
			$rootScope.labelsSizeDisc = ll4;
			$rootScope.labelsSizeNames = lll4;
			
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
		catToggleEditor: catToggleEditor,
		catRemoveEditor: catRemoveEditor,
		catAdd: catAdd,
		catTogglePermission: catTogglePermission,
		getGlobal: getGlobal,
		globalsPath: globalsPath,
		globSetParam: globSetParam,
		catSetParam: catSetParam,
		catChangeName: catChangeName,
		catSort: catSort,
		tagsGet: tagsGet,
		tagDelete: tagDelete,
		tagChange: tagChange,
		productsGet: productsGet,
		productInfo: productInfo,
		prodSetParam: prodSetParam, 
		prodToggleParam: prodToggleParam,
		prodSetTextParam: prodSetTextParam,
		moveProduct: moveProduct,
		setMainPreview: setMainPreview,
		removePreview: removePreview,
		removeTag: removeTag,
		prodDelete: prodDelete,
		prodDeleteFromEdit: prodDeleteFromEdit,
		addTag: addTag,
		findMissingModels: findMissingModels,
		findMissingModelsPreview: findMissingModelsPreview,
		delMissingModelsPreview: delMissingModelsPreview,
		delMissingModel: delMissingModel,
		productChangeName: productChangeName,
		productChangeOverview: productChangeOverview,
		getPreviews: getPreviews,
		usersGet: usersGet,
		usersSetParam: usersSetParam,
		usersGetFilter: usersGetFilter,
		tagsRefresh: tagsRefresh,
		getMsgCnt: getMsgCnt,
		msgGet: msgGet,
		msgSetParam: msgSetParam,
		msgDelete: msgDelete,
		sendCommandMXS: sendCommandMXS,
		getDashBoardInfo: getDashBoardInfo,
		downloadLogGet: downloadLogGet,
		commentsGet: commentsGet,
		commentDelete: commentDelete,
		sendEmail: sendEmail,
		usersAddGroup: usersAddGroup,
		usersGetGroups: usersGetGroups,
		usersDelGroup: usersDelGroup,
		usersRenameGroup: usersRenameGroup,
		usersToggleGroup: usersToggleGroup,
		removeWebGLModel: removeWebGLModel,
		getBackupList: getBackupList,
		backupDatabase: backupDatabase,
		delBackup: delBackup,
		tm: tm
	};
});


