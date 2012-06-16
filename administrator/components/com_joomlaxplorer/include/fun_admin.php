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
defined('_VALID_MOS') or die();
function admin($admin, $dir){
	show_header($GLOBALS["messages"]["actadmin"]);
	include _QUIXPLORER_PATH . "/include/js_admin.php";
	echo "<br/><HR width=\"95%\"><TABLE width=\"350\"><tr><td colspan=\"2\" class=\"header\"><B>";
	echo $GLOBALS["messages"]["actchpwd"] . ":</B></td></tr>\n";
	echo "<FORM name=\"chpwd\" action=\"" . make_link("admin", $dir, null) . "\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action2\" value=\"chpwd\">\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscoldpass"] . ": </td><td align=\"right\">";
	echo "<input type=\"password\" name=\"oldpwd\" size=\"25\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscnewpass"] . ": </td><td align=\"right\">";
	echo "<input type=\"password\" name=\"newpwd1\" size=\"25\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscconfnewpass"] . ": </td><td align=\"right\">";
	echo "<input type=\"password\" name=\"newpwd2\" size=\"25\"></td></tr>\n";
	echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"" . $GLOBALS["messages"]["btnchange"];
	echo "\" onClick=\"return check_pwd();\">\n</td></tr></FORM></TABLE>\n";
	if($admin){
		echo "<HR width=\"95%\"><TABLE width=\"350\"><tr><td colspan=\"6\" class=\"header\" nowrap>";
		echo "<B>" . $GLOBALS["messages"]["actusers"] . ":</B></td></tr>\n";
		echo "<tr><td colspan=\"5\">" . $GLOBALS["messages"]["miscuseritems"] . "</td></tr>\n";
		echo "<FORM name=\"userform\" action=\"" . make_link("admin", $dir, null) . "\" method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"action2\" value=\"edituser\">\n";
		$cnt = count($GLOBALS["users"]);
		for($i = 0; $i < $cnt; ++$i){
			$user = $GLOBALS["users"][$i][0];
			if(strlen($user) > 15)
				$user = substr($user, 0, 12) . "...";
			$home = $GLOBALS["users"][$i][2];
			if(strlen($home) > 30)
				$home = substr($home, 0, 27) . "...";
			echo "<tr><td width=\"1%\"><input TYPE=\"radio\" name=\"user\" value=\"";
			echo $GLOBALS["users"][$i][0] . "\"" . (($i == 0) ? " checked" : "") . "></td>\n";
			echo "<td width=\"30%\">" . $user . "</td><td width=\"60%\">" . $home . "</td>\n";
			echo "<td width=\"3%\">" . ($GLOBALS["users"][$i][4] ? $GLOBALS["messages"]["miscyesno"][2] :
				$GLOBALS["messages"]["miscyesno"][3]) . "</td>\n";
			echo "<td width=\"3%\">" . $GLOBALS["users"][$i][6] . "</td>\n";
			echo "<td width=\"3%\">" . ($GLOBALS["users"][$i][7] ? $GLOBALS["messages"]["miscyesno"][2] :
				$GLOBALS["messages"]["miscyesno"][3]) . "</td></tr>\n";
		}
		echo "<tr><td colspan=\"6\" align=\"right\">";
		echo "<input type=\"button\" value=\"" . $GLOBALS["messages"]["btnadd"];
		echo "\" onClick=\"javascript:location='" . make_link("admin", $dir, null) .
			"&action2=adduser';\">\n";
		echo "<input type=\"button\" value=\"" . $GLOBALS["messages"]["btnedit"];
		echo "\" onClick=\"javascript:Edit();\">\n";
		echo "<input type=\"button\" value=\"" . $GLOBALS["messages"]["btnremove"];
		echo "\" onClick=\"javascript:Delete();\">\n</td></tr></FORM></TABLE>\n";
	}
	echo "<HR width=\"95%\"><input type=\"button\" value=\"" . $GLOBALS["messages"]["btnclose"];
	echo "\" onClick=\"javascript:location='" . make_link("list", $dir, null) . "';\"><br/><br/>\n";
	?>
