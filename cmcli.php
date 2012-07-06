<?

/**
 * @package cmdevel
 * @subpackage cmcli
 **/
 
require 'Console/Getopt.php';
require 'cmdevel.inc.php';
$_CMDEVEL[path] = $mydir;

function __autoload($classname) {
  return __cmautoload($classname);
}

$argv = Console_Getopt::readPHPArgv();
//array_shift($argv);

/** 
 * -s socket
 * -h  dbhost
 * -u db user
 * -d database
 * -p database password
 * -n  name of the project
 * -t table name
 * -c Class Name
 **/

$short_options = "h:u:d:p:n:r:t:c:f:s:";


$options = Console_Getopt::getopt($argv,$short_options);

if($options instanceof PEAR_Error) {
  die("Erro processing parameters: ".$options->message."\n");
}

$opts=$options[0];

$command = (isset($options[1][0])) ? $options[1][0] : null;
switch($command) {

 /**
  * Creates the initial filesystem and config file for an cmdevel project
  **/
 case "init_project":
   $project = array();
   foreach($opts as $opt) {
     switch($opt[0]) {
     case "n":
       $project[name] = $opt[1];
       break;
     case "d":
       $project[database] = $opt[1];
       break;
     case "h":
       $project[host] = $opt[1];
       break;
     case "u":
       $project[user] = $opt[1];
       break;
     case "p":
       $project[password] = $opt[1];
       break;
     case "r":
       $project[url] = $opt[1];
       break;
     }
   }

   if(empty($project[name])) {
     die("You must at least inform the name of the project.\n");
   }

   $n = $project[name];
   $dirs = array("$n",
		 "$n/environment",
		 "$n/etc",
		 "$n/lib");

   foreach($dirs as $dir) {
     @mkdir($dir);
   }


   $dom = new CMConfigFileDom;
   $dom->setURL($project[url]);
   $dom->setDBHost($project[host]);
   $dom->setDBUser($project[user]);
   $dom->setDBName($project[database]);
   $dom->setDBPassword($project[password]);
   $dom->save("$n/etc/config.xml");
   break;


 /**
  * Reads a Table from the database and generate a class from
  * its description.
  **/
 case "gen_class":
   $ops = array();
   $ops[type] = "mysqli";

   foreach($opts as $opt) {
     switch($opt[0]) {
     case "d":
       $ops[database] = $opt[1];
       break;
     case "c":
       $ops["classname"] = $opt[1];
       break;
     case "h":
       $ops[host] = $opt[1];
       break;
     case "u":
       $ops[user] = $opt[1];
       break;
     case "p":
       $ops[password] = $opt[1];
       break;
     case "t":
       $ops[table] = $opt[1];
       break;
     case "s":
       $ops[socket] = $opt[1];
       break;
     case "f":
       include("cmpersistence.inc.php");
       include("cmapp.inc.php");
       $ops[config_file] = $opt[1];
       
       try {
	 $conf_obj = new CMConfig($opt[1]);
       }
       catch(CMErrorLoadingConfigFile $e) {
	 die("Cannot load config file $opt[1].\n");
       }

       $conf = $conf_obj->getObj();
       
       $ops[type] = $conf->app[0]->database[0]->driver;
       $ops[user] = $conf->app[0]->database[0]->user;
       $ops[password] = $conf->app[0]->database[0]->password;
       $ops[host]  = $conf->app[0]->database[0]->host;
       $ops[database] = $conf->app[0]->database[0]->name;
       
     }
   }

   if(empty($ops[table]) || empty($ops["classname"])) {
     die("You must at least inform the Table name and the resulting class name.\n");
   }

   include_once("cmcli/cmclassgen.inc.php");
   include_once("DB.php");

   if(!empty($ops[socket])) {
     $dsn = "mysqli://$ops[user]:$ops[password]@unix($ops[socket])/$ops[database]";
   }
   else {
     if(!empty($ops[password])) {
       $dsn = "mysqli://$ops[user]:$ops[password]@$ops[host]:3306/$ops[database]";
     }
     else {
       $dsn = "mysqli://$ops[user]@$ops[host]:3306/$ops[database]";
     }
   }
   
   $db = new mysqli($ops[host],$ops[user],$ops[password],$ops[database]);

   if(mysqli_connect_errno()) {
     die("Error connecting to database:".mysqli_connect_error()."\n");
   };
   
   echo $ops["classname"].".inc.php\n";
   $classdef = new CMClassGen($ops["classname"],$ops[table],$db);
    //filename
   $fn = strtolower($ops["classname"].".inc.php");

    if (!$handle = fopen($fn, 'a')) {
      echo "Cannot open file ($fn)";
      exit;
    }
    
    echo $classdef->__toString();
    fwrite($handle,$classdef->__toString());
    fclose($handle);
   
}



?>
