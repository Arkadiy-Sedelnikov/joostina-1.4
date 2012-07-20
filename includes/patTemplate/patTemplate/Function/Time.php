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
class patTemplate_Function_Time extends patTemplate_Function{
	var $_name = 'Time';

	function call($params, $content){
		if(!empty($content)){
			$params['time'] = $content;
		}
		if(isset($params['time'])){
			$params['time'] = strtotime($params['time']);
		} else{
			$params['time'] = time();
		}
		return date($params['format'], $params['time']);
	}
}

?>
