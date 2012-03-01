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

    public static function header($text, $directory, $directories) {

        $params = self::getLayout();
        $layout = $params['layout'];
        $act = $params['act'];
        $task = mosGetParam($_REQUEST, 'task', "");

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
    <?php if(empty($task)) { ?>
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
            <?php if ($layout == 'edit' || $layout == 'full') : ?>
		<li><a <?php echo $class_categories; ?> href="index2.php?option=com_boss&act=categories&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_CATEGORIES;?></a></li>
		<li><a <?php echo $class_contents; ?> href="index2.php?option=com_boss&act=contents&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_CONTENTS;?></a></li>
            <?php endif; ?>
            <?php if ($layout == 'manage' || $layout == 'full') : ?>
                <li><a <?php echo $class_manager; ?> href="index2.php?option=com_boss&act=manager&directory=<?php echo $directory; ?>"><?php echo BOSS_CATALOGS; ?></a></li>
		<li><a <?php echo $class_configuration; ?> href="index2.php?option=com_boss&act=configuration&task=edit&directory=<?php echo $directory; ?>"><?php echo BOSS_CONFIGURATION; ?></a></li>
		<li><a <?php echo $class_templates; ?> href="index2.php?option=com_boss&act=templates&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_TEMPLATES;?></a></li>
		<li><a <?php echo $class_fields; ?> href="index2.php?option=com_boss&act=fields&directory=<?php echo $directory; ?>"><?php echo BOSS_FIELDS; ?></a></li>
		<li><a <?php echo $class_fieldimage; ?> href="index2.php?option=com_boss&act=fieldimage&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_FIELDIMAGES;?></a></li>
		<li><a <?php echo $class_plugins; ?> href="index2.php?option=com_boss&act=plugins&directory=<?php echo $directory; ?>"><?php echo BOSS_LIST_PLUGINS;?></a></li>
		<li><a <?php echo $class_export_import; ?> href="index2.php?option=com_boss&act=export_import&directory=<?php echo $directory; ?>"><?php echo BOSS_EX_IM_HEADER;?></a></li>
                <li><a <?php echo $class_users; ?> href="index2.php?option=com_boss&act=users&directory=<?php echo $directory; ?>"><?php echo BOSS_TH_USERS;?></a></li>
            <?php endif; ?>
    </ul>
<?php } else { ?>
	<ul id="boss-menu" class="inactive">
            <?php if ($layout == 'edit' || $layout == 'full') : ?>
		<li><?php echo BOSS_LIST_CATEGORIES;?></li>
		<li><?php echo BOSS_LIST_CONTENTS;?></li>
            <?php endif; ?>
            <?php if ($layout == 'manage' || $layout == 'full') : ?>
		<li><?php echo BOSS_CATALOGS; ?></li>
		<li><?php echo BOSS_CONFIGURATION; ?></li>
		<li><?php echo BOSS_LIST_TEMPLATES;?></li>
		<li><?php echo BOSS_FIELDS; ?></li>
		<li><?php echo BOSS_LIST_FIELDIMAGES;?></li>
		<li><?php echo BOSS_LIST_PLUGINS;?></li>
		<li><?php echo BOSS_EX_IM_HEADER;?></li>
                <li><?php echo BOSS_TH_USERS;?></li>
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
if ($act!="contents" && $act!="plugins" && $act!="categories") echo '<br clear="all"/>';
if ($act=="categories" && $task == 'edit') echo '<br clear="all"/>';
?>


<?php

    }

    public static function displayMain($directory, $directories) {
        HTML_boss::header(BOSS_MAIN_PAGE, $directory, $directories);

    }

    public static function displayTools($directory, $directories) {
        ?>
        <?php HTML_boss::header(BOSS_TOOLS_MAIN_PAGE, $directory, $directories); ?>

        <ul>
            <li>
                <a href="index2.php?option=com_boss&act=tools&task=displayMarketplace&directory=<?php echo $directory; ?>"><?php echo BOSS_CONVERT_MARKETPLACE;?></a>
            </li>
            <li>
                <a href="index2.php?option=com_boss&act=tools&task=installjoomfish&directory=<?php echo $directory; ?>"><?php echo BOSS_INSTALL_JOOMFISH;?></a>
            </li>
            <li>
                <a href="index2.php?option=com_boss&act=tools&task=installsef&directory=<?php echo $directory; ?>"><?php echo BOSS_INSTALL_SEF;?></a>
            </li>
        </ul>
		<?php

    }

    public static function recurseMarketplaceCategories($id, $level, $children, $num) {
        if (@$children[$id]) {
            foreach ($children[$id] as $row) {
                ?>
<tr class="row<?php echo ($num & 1); ?>">

	<td><?php echo $row->id; ?></td>
                <?php
                                    $text = "";
                for ($i = 1; $i < $level; $i++)
                    $text .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                if ($level > 0)
                    $text .= "&nbsp;&nbsp;&nbsp;&nbsp;<sup>L</sup>&nbsp;";
                $text .= $row->name;
                ?>
                    <td>
                    <?php echo $text; ?>
                    </td>
                <?php
                                    $num++;
                $num = HTML_boss::recurseMarketplaceCategories($row->id, $level + 1, $children, $num);
            }
        }
        return $num;
    }


    function displayConvertMarketplace($contents, $cats, $directory, $directories) {
        ?>
        <?php HTML_boss::header(BOSS_CONVERT_MARKETPLACE, $directory, $directories); ?>

        <h3><?php echo BOSS_TOOLS_MARKETPLACE_CATEGORIES; ?></h3>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
    <tr>
    <th class="title" width="2%">Id</th>
    <th class="title" width="30%"><?php echo BOSS_TH_CATEGORY;?></th>
<?php
                HTML_boss::recurseMarketplaceCategories(0, 0, $cats, 0);
?>
</table>
<h3><?php echo BOSS_TOOLS_MARKETPLACE_CONTENTS; ?></h3>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
<tr>
    <th class="title" width="2%">Id</th>
    <th class="title" width="30%"><?php echo BOSS_TH_TITLE;?></th>
    <th class="title" width="30%"><?php echo BOSS_TH_CATEGORY;?></th>
<?php
if (isset($contents)) {
    foreach ($contents as $content) {
        ?>
            <tr>
                <td align="right"><?php echo $content->id; ?></td>
                <td><?php echo $content->name; ?></td>
                <td><?php echo $content->cat; ?></td>
            </tr>
        <?php

    }
}
?>
</table>
<br/>
<h3>
    <a href="index2.php?option=com_boss&act=tools&task=importMarketplace&directory=<?php echo $directory; ?>"><?php echo BOSS_IMPORT_MARKETPLACE;?></a>
</h3>
		<?php

    }

    public static function check_dir($directory, $directories = array()) {
        if ($directory == 0) {
            HTML_boss::header(BOSS_CONFIGURATION_PANEL, $directory, $directories);
            print "<h3>" . BOSS_NEED_CREATE . "</h3>";
            return false;
        }
        else
            return true;
    }

    public static function editConfiguration($row, $templates, $directory, $directories, $sort_fields, $filters) {
        global $mosConfig_live_site;
        HTML_boss::header(BOSS_CONFIGURATION_PANEL, $directory, $directories);
        ?>
        <script language='JavaScript1.2' type='text/javascript'>
            function submitbutton(pressbutton) {
                <?php getEditorContents('editor1', 'fronttext'); ?>
                <?php getEditorContents('editor2', 'rules_text'); ?>
                submitform(pressbutton);
            }

            function showimage() {
                //if (!document.images) return;
                document.images.preview.src = '<?php echo $mosConfig_live_site;?>/templates/com_boss/' + getSelectedValue('adminForm', 'template') + '/template_thumbnail.png';
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
            $configtabs = new mosTabs(0,1);
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
					<img class="ml10" src="<?php echo "$mosConfig_live_site/templates/com_boss/" . $tmpl_tmp . "/template_thumbnail.png";?>"
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
                    <td><?php echo BOSS_NB_IMAGES; ?></td>
                    <td><input type="text" name="nb_images" value="<?php echo @$row->nb_images; ?>"/></td>
                    <td><?php echo BOSS_NB_IMAGES_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_IMAGE_SIZE;?></td>
                    <td><input type="text" name="max_image_size" value="<?php echo @$row->max_image_size; ?>"/></td>
                    <td><?php echo BOSS_MAX_IMAGE_SIZE_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_IMAGE_WIDTH;?></td>
                    <td><input type="text" name="max_width" value="<?php echo @$row->max_width; ?>"/></td>
                    <td><?php echo BOSS_MAX_IMAGE_WIDTH_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_IMAGE_HEIGHT;?></td>
                    <td><input type="text" name="max_height" value="<?php echo @$row->max_height; ?>"/></td>
                    <td><?php echo BOSS_MAX_IMAGE_HEIGHT_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_THUMBNAIL_WIDTH;?></td>
                    <td><input type="text" name="max_width_t" value="<?php echo @$row->max_width_t; ?>"/></td>
                    <td><?php echo BOSS_MAX_THUMBNAIL_WIDTH_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_MAX_THUMBNAIL_HEIGHT;?></td>
                    <td><input type="text" name="max_height_t" value="<?php echo @$row->max_height_t; ?>"/></td>
                    <td><?php echo BOSS_MAX_THUMBNAIL_HEIGHT_LONG;?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_IMAGE_TAG; ?></td>
                    <td><input type="text" name="tag" value="<?php echo @$row->tag; ?>"/></td>
                    <td><?php echo BOSS_IMAGE_TAG_LONG; ?></td>
                </tr>
                <tr>
                    <td><?php echo BOSS_IMAGE_DISPLAY; ?></td>
                    <td>
                        <select id='image_display' name='image_display'>
                            <option value='default' <?php if (@$row->image_display == 'default') {
                                echo "selected";
                            } ?>><?php echo BOSS_IMAGE_DISPLAY_DEFAULT; ?></option>
                            <option value='fancybox' <?php if (@$row->image_display == 'fancybox') {
                                echo "selected";
                            } ?>><?php echo BOSS_IMAGE_DISPLAY_FANCY; ?></option>
                            <option value='popup' <?php if (@$row->image_display == 'popup') {
                                echo "selected";
                            } ?>><?php echo BOSS_IMAGE_DISPLAY_POPUP; ?></option>
                            <option value='gallery' <?php if (@$row->image_display == 'gallery') {
                                echo "selected";
                            } ?>><?php echo BOSS_IMAGE_DISPLAY_GALLERY; ?></option>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
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
            $configtabs->startTab(BOSS_TAB_TEXT, "text-page");
            ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <td><?php echo BOSS_FRONTPAGE; ?></td>
                    <td><?php editorArea('editor1', @$row->fronttext, 'fronttext', '100%;', '350', '75', '20'); ?></td>
                    <td><?php echo BOSS_FRONTPAGE_LONG; ?></td>
                </tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
                <tr>
                    <td><?php echo BOSS_RULES; ?></td>
                    <td><?php editorArea('editor2', @$row->rules_text, 'rules_text', '100%;', '350', '75', '20'); ?></td>
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
            $configtabs->startTab(BOSS_TAB_EXPIRATION, "Expiration-page");
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
                            <td><?php editorArea('editor3', "$recall_text", 'recall_text', '100%;', '350', '75', '20'); ?></td>
                            <td>&nbsp;</td>
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
        global $mosConfig_live_site;
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
                        echo '<img src="' . $mosConfig_live_site . '/images/boss/' . $directory . '/categories/' . $row->id . 'cat_t.jpg"/>';
                    }
                    else {
                        echo '<img src="' . $mosConfig_live_site . '/templates/com_boss/' . $template_name . '/images/default.gif"/>';
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

    public static function listContents($cat, $rows, $pagenav, $cats, $directory, $directories, $categs, $autors, $selectedAutorId) {

		$certain_category = (!empty($cat->name)) ? '<span class="gray">&nbsp;('.$cat->name.')</span>' : '';
		HTML_boss::header(BOSS_CONTENTS . $certain_category, $directory, $directories); ?>
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
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <th width="5"><input type="checkbox" name="toggle" value=""
                                         onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th width="5">Id</th>
                    <th class="title" width="60%"><?php echo BOSS_TH_TITLE;?></th>
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

    public static function displayContent($row, $fields, $field_values, $cats, $users, $nb_images, $directory, $directories, $selected_categ, $tags, $id) {

        HTML_boss::header(BOSS_CONTENT_EDITION, $directory, $directories);
        $plugins = get_plugins($directory, 'fields');
        $tabs = new mosTabs(0,1);
        mosCommonHTML::loadCalendar();
        ?>

    <script type="text/javascript"><!--//--><![CDATA[//><!--
        function submitbutton(pressbutton) {
            if(pressbutton == 'cancel'){
                submitform(pressbutton);
		        return true;
            }
        var me = document.getElementById('name');
        if (me.value == '') {
		    me.style.background = "red";
		    alert("<?php echo html_entity_decode(addslashes( BOSS_REGWARN_ERROR),ENT_QUOTES); ?> : <?php echo html_entity_decode(addslashes(BOSS_TH_TITLE),ENT_QUOTES); ?>");
		}
        else {
            <?php
                foreach($fields as $field) {
                    if($field->type == 'editor')
                    getEditorContents('editor', $field->name);
                }
            ?>
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
    if($nb_images>0){
        $tabs->startTab(BOSS_FORM_CONTENT_PICTURES, "img-page");
        ?>
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
            <?php
				for ($i = 1; $i < $nb_images + 1; $i++) {
                $ext_name = chr(ord('a') + $i - 1);
                ?>
                    <tr>
                        <td><?php echo BOSS_FORM_CONTENT_PICTURE . " " . $i; ?></td>
                        <td>
                            <input type="file" name="content_picture<?php echo $i;?>"/>
                            <br/>
                        <?php
                        $pic = JPATH_BASE . "/images/boss/$directory/contents/" . $row->id . $ext_name . "_t.jpg";
                        if (file_exists($pic)) {
                            echo '<img src="/images/boss/' . $directory . '/contents/' . $row->id . $ext_name . '_t.jpg"/>';
                            echo "<input type='checkbox' name='cb_image$i' value='delete'>" . BOSS_CONTENT_DELETE_IMAGE;
                        }
                        ?>
                            <br/>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                <?php
            }
            ?>
            </table>
        <?php
	$tabs->endTab();
    }
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
            <input type="hidden" name="date_created"
                   value="<?php echo isset($row->date_created) ? $row->date_created : date("Y-m-d"); ?>"/>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
            <input type="hidden" name="act" value="contents"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php

    }

    public static function listcategories($nb, $children, $pageNav, $directory, $directories, $defaultTemplate) {

        HTML_boss::header(BOSS_LIST_CATEGORIES, $directory, $directories);
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

    public static function displayCategory($row, $cats, $directory, $directories, $templates) {
        global $mosConfig_live_site;
        ?>
        <script type="text/javascript">
            function submitbutton(pressbutton) {
            <?php getEditorContents('editor1', 'description'); ?>
                submitform(pressbutton);
            }

            function showimage() {
                //if (!document.images) return;
                document.images.preview.src = '<?php echo $mosConfig_live_site;?>/templates/com_boss/' + getSelectedValue('adminForm', 'template') + '/template_thumbnail.png';
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

        <?php HTML_boss::header(BOSS_CATEGORY_EDITION, $directory, $directories); ?>
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
						<img class="ml10" src="<?php echo "$mosConfig_live_site/templates/com_boss/" . $tmpl_tmp . "/template_thumbnail.png";?>"
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

        if (isset($fields)) {
            foreach ($fields as $field) {
                $return = jDirectoryField::getFieldForm($field, $row, null, $field_values, $directory, $plugins, "write");
                echo "<tr><td>" . $return->title . "</td>\n";
                echo "<td>" . $return->input . "</td><td>&nbsp;</td></tr>";
            }
        }
    }

    public static function showFields(&$rows, $pageNav, $directory, $directories) {
        ?>
        <?php HTML_boss::header(BOSS_FIELDS_LIST, $directory, $directories); ?>
        <script type="text/javascript">
            function cbsaveorder(n) {
                cbcheckAll_button(n);
                submitform('savefieldorder');
            }

            //needed by sbsaveorder function
            function cbcheckAll_button(n) {
                for (var j = 0; j <= n; j++) {
                    box = eval("document.adminForm.cb" + j);
                    if (box.checked == false) {
                        box.checked = true;
                    }
                }
            }
        </script>
        <form action="index2.php" method="post" name="adminForm">
            <table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
                <tr>
                    <th width="1%" class="title acenter">#</th>
                    <th width="1%" class="title"><input type="checkbox" name="toggle" value=""
                                                        onClick="checkAll(<?php echo count($rows); ?>);"/>
                    </th>
                    <th width="10%" class="title"><?php echo BOSS_TH_NAME;?></th>
                    <th width="10%" class="title"><?php echo BOSS_TH_TITLE; ?></th>
                    <th width="10%" class="title"><?php echo BOSS_TH_TYPE; ?></th>
                    <th width="5%" class="title acenter"><?php echo BOSS_TH_REQUIRED;?></th>
                    <th width="5%" class="title acenter"><?php echo BOSS_TH_PUBLISH;?></th>
                    <th width="1%" class="title" colspan="2"><?php echo BOSS_ORDER; ?></th>
                    <th width="1%"><a href="javascript: saveorder(<?php echo count($rows) - 1; ?>)"><img
                            src="/administrator/images/filesave.png" border="0" width="16" height="16"/></a></th>
                </tr>
            <?php
                    $k = 0;
            for ($i = 0, $n = count($rows); $i < $n; $i++) {
                $row =& $rows[$i];
                ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td align="right"><?php echo $i + 1?></td>
                        <td><input type="checkbox" id="cb<?php echo $i;?>" name="tid[]"
                                   value="<?php echo $row->fieldid; ?>" onClick="isChecked(this.checked);"/></td>
                        <td>
                            <a href="index2.php?option=com_boss&act=fields&task=edit&tid=<?php echo $row->fieldid; ?>&directory=<?php echo $directory;?>">
                            <?php echo $row->name; ?> </a></td>
                    <?php $row->title = jdGetLangDefinition($row->title);?>
                        <td><?php echo $row->title; ?></td>
                        <td><?php echo $row->type; ?></td>
                        <td width="10%" class="td-state" align="center" onclick="boss_publ('img-req-<?php echo $row->fieldid; ?>', '<?php echo "act=fields&task=required&tid=" . $row->fieldid . "&directory=$directory"; ?>');">
                        <?php HTML_boss::displayYesNoImg($row->required, "img-req-" . $row->fieldid); ?>
                        </td>
                        <td width="10%" class="td-state" align="center" onclick="boss_publ('img-pub-<?php echo $row->fieldid; ?>', '<?php echo "act=fields&task=publish&tid=" . $row->fieldid . "&directory=$directory"; ?>');">
                        <?php HTML_boss::displayYesNoImg($row->published, "img-pub-" . $row->fieldid); ?>
                        </td>
                        <td><?php echo $pageNav->orderUpIcon($i); ?></td>
                        <td><?php echo $pageNav->orderDownIcon($i, $n); ?></td>
                        <td align="center">
                            <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"
                                   class="text_area" style="text-align: center"/>
                        </td>
                    </tr>
                <?php $k = 1 - $k;
            } ?>
            </table>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="task" value="showField"/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="act" value="fields"/>
        </form>
        <?php

    }

    public static function editfield(&$row, $lists, $fieldvalues, $tabid, $cats, $nbcats, $fieldimages, $directory, $directories) {
        global $mosConfig_live_site;
        $task = mosGetParam($_REQUEST, 'task', '');
        HTML_boss::header(BOSS_EDIT_FIELD, $directory, $directories);
        $mainframe = mosMainFrame::getInstance(true);
        mosCommonHTML::loadJquery();
        mosCommonHTML::loadOverlib();
        $mainframe->addJS(JPATH_SITE . '/administrator/components/com_boss/js/upload.js');
        //$mainframe->addJS($mosConfig_live_site.'/administrator/components/com_boss/js/function.js');
        $plugins = get_plugins($directory, 'fields');
        ?>
        <script type="text/javascript">
        function getObject(obj) {
            var strObj;
            if (document.all) {
                strObj = document.all.item(obj);
            } else if (document.getElementById) {
                strObj = document.getElementById(obj);
            }
            return strObj;
        }

        function submitbutton(pressbutton) {
            if (pressbutton == 'showField' || pressbutton == 'cancel') {
                document.adminForm.type.disabled = false;
                submitform(pressbutton);
                return;
            }
            var coll = document.adminForm;
            var errorMSG = '';
            var iserror = 0;
            if (coll != null) {
                var elements = coll.elements;
                // loop through all input elements in form
                for (var i = 0; i < elements.length; i++) {
                    // check if element is mandatory; here mosReq=1
                    if (elements.item(i).getAttribute('mosReq') == 1) {
                        if (elements.item(i).value == '') {
                            //alert(elements.item(i).getAttribute('mosLabel') + ':' + elements.item(i).getAttribute('mosReq'));
                            // add up all error messages
                            errorMSG += elements.item(i).getAttribute('mosLabel') + ' : <?php echo BOSS_REGWARN_ERROR; ?>\n';
                            // notify user by changing background color, in this case to red
                            elements.item(i).style.background = "red";
                            iserror = 1;
                        }
                    }
                    else if (elements.item(i).getAttribute('mosReq') == 2) {
                        if (elements.item(i).value == 'null') {
                            //alert(elements.item(i).getAttribute('mosLabel') + ':' + elements.item(i).getAttribute('mosReq'));
                            // add up all error messages
                            errorMSG += elements.item(i).getAttribute('mosLabel') + ' : <?php echo BOSS_REGWARN_ERROR; ?>\n';
                            // notify user by changing background color, in this case to red
                            elements.item(i).style.background = "red";
                            iserror = 1;
                        }
                    }
                }
            }
            if (iserror == 1) {
                alert(errorMSG);
            } else {
                document.adminForm.type.disabled = false;
                submitform(pressbutton);
            }
        }

        function insertRow() {
            var oTable = getObject("fieldValuesBody");
            var oRow, oCell, oInput;
            var oCell2, oInput2;
            var i;
            i = document.adminForm.valueCount.value;
            i++;
            // Create and insert rows and cells into the first body.
            oRow = document.createElement("TR");
            oTable.appendChild(oRow);

            oCell = document.createElement("TD");
            oInput = document.createElement("INPUT");
            oInput.name = "vNames[" + i + "]";
            oInput.setAttribute('mosLabel', 'Name');
            oInput.setAttribute('mosReq', 0);
            oCell.appendChild(oInput);
            oCell2 = document.createElement("TD");
            oInput2 = document.createElement("INPUT");
            oInput2.name = "vValues[" + i + "]";
            oInput2.setAttribute('mosLabel', 'Name');
            oInput2.setAttribute('mosReq', 0);
            oCell2.appendChild(oInput2);

            oRow.appendChild(oCell);
            oRow.appendChild(oCell2);
            oInput.focus();

            document.adminForm.valueCount.value = i;
        }

        function insertImageRow() {
            var oTable = getObject("ImagesfieldValuesBody");
            var oRow, oCell;
            var oCell2, oInput2,oImage,oSelect;
            var i, k;
            i = document.adminForm.ImagevalueCount.value;
            i++;
            // Create and insert rows and cells into the first body.
            oRow = document.createElement("tr");
            oTable.appendChild(oRow);

            oCell = document.createElement("td");
            oSelect = document.createElement("select");
            oSelect.onchange = function() {
                showimage('preview' + i, this); //Gestion de la particularitпїЅ d'ie qui n'accepte pas d'ajouter un evement avec setAttribute. ie ignore la ligne au dessus, ff ignore cette ligne
            };
            oSelect.id = 'vSelectImages[' + i + ']';
            oSelect.name = 'vSelectImages[' + i + ']';
            oSelect.setAttribute('class', "img_select");
            k = 0;
            oSelect.length++;
            oSelect.options[0].text = 'No Image';
            oSelect.options[0].value = 'null';
        <?php
                if (isset($fieldimages)) {
            foreach ($fieldimages as $image) {
                ?>
						k++;
						oSelect.length++;
						oSelect.options[k].text = '<?php echo $image; ?>';
						oSelect.options[k].value = '<?php echo $image; ?>';
				<?php

            }
        }
        ?>
            oCell.appendChild(oSelect);
            oImage = document.createElement("img");
            oImage.setAttribute('src', "<?php echo $mosConfig_live_site . '/images/boss/' . $directory . '/fields/' . $row->link_image; ?>");
            oImage.setAttribute('id', "preview" + i);
            oImage.setAttribute('name', "preview" + i);
            oCell.appendChild(oImage);
            oCell2 = document.createElement("td");
            oInput2 = document.createElement("input");
            oInput2.name = "vImagesValues[" + i + "]";
            oInput2.setAttribute('mosLabel', 'Value');
            oInput2.setAttribute('mosReq', 0);
            oCell2.appendChild(oInput2);

            oRow.appendChild(oCell);
            oRow.appendChild(oCell2);
            oSelect.focus();

            document.adminForm.ImagevalueCount.value = i;
        }

        function disableAll() {
            var elem;
            elem = getObject('divValues');
            elem.style.visibility = 'hidden';
            elem.style.display = 'none';
            elem = getObject('divImagesValues');
            elem.style.visibility = 'hidden';
            elem.style.display = 'none';
            elem = getObject('divColsRows');
            elem.style.visibility = 'hidden';
            elem.style.display = 'none';
            elem = getObject('divTextLength');
            elem.style.visibility = 'hidden';
            elem.style.display = 'none';
            if (elem = getObject('vNames[0]')) {
                elem.setAttribute('mosReq', 0);
            }
            if (elem = getObject('vValues[0]')) {
                elem.setAttribute('mosReq', 0);
            }
            if (elem = getObject('vSelectImages[0]')) {
                elem.setAttribute('mosReq', 0);
            }
            if (elem = getObject('vImagesValues[0]')) {
                elem.setAttribute('mosReq', 0);
            }
            elem = getObject('divLink');
            elem.style.visibility = 'hidden';
            elem.style.display = 'none';

        <?php
         foreach ($plugins as $key => $plug) {
            echo $plug->getEditFieldJavaScriptDisable() . "\n";
        }
        ?>
        }

        function selType(sType) {
            var elem;
            //alert(sType);
            switch (sType) {
                    <?php
                    foreach ($plugins as $key => $plug) {
                        echo "case '$key':\n";
                        echo $plug->getEditFieldJavaScriptActive() . "\n";
                        echo "break\n";
                    }
                    ?>
                default:
                    disableAll();
            }
        }

        function prep4SQL(o) {
            if (o.value != '') {
                o.value = o.value.replace('content_', '');
                o.value = 'content_' + o.value.replace(/[^a-zA-Z]+/g, '');
            }
        }

        function showimage(preview, obj) {
            if (getSelectedValue(obj) == 'null' || !getSelectedValue(obj))
                var imgPath = '<?php echo $mosConfig_live_site; ?>/templates/com_boss/default/images/nopic.gif';
            else
                imgPath = '<?php echo $mosConfig_live_site . "/images/boss/$directory";?>/fields/' + getSelectedValue(obj);
            var img = getObject(preview);
            img.src = imgPath;
        }

        function getSelectedValue(obj) {
            var i = obj.selectedIndex;
            if (i != null && i > -1) {
                return obj.options[i].value;
            } else {
                return null;
            }
        }
        </script>

        <form action="index2.php?option=com_boss" method="POST" name="adminForm">
        <table cellspacing="0" cellpadding="0" width="100%">
        <tr valign="top">
        <td width="50%">
        <table class="adminform">
            <th colspan="3">
            <?php echo BOSS_PARAMS;?>
            </th>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_TYPE;?></td>
                <td width="20%"><?php echo $lists['type']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_NAME;?></td>
                <td align=left width="20%"><input onchange="prep4SQL(this);" type="text" name="name" mosReq=1
                                                  mosLabel="Name" class="inputbox" value="<?php echo $row->name; ?>"/>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_TITLE;?></td>
                <td width="20%" align=left><input type="text" name="title" mosReq=1 mosLabel="Title" class="inputbox"
                                                  value="<?php echo $row->title; ?>"/></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_DISPLAY_TITLE;?></td>
                <td width="20%"><?php echo $lists['display_title']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_DESCRIPTION;?></td>
                <td width="20%" align=left><input type="text" name="description" mosLabel="Description" size="40"
                                                  value="<?php echo $row->description; ?>"/></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_TEXT_BEFORE;?></td>
                <td width="20%" align=left><input type="text" name="text_before" mosLabel="TextBefore" size="40"
                                                  value="<?php echo $row->text_before; ?>"/></td>
                <td><?php echo mosToolTip(BOSS_FIELD_TEXT_BEFORE_LONG);?></td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_TEXT_AFTER;?></td>
                <td width="20%" align=left><input type="text" name="text_after" mosLabel="TextAfter" size="40"
                                                  value="<?php echo $row->text_after; ?>"/></td>
                <td><?php echo mosToolTip(BOSS_FIELD_TEXT_AFTER_LONG);?></td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_TAGS_OPEN;?></td>
                <td width="20%" align=left><input type="text" name="tags_open" mosLabel="TagsOpen" size="40"
                                                  value="<?php echo $row->tags_open; ?>"/></td>
                <td><?php echo mosToolTip(BOSS_FIELD_TAGS_OPEN_LONG);?></td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_TAGS_SEPARATOR;?></td>
                <td width="20%" align=left><input type="text" name="tags_separator" mosLabel="TagsSeparator" size="40"
                                                  value="<?php echo $row->tags_separator; ?>"/></td>
                <td><?php echo mosToolTip(BOSS_FIELD_TAGS_SEPARATOR_LONG);?></td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_TAGS_CLOSE;?></td>
                <td width="20%" align=left><input type="text" name="tags_close" mosLabel="Description" size="40"
                                                  value="<?php echo $row->tags_close; ?>"/></td>
                <td><?php echo mosToolTip(BOSS_FIELD_TAGS_CLOSE_LONG);?></td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_REQUIRED;?></td>
                <td width="20%"><?php echo $lists['required']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_PUBLISHED;?></td>
                <td width="20%"><?php echo $lists['published']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_SEARCHABLE;?></td>
                <td width="20%"><?php echo $lists['searchable']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FILTER_ALLOW;?></td>
                <td width="20%"><?php echo $lists['filter']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_EDITABLE;?></td>
                <td width="20%"><?php echo $lists['editable']; ?></td>
                <td>&nbsp;</td>
            </tr>

            <tr>
                <td width="20%"><?php echo BOSS_FIELD_PROFILE;?></td>
                <td width="20%"><?php echo $lists['profile']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_SORT_OPTION;?></td>
                <td width="20%"><?php echo $lists['sort']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_SORT_DIRECTION;?></td>
                <td width="20%"><?php echo $lists['sort_direction']; ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="20%"><?php echo BOSS_FIELD_SIZE;?></td>
                <td width="20%"><input type="text" name="size" mosLabel="Size" class="inputbox"
                                       value="<?php echo $row->size; ?>"/></td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </td>
        <td width="50%">
            <table class="adminform">
                <th><?php echo BOSS_FORM_FIELD_CATEGORY; ?></th>
                <tr>
                    <td>
                        <select name="field_catsid[]" multiple='multiple' id="field_catsid[]"
                                size="<?php echo $nbcats + 2;?>">
                        <?php
                                if (strpos($row->catsid, ",-1,") === false)
                            echo "<option value='-1'>" . BOSS_MENU_ALL_CONTENTS . "</option>";
                        else
                            echo "<option value='-1' selected>" . BOSS_MENU_ALL_CONTENTS . "</option>";
                        HTML_boss::selectCategories(0, BOSS_ROOT . " >> ", $cats, -1, -1, 1, $row->catsid);
                        ?>
                        </select>
                    </td>
                </tr>
            </table>

            <div style="margin:20px; clear:both" > </div>
<?php if($task == 'new'){ ?>
            <table class="adminform">
                <th><?php echo BOSS_EMPTY_DIRS; ?></th>
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
            <div style="margin:20px; clear:both" > </div>
<?php } ?>
            <table class="adminform">
                <th><?php echo BOSS_FIELD_TYPE_PARAMS; ?></th>
                <tr>
                    <td>
        <div id="page1" class="pagetext">

        </div>

        <div id="divTextLength" class="pagetext">
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%"><?php echo BOSS_FIELD_MAX_LENGTH;?></td>
                <?php
                        if (!isset($row->maxlength) || ($row->maxlength == ""))
                    $row->maxlength = 20;
                ?>
                    <td width="20%"><input type="text" name="maxlength" mosLabel="Max Length" class="inputbox"
                                           value="<?php echo $row->maxlength; ?>"/></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <div id=divLink class="pagetext">
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%"><?php echo BOSS_LINK_TEXT;?></td>
                    <td width="20%"><input type="text" name="link_text" mosLabel="Link Text" class="inputbox"
                                           value="<?php echo $row->link_text; ?>"/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%"><?php echo BOSS_LINK_IMAGE;?></td>
                    <td width="20%">
                        <select id='link_image' mosLabel='Image' mosReq=0 name='link_image'
                                onchange="showimage('previewlink',this)">
                            <option value='null' selected="selected">No Image</option>
                        <?php
                                                                                if (isset($fieldimages)) {
                            foreach ($fieldimages as $image) {
                                ?>
                                    <option value='<?php echo $image; ?>' <?php if ($row->link_image == $image) {
                                        echo "selected";
                                    } ?>><?php echo $image; ?></option>
                                <?php

                            }
                        }
                        ?>
                        </select>

                    </td>
                    <td>
                        <img src="<?php echo "$mosConfig_live_site/images/boss/$directory/fields/" . $row->link_image; ?>"
                             id='previewlink' name="previewlink"/>
                    </td>
                </tr>
            </table>
        </div>
        <div id=divColsRows class="pagetext">
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
                <tr>
                    <td width="20%"><?php echo BOSS_FIELD_COLS;?></td>
                    <td width="20%"><input type="text" name="cols" mosLabel="Cols" class="inputbox"
                                           value="<?php echo $row->cols; ?>"/></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td width="20%"><?php echo BOSS_FIELD_ROWS;?></td>
                    <td width="20%"><input type="text" name="rows" mosLabel="Rows" class="inputbox"
                                           value="<?php echo $row->rows; ?>"/></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>

        <div id=divValues style="text-align:left;">
        <?php echo BOSS_FIELD_VALUES_EXPLANATION;?>
            <input type=button onclick="insertRow();" value="Add a Value"/>
            <table align=left id="divFieldValues" cellpadding="4" cellspacing="1" border="0" width="100%"
                   class="adminform">
                <tr>
                    <th width="20%"><?php echo BOSS_FIELD_VALUE_NAME;?></th>
                    <th width="20%"><?php echo BOSS_FIELD_VALUE_VALUE;?></th>
                </tr>
                <tbody id="fieldValuesBody">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php
                for ($i = 0, $n = count($fieldvalues); $i < $n; $i++) {
                    $fieldvalue = $fieldvalues[$i];
                    echo "<tr>\n<td width=\"20%\"><input type=text mosReq=0  mosLabel='Name' value='" . stripslashes($fieldvalue->fieldtitle) . "' id='vNames[$i]' name='vNames[$i]' /></td>\n<td width=\"20%\"><input type=text mosReq=0 mosLabel='Value' value='" . stripslashes($fieldvalue->fieldvalue) . "' id='vValues[$i]' name='vValues[$i]' /></td>\n</tr>\n";
                }
                if ($i > 0)
                    $i--;
                if (count($fieldvalues) < 1) {
                    echo "<tr>\n<td width=\"20%\"><input type=text mosReq=0  mosLabel='Name' value='' id='vNames[0]' name='vNames[0]' /></td>\n<td width=\"20%\"><input type=text mosReq=0  mosLabel='Value' value='' name='vValues[0]' id='vValues[0]' /></td>\n</tr>\n";
                    $i = 0;
                }
                ?>
                </tbody>
            </table>
        </div>

        <div id=divImagesValues style="text-align:left;">
        <?php echo BOSS_IMAGE_FIELD_VALUES_EXPLANATION;?>
            <input type=button onclick="insertImageRow();" value="<?php echo BOSS_FIELD_ADD_VALUES;?>"/>
            <input id="upload" type=button value="<?php echo BOSS_FIELD_UPLOAD_FILE;?>"/>
            <table align=left id="divImagesFieldValues" cellpadding="4" cellspacing="1" border="0" width="100%"
                   class="adminform">
                <tr>
                    <th width="20%"><?php echo BOSS_FIELD_VALUE_IMAGE;?></th>
                    <th width="20%"><?php echo BOSS_FIELD_VALUE_VALUE;?></th>
                </tr>
                <tbody id="ImagesfieldValuesBody">
                <tr>
                    <td colspan="2">
                        <div id="files" style="text-align:center;"></div>
                    </td>
                </tr>
                <?php
                for ($j = 0, $n = count($fieldvalues); $j < $n; $j++) {
                    $fieldvalue = $fieldvalues[$j];
                    ?>
                    <tr>
                        <td width="20%">
                            <select class='img_select' id='vSelectImages[<?php echo $j; ?>]' mosLabel='Image' mosReq=0
                                    name='vSelectImages[<?php echo $j; ?>]'
                                    onchange="showimage('preview<?php echo $j; ?>',this)">
                                <option value='null' selected="selected">No Image</option>
                            <?php
                            if (isset($fieldimages)) {
                                foreach ($fieldimages as $image) {
                                    ?>
                                        <option value='<?php echo $image; ?>' <?php if (stripslashes($fieldvalue->fieldtitle) == $image) {
                                            echo "selected";
                                        } ?>><?php echo $image; ?></option>
                                    <?php

                                }
                            }
                            ?>
                            </select>
                            <img src="<?php echo "$mosConfig_live_site/images/boss/$directory/fields/" . stripslashes($fieldvalue->fieldtitle); ?>"
                                 id='preview<?php echo $j; ?>' name="preview<?php echo $j; ?>"
                                 alt="<?php echo @$row->image;?>"/>
                        </td>
                        <td width="20%">
                            <input type=text mosReq=0 mosLabel='Value'
                                   value='<?php echo stripslashes($fieldvalue->fieldvalue); ?>'
                                   name='vImagesValues[<?php echo $j; ?>]' id='vImagesValues[<?php echo $j; ?>]'/>
                        </td>
                    </tr>
                    <?php

                }
                if ($j > 0)
                    $j--;
                if (count($fieldvalues) < 1) {
                    ?>
                    <tr>
                        <td width="20%">
                            <select class='img_select' id='vSelectImages[0]' name='vSelectImages[0]' mosReq=0
                                    mosLabel='Image'
                                    onchange="showimage('preview0',this)">
                                <option value='null' selected="selected">No Image</option>
                            <?php
                                                                                            if (isset($fieldimages)) {
                                foreach ($fieldimages as $image) {
                                    ?>
                                        <option value='<?php echo $image; ?>' <?php if ($row->link_image == $image) {
                                            echo "selected";
                                        } ?>><?php echo $image; ?></option>
                                    <?php

                                }
                            }
                            ?>
                            </select>
                            <img src="" id='preview0' name="preview0" alt="<?php echo $row->link_image;?>"/>
                        </td>
                        <td width="20%">
                            <input type=text mosReq=0 mosLabel='Value' value='' name='vImagesValues[0]'
                                   id='vImagesValues[0]'/>
                        </td>
                    </tr>
                    <?php
                                                    $j = 0;
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
        foreach ($plugins as $key => $plug) {
            echo $plug->getEditFieldOptions($row->fieldid, $directory);
        }
        ?>
                    </td>
                </tr>
            </table>
            </td>
        </tr>
        </table>
        <input type="hidden" name="valueCount" value="<?php echo $i; ?>"/>
        <input type="hidden" name="ImagevalueCount" value="<?php echo $j; ?>"/>
        <input type="hidden" name="fieldid" value="<?php echo $row->fieldid; ?>"/>
        <input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>"/>
        <input type="hidden" name="option" value="com_boss"/>
        <input type="hidden" name="directory" id="directory" value="<?php echo $directory; ?>"/>
        <input type="hidden" name="act" value="fields"/>
        <input type="hidden" name="task" value=""/>
        </form>

        <?php
        if ($row->fieldid > 0) {
            print "<script type=\"text/javascript\"> document.adminForm.name.readOnly=true; </script>";
        }

        print "<script type=\"text/javascript\"> disableAll(); </script>";
        print "<script type=\"text/javascript\"> selType('" . $row->type . "'); </script>";
    }

    public static function showDirectories($rows, $directory) {
        ?>
        <?php HTML_boss::header(BOSS_LIST_DIRECTORIES, $directory, $rows); ?>
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

    public static function listTemplates($templates, $directory, $directories) {
        HTML_boss::header(BOSS_LIST_TEMPLATES, $directory, $directories); ?>

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
                                <a href="<?php echo JPATH_SITE; ?>/administrator/index2.php?option=com_boss&directory=<?php echo $directory; ?>&act=templates&task=edit_tmpl&template=<?php echo $tpl; ?>&type_tmpl=2">
                                    <img src="<?php echo JPATH_SITE; ?>/administrator/components/com_boss/images/16x16/categories.png"
                                         title="<?php echo BOSS_EDIT_CAT_TMPL; ?>"/>
                                </a>
                            </td>
                            <td align="center">
                                <a href="<?php echo JPATH_SITE; ?>/administrator/index2.php?option=com_boss&directory=<?php echo $directory; ?>&act=templates&task=edit_tmpl&template=<?php echo $tpl; ?>&type_tmpl=1">
                                    <img src="<?php echo JPATH_SITE; ?>/administrator/components/com_boss/images/16x16/contents.png"
                                         title="<?php echo BOSS_EDIT_CONTENT_TMPL; ?>"/>
                                </a>
                            </td>
                            <td align="center">
                                <a href="<?php echo JPATH_SITE; ?>/administrator/index2.php?option=com_boss&directory=<?php echo $directory; ?>&act=templates&task=edit_tmpl_fields&template=<?php echo $tpl; ?>">
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

    public static function editTemplate($directory, $directories, $template, $type_tmpl, $positions, $positionsDesc, $groupfields, $fields, $cats) {
        HTML_boss::header(BOSS_LIST_TEMPLATES, $directory, $directories);
        if ($type_tmpl == 2)
            $img = 'template_category.png';
        else
            $img = 'template_content.png';
        ?>
        <script type="text/javascript">
            var boss_positions = new Array(<?php echo '"'.implode('", "', $positions).'"'; ?>);
            var boss_selected_group = boss_positions[0];
        function boss_showgroup(value) {
                var ge,ce = '';

                if (value == '') {
                        value = boss_positions[0];
                }
                
                for(i=0;i<boss_positions.length;i++) {
                        ge = document.getElementById(boss_positions[i]);
                        ce = document.getElementById('boss_' + boss_positions[i]);
                        if (boss_positions[i] == value) {
                                ge.style.display = 'block';
                                ce.className = 'row1';
                        }
                }

                if (boss_selected_group != value) {
                        ge = document.getElementById(boss_selected_group);
                        ce = document.getElementById('boss_'+boss_selected_group);
                        ge.style.display = 'none';
                        ce.className = 'row0';
                }
                boss_selected_group = value;
        }
        </script>
    <form action="index2.php" method="post" name="adminForm">
        <table class="adminlist">
            <tr>
                <td valign="top">
                    <table class="adminlist">
                        <tr>
                            <th>
                                <?php echo BOSS_SELECT_GROUP_FIELDS; ?>
                            </th>
                        </tr>
                    <?php $i=0;
                    foreach ($positions as $position) {
                        $class= ($i == 0) ?  'class="row1"' : 'class="row0"';
                        ?>
                        <tr id="boss_<?php echo $position; ?>" <?php echo $class; ?> >
                            <td style="cursor: pointer;" onclick="boss_showgroup('<?php echo $position; ?>')">
                            <?php
                            echo ' <img src="/administrator/images/info.png" title="'.$positionsDesc[$i].'"> ';
                            echo $position;
                            ?>
                            </td>
                        </tr>
                    <?php $i++; } ?>
                    </table>
                </td>
                <td>
                    <?php 
                            $j=0;
                            foreach ($positions as $position) { 
                            ($j == 0) ?  $style='style="display: block;"' : $style='style="display: none;"';
                                ?>
                            
                    <div id="<?php echo $position; ?>" <?php echo $style; ?> >
                        <table class="adminlist">
                            <tr>
                                <th class="title"><?php echo BOSS_GROUPS;?></th>
                                <th class="title" colspan="2">
                                    <?php
                                        echo $position;
                                        if (!empty($positionsDesc[$j])){
                                            echo ' <img src="/administrator/images/info.png" title="'.$positionsDesc[$j].'">';
                                        }
                                    ?>
                                </th>
                            </tr>
                            <tr>
                                <td><?php echo BOSS_PUBLISH; ?></td>
                                <td colspan="2">
                                    <select name="published|<?php echo $position; ?>"
                                            id="published|<?php echo $position; ?>">
                                        <option value="1" <?php if (@$groupfields[$position]['published'] == 1) {
                                            echo "selected";
                                        } ?>><?php echo BOSS_PUBLISH; ?></option>
                                        <option value="0" <?php if (@$groupfields[$position]['published'] == 0) {
                                            echo "selected";
                                        } ?>><?php echo BOSS_NO_PUBLISH ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo BOSS_FORM_CATEGORY_GROUPS; ?></td>
                                <td colspan="2">
                                    <select name="catsid|<?php echo $position; ?>[]" multiple='multiple'
                                            id="catsid|<?php echo $position; ?>[]" size="4">
                                    <?php

                                    if (strpos(@$groupfields[$position]['catsid'], ",-1,") === false)
                                        echo "<option value='-1'>" . BOSS_MENU_ALL_CONTENTS . "</option>";
                                    else
                                        echo "<option value='-1' selected>" . BOSS_MENU_ALL_CONTENTS . "</option>";
                                    HTML_boss::selectCategories(0, BOSS_ROOT . " >> ", $cats, -1, -1, 1, @$groupfields[$position]['catsid']);
                                    ?>
                                    </select>
                                </td>
                                </tr>
                            <tr>
                                <th><?php echo BOSS_GROUP_FIELDS; ?></th>
                                <th><?php echo BOSS_TH_PUBLISH; ?></th>
                                <th><?php echo BOSS_ORDER; ?></th>
                            </tr>
                            <tr>
                                <?php
                                if (isset($fields)) {
                                    $k = 0;
                                    $i = 0;
                                    foreach ($fields as $field) {
                                        ?>
                                            <tr class="row<?php echo $k; ?>">
                                                <td><?php echo $field->title; ?></td>
                                            <?php 
                                                if (@in_array($field->fieldid, $groupfields[$position]['fieldid']))
                                                    $checked = 'checked';
                                                else
                                                    $checked = '';
                                                ?>
                                                    <td>
                                                        <input type="checkbox"
                                                               name="required|<?php echo $position; ?>|<?php echo $field->fieldid; ?>"
                                                               value="1" <?php echo $checked; ?> />
                                                     </td>
                                                     <td>
                                                        <input type="text" maxlength="2" size="2"
                                                               name="ordering|<?php echo $position; ?>|<?php echo $field->fieldid; ?>"
                                                               value="<?php echo @$groupfields[$position][$field->fieldid]['ordering']; ?>" <?php echo $checked; ?> />
                                                    </td>
                                                
                                            </tr>
                                        <?php
                                                            $k = !$k;
                                        $i++;
                                    }
                                }
                                ?>
                            </tr>
                        </table>
                    </div>
                            <?php $j++; } ?>
                </td>
                <td valign="top">
                    <img src="<?php echo JPATH_SITE . "/templates/com_boss/$template/$img"; ?>"/>
                </td>
                </tr>
        </table>
        <input type="hidden" name="option" value="com_boss"/>
        <input type="hidden" name="act" value="templates"/>
        <input type="hidden" name="task" value="save_tmpl"/>
        <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
        <input type="hidden" name="template" value="<?php echo $template;?>"/>
        <input type="hidden" name="type_tmpl" value="<?php echo $type_tmpl;?>"/>
        <input type="hidden" name="boxchecked" value="0"/>
    </form>
             

        <?php

    }

    public static function editTemplateFields($directory, $directories, $template, $type_tmpl, $positions) {
        HTML_boss::header(BOSS_EDIT_TEMPLATE_FIELD, $directory, $directories);
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <th><?php echo "Позиции шаблонов категории";?></th>
                    <th><?php echo "Описание позиций шаблонов категории";?></th>
                    <th><?php echo "Позиции шаблонов контента";?></th>
                    <th><?php echo "Описание позиций шаблонов контента";?></th>
                </tr>
                <?php for($i=0;$i<15;$i++){?>
                <tr>
                    <td><input type="text" name="category[]" value="<?php echo @$positions['category'][$i]; ?>" size="20" maxlength="20"/></td>
                    <td><input type="text" name="category_desc[]" value="<?php echo @$positions['category_desc'][$i]; ?>" size="50" maxlength="255"/></td>
                    <td><input type="text" name="content[]" value="<?php echo @$positions['content'][$i]; ?>" size="20" maxlength="20"/></td>
                    <td><input type="text" name="content_desc[]" value="<?php echo @$positions['content_desc'][$i]; ?>" size="50" maxlength="255"/></td>
                </tr>
                <?php } ?>
            </table>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="act" value="templates"/>
            <input type="hidden" name="task" value="save_tmpl_fields"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="template" value="<?php echo $template;?>"/>
            <input type="hidden" name="type_tmpl" value="<?php echo $type_tmpl;?>"/>
            <input type="hidden" name="boxchecked" value="0"/>
        </form>
        <?php
    }

    public static function editTmplSource($directory,$directories,$template,$source_file,$files,$source){
     HTML_boss::header(BOSS_EDIT_TEMPLATE_FIELD, $directory, $directories); ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminlist">
                <tr>
                    <th>
                        <?php echo BOSS_FILES; ?>
                    </th>
                    <th>
                        <?php echo @$source_file; ?>
                    </th>
                </tr>
                <tr>
                    <td>
                        <table class="adminlist">
                    <?php
                    foreach ($files as $file){
                        if ($file == $source_file) $class = ' class="row1"';
                        else $class = ' class="row0"';
                        ;?>
                        <tr<?php echo $class; ?>>
                            <td>
                                <a href="index2.php?option=com_boss&act=templates&task=edit_tmpl_source&source_file=<?php echo $file; ?>&template=<?php echo $template; ?>">
                                    <?php echo $file; ?>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                        </table>
                    </td>
                    <td>
                        <div align="center">
                            <textarea name="source" cols="130" rows="30"><?php echo @$source; ?></textarea>
                        </div>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="com_boss"/>
            <input type="hidden" name="act" value="templates"/>
            <input type="hidden" name="task" value="saveTmplSource"/>
            <input type="hidden" name="directory" value="<?php echo $directory;?>"/>
            <input type="hidden" name="template" value="<?php echo $template;?>"/>
            <input type="hidden" name="source_file" value="<?php echo $source_file;?>"/>
        </form>
        <?php
    }

    public static function listPlugins($directory, $directories, $plugins, $used) {
        HTML_boss::header(BOSS_PLUGINS, $directory, $directories);?>
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
                            <?php	echo $plugin['file']; ?>
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

    public static function listFieldImages($fieldimages, $directory, $directories) {
        global $mosConfig_live_site;
        HTML_boss::header(BOSS_RADIOIMAGE, $directory, $directories); ?>
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
                                <img src="<?php echo "$mosConfig_live_site/images/boss/$directory/fields/$fieldimage";?>"
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
    public static function displaycsvpreview($directory, $directories, $filename, $csvcat, $handle, $fgetcsv_delimiter, $fgetcsv_enclosure, $line, $maxCols, $previewLimit, $actprev) {

        HTML_boss::header(BOSS_CSV_HEADER, $directory, $directories);

        $csv_block = "";
        $display_block = "";
        while (($line < $previewLimit) && ($dataprev = fgetcsv($handle, 1024, $fgetcsv_delimiter))) {
            $numOfCols = count($dataprev);
            if ($numOfCols > $maxCols) $maxCols = $numOfCols;
            if ($line != 0) {
                $csv_block .= "<tr class=\"row" . ($line & 1) . "\" >";
                for ($index = 0; $index < $numOfCols; $index++) {
                    if ($dataprev[$index] == "") {
                        $csv_block .= "<td >"
                                . ""
                                . "&nbsp;"
                                . "</td>";
                    } else {
                        $csv_block .= "<td >"
                                . ""
                                . stripslashes(HTML_boss::normalise($dataprev[$index]))
                                . "</td>";
                    }
                }
                $csv_block .= "</tr>";
            }
            $line++;
        }
        $display_block .= "
		<form  action=\"index2.php?option=com_boss&act=csv&task=" . $actprev . "\" method=\"post\" name=\"csv\" id=\"csv\">
		<table class=\"adminlist\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">
				<tr>
					<td align=\"center\" colspan=\"" . $numOfCols . "\" >Предосмотр загружаемых данных</td>
				</tr>
				<tr>";
        for ($index = 0; $index < $maxCols; $index++) {
            $display_block .= "<th class=\"title\">Столбец" . ($index + 1) . "</th>";
        }
        $display_block .= "</tr>";
        $display_block .= "</tr>";
        $display_block .= $csv_block;
        $display_block .= "</tr>";
        $display_block .= "</tr>
					<td >&nbsp;</td>
				</tr>
			</table>
			    <br>
    			<input type='hidden' name='filename' value='$filename' >
				<input type='hidden' name='csvcat' value='$csvcat' >
				<input type='submit' name='" . $actprev . "' value='Загрузить данные' />&nbsp&nbsp&nbsp&nbsp;
			</form>";
        echo $display_block;
    }

    public static function displaycsvmetod($directory, $directories) {

        HTML_boss::header(BOSS_CSV_HEADER, $directory, $directories);?>
        <ul>
            <li><a href="index2.php?option=com_boss&act=csv&task=build_insert">Добавление новых данных</a></li>
            <li><a href="index2.php?option=com_boss&act=csv&task=build_list">Обновление существующих данных</a></li>
        </ul>
        <?php

    }

    public static function displaycsvbuildinsert($directory, $directories, $cats, $actprev) {

        HTML_boss::header(BOSS_CSV_HEADER, $directory, $directories);?>
        <script type="text/javascript">
            function submitbutton(pressbutton) {
            <?php getEditorContents('editor1', 'description'); ?>
                submitform(pressbutton);
            }
        </script>
        <form enctype="multipart/form-data" action="index2.php?option=com_boss&act=csv&task=csv_preview" method="post"
              name="csv" id="csv">
            <p><input type="file" name="csvname"/></p>

            <select name="csvcat" id="csvcat">
                <option value="0"><?php echo BOSS_ROOT; ?></option>
            <?php HTML_boss::selectCategories(0, BOSS_ROOT . " >> ", $cats, null, null); ?>
            </select>

            <p><?php echo BOSS_CSV_CONTINNUE_LONG; ?></p>
            <input type="submit" name="csv_preview" value="<?php echo BOSS_CSV_CONTINNUE; ?>"/>
            <input type="hidden" name="actprev" value="<?php echo $actprev; ?>"/>


            <input type="hidden" name="option" value="com_boss"/>
        </form>
        <?php

    }

    public static function showImpExpForm($directory, $directories, $packs){
        HTML_boss::header(BOSS_EX_IM_HEADER, $directory, $directories); ?>
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

    public static function listUsers($directory, $directories, $pageNav, $users) {
		HTML_boss::header(BOSS_TH_USERS, $directory, $directories);
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

    public static function editUserInfo($directory, $directories, $userFields, $fields, $users, $selectedUserId){

         HTML_boss::header(BOSS_CONTENT_EDITION, $directory, $directories);
         $plugins = get_plugins($directory, 'fields');
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
}
?>