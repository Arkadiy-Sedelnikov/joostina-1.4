<?php

/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_JLINDEX') or die();

require_once($mainframe->getPath('front_html', 'com_boss'));
require_once($mainframe->getPath('class', 'com_boss'));
require_once(JPATH_BASE . '/components/com_boss/boss.tools.php');
// cache activation
$cache = mosCache::getCache('com_boss');

$task = (isset($frontpageConf->task)) ? $frontpageConf->task : mosGetParam($_REQUEST, 'task', "front");
$text_search = mosGetParam($_REQUEST, 'text_search', "");
$name_search = mosGetParam($_REQUEST, 'name_search', "");
$limitstart = (int)mosGetParam($_REQUEST, 'limitstart', 0);
$userid = (int)mosGetParam($_REQUEST, 'userid', $my->id);
$catid = (int)mosGetParam($_REQUEST, 'catid', 0);
$contentid = (int)mosGetParam($_REQUEST, 'contentid', 0);
$order = (int)mosGetParam($_REQUEST, 'order', 0);
$mode = mosGetParam($_REQUEST, 'mode', 'email');
$tag = mosGetParam($_REQUEST, 'tag', '');
$alpha = urldecode(mosGetParam($_REQUEST, 'alpha', ''));
$directory = (isset($frontpageConf->directory)) ? (int)$frontpageConf->directory : (int)mosGetParam($_REQUEST, 'directory', 0);
$isFrontpage = (isset($isFrontpage)) ? 1 : 0;

//пробуем разобрать ссылки меню если ид каталога не известен
if($directory == 0){
	$database = database::getInstance();
	$params = $database->setQuery("SELECT `params` FROM #__menu WHERE `id` =0");
	$params = explode("\n", $params);
	foreach($params as $param){
		$param = explode('=', $param);
		if($param[0] == 'directory' && isset($param[1]))
			$directory = $param[1];
		if($param[0] == 'catid' && isset($param[1]) && $catid == 0)
			$catid = $param[1];
		if($param[0] == 'task' && isset($param[1]))
			$task = $param[1];
	}
}
//если все-таки не нашлось каталога, выводим первый.
if($directory == 0){
	$directory = $database->setQuery("SELECT MIN(id) FROM #__boss_config")->loadResult();
	if(!$directory){
		$directory = 0;
	}
}
$directory = (int)$directory;

if($task != 'rss'){
	boss_helpers::loadBossLang($directory);
	boss_helpers::addDirectoryScript($directory);
	// get configuration
	$conf = getConfig($directory);

	if(!is_object($conf)){
		$task = 'emptypage';
	}

	$template_name = (isset($conf->template)) ? $conf->template : 'default';
	$mainframe->addCustomHeadTag('<link rel="stylesheet" href="' . $mosConfig_live_site . '/templates/com_boss/' . $template_name . '/css/boss.css" type="text/css" />');

	//запускаем рассылку писем просроченным абонентам
	$last_cron_date = null;
	$fileCron = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $directory . DS . 'cron.php';
	if(is_file($fileCron)){
		require ($fileCron);
		($last_cron_date != date("Ymd")) ? manage_expiration($directory, $conf, $fileCron, $template_name) : null;
	}
}

switch($task){

	case 'emptypage':
		{
		echo 'Каталога #' . $directory . ' не существует. Назначьте другой каталог для показа на главной странице.';
		break;
		}

	case 'show_profile':
		{
		$cache->call('show_profile', $userid, $directory, $template_name);
		break;
		}

	case 'save_profile':
		{
		mosCache::cleanCache("com_boss");
		save_profile($directory);
		break;
		}

	case 'search':
		{
		if($mainframe->getCfg('caching') == 1){
			$results = $cache->call('show_search', $catid, $directory, $template_name);
		} else{
			$results = show_search($catid, $directory, $template_name);
		}
		boss_show_cached_result($results);
		break;
		}

	case 'show_user':
		{
		if($my->id != $userid && $mainframe->getCfg('caching') == 1){
			$results = $cache->call('show_user', $userid, $text_search, $order, $limitstart, $directory, $template_name);
		} else{
			$results = show_user($userid, $text_search, $order, $limitstart, $directory, $template_name);
		}
		boss_show_cached_result($results);
		break;
		}

	case 'show_category':
		{
		if($mainframe->getCfg('caching') == 1){
			$results = $cache->call('show_category', $catid, $text_search, $name_search, $order, $limitstart, $directory, $template_name);
		} else{
			$results = show_category($catid, $text_search, $name_search, $order, $limitstart, $directory, $template_name);
		}
		boss_show_cached_result($results);
		break;
		}

	case 'search_tags':
		{
		$results = search_tags($tag, $order, $limitstart, $directory, $template_name);
		boss_show_cached_result($results);
		break;
		}

	case 'search_alpha':
		{
		$results = search_alpha($alpha, $order, $limitstart, $directory, $template_name);
		boss_show_cached_result($results);
		break;
		}

	case 'show_rules':
		{
		if($mainframe->getCfg('caching') == 1){
			$results = $cache->call('show_rules', $directory, $template_name);
		} else{
			$results = show_rules($directory, $template_name);
		}
		boss_show_cached_result($results);
		break;
		}

	case 'show_content':
		{
		if($mainframe->getCfg('caching') == 1){
			$results = $cache->call('show_content', $contentid, $catid, $directory, $template_name);
		} else{
			$results = show_content($contentid, $catid, $directory, $template_name);
		}
		boss_show_cached_result($results['params']);

		$content_userid = $results['contentid'];

		// increment views. views from item author are not counted to prevent highclicking views of own item
		if($my->id <> $content_userid){
			$sql = "UPDATE #__boss_" . $directory . "_contents SET views = LAST_INSERT_ID(views+1) WHERE id = $contentid";
			$database->setQuery($sql);

			if($database->getErrorNum()){
				echo $database->stderr();
			} else{
				$database->query();
			}
		}
		break;
		}

	case 'emailform':
		{
		$cache->call('emailform', $contentid, $directory, $template_name);
		break;
		}

	case 'emailsend':
		{
		emailsend($directory, $template_name);
		break;
		}

	case 'login':
		{
		login_form($directory, $template_name);
		break;
		}

	case 'write_content':
		{
		write_content($contentid, $catid, $directory, $template_name);
		break;
		}

	case 'save_content':
		{
		mosCache::cleanCache('com_boss');
		save_content($directory);

		break;
		}

	case 'save_vote':
		{
		mosCache::cleanCache('com_boss');
		$rating = BossPlugins::get_plugin($directory, $conf->rating, 'ratings');
		$rating->save_vote($directory);
		break;
		}

	case 'save_review':
		{
		mosCache::cleanCache('com_boss');
		if($conf->comment_sys == 1)
			$comment_sys = 'defaultComment'; else
			$comment_sys = 'jcomment';
		$comments = BossPlugins::get_plugin($directory, $comment_sys, 'comments');
		$comments->save_review($directory);


		break;
		}

	case 'delete_content':
		{
		mosCache::cleanCache('com_boss');
		delete_content($contentid, $directory, $template_name);
		break;
		}

	case 'show_result':
		if(($catid == 0) || (!isset($catid)))
			$results = show_all($text_search, $name_search, $order, $limitstart, $directory, $template_name); else
			$results = show_category($catid, $text_search, $name_search, $order, $limitstart, $directory, $template_name);
		boss_show_cached_result($results);
		break;

	case 'show_all':
		{
		if($mainframe->getCfg('caching') == 1){
			$results = $cache->call('show_all', $text_search, $name_search, $order, $limitstart, $directory, $template_name);
		} else{
			$results = show_all($text_search, $name_search, $order, $limitstart, $directory, $template_name);
		}
		boss_show_cached_result($results);
		break;
		}


	case 'show_frontpage':
		{
		if($mainframe->getCfg('caching') == 1){
			$results = $cache->call('show_frontpage', $text_search, $name_search, $order, $limitstart, $directory, $template_name);
		} else{
			$results = show_frontpage($text_search, $name_search, $order, $limitstart, $directory, $template_name);
		}
		boss_show_cached_result($results);
		break;
		}

	case 'show_message_form':
		{
		$cache->call('show_message_form', $contentid, $mode, $directory, $template_name);
		break;
		}

	case 'send_message':
		{
		send_message($mode, $directory);
		break;
		}

	case 'expiration':
		{
		show_expiration($contentid, $directory, $template_name);
		break;
		}

	case 'extend_expiration':
		{
		extend_expiration($contentid, $directory);
		break;
		}

	case 'rss':
		{
		show_rss($catid, $directory);
		return;
		}

	default:
		{
		if($mainframe->getCfg('caching') == 1){
			$results = $cache->call('front', $directory, $template_name);
		} else{
			$results = front($directory, $template_name);
		}
		boss_show_cached_result($results);
		break;
		}
}

