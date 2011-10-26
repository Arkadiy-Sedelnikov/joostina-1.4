<?php
/**
* @package Joostina
* @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
* @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
* Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
* Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
* @category   Networking
* @package    FTP
* @author     Tobias Schlitt <toby@php.net>
* @copyright  1997-2005 The PHP Group
* @license    http://www.php.net/license/3_0.txt  PHP License 3.0
* @version    CVS: $ Id: FTP.php,v 1.47 2006/02/09 23:12:33 toby Exp $
* @link       http://pear.php.net/package/Net_FTP
* @since      File available since Release 0.0.1
**/
defined('_VALID_MOS') or die();
require_once dirname(__file__).'/PEAR.php';
define('NET_FTP_FILES_ONLY',0,true);
define('NET_FTP_DIRS_ONLY',1,true);
define('NET_FTP_DIRS_FILES',2,true);
define('NET_FTP_RAWLIST',3,true);
define('NET_FTP_ERR_CONNECT_FAILED',-1);
define('NET_FTP_ERR_LOGIN_FAILED',-2);
define('NET_FTP_ERR_DIRCHANGE_FAILED',2);
define('NET_FTP_ERR_DETERMINEPATH_FAILED',4);
define('NET_FTP_ERR_CREATEDIR_FAILED',-4);
define('NET_FTP_ERR_EXEC_FAILED',-5);
define('NET_FTP_ERR_SITE_FAILED',-6);
define('NET_FTP_ERR_CHMOD_FAILED',-7);
define('NET_FTP_ERR_RENAME_FAILED',-8);
define('NET_FTP_ERR_MDTMDIR_UNSUPPORTED',-9);
define('NET_FTP_ERR_MDTM_FAILED',-10);
define('NET_FTP_ERR_DATEFORMAT_FAILED',-11);
define('NET_FTP_ERR_SIZE_FAILED',-12);
define('NET_FTP_ERR_OVERWRITELOCALFILE_FORBIDDEN',-13);
define('NET_FTP_ERR_OVERWRITELOCALFILE_FAILED',-14);
define('NET_FTP_ERR_LOCALFILENOTEXIST',-15);
define('NET_FTP_ERR_OVERWRITEREMOTEFILE_FORBIDDEN',-16);
define('NET_FTP_ERR_UPLOADFILE_FAILED',-17);
define('NET_FTP_ERR_REMOTEPATHNODIR',-18);
define('NET_FTP_ERR_LOCALPATHNODIR',-19);
define('NET_FTP_ERR_CREATELOCALDIR_FAILED',-20);
define('NET_FTP_ERR_HOSTNAMENOSTRING',-21);
define('NET_FTP_ERR_PORTLESSZERO',-22);
define('NET_FTP_ERR_NOMODECONST',-23);
define('NET_FTP_ERR_TIMEOUTLESSZERO',-24);
define('NET_FTP_ERR_SETTIMEOUT_FAILED',-25);
define('NET_FTP_ERR_EXTFILENOTEXIST',-26);
define('NET_FTP_ERR_EXTFILEREAD_FAILED',-27);
define('NET_FTP_ERR_DELETEFILE_FAILED',-28);
define('NET_FTP_ERR_DELETEDIR_FAILED',-29);
define('NET_FTP_ERR_RAWDIRLIST_FAILED',-30);
define('NET_FTP_ERR_DIRLIST_UNSUPPORTED',-31);
define('NET_FTP_ERR_DISCONNECT_FAILED',-32);
define('NET_FTP_ERR_USERNOSTRING',-33);
define('NET_FTP_ERR_PASSWORDWORDNOSTRING',-33);
class Net_FTP extends PEAR {
var $_hostname;
var $_port = 21;
var $_USER;
var $_password;
var $_passv;
var $_mode = FTP_BINARY;
var $_handle;
var $_timeout = 90;
var $_file_extensions;
var $_ls_match = array('unix' => array('pattern' => '/(?:(d)|.)([rwxt-]+)\s+(\w+)\s+([\w\d-]+)\s+([\w\d-]+)\s+(\w+)\s+(\S+\s+\S+\s+\S+)\s+(.+)/',
'map' => array('is_dir' => 1,'rights' => 2,'files_inside' => 3,'user' => 4,
'group' => 5,'size' => 6,'date' => 7,'name' => 8,)),'windows' => array('pattern' =>
'/(.+)\s+(.+)\s+((<DIR>)|[0-9]+)\s+(.+)/','map' => array('name' => 5,'date' => 1,
'size' => 3,'is_dir' => 4,)));
var $_matcher = null;
var $_listeners = array();
function Net_FTP($host = null,$port = null,$timeout = 90) {
$this->PEAR();
if(isset($host)) {
$this->setHostname($host);
}
if(isset($port)) {
$this->setPort($port);
}
$this->_timeout = $timeout;
$this->_file_extensions[FTP_ASCII] = array();
$this->_file_extensions[FTP_BINARY] = array();
}
function connect($host = null,$port = null) {
$this->_matcher = null;
if(isset($host)) {
$this->setHostname($host);
}
if(isset($port)) {
$this->setPort($port);
}
$handle = @ftp_connect($this->getHostname(),$this->getPort(),$this->_timeout);
if(!$handle) {
	return $this->raiseError(_C_JXPLORER_FTP_SERVER_NOT_FOUND,NET_FTP_ERR_CONNECT_FAILED);
} else {
$this->_handle = &$handle;
return true;
}
}
function disconnect() {
$res = @ftp_close($this->_handle);
if(!$res) {
return PEAR::raiseError('Disconnect failed.',NET_FTP_ERR_DISCONNECT_FAILED);
}
return true;
}
function login($username = null,$password = null) {
if(!isset($username)) {
$username = $this->getUsername();
} else {
$this->setUsername($username);
}
if(!isset($password)) {
$password = $this->getPassword();
} else {
$this->setPassword($password);
}
$res = @ftp_login($this->_handle,$username,$password);
if(!$res) {
return $this->raiseError("Unable to login",NET_FTP_ERR_LOGIN_FAILED);
} else {
return true;
}
}
function cd($dir) {
$erg = @ftp_chdir($this->_handle,$dir);
if(!$erg) {
return $this->raiseError("Directory change failed",NET_FTP_ERR_DIRCHANGE_FAILED);
} else {
return true;
}
}
function pwd() {
$res = @ftp_pwd($this->_handle);
if(!$res) {
return $this->raiseError("Could not determine the actual path.",
NET_FTP_ERR_DETERMINEPATH_FAILED);
} else {
return $res;
}
}
function mkdir($dir,$recursive = false) {
$dir = $this->_construct_path($dir);
$savedir = $this->pwd();
$this->pushErrorHandling(PEAR_ERROR_RETURN);
$e = $this->cd($dir);
$this->popErrorHandling();
if($e === true) {
$this->cd($savedir);
return true;
}
$this->cd($savedir);
if($recursive === false) {
$res = @ftp_mkdir($this->_handle,$dir);
if(!$res) {
return $this->raiseError("Creation of '$dir' failed",
NET_FTP_ERR_CREATEDIR_FAILED);
} else {
return true;
}
} else {
if(strpos($dir,'/') === false) {
return $this->mkdir($dir,false);
}
$pos = 0;
$res = $this->mkdir(dirname($dir),true);
$res = $this->mkdir($dir,false);
if($res !== true) {
return $res;
}
return true;
}
}
function execute($command) {
$res = @ftp_exec($this->_handle,$command);
if(!$res) {
return $this->raiseError("Execution of command '$command' failed.",
NET_FTP_ERR_EXEC_FAILED);
} else {
return $res;
}
}
function site($command) {
$res = @ftp_site($this->_handle,$command);
if(!$res) {
return $this->raiseError("Execution of SITE command '$command' failed.",
NET_FTP_ERR_SITE_FAILED);
} else {
return $res;
}
}
function chmod($target,$permissions) {
if(is_array($target)) {
for($i = 0; $i < count($target); $i++) {
$res = $this->chmod($target[$i],$permissions);
if(PEAR::isError($res)) {
return $res;
}
}
} else {
$res = $this->site("CHMOD ".$permissions." ".$target);
if(!$res) {
return PEAR::raiseError("CHMOD ".$permissions." ".$target." failed",
NET_FTP_ERR_CHMOD_FAILED);
} else {
return $res;
}
}
}
function chmodRecursive($target,$permissions) {
static $dir_permissions;
if(!isset($dir_permissions)) {
$dir_permissions = $this->_makeDirPermissions($permissions);
}
if(is_array($target)) {
for($i = 0; $i < count($target); $i++) {
$res = $this->chmodRecursive($target[$i],$permissions);
if(PEAR::isError($res)) {
return $res;
}
}
} else {
$remote_path = $this->_construct_path($target);
$result = $this->chmod($remote_path,$dir_permissions);
if(PEAR::isError($result)) {
return $result;
}
if(substr($remote_path,strlen($remote_path) - 1) != "/") {
$remote_path .= "/";
}
$dir_list = array();
$mode = NET_FTP_DIRS_ONLY;
$dir_list = $this->ls($remote_path,$mode);
foreach($dir_list as $dir_entry) {
if($dir_entry == '.' || $dir_entry == '..') {
;
continue;
}
$remote_path_new = $remote_path.$dir_entry["name"]."/";
$result = $this->chmod($remote_path_new,$dir_permissions);
if(PEAR::isError($result)) {
return $result;
}
$result = $this->chmodRecursive($remote_path_new,$permissions);
if(PEAR::isError($result)) {
return $result;
}
}
$file_list = array();
$mode = NET_FTP_FILES_ONLY;
$file_list = $this->ls($remote_path,$mode);
foreach($file_list as $file_entry) {
$remote_file = $remote_path.$file_entry["name"];
$result = $this->chmod($remote_file,$permissions);
if(PEAR::isError($result)) {
return $result;
}
}
}
return true;
}
function rename($remote_from,$remote_to) {
$res = @ftp_rename($this->_handle,$remote_from,$remote_to);
if(!$res) {
return $this->raiseError("Could not rename ".$remote_from." to ".$remote_to.
" !",NET_FTP_ERR_RENAME_FAILED);
}
return true;
}
function _makeDirPermissions($permissions) {
$permissions = (string )$permissions;
for($i = 0; $i < strlen($permissions); $i++) {
if((int)$permissions{$i}&4 and !((int)$permissions{$i}&1)) {
(int)$permissions{$i} = (int)$permissions{$i} + 1;
}
}
return (string )$permissions;
}
function mdtm($file,$format = null) {
$file = $this->_construct_path($file);
if($this->_check_dir($file)) {
return $this->raiseError("Filename '$file' seems to be a directory.",
NET_FTP_ERR_MDTMDIR_UNSUPPORTED);
}
$res = @ftp_mdtm($this->_handle,$file);
if($res == -1) {
return $this->raiseError("Could not get last-modification-date of '$file'.",
NET_FTP_ERR_MDTM_FAILED);
}
if(isset($format)) {
$res = date($format,$res);
if(!$res) {
return $this->raiseError("Date-format failed on timestamp '$res'.",
NET_FTP_ERR_DATEFORMAT_FAILED);
}
}
return $res;
}
function size($file) {
$file = $this->_construct_path($file);
$res = @ftp_size($this->_handle,$file);
if($res == -1) {
return $this->raiseError("Could not determine filesize of '$file'.",
NET_FTP_ERR_SIZE_FAILED);
} else {
return $res;
}
}
function ls($dir = null,$mode = NET_FTP_DIRS_FILES) {
if(!isset($dir)) {
$dir = @ftp_pwd($this->_handle);
if(!$dir) {
return $this->raiseError("Could not retrieve current directory",
NET_FTP_ERR_DETERMINEPATH_FAILED);
}
}
if(($mode != NET_FTP_FILES_ONLY) && ($mode != NET_FTP_DIRS_ONLY) && ($mode !=
NET_FTP_RAWLIST)) {
$mode = NET_FTP_DIRS_FILES;
}
switch($mode) {
case NET_FTP_DIRS_FILES:
$res = $this->_ls_both($dir);
break;
case NET_FTP_DIRS_ONLY:
$res = $this->_ls_dirs($dir);
break;
case NET_FTP_FILES_ONLY:
$res = $this->_ls_files($dir);
break;
case NET_FTP_RAWLIST:
$res = @ftp_rawlist($this->_handle,$dir);
break;
}
return $res;
}
function rm($path,$recursive = false) {
$path = $this->_construct_path($path);
if($this->_check_dir($path)) {
if($recursive) {
return $this->_rm_dir_recursive($path);
} else {
return $this->_rm_dir($path);
}
} else {
return $this->_rm_file($path);
}
}
function get($remote_file,$local_file,$overwrite = false,$mode = null) {
if(!isset($mode)) {
$mode = $this->checkFileExtension($remote_file);
}
$remote_file = $this->_construct_path($remote_file);
if(@file_exists($local_file) && !$overwrite) {
return $this->raiseError("Local file '$local_file' exists and may not be overwriten.",
NET_FTP_ERR_OVERWRITELOCALFILE_FORBIDDEN);
}
if(@file_exists($local_file) && !@is_writeable($local_file) && $overwrite) {
return $this->raiseError("Local file '$local_file' is not writeable. Can not overwrite.",
NET_FTP_ERR_OVERWRITELOCALFILE_FAILED);
}
if(@function_exists('ftp_nb_get')) {
$res = @ftp_nb_get($this->_handle,$local_file,$remote_file,$mode);
while($res == FTP_MOREDATA) {
$this->_announce('nb_get');
$res = @ftp_nb_continue($this->_handle);
}
} else {
$res = @ftp_get($this->_handle,$local_file,$remote_file,$mode);
}
if(!$res) {
return $this->raiseError("File '$remote_file' could not be downloaded to '$local_file'.",
NET_FTP_ERR_OVERWRITELOCALFILE_FAILED);
} else {
return true;
}
}
function fget($remote_file,$local_handle,$mode = null) {
if(!isset($mode)) {
$mode = $this->checkFileExtension($remote_file);
}
$remote_file = $this->_construct_path($remote_file);
if(@function_exists('ftp_nb_fget')) {
$res = @ftp_nb_fget($this->_handle,$local_handle,$remote_file,$mode);
while($res == FTP_MOREDATA) {
$this->_announce('nb_fget');
$res = @ftp_nb_continue($this->_handle);
}
} else {
$res = @ftp_fget($this->_handle,$local_handle,$remote_file,$mode);
}
if(!$res) {
return $this->raiseError("File '$remote_file' could not be downloaded.",
NET_FTP_ERR_OVERWRITELOCALFILE_FAILED);
} else {
return true;
}
}
function put($local_file,$remote_file,$overwrite = false,$mode = null) {
if(!isset($mode)) {
$mode = $this->checkFileExtension($local_file);
}
$remote_file = $this->_construct_path($remote_file);
if(!@file_exists($local_file)) {
return $this->raiseError("Local file '$local_file' does not exist.",
NET_FTP_ERR_LOCALFILENOTEXIST);
}
if((@ftp_size($this->_handle,$remote_file) != -1) && !$overwrite) {
return $this->raiseError("Remote file '$remote_file' exists and may not be overwriten.",
NET_FTP_ERR_OVERWRITEREMOTEFILE_FORBIDDEN);
}
if(function_exists('ftp_nb_put')) {
$res = @ftp_nb_put($this->_handle,$remote_file,$local_file,$mode);
while($res == FTP_MOREDATA) {
$this->_announce('nb_put');
$res = @ftp_nb_continue($this->_handle);
}
} else {
$res = @ftp_put($this->_handle,$remote_file,$local_file,$mode);
}
if(!$res) {
return $this->raiseError("File '$local_file' could not be uploaded to '$remote_file'.",
NET_FTP_ERR_UPLOADFILE_FAILED);
} else {
return true;
}
}
function fput($local_handle,$remote_file,$overwrite = false,$mode = null) {
if(!isset($mode)) {
$mode = FTP_BINARY;
}
$remote_file = $this->_construct_path($remote_file);
if((@ftp_size($this->_handle,$remote_file) != -1) && !$overwrite) {
return $this->raiseError("Remote file '$remote_file' exists and may not be overwriten.",
NET_FTP_ERR_OVERWRITEREMOTEFILE_FORBIDDEN);
}
if(function_exists('ftp_nb_fput')) {
$res = @ftp_nb_fput($this->_handle,$remote_file,$local_handle,$mode);
while($res == FTP_MOREDATA) {
$this->_announce('nb_put');
$res = @ftp_nb_continue($this->_handle);
}
} else {
$res = @ftp_fput($this->_handle,$remote_file,$local_handle,$mode);
}
if(!$res) {
return $this->raiseError("The File could not be uploaded to '$remote_file'.",
NET_FTP_ERR_UPLOADFILE_FAILED);
} else {
return true;
}
}
function getRecursive($remote_path,$local_path,$overwrite = false,$mode = null) {
$remote_path = $this->_construct_path($remote_path);
if(!$this->_check_dir($remote_path)) {
return $this->raiseError("Given remote-path '$remote_path' seems not to be a directory.",
NET_FTP_ERR_REMOTEPATHNODIR);
}
if(!$this->_check_dir($local_path)) {
return $this->raiseError("Given local-path '$local_path' seems not to be a directory.",
NET_FTP_ERR_LOCALPATHNODIR);
}
if(!@is_dir($local_path)) {
$res = @mkdir($local_path);
if(!$res) {
return $this->raiseError("Could not create dir '$local_path'",
NET_FTP_ERR_CREATELOCALDIR_FAILED);
}
}
$dir_list = array();
$dir_list = $this->ls($remote_path,NET_FTP_DIRS_ONLY);
foreach($dir_list as $dir_entry) {
if($dir_entry['name'] != '.' && $dir_entry['name'] != '..') {
$remote_path_new = $remote_path.$dir_entry["name"]."/";
$local_path_new = $local_path.$dir_entry["name"]."/";
$result = $this->getRecursive($remote_path_new,$local_path_new,$overwrite,$mode);
if($this->isError($result)) {
return $result;
}
}
}
$file_list = array();
$file_list = $this->ls($remote_path,NET_FTP_FILES_ONLY);
foreach($file_list as $file_entry) {
$remote_file = $remote_path.$file_entry["name"];
$local_file = $local_path.$file_entry["name"];
$result = $this->get($remote_file,$local_file,$overwrite,$mode);
if($this->isError($result)) {
return $result;
}
}
return true;
}
function putRecursive($local_path,$remote_path,$overwrite = false,$mode = null) {
$remote_path = $this->_construct_path($remote_path);
if(!$this->_check_dir($local_path) || !is_dir($local_path)) {
return $this->raiseError("Given local-path '$local_path' seems not to be a directory.",
NET_FTP_ERR_LOCALPATHNODIR);
}
if(!$this->_check_dir($remote_path)) {
return $this->raiseError("Given remote-path '$remote_path' seems not to be a directory.",
NET_FTP_ERR_REMOTEPATHNODIR);
}
$old_path = $this->pwd();
if($this->isError($this->cd($remote_path))) {
$res = $this->mkdir($remote_path);
if($this->isError($res)) {
return $res;
}
}
$this->cd($old_path);
$dir_list = $this->_ls_local($local_path);
foreach($dir_list["dirs"] as $dir_entry) {
$remote_path_new = $remote_path.$dir_entry."/";
$local_path_new = $local_path.$dir_entry."/";
$result = $this->putRecursive($local_path_new,$remote_path_new,$overwrite,$mode);
if($this->isError($result)) {
return $result;
}
}
foreach($dir_list["files"] as $file_entry) {
$remote_file = $remote_path.$file_entry;
$local_file = $local_path.$file_entry;
$result = $this->put($local_file,$remote_file,$overwrite,$mode);
if($this->isError($result)) {
return $result;
}
}
return true;
}
function checkFileExtension($filename) {
$pattern = "/\.(.*)$/";
$has_extension = preg_match($pattern,$filename,$eregs);
if(!$has_extension) {
return $this->_mode;
} else {
$ext = $eregs[1];
}
if(!empty($this->_file_extensions[$ext])) {
return $this->_file_extensions[$ext];
}
return $this->_mode;
}
function setHostname($host) {
if(!is_string($host)) {
return PEAR::raiseError("Hostname must be a string.",
NET_FTP_ERR_HOSTNAMENOSTRING);
}
$this->_hostname = $host;
return true;
}
function setPort($port) {
if(!is_int($port) || ($port < 0)) {
PEAR::raiseError("Invalid port. Has to be integer >= 0",
NET_FTP_ERR_PORTLESSZERO);
}
$this->_port = $port;
return true;
}
function setUsername($user) {
if(empty($user) || !is_string($user)) {
return PEAR::raiseError('Username $user invalid.',NET_FTP_ERR_USERNOSTRING);
}
$this->_USER = $user;
}
function setPassword($password) {
if(empty($password) || !is_string($password)) {
return PEAR::raiseError('Password xxx invalid.',NET_FTP_ERR_PASSWORDWORDNOSTRING);
}
$this->_password = $password;
}
function setMode($mode) {
if(($mode == FTP_ASCII) || ($mode == FTP_BINARY)) {
$this->_mode = $mode;
return true;
} else {
return $this->raiseError('FTP-Mode has either to be FTP_ASCII or FTP_BINARY',
NET_FTP_ERR_NOMODECONST);
}
}
function setPassive() {
$this->_passv = true;
@ftp_pasv($this->_handle,true);
}
function setActive() {
$this->_passv = false;
@ftp_pasv($this->_handle,false);
}
function setTimeout($timeout = 0) {
if(!is_int($timeout) || ($timeout < 0)) {
return PEAR::raiseError("Timeout $timeout is invalid, has to be an integer >= 0",
NET_FTP_ERR_TIMEOUTLESSZERO);
}
$this->_timeout = $timeout;
if(isset($this->_handle) && is_resource($this->_handle)) {
$res = @ftp_set_option($this->_handle,FTP_TIMEOUT_SEC,$timeout);
} else {
$res = true;
}
if(!$res) {
return PEAR::raiseError("Set timeout failed.",NET_FTP_ERR_SETTIMEOUT_FAILED);
}
return true;
}
function addExtension($mode,$ext) {
$this->_file_extensions[$ext] = $mode;
}
function removeExtension($ext) {
unset($this->_file_extensions[$ext]);
}
function getExtensionsFile($filename) {
if(!file_exists($filename)) {
return $this->raiseError("Extensions-file '$filename' does not exist",
NET_FTP_ERR_EXTFILENOTEXIST);
}
if(!is_readable($filename)) {
return $this->raiseError("Extensions-file '$filename' is not readable",
NET_FTP_ERR_EXTFILEREAD_FAILED);
}
$this->_file_extension = @parse_ini_file($filename);
return true;
}
function getHostname() {
return $this->_hostname;
}
function getPort() {
return $this->_port;
}
function getUsername() {
return $this->_USER;
}
function getPassword() {
return $this->_password;
}
function getMode() {
return $this->_mode;
}
function isPassive() {
return $this->_passv;
}
function getExtensionMode($ext) {
return @$this->_file_extensions[$ext];
}
function getTimeout() {
return ftp_get_option($this->_handle,FTP_TIMEOUT_SEC);
}
function attach(&$observer) {
if(!($observer instanceof Net_FTP_Observer)) {
return false;
}
$this->_listeners[$observer->getId()] = &$observer;
return true;
}
function detach($observer) {
if(!($observer instanceof Net_FTP_Observer) || !isset($this->_listeners[$observer->getId
()])) {
return false;
}
unset($this->_listeners[$observer->getId()]);
return true;
}
function _announce($event) {
foreach($this->_listeners as $id => $listener) {
$this->_listeners[$id]->notify($event);
}
}
function _construct_path($path) {
if((substr($path,0,1) != "/") && (substr($path,0,2) != "./")) {
$actual_dir = @ftp_pwd($this->_handle);
if(substr($actual_dir,(strlen($actual_dir) - 2),1) != "/") {
$actual_dir .= "/";
}
$path = $actual_dir.$path;
}
return $path;
}
function _check_dir($path) {
if(!empty($path) && substr($path,(strlen($path) - 1),1) == "/") {
return true;
} else {
return false;
}
}
function _rm_file($file) {
if(substr($file,0,1) != "/") {
$actual_dir = @ftp_pwd($this->_handle);
if(substr($actual_dir,(strlen($actual_dir) - 2),1) != "/") {
$actual_dir .= "/";
}
$file = $actual_dir.$file;
}
$res = @ftp_delete($this->_handle,$file);
if(!$res) {
return $this->raiseError("Could not delete file '$file'.",
NET_FTP_ERR_DELETEFILE_FAILED);
} else {
return true;
}
}
function _rm_dir($dir) {
if(substr($dir,(strlen($dir) - 1),1) != "/") {
return $this->raiseError("Directory name '$dir' is invalid, has to end with '/'",
NET_FTP_ERR_REMOTEPATHNODIR);
}
$res = @ftp_rmdir($this->_handle,$dir);
if(!$res) {
return $this->raiseError("Could not delete directory '$dir'.",
NET_FTP_ERR_DELETEDIR_FAILED);
} else {
return true;
}
}
function _rm_dir_recursive($dir) {
if(substr($dir,(strlen($dir) - 1),1) != "/") {
return $this->raiseError("Directory name '$dir' is invalid, has to end with '/'",
NET_FTP_ERR_REMOTEPATHNODIR);
}
$file_list = $this->_ls_files($dir);
foreach($file_list as $file) {
$file = $dir.$file["name"];
$res = $this->rm($file);
if($this->isError($res)) {
return $res;
}
}
$dir_list = $this->_ls_dirs($dir);
foreach($dir_list as $new_dir) {
if($new_dir == '.' || $new_dir == '..') {
continue;
}
$new_dir = $dir.$new_dir["name"]."/";
$res = $this->_rm_dir_recursive($new_dir);
if($this->isError($res)) {
return $res;
}
}
$res = $this->_rm_dir($dir);
if(PEAR::isError($res)) {
return $res;
} else {
return true;
}
}
function _ls_both($dir) {
$list_splitted = $this->_list_and_parse($dir);
if(PEAR::isError($list_splitted)) {
return $list_splitted;
}
if(!is_array($list_splitted["files"])) {
$list_splitted["files"] = array();
}
if(!is_array($list_splitted["dirs"])) {
$list_splitted["dirs"] = array();
}
$res = array();
@array_splice($res,0,0,$list_splitted["files"]);
@array_splice($res,0,0,$list_splitted["dirs"]);
return $res;
}
function _ls_dirs($dir) {
$list = $this->_list_and_parse($dir);
if(PEAR::isError($list)) {
return $list;
}
return $list["dirs"];
}
function _ls_files($dir) {
$list = $this->_list_and_parse($dir);
if(PEAR::isError($list)) {
return $list;
}
return $list["files"];
}
function _list_and_parse($dir) {
$dirs_list = array();
$files_list = array();
$dir_list = @ftp_rawlist($this->_handle,$dir);
if(!is_array($dir_list)) {
return PEAR::raiseError('Could not get raw directory listing.',
NET_FTP_ERR_RAWDIRLIST_FAILED);
}
if(count($dir_list) == 0) {
return array('dirs' => $dirs_list,'files' => $files_list);
}
if(count($dirs_list) == 1 && $dirs_list[0] == 'total 0') {
return array('dirs' => array(),'files' => $files_list);
}
if(!isset($this->_matcher) || PEAR::isError($this->_matcher)) {
$this->_matcher = $this->_determine_os_match($dir_list);
if(PEAR::isError($this->_matcher)) {
return $this->_matcher;
}
}
foreach($dir_list as $entry) {
if(!preg_match($this->_matcher['pattern'],$entry,$m)) {
continue;
}
$entry = array();
foreach($this->_matcher['map'] as $key => $val) {
$entry[$key] = $m[$val];
}
$entry['stamp'] = $this->_parse_Date($entry['date']);
if($entry['is_dir']) {
$dirs_list[] = $entry;
} else {
$files_list[] = $entry;
}
}
@usort($dirs_list,array("Net_FTP","_nat_sort"));
@usort($files_list,array("Net_FTP","_nat_sort"));
$res["dirs"] = (is_array($dirs_list))?$dirs_list:array();
$res["files"] = (is_array($files_list))?$files_list:array();
return $res;
}
function _determine_os_match(&$dir_list) {
foreach($dir_list as $entry) {
foreach($this->_ls_match as $os => $match) {
if(preg_match($match['pattern'],$entry)) {
return $match;
}
}
}
$error = 'The list style of your server seems not to be supported. Please email a "$ftp->ls(NET_FTP_RAWLIST);" output plus info on the server to the maintainer of this package to get it supported! Thanks for your help!';
return PEAR::raiseError($error,NET_FTP_ERR_DIRLIST_UNSUPPORTED);
}
function _ls_local($dir_path) {
$dir = dir($dir_path);
$dir_list = array();
$file_list = array();
while(false !== ($entry = $dir->read())) {
if(($entry != '.') && ($entry != '..')) {
if(is_dir($dir_path.$entry)) {
$dir_list[] = $entry;
} else {
$file_list[] = $entry;
}
}
}
$dir->close();
$res['dirs'] = $dir_list;
$res['files'] = $file_list;
return $res;
}
function _nat_sort($item_1,$item_2) {
return strnatcmp($item_1['name'],$item_2['name']);
}
function _parse_Date($date) {
if(preg_match('/([A-Za-z]+)[ ]+([0-9]+)[ ]+([0-9]+):([0-9]+)/',$date,$res)) {
$year = date('Y');
$month = $res[1];
$day = $res[2];
$hour = $res[3];
$minute = $res[4];
$date = "$month $day, $year $hour:$minute";
$tmpDate = strtotime($date);
if($tmpDate > time()) {
$year--;
$date = "$month $day, $year $hour:$minute";
}
} elseif(preg_match('/^\d\d-\d\d-\d\d/',$date)) {
$date = str_replace('-','/',$date);
}
$res = strtotime($date);
if(!$res) {
return $this->raiseError('Dateconversion failed.',NET_FTP_ERR_DATEFORMAT_FAILED);
}
return $res;
}
}
?>
