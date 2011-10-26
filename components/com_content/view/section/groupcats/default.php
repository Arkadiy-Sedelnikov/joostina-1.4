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

$k = 0; ?>
<div class="page_sectionblog<?php echo $sfx; ?>">
	<?php if ($header && $params->get('page_title')) { ?>
		<div class="componentheading"><h1><?php echo $header; ?></h1></div>
	<?php } ?>
		<?php if ($total) { ?>
		<div class="groupcats">
				<?php if ($display_desc) { ?>
				<div class="contentdescription">
					<?php if ($display_desc_img) { ?>
						<img src="<?php echo JPATH_SITE; ?>/images/stories/<?php echo $obj->image; ?>" align="<?php echo $obj->image_position; ?>" alt="<?php echo $obj->title; ?>" />
					<?php } ?>
					<?php if ($display_desc_text) { ?>
						<p><?php echo $obj->description; ?> </p>
				<?php } ?>
				</div>
			<?php } ?>
			<?php
			foreach ($cats_arr as $key => $v) {
				echo '<h2 class="category_name">' . $v['cat_name'] . '</h2>';
				echo '<table>';
				$kk = 0;
				echo '<tr>';
				foreach ($v['obj'] as $row) {
					echo '<td>';
					$params->set('page_type', 'item_intro_simple');
					_showItem($row, $params, $gid, $access, $pop, '[s]default.php');
					echo '</td>';
					$kk++;
					if ($kk % $columns == 0 && (isset($cats_arr[$row->catid]['obj'][$kk]) && $cats_arr[$row->catid]['obj'][$kk]->catid == $row->catid)) {
						echo "</tr><tr>";
					}
				}
				echo '</tr></table>';
				$cat_link = sefRelToAbs('index.php?option=com_content&amp;task=blogcategory&amp;id=' . $key . '&amp;Itemid=' . $_REQUEST['Itemid']);
				echo '<div class="readmore"><a class="readmore" href="' . $cat_link . '">все статьи</a></div>';
			}
			?>
		</div>
	<?php
	} else {
		echo _EMPTY_BLOG;
	}
	mosHTML::BackButton($params);
	?>
</div>