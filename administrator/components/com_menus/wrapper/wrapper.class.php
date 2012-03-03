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
* Wrapper class
* @package Joostina
* @subpackage Menus
*/
class wrapper_menu {

	function edit(&$uid,$menutype,$option,$menu) {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        $database = database::getInstance();

		// fail if checked out not by 'me'
		if($menu->checked_out && $menu->checked_out != $my->id) {
			mosErrorAlert($menu->title." "._MODULE_IS_EDITING_MY_ADMIN);
		}

		if($uid) {
			$menu->checkout($my->id);
		} else {
			$menu->type = 'wrapper';
			$menu->menutype = $menutype;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
			$menu->link = 'index.php?option=com_wrapper';
		}

		// build the html select list for ordering
		$lists['ordering'] = mosAdminMenus::Ordering($menu,$uid);
		// build the html select list for the group access
		$lists['access'] = mosAdminMenus::Access($menu);
		// build the html select list for paraent item
		$lists['parent'] = mosAdminMenus::Parent($menu);
		// build published button option
		$lists['published'] = mosAdminMenus::Published($menu);
		// build the url link output
		$lists['link'] = mosAdminMenus::Link($menu,$uid);

		// get params definitions
		$params = new mosParameters($menu->params,$mainframe->getPath('menu_xml',$menu->type),
			'menu');
		if($uid) {
			$menu->url = $params->def('url','');
		}

		wrapper_menu_html::edit($menu,$lists,$params,$option);
	}


	function saveMenu($option,$task) {
		$database = database::getInstance();

		$params = mosGetParam($_POST,'params','');
		$params[url] = mosGetParam($_POST,'url','');

		if(is_array($params)) {
			$txt = array();
			foreach($params as $k => $v) {
				$txt[] = "$k=$v";
			}
			$_POST['params'] = mosParameters::textareaHandling($txt);
		}

		$row = new mosMenu($database);

		if(!$row->bind($_POST)) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}

		if(!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if(!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$row->checkin();
		$row->updateOrder('menutype = '.$database->Quote($row->menutype).
			' AND parent = '.(int)$row->parent);


		$msg = _MENU_ITEM_SAVED;
		switch($task) {
			case 'apply':
				mosRedirect('index2.php?option='.$option.'&menutype='.$row->menutype.
					'&task=edit&id='.$row->id,$msg);
				break;

			case 'save':
			default:
				mosRedirect('index2.php?option='.$option.'&menutype='.$row->menutype,$msg);
				break;
		}
	}
}
?>