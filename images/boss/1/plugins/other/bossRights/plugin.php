<?php
/**
 * JoiRights - библиотека для работы с правами пользователей
 *
 * Core Class
 *
 * @version 1.0 
 * @package JoiRights
 * @filename joiRights.class.php
 * @author JoostinaTeam
 * @copyright (C) 2008-2009 Joostina Team
 * @license GNU GPL 2.0. Пожалуйста, не удаляйте копирайты из исходного кода. Спасибо. 
 *
 **/


class bossRights extends joiBossRights {

    var $show_user_content = null;
    var $show_all = null;
    var $show_search = null;
    var $show_all_content = null;
    var $show_category = null;
    var $show_my_content = null;
    var $show_category_content = null;
    var $create_content = null;
    var $edit_user_content = null;
    var $edit_all_content = null;
    var $delete_user_content = null;
    var $delete_all_content = null;
    //для админки
    var $edit_category = null;
    var $edit_content = null;
    var $edit_directories = null;
    var $edit_conf = null;
    var $edit_types = null;
    var $edit_fields = null;
    var $edit_fieldimages = null;
    var $edit_templates = null;
    var $edit_plugins = null;
    var $import_export = null;
    var $edit_users = null;

    function __construct($directory, $params) {
        
        $params[0] = (empty($params[0])) ? 'conf' : $params[0];

        if($params[0] == 'conf_admin'){
            $rights_label = array(
                    'edit_category' => 'Редактирование категорий',
                    'edit_content' => 'Редактирование контента',
                    'edit_directories' => 'Редактирование каталогов',
                    'edit_conf' => 'Редактирование конфигурации каталога',
                    'edit_types' => 'Редактирование типов контента',
                    'edit_fields' => 'Редактирование полей',
                    'edit_fieldimages' => 'Редактирование полей-картинок',
                    'edit_templates' => 'Редактирование шаблонов',
                    'edit_plugins' => 'Редактирование плагинов',
                    'import_export' => 'Импорт-экспорт',
                    'edit_users' => 'Редактирование пользователей'    
            );
        }
        
        else if($params[0] == 'conf_front'){
            $rights_label = array(               
                    //доступ к отдельным страницам
                    'show_user_content' => 'Просмотр контента конкретного пользователя',
                    'show_all' => 'Просмотр страницы всего контента',
                    'show_search' => 'Просмотр страницы поиска',                 
                    //дублирование прав категории
                    'show_all_content' => 'Просмотр всего контента',
                    'show_category' => 'Просмотр страницы категории',
                    'show_my_content' => 'Просмотр своего контента в категории',
                    'show_category_content' => 'Просмотр всего контента категории',
                    'create_content' => 'Создание контента',
                    'edit_user_content' => 'Редактирование своего контента',
                    'edit_all_content' => 'Редактирование всего контента',
                    'delete_user_content' => 'Удаление своего контента',
                    'delete_all_content' => 'Удаление всего контента'
                
            );
        }
        else if($params[0] == 'category'){
            $rights_label = array(
                    'show_all_content' => 'Просмотр всего контента',
                    'show_category' => 'Просмотр страницы категории',
                    'show_my_content' => 'Просмотр своего контента в категории',
                    'show_category_content' => 'Просмотр всего контента категории',
                    'create_content' => 'Создание контента',
                    'edit_user_content' => 'Редактирование своего контента',
                    'edit_all_content' => 'Редактирование всего контента',
                    'delete_user_content' => 'Удаление своего контента',
                    'delete_all_content' => 'Удаление всего контента'
            );
        }
        else{
            $rights_label = array();
        }
        $error_messages = array(
                'show_all_content' => 'Извините, у Вас не достаточно прав для просмотра всего контента',
                'show_my_content' => 'Извините, у Вас не достаточно прав для просмотр своего контента',
                'show_user_content' => 'Извините, у Вас не достаточно прав для просмота контента пользователей',
                'show_all' => 'Извините, у Вас не достаточно прав для просмотра страницы всего контента',
                'show_search' => 'Извините, у Вас не достаточно прав для просмотра страницы поиска',
                'show_category' => 'Извините, у Вас не достаточно прав для просмотра этой категории',
                'show_category_content' => 'Извините, у Вас не достаточно прав для просмотра всего контента категории',
                'create_content' => 'Извините, у Вас не достаточно прав для создание контента',
                'edit_user_content' => 'Извините, у Вас не достаточно прав для редактирование своего контента',
                'edit_all_content' => 'Извините, у Вас не достаточно прав для редактирование контента',
                'delete_user_content' => 'Извините, у Вас не достаточно прав для удаления своего контента',
                'delete_all_content' => 'Извините, у Вас не достаточно прав для удаления чужого контента'
        );

        parent::__construct($rights_label, $error_messages);
        return true;
    }
    
