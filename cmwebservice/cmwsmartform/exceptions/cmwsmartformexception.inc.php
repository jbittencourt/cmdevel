<?
class CMWSmartFormException extends CMException {
  
  function __construct($message) {
    parent::__construct($message);
  }
}

class CMWSmartFormEFieldNotFound  extends CMWSmartFormException {
  
  function __construct($field) {
    parent::__construct("This form hasen't a field with the name $field.");
  }
}


Class CMWSmartFormEVariableChangeNotAllowed extends CMWSmartFormException {
  
  function __construct() {
    parent::__construct("You tried accesess not allowed property $message");
  }
}
?>