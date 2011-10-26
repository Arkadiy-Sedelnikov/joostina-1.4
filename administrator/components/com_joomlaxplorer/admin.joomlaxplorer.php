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
 * MAIN FILE! (formerly known as index.php)
 *
 * @version $Id: $
 *
 * @package joomlaXplorer
 * @copyright soeren 2007
 * @author The joomlaXplorer project (http://joomlacode.org/gf/project/joomlaxplorer/)
 * @author The  The QuiX project (http://quixplorer.sourceforge.net)
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * Alternatively, the contents of this file may be used under the terms
 * of the GNU General Public License Version 2 or later (the "GPL"), in
 * which case the provisions of the GPL are applicable instead of
 * those above. If you wish to allow use of your version of this file only
 * under the terms of the GPL and not to allow others to use
 * your version of this file under the MPL, indicate your decision by
 * deleting  the provisions above and replace  them with the notice and
 * other provisions required by the GPL.  If you do not delete
 * the provisions above, a recipient may use your version of this file
 * under either the MPL or the GPL."
 *
 *
 * This is a component with full access to the filesystem of your joomla Site
 * I wouldn't recommend to let in Managers
 * allowed: Superadministrator
 **/
if(!$acl->acl_check('administration','config','users',$my->usertype)) {
	mosRedirect('index2.php',_NOT_AUTH);
}
// The joomlaXplorer version number
$GLOBALS['jx_version'] = '1.6.1';
$GLOBALS['jx_home'] = 'http://joomlacode.org/gf/project/joomlaxplorer/';

define('_QUIXPLORER_PATH',JPATH_BASE_ADMIN.'/components/com_joomlaxplorer');
define('_QUIXPLORER_FTPTMP_PATH',JPATH_BASE_ADMIN.'/components/com_joomlaxplorer/ftp_tmp');
define('_QUIXPLORER_URL',JPATH_SITE.'/'.JADMIN_BASE.'/components/com_joomlaxplorer');


umask(0002); // Added to make created files/dirs group writable

require _QUIXPLORER_PATH.'/include/init.php'; // Init

$action = stripslashes(mosGetParam($_REQUEST,'action'));
if($action == 'post')
	$action = mosGetParam($_REQUEST,'do_action');
elseif(empty($action))
	$action = 'list';

if(mosGetParam($_GET,'order','')=='') $GLOBALS["order"] = 'type';


mosCommonHTML::loadMootools();

if(jx_isXHR()) {
	error_reporting(0);
	while(@ob_end_clean()) ;
}

switch($action) { // Execute action
	//------------------------------------------------------------------------------
	// EDIT FILE
	case 'edit':
		require _QUIXPLORER_PATH.'/include/fun_edit.php';
		edit_file($dir,$item);
		break;

	// VIEW FILE
	case 'view':
		require _QUIXPLORER_PATH.'/include/fun_view.php';
		jx_show_file($dir,$item);
		break;
	//------------------------------------------------------------------------------
	// DELETE FILE(S)/DIR(S)
	case 'delete':
		require _QUIXPLORER_PATH.'/include/fun_del.php';
		del_items($dir);
		break;
	//------------------------------------------------------------------------------
	// COPY/MOVE FILE(S)/DIR(S)
	case 'copy':
	case 'move':
		require _QUIXPLORER_PATH.'/include/fun_copy_move.php';
		copy_move_items($dir);
		break;
	// RENAME FILE(S)/DIR(S)
	case 'rename':
		require _QUIXPLORER_PATH.'/include/fun_rename.php';
		rename_item($dir,$item);
		break;
	//------------------------------------------------------------------------------
	// DOWNLOAD FILE
	case 'download':
		require _QUIXPLORER_PATH.'/include/fun_down.php';
		@ob_end_clean(); // get rid of cached unwanted output
		download_item($dir,$item);
		ob_start(false); // prevent unwanted output
		exit;
		break;
	//------------------------------------------------------------------------------
	// UPLOAD FILE(S)
	case 'upload':
		require _QUIXPLORER_PATH.'/include/fun_up.php';
		upload_items($dir);
		break;
	//------------------------------------------------------------------------------
	// CREATE DIR/FILE
	case 'mkitem':
		require _QUIXPLORER_PATH.'/include/fun_mkitem.php';
		make_item($dir);
		break;
	//------------------------------------------------------------------------------
	// CHMOD FILE/DIR
	case 'chmod':
		require _QUIXPLORER_PATH.'/include/fun_chmod.php';
		chmod_item($dir,$GLOBALS['item']);
		break;
	//------------------------------------------------------------------------------
	// SEARCH FOR FILE(S)/DIR(S)
	case 'search':
		require _QUIXPLORER_PATH.'/include/fun_search.php';
		search_items($dir);
		break;
	//------------------------------------------------------------------------------
	// CREATE ARCHIVE
	case 'arch':
		require _QUIXPLORER_PATH.'/include/fun_archive.php';
		archive_items($dir);
		break;
	//------------------------------------------------------------------------------
	// EXTRACT ARCHIVE
	case 'extract':
		require _QUIXPLORER_PATH.'/include/fun_archive.php';
		extract_item($dir,$item);
		break;
	//------------------------------------------------------------------------------
	// USER-ADMINISTRATION
	case 'admin':
		require _QUIXPLORER_PATH.'/include/fun_admin.php';
		show_admin($dir);
		break;
	//------------------------------------------------------------------------------
	// joomla System Info
	case 'sysinfo':
		require _QUIXPLORER_PATH.'/include/fun_system_info.php';
		break;
	//------------------------------------------------------------------------------
	// FTP LOGIN
	case 'ftp_authentication':
		$ftp_login = mosGetParam($_POST,'ftp_login_name','');
		$ftp_pass = mosGetParam($_POST,'ftp_login_pass','');
		require (_QUIXPLORER_PATH.'/include/fun_ftpauthentication.php');
		ftp_authentication($ftp_login,$ftp_pass);
		break;
	case 'ftp_logout':
		require (_QUIXPLORER_PATH.'/include/fun_ftpauthentication.php');
		ftp_logout();
		break;
	//------------------------------------------------------------------------------
	// BOOKMARKS
	case 'modify_bookmark':
		$task = mosGetParam($_REQUEST,'task');
		require (_QUIXPLORER_PATH.'/include/fun_bookmarks.php');
		modify_bookmark($task,$dir);

		break;

	//------------------------------------------------------------------------------
	case 'show_error':
		show_error('');
		break;
	//------------------------------------------------------------------------------
	// DEFAULT: LIST FILES & DIRS
	case 'list':
	default:
		require _QUIXPLORER_PATH.'/include/fun_list.php';
		list_dir($dir);
	//------------------------------------------------------------------------------
} // end switch-statement
//------------------------------------------------------------------------------
//show_footer();
// Disconnect from ftp server
if(jx_isFTPMode()) {
	$GLOBALS['FTPCONNECTION']->disconnect();
}
// Empty the output buffer if this is a XMLHttpRequest
if(jx_isXHR()) {
	jx_exit();
}