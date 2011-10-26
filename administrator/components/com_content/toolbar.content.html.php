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
class TOOLBAR_content {
	public static function _EDIT() {
		global $id;
		mosMenuBar::startTable();
		mosMenuBar::preview('contentwindow',true);
		mosMenuBar::spacer();
		mosMenuBar::media_manager();
		mosMenuBar::spacer();
		mosMenuBar::custom('save_and_new','-save-and-new','',_SAVE_AND_ADD,false);
		mosMenuBar::spacer();
		mosMenuBar::save();
		mosMenuBar::spacer();
		if($id) {
			mosMenuBar::ext(_APPLY,'#','-apply','id="tb-apply" onclick="return ch_apply();"');
		}else {
			mosMenuBar::apply();
		}
		mosMenuBar::spacer();
		if($id) {
			mosMenuBar::cancel('cancel',_CLOSE);
		}else {
			mosMenuBar::cancel();
		}
		mosMenuBar::spacer();
		mosMenuBar::help('screen.content.edit');
		mosMenuBar::endTable();
	}

	public static function _CONFIG() {
		mosMenuBar::startTable();
		mosMenuBar::save('save_config');
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function _ARCHIVE() {
		mosMenuBar::startTable();
		mosMenuBar::unarchiveList();
		mosMenuBar::spacer();
		mosMenuBar::custom('remove','-delete','',_TO_TRASH,false);
		mosMenuBar::spacer();
		mosMenuBar::help('screen.content.archive');
		mosMenuBar::endTable();
	}

	public static function _MOVE() {
		mosMenuBar::startTable();
		mosMenuBar::custom('movesectsave','-save','',_SAVE,false);
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function _COPY() {
		mosMenuBar::startTable();
		mosMenuBar::custom('copysave','-save','',_SAVE,false);
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::archiveList();
		mosMenuBar::spacer();
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::customX('movesect','-move',null,_MOVE);
		mosMenuBar::spacer();
		mosMenuBar::customX('copy','-copy',null,_COPY);
		mosMenuBar::spacer();
		mosMenuBar::trash();
		mosMenuBar::spacer();
		mosMenuBar::editListX('editA');
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.content');
		mosMenuBar::endTable();
	}
}