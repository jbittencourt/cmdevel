<?php

include("cmwebservice/cmwsmartform/cmwform.inc.php");
include("cminterface/cmhtmlformat.inc.php");


/**
 * Classe cujo objetivo e automatizar o processo de construcao de formularios
 *
 * Classe cujo objetivo e automatizar o processo de construcao de formularios
 *
 * @author Maicon Brauwers <maicon@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cmwsmartform
 * @see  CMWForm
 */
class CMWSmartForm extends CMWForm {
  
  const CM_NORMAL_FORM = 1;
  const CM_GRID_FORM = 2;
  
  public $components = array();
  public $rows = array();
  public $design;
  private $required_forms = array();
  private $htmlFormat;
  private $subFormHtml;
  
  public $submit_button;
  public $cancel_button;

  function __construct($objClass,$name,$action="",$fields_rec="",$fields_hidden="",$fields_ausentes="",$method="POST",$enctype="") {
    global $_language;

    $this->__settings['spacing'] = 2;
    
    parent::__construct($name,$action,$method,$enctype);
    
    if(is_string($fields_rec) || empty($fields_rec)) {
      $fields_rec = array($fields_rec);
    }
    if(is_string($fields_ausentes) || empty($fields_ausentes)) $fiedls_ausentes = array($fields_ausentes);
    if(is_string($fields_hidden)) $fields_hidden = array($fields_hidden);
    
    if(!empty($objClass)) {

      $obj = new $objClass;
      $this->objClass = $objClass;

      $class_conf = $obj->getConf();
      $fields_def = $class_conf['fieldsDescription'];
  
      foreach($fields_def as $field => $field_def) {
	if(in_array($field, $fields_hidden)) {
	  $widget = new CMWHidden($field,"");
	  $widget->setName("frm_".$field);
	  $this->addComponent($field, $widget);
	  continue;
	}
      
      // || (!in_array($field,$fields_ausentes))

	if(in_array($field,$fields_rec)) {
	  
	  $widget = $this->getWidgetOfDBField($field, $field_def);
	  
	  $widget->setName("frm_".$field);
	  
	  $this->addComponent($field, $widget);
	  
	  if($field_def['notNull']==1) {
	    $this->required_forms[] = $field;
	  }
	}
      }
    }
    //set the submit and cancel buttons;
    $this->submit_button = new CMWButton("submit",$_language['send']);
    $this->cancel_button = new CMWButton("cancel",$_language['cancel'],"button");
      
    $this->addComponent("submit_group",new CMWFormElGroup);
 
    $this->requires("cmwebservice/cmwsmartform/media/javascript/smartform.js.php",self::MEDIA_JS_WRAPPER);
    $this->requires("cmwebservice/cmwsmartform/media/javascript/browserSniffer.js",self::MEDIA_JS_WRAPPER);

  }

  /**
   *Essa funcao retorna a configuracao do smartform 
   *que se encontra no arquivo package.xml
   */

  public static function getConf() {
    global $_CMDEVEL;

    try {
      $file = new CMConfig($_CMDEVEL['path']."/cmwebservice/cmwsmartform/package.xml");
    }catch (CMErrorLoadingConfigFile $e) {
      die($e->getMessage());
    }
    
    return $file->getObj();
    
  }
  /** Seta o objeto de formatacao da tabela do formulario
   *
   */
  function setHtmlFormat($format) {
    $this->htmlFormat = $format;
  }

  /** Retorna a formatacao html
   *
   */
  function getHtmlFormat() {
    return $this->htmlFormat;
  }

  /** Seta  a formatacao html padrao
   *
   */
  function setDefaultHtmlFormat() {
    $this->htmlFormat = new CMHtmlFormat();
    $this->htmlFormat->setTabela("TABLE CELLPADDING=\"".$this->__settings['spacing']."\" CELLSPACING=\"".$this->__settings['spacing']."\"","/TABLE");
    $this->htmlFormat->setLinha("TR","/TR");
    $this->htmlFormat->setColuna("TD","/TD");
  }

  function forceNotCheckField($remove) {
    foreach($this->required_forms as $k=>$field) {
      if($field==$remove) {
	unset($this->required_forms[$k]);
	return 1;
      }
    }
    
    return 0;
  }

