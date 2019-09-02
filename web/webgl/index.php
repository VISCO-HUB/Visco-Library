<?php

$ITEM = $_GET['item'];

$DIR = '/interactive/' . $ITEM . '/';
$OBJ = 'index.obj';
$MTL = 'index.mtl';

FUNCTION E($S) {ECHO "'" . $S . "'";}

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>WebGL Player</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			
			body, html {
				font-family: Verdana;
				/*background-color: #FFF;*/
				background-color: #353535;
				color: #fff;
				margin: 0px;
				padding: 0px;
				overflow: hidden;				
				user-select: none;	
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif";
			}
			.hide {
				opacity: 0.0;
				transition: all 1.3s linear;
			}
			#loading {
				position: absolute;
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				width: 100%;
				height: 100%;
				display: block;
				/*background-color: #FFF;	*/
				background-color: #222;	
				transition: all 1.3s linear;
				/*background-image: url('/img/loading.gif');*/
				background-position: 50% 50%;
				background-repeat: no-repeat;
				z-index: 1000;
			}
			#loading-text {
				color: #7f7f7f;
				position: absolute;
				top: 50%;
				left: 0;
				width: 100%;
				transform: translateY(-50%);
				text-align: center;
				z-index: 100;
				display:block;
				font-weight: bold;
				font-size: 28px;
			}
			
			#info {
				position: absolute;
				width: 500px;
				right: 0px;
				top: 40px;
				color:#000;
				padding: 5px;
				opacity: 0.9;
				z-index: 500;
			}
			#player {
				width: 100%;
				height: 100%;
				top: 0;
				left: 0;

			}
			#container {
				width: 100%;
				height: 100vh;
				position: relative;
				overflow: hidden;
				z-index: 0;
			}
			
			.dot1 {
				animation: dot-animate 1.5s ease-out 1s alternate infinite none running;
			}
			
			.dot2 {
				animation: dot-animate 1s ease-out 2s alternate infinite none running;
			}
			
			.dot3 {
				animation: dot-animate 0.5s ease-out 3s alternate infinite none running;
			}
			
			@keyframes dot-animate {
				from {color: #7F7F7F;}
				to {color: #FFF;}
			}
			
			#measuringData {
				position: absolute;
				display: block;
				min-width: 130px;
				right: 0;
				top: 150px;
				margin-top: 10px;
				padding: 5px;
				z-index: 500;
			}
			
			.btn {
				position: absolute;
				z-index: 500;
				left: 0;
				background-position: 50% 50% !important;
				background-repeat: no-repeat !important;				
				cursor: pointer;
				outline: none;
				height: 35px;
				width: 35px;
				content: '';
				border: none;
				margin: 20px;
			}
			
			#measureButton {								
				top: 0;
				background: url(/img/ruler.svg);
				background-size: 77%;	
				background-color: #337AB7;
				border: 1px solid #2E6DA4;
			}
			
			#measureButton:hover {
				background-color: #286090;
				border: 1px solid #204D74;
			}
			
			#measureButton.active {
				background-color: #5CB85C;
				border: 1px solid #4CAE4C;
			}
			
			#measureErase {			
				background: url(/img/erase.svg);
				background-size: 60%;	
				background-color: #D9534F;
				border: 1px solid #D43F3A;
				cursor: pointer;
				display: inline-block;
				outline: none;
				height: 34px;
				width: 34px;
				background-position: 50% 50% !important;
				background-repeat: no-repeat !important;
				float: left;
				box-sizing: border-box;
				position: fixed;
			}
						
			#measureErase:hover {
				background-color: #C9302C;
				border: 1px solid #AC2925;
			}
			
			#measureText {
				position: absolute;
				z-index: 500;
				transform: translate(-50%, -50%);							
				display: none;	
				margin-left: -17px;
			}
			
			#measureText label {
				    color: #ffffff;
					text-shadow: 1px 1px #000;
					font-size: 18px;
					padding: 0 5px;
					min-height: 34px;
					vertical-align: middle;
					display: inline-block;
					line-height: 32px;
					float: left;
					box-sizing: border-box;
			}
			
			#pointOne, #pointTwo {
				border-radius: 50%;
				position: absolute;
				transform: translate(-50%, -50%);
				display: block;
				content: '';
				height: 10px;
				width: 10px;
				z-index: 400;
			}
			
			#pointLine {
				z-index: 300;
				position: absolute;
				height: 1px;
				content: '';
				left: -100px;
				top: -100px;
			}
			
			#pointOne, 
			#pointTwo, 
			#measureText label,
			#pointLine,
			#measuringData {
				/*background-color: rgba(128, 128, 128, 0.26);
				box-shadow: 1px 1px rgba(0,0,0, 0.6);*/						
				/*box-shadow: 1px 1px rgba(214, 100, 42, 1.0);*/
				background-color: rgba(255, 206, 35, 1.0);
				border: 1px solid #826600;				
			}
			
			#pointOne, 
			#pointTwo, 
			#measureText {
				top: -100px;
				left: -100px;
			}
			
			#mode {
				z-index: 499;
				position: absolute;
				text-align: center;
				display: none;
				margin: auto 0;
				left: 0;
				right: 0;
				color: #5cb85c;
				font-size: 24px;
				text-shadow: 0px 1px #000;
				margin: 20px;
				padding: 3px;
			}
			
		</style>
	</head>

	<body>
		<button id="measureButton" class="btn" onClick="app.toggleMeasure(this)"></button>
		<div id="mode">Measure Mode ON</div>
		<div id="pointOne"></div>
		<div id="pointTwo"></div>
		<div id="pointLine"></div>
		 <div id="measuringData" style="display: none">
                <label><b>Length:</b> <span id="length"></span></label><br>
                <button id="deletePoint" onclick="app.deleteAllPoints()">Clear</button>
            </div>
		<div id="measureText"><label id="measureTextLabel"></label><button id="measureErase" onClick="app.deleteAllPoints(this)"></button></div>
		
		<div id="container">
			<canvas id="player" width="900" height="1045"></canvas>
		</div>
		<?php IF(!ISSET($_GET['item'])) DIE('<div id="loading-text">Access Denied!</div>'); ?>	
		<?php IF(!FILE_EXISTS( $_SERVER['DOCUMENT_ROOT'] . $DIR . $OBJ) OR !FILE_EXISTS($_SERVER['DOCUMENT_ROOT'] . $DIR . $MTL)) DIE('<div id="loading-text">File not found!</div>'); ?>		
				
		<div id="loading"><div id="loading-text">Opening<span class="dot1">.</span><span class="dot2">.</span><span class="dot3">.</span>
		</div></div>
		
		
		<script src="./js/Detector.js"></script>
		<script src="./js/three.js"></script>
		<script src="./js/TrackballControls.js"></script>
		<script src="./js/OrbitControls.js"></script>			
		<script src="./js/MTLLoader.js"></script>
		<script src="./js/OBJLoader2.js"></script>		
		<script src="./js/Projector.js"></script>
		<script src="./js/measuring.js"></script>
		
		
		<script>

			'use strict';
		
			var _OBJLOADER = (function () {

				function _OBJLOADER( elementToBindTo ) {
					this.renderer = null;
					this.canvas = elementToBindTo;
					this.wrapper = document.getElementById('container');
					this.aspectRatio = 1;
					this.recalcAspectRatio();

					this.scene = null;
					this.cameraDefaults = {
						posCamera: new THREE.Vector3( 0.0, 175.0, 500.0 ),
						posCameraTarget: new THREE.Vector3( 0, 0, 0 ),
						near: 0.1,
						far: 10000,
						fov: 45
					};
					this.camera = null;
					this.cameraTarget = this.cameraDefaults.posCameraTarget;

					this.controls = null;

					this.smoothShading = true;
					this.doubleSide = false;
					
					this.pivot = null;
					this.measuring = null;
				}

				
				
				_OBJLOADER.prototype.initGL = function () {
					this.renderer = new THREE.WebGLRenderer( {
						canvas: this.canvas,
						antialias: true,
						autoClear: true
					} );
					this.renderer.setClearColor( 0x353535  );
					this.renderer.setPixelRatio( window.devicePixelRatio );

					this.scene = new THREE.Scene();
						
					
					this.camera = new THREE.PerspectiveCamera( this.cameraDefaults.fov, this.aspectRatio, this.cameraDefaults.near, this.cameraDefaults.far );
					this.resetCamera();
					//this.controls = new THREE.TrackballControls( this.camera, this.renderer.domElement );
					
					this.controls = new THREE.OrbitControls( this.camera, this.renderer.domElement );
					this.controls.target.set( 0, 0, 0 );
					this.camera.position.set( 2, 18, 28 );
					this.controls.update();

					var ambientLight = new THREE.AmbientLight( 0x404040 );
					var directionalLight1 = new THREE.DirectionalLight( 0xC0C090 );
					var directionalLight2 = new THREE.DirectionalLight( 0xC0C090 );

					var ambientLight = new THREE.AmbientLight( 0x404040 );
					var directionalLight1 = new THREE.DirectionalLight( 0xC0C090 );
					var directionalLight2 = new THREE.DirectionalLight( 0xC0C090 );
					var hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.0);	
									

					directionalLight1.position.set( -100, 50, 100 );
					directionalLight2.position.set( 100, 50, -100 );
					hemisphereLight.position.set( 0, 1, 0 );

					this.scene.add( directionalLight1 );
					this.scene.add( directionalLight2 );
					this.scene.add( ambientLight );
					this.scene.add( hemisphereLight );

					/*var helper = new THREE.GridHelper( 1200, 60, 0xFF4444, 0x404040 );
					this.scene.add( helper );*/

					var geometry = new THREE.BoxGeometry( 10, 10, 10 );
					var material = new THREE.MeshNormalMaterial();
													

					this.pivot = new THREE.Object3D();
					this.pivot.name = 'Pivot';
					this.scene.add( this.pivot );
				}

				_OBJLOADER.prototype.getBoundingSphere = function() {
					var helper = new THREE.BoxHelper(this.pivot);
					helper.geometry.computeBoundingSphere();
					return helper.geometry.boundingSphere;
				}
				
				_OBJLOADER.prototype.toggleMeasure = function(el) {
					this.measuring.showHideMeasuring(el);
				}
				
				_OBJLOADER.prototype.deleteLastPoint = function() {
					this.measuring.deleteLastPoint();
				}
				
				_OBJLOADER.prototype.deleteAllPoints = function() {
					this.measuring.deleteAllPoints();	
				}
				
				_OBJLOADER.prototype.measuringAddObjects = function() {
					var object = this.pivot;
					var scope = this;
					
					object.traverse (function (mesh)
					{	
						if ( mesh instanceof THREE.Mesh ) {	
							scope.measuring.targetObjects.push(mesh);
						}
					});
				}
				
				_OBJLOADER.prototype.getBBox = function() {					
					var object = this.pivot;
					
					var minX = 0;
					var minY = 0;
					var minZ = 0;
					var maxX = 0;
					var maxY = 0;
					var maxZ = 0;
					
					object.traverse (function (mesh)
					{	
						if ( mesh instanceof THREE.Mesh ) {					
														
							mesh.geometry.computeBoundingBox ();
							var bBox = mesh.geometry.boundingBox;
							
							minX = Math.min (minX, bBox.min.x);
							minY = Math.min (minY, bBox.min.y);
							minZ = Math.min (minZ, bBox.min.z);
							maxX = Math.max (maxX, bBox.max.x);
							maxY = Math.max (maxY, bBox.max.y);
							maxZ = Math.max (maxZ, bBox.max.z);	
						}						
					});
				
					var bBox_min = new THREE.Vector3 (minX, minY, minZ);
					var bBox_max = new THREE.Vector3 (maxX, maxY, maxZ);
					var bBox_new = new THREE.Box3 (bBox_min, bBox_max);
					
					return bBox_new;
				}
				
				_OBJLOADER.prototype.createGrid = function () {
						
					var boundingSphere = this.getBoundingSphere();
				
					var center = boundingSphere.center;
					var radius = Math.ceil(boundingSphere.radius);
					
					var helperGrid = new THREE.GridHelper( radius * 2, 10, 0xb93e3e, 0x494545 );					
					this.scene.add( helperGrid );										
				}
						
				
				_OBJLOADER.prototype.zoomCamera = function (camera) {
										
					var boundingSphere = this.getBoundingSphere();
					var center = boundingSphere.center;
					var radius = boundingSphere.radius;
					
					var x = -radius; 
					var y = radius / 3 ;
					var z = radius;
										
					camera.position.set(x * 2 , y * 2  , z * 2);	

					var bBox = this.getBBox()
					var h = bBox.max.y / 4;
					console.log(bBox);
									
					this.controls.target.set(0, h, 0);				
				};
				
				_OBJLOADER.prototype.initPostGL = function ( objDef ) {
					var scope = this;

					var mtlLoader = new THREE.MTLLoader();
					mtlLoader.setPath( objDef.texturePath );
					mtlLoader.setCrossOrigin( 'anonymous' );					
					mtlLoader.load( objDef.fileMtl, function( materials ) {

						materials.preload();

						var objLoader = new THREE.OBJLoader2();
						objLoader.setSceneGraphBaseNode( scope.pivot );
						objLoader.setMaterials( materials.materials );
						objLoader.setPath( objDef.path );
						objLoader.setDebug( false, false );

						var onSuccess = function ( object3d ) {
							console.log( 'Loading complete. Meshes were attached to: ' + object3d.name );

							scope.measuring = new Measuring(scope.scene, scope.camera);
							
							scope.measuringAddObjects();
							
							scope.createGrid();
							scope.zoomCamera(scope.camera);
													
							
							var el = document.getElementById('loading');								
							el.classList.add('hide');						
							setTimeout(function() {
								el.style.display = 'none';
							}, 1000);	

							scope.controls.update();						
						};

						var onProgress = function ( event ) {
							if ( event.lengthComputable ) {

								var percentComplete = event.loaded / event.total * 100;
								var output = 'Download of "' + objDef.fileObj + '": ' + Math.round( percentComplete ) + '%';
								console.log(output);

							}
						};

						var onError = function ( event ) {
							console.error( 'Error of type "' + event.type + '" occurred when trying to load: ' + event.src );
						};

						objLoader.load( objDef.fileObj, onSuccess, onProgress, onError );

					});

					return true;
				};

				_OBJLOADER.prototype.resizeDisplayGL = function () {
					
					this.recalcAspectRatio();
					this.renderer.setSize( this.canvas.offsetWidth, this.canvas.offsetHeight, false );

					this.updateCamera();
				};

				_OBJLOADER.prototype.recalcAspectRatio = function () {
					this.aspectRatio = ( this.canvas.offsetHeight === 0 ) ? 1 : this.canvas.offsetWidth / this.canvas.offsetHeight;
				};

				_OBJLOADER.prototype.resetCamera = function () {
					this.camera.position.copy( this.cameraDefaults.posCamera );
					this.cameraTarget.copy( this.cameraDefaults.posCameraTarget );

					this.updateCamera();
				};

				_OBJLOADER.prototype.updateCamera = function () {
					this.camera.aspect = this.aspectRatio;
					this.camera.lookAt( this.cameraTarget );
					this.camera.updateProjectionMatrix();
				};

				_OBJLOADER.prototype.render = function () {
					if ( ! this.renderer.autoClear ) this.renderer.clear();

					if(this.measuring) {
						this.measuring.processIntersections();						
					}
										
					//this.controls.update();
					
					this.renderer.render( this.scene, this.camera );					
				};

				_OBJLOADER.prototype.alterSmoothShading = function () {

					var scope = this;
					scope.smoothShading = ! scope.smoothShading;
					console.log( scope.smoothShading ? 'Enabling SmoothShading' : 'Enabling FlatShading');

					scope.traversalFunction = function ( material ) {
						material.shading = scope.smoothShading ? THREE.SmoothShading : THREE.FlatShading;
						material.needsUpdate = true;
					};
					var scopeTraverse = function ( object3d ) {
						scope.traverseScene( object3d );
					};
					scope.pivot.traverse( scopeTraverse );
				};

				_OBJLOADER.prototype.alterDouble = function () {

					var scope = this;
					scope.doubleSide = ! scope.doubleSide;
					console.log( scope.doubleSide ? 'Enabling DoubleSide materials' : 'Enabling FrontSide materials');

					scope.traversalFunction  = function ( material ) {
						material.side = scope.doubleSide ? THREE.DoubleSide : THREE.FrontSide;
					};

					var scopeTraverse = function ( object3d ) {
						scope.traverseScene( object3d );
					};
					scope.pivot.traverse( scopeTraverse );
				};

				_OBJLOADER.prototype.traverseScene = function ( object3d ) {

					if ( object3d.material instanceof THREE.MultiMaterial ) {

						var materials = object3d.material.materials;
						for ( var name in materials ) {

							if ( materials.hasOwnProperty( name ) )	this.traversalFunction( materials[ name ] );

						}

					} else if ( object3d.material ) {

						this.traversalFunction( object3d.material );

					}

				};

				return _OBJLOADER;

			})();
						
			var app = new _OBJLOADER( document.getElementById('player'));
					
			
			// init three.js example application
			var resizeWindow = function () {
				app.resizeDisplayGL();
			};

			var render = function () {
				requestAnimationFrame( render );
				app.render();
			};

			window.addEventListener( 'resize', resizeWindow, false );
					

			console.log( 'Starting initialisation phase...' );
			app.initGL();
			app.resizeDisplayGL();
			app.initPostGL( {
				path: <?php E($DIR) ?>,
				fileObj: <?php E($OBJ) ?>,
				texturePath: <?php E($DIR) ?>,
				fileMtl: <?php E($MTL) ?>
			} );

			render();
					
			
		</script>
	
<div></div></body>
	</body>
</html>
