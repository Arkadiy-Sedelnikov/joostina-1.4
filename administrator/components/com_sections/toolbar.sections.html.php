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
 * @subpackage Sections
 */
class TOOLBAR_sections {
	public static function _MASADD() {
		mosMenuBar::startTable();
		mosMenuBar::save('massave');
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function _MASNEW() {
		mosMenuBar::startTable();
		mosMenuBar::custom('masadd','-new','',_MENU_MASS_ADD);
		mosMenuBar::endTable();
	}

	/**
	 * Draws the menu for Editing an existing category
	 */
	public static function _EDIT() {
		global $id;

		mosMenuBar::startTable();
		mosMenuBar::media_manager();
		mosMenuBar::spacer();
		mosMenuBar::custom('save_and_new','-save','',_SAVE_AND_ADD,false);
		mosMenuBar::spacer();
		mosMenuBar::save();
		mosMenuBar::spacer();
		if($id) // используем Ajax кнопку "Применить" только для уже существующего рездела
		// кнопка "Применить" с Ajax
			mosMenuBar::ext(_APPLY,'#','-apply','id="tb-apply" onclick="ch_apply();return;"');
		else
			mosMenuBar::apply();

		mosMenuBar::spacer();
		if($id) {
			// for existing content items the button is renamed `close`
			mosMenuBar::cancel('cancel',_CLOSE);
		} else {
			mosMenuBar::cancel();
		}
		mosMenuBar::spacer();
		mosMenuBar::help('screen.sections.edit');
		mosMenuBar::endTable();
	}
	/**
	 * Draws the menu for Copying existing sections
	 * @param int The published state (to display the inverse button)
	 */
	public static function _COPY() {
		mosMenuBar::startTable();
		mosMenuBar::save('copysave');
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
	/**
	 * Draws the menu for Editing an existing category
	 */
	public static function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::ext(_CREATE_CATEGORY,'index2.php?option=com_categories&task=new','-new');
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		mosMenuBar::customX('copyselect','-copy','',_COPY,true);
		mosMenuBar::spacer();
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.sections');
		mosMenuBar::endTable();
	}
}