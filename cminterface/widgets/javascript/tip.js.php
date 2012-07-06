2/* IMPORTANT: Put script after tooltip div or 
 put tooltip div just before </BODY>. */
var dom = (document.getElementById) ? true : false;
var ns5 = ((navigator.userAgent.indexOf("Gecko")>-1) && dom) ? true: false;
var ie5 = ((navigator.userAgent.indexOf("MSIE")>-1) && dom) ? true : false;
var ns4 = (document.layers && !dom) ? true : false;
var ie4 = (document.all && !dom) ? true : false;
var nodyn = (!ns5 && !ns4 && !ie4 && !ie5) ? true : false;

//if you want to generate the message tha will fill the box
//only in runtime, to obtain low bandwidth in long lists, you
//can set this variable to true and passa a function name in the
//array messages.

// resize fix for ns4
var origWidth, origHeight;
if (ns4) {
  origWidth = window.innerWidth; origHeight = window.innerHeight;
  window.onresize = function() { if (window.innerWidth != origWidth || window.innerHeight != origHeight) history.go(0); }
}

// avoid error of passing event object in older browsers
if (nodyn) { event = "nope" }

///////////////////////  CUSTOMIZE HERE   ////////////////////
// settings for tooltip 
// Do you want tip to move when mouse moves over link?
var tipFollowMouse= true;
// Be sure to set tipWidth wide enough for widest image
var tipWidth= 160;
var offX= 2;// how far from mouse to show tip
var offY= 2; 
var tipFontFamily= "Verdana, arial, helvetica, sans-serif";
var tipFontSize= "12pt";
// set default text color and background color for tooltip here
// individual tooltips can have their own (set in messages arrays)
// but don't have to
var tipFontColor= "#000000";
var tipBgColor= "#DDECFF"; 
var tipBorderColor= "#000080";
var tipBorderWidth= 3;
var tipBorderStyle= "ridge";
var tipPadding= 4;
var tipStayUntilClick= false;

// tooltip content goes here (image, description, optional bgColor, optional textcolor)
// multi-dimensional arrays containing: 
// image and text for tooltip
// optional: bgColor and color to be sent to tooltip
var messages = new Array();

////////////////////  END OF CUSTOMIZATION AREA  ///////////////////


// to layout image and text, 2-row table, image centered in top cell
// these go in var tip in doTooltip function
// startStr goes before image, midStr goes between image and text
var startStr = '<table width="' + tipWidth + '"><tr><td align="center" width="100%"><img src="';
var midStr = '" border="0"></td></tr><tr><td valign="top">';
var endStr = '</td></tr></table>';

////////////////////////////////////////////////////////////
//  initTip- initialization for tooltip.
//Global variables for tooltip. 
//Set styles for all but ns4. 
//Set up mousemove capture if tipFollowMouse set true.
////////////////////////////////////////////////////////////
var tooltip, tipcss;
var mouseX, mouseY;
var tipOutOfPosition=true;

function initTip() {
  if (nodyn) return;
  tooltip =  (is_ie)? document.all('tipDiv'): document.getElementById("tipDiv");

  tipcss =  tooltip.style;

  if(!tipCssClass) {
    tipcss.width = tipWidth+"px";
    tipcss.fontFamily = tipFontFamily;
    tipcss.fontSize = tipFontSize;
    tipcss.color = tipFontColor;
    tipcss.backgroundColor = tipBgColor;
    tipcss.borderColor = tipBorderColor;
    tipcss.borderWidth = tipBorderWidth+"px";
    tipcss.padding = tipPadding+"px";
    tipcss.borderStyle = tipBorderStyle;
  }

  if(tipStayUntilClick) {
    tipFollowMouse = false;
    document.onmouseup = tipHideNow;
  }

  if (tooltip&&tipFollowMouse) {
    document.onmousemove = trackMouse;
  }

}


