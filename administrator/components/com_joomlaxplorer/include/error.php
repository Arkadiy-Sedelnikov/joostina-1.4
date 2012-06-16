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
function add_message($message, $type = 'general'){
	$_SESSION['jx_message'][$type][] = $message;
}

function empty_messages(){
	$_SESSION['jx_message'] = array();
}

function count_messages(){
	$count = 0;
	if(empty($_SESSION['jx_message'])){
		return 0;
	}
	foreach($_SESSION['jx_message'] as $type){
		if(!empty($type) && is_array($type)){
			$count += sizeof($type);
		}
	}
	return $count;
}

function add_error($error, $type = 'general'){
	$_SESSION['jx_error'][$type][] = $error;
}

function empty_errors(){
	$_SESSION['jx_error'] = array();
}

function count_errors(){
	$count = 0;
	if(empty($_SESSION['jx_error'])){
		return 0;
	}
	foreach($_SESSION['jx_error'] as $type){
		if(!empty($type) && is_array($type)){
			$count += sizeof($type);
		}
	}
	return $count;
}

function show_error($error, $extra = null){
	$msg = $error;
	if($extra != null){
		$msg .= " - " . $extra;
	}
	add_error($msg);
	if(empty($_GET['error'])){
		session_write_close();
		mosRedirect(make_link("show_error", $GLOBALS["dir"]) . '&error=1&extra=' . urlencode($extra));
	} else{
		show_header($GLOBALS["error_msg"]["error"]);
		$errors = count_errors();
		$messages = count_messages();

		$mmes = isset($GLOBALS["error_msg"]["message"]) ? $GLOBALS["error_msg"]["message"] : '';

		echo '<div class="quote">';
		if($errors){
			echo '<a href="#errors">' . $errors . ' ' . $GLOBALS["error_msg"]["error"] . '</a><br />';
		}
		if($messages){
			echo '<a href="#messages">' . $messages . ' ' . $mmes . '</a><br />';
		}
		echo "</div>\n";
		if(!empty($_SESSION['jx_message'])){
			echo "<a href=\"" . str_replace('&dir=', '&ignore=', make_link("list", '')) . "\">[ " .
				$GLOBALS["error_msg"]["back"] . " ]</a>";
			echo "<div class=\"jx_message\"><a name=\"messages\"></a><h3>" . $mmes . ":</strong>" . "</h3>\n";
			foreach($_SESSION['jx_message'] as $msgtype){
				foreach($msgtype as $message){
					echo $message . "\n<br/>";
				}
				echo '<br /><hr /><br />';
			}
			empty_messages();
			if(!empty($_REQUEST['extra']))
				echo " - " . urldecode($_REQUEST['extra']);
			echo "</div>\n";
		}
		echo "<div class=\"jx_error\"><a name=\"errors\"></a><h3>" . $GLOBALS["error_msg"]["error"] . ":</strong>" . "</h3>\n";
		foreach($_SESSION['jx_error'] as $errortype){
			foreach($errortype as $error){
				echo $error . "\n<br/>";
			}
			echo '<br /><hr /><br />';
		}
		empty_errors();
		echo "<a href=\"" . str_replace('&dir=', '&ignore=', make_link("list", '')) . "\">" .
			$GLOBALS["error_msg"]["back"] . "</a>";
		if(!empty($_REQUEST['extra']))
			echo " - " . urldecode($_REQUEST['extra']);
		echo "</div>\n";
	}
}