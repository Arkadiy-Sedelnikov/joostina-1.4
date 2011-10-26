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
DEFINE('_MYLIB', '1');

class myLib {

    function myLib() {

    }

}

class myLibAdmin {

    function myLibAdmin() {

    }

}


class myFunctions {

    var $func = null;
    var $params = null;
    var $obj = null;

    function myFunctions($func, $params) {
        $this->func = $func;
        $this->params = $params;
        $this->bind();
    }

    function bind() {

        $obj = new stdClass();
        foreach($this->params as $key=>$val) {
            $obj->$key = $val;
        }
        $this->obj = $obj;
    }

    function check_user_function() {
        $mainframe = mosMainFrame::getInstance();
        if(!defined('_MYLIB')) {
            return false;
        }
        if($mainframe->isAdmin()) {
            $methods = get_class_methods('myLibAdmin');
        }
        else {
            $methods = get_class_methods('myLib');
        }
        if(in_array($this->func, $methods)) {
            return true;
        }
        return false;
    }

    function start_user_function() {
        $mainframe = mosMainFrame::getInstance();
        if($mainframe->isAdmin()) {
            $class = 'myLibAdmin';
        }else {
            $class = 'myLib';
        }
        return call_user_func(array($class, $this->func), $this->obj);
    }

}