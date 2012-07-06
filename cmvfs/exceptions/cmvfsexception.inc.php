<?

class CMvfsException extends CMException {

  public function __construct($message) {
    parent::__construct($message);
  }

}

class CMvfsSecurityException extends CMvfsException {
  
  public function __construct($message) {
    parent::__construct($message);
  }
}

class CMvfsUnableToRegister extends CMvfsException {
  
  public function __construct() {
    parent::__construct("The system cannot register this vfs instance.");
  }
}


class CMvfsFileNotFound extends CMvfsException {
  
  public function __construct() {
    parent::__construct("The file you are trying to access doesn't exists");
  }
}
