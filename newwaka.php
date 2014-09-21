<?php

date_default_timezone_set('UTC');
require_once("wakaHelper.php");
require_once("renderHelper.php");
require_once("image.php");
require_once("imagesHelper.php");
require_once("class.phpmailer.php");


$wakakey=generateRandomPwd();

mkdir('data/'.$wakakey.'/');
mkdir('data/'.$wakakey.'/files/');
mkdir('data/'.$wakakey.'/images/');

$waka['dateCreated']=date("Y-m-d H:i:s");
$waka['dateTouched']=date("Y-m-d H:i:s");
$waka['dateUsersTouched']=date("Y-m-d H:i:s");

$waka['maxPostId']=1;
$waka['maxFileId']=1;
$waka['maxImageId']=1;

$waka['starter']['title']=$_REQUEST['title'];
$waka['starter']['content']='[i]... put some content here[/i]';
$waka['starter']['dateTouched']=date("Y-m-d H:i:s");
$waka['starter']['files']=json_decode('[]');
$waka['starter']['images']=json_decode('[]');

$waka['posts']=json_decode('[]');


$newkey=generateRandomPwd();

$waka['users'][$newkey]['email']=$_REQUEST['email'];
$waka['users'][$newkey]['type']='editor';
$waka['users'][$newkey]['notificationCooldown']='3600';
$waka['users'][$newkey]['dateJoined']=date("Y-m-d H:i:s");

$waka['dateUsersTouched']=date("Y-m-d H:i:s");



$waka['drafts'][$newkey]['title']='';
$waka['drafts'][$newkey]['content']='';
$waka['drafts'][$newkey]['files']=json_decode('[]');
$waka['drafts'][$newkey]['images']=json_decode('[]');
$waka['drafts'][$newkey]['dateTouched']=date("Y-m-d H:i:s");

$mail = new PHPMailer();

$mail->From = "waka@dasunwahrscheinliche.de";
$mail->Sender = "waka@dasunwahrscheinliche.de";
$mail->FromName = "Waka";
$mail->AddAddress($_REQUEST['email']);
$mail->WordWrap = 50;                                 // set word wrap to 50 characters
$mail->IsHTML(true);                                  // set email format to HTML
$mail->Subject = 'New waka created: '.$_REQUEST['title'];
$mail->Body    = nl2br("Use the following link to edit it make posts:\n
					
					http://dasunwahrscheinliche.de/waka/?w=".$wakakey."&u=".$newkey."\n
					
					Do not share this link!");
if(!$mail->Send())
{

   //echo "Message could not be sent. <p>";

   //echo "Mailer Error: " . $mail->ErrorInfo."</p>";

   //exit;

}
//echo "Message has been sent to ".$_REQUEST['email']."<br>";	

writeWaka($waka,$wakakey);

header('Location: http://dasunwahrscheinliche.de/waka/?w='.$wakakey.'&u='.$newkey);

?>