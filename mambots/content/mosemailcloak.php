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

$_MAMBOTS->registerFunction('onPrepareContent', 'botMosEmailCloak');

/**
 * Сокрытие от спамботов адресов электронной почты в содержимом, используя javascript
 */
function botMosEmailCloak($published, &$row) {
    $_MAMBOTS = mosMambotHandler::getInstance();
    $database = database::getInstance();

	// check whether mambot has been unpublished
	if (!$published) {
		return true;
	}

	// simple performance check to determine whether bot should process further
	if (strpos($row->text, '@') === false) {
		return true;
	}

	// simple check to allow disabling of bot
	$regex = '{emailcloak=off}';
	if (strpos($row->text, $regex) !== false) {
		$row->text = str_replace($regex, '', $row->text);
		return true;
	}

	// check if param query has previously been processed
	if (!isset($_MAMBOTS->_content_mambot_params['mosemailcloak'])) {
		// загрузка информации о параметрах мамбота
		$query = "SELECT params FROM #__mambots WHERE element = 'mosemailcloak' AND folder = 'content'";
		$database->setQuery($query)->loadObject($mambot);

		// save query to class variable
		$_MAMBOTS->_content_mambot_params['mosemailcloak'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_content_mambot_params['mosemailcloak'];

	$botParams = new mosParameters($mambot->params);
	$mode = $botParams->def('mode', 1);

	// any@email.address.com
	$search_email = "([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)";
	// any@email.address.com?subject=anyText
	$search_email_msg = "([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)([[:alnum:][:space:][:punct:]][^\"<>]+)";
	// anyText
	$search_text = "([[:alnum:][:space:][:punct:]][^<>]+)";

	// поиск кода ссылок вида <a href="mailto:email@amail.com">email@amail.com</a>
	$pattern = botMosEmailCloak_searchPattern($search_email, $search_email);
	while (preg_match("/" . $pattern . "/u", $row->text, $regs)) {
		$mail = $regs[2] . $regs[3] . $regs[4];
		$mail_text = $regs[5] . $regs[6] . $regs[7];

		// проверка, отличается ли адрес почты от адреса почты в текстовом виде
		if ($mail_text) {
			$replacement = mosHTML::emailCloaking($mail, $mode, $mail_text);
		} else {
			$replacement = mosHTML::emailCloaking($mail, $mode);
		}

		// заменить найденный адрес e-mail замаскированным адресом
		$row->text = str_replace($regs[0], $replacement, $row->text);
	}

	// search for derivativs of link code <a href="mailto:email@amail.com">anytext</a>
	$pattern = botMosEmailCloak_searchPattern($search_email, $search_text);
	while (preg_match("/" . $pattern . "/iu", $row->text, $regs)) {
		$mail = $regs[2] . $regs[3] . $regs[4];
		$mail_text = $regs[5];

		$replacement = mosHTML::emailCloaking($mail, $mode, $mail_text, 0);

		// заменить найденный адрес e-mail замаскированным адресом
		$row->text = str_replace($regs[0], $replacement, $row->text);
	}

	// search for derivativs of link code <a href="mailto:email@amail.com?subject=Text&body=Text">email@amail.com</a>
	$pattern = botMosEmailCloak_searchPattern($search_email_msg, $search_email);
	while (preg_match("/" . $pattern . "/iu", $row->text, $regs)) {
		$mail = $regs[2] . $regs[3] . $regs[4] . $regs[5];
		$mail_text = $regs[6] . $regs[7] . $regs[8];
		//needed for handling of Body parameter
		$mail = str_replace('&amp;', '&', $mail);

		// check to see if mail text is different from mail addy
		if ($mail_text) {
			$replacement = mosHTML::emailCloaking($mail, $mode, $mail_text);
		} else {
			$replacement = mosHTML::emailCloaking($mail, $mode);
		}

		// replace the found address with the js cloacked email
		$row->text = str_replace($regs[0], $replacement, $row->text);
	}

	// search for derivativs of link code <a href="mailto:email@amail.com?subject=Text&body=Text">anytext</a>
	$pattern = botMosEmailCloak_searchPattern($search_email_msg, $search_text);
	while (preg_match("/" . $pattern . "/iu", $row->text, $regs)) {
		$mail = $regs[2] . $regs[3] . $regs[4] . $regs[5];
		$mail_text = $regs[6];
		//needed for handling of Body parameter
		$mail = str_replace('&amp;', '&', $mail);

		$replacement = mosHTML::emailCloaking($mail, $mode, $mail_text, 0);

		// replace the found address with the js cloacked email
		$row->text = str_replace($regs[0], $replacement, $row->text);
	}

	// search for plain text email@amail.com
	while (preg_match("/" . $search_email . "/iu", $row->text, $regs)) {
		$mail = $regs[0];

		$replacement = mosHTML::emailCloaking($mail, $mode);

		// replace the found address with the js cloacked email
		$row->text = str_replace($regs[0], $replacement, $row->text);
	}
}

function botMosEmailCloak_searchPattern($link, $text) {
	// <a href="mailto:anyLink">anyText</a>
	return "(<a [[:alnum:] _\"\'=\@\.\-]*href=[\"\']mailto:" . $link . "[\"\'][[:alnum:] _\"\'=\@\.\-]*)>" . $text . "<\/a>";
}