  function forceCheckField($fields) {
    if(is_array($fields)) {
      $this->required_forms = $fields;
    }else $this->required_forms[] = $fields;
  } 


  
  /*
   *Troca o valor da label do botao 
   *submit. 
   *Defaul: Envia
   */
  public function setSubmitButtonLabel($label) {
    $this->submit_label = $label;
  }
 
  /** Muda o design do formlario
   *
   * Muda o modo como sao impressos os labels em relacao com os
   * elementos. A principicio o label pode ser posicionado 
   * na mesma linha (SIDE) ou na linha superiro (OVER)
   *
   * @access public
   * @param int $design pode ser 
   */
  function setDesign($design) {
    $this->__settings['design'] = $design;
  }
  
  /** String que formata o forumulario
   *
   * String que formata o forumulario
   *
   * @access public
   * @param int $design pode ser 
   */
  function setDesignString($designString, $no_interative=0) {
    $this->__settings['designString'] = $designString;
  }

  /**
   * Retorna o widget de formulario conforme o tipo e tamanho do campo da tabela do bco de dados   
   *
   * @access private
   *
  */
  function getWidgetOfDBField($field,$field_def) {
    
    $type = $field_def['type'];
    
    switch ($type) {

    case CMObj::TYPE_VARCHAR:
      if($field_def['size']>1) {
	if ($field_def['size']  < CMWForm::WTEXT_SIZE)
	  $size = $field_def['size'];
	else
	  $size = CMWForm::WTEXT_SIZE;
	$widget = new CMWText($field,"",$size,$field_def['size']);
      }
      else $widget = new CMWCheckbox($field,"1");
      
      break;
      
    case CMObj::TYPE_TEXT:
      $widget = new CMWTextArea($field,CMWForm::WTEXTAREA_ROWS,CMWForm::WTEXTAREA_COLS);
      break;

    case CMObj::TYPE_INTEGER:
      $widget = new CMWText($field,"",$field_def['size'],$field_def['size']);
      break;
      
    case CMObj::TYPE_BLOB:
      $widget = new CMWFile($field);
      break;
    case CMObj::TYPE_ENUM:
      $widget = new CMWSelect($field);
      break;
      
    }
    
    //seta o label
    if (!empty($field_def['label']))
      $widget->addLabel($field_def['label']);
    
    return $widget;
    
  }

  /**
   * Ordena o array de widgets conforme definido pelo usuario
   * $ordem eh um array dos nomes dos campos em ordem que deverao ser exibidos seus widgets
  */
  public function setWidgetOrder($ordem,$appendOthers=0) {
    $comps = array();
    
    //note($this->components);
    foreach ($ordem as $campo) {
      $comps[$campo] = $this->components[$campo];
    }
    
    if ($appendOthers) {
      foreach($this->components as $campo=>$comp) {
	if (!in_array($campo,$ordem))
	  $comps[$campo] = $comp;
      }
    }
    
    foreach($this->components as $campo=>$comp) {
      if(($comp instanceof CMWHidden) || ($comp instanceof CMWFormElGroup))  $comps[$campo] = $comp;
    }
    
    $this->components = $comps;
  }


  /**
   * Normaliza os diversos nomes de tipos de campos em alguns tipos basicos
   * 
   * @param string $field_def Definicao do tipo do campo obtida do banco de dados
   * @return string Tipo do campo Normalizado
  */
  /**
   * Define a estrutura de apresentacao do formulario
   *
   * Definie a estrutura de apresentacao do formulario
   * Cols eh um array em que cada elemento do array eh o numero de colunas para aquela linha da tabela
   *
   * @param integer $cols Numero de colunas nas qual o objeto deve ser apresentado 
  */
  function setStructure($cols) {
    $this->rows = array();
    if(is_array($cols)) {
      foreach ($cols as $col) {
	$this->rows[] = $col;
      }
    }
    else {
      $this->rows[] = $cols;
    }
  }

