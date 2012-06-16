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

class HTML_banners{

	public static function showBanners(&$rows, &$clist, &$clientlist, $myid, &$pageNav, $option){
		mosCommonHTML::loadOverlib();
		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';

		?>
	<table border="0" class="adminheading">
		<tbody>
		<tr>
			<th class="cpanel"><?php echo _BANNERS_MANAGEMENT?></th>
		</tr>
		</tbody>
	</table>

	<form action="index2.php" method="POST" name="adminForm">

		<table cellpadding="3" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<th width="17">#</th>
				<th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);"/></th>
				<th>ID</th>
				<th align="left"><?php echo _ABP_BANNER_NAME; ?></th>
				<th><?php echo _ABP_CATEGORY; ?></th>
				<th align="left"><?php echo _ABP_CLIENT_NAME; ?></th>
				<th><?php echo _ABP_IMPMADE; ?></th>
				<th><?php echo _ABP_IMPLEFT; ?></th>
				<th width="90"><?php echo _ABP_CLICKS; ?></th>
				<th width="90"><?php echo _ABP_PRCLICKS; ?></th>
				<th width="90"><?php echo _ABP_REPEAT_TYPE; ?></th>
				<th width="47"><?php echo _PUBLISHED; ?></th>
			</tr>
			<?php
			$k = 0;
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = &$rows[$i];

				// calcolo le visualizzazioni rimaste
				$imp_left = $row->imp_total - $row->imp_made;
				if($imp_left < 0){
					$imp_left = _ABP_UNLIMITED;
				}

				// calcolo le percentuali di click e il costo
				$percentClicks = 0;
				$total_clickvalue = 0;
				$pay_imp = 0;
				$percentClicksTot = 0;

				if($row->imp_made != 0){
					$percentClicks = substr(100 * $row->clicks / $row->imp_made, 0, 4);
					//Set total price
					$total_clickvalue = substr($row->clicks * $row->click_value, 0, 4);
					$pay_imp = substr($row->imp_made * $row->imp_value, 0, 4);
					$percentClicksTot = substr(100 * $row->complete_clicks / $row->imp_made, 0, 4);
				}

				// se una image visualizzo l'anteprima
				$info = '<br>' . _ABP_COSTS . '<br>';
				$info .= _ABP_TOTAL_PRICE . ': ' . _ABP_CURRENCY . ' ' . $row->click_value . ' / ' . _ABP_CURRENCY . ' ' . $total_clickvalue;
				$info .= '<br>' . _ABP_PRICE_IMPRESSION . ': ' . _ABP_CURRENCY . ' ' . $row->imp_value . ' / ' . _ABP_CURRENCY . ' ' . $pay_imp;

				if(preg_match("/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i", $row->image_url)){
					$over = 'onmouseover="return overlib(\'<img border=0 src=../images/show/' . str_replace(' ', '%20', $row->image_url) . '\><br>' . $info . '\',CAPTION,\'' . _ABP_PREVIEW . '\',WIDTH,468);" onmouseout="return nd();"';
				} else{
					$over = 'onmouseover="return overlib(\'' . _ABP_PREVIEW_NOT_DISP . '<br>' . $info . '\',CAPTION,\'' . _ABP_PREVIEW . '\',WIDTH,468);" onmouseout="return nd();"';
				} ?>


				<tr class="<?php echo "row$k"; ?>">
					<td width="17" align="center"><?php echo $pageNav->rowNumber($i); ?></td>
					<td width="20">
						<?php     if($row->checked_out && $row->checked_out != $myid){
						echo '<img src="' . $cur_file_icons_path . '/checked_out.png" border="0" alt="', _ABP_BANNER_IN_USE, '">';
					} else{
						echo '<input type="checkbox" id="cb', $i, '" name="cid[]" value="', $row->id, '" onClick="isChecked(this.checked);" />';
					}
						?>
					</td>
					<td width="18" align="center"><b><?php echo $row->id; ?></b></td>
					<td width="188" align="left">
						<a href="#editbanner" <?php echo $over; ?> onclick="return listItemTask('cb<?php echo $i; ?>','editbanner')"><?php echo $row->name; ?></a>
					</td>
					<td width="149" align="center"><?php echo $row->category; ?>&nbsp;</td>
					<td width="188" align="left"><?php echo $row->cl_name; ?>&nbsp;</td>
					<td width="108" align="center"><?php echo $row->imp_made; ?></td>
					<td width="92" align="center"><?php echo $imp_left; ?></td>
					<td width="90" align="center"> <?php echo $row->complete_clicks; ?> / <?php echo $row->clicks; ?> </td>
					<td width="90" align="center"><?php echo $percentClicksTot; ?> / <?php echo $percentClicks; ?></td>

					<td width="90" align="center"><?php
						if($row->reccurtype == 1){
							$days = explode(',', $row->reccurweekdays);
							$daysnames = '';
							foreach($days as $day){
								$daysnames .= getShortDayName($day) . ' ';
							}

							echo $daysnames;
						} else{
							echo _ABP_ALLDAYS;
						}
						?>
					</td>
					<?php
					$times = "<tr><td>" . _MAIL_FROM . " : " . $row->publish_up_date . "</td></tr>";
					$times .= "<tr><td>" . _ABP_TO . " : ";

					if(isset($row->publish_down_date)){
						if($row->publish_down_date == '0000-00-00'){
							$times .= _ABP_NEVER;
						} else{
							$times .= $row->publish_down_date;
						}
					}
					$times .= "</td></tr>";

					$times .= "<tr><td>" . _ABP_EVENT_STARTHOURS . " : " . $row->publish_up_time . "</td></tr>";
					if($row->publish_down_time != '00:00:00')
						$times .= "<tr><td>" . _ABP_EVENT_ENDHOURS . " : " . $row->publish_down_time . "</td></tr>";

					$times .= "<tr><td>";