function show_search($catid, $directory, $template_name){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	//get configuration
	$conf = getConfig($directory);
	//права пользователя
	if($conf->allow_rights){
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
		$rights->bind_rights(@$conf->rights);
		$my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
		if(!$rights->allow_me('show_search', $my->groop_id)){
			echo '<div class="error">' . $rights->error('show_search') . '</div>';
			return;
		}
	}

	$params = array();
	$paths = array();
	$jDirectoryHtmlClass = new boss_html();
	$field_values = boss_helpers::loadFieldValues($directory);

	$content_type = mosGetParam($_REQUEST, 'content_types', 0);

	$where = ($content_type > 0) ? "AND (FIND_IN_SET($content_type, `catsid`) > 0 OR `catsid` = ',-1,')" : '';

	// Dynamic Page Title
	$params['title'] = BOSS_PAGE_TITLE . BOSS_ADVANCED_SEARCH;

	$database = database::getInstance();
	$fields_searchable = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields " . "WHERE searchable = 1 AND published = 1 AND profile = 0 " . $where)->loadObjectList();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	$content_types = $database->setQuery("SELECT * FROM #__boss_" . $directory . "_content_types WHERE published = 1")->loadObjectList();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;

	$cats = boss_helpers::get_cattree($directory, $conf, $conf->empty_cat);

	$jDirectoryHtmlClass->user = $my;
	$jDirectoryHtmlClass->conf = $conf;
	$jDirectoryHtmlClass->fields = $fields_searchable;
	$jDirectoryHtmlClass->field_values = $field_values;
	$jDirectoryHtmlClass->category->id = $catid;
	$jDirectoryHtmlClass->categories = $cats;
	$jDirectoryHtmlClass->directory = $directory;
	$jDirectoryHtmlClass->template_name = $template_name;
	$jDirectoryHtmlClass->content_types = $content_types;
	$jDirectoryHtmlClass->content_type = $content_type;
	$jDirectoryHtmlClass->content->id = 0;
	ob_start();
	$jDirectoryHtmlClass->displaySearch();
	$params['page_body'] = ob_get_contents();
	ob_end_clean();

	return $params;
}

function show_all($text_search, $name_search, $order, $limitstart, $directory, $template_name){

	//get configuration
	$conf = getConfig($directory);

	$mainframe = mosMainFrame::getInstance();
	//права пользователя
	if($conf->allow_rights){
		$my = $mainframe->getUser();
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
		$rights->bind_rights(@$conf->rights);
		$my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
		if(!$rights->allow_me('show_all', $my->groop_id)){
			echo '<div class="error">' . $rights->error('show_all') . '</div>';
			return;
		}
	}

	$paths = null;
	$params = array();
	$jDirectoryHtmlClass = new boss_html();

	//Pathway
	$list = boss_helpers::loadCats($directory);

	$subcats = boss_helpers::get_subpathlist($list, 0, $order, $directory);
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;
	$jDirectoryHtmlClass->subcats = $subcats;
	//List
	if(isset($text_search)){
		$url_text_search = "&amp;text_search=" . $text_search;
	}

	$url = "index.php?option=com_boss&amp;task=show_all" . $url_text_search . "&amp;directory=" . $directory . "&amp;order=" . $order;

	ob_start();
	$params = boss_helpers::show_list(BOSS_LIST_TEXT, "", $url, "show_all", "1", $text_search, $name_search, $order, 0, $limitstart, 0, $jDirectoryHtmlClass, $directory, $template_name);
	$params['page_body'] = ob_get_contents();
	ob_end_clean();
	// Dynamic Page Title
	$params['title'] = BOSS_PAGE_TITLE . BOSS_LIST_TEXT;
	return $params;
}

function show_frontpage($text_search, $name_search, $order, $limitstart, $directory, $template_name){

	//get configuration
	$conf = getConfig($directory);

	$mainframe = mosMainFrame::getInstance();
	//права пользователя
	if($conf->allow_rights){
		$my = $mainframe->getUser();
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
		$rights->bind_rights(@$conf->rights);
		$my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
		if(!$rights->allow_me('show_all', $my->groop_id)){
			echo '<div class="error">' . $rights->error('show_all') . '</div>';
			return;
		}
	}

	$paths = null;
	$params = array();
	$jDirectoryHtmlClass = new boss_html();

	//Pathway
	$list = boss_helpers::loadCats($directory);

	$subcats = boss_helpers::get_subpathlist($list, 0, $order, $directory);
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;
	$jDirectoryHtmlClass->subcats = $subcats;
	//List
	if(isset($text_search)){
		$url_text_search = "&amp;text_search=" . $text_search;
	}

	$url = "index.php?option=com_boss&amp;task=show_all" . $url_text_search . "&amp;directory=" . $directory . "&amp;order=" . $order;

	ob_start();
	$params = boss_helpers::show_list(BOSS_LIST_TEXT, "", $url, "show_frontpage", "1", $text_search, $name_search, $order, 0, $limitstart, 0, $jDirectoryHtmlClass, $directory, $template_name);
	$params['page_body'] = ob_get_contents();
	ob_end_clean();
	// Dynamic Page Title
	$params['title'] = BOSS_PAGE_TITLE . BOSS_LIST_TEXT;

	return $params;
}

function show_user($userid, $text_search, $order, $limitstart, $directory, $template_name){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	//get configuration
	$conf = getConfig($directory);
	//права пользователя
	if($conf->allow_rights){
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
		$rights->bind_rights(@$conf->rights);
		$my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;

		if(!$rights->allow_me('show_user_content', $my->groop_id)){
			if(!($my->id == $userid && $rights->allow_me('show_my_content', $my->groop_id))){
				echo '<div class="error">' . $rights->error('show_user_content') . '</div>';
				return;
			}
		}
	}

	$params = array();

	$paths = null;
	$jDirectoryHtmlClass = new boss_html();

	//PathWay
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;

	if($userid == 0){
		$jDirectoryHtmlClass->conf = $conf;
		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayLoginForm($_SERVER['REQUEST_URI']);
	} else{
		//Dynamic Page Title

		$database = database::getInstance();
		$user = new mosUser($database);
		$user->load($userid);
		$name_list = BOSS_LIST_USER_TEXT . " " . $user->username;

		//List
		if(isset($text_search))
			$url_text_search = "&amp;text_search=" . $text_search;
		$url = "index.php?option=com_boss&amp;task=show_user&amp;userid=" . $userid . $url_text_search . "&amp;directory=$directory&amp;order=" . $order;

		if($my->id == $userid){
			$update_possible = 1;
		} else{
			$update_possible = 0;
		}

		ob_start();
		$params = boss_helpers::show_list($name_list, "", $url, "show_user", "a.userid=$userid", $text_search, '', $order, 0, $limitstart, $update_possible, $jDirectoryHtmlClass, $directory, $template_name);
		$params['page_body'] = ob_get_contents();
		ob_end_clean();
		$params['title'] = BOSS_PAGE_TITLE . $name_list;
	}
	return $params;
}

