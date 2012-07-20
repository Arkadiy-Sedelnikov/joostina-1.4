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

global $JPConfiguration, $option;

$task = mosGetParam($_REQUEST, 'task', 'default');
$act = mosGetParam($_REQUEST, 'act', 'default');

?>
<table class="adminheading">
	<tr>
		<th class="config" nowrap rowspan="2"><?php echo _JP_BACKUP_CONFIG?></th>
	</tr>
</table>
<div class="message"><?php echo _JP_CONFIG_SAVING?>: <?php echo colorizeWriteStatus($JPConfiguration->isConfigurationWriteable());?></div>
<form action="index2.php" method="post" name="adminForm">
	<table class="adminform">
		<tr align="center" valign="middle">
			<th colspan="2"><?php echo _JP_MAIN_CONFIG?></th>
		</tr>
		<tr class="row0">
			<td width="30%"><?php echo _JP_CONFIG_DIRECTORY?>:</td>
			<td><input class="inputbox" type="text" name="outdir" size="60" value="<?php echo $JPConfiguration->OutputDirectory; ?>"/></td>
		</tr>
		<tr class="row0">
			<td><?php echo _JP_ARCHIVE_NAME?>:</td>
			<td><input class="inputbox" type="text" name="tarname" size="60" value="<?php echo $JPConfiguration->TarNameTemplate; ?>"/></td>
		</tr>
		<tr class="row1">
			<td><?php echo _JP_LOG_LEVEL?>:</td>
			<td><?php outputLogLevel($JPConfiguration->logLevel); ?></td>
		</tr>
		<tr>
			<th colspan="2"><?php echo _JP_ADDITIONAL_CONFIG?></th>
		</tr>
		<tr class="row1">
			<td><?php echo _JP_DELETE_PREFIX?>:</td>
			<td><?php echo mosHTML::yesnoRadioList('sql_pref', 'class="inputbox"', $JPConfiguration->sql_pref); ?></td>
		</tr>
		<tr class="row0">
			<td><?php echo _JP_EXPORT_TYPE?>:</td>
			<td><?php outputSQLCompat($JPConfiguration->MySQLCompat); ?></td>
		</tr>
		<tr class="row1">
			<td><?php echo _JP_FILELIST_ALGORITHM?>:</td>
			<td><?php AlgorithmChooser($JPConfiguration->fileListAlgorithm, "fileListAlgorithm"); ?></td>
		</tr>
		<tr class="row0">
			<td><?php echo _JP_CONFIG_DB_BACKUP?>:</td>
			<td><?php AlgorithmChooser($JPConfiguration->dbAlgorithm, "dbAlgorithm"); ?></td>
		</tr>
		<tr class="row1">
			<td><?php echo _JP_CONFIG_GZIP?>:</td>
			<td><?php AlgorithmChooser($JPConfiguration->packAlgorithm, "packAlgorithm"); ?></td>
		</tr>
		<tr class="row0">
			<td><?php echo _JP_CONFIG_DUMP_GZIP?>:</td>
			<td><?php outputBoolChooser($JPConfiguration->sql_pack); ?></td>
		</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="act" value="config"/>
</form>
<?php

// доступность сохранения настроек
function colorizeWriteStatus($status){
	if($status){
		return _JP_AVAILABLE;
	} else{
		return _JP_NOT_AVAILABLE;
	}
}

// тип экспорта базы данных
function outputSQLCompat($sqlcompat){
	$options = array(array(
		"value" => "compat", "desc" => _JP_MYSQL4_COMPAT),
		array(
			"value" => "default", "desc" => _DEFAULT));
	echo '<select class="inputbox" name="sqlcompat">';
	foreach($options as $choice){
		$selected = ($sqlcompat == $choice['value']) ? "selected" : "";
		echo "<option value=\"" . $choice['value'] . "\" $selected>" . $choice['desc'] . "</option>";
	}
	echo '</select>';
}

// типы сжатия
function outputBoolChooser($boolOption){
	echo '<select class="inputbox" name="sql_pack">';
	$selected = ($boolOption == "0") ? "selected" : "";
	echo "<option value=\"0\" $selected>" . _JP_NO_GZIP . "</option>";
	$selected = ($boolOption == "1") ? "selected" : "";
	echo "<option value=\"1\" $selected>" . _JP_GZIP_TAR_GZ . "</option>";
	$selected = ($boolOption == "2") ? "selected" : "";
	echo "<option value=\"2\" $selected>" . _JP_GZIP_ZIP . "</option>";
	echo '</select>';
}

// резервирования
function AlgorithmChooser($strOption, $strName){
	echo "<select class=\"inputbox\" name=\"$strName\">";
	$selected = ($strOption == "single") ? "selected" : "";
	echo "<option value=\"single\" $selected>" . _JP_QUICK_METHOD . "</option>";
	$selected = ($strOption == "smart") ? "selected" : "";
	echo "<option value=\"smart\" $selected>" . _JP_STANDARD_METHOD . "</option>";
	$selected = ($strOption == "multi") ? "selected" : "";
	echo "<option value=\"multi\" $selected>" . _JP_SLOW_METHOD . "</option>";
	echo '</select>';
}

// список уровней регистрации лога
function outputLogLevel($strOption){
	echo '<select class="inputbox" name="logLevel">';
	$selected = ($strOption == "1") ? "selected" : "";
	echo "<option value=\"1\" $selected>" . _JP_LOG_ERRORS_OLY . "</option>";
	$selected = ($strOption == "2") ? "selected" : "";
	echo "<option value=\"2\" $selected>" . _JP_LOG_ERROR_WARNINGS . "</option>";
	$selected = ($strOption == "3") ? "selected" : "";
	echo "<option value=\"3\" $selected>" . _JP_LOG_ALL . "</option>";
	$selected = ($strOption == "4") ? "selected" : "";
	echo "<option value=\"4\" $selected>" . _JP_LOG_ALL_DEBUG . "</option>";
	echo '</select>';
}