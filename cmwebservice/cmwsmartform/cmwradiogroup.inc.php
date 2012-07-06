<?

/**
 * Classe que implementa um conjunto de RadioButtons
 *
 * Classe que implementa um conjunto de Radios
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  WForm
 */
class CMWRadioGroup extends CMWFormEl {

  /** 
   * Define o desenho dos radios como um sobre o outro
   */
  const WRADIO_DESIGN_OVER = 0;

  /** 
   *  Define o desenho dos radios como um ao lado do outro
   */
  const WRADIO_DESIGN_SIDE = 1;



  /**
   *  Array contendos os radio buttons criados
   *  @var array
   */
  private $radios = array();
  
  /**
   *  Flag que define como os radios devem serem impressos um sobre o outro ou um ao lado do outro
   *  @var int
   */
  private $radioDesign;

  function __construct($name,$label="") {
    parent::__construct();
    $this->setName($name);
    $this->setLabel($label);
  }


  public function addOption($value,$label) {
    $this->radios[$value] =  new CMWRadio($this->getName(),$value,$label);
  }

  private function parseOptionsFromList($list,$fieldValue,$fieldLabel) {

    foreach ($list->records as $obj) {
      $this->addOption($obj->$fieldValue,$obj->$fieldLabel);
    }

  }

  public function __toString() {
    parent::add("<!-- Inicio do RadioGroup ".$this->getName()."-->");

    if($this->fdesign != CMWFormEl::WFORMEL_DESIGN_STRING_DEFINED) parent::add($this->getLabel());
    if($this->design==CMWFormEl::WFORMEL_DESIGN_OVER) parent::add("<br>");
    
    foreach ($this->radios as $value=>$radio) {
 
      if($this->getValue() == $value ) $radio->check();
      
      parent::add($radio);
      
      if($this->radioDesign==CMWRadioGroup::WRADIO_DESIGN_OVER) parent::add("\n<br>");
      
    }

    parent::add("<!-- Fim do RadioGroup ".$this->getName()."-->");
    
    return parent::__toString();
    
  }


}



?>