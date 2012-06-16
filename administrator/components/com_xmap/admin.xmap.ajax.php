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

$acl = gacl::getInstance();

// check access permissions (only superadmins & admins)
if(!($acl->acl_check('administration', 'config', 'users', $my->usertype)) || $acl->acl_check('administration', 'edit', 'users', $my->usertype, 'components', 'com_xmap')){
	mosRedirect('index2.php', _NOT_AUTH);
}

$value = mosGetParam($_REQUEST, 'value', '');

$action = mosGetParam($_REQUEST, 'action', '');

$database = database::getInstance();

global $mosConfig_lang, $xmapComponentURL, $xmapSiteURL, $xmapComponentPath, $xmapAdministratorURL, $xmapLang, $xmapAdministratorPath;

define ('_XMAP_JOOMLA15', 0);
define('_XMAP_MAMBO', 0);

$xmapLang = strtolower($mosConfig_lang);
$xmapComponentPath = JPATH_BASE_ADMIN . '/components/com_xmap';
$xmapAdministratorPath = JPATH_BASE_ADMIN;
$xmapComponentURL = JPATH_SITE . DS . JADMIN_BASE . '/components/com_xmap';
$xmapAdministratorURL = JPATH_SITE . DS . JADMIN_BASE;
$xmapSiteURL = JPATH_SITE;

require_once($xmapComponentPath . '/classes/XmapAdmin.php');

// load settings from database
require_once($xmapComponentPath . '/classes/XmapConfig.php');
require_once($xmapComponentPath . '/admin.xmap.html.php');

require_once(JPATH_BASE . DS . JADMIN_BASE . '/components/com_xmap/classes/XmapCache.php');
require_once(JPATH_BASE . DS . JADMIN_BASE . '/components/com_xmap/classes/XmapPlugin.php');

