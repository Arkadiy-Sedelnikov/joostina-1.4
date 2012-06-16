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
 * @subpackage Messages
 */
class TOOLBAR_messages{
	public static function _VIEW(){
		mosMenuBar::startTable();
		mosMenuBar::customX('reply', '-move', '', _MAIL_ANSWER, false);
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function _EDIT(){
		mosMenuBar::startTable();
		mosMenuBar::save('save', _SEND_BUTTON);
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.messages.edit');
		mosMenuBar::endTable();
	}

	public static function _CONFIG(){
		mosMenuBar::startTable();
		mosMenuBar::save('saveconfig');
		mosMenuBar::spacer();
		mosMenuBar::cancel('cancelconfig');
		mosMenuBar::spacer();
		mosMenuBar::help('screen.messages.conf');
		mosMenuBar::endTable();
	}

	public static function _DEFAULT(){
		mosMenuBar::startTable();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.messages.inbox');
		mosMenuBar::endTable();
	}
}