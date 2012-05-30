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

/**
 * Users Table Class
 *
 * Provides access to the jos_user table
 * @package Joostina
 */
class mosUser extends mosDBTable {
    /**
     @var int Unique id*/
    var $id = null;
    /**
     @var string The users real name (or nickname)*/
    var $name = null;
    /**
     @var string The login name*/
    var $username = null;
    /**
     @var string email*/
    var $email = null;
    /**
     @var string MD5 encrypted password*/
    var $password = null;
    /**
     @var string*/
    var $usertype = null;
    /**
     @var int*/
    var $block = null;
    /**
     @var int*/
    var $sendEmail = null;
    /**
     @var int The group id number*/
    var $gid = null;
    /**
     @var datetime*/
    var $registerDate = null;
    /**
     @var datetime*/
    var $lastvisitDate = null;
    /**
     @var string activation hash*/
    var $activation = null;
    /**
     @var string*/
    var $params = null;
    /**
     @var string*/
    var $avatar = null;

    /**
     * @param database A database connector object
     */
    function mosUser($database) {
        $this->mosDBTable('#__users','id',$database);
    }

    /**
     * Validation and filtering
     * @return boolean True is satisfactory
     */
    function check() {

        // Validate user information
        if(trim($this->name) == '') {
            $this->_error = addslashes(_REGWARN_NAME);
            return false;
        }

        if(trim($this->username) == '') {
            $this->_error = addslashes(_REGWARN_USERNAME);
            return false;
        }

        // check that username is not greater than 25 characters
        $username = $this->username;
        if(strlen($username) > 25) {
            $this->username = substr($username,0,25);
        }

        // check that password is not greater than 50 characters
        $password = $this->password;
        if(strlen($password) > 50) {
            $this->password = substr($password,0,50);
        }

        if(preg_match("/[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]/",$this->username) || strlen($this->username) <3) {
            $this->_error = sprintf(addslashes(_VALID_AZ09),addslashes(_PROMPT_USERNAME),2);
            return false;
        }

        if((trim($this->email == "")) || (preg_match("/[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}/",$this->email) == false)) {
            $this->_error = addslashes(_REGWARN_MAIL);
            return false;
        }

        // check for existing username
        $query = "SELECT id FROM #__users WHERE username = ".$this->_db->Quote($this->username)." AND id != ".(int)$this->id;
        $this->_db->setQuery($query);
        $xid = intval($this->_db->loadResult());
        if($xid && $xid != intval($this->id)) {
            $this->_error = addslashes(_REGWARN_INUSE);
            return false;
        }

        if(Jconfig::getInstance()->config_uniquemail) {
            // check for existing email
            $query = "SELECT id FROM #__users WHERE email = ".$this->_db->Quote($this->email)." AND id != ".(int)$this->id;
            $this->_db->setQuery($query);
            $xid = intval($this->_db->loadResult());
            if($xid && $xid != intval($this->id)) {
                $this->_error = addslashes(_REGWARN_EMAIL_INUSE);
                return false;
            }
        }

        return true;
    }

    function store($updateNulls = false) {
        global $migrate;

        $acl = gacl::getInstance();

        $section_value = 'users';

        $k = $this->_tbl_key;
        $key = $this->$k;
        if($key && !$migrate) {
            // existing record
            $ret = $this->_db->updateObject($this->_tbl,$this,$this->_tbl_key,$updateNulls);
            // syncronise ACL
            // single group handled at the moment
            // trivial to expand to multiple groups
            $groups = $acl->get_object_groups($section_value,$this->$k,'ARO');
            if(isset($groups[0])) {
                $acl->del_group_object($groups[0],$section_value,$this->$k,'ARO');
            }
            $acl->add_group_object($this->gid,$section_value,$this->$k,'ARO');

            $object_id = $acl->get_object_id($section_value,$this->$k,'ARO');
            $acl->edit_object($object_id,$section_value,$this->_db->getEscaped($this->name),$this->$k,0,0,'ARO');
        } else {
            // new record
            $ret = $this->_db->insertObject($this->_tbl,$this,$this->_tbl_key);
            // syncronise ACL
            $acl->add_object($section_value,$this->_db->getEscaped($this->name),$this->$k,null,null,'ARO');
            $acl->add_group_object($this->gid,$section_value,$this->$k,'ARO');
        }
        if(!$ret) {
            $this->_error = strtolower(get_class($this))."::store failed <br />".$this->_db->getErrorMsg();
            return false;
        } else {
            return true;
        }
    }