switch($action){
	case 'add_sitemap':
		$sitemap = new XmapSitemap();
		$sitemap->save();
		XmapAdminHtml::showSitemapInfo($sitemap);
		break;
	case 'delete_sitemap':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		$config = new XmapConfig();
		$config->load();
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		if($config->sitemap_default == $id){
			echo _XMAP_ERROR_DELETE_DEFAULT;
			exit;
		}

		$sitemap = new XmapSitemap();
		$sitemap->load($id);
		if($sitemap->remove()){
			echo 1;
		} else{
			$database->getErrorMsg();
		}
		break;
	case 'copy_sitemap':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			$sitemap->id = NULL;
			$sitemap->name = sprintf(_XMAP_COPY_OF, $sitemap->name);
			$sitemap->save();
			XmapAdminHtml::showSitemapInfo($sitemap);
		}
		break;
	case 'save_property':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		$property = mosGetParam($_REQUEST, 'property', '');
		$value = joostina_api::convert(mosGetParam($_POST, 'value', ''));
		$value = str_replace(array('"', "'", '\\'), '', $value);
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			if(isset($sitemap->$property)){
				$sitemap->$property = $value;
				if($sitemap->save()){
					if($sitemap->save()){
						if($sitemap->usecache){
							XmapCache::cleanCache($sitemap);
						}
						echo 1;
					} else{
						$database->getErrorMsg();
					}
					exit;
				}
			}
		}
		echo _XMAP_MSG_ERROR_SAVE_PROPERTY;
		exit;
		break;
	case 'edit_sitemap_settings':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			// images for 'external link' tagging
			$javascript = 'onchange="changeDisplayImage();"';
			$directory = '/components/com_xmap/images';
			$lists['ext_image'] = mosAdminMenus::Images('ext_image', $sitemap->ext_image, $javascript, $directory);

			// column count selection
			$columns = array(
				mosHTML::makeOption(1, 1),
				mosHTML::makeOption(2, 2),
				mosHTML::makeOption(3, 3),
				mosHTML::makeOption(4, 4)
			);
			$lists['columns'] = mosHTML::selectList($columns, 'columns', 'id="columns" class="inputbox" size="1"', 'value', 'text', $sitemap->columns);

			// get list of menu entries in all menus
			$query = "SELECT id AS value, name AS text, CONCAT( id, ' - ', name ) AS menu FROM #__menu WHERE published != -2 ORDER BY menutype, parent, ordering";
			$database->setQuery($query);
			$exclmenus = $database->loadObjectList();
			$lists['exclmenus'] = mosHTML::selectList($exclmenus, 'excl_menus', 'class="inputbox" size="1"', 'value', 'menu', NULL);
			XmapAdminHtml::showSitemapSettings($sitemap, $lists);
		} else{
			echo _XMAP_MSG_ERROR_LOADING_SITEMAP;
		}
		break;
	case 'save_sitemap_settings':
		$id = intval(mosGetParam($_REQUEST, 'id', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'id', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();

		if($sitemap->load($id)){
			$_POST['menus'] = $sitemap->menus;
			$_POST['name'] = $database->getEscaped(mosGetParam($_POST, 'name', ''));
			$sitemap->bind($_POST);
			if($sitemap->save()){
				if($sitemap->usecache){
					XmapCache::cleanCache($sitemap);
				}
				echo 1;
			} else{
				echo $database->getErrorMsg();
			}
		} else{
			die(_XMAP_INVALID_SID);
		}
		break;
	case 'save_plugin_settings':
		$id = intval(mosGetParam($_REQUEST, 'id', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'id', '')){
			die(_XMAP_INVALID_SID);
		}
		$plugin = new XmapPlugin($database, $id);
		if($plugin->id){
			$params = mosGetParam($_POST, 'params', '');
			if(is_array($params)){
				$plugin->parseParams();
				$txt = array();
				foreach($params as $k=> $v){
					$txt[] = "$k=$v";
				}

				$params = mosParameters::textareaHandling($txt);
				if($plugin->check() && $plugin->store()){
					echo 1;
				} else{
					echo $database->getErrorMsg();
				}
			}
		} else{
			die(_XMAP_INVALID_SID);
		}
		break;
	case 'set_default':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$config = new XmapConfig();
		# $config->load();
		$config->sitemap_default = $id;
		if($config->save()){
			echo 1;
		} else{
			echo $database->getErrorMsg();
		}
		break;
	case 'change_plugin_state':
		$id = intval(mosGetParam($_REQUEST, 'plugin', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'plugin', '')){
			die(_XMAP_INVALID_SID);
		}
		$plugin = new XmapPlugin($database, $id);
		$plugin->published = ($plugin->published ? 0 : 1);
		if($plugin->store()){
			echo $plugin->published;
		} else{
			echo $database->getErrorMsg();
		}
		break;
	case 'clean_cache_sitemap':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			if(XmapCache::cleanCache($sitemap)){
				echo _XMAP_MSG_CACHE_CLEANED;
			} else{
				echo _XMAP_MSG_ERROR_CLEAN_CACHE;
			}

		} else{
			echo $database->getErrorMsg();
		}
		break;
	case 'add_menu_sitemap':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			$menus = $sitemap->getMenus();
			$newMenus = mosGetParam($_REQUEST, 'menus', array());
			$newMenus = joostina_api::convert($newMenus);
			$ordering = count($menus);
			foreach($newMenus as $aMenu){
				if(empty($menus[$aMenu])){
					$menu = new stdclass;
					$menu->show = 1;
					$menu->showXML = 1;
					$menu->ordering = $ordering++;
					$menu->priority = '0.5';
					$menu->changefreq = 'daily';
					$menus[$aMenu] = $menu;
				}
			}
			$sitemap->setMenus($menus);
			if($sitemap->save() && $sitemap->usecache){
				XmapCache::cleanCache($sitemap);
			}
			XmapAdminHtml::printMenusList($sitemap);
		}
		break;
	case 'remove_menu_sitemap':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			$menus = $sitemap->getMenus();
			$menu_delete = mosGetParam($_REQUEST, 'menu', array());
			$newMenus = array();
			foreach($menus as $aMenu => $menu){
				if($aMenu != $menu_delete){
					$newMenus[$aMenu] = $menu;
				}
			}
			$sitemap->setMenus($newMenus);
			if($sitemap->save() && $sitemap->usecache){
				XmapCache::cleanCache($sitemap);
			}
			XmapAdminHtml::printMenusList($sitemap);
		}
		break;
	case 'move_menu_sitemap':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			$menu_move = mosGetParam($_REQUEST, 'menu', array());
			$move = intval(mosGetParam($_REQUEST, 'move', array()));
			$sitemap->orderMenu($menu_move, $move);
			if($sitemap->save() && $sitemap->usecache){
				XmapCache::cleanCache($sitemap);
			}

			XmapAdminHtml::printMenusList($sitemap);
		}
		break;
	case 'get_menus_sitemap':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		if(!$id || $id != mosGetParam($_REQUEST, 'sitemap', '')){
			die(_XMAP_INVALID_SID);
		}
		$sitemap = new XmapSitemap();
		if($sitemap->load($id)){
			XmapAdminHtml::printMenusList($sitemap);
		}
		break;
	case 'menu_options':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		$sitemap = new XmapSitemap();
		if(!$sitemap->load($id)){
			die('Cannot load sitemap');
		}
		$menutype = mosGetParam($_REQUEST, 'menutype', '');
		$menus = $sitemap->getMenus();
		$menu = $menus[$menutype];
		$changefreq = array();
		$changefreq[] = mosHTML::makeOption('always', _XMAP_CFG_CHANGEFREQ_ALWAYS);
		$changefreq[] = mosHTML::makeOption('hourly', _XMAP_CFG_CHANGEFREQ_HOURLY);
		$changefreq[] = mosHTML::makeOption('daily', _XMAP_CFG_CHANGEFREQ_DAILY);
		$changefreq[] = mosHTML::makeOption('weekly', _XMAP_CFG_CHANGEFREQ_WEEKLY);
		$changefreq[] = mosHTML::makeOption('monthly', _XMAP_CFG_CHANGEFREQ_MONTHLY);
		$changefreq[] = mosHTML::makeOption('yearly', _XMAP_CFG_CHANGEFREQ_YEARLY);
		$changefreq[] = mosHTML::makeOption('never', _ALWAYS);
		$lists['changefreq'] = mosHTML::selectList($changefreq, 'changefreq', 'class="inputbox" size="1"', 'value', 'text', $menu->changefreq);
		$priority = array();
		for($i = 0; $i <= 9; $i++){
			$priority[] = mosHTML::makeOption('0.' . $i, '0.' . $i);
		}
		$priority[] = mosHTML::makeOption('1', '1');
		$lists['priority'] = mosHTML::selectList($priority, 'priority', 'class="inputbox" size="1"', 'value', 'text', $menu->priority);
		XmapAdminHtml::showMenuOptions($sitemap, $menu, $lists);
		break;
	case 'save_menu_options':
		$id = intval(mosGetParam($_REQUEST, 'sitemap', ''));
		$sitemap = new XmapSitemap();
		if(!$sitemap->load($id)){
			die('Cannot load sitemap');
		}
		$menutype = mosGetParam($_REQUEST, 'menutype', '');
		$menus = $sitemap->getMenus();
		if(!empty($menus[$menutype])){
			$menu = &$menus[$menutype];
			$menu->show = mosGetParam($_POST, 'show', '');
			$menu->showXML = mosGetParam($_POST, 'showXML', '');
			$menu->priority = mosGetParam($_POST, 'priority', '');
			$menu->changefreq = mosGetParam($_POST, 'changefreq', '');

			# Clean the cache of the sitemap
			$sitemap->setMenus($menus);
			if($sitemap->save()){
				if($sitemap->usecache){
					XmapCache::cleanCache($sitemap);
				}
				echo 1;
			} else{
				echo $database->getErrorMsg();
			}
		}
		break;
	case 'uninstallplugin':
		$id = intval(mosGetParam($_REQUEST, 'plugin', ''));
		if($id != mosGetParam($_REQUEST, 'plugin', '')){ //Security Check!
			die('Cannot load plugin');
		}
		if(xmapUninstallPlugin($id)){
			echo 1;
		}
		break;
	case 'edit_plugin_settings':
		$id = intval(mosGetParam($_REQUEST, 'plugin', ''));
		$plugin = new XmapPlugin($database);
		if($id != mosGetParam($_REQUEST, 'plugin', '') || !$plugin->load($id)){
			die('Cannot load plugin');
		}
		XmapAdminHtml::showPluginSettings($plugin);

		break;
}
