<?

/**
 * @package cmdevel
 * @subpackage cmapp
 **/
 
/** EXCEPTION CLASS FOR DATABASE ERRORS **/

class CMAPPException extends CMException {

  function __construct($message) {
    parent::__construct($message);
  }
  
}


class CMErrorLoadingConfigFile extends CMAPPException {

  function __construct() {
    parent::__construct("A fatal error orcured reading the config file ");
  }

}


class CMLoginFailure extends CMAPPException {

  function __construct() {
    parent::__construct("The login failed.");
  }

}


class CMGroupException extends CMAPPException {
  function __construct($msg) {
    parent::__construct($msg);
  }
}

class CMGroupCannotAddUser extends CMGroupException {
  function __construct(CMException $reason) {
    parent::__construct('Cannot add user to group: '.$reason->__toString());
  }
}

class CMACLException extends CMAPPException{
  function __construct($error) {
    parent::__construct('Error in the ACL: '.$error);
  }
}

class CMACLEPrivilegeAlreadySet extends CMAPPException {
  function __construct($privilege) {
    parent::__construct('This user or group has already the privilege "'.$privilege.'" set.');
  }
}


?>