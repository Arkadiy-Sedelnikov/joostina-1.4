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

global $task, $option;
$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();

userHelper::_load_core_js();
?>
<script type="text/javascript">
	var _comuser_url = '<?php echo JPATH_SITE;?>/components/com_users';
	var _comuser_ajax_handler = 'ajax.index.php?option=com_users';
	var _comuser_defines = new Array();
</script>
<?php

// Editor usertype check
$access = new stdClass();
$access->canEdit = $acl->acl_check('action', 'edit', 'users', $my->usertype, 'content', 'all');
$access->canEditOwn = $acl->acl_check('action', 'edit', 'users', $my->usertype, 'content', 'own');

require_once ($mainframe->getPath('front_html'));
require_once ($mainframe->getPath('config', 'com_users'));
require_once ($mainframe->getPath('class'));

$id = intval(mosGetParam($_REQUEST, 'id', 0));
$uid = intval(mosGetParam($_REQUEST, 'user', $id));

switch($task){
	case 'edit';
	case 'UserDetails':
		userEdit($option, $my->id, _UPDATE);
		break;

	case 'saveUserEdit':
		// check to see if functionality restricted for use as demo site
		if(joomlaVersion::get('RESTRICT') == 1){
			mosRedirect('index.php', _RESTRICT_FUNCTION);
		} else{
			userSave($option, $my->id);
		}
		break;

	case 'CheckIn':
		CheckIn($my->id, $access, $option);
		break;

	case 'cancel':
		mosRedirect('index.php?option=com_users&task=profile&user=' . mosGetParam($_REQUEST, 'id', 0));
		break;

	case 'profile':
		$_view = strval(mosGetParam($_REQUEST, 'view', ''));
		if($mainframe->getCfg('caching') == 1){
			$cache = mosCache::getCache('user_profile');
			$r = $cache->call('profile', $uid, $_view);
		} else{
			$r = profile($uid, $_view);
		}
		echo $r['content'];
		$mainframe->SetPageTitle($r['title']);
		break;

	case 'lostPassword':
		$config = $mainframe->config;
		if($config->config_frontend_login != null && ($config->config_frontend_login === 0 || $config->config_frontend_login === '0')){
			echo _NOT_AUTH;
			return;
		}
		lostPassForm($option);
		break;

	case 'sendNewPass':
		$config = $mainframe->config;
		if($config->config_frontend_login != null && ($config->config_frontend_login === 0 || $config->config_frontend_login === '0')){
			echo _NOT_AUTH;
			return;
		}
		sendNewPass($option);
		break;

	case 'register':
		$config = $mainframe->config;
		if($config->config_frontend_login != null && ($config->config_frontend_login === 0 || $config->config_frontend_login === '0')){
			echo _NOT_AUTH;
			return;
		}
		registerForm($option, $config->config_useractivation);
		break;

	case 'saveRegistration':
		$config = $mainframe->config;
		if($config->config_frontend_login != null && ($config->config_frontend_login === 0 || $config->config_frontend_login === '0')){
			echo _NOT_AUTH;
			return;
		}
		saveRegistration();
		break;

	case 'activate':
		$config = $mainframe->config;
		if($config->config_frontend_login != null && ($config->config_frontend_login === 0 || $config->config_frontend_login === '0')){
			echo _NOT_AUTH;
			return;
		}
		activate($option);
		break;

	case 'userlist':
		$cache = mosCache::getCache('user_lists');
		$gid = intval(mosGetParam($_GET, 'group', 0));
		$limit = intval(mosGetParam($_REQUEST, 'limit', null));
		$limitstart = intval(mosGetParam($_REQUEST, 'limitstart', 0));
		$cache->call('userList', $gid, $limit, $limitstart);
		break;

	default:
		HTML_user::frontpage();
		break;
}

