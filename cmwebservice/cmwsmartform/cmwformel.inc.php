<?


/**
 * Classe que implementa um Elemento de um formulario
 *
 * Classe que implementa um Elemento de um formulario
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br> 
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */

abstract class CMWFormEl extends CMHTMLObj {

  const WFORMEL_DESIGN_SIDE=0;
  const WFORMEL_DESIGN_STRING_DEFINED=1;
  const WFORMEL_DESIGN_SIDE_RIGTH=2;
  const WFORMEL_DESIGN_LABEL=3;
  const WFORMEL_DESIGN_OVER=3;
  //label a esquerda em uma coluna separada
  const WFORMEL_DESIGN_LEFT_TWO_COLS=4;

  protected $prop = array();

  // The label is the string that is showed in the side of the form element.
  // The labelName is a human readable name of the form element. 
  // For an example if the label is "Enter you age" the would be "Age"
  public $label;
  public $labelName;

  public $design;

  //a pointer to the form object that contains this form element
  protected $parentForm;
  public $formName;

  public function __construct($name="",$value="") {
    parent::__construct();
    $this->setName($name);
    $this->setValue($value);
  }

  public function setType($tipo) {
    $this->prop['type'] = $tipo;
  }
  
  public function setName($name) {
    $this->name = $name;
    $this->prop['name'] = $name;
    $this->prop['id'] = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setValue($value) {
    $this->prop['value'] = $value;
  }

  public function getValue() {
    return $this->prop['value'];
  }

  public function setLabel($label) {
    $this->label = $label;
  }

  public function getLabel() {
    return $this->label;
  }


  public function setLabelName($label) {
    $this->labelName = $label;
  }

  public function getLabelName() {
    return $this->labelName;
  }


  public function setStyleClass($classe) {
    $this->prop["class"] = $classe;
  }

  public function setOnChange($onChange) {
    $this->prop['onChange'] = $onChange;
  }

  public function setProp($propName,$propValue) {
    $this->prop[$propName] = $propValue;
  }
  
  public function getProp($propName){
    return $this->prop[$propName];
  }


  public function setParentForm(CMWSmartForm $value) {
    $this->parentForm = $value;
  }

  public function getParentForm() {
    return $this->parentForm;
  }
  
  public function setFormName($name) {
    $this->formName = $name;
  }
  
  public function getFormName() {
    if(!empty($this->parentForm)) {
      return $this->parentForm->getName();
    } else return $this->formName;
  }
}

?>