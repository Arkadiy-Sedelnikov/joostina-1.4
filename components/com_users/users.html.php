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
 * @subpackage Users
 */
class HTML_user {
	function frontpage() {
		?>
<div class="componentheading"><?php echo _WELCOME; ?></div>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td><?php echo _WELCOME_DESC; ?></td>
	</tr>
</table>
		<?php
	}

	public static function profile($user,$option, &$params, $config) {
		global $my, $_MAMBOTS,$Itemid;

		$mainframe = mosMainFrame::getInstance();
		$database = $mainframe->getDBO();

		$owner=0;
		$admin = 0;

		if($my->user_type = 'Super Administrator') {
			$admin = 1;
		}

		if($my->id && $user->id==$my->id) {
			$owner=1;
			$editable=' editable';
			$edit_info_link=sefRelToAbs('index.php?option=com_users&task=UserDetails&Itemid='.$Itemid);
		}else {
			$editable='';
			$avatar_edit='';
		}

		//Переменные для шаблона
		$avatar_pic = '<img class="avatar" src="'.JPATH_SITE.'/'.$user->get_avatar($user).'" />';
		$user_id= $user->id;
		$user_real_name = $user->name;
		$user_nickname = $user->username;

		$user_status = $user->get_user_status($user->id);
		if($user_status) {
			$user_status='<span class="online">'._USER_ON_LINE.'</span>';
		}else {
			$user_status='<span class="offline">'._USER_OFF_LINE.'</span>';
		}

		$registerDate = mosFormatDate($user->registerDate);

		$lastvisitDate = ($user->lastvisitDate !='0000-00-00 00:00:00') ? mosFormatDate($user->lastvisitDate) : _USER_NONE_LAST_VISIT;

		$user_content_href=sefRelToAbs('index.php?option=com_content&task=mycontent&user='.$user_id.'&Itemid='.$Itemid);

		//Шаблон
		$template = 'default.php';
		$template_dir = 'components/com_users/view/profile';

		//Если используются разные шаблоны для разных групп пользователей
		if(!$config->get('template')) {
			$template=strtolower(str_replace(' ', '', $user->usertype )).'.php';
		}

		if($config->get('template_dir')) {
			$template_dir = 'templates'.DS. JTEMPLATE . '/html/com_users/profile';
		}
		$template_file = JPATH_BASE.DS.$template_dir.DS.$template;

		//Находим плагины профиля пользователя
		$plugins = new userPlugins();
		$profile_plugins = $plugins->get_plugins('profile');

		$plugin_page = '';
		$cur_plugin = mosGetParam( $_REQUEST, 'view', '' );
		//Если плагины установлены
		if($profile_plugins) {
			//выцепляем первый плагин в группе как плагин по-умолчанию
			$plugin_page = $profile_plugins[0]->element;
			//Обращение к странице плагина
			if($cur_plugin) {
				//Проверяем запрашиваемый плагин на доступность
				if($plugins->allow_plugin($cur_plugin)) {
					$plugin_page = $cur_plugin;
				}
			}else {
				$cur_plugin = $plugin_page;
			}
			//подключаем плагин
			$_MAMBOTS->loadBot('profile',$plugin_page,1);

		}
		include ($template_file);
	}

	public static function userEdit($user,$option,$submitvalue,&$params, $user_config) {
		// used for spoof hardening
		$validate = josSpoofValue();

		require_once (JPATH_BASE.'/includes/HTML_toolbar.php');
		$config = Jconfig::getInstance();

		$user_extra = $user->user_extra;
		$bday_date = mosFormatDate($user_extra->birthdate, '%d', '0') ;
		$bday_month = mosFormatDate($user_extra->birthdate, '%m','0') ;
		$bday_year = mosFormatDate($user_extra->birthdate, '%Y', '0') ;

		//Шаблон
		$template_file='default.php';
		if(!$user_config->get('template_edit')) {
			if(is_file(JPATH_BASE.DS.'components'.DS.'com_users'.DS.'view'.DS.'edit'.DS.strtolower(str_replace(' ', '', $user->usertype )).'.php')) {
				$template_file=strtolower(str_replace(' ', '', $user->usertype )).'.php';
			}
		}

		include (JPATH_BASE.DS.'components'.DS.'com_users'.DS.'view'.DS.'edit'.DS.$template_file);

	}

	function confirmation() {
		?><div class="componentheading"><?php echo _SUBMIT_SUCCESS; ?></div>
<table>
	<tr>
		<td><?php echo _SUBMIT_SUCCESS_DESC; ?></td>
	</tr>
</table>
		<?php
	}
}