<?


/**
 * Classe que implementa um Elemento do tipo input
 *
 * Classe que implementa um Elemento do tipo input
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  WFormEl
 */
class CMWInputEl extends CMWFormEl {
  /** 
   * @var int $labelAfter Determina que o label deve ser impresso após o input e não antes dele
   */
  public $design;

    
  function __construct($name,$value,$type="") {
    parent::__construct($name,$value);

    $this->setType($type);

  }


  public function __toString() {

    //desenha o label e o elemento em colunas separadas
    if ($this->design == CMWFormEl::WFORMEL_DESIGN_LEFT_TWO_COLS) {
      $str.= $this->label."</TD><TD>";
    }
    else {
      if(($this->design!=CMWFormEl::WFORMEL_DESIGN_SIDE_RIGTH) &&
	 ($this->design!=CMWFormEl::WFORMEL_DESIGN_STRING_DEFINED))
	$str = $this->label."&nbsp;";
    }

    if($this->design==CMWFormEl::WFORMEL_DESIGN_OVER) {
      $str.="<BR>";
    }
    
    $str.= "<INPUT ";
    
    foreach ($this->prop as $prop=>$valor) {
      if(isset($valor)) {
        $str.= $prop."=\"".$valor."\" ";
      }
      else {
        $str.= " $prop ";
      }
    }
    $str.= ">";
    if($this->design==CMWFormEl::WFORMEL_DESIGN_SIDE_RIGTH) $str.= $this->label."&nbsp;";

    $this->add($str);
    return parent::__toString();   
    
  }
  
} 

?>