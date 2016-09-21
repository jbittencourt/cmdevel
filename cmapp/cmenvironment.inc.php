<?php

/**
 * Main Class that contains information about the environment being executed.
 *
 * O CMEnvironment é uma das classes mais importantes do cmdevel pois comtém
 * as funçõeses necessárias para inicializaçãoo, e gerenciamento do de uma plataforma
 * para EAD. Toda a vez que um usuário acessa um site contruído com o cm->devel, o script
 * config.inc.php se encarrega de criar uma nova inst�ncia do RDAmbiente e coloca-la na
 * variável $_SESSION[environment]. As funçõeses mais comumente utilizadas são as de autenticação, e suporte
 * a várioas linguagens.
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmapp
 * @see CMObj, CMUser, CMCursor
 */
class CMEnvironment {


  /**
   * @var int $logSession Informa ao ambiente se este deve manter o log de acessos dos usu�rios.
   */
  public $logSession=true;

  /**
   * var Boolen $logged True is the user is logged into the system and false otherwise.
   **/
  public $logged=0;


  private $userClass, $userClassBib;


  /**
   * Makes the users login in the system.
   *
   * Realiza o logon do usu�rio no sistema. Em caso de sucesso retorna 1, e
   * seta a v�riavel $_SESSION[usuario], carregando a int�ncia do objeto RDUser. � poss�vel
   * setar uma nova classe para ser inst�nciada no lugar de RDUser. Para tal o nome da classe
   * deve ser setada em $rdambiente->setUserClass. Al�m disse essa classe deve obrigat�riamente ser
   * subclasse de rduser.
   *
   * @param string $login Nome do usu�rio a ser aut�nticado.
   * @param string $senha Senha do usu�rio em um string n�o encriptado.
   */
  public function login($username,$password) {
    global $_CMAPP,$_CMDEVEL;

    $_conf = $_CMAPP['config'];

    /**
     * Creates an instance of the system user. The default user class
     * is CMUser, but can modified by the app->userclass node in the
     * configuration xml.
     **/
    $this->userClass = (string) $_conf->app->userclass->class;

    if(empty($this->userClass) || (strtolower($this->userClass)=="cmuser")) {
      $this->userClass = "CMUser";
    }

    $class = $this->userClass;

    $keys = array();

    $_SESSION['user'] = new $class;
    $_SESSION['user']->username = $username;
    $_SESSION['user']->password = md5($password);

    try {
      $_SESSION['user']->load();
    } catch(CMDBNoRecord $e) {
      unset($_SESSION['user']);
      throw new CMLoginFailure;
    }


    $this->logged = true;

    CMEnvSession::closeDeadSessions();

    //checks if the user has not alredy logged in recently in the same computes
    $_SESSION['session'] = new CMEnvSession;
    $_SESSION['session']->sessID =  session_id();
    $_SESSION['session']->codeUser = $_SESSION['user']->codeUser;

    try {
      $_SESSION['session']->load();
    }
    catch (CMDBNoRecord $e) {

      //discover the time of the user's last login and put it in a session var

      $q = new CMQuery('CMEnvSession');
      $q->setFilter("codeUser=".$_SESSION['user']->codeUser);
      $q->setOrder('timeStart desc');
      $list = $q->execute();

      if(!$list->__isEmpty()) {
		$_SESSION['last_session'] = $list->offsetGet(0);
      }

      $ip = ip2long($_SERVER['REMOTE_ADDR']);
      $ip = (!$ip)?0:$ip; //if ip2long returns false, set ip to 0;

      //fill the $_SESSION[session] with the current session object
      $_SESSION['session']->timeStart = time();
      $_SESSION['session']->timeEnd = $_SESSION['session']->timeStart;
      $_SESSION['session']->IP = $ip;
      $_SESSION['session']->flagEnded = CMEnvSession::ENUM_FLAGENDED_NOT_ENDED;
      $_SESSION['session']->save();
    }

  }


  /**
   * Desloga o usuario do ambiente.
   */
  function logout() {
    global $_CMAPP;

    if ($this->logSession) {
      if(!empty($_SESSION['session'])) {
	$_SESSION['session']->close();
      };
    }

    session_unset();
    session_destroy();
  }

  /**
   * List the users of the system.
   *
   * @return object Returns an CMContainer filled with the users of the system.
   * @see CMQuery, CMUser
   * @todo criar um configura��o em que s� os usu�rios ativos apare�am
   */
  public function listUsers() {
    $q = new CMQuery('CMUser');
    return $q->execute();
  }


  public function listOnlineUsers() {
    $t = CMEnvSession::getTimeout(time());

    $q = new CMQuery('CMUser');
    $q->naturalJoin('CMEnvSession',"v");
    $q->setFilter("(timeEnd>'$t') and (flagEnded=0)");
    return $q->execute();
  }

}

?>