<script language="JavaScript1.2" type="text/javascript">
	<!--
	if (document.chpwd) document.chpwd.oldpwd.focus();
	// -->
</script><?php
}

function changepwd($dir){
	$pwd = md5(stripslashes($GLOBALS['__POST']["oldpwd"]));
	if($GLOBALS['__POST']["newpwd1"] != $GLOBALS['__POST']["newpwd2"])
		show_error($GLOBALS["error_msg"]["miscnopassmatch"]);
	$data = find_user($GLOBALS['__SESSION']["s_user"], $pwd);
	if($data == null)
		show_error($GLOBALS["error_msg"]["miscnouserpass"]);
	$data[1] = md5(stripslashes($GLOBALS['__POST']["newpwd1"]));
	if(!update_user($data[0], $data))
		show_error($data[0] . ": " . $GLOBALS["error_msg"]["chpass"]);
	activate_user($data[0], null);
	header("location: " . make_link("list", $dir, null));
}

function adduser($dir){
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"] ==
		"true"
	){
		$user = stripslashes($GLOBALS['__POST']["user"]);
		if($user == "" || $GLOBALS['__POST']["home_dir"] == ""){
			show_error($GLOBALS["error_msg"]["miscfieldmissed"]);
		}
		if($GLOBALS['__POST']["pass1"] != $GLOBALS['__POST']["pass2"])
			show_error($GLOBALS["error_msg"]["miscnopassmatch"]);
		$data = find_user($user, null);
		if($data != null)
			show_error($user . ": " . $GLOBALS["error_msg"]["miscuserexist"]);
		$data = array($user, md5(stripslashes($GLOBALS['__POST']["pass1"])),
			stripslashes($GLOBALS['__POST']["home_dir"]), stripslashes($GLOBALS['__POST']["home_url"]),
			$GLOBALS['__POST']["show_hidden"], stripslashes($GLOBALS['__POST']["no_access"]),
			$GLOBALS['__POST']["permissions"], $GLOBALS['__POST']["active"]);
		if(!add_user($data))
			show_error($user . ": " . $GLOBALS["error_msg"]["adduser"]);
		header("location: " . make_link("admin", $dir, null));
		return;
	}
	show_header($GLOBALS["messages"]["actadmin"] . ": " . $GLOBALS["messages"]["miscadduser"]);
	include _QUIXPLORER_PATH . "/include/js_admin2.php";
	echo "<form name=\"adduser\" action=\"" . make_link("admin", $dir, null) .
		"&action2=adduser\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"confirm\" value=\"true\"><br/><TABLE width=\"450\">\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscusername"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"text\" name=\"user\" size=\"30\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscpassword"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"password\" name=\"pass1\" size=\"30\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscconfpass"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"password\" name=\"pass2\" size=\"30\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["mischomedir"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"text\" name=\"home_dir\" size=\"30\" value=\"";
	echo $GLOBALS["home_dir"] . "\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["mischomeurl"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"text\" name=\"home_url\" size=\"30\" value=\"";
	echo $GLOBALS["home_url"] . "\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscshowhidden"] . ":</td>";
	echo "<td align=\"right\"><select name=\"show_hidden\">\n";
	echo "<option value=\"0\">" . $GLOBALS["messages"]["miscyesno"][1] . "</option>";
	echo "<option value=\"1\">" . $GLOBALS["messages"]["miscyesno"][0] . "</option>\n";
	echo "</select></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["mischidepattern"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"text\" name=\"no_access\" size=\"30\" value=\"^\\.ht\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscperms"] . ":</td><td align=\"right\"><select name=\"permissions\">\n";
	$permvalues = array(0, 1, 2, 3, 7);
	for($i = 0; $i < count($GLOBALS["messages"]["miscpermnames"]); ++$i){
		echo "<option value=\"" . $permvalues[$i] . "\">";
		echo $GLOBALS["messages"]["miscpermnames"][$i] . "</option>\n";
	}
	echo "</select></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscactive"] . ":</td>";
	echo "<td align=\"right\"><select name=\"active\">\n";
	echo "<option value=\"1\">" . $GLOBALS["messages"]["miscyesno"][0] . "</option>";
	echo "<option value=\"0\">" . $GLOBALS["messages"]["miscyesno"][1] . "</option>\n";
	echo "</select></td></tr>\n";
	echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"" . $GLOBALS["messages"]["btnadd"];
	echo "\" onClick=\"return check_pwd();\">\n<input type=\"button\" value=\"";
	echo $GLOBALS["messages"]["btncancel"] . "\" onClick=\"javascript:location='";
	echo make_link("admin", $dir, null) . "';\"></td></tr></FORM></TABLE><br/>\n";
	?>
