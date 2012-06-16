<?php
/**
 * @package GDNLotos - Главные новости
 * @copyright Авторские права (C) 2000-2011 Gold Dragon.
 * @license http://www.gnu.org/licenses/gpl.htm GNU/GPL
 * GDNLotos - Главные новости - модуль позволяет выводить основные материалы по определённым критериям для Joostina 1.4.0.x
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл view/copyright.php.
 */

// запрет прямого доступа
defined('_VALID_MOS') or die();

class mod_gdnlotos_Helper{
	private $moduleclass_sfx;
	private $template;
	private $template_dir;
	private $modul_link;
	private $modul_link_cat;
	private $directory;
	private $catid;
	private $directory_name;
	private $directory_link;
	private $category_name;
	private $category_link;
	private $content_field;
	private $count_special;
	private $count_basic;
	private $columns;
	private $count_reference;
	private $show_front;
	private $orderby;
	private $time;
	private $image;
	private $image_link;
	private $image_default;
	private $image_prev;
	private $image_size_s;
	private $image_quality_s;
	private $image_size_b;
	private $image_quality_b;
	private $item_title;
	private $link_titles;
	private $text;
	private $crop_text;
	private $crop_text_limit;
	private $crop_text_format;
	private $show_date;
	private $date_format;
	private $show_author;
	private $readmore;
	private $link_text;
	private $hits;

	private $fancybox; // настройки fancybox
	private $special;
	private $basic;
	private $reference;
	private $script;
	private $tpl_dir;
	private $tpl_dir_style;
	private $style;

	private $dir_name; // название каталога
	private $moduleid; // идентификатор модуля

	private $_my;
	private $_mainframe;
	private $_database;

	private $tpl_tit;
	private $tpl_sec;
	private $tpl_cat;
	private $tpl_dat;
	private $tpl_img;
	private $tpl_aut;
	private $tpl_txt;
	private $tpl_but;
	private $tpl_hit;

