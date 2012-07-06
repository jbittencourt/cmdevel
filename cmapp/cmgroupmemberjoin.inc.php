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

class CMGroupMemberJoin extends CMObj {

  const ENUM_TYPE_INVITATION = "INVITATION";
  const ENUM_TYPE_REQUEST = "REQUEST";
  const ENUM_STATUS_NOT_ANSWERED = "NOT_ANSWERED";
  const ENUM_STATUS_REJECTED = "REJECTED";
  const ENUM_STATUS_ACCEPTED = "ACCEPTED";
  const ENUM_ACKRESPONSE_ACK = 'ACK';
  const ENUM_ACKRESPONSE_NOT_ACK = 'NOT_ACK';

  public function configure() {
     $this->setTable("GroupMemberJoin");

     $this->addField("codeGroupMemberJoin",CMObj::TYPE_INTEGER,"20",1,0,1);
     $this->addField("codeUser",CMObj::TYPE_INTEGER,"20",1,0,0);
     $this->addField("codeGroup",CMObj::TYPE_INTEGER,"20",1,0,0);
     $this->addField("type",CMObj::TYPE_ENUM,"",1,self::ENUM_TYPE_INVITATION,0);
     $this->addField("status",CMObj::TYPE_ENUM,"",1,self::ENUM_STATUS_NOT_ANSWERED,0);
     $this->addField("textRequest",CMObj::TYPE_TEXT,"256",1,0,0);
     $this->addField("textResponse",CMObj::TYPE_TEXT,"256",1,0,0);
     $this->addField("ackResponse",CMObj::TYPE_ENUM,"",1,self::ENUM_ACKRESPONSE_NOT_ACK,0);
     $this->addField("timeResponse",CMObj::TYPE_INTEGER,"20",1,0,0);
     $this->addField("codeUserResponse",CMObj::TYPE_INTEGER,"20",1,0,0);
     $this->addField("time",CMObj::TYPE_INTEGER,"20",1,0,0);

     $this->addPrimaryKey("codeGroupMemberJoin");

     $this->setEnumValidValues("type",array(self::ENUM_TYPE_INVITATION,
                                            self::ENUM_TYPE_REQUEST));
     $this->setEnumValidValues("status",array(self::ENUM_TYPE_INVITATION,
                                              self::ENUM_TYPE_REQUEST,
                                              self::ENUM_STATUS_NOT_ANSWERED,
                                              self::ENUM_STATUS_REJECTED,
                                              self::ENUM_STATUS_ACCEPTED));
     $this->setEnumValidValues("ackResponse",array(self::ENUM_ACKRESPONSE_NOT_ACK,
						   self::ENUM_ACKRESPONSE_ACK));
  }
  //put your functions here
}


?>
