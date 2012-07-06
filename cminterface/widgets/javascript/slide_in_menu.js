/***********************************************
 * Sliding Menu Bar Script - Dynamic Drive (www.dynamicdrive.com)
 * Visit http://www.dynamicdrive.com/ for full source code
 * This notice must stay intact for use
 ***********************************************/
var ns4=document.layers?1:0
var ie4=document.all&&navigator.userAgent.indexOf("Opera")==-1
var ns6=document.getElementById&&!document.all?1:0

pull_timeout = 0;
draw_timeout = 0;
can_draw = 0;

function slide_in_init(w,top,renew){
  slidemenu_width = w;
  slidemenu_reveal= renew;
  slidemenu_top= top;
  
  themenu=(ns6)? document.getElementById("slidemenubar").style : document.all.slidemenubar.style;
  
  rightboundary=0;
  leftboundary=(slidemenu_width-slidemenu_reveal)*-1;
}


function pull(){
  clearTimeout(draw_timeout);
  pull_timeout = setTimeout("wait_pull()",300);
  can_draw_timeout = setTimeout("set_can_draw()",400);
}

function set_can_draw() {
  can_draw =1;
}

function wait_pull() {
  if(lock_pull_menu) return 0;

  if (window.drawit)
    clearInterval(drawit);

  pullit=setInterval("pullengine()",10);
}

function draw(){
  clearTimeout(pull_timeout);
  draw_timeout = setTimeout("wait_draw()",300);
}

function wait_draw() {
  if(lock_pull_menu || (!can_draw)) return 0;
  can_draw = 0;
  if(window.pullit)
    clearInterval(pullit);
  drawit=setInterval("drawengine()",10);
}

function pullengine(){
  if ((ie4||ns6)&&parseInt(themenu.left)<rightboundary)
    themenu.left=parseInt(themenu.left)+10;
  else if (window.pullit){
    themenu.left=0;
    clearInterval(pullit);
  };
}

function drawengine(){
  if ((ie4||ns6)&&parseInt(themenu.left)>leftboundary)
    themenu.left=parseInt(themenu.left)-10;
  else if (window.drawit){
    themenu.left=leftboundary;
    clearInterval(drawit);
  };
}