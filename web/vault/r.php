<?php

	

	FUNCTION IS_IMAGE($P) {
		IF(@IS_ARRAY(GETIMAGESIZE($P))){
			RETURN TRUE;
		} 
		
		RETURN FALSE;
	}
	
	/* SETTINGS */
	$IMG_SRC = $_GET['p'];
	$IMG_WIDTH = 98;
	$IMG_HEIGHT = 98;
	$IMG_QUALITY = 100;
	$EXIF_TYPE = EXIF_IMAGETYPE($IMG_SRC);
	$TYPE = IMAGE_TYPE_TO_MIME_TYPE($EXIF_TYPE);
	

	IF(ISSET($IMG_SRC) 
		AND FILE_EXISTS($IMG_SRC) 
		AND IS_IMAGE($IMG_SRC) 
		AND $TYPE != 'image/tiff'
	)
	{
		HEADER("Content-type: " . $TYPE);
		READFILE($IMG_SRC);	
		DIE();
	} 
	
	HEADER("Content-type: image/jpeg");
				
	$IM = @IMAGECREATETRUECOLOR($IMG_WIDTH , $IMG_HEIGHT);
	$BG = IMAGECOLORALLOCATE($IM, 255, 255, 255);
	IMAGEFILLEDRECTANGLE ($IM, 0, 0, $IMG_WIDTH , $IMG_HEIGHT, $BG);
	$TEXT_COLOR = IMAGECOLORALLOCATE($IM, 136, 136, 136);
	 
	IMAGETTFTEXT($IM, 10, 0, 17, ($IMG_HEIGHT / 2) + 5, $TEXT_COLOR, 'arial.ttf', 'No Preview');
	IMAGEJPEG($IM);
	IMAGEDESTROY($IM, NULL, $IMG_QUALITY);

	
?>