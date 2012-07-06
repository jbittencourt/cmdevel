<?php


class CMSearch extends CMQuery {


  protected $search_fields=array();
  protected $search_string;

  /**
   * Sempre tem uma criatura que nao dah credito a uma ferramenta,
   * por isso eu crie o metodo de adicao manual do filtro.
   *
   * @param String filter - Clausula SQL_WHERE.
   * @return Void
   */
  public function setSearchString($str) {
    if(empty($str)) {
      Throw new CMObjException("The search string in CMSeach::setSeachString cannot be empty.");
    }

    $this->search_string = addslashes($str);
    //$this->search_string = stripslashes($this->search_string);
    $this->search_string = htmlentities($this->search_string);
  }

  /**
   * Esta eh a funcao usada para setar quais campos
   * de uma determinada tabela vao ser usados na busca.
   *
   * Ex.: $fields = array();
   *      $fields[] = "Table.fieldsName";
   *      AMSearch::addSearchFields($fields);
   *
   * @param Array fields - Array contendo os campos que serao afetado pela busca
   * @return Void
   */
  public function addSearchFields() {

    $n_args = func_num_args();
    $args = func_get_args();    
    if($n_args==0) {
      Throw new CMObjException("CMSeach::addSearchFields must contain at least one class name as parameter");
    }

    foreach($args as $arg) {
      $this->search_fields[] = $arg;
    }
  }

  public function getWhereClause() 
  {
	    if(!empty($this->search_string)){
      $params = explode(" ", $this->search_string);
      
      $tokNum = 0;
     
      $tokens[$tokNum] = "";
      $concat = "";
      foreach($params as $param) {
	if(!isset($tokens[$tokNum])) {
	  $tokens[$tokNum] = "";
	}
	
	if(!empty($concat)) {
	  $param = $concat.$param;
	}

	if(($param=="+") || ($param=="-")) {
	  $concat = $param;
	  continue;
	}
	else {
	  $concat = "";
	}

	if(ereg("^\"", $param) || ereg("^[+-]\"", $param)) {
	  $inQuotedString = 1;
	} else $inQuotedString = 0;
	
	if($inQuotedString == 1) {
	  $tokens[$tokNum] .= ereg_replace("\"", "", $param)." ";
	} else {
	  $tokens[$tokNum++] = $param;
	}

	if(ereg("\"$", $param)) {
	  $inQuotedString = 0;
	  $tokens[$tokNum] = rtrim($tokens[$tokNum]);
	  $tokNum++;
	}
      }

      //clausula where
      foreach($tokens as $token) {
	$concat = "OR";
	$condition = "";
	foreach($this->search_fields as $field) {
	  $token = ereg_replace(" $", "", $token);
	  if(ereg("^\\+", $token)) {
	    $tok = ereg_replace("^\\+", "", $token);
	    $condition .= $field." LIKE '%$tok%'";
	    if($field != $this->search_fields[(sizeof($this->search_fields)-1)]) $condition  .= " OR ";
	    $concat = "AND";
	  } else if(ereg("^\\-", $token)) {
	    $tok = ereg_replace("^\\-", "", $token);
	    $condition .= $field." NOT LIKE '%$tok%'";
	    if($field != $this->search_fields[(sizeof($this->search_fields)-1)]) $condition  .= " AND ";
	    $concat = "AND";
	  } else {
	    $condition .= $field." LIKE '%$token%'";
	    if($field != $this->search_fields[(sizeof($this->search_fields)-1)]) $condition  .= " OR ";
	  }
	}
	
	if(!empty($sqlWhere)) {
	  $sqlWhere = "($sqlWhere) $concat ($condition) ";
	}
	else { 
	  $sqlWhere = $condition;
	}
	
      }

    }
    $f = $this->getFilter();
    if(!empty($f)) {
      $sqlWhere = "$f AND (".$sqlWhere.")";
    }
    
    return $sqlWhere;

  }

  /**
   * Esta funcao monta, a partir dos campos que seram afetados pela busca
   * e o texto a ser buscado, um filtro para o SQL_WHERE.
   * Usando expressoes regulares eh feita uma depuracao da string de busca e
   * re-organizacao para melhor efeito da busca.
   * No futuro quem sabe eu nao coloco um suporte melhor a expressoes regulares. heheh
   * Mas por enquanto, fica funcionando assim:
   * Um texto como este AAA AAA, nao retorna o mesmo que "AAA AAA". O script sabe entender
   * quando uma coisa esta agrupada ou nao. e coisas do tipo "AAA AAA" -aaa faz com que ele busque
   * qualquer texto que contenha "AAA AAA", e que nao contenha aaa.
   * O mesmo serve para o sinal +.
   *
   * @param Empty
   * @return String sqlWhere - string para a clausula SQL_WHERE
   */
  public function __toString() {
	$this->setFilter($this->getWhereClause());
    return parent::__toString();
  }



}



?>
