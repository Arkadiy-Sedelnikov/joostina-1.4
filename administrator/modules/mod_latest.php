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

global $my;
$mainframe = mosMainFrame::getInstance();
$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

// число объектов содержимого для вывода
$limit = $params->get('num',10);
$type = $params->get('type',0);
$ext = $params->get('ext',1);


$where = $ext ? "\n LEFT JOIN #__categories AS c ON c.id = a.catid LEFT JOIN #__sections AS s ON s.id = a.sectionid" : '';

switch ($type) {
	case 2:
		$where .= "\n WHERE a.state = 0"; // Только не опубликованное
		break;
	case 1:
		$where .= "\n WHERE a.state = 1"; // Только опубликованное
		break;
	case 0:
	default:
		$where .= "\n WHERE a.state != -2"; // Все элементы
		break;
}

$select = $ext ? "\n c.title AS catname, s.name AS secname," : '';
$query = "SELECT a.id, a.sectionid, a.title, a.created, $select u.name, a.created_by_alias, a.created_by, a.publish_up, a.publish_down, a.state"
		."\n FROM #__content AS a"
		."\n LEFT JOIN #__users AS u ON u.id = a.created_by"
		.$where
		."\n ORDER BY created DESC"; // сортировка по времени создания, новые первыми
$database->setQuery($query,0,$limit);
$rows = $database->loadObjectList();
?>

<table class="adminlist">
	<tr>
		<th colspan="3" class="title"><?php echo _LAST_ADDED_CONTENT?> <small>( <a href="index2.php?option=com_content&sectionid=0"><?php echo _ALL_CONTENT?></a> )</small></th>
		<th align="center"><?php echo _USER_WHO_ADD_CONTENT?></th>
	</tr>
	<?php
	$nullDate = $database->getNullDate();
	$now = _CURRENT_SERVER_TIME;
	$k = 0;
	foreach($rows as $row) {
		// получение значка статуса содержимого
		if($now <= $row->publish_up && $row->state == 1) {
			// опубликовано
			$img = 'publish_y.png';
		} else if(($now <= $row->publish_down || $row->publish_down == $nullDate) && $row->state ==1) {
			// Доступно
			$img = 'publish_g.png';
		} else if($now > $row->publish_down && $row->state == 1) {
			// Истекло
			$img = 'publish_r.png';
		} else if($row->state == 0) {
			// Не опубликовано
			$img = 'publish_x.png';
		}
		if($row->sectionid == 0) {
			// статичное содержимое
			$link = 'index2.php?option=com_typedcontent&amp;task=edit&amp;hidemainmenu=1&amp;id='.$row->id;
		} else {
			// обычное содержимое
			$link = 'index2.php?option=com_content&amp;task=edit&amp;hidemainmenu=1&amp;id='.$row->id;
		}
		if($acl->acl_check('administration','manage','users',$my->usertype,'components','com_users')) {
			if($row->created_by_alias) {
				$author = $row->created_by_alias;
			} else {
				$linkA = 'index2.php?option=com_users&task=editA&amp;hidemainmenu=1&id='.$row->created_by;
				$author = '<a href="'.$linkA.'" title="'._CHANGE_USER_DATA.'">'.htmlspecialchars($row->name,ENT_QUOTES).'</a>';
			}
		} else {
			if($row->created_by_alias) {
				$author = $row->created_by_alias;
			} else {
				$author = htmlspecialchars($row->name,ENT_QUOTES);
			}
		}
		?>
	<tr class="row<?php echo $k; ?>">
		<td width="10%" align="center"><?php echo $row->created; ?></td>
		<td align="left" width="60%">
			<a href="<?php echo $link; ?>"><?php echo htmlspecialchars($row->title,ENT_QUOTES); ?></a><br />
				<?php
				if($ext) {
					if($row->sectionid!=0)
						echo $row->secname.' / '.$row->catname; // раздел / категория
					else
						echo _STATIC_CONTENT; // тип добавленного содержимого - статичное содержимое
				}
				?>
		</td>
		<td width="5%" class="td-state" align="center" onclick="ch_publ(<?php echo $row->id;?>,'com_content');">
			<img id="img-pub-<?php echo $row->id;?>" class="img-mini-state" alt="<?php echo _PUBLISHING?>" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>"/>
		</td>
		<td align="left" width="20%"><?php echo $author; ?></td>
	</tr>
		<?php
		$k = 1 - $k;
	}
	unset($rows,$row);
	?>
</table>