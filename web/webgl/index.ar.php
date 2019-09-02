<?php

$ITEM = $_GET['item'];

$DIR = '/interactive/' . $ITEM . '/';
$OBJ = $DIR . 'index.obj';
$MTL = $DIR . 'index.mtl';

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>AR Player</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, user-scalable=no,
  minimum-scale=1.0, maximum-scale=1.0">
  <style>
    body {
      font-family: Verdana;
      margin: 0;
      overflow: hidden;
      position: fixed;
      width: 100%;
      height: 100vh;
      -webkit-user-select: none;
      user-select: none;
    }
	#webgl-error-message {
		z-index: 1000000000 !important;
		position: absolute;
		left: 0;
		right: 0;
		color: #FFF !important;
		background-color: #E69B18 !important;
		border: 4px solid #eabf7b !important;
		box-shadow: 0px 0px 13px 0px rgba(230,155,24,0.6) !important;
		font-family: Tahoma !important;
		padding: 15px;
		display: none;
		margin: 15px;
	}
	
	.change-scale {
		position: absolute;
		z-index: 100;
		bottom: 15px;
		left: 15px;
		width: 50px;
		height: 50px;				
		background-size: 50%;
		background-color: #337AB7;
		border: 1px solid #2E6DA4;
		background-position: center center;
		background-repeat: no-repeat;
	}
	
	.rotate-model {
		position: absolute;
		z-index: 100;
		bottom: 15px;
		right: 15px;
		width: 50px;
		height: 50px;				
		background-size: 70%;
		background-color: #337AB7;
		border: 1px solid #2E6DA4;
		background-position: center center;
		background-repeat: no-repeat;
	}
	
	.rotate-model.left {
		background-image: url(/img/rotate-left.svg);
	}
	
	.rotate-model.right {
		background-image: url(/img/rotate-right.svg);
	}
	
	.change-scale.plus {
		background-image: url(/img/plus.svg);
	}
	
	.change-scale.minus {
		background-image: url(/img/minus.svg);
	}
	
	.btn.active,
	.btn:focus	{
		background-color: #286090;
		border: 1px solid #204D74;
	}
	
    #info {
      position: absolute;
      left: 50%;
      bottom: 0;
      transform: translate(-50%, 0);
      margin: 1em;
      z-index: 10;
      display: block;
      width: 100%;
      line-height: 2em;
      text-align: center;
    }
    #info * {
      color: #fff;
    }
    .title {
      background-color: rgba(40, 40, 40, 0.4);
      padding: 0.4em 0.6em;
      border-radius: 0.1em;
    }
    .links {
      background-color: rgba(40, 40, 40, 0.6);
      padding: 0.4em 0.6em;
      border-radius: 0.1em;
    }
    canvas {
      position: absolute;
      top: 0;
      left: 0;
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
	
  </style>
</head>
<body>

<div id="loading"><div id="loading-text">Opening<span class="dot1">.</span><span class="dot2">.</span><span class="dot3">.</span>
</div></div>

<div id="webgl-error-message" style="font-family: monospace; font-size: 13px; font-weight: normal; text-align: center; max-width: 400px; margin: 5em auto 0px;">This augmented reality experience requires WebARonARCore or WebARonARKit, experimental browsers from Google
  for Android and iOS.<br><br> Download for Android: <a href="https://github.com/google-ar/WebARonARCore/blob/webarcore_57.0.2987.5/apk/WebARonARCore.apk?raw=true" target="_blank">download</a>.
   <br><br>
  <h2><a href="" id="gotowebar">Open in WebAR browser!</a></h2> 
 </div>
 
 <button class="btn change-scale minus" id="scale-down" ontouchstart="changeHoldState(event, true);changeScale(event, -0.005)" ontouchend="changeHoldState(event, false)">&nbsp;</button>  
 <button class="btn change-scale plus" id="scale-up" style="bottom: 65px;" ontouchstart="changeHoldState(event, true);changeScale(event, 0.005)" ontouchend="changeHoldState(event, false)">&nbsp;</button>  
 
 
  <button class="btn rotate-model right" id="rotate-right" style="right: 65px;" ontouchstart="changeHoldState(event, true);rotateModel(event, -0.05)" ontouchend="changeHoldState(event, false)">&nbsp;</button>  
  
   <button class="btn rotate-model left" id="rotate-left"  ontouchstart="changeHoldState(event, true);rotateModel(event, 0.05)" ontouchend="changeHoldState(event, false)">&nbsp;</button> 

<script src="./js/three.dist.ar.js"></script>
<script src="./js/VRControls.js"></script>
<script src="./js/MTLLoader.js"></script>		
<script src="./js/OBJLoader2.js"></script>
<script src="./js/three.ar.js?v=3"></script>
<script>

var vrDisplay;
var vrControls;
var arView;

var canvas;
var camera;
var scene;
var renderer;
var model;

var shadowMesh;
var planeGeometry;
var light;
var directionalLight;

var OBJ_PATH = '<?=$OBJ?>';
var MTL_PATH = '<?=$MTL?>';
var SCALE = 0.1;


THREE.ARUtils.getARDisplay().then(function (display) {
  if (display) {
    vrDisplay = display;
    init();
  } else {
    
	var el = document.getElementById('webgl-error-message');								
	
	setTimeout(function() {
		el.style.display = 'block';
	}, 1000);
		
	var el3 = document.getElementById('gotowebar');
		
	el3.href = 'webar://' + window.location.href;
  }
});

function init() {

  
  

  // Setup the three.js rendering environment
  renderer = new THREE.WebGLRenderer({ alpha: true });
  renderer.setPixelRatio(window.devicePixelRatio);
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.autoClear = false;
  canvas = renderer.domElement;
  document.body.appendChild(canvas);
  scene = new THREE.Scene();
  
    // Turn on the debugging panel
	//var arDebug = new THREE.ARDebug(vrDisplay, scene, {showPlanes: false});
	//document.body.appendChild(arDebug.getElement());

  // Creating the ARView, which is the object that handles
  // the rendering of the camera stream behind the three.js
  // scene
  arView = new THREE.ARView(vrDisplay, renderer);

  // The ARPerspectiveCamera is very similar to THREE.PerspectiveCamera,
  // except when using an AR-capable browser, the camera uses
  // the projection matrix provided from the device, so that the
  // perspective camera's depth planes and field of view matches
  // the physical camera on the device.
  camera = new THREE.ARPerspectiveCamera(
    vrDisplay,
    60,
    window.innerWidth / window.innerHeight,
    vrDisplay.depthNear,
    vrDisplay.depthFar
  );

  // VRControls is a utility from three.js that applies the device's
  // orientation/position to the perspective camera, keeping our
  // real world and virtual world in sync.
  vrControls = new THREE.VRControls(camera);

  // For shadows to work
	renderer.shadowMap.enabled = true;
	renderer.shadowMap.type = THREE.PCFSoftShadowMap;

	var ambientLight = new THREE.AmbientLight( 0x404040 );
	var directionalLight1 = new THREE.DirectionalLight( 0xC0C090 );
	var directionalLight2 = new THREE.DirectionalLight( 0xC0C090 );
	var directionalLight3 = new THREE.DirectionalLight( 0x222222 );
	
	var hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.0);	
			
	directionalLight3.castShadow = true;			

	directionalLight1.position.set( -100, 50, 100 );
	directionalLight2.position.set( 100, 50, -100 );
	directionalLight3.position.set( 0, 150, 35 );
	hemisphereLight.position.set( 0, 1, 0 );

	this.scene.add( directionalLight1 );
	this.scene.add( directionalLight2 );
	this.scene.add( directionalLight3 );
	this.scene.add( ambientLight );
	this.scene.add( hemisphereLight );

  // Make a large plane to receive our shadows
  planeGeometry = new THREE.PlaneGeometry(6000, 6000);
  // Rotate our plane to be parallel to the floor
  planeGeometry.rotateX(-Math.PI / 2);

  // Create a mesh with a shadow material, resulting in a mesh
  // that only renders shadows once we flip the `receiveShadow` property
  shadowMesh = new THREE.Mesh(planeGeometry, new THREE.ShadowMaterial({
    color: 0x111111,
    opacity: 0.35
  }));
  shadowMesh.receiveShadow = true;
  scene.add(shadowMesh);

	
  
  THREE.ARUtils.loadModel({
    objPath: OBJ_PATH,
    mtlPath: MTL_PATH,
    OBJLoader: window.THREE.OBJLoader2, // uses window.THREE.OBJLoader by default
    MTLLoader: undefined, // uses window.THREE.MTLLoader by default
  }).then(function(group) {
    model = group;
		
	
    // As OBJ models may contain a group with several meshes,
    // we want all of them to cast shadow
    model.children.forEach(function(mesh) { mesh.castShadow = true; });

    model.scale.set(SCALE, SCALE, SCALE);

    // Place the model very far to initialize
    model.position.set(10000, 10000, 10000);
    scene.add(model);
	
	var el = document.getElementById('loading');								
	el.classList.add('hide');	
	setTimeout(function() {
		el.style.display = 'none';
	}, 1000);
		
  });

  // Bind our event handlers
  window.addEventListener('resize', onWindowResize, false);
  canvas.addEventListener('click', onClick, false);

  // Kick off the render loop!
  update();
}

