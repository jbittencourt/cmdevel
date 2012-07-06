<?
/**
 * Classe que implementa um form tipo FILE
 *
 * Classe que implementa um form tipo FILE
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */
class CMWFile extends CMWInputEl {

  function __construct($nome) {
    parent::__construct();
    $this->setName($nome);
    $this->setType("file");
  }

} 


?>