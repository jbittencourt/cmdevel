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

class CMACO extends CMObj {

  protected $ACOimplementer;
  protected $validPrivileges;
  protected $groups;
  protected $users;

  protected $userPrivCache = array();

  public function __construct($acoImpl) {

    if(!($acoImpl instanceof CMACLAppInterface)) {
      Throw new  CMACLException("The ACO parente must implement CMACLAppInterface");
    }

    $this->ACOimplementer = $acoImpl;
    $this->validPrivileges = $acoImpl->listPrivileges();

    parent::__construct();
    $this->users =   new CMContainer;
    $this->groups =  new CMContainer;
  }


  public function configure() {
     $this->setTable("ACO");

     $this->addField("code",CMObj::TYPE_INTEGER,"20",1,0,1);
     $this->addField("description",CMObj::TYPE_VARCHAR,"100",1,0,0);
     $this->addField("time",CMObj::TYPE_INTEGER,"20",1,0,0);

     $this->addPrimaryKey("code");
  }


  /**
   * Test if a privilege is valid.
   **/
  protected function validatePrivilege($priv) {
    if(!in_array($priv,$this->validPrivileges)) {
      Throw new CMACLException("[$priv] isn't a valid privilege. Check the listPrivileges() function in the caller class.");
    }
  }
  
  /*****
   * @todo test if the ACO is persistent
   *****/
  public function addGroupPrivilege($codeGroup,$privilege) {
    $this->validatePrivilege($privilege);

    $priv = new CMACLGroup;
    $priv->codeGroup = $codeGroup;
    $priv->codeACO = $this->code;
    $priv->privilege = $privilege;
    
    try{
      $priv->save();
    } catch(CMObjEDuplicatedEntry $e) {
      Throw new CMACLEPrivilegeAlreadySet($privilege);
    }
  }


  public function addUserPrivilege($codeUser,$privilege) {
    $this->validatePrivilege($privilege);

    $priv = new CMACLUser;
    $priv->codeUser = $codeUser;
    $priv->codeACO = $this->code;
    $priv->privilege = $privilege;

    try{
      $priv->save();
    } catch(CMObjEDuplicatedEntry $e) {
      Throw new  CMACLEPrivilegeAlreadySet($privilege);
    }
  }


  public function addWorldPrivilege($privilege) {
    $this->validatePrivilege($privilege);

    $priv = new CMACLWorld;
    $priv->codeACO = $this->code;
    $priv->privilege = $privilege;

    try{
      $priv->save();
    } catch(CMObjEDuplicatedEntry $e) {
      Throw new CMACLEPrivilegeAlreadySet($privilege);
    }

  }



  /**
   * List the privileges of all users of this ACO.
   **/
  public function listUsersPrivileges() {
    //Test if this object is persistent. If not
    //return am empty CMContainer
    if($this->state ==  self::STATE_NEW) {
      return new CMContainer;
    }

    $q = new CMQuery(CMUser);

    $j1 = new CMJoin(CMJoin::INNER);
    $j1->setClass(CMACLUser);
    $j1->on("CMACLUser::codeUser=CMUser::codeUser");

    $q->setFilter("codeACO=$this->code");
    $q->addJoin($j1,'privileges');

    return $q->execute();
  }

  /**
   * List the privileges of all groups of this ACO.
   **/
  public function listGroupsPrivileges() {
    //Test if this object is persistent. If not
    //return am empty CMContainer
    if($this->state ==  self::STATE_NEW) {
      return new CMContainer;
    }   

    $q = new CMQuery(CMGroup);

    $j1 = new CMJoin(CMJoin::INNER);
    $j1->setClass(CMACLGroup);
    $j1->on("CMACLGroup::codeGroup=CMGroup::codeGroup");

    $q->setFilter("codeACO=$this->code");
    $q->addJoin($j1,'privileges');

    return $q->execute();
  }


  /**
   * Get the privileges defined for the World.
   *
   * The world is evebory else that isn't a user or a group. For instance
   * a visitor that is not logged in the system.
   **/
  public function getWorldPrivileges() {
    //Test if this object is persistent. If not
    //return am empty CMContainer
    if($this->state ==  self::STATE_NEW) {
      return new CMContainer;
    }

    $q = new CMQuery('CMACLWorld');
    $q->setFilter("codeACO=$this->code");

    return $q->execute();
  }


  public function getUserPrivileges($codeUser) {
    //Test if this object is persistent. If not
    //return am empty CMContainer
    if($this->state ==  self::STATE_NEW) {
      return new CMContainer;
    }

    $q = new CMQuery('CMACLUser');
    $q->setProjection('privilege');
    $q->setFilter("(CMACLUser::codeUser=$codeUser AND CMACLUser::codeACO=$this->code)");
    
    $q2 = new CMQuery('CMACLGroup','CMGroup','CMGroupMember');
    $q2->setProjection('privilege');
    $q2->setFilter("(CMACLGroup::codeACO=$this->code AND CMACLGroup::codeGroup=CMGroup::codeGroup AND CMGroupMember::codeGroup=CMGroup::codeGroup AND CMGroupMember::codeUser=$codeUser)");


    $q3 = new CMQuery('CMACLWorld');
    $q3->setProjection('privilege');
    $q3->setFilter("codeACO=$this->code");


    $q2->union($q3);
    $q->union($q2);

    $r = $q->execute();

    $priv = array();
    foreach($r as $item) {
      $priv[] = $item->privilege;
    }
    return $priv;
  }


  public function testUserPrivilege($codeUser,$priv) {
    if(empty($this->userPrivCache[$codeUser])) {
      $this->userPrivCache[$codeUser] = $this->getUserPrivileges($codeUser);
    }

    return in_array($priv,$this->userPrivCache[$codeUser]);

  }


  /**
   * List all privileges of this ACO.
   *
   * This function list all privileges that have been assigned to this
   * ACO. It return an associative array with 3 keys: world, users, groups. This function
   * is not optmized, so don't use it extensivaly.
   *
   * @todo Optimize the function to do only one query to the database.
   * @return array An associative array with 3 keys: world, users, groups.
   **/
  public function listPrivileges() {
    
    $ret = Array();
    $ret['world'] = $this->getWorldPrivileges();
    $ret['users'] = $this->listUsersPrivileges();
    $ret['groups'] = $this->listGroupsPrivileges();

    return $ret;
  }

}

?>