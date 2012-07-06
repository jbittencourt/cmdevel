<?

/**
 * Classe cujo objetivo e automatizar o processo de construcao de formularios
 *
 * Classe cujo objetivo e automatizar o processo de construcao de formularios
 *
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */
class CMWHTMLArea extends CMWTextArea {

  protected $path;
  protected $init = true;

  public function __construct($name,$h,$w,$value="") {
    parent::__construct($name,"20","85",$value);
    
    if($w<500) $w = 500;

    $this->setSize($w,$h);
    $_SESSION['smartform']['cmwhtmlarea']['name'] = $name;
    
    $this->path = "cmwebservice/cmwsmartform/media/javascript/htmlarea";
    
    $this->requires("cmwebservice/cmwsmartform/media/css/htmlarea.css",self::MEDIA_CSS_WRAPPER);
    //$this->requires("$this->path/popup.js",self::MEDIA_JS_WRAPPER);
    $this->requires("$this->path/htmlarea_init.js.php",self::MEDIA_JS_WRAPPER);
    $this->requires("$this->path/htmlarea.js",self::MEDIA_JS_WRAPPER);
    //$this->requires("$this->path/dialog.js",self::MEDIA_JS_WRAPPER);
    //$this->requires("$this->path/popupwin.js",self::MEDIA_JS_WRAPPER);
    //$this->requires("$this->path/full-page.js",self::MEDIA_JS_WRAPPER);
    //$this->requires("$this->path/lang/en.js",self::MEDIA_JS_WRAPPER);
    //$this->requires("$this->path/lang/pt-br.js",self::MEDIA_JS_WRAPPER);
    //$this->requires("$this->path/lang/full-page.pt-br.js",self::MEDIA_JS_WRAPPER);

  }



  /**
   * Set the size of an CMWHTMLArea.
   *
   * param Integer $w Width of the htmlArea in pixels.
   * param Integer $h Height of the htmlArea in pixels.
   **/
  public function setSize($w,$h) {
    $this->prop['style'] ="height: $h; width: $w;";
  }


  public function __toString() {
    //$obj = new CMHTMLObj;
    $obj= CMHTMLObj::getScript("initDocument();");
    //$obj->addScript("initDocument();");
    return parent::__toString().$obj;
  }


}





?>