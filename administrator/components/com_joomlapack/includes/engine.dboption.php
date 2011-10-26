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

require_once ($mainframe->getPath('admin_html'));

global $task;

$boxchecked	= intval(mosGetParam($_REQUEST,'boxchecked',0));
$tables		= mosGetParam($_POST,'tables','');

if($task!='' && $task!='viewTables' && $boxchecked && (!is_array($tables))) $task='viewTables';

switch($task) {
	case 'doCheck':
		checkDatabase($option,$task);
		break;
	case 'doAnalyze':
		checkDatabase($option,$task);
		break;
	case 'doOptimize':
		checkDatabase($option,$task);
		break;
	case 'doRepair':
		checkDatabase($option,$task);
		break;
	case 'viewTables':
		viewTables($option);
		break;
	default:
		viewTables($option);
		break;
}

// отображение списка таблиц
function viewTables($option) {
	global $database,$mosConfig_db;
	$sql = 'SHOW TABLE STATUS FROM `'.$mosConfig_db.'`';
	$database->setQuery($sql);
	$table_lists = $database->loadObjectList();
	$i = 0;
	$lists = '';
	$stats_list['rows'] = '';
	$stats_list['data'] = '';
	$stats_list['over'] = '';
	$k = 0;
	foreach($table_lists as $table) {
		if($table->Check_time != "") {
			$check_time = strftime('%d.%m.%Y %H:%M',strtotime($table->Check_time));
		} else {
			$check_time = "";
		}
		$lists .=
				"<tr class=\"row$k\">"
				."\t<td width=\"1%\"><input type=\"checkbox\" id=\"cb".$i++."\" name=\"tables[]\" value=\"".$table->Name."\" onclick=\"isChecked(this.checked);\" /></td>\n"
				."\t<td>".$table->Name."</td>\n"
				."\t<td align=\"right\">".number_format($table->Rows,0,',','.')."</td>\n"
				."\t<td align=\"right\">".mosGetSizes($table->Data_length)."</td>\n"
				."\t<td align=\"right\">".mosGetSizes($table->Data_free)."</td>\n"
				."\t<td align=\"right\">".number_format($table->Auto_increment,0,',','.')."</td>\n"
				."\t<td align=\"right\" style=\"white-space: nowrap;\">".strftime('%d.%m.%Y %H:%M',strtotime($table->Create_time))."</td>\n"
				."\t<td align=\"right\" style=\"white-space: nowrap;\">".$check_time."</td>\n".
				"</tr>\n";
		$stats_list['rows'] = $stats_list['rows'] + $table->Rows;
		$stats_list['data'] = $stats_list['data'] + $table->Data_length;
		$stats_list['over'] = $stats_list['over'] + $table->Data_free;
		$k = 1 - $k;
	}
	$stats_list['rows'] = number_format($stats_list['rows'],0,',','.');
	$stats_list['data'] = mosGetSizes($stats_list['data']);
	$stats_list['over'] = mosGetSizes($stats_list['over']);
	HTML_joomlapack::showTables($option,$lists,$table_lists,$stats_list);
}

// работа с базой, оптимизация, восстановление и т.д.
function checkDatabase($option,$func) {
	global $tables,$database;
	$i = 0;
	$tables = mosGetParam($_POST,'tables','');
	if(is_array($tables)) {
		switch($func) {
			case 'doCheck':
				$sql = 'CHECK TABLE ';
				$title = _JP_CHECK_RESULTS;
				break;
			case 'doAnalyze':
				$sql = 'ANALYZE TABLE ';
				$title = _JP_ANALYZE_RESULTS;
				break;
			case 'doOptimize':
				$sql = 'OPTIMIZE TABLE ';
				$title = _JP_OPTIMIZE_RESULTS;
				break;
			case 'doRepair':
				$sql = 'REPAIR TABLE ';
				$title = _JP_REPAIR_RESULTS;
				break;
		}
		foreach($tables as $table) {
			$i++;
			if($i != count($tables)) {
				$sql .= '`'.$table.'`, ';
			} else {
				$sql .= '`'.$table.'`';
			}
		}
		$database->setQuery($sql);
		$result_msgs = $database->loadObjectList();
		$list = '';
		$results = false;
		if(!$results) {
			$k = 0;
			foreach($result_msgs as $result_msg) {
				$list .=
						"<tr class=\"row$k\">"
						."\t<td align=\"left\" style=\"white-space: nowrap;\">".$result_msg->Table."</td>\n"
						."\t<td align=\"center\" style=\"white-space: nowrap;\">".$result_msg->Op."</td>\n"
						."\t<td align=\"center\" style=\"white-space: nowrap;\">".$result_msg->Msg_type."</td>\n"
						."\t<td align=\"center\" style=\"white-space: nowrap;\">".$result_msg->Msg_text."</td>\n"
						."</tr>\n";
				$results = true;
				$k = 1 - $k;
			}
			HTML_joomlapack::showCheckResults($list,$title);
		}
	}
}
// отображение занимаемого размера файла
function mosGetSizes($size) {
	if($size < 1024)$size = number_format(Round($size,3),0,',','.')." B";
	elseif($size < 1048576) $size = number_format(Round($size / 1024,3),2,',','.')." KB";
	elseif($size < 1073741824) $size = number_format(Round($size / 1048576,3),2,',','.')." MB";
	elseif(1073741824 < $size) $size = number_format(Round($size / 1073741824,3),2,',','.')." GB";
	elseif(1099511627776 < $size) $size = number_format(Round($size / 1099511627776,3),2,',','.')." TB";
	return $size;
}