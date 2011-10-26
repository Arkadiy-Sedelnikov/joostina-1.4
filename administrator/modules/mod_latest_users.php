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

$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/ico';

// число пользователей для вывода
$limit		= $params->get('num',10);
// зарегистрированны сегодня
$show_today	= $params->get('show_today', 1);
// зарегистрированны за неделю
$show_week	= $params->get('show_week', 0);
// зарегистрированны за месяц
$show_month	= $params->get('show_month', 0);
// зарегистрированных всего
$show_total	= $params->get('show_total', 1);
// числос авторизованных
$show_logged	= $params->get('show_logged', 1);

// запрос из базы параметров пользователей
$query = "SELECT id, name, username, registerDate, usertype, block, bad_auth_count FROM #__users ORDER BY registerDate DESC";
$database->setQuery($query,0,$limit);
$rows = $database->loadObjectList();

if($show_today==1) {
	$query = "SELECT count(id) FROM #__users WHERE to_days(registerDate) = to_days(curdate()) AND usertype <> 'administrator' AND usertype <> 'superadministrator'";
	$database->setQuery($query);
	$show_today = $database->loadResult();
};
if($show_week==1) {
	$query = "SELECT count(id) FROM #__users WHERE yearweek(registerDate) = yearweek(curdate()) AND usertype <> 'administrator' AND usertype <> 'superadministrator'";
	$database->setQuery($query);
	$show_week = $database->loadResult();
};
if($show_month==1) {
	$query = "SELECT count(id) FROM #__users WHERE month(registerDate) = month(curdate()) AND year(registerDate) = year(curdate()) AND usertype <> 'administrator' AND usertype <> 'superadministrator'";
	$database->setQuery($query);
	$show_month = $database->loadResult();
};
if($show_total==1) {
	$query = "SELECT count(id) as registered FROM #__users WHERE usertype <> 'administrator' AND usertype <> 'superadministrator'";
	$database->setQuery($query);
	$show_total = $database->loadResult();
}
if($show_logged==1) {
	$query = "SELECT COUNT(*) FROM #__session WHERE userid != 0";
	$database->setQuery($query);
	$show_logged = $database->loadResult();
}
?>
<table class="adminlist">
	<tr>
		<th><?php echo _NEW_USERS?></th>
		<th><?php echo _ALLOWED?></th>
		<th><?php echo _GROUP?></th>
		<th><?php echo _USER_REG_DATE?></th>

	</tr>
	<?php
	$i = 0;
	$k = 0;
	foreach ( $rows as $row ) {
		$i++;
		$check = '';
		if ( $acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_users' ) ) {
			$link		= 'index2.php?option=com_users&task=editA&hidemainmenu=1&id='. $row->id;
			$username	= '<a href="'. $link .'" title="'._CHANGE_USER_DATA.'">'.$row->name.' ( '.$row->username.' )</a>';
			if($row->id!=$my->id) $check = 'class="td-state" onclick="ch_publ('.$row->id.',\'com_users\');"';
		} else {
			$username	= $row->name.' ('.$row->username.')';
		}
		$img =($row->block==0) ? 'tick.png' : 'publish_x.png';
		?>
	<tr class="row<?php echo $k; ?>">
		<td align="left"><?php echo $username; ?></td>
		<td width="5%" align="center" <?php echo $check;?>>
			<img id="img-pub-<?php echo $row->id;?>" class="img-mini-state" alt="<?php echo _USER_BLOCK?>" src="<?php echo $cur_file_icons_path;?>/<?php echo $img;?>"/>
		</td>
		<td align="center"><?php echo $row->usertype;?></td>
		<td align="center"><?php echo mosFormatDate( $row->registerDate ); ?></td>
	</tr>
		<?php
		$k = 1 - $k;
	}
	unset($rows,$row);

	$text = ($my->bad_auth_count>0) ? '<a style="color: red; font-weight: bold;">' . _BAD_AUTH_NUMBER .': <b>'. $my->bad_auth_count. '</b></a><br />' : '';
	$text .= $show_logged ? _NOW_ON_SITE.': <b>'.$show_logged. '</b><br />':null;
	if($show_total or $show_today or $show_week or $show_month ) {
		$text .= _REGISTERED_USERS_COUNT.' ';
	};
	$text .= $show_total	? _ALL_REGISTERED_USERS_COUNT.': <b>'.$show_total. '</b>, ':null;
	$text .= $show_today	? _TODAY_REGISTERED_USERS_COUNT.': <b>'.$show_today. '</b>, ':null;
	$text .= $show_week		? _WEEK_REGISTERED_USERS_COUNT.': <b>'.$show_week. '</b>, ':null;
	$text .= $show_month	? _MONTH_REGISTERED_USERS_COUNT.': <b>'.$show_month. '</b> ':null;
	
	?>
	<tr class="row<?php echo $k; ?>">
		<td colspan="4"><?php echo $text; ?></td>
	</tr>
</table>
<input type="hidden" name="option" value="" />