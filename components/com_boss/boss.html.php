<?php
/**
 * @package Joostina BOSS
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina BOSS - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Joostina BOSS основан на разработках Jdirectory от Thomas Papin
 */
defined('_VALID_MOS') or die();

require_once(JPATH_BASE . '/components/com_boss/boss.api.php');

class boss_html
{ //implements  bossUI {

    var $category;
    var $cats;
    var $ad;
    var $ads;
    var $fields;
    var $field_values;
    var $categories;
    var $plugins;
    var $tasknav;
    var $conf;
    var $order;
    var $text_search;
    var $show_content;
    var $directory;
    var $directory_name;
    var $itemid;
    var $task;
    var $searchs;
    var $fields_searchable;
    var $userid;
    var $url;
    var $navlink;
    var $user;
    var $content;
    var $errorMsg;
    var $paths;
    var $subcats;
    var $reviews;
    var $comments;
    var $vote;
    var $fieldsgroup;
    var $popup;
    var $tags;
    var $template_name;
    var $rating;

    function displayList()
    {
        if($this->task == 'show_frontpage'){
            include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/frontpage.php');
        }
        else{
            include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/list.php');
        }
    }

    function jsJumpmenu()
    {
        if (!defined('JUMPMENU_LOADED')) {
            ?>
        <script language="JavaScript" type="text/JavaScript">
            <!--
            function jumpmenu(target, obj, restore) {
                eval(target + ".location='" + obj.options[obj.selectedIndex].value + "'");
                obj.options[obj.selectedIndex].innerHTML = "<?php echo BOSS_WAIT;?>";
            }
            //-->
        </script> <?php
        DEFINE('JUMPMENU_LOADED', 1);
        }
    }

    function displayWriteForm()
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        $plugins = $this->plugins;
        ?>
    <script type="text/javascript" src="<?php echo JPATH_SITE;?>/includes/js/overlib_mini.js"></script>
    <?php $this->jsJumpmenu(); ?>
    <script type="text/javascript"><!--//--><![CDATA[//><!--
        //*** Parametres
        //*** texte : objet representant le textarea
        //*** max : nombre de caracteres maximum
        function CaracMax(texte, max) {
            if (texte.value.length >= max) {
                texte.value = texte.value.substr(0, max - 1);
            }
        }

