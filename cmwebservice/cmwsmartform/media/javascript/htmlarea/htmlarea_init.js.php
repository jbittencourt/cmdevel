<?

$url = $_CMAPP['media_url'];
$path = "cmwebservice/cmwsmartform/media/javascript/htmlarea";

echo "_editor_url = '$url/mediawrapper.php?type=js&frm_file=$path/';";
echo "_editor_url_js = '$url/mediawrapper.php?type=js&frm_file=$path/';";
echo "_editor_url_plugins = ''; ";
echo "_editor_url_images = '$_CMAPP[images_url]/htmlarea/';";
echo "_editor_url_css = '$url/mediawrapper.php?type=js&frm_file=$path/';";
echo "_editor_url_popups = '$url/mediawrapper.php?type=js&frm_file=$path/';";
?>
function initDocument() {

  var editor = new HTMLArea("<?=$_SESSION['smartform']['cmwhtmlarea']['name']?>");
  var cfg = editor.config; // this is the default configuration;
  
  /**
   *Executando funcoes de inicializacao customizadas 
   */
  var initActions = AM_getRegisteredEditorInitActions(); 
  if(initActions.length > 0) {
    for(var i in initActions) {
      eval(initActions[i]+"();");
    }
  }

  /**
   *Registrando customizacoes dos botoes
   */
  var buttons = AM_getRegisteredEditorButtons();
  if(buttons.length >0) {
    for(var i=0; i<buttons.length;i++) {
      
      var button = buttons[i];
      
      if(cfg.btnList[button.regInfo.id] == undefined) {
	cfg.btnList[button.name] = button.properties;
	cfg.toolbar.push([button.separator, button.name]);
	
	cfg.registerButton(button.regInfo);
      }else {
	cfg.btnList[button.name] = button.properties;
	cfg.registerButton(button.regInfo);
      }
    }
  }
  
  editor.generate();


};


