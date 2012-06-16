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
$module->helper->prepare_logout_form($params); ?>
<form action="<?php echo $params->_action; ?>" method="post" name="logout">
	<div class="mod_ml_login logout">
		<?php if($params->get('ml_avatar', 1)){ ?>
		<img class="avatar" src="<?php echo JPATH_SITE;?>/<?php echo mosUser::get_avatar($my);?>" alt="<?php echo $params->_raw_user_name;?>"/>
		<?php } ?>
		<?php if($params->get('greeting', 1)){ ?>
		<?php echo _HI; ?>
		<?php echo $params->_user_name; ?>
		<?php } ?>
		<?php echo $params->_profile_link; ?>
		<span class="button"><input type="submit" name="Submit" id="logout_button" class="button" value="<?php echo _BUTTON_LOGOUT; ?>"/></span>
	</div>
	<input type="hidden" name="option" value="logout"/>
	<input type="hidden" name="op2" value="logout"/>
	<input type="hidden" name="lang" value="<?php echo $mainframe->getCfg('lang'); ?>"/>
	<input type="hidden" name="return" value="<?php echo sefRelToAbs($params->get('logout', $params->_returnUrl)); ?>"/>
	<input type="hidden" name="message" value="<?php echo $params->get('logout_message', ''); ?>"/>
</form>