    function delete($oid = null) {

        $acl = gacl::getInstance();

        $k = $this->_tbl_key;
        if($oid) {
            $this->$k = intval($oid);
        }
        $aro_id = $acl->get_object_id('users',$this->$k,'ARO');
        $acl->del_object($aro_id,'ARO',true);

        $query = "DELETE FROM $this->_tbl WHERE $this->_tbl_key = ".(int)$this->$k;
        $this->_db->setQuery($query);

        if($this->_db->query()) {
            // cleanup related data

            // :: private messaging
            $query = "DELETE FROM #__messages_cfg WHERE user_id = ".(int)$this->$k;
            $this->_db->setQuery($query);
            if(!$this->_db->query()) {
                $this->_error = $this->_db->getErrorMsg();
                return false;
            }
            $query = "DELETE FROM #__messages WHERE user_id_to = ".(int)$this->$k;
            $this->_db->setQuery($query);
            if(!$this->_db->query()) {
                $this->_error = $this->_db->getErrorMsg();
                return false;
            }

            return true;
        } else {
            $this->_error = $this->_db->getErrorMsg();
            return false;
        }
    }

    /**
     * Gets the users from a group
     * @param string The value for the group (not used 1.0)
     * @param string The name for the group
     * @param string If RECURSE, will drill into child groups
     * @param string Ordering for the list
     * @return array
     */
    function getUserListFromGroup($value,$name,$recurse = 'NO_RECURSE',$order ='name') {

        $acl = gacl::getInstance();

        $group_id = $acl->get_group_id($name, 'ARO');
        $objects = $acl->get_group_objects($group_id,'ARO','RECURSE');

        if(isset($objects['users'])) {
            mosArrayToInts($objects['users']);
            $gWhere = '(id ='.implode(' OR id =',$objects['users']).')';

            $query = "SELECT id AS value, name AS text FROM #__users WHERE block = '0' AND ".$gWhere." ORDER BY ".$order;
            $this->_db->setQuery($query);
            $options = $this->_db->loadObjectList();
            return $options;
        } else {
            return array();
        }
    }
    /**
     * функция получения аватара пользователя, возвращает путь к изображения аватара от корня сайта
     */
    function get_avatar($user) {

        $mainframe = mosMainFrame::getInstance();

        $avatar_file = JPATH_BASE.'/images/avatars/'.$user->avatar;

        if(is_file($avatar_file)) {
            $img = 'images/avatars/'.$user->avatar;
        }else {
            $img = 'images/avatars/none.jpg';
        }
        return $img;
    }

	public static function get_link($user) {
        $mainframe = mosMainFrame::getInstance();

        $url = 'index.php?option=com_users&task=profile&user='.$user->id;
        return sefRelToAbs($url);
    }

    /**
     * Получение статуса пользователя
     */
    function get_user_status($uid) {

        $qq = "SELECT userid FROM #__session WHERE userid=$uid AND guest=0";
        $this->_db->setQuery( $qq,0,1 );
        if($this->_db->loadResult()) {
            return 1;
        }else {
            return 0;
        }
    }

    /**
     * Получение дополнительных данных пользователя
     */
    function get_user_extra($uid=null) {
        $uid = ($uid) ? $uid : $this->id;

        $qq = "SELECT * FROM #__users_extra WHERE user_id = $uid";
        $r = null;
        $this->_db->setQuery( $qq );
        $this->_db->loadObject($r);
        return $r;
    }

    /**
     * Смена аватара
     */

    function update_avatar($id = null, $img, $del=null) {

        $val = $img;
        if($del) $val = '';

        if(!$id) {
            $sql = 'UPDATE #__users SET avatar = \''.$val.'\' WHERE avatar="'.$img.'"';
        }else {
            $sql = 'UPDATE #__users SET avatar = \''.$val.'\' WHERE id='.$id;
        }

        $this->_db->setQuery($sql);
        $this->_db->query();

        if($del) {
            if(file_exists($file_name = JPATH_BASE.DS.'images'.DS.'avatars'.DS.$img)) {
                unlink($file_name);
            }
        }
    }

    function send_mail_to_user($email_info) {
        return mosMail($email_info['adminEmail'],
                $email_info['adminName'],
                $email_info['email'],
                $email_info['subject'],
                $email_info['message']);
    }

