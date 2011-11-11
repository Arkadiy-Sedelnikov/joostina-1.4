<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

class HTML_boss {

    public static function getLayout() {
        $act = mosGetParam($_REQUEST, 'act', '');
        $task = mosGetParam($_REQUEST, 'task', "");
        $layout = mosGetParam($_REQUEST, 'layout', '');
        $exceptionTask = array('save',
                        'apply',
                        'delete',
                        'publish',
                        'save_tmpl_fields',
                        'save_tmpl_source',
                        'required',
                        'export',
                        'import',
                        'import_joostina'
        );

        if($layout=='') {
	    	$_SESSION['boss_layout'] = (!@$_SESSION['boss_layout']) ? 'edit' : $_SESSION['boss_layout'];
	    } else {
	    	$_SESSION['boss_layout'] = $layout;
	    }
        $layout = $_SESSION['boss_layout'];
        if(!in_array($task, $exceptionTask)){
            if($layout == 'edit' && $act != 'contents'){
                $act = 'categories';
            }
            elseif($layout == 'manage' && ($act == 'contents' || $act == 'categories' || $act == '')){
                $act = 'manager';
            }
            else if($act == ''){
                $act = 'manager';
            }
        }
        return array('layout'=>$layout, 'act'=>$act);
    }

    public static function header($text, $directory, $directories, $conf) {
       
        $act = mosGetParam($_REQUEST, 'act', "");
        $task = mosGetParam($_REQUEST, 'task', "");
        $params = self::getLayout();
        if($directory == 0){
            $params['layout'] = 'full';
            $params['act'] = 'manager';
        }
        //права пользователя
        $edit_all_content = true;
        $edit_category = true;
        $edit_directories = true;
        $edit_conf = true;
        $edit_types = true;
        $edit_fields = true;
        $edit_fieldimages = true;
        $edit_templates = true;
        $edit_plugins = true;
        $import_export = true;
        $edit_users = true;

        $conf->allow_rights = (isset($conf->allow_rights)) ? $conf->allow_rights : 0;
        
        if(@$conf->allow_rights){
            global $my;

            $rights = BossPlugins::get_plugin($directory, 'bossRights', 'other', array('conf_admin'));
            $rights->bind_rights(@$conf->rights);

            $edit_all_content = $rights->allow_me('edit_all_content', $my->gid);
            $edit_category = $rights->allow_me('edit_category', $my->gid);
            $edit_directories = ($my->gid != 25) ? $rights->allow_me('edit_directories', $my->gid) : true;
            $edit_conf = ($my->gid != 25) ? $rights->allow_me('edit_conf', $my->gid) : true;
            $edit_types = $rights->allow_me('edit_types', $my->gid);
            $edit_fields = $rights->allow_me('edit_fields', $my->gid);
            $edit_fieldimages = $rights->allow_me('edit_fieldimages', $my->gid);
            $edit_templates = $rights->allow_me('edit_templates', $my->gid);
            $edit_plugins = $rights->allow_me('edit_plugins', $my->gid);
            $import_export = $rights->allow_me('import_export', $my->gid);
            $edit_users = $rights->allow_me('edit_users', $my->gid);
        }
        else{
            $act = $params['act'];
        }

        $layout = $params['layout'];
        $layouts = array('edit' => BOSS_LAYOUT_EDIT, 'manage' => BOSS_LAYOUT_MANAGE, 'full' => BOSS_LAYOUT_FULL); 
        ?>
<table border="0" class="adminheading" cellpadding="0" cellspacing="0" width="100%">
            <tr valign="middle">
                <th class="config">
                    <a href="index2.php?option=com_boss&directory=<?php echo $directory; ?>">
                <?php echo BOSS_COMPONENT; ?><?php
                if (isset($directories[$directory]->name))
                    echo "&nbsp;::&nbsp;"
                    . $directories[$directory]->name . "";
                ?></a>&nbsp;&rarr;&nbsp;<?php echo $text; ?>
        </th>
    <?php if((empty($task) || $task == 'cancel') && !@$conf->allow_rights) { ?>
        <th>
            <select name='layout' onchange="jumpmenu('parent',this)">
                <?php
                foreach ($layouts as $key => $val) {
                    if ($key == $layout) {
                        $selected = "selected='selected'";
                    }
                    else
                        $selected = "";
                    echo "<option value='index2.php?option=com_boss&directory=" . $directory . "&act=" . $act . "&layout=" . $key . "' $selected>" . $val . "</option>";
                }
                ?>
            </select>
        </th>
    <?php } ?>
    </tr>
</table>
<script language="JavaScript" type="text/JavaScript">
    <!--
    function jumpmenu(target, obj) {
        eval(target + ".location='" + obj.options[obj.selectedIndex].value + "'");
        obj.options[obj.selectedIndex].innerHTML = "<?php echo BOSS_WAIT;?>";
    }
    //-->
</script>

<?php
$class_manager       = ($act=="manager") ? 'id="current"' : '';
$class_configuration = ($act=="configuration") ? 'id="current"' : '';
$class_content_types = ($act=="content_types") ? 'id="current"' : '';
$class_categories    = ($act=="categories") ? 'id="current"' : '';
$class_contents      = ($act=="contents") ? 'id="current"' : '';
$class_templates     = ($act=="templates") ? 'id="current"' : '';
$class_fields        = ($act=="fields") ? 'id="current"' : '';
$class_fieldimage    = ($act=="fieldimage") ? 'id="current"' : '';
$class_plugins       = ($act=="plugins") ? 'id="current"' : '';
$class_export_import = ($act=="export_import") ? 'id="current"' : '';
$class_users         = ($act=="users") ? 'id="current"' : '';
$task                = mosGetParam($_GET,'task');
?>

<?php
if (!isset($_REQUEST['tid'])&& $task!='edit'
							&& $task!='edit_tmpl'
							&& $task!='edit_tmpl_fields'
							&& $task!='edit_tmpl_source') { ?>
	<ul id="boss-menu">
            <?php if ($layout == 'edit' || $layout == 'full' || $conf->allow_rights) : ?>
                <?php if ($edit_category) : ?>
		<li><a <?php echo $class_categories; ?> href="index2.php?option=com_boss&act=categories&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_CATEGORIES;?></a></li>
		<?php endif; ?>
                <?php if ($edit_all_content) : ?>
                <li><a <?php echo $class_contents; ?> href="index2.php?option=com_boss&act=contents&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_CONTENTS;?></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($layout == 'manage' || $layout == 'full' || $conf->allow_rights) : ?>
                <?php if ($edit_directories) : ?>
                <li><a <?php echo $class_manager; ?> href="index2.php?option=com_boss&act=manager&directory=<?php echo $directory; ?>"><?php echo BOSS_CATALOGS; ?></a></li>
		<?php endif; ?>
                <?php if ($edit_conf) : ?>
                <li><a <?php echo $class_configuration; ?> href="index2.php?option=com_boss&act=configuration&task=edit&directory=<?php echo $directory; ?>"><?php echo BOSS_CONFIGURATION; ?></a></li>
		<?php endif; ?>
                <?php if ($edit_types) : ?>
                <li><a <?php echo $class_content_types; ?> href="index2.php?option=com_boss&act=content_types&directory=<?php echo $directory; ?>"><?php echo BOSS_CONTENT_TYPES;?></a></li>
		<?php endif; ?>
                <?php if ($edit_fields) : ?>
                <li><a <?php echo $class_fields; ?> href="index2.php?option=com_boss&act=fields&directory=<?php echo $directory; ?>"><?php echo BOSS_FIELDS; ?></a></li>
		<?php endif; ?>
                <?php if ($edit_fieldimages) : ?>
                <li><a <?php echo $class_fieldimage; ?> href="index2.php?option=com_boss&act=fieldimage&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_FIELDIMAGES;?></a></li>
		<?php endif; ?>
                <?php if ($edit_templates) : ?>
                <li><a <?php echo $class_templates; ?> href="index2.php?option=com_boss&act=templates&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_TEMPLATES;?></a></li>
		<?php endif; ?>
                <?php if ($edit_plugins) : ?>
                <li><a <?php echo $class_plugins; ?> href="index2.php?option=com_boss&act=plugins&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_PLUGINS;?></a></li>
		<?php endif; ?>
                <?php if ($import_export) : ?>
                <li><a <?php echo $class_export_import; ?> href="index2.php?option=com_boss&act=export_import&directory=<?php echo $directory; ?>"><?php echo BOSS_EX_IM_HEADER;?></a></li>
                <?php endif; ?>
                <?php if ($edit_users) : ?>
                <li><a <?php echo $class_users; ?> href="index2.php?option=com_boss&act=users&directory=<?php echo $directory; ?>"><?php echo BOSS_TH_USERS;?></a></li>
                <?php endif; ?>
            <?php endif; ?>
    </ul>
<?php } else { ?>
	<ul id="boss-menu" class="inactive">
            <?php if ($layout == 'edit' || $layout == 'full' || $conf->allow_rights) : ?>
                <?php if ($edit_category) : ?>
		<li><?php echo BOSS_LIST_CATEGORIES;?></li>
                <?php endif; ?>
                <?php if ($edit_all_content) : ?>
                <li><?php echo BOSS_LIST_CONTENTS;?></li>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($layout == 'manage' || $layout == 'full' || $conf->allow_rights) : ?>
                <?php if ($edit_directories) : ?>
                <li><?php echo BOSS_CATALOGS; ?></li>
                <?php endif; ?>
                <?php if ($edit_conf) : ?>
                <li><?php echo BOSS_CONFIGURATION; ?></li>
                <?php endif; ?>
                <?php if ($edit_types) : ?>
                <li><?php echo BOSS_CONTENT_TYPES; ?></li>
                <?php endif; ?>
                <?php if ($edit_fields) : ?>
                <li><?php echo BOSS_LIST_TEMPLATES;?></li>
                <?php endif; ?>
                <?php if ($edit_fieldimages) : ?>
                <li><?php echo BOSS_FIELDS; ?></li>
                <?php endif; ?>
                <?php if ($edit_templates) : ?>
                <li><?php echo BOSS_LIST_FIELDIMAGES;?></li>
                <?php endif; ?>
                <?php if ($edit_plugins) : ?>
                <li><?php echo BOSS_LIST_PLUGINS;?></li>
                <?php endif; ?>
                <?php if ($import_export) : ?>
                <li><?php echo BOSS_EX_IM_HEADER;?></li>
                <?php endif; ?>
                <?php if ($edit_users) : ?>
                <li><?php echo BOSS_TH_USERS;?></li>
                <?php endif; ?>
            <?php endif; ?>
    </ul>
<?php } ?>

<div class="fl mb20">
	<span class="gray"><?php echo BOSS_ROOT_TITLE; ?>:</span>&nbsp;
    <select name='directory_change' onchange="jumpmenu('parent',this)">
    <?php foreach ($directories as $d) {
        if ($d->id == $directory) {
            $selected = "selected='selected'";
        }
        else
            $selected = "";
        echo "<option value='index2.php?option=com_boss&directory=" . $d->id . "&act=" . $act . "' $selected>" . $d->name . "&nbsp;(" . $d->id . ")</option>";
    }
    ?>
    </select>
</div>
<?php
if ($act!="contents" && $act!="plugins" && $act!="categories" && $act!="content_types") echo '<br clear="all"/>';
if ($act=="categories" && $task == 'edit') echo '<br clear="all"/>';
?>


<?php

    }

    public static function displayMain($directory, $directories, $conf) {
        HTML_boss::header(BOSS_MAIN_PAGE, $directory, $directories, $conf);
    }

    public static function check_dir($directory, $conf, $directories = array()) {
        if ($directory == 0) {
            HTML_boss::header(BOSS_CONFIGURATION_PANEL, $directory, $directories, $conf);
            print "<h3>" . BOSS_NEED_CREATE . "</h3>";
            return false;
        }
        else
            return true;
    }

