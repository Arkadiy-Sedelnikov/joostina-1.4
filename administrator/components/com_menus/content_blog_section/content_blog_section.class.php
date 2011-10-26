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
class content_blog_section {

	/**
	* @param database A database connector object
	* @param integer The unique id of the section to edit (0 if new)
	*/
	function edit($uid,$menutype,$option,$menu) {
		global $my,$mainframe;

		$database = database::getInstance();

		// fail if checked out not by 'me'
		if($menu->checked_out && $menu->checked_out != $my->id) {
			mosErrorAlert($menu->title." "._MODULE_IS_EDITING_MY_ADMIN);
		}

		if($uid) {
			$menu->checkout($my->id);
			// get previously selected Categories
			$params = new mosParameters($menu->params);
			$secids = $params->def('sectionid','');
			if($secids) {
				$secidsArray = explode(',',$secids);
				mosArrayToInts($secidsArray);
				$secids = 's.id='.implode(' OR s.id=',$secidsArray);
				$query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text` FROM #__sections AS s WHERE s.scope = 'content' AND ( $secids )"." ORDER BY s.name";
				$database->setQuery($query);
				$lookup = $database->loadObjectList();
			} else {
				$lookup = '';
			}
		} else {
			$menu->type = 'content_blog_section';
			$menu->menutype = $menutype;
			$menu->ordering = 9999;
			$menu->parent = intval(mosGetParam($_POST,'parent',0));
			$menu->published = 1;
			$lookup = '';
		}

		// build the html select list for section
		$rows[] = mosHTML::makeOption('',_ALL_SECTIONS);
		$query = "SELECT s.id AS `value`, s.id AS `id`, s.title AS `text` FROM #__sections AS s WHERE s.scope = 'content' ORDER BY s.name";
		$database->setQuery($query);
		$rows = array_merge($rows,$database->loadObjectList());
		$section = mosHTML::selectList($rows,'secid[]','class="inputbox" ondblclick="jadd(\'name\',this.options[this.selectedIndex].text);" size="10" multiple="multiple"','value','text',$lookup);
		$lists['sectionid'] = $section;

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

		content_blog_section_html::edit($menu,$lists,$params,$option);
	}

	function saveMenu($option,$task) {

		$database = database::getInstance();

		$params = mosGetParam($_POST,'params','');

		$secids = josGetArrayInts('secid');
		$secid = implode(',',$secids);

		$params['sectionid'] = $secid;
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

		if(count($secids) == 1 && $secids[0] != '') {
			$row->link = str_replace('id=0','id='.$secids[0],$row->link);
			$row->componentid = $secids[0];
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
		$row->updateOrder("menutype = ".$database->Quote($row->menutype).
			" AND parent = ".(int)$row->parent);

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