    function submitbutton(mfrm) {
        var me = mfrm.elements;
        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
        var r_num = new RegExp("[^0-9\.,]", "i");
        var r_email = new RegExp("^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,3}$", "i");
        var form = mfrm;
        var errorMSG = '';
        var iserror = 0;

        if (mfrm.username && (r.exec(mfrm.username.value) || mfrm.username.value.length < 3)) {
            errorMSG += mfrm.username.getAttribute('mosLabel').replace('&nbsp;', ' ') + ' : <?php echo addslashes(html_entity_decode(sprintf(BOSS_VALID_AZ09, BOSS_PROMPT_UNAME, 4), ENT_QUOTES)); ?>\n';
            mfrm.username.style.background = "red";
            iserror = 1;
        }
        if (mfrm.password && r.exec(mfrm.password.value)) {
            errorMSG += mfrm.password.getAttribute('mosLabel').replace('&nbsp;', ' ') + ' : <?php echo addslashes(html_entity_decode(sprintf(BOSS_VALID_AZ09, BOSS_REGISTER_PASS, 6), ENT_QUOTES)); ?>\n';
            mfrm.password.style.background = "red";
            iserror = 1;
        }

        if (mfrm.email && !r_email.exec(mfrm.email.value) && mfrm.email.getAttribute('mosReq')) {
            errorMSG += mfrm.email.getAttribute('mosLabel').replace('&nbsp;', ' ') + ' : <?php echo html_entity_decode(addslashes(BOSS_REGWARN_MAIL), ENT_QUOTES); ?>\n';
            mfrm.email.style.background = "red";
            iserror = 1;
        }

        // loop through all input elements in form
        for (var i = 0; i < me.length; i++) {

            if ((me[i].getAttribute('test') == 'number' ) && (r_num.exec(me[i].value))) {
                errorMSG += me[i].getAttribute('mosLabel').replace('&nbsp;', ' ') + ' : <?php echo html_entity_decode(addslashes(BOSS_REGWARN_NUMBER), ENT_QUOTES); ?>\n';
                iserror = 1;
            }

            // check if element is mandatory; here mosReq="1"
            if (me[i].getAttribute('mosReq') == 1) {
                if (me[i].type == 'radio' || me[i].type == 'checkbox') {
                    var rOptions = me[me[i].getAttribute('name')];
                    var rChecked = 0;
                    if (rOptions.length > 1) {
                        for (var r = 0; r < rOptions.length; r++) {
                            if (rOptions[r].checked) {
                                rChecked = 1;
                            }
                        }
                    } else {
                        if (me[i].checked) {
                            rChecked = 1;
                        }
                    }
                    if (rChecked == 0) {
                        // add up all error messages
                        errorMSG += me[i].getAttribute('mosLabel').replace('&nbsp;', ' ') + ' : <?php echo html_entity_decode(addslashes(BOSS_REGWARN_ERROR), ENT_QUOTES); ?>\n';
                        // notify user by changing background color, in this case to red
                        me[i].style.background = "red";
                        iserror = 1;
                    }
                }
                if (me[i].value == '') {
                    // add up all error messages
                    errorMSG += me[i].getAttribute('mosLabel') + ' : <?php echo html_entity_decode(addslashes(BOSS_REGWARN_ERROR), ENT_QUOTES); ?>\n';
                    // notify user by changing background color, in this case to red
                    me[i].style.background = "red";
                    iserror = 1;
                }
            }
        }
        <?php
        foreach($this->fields as $field){
            if(method_exists($plugins[$field->type],'addInWriteScript')){
                echo $plugins[$field->type]->addInWriteScript($field);
            }
        }
        ?>

        if (iserror == 1) {
            alert(errorMSG);
            return false;
        } else {
            return true;
        }
    }
    //--><!]]></script>
    <?php
        include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write.php');
    }

    function displayProfile()
    {
        $itemid = $this->itemid;
        $user = $this->user;
        $directory = $this->directory;
        $plugins = BossPlugins::get_plugins($directory, 'fields');
        ?>
    <br/>
    <script language="javascript" type="text/javascript">
        function submitbutton() {
            var form = document.mosUserForm;
            var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

            // do field validation
            if (form.name.value == "") {
                alert("<?php echo BOSS_REGWARN_NAME;?>");
            } else if (form.username.value == "") {
                alert("<?php echo BOSS_REGWARN_UNAME;?>");
            } else if (r.exec(form.username.value) || form.username.value.length < 3) {
                alert("<?php printf(BOSS_VALID_AZ09, BOSS_PROMPT_UNAME, 4);?>");
            } else if (form.email.value == "") {
                alert("<?php echo BOSS_REGWARN_MAIL;?>");
            } else if ((form.password.value != "") && (form.password.value != form.verifyPass.value)) {
                alert("<?php echo BOSS_REGWARN_VPASS2;?>");
            } else if (r.exec(form.password.value)) {
                alert("<?php printf(BOSS_VALID_AZ09, BOSS_REGISTER_PASS, 6);?>");
            } else {
                <?php
                foreach($this->fields as $field){
                    if(method_exists($plugins[$field->type],'addInWriteScript')){
                        echo $plugins[$field->type]->addInWriteScript($field);
                    }
                }
                ?>
                form.submit();
            }
        }
    </script>
    <?php $target = sefRelToAbs("index.php?option=com_boss&amp;task=save_profile&amp;directory=$directory&amp;Itemid=$itemid"); ?>
    <form action="<?php echo $target; ?>" method="post" name="mosUserForm">
        <?php include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/profile.php'); ?>
        <input type="hidden" name="id" value="<?php echo $user->id;?>"/>
        <input type="hidden" name="<?php echo josSpoofValue(); ?>" value="1" />
    </form>
    <?php

    }

    function displayEmailForm()
    {
        ?>
    <script language="javascript" type="text/javascript">
        function submitbutton() {
            var form = document.frontendForm;
            // do field validation
            if (form.email.value == "" || form.youremail.value == "") {
                alert('<?php echo addslashes(BOSS_EMAIL_ERR_NOINFO); ?>');
                return false;
            }
            return true;
        }
    </script>        <?php $target = sefRelToAbs("index2.php?option=com_boss&amp;task=emailsend"); ?>
    <form action="<?php echo $target;?>" name="frontendForm" method="post" onSubmit="return submitbutton();">
        <?php
            include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/emailform.php');
        ?>
        <input type="hidden" name="contentid" value="<?php echo $this->content->id; ?>"/>
        <input type="hidden" name="itemid" value="<?php echo $this->itemid; ?>"/>
        <input type="hidden" name="directory" value="<?php echo $this->directory; ?>"/>
    </form>
        <?php

    }

    function displayNotAllowed()
    {
        ?>
    <?php echo '<img src="/templates/com_boss/' . $this->template_name . '/images/warning.gif" alt="warning" border="0" align="center">'; ?>
    <b><?php echo BOSS_CONTENTD_NOTALLOWED;?></b>
    <?php

    }

    function displayRules()
    {
        include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/rules.php');
    }

    function displayMessageForm($content, $user, $mode, $allow_attachement, $itemid)
    {
        $directory = $this->directory;

        ?>
    <script language="javascript" type="text/javascript">
        function submitbutton() {
            var form = document.forms["saveForm"];// document.getElementById("saveForm");
            var r = new RegExp("[^0-9\.,]", "i");

            <?php if ($mode == 0) : ?>
                // do field validation
                if (form.email.value == "") {
                    alert("<?php echo _REGWARN_EMAIL;?>");
                    return false;
                }
            <?php endif; ?>
            if (form.name.value == "") {
                alert("<?php echo _REGWARN_NAME;?>");
                return false;
            }
            if (form.body.value == "") {
                alert("<?php echo _REGWARN_BODY;?>");
                return false;
            }
            form.submit();
        }
    </script>
    <fieldset id="boss_fieldset">
        <!-- titel -->
        <legend>
            <?php  echo BOSS_FORM_MESSAGE_WRITE; ?>
        </legend>
        <!-- titel -->
        <!-- form -->
        <?php $target = sefRelToAbs("index.php?option=com_boss&amp;task=send_message&amp;mode=$mode&amp;directory=$directory&amp;Itemid=$itemid");?>
        <form action="<?php echo $target;?>" method="post" name="saveForm" enctype="multipart/form-data">
            <?php if ($mode == 0) { ?>
            <!-- name -->
            <label for="name"><?php echo BOSS_FORM_NAME; ?></label>
            <input class='boss_required inputbox' size="50" id='name' type='text' name='name' maxlength='50' value='<?php echo $user->name; ?>' />
            <!-- name -->
            <br/>
            <!-- email -->
            <label for="email"><?php echo BOSS_FORM_EMAIL; ?></label>
            <input class='boss_required inputbox' size="50" id='email' type='text' name='email' maxlength='50' value='<?php echo $user->email; ?>' />
            <!-- email -->
            <br/>
            <?php } ?>
            <!-- title -->
            <label for="title"><?php echo BOSS_FORM_MESSAGE_TITLE; ?></label>
            <input class='inputbox' size="50" id='title' type='text' name='title' maxlength='50' value="<?php echo BOSS_EMAIL_TITLE . htmlspecialchars(stripslashes($content->name), ENT_QUOTES); ?>" />
            <!-- title -->

            <br/>
            <!-- body -->
            <label for="body"><?php echo BOSS_FORM_MESSAGE_BODY; ?></label>
            <textarea class='boss_required inputbox' id='body' name='body' cols='50' rows='10' wrap='VIRTUAL'>
                <?php echo BOSS_EMAIL_BODY . htmlspecialchars(stripslashes('text' /*TODO@$content->content_text*/), ENT_QUOTES); ?>
            </textarea>
            <!-- body -->
            <br/>
            <?php if (($mode == 0) && ($allow_attachement == 1)) { ?>
            <!-- Attach -->
            <label for="attach_file"><?php echo BOSS_ATTACH_FILE; ?></label>
            <input id="attach_file" type="file" class=" inputbox" name="attach_file"/>
            <br/>
            <?php } ?>

            <input type="hidden" name="gflag" value="0">
            <input type='hidden' name='contentid' id='contentid' value='<?php echo $content->id ?>' />
            <!-- buttons -->
            <span class="button">
                <input type="button" class="button" value=<?php echo BOSS_SEND_EMAIL_BUTTON; ?> onclick="submitbutton()" />
            </span>
            <!-- buttons -->

        </form>
        <!-- form -->
    </fieldset>

            <?php

    }

    function displayConfirmation()
    {
        $itemid = $this->itemid;
        $content = $this->content;
        $username = $this->user->name;
        $directory = $this->directory;
        $text = BOSS_CAUTION . " <b>" . $username . "</b> " . BOSS_CAUTION_DELETE1 . "<b>" . $content->name . "</b>" . BOSS_CAUTION_DELETE2;
        $target = sefRelToAbs("index.php?option=com_boss&amp;task=delete_content&amp;contentid=" . $content->id . "&amp;mode=confirm&amp;directory=$directory&amp;Itemid=$itemid");
        $link_yes = "<a href='" . $target . "'>" . BOSS_YES_DELETE . "</a>";
        $target = sefRelToAbs("index.php?option=com_boss&amp;directory=$directory&amp;Itemid=$itemid");
        $link_no = "<a href='" . $target . "'>" . BOSS_NO_DELETE . "</a>";
        include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/confirmation.php');
    }

    function recurseCategories($id, $level, &$children, $itemid, $showChildren=1, $showDesc=1, $showImg=1)
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        $directory = $this->directory;
        $my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
        
        if (@$children[$id]) {
            $first = true;
            foreach ($children[$id] as $row) {
                
                //парва пользователей
                if($this->conf->allow_rights){
                    if(!empty($row->rights)){
                        $this->rights->bind_rights($row->rights);
                    }
                    else{
                        $this->rights->bind_rights($this->conf->rights);
                    }                  
                    
                    if(!$this->rights->allow_me('show_category', $my->groop_id)){
                        continue;
                    }
                }
                
                $link = sefRelToAbs("index.php?option=com_boss&amp;task=show_category&amp;catid=" . $row->id . "&amp;slug=" . $row->slug . "&amp;order=0&amp;directory=$directory&amp;Itemid=" . $itemid);
                if ($level == 0) {
                    ?>
<div class="boss_maincat">
                    <?php
                    if($showImg==1){
                        if (file_exists(JPATH_BASE . "/images/boss/$directory/categories/" . $row->id . "cat.jpg"))
                            echo '<a href="' . $link . '"><img class="imgcat" src="' . JPATH_SITE . '/images/boss/' . $directory . '/categories/' . $row->id . 'cat.jpg" alt="' . $row->name . '" /></a>';
                        else
                            echo '<a href="' . $link . '"><img class="imgcat" src="' . JPATH_SITE . '/templates/com_boss/' . $this->template_name . '/images/default.gif" alt="' . $row->name . '" /></a>';
                    }?>
        <h2 class="boss_maincat_title"><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></h2>
                    
                    <?php if($showDesc==1){ ?>
	<span class="boss_cat_desc">
                        <?php echo $row->description; ?>
        </span>
        <div class="boss_sub_cat">
                    <?php } 
                }
                else {     
                    if ($first == false)
                        echo ' - ';
                    echo '<h3 class="boss_subcat_title"><a href="' . $link . '">' . $row->name . '</a></h3>';
                    $first = false;
                    ?>
                    
                <?php
                }
                if ($level == 0 && $showChildren != 0) {
                    $this->recurseCategories($row->id, $level + 1, $children, $itemid);
                }
                if ($level == 0) {
                ?>
    </div>
</div>
		<?php
                }
            }
            
        }
        ?><div class="boss_spacer"></div><?php
    }

    function displayFront()
    {
        $path = JPATH_BASE . '/templates/com_boss/' . $this->template_name;
        if (!is_dir($path)) {
            echo '<script type="text/javascript">';
            echo 'alert(\'Установите хотя-бы один шаблон, желательно чтобы это был шаблон "default".\')';
            echo '</script>';
            return;
        }
        include($path . '/front.php');
    }

    function displayLoginForm($return)
    {
        global $mosConfig_lang;

        $validate = '<input type="hidden" name="' . josSpoofValue(1) . '" value="1" />';
        $link_login = sefRelToAbs("index.php?option=login");
        $return = sefRelToAbs($return);

        ?>
    <form action="<?php echo $link_login; ?>" method="post" name="login" id="login">

        <?php include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/login.php');     ?>

        <input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>"/>
        <?php echo $validate; ?>
        <input type="hidden" name="message" value="0"/>
        <input type="hidden" name="force_session" value="1"/>
        <input type="hidden" name="return" value="<?php echo $return; ?>"/>
    </form>
    <?php

    }

    function selectCategories($id, $level, $children, &$catid, $root_allowed, $itemid, $linkoption, $current_cat_only = 0)
    {
        if (@$children[$id]) {
            foreach ($children[$id] as $row) {
                if (($root_allowed == 1) || (!@$children[$row->id])) {
                    if ($current_cat_only == 0) {
                        ?>
                    <option value="<?php echo $row->id ?>" <?php if ($row->id == $catid) {
                        echo "selected='selected'";
                    } ?>>
                        <?php echo $level . $row->name; ?>
                    </option>
                    <?php

                    } else if ($row->id == $catid) {
                        echo "<label>" . $level . $row->name . "</label>";
                    }
                }
                $this->selectCategories($row->id, $level . $row->name . " >> ", $children, $catid, $root_allowed, $itemid, $linkoption, $current_cat_only);
            }
        }
    }

    function displaySearchFields()
    {

        $directory = $this->directory;
        $fields_searchable = $this->fields;
        $catid = (!empty($this->category->id)) ? $this->category->id : 0;
        $field_values = $this->field_values;
        $plugins = BossPlugins::get_plugins($directory, 'fields');

        foreach ($fields_searchable as $fsearch) {
            if (($catid == 0) || (strpos($fsearch->catsid, ",$catid,") !== false) || (strpos($fsearch->catsid, ",-1,") !== false)) {
                $return = jDirectoryField::getFieldForm($fsearch, null, null, $field_values, $directory, $plugins, "search");
                $title = $return->title;
                $input = $return->input;
                include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/search_field.php');
            }
        }
    }

    function displayFilter($showName = 0)
    {
        if ($this->conf->filter == 'no')
            return;
        elseif (!is_file(JPATH_BASE . '/images/boss/' . $this->directory . '/plugins/filters/' . $this->conf->filter . '/plugin.php'))
            return;

        $filters = BossPlugins::get_plugins($this->directory, 'filters');
        $filter = $filters[$this->conf->filter];

        $filter->directory = $this->directory;
        $filter->fields_searchable = $this->fields_searchable;
        $filter->category = $this->category;
        $filter->field_values = $this->field_values;
        $filter->itemid = $this->itemid;
        $filter->conf = $this->conf;
        $filter->show_name = $showName;

        $filter->displayFilter();
    }

    function displaySearch()
    {

        $this->jsJumpmenu();
        $action = sefRelToAbs('index.php?option=com_boss&directory=' . $this->directory . '&task=show_result&catid=' . $this->category->id . '&Itemid=' . $this->itemid);
        ?>
    <form action="<?php echo $action; ?>" name="adminform" method="post">
        <?php include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/search.php'); ?>
    </form>
    <?php

    }

    /*************************************************************************************************************************************************/
    /*************************************************************************************************************************************************/
    /*************************************************************************************************************************************************/
    /****                      Public Functions                                                                                                                                                            *******/
    /*************************************************************************************************************************************************/
    /*************************************************************************************************************************************************/
    /*************************************************************************************************************************************************/
    function displaySearchForm()
    {
        $text_search = $this->text_search;
        $task = $this->task;
        $catid = $this->category->id;
        $order = $this->conf->order;
        $itemid = $this->itemid;
        $userid = intval(mosGetParam($_GET, 'userid', 0)); //TODO
        $directory = $this->directory;

        if ($text_search == "")
            $text_search = BOSS_SEARCH_TEXT;

        $this->jsJumpmenu();
        ?>
    <form action="index.php" method="get">
        <input type="hidden" name="option" value="com_boss"/>
        <input type="hidden" name="directory" value="<?php echo $directory; ?>"/>
        <input type="hidden" name="task" value="<?php echo $task;?>"/>
        <?php
            switch ($task) {
        case "show_user":
            echo '<input type="hidden" name="userid" value="' . $userid . '" />';
            break;
        case "show_category":
            echo '<input type="hidden" name="catid" value="' . $catid . '" />';
            break;
    }
        ?>
        <input name="text_search" id="text_search" maxlength="20" alt="search" class="inputbox" type="text" size="20"
               value="<?php echo $text_search;?>" onblur="if(this.value=='') this.value='';"
               onfocus="if(this.value=='<?php echo $text_search;?>') this.value='';"/>
        <input type="hidden" name="order" value="<?php echo $order; ?>"/>
        <input type="hidden" name="Itemid" value="<?php echo $itemid;?>"/>
    </form>
        <?php

    }

    function displayContentTitle($content, $link = true)
    {
        $directory = $this->directory;
        if ($link == true) {
            $linkTarget = sefRelToAbs("index.php?option=com_boss&amp;task=show_content&amp;contentid=" . $content->id . "&amp;catid=" . $content->catid . "&amp;directory=$directory&amp;Itemid=" . $this->itemid);
            echo '<a href="' . $linkTarget . '">' . stripslashes($content->name) . '</a>';
        }
        else {
            echo stripslashes($content->name);
        }
    }





    function isReviewAllowed()
    {
        return $this->conf->allow_comments;
    }
    
    function isRatingAllowed()
    {
        return $this->conf->allow_ratings;
    }

    function displayTags()
    {
        if (!empty($this->tags))
            return BOSS_TAGS . ': ' . $this->tags;
        else
            return false;
    }

    function displayListTags($content)
    {
        if (isset($this->tags[$content->id]))
            return BOSS_TAGS . ': ' . $this->tags[$content->id];
        else
            return false;
    }

    function displayContentEditDelete($content)
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        $directory = $this->directory;
        $itemid = $this->itemid;
        $catid = mosGetParam($_REQUEST, 'catid', '');
        
        $target = sefRelToAbs("index.php?option=com_boss&amp;task=write_content&amp;contentid=" . $content->id . "&amp;content_types=" . $content->type_content . "&amp;catid=" . $catid . "&amp;directory=$directory&amp;Itemid=" . $this->itemid);
        $edit = "<a href='" . $target . "'>" . BOSS_CONTENT_EDIT . "</a> &nbsp;";         
        $target = sefRelToAbs("index.php?option=com_boss&amp;task=delete_content&amp;contentid=" . $content->id . "&amp;catid=" . $content->catid . "&amp;directory=$directory&amp;Itemid=" . $this->itemid);
        $delete = "<a href='" . $target . "'>" . BOSS_CONTENT_DELETE . "</a>";
        
        if($this->conf->allow_rights){
            $tmp = '';
            if($this->perms->edit_all_content || ($this->perms->edit_user_content && $my->id == $content->user_id)){
                $tmp .= $edit;
            }
            if($this->perms->delete_all_content || ($this->perms->delete_user_content && $my->id == $content->user_id)){
                $tmp .= $delete;
            }
            echo ($tmp == '') ? '' : '<div>' . $tmp . '</div>';
        }
        else if (($my->id == $content->user_id && $my->id != 0) || $my->usertype == 'Super Administrator') {
            echo '<div>';
            echo $edit; 
            echo $delete;
            echo '</div>';
        }
    }

    function displayRulesText()
    {
        echo stripslashes($this->conf->rules_text);
    }

    /**
     * Writes PDF icon
     */
    function PdfIcon(&$row, &$params, $hide_js)
    {

    }

    function PrintIcon($content, $text = null)
    {
        $directory = $this->directory;
        $params = new mosParameters('');
        $params->set('popup', $this->popup);
        $params->set('print', 1);
        $params->set('icons', 1);
        $itemid = $this->itemid;
        $print_link = JPATH_SITE . '/index2.php?option=com_boss&amp;task=show_content&amp;contentid=' . $content->id . '&amp;popup=1&amp;directory=' . $directory . '&amp;Itemid=' . $itemid;
        if (isset($text)) {
            echo "<a href='" . $print_link . "'>$text</a>";
        }
        else
            mosHTML::PrintIcon(null, $params, 0, $print_link);
    }

    /**
     * Writes Email icon
     */
    function EmailIcon($content, $text = null)
    {
        $itemid = $this->itemid;
        $directory = $this->directory;

        if (1) {
            $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=250,directories=no,location=no';
            $link = JPATH_SITE . '/index2.php?option=com_boss&amp;task=emailform&amp;contentid=' . $content->id . "&amp;directory=$directory&amp;Itemid=" . $itemid;

            if (isset($text))
                $image = $text;
            else if (1) {
                $image = mosAdminMenus::ImageCheck('emailButton.png', '/images/M_images/', NULL, NULL, BOSS_CMN_EMAIL, BOSS_CMN_EMAIL);
            } else {
                $image = '&nbsp;' . BOSS_CMN_EMAIL;
            }
            $image = $text;
            ?>
        <a href="<?php echo $link; ?>" target="_blank"
           onclick="window.open('<?php echo $link; ?>','win2','<?php echo $status; ?>'); return false;"
           title="<?php echo BOSS_CMN_EMAIL;?>">
            <?php echo $image; ?></a>
        <?php 
        }
    }

    function displayCategoryImage($content)
    {
        $catid = $content->catid;
        $directory = $this->directory;
        $name = $this->category->title;

        if (($catid == 0) || (!file_exists(JPATH_BASE . '/images/boss/' . $directory . '/categories/' . $catid . 'cat_t.jpg')))
            echo '<img  class="imgheading" src="' . JPATH_SITE . '/templates/com_boss/' . $this->template_name . '/images/default.gif" alt="default" />';
        else
            echo '<img  class="imgheading" src="' . JPATH_SITE . '/images/boss/' . $directory . '/categories/' . $catid . 'cat_t.jpg" alt="' . $name . '" />';
    }

    /**
	 * Вывод названия категории
	 * @param  $content
	 * @param  $type
	 * 		0 - показать только категорию,
	 * 		1 - показать категория с родителем
	 * 		2 - показать только категорию ссылкой,
	 * 		3 - показать категория с родителем ссылками
	 * @return void
	 * Изменения внесены 01.02.2012 GoDr
	 */
	function displayCategoryTitle($content, $type = 0) {
		if ($type > 1) {
			$link_cat = sefRelToAbs("index.php?option=com_boss&amp;task=show_category&amp;catid=" . $content->catid . "&amp;directory=" . $this->directory);
			$link_parent = sefRelToAbs("index.php?option=com_boss&amp;task=show_category&amp;catid=" . $content->parentid . "&amp;directory=" . $this->directory);
			switch ($type) {
				case 0:
					echo $content->cat;
					break;
				case 1:
					echo $content->parent . " / " . $content->cat;
					break;
				case 2:
					echo '<a href="' . $link_cat . '" >' . $content->cat . '</a>';
					break;
				case 3:
					echo '<a href="' . $link_parent . '" >' . $content->parent . "</a> / " . '<a href="' . $link_cat . '" >' . $content->cat . '</a>';
					break;
			}
		}
	}

    function displayContentHits($content)
    {
        echo sprintf(BOSS_VIEWS, $content->views);
    }

    function displayContentDate($content)
    {
        echo jdreorderDate($content->date_created);
    }

    function displayContentBy($content)
    {
        $itemid = $this->itemid;
        $directory = $this->directory;

        if ($content->user_id != 0) {
            echo BOSS_FROM;
            $target = sefRelToAbs("index.php?option=com_boss&amp;task=show_user&amp;userid=" . $content->user_id . "&amp;directory=$directory&amp;Itemid=" . $itemid);

            echo "<a href='" . $target . "'>" . $content->user . "</a>";
        }
        else
            echo BOSS_UNREGISTERED;
    }

    function countFieldsInGroup($groupname)
    {
        if (isset($this->fieldsgroup[$groupname])) {
            return count($this->fieldsgroup[$groupname]);
        }
        else
            return 0;
    }

    function loadFieldsInGroup($content, $groupname, $betweenfields = null, $fieldheader = null, $fieldfooter = null, $private = 0, $titleEnable = 1, $hrefNum = 0, $divider = null)
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        $fieldsgroup = $this->fieldsgroup[$groupname];
        $database = database::getInstance();
        $jDirectoryField = new jDirectoryField($database, $this->directory);
        if (($private == 1) && ($my->id == 0)) {
            echo BOSS_CONTACT_NOT_LOGGED;
            return;
        }
        $i = 0;        

        foreach ($fieldsgroup as $key => $f) {
            //если поле не привязвно ко всем типам контента или к текущему контенту, то пролетаем его
            if(!($f->catsid == ',-1,' || (strpos( $f->catsid, ','.$content->type_content.',' ) !== false))){
                continue;
            }
            $return = $jDirectoryField->getFieldValue($f, $content, $this->field_values, $this->directory, $this->itemid, $this->conf);
            if ($return->value != "") {
                $i++;
                if (($key != 0) && ($betweenfields != null) && ($return != ""))
                    echo $betweenfields;

                if ($fieldheader != null)
                    echo $fieldheader;

                if ($hrefNum > 0 && $hrefNum == $i) {
                    $link = sefRelToAbs('/index.php?option=com_boss&task=show_content&contentid=' . $content->id . '&catid=' . $this->category->id . '&directory=' . $this->directory . '&Itemid=' . $this->itemid);
                    $href = '<a href="' . $link . '">';
                    $hrefEnd = '</a>';
                }
                else {
                    $hrefEnd = $href = '';
                }
                echo $href;
                if ($return->title != "" && $return->value != "" && $titleEnable == 1)
                    echo "<span class=\"field_title\">" . $return->title . "</span> " . $divider
                         . "<span class=\"field_value\">" . $return->value . "</span> ";
                else
                    echo $return->value;
                echo $hrefEnd;

                if ($fieldfooter != null)
                    echo $fieldfooter;
            }
        }
    }

    function displayContentLinkMore($content, $text = null)
    {
        $directory = $this->directory;
        $linkTarget = sefRelToAbs("index.php?option=com_boss&amp;task=show_content&amp;contentid=" . $content->id . "&amp;catid=" . $content->category . "&amp;directory=$directory&amp;Itemid=" . $this->itemid);
        if (isset($text))
            echo '<a href="' . $linkTarget . '">' . $text . '</a>';
        else
            echo '<a href="' . $linkTarget . '">' . BOSS_READ_MORE . '</a>';
    }

    function displayCatImage()
    {

        $directory = $this->directory;
        $catid = $this->category->id;
        $name = $this->category->title;

        if (($catid == 0) || (!file_exists(JPATH_BASE . '/images/boss/' . $directory . '/categories/' . $catid . 'cat.jpg')))
            echo '<img  class="imgheading" src="/templates/com_boss/' . $this->template_name . '/images/default.gif" alt="default" />';
        else
            echo '<img  class="imgheading" src="/images/boss/' . $directory . '/categories/' . $catid . 'cat.jpg" alt="' . $name . '" />';
    }

    function displayCatTitle($showDefImg = 1)
    {

        $directory = $this->directory;
        $catid = $this->category->id;
        $name = $this->category->title;

        if ((($catid == 0) || (!file_exists(JPATH_BASE . '/images/boss/' . $directory . '/categories/' . $catid . 'cat_t.jpg'))) && $showDefImg == 1)
            echo '<img  class="imgheading" src="' . JPATH_SITE . '/templates/com_boss/' . $this->template_name . '/images/default_t.gif" alt="default" />';
        else
            echo '<img  class="imgheading" src="' . JPATH_SITE . '/images/boss/' . $directory . '/categories/' . $catid . 'cat_t.jpg" alt="' . $name . '" />';
        echo $name;
    }

    function displayCatDescription()
    {
        if (isset($this->category->description))
            echo $this->category->description;
    }

    function displayAdvancedSearchLink()
    {
        $catid = $this->category->id;
        $itemid = $this->itemid;
        $directory = $this->directory;
        ?>
    <a href="<?php echo sefRelToAbs("index.php?option=com_boss&amp;task=search&amp;catid=$catid&amp;directory=$directory&amp;Itemid=$itemid");?>"><?php echo BOSS_ADVANCED_SEARCH; ?></a>
    <?php

    }

    function displayOrderOption()
    {
        echo BOSS_ORDER_BY_TEXT;
        $fields = $this->fields;
        $url = $this->url;
        $order = mosGetParam($_REQUEST, 'order', '');
        $direction = mosGetParam($_REQUEST, 'direction', 'ASC');
        $itemid = $this->itemid;
        $direction = ($direction == 'ASC') ? 'DESC' : 'ASC';
        $img = ($direction == 'ASC') ? 'arrow_up.png' : 'arrow_down.png' ;
        $imgUrl = '/templates/com_boss/'.$this->template_name.'/images/'.$img;
        $link = sefRelToAbs($url . "&amp;order=" . $order . "&amp;direction=" . $direction . "&amp;Itemid=" . $itemid);

        if (isset($fields)) {
            $this->jsJumpmenu(); ?>
        <select name="order" size="1" onchange="jumpmenu('parent',this)">
            <option value="<?php echo sefRelToAbs($url . "&amp;order=0&amp;direction=" . $direction. "&amp;Itemid=" . $itemid);?>" <?php
               if ($order == "0") {
                echo "selected='selected'";
            } ?>>
                <?php echo BOSS_DATE; ?>
            </option>

            <?php foreach ($fields as $s) {
            if ($s->sort == 1) {
                ?>
                <option value="<?php echo sefRelToAbs($url . "&amp;order=" . $s->fieldid . "&amp;direction=" . $direction. "&amp;Itemid=" . $itemid);?>" <?php
                if ($order == $s->fieldid) {
                    echo "selected='selected'";
                } ?>>
                    <?php echo jdGetLangDefinition($s->title); ?>
                </option>
                <?php

            }
        }
            ?>
        </select>
        <span>
            <a href="<?php echo $link; ?>"><img src="<?php echo $imgUrl; ?>" /></a>
        </span>
        <?php 
        }
    }

    function displayPagesCounter()
    {
        echo $this->tasknav->writePagesCounter();
    }

    function displayPagesLinks()
    {
        echo $this->tasknav->writePagesLinks($this->nav_link);
    }

    function displayContents($listTemplate='default')
    {
        if (isset($this->contents)) {

            $mainframe = mosMainFrame::getInstance();
            $my = $mainframe->getUser();
            $my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
            
            foreach($this->contents as $content) {
                
                //парва пользователей
                if($this->conf->allow_rights){
                    if(!empty($content->rights)){
                        $this->rights->bind_rights($content->rights);
                    }
                    else{
                        $this->rights->bind_rights(@$this->conf->rights);
                    }
                    
                    $this->perms = $this->rights->loadRights(array('show_my_content', 'show_all_content', 'edit_user_content', 'edit_all_content', 'delete_user_content', 'delete_all_content'), $my->groop_id);                    
                    
                    if(!($this->perms->show_all_content || ($this->perms->show_my_content && $my->id == $content->user_id))){
                        continue;
                    }
                }
                if($listTemplate=='frontpage'){
                    $tpl = 'frontpage_list_item.php';
                }
                else if($content->featured == 1){
                    $tpl = 'featured_content.php';
                    //$tpl = $this->viewsPlugin->contentViews($content, $this->views);
                }
                else{
                    $tpl = 'list_item.php';
                }
                $pathToTemplate = JPATH_BASE .'/templates/com_boss/'.$this->template_name.'/'.$tpl;
                include( $pathToTemplate );
            }
        }
    }
    


    function displayPathway()
    {
        global $cur_template;
        $paths = $this->paths;
        $pathway = "";
        $nb = count($paths);
        $task = mosGetParam($_REQUEST, 'task', '');

        for ($i = $nb - 1; $i > 0; $i--) {
            $pathway .= '<a href="' . $paths[$i]->link . '">' . $paths[$i]->text . '</a>';
            $filenamearrow = JPATH_BASE . "/templates/$cur_template/images/arrow.png";
            if (file_exists($filenamearrow))
                $pathway .= ' <img src="' . JPATH_SITE . '/templates/' . $cur_template . '/images/arrow.png" alt="arrow" /> ';
            else
                $pathway .= ' <img src="' . JPATH_SITE . '/templates/com_boss/' . $this->template_name . '/images/arrow.png" alt="arrow" /> ';
        }

        if ($task == 'show_content')
            $pathway .= '<a href="' . $paths[0]->link . '">' . $paths[0]->text . '</a>';
        else
            $pathway .= '<span>' . $paths[0]->text . '</span>';

        echo $pathway;
    }

    function displaySubCats()
    {
        $subcats = $this->subcats;
        $nb = count($subcats);
        if ($nb != 0) {
            for ($i = 0; $i < $nb; $i++) {
                $class = ($i == ($nb-1)) ? 'class="last"' : '';
                echo '<a ' . $class . ' href="' . $subcats[$i]->link . '">' . $subcats[$i]->text . '</a>';
            }
            echo '<div style="clear:both;"></div>';
        }
    }

    function displayShowOther($content)
    {
        global $itemid;
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();

        $row = null;
        $directory = $this->directory;
        $update_possible = 0;
        $itemid = $this->itemid;

        if ($content->userid != 0) {
            $target = sefRelToAbs("index.php?option=com_boss&amp;task=show_user&amp;userid=" . $content->userid . "&amp;directory=$directory&amp;Itemid=" . $itemid);
            echo "<a href='" . $target . "'><b>" . BOSS_SHOW_OTHERS . $content->user . "</b></a>";

            if (($my->id == $row->userid) && ($update_possible == 1)) {
                ?>
            <div>
                <?php
                    $target = sefRelToAbs("index.php?option=com_boss&amp;Itemid=$itemid&amp;task=write_content&amp;contentid=$content->id" . "&amp;directory=$directory&amp;Itemid=" . $itemid);
                echo "<a href='" . $target . "'>" . BOSS_CONTENT_EDIT . "</a>";
                echo "&nbsp;";
                $target = sefRelToAbs("index.php?option=com_boss&amp;Itemid=$itemid&amp;task=delete_content&amp;contentid=$content->id" . "&amp;directory=$directory&amp;Itemid=" . $itemid);
                echo "<a href='" . $target . "'>" . BOSS_CONTENT_DELETE . "</a>";
                ?>
            </div>
                <?php

            }
        }
    }

    function displayPms($content, $private = 0)
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        $conf = $this->conf;
        $directory = $this->directory;
        $itemid = $this->itemid;

        if (($private == 1) && ($my->id == 0)) {
            return;
        }

        if (($content->userid != 0) && ($conf->allow_contact_by_pms == 1)) {
            $pmsText = sprintf(BOSS_PMS_FORM, $content->user);
            $pmsForm = sefRelToAbs("index.php?option=com_boss&amp;task=show_message_form&amp;mode=1&amp;contentid=" . $content->id . "&amp;directory=$directory&amp;Itemid=" . $itemid);
            echo '<a href="' . $pmsForm . '">' . $pmsText . '</a>';
        }
    }

    function displayContent($content, $unique = 1)
    {
        $conf = $this->conf;
        include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/content.php');

        if ($unique == 1) {
            ?>
        <div class="back_button">
            <a href='javascript:history.go(-1)'><?php echo BOSS_BACK_TEXT; ?></a>
        </div>
        <?php

        } else {
            echo "<br />";
        }
    }

    function displayFormFields($mode = "write")
    {

        $content = $this->content;
        $user = $this->user;
        $fields = $this->fields;
        $field_values = $this->field_values;
        $catid = $this->content_type;
        $directory = $this->directory;
        $errorMsg = $this->errorMsg;
        $plugins = BossPlugins::get_plugins($directory, 'fields');
        if (($mode == "write") && ((!isset($catid)) || ($catid == 0))) {

            return;
        }

        if (isset($fields)) {
            echo '<label for="name">' . BOSS_FORM_MESSAGE_TITLE . '</label>
                  <input class="boss_required" mosreq="1" id="name" type="text" moslabel="' . BOSS_FORM_MESSAGE_TITLE . '" name="name" size="0" maxlength="100" value="' . @$content->name . '"> <br /> ';
            foreach ($fields as $field) {

                if ((strpos($field->catsid, ",$catid,") !== false) || (strpos($field->catsid, ",-1,") !== false)) {
                    if ($field->profile == 1)
                        continue;

                    $return = jDirectoryField::getFieldForm($field, $content, $user, $field_values, $directory, $plugins, $mode);

                    $title = $return->title;
                    $input = $return->input;
                    $fieldname = $field->name;

                    include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write_field.php');
                }
            }
        }
    }

    function displayWarningNoAccount()
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        if (($this->conf->submission_type == 2) && ($my->id == "0")) {
            echo BOSS_WARNING_NEW_CONTENT_NO_ACCOUNT;
            ;
        }
    }

    function displayErrorMsg()
    {
        switch ($this->errorMsg) {
            case "bad_password":
                echo "<div class=\"message info\">" . BOSS_BAD_PASSWORD . "</div>";
                break;
            case "email_already_used":
                echo "<div class=\"message info\">" . BOSS_EMAIL_ALREADY_USED . "</div>";
                break;
            case "file_too_big":
                echo "<div class=\"message info\">" . BOSS_FILE_TOO_BIG . "</div>";
            case "bad_captcha":
                echo "<div class=\"message info\">" . BOSS_BAD_CAPTCHA . "</div>";
        }
    }

    function displayFormType()
    {
        if ($this->conf->isUpdateMode) {
            echo BOSS_CONTENT_EDIT;
        }
        else {
            echo BOSS_CONTENT_WRITE;
        }
    }

    function displayCategoriesSelectSearch()
    {
        $itemid = $this->itemid;
        $catid = $this->category->id;
        $categories = $this->categories;
        $directory = $this->directory;

        $this->jsJumpmenu();
        ?>
    <select name='category_choose' onchange="jumpmenu('parent',this)">
        <option value="<?php echo sefRelToAbs("index.php?option=com_boss&amp;task=search&amp;catid=0&amp;directory=$directory&amp;Itemid=$itemid"); ?>" <?php if ($catid == 0) echo 'selected="selected"'; ?>><?php echo BOSS_MENU_ALL_CONTENTS; ?></option>
        <?php
            $linkoption = "&amp;task=search";
        $this->selectCategories(0, "", $categories, $catid, 1, $itemid, $linkoption);
        ?>
    </select>
        <?php

    }

    function displayCategoriesSelect($task='write_content')
    {
        $itemid = $this->itemid;
        $catid = $this->category->id;
        $content_id = $this->content->id;
        $categories = $this->categories;
        $this->jsJumpmenu();
        ?>
    <select class='boss_required inputbox' name='category' mosreq='1' mosLabel="<?php echo BOSS_FORM_CATEGORY; ?>">
        <?php

        if ((@$content_id) && ($content_id != ""))
            $linkoption = "&amp;task=$task&amp;contentid=$content_id";
        else
            $linkoption = "&amp;task=$task";
        
            echo "<option value=''>" . BOSS_SELECT_CATEGORY . "</option>";
        $this->selectCategories(0, "", $categories, $catid, $this->conf->root_allowed, $itemid, $linkoption);
        ?>
    </select><?php

    }
    
    function displayContentTypesSelect($task='write_content')
    {
        $itemid = $this->itemid;
        $content_types = $this->content_types;
        $content_id = $this->content->id;
        
        $this->jsJumpmenu();
        ?>
    <select class='boss_required inputbox' name='content_types_choose' onchange="jumpmenu('parent',this)">
        <?php

        if ((@$content_id) && ($content_id != ""))
            $linkoption = "&task=$task&contentid=$content_id";
        else
            $linkoption = "&task=$task";
            ?>
            <option value='<?php echo sefRelToAbs("index.php?option=com_boss$linkoption&amp;content_types=0&amp;directory=" . $this->directory . "&amp;Itemid=$itemid"); ?>'><?php echo BOSS_SELECT_CONTENT_TYPE2; ?></option>
            <?php
            foreach ($content_types as $row) {
            
               
                        ?>
                    <option value="<?php echo sefRelToAbs("index.php?option=com_boss$linkoption&amp;content_types=" . $row->id . "&amp;directory=" . $this->directory . "&amp;Itemid=$itemid"); ?>" 
                    <?php if ($row->id == $this->content_type) {
                        echo "selected='selected'";
                    } ?>>
                        <?php echo $row->name; ?>
                    </option>
                    <?php

                
              
            }
        ?>
    </select>
            <?php

    }

    function isCategorySelected()
    {
        return $this->category->id;
    }
    
    function isContentTypeSelected()
    {
        return mosGetParam($_REQUEST,  'content_types' , 0 );
    }
    
    function isContentCaptchaActivated()
    {
        return $this->conf->secure_new_content;
    }
    
    function displayCaptchaImage()
    {
        ?>
    <img id="captchaimg" alt="<?php echo _PRESS_HERE_TO_RELOAD_CAPTCHA?>"
         onclick="document.saveForm.captchaimg.src='<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>&' + new String(Math.random())"
         src="<?php echo JPATH_SITE; ?>/includes/libraries/kcaptcha/index.php?session=<?php echo mosMainFrame::sessionCookieName() ?>"/>
    <?php

    }
    
    private function displayCaptchaInput()
    {
        echo '<input class="boss_required" moslabel="'. BOSS_SECURITY .'" type="text" name="captcha" id="captcha" mosreq="1" value="" size="20" />';
    }
    
    function displayFormBegin($mode = "write")
    {
        $directory = $this->directory;
        $itemid = $this->itemid;

        if ($mode == "write")
            $target = sefRelToAbs("index.php?option=com_boss&amp;task=save_content&amp;directory=$directory&amp;Itemid=" . $itemid);
        else
            $target = sefRelToAbs("index.php?option=com_boss&amp;task=send_arrange&amp;directory=$directory&amp;Itemid=" . $itemid);
        $catid = $this->category->id;
        echo '<form action="' . $target . '" method="post" name="saveForm" enctype="multipart/form-data" onsubmit="return submitbutton(this)">';
        echo "<input type='hidden' name='category' value='" . $catid . "' />";
    }

    function displayFormEnd($mode = "write")
    {
        $content_type = mosGetParam($_REQUEST,  'content_types' , 0 );
        $content = $this->content;
        $conf = $this->conf;
        echo '<input type="hidden" name="gflag" value="0" />';
        if (isset($content->date_created))
            echo "<input type='hidden' name='date_created' value='" . $content->date_created . "' />";
        echo "<input type='hidden' name='isUpdateMode' value='$conf->isUpdateMode' />";
        echo "<input type='hidden' name='id' value='$content->id' />";
        echo "<input type='hidden' name='contentid' value='$content->id' />";
        if (!empty($content->userid))
            echo "<input type='hidden' name='userid' value='" . $content->userid . "' />";
        ?>
        <input type="hidden" name="type_content" value="<?php echo $content_type; ?>" />
        <input type="hidden" name="directory" value="<?php echo $this->directory; ?>">
        <?php
        echo "</form>";
    }

    function isAccountCreation()
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
        if (($this->conf->submission_type == 0) && ($my->id == 0)) {
            return 1;
        }
        else
            return 0;
    }

    function displayAccountCreationFields()
    {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();

        $catid = $this->content_type;
        $conf = $this->conf;
        $content = $this->user;
        $fields = $this->fields;


        if ((!isset($catid)) || ($catid == 0)) {
            return;
        }

        /* Submission_type == 0->Account Creation with content posting */
        if (($conf->submission_type == 0) && ($my->id == 0)) {
            echo BOSS_AUTOMATIC_ACCOUNT . "<br />"; //TODO

            if (isset($content->username)) {
                $username = $content->username;
                $password = $content->password;
                $email = $content->email;
                $name = $content->name;
                $style = 'style="background-color:#ff0000"'; //TODO
            }
            else {
                $username = "";
                $password = "";
                $email = "";
                $name = "";
                $style = "";
            }

            if (isset($content->firstname))
                $firstname = $content->firstname;
            else
                $firstname = "";

            if (isset($content->middlename))
                $middlename = $content->middlename;
            else
                $middlename = "";

            $namestyle = 1;

            $fieldname = "username";
            $title = BOSS_UNAME;
            $input = "<input $style class='boss_required' mosReq='1' id='username' type='text' mosLabel='" . htmlspecialchars(BOSS_UNAME, ENT_QUOTES) . "' name='username' size='20' maxlength='20' value='$username' />";
            include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write_field.php');

            $fieldname = "password";
            $title = BOSS_PASS;
            $input = "<input $style class='boss_required' mosReq='1' id='password' type='password' mosLabel='" . htmlspecialchars(BOSS_PASS, ENT_QUOTES) . "' name='password' size='20' maxlength='20' value='$password' />";
            include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write_field.php');

            $emailField = false;
            $nameField = false;
            for ($i = 0, $total = count($fields); $i < $total; $i++) {
                if (($fields[$i]->name == "email") && ((strpos($fields[$i]->catsid, ",$catid,") !== false) || (strpos($fields[$i]->catsid, ",-1,") !== false))) {
                    $emailField = true;
                    /* Force required */
                    $fields[$i]->required = 1;
                }
                else if (($fields[$i]->name == "name") && ((strpos($fields[$i]->catsid, ",$catid,") !== false) || (strpos($fields[$i]->catsid, ",-1,") !== false))) {
                    $nameField = true;
                    /* Force required */
                    $fields[$i]->required = 1;
                }
                else if (($namestyle >= 2) && ($fields[$i]->name == "firstname") && ((strpos($fields[$i]->catsid, ",$catid,") !== false) || (strpos($fields[$i]->catsid, ",-1,") !== false))) {
                    $firstnameField = true;
                    /* Force required */
                    $fields[$i]->required = 1;
                }
                else if (($namestyle == 3) && ($fields[$i]->name == "middlename") && ((strpos($fields[$i]->catsid, ",$catid,") !== false) || (strpos($fields[$i]->catsid, ",-1,") !== false))) {
                    $middlenameField = true;
                    /* Force required */
                    $fields[$i]->required = 1;
                }
            }

            if (($namestyle >= 2) && ($firstnameField == false)) {
                $fieldname = "firstname";
                $title = CONTENTSMANAGER_FNAME;
                $input = "<input $style class='boss_required' mosReq='1' id='firstname' type='text' mosLabel='" . htmlspecialchars(CONTENTSMANAGER_FNAME, ENT_QUOTES) . "' name='firstname' size='20' maxlength='20' value='$firstname' /><br />\n";
                include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write_field.php');
            }
            if (($namestyle == 3) && ($middlenameField == false)) {
                $fieldname = "middlename";
                $title = CONTENTSMANAGER_MNAME;
                $input = "<input $style class='boss_required' mosReq='1' id='middlename' type='text' mosLabel='" . htmlspecialchars(CONTENTSMANAGER_MNAME, ENT_QUOTES) . "' name='middlename' size='20' maxlength='20' value='$middlename' /><br />\n";
                include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write_field.php');
            }
            if ($nameField == false) {
                $fieldname = "name";
                $title = _NAME;
                $input = "<input $style class='boss_required' mosReq='1' id='name' type='text' mosLabel='" . htmlspecialchars(_NAME, ENT_QUOTES) . "' name='name' size='20' maxlength='20' value='$name' /><br />\n";
                include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write_field.php');
            }
            if ($emailField == false) {
                $fieldname = "email";
                $title = _EMAIL;
                $input = "<input $style class='boss_required' mosReq='1' id='email' type='text' mosLabel='" . htmlspecialchars(_EMAIL, ENT_QUOTES) . "' name='email' size='20' maxlength='20' value='$email' /><br />\n";
                include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/write_field.php');
            }
        }
    }

    /**
     * @param  $name - имя поля
     * @return
     */
    function displayProfileField($name)
    {
        $user = $this->user;
        $directory = $this->directory;

        switch ($name) {
            case "username":
                $title = BOSS_UNAME;
                $input = '<input class="inputbox" type="text" name="username" value="' . $user->username . '" size="40" />';
                break;
            case "password":
                $title = BOSS_PASS;
                $input = '<input class="inputbox" type="password" name="password" value="" size="40" />';
                break;
            case "vpassword":
                $title = BOSS_VPASS;
                $input = '<input class="inputbox" type="password" name="verifyPass" size="40" />';
                break;
            case "name":
                $title = BOSS_PROFILE_NAME;
                $input = '<input class="inputbox" type="text" name="name" value="' . $user->name . '" size="40" />';
                break;
            case "email":
                $title = BOSS_EMAIL;
                $input = '<input class="inputbox" type="text" name="email" value="' . $user->email . '" size="40" />';
                break;
            default:
                return;
        }
        include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/profile_item.php');
    }

    function displayCustomProfileFields()
    {
        $fields = $this->fields;
        $field_values = $this->field_values;
        $user = $this->user;
        $directory = $this->directory;
        $plugins = $this->plugins;
        if (isset($fields)) {
            foreach ($fields as $f) {
                if (($f->name != "name") && ($f->name != "email")) {
                    $value = "\$user->" . $f->name;
                    eval("\$value = \"$value\";");
                    $value = jdGetLangDefinition($value);
                    $title = jdGetLangDefinition($f->title);
                    $input = $plugins[$f->type]->getFormDisplay($directory, $user, $f, $field_values);
                    include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/profile_item.php');
                }
            }
        }
    }

    /**  подключает шаблон показа профиля пользователя.
     * @param  $content - объект контента
     * @return
     */
    function showProfile($content)
    {
        if (!isset($content->user_fio))
            return;
        else
            include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/profile_show.php');
    }

    /**  показывает поля профиля пользователя из таблицы Joostina #__users
     * @param  $name                - имя поля
     * @param  $content             - объект контента
     * @param string $emailView     - вид вывода почты (форма отправки, ссылка маайлто, картинка)
     * @return
     */
    function showProfileField($name, $content, $emailView = 'emailForm')
    {
        $directory = $this->directory;
        $itemid = $this->itemid;
        switch ($name) {
            case "username":
                $title = BOSS_UNAME;
                $input = $content->user;
                break;
            case "name":
                $title = BOSS_PROFILE_NAME;
                $input = $content->user_fio;
                break;
            case "email":
                $title = BOSS_EMAIL;
                switch ($emailView) {
                    case 'emailForm':
                        $emailForm = sefRelToAbs("index.php?option=com_boss&amp;task=show_message_form&amp;mode=0&amp;contentid=" . $content->id . "&amp;directory=$directory&amp;Itemid=" . $itemid);
                        $input = '<a href="' . $emailForm . '">' . BOSS_SEND_EMAIL_BUTTON . '</a>';
                        break;
                    case 'img':
                        $input = Txt2Png($content->user_email, $directory);
                        break;
                    default:
                        $input = "<a href='mailto:" . $content->user_email . "'>" . cutLongWord($content->user_email) . "</a>";
                        break;
                }
                break;
            default:
                return;
        }

        include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/profile_item.php');
    }

    /**  показывает дополнительные поля пользователя  из #__boss_(ид каталога)_profile
     * @param  $content         - объект контента
     * @param  $profileFields   - объект дополнительных полей профиля пользователя
     * @return void
     */
    function showCustomProfileFields($content, $profileFields)
    {

        if (isset($profileFields)) {
            foreach ($profileFields as $f) {
                $fname = $f->name;
                $value = $content->$fname;

                $value = jdGetLangDefinition($value);
                $title = jdGetLangDefinition($f->title);
                $input = $value;
                include(JPATH_BASE . '/templates/com_boss/' . $this->template_name . '/profile_item.php');
            }
        }
    }

    function displayWriteLink()
    {
        $itemid = $this->itemid;
        if (isset($this->category->id))
            $catid = $this->category->id;
        $directory = $this->directory;

        if (!isset($catid))
            $link_write_content = sefRelToAbs("index.php?option=com_boss&amp;task=write_content&amp;directory=$directory&amp;Itemid=$itemid");
        else
            $link_write_content = sefRelToAbs("index.php?option=com_boss&amp;task=write_content&amp;catid=$catid&amp;directory=$directory&amp;Itemid=$itemid");
        echo '<a href="' . $link_write_content . '">' . BOSS_MENU_WRITE . '</a>';
    }

    function displayDirectoryName()
    {
        echo $this->directory_name;
    }

    function displayAllContentsLink()
    {
        $itemid = $this->itemid;
        $directory = $this->directory;
        $link_show_all = sefRelToAbs("index.php?option=com_boss&amp;task=show_all&amp;directory=$directory&amp;Itemid=$itemid");
        echo '<a href="' . $link_show_all . '">' . BOSS_MENU_ALL_CONTENTS . '</a>';
    }

    function displayProfileLink()
    {
        $itemid = $this->itemid;
        $directory = $this->directory;
        $link_show_profile = sefRelToAbs("index.php?option=com_boss&amp;task=show_profile&amp;directory=$directory&amp;Itemid=$itemid");

        echo '<a href="' . $link_show_profile . '">' . BOSS_MENU_PROFILE . '</a>';
    }

    function displayUserContentsLink()
    {
        $itemid = $this->itemid;
        $directory = $this->directory;
        $link_show_user = sefRelToAbs("index.php?option=com_boss&amp;task=show_user&amp;directory=$directory&amp;Itemid=$itemid");

        echo '<a href="' . $link_show_user . '">' . BOSS_MENU_USER_CONTENTS . '</a>';
    }

    function displaySearchLink()
    {
        $itemid = $this->itemid;
        $directory = $this->directory;
        $link_show_search = sefRelToAbs("index.php?option=com_boss&amp;task=search&amp;directory=$directory&amp;Itemid=$itemid");

        echo '<a href="' . $link_show_search . '">' . BOSS_SEARCH . '</a>';
    }

    function displayRulesLink()
    {
        $directory = $this->directory;
        $itemid = $this->itemid;

        $link_show_rules = sefRelToAbs("index.php?option=com_boss&amp;task=show_rules&amp;directory=$directory&amp;Itemid=$itemid");
        echo '<a href="' . $link_show_rules . '">' . BOSS_MENU_RULES . '</a>';
    }

    function displayFrontText()
    {
        echo stripslashes($this->conf->fronttext);
    }

    function displayCategories($showChildren=1, $showDesc=1, $showImg=1)
    {
        $this->recurseCategories(0, 0, $this->categories, $this->itemid, $showChildren, $showDesc, $showImg);
    }

    function displayLastContents()
    {

        $contents = $this->contents;
        $nb_images = $this->conf->nb_images;
        $directory = $this->directory;
        $itemid = $this->itemid;

        foreach ($contents as $row) {
            ?>
        <div class="boss_content_box">
            <?php
                    $linkTarget = sefRelToAbs("index.php?option=com_boss&amp;task=show_content&amp;contentid=" . $row->id . "&amp;catid=" . $row->category . "&amp;directory=$directory&amp;Itemid=" . $itemid);
            $ok = 0;
            $i = 1;
            while (!$ok) {
                if ($i < $nb_images + 1) {
                    $ext_name = chr(ord('a') + $i - 1);
                    $pic = JPATH_BASE . "/images/boss/$directory/contents/" . $row->id . $ext_name . "_t.jpg";
                    if (file_exists($pic)) {
                        echo "<a href='" . $linkTarget . "'><img src='" . JPATH_SITE . "/images/boss/$directory/contents/" . $row->id . $ext_name . "_t.jpg' alt='" . htmlspecialchars(stripslashes($row->name), ENT_QUOTES) . "' border='0' /></a>";
                        $ok = 1;
                    }
                }
                else if ($nb_images != 0) {
                    echo "<a href='" . $linkTarget . "'><img src='" . JPATH_SITE . "/templates/com_boss/" . $this->template_name . "/images/nopic.gif' alt='nopic' border='0' /></a>";
                    $ok = 1;
                }
                else {
                    $ok = 1;
                }
                $i++;
            }

            echo "<div class=\"boss_content_title\"><a href='" . $linkTarget . "'>" . stripslashes($row->name) . "</a></div>";
            echo "<div class=\"boss_cat\">(" . $row->parent . " / " . $row->cat . ")</div>";
            echo "<div class=\"boss_content_date\">" . jdreorderDate($row->date_created) . "</div>";
            ?>
        </div>
            <?php

        }
        ?>
    <?php

    }

    function displayLostPasswordLink()
    {
        $link_lostpassword = sefRelToAbs("index.php?option=com_users&task=lostPassword");
        echo '<a href="' . $link_lostpassword . '">' . BOSS_LOST_PASSWORD . "</a>";
    }

    function displayCreateAccountLink()
    {
        $link_create = sefRelToAbs("index.php?option=com_users&task=register");
        echo '<a href="' . $link_create . '">' . BOSS_CREATE_ACCOUNT . "</a>";
    }

    function displayAlphaIndex($directory, $itemid = 0, $separator = '<br />', $ru = true, $en = true, $num = true)
    {
        $data = boss_helpers::loadAlphaIndex($directory);

        if ($ru) {
            $this->alpaPrint($directory, $itemid, $data['ruAlf'], $data['alphaContent']);
        }

        if ($en) {
            echo $separator;
            $this->alpaPrint($directory, $itemid, $data['enAlf'], $data['alphaContent']);
        }

        if ($num) {
            echo $separator;
            $this->alpaPrint($directory, $itemid, $data['numeric'], $data['alphaContent']);
        }

    }

    function alpaPrint($directory, $itemid, $alphabet, $alphaContent)
    {
        $order = mosGetParam($_REQUEST, 'order', '');
        $order = ($order == '') ? '' : '&order='. $order;
        $direction = mosGetParam($_REQUEST, 'direction', '');
        $direction = ($direction == '') ? '' : '&direction='. $direction;

        foreach ($alphabet as $alf) {
            if (in_array($alf, $alphaContent)) {
                echo '<a class="alphaindex" href="' . sefRelToAbs('index.php?option=com_boss&task=search_alpha&directory=' . $directory . '&alpha=' . urlencode($alf) . $order . $direction . '&Itemid=' . $itemid) . '">' . $alf . '</a> ';
            }
            else {
                echo $alf . ' ';
            }
        }
    }

        function show_expiration($content,$conf,$itemid)
	{
            $time = boss_helpers::DateAdd('d', $conf->content_duration, $content->date_created);
            $target = sefRelToAbs("index.php?option=com_boss&task=extend_expiration&contentid=$content->id&Itemid=$itemid");
	?>
            <div class="renew">
		<?php echo sprintf(BOSS_RENEW_CONTENT_QUESTION,$content->name,  $time); ?>
            </div>
            <form action="<?php echo $target;?>" method="post" name="adminForm" enctype="multipart/form-data">
                    <span class='button'>
                        <input type='submit' class='button' value='<?php echo BOSS_RENEW_CONTENT; ?>' />
                    </span>
            </form>
	<?php

	}
}