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
class content_typed_menu {

	function edit(&$uid,$menutype,$option,$menu) {
		global $database,$my,$mainframe;

		// fail if checked out not by 'me'
		if($menu->checked_out && $menu->checked_out != $my->id) {
			mosErrorAlert($menu->title." "._MODULE_IS_EDITING_MY_ADMIN);
		}

		if($uid) {
			$menu->checkout($my->id);
		} else {
			// load values for new entry
			$menu->type = 'content_typed';
			$menu->menutype = $menutype;
			$menu->browserNav = 0;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
		}

		if($uid) {
			$temp = explode('id=',$menu->link);
			$query = "SELECT a.title, a.title_alias, a.id FROM #__content AS a WHERE a.id = ".(int)$temp[1];
			$database->setQuery($query);
			$content = $database->loadObjectlist();
			// outputs item name, category & section instead of the select list
			if($content[0]->title_alias) {
				$alias = '  (<i>'.$content[0]->title_alias.'</i>)';
			} else {
				$alias = '';
			}
			$contents = '';
			$link = 'javascript:submitbutton( \'redirect\' );';
			$lists['content'] = '<input type="hidden" name="content_typed" value="'.$temp[1].'" />';
			$lists['content'] .= '<a href="'.$link.'" title="'._EDIT_CONTENT_TYPED.'">'.$content[0]->title.$alias.'</a>';
		} else {
			$query = "SELECT a.id AS value, CONCAT( a.title, '(', a.title_alias, ')' ) AS text".
				"\n FROM #__content AS a"."\n WHERE a.state = 1"."\n AND a.sectionid = 0"."\n AND a.catid = 0".
				"\n ORDER BY a.title, a.id";
			$database->setQuery($query);
			$contents = $database->loadObjectList();

			//	Create a list of links
			$lists['content'] = mosHTML::selectList($contents,'content_typed','ondblclick="jadd(\'name\',this.options[this.selectedIndex].text);" class="inputbox" size="10"','value','text','');
		}

		// build html select list for target window
		$lists['target'] = mosAdminMenus::Target($menu);

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
		$params = new mosParameters($menu->params,$mainframe->getPath('menu_xml',$menu->type),'menu');

		content_menu_html::edit($menu,$lists,$params,$option,$contents);
	}

	function redirect($id) {
		global $database;

		$menu = new mosMenu($database);
		$menu->bind($_POST);
		$menuid = intval(mosGetParam($_POST,'menuid',0));
		if($menuid) {
			$menu->id = $menuid;
		}
		$menu->checkin();

		mosRedirect('index2.php?option=com_typedcontent&task=edit&id='.$id);
	}
}