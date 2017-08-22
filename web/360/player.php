<?php

	IF(!ISSET($_GET['id'])) DIE('Invalid ID');
	$ID = $_GET['id'];
	
	$FILES = GLOB('./' . $ID . '/*.jpg');
	NATCASESORT($FILES);
	
	$IM = imagecreatefromjpeg($FILES[0]);
	$RGB = IMAGECOLORAT($IM, 0, 0);
	$R = ($RGB >> 16) & 0xFF;
	$G = ($RGB >> 8) & 0xFF;
	$B = $RGB & 0xFF;
	
?>



<html>
<head>
<title>
	Web Player
</title>
<style>

html, body {
	margin: 0;
	padding: 0;
}


.container {
	display: block;
    position: absolute;
    width: 100%;
    height: 100%;
    background-size: auto;
    background-repeat: no-repeat;
    background-position: center;
}

.play {
	position: absolute;
	left: 50%;
	bottom: 100px;
	transform: translate(-50%, -50%);
	width: 80px;
	cursor: pointer;
	transform-origin: center;
	transition: 0.2s all ease-out;
}

.play:hover {
	transform-origin: center;
	transform: translate(-50%, -50%) scale(1.1);
}

</style>
</head>
<body onkeydown="changeImage()" style="background-color: rgba(<?=$R?>, <?=$G?>, <?=$B?>, 1)">



<?php	
	
	FOREACH($FILES AS $FILE) ECHO '<img src="' . $FILE . '" style="display: none;" class="sequence">';
	
?>
<div class="container">
</div>

<img src="play.svg" class="play" onclick="togglePlay()">

</body>
</html>

<script >

	var imgs = document.getElementsByClassName('sequence');
	var container = document.getElementsByClassName('container')[0];
	
	var currImg = 0;
	var playTmr = null;
	
	var toggleImage = function(id, show) {		
		if(!imgs[id]) return false;
		console.log(imgs[id].src)
		container.style.backgroundImage = "url('" + imgs[id].src + "')";
		//imgs[id].style.display = show ? 'block' : 'none';
	}
	
	var changeImage = function(e) {
		
		var event = window.event ? window.event : e;
				
		toggleImage(currImg, false);
		
		
		if(event && event.keyCode == 37) {currImg--;} else {currImg++;}
		
		if(currImg > imgs.length - 1) currImg = 0;
		if(currImg < 0) currImg = imgs.length - 1;
		
		toggleImage(currImg, true);
	}
	
	var togglePlay = function() {
		
		var playButton = document.getElementsByClassName('play')[0];
				
		if(!playTmr) {
			playTmr = setInterval(function() {
				changeImage();
			}, 40);
			
			playButton.src = 'pause.svg';
			
		} else {
			clearInterval(playTmr);
			playTmr = null;
			
			playButton.src = 'play.svg';
		}
	}
	
	
	toggleImage(0, true);

</script>