<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

//require_once ($mainframe->getPath('front_html', 'com_content'));

class mod_latestnews_Helper {
    var $_mainframe = null;

    function __construct($mainframe) {

        $this->_mainframe = $mainframe;
        mosMainFrame::addLib('text');
        mosMainFrame::addLib('images');
    }

    function get_items($params) {
        $mainframe = $this->_mainframe;
        $database = $this->_mainframe->getDBO();
        $count = intval($params->get('count', 5));
        $catid = trim($params->get('catid'));

        $directory = $params->get('directory', 0);
        $content_field = $params->get('content_field', 'content_editor');

        if($directory == 0){
            $directory = mosGetParam( $_REQUEST, 'directory', 0 );
        }

        if($directory == 0){
            require_once ($mainframe->getPath('class', 'com_frontpage'));
            $configObject = new frontpageConfig();
            $directory = $configObject->get('directory', 0);
        }

        $whereCatid = '';
        if ($catid) {
            $catids = explode(',', $catid);
            mosArrayToInts($catids);
            $whereCatid = " AND ( category.id=" . implode(" OR category.id=", $catids) . " )";
        }

        $query = "SELECT content.id as content_id, content.name as title,
		    content." . $content_field . " as introtext, content.userid as created_by,
		    category.id as catid, content.date_created as created,
			u.name AS author, u.name AS created_by_alias, u.usertype, u.username
			FROM      #__boss_" . $directory . "_contents AS content
			LEFT JOIN #__boss_" . $directory . "_content_category_href as cat_href ON cat_href.content_id = content.id
			LEFT JOIN #__boss_" . $directory . "_categories as category ON category.id = cat_href.category_id
			LEFT JOIN #__users AS u ON u.id = content.userid

			WHERE  content.published = 1 "
                . $whereCatid .
                " GROUP BY content.id ORDER BY content.id DESC";

        $database->setQuery($query, 0, $count);
        $rows = $database->loadObjectList();

        return $rows;
    }

    function get_itemid($row, $params) {

        global $_SESSION;

        require_once( JPATH_BASE.'/components/com_boss/boss.tools.php' );

        $itemid = getBossItemid(intval($params->get('directory', 0)), intval($row->catid));

        return $itemid;
    }

    function prepare_row($row, $params) {

        $directory = intval($params->get('directory', 0));

        $row->Itemid_link = '';
        if ($params->get('def_itemid', '')) {
            $row->Itemid_link = '&amp;Itemid=' . $params->get('def_itemid');
        } else {
            $_itemid = $this->get_itemid($row, $params);
            if ($_itemid) {
                $row->Itemid_link = '&amp;Itemid=' . $_itemid;
            }
        }

        $row->link_on = sefRelToAbs('index.php?option=com_boss&amp;task=show_content&amp;contentid=' . $row->content_id . '&amp;catid=' . $row->catid . '&amp;directory=' . $directory . $row->Itemid_link);
        $row->link_text = $params->get('link_text', _READ_MORE);

        $text = $row->introtext;
        $text = mosHTML::cleanText($text);
        if ($params->get('crop_text', 1)) {

            switch ($params->get('crop_text', 1)) {
                case 'simbol':
                default:
                    $text = Text::character_limiter($text, $params->get('text_limit', 250), '');
                    break;

                case 'word':
                    $text = Text::word_limiter($text, $params->get('text_limit', 25), '');
                    break;
            }
        }
        if ($params->get('text', 0) == 2) {
            $text = '<a href="' . $row->link_on . '">' . $text . '</a>';
        }

        $row->image = '';
        if ($params->get('image', 'img') == "img") {

            $img = '/images/boss/' . $directory . '/contents/' . $row->content_id . 'a_t.jpg';
            if (!is_file(JPATH_BASE . $img) && $params->get('image_default', 1) == 1)
                $img = '/images/noimage.jpg';

            if (is_file(JPATH_BASE . $img)) {

                $row->image = '<img title="' . $row->title . '" alt="' . $row->introtext . '" src="' . JPATH_SITE . $img . '" />';

                if ($params->get('image_link', 1) && $row->image) {
                    $row->image = '<a class="thumb" href="' . $row->link_on . '">' . $row->image . '</a>';
                }
            }
        }

        $row->readmore = self::ReadMore($row, $params);
        $row->author = self::Author($row, $params);
        $row->title = self::Title($row, $params);
        $row->text = $text;


        return $row;
    }

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

    	public static function ReadMore(&$row, &$params, $template = '') {
		$return = '';
		if ($params->get('readmore',0) && $params->get('intro_only',0) && $row->link_text) {
			$return = '<a href="' . $row->link_on . '" title="' . $row->title . '" class="readon">' . $row->link_text . '</a>';
		}
		return $return;
	}

    public static function Author(&$row, &$params = '', $config_author_name=4) {

		$author_name = '';
		if (!$params) {
			return $row->username;
		}

		if ($row->author != '') {
			if (!$row->created_by_alias) {

				if ($params->get('author_name', 0)) {
					$switcher = $params->get('author_name');
				} else {
					$switcher = $config_author_name;
				}

				switch ($switcher) {
					case '1':
					case '3':
						$author_name = $row->author;
						break;

					case '2':
					case '4':
					default;
						$author_name = $row->username;
						break;
				}

				if ($switcher == '3' || $switcher == '4') {
					$uid = $row->created_by;
					$author_link = 'index.php?option=com_users&amp;task=profile&amp;user=' . $uid;
					$author_seflink = sefRelToAbs($author_link);
					$author_name = '<a href="' . $author_seflink . '">' . $author_name . '</a>';
				}
			} else {
				$author_name = $row->created_by_alias;
			}
		}
		return $author_name;
	}
}