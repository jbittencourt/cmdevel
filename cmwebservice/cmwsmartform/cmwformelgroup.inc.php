<?
/**
 * Classe que implementa um grupo de elementos smartform
 *
 * Esse elemente tem como objetivo alinhar lado a lado 2 ou mais elementos de um smartform.
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWFormEl
 */
class CMWFormElGroup extends CMWFormEl {
  private $components;
  private $order = array();
  private $disabled = false;
  private $align = "center";

  public function __construct() {
    parent::__construct();
    $this->align = "center";
    $this->class = "";
  }

  public function disable() {
    $this->disabled = true;
  }
  
  public function add($item) {
    
    $this->components[$item[0]] = $item[1];

  }

  public function setOrder($order) {
    if(!is_array($order)) {
      Throw new CMWSmartFormException("The order must be an array of strings.");
    }
    $this->order = $order;
  }

  public function setAlign($align) {
    $this->align=$align;
  }

  public function __toString() {
    if($this->disabled) 
      return "";
    
    $n = count($this->components);

    parent::add("<table align=\"$this->align\"><tr>");
    if(!empty($this->components)) {
      if(count($this->order)==0) {
	$this->order = array_keys($this->components);
      }
    }

    if(!empty($this->order)) {
      foreach($this->order as $key) {
	parent::add("<td class=\"$this->class\">");
	if(isset($this->components[$key]))
	  parent::add($this->components[$key]);
	parent::add("</td>");
      };
    }
    parent::add("</tr></table>");
    return parent::__toString();
  }
}

?>