    public static function editConfiguration($row, $templates, $directory, $directories, $sort_fields, $filters, $ratings, $rights, $conf) {
        HTML_boss::header(BOSS_CONFIGURATION_PANEL, $directory, $directories, $conf);
        ?>
        <script language='JavaScript1.2' type='text/javascript'>
            function submitbutton(pressbutton) {
                <?php getEditorContents('editor1', 'fronttext'); ?>
                <?php getEditorContents('editor2', 'rules_text'); ?>
                submitform(pressbutton);
            }

            function showimage() {
                //if (!document.images) return;
                document.images.preview.src = '<?php echo JPATH_SITE;?>/templates/com_boss/' + getSelectedValue('adminForm', 'template') + '/template_thumbnail.png';
            }

            function getSelectedValue(frmName, srcListName) {
                var form = eval('document.' + frmName);
                var srcList = eval('form.' + srcListName);

                i = srcList.selectedIndex;
                if (i != null && i > -1) {
                    return srcList.options[i].value;
                } else {
                    return null;
                }
            }
        </script>
<table width="100%">
	<tr><td>
			<form action="index2.php" method="post" name="adminForm">
            <?php
            $configtabs = new uiTabs(0,1);
            $configtabs->startPane("config");
            $configtabs->startTab(BOSS_TAB_GENERAL, "general-page");
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
            <tr>
                <td><?php echo BOSS_NAME_DIR;?></td>
                <td><input type="text" name="name" value="<?php echo @$row->name; ?>"/></td>
                <td><?php echo BOSS_NAME_DIR_FULL;?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_NAME_ALIAS;?></td>
                <td><input type="text" name="slug" value="<?php echo @$row->slug; ?>"/></td>
                <td><?php echo BOSS_NAME_ALIAS_FULL;?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_CONTENTS_PER_PAGE;?></td>
                <td><input type="text" name="contents_per_page" value="<?php echo @$row->contents_per_page; ?>"/></td>
                <td><?php echo BOSS_CONTENTS_PER_PAGE_LONG;?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_ORDER_BY_DEFAULT; ?></td>
                <td>
                    <select id='default_order_by' name='default_order_by'>
                        <option value='0' <?php if (@$row->default_order_by == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_DATE; ?></option>

                        <option value='last_comment' <?php if (@$row->default_order_by == 'last_comment') {
                            echo "selected";
                        } ?>><?php echo BOSS_DATE_LAST_COMMENT; ?></option>

                    <?php foreach ($sort_fields as $s) { ?>
                        <option value="<?php echo $s->fieldid;?>" <?php
                                                                    if (@$row->default_order_by == $s->fieldid) echo "selected='selected'"; ?>>
                        <?php echo $s->title; ?>
                        </option>
                    <?php

                    }
                    ?>
                    </select>
                </td>
                <td><?php echo BOSS_ORDER_BY_DEFAULT_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_AUTO_PUBLISH; ?></td>
                <td>
                    <select id='auto_publish' name='auto_publish'>
                        <option value='2' <?php if (@$row->auto_publish == 2) {
                            echo "selected";
                        } ?>><?php echo "Backlink"; ?></option>
                        <option value='1' <?php if (@$row->auto_publish == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_YES; ?></option>
                        <option value='0' <?php if (@$row->auto_publish == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_NO; ?></option>
                    </select>
                </td>
                <td><?php echo BOSS_AUTO_PUBLISH_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_SUBMISSION_TYPE; ?></td>
                <td>
                    <select id='submission_type' name='submission_type'>
                        <option value='0' <?php if (@$row->submission_type == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_SUBMISION_WITH_ACCOUNT_CREATION; ?></option>
                        <option value='1' <?php if (@$row->submission_type == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_SUBMISSION_ALLOWED_ONLY_FOR_REGISTERS; ?></option>
                        <option value='2' <?php if (@$row->submission_type == 2) {
                            echo "selected";
                        } ?>><?php echo BOSS_SUBMISSION_ALLOWED_FOR_VISITORS; ?></option>
                    </select>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><?php echo BOSS_SECURE_NEW_CONTENT; ?></td>
                <td>
                    <select id='secure_new_content' name='secure_new_content'>
                        <option value='1' <?php if (@$row->secure_new_content == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_YES; ?></option>
                        <option value='0' <?php if (@$row->secure_new_content == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_NO; ?></option>
                    </select>
                </td>
                <td><?php echo BOSS_SECURE_NEW_CONTENT_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_CONTENT_MAMBOT; ?></td>
                <td>
                    <select id='use_content_mambot' name='use_content_mambot'>
                        <option value='1' <?php if (@$row->use_content_mambot == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_YES; ?></option>
                        <option value='0' <?php if (@$row->use_content_mambot == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_NO; ?></option>
                    </select>
                </td>
                <td><?php echo BOSS_CONTENT_MAMBOT_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_ROOT_SUBMIT; ?></td>
                <td>
                    <select id='root_allowed' name='root_allowed'>
                        <option value='1' <?php if (@$row->root_allowed == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_ROOT_SUBMIT_ALLOWED; ?></option>
                        <option value='0' <?php if (@$row->root_allowed == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_ROOT_SUBMIT_NOT_ALLOWED; ?></option>
                    </select>
                </td>
                <td><?php echo BOSS_ROOT_SUBMIT_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_EMPTY_CAT; ?></td>
                <td>
                    <select id='empty_cat' name='empty_cat'>
                        <option value='0' <?php if (@$row->empty_cat == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_EMPTY_CAT_ALLOW; ?></option>
                        <option value='1' <?php if (@$row->empty_cat == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_EMPTY_CAT_NOT_ALLOW; ?></option>
                    </select>
                </td>
                <td><?php echo BOSS_EMPTY_CAT_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_NB_CONTENTS_BY_USER; ?></td>
                <td><input type="text" name="nb_contents_by_user" value="<?php echo @$row->nb_contents_by_user; ?>"/>
                </td>
                <td><?php echo BOSS_NB_CONTENTS_BY_USER_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_EMAIL_ON_NEW; ?></td>
                <td>
                    <select id='send_email_on_new' name='send_email_on_new'>
                        <option value='1' <?php if (@$row->send_email_on_new == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_YES; ?></option>
                        <option value='0' <?php if (@$row->send_email_on_new == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_NO; ?></option>
                    </select>
                </td>
                <td><?php echo BOSS_EMAIL_ON_NEW_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_EMAIL_ON_UPDATE; ?></td>
                <td>
                    <select id='send_email_on_update' name='send_email_on_update'>
                        <option value='1' <?php if (@$row->send_email_on_update == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_YES; ?></option>
                        <option value='0' <?php if (@$row->send_email_on_update == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_NO; ?></option>
                    </select>
                </td>
                <td><?php echo BOSS_EMAIL_ON_UPDATE_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_SHOW_RSS; ?></td>
                <td>
                    <select id='show_rss' name='show_rss'>
                        <option value='1' <?php if (@$row->show_rss == 1) {
                            echo "selected";
                        } ?>><?php echo BOSS_YES; ?></option>
                        <option value='0' <?php if (@$row->show_rss == 0) {
                            echo "selected";
                        } ?>><?php echo BOSS_NO; ?></option>
                    </select>
                </td>
                <td>&nbsp;<?php echo BOSS_SHOW_RSS_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_FILTER; ?></td>
                <td>
                    <select id='filter' name='filter'>
                        <option value='no'><?php echo BOSS_NO; ?></option>
                    <?php if (count($filters)>0) {
                        foreach ($filters as $filter) {
                            ?>
                                <option value='<?php echo $filter->value; ?>'
                                    <?php if (@$row->filter == $filter->value) {
                                    echo "selected";
                                } ?>><?php echo $filter->text; ?></option>
                            <?php

                        }
                    } ?>
                    </select>
                </td>
                <td>&nbsp;<?php echo BOSS_FILTER_LONG; ?></td>
            </tr>
            <tr>
                <td><?php echo BOSS_TEMPLATE; ?></td>
                <td>
                    
                    <select id='template' name='template' onchange="showimage()">
                    <?php if (isset($templates)) {
                        foreach ($templates as $tpl) {
                            ?>
                                <option value='<?php echo $tpl; ?>' <?php if (@$row->template == $tpl) {
                                    echo "selected";
                                } ?>><?php echo $tpl; ?></option>
                            <?php

                        }
                    }

                    ?></select>
					<?php $tmpl_tmp = (@$row->template) ? @$row->template : 'default' ?>
					<img class="ml10" src="<?php echo JPATH_SITE."/templates/com_boss/" . $tmpl_tmp . "/template_thumbnail.png";?>"
                         name="preview" border="1" alt="<?php echo $tmpl_tmp;?>"/>
                </td>
                <td>
					<?php echo BOSS_TEMPLATE_LONG; ?>
				</td>
            </tr>
            </table>
            <?php
			$configtabs->endTab();
            $configtabs->startTab(BOSS_TAB_CONTACT, "contact-page");
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_SHOW_CONTACT; ?></td>
                    <td>
                        <select id='show_contact' name='show_contact'>
                            <option value='1' <?php if (@$row->show_contact == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_SHOW_CONTACT_LOGGED_ONLY; ?></option>
                            <option value='0' <?php if (@$row->show_contact == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_SHOW_CONTACT_ALL; ?></option>
                        </select>
                    </td>
                    <td><?php echo BOSS_SHOW_CONTACT_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_DISPLAY_FULLNAME; ?></td>
                    <td>
                        <select id='display_fullname' name='display_fullname'>
                            <option value='1' <?php if (@$row->display_fullname == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES; ?></option>
                            <option value='0' <?php if (@$row->display_fullname == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO; ?></option>
                        </select>
                    </td>
                    <td><?php echo BOSS_DISPLAY_FULLNAME_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_ALLOW_ATTACHMENT; ?></td>
                    <td>
                        <select id='allow_attachement' name='allow_attachement'>
                            <option value='1' <?php if (@$row->allow_attachement == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES; ?></option>
                            <option value='0' <?php if (@$row->allow_attachement == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO; ?></option>
                        </select>
                    </td>
                    <td><?php echo BOSS_ALLOW_ATTACHMENT_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_CONTACT_BY_PMS; ?></td>
                    <td>
                        <select id='allow_contact_by_pms' name='allow_contact_by_pms'>
                            <option value='1' <?php if (@$row->allow_contact_by_pms == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES; ?></option>
                            <option value='0' <?php if (@$row->allow_contact_by_pms == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO; ?></option>
                        </select>
                    </td>
                    <td><?php echo BOSS_CONTACT_BY_PMS_LONG; ?></td>
                </tr>
            </table>
            <?php
            $configtabs->endTab();
            $configtabs->startTab(BOSS_TAB_IMAGE, "image-page");
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_MAX_CATIMAGE_WIDTH;?></td>
                    <td><input type="text" name="cat_max_width" value="<?php echo @$row->cat_max_width; ?>"/></td>
                    <td><?php echo BOSS_MAX_CATIMAGE_WIDTH_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_CATIMAGE_HEIGHT;?></td>
                    <td><input type="text" name="cat_max_height" value="<?php echo @$row->cat_max_height; ?>"/></td>
                    <td><?php echo BOSS_MAX_CATIMAGE_HEIGHT_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_CATTHUMBNAIL_WIDTH;?></td>
                    <td><input type="text" name="cat_max_width_t" value="<?php echo @$row->cat_max_width_t; ?>"/></td>
                    <td><?php echo BOSS_MAX_CATTHUMBNAIL_WIDTH_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_CATTHUMBNAIL_HEIGHT;?></td>
                    <td><input type="text" name="cat_max_height_t" value="<?php echo @$row->cat_max_height_t; ?>"/></td>
                    <td><?php echo BOSS_MAX_CATTHUMBNAIL_HEIGHT_LONG;?></td>
                </tr>
            </table>
            <?php
            $configtabs->endTab();
            $configtabs->startTab(BOSS_TAB_TEXT, "text-page", array('fronttext', 'rules_text'));
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_FRONTPAGE; ?></td>
                    <td><?php editorArea('editor1', @$row->fronttext, 'fronttext', '100%;', '350', '75', '20', 1); ?></td>
                    <td><?php echo BOSS_FRONTPAGE_LONG; ?></td>
                </tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
                <tr>
                    <td><?php echo BOSS_RULES; ?></td>
                    <td><?php editorArea('editor2', @$row->rules_text, 'rules_text', '100%;', '350', '75', '20', 1); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <?php
			$configtabs->endTab();
            $configtabs->startTab(BOSS_META, "meta-page");
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_META_TITLE; ?></td>
                    <td><input type="text" name="meta_title" size="60" maxlength="60"
                               value="<?php echo @$row->meta_title; ?>"/></td>
                    <td><?php echo BOSS_META_TITLE_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_META_DESC; ?></td>
                    <td><textarea name="meta_desc" cols="60" rows="4"><?php echo @$row->meta_desc; ?></textarea></td>
                    <td><?php echo BOSS_META_DESC_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_META_KEYS; ?></td>
                    <td><textarea name="meta_keys" cols="60" rows="4"><?php echo @$row->meta_keys; ?></textarea></td>
                    <td><?php echo BOSS_META_KEYS_LONG; ?></td>
                </tr>
            </table>
            <?php
			$configtabs->endTab();
            $configtabs->startTab(BOSS_TAB_RATINGS_COMMENTS, "rating-page");
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_ALLOW_RATINGS; ?></td>
                    <td>
                        <select id='allow_ratings' name='allow_ratings'>
                            <option value='1' <?php if (@$row->allow_ratings == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES; ?></option>
                            <option value='0' <?php if (@$row->allow_ratings == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO; ?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_RATING; ?></td>
                    <td>
                        <select id='rating' name='rating'>
                            <?php
                                if (count($ratings)>0) {
                                foreach ($ratings as $rating) {
                                    ?>
                                        <option value='<?php echo $rating->value; ?>'
                                            <?php if (@$row->rating == $rating->value) {
                                            echo "selected";
                                        } ?>><?php echo $rating->text; ?></option>
                                    <?php

                                }
                            } ?>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_ALLOW_COMMENTS; ?></td>
                    <td>
                        <select id='allow_comments' name='allow_comments'>
                            <option value='1' <?php if (@$row->allow_comments == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES; ?></option>
                            <option value='0' <?php if (@$row->allow_comments == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO; ?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_SECURE_COMMENT; ?></td>
                    <td>
                        <select id='secure_comment' name='secure_comment'>
                            <option value='1' <?php if (@$row->secure_comment == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES; ?></option>
                            <option value='0' <?php if (@$row->secure_comment == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO; ?></option>
                        </select>
                    </td>
                    <td><?php echo BOSS_SECURE_COMMENT_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_REVIEWS_SYS; ?></td>
                    <td>
                        <select id='comment_sys' name='comment_sys'>
                            <option value='1' <?php if (@$row->comment_sys == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_REVIEWS_SYS_IN; ?></option>

                        <?php //Если установлен jComments, то разрешаем его включить.
                        if (is_file(JPATH_BASE . "/components/com_jcomments/jcomments.php")) : ?>
                            <option value='0' <?php if (@$row->comment_sys == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_REVIEWS_SYS_OUT; ?></option>
                        <?php endif; ?>

                        </select>
                    </td>
                    <td><?php echo BOSS_REVIEWS_SYS; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_ALLOW_UNREG_COMMENTS; ?></td>
                    <td>
                        <select id='allow_unregisered_comment' name='allow_unregisered_comment'>
                            <option value='1' <?php if (@$row->allow_unregisered_comment == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES; ?></option>
                            <option value='0' <?php if (@$row->allow_unregisered_comment == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO; ?></option>
                        </select>
                    </td>
                    <td><?php echo BOSS_ALLOW_UNREG_COMMENTS; ?></td>
                </tr>
            </table>
            <?php
            $configtabs->endTab();
            $configtabs->startTab(BOSS_TAB_EXPIRATION, "Expiration-page", array('recall_text'));
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
            <tr>
                            <td><?php echo BOSS_EXPIRATION; ?></td>
                            <td>
                            <select id='expiration' name='expiration'>
                                <option value='0' <?php if (@$row->expiration == 0) {
                echo "selected";
            } ?>><?php echo BOSS_NO; ?></option>
                                <option value='1' <?php if (@$row->expiration == 1) {
                echo "selected";
            } ?>><?php echo BOSS_YES; ?></option>
                            </select>
                            </td>
                            <td>&nbsp;</td>
                    </tr>
                    <tr>
                            <td><?php echo BOSS_CONTENT_DURATION; ?></td>
                            <td><input type="text" name="content_duration" value="<?php echo @$row->content_duration; ?>" /></td>
                            <td>&nbsp;</td>
                    </tr>
                    <tr>
                            <td><?php echo BOSS_RECALL; ?></td>
                            <td>
                            <select id='recall' name='recall'>
                                    <option value='1' <?php if (@$row->recall == 1) {
                echo "selected";
            } ?>><?php echo BOSS_YES; ?></option>
                                    <option value='0' <?php if (@$row->recall == 0) {
                echo "selected";
            } ?>><?php echo BOSS_NO; ?></option>
                            </select>
                            </td>
                            <td>&nbsp;</td>
                    </tr>
                    <tr>
                            <td><?php echo BOSS_RECALL_TIME; ?></td>
                            <td><input type="text" name="recall_time" value="<?php echo @$row->recall_time; ?>" /></td>
                            <td>&nbsp;</td>
                    </tr>
                    <tr>
                            <td><?php echo BOSS_RECALL_TEXT; ?></td>
            <?php $recall_text = stripslashes(@$row->recall_text); ?>
                            <td><?php editorArea('editor3', "$recall_text", 'recall_text', '100%;', '350', '75', '20', 1); ?></td>
                            <td>&nbsp;</td>
                    </tr>
            </table>
            <?php
            $configtabs->endTab();
            $configtabs->startTab(BOSS_TAB_RIGHTS, "rights-page");
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_ALLOW_RIGHTS;?></td>
                    <td>
                        <select id='allow_rights' name='allow_rights'>
                            <option value='0' <?php if (@$row->allow_rights == 0) {
                echo "selected";
            } ?>><?php echo BOSS_NO; ?></option>
                            <option value='1' <?php if (@$row->allow_rights == 1) {
                echo "selected";
            } ?>><?php echo BOSS_YES; ?></option>
                         </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo BOSS_RIGHTS_ADMIN;?></td>
                    <td><?php echo $rights['admin']->draw_config_table('admin');?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_RIGHTS_FRONT;?></td>
                    <td><?php echo $rights['front']->draw_config_table('front');?></td>
                </tr>
            </table>
            <?php
            $configtabs->endTab();
            $configtabs->endPane();
            ?>

            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="id" value="<?php echo @$row->id ?>"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="act" value="configuration"/>
            </form>
        </td>
    </tr>
</table>
        <?php

    }

    public static function recurseCategories($id, $level, $children, $pageNav, $num, $nb, $directory, $defaultTemplate) {
        if (@$children[$id]) {
            $n = 0;
            $total = count($children[$id]);
            foreach ($children[$id] as $row) {
                ?>
                <tr class="row<?php echo ($num & 1); ?>">
                    <td><input type="checkbox" id="cb<?php echo $num;?>" name="tid[]" value="<?php echo $row->id; ?>"
                               onclick="isChecked(this.checked);"/></td>

                    <td align="right"><?php echo $row->id; ?></td>
                <?php
                                        $text = "";
                for ($i = 1; $i < $level; $i++)
                    $text .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                if ($level > 0)
                    $text .= "&nbsp;&nbsp;&nbsp;&nbsp;<sup>L</sup>&nbsp;";
                $text .= $row->name;
                ?>
                    <td>
                    <?php HTML_boss::displayLinkText($text, "index2.php?option=com_boss&directory=$directory&act=categories&task=edit&tid[]=" . $row->id); ?>
                    </td>
                    <td align="center">
                    <?php
                    $pict = "../images/boss/$directory/categories/" . $row->id . "cat_t.jpg";
                    $template_name = $row->template ? $row->template : $defaultTemplate;
                    if (file_exists($pict)) {
                        echo '<img src="' . JPATH_SITE . '/images/boss/' . $directory . '/categories/' . $row->id . 'cat_t.jpg"/>';
                    }
                    else {
                        echo '<img src="' . JPATH_SITE . '/templates/com_boss/' . $template_name . '/images/default.gif"/>';
                    }
                    ?>
                    </td>
                    <td align="center"><?php echo '<a href="index2.php?option=com_boss&directory='.$directory.'&act=contents&catid=' . $row->id. '">[ '.$row->num_cont.' ]</a>'; ?></td>
                    <td align="right">
                    <?php echo $pageNav->orderUpIcon($num, ($n > 0)); ?>
                    </td>
                    <td align="left">
                    <?php echo $pageNav->orderDownIcon($num, $nb, ($n < $total - 1)); ?>
                    </td>
                    <td align="center">
                        <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"
                               class="text_area" style="text-align: center"/>
                    </td>
                    <td class="td-state" align="center" onclick="boss_publ('img-pub-<?php echo $row->id; ?>', '<?php echo "act=categories&task=publish&tid=" . $row->id . "&directory=$directory"; ?>');">
                    <?php HTML_boss::displayYesNoImg($row->published, "img-pub-" . $row->id); ?>
                    </td>
                    <td><?php $tmpl = $row->template ? $row->template : ''; echo $tmpl; ?></td>
                    <td><?php echo $row->slug; ?></td>
                </tr>
                <?php
                                    $num++;
                $num = HTML_boss::recurseCategories($row->id, $level + 1, $children, $pageNav, $num, $nb, $directory, $template_name);
                $n++;
            }
        }
        return $num;
    }

    public static function displayLinkText($text, $link) {
        echo '<a href="' . $link . '">' . $text . '</a>';
    }

    public static function displayLinkImage($img, $link, $title='') {
        echo '<a href="' . $link . '"><img src="' . $img . '" title="' . $title . '" border=0 /></a>';
    }

    function displayPublish($published, $link, $fieldid = '') {
        if ($published == 1) {
            // Published
            $img = 'tick.png';
            $plink = $link . "&publish=0";
            $alt = BOSS_YES;
        } else {
            // Unpublished
            $img = 'publish_x.png';
            $plink = $link . "&publish=1";
            $alt = BOSS_NO;
        }
        ?>
        <a href="<?php echo $plink; ?>">
            <img id="img-pub-<?php echo $fieldid; ?>" src="images/<?php echo $img;?>" border="0"
                 alt="<?php echo $alt; ?>"/>
        </a>
        <?php

    }

 public static function displayYesNoImg($row, $fieldid = '', $c = 'category') {
     if($c== 'category'){
         if ($row) {
             // Yes
             $img = 'tick.png';
             $alt = BOSS_YES;
         } else {
             // No
             $img = 'publish_x.png';
             $alt = BOSS_NO;
         }
     } else {
        $date = date('Y-m-d H:i:s');

        if ($row->published == 0){
            $img = 'publish_x.png';
            $alt = BOSS_NO;
        }
        elseif($row->published == 1 && ($row->date_publish > $date && $row->date_publish != '0000-00-00 00:00:00')){
           $img = 'publish_y.png';
           $alt = BOSS_NOT_STARTED;
        }
        elseif($row->published == 1 && ($row->date_unpublish < $date && $row->date_unpublish != '0000-00-00 00:00:00') ){
           $img = 'publish_r.png';
           $alt = BOSS_DELAYED;
        }
        else{
           $img = 'tick.png';
           $alt = BOSS_YES;
        }
     }

        ?>

        <img id="<?php echo $fieldid; ?>" src="images/<?php echo $img;?>" alt="<?php echo $alt; ?>"/>

        <?php

    }

    function displayRequired($required, $link) {
        if ($required == 1) {
            // Published
            $img = 'tick.png';
            $plink = $link . "&required=0";
            $alt = BOSS_YES;
        } else {
            // Unpublished
            $img = 'publish_x.png';
            $plink = $link . "&required=1";
            $alt = BOSS_NO;
        }
        ?>
        <a href="<?php echo $plink; ?>">
            <img src="images/<?php echo $img;?>" border="0" alt="<?php echo $alt; ?>"/>
        </a>
        <?php

    }

    public static function selectCategories($id, $level, $children, $catid=null, $nodisplaycatid=null, $multiple = 0, $catsid = "") {
        if (@$children[$id]) {
            foreach ($children[$id] as $row) {
                if ($row->id != $nodisplaycatid) {
                    $selected = '';
                    if (($multiple == 0) && ($row->id == $catid))
                        $selected = 'selected';
                    if ($multiple == 1) {
                        if (is_array($catid) && @in_array($row->id, $catid))
                            $selected = 'selected';
                        elseif (substr_count($catsid, ",$row->id,") > 0)
                            $selected = 'selected';
                    }


                    echo "<option value='" . $row->id . "' " . $selected . ">" . $level . $row->name . "</option>";

                    HTML_boss::selectCategories($row->id, $level . $row->name . " >> ", $children, $catid, $nodisplaycatid, $multiple, $catsid);
                }
            }
        }
    }

    public static function selectAutor($autors, $selectedAutorId=0) {
        if (count($autors)>0) {
            foreach ($autors as $autor) {
                $selected = '';
                if ($autor->userid == $selectedAutorId) {
                    $selected = 'selected="selected"';
                }
                echo "<option value='" . $autor->userid . "' " . $selected . ">[" . $autor->userid .'] '. $autor->name . "</option>";
            }
        }
    }

    public static function selectPublish($select_publish,  $only_pub = 'all') {
        $selectedItem = mosGetParam($_REQUEST, $select_publish, 0);
        if($only_pub == 'all'){
            $option = array(
                1 => BOSS_PUBLISH,
                2 => BOSS_NO_PUBLISH,
                3 => BOSS_DELAYED,
                4 => BOSS_NOT_STARTED
            );
        }
        else{
             $option = array(
                1 => BOSS_PUBLISH,
                2 => BOSS_NO_PUBLISH,
            );
        }
        foreach ($option as $key => $value) {
            $selected = '';
            if ($key == $selectedItem) {
                $selected = 'selected="selected"';
            }
            echo "<option value='" . $key . "' " . $selected . ">" . $value . "</option>";
        }
    }

    public static function listCatsCont($categs, $content_id, $directory) {
        if (!empty($categs)) {
            foreach ($categs as $categ) {
                if ($categ->content_id == $content_id)
                    echo '<a href="index2.php?option=com_boss&act=contents&directory=' . $directory . '&catid=' . $categ->id . '">' . $categ->name . '</a><br />';
            }
        }
    }

    public static function listContents($cat, $rows, $pagenav, $cats, $directory, $directories, $categs, $autors, $selectedAutorId, $typesContent, $conf) {

		$certain_category = (!empty($cat->name)) ? '<span class="gray">&nbsp;('.$cat->name.')</span>' : '';
		HTML_boss::header(BOSS_CONTENTS . $certain_category, $directory, $directories, $conf); 
                mosCommonHTML::loadJquery();
                ?>
        
        <script type="text/javascript"><!--//--><![CDATA[//><!--
        function submitbutton(pressbutton) {
            if(pressbutton == 'new'){
                $('#types_content').slideDown('slow');
            }
            else {
                submitform(pressbutton);
                return true;
            }
	}
	//--><!]]></script>
        
        <form action="index2.php" method="get" name="adminForm">
		<div class="fr mb20">
			<span class="gray"><?php echo BOSS_FORM_CATEGORY; ?></span>&nbsp;
            <select name="catid" id="catid" onchange="document.adminForm.submit();">
				<option value="0" <?php if (!isset($cat)) echo selected; ?>><?php echo BOSS_MENU_ALL_CONTENTS; ?></option>
			    <?php HTML_boss::selectCategories(0, BOSS_ROOT . " >> ", $cats, $cat->id, -1); ?>
			</select>
            <span class="gray"><?php echo BOSS_AUTOR; ?></span>&nbsp;
            <select name="autor" id="autor" onchange="document.adminForm.submit();">
				<option value="0"><?php echo BOSS_ALL; ?></option>
			    <?php self::selectAutor($autors, $selectedAutorId); ?>
			</select>
            <span class="gray"><?php echo BOSS_FIELD_PUBLISHED; ?></span>&nbsp;
            <select name="select_publish" id="select_publish" onchange="document.adminForm.submit();">
				<option value="0"><?php echo BOSS_ALL; ?></option>
			    <?php self::selectPublish('select_publish'); ?>
			</select>
        </div>
		<br clear="all"/>
                <div id="types_content" class="types_content" style="display: none">
                    <h3><?php echo BOSS_SELECT_CONTENT_TYPE; ?></h3>
                    <?php
                    foreach($typesContent as $types){
                        ?>  
                        <a href="/administrator/index2.php?option=com_boss&act=contents&task=new&directory=<?php echo $directory; ?>&type_content=<?php echo $types->id; ?>"><?php echo $types->name; ?></a> &nbsp;
                        <?php  
                    }
                    ?>
                </div>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <th width="5"><input type="checkbox" name="toggle" value=""
                                         onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th width="5">Id</th>
                    <th class="title" width="40%"><?php echo BOSS_TH_TITLE;?></th>
                    <th width="20%"><?php echo BOSS_CONTENT_TYPES;?></th>
                    <th width="10%"><?php echo BOSS_AUTOR;?></th>
                    <th width="5%"><?php echo BOSS_TH_PUBLISH;?></th>
                    <th width="10%"><?php echo BOSS_TH_CATEGORY;?></th>
                    <th width="10%"><?php echo BOSS_TH_DATE;?></th>
                </tr>
            <?php
                                $k = 0;
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                $name = $row->name;
                ?>
                    <tr class="row<?php echo $k; ?>">
                        <td><input type="checkbox" id="cb<?php echo $i;?>" name="tid[]" value="<?php echo $row->id; ?>"
                                   onclick="isChecked(this.checked);"/></td>
                        <td align="right"><?php echo $row->id; ?></td>
                        <td><?php HTML_boss::displayLinkText($name, "index2.php?option=com_boss&act=contents&task=edit&&directory=" . $directory . "&tid[]=" . $row->id); ?></td>
                        <td align="center"><?php echo $row->type_name; ?></td>
                        <td align="center"><?php echo '['.$row->userid.'] '.$autors[$row->userid]->name ?></td>
                        <td class="td-state" align="center" onclick="boss_publ('img-pub-<?php echo $row->id; ?>', '<?php echo "act=contents&task=publish&tid=" . $row->id . "&directory=$directory"; ?>');">
                        <?php HTML_boss::displayYesNoImg($row, "img-pub-" . $row->id, 'content'); ?>
                        </td>
                        <td><?php HTML_boss::listCatsCont($categs, $row->id, $directory); ?></td>
                        <td align="center"><?php echo $row->date_created; ?></td>
                    </tr>
                <?php
				$k = !$k;
            }

            ?>
            </table>

            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="contents"/>
            <input type="hidden" name="boxchecked" value="0"/>
        <?php
			echo $pagenav->getListFooter();
        ?>
        </form>
        <?php

    }

    public static function displayContent($row, $fields, $field_values, $cats, $users, $directory, $directories, $type_content, $selected_categ, $tags, $id, $conf) {

        HTML_boss::header(BOSS_CONTENT_EDITION, $directory, $directories, $conf);
        $plugins = BossPlugins::get_plugins($directory, 'fields');
        $tabs = new mosTabs(0,1);
        mosCommonHTML::loadCalendar();
        ?>

    <script type="text/javascript"><!--//--><![CDATA[//><!--
        function submitbutton(pressbutton) {
            if(pressbutton == 'cancel'){
                submitform(pressbutton);
		        return true;
            }
        var mfrm = document.getElementById('adminForm');
        var errorMSG = '';
        var iserror = 0;
        var category = document.getElementById('category');
        var me = document.getElementById('name');
        if (me.value == '') {
		    me.style.background = "red";
		    alert("<?php echo html_entity_decode(addslashes( BOSS_REGWARN_ERROR),ENT_QUOTES); ?> : <?php echo html_entity_decode(addslashes(BOSS_TH_TITLE),ENT_QUOTES); ?>");
	}
        else if(category.value == ''){
                category.style.background = "red";
		    alert("<?php echo html_entity_decode(addslashes( BOSS_REGWARN_ERROR),ENT_QUOTES); ?> : <?php echo html_entity_decode(addslashes(BOSS_TH_CATEGORY),ENT_QUOTES); ?>");
        }
        else {
     
                    <?php
        foreach($fields as $field){
            if(method_exists($plugins[$field->type],'addInWriteScript')){
                echo $plugins[$field->type]->addInWriteScript($field);
            }
        }
        ?>
        if (iserror == 1) {
            alert(errorMSG);
            return false;
        }
            submitform(pressbutton);
		    return true;
		}
	}
	//--><!]]></script>



        <form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm"
              enctype="multipart/form-data">
        <?php
        $tabs->startPane("content");
        $tabs->startTab(BOSS_TAB_GENERAL, "main-page");
        ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_TH_PUBLISH; ?></td>
                    <td colspan="2">
                        <select name="published" id="published">
                            <option value="1" <?php if ($row->published == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_PUBLISH; ?></option>
                            <option value="0" <?php if ($row->published == 0 && !is_null($row->published)) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO_PUBLISH ?></option>
                        </select>
                        &nbsp;&nbsp;<?php echo BOSS_FROM ?>&nbsp;
                        <input name="date_publish" id="date_publish" value="<?php echo $row->date_publish ?>" size="20"/>
                        <input type="reset" class="button" value="..." onClick="return showCalendar('date_publish');" />
                        &nbsp;&nbsp;<?php echo BOSS_TO ?>&nbsp;
                        <input name="date_unpublish" id="date_unpublish" value="<?php echo $row->date_unpublish ?>" size="20"/>
                        <input type="reset" class="button" value="..." onClick="return showCalendar('date_unpublish');" />
                    </td>

                </tr>
                <tr>
                    <td><?php echo BOSS_TH_FRONTPAGE;?></td>
                    <td>
                        <select name="frontpage" id="frontpage">
                            <option value="1" <?php if ($row->frontpage == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES ?></option>
                            <option value="0" <?php if ($row->frontpage == 0 && !is_null($row->frontpage)) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO ?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                <tr>
                <tr>
                    <td><?php echo BOSS_TH_FEATURED;?></td>
                    <td>
                        <select name="featured" id="featured">
                            <option value="1" <?php if ($row->featured == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_YES ?></option>
                            <option value="0" <?php if ($row->featured == 0 && !is_null($row->featured)) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO ?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                <tr>
                <tr>
                    <td><?php echo BOSS_TH_TITLE;?></td>
                    <td><input name="name" id="name" value="<?php echo $row->name ?>" size="45"/></td>
                    <td>&nbsp;</td>
                <tr>
                <tr>
                    <td><?php echo BOSS_NAME_ALIAS;?></td>
                    <td><input name="slug" id="slug" value="<?php echo $row->slug ?>" size="45"/></td>
                    <td>&nbsp;</td>
                <tr>
                <tr>
                    <td><?php echo BOSS_FORM_CATEGORY;?></td>
                    <td><select size="10" multiple="multiple" name="category[]"
                                id="category"><?php HTML_boss::selectCategories(0, BOSS_ROOT . " >> ", $cats, $selected_categ, -1, 1); ?></select>
                    </td>
                    <td>&nbsp;</td>
                <tr>
                    <td><?php echo BOSS_FORM_USER;?></td>
                    <td>
                        <select name="userid" id="userid">
                        <?php  foreach ($users as $user) { ?>
                            <option value="<?php echo $user->id; ?>" <?php if ($user->id == $row->userid) {
                                echo "selected='selected'";
                            } ?>><?php echo $user->username . "(" . $user->id . ")"; ?></option>
                        <?php } ?>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
				<?php HTML_boss::displayFormFields($row, $fields, $field_values, $directory, $plugins); ?>
            </table>
        <?php
	$tabs->endTab();
        $tabs->startTab(BOSS_META, "meta-page");
        ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_META_TITLE; ?></td>
                    <td><input type="text" name="meta_title" size="60" maxlength="60"
                               value="<?php echo @$row->meta_title; ?>"/></td>
                    <td><?php echo BOSS_META_TITLE_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_META_DESC; ?></td>
                    <td><textarea name="meta_desc" cols="60" rows="4"><?php echo @$row->meta_desc; ?></textarea></td>
                    <td><?php echo BOSS_META_DESC_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_META_KEYS; ?></td>
                    <td><textarea name="meta_keys" cols="60" rows="4"><?php echo @$row->meta_keys; ?></textarea></td>
                    <td><?php echo BOSS_META_KEYS_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TAGS; ?></td>
                    <td><textarea name="tags" cols="60" rows="4"><?php echo @$tags; ?></textarea></td>
                    <td><?php echo BOSS_TAGS_LONG; ?></td>
                </tr>
            </table>
        <?php
		$tabs->endTab();
        $tabs->endPane();
        ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <input type="hidden" name="type_content" value="<?php echo $type_content; ?>"/>
            <input type="hidden" name="date_created"
                   value="<?php echo isset($row->date_created) ? $row->date_created : date("Y-m-d"); ?>"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="act" value="contents"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php

    }

    public static function listcategories($nb, $children, $pageNav, $directory, $directories, $defaultTemplate, $conf) {

        HTML_boss::header(BOSS_LIST_CATEGORIES, $directory, $directories, $conf);
        $src_cat = mosGetParam($_REQUEST, 'src_cat', '');
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <div class="fr mb20">
                <span class="gray"><?php echo BOSS_FORM_CATEGORY; ?></span>&nbsp;
                <input type="text" name="src_cat" id="src_cat" value="<?php echo $src_cat; ?>" />
                <input type="submit" value="<?php echo BOSS_SEARCH; ?>" class="button">
                <span class="gray"><?php echo BOSS_FIELD_PUBLISHED; ?></span>&nbsp;
                <select name="select_publish" id="select_publish" onchange="document.adminForm.submit();">
		    		<option value="0"><?php echo BOSS_ALL; ?></option>
		    	    <?php self::selectPublish('select_publish', 'only_pub'); ?>
		    	</select>
            </div>
            <br clear="all"/>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <th class="title" width="2%"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo $nb; ?>);"/></th>
                    <th class="title" width="2%">Id</th>
                    <th class="title" width="30%"><?php echo BOSS_TH_CATEGORY;?></th>
                    <th class="title" width="5%"><?php echo BOSS_TH_IMAGE;?></th>
                    <th class="title" width="5%"><?php echo BOSS_TH_CONTENTS;?></th>                    
                    <th width="3%" colspan="2">
                    <?php echo BOSS_ORDER; ?>
                    </th>
                    <th width="3%">
                        <a href="javascript: saveorder(<?php echo $nb - 1; ?>)">
                            <img src="/administrator/images/filesave.png" border="0" width="16" height="16"/>
                        </a>
                    </th>
                    <th class="title" width="10%"><?php echo BOSS_TH_PUBLISH;?></th>
                    <th class="title" width="20%"><?php echo BOSS_TEMPLATE;?></th>
                    <th class="title" width="20%"><?php echo BOSS_NAME_ALIAS;?></th>
                </tr>
            <?php

            HTML_boss::recurseCategories(0, 0, $children, $pageNav, 0, $nb, $directory, $defaultTemplate);
            ?>
            </table>

            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="categories"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
        <?php

    }

    public static function displayCategory($row, $cats, $directory, $directories, $templates, $comtentTypes, $rights, $conf) {
        ?>
        <script type="text/javascript">
            function submitbutton(pressbutton) {
            <?php getEditorContents('editor1', 'description'); ?>
                submitform(pressbutton);
            }

            function showimage() {
                //if (!document.images) return;
                document.images.preview.src = '<?php echo JPATH_SITE;?>/templates/com_boss/' + getSelectedValue('adminForm', 'template') + '/template_thumbnail.png';
            }
            function getSelectedValue(frmName, srcListName) {
                var form = eval('document.' + frmName);
                var srcList = eval('form.' + srcListName);

                i = srcList.selectedIndex;
                if (i != null && i > -1) {
                    return srcList.options[i].value;
                } else {
                    return null;
                }
            }

        </script>

        <?php HTML_boss::header(BOSS_CATEGORY_EDITION, $directory, $directories, $conf); ?>
        <form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm"
              enctype="multipart/form-data">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_TH_TITLE; ?></td>
                    <td>
			<input type="text" size="50" maxlength="100" name="name" value="<?php echo @$row->name; ?>"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_NAME_ALIAS; ?></td>
                    <td>
			<input type="text" size="50" maxlength="100" name="slug" value="<?php echo @$row->slug; ?>"/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TH_PARENT; ?></td>
                    <td>
                        <select name="parent" id="parent">
                            <option value="0"><?php echo BOSS_ROOT; ?></option>
                        <?php HTML_boss::selectCategories(0, BOSS_ROOT . " >> ", $cats, @$row->parent, @$row->id); ?>
                        </select>
                    </td>
					<td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TH_IMAGE; ?></td>
                    <td>
                        <input type="file" name="cat_image"/>
                    <?php
                    $a_pic = JPATH_BASE . "/images/boss/$directory/categories/" . @$row->id . "cat.jpg";
                    if (file_exists($a_pic)) {
                        echo '<img src="/images/boss/' . $directory . '/categories/' . @$row->id . 'cat.jpg"/>';
                        echo "<input type='checkbox' name='cb_image' value='delete'>" . BOSS_CONTENT_DELETE_IMAGE;
                    }
                    ?>
                    </td>
					<td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TH_PUBLISH; ?></td>
                    <td>
                        <select name="published" id="published">
                            <option value="1" <?php if ($row->published == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_PUBLISH; ?></option>
                            <option value="0" <?php if ($row->published == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO_PUBLISH ?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_SELECT_CONTENT_TYPE2;?></td>
                    <td>
                        <select name="content_types" id="content_types">
                            <option value="0"><?php echo BOSS_SELECT; ?></option>
                            <?php HTML_boss::contentTypesSelect($comtentTypes, $row->content_types); ?>
                        </select>
                    </td>
                    <td><?php echo BOSS_CATEGORY_CONTENT_TYPE; ?></td>
                </tr>
                <tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
                    <td><?php echo BOSS_TEMPLATE; ?></td>
                    <td>
                        
                        <select id='template' name='template' onchange="showimage()">
                            <option value='0'><?php echo BOSS_TEMPLATE_SELECT; ?></option>
                        <?php if (isset($templates)) {
                            foreach ($templates as $tpl) {
                                ?>
                                    <option value='<?php echo $tpl; ?>' <?php if (@$row->template == $tpl) {
                                        echo "selected";
                                    } ?>><?php echo $tpl; ?></option>
                                <?php

                            }
                        }

                        ?></select>
						<?php $tmpl_tmp = (@$row->template) ? @$row->template : 'default' ?>
						<img class="ml10" src="<?php echo JPATH_SITE."/templates/com_boss/" . $tmpl_tmp . "/template_thumbnail.png";?>"
								 name="preview" border="1" alt="<?php echo $tmpl_tmp;?>"/>
                    </td>
					<td>&nbsp;</td>
                </tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
                <tr>
                    <td><?php echo BOSS_TH_DESCRIPTION; ?></td>
                    <td><?php editorArea('editor1', @$row->description, 'description', '100%;', '350', '75', '20'); ?></td>                    
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
                <tr>
                    <td><?php echo BOSS_META_TITLE; ?></td>
                    <td><input type="text" name="meta_title" size="60" maxlength="60"
                               value="<?php echo @$row->meta_title; ?>"/>
					</td>
					<td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_META_DESC; ?></td>
                    <td><textarea name="meta_desc" cols="60" rows="4"><?php echo @$row->meta_desc; ?></textarea></td>                    
					<td>&nbsp;</td>
				</tr>
                <tr>
                    <td><?php echo BOSS_META_KEYS; ?></td>
                    <td><textarea name="meta_keys" cols="60" rows="4"><?php echo @$row->meta_keys; ?></textarea></td>                    
					<td>&nbsp;</td>
				</tr>
                <?php if(@$conf->allow_rights == 1) : ?>
                <tr>
                    <td><?php echo BOSS_RIGHTS;?></td>
                    <td><?php echo $rights->draw_config_table('category');?></td>
					<td>&nbsp;</td>
				</tr>
                <?php endif; ?>
            </table>
            <input type="hidden" name="id" value="<?php echo @$row->id; ?>"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="act" value="categories"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php

    }

    public static function displayFormFields($row, $fields, $field_values, $directory, $plugins) {
        global $mainframe;
        $isAdmin = $mainframe->isAdmin();

        if (isset($fields)) {
            foreach ($fields as $field) {
                //если админка, то делаем все поля редактируемыми, если поле не редактируемое с фронта и не админка, то пропускаем поле
                if($isAdmin == 1){
                    $field->editable = 1;
                }
                else if ($isAdmin != 1 && $field->editable == 0){
                    continue;
                }
                
                $return = jDirectoryField::getFieldForm($field, $row, null, $field_values, $directory, $plugins, "write");
                echo "<tr><td>" . $return->title . "</td>\n";
                echo "<td>" . $return->input . "</td><td>&nbsp;</td></tr>";
            }
        }
    }
    
    public static function showField($row, $directory, $plugin, $tpl=array()){
        ?>
        <li style="display: block;" id="<?php echo $row->fieldid;?>_li">
            

            <div class="block" id="div_field_<?php echo $row->fieldid; ?>">

                <img class="field_img" src="<?php echo JPATH_SITE.$plugin->getFieldIcon($directory);?>" alt="<?php echo $plugin->name;?>" />
                
                
                <span class="note pointer field_title title" id="span_title_<?php echo $row->fieldid; ?>" onmouseover="tooltip(this.id)" onclick="bossEditField(<?php echo $row->fieldid; ?>)">
                    <?php echo $row->title;?>
                    <span class="hidden" id="span_title_<?php echo $row->fieldid; ?>_tip"><?php echo '<h3>'.BOSS_EDIT.' '.BOSS_FIELD.'</h3><p>'.$row->title.'</p>';?></span>
                </span>
              
                <span class="note name"><?php echo $row->name;?></span>
                
                <span class="note pointer type" id="span_type_<?php echo $row->fieldid; ?>" onclick="change_type(<?php echo $row->fieldid; ?>)" onmouseover="tooltip(this.id)">
                    <?php echo $plugin->name;?>
                    <span class="hidden" id="span_type_<?php echo $row->fieldid; ?>_tip"><?php echo BOSS_CH_TYPE_TIP;?></span>
                </span>
                
                <span class="note pointer template" id="span_template_<?php echo $row->fieldid; ?>" onclick="change_template(<?php echo $row->fieldid; ?>)" onmouseover="tooltip(this.id)">
                    <img src="/administrator/components/com_boss/images/formbuilder/comment.png" />
                    <span class="hidden" id="span_template_<?php echo $row->fieldid; ?>_tip">
                        <?php 
                            echo '<h3>'.BOSS_FIELD_GROUP_HREF.'</h3>';
                            echo '<p>';
                            if(!empty($tpl)){
                                foreach ($tpl as $tp){
                                    echo '<hr />';
                                    echo '<strong>'.BOSS_TEMPLATE.':</strong> '.$tp->template.'<br />';
                                    echo '<strong>'.BOSS_WHERE.':</strong> '.$tp->type_tmpl.'<br />';
                                    echo '<strong>'.BOSS_POZ.':</strong> '.$tp->name;
                                }
                            }
                            echo '</p>';
                        ?>
                    </span>
                </span>
                
                <span class="note pointer required" onclick="boss_publ('img-req-<?php echo $row->fieldid; ?>', '<?php echo "act=fields&task=required&tid=" . $row->fieldid . "&directory=$directory"; ?>');">
                    <?php 
                    HTML_boss::displayYesNoImg($row->required, "img-req-" . $row->fieldid); 
                    ?>
                </span>
                <span class="note pointer published"onclick="boss_publ('img-pub-<?php echo $row->fieldid; ?>', '<?php echo "act=fields&task=publish&tid=" . $row->fieldid . "&directory=$directory"; ?>');">
                    <?php 
                    HTML_boss::displayYesNoImg($row->published, "img-pub-" . $row->fieldid); 
                    ?>
                </span>
     
                <span class="note handle"></span>
                <span class="note del" onclick="deleteField('<?php echo $row->fieldid;?>','<?php echo $row->name;?>','<?php echo $row->title;?>')"></span>
            </div>
            <div class="clear"></div>
            <div class="attrs clear element_<?php echo $row->name;?>">
                <input type="hidden" name="fieldids[]" value="<?php echo $row->fieldid;?>" />
            </div>
        </li>
        <?php
    }
    
    public static function showFields(&$rows, $directory, $directories, $plugins, $tpl, $conf) {
        ?>
        <?php HTML_boss::header(BOSS_FIELDS_LIST, $directory, $directories, $conf); ?>
        <div id="form_builder_nav">
            <ul id="form_builder_properties">
				<?php echo BOSS_FIELDS_OPTIONS;?>
			</ul>
            <?php if(count($plugins)>0) {?>

			<ul id="form_builder_toolbox">
                <div style="text-align: center;" id="form_builder_toolbox_header"><?php echo BOSS_FIELDS_NEW;?></div>
                <?php
                foreach($plugins as $key => $plugin){
                    $style = "";
                    if(method_exists($plugin, 'getFieldIcon')){
                        $icon = $plugin->getFieldIcon($directory);
                        if(is_file(JPATH_BASE.$icon)){
                            $style = 'style="background-image:url('.JPATH_SITE.$icon.');"';
                        }
                    }
                    echo "<li id=\"".$key."\" class=\"toolbox\" ".$style.">".$plugin->name."</li>";
                }
                ?>
			</ul>
            <?php } ?>
		</div>

		<div id="form_builder_panel" class="fancy">
                    <div class='contayner'>
                        <form name="fieldList" id="fieldList">
                            <fieldset class='sml'>
                                <legend><?php echo BOSS_FIELDS_LIST;?></legend>                           
                                <div class="list_fieldHeader">
                                    <span class="field_img"></span>
                                    <span class="note title"><?php echo BOSS_TH_TITLE;?></span>
                                    <span class="note name"><?php echo BOSS_TH_NAME;?></span>
                                    <span class="note type"><?php echo BOSS_FIELD_TYPE; ?></span>
                                    <span class="note template"><?php echo BOSS_TEMPLATE; ?></span>
                                    <span class="note required"><?php echo BOSS_FIELD_REQUIRED; ?></span>
                                    <span class="note published"><?php echo BOSS_FIELD_PUBLISHED;?></span>
                                    <span class="note move"><?php echo BOSS_MOVE;?></span>
                                    <span class="note delete"><?php echo BOSS_DELETE;?></span>
                                </div>
                                <ol class="ui-sortable">
                                    <?php 
                                    foreach($rows as $row){
                                        self::showField($row, $directory, $plugins[$row->type], @$tpl[$row->fieldid]);
                                    }
                                    ?>
                                </ol>
                            </fieldset>
                        </form>
                    </div>
                    <input class="button" type="button" id="saveFieldOrderButton" value="<?php echo BOSS_SAVE_FIELD_ORDER; ?>" onclick="bossSaveFieldOrder(<?php echo $directory;?>)" />
                </div>

        <input type="hidden" name="directory" id="directory" value="<?php echo $directory;?>" />
        <input type="hidden" name="change_type_fieldid" id="change_type_fieldid" value="" />
        <?php
    }

    public static function editfield(&$row, $lists, $plug, $types, $directory, $directories, $task, $fnames) {
        ?>

        <h4 class="edit_field_header"><?php 
                if(!empty($row->name)) 
                        echo $row->title.' - ';
                echo $plug->name;
        ?></h4>
        <form action="index2.php?option=com_boss" method="POST" name="fieldForm" id="fieldForm">
        <?php
        $tabs = new Sliders();
        $tabs->startPane("field_properties");
        $tabs->startTab(BOSS_PARAMS, "params");
        ?>
        <table class="adminform">
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_NAME;?></td>
                <td align=left width="45%"><input onchange="prep4SQL(this, Array('<?php echo implode("', '", $fnames); ?>'));" type="text" name="name" mosReq=1
                                                  mosLabel="Name" size="30" value="<?php echo $row->name; ?>"/>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_TITLE;?></td>
                <td width="45%" align=left><input type="text" name="title" mosReq=1 mosLabel="Title" size="30"
                                                  value="<?php echo $row->title; ?>"/></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_DISPLAY_TITLE;?></td>
                <td width="45%"><?php echo $lists['display_title']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_DESCRIPTION;?></td>
                <td width="45%" align=left><input type="text" name="description" mosLabel="Description" size="30"
                                                  value="<?php echo $row->description; ?>"/></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_TEXT_BEFORE;?></td>
                <td width="45%" align=left><input type="text" name="text_before" mosLabel="TextBefore" size="30"
                                                  value="<?php echo $row->text_before; ?>"/></td>
                <td><?php echo boss_helpers::bossToolTip(BOSS_FIELD_TEXT_BEFORE_LONG);?></td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_TEXT_AFTER;?></td>
                <td width="45%" align=left><input type="text" name="text_after" mosLabel="TextAfter" size="30"
                                                  value="<?php echo $row->text_after; ?>"/></td>
                <td><?php echo boss_helpers::bossToolTip(BOSS_FIELD_TEXT_AFTER_LONG);?></td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_TAGS_OPEN;?></td>
                <td width="45%" align=left><input type="text" name="tags_open" mosLabel="TagsOpen" size="30"
                                                  value="<?php echo $row->tags_open; ?>"/></td>
                <td><?php echo boss_helpers::bossToolTip(BOSS_FIELD_TAGS_OPEN_LONG);?></td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_TAGS_SEPARATOR;?></td>
                <td width="45%" align=left>
                    <input type="text" name="tags_separator" mosLabel="TagsSeparator" size="30" value="<?php echo $row->tags_separator; ?>"/>
                </td>
                <td><?php echo boss_helpers::bossToolTip(BOSS_FIELD_TAGS_SEPARATOR_LONG);?></td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_TAGS_CLOSE;?></td>
                <td width="45%" align=left>
                    <input type="text" name="tags_close" mosLabel="Description" size="30" value="<?php echo $row->tags_close; ?>"/>
                </td>
                <td><?php echo boss_helpers::bossToolTip(BOSS_FIELD_TAGS_CLOSE_LONG);?></td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_SIZE;?></td>
                <td width="45%">
                    <input type="text" name="size" mosLabel="Size" size="30" value="<?php echo $row->size; ?>"/>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_REQUIRED;?></td>
                <td width="45%"><?php echo $lists['required']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_PUBLISHED;?></td>
                <td width="45%"><?php echo $lists['published']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_SEARCHABLE;?></td>
                <td width="45%"><?php echo $lists['searchable']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FILTER_ALLOW;?></td>
                <td width="45%"><?php echo $lists['filter']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_EDITABLE;?></td>
                <td width="45%"><?php echo $lists['editable']; ?></td>
                <td>&nbsp;</td>
            </tr>

            <tr>
                <td width="45%"><?php echo BOSS_FIELD_PROFILE;?></td>
                <td width="45%"><?php echo $lists['profile']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_SORT_OPTION;?></td>
                <td width="45%"><?php echo $lists['sort']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="45%"><?php echo BOSS_FIELD_SORT_DIRECTION;?></td>
                <td width="45%"><?php echo $lists['sort_direction']; ?></td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <?php
        $tabs->endTab();
        $tabs->startTab(BOSS_CONTENT_TYPE_HREF, "field_category");
        ?>
        <table class="adminform">
            <tr>
                <td>
                    <select name="field_catsid[]" multiple='multiple' id="field_catsid"
                            size="5">
                    <?php
                            if (strpos($row->catsid, ",-1,") === false)
                        echo "<option value='-1'>" . BOSS_ALL_CONTENT_TYPE . "</option>";
                    else
                        echo "<option value='-1' selected>" . BOSS_ALL_CONTENT_TYPE . "</option>";
                    HTML_boss::contentTypesSelect($types, $row->catsid);
                    ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php $tabs->endTab();

        if($task == 'new'):
        $tabs->startTab(BOSS_EMPTY_DIRS, "field_dir");?>
            <table class="adminform">
                <tr>
                    <td>
                        <?php foreach ($directories as $dir){
                                $selected = ($dir->id == $directory) ? 'checked="checked"' : '';
                        ?>

                                <div style="float: left; width: 30%; margin: 5px 0 5px 0;">
                                    <legend>
                                        <input type="checkbox" name="directories[]" value="<?php echo $dir->id;?>" <?php echo $selected;?> />
                                        <?php echo $dir->name;?>
                                    </legend>
                                </div>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <?php $tabs->endTab();
            endif;

            if(!empty($plug->html)):
            $tabs->startTab(BOSS_FIELD_TYPE_PARAMS, "field_plug_params"); ?>
            <table class="adminform">
                <tr>
                    <td>
                        <?php

                            echo $plug->html;

                        ?>
                    </td>
                </tr>
            </table>
        <?php
        $tabs->endTab();
        endif;
        $tabs->endPane();
        ?>
        <input type="hidden" name="type" id="type" value="<?php echo $plug->type;?>" />
        <input type="hidden" name="field_action" id="field_action" value="<?php echo $task; ?>" />
        <input type="hidden" name="fieldid" value="<?php echo $row->fieldid; ?>"/>
        <input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>"/>        
        <input type="hidden" name="directory" id="directory" value="<?php echo $directory; ?>"/>
        <div class="center">
            <input class="button" type="button" value="<?php echo BOSS_APPLY;?>" onclick="bossControlFields(<?php echo $directory; ?>)"/>
        </div>
        </form>

        <?php
        if ($row->fieldid > 0) {
            print "<script type=\"text/javascript\"> document.fieldForm.name.readOnly=true; </script>";
        }
    }

    public static function showDirectories($rows, $directory, $conf) {
        ?>
        <?php HTML_boss::header(BOSS_LIST_DIRECTORIES, $directory, $rows, $conf); ?>
        <form action="index2.php" method="post" name="adminForm">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <th class="title" width="5"><input type="checkbox" name="toggle" value=""
                                                       onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th width="25">Id</th>
                    <th class="title" width="97%"><?php echo BOSS_TH_TITLE;?></th>
                </tr>
            <?php
                    $k = 0;
            $i = 0;
            if (count($rows) > 0):
                foreach ($rows as $row) {
                    ?>
                        <tr class="row<?php echo $k; ?>">
                            <td><input type="checkbox" id="cb<?php echo $i;?>" name="tid[]"
                                       value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);"/></td>
                            <td><?php echo $row->id; ?></td>
                            <td><?php HTML_boss::displayLinkText($row->name, "index2.php?option=com_boss&act=configuration&task=edit&directory=" . $row->id . "&tid[]=" . $row->id); ?></td>
                        </tr>
                    <?php
                                    $k = !$k;
                    $i++;
                }
            endif;
            ?>
            </table>

            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="manager"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
        <?php

    }

    public static function displayGroupFields($fields, $group_id) {
        if (isset($fields[$group_id]))
            echo implode(" | ", $fields[$group_id]);
    }

    public static function listTemplates($templates, $directory, $directories, $conf) {
        HTML_boss::header(BOSS_LIST_TEMPLATES, $directory, $directories, $conf); ?>

        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <th class="title acenter" width="5">
                        <input type="checkbox" name="toggle" value=""
                               onclick="checkAll(<?php echo count($templates); ?>);"/>
                    </th>
                    <th class="title"><?php echo BOSS_TH_TITLE;?></th>
                    <th class="title acenter"><?php echo BOSS_TH_IMAGE;?></th>
                    <th class="title acenter"><?php echo BOSS_TH_CAT_TMPL;?></th>
                    <th class="title acenter"><?php echo BOSS_TH_CONTENT_TMPL;?></th>
                    <th class="title acenter"><?php echo BOSS_TH_EDIT_FIELDS_TMPL;?></th>
                    <th class="title acenter"><?php echo BOSS_TH_EDIT_SOURCE_TMPL;?></th>
                </tr>
            <?php
                    if (isset($templates)) {
                $k = 0;
                $i = 0;
                foreach ($templates as $tpl) {
                    ?>
                        <tr class="row<?php echo $k; ?>">
                            <td>
                                <input type="checkbox" id="cb<?php echo $i;?>" name="tid[]" value="<?php echo $tpl;?>"
                                       onclick="isChecked(this.checked);"/>
                            </td>
                            <td><?php echo $tpl; ?></td>
                            <td align="center">
                                <img src="<?php echo JPATH_SITE . "/templates/com_boss/" . $tpl . "/template_thumbnail.png";?>"
                                     border="1"/>
                            </td>
                            <td align="center">
                                <a href="<?php echo JPATH_SITE; ?>/administrator/index2.php?option=com_boss&directory=<?php echo $directory; ?>&act=templates&task=edit_tmpl&template=<?php echo $tpl; ?>&type_tmpl=category">
                                    <img src="<?php echo JPATH_SITE; ?>/administrator/components/com_boss/images/16x16/categories.png"
                                         title="<?php echo BOSS_EDIT_CAT_TMPL; ?>"/>
                                </a>
                            </td>
                            <td align="center">
                                <a href="<?php echo JPATH_SITE; ?>/administrator/index2.php?option=com_boss&directory=<?php echo $directory; ?>&act=templates&task=edit_tmpl&template=<?php echo $tpl; ?>&type_tmpl=content">
                                    <img src="<?php echo JPATH_SITE; ?>/administrator/components/com_boss/images/16x16/contents.png"
                                         title="<?php echo BOSS_EDIT_CONTENT_TMPL; ?>"/>
                                </a>
                            </td>
                            <td align="center">
                                <a href="<?php echo JPATH_SITE; ?>/administrator/index2.php?option=com_boss&directory=<?php echo $directory; ?>&act=templates&task=list_tmpl_fields&template=<?php echo $tpl; ?>">
                                    <img src="<?php echo JPATH_SITE; ?>/administrator/components/com_boss/images/16x16/template_fields.png"
                                         title="<?php echo BOSS_EDIT_TMPL_FIELDS; ?>"/>
                                </a>
                            </td>
                            <td align="center">
                                <a href="<?php echo JPATH_SITE; ?>/administrator/index2.php?option=com_boss&directory=<?php echo $directory; ?>&act=templates&task=edit_tmpl_source&source_file=&template=<?php echo $tpl; ?>">
                                    <img src="<?php echo JPATH_SITE; ?>/administrator/components/com_boss/images/16x16/code.png"
                                         title="<?php echo BOSS_TH_EDIT_SOURCE_TMPL_LONG; ?>"/>
                                </a>
                            </td>
                        </tr>
                    <?php
                    $k = !$k;
                    $i++;
                }
            }
            ?>
            </table>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="templates"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
        <?php

    }
    
    public static function change_template($directory, $fieldid, $fieldtitle, $templates, $type_tpl){
      ?>
        <h4 class="edit_field_header"><?php echo $fieldtitle;?></h4>
        <form action="index2.php" method="post" name="tplForm" id="tplForm">
            <?php echo $templates; ?>
            <?php echo $type_tpl; ?>
            <div id="tpl_poz"><?php echo BOSS_SELECT_TEMPLATE_TYPETPL;?></div>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="fieldid" value="<?php echo $fieldid;?>"/>
        </form>
        <?php
    }
    
    public static function load_poz($pozition, $selected_poz){
        foreach ($pozition as $poz){
            $checked = '';
            if(in_array($poz->id, $selected_poz)){
                $checked = 'checked="checked"';
            }    
            ?>
        <label>            
            <input type="checkbox" value="<?php echo $poz->id; ?>" <?php echo $checked; ?> class="inputbox" name="pozitions[]" />
            <?php echo $poz->name; ?>
        </label>
        <br />
        <?php
        }
        ?>
        <input class="button" type="button" id="savePozButton" value="<?php echo BOSS_SAVE_FIELD_POZ; ?>" onclick="bossSavePoz()" />
        <?php
    }
    
    public static function editTemplate($directory, $directories, $template, $type_tmpl, $positions, $groupfields, $fields, $cats, $conf) {
        HTML_boss::header(BOSS_LIST_TEMPLATES, $directory, $directories, $conf);
        if ($type_tmpl == 'category')
            $img = 'template_category.png';
        else
            $img = 'template_content.png';
        ?>

        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <th>
                        <?php echo BOSS_FIELDS; ?>
                    </th>
                    <?php
                    foreach ($positions as $position) {

                        ?>
                        <th>
                            <?php
                            echo ' <img src="/administrator/images/info.png" title="' . $position->desc . '"> ';
                            echo $position->name;
                            ?>
                        </th>
                    <?php } ?>       
                    </tr>


                        <?php
                        if (isset($fields)) {
                            foreach ($fields as $field) {
                                ?>
                            <tr>
                                <th><?php echo $field->title; ?></th>
                                <?php 
                                foreach ($positions as $position) {
                                    $checked = '';
                                    if (@in_array($field->fieldid, $groupfields[$position->id]['fieldid']))
                                        $checked = 'checked="checked"';

                                ?>
                                <td style="text-align: center;">
                                    <input type="checkbox"
                                           name="required|<?php echo $position->id; ?>|<?php echo $field->fieldid; ?>"
                                           value="1" <?php echo $checked; ?> />

                                    <input type="text" maxlength="2" size="2"
                                           name="ordering|<?php echo $position->id; ?>|<?php echo $field->fieldid; ?>"
                                           value="<?php echo @$groupfields[$position->id][$field->fieldid]['ordering']; ?>" <?php echo $checked; ?> />
                                </td>
                                <?php } ?>                                    
                            </tr>
                            <?php
                        }

                }
                ?>

            </table>
            <br/>
            <div style="text-align: center;">
                <?php echo BOSS_TPL_FIELDS_DESC; ?>
            </div>
            <br/>
        <div style="text-align: center;">
            <img src="<?php echo JPATH_SITE . "/templates/com_boss/$template/$img"; ?>"/>
        </div>
        <input type="hidden" name="option" value="com_boss"/>
        <input type="hidden" name="act" value="templates"/>
        <input type="hidden" name="task" value="save_tmpl"/>
        <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
        <input type="hidden" name="template" value="<?php echo $template; ?>"/>
        <input type="hidden" name="type_tmpl" value="<?php echo $type_tmpl; ?>"/>
        <input type="hidden" name="boxchecked" value="0"/>
        </form>
             

        <?php

    }
    public static function listTemplateFields($directory,$directories, $fields, $cats, $template, $conf){
     HTML_boss::header(BOSS_EDIT_TEMPLATE_FIELD, $directory, $directories, $conf); ?>
        <form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm">
            <table class="adminlist">
                <tr>
                    <th><?php echo BOSS_NAME_DIR; ?></th>
                    <th><?php echo BOSS_FIELD_DESCRIPTION; ?></th>
                    <th><?php echo BOSS_CATEGORIES; ?></th>
                    <th><?php echo BOSS_TYPETPL; ?></th>
                    <th><?php echo BOSS_FIELD_PUBLISHED; ?></th>
                    <th><?php echo BOSS_DELETE; ?></th>
                </tr>
                    <?php
                    foreach ($fields as $field){
                        if (empty($class)|| $class == ' class="row1"') $class = ' class="row0"';
                        else $class = ' class="row1"';
                        
                        $categs = '';
                        if($field->catsid == ''){
                           $categs = BOSS_NO;
                        }
                        else if($field->catsid == ',-1,'){
                           $categs = BOSS_ALL;
                        }
                        else {
                           $catsid = explode(',', $field->catsid);
                           foreach($catsid as $val){
                               if(isset ($cats[$val])){
                                   $categs .= $cats[$val]->name.'<br />';
                               }
                           }
                        }
                        
                        ;?>
                        <tr<?php echo $class; ?>>
                            <td>
                                <a href="index2.php?option=com_boss&act=templates&task=edit_tmpl_field&fieldid=<?php echo $field->id; ?>&directory=<?php echo $directory; ?>">
                                    <?php echo $field->name; ?>
                                </a>
                            </td>
                            <td><?php echo $field->desc; ?></td>
                            <td><?php echo $categs; ?></td>
                            <td><?php echo $field->type_tmpl; ?></td>
                            <td class="td-state" align="center" onclick="boss_publ('img-pub-<?php echo $field->id; ?>', '<?php echo "act=template_fields&task=publish&tid=" . $field->id . "&directory=$directory"; ?>');">
                                <?php HTML_boss::displayYesNoImg($field->published, "img-pub-" . $field->id); ?>
                            </td>
                            <td align="center">
                                <a href="index2.php?option=com_boss&act=templates&task=delete_tmpl_field&fieldid=<?php echo $field->id; ?>&directory=<?php echo $directory; ?>&template=<?php echo $template; ?>">
                                    <img src="images/trash_mini.png" />
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
               </table>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="act" value="templates"/>
            <input type="hidden" name="task" value="new_tmpl_field"/>
        </form>
        <?php
    }
    
    public static function editTemplateField($directory, $directories, $field, $cats, $selectedCats, $templates, $conf){
        HTML_boss::header(BOSS_EDIT_TEMPLATE_FIELD, $directory, $directories, $conf); ?>
        <form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_TH_PUBLISH; ?></td>
                    <td colspan="2">
                        <select name="published" id="published">
                            <option value="1" <?php if (@$field->published == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_PUBLISH; ?></option>
                            <option value="0" <?php if (@$field->published == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO_PUBLISH ?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TH_TITLE;?></td>
                    <td><input name="name" id="name" value="<?php echo @$field->name ?>" size="45"/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_FIELD_DESCRIPTION;?></td>
                    <td><input name="desc" id="desc" value="<?php echo @$field->desc ?>" size="45"/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TEMPLATE;?></td>
                    <td><select name="template" id="template">
                           <?php foreach($templates as $template){ ?> 
                            <option value="<?php echo $template;?>" <?php if (@$field->template == $template) {
                                echo "selected";
                            } ?>><?php echo $template;?></option>
                            <?php } ?> 
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TYPETPL;?></td>
                    <td><select name="type_tmpl" id="type_tmpl">
                            <option value="content" <?php if (@$field->type_tmpl == "content") {
                                echo "selected";
                            } ?>><?php echo BOSS_TH_CONTENT_TMPL;?></option>
                            <option value="category" <?php if (@$field->type_tmpl == "category") {
                                echo "selected";
                            } ?>><?php echo BOSS_TH_CAT_TMPL;?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_FORM_CATEGORY_GROUPS;?></td>
                    <td><select size="10" multiple="multiple" name="catsid[]" id="catsid">
                        <option value="-1" <?php if (in_array('-1', $selectedCats)) {
                                echo "selected";
                            } ?>><?php echo BOSS_ALL;?></option>    
                        <?php HTML_boss::selectCategories(0, BOSS_ROOT . " >> ", $cats, $selectedCats, -1, 1); ?>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <input type="hidden" name="fieldid" value="<?php echo @$field->id; ?>"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="act" value="templates"/>
            <input type="hidden" name="task" value="save_tmpl_field"/>
        </form>
        <?php
    }

    public static function listPlugins($directory, $directories, $plugins, $used, $conf) {
        HTML_boss::header(BOSS_PLUGINS, $directory, $directories, $conf);?>
        <form action="index2.php" method="post" name="filterForm">
            <div class="fr mb20">
			    <span class="gray"><?php echo BOSS_USE; ?></span>&nbsp;
                <select name="used" id="catid" onchange="document.filterForm.submit();">
			    	<option value=""><?php echo BOSS_ALL; ?></option>
			        <option value="0" <?php if ($used === '0') echo 'selected="selected"'; ?>><?php echo BOSS_NOTUSED; ?></option>
                    <option value="1" <?php if ($used === '1') echo 'selected="selected"'; ?>><?php echo BOSS_USED; ?></option>
			    </select>
                <input type="hidden" name="act" value="plugins"/>
                <input type="hidden" name="option" value="com_boss"/>
                <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            </div>
        </form>
        <div style="clear:both;" ></div>
        <form enctype="multipart/form-data" action="index2.php" method="post" name="filename">
            <table class="adminform">
                <tr>
                    <th>
                    <?php echo BOSS_UPLOAD_PACAGE;?>
                    </th>
                </tr>
                <tr>
                    <td align="left">
                    <?php echo BOSS_PACAGE;?>
                        <input class="text_area" name="userfile" type="file" size="70"/>
                        <input class="button" type="submit" value="<?php echo BOSS_UPLOAD_INSTALL;?>"/>
                    </td>
                </tr>
                <tr>
                    <th><?php echo BOSS_SELECT_PLUG_DIR;?></th>
                </tr>
                <tr>
                    <td>
                        <?php foreach ($directories as $dir){
                                $selected = ($dir->id == $directory) ? 'checked="checked"' : '';
                        ?>

                                <div style="float: left; width: 30%; margin: 5px 0 5px 0;">
                                    <legend>
                                        <input type="checkbox" name="directories[]" value="<?php echo $dir->id;?>" <?php echo $selected;?> />
                                        <?php echo $dir->name;?>
                                    </legend>
                                </div>
                        <?php } ?>
                        <br clear="all" />
                    </td>
                </tr>
            </table>

            <input type="hidden" name="task" value="upload"/>
            <input type="hidden" name="act" value="plugins"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
        </form>
        <br/>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <th class="title">
                        <input type="checkbox" name="toggle" value=""
                               onclick="checkAll(<?php echo count($plugins); ?>);"/>
                    </th>
                    <th class="title">
                        <?php echo BOSS_FIELD_VALUE_NAME; ?>
                    </th>
                    <th class="title">
                        <?php echo BOSS_TH_TYPE; ?>
                    </th>
                    <th class="title">
                        <?php echo BOSS_SETTINGS; ?>
                    </th>
                </tr>
            <?php
                if (isset($plugins)) {
                $k = 0;
                $i = 0;
                foreach ($plugins as $plugin) {
                    ?>
                        <tr class="row<?php echo $k; ?>">
                            <td>
                                <input type="checkbox" id="cb<?php echo $i;?>" name="tid[]"
                                       value="<?php echo $plugin['folder'] . '/' . $plugin['file'];?>"
                                       onclick="isChecked(this.checked);"/>
                            </td>
                            <td>
                                <?php echo $plugin['file']; ?>
                            </td>
                            <td>
                                <?php echo $plugin['folder']; ?>
                            </td>
                            <td>
                                <?php if($plugin['folder'] != 'fields'){
                                $bossPlugin = BossPlugins::get_plugin($directory, $plugin['file'], $plugin['folder']);
                                if(method_exists($bossPlugin, 'displaySettingsForm'))
                                           echo '<a href="'.JPATH_SITE.'/administrator/index2.php?option=com_boss&act=plugins&task=edit&directory='.$directory.'&folder='.$plugin['folder'].'&plugin='.$plugin['file'].'">'.BOSS_EDIT.'</a>';
                                      }
                                ?>
                            </td>
                        </tr>
                    <?php
                            $k = !$k;
                    $i++;
                }
            }
            ?>
            </table>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="plugins"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
        <?php

    }
    
    public static function editPlugin($directory, $directories, $plugin, $conf) {
        HTML_boss::header(BOSS_PLUGINS, $directory, $directories, $conf);
        if (method_exists($plugin, 'displaySettingsForm')) {
            $plugin->displaySettingsForm($directory);
        }  
    }
    
    public static function listFieldImages($fieldimages, $directory, $directories, $conf) {
        HTML_boss::header(BOSS_RADIOIMAGE, $directory, $directories, $conf); ?>
        <form enctype="multipart/form-data" action="index2.php" method="post" name="filename">

            <table class="adminform">
                <tr>
                    <th>
                    <?php echo BOSS_FIELDIMAGES_UPLOAD;?>
                    </th>
                </tr>
                <tr>
                    <td align="left">
                    <?php echo BOSS_FIELDIMAGES_FILE;?>
                        <input class="text_area" name="userfile" type="file" size="70"/>
                        <input class="button" type="submit" value="<?php echo BOSS_FIELDIMAGES_BUTTON;?>"/>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="task" value="upload"/>
            <input type="hidden" name="act" value="fieldimage"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
        </form>
        <br/>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <th class="title">
                        <input type="checkbox" name="toggle" value=""
                               onclick="checkAll(<?php echo count($fieldimages); ?>);"/>
                    </th>
                    <th class="title">
                    <?php echo BOSS_TH_IMAGE; ?>
                    </th>
                    <th class="title">
                    <?php echo BOSS_TH_TITLE; ?>
                    </th>
                </tr>
            <?php
                    if (isset($fieldimages)) {
                $k = 0;
                $i = 0;
                foreach ($fieldimages as $fieldimage) {
                    ?>
                        <tr class="row<?php echo $k; ?>">
                            <td>
                                <input type="checkbox" id="cb<?php echo $i;?>" name="tid[]"
                                       value="<?php echo $fieldimage;?>" onclick="isChecked(this.checked);"/>
                            </td>
                            <td>
                                <img src="<?php echo JPATH_SITE."/images/boss/$directory/fields/$fieldimage";?>"
                                     border="1"/>
                            </td>
                            <td>
                            <?php	echo $fieldimage; ?>
                            </td>
                        </tr>
                    <?php
                                    $k = !$k;
                    $i++;
                }
            }
            ?>
            </table>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="fieldimage"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
        <?php

    }

/*
*  Функции для загрузки csv
*/


    public static function showImpExpForm($directory, $directories, $packs, $conf){
        HTML_boss::header(BOSS_EX_IM_HEADER, $directory, $directories, $conf); ?>
        <table width="100%">
            <tr>
                <td width="50%">
                    <fieldset><legend><?php echo BOSS_EX_HEADER; ?></legend>
                        <form action="index2.php" name="export_form" method="POST">
                            <table class="adminform">
                                <tr style="height:40px">
                                    <td width="50%">
                                        <input type="checkbox" name="exp_tables" value="1"/>
                                        <?php echo BOSS_EX_TABLES; ?>
                                    </td>
                                    <td width="50%"><input type="checkbox" name="exp_content" value="1"/>
                                        <?php echo BOSS_EX_CONTENT; ?>
                                    </td>
                                </tr>
                                <tr style="height:40px">
                                    <td width="50%">
                                        <input type="checkbox" name="exp_templates" value="1"/>
                                        <?php echo BOSS_EX_TMPL; ?>
                                    </td>
                                    <td width="50%">
                                        <input type="checkbox" name="exp_plugins" value="1"/>
                                        <?php echo BOSS_EX_PLUG; ?>
                                    </td>
                                </tr>
                                <tr style="height:40px">
                                    <td>
                                        <input type="text" name="pack_name" size="20" maxlength="40" value="pack"/>
                                        <?php echo BOSS_EX_NAME; ?>
                                    </td>
                                    <td>
                                        <span class="button">
                                            <input class="button" type="submit" value=" <?php echo BOSS_EXPORT;?> "/>
                                        </span>
                                    </td>
                                </tr>
                                <?php if(count($packs)>0){?>
                                <tr style="height:40px">
                                    <td colspan="2">
                                        <?php foreach ($packs as $pack){
                                                   $id = str_replace('.zip', '', $pack)
                                        ?>

                                            <div id="<?php echo $id; ?>">
                                                <table>
                                                    <tr>
                                                        <td><img src="/administrator/images/downarrow0.png"/></td>
                                                        <td><a href="<?php echo JPATH_SITE; ?>/images/boss/<?php echo $directory.'/'.$pack; ?>"><?php echo BOSS_DOWNLOAD_FILE." ".$pack;?></a></td>
                                                        <td><img src="/administrator/images/trash_mini.png"/></td>
                                                        <td><a href="" onClick="delete_pack('<?php echo $directory; ?>', '<?php echo $id; ?>'); return false;"><?php echo BOSS_CONTENT_DELETE;?></a></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </table>
                            <input type="hidden" name="option" value="com_boss">
                            <input type="hidden" name="act" value="export_import">
                            <input type="hidden" name="task" value="export">
                            <input type="hidden" name="directory" value="<?php echo $directory; ?>">
                        </form>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <br />
                      <fieldset><legend><?php echo BOSS_IM_HEADER; ?></legend>
                        <form enctype="multipart/form-data" action="index2.php" method="post" name="import_form">

                            <table class="adminform">
                                <tr style="height:40px">
                                    <td>
                                        <?php echo BOSS_DIRECTORY_ID;?>
                                    </td>
                                    <td>
                                        <input type="text" name="new_directory" size="5" maxlength="5" value=""/>
                                    </td>
                                    <td>
                                        <?php echo BOSS_PACAGE;?>
                                    </td>
                                    <td>
                                        <input class="text_area" name="pack" type="file" size="70"/>
                                        <input class="button" type="submit" value="<?php echo BOSS_UPLOAD_INSTALL;?>"/>
                                    </td>
                                </tr>
                                <tr style="height:40px">
                                    <td colspan="4">
                                        <?php echo BOSS_IM_ALARM;?>
                                    </td>
                                </tr>
                            </table>

                            <input type="hidden" name="task" value="import"/>
                            <input type="hidden" name="act" value="export_import"/>
                            <input type="hidden" name="option" value="com_boss"/>
                        </form>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td>
                    <br />
                    <fieldset><legend><?php echo BOSS_IM_JOOS_HEADER; ?></legend>
                       <form name="import_joostina" action="index2.php" method="post">
                           <table class="adminform">
                               <tr style="height:40px">
                                   <td>
                                       <input type="checkbox" name="imp_category" value="1"/>
                                       <?php echo BOSS_IM_JOOS_CATS; ?>&nbsp;&nbsp;&nbsp;
                                   </td>
                                   <td>
                                       <input type="checkbox" name="imp_content" value="1"/>
                                       <?php echo BOSS_IM_JOOS_CONTENT; ?>&nbsp;&nbsp;&nbsp;
                                   </td>
                                   <td rowspan="2"><input class="button" type="submit" value="<?php echo BOSS_IM_HEADER;?>"/></td>
                               </tr>
                               <tr style="height:40px">
                                   <td>
                                       <input type="text" size="10" maxlength="30"  name="introtext" value="content_"/>
                                       <?php echo BOSS_IM_JOOS_INTRO; ?>&nbsp;&nbsp;&nbsp;
                                   </td>
                                   <td>
                                       <input type="text" size="10" maxlength="30" name="fulltext" value="content_"/>
                                       <?php echo BOSS_IM_JOOS_FULL; ?>&nbsp;&nbsp;&nbsp;
                                   </td>
                               </tr>
                           </table>
                            <input type="hidden" name="directory"   value="<?php echo $directory; ?>"/>
                            <input type="hidden" name="task"        value="import_joostina"/>
                            <input type="hidden" name="act"         value="export_import"/>
                            <input type="hidden" name="option"      value="com_boss"/>
                       </form>
                    </fieldset>
                </td>
            </tr>
        </table>
<?php
    }

    public static function listUsers($directory, $directories, $pageNav, $users, $conf) {
		HTML_boss::header(BOSS_TH_USERS, $directory, $directories, $conf);
        $pathToImg = JPATH_SITE.'/administrator/components/com_boss/images/16x16/'
        ?>
        <form action="index2.php" method="get" name="adminForm">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <th width="5"><input type="checkbox" name="toggle" value=""
                                         onclick="checkAll(<?php echo count($users); ?>);"/></th>
                    <th width="5">Id</th>
                    <th width="20%"><?php echo BOSS_NAME_USER;?></th>
                    <th width="20%"><?php echo BOSS_LOGIN;?></th>
                    <th width="20%"><?php echo BOSS_ROLE;?></th>
                    <th width="14%"><?php echo BOSS_EXTRA_FIELDS;?></th>
                    <th width="13%"><?php echo BOSS_DATE_REGISTER;?></th>
                    <th width="13%"><?php echo BOSS_DATE_LAST_VIZIT;?></th>
                </tr>
            <?php
            $k = 0;
            for ($i = 0; $i < count($users); $i++) {
                $user = $users[$i];
                $linkEdit = "index2.php?option=com_boss&act=users&task=edit&directory=" . $directory . "&tid[]=" . $user->userid;
                $linkDelete = "index2.php?option=com_boss&act=users&task=delete&directory=" . $directory . "&tid[]=" . $user->userid;
                ?>
                    <tr class="row<?php echo $k; ?>">
                        <td><input type="checkbox" id="cb<?php echo $i;?>" name="tid[]" value="<?php echo $user->userid; ?>"
                                   onclick="isChecked(this.checked);"/></td>
                        <td align="right"><?php echo $user->userid; ?></td>
                        <td>
                            <?php HTML_boss::displayLinkText($user->name, "index2.php?option=com_users&task=editA&hidemainmenu=1&id=" . $user->userid); ?>
                          </td>
                        <td align="center"><?php echo $user->username; ?></td>
                        <td align="center"><?php echo $user->usertype; ?></td>
                        <td align="center">
                            <?php HTML_boss::displayLinkImage($pathToImg.'pencil.png', $linkEdit, BOSS_EDIT); ?>
                            &nbsp; &nbsp; &nbsp;
                            <?php HTML_boss::displayLinkImage($pathToImg.'delete.png', $linkDelete, BOSS_CONTENT_DELETE); ?>
                        </td>
                        <td align="center"><?php echo $user->registerDate; ?></td>
                        <td align="center"><?php echo $user->lastvisitDate; ?></td>
                    </tr>
                <?php
				$k = !$k;
            }

            ?>
            </table>

            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="users"/>
            <input type="hidden" name="boxchecked" value="0"/>
        <?php
			echo $pageNav->getListFooter();
        ?>
        </form>
        <?php
    }

    public static function editUserInfo($directory, $directories, $userFields, $fields, $users, $selectedUserId, $conf){

         HTML_boss::header(BOSS_CONTENT_EDITION, $directory, $directories, $conf);
         $plugins = BossPlugins::get_plugins($directory, 'fields');
        ?>
        <form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td width="30%"><?php echo BOSS_TH_USER; ?></td>
                    <td width="40%">
                        <select name="userid" id="usreid">
				            <option value="0"><?php echo BOSS_SELECT; ?></option>
			                <?php self::selectAutor($users, $selectedUserId); ?>
			            </select>
                    </td>
                    <td width="30%">&nbsp;</td>
                </tr>
                         <?php
         foreach ($fields as $field){  ?>
                <tr>
                    <td width="30%"><?php echo $field->title; ?></td>
                    <td width="40%">
                        <?php echo $plugins[$field->type]->getFormDisplay($directory, $userFields, $field, null);  ?>
                    </td>
                    <td width="30%"><?php echo $field->description; ?></td>
                </tr>
         <?php }  ?>
            </table>
            <input type="hidden" name="directory"   value="<?php echo $directory; ?>"/>
            <input type="hidden" name="task"        value="save"/>
            <input type="hidden" name="act"         value="users"/>
            <input type="hidden" name="option"      value="com_boss"/>
        </form>
         <?php
    }
    
    public static function listContentTypes($types, $pageNav, $directory, $directories, $nb, $conf) {

        HTML_boss::header(BOSS_CONTENT_TYPES, $directory, $directories, $conf);
        $src_cat = mosGetParam($_REQUEST, 'src_cat', '');
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <div class="fr mb20">
                <span class="gray"><?php echo BOSS_CONTENT_TYPES; ?></span>&nbsp;
                <input type="text" name="src_cat" id="src_cat" value="<?php echo $src_cat; ?>" />
                <input type="submit" value="<?php echo BOSS_SEARCH; ?>" class="button">
                <span class="gray"><?php echo BOSS_FIELD_PUBLISHED; ?></span>&nbsp;
                <select name="select_publish" id="select_publish" onchange="document.adminForm.submit();">
		    		<option value="0"><?php echo BOSS_ALL; ?></option>
		    	    <?php self::selectPublish('select_publish', 'only_pub'); ?>
		    	</select>
            </div>
            <br clear="all"/>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <th class="title" width="2%"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo $nb; ?>);"/></th>
                    <th class="title" width="2%">Id</th>
                    <th class="title" width="30%"><?php echo BOSS_CONTENT_TYPES;?></th>                
                    <th width="3%" colspan="2"><?php echo BOSS_ORDER; ?></th>
                    <th width="3%">
                        <a href="javascript: saveorder(<?php echo $nb - 1; ?>)">
                            <img src="/administrator/images/filesave.png" border="0" width="16" height="16"/>
                        </a>
                    </th>
                    <th class="title" width="10%"><?php echo BOSS_TH_PUBLISH;?></th>
                </tr>
            <?php
            $num =0;
             $total = count($types);
            foreach($types as $type){
                ?>
                <tr class="row<?php echo ($num & 1); ?>">
                    <td><input type="checkbox" id="cb<?php echo $num;?>" name="tid[]" value="<?php echo $type->id; ?>"
                               onclick="isChecked(this.checked);"/></td>

                    <td align="right"><?php echo $type->id; ?></td>
                    <td>
                    <?php HTML_boss::displayLinkText($type->name, "index2.php?option=com_boss&directory=$directory&act=content_types&task=edit&tid[]=" . $type->id); ?>
                    </td>

                    <td align="right">
                    <?php echo $pageNav->orderUpIcon($num, ($num > 0)); ?>
                    </td>
                    <td align="left">
                    <?php echo $pageNav->orderDownIcon($num, $nb, ($num < $total - 1)); ?>
                    </td>
                    <td align="center">
                        <input type="text" name="order[]" size="5" value="<?php echo $type->ordering; ?>"
                               class="text_area" style="text-align: center"/>
                    </td>
                    <td class="td-state" align="center" onclick="boss_publ('img-pub-<?php echo $type->id; ?>', '<?php echo "act=content_types&task=publish&tid=" . $type->id . "&directory=$directory"; ?>');">
                    <?php HTML_boss::displayYesNoImg($type->published, "img-pub-" . $type->id); ?>
                    </td>
                  
                    
                </tr>
            <?php
            $num ++;
            }
            ?>
            </table>

            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="act" value="content_types"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
        <?php

    }
    
    public static function displayContentTypes($row, $directory, $directories, $conf) {
        ?>
        <script type="text/javascript">
            function submitbutton(pressbutton) {
            <?php getEditorContents('editor1', 'description'); ?>
                submitform(pressbutton);
            }
        </script>

        <?php HTML_boss::header(BOSS_CONTENT_TYPES_EDIT, $directory, $directories, $conf); ?>
        <form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm"
              enctype="multipart/form-data">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_TH_PUBLISH; ?></td>
                    <td>
                        <select name="published" id="published">
                            <option value="1" <?php if ($row->published == 1) {
                                echo "selected";
                            } ?>><?php echo BOSS_PUBLISH; ?></option>
                            <option value="0" <?php if ($row->published == 0) {
                                echo "selected";
                            } ?>><?php echo BOSS_NO_PUBLISH ?></option>
                        </select>
                    </td>
					<td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TH_TITLE; ?></td>
                    <td>
			<input type="text" size="50" maxlength="100" name="name" value="<?php echo @$row->name; ?>"/>
                    </td>
					<td>&nbsp;</td>
                </tr>
                <tr>
                    <td><?php echo BOSS_TH_DESCRIPTION; ?></td>
                    <td>
                        <textarea name="desc" cols="50" rows="5"><?php echo @$row->desc; ?></textarea>
                    </td>
					<td>&nbsp;</td>
                </tr>
            </table>
            <input type="hidden" name="id" value="<?php echo @$row->id; ?>"/>
            <input type="hidden" name="ordering" value="<?php echo @$row->ordering; ?>"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="act" value="content_types"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php
    }
    
    public static function contentTypesSelect($rows, $selectedTips=null) {
        
        if (@$rows) {  
            $selectedTips = explode(',', $selectedTips);
            foreach ($rows as $row) {               
                $selected = '';
                
                if (is_array($selectedTips) && @in_array($row->id, $selectedTips))
                    $selected = 'selected';
           
                echo "<option value='" . $row->id . "' " . $selected . ">" . $row->name . "</option>";
            }
        }
    }
}
?>