<?php
/**
* @package Joostina
* @copyright ��������� ����� (C) 2008 Joostina team. ��� ����� ��������.
* @license �������� http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, ��� help/license.php
* Joostina! - ��������� ����������� ����������� ���������������� �� �������� �������� GNU/GPL
* ��� ��������� ���������� � ������������ ����������� � ��������� �� ��������� �����, �������� ���� help/copyright.php.
*
* @version		3.1.0
* @package		patTemplate
* @author		Stephan Schmidt <schst@php.net>
* @license		LGPL
* @link		http://www.php-tools.net
*/
// ������ ������� �������
defined('_VALID_MOS') or die();
class patTemplate_TemplateCache_MMCache extends patTemplate_TemplateCache {
var $_params = array('lifetime' => 'auto');
function load($key,$modTime = -1) {
if(!function_exists('mmcache_lock')) {
return false;
}
$something = mmcache_get($key);
if(is_null($something)) {
return false;
} else {
return unserialize($something);
}
}
function write($key,$templates) {
if(!function_exists('mmcache_lock')) {
return false;
}
mmcache_lock($key);
if($this->getParam('lifetime') == 'auto') {
mmcache_put($key,serialize($templates));
} else {
mmcache_put($key,serialize($templates),$this->getParam('lifetime') * 60);
}
mmcache_unlock($key);
return true;
}
}
?>
