<?
/**
 * Classe que implementa uma lista ou combobox
 *
 * Classe que implementa uma lista ou combobox
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */

class CMWSelect extends CMWFormEl {
  private $options=array();
  
  function __construct($name,$value=0) {
    parent::__construct($name,$value);
  }

  public function addOption($value,$text) {
    $this->options[$value] = $text;
  }

  public function setMultiple() {
    $this->prop['multiple'] = "";
  }

  public function setSize($size) {
    $this->setMultiple();
    $this->prop['size'] = $size;  
  }


  public function addOptions($list,$fieldValue,$fieldLabel) {

    foreach($list as $item) {
      $this->options[$item->$fieldValue] = $item->$fieldLabel;
    }

  }

  public function __toString() {   


    if ($this->design == CMWFormEl::WFORMEL_DESIGN_LEFT_TWO_COLS) {
      $str = $this->label."</TD><TD>";
    }
    else {
      if($this->design != CMWFormEl::WFORMEL_DESIGN_STRING_DEFINED) $str = $this->label;
      if($this->design==CMWFormEl::WFORMEL_DESIGN_OVER) $str .= "<br>";
    }


    $str.= "<SELECT ";
    foreach ($this->prop as $prop=>$valor) {
      if(empty($valor)) {
	$str.= $prop." ";
      }
      else {
	$str.= $prop." =\"".$valor."\" ";
      };
    }
    $str.= ">";
    $this->add($str);
    
    if(count($this->options)>0) {
      foreach ($this->options as $value=>$text) {
	if(!isset($chk)) $chk="";
	$line = "<OPTION value=\"".$value."\" ";
	if($this->getValue() == $value )  $line.= " SELECTED ";
	$line.="$chk>".$text."</OPTION>\n";
	$this->add($line);
      }
    }
    $this->add("</SELECT>");
    return parent::__toString();

  }

}


?>
