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

global $option;
require_once (JPATH_BASE_ADMIN . '/components/com_joomlapack/includes/sajax.php');
require_once (JPATH_BASE_ADMIN . '/components/com_joomlapack/includes/ajaxtool.php');
?>
<script language="JavaScript" type="text/javascript">
	<?php
	sajax_show_javascript();
	?>
	var globRoot;
	function ToggleFilter(myRoot, myDir, myID) {
		var sCheckStatus = (document.getElementById(myID).checked == true) ? "on" : "off";
		globRoot = myRoot;
		document.getElementById("ajax_status").style.display = "block";
		x_toggleDirFilter(myRoot, myDir, sCheckStatus, ToggleFilter_cb);
	}
	function ToggleFilter_cb(myRet) {
		dirSelectionHTML(globRoot);
		document.getElementById("ajax_status").style.display = "none";
	}
	function dirSelectionHTML(myRoot) {
		globRoot = myRoot;
		x_dirSelectionHTML(myRoot, cb_dirSelectionHTML);
	}
	function cb_dirSelectionHTML(myRet) {
		document.getElementById("DEFOperationList").innerHTML = myRet;
	}
</script>

<div id="DEFScreen">
	<table class="adminheading">
		<tr>
			<th class="cpanel" nowrap rowspan="2"><?php echo _JP_DONT_SAVE_DIRECTORIES_IN_BACKUP?></th>
		</tr>
	</table>
	<div id="DEFOperationList">
		<script type="text/javascript">
			dirSelectionHTML('<?php echo JPATH_BASE; ?>');
		</script>
	</div>
</div>