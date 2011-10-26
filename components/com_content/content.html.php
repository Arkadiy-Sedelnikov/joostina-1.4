<?php /**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die(); ?>
<script type="text/javascript">
	var _com_content_url = '<?php echo JPATH_SITE; ?>/components/com_content';
	var _comcontent_ajax_handler = 'ajax.index.php?option=com_content';
	var _comcontent_defines = new Array();
</script>
<?php /**
 * Utility class for writing the HTML for content
 * @package Joostina
 * @subpackage Content
 */
class ContentView {
	/**
	 * Draws a Content List
	 * Used by Content Category & Content Section
	 */
	function showUserContent($user_items, &$access, &$params, &$pageNav = null, &$lists = null, $order = null,$config=null) {
		global $Itemid;

		if(!$user_items) {
			include_once (JPATH_BASE.'/components/com_content/view/user/items/default.php');
			return;
		}

		$items = $user_items->items;

		$user_id = intval(mosGetParam($_REQUEST, 'id', 0));
		$k = 0;

		if($params->get('page_title')) {
			$title = $params->get('my_page_title');
			if(!$title) {
				$database = database::getInstance();
				$menu = new mosMenu($database);
				$menu->load($Itemid);
				$title = $menu->name;
			}
		}

		$page_link = 'index.php?option=com_content&amp;task=user_content&amp;id='.$user_id.'&amp;Itemid='.$Itemid;
		if($params->get('display')) {
			$page_link = 'index.php?option=com_content&amp;task=user_content&amp;id='.$user_id.'&amp;Itemid='.$Itemid.'&amp;order='.$order;
		}

		if( trim($lists['filter']!='')) {
			$page_link .= '&amp;filter='.urlencode($lists['filter']);
		}

		include_once (JPATH_BASE.'/components/com_content/view/user/items/default.php');

	}

	public static function showSectionCatlist($section, &$access, &$params) {
		global $Itemid, $my;

		$id = $section->id;
		$categories_exist = $section->categories_exist;
		$categories = $section->content;

		$items = $section->content;
		$gid = $my->gid;
		//$other_categories = $section->other_categories;
		$order = $params->get('selected');

		if(strtolower(get_class($section)) == 'mossection') {
			$catid = 0;
		} else {
			$catid = $title->id;
		}
		$sfx = $params->get('pageclass_sfx');
		$page_title = '';
		$title_description = '';
		$title_image = '';
		$add_button = '';

		if($params->get('page_title')) {
			$page_title = htmlspecialchars($params->get('header'), ENT_QUOTES);
		}

		if($params->get('description_sec') && $section->description) {
			$title_description = $section->description;
		}

		if($params->get('description_sec_image') && $section->image) {
			$link = JPATH_SITE.'/images/stories/'.$section->image;
			$title_image = '<img class="desc_img" src="'.$link.'" align="'.$section->image_position.'"  alt="'.$section->image.'" />';
		}

		if(($access->canEdit || $access->canEditOwn) && $categories_exist) {
			$link = sefRelToAbs('index.php?option=com_content&amp;task=new&amp;sectionid='.$id.'&amp;Itemid='.$Itemid);
			$add_button = '<a href="'.$link.'" class="add_button add_content">'._NEW.'</a>';
		}

		$templates = $section->templates;

		$template = new ContentTemplate();
		$template->set_template($params->page_type, $templates);

		include_once ($template->template_file);

	}

