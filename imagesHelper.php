<?php

function getThumbUrl($url){

	$type=substr(strrchr($url,"."),0);
			
	return str_ireplace($type,'_thumb'.$type,$url);

}

function getMidsizeUrl($url){

	$type=substr(strrchr($url,"."),0);
			
	return str_ireplace($type,'_midsize'.$type,$url);

}


?>