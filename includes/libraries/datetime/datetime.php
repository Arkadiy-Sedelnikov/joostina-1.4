<?php
/**
 * Класс работы с датами
 *
 * @package Joostina
 * @copyright (C) 2009 Extention Team. Joostina Team. Все права защищены.
 * @license GNU/GPL, подробнее в help/lisense.php
 * @version $Id: text.php 05.07.2009 12:07:48 megazaisl $;
 * @since Version 1.3
 */
defined('_VALID_MOS') or die();

class DateAndTime {
	

	function DateAndTime() {

	}

	// returns the associative array with date deltas.
	function getDelta($first, $last) {
		if ($last < $first) return false;

		// Solve H:M:S part.
		$hms = ($last - $first) % (3600 * 24);
		$delta['seconds'] = $hms % 60;
		$delta['minutes'] = floor($hms/60) % 60;
		$delta['hours']   = floor($hms/3600) % 60;

		// Now work only with date, delta time = 0.
		$last -= $hms;
		$f = getdate($first);
		$l = getdate($last); // the same daytime as $first!

		$dYear = $dMon = $dDay = 0;

		// Delta day. Is negative, month overlapping.
		$dDay += $l['mday'] - $f['mday'];
		if ($dDay < 0) {
			$monlen = self::monthLength(date("Y", $first), date("m", $first));
			$dDay += $monlen;
			$dMon--;
		}
		$delta['mday'] = $dDay;

		if($delta['mday']>1) {
			$delta['mday'] = $delta['mday']- 1;
		}


		// Delta month. If negative, year overlapping.
		$dMon += $l['mon'] - $f['mon'];
		if ($dMon < 0) {
			$dMon += 12;
			$dYear --;
		}
		$delta['mon'] = $dMon;

		// Delta year.
		$dYear += $l['year'] - $f['year'];
		$delta['year'] = $dYear;

		return $delta;
	}

	// Returns the length (in days) of the specified month.
	function monthLength($year, $mon) {
		$l = 28;
		while (checkdate($mon, $l+1, $year)) $l++;
		return $l;
	}


	/**
	 * Converts a MySQL Timestamp to Unix
	 *
	 * @access	public
	 * @param	integer Unix timestamp
	 * @return	integer
	 */

	function mysql_to_unix($time = '') {
		// We'll remove certain characters for backward compatibility
		// since the formatting changed with MySQL 4.1
		// YYYY-MM-DD HH:MM:SS

		$time = str_replace('-', '', $time);
		$time = str_replace(':', '', $time);
		$time = str_replace(' ', '', $time);

		// YYYYMMDDHHMMSS
		return  mktime(
				substr($time, 8, 2),
				substr($time, 10, 2),
				substr($time, 12, 2),
				substr($time, 4, 2),
				substr($time, 6, 2),
				substr($time, 0, 4)
		);
	}
}