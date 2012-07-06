<?


class CMWJSWin extends CMHTMLObj {

  protected $link;
  protected $title;
  protected $width;
  protected $height;
  protected $location;
  protected $status; 
  protected $toolbar; 
  protected $scrolling;
  protected $scrollbars;

  public function __construct($link, $titulo, $w=200, $h=400) {
    parent::__construct();
    $this->link = $link;
    $this->title = $titulo;
    $this->width = $w;
    $this->height = $h;

    $this->location = "no";
    $this->status = "no";
    $this->toolbar = "no";
    $this->scrolling = "yes";
    $this->scrollbars = "yes";
    $this->resize = "yes";

  }


  public function setLocationOn() {
    $this->location = "yes";
  }

  public function setStatusOn() {
    $this->status = "yes";
  }
  
  public function setToolbarOn() {
    $this->toolbar = "yes";
  }
  
  public function setScrollingOff() {
    $this->scrolling = "no";
  }

  public function setScrollbarsOff() {
    $this->scrollbars= "no";
  } 

  public function setResizeOff() {
    $this->resize= "no";
  } 

  public function __toString() {
    $string ="";
    $string.= "handle = window.open('$this->link','$this->title','width=$this->width,";
    $string.= "height=$this->height,resizable=$this->resize,status=$this->status,location=$this->location,";
    $string.= "scrolling=$this->scrolling,toolbar=$this->toolbar,scrollbars=$this->scrollbars');";
    $string.= "handle.opener = self;";

    parent::add($string);

    return parent::__toString();
  }


}



?>