<?


/**
 *  Widget that implements an image that chages when an onMouseOver occurs.
 *  @package cmdevel
 *  @subpackage cminterface
 */
class CMWSwapImage extends CMHTMLObj {
  protected $link, $onClick, $estado=array();
  protected $target;

  function __construct($link,$estado1,$estado2) {
    global $_CMDEVEL;

    parent::__construct();
    $this->preLoadImage($estado2);
    $this->estado = array($estado1,$estado2);
    $this->link = $link;

    if(!isset($_CMDEVEL['smartform'])) $_CMDEVEL['smartform']=array("wswapimage"=>0);
    
    $this->requires("cminterface/widgets/javascript/load_swap.js",self::MEDIA_JS_WRAPPER);
  }


  public function setOnClick($onClick) {
    $this->onClick = $onClick;
  }

  public function setTarget($target) {
    $this->target = $target;
  }

  public function __toString() {
    global $_CMDEVEL;
        
    $count = (isset($_CMDEVEL['smartform']['wswapimage']) 
	      ? $_CMDEVEL['smartform']['wswapimage']++ 
	      : $_CMDEVEL['smartform']['wswapimage']=0
	      );

    $e1 = $this->estado[0];
    $e2 = $this->estado[1];

    if(!empty($this->onClick)) {
      $onclick = " onClick=\"".$this->onClick."\" ";
    } else $onclick="";
    

    //this must be added as one line only, otherwise may cayse some aligns problens when
    //inside a table cell
    if(!empty($this->target)) $temp = "target='$this->target'";
    $str ="<a href=\"$this->link\" $temp onMouseOut=\"CM_swapImgRestore()\" $onclick";
    $str.=" onMouseOver=\"CM_swapImage('img_$count','','$e2',1)\">";
    $str.= "<img src=\"$e1\" id=\"img_$count\">";
    $str.="</a>";
    parent::add($str);
     

    return parent::__toString();
  }


}


?>
