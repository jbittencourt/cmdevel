<?
/**
 * @package cmdevel
 * @subpackage cmcli
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMConfigFileDom extends DomDocument { 
  
  private $url;
  private $db_host, $db_user, $db_password, $db_name;
  
  function __construct() {
    parent::__construct(); 

    $this->loadXML("<cmapp></cmapp>");

    $cm = $this->createElement("cmdevel");
    //$this->documentElement->appendChild($cm);
    $this->documentElement->appendChild($cm);

    $app = $this->createElement("app");
    $this->documentElement->appendChild($app);

    $this->url = $this->createElement("url");
    $app->appendChild($this->url);

    $this->db = $this->createElement("database");
    $app->appendChild($this->db);

    $this->db_driver = $this->createElement("driver");
    $this->db_driver->appendChild($this->createTextNode("mysql"));
    $this->db->appendChild($this->db_driver);

    $this->db_host = $this->createElement("host");
    $this->db->appendChild($this->db_host);
    
    $this->db_user = $this->createElement("user");
    $this->db->appendChild($this->db_user);

    $this->db_password = $this->createElement("password");
    $this->db->appendChild($this->db_password);

    $this->db_name = $this->createElement("name");
    $this->db->appendChild($this->db_name);
    $app->appendChild($this->db);

    $userclass = $this->createElement("userclass");
    $class = $this->createElement("class");
    $class->appendChild($this->createTextNode("CMUser"));
    $userclass->appendChild($class);
    $userclass->appendChild($this->createElement("lib"));
    $app->appendChild($userclass);
  }

  public function setDBHost($name) {
    $this->db_host->appendChild($this->createTextNode($name));
  }

  public function setDBName($name) {
    $this->db_name->appendChild($this->createTextNode($name));
  }

  public function setDBUser($name) {
    $this->db_user->appendChild($this->createTextNode($name));
  }

  public function setDBPassword($name) {
    $this->db_password->appendChild($this->createTextNode($name));
  }

  public function setURL($name) {
    $this->url->appendChild($this->createTextNode($name));
  }

}

?>