function show_category($catid, $text_search, $name_search, $order, $limitstart, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();
	$params = array();
	$category = null;
	$search_user = '';
	$jDirectoryHtmlClass = new boss_html();

	// get category-name: #__boss_".$directory."_category
	$database->setQuery("SELECT c.* " . " FROM #__boss_" . $directory . "_categories as c WHERE c.published='1' AND c.id=$catid");
	$database->loadObject($category);

	//get configuration
	$conf = getConfig($directory);
	//права пользователя
	if($conf->allow_rights){
		$my = $mainframe->getUser();
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
		$rights->bind_rights(@$conf->rights);
		$rights->bind_rights(@$category->rights);
		$my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
		if(!$rights->allow_me('show_category', $my->groop_id)){
			echo '<div class="error">' . $rights->error('show_category') . '</div>';
			return;
		}
		if(!$rights->allow_me('show_category_content', $my->groop_id) && $rights->allow_me('show_my_content', $my->groop_id)){
			$search_user = " AND a.userid = '$my->id'";
		} else if(!$rights->allow_me('show_category_content', $my->groop_id) && !$rights->allow_me('show_my_content', $my->groop_id)){
			echo '<div class="error">' . $rights->error('show_category') . '</div>';
			return;
		}
	}

	$cat_name = $category->name;
	$cat_description = $category->description;
	//$parent = $category->parent;

	if($category->template){
		$template_name = $category->template;
	}

	$paths = array();

	$listcats = boss_helpers::loadCats($directory);
	$nb = count($paths);
	$paths[$nb]->text = $conf->name;
	$paths[$nb]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;order=' . $order . '&amp;directory=' . $directory);
	boss_helpers::get_pathlist($listcats, $catid, $cat_name, $paths, $order, $directory);
	for($i = $nb; $i >= 0; $i--){
		$mainframe->appendPathWay('<a href ="' . $paths[$i]->link . '">' . $paths[$i]->text . '</a>');
	}

	$subcats = boss_helpers::get_subpathlist($listcats, $catid, $order, $directory);
	$jDirectoryHtmlClass->paths = $paths;
	$jDirectoryHtmlClass->subcats = $subcats;

	//List
	$list[] = $catid;
	boss_helpers::recurse_search($listcats, $list, $catid);
	$listids = implode(',', $list);

	$search = "cch.category_id IN ($listids)" . $search_user;
	if(isset($text_search)){
		$url_text_search = "&amp;text_search=" . $text_search;
	}

	$url = "index.php?option=com_boss&amp;task=show_category&amp;catid=" . $catid . $url_text_search . "&amp;directory=" . $directory;

	ob_start();
	$params = boss_helpers::show_list($cat_name, $cat_description, $url, "show_category", $search, $text_search, $name_search, $order, $catid, $limitstart, 0, $jDirectoryHtmlClass, $directory, $template_name, array(), $category->content_types);
	$params['page_body'] = ob_get_contents();
	ob_end_clean();
	// Dynamic Page Title
	$params['title'] = (!empty($category->meta_title)) ? $category->meta_title : $cat_name;
	//Dynamic Page meta
	$params['description'] = (!empty($category->meta_desc)) ? $category->meta_desc : substr(strip_tags($category->description), 0, 200);
	$params['keywords'] = $category->meta_keys;
	return $params;
}

function search_tags($tag, $order, $limitstart, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	$url_text_search = null;
	$paths = null;
	$jDirectoryHtmlClass = new boss_html();

	$tag = urldecode(cutLongWord($tag, 255));

	//header
	$header = BOSS_TAGS_HEADER . ' &laquo;' . $tag . '&raquo;';

	// Dynamic Page Title
	$mainframe->SetPageTitle($header);

	//get configuration
	$conf = getConfig($directory);

	//Pathway
	$list = boss_helpers::loadCats($directory);
	$subcats = boss_helpers::get_subpathlist($list, 0, $order, $directory);
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;
	$jDirectoryHtmlClass->subcats = $subcats;

	$database->setQuery("SELECT `obj_id` FROM #__content_tags WHERE `tag` = '" . $tag . "' AND `obj_type` = 'com_boss_" . $directory . "'");
	$tagContentIds = $database->loadResultArray();
	//List
	$url = "index.php?option=com_boss&amp;task=show_all" . $url_text_search . "&amp;directory=" . $directory . "&amp;order=" . $order;
	$params = boss_helpers::show_list($header, "", $url, "show_all", "1", '', '', $order, 0, $limitstart, 0, $jDirectoryHtmlClass, $directory, $template_name, $tagContentIds);
	return $params;
}

function search_alpha($alpha, $order, $limitstart, $directory, $template_name){
	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	$url_text_search = null;
	$paths = null;
	$jDirectoryHtmlClass = new boss_html();

	//header
	$header = BOSS_ALPHA_HEADER . ' &laquo;' . $alpha . '&raquo;';

	// Dynamic Page Title
	$mainframe->SetPageTitle($header);

	//get configuration
	$conf = getConfig($directory);

	//Pathway
	$list = boss_helpers::loadCats($directory);
	$subcats = boss_helpers::get_subpathlist($list, 0, $order, $directory);
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');

	$jDirectoryHtmlClass->paths = $paths;
	$jDirectoryHtmlClass->subcats = $subcats;

	$alpha = Jstring::strtolower($alpha);

	$database->setQuery("SELECT `id` FROM #__boss_" . $directory . "_contents WHERE LOWER(`name`) LIKE '" . $alpha . "%'");
	$alphaContentIds = $database->loadResultArray();

	//List
	$url = "index.php?option=com_boss&amp;task=show_all" . $url_text_search . "&amp;directory=" . $directory . "&amp;order=" . $order;
	$params = boss_helpers::show_list($header, "", $url, "show_all", "1", '', '', $order, 0, $limitstart, 0, $jDirectoryHtmlClass, $directory, $template_name, $alphaContentIds);
	return $params;
}

function show_message_form($contentid, $mode, $directory, $template_name){
	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();
	$content = null;
	$conf = null;

	$jDirectoryHtmlClass = new boss_html();

	$database->setQuery("SELECT a.* FROM #__boss_" . $directory . "_contents as a WHERE a.id=$contentid")->loadObject($content);

	$user = new mosUser($database);
	if($my->id > 0){
		$user->load($my->id);
	}

	if($mode == 0){ //Email
		//get configuration
		$conf = getConfig($directory);

		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayMessageForm($content, $user, $mode, $conf->allow_attachement);
	} else{ // PMS
		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayMessageForm($content, $user, $mode, 0);
	}
}

function send_message($mode, $directory){

	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();
	$contentid = intval(mosGetParam($_POST, 'contentid', 0));
	$url = JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_content&amp;contentid=$contentid&amp;directory=$directory");

	$name = mosGetParam($_POST, 'name', "");
	$from_email = mosGetParam($_POST, 'email', "");
	$title = mosGetParam($_POST, 'title', "");
	$body = mosGetParam($_POST, 'body', "");

	$userid = $database->setQuery("SELECT `userid` FROM #__boss_" . $directory . "_contents as a WHERE a.id=$contentid LIMIT 1")->loadResult();
	if(empty($userid)){
		mosRedirect($url, BOSS_MESSAGE_NOT_SENT);
	}

	$to_email = $database->setQuery("SELECT `email` FROM #__users as a WHERE a.id=$userid")->loadResult();
	if(empty($to_email)){
		mosRedirect($url, BOSS_MESSAGE_NOT_SENT);
	}

	if($mode == 1){
		$_MAMBOTS = mosMambotHandler::getInstance();
		$_MAMBOTS->loadBotGroup('com_boss');
		$results = $_MAMBOTS->trigger('onSendPMS', array($userid, $my->id, $title, $body), false);
	} else{
		if(!empty($_FILES['attach_file']['tmp_name'])){
			$directory = ini_get('uplocontent_tmp_dir') . "";
			if($directory == "")
				$directory = ini_get('session.save_path') . "";

			$filename = $directory . "/" . basename($_FILES['attach_file']['name']);
			rename($_FILES['attach_file']['tmp_name'], $filename);
			mosMail($from_email, $name, $to_email, $title, $body, 1, NULL, NULL, $filename);
		} else{
			mosMail($from_email, $name, $to_email, $title, $body, 1);
		}
	}
	mosRedirect($url, BOSS_MESSAGE_SENT);
}

