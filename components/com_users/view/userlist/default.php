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

if(!$menu) {
	echo _PAGE_ACCESS_DENIED;
	return;
}

//Общее количество
$users->total = $users->get_total($usertype);
// список
$users->user_list = $users->get_users($usertype, $limitstart, $limit);

//пагинация
if($users->total>0) {
	mosMainFrame::addLib('pageNavigation');
	$link = $menu->link.'&amp;Itemid='.$menu->id;
	$paginate = new mosPageNav( $users->total, $limitstart, $limit );
}
?><div class="userlist">
	<?php if( $params->get('header', $menu->name)) : ?>
	<div class="componentheading"><h1><?php echo $params->get('header', $menu->name); ?></h1></div>
	<?php endif;?>
	<ul>
		<?php foreach($users->user_list as $user) {
			$avatar_pic = '<img class="avatar" src="'.JPATH_SITE.'/'.$users->get_avatar($user).'" />';
			$profile_link = $users->get_link($user); ?>
		<li>
			<a class="thumb" href="<?php echo $profile_link;?>"><?php echo $avatar_pic;?></a>
			<a href="<?php echo $profile_link;?>"><?php echo $user->name;?></a>
			<p><?php echo $user->about;?></p>
		</li>
			<?php };?>
	</ul>
</div>
<?php if($users->total>0) {
	echo '<br clear="all" /> '. $paginate->writePagesLinks($link);
}?>