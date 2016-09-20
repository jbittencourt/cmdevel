<?

/* Classe que guarda e pesquisa as sess�es abertas pelo usu�rios quando se conecta
 *
 * Classe que guarda e pesquisa as sess�es abertas pelo usu�rios quando se conecta.
 * Ela � importante para que se possa recuperar os usu�rios atualmente conectados dentro
 * de um  ambiente.  Toda a vez que o usu�rio acessar um p�gina, realizar um refresh dentro
 * dentro do ambiente, que por sua vez atualiza o objeto de sess�o corrente marcando o datFim com 
 * o tempo corrente.
 * Quando se cria uma nova se��o ela verifica as sess�es que j� est�o abertas, se existe um
 * cujo datFim mais antigo que um limiar(configur�vel), essa sess�o � morta.
 * e aquelas que 
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmapp
 * @see CMEnvironment
 * @todo You have something do finish in the future
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

class CMEnvSession extends CMObj {
  
  const ENUM_VISIBILITY_VISIBLE = "VISIBLE";
  const ENUM_VISIBILITY_HIDDEN = "HIDDEN";
  const ENUM_VISIBILITY_BUSY = "BUSY";

  const ENUM_FLAGENDED_ENDED = "TRUE";
  const ENUM_FLAGENDED_NOT_ENDED = "FALSE";

  public function configure() {
    $this->setTable("EnvSession");

    $this->addField("sessID",CMObj::TYPE_VARCHAR,"32",1,0,0);
    $this->addField("codeUser",CMObj::TYPE_INTEGER,"20",1,0,0);
    $this->addField("timeStart",CMObj::TYPE_INTEGER,"20",1,0,0);
    $this->addField("timeEnd",CMObj::TYPE_INTEGER,"20",1,0,0);
    $this->addField("IP",CMObj::TYPE_INTEGER,"11",1,0,0);
    $this->addField("flagEnded",CMObj::TYPE_ENUM,"12",1,"FALSE",0);
    $this->addField("visibility",CMObj::TYPE_ENUM,"12",1,"VISIBLE",0);

    $this->addPrimaryKey("sessID");
    $this->addPrimaryKey("codeUser");

    $this->setEnumValidValues("visibility",array(self::ENUM_VISIBILITY_VISIBLE,
						 self::ENUM_VISIBILITY_HIDDEN,
						 self::ENUM_VISIBILITY_BUSY));
    $this->setEnumValidValues("flagEnded", array(self::ENUM_FLAGENDED_ENDED,
						 self::ENUM_FLAGENDED_NOT_ENDED));

  }


  public static function getTimeout($time) {
    global $_conf;
    $timeout = $_conf->app->session->timeout;
    //if timeout is 0 then use an standart timeout
    if(!$timeout) 
      $timeout = 3600;
    return $time - $timeout;
  }


  /**
   * Updates the current session.
   **/
  public function update()  {
    
    //evita que quando por algum acaso remova-se os cookies do browser crie um cadastro fantasma
    # if(empty($this->sessID)) {
    #   //echo "sessID".$this->sessID;
    #   return 0;
    # };
    $this->state = CMOBJ::STATE_DIRTY;
     $this->timeEnd = time();
     $this->flagEnded = self::ENUM_FLAGENDED_NOT_ENDED;
     $this->save();
  }
  
  /**
   * Close the current session.
   */
  public function close() {
    $this->timeEnd = time();
    $this->flagEnded = self::ENUM_FLAGENDED_ENDED;
    $this->state = CMOBJ::STATE_DIRTY;
    $this->save();
  }

  /**
   *  Kill the dead sessions from users that don't make an logout.
   *
   *  Kill the dead sessions from users that don't make an logout.
   *  First the system checks when was the last time when this check,
   *  was made and only make an query to the database if an timeout
   *  ocurr.
   **/
  public static function closeDeadSessions() {

    $q = new CMQuery('CMEnvSession');
    $t = self::getTimeout(time());
    $q->setFilter("timeEnd < $t AND flagEnded='".self::ENUM_FLAGENDED_NOT_ENDED."'");
    $c = $q->execute();
 
    foreach($c as $item) {
      $item->state = CMOBJ::STATE_DIRTY;
      $item->flagEnded = self::ENUM_FLAGENDED_ENDED;
      $item->timeEnd = $t;
    }

    //executes only one query to the database saving the context.
    
    try {
    	$c->acidOperation(CMContainer::OPERATION_SAVE);
    } catch(CMObjEContainerOperationFailed $e) {
		continue;
    }
    
  }

  

}

?>
