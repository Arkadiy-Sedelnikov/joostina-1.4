<?php
function getListOptions(){
	global $pl, $params, $jce, $auth;

	$options = '<option value="">--' . $pl['select_link_type'] . '--</option>';
	
	if( $jce->getAuthOption( 'section', '18' ) ){
		$options .= '<option value="section_link">' . $pl['section'] . '</option>';
	}
	if( $jce->getAuthOption( 'category', '18' ) ){
		$options .= '<option value="section_select_category">' . $pl['category'] . '</option>';
	}
	if( $jce->getAuthOption( 'category', '18' ) ){
		$options .= '<option value="section_select_article">' . $pl['article'] . '</option>';
	}
	if( $jce->getAuthOption( 'static', '18' ) ){
		$options .= '<option value="static_link">' . $pl['static'] . '</option>';
	}
	if( $jce->getAuthOption( 'weblink', '18' ) ){
		$options .= '<option value="category_select_weblink">' . $pl['weblink'] . '</option>';
	}
	if( $jce->getAuthOption( 'contact', '18' ) ){	
		$options .= '<option value="category_select_contact">' . $pl['contact'] . '</option>';
	}
	if( $jce->getAuthOption( 'menu', '18' ) ){
		$options .= '<option value="menu_link">' . $pl['menu'] . '</option>';
	}
	return $options;
}
function getByType( $type, $id='' ){
	global $database, $mainframe, $pl, $jce;
	
	$mainframe->getBlogSectionCount();
	$mainframe->getBlogCategoryCount();
	$mainframe->getGlobalBlogSectionCount();
	$section_content = getSection();
	$div = 'list_level_1';
	switch( $type ){
		case 'section_link':
			$label = $pl['select_section'];
			$onchange = "insertLink(this.value);";
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_section'] . '--</option>';
			foreach( $section_content as $section ){
				$html .= '<option value="index.php?option=com_content&amp;task=view&amp;id=' . $section->value . '">' . $section->text . '</option>';
			}
			break;
		case 'section_select_category':
		case 'section_select_article':
			$label = $pl['select_section'];
			$this_type = ( $type == 'section_select_category' ) ? 'category_link' : 'category_select_article';
			$onchange = "if(this.value!=''){loadType('". $this_type ."', this.value);}";
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_section'] . '--</option>';
			foreach( $section_content as $section ){
				$html .= '<option value="' . $section->value . '">' . $section->text . '</option>';
			}
			break;
		case 'category_link':
			$label = $pl['select_category'];
			$category_content = getCategory( $id );
			$onchange = "insertLink(this.value);";
			$div = 'list_level_2';		
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_category'] . '--</option>';
			foreach( $category_content as $category ){
				$itemid = $mainframe->getItemId( $category->value );
				if( !$itemid ) $itemid = '1';
				$html .= '<option value="index.php?option=com_content&amp;task=category&amp;sectionid=' . $id . '&amp;id=' . $category->value . '&amp;Itemid=' . $itemid . '">' . $category->text . '</option>';
			}
			break;
		case 'category_select_article':
		case 'category_select_weblink':
		case 'category_select_contact':
			$onchange = "if(this.value!=''){loadType('article_link', this.value);}";
			$div = 'list_level_2';
			if( $type == 'category_select_weblink' ) {
				$id = 'com_weblinks';
				$onchange = "if(this.value!=''){loadType('weblink_link', this.value);}";
				$div = 'list_level_1';
			}
			if( $type == 'category_select_contact' ) {
				$id = 'com_contact_details';
				$onchange = "if(this.value!=''){loadType('contact_link', this.value);}";
				$div = 'list_level_1';
			}
			$label = $pl['select_category'];
			$category_content = getCategory( $id );
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_category'] . '--</option>';
			foreach( $category_content as $content ){
				$html .= '<option value="' . $content->value . '">' . $content->text . '</option>';
			}
			break;
		case 'article_link':
			$label = $pl['select_article'];
			$article_content = getArticle( $id );
			$onchange = "insertLink(this.value);";
			$div = 'list_level_3';
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_article'] . '--</option>';
			foreach( $article_content as $article ){
				$itemid = $mainframe->getItemId( $article->value );
				if( !$itemid ) $itemid = '1'; 
				$html .= '<option value="index.php?option=com_content&amp;task=view&amp;id=' . $article->value . '&amp;Itemid=' . $itemid . '">' . $article->text . '</option>';
			}
			break;
		case 'weblink_link':
			$label = $pl['select_weblink'];
			$weblink_content = getWeblink( $id );
			$onchange = "insertLink(this.value);";
			$div = 'list_level_2';
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_weblink'] . '--</option>';
			foreach( $weblink_content as $weblink ){
				$html .= '<option value="index.php?option=com_weblinks&task=view&catid=' .$id  . '&amp;id=' . $weblink->value . '">' . $weblink->text . '</option>';
			}
			break;
		case 'contact_link':
			$label = $pl['select_contact'];
			$contact_content = getContact( $id );
			$onchange = "insertLink(this.value);";
			$div = 'list_level_2';
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_contact'] . '--</option>';
			foreach( $contact_content as $contact ){
				$itemid = $mainframe->getItemId( $contact->value );
				if( !$itemid ) $itemid = '1'; 
				$html .= '<option value="index.php?option=com_contact&amp;task=view&amp;contact_id=' . $contact->value. '&amp;Itemid="' . $itemid . '>' . $contact->text . '</option>';
			}
			break;
		case 'static_link':
			$static_list = getStatic();
			$onchange = "insertLink(this.value);";
			$div = 'list_level_1';
			$label = $pl['select_static'];
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_static'] . '--</option>';
			foreach( $static_list as $static ){
				$itemid = $mainframe->getItemId( $static->value );
				if( !$itemid ) $itemid = '1'; 
				$html .= '<option value="index.php?option=com_content&amp;task=view&amp;id=' . $static->value . '&amp;Itemid=' . $itemid . '">' . $static->text . '</option>';
			}
			break;
		case 'menu_link':
			$menu_list = getMenu();
			$onchange = "insertLink(this.value);";
			$div = 'list_level_1';
			$label = $pl['select_menu'];
			$html = '<select class="link_select" name="' . $type . '" onchange="' . $onchange . '">';
			$html .= '<option value="">--' . $pl['select_menu'] . '--</option>';
			foreach( $menu_list as $menu ){
				$itemid = ( strpos( $menu->href, '://' ) ) ? '' : '&Itemid=' . $menu->value;
				$html .= '<option value="' . $menu->href . $itemid . '">' . $menu->text . '</option>';
			}
			break;
	}
	$html .= '</select>';
	return "<script>setSelectList('" . $div . "','" . $jce->ajaxHTML( $label ) . "','" . $jce->ajaxHTML( $html ) . "');</script>";
}
function getSection(){
	global $database;
	$query = "SELECT a.id AS value, CONCAT( a.title, ' /', a.name, '' ) AS text
			 FROM #__sections AS a
			 WHERE a.published = '1' AND a.scope='content'
			 ORDER BY a.id";
			$database->setQuery( $query );
			$section_content = $database->loadObjectList( );
			
	return $section_content;
}
function getCategory( $sid ){
	global $database;
	$query = "SELECT a.id AS value, CONCAT( a.title, ' /', a.name, '' ) AS text
			 FROM #__categories AS a
			 WHERE a.published = '1' AND a.section = '". $sid ."'
			 ORDER BY a.id";
	
			$database->setQuery( $query );
			$category_content = $database->loadObjectList( );
			
	return $category_content;
}
function getArticle( $cid ){
	global $database;
	$query = "SELECT a.id as value, CONCAT( a.title, ' /', a.title_alias, '' ) AS text
			 FROM #__content AS a
			 WHERE a.state = '1' AND a.catid = ". $cid ." 
			 ORDER BY a.id";
	
			$database->setQuery( $query );
			$article_content = $database->loadObjectList();
			
	return $article_content;
}
function getWeblink( $cid ){
	global $database;
	$query = "SELECT a.id AS value, a.title AS text, a.url AS href
			 FROM #__weblinks AS a
			 WHERE a.published = '1' AND a.catid = '" . $cid . "'
			 ORDER BY a.title";
	
			$database->setQuery( $query );
			$weblink_content = $database->loadObjectList();
			
	return $weblink_content;
}
function getContact( $cid ){
	global $database;
	$query = "SELECT a.id AS value, CONCAT( a.name, ' /', a.con_position, '' ) AS text
			 FROM #__contact_details AS a
			 WHERE a.published = '1' AND a.catid = '" . $cid . "'
			 ORDER BY a.id";
	
			$database->setQuery( $query );
			$contact_content = $database->loadObjectList();
			
	return $contact_content;
}
function getMenu(){
	global $database;
	$query = "SELECT a.id AS value, a.menutype, CONCAT( a.name, ' /', a.menutype, '' ) AS text, CONCAT( a.link ) as href
			FROM #__menu AS a
			WHERE a.published = '1'
			ORDER BY a.id";
	
			$database->setQuery( $query );
			$menu_list = $database->loadObjectList( );
	return $menu_list;
}
function getStatic(){
	global $database;
	$query = "SELECT a.id AS value, CONCAT( a.title, ' /', a.title_alias, '' ) AS text
			 FROM #__content AS a
			 WHERE a.state = '1' AND a.sectionid = '0'
			 ORDER BY a.id";
	
			$database->setQuery( $query );
			$static_list = $database->loadObjectList( );
			
	return $static_list;
}
