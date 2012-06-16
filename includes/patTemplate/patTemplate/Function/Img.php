<?php
/**
 * @package Joostina
 * @copyright ��������� ����� (C) 2008 Joostina team. ��� ����� ��������.
 * @license �������� http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, ��� help/license.php
 * Joostina! - ��������� ����������� ����������� ���������������� �� �������� �������� GNU/GPL
 * ��� ��������� ���������� � ������������ ����������� � ��������� �� ��������� �����, �������� ���� help/copyright.php.
 * @version        3.1.0
 * @package        patTemplate
 * @author        Stephan Schmidt <schst@php.net>
 * @license        LGPL
 * @link        http://www.php-tools.net
 */
// ������ ������� �������
defined('_VALID_MOS') or die();
class patTemplate_Function_Img extends patTemplate_Function{
	var $_name = 'Img';
	var $_defaults = array();

	function call($params, $content){
		$src = $params['src'] ? $params['src'] : $content;
		list($width, $height, $type, $attr) = getimagesize($src);
		$this->_defaults = array('border' => 0, 'title' => '', 'alt' => '', 'width' => $width, 'height' => $height);
		$params = array_merge($this->_defaults, $params);
		$tags = '';
		foreach($params as $key => $value){
			$tags .= sprintf('%s="%s" ', $key, htmlentities($value));
		}
		$imgstr = sprintf('<img %s/>', $tags);
		return $imgstr;
	}
}

?>