    function send_mail_to_admins($email_info) {
        $database = database::getInstance();

        // get email addresses of all admins and superadmins set to recieve system emails
        $query = "SELECT email, sendEmail FROM #__users WHERE ( gid = 24 OR gid = 25 ) AND sendEmail = 1 AND block = 0";
        $database->setQuery($query);
        $admins = $database->loadObjectList();
		
		$result = false;

        foreach($admins as $admin) {
            // send email to admin & super admin set to recieve system emails
			// $result will be true if at least one sending was successful
            $result |= mosMail($email_info['adminEmail'],
                    $email_info['adminName'],
                    $admin->email,
                    $email_info['subject'],
                    $email_info['message']);
        }
    }

    function get_gender($user, $params = null) {

        switch($user->user_extra->gender) {
            case 'female':
                $gender = _USERS_FEMALE_S;
                break;

            case 'male':
                $gender = _USERS_MALE_S;
                break;

            case 'no_gender':
            default:
                $gender = _GENDER_NONE;
                break;

        }

        if($params->get('gender')==1 || !$params) {
            return $gender;
        }

        else {
            $gender = '<img alt="" title="'.$gender.'" src="images/system/'.$user->user_extra->gender.'.png" />';
        }
        return $gender;
    }

    function get_birthdate($user, $params = null) {
        mosMainFrame::addLib('text');
        mosMainFrame::addLib('datetime');

        if($params->get('show_birthdate')==1) {
            return mosFormatDate($user->user_extra->birthdate, '%d-%m-%Y', 0);
        }else {
            $delta = DateAndTime::getDelta(DateAndTime::mysql_to_unix($user->user_extra->birthdate), DateAndTime::mysql_to_unix(_CURRENT_SERVER_TIME));
            $age = $delta['year'];
            return $age.' '.Text::declension($age ,array(_YEAR, _YEAR_, _YEARS));
        }

    }

    function get_total($usertype = '', $and = '') {

        if($usertype) {
            $and .= " AND usertype='".$usertype."'";
        }

        $query = "SELECT COUNT(id)
		FROM #__users AS u
		LEFT JOIN #__users_extra AS u_extra ON u_extra.user_id = u.id 
		WHERE block = '0'" .$and;

        $this->_db->setQuery($query);
        return  $this->_db->loadResult();

    }

    function get_users($usertype = '', $limitstart = 0, $limit = 0, $and = '') {

        if($usertype) {
            $and .= " AND usertype='".$usertype."'";
        }

        $query = "SELECT u.*, u_extra.* FROM #__users AS u
				LEFT JOIN #__users_extra AS u_extra ON u_extra.user_id = u.id
				WHERE u.block = '0'"
                .$and;
        $this->_db->setQuery($query, $limitstart, $limit);
        return $this->_db->loadObjectList();

    }

    function paginate($total,$page, $limit) {
        mosMainFrame::addLib('pageNavigation');
        $r = new mosPageNav( $total, $page, $limit );
        return  $r;
    }

}

/* расширенная информация о пользователе */
class userUsersExtra extends mosDBTable {

    var $user_id = null;
    var $gender = null;
    var $about = null;
    var $location = null;
    var $url = null;
    var $icq = null;
    var $skype = null;
    var $jabber = null;
    var $msn = null;
    var $yahoo = null;
    var $phone = null;
    var $fax = null;
    var $mobil = null;
    var $birthdate = null;

    /**
     * @param database A database connector object
     */
    function userUsersExtra($db) {
        $this->mosDBTable('#__users_extra','user_id',$db);
    }
    function insert($id) {
        $this->user_id = $id;
        return $this->_db->insertObject('#__users_extra', $this, 'user_id');

    }
}

class userHelper {

	public static function _load_core_js() {
        $mainframe = mosMainFrame::getInstance();
        $mainframe->addJS(JPATH_SITE.'/components/com_users/js/com_users.js','custom');
    }

    public static function _load_jquery_form() {
        mosCommonHTML::loadJqueryPlugins('jquery.form', false, false, 'js');
    }


