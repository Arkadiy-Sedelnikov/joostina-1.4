<?php 
/**
* @package Joostina
* @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
*/
$lang->setModule("youtube");
$lang->setBlock("youtube_prop");
?>
<script type="text/javascript" src="<?php echo SpawConfig::getStaticConfigValue('SPAW_DIR') ?>plugins/youtube/dialogs/youtube_prop.js"></script>

<script type="text/javascript">
<!--
var spawErrorMessages = new Array();
<?php
echo 'spawErrorMessages["error_wrong_youtube_url"] = "' . $lang->m('error_wrong_youtube_url') . '";' . "\n";
?>
//-->
</script>
<table border="0" cellspacing="0" cellpadding="2" width="336">
<form name="img_prop" id="img_prop" onsubmit="return false;">
<tr>
<td><?php echo $lang->m('source')?>:</td>
</tr>
<tr>
<td nowrap><input type="text" name="csrc" id="csrc" class="input" size="80" /></td>
</tr>
<tr>
<td nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="2" align="right" valign="bottom" nowrap>
<input type="submit" value="<?php echo $lang->m('ok')?>" onClick="SpawYouTubePropDialog.okClick()" class="bt" />
<input type="button" value="<?php echo $lang->m('cancel')?>" onClick="SpawYouTubePropDialog.cancelClick()" class="bt" />
</td>
</tr>
</form>
</table>