					switch(getStato($row)){
						case BANNER_IN_ATTIVAZIONE:
							$img = 'publish_y.png'; // in attivazione
							$times .= _ABP_PUB_BIC;
							break;

						case BANNER_ATTIV0:
							$img = 'publish_g.png'; // attivo
							$times .= _ABP_PUB_AIC;
							break;

						case BANNER_TERMINATO:
							$img = 'publish_r.png'; // terminato
							$times .= _ABP_PUB_BHF;
							break;

						default: //case BANNER_NON_PUBBLICATO:
							$img = "publish_x.png"; // non pubblicato
							$times .= _ABP_OUB_NOT;
							break;
					}

					$times .= "</td></tr>";

					$task = '';
					if($row->cat_pub == 0 || $row->cl_pub == 0){
						$img = 'checked_out.png'; // bloccato
						$task = 'unpublishbanner';
					} else{
						$task = ($row->state == 1) ? 'unpublishbanner' : 'publishbanner';
						$times .= "<tr><td>" . addslashes(_ABP_COITTS) . "</td></tr>";
					}

					if($row->cat_pub == 0){
						$times .= "<tr><td align=center><img src=" . $cur_file_icons_path . "/checked_out.png width=12 height=12 border=0>" . _ABP_CATEGORY_UNPUBLISH . "</td></tr>";
					}

					if($row->cl_pub == 0){
						$times .= "<tr><td align=center><img src=" . $cur_file_icons_path . "/checked_out.png width=12 height=12 border=0>" . _ABP_CLIENT_UNPUBLISH . "</td></tr>";
					}

					$now = mosCurrentDate("%Y-%m-%d");
					$time = mosCurrentDate("%H:%M:%S");

					$onclick = '';

