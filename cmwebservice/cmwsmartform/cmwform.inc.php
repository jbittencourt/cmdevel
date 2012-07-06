<?

/**
 * Classe que implemente um formulário
 *
 * @author Maicon Brauwers <maicon@edu.ufrgs.br>
 * @access public
 * @abstract
 * @version 0.5
 * @package cmdevel
 * @subpackage cminterface
 * @see CMPagObj
 */

include("cmwebservice/cmwsmartform/cmwformel.inc.php");
include("cmwebservice/cmwsmartform/cmwinputel.inc.php");
include("cmwebservice/cmwsmartform/cmwformelgroup.inc.php");
include("cmwebservice/cmwsmartform/cmwtext.inc.php");
include("cmwebservice/cmwsmartform/cmwtextarea.inc.php");
include("cmwebservice/cmwsmartform/cmwselect.inc.php");
include("cmwebservice/cmwsmartform/cmwhidden.inc.php");
include("cmwebservice/cmwsmartform/cmwbutton.inc.php");
include("cmwebservice/cmwsmartform/cmwfile.inc.php");
include("cmwebservice/cmwsmartform/cmwradio.inc.php");
include("cmwebservice/cmwsmartform/cmwradiogroup.inc.php");
include("cmwebservice/cmwsmartform/cmwcheckbox.inc.php");
include("cmwebservice/cmwsmartform/cmwlistadd.inc.php");
include("cmwebservice/cmwsmartform/cmwdate.inc.php");
include("cmwebservice/cmwsmartform/cmwcalendar.inc.php");
include("cmwebservice/cmwsmartform/cmwhtmlarea.inc.php");

class CMWForm extends CMHTMLObj {

  const WTEXT_SIZE=60;
  const WTEXTAREA_ROWS=10;
  const WTEXTAREA_COLS=40;
  const WFLOATSIZE=15;

  public $name,$action,$method,$enctype;
  protected $__settings = array("name"=>"",
				"id"=>"",
				"action"=>"",
				"method"=>"Post",
				"enctype"=>"",
				"urlOnCancel"=>"",
				"objClass"=>"",
				"cancelButtonOff"=>FALSE,
				"submitButtonOff"=>FALSE,
				"urlOnCancel"=>"",
				"spacing"=>"",
				"labelClass"=>"",
				"designString"=>"",
				"designStringIterative"=>"",
				"onSubmitActions"=>"",
				"onSubmit"=>"",
				"design"=>""
			     );




  function __construct($name,$action,$method="",$enctype="") {
    parent::__construct();

    $this->__settings['name'] = $name;
    $this->__settings['id'] = $name;
    $this->__settings['action'] = $action;
    
    if (empty($method)) {
      $this->__settings['method'] = "POST";
    }else $this->__settings['method'] = $method;
    
    $this->__settings['enctype'] = $enctype;
  }


  /** Constroi campos hidden para cada par/valor em $_REQUEST
   *
   */
  function buildHiddenFromRequest() {
    if (count($_REQUEST) > 0) {
      foreach($_REQUEST as $nomeCampo=>$valor) {
	$wHidden = new WHidden($nomeCampo,$valor);
      }
    }
  }




  /** Configura o tamanho do espaçamento entre as células da tabela do smartform
   *
   * @param integer $spc Parâmetro a ser passados para a tabela.
   */
  function setSpacing($spc) {
    $this->__settings['spacing'] = $spc;
  }


  /**
   * Altera a classe css padrão para ser utilizada no label
   *
   * @param string $class Nome da classe CSS.
   */
  function setLabelClass($class) {
    $this->__settings['labelClass'] = $class;
  }


  function setDesignString($str,$no_iterative=0) {
    $this->__settings['designString']=$str;
    $this->__settings['designStringIterative']=!$no_iterative;
  }



  public function getName() {
    return $this->__settings['name'];
  }

  public function setFormName($name) {
    $this->__settings['name'] = $name;
  }


  public function __toString() {

    ob_start();
    $buffer = parent::__toString();
    $data = ob_get_contents();
    ob_end_clean();

    

    
    if(!empty($this->__settings['onSubmit'])) {
      $onSubmit = "onSubmit=\"".$this->__settings['onSubmit']."\"'";
    }else $onSubmit=''; 
    
    $str  = "<FORM name='".$this->__settings['name']."' id='".$this->__settings['id']."' action='";
    $str .= $this->__settings['action']."' method='".$this->__settings['method']."' $onSubmit ";
    if (!empty($this->__settings['enctype'])) {
      $str.= "enctype='".$this->__settings['enctype']."' ";
    }
    $str.= ">";
    $str .= $buffer;
    $str .= $data;
    $str .= "</FORM>";
    
    return $str;
  }


}



?>