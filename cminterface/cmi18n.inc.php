<?php
/**
 * Class that implements an easy way of intertionalize yor application. 
 *
 * When you create an CMi18n instance, it loads the configuration file of
 * your application and parses the app->languagues section, searching for
 * the languages that are configured. Then it loads the language file associated
 * with the app->languages->default parameter. After, the initial load, it part
 * of the application must read its own language file. To put all the application
 * messages in a single file will work, but may cause serious performace problems
 * depending on the size of your file.
 */
class CMi18n {

  protected $languages;
  protected $lang;

  protected $mandatory_sections = array("localization","global","errors");

  public function __construct() {
    global $_CMAPP;

    $_conf = $_CMAPP['config'];
    
    if(empty($_conf->app->languages)) {
      Throw new CMIENoLanguageConfigFound;
    }

    foreach($_conf->app->languages->language as $item) {
      $this->languages[(string) $item['prefix']] = array();
      $this->languages[(string) $item['prefix']]['name'] = (string) $item['name'];
      $this->languages[(string) $item['prefix']]['file'] =  (string) $item['file'];
    }

    $this->lang = (string) $_conf->app->languages->default;
    

    $this->language = @parse_ini_file($_CMAPP['path']."/lang/" .$this->languages[$this->lang]['file'],TRUE);
    if(empty($this->language)) {
      Throw new CMIELanguageFileFound;
    }
  }


  /**
   * Return the actual language that has been configured.
   **/
  public function getActualLang() {
    return $this->lang;
  }
  
  public function addFile($path) {
    $temp = parse_ini_file($path."/".$this->languages[$this->lang][file],TRUE);
    if(empty($temp)) {
      Throw new CMIELanguageFileFound;
    }
    $this->language = array_merge($this->language,$temp);
  }



  /**
   * Return an array with only the section localization, smartform, global, plus
   * the section passed by parameter.
   * 
   **/
  public function getTranslationArray() {
    global $_CMAPP;
    $ret = array();

    $n_args = func_num_args();
    $args = func_get_args();    
    if($n_args==0) {
      Throw new CMObjException("CMQuery->__construct must contain at least one class name as parameter");
    }


    //tests if the section exists
    $temp = array();
    foreach($args as $section) {
      $temp = array_merge($this->language[$section],$temp);
      if(empty($temp)) {
	Throw new CMIELanguageSectionNotDefined;
      }
    }

    
    $ret = array_merge($ret,$temp);
    //add the mandatory sections to the return array
    foreach($this->mandatory_sections as $sec) {
      $ret = array_merge($ret,$this->language[$sec]);
    } 

    //set this language array to be used with smartform
    $_CMAPP['smartform']['language'] = $ret;

    return $ret;
  }

}



?>