function show_content($contentid, $catid, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$params = array();
	$content = null;
	$jDirectoryHtmlClass = new boss_html();

	//get value fields
	$field_values = boss_helpers::loadFieldValues($directory);

	//get configuration
	$conf = getConfig($directory);
	$plugins = BossPlugins::get_plugins($directory, 'fields');

	$rating = BossPlugins::get_plugin($directory, $conf->rating, 'ratings');
	$ratingQuery = $rating->queryString($directory, $conf);

	if($conf->comment_sys == 1)
		$comment_sys = 'defaultComment'; else
		$comment_sys = 'jcomment';
	$comments = BossPlugins::get_plugin($directory, $comment_sys, 'comments');

	$fields = $database->setQuery("SELECT f.* FROM #__boss_" . $directory . "_fields AS f WHERE f.published = 1")->loadObjectList('name');
	//Show Ad

	if(($conf->show_contact == 1) && ($my->id == "0")){
		$show_contact = 0;
	} else if(($conf->show_contact == 1) && ($my->id > 0)){
		$show_contact = 1;
	} else if($conf->show_contact == 0){
		$show_contact = 1;
	} else{
		$show_contact = 0;
	}

	$q = "SELECT a.*, a.userid as user_id, p.name as parent, p.id as parentid, c.rights as rights, ";
	if($show_contact == 1){
		$q .= "profile.*, \n";
		$q .= "u.email as user_email, u.name as user_fio, \n";
	}

	$q .= $ratingQuery['fields'];

	$q .= " c.name as cat, c.id as catid, u.username as user " . "FROM #__boss_" . $directory . "_contents as a ";

	if($show_contact == 1){
		$q .= "LEFT JOIN #__boss_" . $directory . "_profile as profile ON a.userid = profile.userid \n";
	}

	$q .= $ratingQuery['tables'];

	$q .= "LEFT JOIN  #__boss_" . $directory . "_content_category_href AS cch ON a.id = cch.content_id " . "LEFT JOIN #__users as u ON a.userid = u.id " . "LEFT JOIN #__boss_" . $directory . "_categories as c ON cch.category_id = c.id " . "LEFT JOIN #__boss_" . $directory . "_categories as p ON c.parent = p.id " . "WHERE a.id=$contentid ";

	if($catid > 0)
		$q .= "AND c.id=$catid ";

	$q .= "GROUP by a.id";

	$database->setQuery($q)->loadObject($content);

	$perms = null;
	//права пользователя
	if($conf->allow_rights){
		$my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
		if($catid > 0){
			$rights->bind_rights($content->rights);
		} else{
			$rights->bind_rights(@$conf->rights);
		}
		$perms = $rights->loadRights(array('show_my_content', 'show_all_content', 'edit_user_content', 'edit_all_content', 'delete_user_content', 'delete_all_content'), $my->groop_id);
		if(!($perms->show_all_content || ($perms->show_my_content && $my->id == $content->user_id))){
			echo '<div class="error">' . $rights->error('show_category') . '</div>';
			return;
		}
	}

	// Dynamic Page Title
	$params['title'] = (isset($content->meta_title)) ? $content->meta_title : $content->cat . " - " . $content->name;
	//Dynamic Page meta
	$params['description'] = $content->meta_desc;
	$params['keywords'] = $content->meta_keys;

	//подключаем некешируемую информацию из плагинов.
	foreach($fields as $field){
		if(method_exists($plugins[$field->type], 'addInHead')){
			$fv = isset($field_values[$field->fieldid]) ? $field_values[$field->fieldid] : array();
			$params = array_merge_recursive($params, $plugins[$field->type]->addInHead($fields, $fv, $directory));
		}
	}

	if($show_contact == 1){ //вычисляем название полей профиля пользователя для идентификации их в контенте.
		$database->setQuery("SELECT f.name, f.title FROM #__boss_" . $directory . "_fields AS f WHERE f.profile = 1 ORDER BY f.ordering");
		$profileFields = $database->loadObjectList();
	} else{
		$profileFields = array();
	}
	$jDirectoryHtmlClass->profileFields = $profileFields;

	//PathWay
	$listcats = boss_helpers::loadCats($directory);

	$paths = array();

	$nb = count($paths);
	$paths[$nb]->text = $conf->name;
	$paths[$nb]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	boss_helpers::get_pathlist($listcats, $content->catid, $content->cat, $paths, 0, $directory);
	for($i = $nb; $i >= 0; $i--){
		$mainframe->appendPathWay('<a href ="' . $paths[$i]->link . '">' . $paths[$i]->text . '</a>');
	}
	$jDirectoryHtmlClass->paths = $paths;

	//теги
	require_once(JPATH_BASE . '/includes/libraries/tags/tags.php');
	$jDirectoryContentTags = new contentTags($database);
	$obj = null;
	$obj->id = $contentid;
	$obj->obj_type = 'com_boss_' . $directory;
	$tags = $jDirectoryContentTags->load_by($obj);
	$tags = boss_helpers::arr_to_links($directory, $tags, ', ');
	$jDirectoryHtmlClass->tags = $tags;
	unset($tags, $obj);
	//конец тегов

	$database->setQuery("SELECT * FROM #__boss_" . $directory . "_groups WHERE published = 1 AND template='" . $template_name . "'");
	$groupstemp = $database->loadObjectList('name');

	if(!empty($groupstemp)){
		$groups = array();
		foreach($groupstemp as $grp){
			if((strpos($grp->catsid, ",$catid,") !== false) || (strpos($grp->catsid, ",-1,") !== false)){
				$groups[] = $grp->id;
			}
		}
		$groupids = implode(',', $groups);
	}
	if(empty($groupids))
		$groupids = 0;

	$query = "SELECT g.name as gname,f.* FROM #__boss_" . $directory . "_groupfields as fg \n" . "LEFT JOIN #__boss_" . $directory . "_groups AS g ON fg.groupid = g.id \n" . "LEFT JOIN #__boss_" . $directory . "_fields AS f ON fg.fieldid = f.fieldid \n" . "WHERE g.published = 1 AND g.id IN ($groupids) AND f.published = 1 AND fg.type_tmpl = 'content' \n" . "ORDER BY fg.ordering, f.ordering";
	$database->setQuery($query);
	$fieldsgrouptemp = $database->loadObjectList();

	$jDirectoryHtmlClass->fieldsgroup = array();
	$jDirectoryHtmlClass->fields = array();

	foreach($fieldsgrouptemp as $f){
		if(!isset($jDirectoryHtmlClass->fieldsgroup[$f->gname])){
			$jDirectoryHtmlClass->fieldsgroup[$f->gname] = array();
		}

		if(!isset($jDirectoryHtmlClass->fields[$f->name])){
			$jDirectoryHtmlClass->fields[$f->name] = $f;
		}

		$jDirectoryHtmlClass->fieldsgroup[$f->gname][] = $f;
	}

	$jDirectoryHtmlClass->fields = $fields;
	$jDirectoryHtmlClass->perms = $perms;
	$jDirectoryHtmlClass->field_values = $field_values;
	$conf->show_contact = $show_contact;
	$jDirectoryHtmlClass->conf = $conf;
	$jDirectoryHtmlClass->reviews = $comments->queryStringContent($directory, $conf, $content->id);
	$jDirectoryHtmlClass->comments = $comments;
	$jDirectoryHtmlClass->rating = $rating;
	$jDirectoryHtmlClass->popup = intval(mosGetParam($_GET, 'popup', 0));
	$jDirectoryHtmlClass->plugins = $plugins;
	$jDirectoryHtmlClass->directory = $directory;
	$jDirectoryHtmlClass->template_name = $template_name;

	ob_start();
	$jDirectoryHtmlClass->displayContent($content);
	$params['page_body'] = ob_get_contents();
	ob_end_clean();

	return array('contentid' => $content->id, 'params' => $params);
}

