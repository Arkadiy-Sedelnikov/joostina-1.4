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
* Newsfeed item link class
* @package Joostina
* @subpackage Menus
*/
class newsfeed_link_menu {

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
			// load values for new entry
			$menu->type = 'newsfeed_link';
			$menu->menutype = $menutype;
			$menu->browserNav = 0;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
		}

		if($uid) {
			$temp = explode('feedid=',$menu->link);
			$query = "SELECT*, c.title AS category"."\n FROM #__newsfeeds AS a"."\n INNER JOIN #__categories AS c ON a.catid = c.id".
				"\n WHERE a.id = ".(int)$temp[1];
			$database->setQuery($query);
			$newsfeed = $database->loadObjectlist();
			// outputs item name, category & section instead of the select list
			$lists['newsfeed'] = '
			<table width="100%">
			<tr>
				<td width="10%">
				Item:
				</td>
				<td>
				'.$newsfeed[0]->name.'
				</td>
			</tr>
			<tr>
				<td width="10%">
				'._POSITION.':
				</td>
				<td>
				'.$newsfeed[0]->category.'
				</td>
			</tr>
			</table>';
			$lists['newsfeed'] .= '<input type="hidden" name="newsfeed_link" value="'.$temp[1].
				'" />';
			$newsfeeds = '';
		} else {
			$query = "SELECT a.id AS value, CONCAT( c.title, ' - ', a.name ) AS text, a.catid ".
				"\n FROM #__newsfeeds AS a"."\n INNER JOIN #__categories AS c ON a.catid = c.id".
				"\n WHERE a.published = 1"."\n ORDER BY a.catid, a.name";
			$database->setQuery($query);
			$newsfeeds = $database->loadObjectList();

			//	Create a list of links
			$lists['newsfeed'] = mosHTML::selectList($newsfeeds,'newsfeed_link','ondblclick="jadd(\'name\',this.options[this.selectedIndex].text);" class="inputbox" size="10"','value','text','');
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
		$params = new mosParameters($menu->params,$mainframe->getPath('menu_xml',$menu->type),
			'menu');

		newsfeed_link_menu_html::edit($menu,$lists,$params,$option,$newsfeeds);
	}
}
?>