<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @version        3.1.0
 * @package        patTemplate
 * @author        Stephan Schmidt <schst@php.net>
 * @license        LGPL
 * @link        http://www.php-tools.net
 */
// запрет прямого доступа
defined('_JLINDEX') or die();
class patTemplate_InputFilter_StripComments extends patTemplate_InputFilter{
	var $_name = 'StripComments';

	function apply($data){


		$data = preg_replace('°<!--.*-->°msU', '', $data);
		$data = preg_replace('°/\*.*\*/°msU', '', $data);
		return $data;
	}
}

?>
