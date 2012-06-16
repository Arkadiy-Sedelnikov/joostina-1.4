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


/**
 * @package Joostina
 */
class HTML_installer_core{

	public static function showInstallForm($title, $option, $element, $client = '', $p_startdir = '', $backLink = ''){
		?>
	<script language="javascript" type="text/javascript">
		function submitbutton3(pressbutton) {
			var form = document.adminForm_dir;
			if (form.userfile.value == "") {
				alert("<?php echo _CHOOSE_DIRECTORY_PLEASE?>");
			} else {
				form.submit();
			}
		}
		function submitbutton4(pressbutton) {
			var form = document.adminForm_url;
			if (form.url.value == "" || form.url.value == "http://") {
				alert("<?php echo _CHOOSE_URL_PLEASE?>");
			} else {
				form.submit();
			}
		}
	</script>
	<table class="adminheading">
		<tr>
			<th class="install"><?php echo $title; ?></th>
			<td align="right" class="jtd_nowrap"><?php echo $backLink; ?></td>
		</tr>
		<tr><?php HTML_installer::cPanel(); ?></tr>
	</table>
	<table width="100%">
		<tr valign="top">
			<td width="48%">
				<form enctype="multipart/form-data" action="index2.php" method="post" name="filename">
					<table class="adminform" style="width: 100%;">
						<tr>
							<th colspan="2"><?php echo _ZIP_UPLOAD_AND_INSTALL?></th>
						</tr>
						<tr>
							<td align="left" style="width: 15%; color: red; font-weight: bold;">
								<?php
								if(!extension_loaded('zlib')){
									echo _CANNOT_INSTALL_NO_ZLIB;
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="left" style="width: 15%;"><?php echo _PACKAGE_FILE?>:</td>
							<td align="left" style="width: 85%;">
								<input class="text_area" name="userfile" type="file" size="50"/>
								<input class="button" type="submit" value="<?php echo _UPLOAD_AND_INSTALL?>"/>
							</td>
						</tr>
					</table>
					<input type="hidden" name="task" value="uploadfile"/>
					<input type="hidden" name="option" value="com_installer"/>
					<input type="hidden" name="element" value="installer"/>
					<input type="hidden" name="client" value="<?php echo $client; ?>"/>
					<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
				</form>
				<br/>
			</td>
			<td>
				<form enctype="multipart/form-data" action="index2.php" method="post" name="adminForm_dir">
					<table class="adminform" style="width: 100%;">
						<tr>
							<th colspan="2"><?php echo _INSTALL_FROM_DIR?></th>
						</tr>
						<tr>
							<td align="left"><?php echo _INSTALLATION_DIRECTORY?>:</td>
							<td align="left">
								<input type="text" name="userfile" class="text_area" size="50" value="<?php echo $p_startdir; ?>"/>
								<input type="button" class="button" value="<?php echo _INSTALL ?>" onclick="submitbutton3()"/>
							</td>
						</tr>
					</table>
					<input type="hidden" name="task" value="installfromdir"/>
					<input type="hidden" name="option" value="<?php echo $option; ?>"/>
					<input type="hidden" name="element" value="installer"/>
					<input type="hidden" name="client" value="<?php echo $client; ?>"/>
					<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
				</form>
				<br/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<form enctype="multipart/form-data" action="index2.php" method="post" name="adminForm_url">
					<table class="adminform" style="width: 100%;">
						<tr>
							<th colspan="2"><?php echo _INSTALL_FROM_URL?></th>
						</tr>
						<tr>
							<td colspan="2">
								<?php if(!(bool)ini_get('allow_url_fopen')){ ?>
								<div class="jwarning"><?php echo _DISABLE_ALLOW_URL_FOPEN; ?></div>
								<?php } else{ ?>
								<?php echo _INSTALLATION_URL ?>:
								<input type="text" name="url" class="text_area" size="100" value="http://"/>
								<input type="button" class="button" value="<?php echo _INSTALL?>" onclick="submitbutton4()"/>
								<?php } ?>
							</td>
						</tr>
					</table>
					<input type="hidden" name="task" value="installfromurl"/>
					<input type="hidden" name="option" value="<?php echo $option; ?>"/>
					<input type="hidden" name="element" value="installer"/>
					<input type="hidden" name="client" value="<?php echo $client; ?>"/>
					<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1"/>
				</form>
			</td>
		</tr>
	</table>
	<table class="adminlist">
		<tr>
			<td>
				<!-- третья рекламная позиция // -->
				<!--			<div class="message"><a href="http://www.joostina.ru/?from_3" target="_blank">Купить тут рекламу</a></div>-->
				<!--  // третья рекламная позиция  -->
			</td>
		</tr>
	</table>

	<br/>
	<table class="adminlist">
		<?php
		writableCell('media');
		writableCell(JADMIN_BASE . '/components');
		writableCell('components');
		writableCell('images/stories');
		?>
	</table>
	<?php
	}

	public static function showInstallMessage($message, $title, $url){
		global $PHP_SELF;
		?>
	<table class="adminheading">
		<tr>
			<th class="install"><?php echo $title; ?></th>
		</tr>
	</table>
	<table class="adminform">
		<tr>
			<td align="left"><strong><?php echo $message; ?></strong></td>
		</tr>
		<tr>
			<td colspan="2" align="center">[&nbsp;<a href="<?php echo $url; ?>" style="font-size: 16px; font-weight: bold"><?php echo _CONTINUE?> ...</a>&nbsp;]</td>
		</tr>
	</table>
	<?php
	}
}