function write_content($contentid, $catid, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();

	//get configuration
	$conf = getConfig($directory);

	$database = database::getInstance();
	$content_type = mosGetParam($_REQUEST, 'content_types', 0);

	$paths = null;
	$user = null;
	$content = null;

	if($contentid > 0){
		$isUpdateMode = 1;
	} else{
		$isUpdateMode = 0;
	}

	// Update Ad ?
	if($contentid > 0){ // edit ad
		// 1. get data
		$database->setQuery("SELECT * FROM #__boss_" . $directory . "_contents WHERE `id`='$contentid' LIMIT 1");
		$database->loadObject($content);
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}

		$content->name = stripslashes($content->name);

		if($catid == 0){
			$catid = (int)mosGetParam($_REQUEST, 'catid', 0);
		}
	}

	//права пользователя
	if($conf->allow_rights){
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
		if($catid > 0){
			$catRights = $database->setQuery("SELECT `rights` FROM #__boss_" . $directory . "_categories WHERE `id` = '$catid' LIMIT 1")->loadResult();
			$rights->bind_rights($catRights);
		} else{
			$rights->bind_rights(@$conf->rights);
		}

		$my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;

		$edit_user_content = $rights->allow_me('edit_user_content', $my->groop_id);
		$edit_all_content = $rights->allow_me('edit_all_content', $my->groop_id);
		$create_content = $rights->allow_me('create_content', $my->groop_id);

		if($contentid > 0 && $content->userid == $my->id && !($edit_user_content || $edit_all_content)){
			echo '<div class="error">' . $rights->error('edit_all_content') . '</div>';
			return;
		} else if($contentid > 0 && $content->userid != $my->id && !$edit_all_content){
			echo '<div class="error">' . $rights->error('edit_all_content') . '</div>';
			return;
		} else if($contentid == 0 && !$create_content){
			echo '<div class="error">' . $rights->error('$create_content') . '</div>';
			return;
		}
	} else{
		if(@$content->userid == $my->id || $my->usertype == 'Super Administrator'){
			$isUpdateMode = 1;
		} else{
			$isUpdateMode = 0;
			$content = null;
		}
	}

	$jDirectoryHtmlClass = new boss_html();
	$errorMsg = mosGetParam($_POST, 'errorMsg', "");


	if(($contentid == 0) && ($my->id != "0") && ($conf->nb_contents_by_user != -1)){
		$database->setQuery("SELECT count(*) FROM #__boss_" . $directory . "_contents as a WHERE a.userid =" . $my->id);
		$nb = $database->loadResult();
		if($nb >= $conf->nb_contents_by_user){
			$redirect_text = sprintf(BOSS_MAX_NUM_CONTENTS_REACHED, $conf->nb_contents_by_user);
			mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;directory=$directory"), $redirect_text);
		}
	}

	//PathWay
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;

	/* submission_type = 1->Account needed */
	if(($conf->submission_type == 1) && ($my->id == "0")){
		$jDirectoryHtmlClass->conf = $conf;
		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayLoginForm($_SERVER['REQUEST_URI']);
	} else{
		//get fields
		$database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE `published` = 1 AND `profile` = 0 ORDER BY `ordering`, `fieldid`");
		$fields = $database->loadObjectList();
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}

		//get value fields
		$field_values = boss_helpers::loadFieldValues($directory);

		/* No need to user query, if errorMsg */
		if($errorMsg == ""){
			if(@$content->userid > 0){
				$uid = $content->userid;
			} else{
				$uid = $my->id;
			}
			$database->setQuery("SELECT p.*,u.* FROM #__users as u " . "LEFT JOIN #__boss_" . $directory . "_profile as p ON u.id = p.userid " . "WHERE u.id=" . $uid);
			$database->loadObject($user);
			$user->userid = $uid;
		}

		if(!isset($content)){
			$content = new jDirectoryContent($database, $directory);
		}

		$content_types = $database->setQuery("SELECT id, name FROM #__boss_" . $directory . "_content_types WHERE `published` = 1 ORDER BY `ordering`")->loadObjectList();
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}

		$tree = boss_helpers::get_cattree($directory, $conf, 0, 'write', $isUpdateMode);
		$jDirectoryHtmlClass->content = $content;
		$jDirectoryHtmlClass->content_types = $content_types;
		$jDirectoryHtmlClass->content_type = $content_type;
		$jDirectoryHtmlClass->user = $user;
		$jDirectoryHtmlClass->conf = $conf;
		$jDirectoryHtmlClass->conf->isUpdateMode = $isUpdateMode;
		$jDirectoryHtmlClass->errorMsg = $errorMsg;
		$jDirectoryHtmlClass->fields = $fields;
		$jDirectoryHtmlClass->field_values = $field_values;
		$jDirectoryHtmlClass->category->id = $catid;
		$jDirectoryHtmlClass->categories = $tree;
		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->plugins = BossPlugins::get_plugins($directory, 'fields');
		$jDirectoryHtmlClass->displayWriteForm();
	}
	return true;
}

function save_content($directory){

	global $mosConfig_mailfrom, $mosConfig_fromname;

	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$row = new jDirectoryContent($database, $directory);
	$catid = (int)mosGetParam($_POST, 'category', 0);

	//get configuration
	$conf = getConfig($directory);

	if($conf->secure_new_content == 1 && $my->id == 0){
		session_name(mosMainFrame::sessionCookieName());
		session_start();
		$captcha = strval(mosGetParam($_POST, 'captcha', null));
		$captcha_keystring = mosGetParam($_SESSION, 'captcha_keystring');
		if($captcha_keystring !== $captcha){
			$errorMsg = "bad_captcha";

			$url = JSef::getUrlToSef("index.php?option=com_boss&task=write_content&catid=$catid&amp;directory=$directory&errorMsg=$errorMsg");
			echo "<form name='form' action='" . $url . "' method='post'>";
			foreach($_POST as $key => $val){
				echo "<input type='hidden' name='" . $key . "' value='" . stripslashes($val) . "'>";
			}
			echo "<input type='hidden' name='errorMsg' value='$errorMsg'>";
			echo '</form>';
			echo '<script language="JavaScript">';
			echo 'document.form.submit()';
			echo '</script>';
			return false;
		}
		session_unset();
		session_write_close();
	}

	$id = (int)mosGetParam($_POST, 'id', 0);

	if(($id == 0) && ($my->id != "0") && ($conf->nb_contents_by_user != -1)){
		$database->setQuery("SELECT count(*) FROM #__boss_" . $directory . "_contents as a WHERE a.userid =" . $my->id);
		$nb = $database->loadResult();
		if($nb >= $conf->nb_contents_by_user){
			$redirect_text = sprintf(BOSS_MAX_NUM_CONTENTS_REACHED, $conf->nb_contents_by_user);
			mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;directory=$directory"), $redirect_text);
		}
	}

	if(($conf->submission_type == 0) && ($my->id == 0)){
		$username = mosGetParam($_POST, 'username', "");
		$password = mosGetParam($_POST, 'password', "");
		$email = mosGetParam($_POST, 'email', "");
		$errorMsg = boss_helpers::check_account($username, $password, $email, $userid, $conf);
		if(isset($errorMsg)){
			$catid = (int)mosGetParam($_POST, 'category', 0);
			$url = JSef::getUrlToSef("index.php?option=com_boss&task=write_content&catid=$catid&amp;directory=$directory");
			echo "<form name='form' action='" . $url . "' method='post'>";

			foreach($_POST as $key => $val){
				echo "<input type='hidden' name='$key' value='" . stripslashes($val) . "'>";
			}
			echo "<input type='hidden' name='errorMsg' value='$errorMsg'>";
			echo '</form>';
			echo '<script language="JavaScript">';
			echo 'document.form.submit()';
			echo '</script>';
			return false;
		}

		$row->userid = $userid;
	} else{
		$row->userid = $my->id;
	}


	//get fields
	$database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE published = 1 AND profile = 0");
	$fields = $database->loadObjectList();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	$isUpdateMode = (int)mosGetParam($_POST, 'isUpdateMode', 0);

	//Save Field
	$redirect_text = $row->save($directory, $fields, $conf, $isUpdateMode);
	if((($conf->send_email_on_new == 1) && ($isUpdateMode == 0)) || (($conf->send_email_on_update == 1) && ($isUpdateMode == 1))){
		$title = mosGetParam($_POST, "name", "");
		$body = '';
		foreach($fields as $field){
			if($field->searchable == 1){
				$body = '<div>';
				$body .= "<strong>" . $field->title . "</strong><br/>";
				$body .= mosGetParam($_POST, $field->name, "");
				$body = '</div>';
			}
		}

		if($isUpdateMode == 1){
			$subject = BOSS_EMAIL_UPDATE . $title;
		} else{
			$subject = BOSS_EMAIL_NEW . $title;
		}
		mosMail($mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom, $subject, $body, 1);
	}
	return true;
}

