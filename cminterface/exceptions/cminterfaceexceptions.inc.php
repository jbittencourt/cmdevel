<?


class CMInterfaceException extends CMException {

  public function __construct($message) {
    parent::__construct($message);
  }
}


class CMIECMHTMLObjNotInitilied extends CMInterfaceException {
  public function __construct($class) {
    parent::__construct("You do not called the parent::__contruct() for an CMHTMLObj subclass [$class].");
  }
}


class CMIEUnrecognizedObject extends CMInterfaceException {
  public function __construct() {
    parent::__construct("Unrecognized Object: you are trying to print an object that is not a string, a CMHTMLObj or a CMObj.");
  }
}

class CMIENoLanguageConfigFound extends CMInterfaceException {
  public function __construct() {
    parent::__construct("You have not defined a laguage config section in your config.xml.");
  }
}



class CMIELanguageFileFound extends CMInterfaceException {
  public function __construct() {
    parent::__construct("Cannot find language file.");
  }
}

class CMIELanguageSectionNotDefined extends CMInterfaceException {
  public function __construct() {
    parent::__construct("The language section you are requesting is empty or dosen't exists.");
  }
}


?>