	private function getParams($params, $moduleid){
		$this->template = trim($params->get('template', 'default'));

		// идентификатор модуля
		$this->moduleid = $moduleid;

		// стиль по умолчанию
		$this->moduleclass_sfx = trim($params->get('moduleclass_sfx', ''));
		if($this->moduleclass_sfx == '')
			$this->moduleclass_sfx = '-' . $this->template;

		$this->template_dir = trim($params->get('template_dir', ''));
		$this->modul_link = intval($params->get('modul_link', 0));
		$this->modul_link_cat = intval($params->get('modul_link_cat', 0));
		$this->directory = intval($params->get('directory', 0));
		$this->catid = trim($params->get('catid', ''));
		$this->directory_name = intval($params->get('directory_name', 1));
		$this->directory_link = intval($params->get('directory_link', 1));
		$this->category_name = intval($params->get('category_name', 1));
		$this->category_link = intval($params->get('category_link', 1));

		// Выбор поля контента
		$field_arr = explode('-', trim($params->get('content_field', '1-content_editor')));
		$this->content_field = (isset($field_arr[1])) ? $field_arr[1] : 'content_editor';

		$this->count_special = intval($params->get('count_special', 1));
		$this->count_basic = intval($params->get('count_basic', 4));
		$this->columns = intval($params->get('columns', 2));
		$this->count_reference = intval($params->get('count_reference', 3));
		$this->show_front = intval($params->get('show_front', 0));
		$this->orderby = trim($params->get('orderby', ''));
		$this->time = intval($params->get('time', 30));
		$this->image = intval($params->get('image', 3));
		$this->image_link = intval($params->get('image_link', 2));
		$this->image_default = intval($params->get('image_default', 1));
		$this->image_prev = trim($params->get('image_prev', 'width'));
		$this->image_size_s = intval($params->get('image_size_s', 200));
		$this->image_quality_s = intval($params->get('image_quality_s', 75));
		$this->image_size_b = intval($params->get('image_size_b', 100));
		$this->image_quality_b = intval($params->get('image_quality_b', 75));
		$this->item_title = intval($params->get('item_title', 1));
		$this->link_titles = intval($params->get('link_titles', 1));
		$this->text = intval($params->get('text', 1));
		$this->crop_text = $params->get('crop_text', 0);
		$this->crop_text_limit = trim($params->get('crop_text_limit', ''));
		$this->crop_text_format = intval($params->get('crop_text_format', 0));
		$this->show_date = intval($params->get('show_date', 1));
		$this->date_format = $params->get('date_format', '%d-%m-%Y %H:%M');
		$this->show_author = intval($params->get('show_author', 4));
		$this->readmore = intval($params->get('readmore', 1));
		$this->link_text = trim($params->get('link_text', _READ_MORE));
		$this->hits = intval($params->get('hits', 1));

		$this->fancybox = '"overlayShow":true,';
		$this->fancybox .= '"overlayOpacity":0.3,';
		$this->fancybox .= '"transitionIn":"elastic",';
		$this->fancybox .= '"transitionOut":"elastic"';

		$this->_mainframe = mosMainFrame::getInstance();
		$this->_my = $this->_mainframe->getUser();
		$this->_database = $this->_mainframe->getDBO();

		$this->special = '';
		$this->basic = '';
		$this->reference = '';
		$this->style = '';

		$this->tpl_tit = '';
		$this->tpl_sec = '';
		$this->tpl_cat = '';
		$this->tpl_dat = '';
		$this->tpl_img = '';
		$this->tpl_aut = '';
		$this->tpl_txt = '';
		$this->tpl_but = '';
		$this->tpl_hit = '';

		// подключение fansybox
		if($this->image_link == 2){
			$tmp = mosCommonHTML::loadJqueryPlugins('fancybox/jquery.fancybox', true, true);
			echo $tmp;
		}

		// определение путей до шаблона
		if($this->template_dir and is_dir(JPATH_BASE . DS . 'templates' . DS . JTEMPLATE . DS . 'html' . DS . 'modules' . DS . 'mod_gdnlotos' . DS . $this->template)){
			$this->tpl_dir = JPATH_BASE . DS . 'templates' . DS . JTEMPLATE . DS . 'html' . DS . 'modules' . DS . 'mod_gdnlotos' . DS . $this->template;
			$this->tpl_dir_style = JPATH_SITE . '/templates/' . JTEMPLATE . '/html/modules/mod_gdnlotos/' . $this->template . '/style.css';
		} elseif(is_dir(JPATH_BASE . DS . 'modules' . DS . 'mod_gdnlotos' . DS . 'templates' . DS . $this->template)){
			$this->tpl_dir = JPATH_BASE . DS . 'modules' . DS . 'mod_gdnlotos' . DS . 'templates' . DS . $this->template;
			$this->tpl_dir_style = JPATH_SITE . '/modules/mod_gdnlotos/templates/' . $this->template . '/style.css';
		} else{
			$this->tpl_dir = JPATH_BASE . DS . 'modules' . DS . 'mod_gdnlotos' . DS . 'templates' . DS . 'default';
			$this->tpl_dir_style = JPATH_SITE . '/modules/mod_gdnlotos/templates/default/style.css';
		}

		// исключение повторного подулючения стиля
		$tmp = 'gd' . md5($this->tpl_dir_style);
		if(!defined($tmp)){
			define($tmp, 1);
			$this->style = '<link href="' . $this->tpl_dir_style . '" rel="stylesheet" type="text/css" />';
		}

		// получаем имя каталога
		$this->dir_name = Text::gdQuoteReplace($this->_database->setQuery("SELECT name FROM #__boss_config")->loadResult());
	}

