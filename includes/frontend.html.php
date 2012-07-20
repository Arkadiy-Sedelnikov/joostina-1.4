<?php
/**
 * @version $Id: module.php 5132 2006-09-22 15:59:38Z friesengeist $
 * @package Joostina
 * @localized Авторские права (C) 2005 Joom.Ru - Русский дом Joomla!
 * @copyright Авторские права (C) 2005 Open Source Matters. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, смотрите LICENSE.php
 * Joomla! - свободное программное обеспечение. Эта версия может быть изменена
 * в соответствии с Генеральной Общественной Лицензией GNU, поэтому возможно
 * её дальнейшее распространение в составе результата работы, лицензированного
 * согласно Генеральной Общественной Лицензией GNU или других лицензий свободных
 * программ или программ с открытым исходным кодом.
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined('_JLINDEX') or die();

/**
 * @package Joostina
 */
class modules_html{

	var $_mainframe = null;

	function modules_html($mainframe){
		$this->_mainframe = $mainframe;
	}

	/**
	 * Вывод пользовательских модулей
	 * @param  $module
	 * @param  $params
	 * @return void
	 * @modification 19.02.2012 GoDr
	 */
	function module($module, $params){
		$_MAMBOTS = mosMambotHandler::getInstance();

		$moduleclass_sfx = $params->get('moduleclass_sfx');
		$rssurl = $params->get('rssurl');
		$firebots = $params->get('firebots', 0);

		if($rssurl){
			// feed output
			modules_html::modoutput_feed($module, $params, $moduleclass_sfx);
		}

		if($module->content != '' && $firebots){
			$_MAMBOTS->loadBotGroup('content');
			$row = $module;
			$row->text = $module->content;
			$results = $_MAMBOTS->trigger('onPrepareContent', array(&$row, &$params, 0), true);
			$module->content = $row->text;
		}

		$module = mosModule::convert_to_object($module, $this->_mainframe);
		modules_html::modoutput_xhtml($module, $params, $moduleclass_sfx, 1);
	}

	/**
	 * Вывод стандартных модулей
	 * @param  $module
	 * @param  $params
	 * @return void
	 * @modification 19.02.2012 GoDr
	 */
	function module2($module, $params){
		$config = $this->_mainframe->config;

		$moduleclass_sfx = $params->get('moduleclass_sfx');

		$path = JPATH_BASE . DS . 'language' . DS . $config->config_lang . DS . 'frontend' . DS . $module->module . '.php';
		$path_def = JPATH_BASE . DS . 'language/russian/frontend' . DS . $module->module . '.php';

		file_exists($path) ? include_once ($path) : (file_exists($path_def) ? include_once ($path_def) : null);

		$module = mosModule::convert_to_object($module, $this->_mainframe);

		modules_html::modoutput_xhtml($module, $params, $moduleclass_sfx);
	}

