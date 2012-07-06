<?php

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
 * @see  CMWFormEl, CMWSelect
 */

class CMWSelectGroup extends CMWFormEl
{
    var $options,$slist,$dlist,$groupingList,$groupingField,$groupin;

    public function __construct($name,$selectName,$urlaction,$list=array(),$fieldValue="",$fieldLabel="")
    {
        global $smartform;
        parent::__construct();
        $this->list = $list;
        $this->fieldValue = $fieldValue;
        $this->fieldLabel= $fieldLabel;
        $this->selectName = $selectName;
        $this->urlaction = $urlaction;
        $this->setName($name);
        $this->name = $name;

        $this->requires("groupselect.js");

        $this->iframe_display = 0;

    }

    function getChangeGroupScript($valueList,$value,$label)
    {
        $str = "parent.selectClean('".$this->selectName."');\n";

        $atual = array();
        if($valueList instanceof RDLista) {
            $atual = &$valueList->records;
        }
        else {
            $atual = &$valueList;
        }


        if(!empty($atual)) {
            foreach($atual as $item) {
                if($item instanceof RDObj) $item = $item->toArray();
                $str.="parent.selectNewElement('".$this->selectName."','".$item[$value]."','".escapeshellcmd($item[$label])."');\n";
            }

            $str.="parent.selectItem('".$this->selectName."','".$item[$value]."');";
        }
        echo nl2br($str);
        return $str;

    }

    function showIframe($show=1)
    {
        $this->iframe_display = $show;
    }

    function __toString()
    {
        global $smartform,$host;



        $iframe_name = "wselect_group_comands";

        $js = "selectChangeGroup(this.value,'$iframe_name','$this->urlaction','$this->fieldValue');";

        $groupList = new WSelect($this->name);
        $groupList->parseOptions($this->list,$this->fieldValue,$this->fieldLabel);

        $groupList->prop['onChange']= $js;
        $groupList->setValue($this->value);


        if(!$smartform['wselectgroup']['iframe']) {
            if(!$this->iframe_display)
            $style = "style=\"display: none\"";

            parent::add("<iframe  name=\"$iframe_name\" id=\"$iframe_name\" $style src=\"\"></iframe>");
            $smartform['wselectgroup']['iframe'] = 1;
        }

        parent::add($groupList);

        return parent::__toString();
    }

}