function manage_expiration($directory, $conf, $fileCron, $template_name){
	global $mosConfig_mailfrom, $mosConfig_fromname;

	if($conf->expiration == 1){

		$database = database::getInstance();
		$q = "SELECT c.id, u.email, c.name " . "FROM #__boss_" . $directory . "_contents as c, " . "#__users as u " . "WHERE DATE_SUB(date_created, INTERVAL " . $conf->recall_time . " DAY) < (CURDATE() - " . $conf->content_duration . ") " . "AND u.id = c.userid";
		$contents = $database->setQuery($q)->loadObjectList();
		if($database->getErrorNum()){
			echo $database->stderr();
			return false;
		}
		if(isset($contents)){
			foreach($contents as $content){
				if($conf->recall == 1){
					$subject = BOSS_EMAIL_EXPIRATION . ' ' . $content->name;
					$link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory . '&amp;task=expiration&amp;contentid=' . $content->id);
					$href = "<a href='$link'>$link</a>";
					$body = BOSS_EMAIL_EXPIRATION . ' ' . $content->name;
					$body .= $conf->recall_text;
					$body .= sprintf(BOSS_RENEW_CONTENT_MAIL, $content->name, $href);
					mosMail($mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom, $subject, $body, 1);
				}
				delete_content($content->id, $directory, $template_name);
			}
			mosCache::cleanCache('com_boss');
		}
	}
	$last_cron_date = '<?php $last_cron_date=' . date("Ymd") . ';?>';
	file_put_contents($fileCron, $last_cron_date);
	return true;
}

function show_expiration($contentid, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	$content = null;
	// get configuration
	$conf = getConfig($directory);

	$query = "SELECT DISTINCT a.*, p.name as parent, p.id as parentid, c.name as cat, c.id as catid \n" . "FROM #__boss_" . $directory . "_contents as a \n" . "LEFT JOIN #__boss_" . $directory . "_content_category_href as cch ON cch.content_id = a.id \n" . "LEFT JOIN #__users as u ON a.userid = u.id \n" . "LEFT JOIN #__boss_" . $directory . "_categories as c ON cch.category_id = c.id \n" . "LEFT JOIN #__boss_" . $directory . "_categories as p ON c.parent = p.id \n" . "WHERE a.id=$contentid and c.published LIMIT 1";
	$database->setQuery($query)->loadObject($content);

	if($my->id == 0){
		$return = 'index.php?option=com_boss&task=expiration&directory=' . $directory . '&contentid=' . $contentid;
		$jDirectoryHtmlClass = new boss_html();

		//PathWay
		$paths = array();
		$paths[0]->text = $conf->name;
		$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
		$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
		$jDirectoryHtmlClass->paths = $paths;

		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayLoginForm($return);
	} else{
		boss_html::show_expiration($content, $conf);
	}
}

function extend_expiration($contentid, $directory){

	$database = database::getInstance();

	$q = "UPDATE #__boss_" . $directory . "_contents SET date_created = '" . date('Y-m-d H:i:s') . "' WHERE id=$contentid";
	$database->setQuery($q)->query();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}
	mosCache::cleanCache('com_boss');
	mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;directory=$directory"), BOSS_CONTENT_RESUBMIT);

	return true;
}

function delete_content($contentid, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	//get configuration
	$conf = getConfig($directory);
	$paths = null;
	$content = null;
	$jDirectoryHtmlClass = new boss_html();

	if($my->id == "0"){ // user not logged in
		//get configuration
		$conf = getConfig($directory);

		//PathWay
		$paths[0]->text = $conf->name;
		$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
		$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
		$jDirectoryHtmlClass->paths = $paths;
		$jDirectoryHtmlClass->conf = $conf;
		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayLoginForm($_SERVER['REQUEST_URI']);
	} else{ // user logged in
		$mode = mosGetParam($_GET, 'mode', "");
		if($mode == "confirm"){

			$database->setQuery("SELECT * FROM #__boss_" . $directory . "_contents WHERE id=$contentid");
			$database->loadObject($content);
			if($database->getErrorNum()){
				echo $database->stderr();
				return false;
			}

			if(($content->userid == $my->id) || ($my->id == 62)){
				$content = new jDirectoryContent($database, $directory);
				$content->load($contentid);
				if($content != null){
					$content->delete($directory, $conf);
				}
			}
			mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_user&amp;directory=$directory", ''));
		} else{

			$database->setQuery("SELECT * FROM #__boss_" . $directory . "_contents WHERE id=$contentid");
			$database->loadObject($content);
			if($database->getErrorNum()){
				echo $database->stderr();
				return false;
			}
			//PathWay
			$paths[0]->text = $conf->name;
			$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
			$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
			$jDirectoryHtmlClass->paths = $paths;
			$jDirectoryHtmlClass->user->name = $my->username;
			$jDirectoryHtmlClass->content = $content;
			$jDirectoryHtmlClass->directory = $directory;
			$jDirectoryHtmlClass->template_name = $template_name;
			$jDirectoryHtmlClass->displayConfirmation();
		}
	} // user logged in
	return true;
}

function show_profile($userid, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$database = database::getInstance();

	//get configuration
	$conf = getConfig($directory);
	$paths = null;
	$user = null;

	$jDirectoryHtmlClass = new boss_html();
	$catid = (int)mosGetParam($_POST, 'category', 0);

	//PathWay
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;

	if($userid == "0"){
		$jDirectoryHtmlClass->conf = $conf;
		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayLoginForm($_SERVER['REQUEST_URI']);
	} else{
		$database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields as f " . "WHERE f.profile = 1 AND f.published = 1");
		$fields = $database->loadObjectList();

		$database->setQuery("SELECT p.*,u.* FROM #__boss_" . $directory . "_profile as p " . "LEFT JOIN #__users as u ON p.userid = u.id " . "WHERE userid = $userid");

		$database->loadObject($user);

		if(!isset($user)){

			$database->setQuery("INSERT INTO #__boss_" . $directory . "_profile (userid) VALUES ('$userid')");
			$database->query();
			$database->setQuery("SELECT p.*,u.* FROM #__boss_" . $directory . "_profile as p " . "LEFT JOIN #__users as u ON p.userid = u.id " . "WHERE userid = $userid");
			$database->loadObject($user);
		}

		$jDirectoryHtmlClass->field_values = boss_helpers::loadFieldValues($directory);
		$jDirectoryHtmlClass->plugins = BossPlugins::get_plugins($directory, 'fields');
		$jDirectoryHtmlClass->fields = $fields;
		$jDirectoryHtmlClass->user = $user;
		$jDirectoryHtmlClass->directory = $directory;
		$jDirectoryHtmlClass->template_name = $template_name;
		$jDirectoryHtmlClass->displayProfile();
	}
}

