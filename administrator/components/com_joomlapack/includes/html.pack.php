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

$task = mosGetParam($_REQUEST, 'task', '');
$act = mosGetParam($_REQUEST, 'act', 'default');

global $JPConfiguration, $option;

$siteRoot = JPATH_BASE;

?>
<table class="adminheading">
	<tr>
		<th class="cpanel"><?php echo _JP_BACKUP_CREATION?></th>
	</tr>
</table>
<script type="text/JavaScript">
	<?php
	sajax_show_javascript();
	?>
	var tElapsed = 0;
	var tStart = null;
	var timerID = 0;
	var CUBEArray = null;
	var LastTimestamp = null;
	var GUItimerID = null;
	var DoDebug = false;

	sajax_fail_handle = SAJAXTrap;

	function WriteDebug(myString) {
		if (DoDebug) {
			SRAX.get('Debug').innerHTML += myString;
		}
	}
	function SAJAXTrap(myData) {
		StopTimer();
		x_errorTrapReport(myData, SAJAXTrap_cb);
	}
	function SAJAXTrap_cb(myRet) {
		SRAX.get('Timeout').style.display = 'block';
		SRAX.get('startInfo').style.display = 'none';
	}
	function UpdateTimer() {
		if (timerID) {
			clearTimeout(timerID);
		}
		if (!LastTimestamp) {
			if (typeof(CUBEArray) == "object") {
				tStart = new Date();
				LastTimestamp = CUBEArray['Timestamp'];
			}
		} else {
			if (typeof(CUBEArray) != "object") {
				StopTimer();
			} else {
				if (CUBEArray['Timestamp'] != LastTimestamp) {
					tStart = new Date();
					LastTimestamp = CUBEArray['Timestamp'];
				} else {
					var tDate = new Date();
					var tDiff = tDate.getTime() - tStart.getTime();
					tDate.setTime(tDiff);
					tElapsed = tDate.getMinutes() * 60 + tDate.getSeconds();
					if (tElapsed > 60) {
						StopTimer();
						SRAX.get('Timeout').style.display = "block";
					} else {
						timerID = setTimeout("UpdateTimer()", 10000);
					}
				}
			}
		}
	}
	function StartTimer() {
		StopTimer();
		LastTimestamp = null;
		SRAX.get('Timeout').style.display = 'none';
		SRAX.get('down_link').style.display = 'none';
		SRAX.get('done').style.display = 'none';
		SRAX.get('startInfo').style.display = 'block';
		tStart = new Date();
		timerID = setTimeout("UpdateTimer()", 10000);
	}
	function StopTimer() {
		if (timerID) {
			clearTimeout(timerID);
			timerID = 0;
		}
		tStart = null;
		LastTimestamp = null;
	}
	function do_Start(onlyDBMode) {
		x_tick(1, onlyDBMode, do_Start_cb);
	}
	function do_Start_cb(myRet) {
		StartTimer();
		CUBEArray = myRet;
		ParseCUBEArray();
		do_tick();
	}
	function do_tick() {
		WriteDebug('Tick()<br />');
		x_tick(0, do_tick_cb);
	}
	function do_tick_cb(myRet) {
		StopGUITimer();
		CUBEArray = myRet;
		ParseCUBEArray();
		if (typeof(CUBEArray) != "object") {
			AllDone();
		} else {
			if (CUBEArray['Domain'] == "finale") {
				AllDone();
			} else {
				do_tick();
			}
		}
	}
	function do_getCUBEArray() {
		StopGUITimer();
		x_getCUBEArray(do_getCUBEArray_cb);
	}
	function do_getCUBEArray_cb(myRet) {
		CUBEArray = myRet;
		ParseCUBEArray();
		StartGUITimer();
	}
	function StartGUITimer() {
		StopGUITimer();
		GUItimerID = setTimeout("GUITimer()", 2000);
	}
	function StopGUITimer() {
		if (GUItimerID) {
			clearTimeout(GUItimerID);
			GUItimerID = 0;
		}
	}
	function GUITimer() {
		if (GUItimerID) {
			clearTimeout(GUItimerID);
		}
		do_getCUBEArray();
	}
	function ParseCUBEArray() {
		if (typeof( CUBEArray ) != "object") {
			AllDone();
		} else {
			if (CUBEArray['Domain'] == 'FileList') {
				SRAX.get('pack_step_1').className = 'pack_step_activ';
				SRAX.get('state_1').innerHTML = SRAX.get('Init').innerHTML;
				CUBEArray['Substep'] = '<?php echo _JP_GET_FILE_LISTING?>';
			} else if (CUBEArray['Domain'] == 'PackDB') {
				SRAX.get('pack_step_1').className = 'pack_step_done';
				SRAX.get('pack_step_2').className = 'pack_step_activ';
				SRAX.get('state_1').innerHTML = '';
				SRAX.get('state_2').innerHTML = SRAX.get('Init').innerHTML;
			} else if (CUBEArray['Domain'] == 'Packing') {
				SRAX.get('pack_step_2').className = 'pack_step_done';
				SRAX.get('pack_step_3').className = 'pack_step_activ';
				SRAX.get('state_2').innerHTML = '';
				SRAX.get('state_3').innerHTML = SRAX.get('Init').innerHTML;
			} else if (CUBEArray['Domain'] == 'finale') {
				SRAX.get('pack_step_1').className = 'pack_step_done';
				SRAX.get('pack_step_2').className = 'pack_step_done';
				SRAX.get('pack_step_3').className = 'pack_step_done';
				SRAX.get('pack_step_4').className = 'pack_step_done';
				SRAX.get('state_2').innerHTML = '';
				SRAX.get('state_3').innerHTML = '';
				AllDone();
			}
			SRAX.get('JPStep').innerHTML = CUBEArray['Step'];
			SRAX.get('JPSubstep').innerHTML = CUBEArray['Substep'];
			if (CUBEArray['backfile'] != '') {
				SRAX.get('back_file').innerHTML = CUBEArray['backfile'];
				SRAX.get('back_file').href = 'index2.php?option=com_joomlapack&subtask=downloadfile&filename=' + CUBEArray['backfile'];
				SRAX.get('back_file_top').innerHTML = CUBEArray['backfile'];
				SRAX.get('back_file_top').href = 'index2.php?option=com_joomlapack&subtask=downloadfile&filename=' + CUBEArray['backfile'];
			}

		}
	}
	function AllDone() {
		StopTimer();
		StopGUITimer();
		SRAX.get('Timeout').style.display = 'none';
		SRAX.get('startInfo').style.display = 'none';
		SRAX.get('down_link').style.display = 'block';
		SRAX.get('done').style.display = 'block';
	}
