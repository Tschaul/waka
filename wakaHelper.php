<?php


function readWaka($wakakey){

	$waka = file_get_contents(dirname(__FILE__).'/data/'.$wakakey.'/data.json');
	
	//echo dirname(__FILE__).'/data/'.$wakakey.'/data.json';
	//echo $waka;

	$waka = json_decode($waka,true);
	
	
	//patch waka
	
	if(!isset($waka['version']) || $waka['version']<3){
	
		if(isset($waka['starter']['dateTouched'])){
	
			$waka['starter']['dateContentTouched']=$waka['starter']['dateTouched'];
			$waka['starter']['dateImagesTouched']=$waka['starter']['dateTouched'];
			$waka['starter']['dateFilesTouched']=$waka['starter']['dateTouched'];
			unset($waka['starter']['dateTouched']);
	
		}
	
		foreach($waka['drafts'] as $u => $draft){
		
			if(isset($draft['dateTouched'])){
		
				$waka['drafts'][$u]['dateContentTouched']=$waka['drafts'][$u]['dateTouched'];
				$waka['drafts'][$u]['dateImagesTouched']=$waka['drafts'][$u]['dateTouched'];
				$waka['drafts'][$u]['dateFilesTouched']=$waka['drafts'][$u]['dateTouched'];
				unset($waka['drafts'][$u]['dateTouched']);

			}
				
		}
		
		for($p=0; $p<count($waka['posts']); $p++){
		
			if(isset($waka['posts'][$p]['dateTouched'])){
			
				$waka['posts'][$p]['dateContentTouched']=$waka['posts'][$p]['dateTouched'];
				$waka['posts'][$p]['dateImagesTouched']=$waka['posts'][$p]['dateTouched'];
				$waka['posts'][$p]['dateFilesTouched']=$waka['posts'][$p]['dateTouched'];
				$waka['posts'][$p]['dateCommentsTouched']=$waka['posts'][$p]['dateTouched'];
				unset($waka['posts'][$p]['dateTouched']);

			}
				
		}
		
		$waka['version']=3;
	
	}
	
	if($waka['version']<4){
	
		for($p=0; $p<count($waka['posts']); $p++){
			
			$olduser=$waka['posts'][$p]['user'];
		
			unset($waka['posts'][$p]['user']);
			
			//$waka['posts'][$p]['users']=array();
		
			$waka['posts'][$p]['users'][$olduser]['mayEdit']=1;
			$waka['posts'][$p]['users'][$olduser]['dateRead']=date("Y-m-d H:i:s");
			$waka['posts'][$p]['users'][$olduser]['dateTouched']=date("Y-m-d H:i:s");
			
			
			$waka['posts'][$p]['dateUsersTouched']=date("Y-m-d H:i:s");
		
		}
		
		$waka['version']=4;

	}


	return $waka;


}

function userLastEdit($users){

	$firstallready=false;
	$lastemail='';
	$lastdate='';
	
	foreach($users as $email => $values){
	
		if(!$firstallready){
			$lastemail=$email;
			$lastdate=$values['dateTouched'];
			$firstallready=true;
		}
		else{
		
			if(strnatcmp($values['dateTouched'],$lastdate)){
				$lastemail=$email;
				$lastdate=$values['dateTouched'];
			}
		
		}
	
	}
	
	return $lastemail;

}

function userMayEdit($email,$users){

	//echo $email.' ';
	//echo $users[$email]['mayEdit'].'<br>';

	if(isset($users[$email]) && $users[$email]['mayEdit']==1) return true;
	else return false;
	
}

function wakaExists($wakakey){

	return file_exists(dirname(__FILE__).'/data/'.$wakakey.'/data.json');


}

function writeWaka($waka,$wakakey){

	$myFile = dirname(__FILE__).'/data/'.$wakakey.'/data.json';
	$fh = fopen($myFile, 'w');

	//fwrite($fh,"{");


	fwrite($fh,json_encode($waka));

	//fwrite($fh,"}");

	fclose($fh);


}


function makeSortFunctionAsc($field) { 

	$code = "return strnatcmp(\$a['$field'], \$b['$field']);"; 

	return create_function('$a,$b', $code); 
	
} 

function makeSortFunctionDesc($field) { 

	$code = "return -1*strnatcmp(\$a['$field'], \$b['$field']);"; 

	return create_function('$a,$b', $code); 
	
}

