<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2007 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

class jpackScreens {
	// страница конфигурации
	public static function fConfig() {
		global $option;
		require_once (JPATH_BASE_ADMIN.'/components/com_joomlapack/includes/html.config.php');
	}
	// страница выполнения процесса бэкапа
	public static function fPack() {
		global $option;
		require_once (JPATH_BASE_ADMIN.'/components/com_joomlapack/includes/html.pack.php');
	}
	// самая первая страница
	public static function fMain() {
		global $option;
		require_once (JPATH_BASE_ADMIN.'/components/com_joomlapack/includes/html.main.php');
	}
	// список сохранённых бэкапов
	public static function fBUAdmin() {
		global $option;
		require_once (JPATH_BASE_ADMIN.'/components/com_joomlapack/includes/html.files.php');
	}
	// страница со списком каталогов исключаемых из бэкапа
	public static function fDirExclusion() {
		global $option;
		require_once (JPATH_BASE_ADMIN.'/components/com_joomlapack/includes/html.dirs.php');
	}
	// страница с логом выполнения работы
	public static function fLog() {
		global $option;
		require_once (JPATH_BASE_ADMIN.'/components/com_joomlapack/includes/html.log.php');
	}
}


class HTML_joomlapack {
	public static function showTables($option,$list,&$table_lists,$stats_list) {
		// подключение скрипта чудесных таблиц
		mosCommonHTML::loadPrettyTable();
		$content = "<form action=\"index2.php?option=com_joomlapack\" method=\"post\" name=\"adminForm\" id=\"adminForm\">\n"
				."<table class=\"adminheading\">\n"
				."<tr>\n"
				."<th class=\"db\">"._DB_MANAGEMENT."</th>\n"
				."</tr>\n"
				."</table>\n"
				."<table class=\"adminlist\" id=\"adminlist\" >\n"
				."<tr>\n"
				."\t<th width=\"1%\"><!--<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".count($table_lists).");\" />--></th>\n"
				."\t<th align=\"left\">"._SQL_TABLES."</th>\n"
				."\t<th width=\"5%\">"._DB_NUM_RECORDS."</th>\n"
				."\t<th width=\"5%\">"._JP_SIZE."</th>\n"
				."\t<th width=\"5%\">"._CAPTION."</th>\n"
				."\t<th width=\"5%\">"._JP_INCREMENT."</th>\n"
				."\t<th width=\"5%\">"._JP_CREATION_DATE."</th>\n"
				."\t<th width=\"5%\">"._JP_CHECKING."</th>\n"
				."</tr>\n".
				$list
				."<tr>\n"
				."<th colspan=\"2\">&nbsp;</th>\n"
				."<th align=\"right\">".$stats_list['rows']."</th>\n"
				."<th align=\"right\">".$stats_list['data']."</th>\n"
				."<th align=\"right\">".$stats_list['over']."</th>\n"
				."<th colspan=\"3\">&nbsp;</th>\n"
				."</tr>\n"
				."</table>\n"
				."<input type=\"hidden\" name=\"option\" value=\"$option\" />\n"
				."<input type=\"hidden\" name=\"act\" value=\"db\" />\n"
				."<input type=\"hidden\" name=\"task\" value=\"\" />\n"
				."<input type=\"hidden\" name=\"boxchecked\" value=\"0\" />\n"
				."<input type=\"hidden\" name=\"prettytablenoremclass\" id=\"prettytablenoremclass\" value=\"1\" />\n"
				."</form>\n";
		echo $content;
	}

	public static function showCheckResults($list,$title) {
		$content =
				"<table class=\"adminheading\">\n"
				."<tr>\n"
				."<th>".$title."</th>\n"
				."</tr>\n"
				."</table>\n"
				."<table class=\"adminlist\" id=\"adminlist\" >\n"
				."<tr>\n"
				."\t<th align=\"left\">"._SQL_TABLE."</th>\n"
				."\t<th align=\"left\" width=\"5%\">OP</th>\n"
				."\t<th align=\"left\" width=\"5%\">"._TYPE."</th>\n"
				."\t<th align=\"left\" width=\"5%\">"._COM_MESSAGES_STATUS."</th>\n"
				."</tr>\n".
				$list
				."<tr>\n"
				."\t<th colspan=\"4\">&nbsp;</th>\n"
				."</tr>\n"
				."</table>\n";
		echo $content;
	}
}