  /**
   * Define a estrutura de apresentacao do formulario
   *
   * Definie a estrutura de apresentacao do formulario
   * Cols eh um array em que cada elemento do array eh o numero de colunas para aquela linha da tabela
   *
   * @param integer $cols Numero de colunas nas qual o objeto deve ser apresentado 
  */
  function loadDataFromObject($obj) {

    $fields = $obj->getFields();

    foreach($fields as $field) {
      
      if (isset($this->components[$field]) && !($this->components[$field] instanceof CMWFile)) {
	if(!empty($this->components[$field])) {
	  $this->components[$field]->setValue($obj->$field);
	}
      }
    }
    
  }

  /**
   *Adiciona um componente
  */
  
  function addComponent($name,$widget) {
    global $_CMAPP, $_language;
    
    if(!isset($_CMAPP['smartform'])) {
      $_CMAPP['smartform'] = array();
      $_CMAPP['smartform']['language'] = array();
    }

    $lang = $_CMAPP['smartform']['language'];
    
    $this->components[$name] = $widget;
    $this->components[$name]->setParentForm($this);
    $this->components[$name]->name = "frm_$name";
    
    $w = $this->components[$name];

    if(!empty($_language["frm_$name"])) {
      $w->setLabel($_language["frm_$name"]);
    };
    
    if(!empty($_language["frm_".$name."_desc"])) {
      $tip = new CMWTip($_language["frm_".$name."_desc"]);
      $w->tip = $tip;
    }

    if(!empty($_language["frm_".$name."_name"])) {
      $w->setLabelName($_language["frm_".$name."_name"]);
    }
    
  }


  /**
   *  Transforma um campo num hidden.
   *
   * @param string $field  Nome do campo a ser alterado
   * @param string $value  Valor da variavel hidden
  */  
  function setHidden($field,$value) {
    $data = new WHidden("frm_$field",$value);
    $data->formName = $this->name;

    $this->components[$field] = $data;
  }

  /**
   *  Transforma um campo numa wdate.
   *
   * @param string $field  Nome do campo a ser alterado
   * @param string $formato Formato de como os campos devem serem exibidos. Segue o padrao do comando date() do PHP.
  */  

  public function setDate($field,$formato,$calendar=0) {
    $date = new CMWDate("frm_$field","",$formato);
    if($calendar) $date->setCalendarOn();
    $label = $this->components[$field]->getLabel();
    $value = $this->components[$field]->getValue();
    $date->setLabel($label);
    $date->setValue($value);

    $this->addComponent($field,$date);

  }

  /**
   *  Transform an textarea field into a CMWHTMLArea
   *
   * @param string $field  Name of the field to be changed;
   **/  
  public function setHTMLArea($field) {
    if(!array_key_exists($field,$this->components)) {
      Throw new CMWSmartFormEFieldNotFound($field);
    }

    if(!($this->components[$field] instanceof CMWTextarea)) {
      Throw new CMWSmartFormException("You are trying to transform a field that is not an textarea into an CMWHTMLArea");
    }
    $old = $this->components[$field];
    //multiply by * because the textarea is defined in cols and rows and the HTMLArea in pixels
    $comp = new CMWHTMLArea($old->name,$old->getCols()*8, $old->getRows()*8,$old->getValue());
    $comp->setLabel($old->getLabel());
    
    unset($this->components[$field]);
    $this->addComponent($field,$comp);
  }

  

  /**
   *  Transforma um campo num select, onde as opcoes podem ser passadas como parametro ou o objeto lista no qual ira
tirar os dados
   *
   * @param @mixed $options CMContainerIterator retornado pelo CMQuery
  */  

  public function setSelect($field,$options,$index,$list) {
    $name = $this->components[$field]->getName();
    $label = $this->components[$field]->getLabel();
    $value = $this->components[$field]->getValue();
    
    $this->components[$field] = new CMWSelect("frm_".$field,$value);
     
    if(!empty($index) && !empty($list)) {
      foreach($options as $op) {
	$this->components[$field]->addOption($op->$index,$op->$list);
      }
    }
    $this->components[$field]->setLabel($label);

  }
  

