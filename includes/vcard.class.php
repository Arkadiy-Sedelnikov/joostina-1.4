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
 * @version $Id: vcard.class.php 732 2005-10-31 02:53:15Z stingrey $
 * Modified PHP vCard class v2.0
 */

/***************************************************************************
* PHP vCard class v2.0
* (cKai Blankenhorn
* www.bitfolge.de/en
* kaib@bitfolge.de

* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.

* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
***************************************************************************/

// taken from PHP documentation comments
if( !function_exists('quoted_printable_encode') ) {
	function quoted_printable_encode($input,$line_max = 76) {
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines = preg_split("/(?:\r\n|\r|\n)/",$input);
		$eol = "\r\n";
		$linebreak = '=0D=0A';
		$escape = '=';
		$output = '';

		for($j = 0; $j < count($lines); $j++) {
			$line = $lines[$j];
			$linlen = strlen($line);
			$newline = '';

			for($i = 0; $i < $linlen; $i++) {
				$c = substr($line,$i,1);
				$dec = ord($c);

				if(($dec == 32) && ($i == ($linlen - 1))) { // convert space at eol only
					$c = '=20';
				} elseif(($dec == 61) || ($dec < 32) || ($dec > 126)) { // always encode "\t", which is*not* required
					$h2 = floor($dec / 16);
					$h1 = floor($dec % 16);
					$c = $escape.$hex["$h2"].$hex["$h1"];
				}
				if((strlen($newline) + strlen($c)) >= $line_max) { // CRLF is not counted
					$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
					$newline = "    ";
				}
				$newline .= $c;
			} // end of for
			$output .= $newline;
			if($j < count($lines) - 1) {
				$output .= $linebreak;
			}
		}

		return trim($output);
	}
}

function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(';',"\;",$string);
}

class vCard {
	var $properties;
	var $filename;

	function setPhoneNumber($number,$type = '') {
		// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$key = 'TEL';
		if($type != '') {
			$key .= ';'.$type;
		}
		$key .= ';ENCODING=QUOTED-PRINTABLE';

		$this->properties[$key] = quoted_printable_encode($number);
	}

	// UNTESTED !!!
	function setPhoto($type,$photo) { // $type = "GIF" | "JPEG"
		$this->properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
	}

	function setFormattedName($name) {
		$this->properties['FN'] = quoted_printable_encode($name);
	}

	function setName($family = '',$first = '',$additional = '',$prefix = '',$suffix =
			'') {
		$this->properties['N'] = "$family;$first;$additional;$prefix;$suffix";
		$this->filename = "$first%20$family.vcf";
		if($this->properties['FN'] == '') {
			$this->setFormattedName(trim("$prefix $first $additional $family $suffix"));
		}
	}

	function setBirthday($date) { // $date format is YYYY-MM-DD
		$this->properties['BDAY'] = $date;
	}

	function setAddress($postoffice = '',$extended = '',$street = '',$city = '',$region ='',$zip = '',$country = '',$type = 'HOME;POSTAL') {
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = 'ADR';
		if($type != '') {
			$key .= ";$type";
		}

		$key .= ';ENCODING=QUOTED-PRINTABLE';
		$name = null;
		$this->properties[$key] = encode($name).';'.encode($extended).';'.encode($street).';'.encode($city).';'.encode($region).';'.encode($zip).';'.encode($country);


	}

	function setLabel($postoffice = '',$extended = '',$street = '',$city = '',$region =
			'',$zip = '',$country = '',$type = 'HOME;POSTAL') {
		$label = '';
		if($postoffice != '') {
			$label .= $postoffice;
			$label .= "\r\n";
		}

		if($extended != '') {
			$label .= $extended;
			$label .= "\r\n";
		}

		if($street != '') {
			$label .= $street;
			$label .= "\r\n";
		}

		if($zip != '') {
			$label .= $zip.' ';
		}

		if($city != '') {
			$label .= $city;
			$label .= "\r\n";
		}

		if($region != '') {
			$label .= $region;
			$label .= "\r\n";
		}

		if($country != '') {
			$country .= $country;
			$label .= "\r\n";
		}

		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($label);
	}

	function setEmail($address) {
		$this->properties['EMAIL;INTERNET'] = $address;
	}

	function setNote($note) {
		$this->properties['NOTE;ENCODING=QUOTED-PRINTABLE'] = quoted_printable_encode($note);
	}

	function setURL($url,$type = '') {
		// $type may be WORK | HOME
		$key = 'URL';
		if($type != '') {
			$key .= ";$type";
		}

		$this->properties[$key] = $url;
	}

	public function getVCard( $sitename = false ) {
		$text = 'BEGIN:VCARD';
		$text .= "\r\n";
		$text .= 'VERSION:2.1';
		$text .= "\r\n";

		foreach($this->properties as $key => $value) {
			$text .= "$key:$value\r\n";
		}

		$text .= 'REV:'.date('Y-m-d').'T'.date('H:i:s').'Z';
		$text .= "\r\n";
		$text .= 'MAILER:PHP vCard class by Kai Blankenhorn';
		$text .= "\r\n";
		$text .= 'END:VCARD';
		$text .= "\r\n";

		return $text;
	}

	function getFileName() {
		return $this->filename;
	}
}