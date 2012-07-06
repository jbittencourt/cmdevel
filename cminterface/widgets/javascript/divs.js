function toggle(elemento,imClose,imOpen) {
 

  if (document.all)
  {  if (document.all(elemento).style.display=="") 
     {   document.all(elemento).style.display="none";
	eval(elemento+'_open = 0'); 
     }
     else 
     {  document.all(elemento).style.display="";  
	eval(elemento+'_open = 1');
     }
  }
  else 
  {  if (document.getElementById) 
     {  if (document.getElementById(elemento).style.display=="") 
        {  document.getElementById(elemento).style.display="none"; 
	   eval(elemento+'_open = 0');
        }
        else 
        {  document.getElementById(elemento).style.display="";
	   eval(elemento+'_open = 1');
        }
     }
     else
       if (document.layers)
       {  Which = eval('document.layers["'+elemento+'"]');  
           	     
          if (Which.visibility=="hide") 
          {  Which.visibility ="show";
	     eval(elemento+'_open = 1');
          }          
          else 
          {  Which.visibility="hide"; 
             eval(elemento+'_open = 0');
          };
         
//          arrange();
//          history.go(0);
	     
       };
   }   
 



}

//seta o elemento para invisivel
function set_invisible(elemento) 
{ 
    if (document.all)
	{  
	    document.all(elemento).style.display="none";        
	}
    else 
	{ 
	    if (document.getElementById) 
		{   
		    document.getElementById(elemento).style.display="none"; 
		}
	
	    else {
		if (document.layers)
		    {
			Which = eval('document.layers["'+elemento+'"]');             	     
			Which.visibility="hide"; 
		    };         	    
	    };
	}   
}


//seta o elemento para visivel
function set_visible(elemento) 
{ 
    if (document.all)
	{  
	    document.all(elemento).style.display="";        
	}
    else 
	{ 
	    if (document.getElementById) 
		{   
		    document.getElementById(elemento).style.display=""; 
		}
	
	    else {
		if (document.layers)
		    {
			Which = eval('document.layers["'+elemento+'"]');             	     
			Which.visibility="show"; 
		    };
         
	    };
	}   
}