  /**
   *  Transforma um campo em um WRadioGroup
   *
   * @param @mixed $options Array do tipo $options[][value][label] ou um RDLista
  */  
  function setRadioGroup($field,$options,$index="",$list="") {
    global $_CMAPP;

    $_language = $_CMAPP['smartform']['language'];

    $name = $this->components[$field]->getName();
    if(isset($_language["frm_$field"])) $label = $_language["frm_$field"];
    else $label = $this->components[$field]->getLabel();

    $value = $this->components[$field]->getValue();
    
    $this->components[$field] = new CMWRadioGroup($name,$label);

    
    if($options instanceof CMContainer) {
      if(!empty($options->items)) {
	foreach($options as $op) {
	  $this->components[$field]->addOption($op->$index,$op->$list);
	}
      }
    }
    else {
      foreach ($options as $value=>$label) {
	$this->components[$field]->addOption($value,$label);
      };
    };
    
  }

  /** Carrega os labels a partir de um array
   *  
   *  @param array $labels : Array de labels no formato array[nomeDoCampo] = $label
   */ 

  function loadLabels($labels) {
    foreach ($this->components as $field=>$w) {
      $this->components[$field]->addLabel($labels[$field]);
    }
  }


  /** 
   * Don't print the cancel button.
   *
   */
  function setCancelOff() {
    $this->__settings['cancelButtonOff'] = 1;
  }

  /** 
   * Don't print the submit button.
   *
   */
  function setSubmitOff() {
    $this->__settings['submitButtonOff'] = 1;
  }


  /**  
   * Sets the URL that will be loaded when the user clic que cancel button.
   *
   *  @param string $url
   */

  function setCancelUrl($url) {
    $this->__settings['urlOnCancel'] = $url;
  }

  /** Configura o tamanho do espaÁamento entre as cÈlulas da tabela do smartform
   *
   * @param integer $spc Par‚metro a ser passados para a tabela.
   */
  function setSpacing($spc) {
    $this->__settings['spacing'] = $spc;
  }

  /**
   *Esta funcao serve pra setar uma chamada de funcao js
   *para validacao do form.
   *Para incluir um novo js usar a funcao CMHTMLObj::requires(js_file);.
   **/
  public function addOnSubmitAction($action="") {
    $this->__settings['onSubmitActions'][] = "($action == true)";
  }



