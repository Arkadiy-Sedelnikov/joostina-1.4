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
 * @subpackage Newsfeeds
 */
class HTML_newsfeed {

	public static function displaylist(&$categories,&$rows,$catid,$currentcat = null,&$params,$tabclass) {
		global $Itemid,$hide_js;
		?>
<div class="newsfeeds <?php echo $params->get('pageclass_sfx'); ?>">
			<?php if($params->get('page_title')) { ?>
	<div class="componentheading"><h1><?php echo $currentcat->header; ?></h1></div>
				<?php } ?>

	<form action="index.php" method="post" name="adminForm">

				<?php if ($currentcat->descrip || $currentcat->img) {?>
		<div class="contentdescription">
						<?php if($currentcat->img) { ?>
			<img src="<?php echo $currentcat->img; ?>" align="<?php echo $currentcat->align; ?>" hspace="6" alt="<?php echo _LINKS; ?>" />
							<?php } ?>

						<?php echo $currentcat->descrip; ?>
		</div>
					<?php } ?>

				<?php if(count($rows)) { ?>
		<div class="newsfeeds_list">
						<?php HTML_newsfeed::showTable($params,$rows,$catid,$tabclass); ?>
		</div>
					<?php } ?>

		<div class="newsfeeds_cats">
					<?php if(($params->get('type') == 'category') && $params->get('other_cat')) {
						HTML_newsfeed::showCategories($params,$categories,$catid);
					} else
					if(($params->get('type') == 'section') && $params->get('other_cat_section')) {
						HTML_newsfeed::showCategories($params,$categories,$catid);
					}
					?>
		</div>

	</form>

			<?php mosHTML::BackButton($params,$hide_js); ?>

</div>
		<?php	}

	/**
	 * Display Table of items
	 */
	public static  function showTable(&$params,&$rows,$catid,$tabclass) {
		global $Itemid;
		// icon in table display
		$img = mosAdminMenus::ImageCheck('con_info.png','/images/M_images/',$params->get('icon'));

		?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">

			<?php if($params->get('headings')) { ?>
	<tr>
					<?php if($params->get('name')) { ?>
		<th><?php echo _FEED_NAME; ?></th>
						<?php } ?>

					<?php if($params->get('articles')) { ?>
		<th><?php echo _FEED_ARTICLES; ?></th>
						<?php } ?>

					<?php if($params->get('link')) { ?>
		<th><?php echo _FEED_LINK; ?></th>
						<?php } ?>
	</tr>
				<?php } ?>
			<?php
			$k = 0;

			foreach($rows as $row) {
				$link = 'index.php?option=com_newsfeeds&amp;task=view&amp;feedid='.$row->id.'&amp;Itemid='.$Itemid;
				?>

	<tr>
					<?php if($params->get('name')) { ?>
		<td height="20" class="<?php echo $tabclass[$k]; ?>">
			<a href="<?php echo sefRelToAbs($link); ?>" class="category<?php echo $params->get('pageclass_sfx'); ?>"><?php echo $row->name; ?></a>
		</td>
						<?php } ?>

					<?php if($params->get('articles')) { ?>
		<td width="20%" class="<?php echo $tabclass[$k]; ?>" align="center">
							<?php echo $row->numarticles; ?>
		</td>
						<?php } ?>

					<?php if($params->get('link')) { ?>
		<td width="50%" class="<?php echo $tabclass[$k]; ?>">
							<?php echo ampReplace($row->link); ?>
		</td>
						<?php } ?>

	</tr>

				<?php $k = 1 - $k;
		} ?>

</table>
		<?php
	}

	/**
	 * Display links to categories
	 */
	public static function showCategories(&$params,&$categories,$catid) {
		global $Itemid;
		?>

<ul>
			<?php
			foreach($categories as $cat) {

			if($catid == $cat->catid) { ?>
	<li>
		<b><?php echo $cat->title; ?></b>
		&nbsp;
		<span class="small">(<?php echo $cat->numlinks; ?>)</span>
	</li>

					<?php
				} else {
					$link = 'index.php?option=com_newsfeeds&amp;catid='.$cat->catid.'&amp;Itemid='.
							$Itemid;
				?>
	<li>
		<a href="<?php echo sefRelToAbs($link); ?>" class="category<?php echo $params->get('pageclass_sfx'); ?>">
				<?php echo $cat->title; ?>
		</a>
				<?php if($params->get('cat_items')) { ?>
		&nbsp;
		<span class="small">
						(<?php echo $cat->numlinks; ?>)
		</span>
					<?php } ?>

						<?php if($params->get('cat_description')) {
							echo '<br />';
							echo $cat->description;
				} ?>

	</li>
					<?php }
			}

		?>
</ul>
		<?php
	}


