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

global $task;
$mainframe = mosMainFrame::getInstance();
$my = $mainframe->getUser();

// cache some acl checks
$canConfig = $acl->acl_check('administration','config','users',$my->usertype);

$manageTemplates = $acl->acl_check('administration','manage','users',$my->usertype,'components','com_templates');
$manageLanguages = $acl->acl_check('administration','manage','users',$my->usertype,'components','com_languages');
$installModules = $acl->acl_check('administration','install','users',$my->usertype,'modules','all');
$editAllModules = $acl->acl_check('administration','edit','users',$my->usertype,'modules','all');
$installComponents = $acl->acl_check('administration','install','users',$my->usertype,'components','all');
$editAllComponents = $acl->acl_check('administration','edit','users',$my->usertype,'components','all');
$canMassMail = $acl->acl_check('administration','manage','users',$my->usertype,'components','com_massmail');
$canManageUsers = $acl->acl_check('administration','manage','users',$my->usertype,'components','com_users');

$count = intval($params->def('count',10));

$query = "SELECT * FROM #__components ORDER BY ordering, name";
$database->setQuery($query);
$comps = $database->loadObjectList(); // component list

$subs = array(); // sub menus

// first pass to collect sub-menu items
foreach($comps as $row) {
	if($row->parent) {
		if(!array_key_exists($row->parent,$subs)) {
			$subs[$row->parent] = array();
		} // if
		$subs[$row->parent][] = $row;
	} // if
} // foreach

?>
<table class="adminlist">
	<tr>
		<th class="title"><?php echo _COMPONENTS?></th>
	</tr>
	<tr>
		<td>
			<?php
			if($task == 'listcomponents') {
				$topLevelLimit = 10000;
			} else {
				$topLevelLimit = $count;
			}
			$i = 0;
			$z = 0;
			foreach($comps as $row) {
				if($editAllComponents | $acl->acl_check('administration','edit','users',$my->usertype,'components',$row->option)) {

					if($row->parent == 0 && (trim($row->admin_menu_link) || array_key_exists($row->id,
									$subs))) {

						if($i >= $topLevelLimit) {
							if($i == $topLevelLimit) {
								?>
			<div>
				<table width="100%" class="adminlist">
					<tr>
						<td align="center" style="text-align: center; font-weight: bold;">
							<a href="index2.php?option=com_admin&task=listcomponents"><?php echo _FULL_COMPONENTS_LIST?></a>
						</td>
					</tr>
				</table>
			</div>
								<?php
								$i = 1000;
							} // if
						} else {
							?>
			<table width="50%" class="adminlist" border="1" style="text-align: left">
								<?php
								if($i < $topLevelLimit) {
									$i++;
									$name = htmlspecialchars($row->name,ENT_QUOTES);
									// $alt = htmlspecialchars( $row->admin_menu_alt, ENT_QUOTES );
									if($row->admin_menu_link) {
										?>
				<tr>
					<td>
												<?php
												echo '<a href="index2.php?'.htmlspecialchars($row->admin_menu_link,ENT_QUOTES).'"><strong>'.$name.'</strong></a><br/>';
												?>
					</td>
				</tr>
										<?php
									} else {
										?>
				<tr>
					<td>
						<strong><?php echo $name; ?></strong><br />
					</td>
				</tr>
										<?php
									} // if else
									if(array_key_exists($row->id,$subs)) {
										foreach($subs[$row->id] as $sub) { //print_r($row);

											?>
				<tr>
					<td>
						<ul style="padding: 0px 0px 0px 20px; margin: 0px;">
														<?php
														$name = htmlspecialchars($sub->name);
														if($sub->admin_menu_link) {
															?>
							<li><?php echo '<a href="index2.php?'.htmlspecialchars($sub->admin_menu_link,ENT_QUOTES).'">'.$name.'</a><br/>'; ?></li>
															<?php
														} else {
															?>
							<li><?php echo $name; ?><br /></li>
															<?php
														} // if else

														?>
						</ul>
					</td>
				</tr>
											<?php
										} // foreach
									} // if
								} // if

								?>
			</table>
							<?php
						} // if else
					} // if
				} // if
				$z++;
			} // foreach
			?>
		</td>
	</tr>
	<tr><th></th></tr>
</table>