	/**
	 * Функция выводи модуль
	 * @param $params
	 * @param $moduleid
	 * @return void
	 */
	public function getHTML($params, $moduleid){
		mosMainFrame::addLib('text');
		mosMainFrame::addLib('images');
		$result = 'Информация отсутствует';

		// получение параметров модуля
		$this->getParams($params, $moduleid);

		// получить данные из таблицы
		$rows = $this->getContent();

		$i = 0;
		if($rows){
			foreach($rows as $row){
				if($i < $this->count_special
				) // обработка специальных статей
					$this->special .= $this->getSpecial($row); elseif($i >= $this->count_special AND $i < ($this->count_basic + $this->count_special)
				) // обработка основных статей
					$this->basic[] = $this->getBasic($row); else
					// обработка ссылок
					$this->reference .= $this->getReference($row);
				$i++;
			}
			$result = $this->getAssembly();
		}
		echo $result;
	}

	/**
	 * получение HTML специальных статей
	 * @param  $row
	 * @return string
	 */
	private function getSpecial($row){
		$this->getItem($row, 'sp');
		$tpl = file_get_contents($this->tpl_dir . DS . 'special.tpl');
		if(strpos($tpl, '[MODULECLASS_SFX]'))
			$tpl = str_replace('[MODULECLASS_SFX]', $this->moduleclass_sfx, $tpl);
		if(strpos($tpl, '[TITLE]'))
			$tpl = str_replace('[TITLE]', $this->tpl_tit, $tpl);
		if(strpos($tpl, '[DIRECTORY]'))
			$tpl = str_replace('[DIRECTORY]', $this->tpl_sec, $tpl);
		if(strpos($tpl, '[CATEGORY]'))
			$tpl = str_replace('[CATEGORY]', $this->tpl_cat, $tpl);
		if(strpos($tpl, '[DATE]'))
			$tpl = str_replace('[DATE]', $this->tpl_dat, $tpl);
		if(strpos($tpl, '[IMAGE]'))
			$tpl = str_replace('[IMAGE]', $this->tpl_img, $tpl);
		if(strpos($tpl, '[AUTOR]'))
			$tpl = str_replace('[AUTOR]', $this->tpl_aut, $tpl);
		if(strpos($tpl, '[TEXT]'))
			$tpl = str_replace('[TEXT]', $this->tpl_txt, $tpl);
		if(strpos($tpl, '[BUTTOM]'))
			$tpl = str_replace('[BUTTOM]', $this->tpl_but, $tpl);
		if(strpos($tpl, '[HIT]'))
			$tpl = str_replace('[HIT]', $this->tpl_hit, $tpl);
		return $tpl;
	}

	/**
	 * получение HTML основных статей
	 * @param  $row
	 * @return string
	 */
	private function getBasic($row){
		$this->getItem($row, 'bs');
		$tpl = file_get_contents($this->tpl_dir . DS . 'basic.tpl');
		if(strpos($tpl, '[MODULECLASS_SFX]'))
			$tpl = str_replace('[MODULECLASS_SFX]', $this->moduleclass_sfx, $tpl);
		if(strpos($tpl, '[TITLE]'))
			$tpl = str_replace('[TITLE]', $this->tpl_tit, $tpl);
		if(strpos($tpl, '[DIRECTORY]'))
			$tpl = str_replace('[DIRECTORY]', $this->tpl_sec, $tpl);
		if(strpos($tpl, '[CATEGORY]'))
			$tpl = str_replace('[CATEGORY]', $this->tpl_cat, $tpl);
		if(strpos($tpl, '[DATE]'))
			$tpl = str_replace('[DATE]', $this->tpl_dat, $tpl);
		if(strpos($tpl, '[IMAGE]'))
			$tpl = str_replace('[IMAGE]', $this->tpl_img, $tpl);
		if(strpos($tpl, '[AUTOR]'))
			$tpl = str_replace('[AUTOR]', $this->tpl_aut, $tpl);
		if(strpos($tpl, '[TEXT]'))
			$tpl = str_replace('[TEXT]', $this->tpl_txt, $tpl);
		if(strpos($tpl, '[BUTTOM]'))
			$tpl = str_replace('[BUTTOM]', $this->tpl_but, $tpl);
		if(strpos($tpl, '[HIT]'))
			$tpl = str_replace('[HIT]', $this->tpl_hit, $tpl);
		return $tpl;
	}

