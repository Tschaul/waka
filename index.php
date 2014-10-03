

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=856" />
  <head>
	<script type="text/javascript" src="jquery-1.4.min.js"></script>
	<script type="text/javascript" src="jquery.form.js"></script>
	<script type="text/javascript" src="helper.js"></script>
	<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="./fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="shortcut.js"></script>
	<script type="text/javascript" src="date.format.js"></script>
	
	<title>Waka !!!</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
	
	<?php
	
		
		if(isset($_REQUEST['v']) && $_REQUEST['v']=='print') echo '<link rel="stylesheet" type="text/css" href="print.css">';
		else echo '<link rel="stylesheet" type="text/css" media="print" href="style.css"><link rel="stylesheet" type="text/css" media="screen" href="style.css">';
	
	?>
	
	
	<link rel="stylesheet" type="text/css" href="./fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	

</head>



<div class="all">

<img src="img/wakalogo.png" stlye="float:left;">

<?php
		if(isset($_REQUEST['v']) && $_REQUEST['v']=='print' && isset($_REQUEST['w'])) echo '';
		else echo '<div style="float:right;"><a href="?w='.$_REQUEST['w'].'&v=print"><img src="img/Devices-printer-icon.png"></a></div>';
?>


<div id="loading" class="loading" style="display:none; float:right; padding-right:10px;">loading...</div>







<?php

date_default_timezone_set('UTC');

require_once("wakaHelper.php");
require_once("renderHelper.php");
require_once("image.php");
require_once("imagesHelper.php");


if( isset($_REQUEST['w']) && wakaExists($_REQUEST['w']) ){

	$waka=readWaka($_REQUEST['w']);
	
	//print_r($waka);
	if(!isset($_REQUEST['u'])) $_REQUEST['u']='public';
	else if(!isset($waka['users'][$_REQUEST['u']])) $_REQUEST['u']='public';
	
	$wakaisactive=true;
	if(isset($_REQUEST['v']) && $_REQUEST['v']=='print') renderWakaPrint($waka);
	else renderWaka($waka);

}else{

	echo '<h4>Create new waka:</h4>';
	echo '<big><form action="newwaka.php" method="post" name="createWaka" class="createWaka_form" autocomplete="off">';
	echo '<p>Just put your email adress: <input type="text" value="" name="email"></p>';
	echo '<p>... and a title of your new waka: <input type="text" value="" name="title"></p>';
	echo '<p><input type="submit" name="create" value="create"> and voilà.</p></form></big>';

	$wakaisactive=false;

}


?>

</div>


<div style="height: 200px;"></div>
<div id="targetDiv" style=""></div>

<script>

var dateString="<?php echo date("Y-m-d H:i:s"); ?>";
var wkey="<?php echo $_REQUEST['w']; ?>";
var ukey="<?php echo $_REQUEST['u']; ?>";


 
function toggleEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand('mceAddControl', false, id);
	else
		tinyMCE.execCommand('mceRemoveControl', false, id);
}

function insertHTML(html) {
    tinyMCE.execInstanceCommand("mceInsertContent",false,html);
}



var optionsStay = { 
	target:        '#targetDiv',   // target element to update 
	beforeSubmit:  showRequest,  // pre-submit callback 
	success:       showResponseStay  // post-submit callback 
}; 

var temp;

function showRequest(formData, jqForm) {
	//var extra = [ { name: 'ajax', value: '1' }];
	//$.merge( formData, extra);
	//alert('asdasd');
	
	for(i=0; i<formData.length; i++){
	
		if(formData[i].name=='publish') switchDraftEdit();
	
	}
	
	return true;  
} 


function showResponseStay(responseText, statusText)  { 
	//alert('asdasd');
	//window.location.reload();
	update();
} 


function initiate(){

	$('form').bind('form-pre-serialize', function(e) {
    	tinyMCE.triggerSave();
	});
	
	$('.properties_edit_form').ajaxForm(optionsStay);
	$('.starter_edit_form').ajaxForm(optionsStay);
    $('.draft_edit_form').ajaxForm(optionsStay);
    $('.post_edit_form').ajaxForm(optionsStay);
    $('.edit_form').ajaxForm(optionsStay);
    $('.comment_add_form').ajaxForm(optionsStay);
    $('.invite_form').ajaxForm(optionsStay);
    $('.uploader').ajaxForm(optionsStay);
	
 	$("#loading").ajaxStart(function(){
   		$(this).show();
 	}).ajaxStop(function(){
   		$(this).hide();
 	});
	
	$(".fancy").fancybox();

}

$(document).ready(function() { 

	document.title = 'Waka: <?php if(isset($waka['starter']['title'])) echo $waka['starter']['title']; else echo 'create new'?>'

	$('.post').each(function(){
	
		//alert( $(this).attr('id').substr(5) );
		
		fastminPost($(this).attr('id').substr(5));
	
	});
	
	<?php 
	
	if($wakaisactive){
	
		echo 'initiate(); setInterval("update()",60000); maketime();';
		
	}
	
	if(isset($_REQUEST['v']) && $_REQUEST['v']=='print') echo 'window.print();';
	
	?>
	
});
</script>

</html>
