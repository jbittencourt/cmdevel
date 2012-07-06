<?

/** EXCEPTION CLASS FOR DATABASE ERRORS **/

class CMDBException extends CMException {

  function __construct($message) {
    parent::__construct($message);
  }
  
}

/** EXCEPTION FOR CONNECTION FAILURE  */

class CMDBCannotConnect extends CMDBException {
  
  function __construct() {
    parent::__construct("Cannot connect to the database.");
  }
  
}

/** EXCEPTION FOR RECORD(S) NOT FOUND  */

class CMDBNoRecord extends CMDBException {
  
  function __construct($query) {
    parent::__construct("No record(s) returned in query ".$query);
  }
  
}

/**
 * Some error ocured in the last query.
 * 
 * @author Maicon Brauwers <maicon@edu.ufrgs.br>
 * @see CMObj, CMContainer, CMDBConnection
 * @package cmdevel
 * @subpackage cmpersistence
 **/ 

class CMDBQueryError extends CMDBException {

  const ERROR_DUPLICATED_ENTRY = 1062;
  

  protected $query;
  protected $error;

  /**
   * Constructor Method
   *
   * @param String $query The query that returned an error.
   * @param String $error The error message that mysql returned.
   * @param Integer $errno The error number that mysql returned.
   **/
  function __construct($query,$error,$errno) {
    $this->query = $query;
    $this->error = $error;
    $this->errno = $errno;
    if(strlen($query)>1000) $query = substr($query,0,1000);
    
    $style = 'style="color:green"';
    $message = "The query [<span $style>$query</span>] failed with the error $errno:[$error]";
    parent::__construct($message);
  }

  public function getError() {
    return $this->errno;
  }

  public function getQuery() {
    return $this->query;
  }
  
}



/**
 * There is no database connection defined in the $_CMAPP[db] global variable
 * 
 * The CMObj and CMContaner uses a global variable tho connect with the database. 
 * This variable must be in $_CMAPP[db] array, and it is a CMDBConnection
 *
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 * @see CMObj, CMContainer, CMDBConnection
 * @package cmdevel
 * @subpackage cmpersistence
 **/ 

class CMDBConnectionNotFound extends CMDBException {
  function __construct() {
    parent::__construct("Cannot find an valid db connection.");
  }
 
}


/**
 * The field is not defined.
 *
 * This exception means that you are trying to set an field in the
 * object that has not been defined yet. Please verify you object
 * configure method.
 *
 * @author Juliano Bittencourt <juliano@lec.ufrgs.br>
 * @see CMObj, CMContainer
 * @package cmdevel
 * @subpackage cmpersistence
 **/ 

class CMDBFieldNotDefined extends CMDBException {
  function __construct() {
    parent::__construct("You are trying to access a non defined field.");
  }
 
}


/** 
 * Some parameter necessary to connect with the database is missing.
 **/
class CMDBParameterMissing extends CMDBException {
  
  function __construct() {
    parent::__construct("Some parameter necessary to connect with the database is missing. Check yout configuration file.");
  }
  
}




class CMContainerException extends CMException {

  function __construct($message) {
    parent::__construct($message);
  }


}



?>