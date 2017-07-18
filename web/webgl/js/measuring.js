var Helper = {
    getClientY: function (e) {
        return typeof e.clientY === 'undefined' ? e.touches[0].clientY : e.clientY;
    },
    getClientX: function (e) {
        return typeof e.clientX === 'undefined' ? e.touches[0].clientX : e.clientX;
    },
    isMobileDevice: function () {
        var check = false;
        (function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    }
};


function Measuring(scene, camera) {

    var thisObj = this;
	var projector = new THREE.Projector();

    this.length = 0,
    this.isActive = false,
    this.points = [],
    this.lines = [],
    this.labels = [];
    this.canAddPoint = false,
    this.raycaster = new THREE.Raycaster(),
    this.mouse = new THREE.Vector2();
    this.color = 0xCC0000;
	this.container = document.getElementById('container');
	this.canvas = document.getElementById('player');
	this.measureText = document.getElementById('measureText');
	this.measureTextLabel = document.getElementById('measureTextLabel');
	this.measureErase = document.getElementById('measureErase');
	this.lengthLabel = document.getElementById('length');
	this.measuringData =  document.getElementById('measuringData');
	this.pointOne =  document.getElementById('pointOne');
	this.pointTwo =  document.getElementById('pointTwo');
	this.pointLine = document.getElementById('pointLine');
	this.mode = document.getElementById('mode');
	this.targetObjects = [];

		
    this.fontSettings = {
        font: null,
        height: 0.05,
        size: 0.4,
        curveSegments: 12,
        bevelEnabled: true,
        bevelThickness: 0.01,
        bevelSize: 0.01
    };
    this.fontMaterial = new THREE.MultiMaterial([
		new THREE.MeshPhongMaterial({ color: thisObj.color, emissive: thisObj.color, emissiveIntensity: 0.4, shading: THREE.FlatShading }), // front
		new THREE.MeshPhongMaterial({ color: thisObj.color, emissive: thisObj.color, emissiveIntensity: 0.4, shading: THREE.SmoothShading }) // side
    ]);
	
    this.clear = function () {

        thisObj.length = 0;

        for (var i = 0; i < thisObj.points.length; i++) {
            scene.remove(thisObj.points[i]);
        }
        for (var i = 0; i < thisObj.lines.length; i++) {
            scene.remove(thisObj.lines[i]);
        }
        for (var i = 0; i < thisObj.labels.length; i++) {
            scene.remove(thisObj.labels[i]);
        }
        thisObj.points = [];
        thisObj.lines = [];
        thisObj.labels = [];

        thisObj.lengthLabel.textContent = "";
    };

    this.calculateLength = function () {

        thisObj.length = 0;
        if (thisObj.points.length > 1) {
            for (var i = 0; i < thisObj.points.length - 1; i++) {
                thisObj.length += thisObj.points[i].position.distanceTo(thisObj.points[i + 1].position);
            }
        }

        thisObj.lengthLabel.textContent = thisObj.length.toFixed(2) + 'm';
        thisObj.measureTextLabel.textContent = thisObj.lengthLabel.textContent;

    };

    this.getMousePos = function (event) {       		
		thisObj.mouse.x = (Helper.getClientX(event) / thisObj.canvas.offsetWidth) * 2 - 1;
        thisObj.mouse.y = -(Helper.getClientY(event) / thisObj.canvas.offsetHeight) * 2 + 1;
    };

    this.createText = function (scene, text, position, v1, v2) {

        var textGeo = new THREE.TextGeometry(text, thisObj.fontSettings);
        textGeo.computeBoundingBox();
        textGeo.computeVertexNormals();
        var centerOffset = -0.5 * (textGeo.boundingBox.max.x - textGeo.boundingBox.min.x);
        textMesh = new THREE.Mesh(textGeo, thisObj.fontMaterial);

        var group = new THREE.Group();
        textMesh.position.set(centerOffset, thisObj.fontSettings.size / 2, 0.2);
        group.add(textMesh);

        var subV12 = v2.clone().sub(v1).normalize();
        var crossV12 = new THREE.Vector3();
        crossV12.crossVectors(subV12, new THREE.Vector3(0, -1, 0)).normalize();
        var crossV3 = new THREE.Vector3();
        crossV3.crossVectors(subV12, crossV12);

        var rotationMatrix = new THREE.Matrix4().makeBasis(subV12, crossV3, crossV12.multiplyScalar(-1));

        rotationMatrix.setPosition(position);
        group.applyMatrix(rotationMatrix);

        scene.add(group);
        thisObj.labels.push(group);

    };

    this.getLengthBetweenPoint = function (pointA, pointB) {
        var dir = pointB.clone().sub(pointA);
        return dir.length().toFixed(4);
    };

    this.getPointInBetweenByPerc = function(pointA, pointB, percentage) {

        var dir = pointB.clone().sub(pointA);
        var len = dir.length();
        dir = dir.normalize().multiplyScalar(len * percentage);
        return pointA.clone().add(dir);

    };

    var loadFont = function() {
        var loader = new THREE.FontLoader();
        loader.load('/webgl/js/fonts/helvetiker_regular.typeface.json', function (response) {
            thisObj.fontSettings.font = response;
        });
    }
	
	this.showMode = function(mode) {

		thisObj.mode.style.display = 'none';
		thisObj.mode.textContent = '';
		
		if(mode) {
			thisObj.mode.textContent = mode;
			thisObj.mode.style.display = 'block';
		}
	}
	
	this.getScreenCoords = function( position, camera ) {
	    var rect = this.container.getBoundingClientRect();
		var widthHalf = rect.width / 2, heightHalf = rect.height / 2;

		var vector = new THREE.Vector3().copy(position);

		if (vector.project)
			vector.project(camera);
		else
			projector.projectVector( vector, camera );

		return new THREE.Vector2(( vector.x * widthHalf ) + widthHalf, - ( vector.y * heightHalf ) + heightHalf);
	}
		
	
	this.updateTextPos = function() {
		
	}
	
	this.pointGizmo = function(v1, v2) {			
		if(v1) {			
			var p1 = thisObj.getScreenCoords( v1, camera );						
			thisObj.pointOne.style.left = p1.x + 'px';
			thisObj.pointOne.style.top = p1.y + 'px';
		} else {
			thisObj.pointOne.style.left = '-100px';
			thisObj.pointOne.style.top = '-100px';
		}
		
		if(v2) {		
			var p2 = thisObj.getScreenCoords( v2, camera );	
			thisObj.pointTwo.style.left = p2.x + 'px';
			thisObj.pointTwo.style.top = p2.y + 'px';
		} else {
			thisObj.pointTwo.style.left = '-100px';
			thisObj.pointTwo.style.top = '-100px';
		}
	}
	

	this.measureTextGizmo = function(v1, v2) {
		this.oldV1 = v1;
		this.oldV2 = v2;
		
		if(!v1 || !v2) {
			
			thisObj.measureText.style.left = '-100px';
			thisObj.measureText.style.top = '-100px';
			
			return false;
		}
		
		var pos = thisObj.getScreenCoords( thisObj.getPointInBetweenByPerc(v1, v2, 0.5), camera );				
				
		thisObj.measureText.style.left = pos.x + 'px';
		thisObj.measureText.style.top = pos.y + 'px';				
		thisObj.measureText.style.display = 'block';
	}
	
	this.getCenter = function(p1, p2) {
		var x = p1.x + ((p2.x - p1.x) / 2);
		var y = p1.y + ((p2.y - p1.y) / 2);
		
		return {'x': x, 'y': y}
	}
	
	this.lineGizmo = function(v1, v2) {
		
		if(!v1 || !v2) {
			thisObj.pointLine.style.width = '0px';
			thisObj.pointLine.style.left = '-100px';
			thisObj.pointLine.style.top = '-100px';
			
			return false;
		}
		
		var p1 = thisObj.getScreenCoords( v1, camera );				
		var p2 = thisObj.getScreenCoords( v2, camera );	
				
		var pos = thisObj.getCenter(p1, p2);
			
		var angle = Math.atan2(p2.y - p1.y, p2.x - p1.x) * 180 / Math.PI;
		var distance = Math.sqrt((p1.x -= p2.x) * p1.x + (p1.y -= p2.y) * p1.y);
				
		thisObj.pointLine.style.width = distance + 'px';
		thisObj.pointLine.style.left = pos.x + 'px';
		thisObj.pointLine.style.top = pos.y + 'px';
		
		thisObj.pointLine.style.transform = 'translate(-50%, -50%) rotate(' + angle + 'deg)';
		
	}

    loadFont();

    thisObj.canvas.addEventListener('mousedown', function (event) {
        thisObj.canAddPoint = false;
        thisObj.getMousePos(event);						
    }, false);
	
	 thisObj.canvas.addEventListener('mouseup', function (event) {
        var x = thisObj.mouse.x; 
        var y = thisObj.mouse.y;
		thisObj.getMousePos(event);	
		
		if(x == thisObj.mouse.x && y == thisObj.mouse.y) {
			thisObj.canAddPoint = true;
		}        				
    }, false);
	
	
	this.processIntersections = function(){
		
		if(thisObj.isActive) {
			thisObj.raycaster.setFromCamera(thisObj.mouse, camera);
			var intersects = thisObj.raycaster.intersectObjects(thisObj.targetObjects);
						
			thisObj.measureTextGizmo();
			thisObj.lineGizmo();
			thisObj.pointGizmo();
				
			if (thisObj.points.length == 2) {
				
				var v1 = thisObj.points[thisObj.points.length - 1].position;
				var v2 = thisObj.points[thisObj.points.length - 2].position;
				
				thisObj.measureTextGizmo(v1, v2);
				thisObj.pointGizmo(v1, v2);				
				thisObj.lineGizmo(v1, v2);
				
				thisObj.canAddPoint = false;
			} else if (thisObj.points.length == 1)  {
				var v1 = thisObj.points[thisObj.points.length - 1].position;
				
				thisObj.pointGizmo(v1);
			} 	
						
		
			if (thisObj.canAddPoint) {
								
				if (intersects.length > 0 && thisObj.points.length < 2) {
				
					var sphere = new THREE.Mesh(new THREE.SphereGeometry(0.1, 1, 1), new THREE.MeshLambertMaterial({ color: thisObj.color, opacity: 0, transparent: true }));
					sphere.position.set(intersects[0].point.x, intersects[0].point.y, intersects[0].point.z);
					scene.add(sphere);
										
					//thisObj.pointGizmo(intersects[0].point);
					

					if (thisObj.points.length > 0) {

						var v1 = thisObj.points[thisObj.points.length - 1].position;
						var v2 = intersects[0].point;

						var geometry = new THREE.Geometry();
						geometry.vertices.push(v1, v2);
						var line = new THREE.Line(geometry, new THREE.LineBasicMaterial({ color: thisObj.color, linewidth: 2, opacity: 0, transparent: true  }));
						scene.add(line);

						thisObj.lines.push(line);
							
						//thisObj.measureTextGizmo(v1, v2);
						//thisObj.pointGizmo(v1, v2);
					
						/*thisObj.createText(
							scene,
							thisObj.getLengthBetweenPoint(v1, v2),
							thisObj.getPointInBetweenByPerc(v1, v2, 0.5),
							v1,
							v2
						);*/

					}

					thisObj.points.push(sphere);
					thisObj.calculateLength();
					thisObj.canAddPoint = false;
				}
			}
		}
	}

	Measuring.prototype.processIntersections = function() {
		thisObj.processIntersections();
	}
	
	Measuring.prototype.showHideMeasuring = function(obj) {			
            thisObj.isActive = !thisObj.isActive;
           // thisObj.measuringData.style.display = thisObj.isActive ? 'block' : 'none';
			thisObj.measureText.style.display = thisObj.measuringData.style.display;

			thisObj.deleteAllPoints();
			      

            //obj.className = thisObj.isActive ? "active" : "";
						
			if(thisObj.isActive) {
				obj.classList.add('active');
				thisObj.showMode('Measure Mode ON');
			} else {
				obj.classList.remove('active');
				thisObj.showMode();
			}
	}
	
	Measuring.prototype.deleteLastPoint = function() {
		scene.remove(thisObj.points[thisObj.points.length - 1]);
		scene.remove(thisObj.lines[thisObj.lines.length - 1]);
		scene.remove(thisObj.labels[thisObj.labels.length - 1]);
		thisObj.points.pop();
		thisObj.lines.pop();
		thisObj.labels.pop();

		thisObj.calculateLength();
	} 
	
	Measuring.prototype.deleteAllPoints = function() {		
		thisObj.clear();
		
		thisObj.measureText.style.display = 'none';
		thisObj.measureTextGizmo();
		thisObj.lineGizmo();
		thisObj.pointGizmo();
	} 	
}

