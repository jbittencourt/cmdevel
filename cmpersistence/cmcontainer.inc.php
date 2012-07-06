<?php

include("cmpersistence/cmcontaineriterator.inc.php");

if(!function_exists('array_intersect_key')) {
	include 'PHP/Compat.php';
	@PHP_Compat::loadFunction('array_intersect_key');
	@PHP_Compat::loadFunction('array_diff_key');
}


/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMContainer implements IteratorAggregate, ArrayAccess {

	const OPERATION_DELETE=2;
	const OPERATION_SAVE=1;

	public $items;
	private $offsetmap=array();

	protected static $db;
	private static $init=0;

	public function __construct() {
		$this->items = array();

		if(!self::$init) {
			global $_CMAPP;
			if($_CMAPP['db'] instanceof CMDBConnection) {
				self::$db = $_CMAPP['db'];
			}
			else {
				throw new CMDBConnectionNotFound;
			}
			self::$init = 1;
		}
	}

	public function count() {
		return count($this->items);
	}

	public function get($value) {
		return (isset($this->items[$value]) ? $this->items[$value] : 0);
	}

	public function __isEmpty() {
		return !count($this->items);
	}

	public function __hasItems() {
		return count($this->items);
	}

	public function add($key, CMObj $value) {
		$this->items[$key] = $value;
		$this->offsetmap[] = $key;
	}


	public function getIterator() {
		return new CMContainerIterator($this);
	}

	public function acidOperation($operation) {
		$db = self::$db->getDB();
		$db->autoCommit(0);
		$rollback = 0;
		switch($operation) {
			case  self::OPERATION_SAVE:
				foreach($this->items as $item) {
					$item->update_after_insert=0;
					try {
						$item->save();
					}
					catch(Exception $e) {
						$rollback = 1;
						$cause = $e;
						break;
					}
				}
				break;
			case self::OPERATION_DELETE:
				foreach($this->items as $item) {
					try {
						$item->delete();
					}
					catch(Exception $e) {
						$rollback = 1;
						break;
					}
				}
				break;
		}
		if($rollback) {
			self::$db->rollback();
			$e = new CMObjEContainerOperationFailed;
			$e->setRootCause($cause, $item);
			Throw $e;
		}
		else {
			$db->commit();
		}
		
	}
	

	public function __toString() {

		$cols = array(); //contain the cols names
		$cols_size = array(); //contain the max size of the colum
		$rows = array(); //contain the value of the row

		if(!empty($this->items)) {
			reset($this->items);
			list($k,$f)=each($this->items);
			$cols = $f->getFields();
			foreach($cols as $col) {
				$cols_size[$col] = strlen($col);
				$rows[-1][$col] = $col;
			}
		}

		foreach($this->items as $k=>$item) {
			foreach($cols as $col) {
				$rows[$k][$col] = $item->$col;
				if(strlen($item->$col)>$cols_size[$col])
				$cols_size[$col]=strlen($item->$col);
			}
		}

		
		$max = array_sum($cols_size)+(count($cols_size)*2);
		$max += 3;

		$ret = "<pre>".str_repeat("-",$max)."\n";
   
		foreach($rows as $row) {
			$ret.="| ";
			foreach($row as $col=>$item) {
				$ret .= $item. str_repeat(" ",$cols_size[$col]-strlen($item))." | ";
			}
			$ret.="\n";

		}
		$ret.= str_repeat("-",$max)."</pre>";

		return $ret;
	}
	


  /**
   * Array Access interface implementation
   **/
	function offsetExists($offset){
		
		if(isset($this->items[$offset])){
			return TRUE;
		}
		else{
			return FALSE;
		}
		
	}
	
	function offsetGet($offset){
		$temp = array_values($this->items);
		return $temp[$offset];
	}
	
	function offsetSet($offset, $value){
		if($offset){
			$temp = array_values($this->items);
			$needle = $temp[$offset];
			$key = array_search($needle,$this->items);
			$this->items[$key] = $value;
		}
		else{
			$this->items[] = $value;
		}
		
	}
	
	function offsetUnset($offset){
		$temp = array_values($this->items);
		$needle = $temp[$offset];
		$key = array_search($needle,$this->items);
		unset($this->items[$key]);
	}

	public function toArray() {
		return $this->items;
	}

	public function sub(CMContainer $minus) {
		if($minus->__hasItems()) {
			$intersec = array_intersect_key($this->items,$minus->items);
			$this->items = array_diff_key($this->items, $intersec);
		}
	}

	public function intersec(CMContainer $cont) {
		if($cont->__hasItems()) {
			$this->items = array_intersect_key($this->items,$cont->items);
		}
	}

	public function in($key) {
		return array_key_exists($key,$this->items);
	}

	public function remove($key) {
		unset($this->items[$key]);
	}

}