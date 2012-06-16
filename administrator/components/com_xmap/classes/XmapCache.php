<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();


class XmapCache{
	/**
	 * @return object A function cache object
	 */
	public static function getCache(&$sitemap, $handler = 'callback', $storage = 'file'){
		$handler = ($handler == 'function') ? 'callback' : $handler;

		$config = Jconfig::getInstance();

		if(!isset($storage)){
			$storage = ($config->config_cache_handler != '') ? $config->config_cache_handler : 'file';
		}

		$options = array(
			'defaultgroup'     => 'com_xmap',
			'cachebase'        => $config->config_cachepath . '/',
			'lifetime'         => $sitemap->cachelifetime,
			'language'         => $config->config_lang,
			'storage'          => $storage
		);


		require_once (JPATH_BASE . '/includes/libraries/cache/cache.php');
		$cache = JCache::getInstance($handler, $options);
		if($cache != NULL){
			$cache->setCaching($sitemap->usecache);
		}
		return $cache;
	}

	/**
	 * Cleans the cache
	 */
	public static function cleanCache(&$group = false){
		$cache = XmapCache::getCache($group);
		//_xdump($cache);
		if($cache != NULL){
			$cache->clean($cache->_options['defaultgroup']);
		}
	}
}