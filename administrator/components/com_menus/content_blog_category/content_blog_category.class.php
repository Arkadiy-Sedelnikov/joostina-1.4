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
class content_blog_category {

	/**
	* @param database A database connector object
	* @param integer The unique id of the category to edit (0 if new)
	*/
	function edit(&$uid,$menutype,$option,$menu) {
		global $database,$my,$mainframe;

		// fail if checked out not by 'me'
		if($menu->checked_out && $menu->checked_out != $my->id) {
			mosErrorAlert($menu->title." "._MODULE_IS_EDITING_MY_ADMIN);
		}

		if($uid) {
			$menu->checkout($my->id);
			// get previously selected Categories
			$params = new mosParameters($menu->params);
			$catids = $params->def('categoryid','');
			if($catids) {
				$catidsArray = explode(',',$catids);
				mosArrayToInts($catidsArray);
				$catids = 'c.id='.implode(' OR c.id=',$catidsArray);
				$query = "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text`".
					"\n FROM #__sections AS s INNER JOIN #__categories AS c ON c.section = s.id".
					"\n WHERE s.scope = 'content' AND ( $catids )"."\n ORDER BY s.name,c.name";
				$database->setQuery($query);
				$lookup = $database->loadObjectList();
			} else {
				$lookup = '';
			}
		} else {
			$menu->type = 'content_blog_category';
			$menu->menutype = $menutype;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
			$lookup = '';
		}

		// build the html select list for category
		$rows[] = mosHTML::makeOption('',_ALL_CATEGORIES);
		$query = "SELECT c.id AS `value`, c.section AS `id`, CONCAT_WS( ' / ', s.title, c.title) AS `text`".
			"\n FROM #__sections AS s".
			"\n INNER JOIN #__categories AS c ON c.section = s.id".
			"\n WHERE s.scope = 'content'".
			"\n ORDER BY s.name,c.name";
		$database->setQuery($query);
		$rows = array_merge($rows,$database->loadObjectList());
		$category = mosHTML::selectList($rows,'catid[]','ondblclick="jadd(\'name\',this.options[this.selectedIndex].text);" class="inputbox" size="10" multiple="multiple"','value','text',$lookup);
		$lists['categoryid'] = $category;

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

		/* chipjack: passing $sectCatList (categories) instead of $slist (sections)*/
		content_blog_category_html::edit($menu,$lists,$params,$option);
	}

	function saveMenu($option,$task) {
		global $database;

		$params = mosGetParam($_POST,'params','');

		$catids = josGetArrayInts('catid');
		$catid = implode(',',$catids);

		$params['categoryid'] = $catid;
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

		if(count($catids) == 1 && $catids[0] != "") {
			$row->link = str_replace("id=0","id=".$catids[0],$row->link);
			$row->componentid = $catids[0];
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
		$row->updateOrder("menutype = ".$database->Quote($row->menutype)." AND parent = ".(int)$row->parent);

		$msg = _MENU_ITEM_SAVED;
		switch($task) {
			case 'apply':
				mosRedirect('index2.php?option='.$option.'&menutype='.$row->menutype.'&task=edit&id='.$row->id,$msg);
				break;

			case 'save':
			default:
				mosRedirect('index2.php?option='.$option.'&menutype='.$row->menutype,$msg);
				break;

			case 'save_and_new':
			default:
				mosRedirect('index2.php?option='.$option.'&task=new&menutype='.$row->menutype.'&'.josSpoofValue().'=1');
				break;
			}
	}
}
?>