	/**
	 * Draws a Content List
	 * Used by Content Category & Content Section
	 */
	public static function showContentList($obj, &$access, &$params,&$mainframe=null) {
		global $Itemid, $my;

		if(!isset($mainframe)) {
			$mainframe = mosMainFrame::getInstance();
		}

		$config = $mainframe->config;

		if($params->page_type == 'section_table') {
			$id = $obj->id;
			$categories_exist = $obj->categories_exist;
		} else {
			$sectionid = $obj->section;
			$categories_exist = null;
			$lists = $obj->get_lists($params);
		}

		$title = $obj;
		$items = $obj->content;
		$gid = $my->gid;
		$other_categories = $obj->other_categories;

		$order = $params->get('selected');
		$sfx = $params->get('pageclass_sfx');

		if($params->get('date') == '' && $config->config_showCreateDate == 1) {
			$params->set('date', 1);
		}
		if($params->get('author') == '' && $config->config_showAuthor == 1) {
			$params->set('author', 1);
		}

		if(strtolower(get_class($title)) == 'mossection') {
			$catid = 0;
		} else {
			$catid = $title->id;
		}

		$page_title = '';
		$title_description = '';
		$title_image = '';
		$add_button = '';
		$show_categories = 0;

		if($params->get('page_title')) {
			$page_title = htmlspecialchars($params->get('header'), ENT_QUOTES);
		}

		if($params->get('description') && $title->description) {
			$title_description = $title->description;
		}

		if($params->get('description_image') && $title->image) {
			$link = JPATH_SITE.'/images/stories/'.$title->image;
			$title_image = '<img class="desc_img" src="'.$link.'" align="'.$title->image_position.'"  alt="'.$title->image.'" />';
		}

		if(($access->canEdit || $access->canEditOwn) && $categories_exist) {
			$link = sefRelToAbs('index.php?option=com_content&amp;task=new&amp;sectionid='.$id.'&amp;Itemid='.$Itemid);
			$add_button = '<a href="'.$link.'" class="add_button add_content">'._NEW.'</a>';
		}

		if(((count($other_categories) > 1) || (count($other_categories) < 2 && count($items) < 1))) {
			if((($params->get('type') == 'category') && $params->get('other_cat')) || (($params->get('type') == 'section') && $params->get('other_cat_section'))) {
				$show_categories = 1;
			}
		}

		if(!$items) {
			$page_type = 'section_catlist';
			$templates = $params->section_data->templates;
			//include_once(JPATH_BASE.'/components/com_content/view/section/catlist/default.php');
		} else {
			$page_type = 'category_table';
			$templates = $params->category_data->templates;
			//include_once(JPATH_BASE.'/components/com_content/view/category/table/default.php');
		}

		mosMainFrame::addLib('pageNavigation');
		$pageNav = new mosPageNav($obj->total, $params->get('limitstart'), $params->get('limit'));

		$template = new ContentTemplate();
		$template->set_template($params->page_type, $templates);

		include_once ($template->template_file);

	}

	/**
	 * Display links to categories
	 */
	function showCategories(&$params, &$items, $gid, &$other_categories, $catid, $id, $Itemid) {
		//подключается шаблон
	}

	/**
	 * Display Table of items
	 */
	function showTable(&$params, &$items, &$gid, $catid, $id, &$pageNav, &$access, &$sectionid, &$lists, $order) {
		//подключается шаблон /components/com_content/view/table_of_items/default.php
	}

	/**
	 * Display links to content items
	 */
	public static function showLinks(&$rows, $links, $total, $i = 0, $show = 1, $ItemidCount = null) {
		global $mainframe, $Itemid;

		// getItemid compatibility mode, holds maintenance version number
		$compat = (int)$mainframe->getCfg('itemid_compat');

		if($show && isset($rows[$i])) { ?>
<div class="more_items">
	<strong><?php echo _MORE; ?></strong>
	<ul class="more_items">
					<?php for ($z = 0; $z < $links; $z++) {
						if(!isset($rows[$i])) {
							// stops loop if total number of items is less than the number set to display as intro + leading
							break;
						}

						if($compat > 0 && $compat <= 11) {
							$_Itemid = $mainframe->getItemid($rows[$i]->id, 0, 0);
						} else {
							$_Itemid = $Itemid;
						}

						if($_Itemid && $_Itemid != 99999999) {
							// where Itemid value is returned, do not add Itemid to url
							$Itemid_link = '&amp;Itemid='.$_Itemid;
						} else {
							// where Itemid value is NOT returned, do not add Itemid to url
							$Itemid_link = '';
						}

						$link = sefRelToAbs('index.php?option=com_content&amp;task=view&amp;id='.$rows[$i]->id.$Itemid_link) ?>
		<li>
			<a class="blogsection" href="<?php echo $link; ?>" title="<?php echo $rows[$i]->title; ?>"><?php echo $rows[$i]->title; ?></a>
		</li>
						<?php $i++;
					}?>
	</ul>
</div>
			<?php } ?>
		<?php }

