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
global $my;


$module->helper->prepare_login_form($params);
$validate = josSpoofValue(1);
mosCommonHTML::loadJquery(1);

?>
<div class="mod_ml_login">
	<div class="button">
		<button id="log_in"><?php echo $params->get('dr_login_text', _LOGIN_TEXT);?></button>
	</div>
	<div id="box1">
		<form action="<?php echo JSef::getUrlToSef('index.php'); ?>" method="post" name="login">
			<div class="login_form">
				<p><?php echo $params->_input_login; ?></p>

				<p><?php echo $params->_input_pass; ?></p>

				<p>
					<input type="checkbox" name="remember" id="mod_login_remember" value="yes" alt="Remember Me"/>
					<label for="mod_login_remember"><?php echo $params->get('ml_rem_text', _REMEMBER_ME);?></label>
				</p>

				<p><input type="submit" name="Submit" class="button" id="login_button" value="<?php echo $params->get('submit_button_text', _BUTTON_LOGIN);?>"/></p>

				<p>
					<a href="<?php echo JSef::getUrlToSef('index.php?option=com_users&amp;task=register');?>"><?php echo $params->get('ml_reg_text', _CREATE_ACCOUNT)?></a>&nbsp;&nbsp;
					<a href="<?php echo JSef::getUrlToSef('index.php?option=com_users&amp;task=lostPassword');?>"><?php echo $params->get('ml_rem_pass_text', _LOST_PASSWORDWORD);?></a>
				</p>
			</div>
			<input type="hidden" name="option" value="login"/>
			<input type="hidden" name="op2" value="login"/>
			<input type="hidden" name="lang" value="<?php echo $mainframe->getCfg('lang'); ?>"/>
			<input type="hidden" name="return" value="<?php echo JSef::getUrlToSef($params->get('login', $params->_returnUrl)); ?>"/>
			<input type="hidden" name="message" value="<?php echo $params->get('login_message', ''); ?>"/>
			<input type="hidden" name="force_session" value="1"/>
			<input type="hidden" name="<?php echo $validate; ?>" value="1"/>
		</form>
		<div class="closewin">&nbsp;</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#log_in').click(function () {
				$('#box1').toggle(400);
				$('body').addClass("tb");
				return false;
			});
			$('.closewin').click(function () {
				$('#box1').toggle(400);
				$('.closewin').removeClass("tb");
			});
		});
	</script>

</div>
