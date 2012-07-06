<?

/**
 * Classe que implementa um form tipo Textarea
 *
 * Classe que implementa um form tipo Textarea
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */
class CMWTextArea extends CMWFormEl {
  private $content;

  function __construct($name,$numRows,$numCols,$value="") {
    parent::__construct($name,$value,"textarea");  

    $this->setName($name);
    $this->setRows($numRows);
    $this->setCols($numCols);
    $this->add($value);
  }

  public function setRows($numRows) {
    $this->prop['rows'] = $numRows;
  }
  
  public function setCols($numCols) {
    $this->prop['cols'] = $numCols;
  }


  public function setSize($w,$h) {
    $this->prop['cols'] = $w;
    $this->prop['rows'] = $h;
  }

  public function getRows() {
    return $this->prop['rows'];
  }
  
  public function getCols() {
    return $this->prop['cols'];
  }


  public function setValue($value) {
    $this->content = $value;
  }

  public function getValue() {
    return $this->content;
  }

  public function addContent($conteudo) {
    $this->content.= $conteudo;
  }

  public function __toString() {
    
    if ($this->design == CMWFormEl::WFORMEL_DESIGN_LEFT_TWO_COLS) {
      $this->add($this->label);
      $this->add("</TD><TD>");
    }
    else {
      if($this->design != CMWFormEl::WFORMEL_DESIGN_STRING_DEFINED) $str = $this->label;
      if($this->design == CMWFormEl::WFORMEL_DESIGN_OVER) $str.= "<br>";
    }

    $str.= "<TEXTAREA ";
    foreach ($this->prop as $prop=>$valor) {
      $str.= $prop."=\"".$valor."\" ";
    }
    $str.= ">";
    $str.= $this->content;
    $str.= "</TEXTAREA>";
    $this->add($str);
    return parent::__toString();
  }

}


?>
