<?php
/**
 * Captcha for login/register forms
 *
 * @package BoomBox_Theme
 */


/**
 * Render Captcha
 */
$width = isset( $_GET[ 'w' ] ) ? (int)$_GET[ 'w' ] : 300;
$height = isset( $_GET[ 'h' ] ) ? (int)$_GET[ 'h' ] : 40;
boombox_captcha_security_images( $width, $height, rand( 4, 6 ) );

/**
 * Generate Captcha Code
 *
 * @param $characters
 *
 * @return string
 */
function boombox_generate_code( $characters ) {
	$possible = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
	$possible = $possible . $possible . '2345678923456789';
	$code     = '';
	$i        = 0;
	while ( $i < $characters ) {
		$code .= substr( $possible, mt_rand( 0, strlen( $possible ) - 1 ), 1 );
		$i ++;
	}

	return $code;
}

/**
 * Generate Captcha Security Image
 *
 * @param int $width
 * @param int $height
 * @param int $characters
 */
function boombox_captcha_security_images( $width = 145, $height = 35, $characters = 6 ) {

	$font_path = __DIR__ . DIRECTORY_SEPARATOR . 'calibri.ttf';
	$code      = boombox_generate_code( $characters );
	$font_size = $height * 0.60;
	$image     = @imagecreate( $width, $height ); // or die( 'Cannot initialize new GD image stream' );

	/* set the colours */
	$rgb_color = boombox_hex2rgb( '#ececec' );
	$bgR = $rgb_color['red'];
	$bgG = $rgb_color['green'];
	$bgB = $rgb_color['blue'];

	$background_color = imagecolorallocate( $image, $bgR, $bgG, $bgB );
	$noise_color      = imagecolorallocate( $image, abs( 50 - $bgR ), abs( 50 - $bgG ), abs( 50 - $bgB ) );
	$text_color       = imagecolorallocate( $image, abs( 100 - $bgR ), abs( 100 - $bgG ), abs( 100 - $bgB ) );

	/* generate random dots in background */
	for ( $i = 0; $i < ( $width * $height ) / 3; $i ++ ) {
		imagefilledellipse( $image, mt_rand( 0, $width ), mt_rand( 0, $height ), 1, 1, $noise_color );
	}

	/* generate random lines in background */
	for ( $i = 0; $i < ( $width * $height ) / 150; $i ++ ) {
		imageline( $image, mt_rand( 0, $width ), mt_rand( 0, $height ), mt_rand( 0, $width ), mt_rand( 0, $height ), $noise_color );
	}

	/* set random colors */
	$w = imagecolorallocate( $image, abs( 100 - $bgR ), abs( 100 - $bgG ), abs( 100 - $bgB ) );
	$r = imagecolorallocate( $image, abs( 100 - $bgR ), abs( 100 - $bgG ), abs( 100 - $bgB ) );

	/* Draw a dashed line, 5 red pixels, 5 white pixels */
	$style = array( $r, $r, $r, $r, $r, $w, $w, $w, $w, $w );
	imagesetstyle( $image, $style );
	imageline( $image, 0, 0, $width, $height, IMG_COLOR_STYLED );
	imageline( $image, $width, 0, 0, $height, IMG_COLOR_STYLED );

	$textbox = imagettfbbox( $font_size, 0, $font_path, $code ); // or die( 'Error in imagettfbbox function' );
	$x = ( $width - $textbox[4] ) / 2;
	$y = ( $height - $textbox[5] ) / 2;
	imagettftext( $image, $font_size, 0, $x, $y, $text_color, $font_path, $code ); // or die( 'Error in imagettftext function' );

	/* pretty it */
	imageantialias( $image, 100 );
	imagealphablending( $image, 1 );
	imagelayereffect( $image, IMG_EFFECT_OVERLAY );

	if( isset( $_GET['type'] ) && '' != $_GET['type'] ){
		session_start();
		$_SESSION["boombox_{$_GET['type']}_captcha_key"] = $code;
		session_write_close();
	}

	/* output captcha image to browser */
	header( 'Content-Type: image/jpeg' );
	imagejpeg( $image );
	imagedestroy( $image );

}

function boombox_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}