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

if (extension_loaded('mbstring')) {
	mb_internal_encoding('UTF-8');
	define('SERVER_UTF8', TRUE);
} else {
	define('SERVER_UTF8', FALSE);
}


$_GET = Jstring::to_utf8($_GET);
$_POST = Jstring::to_utf8($_POST);
$_COOKIE = Jstring::to_utf8($_COOKIE);
$_SERVER = Jstring::to_utf8($_SERVER);
$_REQUEST = Jstring::to_utf8($_REQUEST);

class Jstring {
	/* конвертирование в UTF8 */

	function to_utf8($text) {
		if (is_array($text) OR is_object($str)) {
			$d = array();
			foreach ($text as $k => &$v) {
				$d[Jstring::to_utf8($k)] = Jstring::to_utf8($v);
			}
			return $d;
		}
		if (is_string($text)) {
			if (self::is_utf8($text)) { // если это юникод - сразу его возвращаем
				return $text;
			}
			if (function_exists('iconv')) { // пробуем конвертировать через iconv
				return iconv('cp1251', 'utf-8//IGNORE//TRANSLIT', $text);
			}

			if (!function_exists('cp1259_to_utf8')) { // конвертируем собственнвми средствами
				include_once JPATH_BASE . '/includes/libraries/utf8/to_utf8.php';
			}
			return cp1259_to_utf8($text);
		}
		return $text;
	}

	function htmlentities($text) {
		return htmlentities($text);
	}

	function ltrim() {
		
	}

	function ord() {
		
	}

	function rtrim() {
		
	}

	function str_ireplace() {
		
	}

	function str_pad() {
		
	}

	function str_split() {
		
	}

	function strcasecmp() {
		
	}

	function strcspn() {
		
	}

	function stristr() {
		
	}

	function strlen($str) {
		if (SERVER_UTF8) {
			return mb_strlen($str);
		}
		if (!self::is_utf8($str)) {
			return strlen($str);
		}

		return strlen(utf8_decode($str));
	}

	function strrpos() {
		if (SERVER_UTF8) {
			return mb_strrpos($str, $search, $offset);
		}
		if (!self::is_utf8($str)) {
			return strrpos($str, $search, $offset);
		}

		include_once JPATH_BASE . '/includes/libraries/utf8/strrpos.php';
		return _strrpos($str, $search, $offset);
	}

	function strrev() {
		
	}

	function strpos($str, $search, $offset = 0) {
		if (SERVER_UTF8) {
			return mb_strpos($str, $search, $offset);
		}
		if (!self::is_utf8($str)) {
			return strpos($str, $search, $offset);
		}

		include_once JPATH_BASE . '/includes/libraries/utf8/strpos.php';
		return _strpos($str, $search, $offset);
	}

	function strspn() {
		
	}

	function strtolower($str) {
		if (SERVER_UTF8) {
			return mb_strtolower($str);
		}
		if (!self::is_utf8($str)) {
			return strtolower($str);
		}

		include_once JPATH_BASE . '/includes/libraries/utf8/strtolower.php';
		return _strtolower($str);
	}

	function strtoupper() {
		
	}

	function substr($str, $offset, $length = NULL) {
		if (SERVER_UTF8) {
			return ($length === NULL) ? mb_substr($str, $offset) : mb_substr($str, $offset, $length);
		}

		if (!self::is_utf8($str)) {
			return substr($str, $offset, $length);
		}

		include_once JPATH_BASE . '/includes/libraries/utf8/substr.php';
		return _substr($str, $offset, $length);
	}

	function substr_replace() {
		
	}

	function to_unicode($str) {
		$mState = 0; // cached expected number of octets after the current octet until the beginning of the next UTF8 character sequence
		$mUcs4 = 0; // cached Unicode character
		$mBytes = 1; // cached expected number of octets in the current sequence

		$out = array();

		$len = strlen($str);

		for ($i = 0; $i < $len; $i++) {
			$in = ord($str[$i]);

			if ($mState == 0) {
				// When mState is zero we expect either a US-ASCII character or a
				// multi-octet sequence.
				if (0 == (0x80 & $in)) {
					// US-ASCII, pass straight through.
					$out[] = $in;
					$mBytes = 1;
				} elseif (0xC0 == (0xE0 & $in)) {
					// First octet of 2 octet sequence
					$mUcs4 = $in;
					$mUcs4 = ($mUcs4 & 0x1F) << 6;
					$mState = 1;
					$mBytes = 2;
				} elseif (0xE0 == (0xF0 & $in)) {
					// First octet of 3 octet sequence
					$mUcs4 = $in;
					$mUcs4 = ($mUcs4 & 0x0F) << 12;
					$mState = 2;
					$mBytes = 3;
				} elseif (0xF0 == (0xF8 & $in)) {
					// First octet of 4 octet sequence
					$mUcs4 = $in;
					$mUcs4 = ($mUcs4 & 0x07) << 18;
					$mState = 3;
					$mBytes = 4;
				} elseif (0xF8 == (0xFC & $in)) {
					// First octet of 5 octet sequence.
					//
					// This is illegal because the encoded codepoint must be either
					// (a) not the shortest form or
					// (b) outside the Unicode range of 0-0x10FFFF.
					// Rather than trying to resynchronize, we will carry on until the end
					// of the sequence and let the later error handling code catch it.
					$mUcs4 = $in;
					$mUcs4 = ($mUcs4 & 0x03) << 24;
					$mState = 4;
					$mBytes = 5;
				} elseif (0xFC == (0xFE & $in)) {
					// First octet of 6 octet sequence, see comments for 5 octet sequence.
					$mUcs4 = $in;
					$mUcs4 = ($mUcs4 & 1) << 30;
					$mState = 5;
					$mBytes = 6;
				} else {
					// Current octet is neither in the US-ASCII range nor a legal first octet of a multi-octet sequence.
					trigger_error('utf8::to_unicode: Illegal sequence identifier in UTF-8 at byte ' . $i, E_USER_WARNING);
					return FALSE;
				}
			} else {
				// When mState is non-zero, we expect a continuation of the multi-octet sequence
				if (0x80 == (0xC0 & $in)) {
					// Legal continuation
					$shift = ($mState - 1) * 6;
					$tmp = $in;
					$tmp = ($tmp & 0x0000003F) << $shift;
					$mUcs4 |= $tmp;

					// End of the multi-octet sequence. mUcs4 now contains the final Unicode codepoint to be output
					if (0 == --$mState) {
						// Check for illegal sequences and codepoints
						// From Unicode 3.1, non-shortest form is illegal
						if (((2 == $mBytes) AND ($mUcs4 < 0x0080)) OR ((3 == $mBytes) AND ($mUcs4 < 0x0800)) OR ((4 == $mBytes) AND ($mUcs4 < 0x10000)) OR (4 < $mBytes) OR (($mUcs4 & 0xFFFFF800) == 0xD800) OR ($mUcs4 > 0x10FFFF)) {
							trigger_error('utf8::to_unicode: Illegal sequence or codepoint in UTF-8 at byte ' . $i, E_USER_WARNING);
							return FALSE;
						}

						if (0xFEFF != $mUcs4) {
							// BOM is legal but we don't want to output it
							$out[] = $mUcs4;
						}

						// Initialize UTF-8 cache
						$mState = 0;
						$mUcs4 = 0;
						$mBytes = 1;
					}
				} else {
					// ((0xC0 & (*in) != 0x80) AND (mState != 0))
					// Incomplete multi-octet sequence
					trigger_error('utf8::to_unicode: Incomplete multi-octet sequence in UTF-8 at byte ' . $i, E_USER_WARNING);
					return FALSE;
				}
			}
		}

		return $out;
	}