					if(getStato($row) != BANNER_TERMINATO){ // terminato
						if($row->cat_pub == 1 && $row->cl_pub == 1){
							if($now < $row->publish_down_date || $row->publish_down_date == "0000-00-00"){
								$onclick = 'onClick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')"';
							} else
								if($now == $row->publish_down_date && ($time <= $row->publish_down_time || $row->publish_down_time == '00:00:00')){
									$onclick = 'onClick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')"';
								}
						}
					}
					?>

					<td align="center">
						<a href="javascript: void(0);" onMouseOver="return overlib('<table border=0 width=100% height=100%><?php echo $times; ?></table>', CAPTION, '<?php echo _PUBLISH_INFO; ?>', BELOW, RIGHT);" onMouseOut="return nd();" <?php echo $onclick; ?>><img src="<?php echo $cur_file_icons_path; ?>/<?php echo $img; ?>" border=0 alt=""/></a>
					</td>
				</tr>

				<?php
				$k = 1 - $k;
			} // end ciclo for
			?>

		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<?php mosCommonHTML::ContentLegend(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="banners">
		<input type="hidden" name="chosen" value="">
		<input type="hidden" name="boxchecked" value="0">

	</form><?php

	} // end showBanners

	public static function editBanner(&$row, &$clientlist, &$categorylist, &$imagelist, $glist, $option, &$dimension){
		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';

		mosMakeHtmlSafe($row, ENT_QUOTES, 'custombannercode');
		mosCommonHTML::loadOverlib();
		mosCommonHTML::loadCalendar();
		?>

	<script language="javascript" type="text/javascript">
	<!--
	function toggleBox(szDivID, iState) // 1 visible, 0 hidden
	{
		if (document.layers) //NN4+
		{
			document.layers[szDivID].visibility = iState ? "show" : "hide";
		}
		else if (document.getElementById) //gecko(NN6) + IE 5+
		{
			var obj = document.getElementById(szDivID);
			obj.style.visibility = iState ? "visible" : "hidden";
		}
		else if (document.all) // IE 4
		{
			document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
		}
	}

	function widthImage(image_url) {
		<?php
		$keys = array_keys($dimension);
		foreach($keys as $key){
			echo "if (image_url == '", $key, "') return ", $dimension[$key]['w'], ";\n";
		}
		?>
	}

	function hieghtImage(image_url) {
		<?php
		$keys = array_keys($dimension);
		foreach($keys as $key){
			echo "if (image_url == '", $key, "') return ", $dimension[$key]['h'], ";\n";
		}
		?>
	}

	function changeDisplayImage(msg) {
		if (document.adminForm.image_url.value != '') {
			w = widthImage(document.adminForm.image_url.value);
			h = hieghtImage(document.adminForm.image_url.value);

			document.adminForm.width_image.value = w;
			document.adminForm.height_image.value = h;

			if (document.adminForm.image_url.value.indexOf('.swf') != -1) {
				toggleBox('flashDiv', 1);
				document.getElementById("flashDiv").innerHTML = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,2,0" border="0" vspace="0" width="' + w + '" height="' + h + '"><param name="SRC" value="../images/show/' + document.adminForm.image_url.value + '"><embed src="../images/show/' + document.adminForm.image_url.value + '" loop="false" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="' + w + '" height="' + h + '"></object>';
				document.adminForm.imagelib.src = 'images/blank.png';

				toggleBox('flashDivText', 1);

				bid = <?php echo ($row->id) ? $row->id : '\'idbannernew\''; ?>;

				document.getElementById("flashDivText").innerHTML = '<br><?php echo JPATH_SITE; ?>/index.php?option=com_banners&task=clk&id=' + bid;
				if (msg == 1) {
					alert("<?php echo _ABP_ALERT_BANNER_FLASH; ?>" + bid);
				}

			} else {
				toggleBox('flashDiv', 0);
				document.getElementById("flashDiv").innerHTML = '';

				toggleBox('flashDivText', 0);
				document.getElementById("flashDivText").innerHTML = '';

				document.adminForm.imagelib.src = '../images/show/' + document.adminForm.image_url.value;
			}
		} else {

			document.adminForm.width_image.value = '';
			document.adminForm.height_image.value = '';

			toggleBox('flashDiv', 0);
			document.getElementById("flashDiv").innerHTML = '';

			toggleBox('flashDivText', 0);
			document.getElementById("flashDivText").innerHTML = '';

			document.adminForm.imagelib.src = 'images/blank.png';
		}

		enableFields();
	}

	function enableFields() {
		var form = document.adminForm;

		if (form.custom_banner_code.value.length > 0) {
			form.image_url.value = '';
			form.click_url.value = '';
			form.title.value = '';
			form.alt.value = '';

			toggleBox('flashDiv', 0);
			document.getElementById("flashDiv").innerHTML = '';

			toggleBox('flashDivText', 0);
			document.getElementById("flashDivText").innerHTML = '';

			document.adminForm.imagelib.src = 'images/blank.png';

			form.image_url.disabled = true;
			form.click_url.disabled = true;
			form.alt.disabled = true;
			form.title.disabled = true;

			form.target.disabled = true;
			form.border_value.disabled = true;
			form.border_style.disabled = true;
			form.border_color.disabled = true;

		} else {
			form.image_url.disabled = false;
			form.click_url.disabled = false;
			form.title.disabled = false;
			form.alt.disabled = false;

			if (form.image_url.value.indexOf('.swf') != -1) {
				//form.click_url.value = '';
				//form.click_url.disabled = true;
				form.target.disabled = true;
				form.border_value.disabled = true;
				form.border_style.disabled = true;
				form.border_color.disabled = true;
			}
			else {
				//form.click_url.disabled = false;
				form.target.disabled = false;
				form.border_value.disabled = false;
				form.border_style.disabled = false;
				form.border_color.disabled = false;
			}
		}

		if (form.unlimited.checked) {
			form.imp_total.disabled = true;
		} else {
			form.imp_total.disabled = false;
		}
	}

	function submitbutton(pressbutton) {
		checkDisable();
		var form = document.adminForm;
		if (pressbutton == 'cancelbanner') {
			submitform(pressbutton);
			return;
		}

		var breccurweekdays = false;
		for (i = 0; i < 7; i++) {
			cb = eval('form.cb_wd' + i);
			if (cb.checked) {
				breccurweekdays = true;
				break;
			}
		}

		// do field validation
		if (form.name.value == "") {
			alert("<?php echo _ABP_YMPABN; ?>");
			form.name.focus();
		} else if (getSelectedValue('adminForm', 'cid') < 1) {
			alert("<?php echo _ABP_PSACLI; ?>");
			form.cid.focus();
		} else if (getSelectedValue('adminForm', 'tid') < 1) {
			alert("<?php echo _ABP_PSACAT; ?>");
			form.tid.focus();
		} else if (form.custom_banner_code.value == "" &&
			!getSelectedValue('adminForm', 'image_url')) {
			alert("<?php echo _ABP_PSANIMG; ?>");
			form.image_url.focus();
		} else if (form.custom_banner_code.value == "" &&
			document.adminForm.image_url.value.indexOf('.swf') == -1 &&
			form.click_url.value == "") {
			alert("<?php echo _ABP_PFITUOCCFTB; ?>");
			form.click_url.focus();
		} else if (form.unlimited.checked &&
			form.imp_total.value != '') {
			alert("<?php echo _ABP_ERROR_IMP; ?>");
			form.imp_total.focus();
			//} else if (!form.unlimited.checked &&
			//		   form.imp_total.value == '') {
			//	alert("<?php echo _ABP_ERROR_IMP; ?>" );
			//	form.imp_total.focus();
		} else if (form.reccurtype.value != 0 && breccurweekdays == false) {
			alert("<?php echo _ABP_ERROR_DAYS_REC; ?>");
		} else if (!form.allday.checked &&
			form._publish_up_hour.value == '00' &&
			form._publish_up_minute.value == '00' &&
			form._publish_down_hour.value == '00' &&
			form._publish_down_minute.value == '00') {
			alert("<?php echo _ABP_ERROR_TIME; ?>");
		} else if (form.send_email.checked && form.password.value == "") {
			alert("<?php echo _ABP_ERROR_PWD; ?>");
			form.password.focus();
		} else {
			submitform(pressbutton);
		}
	}

	function checkPublish() {
		if (document.adminForm._publish_down_date.value == document.adminForm._publish_up_date.value) {
			document.adminForm.reccurtype.disabled = true;
		} else {
			document.adminForm.reccurtype.disabled = false;
		}

		if (document.adminForm._publish_down_date.value < document.adminForm._publish_up_date.value) {
			document.adminForm._publish_down_date.value = "<?php echo _ABP_NEVER; ?>";
		}

		if ((document.adminForm._publish_down_hour.value < document.adminForm._publish_up_hour.value) ||
			(document.adminForm._publish_down_minute.value <= document.adminForm._publish_up_minute.value &&
				document.adminForm._publish_down_hour.value <= document.adminForm._publish_up_hour.value)) {
			document.adminForm._publish_down_hour.value = document.adminForm._publish_up_hour.value;
			document.adminForm._publish_down_minute.value = document.adminForm._publish_up_minute.value;
		}
		checkDisable();
	}

	function checkDisable() {
		if (document.adminForm.allday.checked) {
			document.adminForm._publish_down_hour.disabled = true;
			document.adminForm._publish_down_minute.disabled = true;
			document.adminForm._publish_up_hour.disabled = true;
			document.adminForm._publish_up_minute.disabled = true;
			document.adminForm._publish_up_hour.selectedIndex = 0;
			document.adminForm._publish_up_minute.selectedIndex = 0;
			document.adminForm._publish_down_hour.selectedIndex = 0;
			document.adminForm._publish_down_minute.selectedIndex = 0;
		} else {
			document.adminForm._publish_down_hour.disabled = false;
			document.adminForm._publish_down_minute.disabled = false;
			document.adminForm._publish_up_hour.disabled = false;
			document.adminForm._publish_up_minute.disabled = false;
		}

		if (document.adminForm.reccurtype.value != 0) {
			var f = document.adminForm;
			for (i = 0; i < 7; i++) {
				cb = eval('f.cb_wd' + i);
				cb.disabled = false;
			}
		} else {
			var f = document.adminForm;
			for (i = 0; i < 7; i++) {
				cb = eval('f.cb_wd' + i);
				cb.disabled = true;
			}
		}
	}
	//-->
	</script>
	<table border="0" class="adminheading">
		<tbody>
		<tr>
			<th class="cpanel"><?php echo $row->id ? _EDIT_BANNER : _NEW_BANNER;?></th>
		</tr>
		</tbody>
	</table>
	<form action="index2.php" method="POST" name="adminForm">
	<table width="100%">
	<tr>
	<td width="70%" valign="top">
		<table class="adminform">
			<tr class="row0">
				<td width="150" align="right"><?php echo _ABP_E_BANNER_NAME; ?></td>
				<td colspan="2"><input class="inputbox" type="text" size="80" name="name" value="<?php echo $row->name; ?>"></td>
			</tr>
			<tr class="row1">
				<td align="right"><?php echo _ABP_E_CLIENT_NAME; ?></td>
				<td colspan="2" align="left"><?php echo $clientlist; ?>
				</td>
			</tr>
			<tr class="row0">
				<td align="right"><?php echo _PASSWORDWORD; ?></td>
				<td colspan="2" align="left">
					<input class="inputbox" type="text" name="password" size="30" maxlength="60" value="<?php echo $row->password; ?>">
					&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _ABP_FORM_SEND_CLIENT; ?>&nbsp;
					<input type="checkbox" name="send_email">
				</td>
			</tr>
			<tr class="row1">
				<td align="right"><?php echo _ABP_CATEGORY_NAME; ?></td>
				<td colspan="2" align="left"><?php echo $categorylist; ?></td>
			</tr>
			<tr class="row0">
				<td align="right"><?php echo _ABP_E_IMP_PURCHASED; ?></td>
				<?php
				$unlimited = '';
				if($row->imp_total == '0'){
					$unlimited = 'checked';
					$row->imp_total = '';
				}
				?>
				<td colspan="2"><input class="inputbox" type="text" name="imp_total" size="12" maxlength="11" value="<?php echo $row->imp_total; ?>">&nbsp;<?php echo _ABP_UNLIMITED; ?> <input type="checkbox" name="unlimited" <?php echo $unlimited; ?> onChange="enableFields();">
					<?php echo '&nbsp;&nbsp;', _ABP_IMPMADE, '&nbsp;:&nbsp;', $row->imp_made; ?>
			</tr>
			<tr class="row1">
				<td valign="top" align="right"><?php echo _ABP_CLICKS; ?></td>
				<td colspan="2">
					<?php echo $row->complete_clicks, ' / ', $row->clicks, _ABP_PARZ_DAL, $row->dta_mod_clicks, ' )'; ?>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input name="reset_hits" type="button" class="button" value="<?php echo _ABP_RESET_CLIC_PARZ; ?>" onclick="submitbutton('resethits');" <?php echo ($row->clicks) ? 'enabled' : 'disabled'; ?>/>
					<input name="dta_mod_clicks" type="hidden" value="<?php echo $row->dta_mod_clicks; ?>">
				</td>
			</tr>
			<tr class="row0">
				<td valign="top" align="right"><?php echo _ABP_E_BANNER_URL; ?></td>
				<td colspan="2" align="left"><?php echo $imagelist; ?></td>
			</tr>
			<tr class="row1">
				<td valign="top" align="right"><?php echo _ABP_E_BANNER_IMAGE; ?></td>
				<td colspan="2" valign="top">
					<div id="flashDiv" style="visibility:hidden;"></div>
					<div id="flashDivText" style="visibility:hidden;"></div>
					<?php
					$image_blank = '<img src="images/blank.png" name="imagelib" />';
					if($row->image_url != ''){
						if(preg_match("/.swf/", $row->image_url)){

							$image_url = JPATH_SITE . '/images/show/' . $row->image_url;
							$swfinfo = @getimagesize(JPATH_BASE . '/images/banners/' . $row->image_url);
							$result = "
                                        <object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"
                                            codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\"
                                            border=\"0\"
                                            width=\"$swfinfo[0]\"
                                            height=\"$swfinfo[1]\"
                                            vspace=\"0\">
                                                <param name=\"SRC\" value=\"$image_url\" />
                                                    <embed src=\"$image_url\"
                                                        loop=\"false\"
                                                        pluginspage=\"http://www.macromedia.com/go/get/flashplayer\"
                                                        type=\"application/x-shockwave-flash\"
                                                        width=\"$swfinfo[0]\"
                                                        height=\"$swfinfo[1]\">
                                        </object>
                                    ";
							echo $result;
							echo '<script language="javascript" type="text/javascript">
				<!--
					changeDisplayImage(0);
				//-->
				</script>';
						} else
							if(preg_match("/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/", $row->image_url)){
								?>
								<img src="../images/show/<?php echo $row->image_url; ?>" name="imagelib"/>
								<?php
							} else{
								echo $image_blank;
							}
					} else{
						echo $image_blank;
					}
					?>
				</td>
			</tr>
			<tr class="row0">
				<td>&nbsp;</td>
				<td colspan="2" align="left">
					width: <input type="text" name="width_image" value="" size="5" readonly>
					&nbsp;&nbsp;
					height: <input type="text" name="height_image" value="" size="5" readonly>
				</td>
			</tr>
			<tr class="row1">
				<td align="right"><?php echo _ABP_E_CLICK_URL; ?></td>
				<td colspan="2" align="left"><input class="inputbox" type="text" name="click_url" size="80" maxlength="200" value="<?php echo $row->click_url; ?>"></td>
			</tr>
			<tr class="row0">
				<td align="right">Alt:</td>
				<td colspan="2" align="left"><input class="inputbox" type="text" name="alt" size="80" maxlength="200" value="<?php echo $row->alt; ?>"></td>
			</tr>
			<tr class="row1">
				<td align="right">Title:</td>
				<td colspan="2" align="left"><input class="inputbox" type="text" name="title" size="80" maxlength="200" value="<?php echo $row->title; ?>"></td>
			</tr>
			<tr class="row0">
				<td valign="top" align="right"><?php echo _ABP_E_CUSTOM_BANNER_CODE; ?></td>
				<td colspan="2" align="left">
					<textarea class="inputbox" cols="70" rows="5" name="custom_banner_code" onKeyDown="enableFields()" onKeyUp="enableFields()"><?php echo $row->custom_banner_code; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
		</table>
	</td>
	<td width="2">&nbsp;</td>
	<td width="30%" valign="top">
		<?php
		$tabs = new mosTabs(0);
		$tabs->startPane("content-pane");
		$tabs->startTab(_ABP_OPZ, "opzioni-page");
		?>
		<table width="100%" class="adminlist">
			<tr>
				<th colspan="3"><?php echo _ABP_OPZ_IMP; ?></th>
			</tr>
			<tr>
				<td><label for="target"><?php echo _ABP_TARGET; ?></label></td>
				<td colspan="2">
					<select name="target" id="target">
						<option value="blank" <?php echo ($row->target == 'blank') ? 'selected' : ''; ?>><?php echo _IN_NEW_WINDOW?></option>
						<option value="self" <?php echo ($row->target == 'self') ? 'selected' : ''; ?>><?php echo _IN_CURRENT_WINDOW?></option>
						<option value="parent" <?php echo ($row->target == 'parent') ? 'selected' : ''; ?>><?php echo _IN_PARENT_WINDOW?></option>
						<option value="top" <?php echo ($row->target == 'top') ? 'selected' : ''; ?>><?php echo _IN_MAIN_FRAME?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo _ABP_BORDER_VALUE; ?></td>
				<td><input name="border_value" type="text" class="inputbox" id="border_value" value="<?php echo $row->border_value; ?>" size="12" maxlength="11"></td>
				<td width="50"><?php echo mosToolTip(_ABP_BORDER_VALUE_DESCRIPTION); ?> </td>
			</tr>
			<tr>
				<td><?php echo _ABP_BORDER_STYLE; ?></td>
				<td colspan="2">
					<select name="border_style" id="border_style">
						<option value="solid" <?php echo ($row->border_style == 'solid') ? 'selected' : ''; ?>>Сплошная</option>
						<option value="dotted" <?php echo ($row->border_style == 'dotted') ? 'selected' : ''; ?>>Пунктирная</option>
						<option value="double" <?php echo ($row->border_style == 'double') ? 'selected' : ''; ?>>Двойная</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo _ABP_BORDER_COLOR; ?></td>
				<td><input name="border_color" type="text" class="inputbox" id="border_color" value="<?php echo $row->border_color; ?>" size="12" maxlength="11"></td>
				<td><?php echo mosToolTip(_ABP_BORDER_COLOR_DESCRIPTION); ?> </td>
			</tr>
		</table>
		<?php
		$tabs->endTab();
		$tabs->startTab(_DATE, "date-page");
		?>
		<table width="100%" class="adminlist">
			<tr>
				<th colspan="3"><?php echo _DATE_PUB; ?></th>
			</tr>
			<tr>
				<td width='130' align="left"><?php echo _ABP_EVENT_STARTDATE; ?></td>
				<td colspan="2" align="left"><input class="inputbox" type="text" name="_publish_up_date" id="publish_up" size="12" maxlength="10" value="<?php echo $row->publish_up_date; ?>" onmouseover="checkPublish();" onchange="checkPublish();" onclick="checkPublish();"/>
					<input type="reset" class="button" value="..." onClick="return showCalendar('publish_up');"/>
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo _ABP_EVENT_ENDDATE; ?></td>
				<td colspan="2" align="left"><input class="inputbox" type="text" name="_publish_down_date" id="publish_down" size="12" maxlength="10" value="<?php echo $row->publish_down_date; ?>" onMouseOver="checkPublish();" onChange="checkPublish();" onClick="checkPublish();"/>
					<input type="reset" class="button" value="..." onClick="document.adminForm.never.checked=false; return showCalendar('publish_down');"/>
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo _ABP_EVENT_ENDDATE, ' ', _ABP_NEVER; ?></td>
				<td colspan="2" align="left">
					<input type="checkbox" name="never" onChange="javascript: document.adminForm._publish_down_date.value='<?php echo _ABP_NEVER; ?>'; checkPublish();" onClick="checkPublish();" <?php echo ($row->publish_down_date == _ABP_NEVER) ? 'checked' : ''; ?>>
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo _ABP_ALL_DAY; ?>&nbsp;</td>
				<td colspan="2" align="left">
					<input type="checkbox" name="allday" onChange="checkDisable();" onClick="checkDisable();" <?php echo ($row->publish_up_hour == 0 && $row->publish_up_minute == 0 && $row->publish_down_hour == 0 && $row->publish_down_minute == 0) ? "checked" : ""; ?> />
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo _ABP_EVENT_STARTHOURS; ?>&nbsp;</td>
				<td colspan="2" align="left">
					<?php
					/*Hours Select*/
					echo mosHTML::integerSelectList(0, 23, 1, '_publish_up_hour', 'size="1" onChange="checkPublish();" onClick="checkPublish();"', $row->publish_up_hour, "%02d");
					/*Minutes Select*/
					echo mosHTML::integerSelectList(0, 55, 5, '_publish_up_minute', 'size="1" onChange="checkPublish();" onClick="checkPublish();"', $row->publish_up_minute, "%02d");
					?>
				</td>
			</tr>
			<tr>
				<td align="left"><?php echo _ABP_EVENT_ENDHOURS; ?>&nbsp;</td>
				<td colspan="2" align="left">
					<?php
					/*Hours Select*/
					echo mosHTML::integerSelectList(0, 23, 1, '_publish_down_hour', 'size="1" onChange="checkPublish();" onClick="checkPublish();"', $row->publish_down_hour, "%02d");
					/*Minutes Select*/
					echo mosHTML::integerSelectList(0, 55, 5, '_publish_down_minute', 'size="1" onChange="checkPublish();" onClick="checkPublish();"', $row->publish_down_minute, "%02d");
					?>
				</td>
			</tr>
			<tr onmouseover="checkPublish();">
				<td align="left"><?php echo _ABP_REPEAT_TYPE; ?></td>
				<td colspan="2" align="left">
					<?php
					buildReccurTypeSelect($row->reccurtype, 'onmouseover="checkPublish();" onChange="checkDisable();"');
					?>
				</td>
			</tr>
			<tr>
				<td align="left" colspan="3">
					<?php
					echo _ABP_EVENT_CHOOSE_WEEKDAYS, '<br>';
					buildWeekDaysCheck($row->reccurweekdays, 'disabled=true');
					?>
				</td>
			</tr>
		</table>
		<?php
		$tabs->endTab();
		$tabs->startTab(_ABP_COSTS, "costi-page");
		?>
		<table width="100%" class="adminlist">
			<tr>
				<th colspan="2"><?php echo _ABP_COSTS; ?></th>
			</tr>
			<tr>
				<td valign="top" align="right" width="100"><?php echo _ABP_IMP_VALUE; ?></td>
				<td>
					<input name="imp_value" type="text" class="inputbox" id="imp_value" value="<?php echo $row->imp_value; ?>" size="12" maxlength="11"/>
					&nbsp;&nbsp;<?php echo _ABP_TOT, ($row->imp_value * $row->imp_made); ?>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?php echo _ABP_VALUE_CLICK; ?></td>
				<td>
					<input name="click_value" type="text" class="inputbox" id="click_value" value="<?php echo $row->click_value; ?>" size="12" maxlength="11"/>
					&nbsp;&nbsp;<?php echo _ABP_TOT, ($row->click_value * $row->complete_clicks); ?>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" colspan="2">
					<?php echo _ABP_TOT_IMP_CLIC; ?> Руб. <?php echo $row->click_value * $row->complete_clicks + $row->imp_value * $row->imp_made; ?>
				</td>
			</tr>
		</table>
		<?php
		$tabs->endTab();
		$tabs->endPane();
		?>

		<table width="100%" class="adminlist">
			<tr>
				<th colspan="3"><?php echo _E_STATE; ?> / <?php echo _ABP_EVENT_ACCESSLEVEL; ?></th>
			</tr>
			<tr>
				<td valign="top" width='100' align="left"><?php echo _E_STATE; ?></td>
				<td colspan="2" align="left">
					<?php
					switch(getStato($row)){
						case BANNER_IN_ATTIVAZIONE:
							$img = 'publish_y.png'; // in attivazione
							$times = _ABP_PUB_BIC;
							break;

						case BANNER_ATTIV0:
							$img = 'publish_g.png'; // attivo
							$times = _ABP_PUB_AIC;
							break;

						case BANNER_TERMINATO:
							$img = 'publish_r.png'; // terminato
							$times = _ABP_PUB_BHF;
							break;

						default: //case BANNER_NON_PUBBLICATO:
							$img = "publish_x.png"; // non pubblicato
							$times = _ABP_OUB_NOT;
							break;
					}

					echo $times;
					?>
					&nbsp;&nbsp;<img src="<?php echo $cur_file_icons_path;?>/<?php echo $img; ?>" border="0" alt=""/>
				</td>
			</tr>
			<tr>
				<td><?php echo _ACTIVE; ?></td>
				<td colspan="2">
					<select name="state" id="state" <?php echo ($row->imp_made == $row->imp_total) ? 'readonly' : ''; ?>>
						<option value="1" <?php echo ($row->state == '1') ? 'selected' : ''; ?>><?php echo _YES; ?></option>
						<option value="0" <?php echo ($row->state == '0') ? 'selected' : ''; ?>><?php echo _NONE; ?></option>
					</select>&nbsp;&nbsp;
					<?php echo mosToolTip(_ABP_BANNER_STATE_TOOL_TIP); ?>
				</td>
			</tr>
			<tr>
				<td width="100" align="left"><?php echo _ABP_EVENT_ACCESSLEVEL; ?></td>
				<td colspan="2" align="left"> <?php echo $glist; ?> </td>
			</tr>
		</table>
	</td>
	</tr>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>">
	<input type="hidden" name="id" value="<?php echo $row->id; ?>">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="catid" value="<?php echo mosGetParam($_REQUEST, 'catid', 0); ?>">
	<input type="hidden" name="imp_made" value="<?php echo $row->imp_made; ?>">
	<input type="hidden" name="complete_clicks" value="<?php echo $row->complete_clicks; ?>">
	<input type="hidden" name="clicks" value="<?php echo $row->clicks; ?>">
	</form>
	<script language="javascript" type="text/javascript">
		<!--
		checkPublish();
		enableFields();
		if (getSelectedValue('adminForm', 'image_url')) {
			w = widthImage(document.adminForm.image_url.value);
			h = hieghtImage(document.adminForm.image_url.value);
			document.adminForm.width_image.value = w;
			document.adminForm.height_image.value = h;
		}
		//-->
	</script>
	<?php
	}

	public static function cPanel($info_banner, $info_clients, $info_categories, $option){
		?>

	<table class="adminheading" border="0">
		<tr>
			<th class="cpanel"><?php echo _ABP_FOLDER_BANNER;?></th>
		</tr>
	</table>
	<table>
		<tr>
			<td width="50%" valign="top">
				<div class="cpicons">
					<?php

					$link = 'index2.php?option=com_banners&amp;task=banners';
					HTML_banners::quickiconButton($link, 'pack.png', _BANNERS_MANAGEMENT);

					$link = 'index2.php?option=com_banners&amp;task=categories';
					HTML_banners::quickiconButton($link, 'stopfolder.png', _ABP_BANNER_CATEGORY_MANAGER);

					$link = 'index2.php?option=com_banners&amp;task=clients';
					HTML_banners::quickiconButton($link, 'users.png', _ABP_BANNER_CLIENT_MANAGER);

					$link = 'index2.php?option=com_banners&amp;task=backup';
					HTML_banners::quickiconButton($link, 'db.png', _ABP_ARCHIVE_BANNERS);

					$link = 'index2.php?option=com_banners&amp;task=restore';
					HTML_banners::quickiconButton($link, 'down.png', _ABP_RESTORE_BANNERS);

					?>
				</div>
				<div style="clear:both;">&nbsp;</div>
			</td>
			<td width="50%" valign="top">
				<table class="adminlist">
					<tbody>
					<tr>
						<th>&nbsp;</th>
						<th align="center"><?php echo _ABP_BANNERS_TER; ?></th>
						<th align="center"><?php echo _ABP_BANNERS_IN_ATT; ?></th>
						<th align="center"><?php echo _ABP_TOTAL; ?></th>
						<th align="center"><?php echo _ABP_BANNERS_ATT; ?></th>
						<th align="center"><?php echo _ABP_BANNERS_NO_PUB; ?></th>
					</tr>
					<tr>
						<th class="title"><?php echo _ABP_FOLDER_BANNER;?></th>
						<td align="center"><?php echo $info_banner['terminati']; ?></td>
						<td align="center"><?php echo $info_banner['in_attiv']; ?></td>
						<td align="center"><?php echo $info_banner['attivi'] + $info_banner['terminati'] + $info_banner['non_publ'] + $info_banner['in_attiv']; ?></td>
						<td align="center"><?php echo $info_banner['attivi']; ?></td>
						<td align="center"><?php echo $info_banner['non_publ']; ?></td>
					</tr>
					<tr>
						<th class="title" colspan="3"><?php echo _ABP_FOLDER_CLIENTS;?></th>
						<td align="center"><?php echo $info_clients['attivi'] + $info_clients['non_publ']; ?></td>
						<td align="center"><?php echo $info_clients['attivi']; ?></td>
						<td align="center"><?php echo $info_clients['non_publ']; ?></td>
					</tr>
					<tr>
						<th class="title" colspan="3"><?php echo _CATEGORIES;?></th>
						<td align="center"><?php echo $info_categories['attivi'] + $info_categories['non_publ']; ?></td>
						<td align="center"><?php echo $info_categories['attivi']; ?></td>
						<td align="center"><?php echo $info_categories['non_publ']; ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php
	}

	// прорисовка кнопок управления
	public static function quickiconButton($link, $image, $text){
		?>
	<span>
	<a href="<?php echo $link; ?>" title="<?php echo $text; ?>">
		<?php
		echo mosAdminMenus::imageCheckAdmin($image, '/' . JADMIN_BASE . '/templates/' . mosMainFrame::getInstance(true)->getTemplate() . '/images/system_ico/', null, null, $text);
		echo $text;
		?>
	</a>
</span>
	<?php
	}

} // end HTML_banners

