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

// used for spoof hardening
$validate = josSpoofValue();

?>
<div class="lostpass_page">
	<form action="<?php echo JSef::getUrlToSef('index.php')?>" method="post" name="mosForm" id="mosForm">
		<div class="componentheading"><h1><?php echo $user_config->get('title');?></h1></div>
		<div class="info"><?php echo _NEW_PASSWORD_DESC; ?></div>
		<div class="row">
			<label for="checkusername"><?php echo _PROMPT_USERNAME; ?></label>
			<input type="text" name="checkusername" class="inputbox" size="40" maxlength="25"/>
		</div>
		<div class="row">
			<label for="confirmEmail"><?php echo _PROMPT_EMAIL; ?></label>
			<input type="text" name="confirmEmail" class="inputbox" size="40"/>
		</div>
		<?php if($config->config_captcha_reg){ ?>
		<div class="captcha">
			<img id="captchaimg" alt="<?php echo _PRESS_HERE_TO_RELOAD_CAPTCHA?>" onclick="document.mosForm.captchaimg.src='<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>&' + new String(Math.random())"
				 src="<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>"/>
			<label for="captcha"><?php echo _REG_CAPTCHA; ?></label>
			<input type="text" name="captcha" class="inputbox" size="40" value=""/>
		</div>
		<?php } ?>
		<span class="button"><input type="submit" class="button" value="<?php echo _BUTTON_SEND_PASSWORD; ?>"/></span>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value="sendNewPass"/>
		<input type="hidden" name="<?php echo $validate; ?>" value="1"/>
	</form>
</div>