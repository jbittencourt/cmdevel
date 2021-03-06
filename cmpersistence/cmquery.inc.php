<?php

/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMQuery {

	const UNION_ALL = "ALL";
	const UNION_DISTINCT = "DISTINCT";

	const DEBUG_NONE = 0;
	const DEBUG_SQL = 1;
	const DEBUG_ALL = 2;

	private $filter;
	private $classes;
	private $result;
	public  $tables;
	private $limits = array("upper"=>-1,
			  "lower"=>-1
	);
	protected $_debugLevel = 0;
	private $order;
	public  $variables = array();
	private $_db;
	private $distinct = 0;

	public  $join = array();
	private $return_fields = array("*");
	private $union;
	private $groupby;
	private $count;
	private $countField;
	private $uniton;

	private $projectionFields;

	private $joinVarnameCounter=0;

	public function __construct() {
		global $_CMAPP;

		$n_args = func_num_args();
		$args = func_get_args();
		if($n_args==0) {
			Throw new CMObjException("CMQuery->__construct must contain at least one class name as parameter");
		}

		if($_CMAPP['db'] instanceof CMDBConnection) {
			$this->_db = $_CMAPP['db'];
		}
		else {
			throw new CMDBConnectionNotFound;
		}

		foreach($args as $class) {
			$obj = new $class;
			$tablename = $obj->getTable();
			$this->tables["$class"] = $tablename;
			$this->classes[$tablename] = $class;
		}

	}

	public function setDistinct() {
		$this->distinct = 1;
	}


	public function setProjection() {
		$n_args = func_num_args();
		$args = func_get_args();

		if($n_args==0) {
			Throw new CMObjException("CMQuery->setReturnFields must contain at least one parameter");
		}

		$this->return_fields = $args;
	}

	public function setFilter() {
		$filter = "";

		$n_args = func_num_args();
		$args = func_get_args();
		if($n_args==0) {
			Throw new CMObjException("CMQuery::setFilter must contain at least one class name as parameter");
		}

		foreach($args as $arg) {
			if(is_string($arg)) {
				$filter[]=" $arg";
			}
			elseif ($arg instanceof CMQuery) {
				$filter[]=$arg;
			}
			else {
				Throw new CMObjException("CMQuery::setFilter only accepts string and CMQuery objects as parameters.");
			}
		}


		$this->filter = $filter;
	}
	

	public function getFilter() {
		if(empty($this->filter)) return "";
		return implode("",$this->filter);
	}

	public function setOrder($order) {
		$this->order = $order;
	}

	public function setLimit($lower,$upper) {
		$this->limits['upper'] = $upper;
		$this->limits['lower'] = $lower;
	}

	public function addVariable($name,$value) {
		$this->variables[$name] = $value;
	}

	public function setCount($field="*") {
		$this->count = 1;
		$this->countField = $field;
	}

	public function groupBy($value) {
		$this->group = $value;
	}

	
  /**
   * Adds an join to this query;
   **/
	public function addJoin(CMJoin $value,$varname="") {
		if(empty($varname)) {
			if(!$value->getFake()) {
				Throw new CMQueryJoinNotFake;
			}
			$varname = 'fakejoin_'+$this->joinVarnameCounter;
		}
		$this->joinVarnameCounter++;
		$this->join[$varname] = $value;
	}

	public function union(CMQuery $union_with,$type=self::UNION_ALL) {
		$this->union = array("union"=>$union_with,
			 "type"=>$type);
	}

	public function getResult() {
		return $this->result;
	}


	public function __toString() {

    //variables for class to table string conversion
    //in the filter and variable
		$expr = array();
		$tbls = array();

		foreach($this->classes as $table=>$class) {
			$expr[] = "$class::";
			$tbls[] = "$table.";
		}



		$sql = "SELECT ";
		if($this->distinct)
		$sql.= " DISTINCT ";

		if($this->count) {
			$sql .= " COUNT($this->countField) ";
		}
		else {
			$tabs = $this->return_fields;
			if(!empty($this->variables)) {
				foreach($this->variables as $name=>$value) {
					$tabs[]="$value AS $name";
				}
			}
      //fields
			$sql.= " ".implode(", ",$tabs);
		}
		
    //tables names
		$sql.= " FROM  ".implode(", ",$this->tables);


    //test if this query is a join between two classes
		if($this->joinVarnameCounter>0) {
			foreach($this->join as $join) {
				$expr[] = "$join->class::";
				$tbls[] = "$join->table.";
				$sql.= $join->__toString();
			}
		}
		
		if(!empty($this->filter)) {
			$filter="";
			foreach($this->filter as $arg) {
				if(is_string($arg)) {
					$filter.=" $arg";
				}
				else {
					$filter.="(".$arg->__toString().")";
				}
			}
			$sql.= " WHERE $filter ";
		}

		if(!empty($this->group)) {
			$sql.=" GROUP BY ".$this->group;
		}

		if(!empty($this->order))
		$sql.= " ORDER BY $this->order ";


		if(($this->limits['upper']>0) and ($this->limits['lower']>=0))
		$sql.= " LIMIT ".$this->limits['lower'].",".$this->limits['upper']." ";

		if(!empty($this->union)) {
			$sql = "$sql UNION ".$this->uniton['type']." ".$this->union['union']->__toString()."";
		}

		if(!empty($this->group)) {
			$this->group = str_ireplace($expr,$tbls,$this->group);
		}


		$sql = str_ireplace($expr,$tbls,$sql);
		return $sql;
	}

	/**********
	* Set the query to display debug info.
	* This function set this query to print debug information
	* during it's exection. The displayed information contains
	* the generated SQL, information about the tables and the
	* and the result returned by mysql.
	*
	* @param integer $level The debug level to be set
	**/
	public function setQueryDebugLevel($level) 
	{
		$this->_debugLevel=$level;
	}

	public function execute() 
	{

		$sql = $this->__toString();
		if($this->_debugLevel>=self::DEBUG_SQL) echo "Query SQL [$sql]\n";
		$res = $this->_db->query($sql);

    //return the count query result number 
		if($this->count) {
			$count = $res->fetch_row();
			$res->free();
			return $count[0];
		}

		$cont = new CMContainer;

    //fetch info from fields of the result
		reset($this->classes);
		list($mtable, $mclass) = each($this->classes);
		$mtable = strtolower($mtable);
		$info = $res->fetch_fields();

		if($this->_debugLevel==self::DEBUG_ALL) {
			echo "Query Table Info\n";
			note($info);
		}

		$stru = array();
		$pkeys = array();

    //this is an empy array of variable that is assigned to the CMObj::variableValues
    //This code creates the var on the fly. But in a left join, there is some ocasion
    //that the variable is not created. This code garantee this creation. See the
    //$this->join loop below too.
		$empty_variable_array = array();
		if(!empty($this->variables)) {
			foreach($this->variables as $key=>$var) {
				$empty_variable_array[$key] = "";
			}

		}
		
		if(!empty($this->group)) {
			$temp = explode(".",$this->group);
			if(count($temp)==1) {
				$groupby = $temp[0];
			}
			else {
				$groupby = $temp[1];
			}
		}
		
		foreach($info as $key=>$col) {
			if(!array_key_exists($col->orgtable,$stru)) {
				$stru[strtolower($col->orgtable)] = array();
				$pkeys[strtolower($col->orgtable)] = array();
			}
			$stru[$col->orgtable][] = $key;
			if(empty($groupby)) {
				if($col->flags & MYSQLI_PRI_KEY_FLAG) {
					$pkeys[strtolower($col->orgtable)][] = $key;
				}
			}
			else {
				if(($col->orgname==$groupby) OR ("$col->orgtable.$col->orgname"==$groupby)) {
					if($col->flags & MYSQLI_PRI_KEY_FLAG) {
						$pkeys[strtolower($col->orgtable)][] = $key;
					}
				}
			}
		}

    //make an table of with joininfo
		$join = array();

		if(count($this->join)>0) {
			foreach($this->join as $var=>$j) {
				if(!$j->fake) {
					$join[strtolower($j->table)] = array("class"=>$j->class,
				                             "var"=>$var);
					$empty_variable_array[$var] = "";
				}
				else {
					$pkeys[strtolower($j->table)] = array();
				}
			}
		}
		$obj = null;
		$contPKeys = 0;  //primary keys alternate counter;

		while($row = $res->fetch_row()) {
			$new_obj = true;
			$key_items=array();

            //generate an unique hash for the object
			if(empty($pkeys[$mtable])) {
	            //if the table doesnt have a primary key 
	            //use the counter
				$pkey = $contPKeys++;
			} else {
				foreach($pkeys[$mtable] as $k) {
					$key_items[] = $row[$k];
				}
				$pkey = implode("_",$key_items);
			}
						
			
			if(array_key_exists($pkey,$cont->items)) {
				$obj = $cont->items[$pkey];
			}
			else {
				$obj = new $mclass;
				$obj->variablesValues = $empty_variable_array ;
                $obj->state = CMObj::STATE_PERSISTENT;
				$cont->items[$pkey]=$obj;
			}

			$objs = array();
			$objs[strtolower($mtable)] = $obj;
			foreach($row as $k=>$value) {
				$name    =  $info[$k]->name;
				$cname   =  $info[$k]->orgname;
				$ctable  =  strtolower($info[$k]->orgtable);
				$cmtable =  strtolower($info[$k]->table);
	    
								
				//if the value is empty, go to the next row
				if(empty($value)) continue;

				//garantee that the $cname ins't empty
				if(empty($cname)) $cname = $name;

								
				//test if the column is a variable. In this case it`s computed
				//so it should be assigned to the variables array
				if(array_key_exists($cname,$this->variables)) {
					$obj->variablesValues[$cname] = $value;
					continue;
				}
				
				
				if(!array_key_exists( $ctable ,$objs) && !empty($ctable)) {
					if(!empty($join[$ctable])) {
						$objs[$ctable]        = new $join[$ctable]["class"];
						$objs[$ctable]->state = CMObj::STATE_PERSISTENT;
					}
				}

				if(!isset($objs[$ctable])) $objs[$ctable] = "";
				
				if(empty($ctable)) {
					$tmp_obj = $objs[ (string) $mtable ];
				} else { 
					$tmp_obj = $objs[ (string) $ctable ];
				}
				$tmp_obj->fieldsValues[$cname] = $value;
			}
			
			foreach($join as $table=>$i) {
				if(!array_key_exists($table,$objs)) continue;
				if(!($obj->variablesValues[$i["var"]] instanceof CMContainer)) {
					$obj->variablesValues[$i["var"]] = new CMContainer;
				}
				$obj->variablesValues[$i["var"]]->items[]= $objs[$table];
			}
			
			
		}
		
		$res->free();
		return $cont;

	}

}

