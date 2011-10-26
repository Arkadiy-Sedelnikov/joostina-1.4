<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

switch($task) {
	case 'edit';
		mosMenuBar::startTable();
		mosMenuBar::ext(_APPLY,'#','-apply','id="tb-apply" onclick="UpdateImg(xajax.getFormValues(\'adminForm\'))"');
		mosMenuBar::ext(_JWMM_CANCEL_ALL,'#','-unpublis','onclick="xajax_OriginalImage(xajax.getFormValues(\'adminForm\'));"');
		mosMenuBar::ext(_SAVE,'#','-save','onclick="submitform(\'saveimage\');"');
		mosMenuBar::ext(_CLOSE,'#','-cancel','onclick="submitform(\'returnfromedit\');"');
		mosMenuBar::endTable();
		break;
	default;
		break;
}