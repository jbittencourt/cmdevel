<?
/**
 * @package cmdevel
 **/

$inc_path = get_include_path();
$includes = get_included_files();
$mydir = dirname($includes[count($includes)-1]);

$incs = explode(":",$inc_path);
$configured = 0;
foreach($incs as $dir) {
  if($dir==$mydir) {
    $configured = 1;
    break;
  }
}

if(!$configured) {
  $inc_path = $inc_path.":$mydir";
  set_include_path($inc_path);
}



function __cmautoload($classname) {
  global $_CMDEVEL;
  if(array_key_exists($classname,$_CMDEVEL["class_register"])) {
    include($_CMDEVEL['path'].'/'.$_CMDEVEL["class_register"][$classname]);
    return true;
  } else {
    Throw new CMException('Cannot find class '.$classname);
  }
}

//include exceptions
include("exceptions/debugUtils.inc.php");
include("exceptions/cmexception.inc.php");
include("exceptions/cmnestedexception.inc.php");

?>
