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

include_once (JPATH_BASE.'/includes/vcard.class.php');

/**
 * @package Joostina
 * @subpackage Contact
 */
class mosContact extends mosDBTable {
	/**
	 @var int Primary key*/
	var $id = null;
	/**
	 @var string*/
	var $name = null;
	/**
	 @var string*/
	var $con_position = null;
	/**
	 @var string*/
	var $address = null;
	/**
	 @var string*/
	var $suburb = null;
	/**
	 @var string*/
	var $state = null;
	/**
	 @var string*/
	var $country = null;
	/**
	 @var string*/
	var $postcode = null;
	/**
	 @var string*/
	var $telephone = null;
	/**
	 @var string*/
	var $fax = null;
	/**
	 @var string*/
	var $misc = null;
	/**
	 @var string*/
	var $image = null;
	/**
	 @var string*/
	var $imagepos = null;
	/**
	 @var string*/
	var $email_to = null;
	/**
	 @var int*/
	var $default_con = null;
	/**
	 @var int*/
	var $published = null;
	/**
	 @var int*/
	var $checked_out = null;
	/**
	 @var datetime*/
	var $checked_out_time = null;
	/**
	 @var int*/
	var $ordering = null;
	/**
	 @var string*/
	var $params = null;
	/**
	 @var int A link to a registered user*/
	var $user_id = null;
	/**
	 @var int A link to a category*/
	var $catid = null;
	/**
	 @var int*/
	var $access = null;

	/**
	 * @param database A database connector object
	 */
	function mosContact() {
		$database = database::getInstance();
		$this->mosDBTable('#__contact_details','id',$database);
	}

	function check() {
		$this->default_con = intval($this->default_con);
		return true;
	}
}

/**
 * @package Joostina
 * class needed to extend vcard class and to correct minor errors
 */
class MambovCard extends vCard {

	// needed to fix bug in vcard class
	function setName($family = '',$first = '',$additional = '',$prefix = '',$suffix = '') {
		$this->properties["N"] = "$family;$first;$additional;$prefix;$suffix";
		$this->setFormattedName(trim("$prefix $first $additional $family $suffix"));
	}

	// needed to fix bug in vcard class
	function setAddress($postoffice = '',$extended = '',$street = '',$city = '',$region ='',$zip = '',$country = '',$type = 'HOME;POSTAL') {
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$separator = ';';

		$key = 'ADR';
		if($type != '') {
			$key .= $separator.$type;
		}
		$key .= ';ENCODING=QUOTED-PRINTABLE';

		$return = encode($postoffice);
		$return .= $separator.encode($extended);
		$return .= $separator.encode($street);
		$return .= $separator.encode($city);
		$return .= $separator.encode($region);
		$return .= $separator.encode($zip);
		$return .= $separator.encode($country);

		$this->properties[$key] = $return;
	}

	// added ability to set filename
	function setFilename($filename) {
		$this->filename = $filename.'.vcf';
	}

	// added ability to set position/title
	function setTitle($title) {
		$title = trim($title);

		$this->properties['TITLE'] = $title;
	}

	// added ability to set organisation/company
	function setOrg($org) {
		$org = trim($org);

		$this->properties['ORG'] = $org;
	}

	public function getVCard($sitename = false) {
		$text = 'BEGIN:VCARD';
		$text .= "\r\n";
		$text .= 'VERSION:2.1';
		$text .= "\r\n";

		foreach($this->properties as $key => $value) {
			$text .= "$key:$value";
			$text .= "\r\n";
		}
		$text .= 'REV:'.date('Y-m-d').'T'.date('H:i:s').'Z';
		$text .= "\r\n";
		$text .= 'MAILER: Joomla! vCard for '.$sitename;
		$text .= "\r\n";
		$text .= 'END:VCARD';
		$text .= "\r\n";

		return $text;
	}
}
?>
