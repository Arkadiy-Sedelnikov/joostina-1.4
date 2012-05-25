<?php
/**
 * @package GDNLotos - Главные новости
 * @copyright Авторские права (C) 2000-2011 Gold Dragon.
 * @license http://www.gnu.org/licenses/gpl.htm GNU/GPL
 * GDNLotos - Главные новости - модуль позволяет выводить основные материалы по определённым критериям для Joostina 1.4.0.x
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл view/copyright.php.
 */

if (!isset($_REQUEST["src"])) {
	die("no image specified");
}

$src = clean_source($_REQUEST["src"]);
$doc_root = get_document_root($src);
$src = $doc_root . '/' . $src;
if (!function_exists('imagecreatetruecolor')) {
	die("GD Library Error: imagecreatetruecolor does not exist");
}

if (strlen($src)) {
	$new_width = preg_replace("/[^0-9]+/", "", get_request('w', 100));
	$new_height = preg_replace("/[^0-9]+/", "", get_request('h', 100));
	$quality = preg_replace("/[^0-9]+/", "", get_request('q', 75));

	if (!is_dir($doc_root . '/cache/mod_gdnews/')) {
		mkdir($doc_root . '/cache/mod_gdnews/', 0755);
	}
	$cache_dir = $doc_root . '/cache/mod_gdnews/';

	$info_img = getimagesize($src);
	$width = $info_img[0];
	$height = $info_img[1];

	@ini_set('gd.jpeg_ignore_warning', 1);
	$image = imagecreatefromjpeg($src);

	$canvas = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	show_image($canvas, $quality, $cache_dir);
	imagedestroy($canvas);
} else {
	die($src . ' not found.');
}

function show_image($image_resized, $quality, $cache_dir) {
	$is_writable = 0;
	$cache_file_name = $cache_dir . '/' . get_cache_file();
	if (touch($cache_file_name)) {
		chmod($cache_file_name, 0666);
		$is_writable = 1;
	} else {
		$cache_file_name = NULL;
		header('Content-type: image/jpeg');
	}
	imagejpeg($image_resized, $cache_file_name, $quality);

	if ($is_writable) {
		show_cache_file($cache_dir);
	}
	die();
}

function get_request($property, $default = 0) {
	if (isset($_REQUEST[$property])) {
		return $_REQUEST[$property];
	} else {
		return $default;
	}
}

function show_cache_file($cache_dir) {
	$cache_file = $cache_dir . '/' . get_cache_file();
	if (file_exists($cache_file)) {
		if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
			$if_modified_since = preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]);
			$gmdate_mod = gmdate('D, d M Y H:i:s', filemtime($cache_file));
			if (strstr($gmdate_mod, 'GMT')) {
				$gmdate_mod .= " GMT";
			}
			if ($if_modified_since == $gmdate_mod) {
				header("HTTP/1.1 304 Not Modified");
				exit;
			}
		}

		$fileSize = filesize($cache_file);

		// send headers then display image
		header("Content-Type: image/jpeg");
		//header("Accept-Ranges: bytes");
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', filemtime($cache_file)) . " GMT");
		header("Content-Length: " . $fileSize);
		header("Cache-Control: max-age=9999, must-revalidate");
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + 9999) . "GMT");

		readfile($cache_file);

		die();
	}
}

function get_cache_file() {
	static $cache_file;
	if (!$cache_file) {
		$cachename = get_request('src', 'imgsketch') . get_request('w', 100) . get_request('h', 100) . get_request('q', 100);
		$cache_file = md5($cachename) . '.jpg';
	}
	return $cache_file;
}

function clean_source($src) {
	$src = preg_replace("/^((ht|f)tp(s|):\/\/)/i", "", $src);
	$host = $_SERVER["HTTP_HOST"];
	$src = str_replace($host, "", $src);
	$host = str_replace("www.", "", $host);
	$src = str_replace($host, "", $src);
	$src = preg_replace("/\.\.+\//", "", $src);
	return $src;
}

function get_document_root($src) {
	if (@file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $src)) {
		return $_SERVER['DOCUMENT_ROOT'];
	}
	$paths = array('..', '../..', '../../..', '../../../..');
	foreach ($paths as $path) {
		if (@file_exists($path . '/' . $src)) {
			return $path;
		}
	}
}

?>
