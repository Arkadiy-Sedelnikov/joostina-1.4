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

$mainframe->addLib('dbconfig');

/**
 * @package Joostina
 * @subpackage Content
 */
class mosFrontPage extends mosDBTable {
	/**
	 @var int Primary key*/
	var $content_id = null;
	/**
	 @var int*/
	var $ordering = null;

	/**
	 * @param database A database connector object
	 */
	function mosFrontPage(&$db) {
		$this->mosDBTable('#__content_frontpage','content_id',$db);
	}
}

/**
 * конфигурация компонента
 */
class frontpageConfig extends dbConfig {

    var $directory  = null;
    var $page       = null;

    function __construct($group = 'com_frontpage', $subgroup = 'default') {
        global $database;
        $db = $database::getInstance();
        parent::__construct($db, $group, $subgroup);
    }



    function getConfig() {
        $confObject = null;
        $config = $this->getBatchValues();
        if(count($config)>0){
            foreach($config as $conf){
                $confName = $conf->name;
                $confObject->$confName = $conf->value;
            }
        }
        return $confObject;
    }

    function save_config() {

        if(!$this->bindConfig($_REQUEST)) {
            echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
            exit();
        }

        if(!$this->storeConfig()) {
            echo "<script> alert('".$this->_error."'); window.history.go(-1); </script>";
            exit();
        }
    }
}