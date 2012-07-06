<?

/**
 *  Widget que imprime itens na disposicao de uma arvore
 *  @package cmdevel
 *  @subpackage cminterface
 */
class CMWTreeNode extends CMHTMLObj {
  /**
   * @var array $itens Items que foram adicionados a árvore
   */
  static private $nodecount=0;

  private $itens;
  private $images;
  public $name;
  private $display;
  private $open;
  private $caption;
  private $ident;
  protected $classes;
  protected $no_bullets = false;
  protected $additionalJSCall;

  function __construct($caption) {
    global $_CMDEVEL;
    parent::__construct();
    
    $this->requires("cminterface/widgets/javascript/divs.js",self::MEDIA_JS_WRAPPER);

    $this->caption = $caption;
    $this->name = "treenode_".(++self::$nodecount);

    $this->display = "none";
    $this->ident = "10px";
  }


  public function open() {
    $this->open = true;
  }

  /**
   * This functions set an extra JS function call that will be called after the open or close of the tree.
   **/
  function setJSCall($str) {
    $this->additionalJSCall = $str;
  }

  public function add($item) {
    $this->itens[] = $item;
  }


  public function setBullets($im1,$im2) {
    $this->images['close'] = $im1;
    $this->images['open'] = $im2;
  }

  public function setNoBullets() {
    $this->no_bullets =true;
  }


  /**
   * @decrepted
   **/
  public function setClasses($link,$div) {
    $this->classes['link'] = $link;
    $this->classes['div'] = $div;
  }


  public function setClassLink($value) {
    $this->classes['link'] = $value;
  }

  public function setClassDiv($value) {
    $this->classes['div'] = $value;
  }


  public function setClassData($value) {
    $this->classes['data'] = $value;
  }


  public function setIdentDistance($w) {
    $this->ident =  $w;
  }

  public function __toString() {
    
    if(empty($this->open)) $this->open = "0";


    parent::addScript($this->name."_open = $this->open;");

    if(!$this->no_bullets) {
      if(!$this->open) {
	parent::add("<img name=\"".$this->name."_img\" src=\"".$this->images['close']."\">");
      } else {
	parent::add("<img name=\"".$this->name."_img\" src=\"".$this->images['open']."\">");
      };
    }

    $onclick = "$('$this->name').toggle();";
    if(!empty($this->additionalJSCall)) {
      $onclick .= $this->additionalJSCall.";";
    }

    $v_open = $this->name."_open";
    $v_img = $this->name."_img";

    if(!$this->no_bullets) {
      $onclick.= "if(!$v_open) {";
      $onclick.= "  document.$v_img.src = '".$this->images['close']."';";
      $onclick.= "} else {";
      $onclick.= "  document.$v_img.src = '".$this->images['open']."';";
      $onclick.= "}";
    }
    
    if($this->caption instanceof CMHTMLObj) {
      $this->caption->setOnClick($onclick);
      parent::add($this->caption);
    } 
    else {
      parent::add("<a style='cursor: pointer' onClick=\"$onclick\" class=\"".$this->classes['link']."\">");
      //caption can be a text or a rdpagobj. But be carfull. Just simple rdpagobj
      //will work
      parent::add($this->caption);
      parent::add("</a>");
    }


    if($this->open) {
      $this->display = "visible";
    }

    parent::add("<DIV name=\"".$this->name."\" id=\"".$this->name."\" class=\"".$this->classes['div']."\" style=\"display: $this->display\">");
   

    parent::add("<table border=0 cellpading=0 cellspacing=0><tr>");
    parent::add("<td style=\" width: $this->ident\" >&nbsp;</td>");
    parent::add("<td class=\"".$this->classes['data']."\">");

    if(!empty($this->itens)) {
      foreach($this->itens as $item) {
	parent::add($item);
      }
    }

    parent::add("</td></tr></table>");
    parent::add("</DIV>");
    return parent::__toString();
     
  }
  

}


?>
