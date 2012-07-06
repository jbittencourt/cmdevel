<?

/**
 * Classe que implementa um RadioButton
 *
 * Classe que implementa um RadioButton
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */
class CMWRadio extends CMWInputEl {

  function __construct($name,$value,$label="") {
    parent::__construct($name,$value,"radio");
    $this->setLabel($label);

    $this->labelAfter = 1;
  }

  function check() {
    $this->prop['checked']="";
  }



}



?>
