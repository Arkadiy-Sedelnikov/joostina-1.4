<?php
/**
 * SPAW Editor v.2 Javascript file
 *
 * Outputs javascript code for the editor 
 * @package spaw2
 * @subpackage JavaScript  
 * @author Alan Mendelevich <alan@solmetra.lt> 
 * @copyright UAB Solmetra
 */
//header('Content-Type: application/x-javascript; charset=utf-8'); 

require_once(str_replace('\\\\','/',dirname(__FILE__)).'/../config/config.php');
require_once(str_replace('\\\\','/',dirname(__FILE__)).'/../class/util.class.php');

$agent = SpawAgent::getAgentName();
global $alljs_exist_ok, $spaw_js_list, $alljs_time, $spaw_js_inc;
$js_file_name = "spaw2alljs$agent.js";
if(!defined( '_VALID_MOS' )) {//вызов из диалога
	$spaw_js_inc = "<script language=\"javascript\" type=\"text/javascript\" src=\"".$config->getConfigValue('SITE_DIR')."/cache/$js_file_name\"></script>\n";
	return(0);
}
$alljs_exist_ok = true;
$spaw_js_list = array();
$agent_js_file = SpawConfig::getStaticConfigValue('SITE_PATH')."/cache/$js_file_name";
$alljs_time = file_exists($agent_js_file) ? filemtime($agent_js_file) : 0;
$spaw_js_inc = "<script language=\"javascript\" type=\"text/javascript\" src=\"".SpawConfig::getStaticConfigValue('SITE_DIR')."/cache/$js_file_name\"></script>\n";
$spaw_root = SpawConfig::getStaticConfigValue("SPAW_ROOT");
function add_file($fn) {
	global $alljs_exist_ok, $spaw_js_list, $alljs_time;
	if (strtolower(substr($fn,-3)) == '.js' && !is_dir($fn)) {
		$alljs_exist_ok = $alljs_exist_ok && $alljs_time > filemtime($fn);
		$spaw_js_list[] = $fn;
	}
}

if (is_dir($spaw_root.'js/common'))
{
  if ($dh = opendir($spaw_root.'js/common')) 
  {
    while (($fn = readdir($dh)) !== false)
		add_file($spaw_root.'js/common/'.$fn);
    closedir($dh);
  }
}
// load main javascript specific for current browser
if (is_dir($spaw_root.'js/'.$agent))
{
  if ($dh = opendir($spaw_root.'js/'.$agent)) 
  {
    while (($fn = readdir($dh)) !== false) 
        add_file($spaw_root.'js/'.$agent.'/'.$fn);
    closedir($dh);
  }
}

// load plugin javascript
$pgdir = $spaw_root.'plugins/';
if (is_dir($pgdir)) 
{
  if ($dh = opendir($pgdir)) 
  {
    while (($pg = readdir($dh)) != false) 
    {
      if ($pg != '.' && $pg != '..')
      {
        // load javascript for all browsers
        if (is_dir($pgdir.$pg.'/js/common'))
        {
          if ($pgdh = opendir($pgdir.$pg.'/js/common')) 
          {
            while (($fn = readdir($pgdh)) !== false) 
                add_file($pgdir.$pg.'/js/common/'.$fn);
            closedir($pgdh);
          }
        }
        // load javascript for current browser
        if (is_dir($pgdir.$pg.'/js/'.$agent))
        {
          if ($pgdh = opendir($pgdir.$pg.'/js/'.$agent)) 
          {
            while (($fn = readdir($pgdh)) !== false) 
                add_file($pgdir.$pg.'/js/'.$agent.'/'.$fn);
            closedir($pgdh);
          }
        }
        // theme scripts
        if (is_dir($pgdir.$pg.'/lib/theme'))
        {
          if ($tdh = opendir($pgdir.$pg.'/lib/theme'))
          {
            while(($th = readdir($tdh)) != false)
            {
              if ($th != '.' && $th != '..')
              {
                // load javascript for all browsers
                if (is_dir($pgdir.$pg.'/lib/theme/'.$th.'/js/common'))
                {
                  if ($thdh = opendir($pgdir.$pg.'/lib/theme/'.$th.'/js/common')) 
                  {
                    while (($fn = readdir($thdh)) !== false) 
                        add_file($pgdir.$pg.'/lib/theme/'.$th.'/js/common/'.$fn);
                    closedir($thdh);
                  }
                }
                // load javascript for current browser
                if (is_dir($pgdir.$pg.'/lib/theme/'.$th.'/js/'.$agent))
                {
                  if ($thdh = opendir($pgdir.$pg.'/lib/theme/'.$th.'/js/'.$agent)) 
                  {
                    while (($fn = readdir($thdh)) !== false) 
                        add_file($pgdir.$pg.'/lib/theme/'.$th.'/js/'.$agent.'/'.$fn);
                    closedir($thdh);
                  }
                }
              }
            }
          }
        }
      }
    }
    closedir($dh);
  }
}    
$mycrc='//###'.crc32(implode('',$spaw_js_list)).'###';
if ($alljs_exist_ok) {
	$of = fopen ($agent_js_file,'r');
	$alljs_exist_ok = $mycrc == fread($of,strlen($mycrc));
	fclose($of);
}
if (!$alljs_exist_ok) {
	$of = fopen ($agent_js_file,'w');
	fwrite($of,"$mycrc\n");
	if (substr(PHP_VERSION,0,1) >= 5) {
		include(dirname(__FILE__).'/jsmin.php');
		foreach ($spaw_js_list as $fn) {
			$jsMin = new JSMin($fn);
			fwrite($of,$jsMin -> minify());
		}
	} else 
		foreach ($spaw_js_list as $fn) 
			fwrite($of,implode( '', file($fn)));
	fclose($of);
}
?>
