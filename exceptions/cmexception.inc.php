<?php

/** BASE EXCEPTION CLASS FOR CMDEVEL **/
/**
 * @package cmdevel
 * @subpackage exceptions
 * 
 * @author Juliano Maicon Brauwer <maicon@edu.ufrgs.br>
 **/
class CMException extends Exception 
{
  protected $module;
  protected $message;

  function __construct() {
    $numArgs = func_num_args();
    $args = func_get_args();
    if( $numArgs==2) {
      $this->module = $args[0];
      $this->message = $args[1];
    }
    else {
      $this->module = "CMDEVEL";
      $this->message = $args[0];
    }
    parent::__construct($this->printException());
  }

  public function printException() {
    $message = "<FONT COLOR=\"#2200AA\">$this->module throws an exception with message: <FONT COLOR=\"#AA0000\">".$this->message."</FONT></FONT></FONT>";
    return $message;
  }

  public function __toString() {
    $men = parent::__toString();
    return nl2br($men);
  }
  
}


?>
