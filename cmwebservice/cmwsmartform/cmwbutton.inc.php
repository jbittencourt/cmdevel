<?
/**
 * Classe que implementa um Botao
 *
 * Classe que implementa um Botao
 *
 * @author Maicon Browers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see CMWForm
 */
class CMWButton extends CMWInputEl {

  function __construct($name,$value,$type="submit") {
    parent::__construct($name,$value,$type);
  }

  function setOnClick($onClick) {
    $this->prop['onClick'] = $onClick;
  }
  
  /** Seta a acao(onclick) deste botao como redirecionamento para a url desejado
   *
   */
  function setRedir($url) {
    $this->setOnClick("window.location.href = '".$url."'");
  }

}

?>