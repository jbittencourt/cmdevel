<?


class CMWSimpleMail {

  private $message;
  private $subject;
  private $to = array();  //just email address
  private $from;
  private $fromName; 
  private $replyTo;


  private $htmlEmail = false;


  public function __construct($from,$fromName="") {
    $this->from = $from;
    $this->fromName = $fromName;
  }


  public function setMessage($message) {
    $this->message = $message;
  }

  public function setSubject($sub) {
    $this->subject = $sub;
  }

  public function addTo($email,$name="") {
    if(empty($name)) {
      $this->to[$email] = $email;
    }
    else {
      $this->to[$email] = "$name <$email>";
    }
    
  }

  public function setReplyTo($mail) {
    $this->replyTo = $mail;
  }

  public function setHTMLMessage() {
    $this->htmlEmail = true;
  }

  

  public function send() {

    if(count($this->to)==0) {
      Throw new CMWEmailRecipientNotDefined;
    }

    if(empty($this->message)) {
      Throw new CMWEmailNoMessage;
    }

    if(empty($this->subject)) {
      Throw new CMWEmailNoSubject;
    }

    if(empty($this->from)) {
      Throw new CMWEmailSenderNotDefined;
    }

    /* recipients */
    $to = array_keys($this->to);
    $to  = implode(", ",$to);



    /* message */
    $headers = array();
    $headers[] = "X-Mailer: PHP/" . phpversion();
    /* To send HTML mail, you can set the Content-type header. */
    if($this->htmlEmail) {
      $headers[]= "MIME-Version: 1.0\r\n";
      $headers[]= "Content-type: text/html; charset=iso-8859-1\r\n";
    }

    /* additional headers */
    $headers[] = "To: ".implode(", ",$this->to);
    if(empty($this->fromName)) {
      $headers[]= "From: ".$this->from;
    }
    else {
      $headers[] = "From: $this->from <$this->fromName>";
    }

    
    if(!empty($this->replyTo)) {
      $headers[] = "Reply-To: ".$this->replyTo;
    }

    //if the message is an CMHTMLObj convert it to a string
    if($this->message instanceof CMHTMLObj) {
      $message = $this->message->__toString();
    }
    else {
      $message = $this->message;
    }


    /* and now mail it */
    $ret = mail($to, $this->subject, $message, implode("",$headers));
    
    if($ret==false) {
      Throw new CMWEmailNotSend;
    };
  }

}


?>