function sortItemsAsc($items,$fieldname){
	
	//define("SORTFIELD",$fieldname);

	if(count($items)>0){

		usort($items,makeSortFunctionAsc($fieldname));

		return $items;

	}else{

		return null;

	}

}

function sortItemsDesc($items,$fieldname){
	
	//define("SORTFIELD",$fieldname);

	if(count($items)>0){

		usort($items,makeSortFunctionDesc($fieldname));

		return $items;

	}else{

		return null;

	}

}



function convert_datetime($str) {

	list($date, $time) = explode(' ', $str);
	list($year, $month, $day) = explode('-', $date);
	list($hour, $minute, $second) = explode(':', $time);

	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	return $timestamp;
}

function listHasEntry($list,$entryname){

	if(strstr($list,$entryname.';')!=false) return true;
	else return false;

}

function listToArray($list){

	$a=explode(';',$list);
	
	$b=array();

	$j = 0; 
		for($i = 0; $i < count($a); $i++){ 

			if($a[$i] != ""){ 
				
				$b[$j++] = $a[$i]; 

			} 
		} 

	return $b; 

}

function arrayToList($array){

	$list='';

	for($i=0; $i<count($array); $i++){

		$list=$list.$array[$i].';';

	}

	return $list;

}

function parseBBCode2HTML( $bb )
{
    $bb = preg_replace('/\[b\](.*?)\[\/b\]/', '<b>$1</b>', $bb);
    $bb = preg_replace('/\[i\](.*?)\[\/i\]/', '<i>$1</i>', $bb);
    $bb = preg_replace('/\[color=([[:alnum:]]{6}?).*\](.*?)\[\/color\]/', '<font color="#$1">$2</font>', $bb);
    $bb = preg_replace('/\[url\](.*?)\[\/url\]/', '<a href="$1">$1</a>', $bb);
    //$bb = preg_replace('/\[url=([^ ]+).*\](.*)\[\/url\]/', '<a href="$1">$2</a>', $bb);

    $bb = preg_replace('/\n/', "<br/>\n", $bb);

    return $bb;

}


function parseLatexBBCode($content){

	return preg_replace_callback(
        '/\[tex\](.*?)\[\/tex\]/', 
        create_function(
            '$hit',
            '$str=substr($hit[0],5,-6);
             $str = \'<img src="http://latex.codecogs.com/png.latex?\'.$str.\'">\';
             
             return $str;'
        ),
        $content
    );


}

function renderedContent($item){

	$renderedContent=stripslashes($item['content']);

	$renderedContent=parseLatexBBCode($renderedContent);
    
    $icount=count($item['images']);

    for($i=0; $i<$icount; $i++){

        //print_r($item['sections'][$s]['images'][$i]);

        $html='<img src="'.getMidsizeUrl($item['images'][$i]['url']).'">';

        $code='[image_'.$item['images'][$i]['id'].']';

        $renderedContent=str_replace($code,$html,$renderedContent);

    }
	
	$renderedContent = parseBBCode2HTML($renderedContent); 

	return $renderedContent;

}

function generateRandomPwd(){

	$length = 10;
	    
	// start with a blank password
	$password = "";
	
	// define possible characters
	$possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
		
	// set up a counter
	$i = 0; 
		
	// add random characters to $password until $length is reached
	while ($i < $length) { 
		
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			   
		// we don't want this character if it's already in the password
		
		$password .= $char;
		$i++;
		
		
	}

	return $password;
	
}


/*

function displayDatetime( $str ){

	$r='';

	$t=convert_datetime($str);

	$post = getdate(mktime(date("H",$t), date("i",$t), date("s",$t), date("m",$t),date("d",$t),date("Y",$t))); 
    $now  = getdate(mktime(date("H"), date("i"), date("s"), date("m"),date("d"),date("Y"))); 
     
	$result['seconds'] = $now['seconds'] - $post['seconds']; 
    $result['minutes'] = $now['minutes'] - $post['minutes']; 
    $result['hours'] = $now['hours'] - $post['hours']; 
    $result['mday'] = $now['mday'] - $post['mday']; 
    //$result['wday'] = $now['wday'] - $post['wday'];  
    //$result['mon'] = $now['mon'] - $post['mon']; 
    //$result['year'] = $now['year']  - $post['year']; 
    //$result['yday'] = $now['yday'] - $post['yday']; 
    //$result['weekday'] = $now['weekday'] - $post['weekday'];   
    //$result['month'] = $now['month'] - $post['month']; 
    
    if($result['mday']==3) $r.='3 days ago at '.$post['']

}

*/

?>