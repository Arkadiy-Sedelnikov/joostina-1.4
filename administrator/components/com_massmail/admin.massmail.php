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

// ensure user has access to this function
if(!$acl->acl_check('administration','manage','users',$my->usertype,
'components','com_massmail')) {
	mosRedirect('index2.php',_NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));

switch($task) {
	case 'send':
		sendMail();
		break;

	case 'cancel':
		mosRedirect('index2.php');
		break;

	default:
		messageForm($option);
		break;
}

function messageForm($option) {
	global $acl;

	$gtree = array(mosHTML::makeOption(0,_ALL_USER_GROUPS));

	// get list of groups
	$lists = array();
	$gtree = array_merge($gtree,$acl->get_group_children_tree(null,'USERS',false));
	$lists['gid'] = mosHTML::selectList($gtree,'mm_group','size="10"','value',
			'text',0);

	HTML_massmail::messageForm($lists,$option);
}

function sendMail() {
	global $database,$my,$acl;
	global $mosConfig_sitename;
	global $mosConfig_mailfrom,$mosConfig_fromname;
	josSpoofCheck();
	$mode = intval(mosGetParam($_POST,'mm_mode',0));
	$subject = strval(mosGetParam($_POST,'mm_subject',''));
	$gou = mosGetParam($_POST,'mm_group',null);
	$recurse = strval(mosGetParam($_POST,'mm_recurse','NO_RECURSE'));
	// pulls message inoformation either in text or html format
	if($mode) {
		$message_body = $_POST['mm_message'];
	} else {
		// automatically removes html formatting
		$message_body = strval(mosGetParam($_POST,'mm_message',''));
	}
	$message_body = stripslashes($message_body);

	if(!$message_body || !$subject || $gou === null) {
		mosRedirect('index2.php?option=com_massmail&mosmsg='._PLEASE_FILL_FORM);
	}

	// get users in the group out of the acl
	$to = $acl->get_group_objects($gou,'ARO',$recurse);

	$rows = array();
	if(count($to['users']) || $gou === '0') {
		// Get sending email address
		$query = "SELECT email"."\n FROM #__users"."\n WHERE id = ".(int)$my->id;
		$database->setQuery($query);
		$my->email = $database->loadResult();

		mosArrayToInts($to['users']);
		$user_ids = 'id='.implode(' OR id=',$to['users']);

		// Get all users email and group except for senders
		$query = "SELECT email"."\n FROM #__users"."\n WHERE id != ".(int)$my->id.($gou
						!== '0'?" AND ( $user_ids )":'');
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		// Build e-mail message format
		$message_header = sprintf(_MASSMAIL_MESSAGE,html_entity_decode($mosConfig_sitename,
				ENT_QUOTES));
		$message = $message_header.$message_body;
		$subject = html_entity_decode($mosConfig_sitename,ENT_QUOTES).' / '.
				stripslashes($subject);

		//Send email
		foreach($rows as $row) {
			mosMail($mosConfig_mailfrom,$mosConfig_fromname,$row->email,$subject,$message,$mode);
		}
	}

	$msg = _MESSAGE_SENDED_TO_USERS.count($rows);
	mosRedirect('index2.php?option=com_massmail',$msg);
}