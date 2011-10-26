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

class TOOLBAR_jpack {
	public static function _CONFIG() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::apply();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
	public static function _PACK() {
		mosMenuBar::startTable();
		mosMenuBar::ext(_JP_FULL_BACKUP,'#','-apply','id="tb-apply" onclick="do_Start(0);return;"');
		mosMenuBar::ext(_JP_BACKUP_BASE,'#','-apply','id="tb-apply" onclick="do_Start(1);return;"');
		mosMenuBar::spacer();
		mosMenuBar::back(_JP_BACKUP_PANEL,'index2.php?option=com_joomlapack');
		mosMenuBar::endTable();
	}
	public static function _DEF() {
		mosMenuBar::startTable();
		mosMenuBar::back(_JP_BACKUP_PANEL,'index2.php?option=com_joomlapack');
		mosMenuBar::endTable();
	}
	public static function _DB_MENU() {
		mosMenuBar::startTable();
		mosMenuBar::back(_DB_MANAGEMENT,'index2.php?option=com_joomlapack&act=db');
		mosMenuBar::spacer();
		mosMenuBar::back(_JP_BACKUP_PANEL,'index2.php?option=com_joomlapack');
		mosMenuBar::endTable();
	}
	public static function _DB_DEFAULT() {
		global $act;
		mosMenuBar::startTable();
		mosMenuBar::custom('doCheck','-check','',_JP_CHECK);
		mosMenuBar::spacer();
		mosMenuBar::custom('doAnalyze','-info','',_JP_ANALYSE);
		mosMenuBar::spacer();
		mosMenuBar::custom('doOptimize','-optimize','',_JP_OPTIMIZE);
		mosMenuBar::spacer();
		mosMenuBar::custom('doRepair','-help','',_JP_REPAIR);
		if($act!='db') {
			mosMenuBar::spacer();
			mosMenuBar::back(_DB_MANAGEMENT,'index2.php?option=com_joomlapack&ack=db');
		}
		mosMenuBar::spacer();
		mosMenuBar::back(_JP_BACKUP_PANEL,'index2.php?option=com_joomlapack');
		mosMenuBar::endTable();
	}
}