	public static function _build_img_upload_area($obj, $form_params, $state) {
        $field = $form_params->img_field;

        ?><script type="text/javascript">
    $(document).ready(function() {

        //---Кнопка "Сменить"
        $("a#reupload_<?php echo $form_params->img_field;?>").live('click', function () {
            $(".upload_area_<?php echo $form_params->img_field;?>").fadeIn(1000);
            $("#<?php echo $form_params->img_field;?>").addClass("required");
            return false;
        });
        //---Кнопка "Удалить"
        $('a#del_<?php echo $form_params->img_field;?>').live('click', function(){
            //Индикатор выполнения
            $('#indicate_<?php echo $form_params->img_field;?>').fadeIn(1000, function () {
                $("#indicate_<?php echo $form_params->img_field;?>").addClass("inprogress");
                $("#indicate_<?php echo $form_params->img_field;?>").html("<?php echo _INPROGRESS; ?>");
            });

            //отправляем ajax-запрос
            $.post( //---post:begin
            '<?php echo $form_params->ajax_handler; ?>',{
                task: "del_avatar",
            } ,
            //пришёл ответ
            function onAjaxSuccess(data){
                //Плавная смена изображения
                //$('#current_<?php echo $form_params->img_field;?>_img').fadeOut(1000);
                $('#current_<?php echo $form_params->img_field;?>_img').fadeOut(1000, function(){
                    $('#current_<?php echo $form_params->img_field;?>_img').html('<img class="avatar" src="<?php echo JPATH_SITE;?>/<?php echo $form_params->img_path;?>/'+data+'" />');
                    //Скрываем индикатор
                    $("#indicate_<?php echo $form_params->img_field;?>").removeClass("inprogress");
                    $("#indicate_<?php echo $form_params->img_field;?>").html("");
                });
                $('#current_<?php echo $form_params->img_field;?>_img').fadeIn(1000, function () {
                    $('#current_<?php echo $form_params->img_field;?>_img').show('slow');
                });
                //Скрываем кнопку "Удалить"
                $('a#del_<?php echo $form_params->img_field;?>').parent().fadeOut("slow");
            }
        ); //---post:end
            return false;
        });
    });
</script>
        <?php if($state!='upload') {?>
<div id="current_<?php echo $form_params->img_field;?>">
    <div class="current_img" id="current_<?php echo $form_params->img_field;?>_img">
        <img class="avatar" src="<?php echo JPATH_SITE;?>/<?php echo $form_params->img_path;?>/<?php echo $obj->$field;?>" />
        <input type="hidden" name="curr_<?php echo $form_params->img_field;?>" id="curr_<?php echo $form_params->img_field;?>" value="<?php echo $obj->$field;?>" />
    </div>
    <div class="indicator" id="indicate_<?php echo $form_params->img_field;?>">&nbsp;</div>
    <div class="user_buttons buttons_<?php echo $form_params->img_field;?>">
        <span class="button">
            <a class="reupload_button button" href="#" id="reupload_<?php echo $form_params->img_field;?>"><?php echo _C_USERS_AVATARS_SHOISE?></a>
        </span>
        <span class="button">
            <a class="del_button button" href="javascript:void(0)" id="del_<?php echo $form_params->img_field;?>"><?php echo _DELETE?></a>
        </span>
    </div>
</div>
<div class="upload_area upload_area_<?php echo $form_params->img_field;?>" style="display:none;">
                <?php echo self::_build_img_upload_form($obj, $form_params);?>
</div>
            <?php } else { ?>
<div id="current_<?php echo $form_params->img_field;?>">
    <div class="current_img" id="current_<?php echo $form_params->img_field;?>_img">
        <img class="avatar" src="<?php echo JPATH_SITE;?>/<?php echo $form_params->default_img;?>" />
    </div>
    <div class="indicator" id="indicate_<?php echo $form_params->img_field;?>">&nbsp;</div>
    <div class="user_buttons buttons_<?php echo $form_params->img_field;?>" style="display:none;">
        <span class="button">
            <a class="reupload_button button"  href="#" id="reupload_<?php echo $form_params->img_field;?>"><?php echo _C_USERS_AVATARS_SHOISE?></a>
        </span>
        <span class="button">
            <a class="del_button button"  href="javascript:void(0)" id="del_<?php echo $form_params->img_field;?>"><?php echo _DELETE?></a>
        </span>
    </div>
</div>
<div class="upload_area_<?php echo $form_params->img_field;?>">
                <?php echo self::_build_img_upload_form($obj, $form_params);?>
</div>
            <?php } ?>
        <?php
    }