	function from_unicode($arr) {
		ob_start();

		$keys = array_keys($arr);

		foreach ($keys as $k) {
			// ASCII range (including control chars)
			if (($arr[$k] >= 0) AND ($arr[$k] <= 0x007f)) {
				echo chr($arr[$k]);
			}
			// 2 byte sequence
			elseif ($arr[$k] <= 0x07ff) {
				echo chr(0xc0 | ($arr[$k] >> 6));
				echo chr(0x80 | ($arr[$k] & 0x003f));
			}
			// Byte order mark (skip)
			elseif ($arr[$k] == 0xFEFF) {
				// nop -- zap the BOM
			}
			// Test for illegal surrogates
			elseif ($arr[$k] >= 0xD800 AND $arr[$k] <= 0xDFFF) {
				// Found a surrogate
				trigger_error('utf8::from_unicode: Illegal surrogate at index: ' . $k . ', value: ' . $arr[$k], E_USER_WARNING);
				return FALSE;
			}
			// 3 byte sequence
			elseif ($arr[$k] <= 0xffff) {
				echo chr(0xe0 | ($arr[$k] >> 12));
				echo chr(0x80 | (($arr[$k] >> 6) & 0x003f));
				echo chr(0x80 | ($arr[$k] & 0x003f));
			}
			// 4 byte sequence
			elseif ($arr[$k] <= 0x10ffff) {
				echo chr(0xf0 | ($arr[$k] >> 18));
				echo chr(0x80 | (($arr[$k] >> 12) & 0x3f));
				echo chr(0x80 | (($arr[$k] >> 6) & 0x3f));
				echo chr(0x80 | ($arr[$k] & 0x3f));
			}
			// Out of range
			else {
				trigger_error('utf8::from_unicode: Codepoint out of Unicode range at index: ' . $k . ', value: ' . $arr[$k], E_USER_WARNING);
				return FALSE;
			}
		}

		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function transliterate_to_ascii() {
		
	}

	function trim($data) {
		return $data;
	}

	function ucfirst() {
		
	}

	function ucwords() {
		
	}

	/* проверка на юникод */

	function is_utf8(&$data, $is_strict = true) {
		if (is_array($data)) { // массив
			foreach ($data as $k => &$v) {
				if (!self::is_utf8($v, $is_strict)) {
					return false;
				}
			}
			return true;
		} elseif (is_string($data)) { // строка
			if (function_exists('iconv')) {
				$distance = strlen($data) - strlen(iconv('UTF-8', 'UTF-8//IGNORE', $data));
				if ($distance > 0) {
					return false;
				}
				if ($is_strict && preg_match('/[^\x09\x0A\x0D\x20-\xFF]/sS', $data)) {
					return false;
				}
				return true;
			}

			return self::utf8_check($data, $is_strict);
		} elseif (is_scalar($data) || is_null($data)) { //числа, булево и ничего
			return true;
		}
		return false;
	}

	/* проверка на юникод */

	function utf8_check($str, $is_strict = true) {
		for ($i = 0, $len = strlen($str); $i < $len; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) {
				if ($is_strict === false || ($c > 0x1F && $c < 0x7F) || $c == 0x09 || $c == 0x0A || $c == 0x0D)
					continue;
			}
			if (($c & 0xE0) == 0xC0)
				$n = 1;
			elseif (($c & 0xF0) == 0xE0)
				$n = 2;
			elseif (($c & 0xF8) == 0xF0)
				$n = 3;
			elseif (($c & 0xFC) == 0xF8)
				$n = 4;
			elseif (($c & 0xFE) == 0xFC)
				$n = 5;
			else
				return false;
			for ($j = 0; $j < $n; $j++) {
				$i++;
				if ($i == $len || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

}

?>
