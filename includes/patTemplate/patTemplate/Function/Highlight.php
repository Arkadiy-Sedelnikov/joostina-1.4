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
class patTemplate_Function_Highlight extends patTemplate_Function{
	var $_name = 'Highlight';

	function call($params, $content){
		if(!include_once 'Text/Highlighter.php'){
			return false;
		}
		include_once 'Text/Highlighter/Renderer/Html.php';
		if(!isset($params['type'])){
			return $content;
		}
		$type = $params['type'];
		unset($params['type']);
		if(isset($params['numbers']) && defined($params['numbers'])){
			$params['numbers'] = constant($params['numbers']);
		}
		$renderer = new Text_Highlighter_Renderer_HTML($params);
		$highlighter = &Text_Highlighter::factory($type);
		$highlighter->setRenderer($renderer);
		return $highlighter->highlight(trim($content));
	}
}

?>
