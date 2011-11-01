<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

global $mosConfig_offset,$my,$moduleclass_sfx;

$count		= intval( $params->get( 'count', 5 ) );
$catid		= trim( $params->get( 'catid' ) );
$secid		= trim( $params->get( 'secid' ) );
$noncss		= intval( $params->get( 'noncss',1 ) );
$show_front	= $params->get( 'show_front', 1 );
$show_hits	= $params->get( 'show_hits', 0 );
$def_itemid	= $params->get( 'def_itemid', false );

//определение каталога
$directory = mosGetParam($_REQUEST,'directory',0);
if($directory == 0){
    require_once ($mainframe->getPath('class', 'com_frontpage'));
    $configObject = new frontpageConfig();
    $directory = $configObject->get('directory', 0);
}

$now		= _CURRENT_SERVER_TIME;
$access	= !$mainframe->getCfg( 'shownoauth' );
$nullDate = $database->getNullDate();

	// Только объекты содержимого
		$whereCatid = '';
		if ($catid) {
			$catids = explode( ',', $catid );
			mosArrayToInts( $catids );
			$whereCatid = "\n AND ( cc.id=" . implode( " OR cc.id=", $catids ) . " )";
		}

		$query = "SELECT a.id, a.name as title, cc.id as catid, a.views as hits"
				. "\n FROM #__boss_" . $directory . "_contents AS a"
				. "\n INNER JOIN #__boss_" . $directory . "_content_category_href AS cch ON cch.content_id = a.id"
				. "\n INNER JOIN #__boss_" . $directory . "_categories AS cc ON cc.id = cch.category_id"
				. "\n WHERE a.published = 1"
				. "\n AND ( a.date_publish = " . $database->Quote( $nullDate ) . " OR a.date_publish <= " . $database->Quote( $now ) . " )"
				. "\n AND ( a.date_unpublish = " . $database->Quote( $nullDate ) . " OR a.date_unpublish >= " . $database->Quote( $now ) . " )"
				//. ( $access ? "\n AND a.access <= " . (int) $my->gid . " AND cc.access <= " . (int) $my->gid . " AND s.access <= " . (int) $my->gid : '' )
				. $whereCatid
				. ( $show_front == "0" ? "\n AND a.id = 0" : '' )

				. "\n AND cc.published = 1"
				. "\n ORDER BY hits DESC"
		;
		$database->setQuery( $query, 0, $count );
		$rows = $database->loadObjectList();


if(!$def_itemid>0) {
	// требование уменьшить запросы, используемые getItemid для объектов содержимого
		$bs	= $mainframe->getBlogSectionCount();
		$bc	= $mainframe->getBlogCategoryCount();
		$gbs	= $mainframe->getGlobalBlogSectionCount();
}

// Вывод
?>
<ul class="mostread<?php echo $moduleclass_sfx; ?>">
	<?php
	foreach ($rows as $row) {
		if(!$def_itemid>0) {
			// get Itemid
            $Itemid = $mainframe->getItemid( $row->id, 0, 0, $bs, $bc, $gbs );

		}else {
			$Itemid=$def_itemid;
		}
		// Очистка счетчика itemid для SEF
		if ($Itemid == NULL) {
			$Itemid = '';
		} else {
			$Itemid = '&amp;Itemid='.$Itemid;
		}

		$link = sefRelToAbs( 'index.php?option=com_boss&task=show_content&contentid='.$row->id.'&catid='.$row->catid.'&directory='. $directory . $Itemid );
		$class	= ($noncss ? '':' class="mostread'.$moduleclass_sfx.'"');
		?>
	<li<?php echo $class ?>>
		<a href="<?php echo $link; ?>" title="<?php echo $row->title; ?>"<?php echo $class ?>><?php echo $row->title; ?></a><?php echo $show_hits ? ' ('.$row->hits.')':'';?>
	</li><?php
	}
	?>
</ul>