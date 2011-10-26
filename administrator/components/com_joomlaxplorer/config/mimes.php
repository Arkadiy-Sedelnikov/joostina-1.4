<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/

// запрет прямого доступа
defined('_VALID_MOS') or die();

$GLOBALS["editable_ext"] = array("\.txt$|\.php$|\.php3$|\.php5$|\.phtml$|\.inc$|\.sql$|\.pl$","\.htm$|\.html$|\.shtml$|\.dhtml$|\.xml$","\.js$|\.css$|\.cgi$|\.cpp$|\.c$|\.cc$|\.cxx$|\.hpp$|\.h$","\.pas$|\.p$|\.java$|\.py$|\.sh$\.tcl$|\.tk$");

$GLOBALS["images_ext"] = "\.png$|\.bmp$|\.jpg$|\.jpeg$|\.png$|\.ico$";

// mime types: (description,image,extension)
$GLOBALS["super_mimes"] = array( // dir, exe, file
	"dir" => array($GLOBALS["mimes"]["dir"],"folder.png"),
	"exe" => array($GLOBALS["mimes"]["exe"],"exe.png","\.exe$|\.com$|\.bin$"),
	"file" => array($GLOBALS["mimes"]["file"],"file.png"));
$GLOBALS["used_mime_types"] = array( // text
	"text" => array($GLOBALS["mimes"]["text"],"txt.png","\.txt$"), // programming
	"php" => array($GLOBALS["mimes"]["php"],"php.png","\.php$|\.php3$|\.phtml$|\.inc$"),
	"sql" => array($GLOBALS["mimes"]["sql"],"src.png","\.sql$"),
	"js" => array($GLOBALS["mimes"]["js"],"js.png","\.js$"),
	"css" => array($GLOBALS["mimes"]["css"],"css.png","\.css$"),
	"cgi" => array($GLOBALS["mimes"]["cgi"],"exe.png","\.cgi$"),
	"cpps" => array($GLOBALS["mimes"]["cpps"],"cpp.png","\.cpp$|\.c$|\.cc$|\.cxx$"),
	"cpph" => array($GLOBALS["mimes"]["cpph"],"h.gif","\.hpp$|\.h$"), // Java
	"pas" => array($GLOBALS["mimes"]["pas"],"src.png","\.p$|\.pas$"), // images
	"gif" => array($GLOBALS["mimes"]["gif"],"image.png","\.png$"),
	"jpg" => array($GLOBALS["mimes"]["jpg"],"image.png","\.jpg$|\.jpeg$"),
	"bmp" => array($GLOBALS["mimes"]["bmp"],"image.png","\.bmp$"),
	"png" => array($GLOBALS["mimes"]["png"],"image.png","\.png$"),
	// compressed
	"zip" => array($GLOBALS["mimes"]["zip"],"zip.png","\.zip$"),
	"tar" => array($GLOBALS["mimes"]["tar"],"zip.png","\.tar$"),
	"gzip" => array($GLOBALS["mimes"]["gzip"],"zip.png","\.tgz$|\.gz$"),
	"bzip2" => array($GLOBALS["mimes"]["bzip2"],"zip.png","\.bz2$|\.tbz$"),
	"rar" =>array($GLOBALS["mimes"]["rar"],"zip.png","\.rar$"),
	// music
	"mp3" => array($GLOBALS["mimes"]["mp3"],"sound.png","\.mp3$"),
	"wav" => array($GLOBALS["mimes"]["wav"],"sound.png","\.wav$"),
	"midi" => array($GLOBALS["mimes"]["midi"],"sound.png","\.mid$"),
	"real" => array($GLOBALS["mimes"]["real"],"sound.png","\.rm$|\.ra$|\.ram$"),
	// movie
	"mpg" => array($GLOBALS["mimes"]["mpg"],"video.png","\.mpg$|\.mpeg$"),
	"mov" =>array($GLOBALS["mimes"]["mov"],"video.png","\.mov$"),
	"avi" => array($GLOBALS["mimes"]["avi"],"video.png","\.avi$"),
	"flash" => array($GLOBALS["mimes"]["flash"],"flash.png","\.swf$"),
	// Micosoft / Adobe
	"word" => array($GLOBALS["mimes"]["word"],"word.png","\.doc$"),
	"excel" => array($GLOBALS["mimes"]["excel"],"excel.png","\.xls$"),
	"pdf" => array($GLOBALS["mimes"]["pdf"],"pdf.png","\.pdf$"));

?>
