<?php

echo 'check';



require_once('../meta/DBFunctions.php');

require_once('../meta/indexFunctions.php');

require_once('../config.php');

require("class.phpmailer.php");

/*function convert_datetime($str) {

	list($date, $time) = explode(' ', $str);
	list($year, $month, $day) = explode('-', $date);
	list($hour, $minute, $second) = explode(':', $time);

	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	return $timestamp;
}*/ 



function sendmail($subject,$message,$file){

	$subscribers=getBelongingItemsFromDB('subscribers','1','lectures');

	for($i=0; $i<count($subscribers); $i++){

		$mail = new PHPMailer();

		$mail->From = "joanna.smiglak.cbi.uni-erlangen.de";
		$mail->Sender = "joanna.smiglak.cbi.uni-erlangen.de";
		$mail->FromName = "Joanna Smiglak";
		$mail->AddAddress($subscribers[$i]['address']);

		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->AddAttachment($file, "Aushang.pdf");    // optional name
		$mail->IsHTML(true);                                  // set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $message;
		//$mail->AltBody = $message;

		if(!$mail->Send())
		{
		   echo "Message could not be sent. <p>";
		   echo "Mailer Error: " . $mail->ErrorInfo;
		   exit;
		}

		echo "Message has been sent to ".$subscribers[$i]['address']."<br>";

	}

}

$message='Dies ist eine Testmail';

$subject='Testbetreff';

$headers = 'From: joanna.smiglak@cbi.uni-erlangen.de' . "\r\n" .
    'Reply-To: joanna.smiglak@cbi.uni-erlangen.de' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$id=1;

if($_GET['id']!=''){ $id=$_GET['id']; }

$item=getItemFromDB('lectures',$id);

$feeditems=getBelongingItemsFromDB('lecturefeeds',$id,'lectures');

$time_now=0;



if(isset($_GET['date'])){
	$time_now=convert_datetime($_GET['date']);
}else{
	$time_now=time();
}

echo $time_now;



// check for the two weeks anouncements


for($i=0; $i<count($feeditems); $i++){

	$time_intwoweeks=$time_now + (10 * 24 * 60 * 60);

	$time_feeditem=convert_datetime($feeditems[$i]['date']);

	if(abs($time_intwoweeks-$time_feeditem)<12*60*60){

		list($date, $time) = explode(' ', $feeditems[$i]['date']);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute, $second) = explode(':', $time);

		$subject='CBI-Kolloquiumstermin am '.$day.'.'.$month.'.'.$year.' '.$hour.':'.$minute;

		$message=nl2br(htmlentities(utf8_decode("
		
		Liebe Kollegen,\n
		\n
		Am ".$day.".".$month.".".$year." wird im Rahmen des CBI-Kolloquiums ".$feeditems[$i]['lecturer'].", ".$feeditems[$i]['affiliation'].", zum Thema \"".$feeditems[$i]['title']."\" einen Vortag halten. Wir würden uns über Ihre Anwesenheit freuen.\n
		\n
		Nähere Information entnehmen Sie bitte der angehängten PDF-Datei.\n
		\n
		Mit freundlichen Grüßen, Joanna Smiglak\n
		\n
		PS: Um diesen Newsletter abzubestellen, bitte formlos auf diese Mail entsprechend antworten.")));
		
		$filesitems=getBelongingItemsFromDB('files',$feeditems[$i]['ID'],'lecturefeeds');

		$file='';

		if($filesitems[0]['url']!=''){
			
			$file='../database/files/'.$filesitems[0]['url'];

			echo $file;

		}

		if($file==''){

			$errormessage='Für die Verstaltung am '.$feeditems[$i]['date'].' mit der ID '.$feeditems[$i]['ID'].' fehlt das Pdf. Email wurde nicht versandt.';

			$erroraddress='julian.steinwachs@cbi.uni-erlangen.de';

			$errorsubject='Fehler beim versandt des Kolloquiums-Newsletter!';

			mail($erroraddress,$errorsubject,$errormessage);

		}else{

			sendmail($subject,$message,$file);
		
			echo '<br><br>'.$message;
		
			echo '<br><br>mail sent';

		}

	}

	

}



// check for the three days anouncements




for($i=0; $i<count($feeditems); $i++){

	$time_inthreedays=$time_now + (24 * 60 * 60);

	$time_feeditem=convert_datetime($feeditems[$i]['date']);

	if(abs($time_inthreedays-$time_feeditem)<12*60*60){

		list($date, $time) = explode(' ', $feeditems[$i]['date']);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute, $second) = explode(':', $time);

		$subject='Erinnerung an CBI-Kolloquiumstermin morgen '.$day.'.'.$month.'.'.$year.' '.$hour.':'.$minute;

		$message=nl2br(htmlentities(utf8_decode("
		
		Liebe Kollegen,\n
		\n
		Wir möchten Sie an das morgige CBI-Kolloquium erinnern.\n
		".$feeditems[$i]['lecturer']." - \"".$feeditems[$i]['title']."\".\n
		\n
		Mit freundlichen Grüßen\n 
		Joanna Smiglak")));

		sendmail($subject,$message,'');
	
		echo '<br><br>'.$subject;
	
		echo '<br><br>'.$message;
	
		echo '<br><br>mail sent';

	}

}



?>



