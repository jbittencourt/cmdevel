<?


class CMClassGen {

  protected $name;
  protected $table;
  protected $db;

  function __construct($name,$table,$db) {
    $this->db = $db;
    $this->config = $db->tableInfo($table);
    $this->name = $name;
    $this->table = $table;
  }



  public function defaultComments() {

    $ret   = array();
    $ret[] = "/**";
    $ret[] = " * Short descrition";
    $ret[] = " *";
    $ret[] = " * Long description (can contain many lines)";
    $ret[] = " *";
    $ret[] = " * @author You Name <your@email.org>";
    $ret[] = " * @todo You have something to finish in the future";
    $ret[] = " * @license http://opensource.org/licenses/gpl-license.php GNU Public License";
    $ret[] = " **/";
 
    return implode("\n",$ret);
  }


  /**
   * This function retrives the valid values of an enum.
   * 
   * This function was adpeted from the one written by Mika Tuupola <tuupola@appelsiini.net>,
   * for the DB_DataContainer_Form. This function is too much Mysql centric, and we don't 
   * know how it will operate with other SGBDs.
   *
   * @param Array $field The name of the propertie to retrive the valid types.
   **/
  public function parseField($field) {
    $temp = array();
    $temp[name] = $field->Field;
    $temp[not_null] = !$field->Null;
    if($field->Key=="PRI")
      $temp[pk] = 1;
    else
      $temp[pk] = 0;

    $temp[def] = $field->Default;

    if(strpos($field->Extra,"auto_increment")===false) 
      $temp[autoincrement] = 0;
    else 
      $temp[autoincrement] = 1;

    //dificult part, parse the type argument
    preg_match("/^(\w+)\(?((\d+)|([\w',]+))\)?$/",
	       $field->Type,
	       $matches);
    $temp[type] = $matches[1];

    switch($temp[type]) {
    default:
      $temp[length] = $matches[2];
      break;
    case 'set':
    case 'enum':
      $enum  = str_replace("'", '', $matches[2]);
      $temp[enums] =  explode(',', $enum);
      break;
    }

    return $temp;
  }

  
  public function getInfo() {

    $res = $this->db->query("DESCRIBE $this->table");
    
    while($row = $res->fetch_object()) {
      $field = $this->parseField($row);

      
      switch(strtoupper($field[type])) {
      case "VARCHAR":
	$field[type] = "CMObj::TYPE_VARCHAR"; break;
      case "CHAR":
	$field[type] = "CMObj::TYPE_CHAR"; break;

      case "TINYINT":
      case "SMALLINT":
      case "MEDIUMINT":
      case "INT":
      case "BIGINT":
      case "TIMESTAMP":
	$field[type] ="CMObj::TYPE_INTEGER"; break;

      case "FLOAT":
      case "DOUBLE":
	$field[type] ="CMObj::TYPE_FLOAT"; break;
	
      case "TEXT":
      case "TINYTEXT":
      case "MEDIUMTEXT":
      case "LONGTEXT":
	$field[type] = "CMObj::TYPE_TEXT";
	
      case "TINYBLOB":
      case "BLOB":
      case "MEDIUMBLOB":
      case "LONGBLOB":
	$field[type] = "CMObj::TYPE_BLOB"; 
	
      case "ENUM":
	$field[type] = "CMObj::TYPE_ENUM"; break;
	
      case "SET":
	$field[type] = "CMObj::TYPE_SET"; break;
	
// 	case "DATETIME":
// 	case "DATE":
// 	case "TIME":
// 	case "YEAR":
	
      }
      
      $fields[] = $field;
    }
    return $fields;
  }



  Public function __toString() {
    $fields = array();
    $keys = array();
    $enums = array();
    $conts = array();

    $info = $this->getInfo();

    foreach($info as $field) {

      if($field[pk]) {
	$keys[] = $field[name];
      }

      if($field[type]=="CMObj::TYPE_ENUM") {
	foreach($field[enums] as $val) {
	  $tmp = strtoupper(str_replace(" ","_",$val));
	  $name = "ENUM_".strtoupper($field[name])."_$tmp";
	  $consts[] = array("name"=>$name,
			    "value"=>$val);
	  $values[] = "self::$name";
	  if($field[def] == $val)
	    $field[def] = "self::$name";
	}
	if(!$field[not_null]) {
	  $name = "ENUM_".$tmp_name."_NULL";
	  $consts[] = array("name"=> $name,
			    "value"=>"");
	  $values[] = "self::$name";
	}
	
	$enums[] = array("field"=>$field[name],
			 "values"=>$values);
      }

      if(!(strpos($field[flags],"primary_key")===false)) {
	$keys[] = $field[name];
      }

      $fields[] = "\$this->addField(\"$field[name]\",$field[type],\"$field[length]\",$field[not_null],$field[def],$field[autoincrement])";
      
    }

    

    $str[] = "<?";
    $str[] = $this->defaultComments();
    $str[] = "";
    $str[] = "class $this->name extends CMObj {";
    $str[] = "";

    if(!empty($consts)) {
      foreach($consts as $cons) {
	$str[] = "   const $cons[name] = \"$cons[value]\";";
      }
    }
    
    $str[] = "";
    $str[] = "   public function configure() {";
    $str[] = "     \$this->setTable(\"$this->table\");";
    $str[] = "";
    
    foreach($fields as $field)
      $str[] = "     $field;";

    $str[] = "";

    if(!empty($keys)) {
      foreach($keys as $key)
	$str[] = "     \$this->addPrimaryKey(\"$key\");";
    }
    
    $str[] = "";
    
    if(!empty($enums)) {
      foreach($enums as $enum) {
	$tmp = "     \$this->setEnumValidValues(\"$enum[field]\",array(";
	$tmp.= implode(",\n".str_repeat(" ",strlen($tmp)),$enum[values])."));";
	$str[] = $tmp;
      }
      
    }

    $str[] = "  }";
    $str[] = "  //put your functions here";
    $str[] = "}";
    $str[] = "";
    $str[] = "";
    $str[] = "?>";
    $str[] = "";

    return implode("\n",$str);
  }}
  


?>