/**
 * Banner clients
 */
class HTML_bannerClient{
	public static function showClients(&$rows, &$info_banner, $myid, &$pageNav, $option, $stateslist){

		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		?>
	<table class="adminheading">
		<tbody>
		<tr>
			<th class="user"><?php echo _BANNER_CLIENTS?></th>
		</tr>
		</tbody>
	</table>
	<form action="index2.php" method="POST" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<th width="17">#</th>
				<th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);"/></th>
				<th width="20"><b>ID</b></th>
				<th align="left"><?php echo _ABP_CLIENT_NAME; ?></th>
				<th width="177" align="left"><?php echo _ABP_CONTACT; ?></th>
				<th width="177" align="left"><?php echo _ABP_E_EMAIL; ?></th>
				<th align="center"><?php echo _NONE_OF_BANNERS; ?></th>
				<th align="center"><?php echo _ABP_BANNERS_ATT; ?></th>
				<th align="center"><?php echo _ABP_BANNERS_TER; ?></th>
				<th align="center"><?php echo _ABP_BANNERS_NO_PUB; ?></th>
				<th align="center"><?php echo _ABP_BANNERS_IN_ATT; ?></th>
				<th width="10%"><?php echo _PUBLISHED; ?></th>
			</tr>
			<?php
			$k = 0;
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = &$rows[$i];
				$img = $row->published ? 'publish_g.png' : 'publish_x.png';
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="17" align="center"><?php echo $pageNav->rowNumber($i); ?></td>
					<td width="20">
						<?php
						if($row->checked_out && $row->checked_out != $myid){
							echo '<img src="' . $cur_file_icons_path . '/checked_out.png" width="12" height="12" border="0" alt="', _ABP_CICBEBAA, '">';
						} else{
							echo '<input type="checkbox" id="cb', $i, '" name="cid[]" value="', $row->cid, '" onClick="isChecked(this.checked);" />';
						}
						?>
					</td>
					<td width="18" align="center"><b><?php echo $row->cid; ?></b></td>
					<td width="514"><a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','editclient')"><?php echo $row->name; ?></a></td>
					<td><?php echo $row->contact; ?></td>
					<td><?php echo $row->email; ?></td>
					<td width="100" align="center"><?php echo $row->id; ?></td>
					<td width="100" align="center"><?php echo $info_banner[$i]['attivi']; ?></td>
					<td width="100" align="center"><?php echo $info_banner[$i]['terminati']; ?></td>
					<td width="100" align="center"><?php echo $info_banner[$i]['non_publ']; ?></td>
					<td width="100" align="center"><?php echo $info_banner[$i]['in_attiv']; ?></td>
					<td align="center" onclick="ch_publ(<?php echo $row->cid; ?>,'com_banners','&act=client_publish');" class="td-state">
						<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>" id="img-pub-<?php echo $row->cid;?>" alt="<?php echo _PUBLISHING?>"/>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="clients">
		<input type="hidden" name="boxchecked" value="0">
	</form>
	<?php
	}

	public static function editClient(&$row, $option){
		?>
	<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancelclient') {
				submitform(pressbutton);
				return;
			}
			// do field validation
			if (form.name.value == "") {
				alert("<?php echo _ABP_PFITCN1; ?>");
			} else if (form.contact.value == "") {
				alert("<?php echo _ABP_PFITCN2; ?>");
			} else if (form.email.value == "") {
				alert("<?php echo _ABP_PFITCE; ?>");
			} else {
				submitform(pressbutton);
			}
		}
		//-->
	</script>
	<table class="adminheading">
		<tbody>
		<tr>
			<th class="user"><?php echo $row->cid ? _ABP_EDIT_BANNER_CLIENT : _ABP_ADD_BANNER_CLIENT; ?></th>
		</tr>
		</tbody>
	</table>
	<form action="index2.php" method="POST" name="adminForm">
		<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
			<tr class="row0">
				<td width="15%"><?php echo _ABP_E_CLIENT_NAME; ?></td>
				<td><input class="inputbox" type="text" name="name" size="70" maxlength="60" valign="top" value="<?php echo $row->name; ?>"></td>
			</tr>
			<tr class="row1">
				<td><?php echo _ABP_E_CONTACT_NAME; ?></td>
				<td><input class="inputbox" type="text" name="contact" size="70" maxlength="60" value="<?php echo $row->contact; ?>"></td>
			</tr>
			<tr class="row0">
				<td><?php echo _ABP_E_EMAIL; ?></td>
				<td><input class="inputbox" type="text" name="email" size="70" maxlength="60" value="<?php echo $row->email; ?>"></td>
			</tr>
			<tr class="row1">
				<td valign="top"><?php echo _ABP_E_EXTRA_INFO; ?></td>
				<td><textarea class="inputbox" name="extrainfo" cols="60" rows="10"><?php echo str_replace('&', '&amp;', $row->extrainfo); ?></textarea></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="cid" value="<?php echo $row->cid; ?>">
		<input type="hidden" name="task" value="">
	</form>
	<?php
	}
} // end HTML_bannerClient

