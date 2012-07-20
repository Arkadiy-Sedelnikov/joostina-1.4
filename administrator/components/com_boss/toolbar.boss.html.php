<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_JLINDEX') or die();

class menuBOSS{
	public static function backSave(){
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::apply();
		mosMenuBar::save();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function newTmplField(){
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::addNew('new_tmpl_field');
		mosMenuBar::endTable();
	}

	public static function editTmplSource(){
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::save('save_tmpl_source');
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function addEditDelete(){
		mosMenuBar::startTable();
		mosMenuBar::addNew('new');
		mosMenuBar::editList('edit', _EDIT);
		mosMenuBar::deleteList(' ', 'delete', _DELETE);
		mosMenuBar::endTable();
	}

	public static function delete(){
		mosMenuBar::startTable();
		mosMenuBar::deleteList(' ', 'delete', _DELETE);
		mosMenuBar::endTable();
	}

	public static function edit_tmpl_field(){
		mosMenuBar::startTable();
		mosMenuBar::back();
		mosMenuBar::spacer();
		mosMenuBar::save('save_tmpl_field');
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	public static function addEditDeleteCopy(){
		mosMenuBar::startTable();
		mosMenuBar::addNew('new');
		mosMenuBar::editList('copy', _COPY);
		mosMenuBar::editList('edit', _EDIT);
		mosMenuBar::deleteList(' ', 'delete', _DELETE);
		mosMenuBar::endTable();
	}
}