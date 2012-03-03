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

$view = mosGetParam( $_REQUEST, 'view', 'html' );

if ($view == 'xslfile') {
	header('Content-Type: text/xml');
	@readfile(JPATH_BASE.'/components/com_xmap/gss.xsl');
	exit;
}

require_once( JPATH_BASE.DS.JADMIN_BASE.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapConfig.php' );
require_once( JPATH_BASE.DS.JADMIN_BASE.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapSitemap.php' );
require_once( JPATH_BASE.DS.JADMIN_BASE.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapPlugins.php' );
require_once( JPATH_BASE.DS.JADMIN_BASE.DS.'components'.DS.'com_xmap'.DS.'classes'.DS.'XmapCache.php' );

$mainframe = mosMainFrame::getInstance();

$menu = $mainframe->get('menu');

$params = new mosParameters($menu->params);
$params->def('page_title',1);
$params->def('header',$menu->name);

if($params->get('header') == '') {
	$mainframe->SetPageTitle($menu->name,$params);
} else {
	$mainframe->SetPageTitle($params->get('header'),$params);
}

set_robot_metatag($params->get('robots'));

if($params->get('meta_description') != "") {
	$mainframe->addMetaTag('description',$params->get('meta_description'));
} else {
	$mainframe->addMetaTag('description',$mosConfig_MetaDesc);
}
if($params->get('meta_keywords') != "") {
	$mainframe->addMetaTag('keywords',$params->get('meta_keywords'));
} else {
	$mainframe->addMetaTag('keywords',$mosConfig_MetaKeys);
}
if($params->get('meta_author') != "") {
	$mainframe->addMetaTag('author',$params->get('meta_author'));
}

global $xSitemap,$xConfig;
$xConfig = new XmapConfig;
$xConfig->load();

$Itemid = intval(mosGetParam( $_REQUEST, 'Itemid', '' ));

$sitemapid =intval($params->get( 'sitemap','' ));

if (!$sitemapid) { //If the is no sitemap id specificated
	$sitemapid = intval(mosGetParam($_REQUEST,'sitemap',''));
}

if ( !$sitemapid && $xConfig->sitemap_default ) {
	$sitemapid = $xConfig->sitemap_default;
}
$xSitemap = new XmapSitemap();
$xSitemap->load($sitemapid);

if (!$xSitemap->id) {
	echo _XMAP_MSG_NO_SITEMAP;
	return;
}
if ( $view=='xml' ) {
	Header("Content-type: text/xml; charset=UTF-8");
	Header("Content-encoding: UTF-8");
}

global $xmap;

$xmapCache = XmapCache::getCache($xSitemap);
if ($xSitemap->usecache) {
	$config = &$mainframe->config;
	$xmapCache->call('xmapCallShowSitemap',$view,$xSitemap->id,$config->config_locale,$config->config_sef,$menu->name);	// call plugin's handler function
} else {
	xmapCallShowSitemap($view,$xSitemap->id,null,null,$menu->name);
}

unset($menu,$params);

switch ($view) {
	case 'html':
		$xSitemap->views_html++;
		$xSitemap->lastvisit_html = time();
		$xSitemap->save();
		break;

	case 'xml':
		$xSitemap->views_xml++;
		$xSitemap->lastvisit_xml = time();
		$xSitemap->save();

		$scriptname = basename($_SERVER['SCRIPT_NAME']);
		$no_html = intval(mosGetParam($_REQUEST, 'no_html', '0'));
		if ($view=='xml' && $scriptname != 'index2.php' || $no_html != 1) {
			die();
		}
		break;
}

/**
 * Function called to generate and generate the tree. Created specially to
 * use with the cache call method
 * The params locale and sef are only for cache purppses
 */
function xmapCallShowSitemap($view,$sitemapid,$locale='',$sef='',$title='') {
	global $xmapCache,$xSitemap,$xConfig;


	switch( $view ) {
		case 'xml': 	// XML Sitemaps output
			require_once( JPATH_BASE .DS.'components'.DS.'com_xmap'.DS.'xmap.xml.php' );
			$xmap = new XmapXML( $xConfig, $xSitemap );
			$xmap->generateSitemap($view,$xConfig,$xmapCache,$title);
			$xSitemap->count_xml = $xmap->count;
			break;
		default:	// Html output
			$mainframe = mosMainFrame::getInstance();
			require_once( $mainframe->getPath('front_html') );
			$xmap = new XmapHtml( $xConfig, $xSitemap );
			$xmap->generateSitemap($view,$xConfig,$xmapCache,$title);
			$xSitemap->count_html = $xmap->count;
			break;
	}
}


class Xmap {
	/** @var XmapConfig Configuration settings */
	var $config;
	/** @var XmapSitemap Configuration settings */
	var $sitemap;
	/** @var integer The current user's access level */
	var $gid;
	/** @var boolean Is authentication disabled for this website? */
	var $noauth;
	/** @var string Current time as a ready to use SQL timeval */
	var $now;
	/** @var object Access restrictions for user */
	var $access;
	/** @var string Type of sitemap to be generated */
	var $view;
	/** @var string count of links on sitemap */
	var $count=0;

	/** Default constructor, requires the config as parameter. */
	function Xmap( &$config, &$sitemap ) {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
		/* класс работы с правами пользователей */
		mosMainFrame::addLib('gacl');
		$acl = &gacl::getInstance();

		$access = new stdClass();
		$access->canEdit	 = $acl->acl_check( 'action', 'edit', 'users', $my->usertype, 'content', 'all' );
		$access->canEditOwn = $acl->acl_check( 'action', 'edit', 'users', $my->usertype, 'content', 'own' );
		$access->canPublish = $acl->acl_check( 'action', 'publish', 'users', $my->usertype, 'content', 'all' );
		$this->access = &$access;

		$this->noauth 	= mosMainFrame::getInstance()->getCfg( 'shownoauth' );
		$this->gid	= $my->gid;
		$this->now	= (time() - (Jconfig::getInstance()->config_offset * 60 * 60));
		$this->config = &$config;
		$this->sitemap = &$sitemap;
	}

	/** Generate a full website tree */
	function generateSitemap( $type,&$config, &$cache,$title ) {
		$menus = $this->sitemap->getMenus();
		$plugins = XmapPlugins::loadAvailablePlugins();
		$root = array();
		$this->startOutput($menus,$config,$title);
		foreach ( $menus as $menutype => $menu ) {
			if ( ($type == 'html' && !$menu->show) || ($type == 'xml' && !$menu->showXML ) ) {
				continue;
			}

			$node = new stdclass();
			$menu->id = 0;
			$menu->menutype = $menutype;

			$node->uid = $menu->uid = "menu".$menu->id;
			$node->menutype = $menutype;
			$node->ordering = $menu->ordering;
			$node->priority = $menu->priority;
			$node->changefreq = $menu->changefreq;
			$node->browserNav = 3;
			$node->type = 'separator';
			$node->name = $this->getMenuTitle($menutype);	// get the mod_mainmenu title from modules table

			$this->startMenu($node);
			$this->printMenuTree($menu,$cache,$plugins);
			$this->endMenu($node);
		}
		$this->endOutput($menus);
		return true;
	}

	/** Get a Menu's tree
	 * Get the complete list of menu entries where the menu is in $menutype.
	 * If the component, that is linked to the menuentry, has a registered handler,
	 * this function will call the handler routine and add the complete tree.
	 * A tree with subtrees for each menuentry is returned.
	 */
	function printMenuTree( &$menu, &$cache, $plugins) {

		$database = database::getInstance();

		if( strlen($menu->menutype) == 0 ) {
			$result = null;
			return $result;
		}

		$menuExluded	= explode( ',', $this->sitemap->exclmenus ); 		// by mic: fill array with excluded menu IDs

		$items = $this->_getmenuTree($menu);

		if( count($items) <= 0) {	//ignore empty menus
			$result = null;
			return $result;
		}

		$this->changeLevel(1);

		foreach ( $items as $i => $item ) {		// Add each menu entry to the root tree.
			$item->priority = $menu->priority;
			$item->changefreq = $menu->changefreq;
			if( in_array( $item->id, $menuExluded ) ) {	// ignore exluded menu-items
				continue;
			}

			$node = new stdclass;

			$node->id 			= $item->id;
			$node->uid 			= "item".$item->id;
			$node->name 		= $item->name;							// displayed name of node
			$node->parent 		= $item->parent;						// id of parent node
			$node->browserNav 	= $item->browserNav;						// how to open link
			$node->ordering 	= isset( $item->ordering ) ? $item->ordering : $i;		// display-order of the menuentry
			$node->priority 	= $item->priority;
			$node->changefreq 	= $item->changefreq;
			$node->type 		= $item->type;							// menuentry-type
			$node->menutype 	= $item->menutype;						// menuentry-type
			$node->link 	= isset( $item->link ) ? htmlspecialchars( $item->link ) : '';	// convert link to valid xml
			$this->printNode($node);
			XmapPlugins::printTree( $this, $item, $cache, $plugins );	// Determine the menu entry's type and call it's handler
			$this->printMenuTree($node,$cache,$plugins);

		}
		$this->changeLevel(-1);
	}

	/** Look up the title for the module that links to $menutype */
	function getMenuTitle($menutype) {

		$database = database::getInstance();

		$query = "SELECT title FROM #__modules WHERE published='1' AND (module='mod_mainmenu' OR module='mod_mljoostinamenu') AND params LIKE '%menutype=". $menutype ."%' LIMIT 1";
		$database->setQuery( $query );
		if( !$database->loadObject($row) ) {
			return '';
		}
		return $row->title;
	}

	function getItemLink (&$node) {

		$config = Jconfig::getInstance();

		$link = $node->link;
		if ( isset($node->id) ) {
			switch( @$node->type ) {
				case 'separator':
					break;
				case 'url':
					if ( preg_match( "#^/?index\.php\?#", $link ) ) {
						if ( strpos( $link, 'Itemid=') === FALSE ) {
							if (strpos( $link, '?') === FALSE ) {
								$link .= '?Itemid='.$node->id;
							} else {
								$link .= '&amp;Itemid='.$node->id;
							}
						}
					}
					break;
				default:
					if ( strpos( $link, 'Itemid=' ) === FALSE ) {
						$link .= '&amp;Itemid='.$node->id;
					}
					break;
			}
		}
		if( strcasecmp( substr( $link, 0, 4), 'http' ) ) {
			if (strcasecmp( substr( $link, 0, 9), 'index.php' ) === 0 ) {
				$link = sefRelToAbs($link);             // apply SEF transformation
				if( strcasecmp( substr($link,0,4), 'http' ) ) {       // fix broken sefRelToAbs()
					$link = JPATH_SITE. (substr($link,0,1) == '/'? '' : '/').$link;
				}
			} else { // Case for internal links not starting with index.php
				$link = JPATH_SITE. '/' .$link;
			}
		}

		return $link;
	}

	/** Print tree details for debugging and testing */
	function printDebugTree( &$tree ) {
		foreach( $tree as $menu) {
			echo $menu->name."<br />\n";
			_xdump( $menu->tree );
		}
	}

	/** called with usort to sort menus */
	function sort_ordering( &$a, &$b) {
		if( $a->ordering == $b->ordering ) {
			return 0;
		}
		return $a->ordering < $b->ordering ? -1 : 1;
	}

	function _getmenuTree($menu) {
		static $instance;

		if (!is_array( $instance ) OR !isset($instance[$menu->menutype]) ) {
			$database = database::getInstance();
			$sql = "SELECT m.id, m.name, m.parent, m.link, m.type, m.browserNav, m.menutype, m.ordering, m.params, m.componentid, c.name AS component"
					. "\n FROM #__menu AS m"
					. "\n LEFT JOIN #__components AS c ON m.type='components' AND c.id=m.componentid"
					. "\n WHERE m.published='1' AND m.menutype = '".$menu->menutype."'"
					. ( $this->noauth ? '' : "\n AND m.access <= '". $this->gid ."'" )
					. "\n ORDER BY m.menutype,m.parent,m.ordering";

			$database->setQuery( $sql );
			$items = $database->loadObjectList();
			$instance = array();
			foreach ($items as $item) {
				$instance[$menu->menutype][$item->parent][]=$item;
			}
			unset($items,$item);
		}

		return (isset($instance[$menu->menutype][$menu->id])) ? $instance[$menu->menutype][$menu->id]:null;


	}

}

