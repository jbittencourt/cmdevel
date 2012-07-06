//scripts para movimentacao de itens em selects em forma de listas
// This script and many more are available free online at ";
// The JavaScript Source!! http://javascript.internet.com ";
sortitems = 1;  // Automatically sort items within lists? (1 or 0)
	 
function move(fbox,tbox) {
  var ln=0;
  for(var i=0; i<fbox.options.length; i++) {
    if(fbox.options[i].selected && fbox.options[i].value != "") {
      var no = new Option();
      no.value = fbox.options[i].value;
      no.text = fbox.options[i].text;
      tbox.options[tbox.options.length] = no;
      fbox.options[i] = null;
      ln++;
     }
  }

  if(ln < box.options.length)  {
    box.options.length -= ln;
  }	

  if (sortitems) SortD(tbox);
}
    
        
function SortD(box)  {
  var temp_opts = new Array();
  var temp = new Object();


  if(box.options.length>100) {
    return 0;
  }	

  for(var i=0; i<box.options.length; i++)  {
    temp_opts[i] = box.options[i];
  }
  for(var x=0; x<temp_opts.length-1; x++)  {
    for(var y=(x+1); y<temp_opts.length; y++)  {
      if(temp_opts[x].text > temp_opts[y].text)  {
        temp = temp_opts[x].text;
        temp_opts[x].text = temp_opts[y].text;
        temp_opts[y].text = temp;
        temp = temp_opts[x].value;
        temp_opts[x].value = temp_opts[y].value;
        temp_opts[y].value = temp;
      }
    }
  }
  for(var i=0; i<box.options.length; i++)  {
    box.options[i].value = temp_opts[i].value;
    box.options[i].text = temp_opts[i].text;
  }
}    

function addListSend(box) {
    var cont;
    cont = box.options.length;
    for (var i=0;i<=cont-1;i++){
        box.options[i].selected = 1; 
    }
}