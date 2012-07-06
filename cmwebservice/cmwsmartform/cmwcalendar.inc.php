<?

/**
 * Classe que implementa um calendario pop-up
 *
 * Classe que implementa um calendario pop-up
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */

class CMWCalendar extends CMHTMLObj {
  protected $wdate;
  protected static $_initialized=false;
  protected static $_count;

  public function __construct($wdate) {
    parent::__construct();
    $this->wdate = $wdate; 

    $this->requires("cmwebservice/cmwsmartform/media/javascript/calendar.js.php",self::MEDIA_JS_WRAPPER);
    $this->requires("cmwebservice/cmwsmartform/media/css/dynCalendar.css",self::MEDIA_CSS_WRAPPER);
  }

  public function __toString() {
    $wd = $this->wdate;

    if(empty($wd->name)) {
      return "";
    };


    if(self::$_initialized==false) {
      self::$_initialized=true;
    }

    $str = "";
    $str.= " function setDateFromCalendar_".$wd->name."(date,month,year) {";
    $str.= "    document.".$wd->getFormName().".".$wd->name."_day.value = date;"; 
    $str.= "    document.".$wd->getFormName().".".$wd->name."_month.value = month;"; 
    $str.= "    document.".$wd->getFormName().".".$wd->name."_year.value = year;";
    $str.= "};";

    $this->addScript($str);
    $layerID = "cmwcalendar_".self::$_count++;
    $this->addScript("calendar_".$wd->name." = new dynCalendar('calendar_".$wd->name."', 'setDateFromCalendar_".$wd->name."','','$layerID');");

    self::addPageEnd('<div class="dynCalendar" id="' .$layerID. '" onmouseover="' .$wd->name. '._mouseover(true)" onmouseout="'. $wd->name. '._mouseover(false)"></div>');


    return parent::__toString();
  }
}


?>
