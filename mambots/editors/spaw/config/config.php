<?php

require_once(dirname(__FILE__).'/../class/config.class.php');
require_once(dirname(__FILE__).'/../class/util.class.php');
function getJSpawParam($name,$default=0){
	global $j_spaw_config;
	return isset($j_spaw_config) && array_key_exists($name, $j_spaw_config) && $j_spaw_config[$name] != '' ?
		$j_spaw_config[$name]
		: $default;
}
function setJSpawParams($name,$value,$transfer_type=false,$default_first=false){
	global $j_spaw_config;
	if ($transfer_type === false) $transfer_type=SPAW_CFG_TRANSFER_NONE;
	if (isset($j_spaw_config) && array_key_exists($name, $j_spaw_config) && $j_spaw_config[$name] != '') //если параметр есть в j_spaw_config, берем оттуда
		if (is_array($value)){
			$parr = preg_split("/[\n\r]+/", $j_spaw_config[$name]);
			$sp = is_array($default_first) ? $default_first : array();
			foreach ($parr as $itm) 
				if (preg_match('/^(?i:<br.*?>|\s)*(\S+)?((?:\s+)([^<]+))?/',$itm,$m) && isset($m[1]))
					$sp[$m[1]] = isset($m[3]) ? $m[3] : $m[1];
			SpawConfig::setStaticConfigItem($name, $sp, $transfer_type);
		} else
			SpawConfig::setStaticConfigItem($name, $j_spaw_config[$name], $transfer_type);
	else
		SpawConfig::setStaticConfigItem($name, $value, $transfer_type);
}