	/**
	 * Отображение содержимого
	 * @param object An object with the record
	 * @param boolean If <code>false</code>, the print button links to a popup window.  If <code>true</code> then the print button invokes the browser print method.
	 */
	public static function show(&$row, &$params, &$access, $page = 0, $_template = '',$mainframe=null) {
		global $hide_js, $_MAMBOTS;
		global $news_uid, $task;

		if(!isset($mainframe)) {
			//jd_inc(':(--show');
			$mainframe = mosMainFrame::getInstance();
		}

		// уникальные идентификаторы новостей
		$news_uid_css_title = '';
		$news_uid_css_body = '';
		if($mainframe->config->config_uid_news) {
			$news_uid++;
			$news_uid_css_title = 'id="title-news-uid-'.$news_uid.'" ';
			$news_uid_css_body = 'id="body-news-uid-'.$news_uid.'" ';
		}

		// получение параметров текущего содержимого
		$cur_params = new mosParameters($row->attribs);
		$news_uid_css_page = $cur_params->get('pageclass_uids');
		if($cur_params->get('pageclass_uids_full') && trim($news_uid_css_page) != '') {
			$news_uid_css_title = 'id="title-news-'.$news_uid_css_page.'" ';
			$news_uid_css_body = 'id="body-news-'.$news_uid_css_page.'" ';
		}

		// расчет Itemid
		ContentView::_Itemid($row,$mainframe);
		// determines the link and `link text` of the readmore button & linked title
		ContentView::_linkInfo($row, $params);

		// link used by print button
		$print_link = JPATH_SITE.'/index2.php?option=com_content&amp;task=view&amp;id='.$row->id.'&amp;pop=1&amp;page='.$page.$row->Itemid_link;
		$readmore = ContentView::ReadMore($row, $params);

		// обработка контента ботами, если в глобальной конфигурации они отключены - то мамботы не используем
		if($mainframe->getCfg('mmb_content_off') != 1) {
			$_MAMBOTS->loadBotGroup('content');
			$results = $_MAMBOTS->trigger('onPrepareContent', array(&$row, &$params, $page), true);
		}

		$row->title = ContentView::Title($row, $params, $access);

		//зануляем
		$loadbot_onAfterDisplayTitle = '';
		$loadbot_onBeforeDisplayContent = '';

		if(!$params->get('intro_only')) {
			$results_onAfterDisplayTitle = $_MAMBOTS->trigger('onAfterDisplayTitle', array(&$row, &$params, $page));
			$loadbot_onAfterDisplayTitle = trim(implode("\n", $results_onAfterDisplayTitle));
		}

		$results_onBeforeDisplayContent = $_MAMBOTS->trigger('onBeforeDisplayContent', array(&$row, &$params, $page));
		$loadbot_onBeforeDisplayContent = trim(implode("\n", $results_onBeforeDisplayContent));

		$create_date = null;
		if($row->created != 0) {
			$create_date = mosFormatDate($row->created);
		}

		$mod_date = null;
		if(intval($row->modified) != 0) {
			$mod_date = mosFormatDate($row->modified);
		}

		$author = mosContent::Author($row, $params,$mainframe->config->config_author_name);

		$edit = '';
		if($access->canEdit) {
			$edit = mosContent::EditIcon2($row, $params, $access, _EDIT);
		}

		//если 'template' задано - значит выводится интро записи в блоге,
		//поэтому никаких дополнительных манипуляций не требуется,
		// так как имя шаблона задается непосредственно в шаблоне блога раздела или категории
		if($params->get('page_type')=='item_intro_simple' || $params->get('page_type')=='item_intro_leading') {

			$template = new ContentTemplate();
			$_template = $params->get('page_type').'='.$_template;
			$template->set_template($params->get('page_type'), $_template);
			include ($template->template_file);
			//include (JPATH_BASE.'/components/com_content/view/item/'.$template);
		}
		//иначе - это страница записи и нужно определить, какой шаблон  использовать для вывода
		else {
			$template = new ContentTemplate();
			$templates = null;

			//если это статичное содержимое или у записи задан шаблон
			if(!$row->sectionid || $row->templates) {
				if($row->templates) {
					$templates = $row->templates;
				}
			}
			//иначе - проверяем настройки категории, возможно, шаблон задан там
			elseif($template->isset_settings($params->page_type, $params->category_data->templates)) {
				$templates = $params->category_data->templates;
			}
			//иначе - проверяем настройки раздела
			elseif($template->isset_settings($params->page_type, $params->section_data->templates)) {
				$templates = $params->section_data->templates;
			}

			$template->set_template($params->page_type, $templates);
			include_once ($template->template_file);
		}
	}