/////////////////////////////////////////////////
//  doTooltip function
//Assembles content for tooltip and writes 
//it to tipDiv
/////////////////////////////////////////////////
var t1,t2;// for setTimeouts
var tipOn = false;// check if over tooltip link
function doTooltip(evt,im,des,colorBg,colorFg,wrapper) {
  if (!tooltip) return;
  if (t1) clearTimeout(t1);if (t2) clearTimeout(t2);
  tipOn = true;
  // set colors if included in messages array
  if (colorBg)var curBgColor = colorBg;
  else curBgColor = tipBgColor;
  if (colorFg)var curFontColor = colorFg;
  else curFontColor = tipFontColor;
  if(im)var img = startStr + im + midStr;
  else img = '';

  var men;
  if(wrapper==true) {
    mem = eval(des);
  }
  else {
    mem = des;
  }

  if (ns4) {
    var tip = '<table bgcolor="' + tipBorderColor + '" width="' + tipWidth + '" cellspacing="0" cellpadding="' + tipBorderWidth + '" border="0"><tr><td><table bgcolor="' + curBgColor + '" width="100%" cellspacing="0" cellpadding="' + tipPadding + '" border="0"><tr><td>'+ img + '<span style="font-family:' + tipFontFamily + '; font-size:' + tipFontSize + '; color:' + curFontColor + ';">' + mem + '</span>' + endStr + '</td></tr></table></td></tr></table>';
    tooltip.write(tip);
    tooltip.close();
  } else if (ie4||ie5||ns5) {
    var tip = '<span id="toolTipSpan"style="font-family:' + tipFontFamily + '; font-size:' + tipFontSize + '; color:' + curFontColor + ';">' + mem + '</span>';
    if(!tipCssClass)
      tipcss.backgroundColor = curBgColor;
    tooltip.innerHTML = tip;
  }


 t1=setTimeout("tipcss.visibility='visible'",100);
 positionTip(evt);
}

function trackMouse(evt) {
   mouseX = (ns4||ns5)? evt.pageX: window.event.clientX ;
   mouseY = (ns4||ns5)? evt.pageY: window.event.clientY ;
   if (tipOn) positionTip(evt);
}



/////////////////////////////////////////////////////////////
//  positionTip function
//If tipFollowMouse set false, so trackMouse function
//not being used, get position of mouseover event.
//Calculations use mouseover event position, 
//offset amounts and tooltip width to position
//tooltip within window.
/////////////////////////////////////////////////////////////


function positionTip(evt) {
  if (!tipFollowMouse) {
    mouseX = (ns4||ns5)? evt.pageX: window.event.clientX + document.body.scrollLeft;
    mouseY = (ns4||ns5)? evt.pageY: window.event.clientY + document.body.scrollTop;
  }
  
  // tooltip width and height
  var tpWd =  (ie4||ie5)? tooltip.clientWidth: tooltip.offsetWidth;
  var tpHt =  (ie4||ie5)? tooltip.clientHeight: tooltip.offsetHeight;
  // document area in view (subtract scrollbar width for ns)
  var winWd = (ns4||ns5)? window.innerWidth-20+window.pageXOffset: document.body.clientWidth+document.body.scrollLeft;
  var winHt = (ns4||ns5)? window.innerHeight-20+window.pageYOffset: document.body.clientHeight+document.body.scrollTop;

  // check mouse position against tip and window dimensions
  // and position the tooltip 
  //  if ((mouseX+offX+tpWd)>winWd) 
  //tipcss.left = mouseX-(tpWd+offX)+"px";
  //else
  tipcss.left = mouseX+offX+"px";

  //if ((mouseY+offY+tpHt)>winHt) 
  //tipcss.top =  winHt-(tpHt+offY)+"px";
  //else 
  tipcss.top = mouseY+offY+"px";

  if (!tipFollowMouse) t1=setTimeout("tipcss.visibility='visible'",100);
}

function hideTip($timeout) {
  if (!tooltip) return;
  t2=setTimeout("tipcss.visibility='hidden'",$timeout);
  tipOn = false;
}

function tipHideNow(evt) {
  hideTip(10)
}


function cancelHideTip() {
  clearTimeout(t2);
}

