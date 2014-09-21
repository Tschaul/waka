<?php


date_default_timezone_set('UTC');
require_once("wakaHelper.php");
require_once("renderHelper.php");
require_once("image.php");
require_once("imagesHelper.php");
require_once("class.phpmailer.php");

print_r($_REQUEST);

if(wakaExists($_REQUEST['w'])){


	$notify=true;

	$waka=readWaka($_REQUEST['w']);
	
	//echo $waka['users'][$_REQUEST['u']]['type'];
	
	if(isset($_REQUEST['action']) && $_REQUEST['action']=="invite"){
	
		$notify=false;
		
		$permitted=false;
		
		if($_REQUEST['type']=='subscriber') $permitted=true;
		if($_REQUEST['type']=='editor' && $waka['users'][$_REQUEST['u']]['type']=='editor') $permitted=true;
		
		
		$_REQUEST['email']=trim($_REQUEST['email']);
		
		if($permitted){
		
			$newkey=generateRandomPwd();
			
			$waka['users'][$newkey]['email']=$_REQUEST['email'];
			$waka['users'][$newkey]['type']=$_REQUEST['type'];
			$waka['users'][$newkey]['notificationCooldown']='3600';
			$waka['users'][$newkey]['dateJoined']=date("Y-m-d H:i:s");
			
			$waka['dateUsersTouched']=date("Y-m-d H:i:s");
			
			
			
			$waka['drafts'][$newkey]['title']='';
			$waka['drafts'][$newkey]['content']='';
			$waka['drafts'][$newkey]['files']=json_decode('[]');
			$waka['drafts'][$newkey]['images']=json_decode('[]');
			$waka['drafts'][$newkey]['dateContentTouched']=date("Y-m-d H:i:s");
			$waka['drafts'][$newkey]['dateImagesTouched']=date("Y-m-d H:i:s");
			$waka['drafts'][$newkey]['dateFilesTouched']=date("Y-m-d H:i:s");
			
			$mail = new PHPMailer();
			
			$mail->From = "waka@dasunwahrscheinliche.de";
			$mail->Sender = "waka@dasunwahrscheinliche.de";
			$mail->FromName = "Waka";
			$mail->AddAddress($_REQUEST['email']);
			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
			$mail->IsHTML(false);                                  // set email format to HTML
			$mail->Subject = 'You have been invited to a waka: '.$waka['starter']['title'];
			$mail->Body    = $waka['users'][$_REQUEST['u']]['email']." invited you to participate in a waka. Use the following link to read the latest version and make posts:\n
								
								http://dasunwahrscheinliche.de/waka/?w=".$_REQUEST['w']."&u=".$newkey."\n
								
								Do not share this link!";
			if(!$mail->Send())
			{
	
			   echo "Message could not be sent. <p>";
	
			   echo "Mailer Error: " . $mail->ErrorInfo."</p>";
	
			   exit;
	
			}
			echo "Message has been sent to ".$user['email']."<br>";	
			
		}
	}
	
	
	if($waka['users'][$_REQUEST['u']]['type']=='editor'){
	
		
	
		if(isset($_REQUEST['action']) && $_REQUEST['action']=="edit"){
		
			if($_REQUEST['location']=="starter") $item=&$waka['starter'];
			else if($_REQUEST['location']=="draft"){ 
				$item=&$waka['drafts'][$_REQUEST['u']];
				$notify=false;
			}else if(strpos($_REQUEST['location'],'post')==0){
			
				$id=substr($_REQUEST['location'],5);
				$postcount=count($waka['posts']);
				
				
				
				for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$id && userMayEdit($waka['users'][$_REQUEST['u']]['email'],$waka['posts'][$p]['users'])){
			
					$item=&$waka['posts'][$p];
					$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']=date("Y-m-d H:i:s");
			
				}
			}
			
			if(isset($item)){
			
				$item['title']=$_REQUEST['title'];
				$item['content']=$_REQUEST['content'];
				$item['dateContentTouched']=date("Y-m-d H:i:s");
			
			
			}
			
			if($_REQUEST['location']=="draft" && isset($_REQUEST['publish'])){
			
				$newpost['users'][$waka['users'][$_REQUEST['u']]['email']]['mayEdit']=1;
				$newpost['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']=date("Y-m-d H:i:s");
				$newpost['users'][$waka['users'][$_REQUEST['u']]['email']]['dateRead']=date("Y-m-d H:i:s");
				$waka['maxPostId']++;
				$newpost['id']=$waka['maxPostId'];
				$newpost['content']=$waka['drafts'][$_REQUEST['u']]['content'];
				$newpost['title']=$waka['drafts'][$_REQUEST['u']]['title'];
				$newpost['dateCreated']=date("Y-m-d H:i:s");
				$newpost['dateContentTouched']=date("Y-m-d H:i:s");
				$newpost['dateImagesTouched']=date("Y-m-d H:i:s");
				$newpost['dateFilesTouched']=date("Y-m-d H:i:s");
				$newpost['dateCommentsTouched']=date("Y-m-d H:i:s");
				$newpost['dateUsersTouched']=date("Y-m-d H:i:s");
				$newpost['files']=$waka['drafts'][$_REQUEST['u']]['files'];
				$newpost['images']=$waka['drafts'][$_REQUEST['u']]['images'];
				$newpost['comments']=json_decode('[]');
				
				$waka['posts'][count($waka['posts'])]=$newpost;
				
				$waka['drafts'][$_REQUEST['u']]['title']='';
				$waka['drafts'][$_REQUEST['u']]['content']='';
				$waka['drafts'][$_REQUEST['u']]['files']=json_decode('[]');
				$waka['drafts'][$_REQUEST['u']]['images']=json_decode('[]');
				$waka['drafts'][$_REQUEST['u']]['dateContentTouched']=date("Y-m-d H:i:s");
				$waka['drafts'][$_REQUEST['u']]['dateFilesTouched']=date("Y-m-d H:i:s");
				$waka['drafts'][$_REQUEST['u']]['dateImagesTouched']=date("Y-m-d H:i:s");
				
				$postcount=count($waka['posts']);
			
				for($p=0; $p<$postcount; $p++) $waka['posts'][$p]['dateUsersTouched']=date("Y-m-d H:i:s");
			
			}
		
		}
		
		
		if(isset($_REQUEST['action']) && $_REQUEST['action']=="deletePost"){
		
			$notify=false;
		
			$postcount=count($waka['posts']);
			
			for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$_REQUEST['id'] && userMayEdit($waka['users'][$_REQUEST['u']]['email'],$waka['posts'][$p]['users'])){
				
				$fcount=count($waka['posts'][$p]['files']);
				
				for($f=0; $f<$fcount; $f++){
				
					unlink($waka['posts'][$p]['files'][$f]['url']);
					unlink(dirname($waka['posts'][$p]['files'][$f]['url']));
				
					array_splice($waka['posts'][$p]['files'],$f,1);
					
				
				}
				
				$fcount=count($waka['posts'][$p]['images']);
				
				for($f=0; $f<$fcount; $f++) if($waka['posts'][$p]['images'][$f]['id']==$_REQUEST['iid']){
				
					unlink($waka['posts'][$p]['images'][$f]['url']);
					unlink(getThumbUrl($waka['posts'][$p]['images'][$f]['url']));
					unlink(getMidsizeUrl($waka['posts'][$p]['images'][$f]['url']));
					unlink(dirname($waka['posts'][$p]['images'][$f]['url']));
				
					array_splice($waka['posts'][$p]['images'],$f,1);
					
				
				}
				
				array_splice($waka['posts'],$p,1);
			
			}
		
		
		
		}
		
		if(isset($_REQUEST['uploadType']) && ( $_REQUEST['uploadType']=='image' || $_REQUEST['uploadType']=='file' ) ){
		
			$permitted=false;
			
			
				//echo 'check permitted';
			
				if($_REQUEST['location']=="starter") {
					$item=&$waka['starter'];
					$permitted=true;
				}
				else if($_REQUEST['location']=="draft"){ 
					$item=&$waka['drafts'][$_REQUEST['u']];
					$notify=false;
					$permitted=true;
				}else if(strpos($_REQUEST['location'],'post')==0){
			
					$id=substr($_REQUEST['location'],5);
					$postcount=count($waka['posts']);
					for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$id && userMayEdit($waka['users'][$_REQUEST['u']]['email'],$waka['posts'][$p]['users'])){
			
						$item=&$waka['posts'][$p];
						$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']=date("Y-m-d H:i:s");
						$permitted=true;
			
					}
				}

			if($permitted){
			
			

				if($_REQUEST['uploadType']=='file'){
				
					$waka['maxFileId']++;
					$target = "data/".$_REQUEST['w']."/files/".$waka['maxFileId']."/";
					mkdir($target);
					//echo $target;
		
				}else if($_REQUEST['uploadType']=='image'){
				
					$waka['maxImageId']++;
					$target = "data/".$_REQUEST['w']."/images/".$waka['maxImageId']."/";
					mkdir($target);
			
				}else {
			
				}
				
				$target = $target.basename($_FILES['uploaded']['name']); 
			
				if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
			
					chmod($target,0755);
			
					if($_REQUEST['uploadType']=='file'){
						
						$file['url']=$target;
						$file['dateCreated']=date("Y-m-d H:i:s");
						$file['id']=$waka['maxFileId'];
						$file['name']=basename($_FILES['uploaded']['name']);
						
						$item['files'][count($item['files'])]=$file;
						$item['dateFilesTouched']=date("Y-m-d H:i:s");
						
			
					}else if($_REQUEST['uploadType']=='image'){
						 
						$img = new image;
						$img->createfromfile($target);
					
						if($img->width()>$img->height()){
					
							$left=($img->width()-$img->height())/2;
							$img->cut($left,0,$img->height(),$img->height());
					
					
						}else{
					
							$top=($img->height()-$img->width())/2;
							$img->cut(0,$top,$img->width(),$img->width());
					
						}			
					
						$img->resize(64,64,true);
						$thumbtarget=getThumbUrl($target);
						$img->save($thumbtarget,90);
						$img2 = new image;
						$img2->createfromfile($target);
					
						if($img2->width()>640 || $img2->height()>640 ){
							$img2->resize(540,540,true);
						}
					
						$midsizetarget=getMidsizeUrl($target);
						$img2->save($midsizetarget,90);
						
						$image['url']=$target;
						$image['dateCreated']=date("Y-m-d H:i:s");
						$image['id']=$waka['maxImageId'];
						$image['name']=basename($_FILES['uploaded']['name']);
						
						$item['images'][count($item['images'])]=$image;
						$item['dateImagesTouched']=date("Y-m-d H:i:s");
			
					}
			
				} else {
		
					echo "Sorry, there was a problem uploading your file.";
			
				}
			
		
			}
	
		}
		
		if(isset($_REQUEST['action']) && ( $_REQUEST['action']=='addUser' || $_REQUEST['action']=='kickUser' ) ){
			
			$notify=false;
			
			if($_REQUEST['location']=="starter") $item=&$waka['starter'];
			else if($_REQUEST['location']=="draft"){ 
				$item=&$waka['drafts'][$_REQUEST['u']];
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']=date("Y-m-d H:i:s");
				$notify=false;
			}else if(strpos($_REQUEST['location'],'post')==0){
			
				$id=substr($_REQUEST['location'],5);
				$postcount=count($waka['posts']);
				for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$id && userMayEdit($waka['users'][$_REQUEST['u']]['email'],$waka['posts'][$p]['users'])){
			
					$item=&$waka['posts'][$p];
			
				}
			}
			
			if($_REQUEST['action']=='addUser'){
			
				if(!isset($item['users'][$_REQUEST['email']])){
				
					$item['users'][$_REQUEST['email']]['mayEdit']=1;
					$item['users'][$_REQUEST['email']]['dateTouched']="1985-09-02 14:00:00";
					$item['users'][$_REQUEST['email']]['dateRead']="1985-09-02 14:00:00";
				
				}else{
				
					$item['users'][$_REQUEST['email']]['mayEdit']=1;
				
				}
			
			}else{
			
				$item['users'][$_REQUEST['email']]['mayEdit']=0;
			
			}
		
			$item['dateUsersTouched']=date("Y-m-d H:i:s");
		
		}
		
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='renameFile'){
			
			$notify=false;
			
			if($_REQUEST['location']=="starter") $item=&$waka['starter'];
			else if($_REQUEST['location']=="draft"){ 
				$item=&$waka['drafts'][$_REQUEST['u']];
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']=date("Y-m-d H:i:s");
				$notify=false;
			}else if(strpos($_REQUEST['location'],'post')==0){
			
				$id=substr($_REQUEST['location'],5);
				$postcount=count($waka['posts']);
				for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$id && userMayEdit($waka['users'][$_REQUEST['u']]['email'],$waka['posts'][$p]['users'])){
			
					$item=&$waka['posts'][$p];
			
				}
			}
			
			$fcount=count($item['files']);
				
			for($f=0; $f<$fcount; $f++) if($item['files'][$f]['id']==$_REQUEST['fid']){
				
				$newurl=dirname($item['files'][$f]['url']).'/'.$_REQUEST['newname'];
				
				rename($item['files'][$f]['url'],$newurl);
				
				$item['files'][$f]['name']=$_REQUEST['newname'];
				$item['files'][$f]['url']=$newurl;
					
				$item['dateFilesTouched']=date("Y-m-d H:i:s");
				
			}
		
		
		}
		
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='deleteFile'){
			
			$notify=false;
			
			if($_REQUEST['location']=="starter") $item=&$waka['starter'];
			else if($_REQUEST['location']=="draft"){ 
				$item=&$waka['drafts'][$_REQUEST['u']];
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']=date("Y-m-d H:i:s");
				$notify=false;
			}else if(strpos($_REQUEST['location'],'post')==0){
			
				$id=substr($_REQUEST['location'],5);
				$postcount=count($waka['posts']);
				for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$id && userMayEdit($waka['users'][$_REQUEST['u']]['email'],$waka['posts'][$p]['users'])){
			
					$item=&$waka['posts'][$p];
			
				}
			}
			
			$fcount=count($item['files']);
				
			for($f=0; $f<$fcount; $f++) if($item['files'][$f]['id']==$_REQUEST['fid']){
				
				unlink($item['files'][$f]['url']);
				unlink(dirname($item['files'][$f]['url']));
				
				array_splice($item['files'],$f,1);
					
				$item['dateFilesTouched']=date("Y-m-d H:i:s");
				
			}
		
		
		}
		
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='deleteImage'){
			
			$notify=false;
			
			if($_REQUEST['location']=="starter") $item=&$waka['starter'];
			else if($_REQUEST['location']=="draft"){ 
				$item=&$waka['drafts'][$_REQUEST['u']];
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']=date("Y-m-d H:i:s");
				$notify=false;
			}else if(strpos($_REQUEST['location'],'post')==0){
			
				$id=substr($_REQUEST['location'],5);
				$postcount=count($waka['posts']);
				for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$id && userMayEdit($waka['users'][$_REQUEST['u']]['email'],$waka['posts'][$p]['users'])){
			
					$item=&$waka['posts'][$p];
			
				}
			}
			
			$fcount=count($item['images']);
				
			for($f=0; $f<$fcount; $f++) if($item['images'][$f]['id']==$_REQUEST['iid']){
				
				unlink($item['images'][$f]['url']);
				unlink(getThumbUrl($item['images'][$f]['url']));
				unlink(getMidsizeUrl($item['images'][$f]['url']));
				unlink(dirname($item['images'][$f]['url']));
				
				array_splice($item['images'],$f,1);
					
				$item['dateImagesTouched']=date("Y-m-d H:i:s");
					
				
			}
		
		
		}
	
	
	}
	
	//echo $_REQUEST['location'];
	
	if($waka['users'][$_REQUEST['u']]['type']=='editor' || $waka['users'][$_REQUEST['u']]['type']=='subscriber'){
	
	
		
	
		if(isset($_REQUEST['action']) && isset($_REQUEST['location']) && (strpos($_REQUEST['location'],'post')==0)){
			
			$notify=false;
			
			/*
			
			if($_REQUEST['location']=="starter") $item=&$waka['starter'];
			else if($_REQUEST['location']=="draft"){ 
				$item=&$waka['drafts'][$_REQUEST['u']];
				$notify=false;
			}else 
			
			*/
			
			
			if(strpos($_REQUEST['location'],'post')==0){
			
				$id=substr($_REQUEST['location'],5);
				$postcount=count($waka['posts']);
				for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$id){
			
					$item=&$waka['posts'][$p];
			
				}
				
			}
			
			if(!isset($item['users'][$waka['users'][$_REQUEST['u']]['email']])){
			
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]=array();
			
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['mayEdit']=0;
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateTouched']="1985-09-02 14:00:00";
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateRead']=date("Y-m-d H:i:s");
			
			}else{
			
				$item['users'][$waka['users'][$_REQUEST['u']]['email']]['dateRead']=date("Y-m-d H:i:s");
			
			}
		
		}
	
		//echo $_REQUEST['action'];
		
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='properties_edit'){
		
			$notify=false;
			$waka['users'][$_REQUEST['u']]['notificationCooldown']=$_REQUEST['notificationCooldown'];
			$waka['dateUsersTouched']=date("Y-m-d H:i:s");
			
			echo '<br>'.$waka['users'][$_REQUEST['u']]['notificationCooldown'].'<br>';
		
		}
	
		if(isset($_REQUEST['action']) && $_REQUEST['action']=='comment_add'){
		
			echo 'check';
		
			$postcount=count($waka['posts']);
			
			for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$_REQUEST['id']){
			
				echo $waka['posts'][$p]['id'];
		
				$waka['posts'][$p]['maxCommentId']++;
				$comment['id']=$waka['posts'][$p]['maxCommentId'];
				$comment['user']=$waka['users'][$_REQUEST['u']]['email'];
				$comment['content']=$_REQUEST['content'];
				$comment['dateCreated']=date("Y-m-d H:i:s");
		
				$waka['posts'][$p]['comments'][count($waka['posts'][$p]['comments'])]=$comment;
				
				$waka['posts'][$p]['dateCommentsTouched']=date("Y-m-d H:i:s");
				
				//print_r($comment);
		
			}
		
		
		}else if(isset($_REQUEST['action']) && $_REQUEST['action']=='comment_delete'){
		
			//echo 'check';
			$notify=false;
			$postcount=count($waka['posts']);
			
			for($p=0; $p<$postcount; $p++) if($waka['posts'][$p]['id']==$_REQUEST['id']){
			
				$ccount=count($waka['posts'][$p]['comments']);
				
				for($c=0; $c<$ccount; $c++) if($waka['posts'][$p]['comments'][$c]['id']==$_REQUEST['cid']) array_splice($waka['posts'][$p]['comments'],$c,1);
				
				$waka['posts'][$p]['dateCommentsTouched']=date("Y-m-d H:i:s");
				
				//print_r($comment);
		
			}
		
		
		}
		
	}
	
	
	//notify!!
	
	
	foreach($waka['users'] as $key => $user) if($key!=$_REQUEST['u'] && $notify && $user['notificationCooldown']!='0'){
	
		if(!isset($user['lastNotification'])) $tlastntf=0;
		else $tlastntf=convert_datetime($user['lastNotification']);
		
		echo (time()-$tlastntf);
		
		if( (time()-$tlastntf) > $user['notificationCooldown']){
		
			$mail = new PHPMailer();
	
			$mail->From = "waka@dasunwahrscheinliche.de";
			$mail->Sender = "waka@dasunwahrscheinliche.de";
	
			$mail->FromName = "Waka";
	
			$mail->AddAddress($user['email']);
	
	
	
			$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	
			//$mail->AddAttachment($file, "Aushang.pdf");    // optional name
	
			$mail->IsHTML(false);                                  // set email format to HTML
	
	
	
			$mail->Subject = 'Activity on waka: '.$waka['starter']['title'];
	
			$mail->Body    = "There has been an activity on the waka you are subscribed to. Use the following link to read the latest version:\n	
							\n			
							http://dasunwahrscheinliche.de/waka/?w=".$_REQUEST['w']."&u=".$key."\n
							\n
							Do not share this link!";
	
			//$mail->AltBody = $message;
	
	
	
			if(!$mail->Send())
	
			{
	
			   echo "Message could not be sent. <p>";
	
			   echo "Mailer Error: " . $mail->ErrorInfo."</p>";
	
			   exit;
	
			}
	
	
	
			echo "Message has been sent to ".$user['email']."<br>";	
			
			$waka['users'][$key]['lastNotification']=date("Y-m-d H:i:s");
		
		}
	
	}

	writeWaka($waka,$_REQUEST['w']);
	
	//echo $_REQUEST['action'];

}

?>