	public static function afterDisplayContent(&$row, &$params, $page){
		global $_MAMBOTS;
		$results_onAfterDisplayContent = $_MAMBOTS->trigger('onAfterDisplayContent', array(&$row, &$params, $page));
		return trim(implode("\n", $results_onAfterDisplayContent));
	}

	/**
	 * calculate Itemid
	 */
	public static function _Itemid(&$row,&$mainframe) {
		global $task, $Itemid;

		if(!isset($mainframe)) {
			$mainframe = mosMainFrame::getInstance();
			//jd_inc('_Itemid');
		}

		// getItemid compatibility mode, holds maintenance version number
		$compat = (int)$mainframe->getCfg('itemid_compat');
		if(($compat > 0 && $compat <= 11) && $task != 'view' && $task != 'category') {
			$row->_Itemid = $mainframe->getItemid($row->id, 0, 0);
		} else {
			// when viewing a content item, it is not necessary to calculate the Itemid
			$row->_Itemid = $Itemid;
		}

		if($row->_Itemid && $row->_Itemid != 99999999) {
			// where Itemid value is returned, do not add Itemid to url
			$row->Itemid_link = '&amp;Itemid='.$row->_Itemid;
		} else {
			// where Itemid value is NOT returned, do not add Itemid to url
			$row->Itemid_link = '';
		}
	}

	/**
	 * determines the link and `link text` of the readmore button & linked title
	 */
	public static function _linkInfo(&$row, &$params) {
		global $my;

		$row->link_on = '';
		$row->link_text = '';

		if($params->get('readmore') || $params->get('link_titles')) {
			if($params->get('intro_only')) {
				// checks if the item is a public or registered/special item
				if($row->access <= $my->gid) {
					$row->link_on = sefRelToAbs('index.php?option=com_content&amp;task=view&amp;id='.$row->id.$row->Itemid_link);

					if(isset($row->readmore) && @$row->readmore) {
						// text for the readmore link
						$row->link_text = _READ_MORE;
					}
				} else {
					$row->link_on = sefRelToAbs('index.php?option=com_registration&amp;task=register');

					if(isset($row->readmore) && @$row->readmore) {
						// text for the readmore link if accessible only if registered
						$row->link_text = _READ_MORE_REGISTER;
					}
				}
			}
		}
	}

	/**
	 * Вывод заголовка
	 */
	public static function Title(&$row, &$params, &$access = null) {
		global $task;

		if($params->get('item_title')) {

			// Проверяем, нужно ли делать заголовки ссылками
			if($params->get('link_titles') && $row->link_on != '') {
				$row->title = '<a href="'.$row->link_on.'" title="'.$row->title.'" class="contentpagetitle">'.$row->title.'</a>';
			}

			return $row->title;
		}
		return $row->title;
	}

	/**
	 * Writes Edit icon that links to edit page
	 */
	function EditIcon(&$row, &$params, &$access, $text = '') {
		global $my;

		if($params->get('popup')) {
			return;
		}
		if($row->state < 0) {
			return;
		}
		if(!$access->canEdit && !($access->canEditOwn && $row->created_by == $my->id)) {
			return;
		}

		//mosCommonHTML::loadJqueryPlugins('tooltip/jquery.tooltip', false, true, 'js');

		$link = 'index.php?option=com_content&amp;task=edit&amp;id='.$row->id;
		$image = mosAdminMenus::ImageCheck('edit.png', '/images/M_images/', null, null, _EDIT, _EDIT);
		if($row->state == 0) {
			$overlib = _UNPUBLISHED;
		} else {
			$overlib = _PUBLISHED;
		}
		$date = mosFormatDate($row->created);
		$author = $row->created_by_alias?$row->created_by_alias : $row->author;

		$overlib .= ' / ';
		$overlib .= $row->groups;
		$overlib .= '<br />';
		$overlib .= $date;
		$overlib .= '<br />';
		$overlib .= $author; ?>
<a href="<?php echo sefRelToAbs($link); ?>"><?php echo $image; ?></a>
		<?php }

