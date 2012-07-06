<?
/**
 * Short descrition
 *
 * Long description (can contatin many lines)
 *
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 * @todo You have something do finish in the future
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

class CMACLWorld extends CMObj {

  public function configure() {
     $this->setTable("ACLWorld");

     $this->addField("codeACO",CMObj::TYPE_INTEGER,"20",1,0,0);
     $this->addField("privilege",CMObj::TYPE_VARCHAR,"100",1,0,0);

     $this->addPrimaryKey("codeACO");
     $this->addPrimaryKey("privilege");
  }
}

?>