/**
 * The render loop, called once per frame. Handles updating
 * our scene and rendering.
 */
function update() {
  // Clears color from the frame before rendering the camera (arView) or scene.
  renderer.clearColor();

  // Render the device's camera stream on screen first of all.
  // It allows to get the right pose synchronized with the right frame.
  arView.render();

  // Update our camera projection matrix in the event that
  // the near or far planes have updated
  camera.updateProjectionMatrix();

  // Update our perspective camera's positioning
  vrControls.update();

  // Render our three.js virtual scene
  renderer.clearDepth();
  renderer.render(scene, camera);

  // Kick off the requestAnimationFrame to call this function
  // when a new VRDisplay frame is rendered
  vrDisplay.requestAnimationFrame(update);
}

/**
 * On window resize, update the perspective camera's aspect ratio,
 * and call `updateProjectionMatrix` so that we can get the latest
 * projection matrix provided from the device
 */
function onWindowResize () {
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
}

var hold = false;
/*
var elScaleUp = document.getElementById('scale-up');
elScaleUp.addEventListener("touchstart", changeScale, false);
elScaleUp.addEventListener("touchend", handleEnd, false);
elScaleUp.addEventListener("touchcancel", handleCancel, false);
elScaleUp.addEventListener("touchmove", handleMove, false);*/



