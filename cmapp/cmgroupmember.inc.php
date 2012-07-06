<?
/**
 * Short descrition
 *
 * Long description (can contain many lines)
 *
 * @author You Name <your@email.org>
 * @todo You have something to finish in the future
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

class CMGroupMember extends CMObj {

   const ENUM_STATUS_ACTIVE = "ACTIVE";
   const ENUM_STATUS_RETIRED = "RETIRED";

   public function configure() {
     $this->setTable("GroupMember");

     $this->addField("codeGroup",CMObj::TYPE_INTEGER,"20",1,0,0);
     $this->addField("codeUser",CMObj::TYPE_INTEGER,"20",1,0,0);
     $this->addField("status",CMObj::TYPE_ENUM,"",1,self::ENUM_STATUS_ACTIVE,0);
     $this->addField("time",CMObj::TYPE_INTEGER,"20",1,0,0);

     $this->addPrimaryKey("codeGroup");
     $this->addPrimaryKey("codeUser");

     $this->setEnumValidValues("status",array(self::ENUM_STATUS_ACTIVE,
                                              self::ENUM_STATUS_RETIRED));
  }
  //put your functions here
}


?>
