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

//
// Classes and helper functions to the banner system
//
class mosArtBannerClient extends mosDBTable{
	var $cid = null;
	var $name = '';
	var $contact = '';
	var $email = '';
	var $extrainfo = '';
	var $published = 0;
	var $checked_out = 0;
	var $checked_out_time = 0;

	function mosArtBannerClient($db){
		$this->mosDBTable('#__banners_clients', 'cid', $db);
	}

	function check(){
		// check for valid client name
		if(trim($this->name == "")){
			$this->_error = _BNR_CLIENT_NAME;
			return false;
		}

		// check for valid client contact
		if(trim($this->contact == "")){
			$this->_error = _ABP_CL_MSCF;
			return false;
		}

		// check for valid client email
		if((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/", $this->email) == false)){
			$this->_error = _ABP_CL_MSEF;
			return false;
		}

		return true;
	}
}

class mosArtBanner extends mosDBTable{
	var $id = null; // int(11) NOT NULL PRIMARY auto_increment,
	var $cid = null; // int(11) NOT NULL default '0',
	var $tid = null; // int(11) NOT NULL default '0',
	var $type = ""; // varchar(10) NOT NULL default 'banner',
	var $name = ""; // varchar(50) NOT NULL default '',
	var $imp_total = 0; // int(11) NOT NULL default '0',
	var $imp_made = 0; // int(11) NOT NULL default '0',
	var $clicks = 0; // int(11) NOT NULL default '0',
	var $image_url = ""; // varchar(100) NOT NULL default '',
	var $click_url = ""; //  varchar(200) NOT NULL default '',
	var $last_show = null; // datetime default NULL,
	var $msec = null; // int(11) NOT NULL,
	var $state = 1; // tinyint(1) NOT NULL default '0',
	var $checked_out = 0; // tinyint(1) NOT NULL default '0',
	var $checked_out_time = 0; // time default NULL,
	var $reccurtype = null;
	var $reccurweekdays = '';
	var $custom_banner_code = ""; // text,
	var $access = 0; // int(11) NOT NULL default '0',
	var $target = "blank";
	var $border_value = 0;
	var $border_style = "solid";
	var $border_color = "green";
	var $click_value = 0;
	var $complete_clicks = 0;
	var $imp_value = 0;
	var $dta_mod_clicks = '0000-00-00';
	var $password = '';

	var $publish_up_date = '0000-00-00';
	var $publish_up_time = '00:00:00';
	var $publish_down_date = '0000-00-00';
	var $publish_down_time = '00:00:00';

	var $alt = '';
	var $title = '';

	function mosArtBanner($db){
		$this->mosDBTable('#__banners', 'id', $db);
	}

	function setDate(){
		$this->set("last_show", mosCurrentDate("%Y-%m-%d %H:%M:%S"));
	}

	function clicks(){
		$this->_db->setQuery("UPDATE #__banners SET clicks=(clicks+1), complete_clicks=complete_clicks+1 WHERE id='$this->id'");
		$this->_db->query();
	}

	function check(){
		// check for valid client id
		if(is_null($this->cid) || $this->cid == 0){
			$this->_error = _ABP_BN_MSC;
			return false;
		}

		// check for valid category id
		if(is_null($this->tid) || $this->tid == 0){
			$this->_error = _ABP_BN_MSCA;
			return false;
		}

		if(trim($this->name) == ""){
			$this->_error = _ABP_BN_MSNB;
			return false;
		}

		$this->custom_banner_code = trim($this->custom_banner_code);
		if($this->custom_banner_code == ""){
			// if is not banner swf
			if(!preg_match("/.swf/", $this->image_url)){
				if(trim($this->image_url) == ""){
					$this->_error = _ABP_BN_MSIB;
					return false;
				}

				if(trim($this->click_url) == ""){
					$this->_error = _ABP_BN_MSUB;
					return false;
				}
			}
		}

		if($this->reccurtype != 0 && $this->reccurweekdays == ""){
			$this->_error = _ABP_BN_REC;
			return false;
		}

		return true;
	}
}

/**
 * Category database table class
 */
class mosArtCategory extends mosDBTable{
	/**
	 *  *  *  * @var int Primary key */
	var $id = null;
	/**
	 *  *  *  * @var string The full name for the Category*/
	var $name = null;
	/**
	 *  *  *  * @var string */
	var $description = null;
	/**
	 *  *  *  * @var boolean */
	var $published = null;
	/**
	 *  *  *  * @var boolean */
	var $checked_out = null;
	/**
	 *  *  *  * @var time */
	var $checked_out_time = null;

	/**
	 * @param database A database connector object
	 */
	function mosArtCategory($db){
		$this->mosDBTable('#__banners_categories', 'id', $db);
	}

	// overloaded check function
	function check(){
		// check for valid name
		if(trim($this->name) == ''){
			$this->_error = _ABP_YCMHAN;
			return false;
		}
		// check for existing name
		$this->_db->setQuery("SELECT id FROM #__banners_categories WHERE name='" . $this->name . "'");

		$xid = intval($this->_db->loadResult());
		if($xid && $xid != intval($this->id)){
			$this->_error = _ABP_TIACAWTHPTA;
			return false;
		}
		return true;
	}
}

class mosArtBannersTime{
	var $hour = null;
	var $minute = null;
	var $second = null;

	function mosArtBannersTime($time = null){
		if($time == null){
			$time = mosCurrentDate("%H:%M:%S");
		}

		if(preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $time, $regs)){
			$this->hour = $regs[1];
			$this->minute = $regs[2];
			$this->second = $regs[3];
		} else{
			$this->hour = 0;
			$this->minute = 0;
			$this->second = 0;
		}
	}
}