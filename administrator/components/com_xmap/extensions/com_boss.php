<?php
/**
 * @package Xmap
 * @license GNU/GPL
 * Xmap plugin for JoiBoss
 */

defined('_VALID_MOS') or defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/** Adds support for AdsManager categories to Xmap */
class xmap_com_boss
{

    /** Get the content tree for this kind of content */
    public static function getTree($xmap, $parent, $params)
    { 
        $database = database::getInstance();
        $include_entries = (!empty($params['include_entries'])) ? $params['include_entries'] : 1;
        $include_entries = ($include_entries == 1
                            || ($include_entries == 2 && $xmap->view == 'xml')
                            || ($include_entries == 3 && $xmap->view == 'html')
                            || $xmap->view == 'navigator');
        $params['include_entries'] = $include_entries;
        $priority = (!empty($params['priority'])) ? $params['priority'] : '-1';
        $changefreq = (!empty($params['changefreq'])) ? $params['changefreq'] : '-1';
        $entry_priority = (!empty($params['entry_priority'])) ? $params['entry_priority'] : '-1';
        $entry_changefreq = (!empty($params['entry_changefreq'])) ? $params['entry_changefreq'] : '-1';

        if ($priority == '-1')
            $priority = $parent->priority;
        if ($changefreq == '-1')
            $changefreq = $parent->changefreq;
        if ($entry_priority == '-1')
            $entry_priority = $parent->priority;
        if ($entry_changefreq == '-1')
            $entry_changefreq = $parent->changefreq;

        $params['priority'] = $priority;
        $params['changefreq'] = $changefreq;
        $params['entry_priority'] = $entry_priority;
        $params['entry_changefreq'] = $entry_changefreq;


        if ($include_entries) {
            $params['limit'] = '';
            $limit = (!empty($params['max_entries'])) ? $params['max_entries'] : 0;
            if (intval($limit))
                $params['limit'] = ' LIMIT ' . $limit;
        }

        $linkParams = self::parseUri($parent->link);

        if($parent->type == 'components'){//если ссылка на компонент Босс
            $database->setQuery("SELECT id FROM #__boss_config");
            $rows = $database->loadResultArray();
            foreach ($rows as $directory) {
                self::getCategoryTree($xmap, $parent, $params, $directory, 0);
            }
            return true;
        }
        else if($parent->type == 'boss_all_content'){//если ссылка на весь контент каталога
            if(isset($linkParams['directory']) && $linkParams['directory']>0){
                self::getAllContent($xmap, $parent, $params, $linkParams['directory']);
            }
            return true;
        }
        else if($parent->type == 'boss_category_content'){//если ссылка на контент категории
            if(!isset($linkParams['directory']) || $linkParams['directory'] == 0 || !isset($linkParams['catid'])){
                return true;
            }
            self::getChildren($xmap, $parent, $params, $linkParams['directory'], $linkParams['catid']);
            self::getCategoryTree($xmap, $parent, $params, $linkParams['directory'], $linkParams['catid']);
            return true;
        }
        else if($parent->type == 'boss_item_content'){//если ссылка на контент
            if(!isset($linkParams['contentid']) || $linkParams['contentid'] == 0){
                return true;
            }
            $xmap->changeLevel(1);
            $node = new stdclass;
            $node->id = $linkParams['contentid'];
            $node->uid = $parent->uid . 'c' . $linkParams['contentid'];
            $node->browserNav = $parent->browserNav;
            $node->name = stripslashes($parent->name);
            $node->priority = $params['entry_priority'];
            $node->changefreq = $params['entry_changefreq'];
            $node->link = 'index.php?option=com_boss&task=show_content&contentid=' . $linkParams['contentid'] . '&directory=' . $linkParams['directory'] . '&catid=' . $linkParams['catid'] . '&Itemid=' . $parent->id;
            $node->pid = $linkParams['contentid'];
            $node->expandible = false;
            $xmap->printNode($node);
            $xmap->changeLevel(-1);
            return true;
        }

        return true;
    }