	/**
	 * Writes Email icon
	 */
	public static function EmailIcon(&$row, &$params, $hide_js) {
		global $Itemid, $task, $cne_i;
		if(!isset($cne_i)) {
			$cne_i = '';
		}

		if($params->get('email') && !$params->get('popup') && !$hide_js) {
			$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=250,directories=no,location=no';

			if($task == 'view') {
				$_Itemid = '&amp;itemid='.$Itemid;
			} else {
				$_Itemid = '';
			}

			$link = JPATH_SITE.'/index2.php?option=com_content&amp;task=emailform&amp;id='.$row->id.$_Itemid;

			if($params->get('icons')) {
				$image = mosAdminMenus::ImageCheck('emailButton.png', '/images/M_images/', null, null, _EMAIL, 'email'.$cne_i);
				$cne_i++;
			} else {
				$image = '&nbsp;'._EMAIL;
			} ?>
<a href="<?php echo $link; ?>" target="_blank" onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>'); return false;" title="<?php echo _EMAIL; ?>"><?php echo $image; ?></a>
			<?php }
	}

	/**
	 * Writes Container for Section & Category
	 */
	function Section_Category(&$row, &$params) {
		if($params->get('section') || $params->get('category')) { ?>
<tr>
	<td>
					<?php }

				// displays Section Name
				ContentView::Section($row, $params);

				// displays Section Name
				ContentView::Category($row, $params);

				if($params->get('section') || $params->get('category')) { ?>
	</td>
</tr>
			<?php }
	}

	/**
	 * Writes Section
	 */
	function Section(&$row, &$params) {
		if($params->get('section')) { ?>
<span class="section_name">
				<?php echo $row->section;
				// writes dash between section & Category Name when both are active
				if($params->get('category')) {
					echo ' - ';
				} ?>
</span>
			<?php }
	}

	/**
	 * Writes Category
	 */
	function Category(&$row, &$params) {
		if($params->get('category')) { ?>
<span class="category_name"><?php echo $row->category; ?></span>
			<?php }
	}

	/**
	 * Writes Create Date
	 */
	function CreateDate(&$row, &$params) {
		$create_date = null;

		if(intval($row->created) != 0) {
			$create_date = mosFormatDate($row->created);
		}

		if($params->get('createdate')) { ?>
<span class="date"><?php echo $create_date; ?></span>
			<?php }
	}

	/**
	 * Writes URL's
	 */
	function URL(&$row, &$params) {
		if($params->get('url') && $row->urls) { ?>
<tr>
	<td valign="top" colspan="2"><a href="http://<?php echo $row->urls; ?>" target="_blank"><?php echo $row->urls; ?></a></td>
</tr>
			<?php }
	}

	/**
	 * Writes TOC
	 */
	function TOC(&$row) {
		if(isset($row->toc)) {
			echo $row->toc;
		}
	}

	/**
	 * Writes Modified Date
	 */
	function ModifiedDate(&$row, &$params) {
		$mod_date = null;

		if(intval($row->modified) != 0) {
			$mod_date = mosFormatDate($row->modified);
		}

		if(($mod_date != '') && $params->get('modifydate')) {
			?><div class="modifydate"><?php echo _LAST_UPDATED; ?> ( <?php echo $mod_date; ?> )</div>
			<?php
		}
	}

	/**
	 * Writes Readmore Button
	 */
	public static function ReadMore(&$row, &$params, $template = '') {
		$return = '';
		if ($params->get('readmore',0) && $params->get('intro_only',0) && $row->link_text) {
			$return = '<a href="' . $row->link_on . '" title="' . $row->title . '" class="readon">' . $row->link_text . '</a>';
		}
		return $return;
	}

