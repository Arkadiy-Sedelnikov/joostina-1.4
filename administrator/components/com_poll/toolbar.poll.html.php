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
 * @subpackage Polls
 */
class TOOLBAR_poll{
	/**
	 * Draws the menu for a New category
	 */
	public static function _NEW(){
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.polls.edit');
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu for Editing an existing category
	 */
	public static function _EDIT($pollid, $cur_template){
		$database = database::getInstance();
		global $id;

		$sql = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0";
		$database->setQuery($sql);
		$cur_template = $database->loadResult();
		mosMenuBar::startTable();
		$popup = 'pollwindow';
		mosMenuBar::ext(_PREVIEW, '#', '-preview', " onclick=\"window.open('popups/$popup.php?pollid=$pollid&t=$cur_template', 'win1', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"");
		mosMenuBar::spacer();
		mosMenuBar::save();
		mosMenuBar::spacer();
		if($id){
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel('cancel', _CLOSE);
		} else{
			mosMenuBar::cancel();
		}
		mosMenuBar::spacer();
		mosMenuBar::help('screen.polls.edit');
		mosMenuBar::endTable();
	}

	public static function _DEFAULT(){
		mosMenuBar::startTable();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.polls');
		mosMenuBar::endTable();
	}
}