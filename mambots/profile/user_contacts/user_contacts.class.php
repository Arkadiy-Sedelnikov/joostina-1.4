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

class UserContactsEmail{

	var $pretext = '';
	var $posttext = '';
	var $label_position = 1;
	var $from = '';
	var $fromname = '';
	var $recipient = '';
	var $subject = '';
	var $message = '';

	var $_error = '';

	function UserContactsEmail(){
		$this->subject = BOT_USER_CONTACTS_MESSAGE_FROM . Jconfig::getInstance()->config_sitename;
	}

	function clean_message($text){
		$text = preg_replace("'<script[^>]*>.*?</script>'si", "", $text);
		$text = preg_replace("'<?php*.*?>'", "", $text);
		$text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);

		return $text;
	}

	function send_message(){

		if(preg_match("/[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]/i", $this->fromname) || strlen($this->fromname) < 3){
			$this->_error = BOT_USER_CONTACTS_CHECK_NAME;
			return false;
		}

		if((trim($this->from == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->from) == false)){
			$this->_error = BOT_USER_CONTACTS_CHECK_EMAIL;
			return false;
		}

		if(strlen($this->fromname) > 35){
			$this->fromname = substr($this->fromname, 0, 35);
		}
		if(mosMail($this->from, $this->fromname, $this->recipient, $this->subject, $this->message)){
			return true;
		} else{
			$this->_error = BOT_USER_CONTACTS_SYSTEM_ERROR;
			return false;
		}

	}
}