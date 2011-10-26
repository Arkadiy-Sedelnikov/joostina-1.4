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


/**
 * @package Joostina
 * @subpackage Polls
 */
class poll_html {
	
	public static function showResults(&$poll, &$votes, $first_vote, $last_vote, $pollist, $params) {
		mosMainFrame::getInstance()->addCSS(JPATH_SITE.'/components/com_poll/css/poll.css');
		?>
<div class="polls <?php echo $params->get('pageclass_sfx'); ?>">

	<div class="componentheading">
		<h1>
					<?php if($params->get('page_title', '')) {?>
						<?php echo $params->get('header'); ?>
						<?php } else { ?>
						<?php echo _POLLS ?>
						<?php }?>
		</h1>
	</div>

	<form action="index.php" method="post" name="poll" id="poll">
		<div class="contentpane<?php echo $params->get('pageclass_sfx'); ?>">
					<?php echo _SEL_POLL; ?>&nbsp;<?php echo $pollist; ?><br />
					<?php
					if($votes) {
						$j = 0;
						$data_arr["text"] = null;
						$data_arr["hits"] = null;
						$data_arr['voters'] = null;
						foreach($votes as $vote) {
							$data_arr["text"][$j] = trim($vote->text);
							$data_arr["hits"][$j] = $vote->hits;
							$data_arr["voters"][$j] = $vote->voters;
							$j++;
						}
						poll_html::graphit($data_arr, $poll->title, $first_vote, $last_vote);
					}
					?>
		</div>
	</form>
			<?php mosHTML::BackButton($params); ?>
</div>
		<?php }


	public static function graphit($data_arr, $graphtitle, $first_vote, $last_vote) {
		global  $polls_maxcolors, $tabclass, $polls_barheight, $polls_graphwidth, $polls_barcolor;

		$tabclass_arr = explode(",", $tabclass);
		$tabcnt = 1;
		$colorx = 0;
		$maxval = 0;

		array_multisort($data_arr["hits"], SORT_NUMERIC, SORT_DESC, $data_arr["text"]);

		foreach($data_arr["hits"] as $hits) {
			if($maxval < $hits) {
				$maxval = $hits;
			}
		}
		?>
<div class="poll_quest"><h4><?php echo $graphtitle; ?>  </h4></div>
<table class="com_poll" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<th class="poll_question"><?php echo _POLL_OPTION ?></th>
		<th class="poll_hits"><?php _POLL_VOTES?></th>
		<th class="poll_percent">%</th>
		<th class="poll_graph"><?php echo _POLL_SCHEDULE ?></th>
	</tr>
			<?php
			$_n = count($data_arr["text"]);
			for($i = 0, $n = $_n; $i < $n; $i++) {
				$text = &$data_arr["text"][$i];
				$hits = &$data_arr["hits"][$i];
				$sumval = &$data_arr['voters'][$i];
				if($maxval > 0 && $sumval > 0) {
					$width = ceil($hits * $polls_graphwidth / $maxval);
					$percent = round(100 * $hits / $sumval, 1);
				} else {
					$width = 0;
					$percent = 0;
				}
				?>
	<tr class="<?php echo $tabclass_arr[$tabcnt]; ?>">
		<td class="poll_question"><?php echo stripslashes($text); ?></td>
		<td class="poll_hits"><?php echo $hits; ?></td>
		<td class="poll_percent"><?php echo $percent; ?></td>
					<?php
					$tdclass = '';
					if($polls_barcolor == 0) {
						if($colorx < $polls_maxcolors) {
							$colorx = ++$colorx;
						} else {
							$colorx = 1;
						}
						$tdclass = "polls_color_" . $colorx;
					} else {
						$tdclass = "polls_color_" . $polls_barcolor;
					}
					?>
		<td class="poll_graph">&nbsp;
			<img src="<?php echo JPATH_SITE; ?>/components/com_poll/images/blank.png" class="<?php echo $tdclass; ?>" height="<?php echo $polls_barheight; ?>" width="<?php echo $width; ?>" alt="" />
		</td>
	</tr>
				<?php
				$tabcnt = 1 - $tabcnt;
			}
			?>
</table>
<br />
<div class="poll_dop_info">
	<b><?php echo _NUM_VOTERS; ?></b>&nbsp;<?php echo $sumval; ?> <br />
	<b><?php echo _FIRST_VOTE; ?></b>&nbsp;<?php echo $first_vote; ?> <br />
	<b><?php echo _LAST_VOTE; ?></b>&nbsp;<?php echo $last_vote; ?> <br />
</div>
<br />
		<?php
	}
}