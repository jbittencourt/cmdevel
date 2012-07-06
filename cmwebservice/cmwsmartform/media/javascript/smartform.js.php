/*
Required field(s) validation- By NavSurf
Visit NavSurf.com at http://navsurf.com
Visit http://www.dynamicdrive.com for this script
*/

function formCheck(formobj,fieldRequired,fieldDescription){
	var alertMsg = <? echo "\"$_language[required]\\n\""; ?>;
	

	var l_Msg = alertMsg.length;
	
	for (var i = 0; i < fieldRequired.length; i++){
		var obj = formobj.elements[fieldRequired[i]];
		if (obj){
			switch(obj.type){
			case "select-one":
				if (obj.selectedIndex == -1 || obj.options[obj.selectedIndex].text == ""){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			case "select-multiple":
				if (obj.selectedIndex == -1){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			case "text":
			case "textarea":
				if (obj.value == "" || obj.value == null){
					alertMsg += " - " + fieldDescription[i] + "\n";
				}
				break;
			}
		}
	}

	if (alertMsg.length == l_Msg){
	  return true;
	}else{
		alert(alertMsg);
		return false;
	}
}




