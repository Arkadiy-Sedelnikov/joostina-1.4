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

$_MAMBOTS->registerFunction('onBeforeDisplayContent', 'pluginJWAjaxVote');

function pluginJWAjaxVote(&$row, &$params){
	$mainframe = mosMainFrame::getInstance();

	// подключаем файл локализации
	include_once($mainframe->getLangFile('com_poll'));

	$id = $row->id;
	$result = 0;
	$return = '';
	if($params->get('rating') && !$params->get('popup')){
		$vote = new stdClass;
		$vote->rating_count = $row->rating_count;
		$vote->rating_sum = $row->rating;
		if($vote->rating_count != 0) $result = number_format(intval($vote->rating_sum), 2) * 20;
		$rating_sum = intval($vote->rating_sum);
		$rating_count = intval($vote->rating_count);
		$thmess = $mainframe->config->config_caching ? _AV_THANKS_CACHE : _AV_THANKS;
		$script = '<script type="text/javascript">var live_site = \'' . JPATH_SITE . '\';var jwajaxvote_lang = new Array();jwajaxvote_lang[\'UPDATING\'] = \'' . _AV_UPDATING . '\';jwajaxvote_lang[\'THANKS\'] = \'' . $thmess . '\';jwajaxvote_lang[\'ALREADY_VOTE\'] = \'' . _AV_ALREADY_VOTE . '\';jwajaxvote_lang[\'VOTES\'] = \'' . _POLL_VOTES . '\';jwajaxvote_lang[\'VOTE\'] = \'' . _AV_VOTE . '\';</script>' . "\n";
		$script .= '<script type="text/javascript" src="' . JPATH_SITE . '/mambots/content/plugin_jw_ajaxvote/js/ajaxvote.js"></script>' . "\n";
		if(!defined('_ADD_SCRIPT_AJAXVOTE')){
			define('_ADD_SCRIPT_AJAXVOTE', 1);
			/* при включенном кэшировании выведем подключение js кода вместе с первым выводом кнопок голосования*/
			if($mainframe->config->config_caching){
				$return .= $script;
			} else{ // если кэширование не активно - добавим js код в заголовок страницы - так правильнее
				$mainframe->addCustomHeadTag($script);
			}
		}
		$return .= '
		<div class="jwajaxvote-inline-rating">
			<ul class="jwajaxvote-star-rating">
				<li id="rating' . $id . '" class="current-rating" style="width:' . $result . '%;"></li>
				<li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote(' . $id . ',1,' . $rating_sum . ',' . $rating_count . ');" title="1 балл из 5" class="one-star">&nbsp;</a></li>
				<li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote(' . $id . ',2,' . $rating_sum . ',' . $rating_count . ');" title="2 балла из 5" class="two-stars">&nbsp;</a></li>
				<li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote(' . $id . ',3,' . $rating_sum . ',' . $rating_count . ');" title="3 балла из 5" class="three-stars">&nbsp;</a></li>
				<li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote(' . $id . ',4,' . $rating_sum . ',' . $rating_count . ');" title="4 балла из 5" class="four-stars">&nbsp;</a></li>
				<li><a href="javascript:void(null)" onclick="javascript:jwAjaxVote(' . $id . ',5,' . $rating_sum . ',' . $rating_count . ');" title="5 баллов из 5" class="five-stars">&nbsp;</a></li>
			</ul>
			<div id="jwajaxvote' . $id . '" class="jwajaxvote-box">';
		if($rating_count != 1){
			$return .= '(' . $rating_count . ' ' . _POLL_VOTES . ')';
		} else{
			$return .= '(' . $rating_count . ' ' . _AV_VOTE . ')';
		}
		$return .= '
			</div>
		</div>
		<div class="jwajaxvote-clr"></div>';
		$row->rating = $return;
	}
}