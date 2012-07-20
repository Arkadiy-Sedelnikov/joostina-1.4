<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @package joomlaXplorer
 * @copyright soeren 2007
 * @author The joomlaXplorer project (http://joomlacode.org/gf/project/joomlaxplorer/)
 * @author The  The QuiX project (http://quixplorer.sourceforge.net)
 **/
defined('_JLINDEX') or die();
function show_footer(){
	echo "\n<br style=\"clear:both;\"/>
	<small>
	<a class=\"title\" href=\"" . $GLOBALS['jx_home'] . "\" target=\"_blank\">joomlaXplorer</a>
 (<a href=\"http://virtuemart.net/index2.php?option=com_versions&amp;catid=2&amp;myVersion=" .
		$GLOBALS['jx_version'] . "\" onclick=\"javascript:void window.open('http://virtuemart.net/index2.php?option=com_versions&catid=2&myVersion=" .
		$GLOBALS['jx_version'] .
		"', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=580,directories=no,location=no'); return false;\" title=\"" .
		$GLOBALS["messages"]["check_version"] . "\">" . $GLOBALS["messages"]["check_version"] .
		"</a>)
	</small>
	</div>
	<hr/>";
}


?>