	// feed output
	function modoutput_feed(&$module, &$params, $moduleclass_sfx){
		$config = $this->_mainframe->get('config');

		// check if cache directory is writeable
		$cacheDir = $config->config_cachepath . DS;
		if(!is_writable($cacheDir)){
			$module->content = _CACHE_DIR_IS_NOT_WRITEABLE2;
			return;
		}

		$rssurl = $params->get('rssurl');
		$rssitems = $params->get('rssitems', 5);
		$rssdesc = $params->get('rssdesc', 1);
		$rssimage = $params->get('rssimage', 1);
		$rssitemdesc = $params->get('rssitemdesc', 1);
		$words = $params->def('word_count', 0);
		$rsstitle = $params->get('rsstitle', 1);
		$rsscache = $params->get('rsscache', 3600);

		$contentBuffer = '';

		$LitePath = JPATH_BASE . '/includes/Cache/Lite.php';
		require_once (JPATH_BASE . '/includes/domit/xml_domit_rss.php');

		$rssDoc = new xml_domit_rss_document();
		$rssDoc->setRSSTimeout(2);
		$rssDoc->useCacheLite(false, $LitePath, $cacheDir, $rsscache);
		$success = $rssDoc->loadRSS($rssurl);

		if($success){
			$content_buffer = '';
			$totalChannels = $rssDoc->getChannelCount();

			for($i = 0; $i < $totalChannels; $i++){
				$currChannel = &$rssDoc->getChannel($i);
				$elements = $currChannel->getElementList();
				$iUrl = 0;
				foreach($elements as $element){
					//image handling
					if($element == 'image'){
						$image = &$currChannel->getElement(DOMIT_RSS_ELEMENT_IMAGE);
						$iUrl = $image->getUrl();
						$iTitle = $image->getTitle();
					}
				}

				// feed title
				$content_buffer = '<table cellpadding="0" cellspacing="0" class="moduletable' . $moduleclass_sfx . '">' . "\n";

				if($currChannel->getTitle() && $rsstitle){
					$feed_title = $currChannel->getTitle();
					$feed_title = mosCommonHTML::newsfeedEncoding($rssDoc, $feed_title);

					$content_buffer .= "<tr>\n";
					$content_buffer .= "	<td>\n";
					$content_buffer .= "		<strong>\n";
					$content_buffer .= "		<a href=\"" . ampReplace($currChannel->getLink()) . "\" target=\"_blank\">\n";
					$content_buffer .= $feed_title . "</a>\n";
					$content_buffer .= "		</strong>\n";
					$content_buffer .= "	</td>\n";
					$content_buffer .= "</tr>\n";
				}

				// feed description
				if($rssdesc){
					$feed_descrip = $currChannel->getDescription();
					$feed_descrip = mosCommonHTML::newsfeedEncoding($rssDoc, $feed_descrip);

					$content_buffer .= "<tr>\n";
					$content_buffer .= "	<td>\n";
					$content_buffer .= $feed_descrip;
					$content_buffer .= "	</td>\n";
					$content_buffer .= "</tr>\n";
				}

				// feed image
				if($rssimage && $iUrl){
					$content_buffer .= "<tr>\n";
					$content_buffer .= "	<td align=\"center\">\n";
					$content_buffer .= "		<image src=\"" . $iUrl . "\" alt=\"" . @$iTitle . "\"/>\n";
					$content_buffer .= "	</td>\n";
					$content_buffer .= "</tr>\n";
				}

				$actualItems = $currChannel->getItemCount();
				$setItems = $rssitems;

				if($setItems > $actualItems){
					$totalItems = $actualItems;
				} else{
					$totalItems = $setItems;
				}

				$content_buffer .= "<tr>\n";
				$content_buffer .= "	<td>\n";
				$content_buffer .= "		<ul class=\"newsfeed" . $moduleclass_sfx . "\">\n";

				for($j = 0; $j < $totalItems; $j++){
					$currItem = &$currChannel->getItem($j);
					// item title

					$item_title = $currItem->getTitle();
					$item_title = mosCommonHTML::newsfeedEncoding($rssDoc, $item_title);

					// START fix for RSS enclosure tag url not showing
					$content_buffer .= "<li class=\"newsfeed" . $moduleclass_sfx . "\">\n";
					$content_buffer .= "	<strong>\n";
					if($currItem->getLink()){
						$content_buffer .= "<a href=\"" . ampReplace($currItem->getLink()) . "\" target=\"_blank\">\n";
						$content_buffer .= "  " . $item_title . "</a>\n";
					} else
						if($currItem->getEnclosure()){
							$enclosure = $currItem->getEnclosure();
							$eUrl = $enclosure->getUrl();
							$content_buffer .= "<a href=\"" . ampReplace($eUrl) . "\" target=\"_blank\">\n";
							$content_buffer .= " " . $item_title . "</a>\n";
						} else
							if(($currItem->getEnclosure()) && ($currItem->getLink())){
								$enclosure = $currItem->getEnclosure();
								$eUrl = $enclosure->getUrl();
								$content_buffer .= "  <a href=\"" . ampReplace($currItem->getLink()) . "\" target=\"_blank\">\n";
								$content_buffer .= "      " . $item_title . "</a><br/>\n";
								$content_buffer .= "   <a href=\"" . ampReplace($eUrl) . "\" target=\"_blank\"><u>Download</u></a>\n";
							}
					$content_buffer .= "</strong>\n";
					// END fix for RSS enclosure tag url not showing
					// item description
					if($rssitemdesc){
						// item description
						$text = $currItem->getDescription();
						$text = mosCommonHTML::newsfeedEncoding($rssDoc, $text);
						// word limit check
						if($words){
							$texts = explode(' ', $text);
							$count = count($texts);
							if($count > $words){
								$text = '';
								for($i = 0; $i < $words; $i++){
									$text .= ' ' . $texts[$i];
								}
								$text .= '...';
							}
						}
						$content_buffer .= "<div>\n";
						$content_buffer .= "  " . $text;
						$content_buffer .= "</div>\n";
					}
					$content_buffer .= "</li>\n";
				}
				$content_buffer .= "</ul>\n";
				$content_buffer .= "</td>\n";
				$content_buffer .= "</tr>\n";
				$content_buffer .= "</table>\n";
			}
			$module->content = $content_buffer;
		}
	}

	/**
	 * Вывод конкретного модуля
	 * @param     $module
	 * @param     $params
	 * @param     $moduleclass_sfx
	 * @param int $type
	 * @return void
	 * @modification 19.02.2012 GoDr
	 */
	function modoutput_xhtml($module, $params, $moduleclass_sfx, $type = 0){
		$mainframe = mosMainFrame::getInstance();
		$database = database::getInstance();

		echo '<div class="moduletable' . $moduleclass_sfx . '" id="module_' . $module->id . '">';
		if($module->showtitle != 0){
			echo '<h3>' . htmlspecialchars($module->title) . '</h3>';
		}
		if($type){
			modules_html::CustomContent($module, $params);
		} else{
			include (JPATH_BASE . DS . 'modules' . DS . $module->module . '.php');
			if(isset($content)){
				echo $content;
			}
		}
		echo '</div>';
	}

	function CustomContent(&$module, $params){
		$_MAMBOTS = mosMambotHandler::getInstance();

		$firebots = $params->get('firebots', 0);

		if($firebots){
			$row = $module;
			$row->text = $module->content;
			$results = $_MAMBOTS->trigger('onBeforeDisplayContent', array(&$row, &$params, 0));
			echo trim(implode("\n", $results));
			$module->content = $row->text;
		}
		// output custom module contents
		if($params->get('user_template', '') && $module->set_template_custom($params->get('user_template', ''))){
			require($module->template);
		} else{
			echo $module->content;
		}

		if($firebots){
			$results = $_MAMBOTS->trigger('onAfterDisplayContent', array(&$row, &$params, 0));
			echo trim(implode("\n", $results));

			$module->content = $row->text;
		}
	}
}