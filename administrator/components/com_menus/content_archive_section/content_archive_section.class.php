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
class content_archive_section_menu {
	/**
	* @param database A database connector object
	* @param integer The unique id of the category to edit (0 if new)
	*/
	function editSection($uid,$menutype,$option,$menu) {
		global $database,$my,$mainframe;

		// fail if checked out not by 'me'
		if($menu->checked_out && $menu->checked_out != $my->id) {
			mosErrorAlert($menu->title." "._MODULE_IS_EDITING_MY_ADMIN);
		}

		if($uid) {
			$menu->checkout($my->id);
		} else {
			$menu->type = 'content_archive_section';
			$menu->menutype = $menutype;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
		}

		// build the html select list for section
		$lists['componentid'] = mosAdminMenus::Section($menu,$uid,1);

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

		content_archive_section_menu_html::editSection($menu,$lists,$params,$option);
	}
}
?>