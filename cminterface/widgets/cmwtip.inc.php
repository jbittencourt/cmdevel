<?

/**
 * Widget que implementa um botão de dicas.
 *
 * Permite que o projetista da interface adicione botões de dicas a um smartform. Usualmente o smartform tenta
 * detectar os labels dos campos no arquivo de linguagem. Caso exista no arquivo .lang um nome de campo com o sufixo
 * _desc (<i>description</i>), o smartform cria automaticamente um tip com esse string.
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cminterface
 * @see  CMWForm
 */
class CMWTip extends CMHTMLObj {
  
  const BEHAVIOR_MOUSEOVER=0;
  const BEHAVIOR_MOUSEOVER_AND_STAY=1;
  const BEHAVIOR_CLICK_AND_STAY=2;

  protected static $count=0;
  private static $_inicialized=false;

  protected $link="#";
  protected $text;
  protected $des;
  protected $class;
  protected $divClass;
  protected $num;
  protected $wrapperfunction=false;
  protected $behavior= 0;

  public $zindex = 100; //set a veryhight z-index to the div don't be overlaied

  public function __construct($linkText,$des="") {
    global $_CMDEVEL;
    parent::__construct();

    $this->text = $linkText;
    $this->des = $des;

    if(!isset($_SESSION['CMDEVEL'])) $_SESSION['CMDEVEL'] = array("tip"=>0);

    $_SESSION['CMDEVEL']['tip']++;
    $this->num = $_SESSION['CMDEVEL']['tip'];

    $this->requires("cminterface/widgets/javascript/tip.js.php",self::MEDIA_JS_WRAPPER);
    $this->requires("cminterface/widgets/css/tip.css",self::MEDIA_CSS_WRAPPER);

  }

  function setBehavior($b) {
    $this->behavior = $b;
  }


  public function setLink($link) {
    $this->link = $link;
  }

  public function setClass($value) {
    $this->class = $value;
  }

  public function setDivClass($value) {
    $this->divClass = $value;
  }

  /**
   * Tell to the object to interpretated the $des as a Javascript function that
   * will generate the text. This will allow the user to generate the text of
   * the div by html on the fly. This can help to build sophisticated behaviors
   * with low bandwith.
   **/
  public function setJSWrapperFunction($value) {
    $this->des=$value;
    $this->wrapperfunction=true;
  }
  
  public function getNum() {
    return $this->num;
  }

  public function __toString() {
    global $_CMAPP,$_CMDEVEL;

    $stay = False;
    $ini_js = "";

    $do_tooltip = "window.doTooltip(event,'',$this->des,'#FFFFFF','',$this->wrapperfunction)";

    switch($this->behavior) {
    case  self::BEHAVIOR_MOUSEOVER:
      $event_on = "onMouseOver=\"$do_tooltip\"";
      $event_off = "onMouseOut==\"hideTip(100)\"";
      $str_link = "href=\"$this->link\"";
      $timeout = 100;
      break;
    case self::BEHAVIOR_MOUSEOVER_AND_STAY:
      $event_on = "onMouseOver=\"$do_tooltip\"";
      $event_off = "onMouseOut==\"hideTip(1000)\"";
      $str_link = "href=\"$this->link\"";
      $stay = True;
      break;
    case self::BEHAVIOR_CLICK_AND_STAY:
      //      $this->link = "javascript: return false;";
      $event_on = "onClick=\" $do_tooltip\"";
      $event_off = "";
      $str_link = "";
      $this->stay = true;
      $ini_js = "tipStayUntilClick=true;";
      break;
    }

    if(!self::$_inicialized) {
      self::$_inicialized = true;

      $st = "<div id=\"tipDiv\" style=\"position:absolute; visibility:hidden; z-index:$this->zindex;\" class=\"$this->divClass\"";

      if($stay) {
	$st.= "onMouseOver=\"cancelHideTip()\" onMouseOut=\"hideTip(100)\"";
      }

      $st.=  "></div>";

      self::addPageEnd($st);

      //sets an javascript variable that tell the initTip function
      //to not aply any style information
      if(!empty($this->divClass)) {
	$ini_js.= "var tipCssClass = 1;";
      }
      else {
	$ini_js.= "var tipCssClass = 0;";
      }
      $ini_js.= "initTip();"; //initialize the tip

      self::addPageEnd(CMHTMLObj::getScript("$ini_js")); //the tip inicialization must be done at the end of the page.
    };  
    
    
    if(!empty($this->class)) {
      $class = "class=\"$this->class\"";
    }
    
    parent::add("<a id=\"cmwtip\" $str_link $class $event_on $event_off>$this->text</a>");
    return parent::__toString();
  }

}


?>