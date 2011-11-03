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

if(!$acl->acl_check('administration','manage','users',$my->usertype,'components','com_users')) {
	mosRedirect('index2.php',_NOT_AUTH);
}

require_once ($mainframe->getPath('admin_html'));
require_once ($mainframe->getPath('class'));
require_once ($mainframe->getPath('config','com_users'));
//require_once (JPATH_BASE.'/components/com_users/users.config.php');

$cid = josGetArrayInts('cid');

switch($task) {
	case 'new':
		editUser(0,$option);
		break;

	case 'edit':
		editUser(intval($cid[0]),$option);
		break;

	case 'editA':
		editUser($id,$option);
		break;

	case 'save':
	case 'apply':
	// check to see if functionality restricted for use as demo site
		if(joomlaVersion::get('RESTRICT') == 1) {
			mosRedirect('index2.php?mosmsg='._RESTRICT_FUNCTION);
		} else {
			saveUser($task);
		}
		break;

	case 'remove':
		removeUsers($cid,$option);
		break;

	case 'block':
	// check to see if functionality restricted for use as demo site
		if(joomlaVersion::get('RESTRICT') == 1) {
			mosRedirect('index2.php?mosmsg='._RESTRICT_FUNCTION);
		} else {
			changeUserBlock($cid,1,$option);
		}
		break;

	case 'unblock':
		changeUserBlock($cid,0,$option);
		break;

	case 'logout':
		logoutUser($cid,$option,$task);
		break;

	case 'flogout':
		logoutUser($id,$option,$task);
		break;

	case 'cancel':
		cancelUser($option);
		break;

	case 'contact':
		$contact_id = mosGetParam($_POST,'contact_id','');
		mosRedirect('index2.php?option=com_contact&task=editA&id='.$contact_id);
		break;

	case 'config':
		config($option);
		break;

	case 'save_config':
		save_config();
		break;

	default:
		showUsers($option);
		break;
}

function config($option) {
	$database = database::getInstance();

	mosCommonHTML::loadOverlib();

	$act = mosGetParam($_REQUEST,'act','');
	$config_class = 'configUser_'.$act;
	$config = new $config_class($database);
	$config->display_config($option);

}

function save_config() {
	$database = database::getInstance();

	$act = mosGetParam($_REQUEST,'act','');
	$config_class = 'configUser_'.$act;
	$config = new $config_class($database);
	$config->save_config();

	mosRedirect('index2.php?option=com_users&task=config&act='.$act, _CONFIG_SAVED);
}

