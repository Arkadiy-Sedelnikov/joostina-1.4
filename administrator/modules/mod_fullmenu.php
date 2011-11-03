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

if(!defined('_JOS_FULLMENU_MODULE')) {
	/** ensure that functions are declared only once*/
	define('_JOS_FULLMENU_MODULE',1);

	/**
	 * Full DHTML Admnistrator Menus
	 * @package Joostina
	 */
	class mosFullAdminMenu {
		/**
		 * Show the menu
		 * @param string The current user type
		 */
		public static function show($usertype = '') {
			global $acl,$my;
			$database = database::getInstance();
			$config = Jconfig::getInstance();

			echo '<div id="myMenuID"></div>'; // в этот слой выводится содержимое меню
			if($config->config_adm_menu_cache) { // проверяем, активировано ли кэширование в панели управления
				$usertype = $my->usertype;
				$usertype_menu = str_replace(' ','_',$usertype);
				// название файла меню получим из md5 хеша типа пользователя и секретного слова конкретной установки
				$menuname = md5($usertype_menu.$config->config_secret);
				echo '<script type="text/javascript" src="'.JPATH_SITE.'/cache/adm_menu_'.$menuname.'.js?r='.$config->config_cache_key.'"></script>';
				if(js_menu_cache('',$usertype_menu,1) == 'true') { // файл есть, выводим ссылку на него и прекращаем работу
					return; // дальнейшую обработку меню не ведём
				} // файла не было - генерируем его, создаём и всё равно возвращаем ссылку
			}
			// получение данных о правах пользователя
			$canConfig = $acl->acl_check('administration','config','users',$usertype);
			$manageTemplates = $acl->acl_check('administration','manage','users',$usertype,'components','com_templates');
			$manageTrash = $acl->acl_check('administration','manage','users',$usertype,'components','com_trash');
			$manageMenuMan = $acl->acl_check('administration','manage','users',$usertype,'components','com_menumanager');
			$manageLanguages = $acl->acl_check('administration','manage','users',$usertype,'components','com_languages');
			$installModules = $acl->acl_check('administration','install','users',$usertype,'modules','all');
			$editAllModules = $acl->acl_check('administration','edit','users',$usertype,'modules','all');
			$installMambots = $acl->acl_check('administration','install','users',$usertype,'mambots','all');
			$editAllMambots = $acl->acl_check('administration','edit','users',$usertype,'mambots','all');
			$installComponents = $acl->acl_check('administration','install','users',$usertype,'components','all');
			$editAllComponents = $acl->acl_check('administration','edit','users',$usertype,'components','all');
			$canMassMail = $acl->acl_check('administration','manage','users',$usertype,'components','com_massmail');
			$canManageUsers = $acl->acl_check('administration','manage','users',$usertype,'components','com_users');
			$menuTypes = mosAdminMenus::menutypes();
			$query = "SELECT id, name FROM #__boss_config ORDER BY name";
			$database->setQuery($query);
			$directories = $database->loadObjectList();

			// получеполучаем каталог с графикой верхнего меню
			$cur_file_icons_path = JPATH_SITE.'/'.JADMIN_BASE.'/templates/'.JTEMPLATE.'/images/menu_ico/';

			ob_start(); // складываем всё выдаваемое меню в буфер
			?>
var myMenu =[
[null,'<?php echo _SITE?>',null,null,'<?php echo _MOD_FULLMENU_CMS_FEATURES?>',
			<?php
			if($canConfig) {
				?>['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _GLOBAL_CONFIG?>','index2.php?option=com_config&hidemainmenu=1',null,'<?php echo _GLOBAL_CONFIG_TIP?>'],
				<?php
			}
			if($manageLanguages) {
				?>['<img src="<?php echo $cur_file_icons_path ?>language.png" />','<?php echo _LANGUAGE_PACKS?>','index2.php?option=com_languages',null,'<?php echo _LANGUAGE_PACKS_TIP?>',

],
				<?php
			}
			?>['<img src="<?php echo $cur_file_icons_path ?>preview.png" />', '<?php echo _MOD_FULLMENU_SITE_PREVIEW?>', null, null, '<?php echo _MOD_FULLMENU_SITE_PREVIEW?>',
['<img src="<?php echo $cur_file_icons_path ?>preview.png" />','<?php echo _BUTTON_LINK_IN_NEW_WINDOW?>','<?php echo JPATH_SITE; ?>/index.php','_blank','<?php echo JPATH_SITE; ?>'],
['<img src="<?php echo $cur_file_icons_path ?>preview.png" />','<?php echo _MOD_FULLMENU_SITE_PREVIEW_IN_THIS_WINDOW?>','index2.php?option=com_admin&task=preview',null,'<?php echo JPATH_SITE; ?>'],
['<img src="<?php echo $cur_file_icons_path ?>preview.png" />','<?php echo _MOD_FULLMENU_SITE_PREVIEW_WITH_MODULE_POSITIONS?>','index2.php?option=com_admin&task=preview2',null,'<?php echo JPATH_SITE; ?>'],
],
['<img src="<?php echo $cur_file_icons_path ?>globe1.png" />', '<?php echo _MOD_FULLMENU_SITE_STATS?>', null, null, '<?php echo _MOD_FULLMENU_SITE_STATS_TIP?>',
			<?php
			if($config->config_enable_stats == 1) {
				?> ['<img src="<?php echo $cur_file_icons_path ?>globe4.png" />', '<?php echo _MOD_FULLMENU_STATS_BROWSERS?>', 'index2.php?option=com_statistics', null, '<?php echo _MOD_FULLMENU_STATS_BROWSERS_TIP?>'],
				<?php
			}
			?>['<img src="<?php echo $cur_file_icons_path ?>search_text.png" />', '<?php echo _MOD_FULLMENU_SEARCHES?>', 'index2.php?option=com_statistics&task=searches', null, '<?php echo _MOD_FULLMENU_SEARCHES_TIP?>'],
['<img src="<?php echo $cur_file_icons_path ?>globe3.png" />', '<?php echo _PAGES_HITS?>', 'index2.php?option=com_statistics&task=pageimp', null, '<?php echo _PAGES_HITS?>']
],
			<?php
			if($manageTemplates) {
				?>['<img src="<?php echo $cur_file_icons_path ?>template.png" />','<?php echo _TEMPLATES?>',null,null,'<?php echo _MOD_FULLMENU_NEW_SITE_TEMPLATE?>',
	['<img src="<?php echo $cur_file_icons_path ?>template.png" />','<?php echo _COM_INSTALLER_SITE_TEMPLATES?>','index2.php?option=com_templates',null,'<?php echo _COM_INSTALLER_SITE_TEMPLATES?>'],
	['<img src="<?php echo $cur_file_icons_path ?>template.png" />','<?php echo _COM_INSTALLER_ADMIN_TEMPLATES?>','index2.php?option=com_templates&client=admin',null,'<?php echo _COM_INSTALLER_ADMIN_TEMPLATES?>'],
	_cmSplit,
	['<img src="<?php echo $cur_file_icons_path ?>template.png" />','<?php echo _MOD_FULLMENU_MODULES_POSITION?>','index2.php?option=com_templates&task=positions',null,'<?php echo _MOD_FULLMENU_MODULES_POSITION?>'],
	['<img src="<?php echo $cur_file_icons_path ?>install.png" />','<?php echo _MOD_FULLMENU_NEW_SITE_TEMPLATE?>','index2.php?option=com_installer&element=template&client=admin',null,'<?php echo _MOD_FULLMENU_NEW_SITE_TEMPLATE?>']
	],
				<?php }
			// Menu Sub-Menu
			?>],
			<?php if($canManageUsers || $canMassMail) {
				?>[null,'<?php echo _USERS?>',null,null,'<?php echo _USERS?>',
	['<img src="<?php echo $cur_file_icons_path ?>user.png" />','<?php echo _MOD_FULLMENU_ALL_USERS?>','index2.php?option=com_users&task=view',null,'<?php echo _MOD_FULLMENU_ALL_USERS?>'],
	['<img src="<?php echo $cur_file_icons_path ?>template.png" />','<?php echo _MOD_FULLMENU_ADD_USER?>','index2.php?option=com_users&task=edit',null,'<?php echo _MOD_FULLMENU_ADD_USER?>'],
	_cmSplit,
	['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _MOD_FULLMENU_REGISTER_SETUP?>','index2.php?option=com_users&task=config&act=registration',null,'<?php echo _MOD_FULLMENU_REGISTER_SETUP?>'],
	['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _MOD_FULLMENU_PROFILE_SETUP?>','index2.php?option=com_users&task=config&act=profile',null,'<?php echo _MOD_FULLMENU_PROFILE_SETUP?>'],
		['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _MOD_FULLMENU_LOSTPASS_SETUP?>','index2.php?option=com_users&task=config&act=lostpass',null,'<?php echo _MOD_FULLMENU_LOSTPASS_SETUP?>']
],
				<?php } ?>

[null,'<?php echo _MENU?>',null,null,'<?php echo _MENU?>',
			<?php
			if($manageMenuMan) {
				?>['<img src="<?php echo $cur_file_icons_path ?>menus.png" />','<?php echo _MENU?>','index2.php?option=com_menumanager',null,'<?php echo _MENU?>'],
_cmSplit,
				<?php
			}
			foreach($menuTypes as $menuType) {
				?>['<img src="<?php echo $cur_file_icons_path ?>menus.png" />','<?php echo $menuType; ?>','index2.php?option=com_menus&menutype=<?php echo $menuType; ?>',null,''],
				<?php
			}
			if($manageTrash) {
				?>
_cmSplit,['<img src="<?php echo $cur_file_icons_path ?>trash.png" />','<?php echo _TRASH?>','index2.php?option=com_trash&catid=menu',null,'<?php echo _TRASH?>'],
				<?php
			}
			?>
],[null,'<?php echo _CONTENT?>',null,null,'<?php echo _CONTENT?>',
            <?php



            //Меню БОССА
			if(count($directories) > 0) {
				?>
				<?php
				foreach($directories as $directory) {
					$txt = addslashes($directory->name);
					?>
    ['<img src="<?php echo $cur_file_icons_path ?>add_section.png" />','<?php echo $txt; ?>', null, null,'<?php echo _SECTION?>: <?php echo $txt; ?>',
	    ['<img src="<?php echo $cur_file_icons_path ?>edit.png" />', '<?php echo _MOD_FULLMENU_CONTENT_IN_SECTION?>: <?php echo $txt; ?>', 'index2.php?option=com_boss&act=contents&layout=edit&directory=<?php echo $directory->id; ?>',null,null],
	    ['<img src="<?php echo $cur_file_icons_path ?>sections.png" />', '<?php echo _MOD_FULLMENU_SECTION_CATEGORIES2?>: <?php echo $txt; ?>', 'index2.php?option=com_boss&act=categories&layout=edit&directory=<?php echo $directory->id; ?>',null, null],
    ],
				<?php } // foreach ?>
    _cmSplit,
			<?php } ?>


    ['<img src="<?php echo $cur_file_icons_path ?>home.png" />','<?php echo _MOD_FULLMENU_CONTENT_ON_FRONTPAGE?>','index2.php?option=com_frontpage',null,'<?php echo _MOD_FULLMENU_CONTENT_ON_FRONTPAGE?>'],
    _cmSplit,

        <?php if($canConfig) { ?>
            ['<img src="<?php echo $cur_file_icons_path ?>globe3.png" />', '<?php echo _MOD_FULLMENU_DIRECTORIES?>', 'index2.php?option=com_boss&act=manager&layout=manage', null, '<?php echo _PAGES_HITS?>'],
        <?php } ?>
],
<?php




			// Components Sub-Menu
			if($installComponents | $editAllComponents) {
				?>
[null,'<?php echo _COMPONENTS?>',null,null,'<?php echo _COMPONENTS?>',
				<?php
				$query = "SELECT* FROM #__components ORDER BY ordering, name";
				$database->setQuery($query);
				$comps = $database->loadObjectList(); // component list
				$subs = array(); // sub menus
				// first pass to collect sub-menu items
				foreach($comps as $row) {
					if($row->parent) {
						if(!array_key_exists($row->parent,$subs)) {
							$subs[$row->parent] = array();
						}
						$subs[$row->parent][] = $row;
					}
				}
				$topLevelLimit = 19; //You can get 19 top levels on a 800x600 Resolution
				$topLevelCount = 0;
				foreach($comps as $row) {
					if($editAllComponents | $acl->acl_check('administration','edit','users',$usertype,'components',$row->option)) {
						if($row->parent == 0 && (trim($row->admin_menu_link) || array_key_exists($row->id,
										$subs))) {
							$topLevelCount++;
							if($topLevelCount > $topLevelLimit) {
								continue;
							}
							$name = addslashes($row->name);
							$alt = addslashes($row->admin_menu_alt);
							$link = $row->admin_menu_link?"'index2.php?$row->admin_menu_link'":"null";
							echo "\t['<img src=\"../includes/$row->admin_menu_img\" />','$name',$link,null,'$alt'";
							if(array_key_exists($row->id,$subs)) {
								foreach($subs[$row->id] as $sub) {
									echo ",\n";
									$name = addslashes($sub->name);
									$alt = addslashes($sub->admin_menu_alt);
									$link = $sub->admin_menu_link?"'index2.php?$sub->admin_menu_link'":"null";
									echo "['<img src=\"../includes/$sub->admin_menu_img\" />','$name',$link,null,'$alt']";
								}
							}
							echo "],\n";
						}
					}
				}
				if($topLevelLimit < $topLevelCount) {
					echo "['<img src=\"<?php echo $cur_file_icons_path ?>sections.png\" />','"._MOD_FULLMENU_ALL_COMPONENTS."','index2.php?option=com_admin&task=listcomponents',null,'"._MOD_FULLMENU_ALL_COMPONENTS."'],\n";
				}
				if($installModules) {
					?> _cmSplit,
					['<img src="<?php echo $cur_file_icons_path ?>install.png" />', '<?php echo _MOD_FULLMENU_EDIT_COMPONENTS_MENU?>','index2.php?option=com_linkeditor ',null,'<?php echo _MOD_FULLMENU_EDIT_COMPONENTS_MENU?>'],
					['<img src="<?php echo $cur_file_icons_path ?>install.png" />', '<?php echo _MOD_FULLMENU_COMPONENTS_INSTALL_UNINSTALL?>','index2.php?option=com_installer&element=component',null,'<?php echo _MOD_FULLMENU_COMPONENTS_INSTALL_UNINSTALL?>'],
					],
					<?php
				}
				// Modules Sub-Menu
				if($installModules | $editAllModules) {
					?>
[null,'<?php echo _MODULES?>',null,null,'<?php echo _MOD_FULLMENU_MODULES_SETUP?>',
					<?php
					if($editAllModules) {
						?>
	['<img src="<?php echo $cur_file_icons_path ?>module.png" />', '<?php echo _SITE_MODULES?>', "index2.php?option=com_modules", null, '<?php echo _SITE_MODULES?>'],
	['<img src="<?php echo $cur_file_icons_path ?>module.png" />', '<?php echo _ADMIN_MODULES?>', "index2.php?option=com_modules&client=admin", null, '<?php echo _ADMIN_MODULES?>'],
	_cmSplit,
	['<img src="<?php echo $cur_file_icons_path ?>install.png" />', '<?php echo _MOD_FULLMENU_MODULES_SETUP?>', 'index2.php?option=com_installer&element=module', null, '<?php echo _MOD_FULLMENU_MODULES_SETUP?>'],
						<?php
					}
					?>],
					<?php
				}
			} if

			($installMambots | $editAllMambots) { ?>
[null,'<?php echo _MAMBOTS?>',null,null,'<?php echo _MAMBOTS?>',
				<?php if($editAllMambots) { ?>
['<img src="<?php echo $cur_file_icons_path ?>module.png" />', '<?php echo _MOD_FULLMENU_SITE_MAMBOTS?>', "index2.php?option=com_mambots", null, '<?php echo _MOD_FULLMENU_SITE_MAMBOTS?>'],
_cmSplit,
['<img src="<?php echo $cur_file_icons_path ?>install.png" />', '<?php echo _MOD_FULLMENU_MAMBOTS_INSTALL_UNINSTALL?>', 'index2.php?option=com_installer&element=mambot', null, '<?php echo _MOD_FULLMENU_MAMBOTS_INSTALL_UNINSTALL?>'],
					<?php } ?>
],
				<?php } if

			($installModules) { ?>
[null,'<?php echo _EXTENSIONS?>',null,null,'<?php echo _EXTENSION_MANAGEMENT?>',
['<img src="<?php echo $cur_file_icons_path ?>install.png" />', '<?php echo _INSTALLATION . " / " . _DELETING?>','index2.php?option=com_installer&element=installer',null,'<?php echo _INSTALLATION . " / " . _DELETING?>'],
				<?php if($manageLanguages) { ?>
_cmSplit,['<img src="<?php echo $cur_file_icons_path ?>install.png" />','<?php echo _COM_INSTALLER_SITE_LANGUAGES?>','index2.php?option=com_installer&element=language',null,'<?php echo _COM_INSTALLER_SITE_LANGUAGES?>'],
					<?php } if

				($manageTemplates) {?>
_cmSplit,
['<img src="<?php echo $cur_file_icons_path ?>install.png" />','<?php echo _COM_INSTALLER_SITE_TEMPLATES?>','index2.php?option=com_installer&element=template&client=',null,'<?php echo _COM_INSTALLER_SITE_TEMPLATES?>'],
['<img src="<?php echo $cur_file_icons_path ?>install.png" />','<?php echo _COM_INSTALLER_ADMIN_TEMPLATES?>','index2.php?option=com_installer&element=template&client=admin',null,'<?php echo _COM_INSTALLER_ADMIN_TEMPLATES?>'],
					<?php } ?>
],
				<?php }?>
[null,'<?php echo _MOD_FULLMENU_JOOMLA_TOOLS?>',null,null,'<?php echo _MOD_FULLMENU_JOOMLA_TOOLS?>',
['<img src="<?php echo $cur_file_icons_path ?>messaging_inbox.png" />','<?php echo _PRIVATE_MESSAGES?>','index2.php?option=com_messages',null,'<?php echo _PRIVATE_MESSAGES?>'],
['<img src="<?php echo $cur_file_icons_path ?>messaging_config.png" />','<?php echo _PRIVATE_MESSAGES_CONFIG?>','index2.php?option=com_messages&task=config&hidemainmenu=1',null,'<?php echo _PRIVATE_MESSAGES_CONFIG?>'],
_cmSplit,
['<img src="<?php echo $cur_file_icons_path ?>media.png" />','<?php echo _MOD_FULLMENU_MEDIA_MANAGER?>','index2.php?option=com_jwmmxtd',null,'<?php echo _MOD_FULLMENU_MEDIA_MANAGER?>'],
			<?php if($canConfig) { ?>
['<img src="<?php echo $cur_file_icons_path ?>jfmanager.png" />','<?php echo _MOD_FULLMENU_FILE_MANAGER?>','index2.php?option=com_joomlaxplorer',null,'<?php echo _MOD_FULLMENU_FILE_MANAGER?>'],
['<img src="<?php echo $cur_file_icons_path ?>license.png" />','<?php echo _SQL_CONSOLE?>','index2.php?option=com_easysql',null,'<?php echo _SQL_CONSOLE?>'],
_cmSplit,
['<img src="<?php echo $cur_file_icons_path ?>checkin.png" />', '<?php echo _GLOBAL_CHECKIN?>', 'index2.php?option=com_checkin', null,'<?php echo _GLOBAL_CHECKIN?>'],
['<img src="<?php echo $cur_file_icons_path ?>checkin.png" />', '<?php echo _BLOCKED_OBJECTS?>', 'index2.php?option=com_checkin&task=mycheckin', null,'<?php echo _BLOCKED_OBJECTS?>'],
_cmSplit,
['<img src="<?php echo $cur_file_icons_path ?>jbackup.png" />','<?php echo _MOD_FULLMENU_JP_BACKUP_MANAGEMENT?>','index2.php?option=com_joomlapack',null,'<?php echo _MOD_FULLMENU_JP_BACKUP_MANAGEMENT?>',
['<img src="<?php echo $cur_file_icons_path ?>jbackup.png" />','<?php echo _MOD_FULLMENU_JP_CREATE_BACKUP?>','index2.php?option=com_joomlapack&act=pack&hidemainmenu=1',null,'<?php echo _MOD_FULLMENU_JP_CREATE_BACKUP?>'],
['<img src="<?php echo $cur_file_icons_path ?>db.png" />','<?php echo _MOD_FULLMENU_DB_MANAGEMENT?>','index2.php?option=com_joomlapack&act=db',null,'<?php echo _MOD_FULLMENU_DB_MANAGEMENT?>'],
['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _MOD_FULLMENU_BACKUP_CONFIG?>','index2.php?option=com_joomlapack&act=config',null,'<?php echo _MOD_FULLMENU_BACKUP_CONFIG?>']],
				<?php } ?>
			<?php if($config->config_caching == 1) { ?>
				<?php if($config->config_cache_handler == 'file') { ?>
		['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _CACHE_MANAGEMENT?>','index2.php?option=com_cache',null,'<?php echo _CACHE_MANAGEMENT?>'],
					<?php }?>
	['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _MOD_FULLMENU_CLEAR_CONTENT_CACHE?>','index2.php?option=com_admin&task=clean_cache',null,'<?php echo _MOD_FULLMENU_CLEAR_CONTENT_CACHE?>'],
	['<img src="<?php echo $cur_file_icons_path ?>config.png" />','<?php echo _MOD_FULLMENU_CLEAR_ALL_CACHE?>','index2.php?option=com_admin&task=clean_all_cache',null,'<?php echo _MOD_FULLMENU_CLEAR_ALL_CACHE?>'],
				<?php } ?>
			<?php
			if($canConfig) {?>
['<img src="<?php echo $cur_file_icons_path ?>sysinfo.png" />', '<?php echo _MOD_FULLMENU_SYSTEM_INFO?>', 'index2.php?option=com_admin&task=sysinfo', null,'<?php echo _MOD_FULLMENU_SYSTEM_INFO?>'],
				<?php
			}
			?>
['<img src="<?php echo $cur_file_icons_path ?>favicon.ico" />', '<?php echo _MOD_FULLMENU_JOOSTINARU?>', 'http://www.joostina.ru/?from_adminpanel', '_blank','<?php echo _MOD_FULLMENU_JOOSTINARU?>'],
],
_cmSplit];
cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
			<?php
			// boston, складываем меню в кэш, и записываем в файл
			$cur_menu = ob_get_contents();
			ob_end_clean();
			if($config->config_adm_menu_cache) {
				js_menu_cache($cur_menu,$usertype_menu);
			}else {
				echo '<script language="JavaScript" type="text/javascript">'.$cur_menu.'</script>';
			}

		}
		/**
		 * Show an disbaled version of the menu, used in edit pages
		 * @param string The current user type
		 */
		public static function showDisabled($usertype = '') {
			global $acl;

			$canConfig = $acl->acl_check('administration','config','users',$usertype);
			$installModules = $acl->acl_check('administration','install','users',$usertype,'modules','all');
			$editAllModules = $acl->acl_check('administration','edit','users',$usertype,'modules','all');
			$installMambots = $acl->acl_check('administration','install','users',$usertype,'mambots','all');
			$editAllMambots = $acl->acl_check('administration','edit','users',$usertype,'mambots','all');
			$installComponents = $acl->acl_check('administration','install','users',$usertype,'components','all');
			$editAllComponents = $acl->acl_check('administration','edit','users',$usertype,'components','all');
			$text = _MOD_FULLMENU_NO_ACTIVE_MENU_ON_THIS_PAGE;
			?>
<div id="myMenuID" class="inactive"></div>
<script language="JavaScript" type="text/javascript">
	var myMenu =
		[
		[null,'<?php echo _SITE; ?>',null,null,'<?php echo $text; ?>'],
		_cmSplit,
		[null,'<?php echo _USERS?>',null,null,'<?php echo _USERS?>'],
		[null,'<?php echo _MENU; ?>',null,null,'<?php echo $text; ?>'],
		_cmSplit,
			<?php
			/* Content Sub-Menu*/
			?>
					[null,'<?php echo _CONTENT; ?>',null,null,'<?php echo $text; ?>'
					],
			<?php
			/* Components Sub-Menu*/
			if ( $installComponents | $editAllComponents) {
				?>
						_cmSplit,
						[null,'<?php echo _COMPONENTS; ?>',null,null,'<?php echo $text; ?>'
						],
				<?php
			} // if $installComponents

			?>
			<?php
			/* Modules Sub-Menu*/
			if($installModules | $editAllModules) {
				?>
						_cmSplit,
						[null,'<?php echo _MODULES; ?>',null,null,'<?php echo $text; ?>'
						],
				<?php
			} // if ( $installModules | $editAllModules)
			/* Mambots Sub-Menu*/
			if($installMambots | $editAllMambots) {
				?>
						_cmSplit,
						[null,'<?php echo _MAMBOTS; ?>',null,null,'<?php echo $text; ?>'],
				<?php
			} // if ( $installMambots | $editAllMambots)
			/* Installer Sub-Menu*/
			if($installModules) {
				?>
						_cmSplit,
						[null,'<?php echo _EXTENSIONS; ?>',null,null,'<?php echo $text; ?>'
						],
				<?php
			} // if ( $installModules)
			/* System Sub-Menu*/
			if($canConfig) {
				?>
						_cmSplit,[null,'<?php echo _MOD_FULLMENU_JOOMLA_TOOLS; ?>',null,null,'<?php echo $text; ?>'],
				<?php
			}
			?>
				];
				cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
</script>
			<?php
		}
	}
}
$hide = intval(mosGetParam($_REQUEST,'hidemainmenu',0));

global $my;

if($hide) {
	mosFullAdminMenu::showDisabled($my->usertype);
} else {
	mosFullAdminMenu::show($my->usertype);
}