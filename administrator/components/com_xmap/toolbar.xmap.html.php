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

/** Administrator Toolbar output */
class TOOLBAR_xmap{
	/**
	 * Draws the toolbar
	 */
	public static function _DEFAULT(){
		mosMenuBar::startTable();
		mosMenuBar::ext(_SAVE_SITEMAP, '#', '-new', 'onclick="addSitemap();return false;"');
		mosMenuBar::endTable();
	}
}