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
?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".jstProfile_menu > ul> li > a#user_<?php echo $cur_plugin;?>_link").addClass("active");
	});
</script>
<div class="componentheading"><h1 class="profile"><?php echo $config->get('title');?></h1></div><br />
<div class="jstProfile">
	<div class="jstProfile_info">
		<?php echo $avatar_pic;?>
		<h3><?php echo $user_real_name; ?> <span class="blue">(<?php echo $user_nickname; ?>)</span></h3>
		<?php echo $user_status;?>
		<span class="last_visite"><strong><?php echo _USER_LAST_VISIT?>:</strong> <?php echo mosFormatDate($lastvisitDate, _CURRENT_SERVER_TIME_FORMAT)?></span>
	</div>
	<?php if($owner) {?>
	<span class="edit">
		<a class="edit" title="<?php echo _USER_EDIT_PROFILE?>" href="<?php echo $edit_info_link;?>">
				<?php echo _USER_EDIT_PROFILE?>
		</a>
	</span>
		<?php } ?>
	<div class="jstProfile_menu">
		<ul class="menu_userInfo">
			<?php
			$tabs = $_MAMBOTS->trigger( 'userProfileTab', array($user) );
			foreach ($tabs as $tab) {
				$id = isset($tab['id']) ? ' id="'.$tab['id'].'"' : '';
				$class = isset($tab['class']) ? ' class="'.$tab['class'].'"' : '';
				$title = isset($tab['title']) ? $tab['title'] : $tab['name'];
				?><li><a title="<?php echo $title?>" href="<?php echo sefRelToAbs($tab['href']) ?>" <?php echo $id.$class?>><?php echo $tab['name'] ?></a></li><?php
			}
			?>
		</ul>
	</div>
	<div class="plugins_area">
		<?php
		//Вывод плагинов
		$_MAMBOTS->call_mambot('userProfile', $plugin_page, $user);
		?>
	</div>
</div>