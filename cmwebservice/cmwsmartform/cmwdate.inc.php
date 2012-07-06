<?

/**
 * Classe que implementa um form tipo Data
 *
 * Classe que implementa um form tipo Data
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm, CMWSmartform, CMWText
 */
class CMWDate extends CMWFormEl {
  private $formato;
  private $calendarOn;
  private $value;

  function __construct($name,$value,$format) {
    parent::__construct($name,$value);

    $this->format = $format;
    $this->requires("cmwebservice/cmwsmartform/media/javascript/data.js.php",self::MEDIA_JS_WRAPPER);
  }

  public function setCalendarOn() {
    $this->calendarOn = 1;
    $this->calendar = new CMWCalendar($this);
  }

  public function __toString() {
    global $_CMAPP, $_language;
    
    $date = array("mday"=>"","mon"=>"","year"=>"","hours"=>"","minutes"=>"",);
    
    if($this->design != CMWFormEl::WFORMEL_DESIGN_STRING_DEFINED) $str = $this->label;
    if($this->design == CMWFormEl::WFORMEL_DESIGN_OVER) $str.= "<br>";

    $format = $this->format;
    $n = strlen($format);

    if(!empty($this->prop['value'])) {
      $date = getdate($this->prop['value']);
    };

   
    $form['day'] = 0;
    $form['month'] = 0;
    $form['year'] = 0;
    $form['hours'] = 0;
    $form['minutes'] = 0;
    $form['seconds'] = 0;

    $last ="";
    for($i=0;$i<$n;$i++) {
      $simb = $format[$i];
      if($last!='\\') {

	switch($simb) {
	case "d":
	  $input = new CMWText($this->name."_day",$date['mday'],2,2);
	  $str .= $input->__toString();
	  $form['day'] = "document.".$this->getFormName()."['".$this->name."_day'].value";
	  break;
	case "m":
	  $input = new CMWText($this->name."_month",$date['mon'],2,2);
	  $str .= $input->__toString();
	  $form['month'] = "document.".$this->getFormName()."['".$this->name."_month'].value";
	  break;
	case "F":
	  $meses = array(1=>$_language['january'],
			 2=>$_language['february'],
			 3=>$_language['march'],
			 4=>$_language['april'],
			 5=>$_language['may'],
			 6=>$_language['june'],
			 7=>$_language['july'],
			 8=>$_language['august'],
			 9=>$_language['september'],
			 10=>$_language['october'],
			 11=>$_language['november'],
			 12=>$_language['december']);
	  $input = new WSelect($this->name."_month");
	  $input->parseOptions($meses);
	  $input->setValue($date['mon']);
	  $str .= $input->__toString();
	  $form['month'] = "document.".$this->parentForm->getName()."['".$this->name."_month'].value";
	  break;
	case "y":
	  $input = new CMWText($this->name."_year",$date['year'],2,2);
	  $str .= $input->__toString();
	  $form['year'] = "document.".$this->parentForm->getName()."['".$this->name."_year'].value";
	  break;
	case "Y":
	  $input = new CMWText($this->name."_year",$date['year'],4,4);
	  $str .= $input->__toString();
	  $form['year'] = "document.".$this->getFormName()."['".$this->name."_year'].value";
	  break;
	case "h":
	  $input = new CMWText($this->name."_hour",$date['hours'],2,2);
	  $input->setProp("onBlur", "return validateHour(this);");
	  $str .= $input->__toString();
	  $form['hours'] = "document.".$this->getFormName()."['".$this->name."_hour'].value";
	  break;
	case "i":
	  $input = new CMWText($this->name."_minutes",$date['minutes'],2,2);
	  $input->setProp("onBlur", "return validateMinutes(this);");
	  $str .= $input->__toString();
	  $form['minutes'] = "document.".$this->getFormName()."['".$this->name."_minutes'].value";
	  break;
	case "s":
	  $input = new CMWText($this->name."_seconds",$date['seconds'],2,2);
	  $input->setProp("onBlur", "return validateSeconds(this);");
	  $str .= $input->__toString();
	  $form['seconds'] = "document.".$this->getFormName()."['".$this->name."_seconds'].value";
	  break;

	default:
	  if($simb!="\\") 
	    $str .= $simb;
	}
      }
      else {
	$str .= $simb;
      }
      $last = $simb;
    }
    parent::add($str);

    if($this->calendarOn) {
      parent::add($this->calendar);
    }


    //Add an submit action to the smartform.
    $js_fname = "validate".$this->name."()";
    $js = "function $js_fname {";
    $js.= "if (validateDate('$this->labelName',$form[day],$form[month],$form[year]) && validateHour('$this->labelName',$form[hours],$form[minutes],$form[seconds])) {";
    $js.= " makeUnixDate(document.".$this->getFormName()."['".$this->name."'],date2timestamp($form[hours],$form[minutes],$form[seconds],$form[month],$form[day],$form[year]));";
    $js.= " return true;";
    $js.= "} else { return false; } ";
    $js.= "};";

    parent::addScript($js);
    
    if(isset($this->parentForm))
      $this->parentForm->addOnSubmitAction($js_fname);
    $hid = new CMWHidden($this->name,$this->value);
    parent::add($hid);
    
    return parent::__toString();
  }

}


?>
