<?
/**
 * CMPagObj is the main interfaces file of the Code Monkey Developer. It is the base class for build HTML files
 *
 * CMPagObj � o principal arquivo de interfaces do Code Monkey Developer . Ele � a classe de base para construir arquivos HTML
 *
 * CMPagObj  � o principal arquivo de interfaces do Code Monkey Developer. Ele � a classe de base para construir arquivos HTML
 * pois permite que as subclasses adicionem linhas atrav�s do comando add() e depois gerem o c�digo fonte atrav�s do
 * comando printPage(). Atrav�s de add() podem ser adicionados outros objetos descendentes de CMPagObj, pois o comando
 * printPage() reconhece o objeto e chama recursivamente o printPage() deste objeo.
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cminterface
 */

class CMHTMLObj {
  /**
   * @var array $body Contains the added intens with add command
   */
  public $id;
  public $name, $body;
  public $force_newline;
  protected static $_pageRequires = array();

  const MEDIA_JS_WRAPPER=0;
  const MEDIA_CSS_WRAPPER=1;
  const MEDIA_JS=2;
  const MEDIA_CSS=3;
  const MEDIA_CUSTOM_JS=4;
  const MEDIA_CUSTOM_CSS=5;

  private $inicialized=false;

  /**
   * Inicialize the object propreties with the default value.
   */
  function __construct($id="") {
    $this->id = $id;

    $this->inicialized=true;
    if(in_array("CMActionListener",class_implements($this))) {
      $this->doAction();
    }
  }


  /**
   *  Adds a String or an CMHTMLObj to the actual object.
   *
   * @param mixed $line An string or an CMHTMLObj.
   * @access public
   */
  public function add($line) {
    if(is_string($line)) $line .= "\n";
    $this->body[]=$line;
  }


  /**
   * Adds an line in the end of the html.
   *
   * This function adds a line in the end of the HTML page
   * just before the </body> tag.
   *
   * @var mixed $value  An string or an CMHTMLObj.
   **/
  public static function addPageEnd($value) {
    global $_CMDEVEL;
    $_CMDEVEL['page']['pag_end'][] = $value;
  }

  /**
   * This function adds an string or an CMObj in the beggining of the page.
   *
   * This function adds an string or an CMObj in the beggining of the page. It
   * can be very usefull when writing some Javascripts that for compatibilty
   * with old browser like IE, should add div in the and of the page, out of
   * any <div> or <table>
   **/
  function addPageBegin($item) {
    global $_CMDEVEL;
    $_CMDEVEL['page']['pag_begin'][] = $item;
  }


  /**
   * This function return an string formated as an script.
   **/
  public static function getScript($js) {
    $line = "<script type=\"text/javascript\">";
    $line.=$js;
    $line.="</script>\n";

    return $line;
  }


  /**
   * Adds an Javascript to the object.
   *
   * @param mixed $js\ A String with the Javascript ou an CMHTMLObj that will generate this code.
   * @access public
   */
  public function addScript($js) {
    $this->body[] = self::getScript($js);
  }



  /**
   * Tell to the HTMLPage that que current object requires an JS ou CSS file.
   **/
  public function requires($file,$type=self::MEDIA_JS) {
    global $_CMDEVEL;

    self::$_pageRequires[$file] = array("file"=>$file,
					"type"=>$type);
  }


  public function getRequires() {
    return self::$_pageRequires;
  }


  public static function getPageRequires() {
    return self::$_pageRequires;
  }


  public function preLoadImage($imgurl) {
    global $_CMDEVEL;

    if(empty($_CMDEVEL['page']['preloadimages'])) {
      $this->requires("cminterface/widgets/javascript/load_swap.js",self::MEDIA_JS_WRAPPER);
    }

    $_CMDEVEL['page']['preloadimages'][$imgurl] = $imgurl;

  }

  public static function  returnArray($contents) {
    $buff = "";
    foreach($contents as $item) {
      if(is_string($item)) {
	       $buff.= $item;
      } elseif (is_array($item)) {
	        $buff.= self::returnArray($item);
      } elseif(($item instanceof CMHTMLObj) or ($item instanceof CMObj) or ($item instanceof CMContainer)) {
	        //if is an interface object or a database object, call the __toString() method.

        	if($item instanceof CMHTMLObj) {
            // echo "Redering class: ".get_class($item)." </br>";
            // $trash = $item->__toString();
        	  $buff.= $item->__toString();
        	}
        	else {
        	  //otherwise throw an exception
        	  if(empty($item)) continue;
        	  Throw new CMIEUnrecognizedObject;
        	}
      }
    }
    return $buff;
  }


  /**
   * Envia para o cliente(browser) o HTML referente ao objeto
   *
   * A fun��o imprime � o principal comando de CMPagObj na medida em que ela � respons�vel por percorrer
   * todos os itens adicionados atrav�s da fun��o add(), identificar quais s�o strings e quais s�o subclasses
   * de CMPagObj, e dar tratamento a eles. No caso dos strings, ela os imprime diretamente para  o browser, se
   * for um objeto instanciado de uma subclasse de CMPagObj, ele chama a fun��o imprime desse pr�prio objeto.
   * Uma procedimento normal na constru��o de subclasses de CMPagObj � re-implementar a classe imprime(), fazendo
   * as impress�es e configura��es necess�rias e depois chamando a fun��o  imprime() de CMPagObj atrav�s do comando
   * parent::imprime(). Um exemplo de uso dessa t�cnica � a classe CMPagina.
   *
   * @see CMHTMLPage
   * @access public
   */
  public function __toString()  {
    if(!$this->inicialized) {
      Throw new CMIECMHTMLObjNotInitilied(get_class($this));
    }

    $buff = array();

    if(!empty($this->body)) {
      reset($this->body);
      $buff[] = self::returnArray($this->body);
    }
    return implode("\n",$buff);
  }

}

?>
