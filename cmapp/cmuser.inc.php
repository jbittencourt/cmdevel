<?
/**
 * Class that implements a standart user.
 * 
 * This class is one of the main classes of CMDevel. It objective
 * is to implement a standart User to an application. It contains
 * functions to handle group management and logon control. The
 * default way of using this class in extendind it and implementing
 * the especific methods for your application.
 *
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmapp
 * @see CMEnvironment
 */
class CMUser extends CMObj {

  protected $old_password;
  protected $force_create_email;
  protected $force_create_homedir;

  
  function __construct($key="") {
    parent::__construct($key);

    $this->old_password = null;
  }

  /**
   * This function is an overwrite of the CMObj::load() function 
   * used to save the load
   */
  public function load() {
    // This variable is used to know when the user
    // changed this password, so the object can crypt it.
    parent::load();
    $this->old_password = $this->password;
  }


  public function configure() {
    $this->setTable("User");

    $this->addField("codeUser",CMObj::TYPE_INTEGER,11,1,0,1);
    $this->addField("username",CMObj::TYPE_VARCHAR,20,1,0,0);
    $this->addField("time",CMObj::TYPE_INTEGER,20,1,0,0);
    $this->addField("name",CMObj::TYPE_VARCHAR,100,1,0,0);
    $this->addField("password",CMObj::TYPE_VARCHAR,100,1,0,0);
    $this->addField("active",CMObj::TYPE_CHAR,1,1,0,0);

    $this->addPrimaryKey("codeUser");
  }



  /**
   * This function is an overwrite of the CMObj::save() function 
   * used to crypt the users password and to create the users 
   * directory, if necessary.
   */ 
  function save() {
    global $_CMAPP;
    
    $_conf = $_CMAPP['config'];
    
    //if the password was changed or the user is new, encrypt the password.
    if((($this->password!=$this->old_password) && (($this->state==self::STATE_DIRTY) 
						   || ($this->state==self::STATE_DIRTY_NEW)) )
        || ($this->state==self::STATE_NEW)) {
      $this->password = md5($this->password);
    }

    //pass the exception to the programmer
    parent::save();

  }


  public function listGroupJoinResponses() {
    $q = new CMQuery(CMGroup);
    
    $j = new CMJoin(CMJoin::NATURAL);
    $j->setClass(CMGroupMemberJoin);
    $j->using('CMGroupMemberJoin::codeGroup');

    $q->addJoin($j,'invitation');
    $filter = 'CMGroupMemberJoin::status='.CMGroupMemberJoin::ENUM_STATUS_ANSWERED;
    $filter.= ' AND CMGroupMemberJoin::ackResponse='.CMGroupMemberJoin::ENUM_ACKRESPONSE_NOT_ACK;
    $filter.= ' AND CMGroupMemberJoin::type='.CMGroupMemberJoin::ENUM_TYPE_REQUEST;
    $filter.=' AND AMGroupMemberJoin::codeUser='.$this->codeUser;
    $q->setFilter($filter);

    return $q->execute();
  }

  public function listGroupJoinInvitations() {
    $q = new CMQuery(CMGroup);
    
    $j = new CMJoin(CMJoin::NATURAL);
    $j->setClass(CMGroupMemberJoin);
    $j->using('CMGroupMemberJoin::codeGroup');

    $q->addJoin($j,'invitation');
    $filter = 'CMGroupMemberJoin::status='.CMGroupMemberJoin::ENUM_STATUS_NOT_ANSWERED;
    $filter.= ' AND CMGroupMemberJoin::type='.CMGroupMemberJoin::ENUM_TYPE_REQUEST;
    $filter.=' AND AMGroupMemberJoin::codeUser='.$this->codeUser;
    $q->setFilter($filter);

    return $q->execute();
  }

  
}

?>