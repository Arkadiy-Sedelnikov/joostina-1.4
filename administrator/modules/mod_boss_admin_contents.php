<?php

/**
* @BOSS Backend Contents Module
* @version 1.0.1 27.02.2011
* @author: Алексей Поздняков <mosgaz@list.ru>
* @package Joostina BOSS
* @copyright (C) 2011 Woodell Web Works
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// запрет прямого доступа
defined('_VALID_MOS') or die();

global $my;
$mainframe = mosMainFrame::getInstance();
$mainframe->addJS( JPATH_SITE . '/' . JADMIN_BASE . '/components/com_boss/js/function.js');

//Подключаем языковой файл 
if (file_exists($lang_file = JPATH_BASE.'/components/com_boss/lang/'.$mainframe->getCfg('lang').'.php'))
     $lang_file = $lang_file;
else $lang_file = JPATH_BASE.'/components/com_boss/lang/russian.php';
require_once ($lang_file);

// Получаем настройки модуля
$cat_ids      	 	  = $params->get( 'cat_ids' );
$moduleclass_sfx   	  = $params->get( 'moduleclass_sfx', "" );
$date_field      	  = $params->get( 'date_field', "date_created" );
$content_title        = $params->get( 'content_title', "Последнее добавленное содержимое" );
$content_title_link   = $params->get( 'content_title_link', "Все содержимое" );
$publ 				  = intval($params->get( 'publ' , 0 ));
$displaycategory	  = intval($params->get( 'displaycategory' , 1 ));
$limit 			 	  = intval($params->get( 'limit', 5 ));
$sort_sql 			  = intval($params->get( 'sort', 0 ));
$directory 			  = intval($params->get( 'directory', mosGetParam( $_GET, 'directory', 1 ) )) ;
$display_author  	  = intval($params->get( 'display_author', 3 ));

// Сортировка объектов
switch($sort_sql) {	
	case 0:// по ID (от больших к меньшим)
		$order_sql = "ORDER BY a.date_created DESC, a.id DESC ";
		break;	
	case 1:// по ID (от меньших к большим)
		$order_sql = "ORDER BY a.date_created ASC, a.id ASC ";
		break;	
	case 2:// случайным образом
		$order_sql = "ORDER BY RAND() ";
		break;		
	case 3:// по названию А до я
		$order_sql = "ORDER BY a.name ";
		break;			
	case 4:// по названию Я до а
		$order_sql = "ORDER BY a.name DESC ";
		break;		
	case 5:// по дате создания (от новых к старым)
	default:	
		$order_sql = "ORDER BY a.date_created DESC ";
		break;		
	case 6:// по дате создания (от старых к новым)
		$order_sql = "ORDER BY a.date_created ASC ";
		break;	
	case 7:// по дате публикации (от свежих к ранним)
		$order_sql = "ORDER BY a.date_publish DESC ";
		break;		
	case 8:// по дате публикации (от ранних к свежим)
		$order_sql = "ORDER BY a.date_publish ASC ";
		break;
}

// Отображение объектов из конкретных категорий
$cat_query  = "";
$cat_filter = "";
if ($cat_ids) {
	$cat_ids_array = explode(',', $cat_ids);
	if (count($cat_ids_array) > 1) { 	
		$i = 1;
		$cat_query = " (";
		foreach ($cat_ids_array as $cat_id){
			if ($i == count($cat_ids_array))
			    $cat_query .= "(c.id = '" . (int)$cat_id . "')";
			else           			
			    $cat_query .= "(c.id = '" . (int)$cat_id . "') OR \n";			
			$i++;
		}
		$cat_query .= ") AND \n";
	} 
	else {
		$cat_query  = " c.id = '" . (int)$cat_ids . "' AND ";
		$cat_filter = "&catid=" . $cat_ids;
	}	
}
//echo '<pre>';print_r($cat_query);echo '</pre>';

// Объекты какого типа отображать в модуле
switch ($publ) { 
	case 2:// только не опубликованные
		$publishment = "AND a.published = 0 "; 
		break;
	case 1:// только опубликованные
		$publishment = "AND a.published = 1 "; 
		break;
	case 0:// все объекты
	default:
		$publishment = ""; 
		break;
}

// Отображение даты
$date_field = $date_field ? 'a.' . $date_field . ' as date,' : '';
$colspan = $date_field ? 'colspan="2"' : '';// для таблицы

// Максимальное количество отображаемых объектов
$limit = ($limit) ? 'LIMIT ' . $limit : ''; 

// Получаем список объектов
$rows = $database->setQuery(
	"SELECT a.id, a.name, a.published, a.date_unpublish, a.date_publish, a.userid, c.id as category, " . $date_field . " p.id as parentid,".
	"\n p.name as parent,c.id as catid, c.name as cat".
	"\n FROM #__boss_" . $directory . "_contents as a ".
	"\n LEFT JOIN #__boss_" . $directory . "_content_category_href as cx ON a.id = cx.content_id".
	"\n LEFT JOIN #__boss_" . $directory . "_categories as c ON cx.category_id = c.id".
	"\n LEFT JOIN #__boss_" . $directory . "_categories as p ON c.parent = p.id".
	"\n WHERE " . $cat_query . " c.published = 1 " . $publishment . ""
	. $order_sql . $limit )->loadObjectList();

// Получаем список авторов
$autors = $database->setQuery(
	"SELECT DISTINCT c.userid, u.name ".
	"FROM #__boss_" . $directory . "_contents as c, ".
	"#__users as u ".
	"WHERE u.id = c.userid ORDER BY u.name" )->loadObjectList('userid');

if ($database->getErrorNum()) {	
	echo 'Проверьте настройки модуля <strong>mod_boss_contents_admin</strong>. ' . $database->stderr();
	return false;
}

if (isset($rows[0])) {// если есть объекты -> продолжаем 
	
	?>

	<table class="adminlist mod_boss_admin_contents<?php echo $moduleclass_sfx ?>">
		<tr>
			<th <?php echo $colspan; ?> class="title"><?php echo $content_title ?> <small>( <a href="index2.php?option=com_boss&directory=<?php echo $directory ?>&act=contents<?php echo $cat_filter ?>"><?php echo $content_title_link ?></a> )</small></th>
			<th align="center"><?php echo _PUBLISHING//BOSS_FIELD_PUBLISHED//_PUBLISHING ?></th>
			<?php if ($display_author) { ?> 
			<th align="center"><?php echo _AUTHOR//BOSS_AUTOR//BOSS_AUTHOR//_AUTHOR ?></th>
			<?php } ?> 
		</tr>
		<?php
		$nullDate = $database->getNullDate();
		$now = _CURRENT_SERVER_TIME;
		$k = 0;
		foreach($rows as $row) {
			// получение значка статуса содержимого
			$date = date('Y-m-d');

			if ($row->published == 0){
				$img = 'publish_x.png';
				$alt = BOSS_NO;
			}
			elseif($row->published == 1 && ($row->date_publish > $date && $row->date_publish != '0000-00-00')){
			   $img = 'publish_y.png';
			   $alt = BOSS_NOT_STARTED;
			}
			elseif($row->published == 1 && ($row->date_unpublish < $date && $row->date_unpublish != '0000-00-00')){
			   $img = 'publish_r.png';
			   $alt = BOSS_DELAYED;
			}
			else{
			   $img = 'publish_g.png';
			   $alt = BOSS_YES;
			}
									
			// Ссылка на редактирование объекта
			$link_content = 'index2.php?option=com_boss&act=contents&task=edit&directory=' . $directory . '&tid[]=' . $row->id;
			
			// Имя автора ссылкой или текстом
			if($acl->acl_check('administration', 'manage', 'users', $my->usertype, 'components', 'com_users')) {			
					// Ссылка на автора
					$link_author = 'index2.php?option=com_users&task=editA&amp;hidemainmenu=1&id=' . $row->userid;
					$author = '<a href="' . $link_author . '" title="' . _CHANGE_USER_DATA . '">' . htmlspecialchars($autors[$row->userid]->name, ENT_QUOTES) . '</a>';		
			} 
			else	$author = htmlspecialchars($autors[$row->userid]->name, ENT_QUOTES);					
			
			// Варианты отображения автора
			switch ($display_author) { 
				case 0:// не отображать	
					$display_author_style = ""; 				
					break;
				case 2:// только ID
					$display_author_style = '[' . $row->userid . ']'; 
					break;
				case 1:// только Имя
					$display_author_style = $author; 
					break;
				case 3:// ID и Имя
				default:
					$display_author_style = '[' . $row->userid . '] '.$author; 
					break;
			}						
			?>
			
			<tr class="row<?php echo $k; ?>">
				<?php if ($date_field) { ?>
				<td width="10%" align="center"><?php echo mosFormatDate($row->date); ?></td>
				<?php } ?>
				<td align="left" width="60%">
					<a href="<?php echo $link_content; ?>"><?php echo htmlspecialchars($row->name, ENT_QUOTES); ?></a>
					<?php echo $displaycategory ? '<div class="mod_boss_admin_contents-path">' . $row->cat . '</div>' : '';// Отображаем категорию? ?>
				</td>
				<td class="td-state" align="center" onclick="boss_publ('img-pub-<?php echo $directory.$row->catid.$row->id; ?>', '<?php echo "act=contents&task=publish&tid=" . $row->id . "&directory=". $directory; ?>');">
					<img id="img-pub-<?php echo $directory.$row->catid.$row->id;?>" class="img-mini-state" alt="<?php echo _PUBLISHING?>" src="<?php echo JPATH_SITE . '/' . JADMIN_BASE . '/templates/' . JTEMPLATE . '/images/ico/' . $img; ?>"/>			
				</td>
				<?php if ($display_author) { ?>  
				<td align="center"><?php echo $display_author_style; ?></td>
				<?php } ?>  
			</tr>
			<?php
			$k = 1 - $k;
		}
		unset($rows, $row);
		?>
	</table>
<?php 
}//if (isset($rows[0]))

?>