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
class patTemplate_Modifier_Truncate extends patTemplate_Modifier{
	function modify($value, $params = array()){

		if(!isset($params['length'])){
			return $value;
		}
		settype($params['length'], 'integer');
		$decode = isset($params['htmlsafe']);
		if(function_exists('html_entity_decode') && $decode){
			$value = html_entity_decode($value);
		}

		if(isset($params['start'])){
			settype($params['start'], 'integer');
		} else{
			$params['start'] = 0;
		}

		if(isset($params['prefix'])){
			$prefix = ($params['start'] == 0 ? '' : $params['prefix']);
		} else{
			$prefix = '';
		}

		if(isset($params['suffix'])){
			$suffix = $params['suffix'];
		} else{
			$suffix = '';
		}
		$initial_len = strlen($value);
		$value = substr($value, $params['start'], $params['length']);
		if($initial_len <= strlen($value)){
			$suffix = '';
		}
		$value = $prefix . $value . $suffix;
		return $decode ? htmlspecialchars($value, ENT_QUOTES) : $value;
	}
}

?>