    //загружаем права скопом
    function loadRights($rights=array(), $groupId){
        $perms = null;
        foreach($rights as $right){
            $perms->$right = $this->allow_me($right, $groupId);
        }
        return $perms;
    }
}

 class joiBossRights{
	
	var $_me = null;
	var $_error = null;
	var $_rights_label = null;
	var $_error_messages = null;
	
	function __construct($rights_label, $error_messages = array()){
		
		$this->_rights_label = $rights_label;
		$this->_error_messages = $error_messages;	
	}
        
	/**
         *
         * @param type $action
         * @param type $gid
         * @param type $uid
         * @param type $authorId
         * @return type 
         */
	function allow_me($action, $gid){
   
        if(isset($this->$action) && is_array($this->$action) && in_array($gid, $this->$action)){
			return true;
		}
		else{
			return false;
		}	
	}
        
	//
	function bind_rights($config_rights){
		
		$rights = array();
		$rights0 = explode('*', $config_rights);
		
		foreach($rights0 as $right){
			$arr = explode('=', $right);
			if(isset($arr[1])){
				$rights[$arr[0]] = explode( ',' , $arr[1] );
			}

		}
		
		$rows = get_object_vars($this);
		
		foreach ($rows as $key => $value) {
			if(substr($key, 0, 1) !== '_'){
				if (isset($rights[$key])){
					$this->$key = $rights[$key];
				}
				else{
					$this->$key = array();	
				}	
				
			}
		}
					
	}
	//конвертация массива в строку для сохранения
	function prepare_for_saving($array){		
		$return = '';
				
		foreach($array as $key => $v){
			$return .= $key.'='.implode(',' , $v).'*';	
		}		
		return $return;		
	}

	//создать таблицу разрешений в админке
	function draw_config_table($object){
		
		$rights = get_object_vars($this);			
		$groups = $this->get_all_user_groups();		
				
		//Шапка таблицы
		$return = '<table class="adminlist" width="100%">
                            <tr>
                             <th>
                              <span id="check-all">
                               <a class="check_it checker active" id="check_'.$object.'" href="#">Отметить все</a>
                               <a class="uncheck_it checker" id="uncheck_'.$object.'" href="#">Снять отметки</a>
                              </span>
                             </th>';
		foreach($groups as $key => $v ){
			$return .='<th>
                                     <a class="checker_group" id="group_'.$object.'_'.$key.'" href="#">'.$v.'</a>
                                     <input type="hidden" id="hidden_'.$object.'_'.$key.'" value="1"/>
                                   </th>';
		} 
		$return .= '</tr>';
		
		
		//Таблица с чекбоксами	
		foreach($rights as $right => $allow_groups){
			 
			if(substr($right, 0, 1) !== '_' && $this->get_label($right) !== FALSE){				
			
				$return .='<tr>';			
				$return .='<td>'.$this->get_label($right).'</td>';

				foreach($groups as $gid => $gname){
					$checked = (@in_array($gid, $allow_groups) ? 'checked="checked"' : '');
					$return .= '<td align="center"><input type="checkbox" class="urights_box_'.$object.'" name="u_rights['.$right.'][]" value="'.$gid.'" '.$checked.' /></td>';
				}
				
				$return .='</tr>';	
			}
		}		
		$return .= '</table>';
		
		return $return;
	}

	//массив групп и пользователей
	function get_all_user_groups(){
		
		$groups = array(
			 0 => 'Guest',
			18 => 'Registered',
			19 => 'Author',
			20 => 'Editor',
			21 => 'Publisher',
			23 => 'Manager',
			24 => 'Administrator',
			25 => 'Super Administrator'
		);
		
		return $groups;	
	}
	
	
	function get_label($right){
		
		if(isset($this->_rights_label[$right])){
			return $this->_rights_label[$right];	
		}
		else{
			return FALSE;	
		}
				
	}
	
	function error($right = ''){
		if($right && isset($this->_error_messages[$right])){
			return $this->_error_messages[$right];	
		}
		else{
			return 'Доступ запрещен';
		}		
	}
}