function profile($uid){

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	$row = new mosUser($database);
	if($row->load($uid)){
		//Дополнительная информация о пользователе
		$row->user_extra = $row->get_user_extra();

		$file = $mainframe->getPath('com_xml', 'com_users');
		$params = new mosUserParameters($row->params, $file, 'component');

		$config = new configUser_profile($database);
		$config->set('title', sprintf($config->get('title'), $row->name));
		$title = $config->get('title');

		ob_start();
		HTML_user::profile($row, 'com_users', $params, $config);
		$content_boby = ob_get_contents(); // главное содержимое - стек вывода компонента - mainbody
		ob_end_clean();
		return array('content' => $content_boby, 'title' => $title);
	} else{
		return _USER_NOT_FOUND;
	}

}

function userEdit($option, $uid, $submitvalue){

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	if($uid == 0){
		mosNotAuth();
		return;
	}
	$user = new mosUser($database);
	$user->load((int)$uid);
	$user->orig_password = $user->password;

	$user->name = trim($user->name);
	$user->email = trim($user->email);
	$user->username = trim($user->username);

	$file = $mainframe->getPath('com_xml', 'com_users');
	$params = new mosUserParameters($user->params, $file, 'component');

	$user_extra = new userUsersExtra($database);
	$user_extra->load((int)$uid);
	$user->user_extra = $user_extra;

	$user_config = new configUser_profile($database);

	HTML_user::userEdit($user, $option, $submitvalue, $params, $user_config);
}

