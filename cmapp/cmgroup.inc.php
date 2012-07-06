<?
/**
 * Implements a group of users.
 *
 * CMGroup is a generic implementation of a group of users. It contains
 * the basic functions that are performed in a group, such as register
 * unregister, list, request to join, approval, etc.
 *
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 * @todo You have something do finish in the future
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/
class CMGroup extends CMObj {

  const ENUM_MANAGED_MANAGED = "MANAGED";
  const ENUM_MANAGED_NOT_MANAGED = "NOT_MANAGED";
  
  public $force_add = false; //this variable forces an managed group to add an member directly;

  public function configure() {
     $this->setTable("Groups");

     $this->addField("codeGroup",CMObj::TYPE_INTEGER,"20",1,0,1);
     $this->addField("description",CMObj::TYPE_VARCHAR,"100",1,0,0);
     $this->addField("managed",CMObj::TYPE_ENUM,"",1,self::ENUM_MANAGED_NOT_MANAGED,0);
     $this->addField("time",CMObj::TYPE_INTEGER,"20",1,0,0);

     $this->addPrimaryKey("codeGroup");

     $this->setEnumValidValues("managed",array(self::ENUM_MANAGED_MANAGED,
                                               self::ENUM_MANAGED_NOT_MANAGED));
  }

  /**
   * Test if the especified user is part of the group.
   *
   * Test if the especified user is part of the group. It only
   * returns users that are approved and active.
   *
   * @param integer $codeUser The code of the user.
   **/
  public function isMember($codeUser) {
    $m = new CMGroupMember;
    $m->codeGroup = $this->codeGroup;
    $m->codeUser = $codeUser;
    $m->status = CMGroupMember::ENUM_STATUS_ACTIVE;

    try {
      $m->load();
    } catch(CMDBNoRecord $e) {
      return false;
    }
      
    return true;
  }


  /**
   * Insert an member in the current group.
   *
   * This function is used to directly insert a user in the current group.
   * It's an internal function of this class and should not be used by the
   * user. To add a new user to the current group you should use the addMember
   * function.
   *
   * @see CMGroup::addMemeber()
   * @param integer $codeUser The code of the user being inserted in the group.
   **/
  protected function insertMember($codeUser) {
    $member = new CMGroupMember;
    $member->codeGroup = $this->codeGroup;
    $member->codeUser = $codeUser;
	
	
    //do nothing, just force to read the object
    try {
      $member->load();
    } catch (CMDBException $e) {  };

    $member->time = time();
    $member->status = CMGroupMember::ENUM_STATUS_ACTIVE;

    try {
      $member->save();
    } catch(CMDBException $e) {
      Throw new CMGroupCannotAddUser($e);
    }

    return $member;
  }


  /**
   * Adds an user to the current group.
   * 
   * This method adds a user to the current group. It only
   * performs the action if the group is unmanaged. Otherwise
   * an exception is throw.
   *
   * @see CMGroup::insertMemeber(), CMGroup::insertMemberJoin()
   * @param integer $codeUser The code of the user being inserted in the group.
   **/

  public function addMember($codeUser) {
    if($this->state==CMObj::STATE_NEW) {
      Throw new CMGroupException('Can only add a group member after savind the group');
    }

    if(!$this->force_add) {
      if($this->managed=self::ENUM_MANAGED_MANAGED) {
		Throw new CMGroupException('You can only add users to an Unmanaged group');
      }
    }

    return $this->insertMember($codeUser);
  }

  /**
   * Change the status of the member to retired.
   *
   * In CMGroup there isn't the concept of deleting a member
   * of the group. Once the member is added, it cannot be
   * removed of the group. But the user can be set to an 
   * inactive state called retired. When the member is retired
   * it is not considered an active member, and is not listed in the
   * listMembers() method and the isMember() returns false.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   **/

  public function retireMember($codeUser) {
    if($this->state==CMObj::STATE_NEW) {
      Throw new CMGroupException('Can only retire a group member after savind the group');
    }

    $member = new CMGroupMember;
    $member->codeGroup = $this->codeGroup;
    $member->codeUser = $codeUser;

    try {
      $member->load(); 
    } catch (CMDBNoRecord $e) {
      Throw new CMGroupException('The user '.$codeUser.' is not a part of this group');
    }
    
    $member->status = CMGroupMember::ENUM_STATUS_RETIRED;

    try {
      $member->save();
    } catch (CMDBException $e) {
      Throw new CMGroupException('The user '.$codeUser.' cannot be retired of this group');
    }
  }


  /**
   * Insert a new invitation or request to a member in the current Group.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $type Defines if the join is a request or an invitation.
   * @param string $text An option text that can be added to the invitation.
   **/
  protected function insertMemberJoin($codeUser,$type,$text='') {
    $join = new CMGroupMemberJoin;
    $join->codeUser = $codeUser;
    $join->codeGroup = $this->codeGroup;


    $join->type = $type;
    $join->textRequest = $text;
    $join->time = time();

    try {
      $join->save();
    } catch(CMDBException $e) {
      Throw new CMGroupCannotAddUser($e);
    }

  }

  /**
   * Sends a request to the group to a member joining in.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $text An option text that can be added to the invitation.
   **/
  public function userRequestJoin($codeUser,$text) {
    $this->insertMemberJoin($codeUser,CMGroupMemberJoin::ENUM_TYPE_REQUEST,$text);
  }

  /**
   * Sends a invitation to the user be a member of the group.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $text An option text that can be added to the invitation.
   **/
    public function userInvitationJoin($codeUser,$text) {
    $this->insertMemberJoin($codeUser,CMGroupMemberJoin::ENUM_TYPE_INVITATION,$text);
  }


  /**
   * Handle the a reponse to a member invitation or request.
   *
   * This function is internal to this class. It handles a response
   * to a member invitation or request, changing the status of the
   * join and adding the user to the members table.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $type Defines if the join is a request or an invitation.
   * @param string $text An option text that can be added to the invitation.
   **/
  protected function handleReponseMember($code,$text,$type,$response) {
    $obj = new CMGroupMemberJoin;
    $obj->codeGroupMemberJoin = $code;
    try {
      $obj->load();
    } catch(CMDBNoRecord $e) {
      CMGroupException('The user '.$codeUser.' has no invitation/request at this moment.');
    } catch(CMObjEMoreThanOneRow $e) {
      CMGroupException('The user '.$codeUser.' has more than one invitation/request in this group at this moment.');
    }

    $obj->status = $response;
    $obj->textResponse = $text;
    $obj->timeResponse = time();

    try {
      $obj->save();
    } catch(CMDBException $e) {
      Throw new CMGroupCannotAddUser($e);
    }

    if($response==CMGroupMemberJoin::ENUM_STATUS_ACCEPTED) {
      $this->insertMember($obj->codeUser);
    }

    return $obj;
  }


  /**
   * The member accepts the invitation to be part of the group.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $text An option text that can be added to the invitation.
   **/
  public function acceptInvitation($code,$text) {
    return $this->handleReponseMember($code,$text,CMGroupMemberJoin::ENUM_TYPE_INVITATION,CMGroupMemberJoin::ENUM_STATUS_ACCEPTED);
  }

  /**
   * The group accepts the request of the user to be a member.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $text An option text that can be added to the invitation.
   **/
  public function acceptRequest($code,$text) {
    return $this->handleReponseMember($code,$text,CMGroupMemberJoin::ENUM_TYPE_REQUEST,CMGroupMemberJoin::ENUM_STATUS_ACCEPTED);
  }

  /**
   * The member reject the invitation to be part of the group.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $text An option text that can be added to the invitation.
   **/
  public function rejectInvitation($code,$text) {
    return $this->handleReponseMember($code,$text,CMGroupMemberJoin::ENUM_TYPE_INVITATION,CMGroupMemberJoin::ENUM_STATUS_REJECTED);
  }

  /**
   * The group reject the request of the user to be a member.
   *
   * @param integer $codeUser The code of the user being inserted in the group.
   * @param string $text An option text that can be added to the invitation.
   **/
  public function rejectRequest($code,$text) {
    return $this->handleReponseMember($code,$text,CMGroupMemberJoin::ENUM_TYPE_REQUEST,CMGroupMemberJoin::ENUM_STATUS_REJECTED);
  }

  /**
   * List the members of the group with a specified status.
   *
   * @param string $status The status of the member to be filtered.
   **/
  protected function listMembersWithStatus($status) {
    
    $q = new CMQuery("CMUser");

    $j = new CMJoin(CMJoin::INNER);
    $j->setClass("CMGroupMember");
    $j->on('CMUser::codeUser=CMGroupMember::codeUser');
    $j->setFake();

    $j2 = new CMJoin(CMJoin::LEFT);
    $j2->setClass("CMGroupMemberJoin");
    $j2->on('CMUser::codeUser=CMGroupMemberJoin::codeUser AND CMGroupMemberJoin::codeGroup='.$this->codeGroup);


    $q->addJoin($j,"members");
    $q->addJoin($j2,"request");

    $q->setFilter('CMGroupMember::codeGroup='.$this->codeGroup.' AND CMGroupMember::status="'.$status.'"');

    
    return $q->execute();
  }

  /**
   * List all members of the group.
   *
   * @return CMContainer A list of CMUsers
   **/
  public function listAllMembers() {
    $q = new CMQuery("CMUser");

    $j = new CMJoin(CMJoin::INNER);
    $j->setClass(CMGroupMember);
    $j->on('CMUser::codeUser=CMGroupMember::codeUser');
    $j->setFake();

    $q->addJoin($j,"members");
    $q->setFilter('CMGroupMember::codeGroup='.$this->codeGroup);

    return $q->execute();
  }

  /**
   * List only the active members of the group.
   *
   * @return CMContainer A list of CMUsers
   **/
  public function listActiveMembers() {
    return $this->listMembersWithStatus(CMGroupMember::ENUM_STATUS_ACTIVE);
  }

  /**
   * List only the retired members of the group.
   *
   * @return CMContainer A list of CMUsers
   **/
  public function listRetiredMembers() {
    return $this->listMembersWithStatus(CMGroupMember::ENUM_STATUS_RETIRED);
  }



  /**
   * List the members requests to join the group.
   *
   * @return CMContainer A list of CMUsers.
   **/
  public function listGroupJoinRequests() {
    $q = new CMQuery("CMUser");
    
    $j = new CMJoin(CMJoin::INNER);
    $j->setClass("CMGroupMemberJoin");
    $j->using('codeUser');

    $q->addJoin($j,'request');
    $filter = 'CMGroupMemberJoin::status="'.CMGroupMemberJoin::ENUM_STATUS_NOT_ANSWERED.'"';
    $filter.= ' AND CMGroupMemberJoin::type="'.CMGroupMemberJoin::ENUM_TYPE_REQUEST.'"';
    $filter.= ' AND CMGroupMemberJoin::codeGroup='.$this->codeGroup;
    $q->setFilter($filter);

    return $q->execute();

  }

  
  /**
   * Test if the member has an not answered request to join.
   * 
   * @return boolean
   **/
  public function hasRequestedJoin($codeUser) {
    $group_join = new CMGroupMemberJoin;
    $group_join->codeGroup = $this->codeGroup;
    $group_join->codeUser = $codeUser;
    $group_join->status = CMGroupMemberJoin::ENUM_STATUS_NOT_ANSWERED;

    try {
      $group_join->load();
      return true;
    } catch(CMDBNoRecord $e) {
      return false;
    }
  }


}


?>
