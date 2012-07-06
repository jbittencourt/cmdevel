<?

/**
 * @package cmdevel
 * @subpackage cmapp
 **/
class CMConfig {


  private $xml_config_file;
  
  public function __construct ($config_file) {
    
    $ret = $this->xml_config_file = simplexml_load_file($config_file);
    if(!$ret) {
      throw  new CMErrorLoadingConfigFile();
    }
  }

  public function getObj() {
    return $this->xml_config_file;
  }


}



?>