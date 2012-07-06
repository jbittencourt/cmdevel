<?
/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMContainerIterator implements Iterator {

  private $cont;
  private $size;
  private $index=0;
  private $returned = 0;
  private $map = array();

  public function __construct(CMContainer $cont) {
    $this->cont = $cont;
    $this->size = count($this->cont->items);
    foreach($this->cont->items as $k=>$temp) {
      $this->map[] = $k;
    }
    
  } 

  function rewind() {
    $this->index = 0;
  }

  function hasMore() {
    return $this->returned < $this->size;
  }


  function key() {
    return $this->map[$this->index];
  }

  function current() {
    if(isset($this->cont->items[$this->map[$this->index]])) {
       $this->returned++;
       return $this->cont->items[$this->map[$this->index]];
    }
    if($this->index>$this->size) {
      Throw new CMObjException("Out of bounds");
    }
    $this->index++;
    return $this->current();
  }

  function next() {
    $this->index++;
  }

  function valid() {
    return $this->returned < $this->size;
  }

}

?>
