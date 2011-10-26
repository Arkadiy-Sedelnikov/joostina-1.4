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

global $Itemid;

function pathwayMakeLink($id,$name,$link,$parent) {
	$mitem = new stdClass();
	$mitem->id = $id;
	$mitem->name = html_entity_decode($name);
	$mitem->link = $link;
	$mitem->parent = $parent;
	$mitem->type = '';
	return $mitem;
}

/**
 * Outputs the pathway breadcrumbs
 * @param database A database connector object
 * @param int The db id field value of the current menu item
 */
function showPathway($Itemid) {
	global $database,$option,$task,$mainframe,$my,$mosConfig_pathway_clean,$mosConfig_disable_access_control;
	if($_SERVER['QUERY_STRING'] == '' & $mosConfig_pathway_clean) {
		echo '&nbsp;';
		return;
	}
	if(!$mosConfig_disable_access_control) {
		$where_ac = "\n AND access <= ".(int)$my->gid;
	}else {
		$where_ac = '';
	}
	// the the whole menu array and index the array by the id
	$query = "SELECT id, name, link, parent, type, menutype, access"
			."\n FROM #__menu"
			."\n WHERE published = 1".$where_ac
			."\n ORDER BY menutype, parent, ordering";
	$database->setQuery($query);
	$mitems = $database->loadObjectList('id');

	// get the home page
	$home_menu = new mosMenu($database);
	foreach($mitems as $mitem) {
		if($mitem->menutype == 'mainmenu') {
			$home_menu = $mitem;
			break;
		}
	}

	$optionstring = '';
	if(isset($_SERVER['REQUEST_URI'])) {
		$optionstring = $_SERVER['REQUEST_URI'];
	} else
	if(isset($_SERVER['QUERY_STRING'])) {
		$optionstring = $_SERVER['QUERY_STRING'];
	}

	// are we at the home page or not
	$home = @$mitems[$home_menu->id]->name;
	$path = '';

	// this is a patch job for the frontpage items! aje
	if($Itemid == $home_menu->id) {
		switch($option) {
			case 'content':
				$id = intval(mosGetParam($_REQUEST,'id',0));

				if($task == 'blogsection') {
					$query = "SELECT title, id"
							."\n FROM #__sections"
							."\n WHERE id = ".(int)$id;
				} else
				if($task == 'blogcategory') {
					$query = "SELECT title, id"
							."\n FROM #__categories"
							."\n WHERE id = ".(int)$id;
				} else {
					$query = "SELECT title, catid, id"
							."\n FROM #__content"
							."\n WHERE id = ".(int)$id;
				}
				$database->setQuery($query);

				$row = null;
				$database->loadObject($row);

				$id = max(array_keys($mitems)) + 1;

				// add the content item
				$mitem2 = pathwayMakeLink($Itemid,$row->title,'',1);
				$mitems[$id] = $mitem2;
				$Itemid = $id;

				$home = '<li><a href="'.sefRelToAbs(JPATH_SITE).'" class="pathway" title="'.$home.'">'.$home.'</a></li>';
				break;
		}
	}

	// breadcrumbs for content items
	switch(@$mitems[$Itemid]->type) {
		// menu item = List - Content Section
		case 'content_section':
			$id = intval(mosGetParam($_REQUEST,'id',0));

			switch($task) {
				case 'category':
					if($id) {
						$query = "SELECT title, id"
								."\n FROM #__categories"
								."\n WHERE id = ".(int)$id
								."\n AND access <= ".(int)
								$my->id;
						$database->setQuery($query);
						$title = $database->loadResult();

						$id = max(array_keys($mitems)) + 1;
						$mitem = pathwayMakeLink($id,$title,'index.php?option='.$option.'&task='.$task.'&id='.$id.'&Itemid='.$Itemid,$Itemid);

						$mitems[$id] = $mitem;
						$Itemid = $id;
					}
					break;

				case 'view':
					if($id) {
						// load the content item name and category
						$query = "SELECT title, catid, id, access"
								."\n FROM #__content"
								."\n WHERE id = ".(int)$id;
						$database->setQuery($query);
						$row = null;
						$database->loadObject($row);

						if( $row->catid > 0) {
							// load and add the category
							$query = "SELECT c.title AS title, s.id AS sectionid, c.id AS id, c.access AS cat_access"
									."\n FROM #__categories AS c"
									."\n LEFT JOIN #__sections AS s"
									."\n ON c.section = s.id"
									."\n WHERE c.id = ".(int)$row->catid
									."\n AND c.access <= ".(int)$my->id;
							$database->setQuery($query);
							$result = $database->loadObjectList();

							$title = $result[0]->title;
							$sectionid = $result[0]->sectionid;

							$id = max(array_keys($mitems)) + 1;
							$mitem1 = pathwayMakeLink($Itemid,$title,'index.php?option='.$option.'&task=category&sectionid='.$sectionid.'&id='.$row->catid,$Itemid);

							$mitems[$id] = $mitem1;
						}
						if($row->access <= $my->gid) {
							// add the final content item
							$id++;
							$mitem2 = pathwayMakeLink($Itemid,$row->title,'',$id - 1);

							$mitems[$id] = $mitem2;
						}
						$Itemid = $id;
					}
					break;
			}
			break;

		// menu item = Table - Content Category
		case 'content_category':
			$id = intval(mosGetParam($_REQUEST,'id',0));

			switch($task) {
				case 'view':
					if($id) {
						// load the content item name and category
						$query = "SELECT title, catid, id"
								."\n FROM #__content"
								."\n WHERE id = ".(int)$id
								."\n AND access <= ".(int)$my->id;
						$database->setQuery($query);
						$row = null;
						$database->loadObject($row);

						$id = max(array_keys($mitems)) + 1;
						// add the final content item
						$mitem2 = pathwayMakeLink($Itemid,$row->title,'',$Itemid);

						$mitems[$id] = $mitem2;
						$Itemid = $id;

					}
					break;
			}
			break;

		// menu item = Blog - Content Category
		// menu item = Blog - Content Section
		case 'content_blog_category':
		case 'content_blog_section':
			switch($task) {
				case 'view':
					$id = intval(mosGetParam($_REQUEST,'id',0));
					if($id) {
						// load the content item name and category

						$query = 'SELECT title, catid, id FROM #__content WHERE id ='.(int)$id.' AND access <= '.(int)$my->id;
						$database->setQuery($query);
						$row = null;
						$database->loadObject($row);

						$id = max(array_keys($mitems)) + 1;
						$mitem2 = pathwayMakeLink($Itemid,$row->title,'',$Itemid);
						$mitems[$id] = $mitem2;
						$Itemid = $id;

					}
					break;
			}
			break;
	}

	$i = count($mitems);
	$mid = $Itemid;

	$imgPath = 'templates/'.JTEMPLATE.'/images/arrow.png';
	if(file_exists(JPATH_BASE.DS.$imgPath)) {
		$img = '<img src="'.JPATH_SITE.'/'.$imgPath.'" border="0" alt=">>" />';
	} else {
		$imgPath = '/images/M_images/arrow.png';
		if(file_exists(JPATH_BASE.$imgPath)) {
			$img = '<img src="'.JPATH_SITE.'/images/M_images/arrow.png" alt=">>" />';
		} else {
			$img = '&gt;';
		}
	}

	while($i--) {
		if(!$mid || empty($mitems[$mid]) || $Itemid == $home_menu->id || !preg_match("/option/i",
				$optionstring)) {
			break;
		}
		$item = &$mitems[$mid];

		$itemname = stripslashes($item->name);

		// if it is the current page, then display a non hyperlink
		if(($item->id == $Itemid && !$mainframe->getCustomPathWay()) || empty($mid) ||
				empty($item->link)) {
			$newlink = '<li>'.$itemname.'</li>';
		} else
		if(isset($item->type) && $item->type == 'url') {
			$correctLink = preg_match('/http:\/\//i',$item->link);
			if($correctLink == 1) {
				$newlink = '<li><a href="'.$item->link.'" target="_window" class="pathway" title="'.$itemname.'">'.$itemname.'</a></li>';
			} else {
				$newlink = '<li>'.$itemname.'</li>';
			}
		} else {
			$newlink = '<li><a href="'.sefRelToAbs($item->link.'&Itemid='.$item->id).'" class="pathway" title="'.$itemname.'">'.$itemname.'</a></li>';
		}

		// converts & to &amp; for xtml compliance
		$newlink = ampReplace($newlink);

		if(trim($newlink) != "") {
			$path = '<li class="pathway_arrow">&nbsp;</li> '.$newlink.' '.$path;
		} else {
			$path = '';
		}

		$mid = $item->parent;
	}

	if(preg_match('/option/i',$optionstring) && trim($path)) {
		$home = '<li class="pahway_home"><a href="'.sefRelToAbs(JPATH_SITE).'" class="pathway" title="'.$home.'">'.$home.'</a></li>';
	}

	if($mainframe->getCustomPathWay()) {
		$path .= '<li class="pathway_arrow">&nbsp;</li><li>';
		$path .= implode('</li><li class="pathway_arrow"></li><li>',$mainframe->getCustomPathWay());
		$path .='</li>';
	}

	if($Itemid && $Itemid != 99999999 && $path != '') {
		echo '<div class="pathway"><ul>'.$home.' '.$path.'</ul></div>';
	} else
		echo '&nbsp;';
}

// code placed in a function to prevent messing up global variables
if(!defined('_JOS_PATHWAY')) {
	// ensure that functions are declared only once
	define('_JOS_PATHWAY',1);

	showPathway($Itemid);
}