<?

/**
 * Classe que implementa duas listas, uma com os objetos e outra que receberá os objetos da primeira
 *
 * Classe que implementa duas listas, uma com os objetos e outra que receberá os objetos da primeira
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  WFormEl, WSelect
 */

class CMWListAdd extends CMWFormEl {
  private $options;
  private $slist;
  private $dlist;
  private $groupingList;
  private $groupingField;
  private $groupin;

  function __construct($name,$source_list,$dest_list="",$fieldValue="",$fieldLabel="") {
    global $smartform;

    parent::__construct();
    $this->source_list = $source_list;
    $this->dest_list = $dest_list;
    $this->fieldValue = $fieldValue;
    $this->fieldLabel= $fieldLabel;
    $this->setName($name);
    $this->name = $name;

    $this->requires("addlist.js");
    $path = "cmwebservice/cmwsmartform/media/javascript/";

    $this->requires("$path/addlist.js",self::MEDIA_JS_WRAPPER);

    $this->slist = new CMWSelect($this->name."_source");
    if(!empty($this->source_list)) {
      $this->slist->addOptions($this->source_list,$this->fieldValue,$this->fieldLabel);
    }
    $this->slist->setProp("multiple","");
    $this->slist->setSize(10);


    $this->dlist = new CMWSelect($this->name."[]");
    if(!empty($this->dest_list)) {
      $this->dlist->addOptions($this->dest_list,$this->fieldValue,$this->fieldLabel);
    }
    $this->dlist->setProp("multiple","");
    $this->dlist->setSize(10);
        
  }


  public function getSourceWidget() {
    return $this->slist;
  }

  public function getDestinationWidget() {
    return $this->dlist;
  }

  function __toString() {
    global $_CMAPP;


    $this->parentForm->addOnSubmitAction("addListSend(this['".$this->dlist->name."'])");

    if (!empty($this->label)) {
      parent::add($this->label);
    }
    
    parent::add("<table border=0>");


    parent::add("<tr><td rowspan=2>");

    parent::add($this->slist);

    $b1 = new CMWButton("send",">>","button");
    $b1->setOnClick("javascript:move(this.form['".$this->slist->name."'],this.form['".$this->dlist->name."'])");

    $b2 = new CMWButton("del","<<","button");
    $b2->setOnClick("javascript:move(this.form['".$this->dlist->name."'],this.form['".$this->slist->name."'])");

    parent::add("<td>");
    parent::add($b1);
    parent::add("</td><td rowspan=2>");

    parent::add($this->dlist);

    parent::add("</td><tr><td>");
    parent::add($b2);

    parent::add("</table>");

    return parent::__toString();

  }

}

?>