  /**
   * Chamada recursiva que imprime o resultado da pagina no objeto de mais alto nÅ√ÉÅ¬Å≠vel
   * @param int $callWFormPrint : Se igual a 1 entao chama o metodo imprime do wform senao chama do rdpagobj direto.
      Isto serve para quando for adicionar um subformulario ele nao colocar as tags do <FORM> e </FORM> novamente.
   */
  public function __toString($callWFormPrint=1) {
    global $_CMAPP, $_language;
    
    if (empty($this->htmlFormat)) {
      //seta a 
      $this->setDefaultHtmlFormat();
      
    }
    
    //pega a formatacao
    $hf = $this->getHtmlFormat();
    
    $this->add("<!- Inicio do SmartForm >");

    $this->add("<!- Inicio dos campos hidden>");
    foreach ($this->components as $k=>$form_el) {
      if(get_class($form_el)=="CMWHidden") {
	$this->add($form_el);
      };
    };
    $this->add("<!- Fim dos campos hidden>");
    
    //$this->add("<TABLE CELLPADDING=\"$this->__settings[spacing]\" CELLSPACING=\"$this->spacing\"><TR>");
    $this->add($hf->getIniTabelaTag() . $hf->getIniLinhaTag());
    
    $row = 0;    //contador das linhas
    $col = 1;    //contador das colunas

    if(empty($this->submit_label)) 
      $this->submit_label = $_language['send'];
    
    $group = $this->components['submit_group'];

    $group->setName("submit_group");
    
    unset($this->components['submit_group']);


    if (!$this->__settings['submitButtonOff']) {
      $group->add(array("submit",$this->submit_button));
      if(!empty($this->submit_label)) 
	$this->submit_button->setValue($this->submit_label);
    }
            
    if(!$this->__settings['cancelButtonOff'])  {
      //se nao tiver sido setada a url que devera ir caso cancelar
      //entao nao faz nada

      if (!empty($this->__settings['urlOnCancel'])) {
	$this->cancel_button->setOnClick("window.location.href = '".$this->__settings['urlOnCancel']."'");
      }
      $group->add(array("cancel",$this->cancel_button));
      
    }    
    
    $this->components['submit_group'] = $group;
    
    $this->add("<".$hf->getIniColuna() . " class=\"".$this->__settings['labelClass']."\">");
	
    $taborder=0;
    foreach ($this->components as $form_el) {
      
      if(get_class($form_el)=="CMWHidden") {
	continue;
      }

      if(get_class($form_el)=="CMWFile") {
	$this->enctype = "multipart/form-data";
      }

      
      if($this->__settings['design']!=CMWFormEl::WFORMEL_DESIGN_STRING_DEFINED) {
	if(!isset($this->rows[$row])) $this->rows[$row] = 0;
	if ($col > $this->rows[$row]) {
	  $col = 1;
	  $row++;
	  //$this->add("</TD></TR><TR>");
	  $this->add($hf->getFimColunaTag() . $hf->getFimLinhaTag() . $hf->getIniLinhaTag());
	}
	
	if ($col!= 1) {
	  //$this->add("</TD>");
	  $this->add($hf->getFimColunaTag());
	}
	
	
	//$this->add("<TD class=\"$this->labelclass\">");
	$this->add("<".$hf->getIniColuna() . " class=\"".$this->__settings['labelClass']."\">");
	
	$form_el->design = $this->__settings['design'];

	//We forces the $form_el to be converted to an string here because some
        //configuration problems with the formel. For an exemple, the WDate element
	//add some submitActions to the form. But it can only do this in render time(__toString)
	//so, when CMHTMLPage call it, the form has already been rendered and it configuration
	//will take no effect.
	$this->add($form_el->__toString());
	
	if(!empty($form_el->tip)) {
	  $this->add("&nbsp;");
	  $this->add($form_el->tip);
	}

		
      } else {
	
	$form_el->design = $this->__settings['design'];

	if($this->__settings['designStringIterative']) {
	  $el= @ereg_replace("{LABEL}",$form_el->label,$this->__settings['designString']);
	  $el= @ereg_replace("{FORM_EL}",$form_el->__toString(),$el);
	  if(!empty($form_el->tip)) {
	    $el= @ereg_replace("{TIP}",$form_el->tip->__toString(),$el);
	  }

	}
	else {
	  
	  $name = strtoupper($form_el->name);
	  $this->__settings['designString'] = @ereg_replace("{LABEL_$name}",$form_el->label,$this->__settings['designString']);
	  $this->__settings['designString'] = ereg_replace("{FORM_EL_$name}",
							  $form_el->__toString(),$this->__settings['designString']);
	  if(!empty($form_el->tip)) {
	    $this->__settings['designString'] = @ereg_replace("{TIP_$name}",
							     $form_el->tip->__toString(),$this->__settings['designString']);
	  }


	}
	
	parent::add($el);
      }
      
      $col++;
    }


    if($this->__settings['design']!=CMWFORMEL::WFORMEL_DESIGN_STRING_DEFINED) {
      //$this->add("</TD></TR>");
      $this->add($hf->getFimColunaTag() . $hf->getFimLinhaTag());
      
    } else {
      if($this->__settings['designStringSterative']=="") 
	parent::add($this->__settings['designString']);
    }
    
    //$this->add("</TABLE>");
    $this->add($hf->getFimTabelaTag());
    
    $this->add("<!- Fim do SmartForm >");

            
    //faz um parse dos campos que sao marcados como not null
    //e inscreve eles na funcao javascript que so vai permitir o envio
    // de forms completos


    if(!empty($this->required_forms)) {
      $str = "";
      $str2 = "";
      foreach($this->required_forms as $field) {
	$w = $this->components[$field];
	if(!empty($w)) {
	  if(!empty($str)) $str.=",";
	  if(!empty($str2)) $str2.=",";
	  $str.="'$w->name'";
	  $str2.="'$w->label'";
	}
      }
      
      if(!empty($str)) {
	$this->__settings['onSubmitActions'][] = "(formCheck(this,Array($str),Array($str2)) == true)";
      }
      
      
    }

    if(!empty($this->__settings['onSubmitActions'])) {
      $this->__settings['onSubmit'] = "if( ";
      $this->__settings['onSubmit'] .= implode(" && ",$this->__settings['onSubmitActions']);
      $this->__settings['onSubmit'] .= " ) {return true;} else {return false;}";

    }
    
    if ($callWFormPrint) {
      return parent::__toString();
     }else
      return CMHTMLObj::__toString();
    
  }



}

?>