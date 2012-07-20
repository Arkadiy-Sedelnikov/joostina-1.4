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
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id:Observer.php 13 2007-05-13 07:10:43Z soeren $
 * @link       http://pear.php.net/package/Net_FTP
 * @since      File available since Release 0.0.1
 **/

defined('_JLINDEX') or die();
define('FTP_ASCII', 0);
define('FTP_TEXT', 0);
define('FTP_BINARY', 1);
define('FTP_IMAGE', 1);
define('FTP_TIMEOUT_SEC', 0);
function &ftp_connect($host, $port = 21, $timeout = 90){
	$false = false;
	if(!is_string($host) || !is_integer($port) || !is_integer($timeout)){
		return $false;
	}
	$control = @fsockopen($host, $port, $iError, $sError, $timeout);
	$GLOBALS['_NET_FTP']['timeout'] = $timeout;
	if(!is_resource($control)){
		return $false;
	}
	stream_set_blocking($control, true);
	stream_set_timeout($control, $timeout);
	do{
		$content[] = fgets($control, 8129);
		$array = socket_get_status($control);
	} while($array['unread_bytes'] > 0);
	if(substr($content[count($content) - 1], 0, 3) == 220){
		return $control;
	}
	return $false;
}

function ftp_login(&$control, $username, $password){
	if(!is_resource($control) || is_null($username)){
		return false;
	}
	fputs($control, 'USER ' . $username . "\r\n");
	$contents = array();
	do{
		$contents[] = fgets($control, 8192);
		$array = socket_get_status($control);
	} while($array['unread_bytes'] > 0);
	if(substr($contents[count($contents) - 1], 0, 3) != 331){
		return false;
	}
	fputs($control, 'PASS ' . $password . "\r\n");
	$contents = array();
	do{
		$contents[] = fgets($control, 8192);
		$array = socket_get_status($control);
	} while($array['unread_bytes']);
	if(substr($contents[count($contents) - 1], 0, 3) == 230){
		return true;
	}
	trigger_error('ftp_login() [<a
            href="function.ftp-login">function.ftp-login</a>]: ' . $contents[count
	($contents) - 1], E_USER_WARNING);
	return false;
}

function ftp_quit(&$control){
	if(!is_resource($control)){
		return false;
	}
	fputs($control, 'QUIT' . "\r\n");
	fclose($control);
	$control = null;
	return true;
}

function ftp_close(&$control){
	return ftp_quit($control);
}

function ftp_pwd(&$control){
	if(!is_resource($control)){
		return $control;
	}
	fputs($control, 'PWD' . "\r\n");
	$content = array();
	do{
		$content[] = fgets($control, 8192);
		$array = socket_get_status($control);
	} while($array['unread_bytes'] > 0);
	if(substr($cont = $content[count($content) - 1], 0, 3) == 257){
		$pos = strpos($cont, '"') + 1;
		$pos2 = strrpos($cont, '"') - $pos;
		$path = substr($cont, $pos, $pos2);
		return $path;
	}
	return false;
}

function ftp_chdir(&$control, $pwd){
	if(!is_resource($control) || !is_string($pwd)){
		return false;
	}
	fputs($control, 'CWD ' . $pwd . "\r\n");
	$content = array();
	do{
		$content[] = fgets($control, 8192);
		$array = socket_get_status($control);
	} while($array['unread_bytes'] > 0);
	if(substr($content[count($content) - 1], 0, 3) == 250){
		return true;
	}
	trigger_error('ftp_chdir() [<a
            href="function.ftp-chdir">function.ftp-chdir</a>]:
                ' . $content[count($content) - 1], E_USER_WARNING);
	return false;
}

