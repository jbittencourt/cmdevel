<?

/**
 * Classe que implementa um form HIDDEN
 *
 * Classe que implementa um form HIDEEN
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */
class CMWHidden extends CMWInputEl {

  function __construct($name,$value) {    
    parent::__construct($name,$value,"hidden");
  }

}

?>