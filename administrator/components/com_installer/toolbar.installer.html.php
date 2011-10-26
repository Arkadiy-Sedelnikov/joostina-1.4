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
 * @subpackage Installer
 */
class TOOLBAR_installer {
	public static function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::help('screen.installer');
		mosMenuBar::endTable();
	}

	public static function _DEFAULT2() {
		mosMenuBar::startTable();
		mosMenuBar::deleteList('','remove',_DELETE);
		mosMenuBar::spacer();
		mosMenuBar::help('screen.installer2');
		mosMenuBar::endTable();
	}

	public static function _NEW() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
}