    public static function _build_img_upload_form($obj, $form_params) {
        self::_load_jquery_form();


        $mainframe = mosMainFrame::getInstance();
        $action = 'ajax.index.php';
        if(!$mainframe->isAdmin()) {
            $action = sefRelToAbs($action);
        }

        ?><script type="text/javascript">
    $(document).ready(function(){
        $('#<?php echo $form_params->img_field;?>_upload_button').live('click', function() {
            $('#<?php echo $form_params->img_field;?>_uploadForm').ajaxSubmit({
                beforeSubmit: function(a,f,o) {
                    o.dataType = "html";
                    $('#<?php echo $form_params->img_field;?>_uploadOutput').fadeIn(1000, function () {
                        $('#<?php echo $form_params->img_field;?>_uploadOutput').addClass("inprogress");
                    });
                    //$('#current_<?php echo $form_params->img_field;?>').fadeOut(1000);
                    if(!$('#upload_<?php echo $form_params->img_field;?>').val()){
                        $('#<?php echo $form_params->img_field;?>_uploadOutput').html('<?php echo _CHOOSE_IMAGE?>');
                        return false;
                    }
                    $('#current_<?php echo $form_params->img_field;?>').fadeIn(1000);

                },
                success: function(data) {
                    var $out = $('#<?php echo $form_params->img_field;?>_uploadOutput');
                    $out.html('');
                    if(data){
                        if (typeof data == 'object' && data.nodeType)
                            data = elementToString(data.documentElement, true);
                        else if (typeof data == 'object')
                            data = objToString(data);
                        $(".upload_area_<?php echo $form_params->img_field;?>").fadeOut(900);
                        $(".buttons_<?php echo $form_params->img_field;?>").fadeOut(1000);
                        //$('#current_<?php echo $form_params->img_field;?>_img').fadeOut(1000);
                        $('#current_<?php echo $form_params->img_field;?>_img').fadeOut(1000, function(){
                            $('#current_<?php echo $form_params->img_field;?>_img').html('<img class="avatar" src="<?php echo JPATH_SITE;?>/<?php echo $form_params->img_path;?>/'+data+'?r=' + Math.random()+'" />');
                        });
                        $('#current_<?php echo $form_params->img_field;?>_img').fadeIn(1000, function () {
                            $('#current_<?php echo $form_params->img_field;?>_img').show('slow', function () {
                                //$('#current_<?php echo $form_params->img_field;?>_img').fadeIn(1000);
                            });
                        });
                        $(".buttons_<?php echo $form_params->img_field;?>").fadeIn(1000);
                        $('#new_<?php echo $form_params->img_field;?>').val(data);
                    }

                }
            });
            return false;
        });

    });
</script>
<form name="<?php echo $form_params->img_field;?>_uploadForm" class="ajaxForm" enctype="multipart/form-data" method="post" action="<?php echo $action; ?>" id="<?php echo $form_params->img_field;?>_uploadForm">
    <input name="<?php echo $form_params->img_field;?>"  id="upload_<?php echo $form_params->img_field;?>"  type="file" />
    <span class="button"><button type="button" id="<?php echo $form_params->img_field;?>_upload_button" class="button" ><?php echo _TASK_UPLOAD?></button></span>
    <input type="hidden" name="task" value="upload_<?php echo $form_params->img_field;?>" />
    <input type="hidden" name="id" value="<?php echo $obj->id;?>" />
    <input type="hidden" name="option" value="com_users" />
</form>
<div id="<?php echo $form_params->img_field;?>_uploadOutput" style="display:none;"><?php echo _UPLOADING?></div>
        <?php
    }
}

/**
 * Session database table class
 * @package Joostina
 */
class mosSession extends mosDBTable {
    /**
     @var int Primary key*/
    var $session_id = null;
    /**
     @var string*/
    var $time = null;
    /**
     @var string*/
    var $userid = null;
    /**
     @var string*/
    var $usertype = null;
    /**
     @var string*/
    var $username = null;
    /**
     @var time*/
    var $gid = null;
    /**
     @var int*/
    var $guest = null;
    /**
     @var string*/
    var $_session_cookie = null;

    /**
     * @param database A database connector object
     */
    function mosSession($db) {
        $this->mosDBTable('#__session','session_id',$db);
    }

    /**
     * @param string Key search for
     * @param mixed Default value if not set
     * @return mixed
     */
    function get($key,$default = null) {
        return mosGetParam($_SESSION,$key,$default);
    }

    /**
     * @param string Key to set
     * @param mixed Value to set
     * @return mixed The new value
     */
    function set($key,$value) {
        $_SESSION[$key] = $value;
        return $value;
    }

