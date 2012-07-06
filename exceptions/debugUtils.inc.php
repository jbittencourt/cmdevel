<?php

function note($var,$print=true) {
	ob_start();
	echo "<pre>Men:\n"; nl2br(print_r($var)); echo "</pre>";
	$data = ob_get_contents();
	ob_end_clean();
	if($print) {
		echo $data;
		return "";
	}
	return $data;
}

function noteAtrib($cursor,$atribs) {
	if (!is_array($atribs))
	$atribs = array($atribs);
	foreach($cursor->records as $obj) {
		echo "<pre>Men: \n";
		foreach($atribs as $atr)
		echo $atr." : ".$obj->$atr."\n";
	}
	echo "</pre>";
}

function noteLastquery($print=true) {
	global $_CMDEVEL;
	return note($_CMDEVEL['last_querys'],$print);
}