	/**
	 * Обработка ссылок
	 * @param  $row
	 * @return mixed
	 */
	private function getReference($row){
		$search = array('[MODULECLASS_SFX]', '[REFERENCE]');
		$link = sefRelToAbs('index.php?option=com_boss&amp;task=show_content&amp;directory=' . $this->directory . '&amp;catid=' . $row->category_id . '&amp;contentid=' . $row->id);
		$tpl = file_get_contents($this->tpl_dir . DS . 'reference.tpl');
		$row->title = Text::gdQuoteReplace($row->title);
		$row->title = '<a href="' . $link . '" title="' . $row->title . '">' . $row->title . '</a>';
		$result = str_replace($search, array($this->moduleclass_sfx, $row->title), $tpl);
		return $result;
	}

	/**
	 * Сборка всего шаблона
	 * @return mixed
	 */
	private function getAssembly(){
		$search = array('[STYLE]', '[MODULECLASS_SFX]', '[SPECIAL]', '[BASIC]', '[REFERENCE]', '[SCRIPT]');

		// обработка basic
		if($this->basic){
			$basic = '<table><tr>';
			$gdn_columns = $this->columns;
			$gdn_width = 'width="' . intval(100 / $gdn_columns) . '%"';
			$gdn_rows = count($this->basic);
			foreach($this->basic as $item){
				$gdn_columns--;
				$gdn_rows--;
				$basic .= '<td ' . $gdn_width . '>';
				$basic .= $item;
				$basic .= '</td>';
				if($gdn_columns == 0 AND $gdn_rows > 0){
					$basic .= '</tr><tr>';
					$gdn_columns = $this->columns;
				}
			}
			if($gdn_columns != $this->columns){
				for($i = 0; $i < $gdn_columns; $i++){
					$basic .= '<td></td>';
				}
			}
			$basic .= '</tr></table>';
		} else{
			$basic = '';
		}
		// обработка заголовка модуля
		if($this->modul_link){
			if($this->modul_link == 1){
				$link = sefRelToAbs("index.php?option=com_boss&amp;task=show_all&amp;directory=" . $this->directory);
			} else{
				$link = sefRelToAbs("index.php?option=com_boss&amp;task=show_category&amp;directory=" . $this->directory . "&amp;catid=" . $this->catid);
			}
			$this->script .= '$("#module_' . $this->moduleid . ' > h3").wrap(\'<a href="' . $link . '" />\');';
		}
		if($this->image_link == 2 OR $this->modul_link){
			$this->script = '<script type="text/javascript">$(function() {' . $this->script . '});</script>';
		}
		$replace = array($this->style, $this->moduleclass_sfx, $this->special, $basic, $this->reference, $this->script);
		$tpl = file_get_contents($this->tpl_dir . DS . 'assembly.tpl');
		$result = str_replace($search, $replace, $tpl);
		return $result;
	}