	//TODO:бардак в выводе - переделать в 1.3.1
	public static function showNewsfeeds(&$newsfeed,$LitePath,$cacheDir,&$params) {
		?>
<div class="newsfeeds_show">
		<?php if($params->get('header')) { ?>
	<div class="componentheading"><h1><?php echo $params->get('header'); ?></h1></div>
				<?php } ?>
			<?php
			// full RSS parser used to access image information
			$rssDoc = new xml_domit_rss_document();
			$rssDoc->setRSSTimeout(5);

			$rssDoc->useCacheLite(false,$LitePath,$cacheDir,$newsfeed->cache_time);
			$success = $rssDoc->loadRSS($newsfeed->link);

			$utf8enc = $newsfeed->code;
			if($success) {
				$totalChannels = $rssDoc->getChannelCount();

				for($i = 0; $i < $totalChannels; $i++) {
					$currChannel = &$rssDoc->getChannel($i);
					$elements = $currChannel->getElementList();
					$descrip = 0;
					$iUrl = 0;

					foreach($elements as $element) {
						//image handling
						if($element == 'image') {
							$image = &$currChannel->getElement(DOMIT_RSS_ELEMENT_IMAGE);
							$iUrl = $image->getUrl();
							$iTitle = $image->getTitle();
						}
						if($element == 'description') {
							$descrip = 1;
							// hide com_rss descrip in 4.5.0 feeds
							if($currChannel->getDescription() == 'com_rss') {
								$descrip = 0;
							}
						}
					}
					$feed_title = $currChannel->getTitle();
					$feed_title = mosCommonHTML::newsfeedEncoding($rssDoc,$feed_title,$utf8enc);
				?>
	<div class="contentdescription">
		<h2><a href="<?php echo ampReplace($currChannel->getLink()); ?>" target="_blank"><?php echo $feed_title; ?></a></h2>
						<?php if($descrip && $params->get('feed_descr')) {
							$feed_descrip = $currChannel->getDescription();
					$feed_descrip = mosCommonHTML::newsfeedEncoding($rssDoc,$feed_descrip,$utf8enc); ?>

					<?php if($iUrl && $params->get('feed_image')) { ?>
		<img src="<?php echo $iUrl; ?>" alt="<?php echo mosCommonHTML::newsfeedEncoding($rssDoc,$iTitle,$utf8enc); ?>" />
								<?php } ?>
							<?php echo $feed_descrip; ?>
					<?php } ?>
	</div>
					<?php
					$actualItems = $currChannel->getItemCount();
					$setItems = $newsfeed->numarticles;
					if($setItems > $actualItems) {
						$totalItems = $actualItems;
					} else {
						$totalItems = $setItems;
					}
				?>

	<ul>

						<?php
						for($j = 0; $j < $totalItems; $j++) {
							$currItem = &$currChannel->getItem($j);

							$item_title = $currItem->getTitle();
					$item_title = mosCommonHTML::newsfeedEncoding($rssDoc,$item_title,$utf8enc); ?>

		<li>
					<?php if($currItem->getLink()) { ?>
			<a href="<?php echo ampReplace($currItem->getLink()); ?>" target="_blank"><?php echo $item_title; ?></a>

									<?php } else if($currItem->getEnclosure()) {
									$enclosure = $currItem->getEnclosure();
						$eUrl = $enclosure->getUrl(); ?>

			<a href="<?php echo ampReplace($eUrl); ?>" target="_blank"><?php echo $item_title; ?></a>

									<?php } else if(($currItem->getEnclosure()) && ($currItem->getLink())) {
									$enclosure = $currItem->getEnclosure();
						$eUrl = $enclosure->getUrl(); ?>

			<a href="<?php echo ampReplace($currItem->getLink()); ?>" target="_blank"><?php echo $item_title; ?></a>
			<br />
			<a href="<?php echo $eUrl; ?>" target="_blank"><?php echo ampReplace($eUrl); ?></a>

						<?php } ?>


								<?php // END fix for RSS enclosure tag url not showing


								// item description
								if($params->get('item_descr')) {
									$text = $currItem->getDescription();
									$text = mosCommonHTML::newsfeedEncoding($rssDoc,$text,$utf8enc);
									$num = $params->get('word_count');

									// word limit check
									if($num) {
										$texts = explode(' ',$text);
										$count = count($texts);
										if($count > $num) {
											$text = '';
											for($i = 0; $i < $num; $i++) {
												$text .= ' '.$texts[$i];
											}
											$text .= '...';
										}
									}
						?>

			<br />
						<?php echo $text; ?>
			<br />
			<br />
						<?php } ?>


		</li>
					<?php } ?>

	</ul>

				<?php } ?>

			<?php } ?>

		<?php mosHTML::BackButton($params); ?>

</div>


		<?php
	}
}