function userSave($option, $uid){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();

	// simple spoof check security
	josSpoofCheck();

	$config = $mainframe->config;
	$database = database::getInstance();

	$user_id = intval(mosGetParam($_POST, 'id', 0));

	// do some security checks
	if($uid == 0 || $user_id == 0 || $user_id != $uid){
		mosNotAuth();
		return;
	}

	$row = new mosUser($database);
	$row->load((int)$user_id);

	$orig_password = $row->password;
	$orig_USER = $row->username;

	if(!$row->bind($_POST, 'gid usertype')){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->name = trim($row->name);
	$row->email = trim($row->email);
	$row->username = trim($row->username);

	mosMakeHtmlSafe($row);

	if(isset($_POST['password']) && $_POST['password'] != ''){
		if(isset($_POST['verifyPass']) && ($_POST['verifyPass'] == $_POST['password'])){
			$row->password = trim($row->password);
			$salt = mosMakePassword(16);
			$crypt = md5($row->password . $salt);
			$row->password = $crypt . ':' . $salt;
		} else{
			echo "<script> alert(\"" . addslashes(_PASSWORD_MATCH) . "\"); window.history.go(-1); </script>\n";
			exit();
		}
	} else{
		// Restore 'original password'
		$row->password = $orig_password;
	}

	if($config->config_frontend_userparams == '1' || $config->config_frontend_userparams == 1 || $config->config_frontend_userparams == null){
		// save params
		$params = mosGetParam($_POST, 'params', '');
		if(is_array($params)){
			$txt = array();
			foreach($params as $k => $v){
				$txt[] = "$k=$v";
			}
			$row->params = implode("\n", $txt);
		}
	}
	if(!$row->check()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	if(!$row->store()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$user_extra = new userUsersExtra($database);
	$ret = $user_extra->load((int)$user_id);
	if(!$user_extra->bind($_POST)){
		echo "<script> alert('" . $user_extra->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	$user_extra->birthdate = $_POST['birthdate_year'] . '-' . $_POST['birthdate_month'] . '-' . $_POST['birthdate_day'] . ' 00:00:00';

	if(!$ret){
		$user_extra->insert($user_id);
	}

	$user_extra->store();

	// check if username has been changed
	if($orig_USER != $row->username){
		// change username value in session table
		$query = "UPDATE #__session"
			. "\n SET username = " . $database->Quote($row->username)
			. "\n WHERE username = " . $database->Quote($orig_USER)
			. "\n AND userid = " . (int)$my->id
			. "\n AND gid = " . (int)$my->gid
			. "\n AND guest = 0";
		$database->setQuery($query);
		$database->query();
	}

	mosRedirect('index.php?option=com_users&task=UserDetails', _USER_DETAILS_SAVE);

	//userEdit($option,$my->id,_UPDATE);
}

function userList($gid, $limit, $limitstart = 0){

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();
	$acl = &gacl::getInstance();

	$menu = null;

	if(isset($mainframe->menu->params)){
		$menu = $mainframe->menu;
	}

	$users = new mosUser($database);

	$params = new mosParameters($mainframe->menu->params);

	$usertype = $acl->get_group_name($params->get('group', 0));
	$limit = $limit ? $limit : $params->get('limit', 20);

	$template = $params->get('template', 'default.php');
	$template_dir = 'components' . DS . 'com_users' . DS . 'view' . DS . 'userlist';

	if($params->get('template_dir')){
		$template_dir = 'templates' . DS . JTEMPLATE . DS . 'html' . DS . 'com_users' . DS . 'userlist';
	}
	if(is_file($template_file = JPATH_BASE . DS . $template_dir . DS . $template)){
		include_once($template_file);
	} else{
		include_once(JPATH_BASE . DS . $template_dir . DS . 'default.php');
	}

}

function CheckIn($userid, $access){

	$database = database::getInstance();
	$config = Jconfig::getInstance();

	$nullDate = $database->getNullDate();
	if(!($access->canEdit || $access->canEditOwn || $userid > 0)){
		mosNotAuth();
		return;
	}

	// security check to see if link exists in a menu
	$link = 'index.php?option=com_users&task=CheckIn';
	$query = "SELECT id"
		. "\n FROM #__menu"
		. "\n WHERE link LIKE '%$link%'"
		. "\n AND published = 1";
	$database->setQuery($query);
	$exists = $database->loadResult();
	if(!$exists){
		mosNotAuth();
		return;
	}

	$lt = mysql_list_tables($config->config_db);
	$k = 0;
	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
	while(list($tn) = mysql_fetch_array($lt)){
		// only check in the jos_* tables
		if(strpos($tn, $database->_table_prefix) !== 0){
			continue;
		}
		$lf = mysql_list_fields($config->config_db, "$tn");
		$nf = mysql_num_fields($lf);

		$checked_out = false;
		$editor = false;

		for($i = 0; $i < $nf; $i++){
			$fname = mysql_field_name($lf, $i);
			if($fname == "checked_out"){
				$checked_out = true;
			} else
				if($fname == "editor"){
					$editor = true;
				}
		}

		if($checked_out){
			if($editor){
				$query = "SELECT checked_out, editor"
					. "\n FROM `$tn`"
					. "\n WHERE checked_out > 0"
					. "\n AND checked_out = " . (int)$userid;
				$database->setQuery($query);
			} else{
				$query = "SELECT checked_out"
					. "\n FROM `$tn`"
					. "\n WHERE checked_out > 0"
					. "\n AND checked_out = " . (int)$userid;
				$database->setQuery($query);
			}
			$res = $database->query();
			$num = $database->getNumRows($res);

			if($editor){
				$query = "UPDATE `$tn`"
					. "\n SET checked_out = 0, checked_out_time = " . $database->Quote($nullDate) . ", editor = NULL"
					. "\n WHERE checked_out > 0"
					. "\n AND checked_out = " . (int)$userid;
				$database->setQuery($query);
			} else{
				$query = "UPDATE `$tn`"
					. "\n SET checked_out = 0, checked_out_time = " . $database->Quote($nullDate)
					. "\n WHERE checked_out > 0"
					. "\n AND checked_out = " . (int)$userid;
				$database->setQuery($query);
			}
			$res = $database->query();

			if($res == 1){
				if($num > 0){
					echo "\n<tr class=\"row$k\">";
					echo "\n\t<td width=\"250\">";
					echo _CHECK_TABLE;
					echo " - $tn</td>";
					echo "\n\t<td>";
					echo _CHECKED_IN;
					echo "<b>$num</b>";
					echo _CHECKED_IN_ITEMS;
					echo "</td>";
					echo "\n</tr>";
				}
				$k = 1 - $k;
			}
		}
	}
	?>
<tr>
	<td colspan="2">
		<b><?php echo _CONF_CHECKED_IN; ?></b>
	</td>
</tr>
</table>
<?php
}

/* форма восстановления пароля */
function lostPassForm($option){

	$mainframe = mosMainFrame::getInstance();
	$mainframe->SetPageTitle(_LOST_PASSWORDWORD);

	$config = Jconfig::getInstance();
	$database = database::getInstance();

	$user_config = new configUser_lostpass($database);

	//Шаблон
	$template = $user_config->get('template');
	$template_dir = 'components/com_users/view/lostpass';

	if($user_config->get('template_dir')){
		$template_dir = 'templates' . DS . JTEMPLATE . '/html/com_users/lostpass';
	}
	$template_file = JPATH_BASE . DS . $template_dir . DS . $template;

	if(is_file($template_file)){
		include_once ($template_file);
	}
}

function sendNewPass(){
	josSpoofCheck();

	$database = database::getInstance();
	$config = Jconfig::getInstance();

	$checkusername = stripslashes(mosGetParam($_POST, 'checkusername', ''));
	$confirmEmail = stripslashes(mosGetParam($_POST, 'confirmEmail', ''));

	if($config->config_captcha_reg){
		session_name(mosMainFrame::sessionCookieName());
		session_start();
		$captcha = strval(mosGetParam($_POST, 'captcha', null));
		$captcha_keystring = mosGetParam($_SESSION, 'captcha_keystring');
		if($captcha_keystring !== $captcha){
			unset($_SESSION['captcha_keystring']);
			mosRedirect('index.php?option=com_users&task=lostPassword', _BAD_CAPTCHA_STRING);
			exit;
		}
		session_unset();
		session_write_close();
	}

	$query = "SELECT id FROM #__users WHERE username = " . $database->Quote($checkusername) . " AND email = " . $database->Quote($confirmEmail);
	$database->setQuery($query);
	if(!($user_id = $database->loadResult()) || !$checkusername || !$confirmEmail){
		mosRedirect("index.php?option=com_users&task=lostPassword", _ERROR_PASSWORD);
	}

	echo $newpass = mosMakePassword();
	$message = _NEWPASS_MSG;
	eval("\$message = \"$message\";");
	$subject = _NEWPASS_SUB;
	eval("\$subject = \"$subject\";");

	mosMail($config->config_mailfrom, $config->config_fromname, $confirmEmail, $subject, $message);

	$salt = mosMakePassword(16);
	$crypt = md5($newpass . $salt);
	$newpass = $crypt . ':' . $salt;
	$sql = "UPDATE #__users SET block = 0, password = " . $database->Quote($newpass) . " WHERE id = " . (int)$user_id;
	$database->setQuery($sql);
	if(!$database->query()){
		die("SQL error" . $database->stderr(true));
	}

	mosRedirect('index.php', _NEWPASS_SENT);
}

function registerForm($option, $useractivation){

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();
	$acl = &gacl::getInstance();

	if(!$mainframe->getCfg('allowUserRegistration')){
		mosNotAuth();
		return;
	}

	$params = new configUser_registration($database);

	$type = mosGetParam($_REQUEST, 'type', '');
	$gid = $params->get('gid');

	$mainframe->SetPageTitle($params->get('title'));

	//Определяем шаблон для вывода регистрационной формы
	$template = 'default.php';

	if(!$params->get('template')){
		if($type){
			if(is_file(JPATH_BASE . DS . 'components' . DS . 'com_users' . DS . 'view' . DS . 'registration' . DS . $type . '.php')){
				$template = $type . '.php';
			}
			$gid = $acl->get_group_id($type, 'ARO');
		}
	}
	$gid_check = mosHash($gid);

	// used for spoof hardening
	$validate = josSpoofValue();

	include (JPATH_BASE . DS . 'components' . DS . 'com_users' . DS . 'view' . DS . 'registration' . DS . $template);

}

function saveRegistration(){

	josSpoofCheck();

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();
	$acl = &gacl::getInstance();

	if($mainframe->getCfg('allowUserRegistration') == 0){
		mosNotAuth();
		return;
	}

	$params = new configUser_registration($database);

	if($mainframe->getCfg('captcha_reg')){
		session_name(mosMainFrame::sessionCookieName());
		session_start();
		$captcha = strval(mosGetParam($_POST, 'captcha', null));
		$captcha_keystring = mosGetParam($_SESSION, 'captcha_keystring');
		if($captcha_keystring !== $captcha){
			unset($_SESSION['captcha_keystring']);
			mosRedirect('index.php?option=com_users&task=register', _BAD_CAPTCHA_STRING);
			exit;
		}
		session_unset();
		session_write_close();
	}

	$row = new mosUser($database);

	if(!$row->bind($_POST, 'usertype')){
		mosErrorAlert($row->getError());
	}

	$row->name = trim($row->name);
	$row->email = trim($row->email);
	$row->username = trim($row->username);
	$row->password = trim($row->password);

	mosMakeHtmlSafe($row);

	$row->id = 0;

	//Определяем группу пользователя
	//Если в настройках регистрации выбрано использование разных шаблонов - будем брать группу из скрытого поля
	//регистрационной формы
	//Если используется единый шаблон - группу берем из натроек регистрации.
	if(!$params->get('template')){
		$row->gid = $_POST['gid'];
	} else{
		$row->gid = $params->get('gid');
	}
	//Проверяем, не подменена ли группа "на лету"
	$gid_md5 = $_POST['gid_check'];

	if($gid_md5 != md5($mainframe->getCfg('secret') . md5($row->gid))){
		mosErrorAlert('Ooops!');
	}

	$row->usertype = $acl->get_group_name($row->gid, 'ARO');

	if($mainframe->getCfg('useractivation') == 1){
		$row->activation = md5(mosMakePassword());
		$row->block = '1';
	}

	if(!$row->check()){
		echo "<script> alert('" . html_entity_decode($row->getError()) . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$pwd = $row->password;

	$salt = mosMakePassword(16);
	$crypt = md5($row->password . $salt);
	$row->password = $crypt . ':' . $salt;

	$row->registerDate = date('Y-m-d H:i:s');

	if(!$row->store()){
		echo "<script> alert('" . html_entity_decode($row->getError()) . "'); window.history.go(-1); </script>\n";
		exit();
	}
	//$row->id = $database->insertid();
	$row->checkin();

	$email_info = array();
	$email_info['name'] = trim($row->name);
	$email_info['email'] = trim($row->email);
	$email_info['username'] = trim($row->username);

	//Подготавливаем письмо пользователю
	$email_info['subject'] = sprintf(_SEND_SUB, $email_info['name'], $mainframe->getCfg('sitename'));
	$email_info['subject'] = html_entity_decode($email_info['subject'], ENT_QUOTES);

	if($mainframe->getCfg('useractivation') == 1){
		$email_info['message'] = sprintf(_USEND_MSG_ACTIVATE, $email_info['name'],
			$mainframe->getCfg('sitename'),
			JPATH_SITE . "/index.php?option=com_users&task=activate&activation=" . $row->activation,
			JPATH_SITE, $email_info['username'], $pwd);
	} else{
		$email_info['message'] = sprintf(_USEND_MSG, $email_info['name'], $mainframe->getCfg('sitename'), JPATH_SITE);
	}
	$email_info['message'] = html_entity_decode($email_info['message'], ENT_QUOTES);

	if($mainframe->getCfg('mailfrom') != '' && $mainframe->getCfg('fromname') != ''){
		$email_info['adminName'] = $mainframe->getCfg('fromname');
		$email_info['adminEmail'] = $mainframe->getCfg('mailfrom');
	} else{
		// use email address and name of first superadmin for use in email sent to user
		$query = "SELECT name, email FROM #__users WHERE LOWER( usertype ) = 'superadministrator' OR LOWER( usertype ) = 'super administrator'";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		$row2 = $rows[0];
		$email_info['adminName'] = $row2->name;
		$email_info['adminEmail'] = $row2->email;
	}

	// Отсылаем пользователю письмо только в случае, если не включено "Активация администратором"
	if(!$params->get('admin_activation')){
		$row->send_mail_to_user($email_info);
	}

	// Подготавливаем письмо администраторам сайта
	$email_info['subject'] = sprintf(_SEND_SUB, $email_info['name'], $mainframe->getCfg('sitename'));
	$email_info['message'] = sprintf(_ASEND_MSG, $email_info['adminName'], $mainframe->getCfg('sitename'), $row->name, $email_info['email'], $email_info['username']);
	$email_info['subject'] = html_entity_decode($email_info['subject'], ENT_QUOTES);
	$email_info['message'] = html_entity_decode($email_info['message'], ENT_QUOTES);
	//отправляем письма
	$row->send_mail_to_admins($email_info);

	if($mainframe->getCfg('useractivation') == 1){

		$msg = _REG_COMPLETE_ACTIVATE;
		if($params->get('admin_activation')){
			$msg = _WAIT_ACTIVATION;
		}

		if($params->get('redirect_url')){
			mosRedirect($params->get('redirect_url'), $msg);
		}

		//Определяем шаблон
		$template = 'default.php';

		//Если в параметрах настройки регистрации задано использование
		//разных шаблонов для разных групп пользователей -
		//даём возможность выводить сообщения также с помощью разных шаблонов
		//Если шаблон для группы не найден - используем стандартный шаблон
		if(!$params->get('template')){
			$group_name = $acl->get_group_name($row->gid, 'ARO');
			if($group_name){
				if(!is_file(JPATH_BASE . DS . 'components' . DS . 'com_users' . DS . 'view' . DS . 'after_registration' . DS . $group_name . '.php')){
					$template = $group_name . '.php';
				}
			}
		}

		include (JPATH_BASE . DS . 'components' . DS . 'com_users' . DS . 'view' . DS . 'after_registration' . DS . $template);
		return;

	} else{
		$msg = _REG_COMPLETE;
		//$mainframe->login($row->username,$row->password,0,$row->id);
		mosRedirect('index.php?option=com_users&task=profile&user=' . $row->id, $msg);
	}
}

function activate(){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if($my->id){
		mosRedirect('index.php');
	}

	if($mainframe->getCfg('allowUserRegistration') == '0' || $mainframe->getCfg('useractivation') == '0'){
		mosNotAuth();
		return;
	}

	$activation = stripslashes(mosGetParam($_REQUEST, 'activation', ''));

	if(empty($activation)){
		echo _REG_ACTIVATE_NOT_FOUND;
		return;
	}

	$query = "SELECT id FROM #__users WHERE activation = " . $database->Quote($activation) . " AND block = 1";
	$database->setQuery($query);
	$result = $database->loadResult();

	if($result){
		$query = "UPDATE #__users SET block = 0, activation = '' WHERE activation = " . $database->Quote($activation) . " AND block = 1";
		$database->setQuery($query);
		if(!$database->query()){
			if(!defined(_REG_ACTIVATE_FAILURE)){
				DEFINE('_REG_ACTIVATE_FAILURE', _USER_ACTIVATION_FAILED);
			}
			echo _REG_ACTIVATE_FAILURE;
		} else{
			if($mainframe->getCfg('auto_activ_login') == 1){
				$user = new mosUser($database);
				if($user->load($result)){
					$_POST['remember'] = 1;
					$mainframe->login($user->username, $user->password);
					mosRedirect('index.php', _REG_ACTIVATE_COMPLETE);
				}
			} else{
				echo _REG_ACTIVATE_COMPLETE;
			}
		}
	} else{
		echo _REG_ACTIVATE_NOT_FOUND;
	}
}