function save_profile($directory){

	josSpoofCheck();

	$mainframe = mosMainFrame::getInstance();
	$my = $mainframe->getUser();
	$database = database::getInstance();

	if($my->id == 0){
		return;
	}

	$catid = (int)mosGetParam($_POST, 'category', 0);

	$row = new mosUser($database);
	$row->load($my->id);
	$row->orig_password = $row->password;

	$password = mosGetParam($_POST, 'password', "");
	$verifyPass = mosGetParam($_POST, 'verifyPass', "");
	if($password != ""){
		if($verifyPass == $password){
			$row->password = md5($password);
		} else{
			echo "<script> alert(\"" . _PASS_MATCH . "\"); window.history.go(-1); </script>\n";
			exit();
		}
	} else{
		// Restore 'original password'
		$row->password = $row->orig_password;
	}

	$row->name = mosGetParam($_POST, 'name', "");
	$row->username = mosGetParam($_POST, 'username', "");
	$row->email = mosGetParam($_POST, 'email', "");

	unset($row->orig_password); // prevent DB error!!

	if(!$row->store()){
		echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields " . "WHERE profile = 1 AND published = 1");
	$fields = $database->loadObjectList();
	$plugins = BossPlugins::get_plugins($directory, 'fields');

	$sql = "UPDATE #__boss_" . $directory . "_profile SET ";

	for($i = 0, $nb = count($fields); $i < $nb; $i++){
		$value = $plugins[$fields[$i]->type]->onFormSave($directory, $fields[$i]->fieldid, $fields[$i], 0, 0);
		$sql .= $fields[$i]->name . " = '" . $value . "'";
		if($i != $nb - 1){
			$sql .= ",";
		}
	}
	$sql .= " WHERE userid = " . $my->id;

	$database->setQuery($sql)->query();

	mosRedirect(JSef::getUrlToSef("index.php?option=com_boss&amp;directory=$directory", BOSS_UPDATE_PROFILE_SUCCESSFULL));
}

function front($directory, $template_name){

	if($directory == 0){
		return false;
	}

	$database = database::getInstance();

	// сюда будем складывать все данные формируемые в функции и попадающие в кеш - тело страницы, title + description + keywords
	$return_params = array();

	//get configuration
	$conf = getConfig($directory);

	//права пользователя
	$rights = null;
	if($conf->allow_rights){
		$rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_front'));
	}

	$jDirectoryHtmlClass = new boss_html();
	$catid = (int)mosGetParam($_POST, 'category', 0);

	$tree = boss_helpers::get_cattree($directory, $conf, $conf->empty_cat);

	$database->setQuery("SELECT a.id, a.name, a.date_created,p.id as parentid, \n" . "p.name as parent,c.id as catid, c.id as category, c.name as cat \n" . "FROM #__boss_" . $directory . "_contents as a, \n" . "#__boss_" . $directory . "_content_category_href as cch, \n" . "#__boss_" . $directory . "_categories as c, \n" . "#__boss_" . $directory . "_categories as p \n" . "WHERE a.id = cch.content_id AND c.parent = p.id \n" . "AND c.id = cch.category_id AND c.published = 1 \n" . "AND a.published = 1 \n" . "ORDER BY a.date_created DESC, a.id DESC \n" . "LIMIT 0, 3");
	$contents = $database->loadObjectList();

	// Dynamic Page Title
	$return_params['title'] = (!empty($conf->meta_title)) ? $conf->meta_title : $conf->name;
	//Dynamic Page meta
	$return_params['description'] = (isset($conf->meta_desc)) ? $conf->meta_desc : Jstring::substr(strip_tags($conf->fronttext), 0, 200);
	$return_params['keywords'] = $conf->meta_keys;


	$jDirectoryHtmlClass->directory_name = $conf->name;
	$jDirectoryHtmlClass->contents = $contents;
	$jDirectoryHtmlClass->conf = $conf;
	$jDirectoryHtmlClass->rights = $rights;
	$jDirectoryHtmlClass->categories = $tree;
	$jDirectoryHtmlClass->directory = $directory;
	$jDirectoryHtmlClass->template_name = $template_name;

	// а тут посложнее - функция цепляет шаблон вывода, поэтому захватим её в буфер и заберём как переменную
	ob_start();
	$jDirectoryHtmlClass->displayFront();
	$boss_page_body = ob_get_contents();
	ob_end_clean();

	// главные текст страницы выдаваемый компонентом
	$return_params['page_body'] = $boss_page_body;

	return $return_params;
}

function show_rules($directory, $template_name){

	$mainframe = mosMainFrame::getInstance();
	$params = array();
	$paths = null;

	$jDirectoryHtmlClass = new boss_html();
	$catid = (int)mosGetParam($_POST, 'category', 0);

	//get configuration
	$conf = getConfig($directory);
	$params['title'] = BOSS_RULES;
	//PathWay
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass->paths = $paths;
	$jDirectoryHtmlClass->conf->rules_text = $conf->rules_text;
	$jDirectoryHtmlClass->directory = $directory;
	$jDirectoryHtmlClass->template_name = $template_name;
	ob_start();
	$jDirectoryHtmlClass->displayRules();
	$params['page_body'] = ob_get_contents();
	ob_end_clean();

	return $params;
}

function login_form($directory, $template_name){
	$mainframe = mosMainFrame::getInstance();

	$conf = null;
	$paths = null;

	//get configuration
	$conf = getConfig($directory);

	//PathWay
	$paths[0]->text = $conf->name;
	$paths[0]->link = JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory);
	$mainframe->appendPathWay('<a href ="' . $paths[0]->link . '">' . $paths[0]->text . '</a>');
	$jDirectoryHtmlClass = new boss_html();
	$jDirectoryHtmlClass->paths = $paths;
	$jDirectoryHtmlClass->conf = $conf;
	$jDirectoryHtmlClass->directory = $directory;
	$jDirectoryHtmlClass->template_name = $template_name;
	$jDirectoryHtmlClass->displayLoginForm(JSef::getUrlToSef('index.php?option=com_boss&amp;directory=' . $directory));
}

/**
 * Shows the email form for a given content item.
 * @param int The content item id
 */
function emailform($contentid, $directory, $template_name){

	$mainframe = mosMainFrame::getInstance();

	$jDirectoryHtmlClass = new boss_html();

	$catid = (int)mosGetParam($_REQUEST, 'catid', 0);

	$mainframe->setPageTitle(BOSS_SEND_TO_FRIEND);
	$jDirectoryHtmlClass->content->id = $contentid;
	$jDirectoryHtmlClass->directory = $directory;
	$jDirectoryHtmlClass->template_name = $template_name;
	$jDirectoryHtmlClass->template_name = $template_name;
	$jDirectoryHtmlClass->displayEmailForm();
}

/**
 * Shows the email form for a given content item.
 * @param int The content item id
 */
function emailsend($directory){
	$mainframe = mosMainFrame::getInstance();
	$mosConfig_sitename = $mainframe->getCfg('sitename');

	$database = database::getInstance();
	$content = null;
	$contentid = (int)mosGetParam($_POST, 'contentid', 0);
	$database->setQuery("SELECT c.* " . "FROM #__boss_" . $directory . "_contents as c " . "WHERE c.id=$contentid");

	if($database->loadObject($content)){
		$email = strval(mosGetParam($_POST, 'email', ''));
		$yourname = strval(mosGetParam($_POST, 'yourname', ''));
		$youremail = strval(mosGetParam($_POST, 'youremail', ''));
		$subject = strval(mosGetParam($_POST, 'subject', ''));
		$catid = (int)mosGetParam($_REQUEST, 'catid', 0);

		if(empty($subject)){
			$subject = _EMAIL_INFO . ' ' . $yourname;
		}

		if(!$email || !$youremail || (isValidEmail($email) == false) || (isValidEmail($youremail) == false)){
			mosErrorAlert(BOSS_EMAIL_ERR_NOINFO);
		}

		// link sent in email
		$link = JSef::getUrlToSef('index.php?option=com_boss&task=show_content&contentid=' . $contentid . '&directory=' . $directory);
		if(trim(strpos($link, 'index.php')) === '0'){
			$link = JPATH_SITE . '/' . $link;
		}

		// message text
		$msg = sprintf(BOSS_EMAIL_MSG, $yourname, $youremail, $mosConfig_sitename, $subject, $link);

		// mail function
		$success = mosMail($youremail, $yourname, $email, $subject, $msg);
		if(!$success){
			mosErrorAlert(BOSS_EMAIL_ERR_NOINFO);
		}
		echo '<script>window.close();</script>';
	} else{
		mosNotAuth();
		return;
	}
}