$_NET_FTP = array();
$_NET_FTP['USE_PASSWORDIVE'] = false;
$_NET_FTP['DATA'] = null;
function ftp_pasv(&$control, $pasv){
	if(!is_resource($control) || !is_bool($pasv)){
		return false;
	}
	if(isset($GLOBALS['_NET_FTP']['DATA'])){
		fclose($GLOBALS['_NET_FTP']['DATA']);
		$GLOBALS['_NET_FTP']['DATA'] = null;
		do{
			fgets($control, 16);
			$array = socket_get_status($control);
		} while($array['unread_bytes'] > 0);
	}
	if(!$pasv){
		$GLOBALS['_NET_FTP']['USE_PASSWORDIVE'] = false;
		$low = rand(39, 250);
		$high = rand(39, 250);
		$port = ($low << 8) + $high;
		$ip = str_replace('.', ',', $_SERVER['SERVER_ADDR']);
		$s = $ip . ',' . $low . ',' . $high;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(is_resource($socket)){
			if(socket_bind($socket, '0.0.0.0', $port)){
				if(socket_listen($socket)){
					$GLOBALS['_NET_FTP']['DATA'] = &$socket;
					fputs($control, 'PORT ' . $s . "\r\n");
					$line = fgets($control, 512);
					if(substr($line, 0, 3) == 200){
						return true;
					}
				}
			}
		}
		return false;
	}
	$i = fputs($control, 'PASV' . "\r\n");
	$content = array();
	do{
		$content[] = fgets($control, 128);
		$array = socket_get_status($control);
	} while($array['unread_bytes']);
	if(substr($cont = $content[count($content) - 1], 0, 3) != 227){
		return false;
	}
	$pos = strpos($cont, '(') + 1;
	$pos2 = strrpos($cont, ')') - $pos;
	$string = substr($cont, $pos, $pos2);
	$array = explode(',', $string);
	$ip = $array[0] . '.' . $array[1] . '.' . $array[2] . '.' . $array[3];
	$port = ($array[4] << 8) + $array[5];
	$data = fsockopen($ip, $port, $iError, $sError, $GLOBALS['_NET_FTP']['timeout']);
	if(is_resource($data)){
		$GLOBALS['_NET_FTP']['USE_PASSWORDIVE'] = true;
		$GLOBALS['_NET_FTP']['DATA'] = &$data;
		stream_set_blocking($data, true);
		stream_set_timeout($data, $GLOBALS['_NET_FTP']['timeout']);
		return true;
	}
	return false;
}

function ftp_rawlist(&$control, $pwd, $recursive = false){
	if(!is_resource($control) || !is_string($pwd)){
		return false;
	}
	if(!isset($GLOBALS['_NET_FTP']['DATA']) || !is_resource($GLOBALS['_NET_FTP']['DATA'])){
		ftp_pasv($control, $GLOBALS['_NET_FTP']['USE_PASSWORDIVE']);
	}
	fputs($control, 'LIST ' . $pwd . "\r\n");
	$msg = fgets($control, 512);
	if(substr($msg, 0, 3) == 425){
		return false;
	}
	$data = &$GLOBALS['_NET_FTP']['DATA'];
	if(!$GLOBALS['_NET_FTP']['USE_PASSWORDIVE']){
		$data = &socket_accept($data);
	}
	$content = array();
	switch($GLOBALS['_NET_FTP']['USE_PASSWORDIVE']){
		case true:
			while(true){
				$string = rtrim(fgets($data, 1024));
				if($string == ''){
					break;
				}
				$content[] = $string;
			}
			fclose($data);
			break;
		case false:
			$string = socket_read($data, 1024, PHP_BINARY_READ);
			$content = explode("\n", $string);
			unset($content[count($content) - 1]);
			socket_close($GLOBALS['_NET_FTP']['DATA']);
			socket_close($data);
			break;
	}
	$data = $GLOBALS['_NET_FTP']['DATA'] = null;
	$f = fgets($control, 1024);
	return $content;
}

function ftp_systype(&$control){
	if(!is_resource($control)){
		return false;
	}
	fputs($control, 'SYST' . "\r\n");
	$line = fgets($control, 256);
	if(substr($line, 0, 3) != 215){
		return false;
	}
	$os = substr($line, 4, strpos($line, ' ', 4) - 4);
	return $os;
}

function ftp_alloc(&$control, $int, &$msg){
	if(!is_resource($control) || !is_integer($int)){
		return false;
	}
	fputs($control, 'ALLO ' . $int . ' R ' . $int . "\r\n");
	$msg = rtrim(fgets($control, 256));
	$code = substr($msg, 0, 3);
	if($code == 200 || $code == 202){
		return true;
	}
	return false;
}