	/**
	 * Writes Next & Prev navigation button
	 */
	public static function Navigation(&$row, &$params) {
		global $task;

		if($params->get('pop')) {
			return;
		}

		$link_part = 'index.php?option=com_content&amp;task=view&amp;id=';

		// determines links to next and prev content items within category
		if($params->get('item_navigation')) {
			if($row->prev) {
				$row->prev = sefRelToAbs($link_part.$row->prev.$row->Itemid_link);
			} else {
				$row->prev = 0;
			}

			if($row->next) {
				$row->next = sefRelToAbs($link_part.$row->next.$row->Itemid_link);
			} else {
				$row->next = 0;
			}
		}

		if($params->get('item_navigation') && ($task == 'view') && !$params->get('popup') && ($row->prev || $row->next)) { ?>

<table class="page_navigation">
	<tr>
					<?php if($row->prev) { ?>
		<th class="pagenav_prev">
			<a href="<?php echo $row->prev; ?>" title="<?php echo $row->prev_title; ?>"><?php echo _ITEM_PREVIOUS.$row->prev_title; ?></a>
		</th>
						<?php } ?>
					<?php if($row->prev && $row->next) { ?>
		<th width="50">&nbsp;</th>
						<?php } ?>
					<?php if($row->next) { ?>
		<th class="pagenav_next">
			<a href="<?php echo $row->next; ?>" title="<?php echo $row->next_title; ?>"><?php echo $row->next_title._ITEM_NEXT; ?></a>
		</th>
						<?php } ?>
	</tr>
</table>
			<?php }
	}

	/**
	 * Writes the edit form for new and existing content item
	 *
	 * A new record is defined when <var>$row</var> is passed with the <var>id</var>
	 * property set to 0.
	 * @param mosContent The category object
	 * @param string The html for the groups select list
	 */
	function editContent(&$row, &$page, $task) {
		global $my;
		$mainframe = mosMainFrame::getInstance();

		mosMakeHtmlSafe($row);

		//Загрузка скрипта ajax-валидации
	    $mainframe->addJS(JPATH_SITE . '/includes/js/jquery/plugins/jquery.validate.js', 'js');
		//mosCommonHTML::loadJqueryPlugins('jquery.validate');
		mosCommonHTML::loadCalendar();

		require_once (JPATH_BASE.DS.'includes/HTML_toolbar.php');
		$s_id = mosGetParam($_REQUEST, 'section', 0);
		// used for spoof hardening
		$validate = josSpoofValue();

		if($task == 'edit') {
			$section_id = $row->sectionid;
		} else {
			$section_id = $s_id;
		}

		// параметры полученные из настроек ссылки в меню
		$params = $page->params;

		$p_wwig = $params->get('wwig', 1); // использование визуального редактора
		$wwig_buttons = $params->get('wwig_buttons', 0); // отображение кнопок под визуальным редактором
		$content_type = $params->get('content_type', 1); // тип контента
		$p_fulltext = $params->get('fulltext', 1); // отображать поле полного основного текста
		$allow_alias = $params->get('allow_alias', 0); // отображать поле "Псевдоним заголовка"
		$allow_info = $params->get('allow_info', 1); // отображать информацию о записе
		$allow_params = $params->get('allow_params', 1); // отображать вкладку "параметры"
		$allow_desc = $params->get('allow_desc', 1); // отображать поле "Описание"
		$allow_tags = $params->get('allow_tags', 1); // отображать поле "Ключевые слова"
		$auto_publish = $params->get('auto_publish', 0); // настройки автопубликации
		$allow_frontpage = $params->get('allow_frontpage', 0); // переключатель "На главной"
		$front_label = $params->get('front_label', _E_SHOW_FP); // подпись переключателя "На главной"

		if($p_wwig) {
			$mainframe->set('allow_wysiwyg', 1);
			$wwig_params = array('m_buttons' => $wwig_buttons);
		}
		$access = $page->access;
		$lists = $row->lists;

		$class = '';
		if(count($lists['catid']) > 1) {
			$class = ' class="hidden" ';
		}

		$good_exit_link = 'index.php?option=com_content&task='.$task;
		if($s_id) {
			$good_exit_link .= '&section='.$section_id;
		}
		if($task == 'edit') {
			$good_exit_link .= '&id='.$row->id;
		}
		$good_exit_link = sefRelToAbs($good_exit_link);

		//Если это редактирование статичного содержимого - подключаем шаблон item/edit/static.php
		if($task == 'edit' && $row->sectionid == 0) {
			include (JPATH_BASE.DS.'components/com_content/view/item/edit/static.php');
			return;
		}else { //иначе - проверяем, задан ли шаблон в настройках раздела
			$template = new ContentTemplate();
			$templates = null;

			if(isset($params->section_data->templates) && $template->isset_settings($params->page_type, $params->section_data->templates)) {
				$templates = $params->section_data->templates;
			}

			$template->set_template($params->page_type, $templates);
			include_once ($template->template_file);
		}

		//include(JPATH_BASE.'/components/com_content/view/item/edit_form/'.$template);
	}

