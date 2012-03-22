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
 * @subpackage Categories
 */
class TOOLBAR_categories {
	/**
	 * Draws the menu for Editing an existing category
	 * @param int The published state (to display the inverse button)
	 */
	public static function _EDIT() {
		global $id;
		$option = mosGetParam($_REQUEST,'option','');

		mosMenuBar::startTable();
		mosMenuBar::media_manager();
		if($option == 'com_categories') { // boston, этот файлик используется еще в компоненте каталога ссылок, так что проверяем какой сейчас активен
			mosMenuBar::spacer();
			mosMenuBar::custom('save_and_new','-save','',_SAVE_AND_ADD,false);
		}
		mosMenuBar::spacer();
		mosMenuBar::save();
		mosMenuBar::spacer();
		if($id) // используем Ajax кнопку "Применить" только для уже существующей категории
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
		mosMenuBar::help('screen.categories.edit');
		mosMenuBar::endTable();
	}
	/**
	 * Draws the menu for Moving existing categories
	 * @param int The published state (to display the inverse button)
	 */
	public static function _MOVE() {
		mosMenuBar::startTable();
		mosMenuBar::save('movesave');
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
	/**
	 * Draws the menu for Copying existing categories
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
		$section = mosGetParam($_REQUEST,'section','');
		$option = mosGetParam($_REQUEST,'option','');

		mosMenuBar::startTable();
		if($section == 'content' || ($section > 0)) {
			mosMenuBar::ext(_CREATE_CONTENT,'index2.php?option=com_content&sectionid=0&task=new','-new');
		}
		mosMenuBar::publishList();
		mosMenuBar::spacer();
		mosMenuBar::unpublishList();
		mosMenuBar::spacer();
		if($section == 'content' || ($section > 0)) {
			mosMenuBar::customX('moveselect','-move','',_MOVE,true);
			mosMenuBar::spacer();
			mosMenuBar::customX('copyselect','-copy','',_COPY,true);
			mosMenuBar::spacer();
		}
		mosMenuBar::deleteList();
		mosMenuBar::spacer();
		mosMenuBar::editListX();
		mosMenuBar::spacer();
		mosMenuBar::addNewX();
		mosMenuBar::spacer();
		mosMenuBar::help('screen.categories');
		mosMenuBar::endTable();
	}
}