function show_rss($catid, $directory){

	global $mosConfig_live_site, $mosConfig_cachepath;
	$database = database::getInstance();

	$category = null;
	// load feed creator class
	mosMainFrame::addLib('feedcreator');

	$info = array();

	// parameter intilization
	$info['date'] = date('r');
	$info['year'] = date('Y');
	$iso = explode('=', _ISO);
	$info['encoding'] = $iso[1];

	$info['link'] = htmlspecialchars($mosConfig_live_site);
	$info['cache'] = 1; //$params->def( 'cache', 1 );
	$info['cache_time'] = 3600; //$params->def( 'cache_time', 3600 );

	$info['count'] = 20; //$params->def( 'count', 20 );
	$info['orderby'] = ''; //$params->def( 'orderby', '' );
	$info['title'] = 'title'; //$params->def( 'title', 'Joomla! powered Site' );
	$info['description'] = 'description'; //$params->def( 'description', 'Joomla! site syndication' );
	$info['image_file'] = 'joomla_rss.png'; //$params->def( 'image_file', 'joomla_rss.png' );
	$info['image_alt'] = 'Powered by Joomla!'; //$params->def( 'image_alt', 'Powered by Joomla!' );
	$info['limit_text'] = 0; //$params->def( 'limit_text', 0 );
	$info['text_length'] = 20; //$params->def( 'text_length', 20 );
	// get feed type from url
	$info['feed'] = strval(mosGetParam($_GET, 'feed', 'RSS2.0'));
	// live bookmarks
	$info['live_bookmark'] = ''; //$params->def( 'live_bookmark', '' );
	$info['bookmark_file'] = ''; //$params->def( 'bookmark_file', '' );
	// set filename for rss feeds
	$info['file'] = "boss__" . $catid . "" . strtolower(str_replace('.', '', $info['feed']));
	$filename = $info['file'] . '.xml';

	// security check to stop server path disclosure
	if(strstr($filename, '/')){
		echo _NOT_AUTH;
		return false;
	}
	$info['file'] = $mosConfig_cachepath . '/' . $filename;

	// load feed creator class
	$rss = new UniversalFeedCreator();
	// load image creator class
	$image = new FeedImage();

	// loads cache file
	if($info['cache']){
		$rss->useCached($info['feed'], $info['file'], $info['cache_time']);
	}

	if($catid == 0){
		$info['title'] = "All Ads";
		$info['description'] = "Description";
		$info['link'] = JSef::getUrlToSef("$mosConfig_live_site/index.php?option=com_boss&amp;directory=$directory");
		$info['rsslink'] = JSef::getUrlToSef("$mosConfig_live_site/index.php?option=com_boss&amp;task=rss&amp;no_html=1&amp;directory=$directory");
		$search = "1";
	} else{
		// get category-name: #__boss_".$directory."_category
		$database->setQuery("SELECT c.id, c.name, c.description, c.parent " . " FROM #__boss_" . $directory . "_categories as c WHERE c.published='1' AND c.id=$catid");
		$database->loadObject($category);

		$info['title'] = $category->name;
		$info['description'] = $category->description;
		$info['link'] = JSef::getUrlToSef("$mosConfig_live_site/index.php?option=com_boss&amp;task=show_category&amp;catid=$catid&amp;directory=$directory");
		$info['rsslink'] = JSef::getUrlToSef("$mosConfig_live_site/index.php?option=com_boss&amp;task=rss&amp;catid=$catid&amp;no_html=1&amp;directory=$directory");

		$linkTarget = JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_category&amp;catid=$catid&amp;directory=$directory");

		//$listcats = boss_helpers::loadCats($directory);

		$list[] = $catid;
		$listids = implode(',', $list);
		$search = "category IN ($listids)";
	}

	//$order_text = "a.date_created DESC ,a.id DESC";
	//$limitstart = 0;

	$database->setQuery("SELECT a.*, p.name as parent, p.id as parentid, c.name as cat, c.id as catid, u.username as user, " . "FROM #__boss_" . $directory . "_contents as a " . "LEFT JOIN #__users as u ON a.userid = u.id " . "LEFT JOIN #__boss_" . $directory . "_categories as c ON a.category = c.id " . "LEFT JOIN #__boss_" . $directory . "_categories as p ON c.parent = p.id " . "WHERE $search and c.published = 1 " . "GROUP BY a.id " . "ORDER BY a.date_created DESC ,a.id DESC ", 0, 20);

	$contents = $database->loadObjectList();

	$rss->title = $info['title'];
	$rss->description = $info['description'];
	$rss->link = $info['link'];
	$rss->syndicationURL = $info['rsslink'];
	$rss->cssStyleSheet = NULL;
	$rss->encoding = $info['encoding'];

	if($info['image']){
		$image->url = $info['image'];
		$image->link = $info['link'];
		$image->title = $info['image_alt'];
		$image->description = $info['description'];
		// loads image info into rss array
		$rss->image = $image;
	}

	//get fields
	$database->setQuery("SELECT * FROM #__boss_" . $directory . "_fields WHERE published = 1");
	$fields = $database->loadObjectList();
	if($database->getErrorNum()){
		echo $database->stderr();
		return false;
	}

	foreach($contents as $content){
		$linkTarget = JSef::getUrlToSef("index.php?option=com_boss&amp;task=show_content&amp;catid=$catid&ampcontentid=" . $content->id . "&amp;directory=$directory");
		$item_title = html_entity_decode(htmlspecialchars($content->name));
		$item_description = '';

		foreach($fields as $field){
			if($field->searchable == 1){
				$item_description = '<div>';
				$item_description .= "<strong>" . $field->title . "</strong><br/>";
				$item_description .= $content->$field->name;
				$item_description .= '</div>';
			}
		}

		$item_description = mosHTML::cleanText($item_description);
		$item_description = html_entity_decode($item_description);

		if($info['limit_text']){
			if($info['text_length']){
				// limits description text to x words
				$item_description_array = explode(' ', $item_description);
				$count = count($item_description_array);
				if($count > $info['text_length']){
					$item_description = '';
					for($a = 0; $a < $info['text_length']; $a++){
						$item_description .= $item_description_array[$a] . ' ';
					}
					$item_description = Jstrint::trim($item_description);
					$item_description .= '...';
				}
			} else{
				// do not include description when text_length = 0
				$item_description = NULL;
			}
		}

		// load individual item creator class
		$item = new FeedItem();
		// item info
		$item->title = $item_title;
		$item->link = $linkTarget;
		$item->description = $item_description;
		$item->source = $info['link'];
		$item->date = date('r', $content->date_created);
		$item->category = $content->parent . ' - ' . $content->cat;

		// loads item info into rss array
		$rss->addItem($item);
	}

	// save feed file
	$rss->saveFeed($info['feed'], $info['file'], 1);
	return true;
}

/**
 * @params - массив некешируемых элементов
 * Названия элементов массива @params
 * @title - заголовок страницы (string)
 * @description - описание страницы (string)
 * @keywords - клюючевые слова (string)
 * @css - каскадные стили (string, array)
 * @js - ява-скрипты (string, array)
 * @custom_head_tag - произвольный тег в голову (string, array)
 * @page_body - тело страницы (string)
 * @custom_script - скрипт, который надо печатать ниже головы (string, array)
 */
function boss_show_cached_result($params){

	$mainframe = mosMainFrame::getInstance();
	// выставляем на страницу наш закешированный  title
	isset($params['title']) ? $mainframe->SetPageTitle($params['title']) : null;
	// и дополнительные мета-тэги
	isset($params['description']) ? $mainframe->addMetaTag('description', $params['description']) : null;
	isset($params['keywords']) ? $mainframe->addMetaTag('keywords', $params['keywords']) : null;

	//ява-скрипты
	if(isset($params['js'])){
		if(is_array($params['js'])){
			foreach($params['js'] as $param){
				$mainframe->addJS($param);
			}
		} else{
			$mainframe->addJS($params['js'], 'js');
		}
	}

	//стили
	if(isset($params['css'])){
		if(is_array($params['css'])){
			foreach($params['css'] as $param){
				$mainframe->addCSS($param);
			}
		} else{
			$mainframe->addCSS($params['css']);
		}
	}

	//произвольный тег в голову
	if(isset($params['custom_head_tag'])){
		if(is_array($params['custom_head_tag'])){
			foreach($params['custom_head_tag'] as $param){
				$mainframe->addCustomHeadTag($param);
			}
		} else{
			$mainframe->addCustomHeadTag($params['custom_head_tag']);
		}
	}

	//скрипт, который надо печатать ниже головы
	if(isset($params['custom_script'])){
		if(is_array($params['custom_script'])){
			foreach($params['custom_script'] as $param){
				echo ($param);
			}
		} else{
			echo ($params['custom_script']);
		}
	}

	// а тут основное содержимое страницы - его просто надо вывести
	if(isset($params['page_body'])){
		echo $params['page_body'];
	}
}

