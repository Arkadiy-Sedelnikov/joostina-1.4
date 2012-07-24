<?php
/***
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * Информация о версии
 * @package Joostina
 */
class joomlaVersion{
	/** @var строка Продукт*/
	var $PRODUCT = 'Joostina';
	/** @var строка CMS*/
	var $CMS = 'Joostina';
	/** @var версия*/
	var $CMS_ver = '1.4.1';
	/** @var int Номер основной версии*/
	var $RELEASE = '1.4';
	/** @var строка  статус разработки*/
	var $DEV_STATUS = 'alfa';
	/** @var int Подверсия*/
	var $DEV_LEVEL = '1400';
	/** @var int Номер сборки*/
	var $BUILD = '201207241002';
	/** @var string Кодовое имя*/
	var $CODENAME = 'Lotos';
	/** @var string Дата*/
	var $RELDATE = '24.07.2012';
	/** @var string Время*/
	var $RELTIME = '10:02';
	/** @var string Временная зона*/
	var $RELTZ = '+3 GMT';
	/** @var string Текст авторских прав*/
	var $COPYRIGHT = 'Авторские права &copy; 2011-2012 Joostina Lotos. Все права защищены.';
	/** @var string URL*/
	var $URL = '<a href="http://joostina-cms.ru" target="_blank" title="Система создания и управления сайтами Joostina Lotos CMS">Joostina Lotos!</a> - бесплатное и свободное программное обеспечение для создания сайтов, распространяемое по лицензии GNU/GPL.';
	/** @var string для реального использования сайта установите = 1 для демонстраций = 0: 1 используется по умолчанию*/
	var $SITE = 1;
	/** @var string Whether site has restricted functionality mostly used for demo sites: 0 is default*/
	var $RESTRICT = 0;
	/** @var string Whether site is still in development phase (disables checks for /installation folder) - should be set to 0 for package release: 0 is default*/
	var $SVN = 0;
	/** @var string центр поддержки */
	var $SUPPORT_CENTER = 'http://joostina-cms.ru';
	/** @var string ссылки на сайты поддержки*/
	var $SUPPORT = 'Поддержка: <a href="http://joostina-cms.ru" target="_blank" title="Официальный сайт CMS Joostina">joostina-cms.ru</a> | <a href="http://wiki.joostina-cms.ru" target="_blank" title="Wiki-документация">wiki.joostina-cms.ru</a>';

	/** * @return string Длинный формат версии */
	function getLongVersion(){
		return $this->CMS . ' ' . $this->RELEASE . '. ' . $this->CMS_ver . ' [ ' . $this->CODENAME . ' ] ' . $this->RELDATE . ' ' . $this->RELTIME . ' ' . $this->RELTZ;
	}

	/*** @return string Краткий формат версии */
	function getShortVersion(){
		return $this->RELEASE . '.' . $this->DEV_LEVEL;
	}

	/*** @return string Version suffix for help files*/
	function getHelpVersion(){
		return '.' . str_replace('.', '', $this->RELEASE);
	}

	// получение переменных окружения информации осистеме
	public static function get($name){
		$v = new joomlaVersion();
		return $v->$name;
	}
}