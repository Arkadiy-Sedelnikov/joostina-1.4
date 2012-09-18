<?php
$lang->setModule("yahooMaps");
$lang->setBlock("yahooMaps_prop");
?>
<script type="text/javascript" src="<?php echo SpawConfig::getStaticConfigValue('SPAW_DIR') ?>plugins/yahooMaps/dialogs/yahooMaps_prop.js"></script>
<script type="text/javascript">
<!--
	var spawErrorMessages = new Array();
	<?php
		echo 'spawErrorMessages["error_wrong_yahooMaps_address"] = "' . $lang->m('error_wrong_yahooMaps_address') . '";' . "\n";
		echo 'spawErrorMessages["error_wrong_yahooMaps_desc"] = "' . $lang->m('error_wrong_yahooMaps_desc') . '";' . "\n";
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
		<tr>
			<td><?php echo $lang->m('description')?>:</td>
		</tr>
		<tr>
			<td nowrap><input type="text" name="cdesc" id="cdesc" class="input" size="80" /></td>
		</tr>
		<tr>
			<td nowrap>
				<hr width="100%">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right" valign="bottom" nowrap>
				<input type="submit" value="<?php echo $lang->m('ok')?>" onClick="SpawyahooMapsPropDialog.okClick()" class="bt">
				<input type="button" value="<?php echo $lang->m('cancel')?>" onClick="SpawyahooMapsPropDialog.cancelClick()" class="bt">
			</td>
		</tr>
	</form>
</table>