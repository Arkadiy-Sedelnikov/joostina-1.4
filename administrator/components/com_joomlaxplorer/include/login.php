<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 * @package joomlaXplorer
 * @copyright soeren 2007
 * @author The joomlaXplorer project (http://joomlacode.org/gf/project/joomlaxplorer/)
 * @author The  The QuiX project (http://quixplorer.sourceforge.net)
 **/
defined('_JLINDEX') or die();
require _QUIXPLORER_PATH . "/include/fun_users.php";
load_users();
if(isset($_SESSION))
	$GLOBALS['__SESSION'] = &$_SESSION;
elseif(isset($HTTP_SESSION_VARS))
	$GLOBALS['__SESSION'] = &$HTTP_SESSION_VARS;
else
	logout();
function login(){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	if(isset($GLOBALS['__SESSION']["s_user"])){
		if(!activate_user($GLOBALS['__SESSION']["s_user"], $GLOBALS['__SESSION']["s_pass"])){
			logout();
		}
	} else{
		if(isset($GLOBALS['__POST']["p_pass"]))
			$p_pass = $GLOBALS['__POST']["p_pass"];
		else
			$p_pass = "";
		if(isset($GLOBALS['__POST']["p_user"])){
			if(!activate_user(stripslashes($GLOBALS['__POST']["p_user"]), md5(stripslashes($p_pass)))){
				logout();
			}
			return;
		} else{
			show_header($GLOBALS["messages"]["actlogin"]);
			echo "<br><table width=\"300\"><tr><td colspan=\"2\" class=\"header\" nowrap><b>";
			echo $GLOBALS["messages"]["actloginheader"] . "</b></td></tr>\n<form name=\"login\" action=\"";
			echo make_link("login", null, null) . "\" method=\"post\">\n";
			echo "<tr><td>" . $GLOBALS["messages"]["miscusername"] . ":</td><td align=\"right\">";
			echo "<input name=\"p_user\" type=\"text\" value=\"" . $my->username . "\" size=\"25\"></td></tr>\n";
			echo "<tr><td>" . $GLOBALS["messages"]["miscpassword"] . ":</td><td align=\"right\">";
			echo "<input name=\"p_pass\" type=\"password\" size=\"25\"></td></tr>\n";
			echo "<tr><td>" . $GLOBALS["messages"]["misclang"] . ":</td><td align=\"right\">";
			echo "<select name=\"lang\">\n";
			@include _QUIXPLORER_PATH . "/languages/_info.php";
			echo "</select></td></tr>\n";
			echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"";
			echo $GLOBALS["messages"]["btnlogin"] . "\"></td></tr>\n</form></table><br>\n";
			?>
		<script language="JavaScript1.2" type="text/javascript">
			<!--
			if (document.login) document.login.p_user.focus();
			// -->
		</script><?php
			show_footer();
			exit;
		}
	}
}

function logout(){
	$GLOBALS['__SESSION']["s_user"] = "";
	$GLOBALS['__SESSION']["s_pass"] = "";
	header("location: " . $GLOBALS["script_name"]);
}


?>