<script language="JavaScript1.2" type="text/javascript">
	<!--
	if (document.adduser) document.adduser.user.focus();
	// -->
</script><?php
}

function edituser($dir){
	$user = stripslashes($GLOBALS['__POST']["user"]);
	$data = find_user($user, null);
	if($data == null)
		show_error($user . ": " . $GLOBALS["error_msg"]["miscnofinduser"]);
	if($self = ($user == $GLOBALS['__SESSION']["s_user"]))
		$dir = "";
	if(isset($GLOBALS['__POST']["confirm"]) && $GLOBALS['__POST']["confirm"] ==
		"true"
	){
		$nuser = stripslashes($GLOBALS['__POST']["nuser"]);
		if($nuser == "" || $GLOBALS['__POST']["home_dir"] == ""){
			show_error($GLOBALS["error_msg"]["miscfieldmissed"]);
		}
		if(isset($GLOBALS['__POST']["chpass"]) && $GLOBALS['__POST']["chpass"] == "true"){
			if($GLOBALS['__POST']["pass1"] != $GLOBALS['__POST']["pass2"])
				show_error($GLOBALS["error_msg"]["miscnopassmatch"]);
			$pass = md5(stripslashes($GLOBALS['__POST']["pass1"]));
		} else
			$pass = $data[1];
		if($self)
			$GLOBALS['__POST']["active"] = 1;
		$data = array($nuser, $pass, stripslashes($GLOBALS['__POST']["home_dir"]),
			stripslashes($GLOBALS['__POST']["home_url"]), $GLOBALS['__POST']["show_hidden"],
			stripslashes($GLOBALS['__POST']["no_access"]), $GLOBALS['__POST']["permissions"],
			$GLOBALS['__POST']["active"]);
		if(!update_user($user, $data))
			show_error($user . ": " . $GLOBALS["error_msg"]["saveuser"]);
		if($self)
			activate_user($nuser, null);
		header("location: " . make_link("admin", $dir, null));
		return;
	}
	show_header($GLOBALS["messages"]["actadmin"] . ": " . sprintf($GLOBALS["messages"]["miscedituser"],
		$data[0]));
	include _QUIXPLORER_PATH . "/include/js_admin3.php";
	echo "<FORM name=\"edituser\" action=\"" . make_link("admin", $dir, null) .
		"&action2=edituser\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"confirm\" value=\"true\"><input type=\"hidden\" name=\"user\" value=\"" .
		$data[0] . "\">\n";
	echo "<br/><TABLE width=\"450\">\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscusername"] . ":</td>\n";
	echo "<td align=\"right\"><input type\"text\" name=\"nuser\" size=\"30\" value=\"";
	echo $data[0] . "\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscconfpass"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"password\" name=\"pass1\" size=\"30\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscconfnewpass"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"password\" name=\"pass2\" size=\"30\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscchpass"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"checkbox\" name=\"chpass\" value=\"true\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["mischomedir"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"text\" name=\"home_dir\" size=\"30\" value=\"";
	echo $data[2] . "\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["mischomeurl"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"text\" name=\"home_url\" size=\"30\" value=\"";
	echo $data[3] . "\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscshowhidden"] . ":</td>";
	echo "<td align=\"right\"><select name=\"show_hidden\">\n";
	echo "<option value=\"0\">" . $GLOBALS["messages"]["miscyesno"][1] . "</option>";
	echo "<option value=\"1\"" . ($data[4] ? " selected " : "") . ">";
	echo $GLOBALS["messages"]["miscyesno"][0] . "</option>\n";
	echo "</select></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["mischidepattern"] . ":</td>\n";
	echo "<td align=\"right\"><input type=\"text\" name=\"no_access\" size=\"30\" value=\"";
	echo $data[5] . "\"></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscperms"] . ":</td><td align=\"right\"><select name=\"permissions\">\n";
	$permvalues = array(0, 1, 2, 3, 7);
	for($i = 0; $i < count($GLOBALS["messages"]["miscpermnames"]); ++$i){
		echo "<option value=\"" . $permvalues[$i] . "\"" . ($permvalues[$i] == $data[6] ?
			" selected " : "") . ">";
		echo $GLOBALS["messages"]["miscpermnames"][$i] . "</option>\n";
	}
	echo "</select></td></tr>\n";
	echo "<tr><td>" . $GLOBALS["messages"]["miscactive"] . ":</td>";
	echo "<td align=\"right\"><select name=\"active\"" . ($self ? " DISABLED " : "") . ">\n";
	echo "<option value=\"1\">" . $GLOBALS["messages"]["miscyesno"][0] . "</option>";
	echo "<option value=\"0\"" . ($data[7] ? "" : " selected ") . ">";
	echo $GLOBALS["messages"]["miscyesno"][1] . "</option>\n";
	echo "</select></td></tr>\n";
	echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"" . $GLOBALS["messages"]["btnsave"];
	echo "\" onclick=\"return check_pwd();\">\n<input type=\"button\" value=\"";
	echo $GLOBALS["messages"]["btncancel"] . "\" onClick=\"javascript:location='";
	echo make_link("admin", $dir, null) . "';\"></td></tr></form></table><br/>\n";
}

