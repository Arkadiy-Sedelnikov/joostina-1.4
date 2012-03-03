<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
// запрет прямого доступа
defined( '_VALID_MOS' ) or die();
$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();


$module->helper->prepare_login_form($params);
$validate = josSpoofValue(1);
mosCommonHTML::loadJquery(1);

?>
<div class="mod_ml_login login popup">
<script type="text/javascript">
	$(document).ready(function(){
		$('#log_in').click (function() {
			$('.loginform_area').toggle(100);
			$('body').addClass("tb");
			return false;
		});
		$('.closewin').click(function(){
			$('.loginform_area').toggle(300);
			$('.closewin').removeClass("tb");
		});
	});
</script>
<div class="button">
	<button type="button" class="button" id="log_in"><?php echo $params->get( 'dr_login_text', _LOGIN_TEXT);?></button>
</div>
<div id="box1">
	<div class="loginform_area">
		<div class="loginform_area_inside">
			<div class="form_pretext"><?php echo $params->get('pretext' ,'')?></div>
			<form action="<?php echo sefRelToAbs( 'index.php' ); ?>" method="post" name="login">
				<div class="login_form">
					<?php echo $params->_input_login; ?>
					<?php echo $params->_input_pass; ?>
<?php if ($params->get( 'show_remember', 1)) { ?>
					<input type="checkbox" name="remember" id="mod_login_remember"  value="yes" alt="Remember Me" />
					<label for="mod_login_remember">
	<?php echo $params->get( 'ml_rem_text', _REMEMBER_ME );?>
					</label>
	<?php } ?>
					<span class="button">
						<input type="submit" name="Submit" class="button" id="login_button" value="<?php echo $params->get( 'submit_button_text', _BUTTON_LOGIN );?>" />
					</span>
					<br />
<?php if ($params->get('show_lost_pass', 1)) { ?>
					&nbsp;<a href="<?php echo sefRelToAbs( 'index.php?option=com_users&amp;task=lostPassword' );?>"><?php echo $params->get('ml_rem_pass_text', _LOST_PASSWORDWORD) ;?></a>
						<?php } ?>
<?php if($params->get('show_register', 1)) {?>
					&nbsp;<a href="<?php echo sefRelToAbs( 'index.php?option=com_users&amp;task=register' );?>"><?php echo $params->get('ml_reg_text', _CREATE_ACCOUNT)?></a>
	<?php }?>
				</div>
				<div class="form_posttext">
<?php echo $params->get('posttext', '');?>
				</div>
				<input type="hidden" name="option" value="login" />
				<input type="hidden" name="op2" value="login" />
				<input type="hidden" name="lang" value="<?php echo $mainframe->getCfg('lang'); ?>" />
				<input type="hidden" name="return" value="<?php echo sefRelToAbs($params->get('login',$params->_returnUrl)); ?>" />
				<input type="hidden" name="message" value="<?php echo $params->get('login_message',''); ?>" />
				<input type="hidden" name="force_session" value="1" />
				<input type="hidden" name="<?php echo $validate; ?>" value="1" />
			</form>
		</div>
		<div class="closewin">&nbsp;</div>
	</div>
</div>
</div>