	/**
	 * Получение одной записи
	 * @param  $row
	 * @param  $file - суффикс файла
	 * @return void
	 */
	private function getItem($row, $file){
		// ссылка на метариал
		$link = sefRelToAbs('index.php?option=com_boss&amp;task=show_content&amp;directory=' . $this->directory . '&amp;catid=' . $row->category_id . '&amp;contentid=' . $row->id);
		// обработка заголовка
		if($this->item_title){
			$row->title = Text::gdQuoteReplace($row->title);
			if($this->link_titles){
				$row->title = '<a href="' . $link . '" title="' . $row->title . '">' . $row->title . '</a>';
			}
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.tit.tpl');
			$this->tpl_tit = str_replace(array('[MODULECLASS_SFX]', '[TITLE]'), array($this->moduleclass_sfx, $row->title), $tpl);
		}

		// обработка раздела
		if($this->directory_name){
			$dir_name = $this->dir_name;
			if($this->directory_link AND $this->directory){
				$link_dir = sefRelToAbs('index.php?option=com_boss&amp;task=show_all&amp;directory=' . $this->directory);
				$dir_name = '<a href="' . $link_dir . '" title="' . $dir_name . '">' . $dir_name . '</a>';
			}
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.sec.tpl');
			$this->tpl_sec = str_replace(array('[MODULECLASS_SFX]', '[DIRECTORY]'), array($this->moduleclass_sfx, $dir_name), $tpl);
		}

		// обработка категории
		if($this->category_name){
			$row->cat_name = Text::gdQuoteReplace($row->cat_name);
			if($this->category_link AND $this->directory){
				$link_cat = sefRelToAbs('index.php?option=com_boss&amp;task=show_category&amp;directory=' . $this->directory . '&amp;catid=' . $row->category_id);
				$row->cat_name = '<a href="' . $link_cat . '" title="' . $row->cat_name . '">' . $row->cat_name . '</a>';
			}
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.cat.tpl');
			$this->tpl_cat = str_replace(array('[MODULECLASS_SFX]', '[CATEGORY]'), array($this->moduleclass_sfx, $row->cat_name), $tpl);
		}

		// обработка даты
		if($this->show_date){
			$row->date_created = mosFormatDate($row->date_created, $this->date_format);
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.dat.tpl');
			$this->tpl_dat = str_replace(array('[MODULECLASS_SFX]', '[DATE]'), array($this->moduleclass_sfx, $row->date_created), $tpl);
		}

		// обработка изображения
		if(ini_get('allow_url_fopen')){
			if($this->image){
				$text_with_image = $row->introtext;
				if($this->image){
					$tmp_img = Image::get_image_from_text($text_with_image, 'img', $this->image_default);
					if(!file_exists(JPATH_BASE . str_replace(JPATH_SITE, '', $tmp_img))){
						$tmp_img = '/images/noimage.jpg';
					}
				} else{
					$tmp_img = '';
				}
				if(trim($tmp_img) != ''){

					if(substr($tmp_img, 0, 4) != 'http'){
						$tmp_img = JPATH_SITE . $tmp_img;
					}

					if($file == 'sp'){
						$image_size = $this->image_size_s;
						$image_quality = $this->image_quality_s;
					} else{
						$image_size = $this->image_size_b;
						$image_quality = $this->image_quality_b;
					}


					// формирование размера
					$info_img = getimagesize($tmp_img);
					$size[0] = $info_img[0];
					$size[1] = $info_img[1];

					if($this->image_prev == 'width'){
						$img_thumb_width = ($size[0] > $image_size) ? $image_size : $size[0];
						$coeff = $size[0] / $size[1]; // если да, то делим ширину на высоту
						$img_thumb_height = (int)($img_thumb_width / $coeff);
					} else{
						$img_thumb_height = ($size[1] > $image_size) ? $image_size : $size[1];
						$coeff = $size[1] / $size[0]; // и наоборот...
						$img_thumb_width = (int)($img_thumb_height / $coeff);
					}

					// формируем миниэскиз
					$src = JPATH_SITE . '/modules/mod_gdnlotos/imgsketch.php?' . 'src=' . $tmp_img . '&w=' . $img_thumb_width . '&h=' . $img_thumb_height . '&q=' . $image_quality;

					$this->tpl_img = '<img alt="' . strip_tags($row->title) . '" src="' . $src . '" />';

					// эффект на нажатие на изображение
					switch($this->image_link){
						case 1:
							$this->tpl_img = '<a href="' . $link . '">' . $this->tpl_img . '</a>';
							break;
						case 2:
							$this->tpl_img = '<a id="gdn_limg_' . $this->moduleid . '_' . $row->id . '" href="' . $tmp_img . '">' . $this->tpl_img . "</a>";
							$this->script .= '$("#gdn_limg_' . $this->moduleid . '_' . $row->id . '").fancybox({' . $this->fancybox . '});';
							break;
						case 3;
							$this->tpl_img = '<a target="_blank" href="' . $tmp_img . '">' . $this->tpl_img . '</a>';
							break;
					}
				}
				$tpl = file_get_contents($this->tpl_dir . DS . $file . '.img.tpl');
				$this->tpl_img = str_replace(array('[MODULECLASS_SFX]', '[IMAGE]'), array($this->moduleclass_sfx, $this->tpl_img), $tpl);
			}
		}

		// обработка автора
		if($this->show_author){
			$this->tpl_aut = $this->getAuthor($row, $this->show_author);
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.aut.tpl');
			$this->tpl_aut = str_replace(array('[MODULECLASS_SFX]', '[AUTOR]'), array($this->moduleclass_sfx, $this->tpl_aut), $tpl);
		}

		// обработка текста
		if($this->text){
			switch($this->crop_text){
				case 'simbol':
					if($this->crop_text_format){
						$this->tpl_txt = $this->htmlSubstr($this->htmlImage($row->introtext), $this->crop_text_limit, "&hellip;");
					} else{
						$this->tpl_txt = Text::simple_clean($row->introtext);
						$this->tpl_txt = Text::character_limiter($this->tpl_txt, $this->crop_text_limit, '') . "&hellip;";
					}
					break;
				case 'word':
					$this->tpl_txt = Text::simple_clean($row->introtext);
					$this->tpl_txt = Text::word_limiter($this->tpl_txt, $this->crop_text_limit, '') . "&hellip;";
					break;
				default:
					$this->tpl_txt = $this->htmlImage($row->introtext);
			}
			if($this->text == 2){
				$this->tpl_txt = '<a href="' . $link . '">' . Text::simple_clean($this->tpl_txt) . '</a>';
			}
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.txt.tpl');
			$this->tpl_txt = str_replace(array('[MODULECLASS_SFX]', '[TEXT]'), array($this->moduleclass_sfx, $this->tpl_txt), $tpl);
		}

		// обработка кнопки Далее
		if($this->readmore){
			$this->tpl_but = '<a href="' . $link . '" title="' . strip_tags($row->title) . '">' . $this->link_text . '</a>';
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.but.tpl');
			$this->tpl_but = str_replace(array('[MODULECLASS_SFX]', '[BUTTOM]'), array($this->moduleclass_sfx, $this->tpl_but), $tpl);
		}

		// обработка количество просмотров
		if($this->hits){
			$this->tpl_hit = _HITS . ': ' . $row->views;
			$tpl = file_get_contents($this->tpl_dir . DS . $file . '.hit.tpl');
			$this->tpl_hit = str_replace(array('[MODULECLASS_SFX]', '[HIT]'), array($this->moduleclass_sfx, $this->tpl_hit), $tpl);
		}
	}

