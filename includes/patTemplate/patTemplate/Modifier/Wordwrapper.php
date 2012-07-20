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
defined('_JLINDEX') or die();
class patTemplate_Modifier_Wordwrapper extends patTemplate_Modifier{
	function modify($value, $params = array()){
		if(!isset($params['width']))
			$params['width'] = 72;
		settype($params['width'], 'integer');
		if(!isset($params['break']))
			$params['break'] = "\n";
		if(!isset($params['cut']))
			$params['cut'] = 'no';
		$params['cut'] = ($params['cut'] === 'yes') ? true : false;
		$value = wordwrap($value, $params['width'], $params['break'], $params['cut']);
		if(isset($params['nl2br']) && $params['nl2br'] === 'yes')
			$value = nl2br($value);
		return $value;
	}
}

?>
