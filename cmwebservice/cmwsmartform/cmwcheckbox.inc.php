<?

/**
 * Classe que implementa um checkbox
 *
 * Classe que implementa um checkbox
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */

class CMWCheckBox extends CMWInputEl {

  function __construct($name,$value,$label="") {
    parent::__construct($name,$value,"checkbox");
    $this->setLabel($label);    
  }

  public function check() {
    $this->prop['checked'] = 1;
  }

  public function uncheck() {
    unset($this->prop['checked']);
  }

  public function __toString() {
    $this->design = CMWFormEl::WFORMEL_DESIGN_SIDE_RIGTH;
    return parent::__toString();
  }
}


?>