	/**
	 * получение данных из таблицы
	 * @return bool $rows
	 */
	private function getContent(){
		$result = false;

		// общее количество статей
		$count = $this->count_special + $this->count_basic + $this->count_reference;

		if($count){
			// дополнительный фильтр по категориям
			if($this->catid){
				$catids = explode(',', $this->catid);
				mosArrayToInts($catids);
				$whereCatid = " AND ( a.catid=" . implode(" OR a.catid=", $catids) . " )";
			} else{
				$whereCatid = '';
			}

			// Определение сортировки
			switch($this->orderby){
				case 'date': // Сначала самые старые
					$orderby = 'a.date_created ASC';
					break;
				case 'rdate': // Сначала самые новые
					$orderby = 'a.date_created DESC';
					break;
				case 'alpha': // Заголовки по алфавиту
					$orderby = 'a.name ASC';
					break;
				case 'ralpha': // Заголовки в обратном порядке
					$orderby = 'a.name DESC';
					break;
				case 'hits': // сначала менее читаемые
					$orderby = 'a.views ASC';
					break;
				case 'rhits': // сначала самые читаемые
					$orderby = 'a.views DESC';
					break;
				case 'author': // Авторы (настоящее имя) по алфавиту
					$orderby = 'u.name ASC';
					break;
				case 'rauthor': // Авторы (настоящее имя) в обратном порядке алфавита
					$orderby = 'u.name DESC';
					break;
				case 'authorlog': // Авторы (логин) по алфавиту
					$orderby = 'u.username ASC';
					break;
				case 'rauthorlog': // Авторы (логин) в обратном порядке алфавита
					$orderby = 'u.username DESC';
					break;
				case 'rand': // В случайном порядуке
					$orderby = 'RAND()';
					break;
				default: // по умолчанию
					$orderby = 'a.id DESC';
					break;
			}

			// Определяем период времени
			if($this->time){
				$time = $this->time * 24 * 60 * 60;
				$time = ($time) ? "AND UNIX_TIMESTAMP(CURRENT_TIMESTAMP)-UNIX_TIMESTAMP(date_created) <= " . $time : '';
			} else{
				$time = '';
			}

			$sql = "SELECT a.id,
							a.name AS title,
							a.date_created,
							a." . $this->content_field . " AS introtext,
							a.userid,
							a.views,
			 				u.name AS autor_name,
			 				u.username AS autor_login,
			 				c.name AS cat_name,
			 				b.category_id
					FROM #__boss_" . $this->directory . "_contents AS a
					LEFT JOIN #__users AS u ON u.id = a.userid
					LEFT JOIN #__boss_" . $this->directory . "_content_category_href AS b ON b.content_id = a.id
					LEFT JOIN #__boss_" . $this->directory . "_categories AS c ON c.id = b.category_id
                    WHERE  a.published = 1
                    " . $whereCatid . "
                    " . $time . "
					ORDER BY " . $orderby;
			$result = $this->_database->setQuery($sql, 0, $count)->loadObjectList();
		}
		return $result;
	}