    private static function getCategoryTree($xmap, $parent, $params, $directory, $pid)
    {
        $database = database::getInstance();
        $database->setQuery("SELECT * FROM #__boss_" . $directory . "_categories where parent='$pid' and published >='1'");
        $rows = $database->loadObjectList();

        $xmap->changeLevel(1);
        if (count($rows)) {
            foreach ($rows as $row)
            {
                $node = new stdclass;
                $node->id = $parent->id;
                $node->uid = $parent->uid . 'c' . $row->id;
                $node->browserNav = $parent->browserNav;
                $node->name = stripslashes($row->name);
                $node->priority = $params['priority'];
                $node->changefreq = $params['changefreq'];
                $node->link = 'index.php?option=com_boss&task=show_category&catid=' . $row->id . '&slug=' . $row->slug . '&directory=' . $directory . '&Itemid=' . $parent->id;
                $node->pid = $row->id;
                $node->expandible = true;
                $pid = $row->id;
                if (($xmap->printNode($node) !== FALSE) && $params['include_entries']) {
                    // list recipies
                    self::getChildren($xmap, $parent, $params, $directory, $pid);
                    // see children category recursiv...
                    self::getCategoryTree($xmap, $parent, $params, $directory, $pid);
                }
            }
        }
        $xmap->changeLevel(-1);
        return true;
    }

    private static function getChildren($xmap, $parent, $params, $directory, $catid)
    {
        $database = database::getInstance();
        $database->setQuery("SELECT content_id FROM #__boss_" . $directory . "_content_category_href WHERE category_id = '$catid' ORDER BY content_id DESC " . $params['limit']);
        $content_ids = $database->loadResultArray();
        $xmap->changeLevel(1);
        if (count($content_ids)) {
            foreach ($content_ids as $content_id)
            {
                // get name
                $database->setQuery("SELECT id, name FROM #__boss_" . $directory . "_contents WHERE published='1' and id='$content_id'");
                $items = $database->loadObjectList();
                // just 1 but anyway ...
                if (count($items)) {
                    foreach ($items as $item)
                    {
                        $node = new stdclass;
                        $node->id = $parent->id;
                        $node->uid = $parent->uid . 'c' . $item->id;
                        $node->browserNav = $parent->browserNav;
                        $node->name = stripslashes($item->name);
                        $node->priority = $params['entry_priority'];
                        $node->changefreq = $params['entry_changefreq'];
                        $node->link = 'index.php?option=com_boss&task=show_content&contentid=' . $item->id . '&directory=' . $directory . '&catid=' . $catid . '&Itemid=' . $parent->id;
                        $node->pid = $item->id;
                        $node->expandible = false;
                        $xmap->printNode($node);
                    }
                }
            }
        }
        $xmap->changeLevel(-1);
        return true;
    }

    private static function getAllContent($xmap, $parent, $params, $directory)
    {
        $database = database::getInstance();
        $limit = (!empty($params['max_entries'])) ? 'LIMIT '.$params['max_entries'] : '';
        // get name
        $q = "SELECT c.id, c.name, cch.category_id
            FROM #__boss_" . $directory . "_contents as c
            LEFT JOIN #__boss_" . $directory . "_content_category_href AS cch ON cch.content_id=c.id
            WHERE c.published='1' 
            GROUP BY c.id
            $limit";
        $database->setQuery($q);
        $items = $database->loadObjectList();
        // just 1 but anyway ...
        if (count($items)) {
            $xmap->changeLevel(1);
            foreach ($items as $item)
            {
                $node = new stdclass;
                $node->id = $parent->id;
                $node->uid = $parent->uid . 'c' . $item->id;
                $node->browserNav = $parent->browserNav;
                $node->name = stripslashes($item->name);
                $node->priority = $params['entry_priority'];
                $node->changefreq = $params['entry_changefreq'];
                $node->link = 'index.php?option=com_boss&task=show_content&contentid=' . $item->id . '&directory=' . $directory . '&catid=' . $item->category_id . '&Itemid=' . $parent->id;
                $node->pid = $item->id;
                $node->expandible = false;
                $xmap->printNode($node);
            }
            $xmap->changeLevel(-1);
        }
        return true;
    }

    private static function parseUri($uri){
        $uri = str_replace('index.php?', '', $uri);
        $uri = explode('&', $uri);
        $paramArray = array();
        foreach($uri as $row){
            $row = explode('=', $row);
            $paramArray[$row[0]] = (isset($row[1])) ? $row[1] : '';
        }
        return $paramArray;
    }
}
