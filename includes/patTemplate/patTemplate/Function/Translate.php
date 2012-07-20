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
define('PATTEMPLATE_FUNCTION_TRANSLATE_WARNING_LANGFOLDER_NOT_CREATABLE', 'patTemplate:Function:Translate:01');
define('PATTTEMPLATE_FUNCTION_TRANSLATE_WARNING_LANGFILE_NOT_CREATABLE', 'patTemplate:Function:Translate:02');
class patTemplate_Function_Translate extends patTemplate_Function{
	var $_name = 'Translate';

	function call($params, $content){
		$escape = isset($params['escape']) ? $params['escape'] : '';
		if(class_exists('JText')){

			if(count($params) > 0 && key_exists('key', $params)){
				$text = JText::_($params['key']);
			} else{
				$text = JText::_($content);
			}
		} else{
			if(defined($content)){
				$text = constant($content);
			} else{
				$text = $content;
			}
		}
		if($escape == 'yes' || $escape == 'true'){
			$text = addslashes($text);
		}
		return $text;
	}
}

?>
