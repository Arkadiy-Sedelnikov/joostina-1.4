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
 * @subpackage Menus
 */
class TOOLBAR_menumanager {
	/**
	 * Draws the menu for the Menu Manager
	 */
	public static function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::customX('copyconfirm','-copy','',_COPY,true);
		mosMenuBar::spacer();
		mosMenuBar::customX('deleteconfirm','-delete','',_DELETE,true);
		mosMenuBar::spacer();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.menumanager');
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu to delete a menu
	 */
	public static function _DELETE() {
		mosMenuBar::startTable();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu to create a New menu
	 */
	public static function _NEWMENU() {
		mosMenuBar::startTable();
		mosMenuBar::custom('savemenu','-save','',_SAVE,false);
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.menumanager.new');
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu to create a New menu
	 */
	public static function _COPYMENU() {
		mosMenuBar::startTable();
		mosMenuBar::custom('copymenu','-copy','',_COPY,false);
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.menumanager.copy');
		mosMenuBar::endTable();
	}
}