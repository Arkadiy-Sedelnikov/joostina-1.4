<?php

/**
 * @package Joostina
 * @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */
// запрет прямого доступа
defined('_VALID_MOS') or die();

class mod_ml_login_Helper {

	var $_mainframe = null;

	function mod_ml_login_Helper($mainframe) {

		$this->_mainframe = $mainframe;
	}

	function prepare_logout_form($params) {
		global $my;
		$mainframe = $this->_mainframe;

		//Отображаемое имя пользователя
		if ($params->get('user_name', 1)) {
			$params->_user_name = $my->name;
		} else {
			$params->_user_name = $my->username;
		}

		$params->_raw_user_name = $params->_user_name;

		//Ссылка на профиль пользователя
		$params->_profile_link = '';
		if ($params->get('profile_link', 0) == 0) {
			$params->_user_name = '<a href="' . mosUser::get_link($my) . '">' . $params->_user_name . '</a>';
		} else if ($params->get('profile_link') == 1) {
			$params->_profile_link = '<a href="' . mosUser::get_link($my) . '">' . $params->get('profile_link_text', '') . '</a>';
		}

		$params->_action = sefRelToAbs('index.php?option=logout');
		$params->_returnUrl = self::get_return($params);

		return $params;
	}

	function prepare_login_form($params) {
		global $my;
		$mainframe = $this->_mainframe;

		$params->def('ml_login_text', _USER);
		$params->def('ml_pass_text', _PASSWORDWORD);

		$login_label_def = '<label for="mod_login_USER" id="login_lbl">' . $params->get('ml_login_text') . '</label>';
		$login_input_def = '<input type="text" name="username" id="mod_login_USER" class="inputbox" alt="username" value="" />';

		$pass_label_def = '<label for="mod_login_password" id="pass_lbl">' . $params->get('ml_pass_text') . '</label>';
		$pass_input_def = '<input type="password" id="mod_login_password" name="passwd" class="inputbox" alt="password" value="" />';

		switch ($params->get('show_login_text', 1)) {
			case '0':
				$params->_input_login = $login_input_def;
				break;

			case '1':
			default:
				$params->_input_login = $login_label_def . '<br />' . $login_input_def;
				break;

			case '2':
				$params->_input_login = '<input type="text" name="username" id="mod_login_USER" class="inputbox" alt="username" value="' . $params->get('ml_login_text') . '" onblur="if(this.value==\'\') this.value=\'' . $params->get('ml_login_text') . '\';" onfocus="if(this.value==\'' . $params->get('ml_login_text') . '\') this.value=\'\';" />';
				break;

			case '3':
			default:
				$params->_input_login = $login_label_def . $login_input_def;
				break;
		}

		switch ($params->get('show_pass_text', 1)) {
			case '0':
				$params->_input_pass = $pass_input_def;
				break;

			case '1':
			default:
				$params->_input_pass = $pass_label_def . '<br />' . $pass_input_def;
				break;

			case '2':
				$params->_input_pass = '<input type="password" id="mod_login_password" name="passwd" class="inputbox" alt="password" value="' . $params->get('ml_pass_text') . '" onblur="if(this.value==\'\') this.value=\'' . $params->get('ml_pass_text') . '\';" onfocus="if(this.value==\'' . $params->get('ml_pass_text') . '\') this.value=\'\';" />';
				break;

			case '3':
			default:
				$params->_input_pass = $pass_label_def . $pass_input_def;
				break;
		}


		$params->_returnUrl = self::get_return($params);

		return $params;
	}

	function get_return($params) {
		// url of current page that user will be returned to after login
		$query_string = mosGetParam($_SERVER, 'QUERY_STRING', '');
		if (trim($query_string) != '') {
			$return = 'index.php?' . $query_string;
		} else {
			$return = 'index.php';
		}
		$return = str_replace(JPATH_SITE.'/','',$return);
		$return = ampReplace($return);
					
		return htmlentities(sefRelToAbs($return));
	}

}