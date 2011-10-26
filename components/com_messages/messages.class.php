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
 * @package Joostina
 * @subpackage Messages
 */
class mosMessage extends mosDBTable {
	/**
	 @var int Primary key*/
	var $message_id = null;
	/**
	 @var int*/
	var $user_id_from = null;
	/**
	 @var int*/
	var $user_id_to = null;
	/**
	 @var int*/
	var $folder_id = null;
	/**
	 @var datetime*/
	var $date_time = null;
	/**
	 @var int*/
	var $state = null;
	/**
	 @var int*/
	var $priority = null;
	/**
	 @var string*/
	var $subject = null;
	/**
	 @var text*/
	var $message = null;

	/**
	 * @param database A database connector object
	 */
	function mosMessage(&$db) {
		$this->mosDBTable('#__messages','message_id',$db);
	}

	/**
	 * Validation and filtering
	 */
	function check() {
		// filter malicious code
		$this->filter();

		return true;
	}

	function send($from_id = null,$to_id = null,$subject = null,$message = null) {
		$database = database::getInstance();
		$config = Jconfig::getInstance();

		if(is_object($this)) {
			$from_id = $from_id?$from_id:$this->user_id_from;
			$to_id = $to_id?$to_id:$this->user_id_to;
			$subject = $subject?$subject:$this->subject;
			$message = $message?$message:$this->message;
		}

		$query = "SELECT cfg_name, cfg_value FROM #__messages_cfg WHERE user_id = ".(int)$to_id;
		$database->setQuery($query);
		$config = $database->loadObjectList('cfg_name');
		$locked = @$config['lock']->cfg_value;
		$domail = @$config['mail_on_new']->cfg_value;

		if(!$locked) {

			$this->user_id_from = $from_id;
			$this->user_id_to = $to_id;
			$this->subject = $subject;
			$this->message = $message;
			$this->date_time = date('Y-m-d H:i:s');

			if($this->store()) {
				if($domail) {
					$query = "SELECT email FROM #__users WHERE id = ".(int)$to_id;
					$database->setQuery($query);
					$recipient = $database->loadResult();
					$subject = _NEW_MESSAGE;
					$msg = _NEW_MESSAGE;

					mosMail($config->config_mailfrom,$config->config_fromname,$recipient,$subject,$msg);
				}
				return true;
			}
		} else {
			if(is_object($this)) {
				$this->_error = _MESSAGE_FAILED;
			}
		}
		return false;
	}
}