<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();


class menubanners{
	/**
	 * Draws the menu for a New banner
	 */
	public static function NEW_EDIT_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::save('savebanner');
		mosMenuBar::apply('applybanner');
		mosMenuBar::cancel('cancelbanner');
		mosMenuBar::endTable();
	}

	public static function DEFAULT_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::ext(_TASK_UPLOAD, '#', '-media-manager', 'id="tb-media-manager" onclick="popupWindow(\'components/com_banners/uploadbanners.php\',\'win1\',250,100,\'no\');"');
		mosMenuBar::publishList('publishbanner');
		mosMenuBar::unpublishList('unpublishbanner');
		mosMenuBar::addNew('newbanner');
		mosMenuBar::editList('editbanner');
		mosMenuBar::deleteList('', 'removebanners');
		mosMenuBar::back(_BANNERS_PANEL, 'index2.php?option=com_banners');
		mosMenuBar::endTable();
	}

	public static function MAIN_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::back(_BANNERS_PANEL, 'index2.php?option=com_banners');
		mosMenuBar::endTable();
	}
}

class menubannerClient{

	/**
	 * Draws the menu for a New client
	 */
	public static function NEW_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::save('saveclient');
		mosMenuBar::cancel('cancelclient');
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu for a client
	 */
	public static function EDIT_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::save('saveclient');
		mosMenuBar::cancel('cancelclient');
		mosMenuBar::endTable();
	}

	/**
	 * Draws the default menu
	 */
	public static function DEFAULT_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::publishList('publishclient');
		mosMenuBar::unpublishList('unpublishclient');
		mosMenuBar::addNew('newclient');
		mosMenuBar::editList('editclient');
		mosMenuBar::deleteList('', 'removeclients');
		mosMenuBar::back(_BANNERS_PANEL, 'index2.php?option=com_banners');
		mosMenuBar::endTable();
	}
}

class menubannerCategory{
	/**
	 * Draws the menu for a New category
	 */
	public static function NEW_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::save('savecategory');
		mosMenuBar::cancel('cancelcategory');
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu for Editting an existing category
	 * @param int The published state (to display the inverse button)
	 */
	public static function EDIT_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::save('savecategory');
		mosMenuBar::cancel('cancelcategory');
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu for Editting an existing category
	 */
	public static function DEFAULT_MENU(){
		mosMenuBar::startTable();
		mosMenuBar::publishList('publishcategory');
		mosMenuBar::unpublishList('unpublishcategory');
		mosMenuBar::addNew('newcategory');
		mosMenuBar::editList('editcategory');
		mosMenuBar::deleteList('', 'removecategory');
		mosMenuBar::back(_BANNERS_PANEL, 'index2.php?option=com_banners');
		mosMenuBar::endTable();
	}
}