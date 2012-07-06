<?

class CMWEmailException extends CMException {

  public function __construct($message) {
    parent::__construct("CMWEmail exception: $message");
  }
}


class CMWEmailSenderNotDefined extends CMWEmailException {
  public function __construct() {
    parent::__construct("You must define a sender for the e-mail.");
  }
}

class CMWEmailRecipientNotDefined extends CMWEmailException {
  public function __construct() {
    parent::__construct("You must define who will receive this e-mail.");
  }
}

class CMWEmailNoSubject extends CMWEmailException {
  public function __construct() {
    parent::__construct("You doesn't define an subject for this email.");
  }
}

class CMWEmailNoMessage extends CMWEmailException {
  public function __construct() {
    parent::__construct("You doesn't define an message body for this email.");
  }
}

class CMWEmailNotSend extends CMWEmailException {
  public function __construct() {
    parent::__construct("The mail function cannot send this email message.");
  }
}



?>