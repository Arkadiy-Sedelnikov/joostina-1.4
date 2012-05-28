<?php
defined('_VALID_MOS') or die();
global $my, $mainframe;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<?php
	if($my->id && $mainframe->allow_wysiwyg){
		initEditor();
	}
	echo $mainframe->addJS(JPATH_SITE . '/templates/' . JTEMPLATE . '/js/html5.js');
	echo $mainframe->addCSS(JPATH_SITE . '/templates/' . JTEMPLATE . '/css/template_css.css');
	mosShowHead(array('js'=>1,'css'=>1,'jquery'=>1));
	?>
</head>
<body>
<div id="body">
	<div id="header">
		<div id="header-1">
			<a href="<?php echo JPATH_SITE; ?>" class="nodorder">
				<img src="<?php echo JPATH_SITE . '/templates/' . JTEMPLATE . '/images/1x1.png' ?>" width="500" height="150">
			</a>
		</div>
		<div id="header-2"><?php mosLoadModules('header'); ?></div>
	</div>

	<div id="menu1" class="cf"><?php mosLoadModules('menu1'); ?></div>

	<?php if(($option == '') || ($option == 'com_frontpage')){ ?>
	<div id="top" class="cf">
		<div id="top-1"><?php mosLoadModules('banner1'); ?></div>
		<div id="top-2"><?php mosLoadModules('top'); ?></div>
	</div>
	<?php } ?>

	<div id="pathway" class="cf"></div>

	<div id="main" class="cf">
		<div id="main-1">
			<?php mosLoadModules('user1'); ?>
			<?php mosLoadModules('left'); ?>
			<?php mosLoadModules('user2'); ?>
		</div>
		<div id="main-2">
			<?php  if(($option == '') || ($option == 'com_frontpage')){ mosLoadModules('user3');} ?>
			<div id="main-2-2"><?php mosMainbody(); ?></div>
			<?php mosLoadModules('user4'); ?>
		</div>
		<div id="main-3">
			<?php mosLoadModules('user5'); ?>
			<?php mosLoadModules('right'); ?>
			<?php mosLoadModules('user6'); ?>
		</div>
	</div>
	<div id="banner" class="cf">
		<div id="banner-1"><?php mosLoadModules('banner2'); ?></div>
		<div id="banner-2"><?php mosLoadModules('banner3'); ?></div>
		<div id="banner-3"><?php mosLoadModules('banner4'); ?></div>
	</div>
	<div id="bottom" class="cf"><?php mosLoadModules('bottom'); ?></div>
	<div id="menu2" class="cf"><?php mosLoadModules('menu2'); ?></div>
	<div id="footer" class="cf"><?php mosLoadModules('footer'); ?></div>
</div>
<?php
mosShowFooter(array('js'=>1));
mosShowFooter(array('custom'=>1));
?>
</body>
</html>