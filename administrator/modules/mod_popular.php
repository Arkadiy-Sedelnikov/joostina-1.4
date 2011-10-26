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

$query = "SELECT a.hits, a.id, a.sectionid, a.title, a.created, u.name"."\n FROM #__content AS a".
		"\n LEFT JOIN #__users AS u ON u.id=a.created_by"."\n WHERE a.state != -2"."\n ORDER BY hits DESC";
$database->setQuery($query,0,10);
$rows = $database->loadObjectList();
?>

<table class="adminlist">
	<tr>
		<th class="title"><?php echo _POPULAR_CONTENT?></th>
		<th class="title"><?php echo _CREATED_CONTENT?></th>
		<th class="title"><?php echo _CONTENT_HITS?></th>
	</tr>
	<?php
	foreach($rows as $row) {
		if($row->sectionid == 0) {
			$link = 'index2.php?option=com_typedcontent&amp;task=edit&amp;hidemainmenu=1&amp;id='.
					$row->id;
		} else {
			$link = 'index2.php?option=com_content&amp;task=edit&amp;hidemainmenu=1&amp;id='.
					$row->id;
		}
		?>
	<tr>
		<td><a href="<?php echo $link; ?>"><?php echo htmlspecialchars($row->title,ENT_QUOTES); ?></a></td>
		<td><?php echo $row->created; ?></td>
		<td style="text-align: center"><?php echo $row->hits; ?></td>
	</tr>
		<?php
	}
	?>
	<tr><th colspan="3"></th></tr>
</table>