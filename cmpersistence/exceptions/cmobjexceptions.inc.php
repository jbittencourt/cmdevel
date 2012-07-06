<?

/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Maicon Brauwers <maicon@edu.ufrgs.br>
 **/
class CMObjException extends CMException {
  function __construct($message) {
    parent::__construct($message);
  }
}


/**
 * This  exception is throw when you try to access an enum propertie thats is not inicialized.
 *
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEEnumValuesNotSet extends CMObjException {
  function __construct() {
    parent::__construct("The object initilization is not complete. The enum values are not set.");
  }

}



/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEDeleteNotAllowed extends CMObjException {
  function __construct() {
    parent::__construct("You are trying to delete an object that isnt saved yet.");
  }

}


/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEMoreThanOneRow extends CMObjException {

  function __construct() {
    parent::__construct("The query returned more than one row. Do you dont want to use a CMQuery?");
  }

}


/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjENoPrimaryKeySet extends CMObjException {
  function __construct() {
    parent::__construct("This object doesnt have primary keys, so we cant delete it.");
  }
}


/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEAutoIncrementFieldSet  extends CMObjException {
  function __construct($name) {
    parent::__construct("Field $name is an autoincrement and cannot be altered.");
  }
}



/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEEmptyObj  extends CMObjException {
  function __construct() {
    parent::__construct("You are trying to save an empty object.");
  }
}


/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEVariableChangeNotAllowed  extends CMObjException {
  function __construct($name) {
    parent::__construct("You are trying to access the variable $name. Variables are read only." );
  }
}


/**
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEPropertieNotDefined  extends CMObjException {
  function __construct($name) {
    parent::__construct("Propertie or variable not defined: [$name].") ;
  }
}



/**
 * This occur when an valud type is no valid.
 *
 * This error usually ocurs when you are trying to make an attribution
 * of an invalid type to an propertie, suck to assign an string to an 
 * integer. If the propertie is an enum, it must be an valid string.
 *
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEPropertieValueNotValid extends CMObjException {
  function __construct($message) {
    parent::__construct($message) ;
  }
}


/**
 * This exception occurs when a determined field type is mandatory in an operation.
 *
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEPropertieTypeMismacthed extends CMObjException {
  function __construct($message) {
    parent::__construct($message);
  }

}

/**
 * This exception occurs when mysql return a duplicate entry
 *
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEDuplicatedEntry extends CMObjException {
  function __construct() {
    parent::__construct("This entry alredy exists.");
  }

}


/**
 * This exception occurs when an operation done in a contationer fail.
 *
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMObjEContainerOperationFailed extends CMNestedException {
  function __construct() {
    parent::__construct("The ACID operation you requested failed.");
  }

}

/**
 * This exception occurs when the user tries to set a join in a CMQuery withou inform the variable name
 *
 * @package cmdevel
 * @subpackage cmpersistence
 * 
 * @see CMJoin::setFake()
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 **/
class CMQueryJoinNotFake extends CMException {
  function __construct() {
    parent::__construct("You must set a variable name to CMJoin that isn't set as fake.");
  }

}



?>