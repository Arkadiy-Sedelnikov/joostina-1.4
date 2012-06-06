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
 * @subpackage Statistics
 */
class HTML_statistics {
	function show(&$browsers,&$platforms,$tldomains,$bstats,$pstats,$dstats,$sorts,$option) {
		$tab = mosGetParam($_REQUEST,'tab','tab1');
		$width = 400; // width of 100%
		$tabs = new mosTabs(1);
		?>
<style type="text/css">
	.bar_1{ background-color: #8D1B1B; border: 2px ridge #B22222; }
	.bar_2{ background-color: #6740E1; border: 2px ridge #4169E1; }
	.bar_3{ background-color: #8D8D8D; border: 2px ridge #D2D2D2; }
	.bar_4{ background-color: #CC8500; border: 2px ridge #FFA500; }
	.bar_5{ background-color: #5B781E; border: 2px ridge #6B8E23; }
</style>
<table class="adminheading">
	<tr>
		<th class="browser"><?php echo _STAT_BROWSERS_AND_OSES?></th>
	</tr>
</table>
<form action="index2.php" method="post" name="adminForm">
			<?php
			$tabs->startPane("statsPane");
			$tabs->startTab(_BROWSERS,"browsers-page");
			?>
	<table class="adminlist">
		<tr>
			<th align="left">&nbsp;<?php echo _BROWSER?> <?php echo $sorts['b_agent']; ?></th>
			<th>&nbsp;</th>
			<th width="100" align="left">% <?php echo $sorts['b_hits']; ?></th>
			<th width="100" align="left">#</th>
		</tr>
				<?php
				$c = 1;
				if(is_array($browsers) && count($browsers) > 0) {
					$k = 0;
					foreach($browsers as $b) {
						$f = $bstats->totalhits > 0?$b->hits / $bstats->totalhits:0;
						$w = $width* $f;
						?>
		<tr class="row<?php echo $k; ?>">
			<td width="200" align="left">&nbsp;<?php echo $b->agent; ?>&nbsp;</td>
			<td align="left" width="<?php echo $width + 10; ?>">
				<div align="left">&nbsp;<img src="<?php echo JPATH_SITE; ?>/components/com_poll/images/blank.png" class="bar_<?php echo $c; ?>" height="6" width="<?php echo $w; ?>"></div>
			</td>
			<td align="left"><?php printf("%.2f%%",$f* 100); ?></td>
			<td align="left"><?php echo $b->hits; ?></td>
		</tr>
						<?php
						$c = $c % 5 + 1;
						$k = 1 - $k;
					}
				}
				?>
		<tr>
			<th colspan="4">&nbsp;</th>
		</tr>
	</table>
			<?php
			$tabs->endTab();
			$tabs->startTab(_OS,"os-page");
			?>
	<table class="adminlist">
		<tr>
			<th align="left"><?php echo _OS?> <?php echo $sorts['o_agent']; ?></th>
			<th>&nbsp;</th>
			<th width="100" align="left">% <?php echo $sorts['o_hits']; ?></th>
			<th width="100" align="left">#</th>
		</tr>
				<?php
				$c = 1;
				if(is_array($platforms) && count($platforms) > 0) {
					$k = 0;
					foreach($platforms as $p) {
						$f = $pstats->totalhits > 0?$p->hits / $pstats->totalhits:0;
						$w = $width* $f;
						?>
		<tr class="row<?php echo $k; ?>">
			<td width="200" align="left">&nbsp;<?php echo $p->agent; ?>&nbsp;</td>
			<td align="left" width="<?php echo $width + 10; ?>">
				<div align="left">&nbsp;<img src="<?php echo JPATH_SITE; ?>/components/com_poll/images/blank.png" class="bar_<?php echo $c; ?>" height="6" width="<?php echo $w; ?>"></div>
			</td>
			<td align="left"><?php printf("%.2f%%",$f* 100); ?></td>
			<td align="left"><?php echo $p->hits; ?></td>
		</tr>
						<?php
						$c = $c % 5 + 1;
						$k = 1 - $k;
					}
				}
				?>
		<tr>
			<th colspan="4">&nbsp;</th>
		</tr>
	</table>
			<?php
			$tabs->endTab();
			$tabs->startTab(_DOMAINS,"domain-page");
			?>
	<table class="adminlist">
		<tr>
			<th align="left">&nbsp;<?php echo _DOMAIN?> <?php echo $sorts['d_agent']; ?></th>
			<th>&nbsp;</th>
			<th width="100" align="left">% <?php echo $sorts['d_hits']; ?></th>
			<th width="100" align="left">#</th>
		</tr>
				<?php
				$c = 1;
				if(is_array($tldomains) && count($tldomains) > 0) {
					$k = 0;
					foreach($tldomains as $b) {
						$f = $dstats->totalhits > 0?$b->hits / $dstats->totalhits:0;
						$w = $width* $f;
						?>
		<tr class="row<?php echo $k; ?>">
			<td width="200" align="left">
				&nbsp;<?php echo $b->agent; ?>&nbsp;
			</td>
			<td align="left" width="<?php echo $width + 10; ?>">
				<div align="left">&nbsp;<img src="<?php echo JPATH_SITE; ?>/components/com_poll/images/blank.png" class="bar_<?php echo $c; ?>" height="6" width="<?php echo $w; ?>"></div>
			</td>
			<td align="left"><?php printf("%.2f%%",$f* 100); ?></td>
			<td align="left"><?php echo $b->hits; ?></td>
		</tr>
						<?php
						$c = $c % 5 + 1;
						$k = 1 - $k;
					}
				}
				?>
		<tr>
			<th colspan="4">&nbsp;</th>
		</tr>
	</table>
			<?php
			$tabs->endTab();
			$tabs->endPane();
			?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="tab" value="<?php echo $tab; ?>" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}

	function pageImpressions(&$rows,$pageNav,$option,$task) {
		?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminheading">
	<tr>
		<th width="100%" class="impressions"><?php echo _PAGES_HITS?></th>
	</tr>
</table>

<form action="index2.php" method="post" name="adminForm">
	<table class="adminlist">
		<tr>
			<th style="text-align:right">#</th>
			<th class="title"><?php echo _CONTENT_TITLE?></th>
			<th align="center" class="jtd_nowrap"><?php echo _HITS?></th>
		</tr>
				<?php
				$i = $pageNav->limitstart;
				$k = 0;
				foreach($rows as $row) {
					?>
		<tr class="row<?php echo $k; ?>">
			<td align="right">
							<?php echo ++$i; ?>
			</td>
			<td align="left">
				&nbsp;<?php echo $row->title." (".$row->created.")"; ?>&nbsp;
			</td>
			<td align="center"><?php echo $row->hits; ?></td>
		</tr>
					<?php
					$k = 1 - $k;
				}
				?>
	</table>
			<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}

	function showSearches(&$rows,$pageNav,$option,$task,$showResults) {
		$mainframe = mosMainFrame::getInstance();;
		mosCommonHTML::loadOverlib();
		?>
<form action="index2.php" method="post" name="adminForm">
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminheading">
		<tr>
			<th class="searchtext">
						<?php echo _SEARCH_QUERIES?>:
				<span class="componentheading"><?php echo _LOG_SEARCH_QUERIES?>:
							<?php echo $mainframe->getCfg('enable_log_searches')?'<b><span style="color:green">'._ALLOWED.'</span></b>':'<b><span style="color:#ff0000">'._DISALLOWED.'</span></b>' ?>
				</span>
			</th>
			<td align="right">
						<?php
						if(!$showResults) {
							echo mosWarning(_LOG_LOW_PERFOMANCE);
						}
						?>
			</td>
			<td align="right">
						<?php
						if($showResults) {
							?>
				<input name="search_results" type="button" class="button" value="<?php echo _HIDE_SEARCH_RESULTS?>" onclick="submitbutton('searches');">
							<?php
						} else {
							?>
				<input name="search_results" type="button" class="button" value="<?php echo _SHOW_SEARCH_RESULTS?>" onclick="submitbutton('searchesresults');">
							<?php
						}
						?>
			</td>
		</tr>
	</table>

	<table class="adminlist">
		<tr>
			<th style="text-align:right" width="10">#</th>
			<th class="title"><?php echo _SEARCH_QUERY_TEXT?></th>
			<th class="jtd_nowrap"><?php echo _SEARCH_QUERY_COUNT?></th>
					<?php
					if($showResults) {
						?>
			<th class="jtd_nowrap"><?php echo _SHOW_RESULTS?></th>
						<?php
					}
					?>
		</tr>
				<?php
				$k = 0;
				$_n = count($rows);
				for($i = 0,$n = $_n; $i < $n; $i++) {
					$row = &$rows[$i];
					?>
		<tr class="row<?php echo $k; ?>">
			<td align="right"><?php echo $i + 1 + $pageNav->limitstart; ?></td>
			<td align="left"><?php echo $row->search_term; ?></td>
			<td align="center"><?php echo $row->hits; ?></td>
						<?php
						if($showResults) {
							?>
			<td align="center"><?php echo $row->returns; ?></td>
							<?php
						}
						?>
		</tr>
					<?php
					$k = 1 - $k;
				}
				?>
	</table>
			<?php echo $pageNav->getListFooter(); ?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />
	<input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
</form>
		<?php
	}
}