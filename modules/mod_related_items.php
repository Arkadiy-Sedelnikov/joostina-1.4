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

global $mosConfig_offset, $option, $task;
$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();

$id = intval(mosGetParam($_REQUEST, 'contentid', null));
$directory = intval(mosGetParam($_REQUEST, 'directory', null));
$limit = $params->get('limit', 5);

$now = _CURRENT_SERVER_TIME;
$nullDate = $database->getNullDate();

if($option == 'com_boss' && $task == 'show_content' && $id && $directory){
	// выборка ключевых слов из объекта
	$query = 'SELECT meta_keys FROM #__boss_' . $directory . '_contents WHERE id = ' . (int)$id;
	$database->setQuery($query);
	if($metakey = trim($database->loadResult())){
		// разделить ключевые слова запятыми
		$keys = explode(',', $metakey);
		$likes = array();

		// собирание любых непустых слов
		foreach($keys as $key){
			$key = trim($key);
			if($key){
				$likes[] = $database->getEscaped($key, true);
			}
		}

		if(count($likes)){
			// select other items based on the metakey field 'like' the keys found
			$query = "SELECT a.id, a.name as title, cch.category_id as catid "
				. "\n FROM #__boss_" . $directory . "_contents AS a"
				. "\n LEFT JOIN #__boss_" . $directory . "_content_category_href AS cch ON cch.content_id = a.id"
				. "\n WHERE a.id != " . (int)$id
				. "\n AND a.published = 1"
				. "\n AND ( a.meta_keys LIKE '%" . implode("%' OR a.meta_keys LIKE '%", $likes) . "%' )"
				. "\n AND ( a.date_publish = " . $database->Quote($nullDate) . " OR a.date_publish <= " . $database->Quote($now) . " )"
				. "\n AND ( a.date_unpublish = " . $database->Quote($nullDate) . " OR a.date_unpublish >= " . $database->Quote($now) . " )"
				. "\n GROUP BY a.id";

			$database->setQuery($query, 0, $limit);
			$related = $database->loadObjectList();

			if(count($related)){
				?>
			<ul>
				<?php
				foreach($related as $item){

					$href = sefRelToAbs("index.php?option=com_boss&amp;task=show_content&amp;contentid=$item->id&amp;catid=$item->catid&amp;directory=$directory");
					?>
					<li>
						<a href="<?php echo $href; ?>"><?php echo $item->title; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
			}
		}
	}
}
unset($related, $item, $likes);