function ftp_put(&$control, $remote, $local, $mode, $pos = 0){
	if(!is_resource($control) || !is_readable($local) || !is_integer($mode) || !
	is_integer($pos)
	){
		return false;
	}
	$types = array(0 => 'A', 1 => 'I');
	$windows = array(0 => 't', 1 => 'b');
	if(!isset($GLOBALS['_NET_FTP']['DATA']) || !is_resource($GLOBALS['_NET_FTP']['DATA'])){
		ftp_pasv($control, $GLOBALS['_NET_FTP']['USE_PASSWORDIVE']);
	}
	$data = &$GLOBALS['_NET_FTP']['DATA'];
	fputs($control, 'TYPE ' . $types[$mode] . "\r\n");
	$line = fgets($control, 256);
	if(substr($line, 0, 3) != 200){
		return false;
	}
	fputs($control, 'STOR ' . $remote . "\r\n");
	sleep(1);
	$line = fgets($control, 256);
	if(substr($line, 0, 3) != 150){
		return false;
	}
	$fp = fopen($local, 'r' . $windows[$mode]);
	if(!is_resource($fp)){
		$fp = null;
		return false;
	}
	$i = 0;
	switch($GLOBALS['_NET_FTP']['USE_PASSWORDIVE']){
		case false:
			$data = &socket_accept($data);
			while(!feof($fp)){
				$i += socket_write($data, fread($fp, 10240), 10240);
			}
			socket_close($data);
			break;
		case true:
			while(!feof($fp)){
				$i += fputs($data, fread($fp, 10240), 10240);
			}
			fclose($data);
			break;
	}
	$data = null;
	do{
		$line = fgets($control, 256);
	} while(substr($line, 0, 4) != "226 ");
	return true;
}

function ftp_get(&$control, $local, $remote, $mode, $resume = 0){
	if(!is_resource($control) || !is_writable(dirname($local)) || !is_integer($mode) ||
		!is_integer($resume)
	){
		return false;
	}
	$types = array(0 => 'A', 1 => 'I');
	$windows = array(0 => 't', 1 => 'b');
	if(!isset($GLOBALS['_NET_FTP']['DATA']) || !is_resource($GLOBALS['_NET_FTP']['DATA'])){
		ftp_pasv($control, $GLOBALS['_NET_FTP']['USE_PASSWORDIVE']);
	}
	$data = &$GLOBALS['NET_FTP']['DATA'];
	fputs($control, 'TYPE ' . $types[$mode] . "\r\n");
	$line = fgets($control, 256);
	if(substr($line, 0, 3) != 200){
		return false;
	}
	$fp = fopen($local, 'w' . $windows[$mode]);
	if(!is_resource($fp)){
		$fp = null;
		return false;
	}
}

function ftp_cdup(&$control){
	fputs($control, 'CDUP' . "\r\n");
	$line = fgets($control, 256);
	if(substr($line, 0, 3) != 250){
		return false;
	}
	return true;
}

function ftp_chmod(&$control, $mode, $file){
	if(!is_resource($control) || !is_integer($mode) || !is_string($file)){
		return false;
	}
	fputs($control, 'SITE CHMOD ' . $mode . ' ' . $file . "\r\n");
	$line = fgets($control, 256);
	if(substr($line, 0, 3) == 200){
		return $mode;
	}
	trigger_error('ftp_chmod() [<a
            href="function.ftp-chmod">function.ftp-chmod</a>]: ' . rtrim($line),
		E_USER_WARNING);
	return false;
}

function ftp_delete(&$control, $path){
	if(!is_resource($control) || !is_string($path)){
		return false;
	}
	fputs($control, 'DELE ' . $path . "\r\n");
	$line = fgets($control, 256);
	if(substr($line, 0, 3) == 250){
		return true;
	}
	return false;
}

function ftp_exec(&$control, $cmd){
	if(!is_resource($control) || !is_string($cmd)){
		return false;
	}
	fputs($control, 'SITE EXEC ' . $cmd . "\r\n");
	$line = fgets($control, 256);
	if(substr($line, 0, 3) == 200){
		return true;
	}
	return false;
}

?>