function removeuser($dir){
	$user = stripslashes($GLOBALS['__POST']["user"]);
	if($user == $GLOBALS['__SESSION']["s_user"])
		show_error($GLOBALS["error_msg"]["miscselfremove"]);
	if(!remove_user($user))
		show_error($user . ": " . $GLOBALS["error_msg"]["deluser"]);
	header("location: " . make_link("admin", $dir, null));
}

function show_admin($dir){
	$pwd = (($GLOBALS["permissions"] & 2) == 2);
	$admin = (($GLOBALS["permissions"] & 4) == 4);
	if(!$GLOBALS["require_login"])
		show_error($GLOBALS["error_msg"]["miscnofunc"]);
	if(!$pwd && !$admin)
		show_error($GLOBALS["error_msg"]["accessfunc"]);
	if(isset($GLOBALS['__GET']["action2"]))
		$action2 = $GLOBALS['__GET']["action2"];
	elseif(isset($GLOBALS['__POST']["action2"]))
		$action2 = $GLOBALS['__POST']["action2"];
	else
		$action2 = "";
	switch($action2){
		case "chpwd":
			changepwd($dir);
			break;
		case "adduser":
			if(!$admin)
				show_error($GLOBALS["error_msg"]["accessfunc"]);
			adduser($dir);
			break;
		case "edituser":
			if(!$admin)
				show_error($GLOBALS["error_msg"]["accessfunc"]);
			edituser($dir);
			break;
		case "rmuser":
			if(!$admin)
				show_error($GLOBALS["error_msg"]["accessfunc"]);
			removeuser($dir);
			break;
		default:
			admin($admin, $dir);
	}
}


?>
