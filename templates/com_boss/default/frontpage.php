
<div class="frontpage_content">
  <?php
        if (isset($this->contents)) {

            global $my;
            $my->groop_id = (isset($my->groop_id)) ? $my->groop_id : 0;
            //путь до шаблона контента
            $pathToTemplate = JPATH_BASE .'/templates/com_boss/'.$this->template_name.'/frontpage_list_item.php';
            $class = 'list_item_50';
            $i=1;
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

                include( $pathToTemplate );

                if($i == 2){
                    $i = 1;
                    ?>
                    <br style="clear: both;" />
                    <?php
                }
                else{
                    $i++;
                }

            }
            ?>
                <br style="clear: both;" />
            <?php
        }
    ?>
</div>

<p align="center">
<?php echo $this->displayPagesLinks(); ?>
</p>