	/**
	 * Writes Email form for filling in the send destination
	 */
	function emailForm($uid, $title, $template = '', $itemid) {
		$mainframe = mosMainFrame::getInstance();

		// used for spoof hardening
		$validate = josSpoofValue();

		$mainframe->setPageTitle($title);
		$mainframe->addCustomHeadTag('<link rel="stylesheet" href="templates/'.$template.'/css/template_css.css" type="text/css" />'); ?>
<script language="javascript" type="text/javascript">
	function submitbutton() {
		var form = document.frontendForm;
		// do field validation
		if (form.email.value == "" || form.youremail.value == "") {
			alert( '<?php echo addslashes(_EMAIL_ERR_NOINFO); ?>' );
			return false;
		}
		return true;
	}
</script>

<form action="index2.php?option=com_content&amp;task=emailsend" name="frontendForm" method="post" onSubmit="return submitbutton();">
	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td colspan="2"><?php echo _EMAIL_FRIEND; ?></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="130"><?php echo _EMAIL_FRIEND_ADDR; ?></td>
			<td><input type="text" name="email" class="inputbox" size="25" /></td>
		</tr>
		<tr>
			<td height="27"><?php echo _EMAIL_YOUR_NAME; ?></td>
			<td><input type="text" name="yourname" class="inputbox" size="25" /></td>
		</tr>
		<tr>
			<td><?php echo _EMAIL_YOUR_MAIL; ?></td>
			<td><input type="text" name="youremail" class="inputbox" size="25" /></td>
		</tr>
		<tr>
			<td><?php echo _SUBJECT_PROMPT; ?></td>
			<td><input type="text" name="subject" class="inputbox" maxlength="100" size="40" /></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<span class="button"><input type="submit" name="submit" value="<?php echo _BUTTON_SUBMIT_MAIL; ?>" /></span>
				<span class="button"><input type="button" name="cancel" value="<?php echo _CANCEL; ?>" onclick="window.close();" /></span>
			</td>
		</tr>
	</table>
	<input type="hidden" name="id" value="<?php echo $uid; ?>" />
	<input type="hidden" name="itemid" value="<?php echo $itemid; ?>" />
	<input type="hidden" name="<?php echo $validate; ?>" value="1" />
</form>
		<?php }

	/**
	 * Writes Email sent popup
	 * @param string Who it was sent to
	 * @param string The current template
	 */
	function emailSent($to, $template = '') {
		$mainframe = mosMainFrame::getInstance();

		$mainframe->setPageTitle($mainframe->getCfg('sitename'));
		$mainframe->addCustomHeadTag('<link rel="stylesheet" href="templates/'.$template.'/css/template_css.css" type="text/css" />'); ?>
<span class="contentheading"><?php echo _EMAIL_SENT." $to"; ?></span>
<br />
<br />
<br />
<a href='javascript:window.close();'><span class="small"><?php echo _PROMPT_CLOSE; ?></span></a>
		<?php }

	function _no_access($message = _NOT_AUTH) { ?>
<div class="error"><?php echo $message; ?></div>
		<?php
	}

	function _after_create_content($row) { ?>
		Усё пучкомм
		<?php
	}
}