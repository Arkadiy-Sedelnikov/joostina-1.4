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
* Content item link class
* @package Joostina
* @subpackage Menus
*/
class content_item_link_menu {

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
			$menu->type = 'content_item_link';
			$menu->menutype = $menutype;
			$menu->browserNav = 0;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
		}

		if($uid) {
			$link = 'javascript:submitbutton( \'redirect\' );';

			$temp = explode('id=',$menu->link);
			$query = "SELECT a.title, c.name AS category, s.name AS section"."\n FROM #__content AS a".
				"\n LEFT JOIN #__categories AS c ON a.catid = c.id"."\n LEFT JOIN #__sections AS s ON a.sectionid = s.id".
				"\n WHERE a.id = ".(int)$temp[1];
			$database->setQuery($query);
			$content = $database->loadObjectlist();
			// outputs item name, category & section instead of the select list
			$lists['content'] = '
			<table width="100%">
			<tr>
				<td width="10%">
				Item:
				</td>
				<td>
				<a href="'.$link.'" title="'._CHANGE_CONTENT_ITEM.'">
				'.$content[0]->title.'
				</a>
				</td>
			</tr>
			<tr>
				<td width="10%">
				'._CATEGORY.':
				</td>
				<td>
				'.$content[0]->category.'
				</td>
			</tr>
			<tr>
				<td width="10%">
				'._SECTION.':
				</td>
				<td>
				'.$content[0]->section.'
				</td>
			</tr>
			</table>';
			$contents = '';
			$lists['content'] .= '<input type="hidden" name="content_item_link" value="'.$temp[1].'" />';
		} else {
			$query = "SELECT a.id AS value,"."\n CONCAT(s.title, ' - ',c.title,' / ',a.title, '&nbsp;&nbsp;&nbsp;&nbsp;') AS text".
				"\n FROM #__content AS a"."\n INNER JOIN #__categories AS c ON a.catid = c.id".
				"\n INNER JOIN #__sections AS s ON a.sectionid = s.id AND s.scope = 'content'".
				"\n WHERE a.state = 1"."\n ORDER BY a.sectionid, a.catid, a.title";
			$database->setQuery($query);
			$contents = $database->loadObjectList();

			//	Create a list of links
			$lists['content'] = mosHTML::selectList($contents,'content_item_link','ondblclick="jadd(\'name\',this.options[this.selectedIndex].text);" class="inputbox" size="10"','value','text','');
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

		content_item_link_menu_html::edit($menu,$lists,$params,$option,$contents);
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

		mosRedirect('index2.php?option=com_content&task=edit&id='.$id);
	}
}
?>
