<?php
/**
 * @package Joostina
 * @copyright ��������� ����� (C) 2008-2010 Joostina team. ��� ����� ��������.
 * @license �������� http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, ��� help/license.php
 * Joostina! - ��������� ����������� ����������� ���������������� �� �������� �������� GNU/GPL
 * ��� ��������� ���������� � ������������ ����������� � ��������� �� ��������� �����, �������� ���� help/copyright.php.
 */

// ������ ������� �������
defined('_VALID_MOS') or die();

$_MAMBOTS->registerFunction('onSearch','botSearchCategories');

/**
 * ����� ������ ���������
 *
 * ������ sql ������ ���������� ����, ������������ � ������� ��������
 * �����������: href, title, section, created, text, browsernav
 * @param ���������� ���� ������
 * @param ������������ ���������: exact|any|all
 * @param ���������� ������� ����������: newest|oldest|popular|alpha|category
 */
function botSearchCategories($text,$phrase = '',$ordering = '') {
    $_MAMBOTS = mosMambotHandler::getInstance();
    $mainframe = mosMainFrame::getInstance();
    $my = $mainframe->getUser();
    $mambot = null;
	$database = database::getInstance();

	// check if param query has previously been processed
	if(!isset($_MAMBOTS->_search_mambot_params['categories'])) {
		// load mambot params info
		$query = "SELECT params FROM #__mambots WHERE element = 'categories.searchbot' AND folder = 'search'";
		$database->setQuery($query);
		$database->loadObject($mambot);

		// save query to class variable
		$_MAMBOTS->_search_mambot_params['categories'] = $mambot;
	}

	// pull query data from class variable
	$mambot = $_MAMBOTS->_search_mambot_params['categories'];

	$botParams = new mosParameters($mambot->params);

	$limit = $botParams->def('search_limit',50);

    //��������� �������� ��� ������
    $directories = $botParams->get('directories', '');
    if (!empty($directories)) {
        $directories = explode(',', $directories);
    }
    else {
		$query = "SELECT `id` FROM #__boss_config";
		$database->setQuery($query);
		$directories = $database->loadResultArray();
    }

	$text = trim($text);
	if($text == '') {
		return array();
	}

        $list = array();
    foreach ($directories as $directory){
        $directory = intval(trim($directory));
        
	    switch($ordering) {
	    	case 'alpha':
	    		$order = 'name ASC';
	    		break;

	    	case 'category':
	    	case 'popular':
	    	case 'newest':
	    	case 'oldest':
	    	default:
	    		$order = 'name DESC';
	    }

        // search content items
	    $query = "SELECT name AS title,".
                 "\n '' AS created," .
                 "\n description AS text," .
                 "\n '' AS section," .
                 "\n '0' AS secid," .
                 "\n CONCAT( 'index.php?option=com_boss&task=show_category&catid=', id, '&slug=', slug, '&order=0&directory=$directory' ) AS href," .
                 "\n '2' AS browsernav," .
                 "\n 'content' AS type" . "\n, 3 AS sec_id, 4 as cat_id" .
                 "\n FROM #__boss_" . $directory . "_categories" .
                 "\n WHERE LOWER(name) LIKE LOWER('%$text%')" . "\n AND published = 1" .
                 "\n ORDER BY $order";

	    $database->setQuery($query,0,$limit);
	    $list_tmp = $database->loadObjectList();

        if(is_array($list_tmp)){
			$list = array_merge($list, $list_tmp);
        }
    }
	return $list;









    $query = "SELECT " .






             "\n m.id AS menuid, m.type AS menutype" .
             "\n FROM #__categories AS a" .
             "\n LEFT JOIN #__menu AS m ON m.componentid = a.id" .
             "\n WHERE ( a.title LIKE '%$text%'" .
             "\n OR a.title LIKE '%$text%'" . "\n OR a.description LIKE '%$text%' )" .
             "\n AND a.published = 1" .
             "\n AND a.access <= " . (int)$my->gid .
             "\n AND ( m.type = 'content_section' OR m.type = 'content_blog_section'" .
             "\n OR m.type = 'content_category' OR m.type = 'content_blog_category')" . "\n GROUP BY a.id" .
             "\n ORDER BY $order";
	$database->setQuery($query,0,$limit);
	$rows = $database->loadObjectList();

	$count = count($rows);
	for($i = 0; $i < $count; $i++) {
	    $rows[$i]->href = 'index.php?option=com_content&task=category&sectionid='.$rows[$i]->secid.'&id='.$rows[$i]->catid.'&Itemid='.$rows[$i]->menuid;
		$rows[$i]->section = _SEARCH_CATLIST;
	}

	return $rows;
}