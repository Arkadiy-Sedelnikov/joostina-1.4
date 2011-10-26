<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*
* @version		3.1.0
* @package		patTemplate
* @author		Stephan Schmidt <schst@php.net>
* @license		LGPL
* @link		http://www.php-tools.net
*/
// запрет прямого доступа
defined('_VALID_MOS') or die();
class patTemplate_TemplateCache_File extends patTemplate_TemplateCache {
var $_params = array('cacheFolder' => './cache','lifetime' => 'auto','prefix' => '','filemode' => null);
function load($key,$modTime = -1) {
$filename = $this->_getCachefileName($key);
if(!file_exists($filename) || !is_readable($filename)) {
return false;
}
$generatedOn = filemtime($filename);
$ttl = $this->getParam('lifetime');
if($ttl == 'auto') {
if($modTime < 1) {
return false;
}
if($modTime > $generatedOn) {
return false;
}
return unserialize(file_get_contents($filename));
} elseif(is_int($ttl)) {
if($generatedOn + $ttl < time()) {
return false;
}
return unserialize(file_get_contents($filename));
}
return false;
}
function write($key,$templates) {
$cacheFile = $this->_getCachefileName($key);
$fp = @fopen($cacheFile,'w');
if(!$fp) {
return false;
}
flock($fp,LOCK_EX);
fputs($fp,serialize($templates));
flock($fp,LOCK_UN);
$filemode = $this->getParam('filemode');
if($filemode !== null) {
chmod($cacheFile,$filemode);
}
return true;
}
function _getCachefileName($key) {
return $this->getParam('cacheFolder').'/'.$this->getParam('prefix').$key.'.cache';
}
}
?>
