<?

/**
 * Classe que implementa um form tipo Text
 *
 * Classe que implementa um form tipo Text
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */
class CMWText extends CMWInputEl {

  public function __construct($name,$value="",$size="",$maxLength="") {
    parent::__construct($name,$value,"text");  

    $this->prop['size'] = "";
    $this->prop['maxLength'] = "";
    $this->setSize($size);
    $this->setMaxLength($maxLength);
  }

  public function setPassword() {
    $this->prop['type'] = "password";
  }

  public function setSize($size) {
    $this->prop['size'] = $size;
  }

  public function setMaxLength($max) {
    $this->prop['maxLength'] = $max;
  }

}



?>
