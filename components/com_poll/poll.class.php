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
 * @subpackage Polls
 */
class mosPoll extends mosDBTable {
	/**
	 @var int Primary key*/
	var $id = null;
	/**
	 @var string*/
	var $title = null;
	/**
	 @var string*/
	var $checked_out = null;
	/**
	 @var time*/
	var $checked_out_time = null;
	/**
	 @var boolean*/
	var $published = null;
	/**
	 @var int*/
	var $access = null;
	/**
	 @var int*/
	var $lag = null;

	/**
	 * @param database A database connector object
	 */
	function mosPoll(&$db) {
		$this->mosDBTable('#__polls','id',$db);
	}

	// overloaded check function
	function check() {
		// check for valid name
		if(trim($this->title) == '') {
			$this->_error = _ENTER_POLL_NAME;
			return false;
		}
		// check for valid lag
		$this->lag = intval($this->lag);
		if($this->lag == 0) {
			$this->_error = _ENTER_POLL_LAG;
			return false;
		}
		// check for existing title
		$query = "SELECT id FROM #__polls WHERE title = ".$this->_db->Quote($this->title);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if($xid && $xid != intval($this->id)) {
			$this->_error = _MODULE_WITH_THIS_NAME_ALREADY_EDISTS;
			return false;
		}

		return true;
	}

	// overloaded delete function
	function delete($oid = null) {
		$k = $this->_tbl_key;
		if($oid) {
			$this->$k = intval($oid);
		}

		if(mosDBTable::delete($oid)) {
			$query = "DELETE FROM #__poll_data WHERE pollid = ".(int)$this->$k;
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				$this->_error .= $this->_db->getErrorMsg()."\n";
			}

			$query = "DELETE FROM #__poll_date WHERE poll_id = ".(int)$this->$k;
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				$this->_error .= $this->_db->getErrorMsg()."\n";
			}

			$query = "DELETE from #__poll_menu WHERE pollid = ".(int)$this->$k;
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				$this->_error .= $this->_db->getErrorMsg()."\n";
			}

			return true;
		} else {
			return false;
		}
	}
}