    /**
     * Sets a key from a REQUEST variable, otherwise uses the default
     * @param string The variable key
     * @param string The REQUEST variable name
     * @param mixed The default value
     * @return mixed
     */
    function setFromRequest($key,$varName,$default = null) {
        if(isset($_REQUEST[$varName])) {
            return mosSession::set($key,$_REQUEST[$varName]);
        } else
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return mosSession::set($key,$default);
        }
    }

    /**
     * Insert a new row
     * @return boolean
     */
    function insert() {
        $ret = $this->_db->insertObject($this->_tbl,$this);
        if(!$ret) {
            $this->_error = strtolower(get_class($this))."::store failed <br />".$this->_db->stderr();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Update an existing row
     * @return boolean
     */
    function update($updateNulls = false) {
        $ret = $this->_db->updateObject($this->_tbl,$this,'session_id',$updateNulls);
        if(!$ret) {
            $this->_error = strtolower(get_class($this))."::update error <br />".$this->_db->stderr();
            return false;
        } else {
            return true;
        }
    }
    /**
     * Generate a unique session id
     * @return string
     */
    function generateId() {
        $failsafe = 20;
        $randnum = 0;
        while($failsafe--) {
            $randnum = md5(uniqid(microtime(),1));
            $new_session_id = mosMainFrame::sessionCookieValue($randnum);
            if($randnum != '') {
                $query = "SELECT $this->_tbl_key FROM $this->_tbl WHERE $this->_tbl_key = ".$this->_db->Quote($new_session_id);
                $this->_db->setQuery($query);
                if(!$result = $this->_db->query()) {
                    die($this->_db->stderr(true));
                }
                if($this->_db->getNumRows($result) == 0) {
                    break;
                }
            }
        }
        $this->_session_cookie = $randnum;
        $this->session_id = $new_session_id;
    }

    /**
     * @return string The name of the session cookie
     */
    function getCookie() {
        return $this->_session_cookie;
    }

    /**
     * Purge lapsed sessions
     * @return boolean
     */
    function purge($inc = 1800,$and = '',$lifetime='') {

        if($inc == 'core') {
            $past_logged = time() - $lifetime;
            //$past_guest = time() - 900;
            //$query = "DELETE FROM $this->_tbl WHERE ("
            // purging expired logged sessions
            //	."\n ( time < '".(int)$past_logged."' ) AND guest = 0 AND gid > 0 ) OR ("
            // purging expired guest sessions
            //	."\n ( time < '".(int)$past_guest."' ) AND guest = 1 AND userid = 0)";
            // TODO, при неполадках с сессиями использовать SQL запрос выше
            $query = "DELETE FROM $this->_tbl WHERE time < '".(int)$past_logged."'";
        } else {
            // kept for backward compatability
            $past = time() - $inc;
            $query = "DELETE FROM $this->_tbl WHERE ( time < '".(int)$past."' )".$and;
        }
        $this->_db->setQuery($query);

        return $this->_db->query();
    }
}

class mosUserParameters extends mosParameters {
    /**
     * @param string The name of the form element
     * @param string The value of the element
     * @param object The xml element for the parameter
     * @param string The control name
     * @return string The html for the element
     */
    function _form_editor_list($name,$value,$node,$control_name) {
        $database = database::getInstance();
        // compile list of the editors
        $query = "SELECT element AS value, name AS text"
                ."\n FROM #__mambots"
                ."\n WHERE folder = 'editors'"
                ."\n AND published = 1"
                ."\n ORDER BY ordering, name";
        $database->setQuery($query);
        $editors = $database->loadObjectList();
        array_unshift($editors,mosHTML::makeOption('',_SELECT_EDITOR));

        return mosHTML::selectList($editors,''.$control_name.'['.$name.']','class="inputbox"','value','text',$value);
    }
}

class userPlugins {

    var $_group = null;
    var $_plugins = null;

    function get_plugins($group = 'profile') {
        $_MAMBOTS = mosMambotHandler::getInstance();

        $plugins = $_MAMBOTS->loadBotGroup($group, 1);
        $this->_group = $group;
        $this->_plugins = $plugins;

        return $this->_plugins;
    }

    function allow_plugin($cur_plugin) {

        foreach($this->_plugins as $plug) {
            if($plug->element == $cur_plugin) {
                return true;
            }
        }
        //$plugins_array = mosObjectToArray($this->_plugins);

        return false;
    }
}