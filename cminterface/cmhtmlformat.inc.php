<?

/** 
 *  Classe para gerenciar as formatacoes de html
 *
 *  @package cmdevel
 *  @subpackage cminterface
 *  @author Maicon Brauwers <maicon@edu.ufrgs.br>
 */
class CMHtmlFormat {
  
  private $iniTabela,$fimTabela;
  private $iniLinha,$fimLinha;
  private $iniColuna,$fimColuna;
  private $iniLinTitulo,$fimLinTitulo;
  private $iniColTitulo,$fimColTitulo;
  
  function __construct() {
    $this->defaultFormat();
  }
  
  /** Seta a formatacao default
   *
   */  
  public function defaultFormat() {
    $this->iniTabela="table bgcolor=black><tr><td><table cellspacing=1><tr";
    $this->fimTabela="/table></td></tr></table";
    $this->iniLinha="tr";
    $this->fimLinha="/tr";
    $this->iniColuna="td";
    $this->fimColuna="/td";
    //Titulo
    $this->iniLinTitulo="tr";
    $this->fimLinTitulo="/font></tr";
    $this->iniColTitulo="td";
    $this->fimColTitulo="/td";
  }

  public function getIniTabela() {    
    return $this->iniTabela;
  }
  
  public function getFimTabela() {
    return $this->fimTabela;
  }
  
  public function getIniLinha() {
    return $this->iniLinha;
  }

  public function getFimLinha() {
    return $this->fimLinha;
  }
  
  public function getIniColuna() {
    return $this->iniColuna;
  }
  
  public function getFimColuna() {
    return $this->fimColuna;
  }
  
  public function getIniLinTitulo() {
    return $this->iniLinTitulo;
  }

  public function getFimLinTitulo() {
    return $this->fimLinTitulo;
  }
  
  public function getIniColTitulo() {
    return $this->iniColTitulo;
  }
  
  public function getFimColTitulo() {
    return $this->fimColTitulo;
  }
  
  public function getIniTabelaTag() {    
    return "<".$this->iniTabela.">";
  }
  
  public function getFimTabelaTag() {
    return "<".$this->fimTabela.">";
  }
  
  public function getIniLinhaTag() {
    return "<".$this->iniLinha.">";
  }

  public function getFimLinhaTag() {
    return "<".$this->fimLinha.">";
  }
  
  public function getIniColunaTag() {
    return "<".$this->iniColuna.">";
  }
  
  public function getFimColunaTag() {
    return "<".$this->fimColuna.">";
  }
  
  public function getIniLinTituloTag() {
    return "<".$this->iniLinTitulo.">";
  }

  public function getFimLinTituloTag() {
    return "<".$this->fimLinTitulo.">";
  }
  
  public function getIniColTituloTag() {
    return "<".$this->iniColTitulo.">";
  }
  
  public function getFimColTituloTag() {
    return "<".$this->fimColTitulo.">";
  }

  public function setTabela($iniTabela,$fimTabela) {
    $this->iniTabela = $iniTabela;
    $this->fimTabela = $fimTabela;
  }
  
  public function setLinha($iniLinha,$fimLinha) {
    $this->iniLinha = $iniLinha;
    $this->fimLinha = $fimLinha;
  }
  
  public function setColuna($iniColuna,$fimColuna) {
    $this->iniColuna = $iniColuna;
    $this->fimColuna = $fimColuna;
  }
  
  public function setLinhaTitulo($iniLinTitulo,$fimLinTitulo) {
    $this->iniLinTitulo = $iniLinTitulo;
    $this->fimLinTitulo = $fimLinTitulo;
  }
  
  public function setColunaTitulo($iniColTitulo,$fimColTitulo) {
    $this->iniColTitulo = $iniColTitulo;
    $this->fimColTitulo = $fimColTitulo;
  }
  
}


?>