</script>
<div class="jwarning" id="startInfo"><?php echo _JP_DONT_CLOSE_BROWSER_WINDOW?></div>
<div class="jwarning" id="Timeout" style="display:none"><?php echo _JP_ERRORS_VIEW_LOG?></div>
<div class="message" id="done" style="display:none"><?php echo _JP_BACKUP_SUCCESS?>: <a href="" id="back_file_top">&nbsp;</a></div>
<div id="pack_step">
	<div id="pack_step_1" class="pack_step"><?php echo _JP_CREATION_FILELIST?></div>
	<span id="state_1"></span>

	<div id="pack_step_2" class="pack_step"><?php echo _JP_BACKUPPING_DB?></div>
	<span id="state_2"></span>

	<div id="pack_step_3" class="pack_step"><?php echo _JP_CREATION_OF_ARCHIVE?></div>
	<span id="state_3"></span>

	<div id="pack_step_4" class="pack_step"><?php echo _JP_ALL_COMPLETED_2?>.<br/><span id="down_link" style="display:none"><?php echo _JP_DOWNLOAD_FILE?>: <a href="" id="back_file">&nbsp;</a></span>
	</div>
</div>
<span id="Init" style="display:none">
	<div><?php echo _JP_PROGRESS?>: <b><span id="JPStep"></span></b></div>
	<div><?php echo _E_STATE?>: <b><span id="JPSubstep">0</span></b></div>
</span>