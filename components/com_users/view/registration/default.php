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

?>
<script language="javascript" type="text/javascript">
	function submitbutton_reg() {
		var form = document.mosForm;
		var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+]", "i");

		// do field validation
		if (form.name.value == "" || r.exec(form.name.value)) {
			alert("<?php echo addslashes(html_entity_decode(_REGWARN_NAME)); ?>");
		} else if (form.username.value == "") {
			alert("<?php echo addslashes(html_entity_decode(_REGWARN_USERNAME)); ?>");
		} else if (r.exec(form.username.value) || form.username.value.length < 3) {
			alert("<?php printf(addslashes(html_entity_decode(_VALID_AZ09_USER)), addslashes(html_entity_decode(_PROMPT_USERNAME)), 2); ?>");
		} else if (form.email.value == "") {
			alert("<?php echo addslashes(html_entity_decode(_REGWARN_MAIL)); ?>");
		} else if (form.password.value.length < 6) {
			alert("<?php echo addslashes(html_entity_decode(_REGWARN_PASSWORD)); ?>");
		} else if (form.password2.value == "") {
			alert("<?php echo addslashes(html_entity_decode(_REGWARN_VPASS1)); ?>");
		} else if ((form.password.value != "") && (form.password.value != form.password2.value)) {
			alert("<?php echo addslashes(html_entity_decode(_REGWARN_VPASS2)); ?>");
		} else if (r.exec(form.password.value)) {
			alert("<?php printf(addslashes(html_entity_decode(_VALID_AZ09)), addslashes(html_entity_decode(_REGISTER_PASSWORD)), 6); ?>");
		}
		<?php if($mainframe->getCfg('captcha_reg')){ ?>
		else if (form.captcha.value == "") {
			alert("<?php echo addslashes(html_entity_decode(_REG_CAPTCHA_VAL)); ?>");
		}
			<?php };?>
		else {
			form.submit();
		}
	}
</script>
<div class="registration_page">

	<form action="<?php echo JSef::getUrlToSef('index.php') ?>" method="post" name="mosForm" id="mosForm">
		<div class="componentheading"><h1><?php echo $params->get('title'); ?></h1></div>

		<?php if($params->get('pre_text')){ ?>
		<div class="info">
			<?php echo $params->get('pre_text'); ?>
		</div>
		<?php } ?>

		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
			<tr>
				<td width="30%" align="right"><?php echo _REGISTER_NAME; ?>*</td>
				<td>
					<input type="text" name="name" size="40" value="" class="inputbox" maxlength="50"/>
				</td>
			</tr>
			<tr>
				<td align="right"><?php echo _REGISTER_USERNAME; ?>*</td>
				<td>
					<input type="text" name="username" size="40" value="" class="inputbox" maxlength="25"/>
				</td>
			</tr>
			<tr>
				<td align="right"><?php echo _REGISTER_EMAIL; ?>*</td>
				<td>
					<input type="text" name="email" size="40" value="" class="inputbox" maxlength="100"/>
				</td>
			</tr>
			<tr>
				<td align="right"><?php echo _REGISTER_PASSWORD; ?>*</td>
				<td>
					<input class="inputbox" type="password" name="password" size="40" value=""/>
				</td>
			</tr>
			<tr>
				<td align="right">
					<?php echo _REGISTER_VPASS; ?>*
				</td>
				<td>
					<input class="inputbox" type="password" name="password2" size="40" value=""/>
				</td>
			</tr>

			<?php if($mainframe->getCfg('captcha_reg')){ ?>
			<tr>
				<td>&nbsp;</td>
				<td>
					<img id="captchaimg" alt="<?php echo _PRESS_HERE_TO_RELOAD_CAPTCHA?>" onclick="document.mosForm.captchaimg.src='<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>&' + new String(Math.random())"
						 src="<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>"/>
				</td>
			</tr>
			<tr>
				<td><?php echo _REG_CAPTCHA; ?></td>
				<td>
					<input type="text" name="captcha" class="inputbox" size="40" value=""/>
				</td>
			</tr>
			<?php } ?>

		</table>

		<?php if($params->get('post_text')){ ?>
		<div class="info">
			<?php echo $params->get('post_text'); ?>
		</div>
		<?php }?>

		<br/><span class="button"><input type="button" value="<?php echo _BUTTON_SEND_REG; ?>" class="button" onclick="submitbutton_reg()"/></span>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value="saveRegistration"/>
		<input type="hidden" name="gid" value="<?php echo $gid; ?>"/>
		<input type="hidden" name="gid_check" value="<?php echo $gid_check; ?>"/>
		<input type="hidden" name="<?php echo $validate; ?>" value="1"/>
	</form>

</div>