function showUsers($option) {
	global $my;

	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance(true);
	$acl = &gacl::getInstance();

	$filter_type = $mainframe->getUserStateFromRequest("filter_type{$option}",'filter_type',0);
	$filter_logged = intval($mainframe->getUserStateFromRequest("filter_logged{$option}",'filter_logged',0));
	$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit",'limit',$mainframe->getCfg('list_limit')));
	$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart",'limitstart',0));
	$search = $mainframe->getUserStateFromRequest("search{$option}",'search','');
	if(get_magic_quotes_gpc()) {
		$filter_type = stripslashes($filter_type);
		$search = stripslashes($search);
	}
	$where = array();

	if(isset($search) && $search != "") {
		$searchEscaped = $database->getEscaped(Jstring::trim(Jstring::strtolower($search)));
		$where[] = "(a.username LIKE '%$searchEscaped%' OR a.email LIKE '%$searchEscaped%' OR a.name LIKE '%$searchEscaped%')";
	}
	if($filter_type) {
		if($filter_type == 'Public Frontend') {
			$where[] = "(a.usertype = 'Registered' OR a.usertype = 'Author' OR a.usertype = 'Editor'OR a.usertype = 'Publisher')";
		} else
		if($filter_type == 'Public Backend') {
			$where[] = "(a.usertype = 'Manager' OR a.usertype = 'Administrator' OR a.usertype = 'Super Administrator')";
		} else {
			$where[] = "a.usertype = LOWER( ".$database->Quote($filter_type)." )";
		}
	}
	if($filter_logged == 1) {
		$where[] = "s.userid = a.id";
	} else
	if($filter_logged == 2) {
		$where[] = "s.userid IS NULL";
	}

	// exclude any child group id's for this user
	$pgids = $acl->get_group_children($my->gid,'ARO','RECURSE');

	mosArrayToInts($pgids);
	if(is_array($pgids) && count($pgids) > 0) {
		$where[] = '( a.gid != '.implode(' OR a.gid != ',$pgids).' )';
	}

	$query = "SELECT COUNT(a.id) FROM #__users AS a";

	if($filter_logged == 1 || $filter_logged == 2) {
		$query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
	}

	$query .= (count($where)?"\n WHERE ".implode(' AND ',$where):'');
	$database->setQuery($query);
	$total = $database->loadResult();

	require_once (JPATH_BASE.DS.JADMIN_BASE.'/includes/pageNavigation.php');
	$pageNav = new mosPageNav($total,$limitstart,$limit);

	$query = "SELECT a.*, g.name AS groupname FROM #__users AS a"
			."\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"
			// map user to aro
			."\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.aro_id"
			// map aro to group
			."\n INNER JOIN #__core_acl_aro_groups AS g ON g.group_id = gm.group_id";
	if($filter_logged == 1 || $filter_logged == 2) {
		$query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
	}

	$query .= (count($where)?"\n WHERE ".implode(' AND ',$where):"")."\n GROUP BY a.id";
	$database->setQuery($query,$pageNav->limitstart,$pageNav->limit);
	$rows = $database->loadObjectList();

	if($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$template = 'SELECT COUNT(s.userid) FROM #__session AS s WHERE s.userid = ';
	$n = count($rows);
	for($i = 0; $i < $n; $i++) {
		$row = &$rows[$i];
		$query = $template.(int)$row->id;
		$database->setQuery($query);
		$row->loggedin = $database->loadResult();
	}

	// get list of Groups for dropdown filter
	$query = "SELECT name AS value, name AS text FROM #__core_acl_aro_groups WHERE name != 'ROOT' AND name != 'USERS'";
	$types[] = mosHTML::makeOption('0',_com_users_SELECT_GROOP);
	$database->setQuery($query);
	$types = array_merge($types,$database->loadObjectList());
	$lists['type'] = mosHTML::selectList($types,'filter_type','class="inputbox" size="1" onchange="document.adminForm.submit( );"','value','text',"$filter_type");

	// get list of Log Status for dropdown filter
	$logged[] = mosHTML::makeOption(0,_com_users_SELECT_STATUS);
	$logged[] = mosHTML::makeOption(1,_com_users_USER_LOGED);
	$lists['logged'] = mosHTML::selectList($logged,'filter_logged','class="inputbox" size="1" onchange="document.adminForm.submit( );"','value','text',"$filter_logged");

	HTML_users::showUsers($rows,$pageNav,$search,$option,$lists);
}

/**
 * Edit the user
 * @param int The user ID
 * @param string The URL option
 */
function editUser($uid = '0',$option = 'users') {
	global $my;

	$mainframe = mosMainFrame::getInstance(true);
	$database = $mainframe->getDBO();
	$acl = &gacl::getInstance();

	$msg = checkUserPermissions(array($uid),"edit",true);
	if($msg) {
		echo "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1);</script>\n";
		exit;
	}

	$row = new mosUser($database);
	// load the row from the db table
	$row->load((int)$uid);

	if($uid) {
		$query = "SELECT* FROM #__contact_details WHERE user_id = ".(int)$row->id;
		$database->setQuery($query);
		$contact = $database->loadObjectList();

		$row->name = trim($row->name);
		$row->email = trim($row->email);
		$row->username = trim($row->username);
		$row->password = trim($row->password);

	} else {
		$contact = null;
		$row->block = 0;
	}

	// check to ensure only super admins can edit super admin info
	if(($my->gid < 25) && ($row->gid == 25)) {
		mosRedirect('index2.php?option=com_users',_NOT_AUTH);
	}

	$my_group = strtolower($acl->get_group_name($row->gid,'ARO'));
	if($my_group == 'super administrator' && $my->gid != 25) {
		$lists['gid'] = '<input type="hidden" name="gid" value="'.$my->gid.'" /><strong>'._SUPER_ADMINISTRATOR.'</strong>';
	} elseif($my->gid == 24 && $row->gid == 24) {
		$lists['gid'] = '<input type="hidden" name="gid" value="'.$my->gid.'" /><strong>'._ADMINISTRATOR.'</strong>';
	} else {
		// ensure user can't add group higher than themselves
		$my_groups = $acl->get_object_groups('users',$my->id,'ARO');
		if(is_array($my_groups) && count($my_groups) > 0) {
			$ex_groups = $acl->get_group_children($my_groups[0],'ARO','RECURSE');
		} else {
			$ex_groups = array();
		}
		$gtree = $acl->get_group_children_tree(null,'USERS',false);
		// remove users 'above' me
		$i = 0;
		while($i < count($gtree)) {
			if(in_array($gtree[$i]->value,$ex_groups)) {
				array_splice($gtree,$i,1);
			} else {
				$i++;
			}
		}
		$lists['gid'] = mosHTML::selectList($gtree,'gid','size="10"','value','text',$row->gid);
	}

	// build the html select list
	$lists['block'] = mosHTML::yesnoRadioList('block','class="inputbox" size="1"',$row->block);
	// build the html select list
	$lists['sendEmail'] = mosHTML::yesnoRadioList('sendEmail','class="inputbox" size="1"',$row->sendEmail);

	$file = $mainframe->getPath('com_xml','com_users');
	$params = new mosUserParameters($row->params,$file,'component');

	$user_extra = new userUsersExtra($database);
	$user_extra->load((int)$uid);
	$row->user_extra = $user_extra;

	HTML_users::edituser($row,$contact,$lists,$option,$uid,$params);
}

function saveUser($task) {
	global $my;
	global $mosConfig_mailfrom,$mosConfig_fromname,$mosConfig_sitename;

	josSpoofCheck();

	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance(true);
	$acl = &gacl::getInstance();

	$userIdPosted = mosGetParam($_POST,'id');
	if($userIdPosted) {
		$msg = checkUserPermissions(array($userIdPosted),'save',in_array($my->gid,array(24,25)));
		if($msg) {
			echo "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1);</script>\n";
			exit;
		}
	}

	$row = new mosUser($database);
	if(!$row->bind($_POST)) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->name = trim($row->name);
	$row->email = trim($row->email);
	$row->username = trim($row->username);

	// sanitise fields
	$row->id = (int)$row->id;
	// sanitise gid field
	$row->gid = (int)$row->gid;

	$isNew = !$row->id;
	$pwd = '';

	// MD5 hash convert passwords
	if($isNew) {
		// new user stuff
		if($row->password == '') {
			$pwd = mosMakePassword();

			$salt = mosMakePassword(16);
			$crypt = md5($pwd.$salt);
			$row->password = $crypt.':'.$salt;
		} else {
			$pwd = trim($row->password);

			$salt = mosMakePassword(16);
			$crypt = md5($pwd.$salt);
			$row->password = $crypt.':'.$salt;
		}
		$row->registerDate = date('Y-m-d H:i:s');
	} else {
		$original = new mosUser($database);
		$original->load((int)$row->id);

		// existing user stuff
		if($row->password == '') {
			// password set to null if empty
			$row->password = null;
		} else {
			$row->password = trim($row->password);
			$salt = mosMakePassword(16);
			$crypt = md5($row->password.$salt);
			$row->password = $crypt.':'.$salt;
		}

		// if group has been changed and where original group was a Super Admin
		if($row->gid != $original->gid) {
			if($original->gid == 25) {
				// count number of active super admins
				$query = "SELECT COUNT( id ) FROM #__users WHERE gid = 25 AND block = 0";
				$database->setQuery($query);
				$count = $database->loadResult();

				if($count <= 1) {
					// disallow change if only one Super Admin exists
					echo "<script> alert('"._NO_RIGHT_TO_CHANGE_GROUP."'); window.history.go(-1); </script>\n";
					exit();
				}
			}

			$user_group = strtolower($acl->get_group_name($original->gid,'ARO'));
			if(($user_group == 'super administrator' && $my->gid != 25)) {
				// disallow change of super-Admin by non-super admin
				echo "<script> alert('"._NO_RIGHT_TO_CHANGE_GROUP."'); window.history.go(-1); </script>\n";
				exit();
			} else
			if($my->gid == 24 && $original->gid == 24) {
				// disallow change of super-Admin by non-super admin
				echo "<script> alert('"._NO_RIGHT_TO_CHANGE_GROUP."'); window.history.go(-1); </script>\n";
				exit();
			} // ensure user can't add group higher than themselves done below
		}
	}
	/*
	* // if user is made a Super Admin group and user is NOT a Super Admin		
	* if ( $row->gid == 25 && $my->gid != 25 ) {
	* // disallow creation of Super Admin by non Super Admin users
	* echo "<script> alert('Вы не можете создать пользователя с этим уровнем доступа. Это может сделать только Главный администратор сайта'); window.history.go(-1); </script>\n";
	* exit();
	* }
	*/
	// Security check to avoid creating/editing user to higher level than himself: response to artf4529.
	if(!in_array($row->gid,getGIDSChildren($my->gid))) {
		// disallow creation of Super Admin by non Super Admin users
		echo "<script> alert('"._NO_RIGHT_TO_USER_CREATION."'); window.history.go(-1); </script>\n";
		exit();
	}

	// save usertype to usertype column
	$query = "SELECT name FROM #__core_acl_aro_groups WHERE group_id = ".(int)$row->gid;
	$database->setQuery($query);
	$usertype = $database->loadResult();
	$row->usertype = $usertype;

	// save params
	$params = mosGetParam($_POST,'params','');
	if(is_array($params)) {
		$txt = array();
		foreach($params as $k => $v) {
			$txt[] = "$k=$v";
		}
		$row->params = implode("\n",$txt);
	}

	if(!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if(!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$user_id = $row->id;

	//Сохранение дополнительной информации
	$user_extra = new userUsersExtra($database);
	$ret = $user_extra->load((int)$user_id);
	if(!$user_extra->bind($_POST)) {
		echo "<script> alert('".$user_extra->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$user_extra->birthdate  = $_POST['birthdate_year'].'-'.$_POST['birthdate_month'].'-'.$_POST['birthdate_day'].' 00:00:00';

	if(!$ret) {
		$user_extra->insert($user_id);
	}
	$user_extra->store();

	$row->checkin();

	// updates the current users param settings
	if($my->id == $row->id) {
		//session_start();
		$_SESSION['session_user_params'] = $row->params;
		session_write_close();
	}

	// update the ACL
	if(!$isNew) {
		$query = "SELECT aro_id FROM #__core_acl_aro WHERE value = ".(int)$row->id;
		$database->setQuery($query);
		$aro_id = $database->loadResult();

		$query = "UPDATE #__core_acl_groups_aro_map SET group_id = ".(int)$row->gid." WHERE aro_id = ".(int)$aro_id;
		$database->setQuery($query);
		$database->query() or die($database->stderr());
	}

	// for new users, email username and password
	if($isNew) {
		$query = "SELECT email FROM #__users WHERE id = ".(int)$my->id;
		$database->setQuery($query);
		$adminEmail = $database->loadResult();

		$subject = _NEW_USER_MESSAGE_SUBJECT;
		$message = sprintf(_NEW_USER_MESSAGE,$row->name,$mosConfig_sitename,JPATH_SITE,$row->username,$pwd);

		if($mosConfig_mailfrom != "" && $mosConfig_fromname != "") {
			$adminName = $mosConfig_fromname;
			$adminEmail = $mosConfig_mailfrom;
		} else {
			$query = "SELECT name, email FROM #__users WHERE gid = 25";
			$database->setQuery($query);
			$admins = $database->loadObjectList();
			$admin = $admins[0];
			$adminName = $admin->name;
			$adminEmail = $admin->email;
		}

		mosMail($adminEmail,$adminName,$row->email,$subject,$message);
	}

	if(!$isNew) {
		// if group has been changed
		if($original->gid != $row->gid) {
			// delete user acounts active sessions
			logoutUser($row->id,'com_users','change');
		}
	}

	switch($task) {
		case 'apply':
			$msg = _PROFILE_SAVE_SUCCESS.': '.$row->name;
			mosRedirect('index2.php?option=com_users&task=editA&hidemainmenu=1&id='.$row->id,$msg);
			break;

		case 'save':
		default:
			$msg = _PROFILE_SAVE_SUCCESS.': '.$row->name;
			mosRedirect('index2.php?option=com_users',$msg);
			break;
	}
}

/**
 * Cancels an edit operation
 * @param option component option to call
 */
function cancelUser($option) {
	mosRedirect('index2.php?option='.$option.'&task=view');
}

function removeUsers($cid,$option) {
	global $my;
	josSpoofCheck();

	$database = database::getInstance();
	$mainframe = mosMainFrame::getInstance(true);
	$acl = &gacl::getInstance();

	if(!is_array($cid) || count($cid) < 1) {
		echo "<script> alert('"._CHOOSE_OBJ_DELETE."'); window.history.go(-1);</script>\n";
		exit;
	}

	$msg = checkUserPermissions($cid,'delete');

	if(!$msg && count($cid)) {
		$obj = new mosUser($database);
		foreach($cid as $id) {
			$obj->load($id);
			$count = 2;
			if($obj->gid == 25) {
				// count number of active super admins
				$query = "SELECT COUNT( id ) FROM #__users WHERE gid = 25 AND block = 0";
				$database->setQuery($query);
				$count = $database->loadResult();
			}

			if($count <= 1 && $obj->gid == 25) {
				// cannot delete Super Admin where it is the only one that exists
				$msg = _CANNOT_DEL_ONE_SUPER_ADMIN;
			} else {
				// delete user
				$obj->delete($id);
				$msg = $obj->getError();

				// delete user acounts active sessions
				logoutUser($id,'com_users','remove');
			}
		}
	}

	mosRedirect('index2.php?option='.$option,$msg);
}


/**
 * Blocks or Unblocks one or more user records
 * @param array An array of unique category id numbers
 * @param integer 0 if unblock, 1 if blocking
 * @param string The current url option
 */
function changeUserBlock($cid = null,$block = 1,$option) {
	josSpoofCheck();

	$database = database::getInstance();

	$action = $block?'block':'unblock';

	if(count($cid) < 1) {
		echo "<script type=\"text/javascript\"> alert('"._CHOOSE_USER_TO." $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$msg = checkUserPermissions($cid,$action);
	if($msg) {
		echo "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1);</script>\n";
		exit;
	}

	mosArrayToInts($cid);
	$cids = 'id='.implode(' OR id=',$cid);

	$query = "UPDATE #__users"
			."\n SET block = ".(int)$block
			."\n WHERE ( $cids )";
	$database->setQuery($query);
	if(!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	// if action is to block a user
	if($block == 1) {
		foreach($cid as $id) {
			// delete user acounts active sessions
			logoutUser($id,'com_users','block');
		}
	}

	//TODO: сделать отсылку письма
	//Если в настройках регистрации включен параметр "Активация администратором",
	//отправляем письмо пользователю, что его аккаунт был активирован

	mosRedirect('index2.php?option='.$option);
}

/**
 * @param array An array of unique user id numbers
 * @param string The current url option
 */
function logoutUser($cid = null,$option,$task) {
	global $my;
	josSpoofCheck(null, null, 'request');

	$database = database::getInstance();

	if(is_array($cid)) {
		if(count($cid) < 1) {
			mosRedirect('index2.php?option='.$option,_PLEASE_CHOOSE_USER);
		}

		foreach($cid as $cidA) {
			$temp = new mosUser($database);
			$temp->load($cidA);

			// check to see whether a Administrator is attempting to log out a Super Admin
			if(!($my->gid == 24 && $temp->gid == 25)) {
				$id[] = $cidA;
			}
		}
		mosArrayToInts($cid);
		$ids = 'userid='.implode(' OR userid=',$cid);
	} else {
		$temp = new mosUser($database);
		$temp->load($cid);

		// check to see whether a Administrator is attempting to log out a Super Admin
		if($my->gid == 24 && $temp->gid == 25) {
			echo "<script> alert('"._CANNOT_DISABLE_SUPER_ADMIN."'); window.history.go(-1); </script>\n";
			exit();
		}
		$ids = 'userid='.(int)$cid;
	}

	$query = "DELETE FROM #__session WHERE ( $ids )";
	$database->setQuery($query);
	$database->query();

	switch($task) {
		case 'flogout':
			mosRedirect('index2.php',$database->getErrorMsg());
			break;

		case 'remove':
		case 'block':
		case 'change':
			return;
			break;

		default:
			mosRedirect('index2.php?option='.$option,$database->getErrorMsg());
			break;
	}
}

/**
 * Check if users are of lower permissions than current user (if not super-admin) and if the user himself is not included
 *
 * @param array of userId $cid
 * @param string $actionName to insert in message.
 * @return string of error if error, otherwise null
 * Added 1.0.11
 */
function checkUserPermissions($cid,$actionName,$allowActionToMyself = false) {
	global $my;

	$database = database::getInstance();
	$acl = &gacl::getInstance();

	$msg = null;
	if(is_array($cid) && count($cid)) {
		$obj = new mosUser($database);
		foreach($cid as $id) {
			if($id != 0) {
				$obj->load($id);
				$groups = $acl->get_object_groups('users',$id,'ARO');
				$this_group = strtolower($acl->get_group_name($groups[0],'ARO'));
			} else {
				$this_group = 'Registered'; // minimal user group
				$obj->gid = $acl->get_group_id($this_group,'ARO');
			}

			if(!$allowActionToMyself && $id == $my->id) {
				$msg .= 'You cannot '.$actionName.' yourself!';
			} else
			if(($obj->gid == $my->gid && !in_array($my->gid,array(24,25))) || ($obj->gid &&
							!in_array($obj->gid,getGIDSChildren($my->gid)))) {
				$msg .= 'You cannot '.$actionName.' `'.$this_group.'`. '._THIS_CAN_DO_HIGHLEVEL_USERS;
			}
		}
	}

	return $msg;
}

/**
 * Added 1.0.11
 */
function getGIDSChildren($gid) {
	$database = database::getInstance();

	$standardlist = array(-2,);

	$query = "SELECT g1.group_id, g1.name FROM #__core_acl_aro_groups g1"
			."\n LEFT JOIN #__core_acl_aro_groups g2 ON g2.lft >= g1.lft WHERE g2.group_id = ".(int)$gid."\n ORDER BY g1.name";
	$database->setQuery($query);
	$array = $database->loadResultArray();

	if($gid > 0) {
		$standardlist[] = -1;
	}
	$array = array_merge($array,$standardlist);

	return $array;
}

/**
 * Added 1.0.11
 */
function getGIDSParents($gid) {
	$database = database::getInstance();

	$query = "SELECT g1.group_id, g1.name"
			."\n FROM #__core_acl_aro_groups g1"
			."\n LEFT JOIN #__core_acl_aro_groups g2 ON g2.lft <= g1.lft"
			."\n WHERE g2.group_id = ".(int)$gid."\n ORDER BY g1.name";
	$database->setQuery($query);
	$array = $database->loadResultArray();

	return $array;
}