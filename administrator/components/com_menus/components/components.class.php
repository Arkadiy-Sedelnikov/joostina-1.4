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
* @package Joostina
* @subpackage Menus
*/
class components_menu {
	/**
	* @param database A database connector object
	* @param integer The unique id of the category to edit (0 if new)
	*/
	function edit($uid,$menutype,$option,$menu) {
		global $database,$my,$mainframe;

		$row = new mosComponent($database);
		// load the row from the db table
		$row->load((int)$menu->componentid);

		// fail if checked out not by 'me'
		if($menu->checked_out && $menu->checked_out != $my->id) {
			mosErrorAlert($menu->title." "._MODULE_IS_EDITING_MY_ADMIN);
		}

		if($uid) {
			// do stuff for existing item
			$menu->checkout($my->id);
		} else {
			// do stuff for new item
			$menu->type = 'components';
			$menu->menutype = $menutype;
			$menu->browserNav = 0;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
		}

		$query = "SELECT c.id AS value, c.name AS text, c.link FROM #__components AS c WHERE c.link != '' ORDER BY c.name";
		$database->setQuery($query);
		$components = $database->loadObjectList();

		// build the html select list for section
		$lists['componentid'] = mosAdminMenus::Component($menu,$uid,$components);

		// componentname
		$lists['componentname'] = mosAdminMenus::ComponentName($menu,$components);
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
		$params = new mosParameters($menu->params,$mainframe->getPath('com_xml',$row->option),'component');

		components_menu_html::edit($menu,$components,$lists,$params,$option);
	}
}