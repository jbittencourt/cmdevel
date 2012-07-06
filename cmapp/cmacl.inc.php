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

class CMACL extends CMObj {

  public function configure() {
     $this->setTable("ACL");

     $this->addField("code",CMObj::TYPE_INTEGER,"20",1,0,1);
     $this->addField("id",CMObj::TYPE_VARCHAR,"100",1,0,0);

     $this->addPrimaryKey("code");
  }
}

?>