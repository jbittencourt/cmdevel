/**
 * Basic Javascript function that are loaded with an CMHTMLPage
 *
 * @package cmdevel
 * @subpackage cminterface
 **/



/**
 * The code below was baseated in the one found at http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
 **/

// convert all characters to lowercase to simplify testing
var agt=navigator.userAgent.toLowerCase();

var is_gecko = (agt.indexOf('gecko') != -1);
var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
var is_opera = (agt.indexOf("opera") != -1);
var is_dom = (document.getElementById);
//--> end hide JavaScript




function getBlockElement(name) {
  if (document.getElementById) {
    return document.getElementById(name);
  }
  else {
    if(document.all) {
      return document.all[name];
    }
  }
}