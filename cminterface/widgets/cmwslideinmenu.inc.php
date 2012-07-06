<?

/**
 *  Widget that implements a Sliding box.
 *
 *  @package cmdevel
 *  @subpackage cminterface 
 */
class CMWSlideInMenu extends CMHTMLObj {

  const STATE_CLOSED=0;
  const STATE_OPEN=1;


  /**
   * @var array $content Content for custom add
   */
  private $content;
  public $width,$top;

  function __construct($width,$top) {
    parent::__construct();
    $this->requires("cminterface/widgets/javascript/slide_in_menu.js",MEDIA_JS_WRAPPER);
    $this->width= $width;
    $this->top = $top;

    $this->reveal = 12;
  }


  public function setMode($mode) {
    $this->mode = $mode;
  }

  public function setRevealSize($tam) {
    $this->reveal = $tam;
  }

  public function add($line) {
    $this->content[] = $line;
  }

  public function __toString() {
    global $urlimagens;

    $w = $this->width;
    $t = $this->top;
    $r = $this->reveal;
    
    
    parent::add("\n <!-- Slide In Menu Start -->");

    if($this->mode==self::STATE_OPEN) {
      $pos = 0;
    }
    else {
      $pos = ($w-$r) * -1;
    }

    parent::add("<div id=\"slidemenubar\" style=\"position:absolute; left: $pos; top:$t; width:$w;\" onMouseover=\"pull()\" onMouseout=\"draw()\">");

    if(!empty($this->content)) {
      foreach($this->content as $linha) {
	parent::add($linha);
      }
    }

    parent::add("</div>");
    parent::addScript("slide_in_init($w,$t,$r);");
    parent::add("\n <!-- Slide In Menu End -->");

    return parent::__toString();


  }

}



?>