if (defined( '_JLINDEX' )) {//если запуск в среде Joo..., загружаем параметры мамбота
	global $database, $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_lang, $mainframe, $j_spaw_config, $my;
	if (!session_id()) 
		session_start();
	// Get Mambot Parameters
	$query = "SELECT id FROM #__mambots WHERE element='spaw' AND folder='editors'";
	$database->setQuery( $query );
	$id = $database->loadResult();
	$mambot = new mosMambot( $database );
	$mambot->load( $id );
	$params = new mosParameters( $mambot->params );
	$j_spaw_config = $params->toArray();
	SpawConfig::setStaticConfigItem('SITE_PATH', $mosConfig_absolute_path,SPAW_CFG_TRANSFER_SECURE);
	SpawConfig::setStaticConfigItem('SITE_URL', $mosConfig_live_site);
	$site_dir = preg_replace('|^http://[^/]+|','',$mosConfig_live_site);
	SpawConfig::setStaticConfigItem('SITE_DIR', $site_dir ,SPAW_CFG_TRANSFER_SECURE);//url без домена
}
if(!defined('JPATH_BASE')) define('JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);
// sets physical filesystem directory of web site root
// if calculation fails (usually if web server is not apache) set this manually
SpawConfig::setStaticConfigItem('DOCUMENT_ROOT', JPATH_BASE.'/');
// sets physical filesystem directory where spaw files reside
// should work fine most of the time but if it fails set SPAW_ROOT manually by providing correct path
SpawConfig::setStaticConfigItem('SPAW_ROOT', JPATH_BASE.'/mambots/editors/spaw/');
// sets virtual path to the spaw directory on the server
// if calculation fails set this manually
SpawConfig::setStaticConfigItem('SPAW_DIR', '/mambots/editors/spaw/');
//error_log("\n".SpawConfig::getStaticConfigValue('DOCUMENT_ROOT').",".SpawConfig::getStaticConfigValue('SPAW_ROOT').",".SpawConfig::getStaticConfigValue('SPAW_DIR')."\n",3,'/tmp/spaw.log');
// DEFAULTS used when no value is set from code
// language
SpawConfig::setStaticConfigItem('default_lang','ru');
// output charset (empty strings means charset specified in language file)
SpawConfig::setStaticConfigItem('default_output_charset','utf-8');
// theme
SpawConfig::setStaticConfigItem('default_theme','spaw2');
// toolbarset
setJSpawParams('default_toolbarset','all');
// stylesheet
setJSpawParams('default_stylesheet',SpawConfig::getStaticConfigValue('SPAW_DIR').'wysiwyg.css');
// width
setJSpawParams('default_width','100%');
// height
setJSpawParams('default_height','400px');

// specifies if language subsystem should use iconv functions to convert strings to the specified charset
SpawConfig::setStaticConfigItem('USE_ICONV',false);
// specifies rendering mode to use: "xhtml" - renders using spaw's engine, "builtin" - renders using browsers engine
setJSpawParams('rendering_mode', 'xhtml', SPAW_CFG_TRANSFER_JS);
// specifies that xhtml rendering engine should indent it's output
setJSpawParams('beautify_xhtml_output', true, SPAW_CFG_TRANSFER_JS);
// specifies host and protocol part (like http://mydomain.com) that should be added to urls returned from file manager (and probably other places in the future)
setJSpawParams('base_href', '', SPAW_CFG_TRANSFER_JS);
// specifies if spaw should strip domain part from absolute urls (IE makes all links absolute)
setJSpawParams('strip_absolute_urls', true, SPAW_CFG_TRANSFER_JS);
// specifies in which directions resizing is allowed (values: none, horizontal, vertical, both)
setJSpawParams('resizing_directions', 'vertical', SPAW_CFG_TRANSFER_JS);
// specifies that special characters should be converted to the respective html entities
setJSpawParams('convert_html_entities', false, SPAW_CFG_TRANSFER_JS);

// data for fonts dropdown list
SpawConfig::setStaticConfigItem("dropdown_data_core_fontname",
  array(
    'Arial' => 'Arial',
    'Courier' => 'Courier',
    'Tahoma' => 'Tahoma',
    'Times New Roman' => 'Times',
    'Verdana' => 'Verdana'
  )
);
// data for fontsize dropdown list
SpawConfig::setStaticConfigItem("dropdown_data_core_fontsize",
  array(
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6'
  )
);
// data for paragraph dropdown list
SpawConfig::setStaticConfigItem("dropdown_data_core_formatBlock",
  array(
    '<p>' => 'Normal',
    '<H1>' => 'Heading 1',
    '<H2>' => 'Heading 2',
    '<H3>' => 'Heading 3',
    '<H4>' => 'Heading 4',
    '<H5>' => 'Heading 5',
    '<H6>' => 'Heading 6',
    '<pre>' => 'Preformatted',
    '<address>' => 'Address'
  )
);
// data for link targets drodown list in hyperlink dialog
SpawConfig::setStaticConfigItem("a_targets",
  array(
    '_self' => 'Self',
    '_blank' => 'Blank',
    '_top' => 'Top',
    '_parent' => 'Parent'
  )
);


// toolbar sets (should start with "toolbarset_"
// standard core toolbars
SpawConfig::setStaticConfigItem('toolbarset_standard',
  array(
    "format" => "format",
    "style" => "style",
    "edit" => "edit",
    "table" => "table",
    "plugins" => "plugins",
    "insert" => "insert",
    "tools" => "tools"
  )
);
// all core toolbars
SpawConfig::setStaticConfigItem('toolbarset_all',
  array(
    "format" => "format",
    "style" => "style",
    "edit" => "edit",
    "table" => "table",
    "plugins" => "plugins",
    "insert" => "insert",
    "tools" => "tools",
    "font" => "font"
  )
);
// user toolbars
/*
setJSpawParams('toolbarset_user',
  array(
    "format" => "format",
    "style" => "style",
    "edit" => "edit",
    "table" => "table",
    "plugins" => "plugins",
    "insert" => "insert",
    "tools" => "tools",
    "font" => "font"
  )
);*/
// mini core toolbars
SpawConfig::setStaticConfigItem('toolbarset_mini',
  array(
    "format" => "format_mini",
    "edit" => "edit",
    "tools" => "tools"
  )
);

if (defined( '_JLINDEX' )) {//если запуск в среде Joo..., дозагружаем параметры мамбота
	global $mosConfig_absolute_path, $mosConfig_live_site, $j_spaw_config, $my;
	// data for style (css class) dropdown in table properties dialog
	setJSpawParams("table_styles",
	  array(
	    '' => 'Normal',
	    'moduletable' => 'moduletable',
	    'content' => 'content',
	    'contenttoc' => 'contenttoc',
	    'contentpane' => 'contentpane',
		'prctable' => 'prctable'
	  ),
	  SPAW_CFG_TRANSFER_SECURE,
	  array('' => 'Normal')
	);
	// data for style (css class) dropdown list
	setJSpawParams("dropdown_data_core_style",
	  array(
	    '' => 'Normal',
	    'contact_email' => 'contact_email',
	    'sectiontableheader' => 'sectiontableheader',
	    'sectiontableentry1' => 'sectiontableentry1',
	    'sectiontableentry2' => 'sectiontableentry2',
	    'date' => 'date',
	    'small' => 'small',
	    'smalldark' => 'smalldark',
	    'contentheading' => 'contentheading',
	    'footer' => 'footer',
	    'lcol' => 'lcol',
	    'rcol' => 'rcol',
	    'contentdescription' => 'contentdescription',
	    'blog_more' => 'blog_more'
	  ),
	  false,
	  array('' => 'Normal')
	);
	$userdir = '';
	if(@$j_spaw_config['user_dir'] == 1) {
		$userdir = $mosConfig_absolute_path.'/images/stories/users';
		is_dir($userdir) or mkdir($userdir) or die("Error creating dir $userdir !");
		$userdir .= '/'.$my->id;
		is_dir($userdir) or mkdir($userdir) or die("Error creating dir $userdir !");
		$userdir = 'users/'.$my->id.'/';
	}
	$mediadir = $site_dir.'/images/stories/'.$userdir;
	SpawConfig::setStaticConfigItem(
	  'PG_SPAWFM_DIRECTORIES',
	  array(
	    array(
	      'dir'     => $mediadir,
	      'fsdir'   => $mosConfig_absolute_path.'/images/stories/'.$userdir, // optional absolute physical filesystem path
	      'caption' => 'Flash movies',
	      'params'  => array(
	        'allowed_filetypes' => array('flash')
	      )
	    ),
	    array(
	      'dir'     => $mediadir,
	      'fsdir'   => $mosConfig_absolute_path.'/images/stories/'.$userdir, // optional absolute physical filesystem path
	      'caption' => 'Images',
	      'params'  => array(
	        'default_dir' => true, // set directory as default (optional setting)
	        'allowed_filetypes' => array('images')
	      )
	    ),
	    array(
	      'dir'     => $mediadir,
	      'fsdir'   => $mosConfig_absolute_path.'/images/stories/'.$userdir, // optional absolute physical filesystem path
	      'caption' => 'Files',
	      'params'  => array(
	        'allowed_filetypes' => array('any')
	      )
	    ),
	  ),
	  SPAW_CFG_TRANSFER_SECURE
	);

	$tempcss = '';
	if( @$j_spaw_config['template'] == 1 ) { //если используем css шаблона
		$tempcss = $mosConfig_live_site.'/templates/'.$mainframe->getTemplate().'/css/template_css.css';
		if (!file_exists($tempcss)) {//для FrosTPK : )
			$database->setQuery( "SELECT template FROM #__templates_menu WHERE client_id='0' AND menuid='0'" );
			$tempcss = $mosConfig_live_site.'/templates/'.$database->loadResult().'/css/template_css.css';
		}
	}
	if($tempcss)
		SpawConfig::setStaticConfigItem('default_stylesheet',$tempcss);
		
	// colorpicker config
	SpawConfig::setStaticConfigItem('colorpicker_predefined_colors',
	  array(
	    'black',
	    'silver',
	    'gray',
	    'white',
	    'maroon',
	    'red',
	    'purple',
	    'fuchsia',
	    'green',
	    'lime',
	    'olive',
	    'yellow',
	    'navy',
	    'blue',
	    '#fedcba',
	    'aqua'
	  ),
	  SPAW_CFG_TRANSFER_SECURE
	);

	// SpawFm plugin config:

	// global filemanager settings
	SpawConfig::setStaticConfigItem(
	  'PG_SPAWFM_SETTINGS',
	  array(
	    'allowed_filetypes'   => array('any'),  // allowed filetypes groups/extensions
	    'allow_modify'        => getJSpawParam('allow_modify',false),  // allow edit filenames/delete files in directory
	    'allow_upload'        => getJSpawParam('allow_upload',true),   // allow uploading new files in directory
	    //'chmod_to'          => 0777,          // change the permissions of an uploaded file if allowed
	                                            // (see PHP chmod() function description for details), or comment out to leave default
	    'max_upload_filesize' => getJSpawParam('max_upload_filesize', 200000),             // max upload file size allowed in bytes, or 0 to ignore
	    'max_img_width'       => 0,             // max uploaded image width allowed, or 0 to ignore
	    'max_img_height'      => 0,             // max uploaded image height allowed, or 0 to ignore
	    'recursive'           => true,         // allow using subdirectories
	    'allow_modify_subdirectories' => false, // allow renaming/deleting subdirectories
	    'allow_create_subdirectories' => true, // allow creating subdirectories
	    'forbid_extensions'   => array('php'),  // disallow uploading files with specified extensions
	    'forbid_extensions_strict' => true,     // disallow specified extensions in the middle of the filename
	  ),
	  SPAW_CFG_TRANSFER_SECURE
	);
	// directories
}

?>
