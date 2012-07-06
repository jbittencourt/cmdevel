<?php

class CMJoin {
  const NATURAL = 1;
  const LEFT = 2;
  const INNER = 3;
  const NATURAL_LEFT = 4;
  
  public $type;
  public $class;
  public $table;
  public $on;
  public $using = array();
  public $join;
  public $as;

  public $fake=false;

  public function __construct($type,$as="") {
    $this->type = $type;
    $this->as = $as;
  }

  public function setClass($value) {
    $this->class = $value;
    $obj = new $value;
    $this->table = $obj->getTable();
  }

  
  public function on($value) {
    $this->on = $value;
  }
  
  public function using() {
    $n_args = func_num_args();
    $args = func_get_args();

    if($n_args==0) {
      Throw new CMObjException("CMJoin->using must contain at least one propertie name.");   }
    
    foreach($args as $use) {
      $this->using[] = $use;
    }

  }

  /**
   * Set that this join if fake.
   *
   * This function sets that this join is fake. This means
   * that this join don't return a value in the CMQuery and
   * only is used in the generation of the SQL.
   **/
  public function setFake($value=true) {
    $this->fake = $value;
  }

  /**
   * Return if this join is fake or not.
   * 
   * @return boolean;
   **/
  public function getFake() {
    return $this->fake;
  }


  /**
   *
   **/
  public function __toString() 
  {    $sql = "";
    $as = "";
    //the geneation of the join condition ocurrs here because in this
    //way we can decide if the type of the query can be changed.
    $condition = "";
    if(!empty($this->on)) {
      $condition = "ON (".$this->on.")";
    }
    else {
      if(count($this->using)>0) {
      	//transforms the array of join clauses into a string like "USING (f1,f2,f3...)"
      	$condition = "USING (".implode($this->using,",").")";
      }
      else {
    	//if the on and join clause are empty, this probably means that this
    	//query is a natural join.
	      switch($this->join) {
        	case self::INNER:
	          $this->join = self::NATURAL;
        	  break;
        	case self::LEFT:
	          $this->join = self::NATURAL_LEFT;
      	}
      }
    }

    if(!empty($this->as)) $as = " AS $this->as";
    switch($this->type) {
      case self::INNER:
        $sql .= " INNER JOIN $this->table $as $condition";
        break;
      case self::LEFT:
        $sql .= " LEFT JOIN $this->table $as $condition";
        break;
      case self::NATURAL:
        $sql .= " NATURAL JOIN $this->table $as";
        break;
      case self::NATURAL_LEFT:
        $sql .= " NATURAL LEFT JOIN $this->table $as";
        break;
    }

    return $sql;
  }

} 