function rotateModel(e, n) {
	e.preventDefault();
	
	e.target.classList.add('active');
	
	if (!model) {
		console.warn('Model not yet loaded');
		return;
	}
		
	var yAxis = new THREE.Vector3(0,1,0);
	model.rotateY(n);
	
	setTimeout(function(){
		if(hold == true) {rotateModel(e, n);}
	}, 20);	
}

function changeHoldState(e, s) {
	hold = s;
	
	e.target.classList.remove('active');
}

function changeScale(e, n) {
	
	e.preventDefault();	
	e.target.classList.add('active');
	
	if (!model) {
		console.warn('Model not yet loaded');
		return;
	}
  
	SCALE += n;
	if(SCALE <= 0.001) {SCALE = 0.001}
  	 
	model.scale.set(SCALE, SCALE, SCALE);
	
	setTimeout(function(){
		if(hold == true) {changeScale(e, n);}
	}, 20);	
}

/**
 * When clicking on the screen, fire a ray from where the user clicked
 * on the screen and if a hit is found, place a cube there.
 */
function onClick (e) {
  // Inspect the event object and generate normalize screen coordinates
  // (between 0 and 1) for the screen position.
  var x = e.clientX / window.innerWidth;
  var y = e.clientY / window.innerHeight;

  // Send a ray from the point of click to the real world surface
  // and attempt to find a hit. `hitTest` returns an array of potential
  // hits.
  var hits = vrDisplay.hitTest(x, y);

  if (!model) {
    console.warn('Model not yet loaded');
    return;
  }

  // If a hit is found, just use the first one
  if (hits && hits.length) {
    var hit = hits[0];

    // Turn the model matrix from the VRHit into a
    // THREE.Matrix4 so we can extract the position
    // elements out so we can position the shadow mesh
    // to be directly under our model. This is a complicated
    // way to go about it to illustrate the process, and could
    // be done by manually extracting the "Y" value from the
    // hit matrix via `hit.modelMatrix[13]`
    var matrix = new THREE.Matrix4();
    var position = new THREE.Vector3();
    matrix.fromArray(hit.modelMatrix);
    position.setFromMatrixPosition(matrix);
	
	

    // Set our shadow mesh to be at the same Y value
    // as our hit where we're placing our model
    // @TODO use the rotation from hit.modelMatrix
    shadowMesh.position.y = position.y;

    // Use the `placeObjectAtHit` utility to position
    // the cube where the hit occurred
    THREE.ARUtils.placeObjectAtHit(model,  // The object to place
                                   hit,   // The VRHit object to move the cube to
                                   1,     // Easing value from 0 to 1; we want to move
                                          // the cube directly to the hit position
                                   true); // Whether or not we also apply orientation

    // Rotate the model to be facing the user
    var angle = Math.atan2(
      camera.position.x - model.position.x,
      camera.position.z - model.position.z
    );
    /*model.rotation.set(0, angle, 0);
	SCALE = 0.1;
	model.scale.set(SCALE, SCALE, SCALE);*/
  }
}
</script>
</body>
</html>
