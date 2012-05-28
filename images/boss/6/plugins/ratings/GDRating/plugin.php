<?php
/**
 * Плагин рейтинга
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2000-2012 Gold Dragon.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

class GDRating{
	private $mainframe;
	private $my;
	private $database;

	private $units;
	private $rating_unitwidth;
	private $result_only;

	private $content;
	private $directory;
	private $conf;

	public function __construct($directory){
		$this->mainframe = mosMainFrame::getInstance();
		$this->my = $this->mainframe->getUser();
		$this->database = database::getInstance();

		// количество звёздочек
		$this->units = 10;
		// ширина звёздочки
		$this->rating_unitwidth = 16;
		// включаем показ рейтинга полностью
		$this->result_only = true;
	}

	/**
	 * Вызов формы рейтинга
	 * @param $content - запись контента
	 * @param $directory - каталог
	 * @param $conf - данные конфигурации
	 * @internal param bool $gust - разрешение голосовать гостям (принудительно)
	 * @return bool|string
	 */
	public function displayVoteForm($content, $directory, $conf){
		// проверяем разрешёл ли рейтинг
		if($conf->allow_ratings){
			$this->content = $content;
			$this->directory = $directory;
			$this->conf = $conf;

			// подключение языкового файла
			$path = JPATH_BASE . DS . 'images' . DS . 'boss' . DS . $this->directory . DS . 'plugins' . DS . 'ratings' . DS . 'GDRating' . DS . 'lang';
			$lang = $this->mainframe->getCfg('lang');
			if(file_exists($path . DS . $lang . '.php')){
				require_once($path . DS . $lang . '.php');
			} else{
				require_once($path . DS . 'russian.php');
			}

			// подключаем стили один раз
			if(!defined('_GDRATING_CSS')){
				define('_GDRATING_CSS', 1);
				$this->mainframe->addCSS(JPATH_SITE . '/images/boss/' . $this->directory . '/plugins/ratings/GDRating/style.css');
			}
			// выводим рейтинг
			$result = $this->ratingBar();
			echo $result;

			return true;
		} else{
			return false;
		}
	}

	/**
	 * Вызов результата рейтинга
	 * @param $content - запись контента
	 * @param $directory - каталог
	 * @param $conf - данные конфигурации
	 * @internal param bool $gust - разрешение голосовать гостям (принудительно)
	 */
	public function displayVoteResult($content, $directory, $conf){
		$this->result_only = false;
		$this->displayVoteForm($content, $directory, $conf);
	}
	/**
	 * Вывод рейтинга
	 * @return string
	 */
	private function ratingBar(){
		$sql = "SELECT COUNT(*) AS count, SUM(value) AS sum
				FROM #__boss_" . $this->directory . "_rating
				WHERE `contentid` =" . $this->content->id;
		$this->database->setQuery($sql);
		$rows = $this->database->loadObjectList();

		$this->mainframe->addLib('text');

		if(count($rows)){
			$row = $rows[0];
			$rating_width = @number_format($row->sum / $row->count, 2) * $this->rating_unitwidth;

			$rating1 = ($row->count) ? number_format($row->sum / $row->count, 1) : "0.0";
			$rating2 = ($row->count) ? number_format($row->sum / $row->count, 2) : "0.00";

			$tense = Text::declension($row->count, array(_GDRATING_MES01, _GDRATING_MES02, _GDRATING_MES03));
			if(!$this->result_only){
				$result = '<span>' . _GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $this->units . ' (' . $row->count . ' ' . $tense . ')' . '</span>';
				return $result;
			}else{
				if($this->my->id == 0 AND $this->conf->allow_unregisered_comment == 0){
					$result = array();
					$result[] .= '<div class="ratingblock" id="ratingblock_' . $this->content->id . '">';
					$result[] .= '<div id="unit_long' . $this->content->id . '">';
					$result[] .= '<ul id="unit_ul' . $this->content->id . '" class="unit-rating" style="width:' . $this->rating_unitwidth * $this->units . 'px;">';
					$result[] .= '<li class="current-rating" style="width:' . $rating_width . 'px;">' . _GDRATING_MES04 . ' ' . $rating2 . '/' . $this->units . '</li>';
					$result[] .= '</ul>';
					$result[] .= '<p class="static">';
					$result[] .= '<span>' . _GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $this->units . ' (' . $row->count . ' ' . $tense . ')' . '</span>';
					$result[] .= '<br /><span style="font-size:90%">' . _GDRATING_MES07 . '.</span></p>';
					$result[] .= '</div>';
					$result[] .= '</div>';
					return join("", $result);
				} else{
					// получаем IP
					$ip = getIp();

					// проверяем голосовавших
					if($this->my->id)
						$sql = "SELECT * FROM #__boss_" . $this->directory . "_rating WHERE contentid =" . $this->content->id . " AND userid =" . $this->my->id;
					else
						$sql = "SELECT * FROM #__boss_" . $this->directory . "_rating WHERE contentid =" . $this->content->id . " AND userid =0 AND ip =" . ip2long($ip);
					$this->database->setQuery($sql);
					$res = $this->database->query();
					$voted = $this->database->getNumRows($res);

					$result = '<div class="ratingblock" id="ratingblock_' . $this->content->id . '">';
					$result .= '<div id="unit_long' . $this->content->id . '">';
					$result .= '<ul id="unit_ul' . $this->content->id . '" class="unit-rating" style="width:' . $this->rating_unitwidth * $this->units . 'px;">';
					$result .= '<li class="current-rating" style="width:' . $rating_width . 'px;">' . _GDRATING_MES04 . ' ' . $rating2 . '/' . $this->units . '</li>';
					for($i = 1; $i <= $this->units; $i++){
						if(!$voted){
							$result .= '<li><a
						 onclick="gd_rating_plugin('
								. $i . ','
								. $this->content->id . ','
								. ip2long($ip) . ','
								. $this->units . ','
								. $this->rating_unitwidth . ','
								. $this->directory . ','
								. $this->my->id . ', \''
								. JPATH_SITE . '/images/boss/' . $this->directory . '/plugins/ratings/GDRating/db.php\')"
						href="javascript:void(0)"
						title="' . $i . ' out of ' . $this->units . '"
						class="r' . $i . '-unit rater"
						rel="nofollow">' . $i . '</a></li>';
						}
					}
					$result .= '</ul>';
					$result .= '<p>' ._GDRATING_MES05 . ': <strong> ' . $rating1 . '</strong>/' . $this->units . ' (' . $row->count . ' ' . $tense . ')'.'</span></p>';
					$result .= '</div>';
					$result .= '</div>';
					if(!defined('_GDRATING_JS')){
						define('_GDRATING_JS', 1);
						$this->mainframe->addJS(JPATH_SITE . '/images/boss/' . $this->directory . '/plugins/ratings/GDRating/script.js');
					}
					return $result;
				}
			}
		} else{
			return false;
		}
	}

	/**
	 * функция для вставки таблиц и полей рейтинга в запрос категории и контента
	 * @param $directory
	 * @param $conf
	 * @return array
	 */
	public function queryString($directory, $conf){
		$query = array();
		if($conf->allow_ratings == 1){
			$query['tables'] = " LEFT JOIN #__boss_" . $directory . "_rating as rat ON a.id = rat.contentid \n";
			$query['fields'] = " count(DISTINCT rat.id) as num_votes, AVG(rat.value) as sum_votes, rat.id as not_empty, \n";
			$query['wheres'] = '';
		} else{
			$query['tables'] = '';
			$query['fields'] = '';
			$query['wheres'] = '';
		}
		return $query;
	}

	/**
	 * действия при установке плагина
	 * @param $directory
	 * @return void
	 */
	public function install($directory){
		$sql = "CREATE TABLE IF NOT EXISTS `#__boss_" . $directory . "_rating` (
  					`id` int(10) NOT NULL AUTO_INCREMENT,
  					`contentid` int(10) DEFAULT '0',
  					`userid` int(10) DEFAULT '0',
  					`value` tinyint(1) DEFAULT '5',
  					`ip` int(11) DEFAULT '0',
  					`date` int(10) DEFAULT '0',
  				PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
		$this->database->setQuery($sql)->query();
	}

	/**
	 * действия при удалении плагина
	 * @param $directory
	 * @return void
	 */
	public function uninstall($directory){
		$query = "DROP TABLE IF EXISTS `#__boss_" . $directory . "_rating`";
		$this->database->setQuery($query)->query();
	}
}