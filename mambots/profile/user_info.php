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

$_MAMBOTS->registerFunction('userProfile', 'botUserInfo');
$_MAMBOTS->registerFunction('userProfileTab', 'botUserInfo_tab');

/* добавляем вкладку профиля */
function botUserInfo_tab($user){
	return array(
		'name' => _USER_PERSONAL_DATA,
		'title'=> _USER_PERSONAL_DATA,
		'href' => 'index.php?option=com_users&task=profile&view=user_info&user=' . $user->id,
		'id'   => 'user_user_info_link',
		'class'=> ''
	);
}

/**
 */
function botUserInfo(&$user){
	$_MAMBOTS = mosMambotHandler::getInstance();

	$database = database::getInstance();
	$params = new mosParameters($_MAMBOTS->_mambot_params['user_info']);

	?>
<div id="userInfo_area">
	<?php if($params->get('show_header') && $params->get('header')){ ?>
	<h6><?php echo $params->get('header') ?></h6>
	<?php } ?>
	<?php if($params->get('gender')){ ?>
	<?php if(isset($user->user_extra->gender)){ ?>
		<strong><?php echo BOT_USER_INFO_GENDER ?></strong> <?php echo $user->get_gender($user, $params); ?>
		<?php } else{ ?>
		<strong><?php echo BOT_USER_INFO_GENDER ?></strong> <?php echo BOT_USER_INFO_GENDER_NON_SELECT ?>
		<?php } ?>
	<?php } ?>
	<?php if($params->get('show_location')){ ?>
	<?php if(isset($user->user_extra->location) && Jstring::trim($user->user_extra->location) != ''){ ?>
		<strong><?php echo BOT_USER_INFO_FROM?></strong> <?php echo $user->user_extra->location; ?>
		<?php } else{ ?>
		<strong><?php echo BOT_USER_INFO_FROM?></strong> <?php echo BOT_USER_INFO_FROM_NON_SELECT ?>
		<?php } ?>
	<?php } ?>
	<br/>
	<?php if($params->get('show_birthdate')){ ?>
	<?php if(isset($user->user_extra->birthdate)){ ?>
		<?php echo $user->get_birthdate($user, $params); ?>
		<?php } else{ ?>
		<?php echo BOT_USER_INFO_AGE_NON_SELECT ?>
		<?php } ?>
	<?php } ?>
	<br/>
	<?php if($params->get('show_about')){ ?>
	<?php if(isset($user->user_extra->about)){ ?>
		<p><?php echo $user->user_extra->about;?></p>
		<?php } else{ ?>
		<p><?php echo BOT_USER_INFO_USER_INFO_NONE?></p>
		<?php } ?>
	<?php } ?>
</div>
<?php
}