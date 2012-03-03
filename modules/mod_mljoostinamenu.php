<?php
/**
 * @package Joostina
 * @copyright Авторские права (C) 2008-2010 Joostina team. Все права защищены.
 * @license Лицензия http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL, или help/license.php
 * Joostina! - свободное программное обеспечение распространяемое по условиям лицензии GNU/GPL
 * Для получения информации о используемых расширениях и замечаний об авторском праве, смотрите файл help/copyright.php.
 */

// запрет прямого доступа
defined( '_VALID_MOS' ) or die();

if (!defined( '_MOS_MLJOOSTINAMENU_MODULE' )) {
	/** ensure that functions are declared only once*/
	define( '_MOS_MLJOOSTINAMENU_MODULE', 1 );
	$ml_rollover_use = $params->get('ml_rollover_use');
	if ($ml_rollover_use == 1) {
		?>
<script type="text/javascript">
	function MlImageOn (imgname) {
		document.images[imgname].src = onImgArray[imgname].src;
	}
	function MlImageOff (imgname) {
		document.images[imgname].src = offImgArray[imgname].src;
	}
	var offImgArray = new Array();
	var onImgArray = new Array();
</script>
		<?php
	}
	function mosGetJoostinaLink( $mitem, $level=0, $params, $open=null ) {
		global $Itemid;

		$txt = '';
		$mitem->params = (isset($mitem->params)) ? $mitem->params : '';

		$menuparams = new mosParameters( $mitem->params );
		$pg_title = $menuparams->get('title',$mitem->name);
		$pg_title = htmlspecialchars( $pg_title, ENT_QUOTES );
		unset($menuparams);
		switch ($mitem->type) {
			case 'separator':
			case 'component_item_link':
				break;
			case 'url':
				if ( preg_match( '/index.php\?/', $mitem->link ) && !preg_match( '/http/', $mitem->link ) && !preg_match( '/https/', $mitem->link ) ) {
					if ( !preg_match( '/Itemid=/', $mitem->link ) ) {
						$mitem->link .= '&Itemid='. $mitem->id;
					}
				}
				break;
			case 'content_item_link':
			case 'content_typed':
			// load menu params
				$mainframe = mosMainFrame::getInstance();
				$menuparams = new mosParameters( $mitem->params, $mainframe->getPath( 'menu_xml', $mitem->type ), 'menu' );
				$unique_itemid = $menuparams->get( 'unique_itemid', 1 );

				if ( $unique_itemid ) {
					$mitem->link .= '&Itemid='. $mitem->id;
				} else {
					$temp = explode('&task=view&id=', $mitem->link);
					if ( $mitem->type == 'content_typed' ) {
						// еще один небольшой эксперимент, вместе лишнего запроса в базу - возьмём идентификатор ссылки на статичное содержимое из глобального объекта
						//$mitem->link .= '&Itemid='. $mainframe->getItemid($temp[1], 1, 0);
						$all_menu_links = mosMenu::get_menu_links();
						$mitem->link .= '&Itemid='. ( (isset($all_menu_links[$mitem->link]['id']) ? $all_menu_links[$mitem->link]['id']:$mitem->id));
					} else {
						$mitem->link .= '&Itemid='. $mainframe->getItemid($temp[1], 0, 1);
					}
				}
				break;
			default:
				$mitem->link .= '&Itemid='. $mitem->id;
				break;
		}

		// Active Menu highlighting
		$current_itemid = $Itemid;

		if ( !$current_itemid ) {
			$id = '';
		} else if ( $current_itemid == $mitem->id ) {
			$id = 'id="active_menu'. $params->get( 'class_sfx' ) .'"';
		} else if( $params->get( 'activate_parent' ) && isset( $open ) && in_array( $mitem->id, $open ) ) {
			$id = 'id="active_menu'. $params->get( 'class_sfx' ) .'"';
		} else {
			$id = '';
		}

		if ( $params->get( 'full_active_id' ) ) {
			// support for `active_menu` of 'Link - Component Item'
			if ( $id == '' && $mitem->type == 'component_item_link' ) {
				parse_str( $mitem->link, $url );
				if ( $url['Itemid'] == $current_itemid ) {
					$id = 'id="active_menu'. $params->get( 'class_sfx' ) .'"';
				}
			}

			// support for `active_menu` of 'Link - Url' if link is relative
			if ( $id == '' && $mitem->type == 'url' && strpos( 'http', $mitem->link ) === false) {
				parse_str( $mitem->link, $url );
				if ( isset( $url['Itemid'] ) ) {
					if ( $url['Itemid'] == $current_itemid ) {
						$id = 'id="active_menu'. $params->get( 'class_sfx' ) .'"';
					}
				}
			}
		}

		if ($params->get('ml_separated_active') == 1) {
			if ($params->get('ml_linked_sep_active') == 1 ) {
				$link_replacer_id = 'id="active_menu-'.$mitem->id;
			}else {
				$link_replacer_id = 'id="active_menu';
			}
			$id = str_replace( 'id="active_menu',$link_replacer_id,$id);
		}

		// replace & with amp; for xhtml compliance
		$mitem->link = ampReplace( $mitem->link );

		// run through SEF convertor
		$mitem->link = sefRelToAbs( $mitem->link );

		$menuclass = 'mainlevel'. $params->get( 'class_sfx' );
		if ($params->get('ml_separated_link') == 1) {
			if ($params->get('ml_linked_sep') == 1 ) {
				$link_replacer = 'mainlevel-'.$mitem->id;
			} else {
				$link_replacer = 'mainlevel';
			}
			$menuclass = str_replace( 'mainlevel',$link_replacer,$menuclass);
		}

		if ($level > 0) {
			$menuclass = 'sublevel'. $params->get( 'class_sfx');
		}

		// replace & with amp; for xhtml compliance
		// remove slashes from excaped characters
		$mitem->name = stripslashes( ampReplace($mitem->name) );

		// strip text if needed
		if( $params->get( 'ml_imaged' ) == 1 ) {
			$ml_alt = $mitem->name;
			$ml_img_title = ' title="'.$pg_title.'"';
			$mitem->name = '<img src="'.JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/zaglushka.gif" border="0" alt="'.$ml_alt.'" />';
		}
		elseif  (($params->get( 'ml_imaged' ) == 2) && ($params->get('ml_aligner') == 'left')) {
			$ml_alt = $mitem->name;
			$ml_img_title = ' title="'.$pg_title.'"';
			$mitem->name = '<img src="'.JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/zaglushka.gif" border="0" alt="'.$ml_alt.'" /><em>'.$mitem->name.'</em>';
		}elseif  (($params->get( 'ml_imaged' ) == 2) && ($params->get('ml_aligner') == 'right')) {
			$ml_alt = $mitem->name;
			$ml_img_title = ' title="'.$pg_title.'"';
			$mitem->name = '<em>'.$mitem->name.'</em><img src="'.JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/zaglushka.gif" border="0" alt="'.$ml_alt.'" />';
		}else {
			$ml_alt = ' title="'.$pg_title.'"';
			$ml_img_title = ' title="'.$pg_title.'"';
		}

		// поняли скрываем активные ссылки или выводим просто текстом и дальше тягаем эту переменную
		$ml_hide_active = $params->get('ml_hide_active');

		//$mitem->name = '';

		switch ($mitem->browserNav) {
			// cases are slightly different
			case 1:
			// open in a new window
				if ($ml_hide_active == 1 && $current_itemid == $mitem->id ) {
					$txt = $mitem->name;
				}else {
					$txt = '<a href="'. $mitem->link .'"'.$ml_img_title.' target="_blank" class="'. $menuclass .'" '. $id .'>'. $mitem->name .'</a>';
				}
				break;

			case 2:
			// open in a popup window
				if ($ml_hide_active == 1 && $current_itemid == $mitem->id ) {
					$txt = $mitem->name;
				}else {
					$txt = '<a href="#"'.$ml_img_title.' onclick="javascript: window.open(\''. $mitem->link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\" class=\"$menuclass\" ". $id .'>'. $mitem->name ."</a>\n";
				}
				break;

			case 3:
			// don't link it
				$txt = '<span class="'. $menuclass .'" '. $id .'>'. $mitem->name .'</span>';
				break;

			default:
			// открываем в текущем окне
				if ($ml_hide_active == 1 && $current_itemid == $mitem->id ) {
					// если в параметрах указано что активный пункт меню не должен быть ссылкой
					$txt = $mitem->name;
				}else {
					$txt = '<a href="'. $mitem->link .'"'.$ml_img_title.' class="'. $menuclass .'" '. $id .'>'.$mitem->name.'</a>';
				}
				break;
		}

		return $txt;
	}
	function mosJoostinaLinkReplacer ($count_link,$link,$style,$params,$full_count) {
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();
		$moduleclass_sfx		= $params->get('moduleclass_sfx') ;
		$ml_separated_element	= $params->get('ml_separated_element') ;
		// joostina patch
		$hide_logged = $params->get('ml_hide_logged'.$count_link,1);
		if (( $hide_logged== 1) || !$my->id) {
			if ($ml_separated_element == 1) {
				$ml_elementer = ' class="element-'.$count_link.$moduleclass_sfx.'"';
			}else {
				$ml_elementer = '';
			}
			if (($params->get('ml_separated_element_first') == 1) && ($ml_separated_element != 1)) {
				if ($count_link == 1) {
					$ml_elementer = ' class="element-first'.$moduleclass_sfx.'"';
				}elseif (($count_link == 2) && ($params->get('ml_first_hidden') == 1)) {
					$ml_elementer = ' class="element-first'.$moduleclass_sfx.'"';
				} else {
					$ml_elementer = ' class="element'.$moduleclass_sfx.'"';
				}
			}
			if (($params->get('ml_separated_element_last') == 1) && ($ml_separated_element != 1)) {
				if ($count_link == $full_count) {
					$ml_elementer = ' class="element-last'.$moduleclass_sfx.'"';
				}
			}
			if ($params->get('ml_div') == 2) {
				$prelink = '<div class="ml-div"><div>';
				$postlink = '</div></div>';
			}
			elseif ($params->get('ml_div') == 1) {
				$prelink = '<div class="ml-div">';
				$postlink = '</div>';
			}else {
				$prelink = '';
				$postlink = '';
			}

			if ($params->get('ml_imaged') == 1 || $params->get('ml_imaged') == 2) {
				$ml_rollover_use = $params->get('ml_rollover_use');
				$ml_module_number = $params->get('ml_module_number');
				//а вот тут мы начали догонять есть ли у нас rollover картинка или мы дальше просто так поедем
				if ($ml_rollover_use == 1 && $params->get('ml_image_roll_'.$count_link) != -1 && $params->get('ml_image'.$count_link) != -1 ) {
					$link = str_replace('zaglushka.gif',$params->get('ml_image'.$count_link).'" name="ml_img_'.$count_link.'_'.$ml_module_number,$link);
					$link = str_replace('<a','<a onmouseover="MlImageOn(\'ml_img_'.$count_link.'_'.$ml_module_number.'\')" onmouseout="MlImageOff(\'ml_img_'.$count_link.'_'.$ml_module_number.'\')"',$link);
				}elseif ($params->get('ml_image'.$count_link) != -1) {
					$link = str_replace('zaglushka.gif',$params->get('ml_image'.$count_link).'" name="ml_img_'.$count_link.'_'.$ml_module_number,$link);
				}
			}

			$ml_first_hide = $params->get('ml_first_hidden');

			switch ($style) {
				case 1:
					if ($ml_first_hide == 1 && $count_link == 1) {
						//echo '<td style="display:none;" '.$ml_elementer.'>';
						//echo $prelink.$link.$postlink;
						//echo '</td>';
					} else {
						echo '<td'.$ml_elementer.'>';
						echo $prelink.$link.$postlink;
						echo '</td>';
					}
					break;
				case 2:
					if ($ml_first_hide == 1 && $count_link == 1) {
						//echo '<li style="display:none;" '.$ml_elementer.'>';
						//echo $prelink.$link.$postlink;
						//echo '</li>';
					} else {
						echo '<li'.$ml_elementer.'>';
						echo $prelink.$link.$postlink;
						echo '</li>';
					}
					break;

				case 3:
					if ($ml_first_hide == 1 && $count_link == 1) {
						//echo '<div style="display:none">';
						//echo $prelink.$link.$postlink;
						//echo '</div>';
					} else {
						echo $prelink.$link.$postlink;
					}
					break;
				case 4:
					if ($ml_first_hide == 1 && $count_link == 1) {
						if ($params->get('ml_td_width') == 1) {
							$ml_td_width = 'width="';
							$ml_td_width .= round(100/$full_count);
							$ml_td_width .= '%"';
						}else {
							$ml_td_width = '';
						}
						//echo '<td style="display:none;" '.$ml_elementer.' '.$ml_td_width.'>';
						//echo $prelink.$link.$postlink;
						//echo '</td>';
					} else {
						if ($params->get('ml_td_width') == 1) {
							$ml_td_width = 'width="';
							$ml_td_width .= round(100/$full_count);
							$ml_td_width .= '%"';
						}else {
							$ml_td_width = '';
						}
						echo '<td'.$ml_elementer.' '.$ml_td_width.'>';
						echo $prelink.$link.$postlink;
						echo '</td>';

					}
					break;

				case 5:
					if ($ml_first_hide == 1 && $count_link == 1) {
						//echo '<div style="display:none;" '.$ml_elementer.'>';
						//echo $prelink.$link.$postlink;
						//echo '</div>';
					} else {
						echo '<div '.$ml_elementer.'>';
						echo $prelink.$link.$postlink;
						echo '</div>';
					}
					break;

				case 6:
					if (($ml_first_hide == 1) && ($count_link == 1)) {
						//echo '<tr style="display:none;"><td '.$ml_elementer.'>';
						//echo $prelink.$link.$postlink;
						//echo '</td></tr>';
					} else {
						echo '<tr><td '.$ml_elementer.'>';
						echo $prelink.$link.$postlink;
						echo '</td></tr>';
					}
					break;
			}
		}
	}

	function mosJoostinaGetmenu(&$params,$gid) {

		$all_menu = mosMenu::get_all();

		$menus = isset($all_menu[$params->get( 'menutype' )]) ? $all_menu[$params->get( 'menutype' )] : array() ;

		$return = array();
		foreach ($menus as $menu) {
			if($menu->parent==0 && $menu->access<=(int) $gid) {
				$return[$menu->id]=$menu;
			}
		}

		return $return;
	}

	// подготовка ссылок ,замена стилей в ссылках
	function mosJoostinaPrepareLink (&$params, $style=0) {
		global $mosConfig_shownoauth,$mosConfig_disable_access_control;
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();

		$rows = mosJoostinaGetmenu($params,$my->gid);

		$links = array();

		//$count = count ($rows);
		foreach ($rows as $row) {
			$links[] = mosGetJoostinaLink( $row, 0, $params );
		}
		$full_count = count($links);

		if ($full_count) {
			$count_link = 1;

			// для меню в несколько столбцов
			$nrow = intval($params->get('numrow',0));
			$ii=-1;
			foreach ($links as $link) {
				// начинаем издевательство над линками для приведения их к божьему виду
				if ($params->get('ml_separated_link') == 1) {
					if ($params->get('ml_linked_sep') != 1 ) {
						$link_replacer = 'class="mainlevel-'.$count_link;
					} else {
						$link_replacer = 'class="mainlevel';
					}
					$link = str_replace( 'class="mainlevel',$link_replacer,$link);
				}

				if ($params->get('ml_separated_active') == 1) {
					if ($params->get('ml_linked_sep_active') != 1 ) {
						$link_replacer_id = 'id="active_menu-'.$count_link;
					}  else {
						$link_replacer_id = 'id="active_menu';
					}
					$link = str_replace( 'id="active_menu',$link_replacer_id,$link);
				}

				if (($params->get('ml_separated_link_first') == 1) && ($params->get('ml_separated_link') != 1) ) {
					if (($count_link == 1) || (($count_link == 2) && ($params->get('ml_first_hidden') == 1))) {
						$first_replacer = 'mainlevel-first';
					}else {
						$first_replacer = 'mainlevel';
					}
					$link = str_replace('mainlevel',$first_replacer,$link);
				}
				if (($params->get('ml_separated_link_last') == 1) && ($params->get('ml_separated_link') != 1) ) {
					if ($count_link == $full_count) {
						$last_replacer = 'mainlevel-last';
					} else {
						$last_replacer = 'mainlevel';
					}
					$link = str_replace('mainlevel',$last_replacer,$link);
				}
				if (($params->get('ml_separated_active_first') == 1) && ($params->get('ml_separated_active') != 1)) {
					if ($count_link == 1) {
						$first_replacer_id = 'active_menu-first';
					} else {
						$first_replacer_id = 'active_menu';
					}
					$link = str_replace('active_menu',$first_replacer_id,$link);
				}
				if (($params->get('ml_separated_active_last') == 1) && ($params->get('ml_separated_active') != 1)) {
					if ($count_link == $full_count) {
						$last_replacer_id = 'active_menu-last';
					} else {
						$last_replacer_id = 'active_menu';
					}
					$link = str_replace('active_menu',$last_replacer_id,$link);
				}
				if($params->get('menu_style')=='ulli') {
					// для меню в несколько столбцов
					$ii++;
					if($nrow>0) {
						if($ii==$nrow) {
							$ii=0;
							echo '</ul><ul class="menulist'.$params->get('moduleclass_sfx').'">';
						}
					}
				}
				mosJoostinaLinkReplacer ($count_link,$link,$style,$params,$full_count);
				$count_link = $count_link + 1;
			}
			//конец генерации вывода
		}

	}

	function mosShowVIMenuMLZ( &$params ) {
		global $cur_template, $Itemid,$mosConfig_disable_access_control;
		global $mosConfig_shownoauth;
        $mainframe = mosMainFrame::getInstance();
        $my = $mainframe->getUser();

		$database = database::getInstance();

		$and = '';
		if ( !$mosConfig_shownoauth AND !$mosConfig_disable_access_control ) {
			$and = "\n AND access <= " . (int) $my->gid;
		}
		$sql = "SELECT m.*"
				. "\n FROM #__menu AS m"
				. "\n WHERE menutype = " . $database->Quote( $params->get( 'menutype' ) )
				. "\n AND published = 1"
				. $and
				. "\n ORDER BY parent, ordering";
		$database->setQuery( $sql );
		$rows = $database->loadObjectList( 'id' );

		// indent icons
		switch ( $params->get( 'indent_image' ) ) {
			case '1':
			// Default images
				$imgpath = JPATH_SITE .'/images/M_images';
				for ( $i = 1; $i < 7; $i++ ) {
					$img[$i] = '<img src="'. $imgpath .'/indent'. $i .'.png" alt="" />';
				}
				break;

			case '2':
			// Use Params
				$imgpath = JPATH_SITE .'/images/M_images';
				for ( $i = 1; $i < 7; $i++ ) {
					if ( $params->get( 'indent_image'. $i ) == '-1' ) {
						$img[$i] = NULL;
					} else {
						$img[$i] = '<img src="'. $imgpath .'/'. $params->get( 'indent_image'. $i ) .'" alt="" />';
					}
				}
				break;

			case '3':
			// None
				for ( $i = 1; $i < 7; $i++ ) {
					$img[$i] = NULL;
				}
				break;

			default:
			// Template
				$imgpath = JPATH_SITE .'/templates/'. $cur_template .'/images';
				for ( $i = 1; $i < 7; $i++ ) {
					$img[$i] = '<img src="'. $imgpath .'/indent'. $i .'.png" alt="" />';
				}
				break;
		}

		$indents = array(
				// block prefix / item prefix / item suffix / block suffix
				array( '<table width="100%" border="0" cellpadding="0" cellspacing="0">', '<tr align="left"><td>' , '</td></tr>', '</table>' ),
				array( '', '<div class="first-level">'. $img[1] , '</div>', '' ),
				array( '', '<div class="second-level">'. $img[2] , '</div>', '' ),
				array( '', '<div class="third-level">'. $img[3] , '</div>', '' ),
		);

		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		$ml_first_hide = $params->get('ml_first_hidden');

		$n = false;
		foreach ($rows as $v ) {
			if( $n==false && $ml_first_hide==1 ) {
				unset ($v);
				$n = true;

			}else {
				$pt		= $v->parent;
				$list	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - collect 'open' menus
		$open	 = array( $Itemid );
		$count	 = 20; // maximum levels - to prevent runaway loop
		$id	 = $Itemid;

		while (--$count) {
			if (isset($rows[$id]) && $rows[$id]->parent > 0) {
				$id = $rows[$id]->parent;
				$open[] = $id;
			} else {
				break;
			}
		}
		mosRecurseVIMenuMLZ( 0, 0, $children, $open, $indents, $params );
	}

	/**
	 * Utility function to recursively work through a vertically indented
	 * hierarchial menu
	 */

	function mosRecurseVIMenuMLZ( $id, $level, &$children, &$open, &$indents, &$params ) {
		if (@$children[$id]) {
			$n = min( $level, count( $indents )-1 );
			echo "\n".$indents[$n][0];
			foreach ($children[$id] as $row) {
				echo "\n".$indents[$n][1];
				echo mosGetJoostinaLink( $row, $level, $params, $open );
				// show menu with menu expanded - submenus visible
				if ( !$params->get( 'expand_menu' ) ) {
					if ( in_array( $row->id, $open )) {
						mosRecurseVIMenuMLZ( $row->id, $level+1, $children, $open, $indents, $params );
					}
				} else {
					mosRecurseVIMenuMLZ( $row->id, $level+1, $children, $open, $indents, $params );
				}
				echo $indents[$n][2];
			}
			echo "\n".$indents[$n][3];
		}
	}



	function mosJoostinaShowLink ($params, $style=0) {

		$ml_module_number = $params->get('ml_module_number');
		$ml_rollover_use = $params->get('ml_rollover_use');
		if ($ml_rollover_use == 1) {
			$ml_image1 = $params->get('ml_image1');
			$ml_image2 = $params->get('ml_image2');
			$ml_image3 = $params->get('ml_image3');
			$ml_image4 = $params->get('ml_image4');
			$ml_image5 = $params->get('ml_image5');
			$ml_image6 = $params->get('ml_image6');
			$ml_image7 = $params->get('ml_image7');
			$ml_image8 = $params->get('ml_image8');
			$ml_image9 = $params->get('ml_image9');
			$ml_image10 = $params->get('ml_image10');
			$ml_image11 = $params->get('ml_image11');
			$ml_image_roll_1 = $params->get('ml_image_roll_1');
			$ml_image_roll_2 = $params->get('ml_image_roll_2');
			$ml_image_roll_3 = $params->get('ml_image_roll_3');
			$ml_image_roll_4 = $params->get('ml_image_roll_4');
			$ml_image_roll_5 = $params->get('ml_image_roll_5');
			$ml_image_roll_6 = $params->get('ml_image_roll_6');
			$ml_image_roll_7 = $params->get('ml_image_roll_7');
			$ml_image_roll_8 = $params->get('ml_image_roll_8');
			$ml_image_roll_9 = $params->get('ml_image_roll_9');
			$ml_image_roll_10 = $params->get('ml_image_roll_10');
			$ml_image_roll_11 = $params->get('ml_image_roll_11');

			?>
<script type="text/javascript">
	offImgArray["ml_img_1_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_2_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_3_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_4_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_5_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_6_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_7_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_8_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_9_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_10_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_11_<?php echo $ml_module_number; ?>"] = new Image ();
	offImgArray["ml_img_1_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image1; ?>";
	offImgArray["ml_img_2_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image2; ?>";
	offImgArray["ml_img_3_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image3; ?>";
	offImgArray["ml_img_4_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image4; ?>";
	offImgArray["ml_img_5_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image5; ?>";
	offImgArray["ml_img_6_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image6; ?>";
	offImgArray["ml_img_7_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image7; ?>";
	offImgArray["ml_img_8_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image8; ?>";
	offImgArray["ml_img_9_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image9; ?>";
	offImgArray["ml_img_10_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image10; ?>";
	offImgArray["ml_img_11_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image11; ?>";

	onImgArray["ml_img_1_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_2_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_3_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_4_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_5_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_6_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_7_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_8_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_9_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_10_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_11_<?php echo $ml_module_number; ?>"] = new Image ();
	onImgArray["ml_img_1_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_1; ?>";
	onImgArray["ml_img_2_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_2; ?>";
	onImgArray["ml_img_3_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_3; ?>";
	onImgArray["ml_img_4_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_4; ?>";
	onImgArray["ml_img_5_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_5; ?>";
	onImgArray["ml_img_6_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_6; ?>";
	onImgArray["ml_img_7_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_7; ?>";
	onImgArray["ml_img_8_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_8; ?>";
	onImgArray["ml_img_9_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_9; ?>";
	onImgArray["ml_img_10_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_10; ?>";
	onImgArray["ml_img_11_<?php echo $ml_module_number; ?>"].src = "<?php echo JPATH_SITE.'/modules/mod_mljoostinamenu/menuimages/'.$ml_image_roll_11; ?>";
</script>
			<?php
		}
		switch ($style) {

			// вывод горизонтальной таблицей
			case 1:
				echo '<table class="menutable'.$params->get('moduleclass_sfx').'" cellspacing="0" cellpadding="0" border="0"><tr>';
				mosJoostinaPrepareLink($params,1);
				echo '</tr></table>';
				break;

			// вывод списком
			case 2:
				echo '<ul class="menulist'.$params->get('moduleclass_sfx').'">';
				mosJoostinaPrepareLink($params,2);
				echo '</ul>';
				break;

			// вывод чистых ссылок
			case 3:
				mosJoostinaPrepareLink($params,3);
				break;

			// вывод в 100% ширины
			case 4:
				echo '<table class="menutable'.$params->get('moduleclass_sfx').'" cellspacing="0" cellpadding="0" border="0" width="100%"><tr>';
				mosJoostinaPrepareLink($params,4);
				echo '</tr></table>';
				break;

			case 5:
				echo '<div class="maindiv">';
				mosJoostinaPrepareLink($params,5);
				echo '</div>';
				break;

			case 6:
				echo '<table class="menutable'.$params->get('moduleclass_sfx').'" cellspacing="0" cellpadding="0" border="0" >';
				mosJoostinaPrepareLink($params,6);
				echo '</table>';
				break;

			default:
				echo 'empty';
				break;
		}
	}
}

$params->def('menutype', 'mainmenu');

$config_caching = $mainframe->getCfg('caching');

if($config_caching) {
	$menu_cache = mosCache::getCache('mod_mljoostinamenu');
}
// убираем лишний элемент
unset($params->_raw);

switch ($params->get( 'menu_style' ) ) {

	case 'horizontal':
		echo $config_caching ? $menu_cache->call('mosJoostinaShowLink',$params,1,$Itemid) : mosJoostinaShowLink($params,1,$Itemid);
		break;

	case 'ulli':
		echo $config_caching ?  $menu_cache->call('mosJoostinaShowLink',$params,2,$Itemid) : mosJoostinaShowLink($params,2,$Itemid);
		break;

	case 'linksonly':
		echo $config_caching ?  $menu_cache->call('mosJoostinaShowLink',$params,3,$Itemid) : mosJoostinaShowLink($params,3,$Itemid);
		break;

	case 'horiz_tab':
		echo $config_caching ?  $menu_cache->call('mosJoostinaShowLink',$params,4,$Itemid) : mosJoostinaShowLink($params,4,$Itemid);
		break;

	case 'divs':
		echo $config_caching ?  $menu_cache->call('mosJoostinaShowLink',$params,5,$Itemid) : mosJoostinaShowLink($params,5,$Itemid);
		break;

	case 'ml_vertical':
		echo $config_caching ?  $menu_cache->call('mosJoostinaShowLink',$params,6,$Itemid) : mosJoostinaShowLink($params,6,$Itemid);
		break;

	default:
		echo $config_caching ?  $menu_cache->call('mosShowVIMenuMLZ',$params,$Itemid) : mosShowVIMenuMLZ($params,$Itemid);
		break ;
}
unset($menu_cache,$params,$config,$config_caching);