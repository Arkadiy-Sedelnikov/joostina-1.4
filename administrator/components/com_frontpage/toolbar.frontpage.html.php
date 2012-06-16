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

/**
 * @package Joostina
 * @subpackage Content
 */
class TOOLBAR_FrontPage{
	public static function _DEFAULT(){
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::custom('remove', '-delete', '', _DELETE, true);
		mosMenuBar::spacer();
		mosMenuBar::custom('settings', '-check', '', _SETTINGS, false);
		mosMenuBar::spacer();
		mosMenuBar::help('screen.frontpage');
		mosMenuBar::endTable();
	}

	public static function _SETTINGS(){
		mosMenuBar::startTable();
		mosMenuBar::save('save_settings');
		mosMenuBar::apply('apply_settings');
		mosMenuBar::cancel('cancel');
		mosMenuBar::endTable();
	}
}