/**
 * Utility class for the display of category functions
 */
class HTML_bannerCategory{
	/**
	 * Writes a list of the categories for a section
	 * @param array An array of category objects
	 * @param string The name of the category section
	 */
	public static function showCategories(&$rows, $myid, &$pageNav, $option, $stateslist){
		$mainframe = mosMainFrame::getInstance();
		$cur_file_icons_path = JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico';
		$mainframe = mosMainFrame::getInstance();
		$my = $mainframe->getUser();
		?>
	<form action="index2.php" method="POST" name="adminForm">
		<table border="0" class="adminheading">
			<tbody>
			<tr>
				<th class="categories"><?php echo _BANNER_CATEGORIES?></th>
			</tr>
			</tbody>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<th width="20">#</th>
				<th width="20"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);"/></th>
				<th width="20"><b>ID</b></th>
				<th class="title" width="75%"><?php echo _ABP_C_CATEGORY_NAME; ?></th>
				<th width="15%"><?php echo _ABP_C_NUM_OF_RECORDS; ?></th>
				<th width="10%"><?php echo _PUBLISHED; ?></th>
			</tr>
			<?php
			$k = 0;
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = &$rows[$i];
				$img = $row->published ? 'publish_g.png' : 'publish_x.png';
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="20" align="right"><?php echo $pageNav->rowNumber($i); ?></td>
					<td width="20">
						<?php
						if($row->checked_out && $row->checked_out != $myid){
							echo '<img src="' . $cur_file_icons_path . '/checked_out.png" border="0" alt="', _ABP_TCICBEBAA, '">';
						} else{
							echo '<input type="checkbox" id="cb', $i, '" name="cid[]" value="', $row->id, '" onClick="isChecked(this.checked);" />';
						}
						?>
					</td>
					<td width="20" align="center"><b><?php echo $row->id; ?></b></td>
					<td width="55%">
						<a href="#edit" onClick="return listItemTask('cb<?php echo $i; ?>','editcategory')"><?php echo $row->name; ?></a>
					</td>
					<td width="15%" align="center"><?php echo $row->banners; ?></td>
					<td align="center" <?php echo ($row->checked_out && ($row->checked_out != $my->id)) ? null : 'onclick="ch_publ(' . $row->id . ',\'com_banners\',\'&act=cat_publish\');" class="td-state"';?>>
						<img class="img-mini-state" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>" id="img-pub-<?php echo $row->id;?>" alt="<?php echo _PUBLISHING?>"/>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			} // for loop
			?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value="categories"/>
		<input type="hidden" name="chosen" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
	</form>
	<?php
	}

	/**
	 * Writes the edit form for new and existing categories
	 * A new record is defined when <var>$row</var> is passed witht the <var>id</var>
	 * property set to 0.  Note that the <var>section</var> property <b>must</b> be defined
	 * even for a new record.
	 * @param mosCategory The category object
	 */
	public static function editCategory(&$row){
		?>
	<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton, section) {
			if (pressbutton == 'cancelcategory') {
				submitform(pressbutton);
				return;
			}
			if (document.adminForm.name.value == "") {
				alert("<?php echo _ABP_CATEGORY_MUST_HAVE_A_NAME; ?>");
			} else {
				submitform(pressbutton);
			}
		}
		//-->
	</script>
	<form action="index2.php" method="post" name="adminForm">
		<table border="0" class="adminheading">
			<tbody>
			<tr>
				<th class="categories"><?php echo $row->id ? _CHANGE_CATEGORY : _ABP_ADD_CATEGORY; ?></th>
			</tr>
			</tbody>
		</table>
		<table class="adminform">
			<tr class="row0">
				<td><?php echo _ABP_CATEGORY_NAME; ?></td>
				<td><input class="inputbox" type="text" name="name" value="<?php echo $row->name; ?>" size="70" maxlength="255"/></td>
			</tr>
			<tr class="row1">
				<td valign="top"><?php echo _DESCRIPTION; ?></td>
				<td><textarea class="inputbox" name="description" rows="5" cols="50"><?php echo $row->description; ?></textarea></td>
			</tr>
			<input type="hidden" name="option" value="com_banners"/>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
			<input type="hidden" name="task" value=""/>
		</table>
	</form>
	<?php
	}
} // end HTML_bannerCategory

class HTML_bannersOther{
	public static function restore($option){
		?>
	<form action="index2.php" method="POST" name="adminForm" enctype="multipart/form-data">
		<table class="adminform">
			<tr>
				<th class="title">
					<?php echo _TASK_UPLOAD_FILE; ?> :
				</th>
			</tr>
			<tr>
				<td align="center">
					<input class="inputbox" name="userfile" type="file"/>
					<input class="button" type="submit" value="<?php echo _TASK_UPLOAD; ?>" name="fileupload"/>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="dorestore">
	</form>
	<?php
	}
}