	/**
	 * Обрезка текста с сохранением форматирования
	 * @param        $html - текст
	 * @param        $length - длина
	 * @param string $hellip
	 * @return string
	 */
	private function htmlSubstr($html, $length, $hellip = ''){
		$out = '';
		$arr = preg_split('/(<.+?>|&#?\\w+;)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
		$tagStack = array();

		for($i = 0, $l = 0; $i < count($arr); $i++){
			if($i & 1){
				if(substr($arr[$i], 0, 2) == '</'){
					array_pop($tagStack);
				} elseif($arr[$i][0] == '&'){
					$l++;
				} elseif(substr($arr[$i], -2) != '/>'){
					array_push($tagStack, $arr[$i]);
				}

				$out .= $arr[$i];
			} else{
				if(($l += strlen($arr[$i])) >= $length){
					$out .= substr($arr[$i], 0, $length - $l + strlen($arr[$i]));
					break;
				} else{
					$out .= $arr[$i];
				}
			}
		}
		$out .= $hellip;
		while(($tag = array_pop($tagStack)) !== NULL){
			$out .= '</' . strtok(substr($tag, 1), " \t>") . '>';
		}

		return $out;
	}

	/**
	 * вырезаем <IMG> {mosimage} {hsimage} и другие
	 * @param  $str
	 * @return mixed
	 */
	private function htmlImage($str){
		$regex = array('#<img[^>]*src=(["\'])([^"\']*)\1[^>]*>#is', '#<video[^>]*>.*?</video>#si', '#<object[^>]*>.*?</object>#si', '#\{mosimage\}#', '#\{hsimage\}#');
		$str = preg_replace($regex, '', $str);
		return $str;
	}

	/**
	 * Получение автора
	 * @param        $row
	 * @param string $params
	 * @return string
	 */
	private function getAuthor($row, $params){
		if($row->userid != ''){
			switch($params){
				case '1':
				case '3':
					$result = $row->autor_name;
					break;

				case '2':
				case '4':
				default;
					$result = $row->autor_name;
					break;
			}
			if($params == '3' OR $params == '4'){
				$author_link = 'index.php?option=com_users&amp;task=profile&amp;user=' . $row->userid;
				$author_seflink = sefRelToAbs($author_link);
				$result = '<a href="' . $author_seflink . '">' . $result . '</a>';
			}
		} else{
			$result = _GUEST;
		}
		return $result;
	}
}