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

function writableCell($folder) {
	echo '<tr><td class="item">'.$folder.'/</td><td align="left">';
	echo is_writable(JPATH_BASE.'/'.$folder)?'<b><font color="green">'._WRITEABLE.'</font></b>':'<b><font color="red">'._UNWRITEABLE.'</font></b></td>';
	echo '</tr>';
}

/**
 * @package Joostina
 */
class HTML_installer {

	public static function showInstallForm($title,$option,$element,$client = "",$p_startdir = "",$backLink = "") {
		?>
<script language="javascript" type="text/javascript">
	function submitbutton3(pressbutton) {
		var form = document.adminForm_dir;
		if (form.userfile.value == ""){
			alert( "<?php echo _CHOOSE_DIRECTORY_PLEASE?>" );
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
</table>
<table width="100%">
	<tr>
		<td style="width:49%">
			<form enctype="multipart/form-data" action="index2.php" method="post" name="filename">
				<table class="adminform">
					<tr>
						<th><?php echo _ZIP_UPLOAD_AND_INSTALL?></th>
					</tr>
					<tr>
						<td align="left">
									<?php echo _PACKAGE_FILE?>:
							<input class="text_area" name="userfile" type="file" size="50"/>
							<input class="button" type="submit" value="<?php echo _UPLOAD_AND_INSTALL?>" />
						</td>
					</tr>
				</table>
				<input type="hidden" name="task" value="uploadfile"/>
				<input type="hidden" name="option" value="<?php echo $option; ?>"/>
				<input type="hidden" name="element" value="<?php echo $element; ?>"/>
				<input type="hidden" name="client" value="<?php echo $client; ?>"/>
				<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
			</form>
		</td>
		<td style="width:49%">
			<form enctype="multipart/form-data" action="index2.php" method="post" name="adminForm_dir">
				<table class="adminform">
					<tr>
						<th><?php echo _INSTALL_FROM_DIR?></th>
					</tr>
					<tr>
						<td align="left">
									<?php echo _INSTALLATION_DIRECTORY?>:
							<input type="text" name="userfile" class="text_area" size="50" value="<?php echo $p_startdir; ?>"/>
							<input type="button" class="button" value="Установить" onclick="submitbutton3()" />
						</td>
					</tr>
				</table>
				<input type="hidden" name="task" value="installfromdir" />
				<input type="hidden" name="option" value="<?php echo $option; ?>"/>
				<input type="hidden" name="element" value="<?php echo $element; ?>"/>
				<input type="hidden" name="client" value="<?php echo $client; ?>"/>
				<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
			</form>
		</td>
	</tr>
</table>

		<?php
	}

	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	function showInstallMessage($message,$title,$url) {
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
		<td colspan="2" align="center">[&nbsp;<a href="<?php echo $url; ?>" style="font-size: 16px; font-weight: bold"><?php echo _CONTINUE ?> ...</a>&nbsp;]</td>
	</tr>
</table>
		<?php
	}
	// прорисовка кнопок управления
	public static function quickiconButton($link,$image,$text) {
		?>
<span><a href="<?php echo $link; ?>" title="<?php echo $text; ?>">
				<?php
				echo mosAdminMenus::imageCheckAdmin($image,'/'.JADMIN_BASE.'/templates/'.mosMainFrame::getInstance(true)->getTemplate().'/images/system_ico/',null,null,$text);
				echo $text;
				?>
	</a></span>
		<?php
	}
	public static function cPanel() {?>

<td width="100%" valign="top">
	<div class="cpicons">
				<?php

				$link = 'index2.php?option=com_installer&amp;element=installer';
				HTML_installer::quickiconButton($link,'install.png', _INSTALLATION);

				$link = 'index2.php?option=com_installer&amp;element=component';
				HTML_installer::quickiconButton($link,'db.png', _COMPONENTS);

				$link = 'index2.php?option=com_installer&amp;element=module';
				HTML_installer::quickiconButton($link,'db.png', _MODULES);

				$link = 'index2.php?option=com_installer&amp;element=mambot';
				HTML_installer::quickiconButton($link,'ext.png', _MAMBOTS);

				$link = 'index2.php?option=com_installer&amp;element=template';
				HTML_installer::quickiconButton($link,'joostina.png', _COM_INSTALLER_SITE_TEMPLATES);

				$link = 'index2.php?option=com_installer&amp;element=template&client=admin';
				HTML_installer::quickiconButton($link,'joostina.png', _COM_INSTALLER_ADMIN_TEMPLATES);

				$link = 'index2.php?option=com_installer&amp;element=language';
				HTML_installer::quickiconButton($link,'log.png', _COM_INSTALLER_SITE_LANGUAGES);

				?>
	</div>
</td>
		<?php
	}
}