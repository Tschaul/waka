<?php


function renderWaka($waka){

	//print_r($waka['users'][$_REQUEST['u']]['notificationCooldown']);

	if(isset($waka['users'][$_REQUEST['u']]['type'])) {
		$user['type']=$waka['users'][$_REQUEST['u']]['type'];
		$user['email']=$waka['users'][$_REQUEST['u']]['email'];
	}else{
		$user['type']='public';
		$user['email']='public';
	}
	
	echo '<div class="waka">';
	
	echo '<a href="javascript:switchHead()" style="text-decoration: none;"><span style="float:right; padding-bottom: 7px;">';
	if($user['type']!='public') echo '<small style="margin-bottom: 10px; padding-right: 8px;"><small style="color: #555;"">'.$user['email'].'</small></small>';
	echo '<img src="img/Apps-system-users-icon.png" style="vertical-align: bottom;"></span></a>';

	echo '<div style="clear:both"></div>';

	echo '<div class="headwrapper"><div class="head" id="head" style="display:none;">';
	
	echo renderHead($waka,$user);
	
	echo '</div></div>';

	
	//starter
	
	echo '<div class="starter" id="starter">';
	
	echo renderStarter($waka['starter'],$user);
	
	echo '</div>';

	
	//draft form
	
	echo '<div class="draft" id="draft"';
	if($user['type']!='editor') echo ' style="display:none"';
	echo '>';
	
	if($user['type']=='editor'){
	
		echo renderDraft($waka['drafts'][$_REQUEST['u']],$user);
	
	
	}
	echo '</div>';
	//echo '<hr>';
	
	//posts
	
	$waka['posts']=sortItemsDesc($waka['posts'],'dateCreated');

	$postcount=count($waka['posts']);
	
	//$dateread=date("Y-m-d H:i:s");
	
	for($p=0; $p<$postcount; $p++){
	
		if(isset($waka['posts'][$p]['users'][$user['email']]) && isset($waka['posts'][$p]['users'][$user['email']]['dateRead'])) $dateread=$waka['posts'][$p]['users'][$user['email']]['dateRead'];
		else $dateread="1985-09-02 14:00:01";
	
		//echo $waka['posts'][$p]['users'][$user['email']]['dateRead'].'<br>';
	
		//echo $waka['posts'][$p]['dateContentTouched'].' : '.$dateread.' -> '.strnatcmp($waka['posts'][$p]['dateContentTouched'],$dateread).'<br>';
		//echo $waka['posts'][$p]['dateFilesTouched'].' : '.$dateread.'<br>';
		//echo $waka['posts'][$p]['dateImagesTouched'].' : '.$dateread.'<br>';
		//echo $waka['posts'][$p]['dateUsersTouched'].' : '.$dateread.'<br>';
	
		echo '<div class="post" id="post_'.$waka['posts'][$p]['id'].'"';
		
		if(!(strnatcmp($waka['posts'][$p]['dateContentTouched'],$dateread)==-1 && strnatcmp($waka['posts'][$p]['dateFilesTouched'],$dateread)==-1 && strnatcmp($waka['posts'][$p]['dateImagesTouched'],$dateread)==-1)) echo ' unread="yes"';
		else echo ' unread="no"';
		
		echo '>';
		
		echo renderPost($waka['posts'][$p],$waka['users'],$user);
		
		echo '</div>';
	
	}

 

	echo '</div>';

}

function renderWakaPrint($waka){

	echo '<div class="waka">';
	
	//starter
	
	echo '<div class="starter" id="starter">';
	
	echo '<div class="starter_display" id="starter_display">';
	
	echo '<div class="starter_content" id="starter_content">';
	echo renderItemContent($waka['starter'],'starter');
	echo '</div>';
	
	echo '</div>';
	
	echo '</div>';
	
	//posts
	
	$waka['posts']=sortItemsDesc($waka['posts'],'dateCreated');

	$postcount=count($waka['posts']);
	
	for($p=0; $p<$postcount; $p++){
	
		echo '<div class="post" id="post_'.$waka['posts'][$p]['id'].'">';
			
		echo '<h3 class="post_title">'.stripslashes($waka['posts'][$p]['title']).'</h3>';
		echo '<div class="post_identifier"><small><span class="time" utctime="'.convert_datetime($waka['posts'][$p]['dateCreated']).'">'.$waka['posts'][$p]['dateCreated'].' (UTC)</span> '.$waka['posts'][$p]['user'].'</small></div>';
		echo '<div class="post_content" id="post_'.$waka['posts'][$p]['id'].'_content">';
		echo ''.renderedContent($waka['posts'][$p]).'';
		echo '</div>';
	
		
		echo '</div>';
		
		//echo '<hr>';
	
	}

 

	echo '</div>';

}

function renderHead($waka,$user){

	$isuser=($user['type']=='editor' || $user['type']=='subscriber');

	$str='';

	

		
	$str.='<div class="headLeft"><div class="headProperties">';
	
	if($isuser){
		
		$str.= '<form action="query.php" method="post" name="editproperties" class="properties_edit_form" autocomplete="off">
				<input type="hidden" name="w" value="'.$_REQUEST['w'].'">
				<input type="hidden" name="u" value="'.$_REQUEST['u'].'">
				<input type="hidden" name="action" value="properties_edit"><small>';
				
				$str.= 'notificationCooldown:<br>
				  <input type="radio" name="notificationCooldown" value="60"';
				if($waka['users'][$_REQUEST['u']]['notificationCooldown']=='60') $str.=' checked="yes" ';
				$str.='>1min <input type="radio" name="notificationCooldown" value="300"';
				if($waka['users'][$_REQUEST['u']]['notificationCooldown']=='300') $str.=' checked="yes" ';
				$str.='>5min <input type="radio" name="notificationCooldown" value="900"';
				if($waka['users'][$_REQUEST['u']]['notificationCooldown']=='900') $str.=' checked="yes" ';
				$str.='>15min <input type="radio" name="notificationCooldown" value="3600"';
				if($waka['users'][$_REQUEST['u']]['notificationCooldown']=='3600') $str.=' checked="yes" ';
				$str.='>1h <input type="radio" name="notificationCooldown" value="14400"';
				if($waka['users'][$_REQUEST['u']]['notificationCooldown']=='14400') $str.=' checked="yes" ';
				$str.='>4h <input type="radio" name="notificationCooldown" value="57600"';
				if($waka['users'][$_REQUEST['u']]['notificationCooldown']=='57600') $str.=' checked="yes" ';
				$str.='>16h <input type="radio" name="notificationCooldown" value="0"';
				if($waka['users'][$_REQUEST['u']]['notificationCooldown']=='0') $str.=' checked="yes" ';
				$str.='>inf';
				
		$str.= '<p><input type="submit" value="save"></p>';
				
		$str.='</small></form>';
		
		$str.= '<p><small>public link: <a href="http://dasunwahrscheinliche.de/waka/?w='.$_REQUEST['w'].'">http://dasunwahrscheinliche.de/waka/?w='.$_REQUEST['w'].'</a></small></p>';

		
	}else{
	
		$str.='<small><i>You are not subscribed to this Waka.</i></small>';
	
	}
	
	
	$str.='</div></div>';
		
	$str.='<div class="headRight">';
		
	$str.='<b>Editors</b><br><small>';
		
	foreach($waka['users'] as $key => $wuser) if($wuser['type']=='editor'){
		
		$str.=''.$wuser['email'].'<br>';
		
		
	}
		
	$str.='</small><br><b>Subscribers</b><br><small>';
		
	foreach($waka['users'] as $key => $wuser) if($wuser['type']=='subscriber'){
		
		$str.=$wuser['email'].'<br>';
		
		
	}
		
	$str.='</small>';
		
	if($user['type']=='editor'){
	
		//print_r($user);
		
		$str.='<br><small>Invite somebody:<br>';
			
		$str.= '<form action="query.php" method="post" name="invite" class="invite_form" autocomplete="off">
					<input type="hidden" name="w" value="'.$_REQUEST['w'].'">
					<input type="hidden" name="u" value="'.$_REQUEST['u'].'">
					<input type="hidden" name="action" value="invite">';
					
					
		$str.= '<input style="width:200px;" type="text" name="email" value=""><br>';
		$str.= 'type: <input type="radio" name="type" value="editor"> editor';
		$str.= ' <input type="radio" name="type" value="subscriber" checked="yes"> subscriber ';
			
		$str.= '<input type="submit" value="Invite"></form></small>';
			
	}else{
	
		//if($user['type']=='public') $str.='<br><small>Subscribe:<br>';
		//else $str.='<br><small>Invite somebody:<br>';
			
		$str.= '<br><form action="query.php" method="post" name="invite" class="invite_form" autocomplete="off">
					<input type="hidden" name="w" value="'.$_REQUEST['w'].'">
					<input type="hidden" name="u" value="'.$_REQUEST['u'].'">
					<input type="hidden" name="action" value="invite">
					<input type="hidden" name="type" value="subscriber">';
					
					
		$str.= '<small>email:</small> <input style="width:200px;" type="text" name="email" value="">';
			
		$str.= '<input type="submit" value="';
		if($user['type']=='public') $str.='Subscribe';
		else $str.='Invite';
		$str.='"></form></small>';
	
	
	}
		
	$str.='</div><small><small>&nbsp;</small></small></div><div class="clearspacer"></div>';
		
	
		
	return $str;

}

function renderStarter($starter,$user){

	$permitted=($user['type']=='editor');

	$str='';

	if($permitted) $str.= '<div class="starterEditSwitch" id="starterEditSwitch_on"><a href="javascript:switchStarterEdit()"><img src="img/Actions-document-edit-icon-1.png"></a></div>';
	if($permitted) $str.= '<div class="starterEditSwitch" id="starterEditSwitch_off" style="display:none;"><a href="javascript:switchStarterEdit()"><img src="img/Actions-application-exit-icon-1.png"></a></div>';
	

	$str.= '<div class="starter_display" id="starter_display">';
	
	$str.= '<div class="starter_content" id="starter_content">';
	$str.=renderItemContent($starter,'starter');
	$str.= '</div><div class="starter_images" id="starter_images">';
	$str.=renderItemImages($starter,'starter');
	$str.= '</div><div class="starter_files" id="starter_files">';	
	$str.=renderItemFiles($starter,'starter');
	$str.= '</div>';
	
	$str.= '</div>';
	
	if($permitted) {
	
		$str.= '<div id="starter_form" style="display:none;">';
		
		$str.= '<div class="starter_content_form" id="starter_content_form">';
		$str.=renderItemContentForm($starter,'starter');
		$str.= '</div><div class="starter_images_form" id="starter_images_form">';
		$str.=renderItemImagesForm($starter,'starter');
		$str.= '</div><div class="starter_files_form" id="starter_files_form">';
		$str.=renderItemFilesForm($starter,'starter');
		$str.= '</div>';
	
		$str.= '</div>';
		
	}
	
	
	
	//$str.= '</div>';
	
	return $str;

}

function renderDraft($draft,$user){

	$email=$user['email'];

	$str='';

	$str.= '<div class="draftEditSwitch" id="draftEditSwitch_on"><a href="javascript:switchDraftEdit()"><img src="img/Actions-document-new-icon-1.png"></a></div>';
	$str.= '';
	

	$str.= '<div class="draft_small" id="draft_small">';
	
	
	$str.= '</div><div class="draft_form" id="draft_form" style="display:none;">';
	

	//$str.= '<img src="http://www.gravatar.com/avatar/'.hash('md5',$email).'.gif?s=64&default=identicon&r=PG" class="avatar">';
	
	$str.= '<div class="draftEditWrapper">
			<div class="draftEditSwitch" id="draftEditSwitch_off" style="display:none;"><a href="javascript:switchDraftEdit()"><img src="img/Actions-application-exit-icon-1.png"></a></div>';
	
	$str.= '<div class="draft_content_form" id="draft_content_form">';	
	$str.=renderItemContentForm($draft,'draft');
	$str.= '</div><div class="draft_images_form" id="draft_images_form">';
	$str.=renderItemImagesForm($draft,'draft');
	$str.= '</div><div class="draft_files_form" id="draft_files_form">';
	$str.=renderItemFilesForm($draft,'draft');
	
	$str.= '</div>';
	$str.= '</div></div><br>&nbsp;<div class="clearspacer"></div>';

	return $str;

}

function renderPost($post,$users,$user){
	
	$permitted=false;
	
	if($user['type']=='editor' && userMayEdit($user['email'],$post['users'])) $permitted=true;
	
	$email=$user['email'];

	$str='';
	
	$str.= '<div class="post_avatar" id="post_'.$post['id'].'_avatar">';
	$str.= renderAvatar($post);
	$str.= '</div>';
	
	$str.= '<div class="post_wrapper" id="post_wrapper_'.$post['id'].'"><div class="post_small_gradient" style="display:none" id="post_small_gradient_'.$post['id'].'" onclick="minmaxPost(\''.$post['id'].'\');"></div><div class="post_meta"><a href="javascript:minmaxPost(\''.$post['id'].'\')" style="text-decoration:none;">['.$post['id'].'] <small><b><span class="time" utctime="'.convert_datetime($post['dateCreated']).'">'.$post['dateCreated'].' (UTC)</span></b>';
	
	$str.='</small></a>';
	
	if($permitted) $str.= '<div class="postEditSwitch" id="postEditSwitch_'.$post['id'].'_on"><a href="javascript:switchPostEdit(\''.$post['id'].'\')"><img src="img/Actions-document-edit-icon-1.png"></a></div>';
	if($permitted) $str.= '<div class="postEditSwitch" id="postEditSwitch_'.$post['id'].'_off" style="display:none;"><a href="javascript:switchPostEdit(\''.$post['id'].'\')"><img src="img/Actions-application-exit-icon-1.png"></a></div>';
	
	
	
	$str.= '</div>';
	
	$str.='<div class="post_display" id="post_'.$post['id'].'_display">';
	
	$str.= '<div class="post_content" id="post_'.$post['id'].'_content">';
	$str.= renderItemContent($post,'post_'.$post['id']);
	$str.= '</div><div class="post_images" id="post_'.$post['id'].'_images">';
	$str.= renderItemImages($post,'post_'.$post['id']);
	$str.= '</div><div class="post_files" id="post_'.$post['id'].'_files">';	
	$str.= renderItemFiles($post,'post_'.$post['id']);
	$str.= '</div>';
	
	$str.= '</div>';
	
	if($permitted){ 
	
		$str.='<div class="post_form" id="post_'.$post['id'].'_form" style="display:none;">';
	
		$str.= '<div class="post_content_form" id="post_'.$post['id'].'_content_form">';
		$str.= renderItemContentForm($post,'post_'.$post['id']);
		$str.= '</div><div class="post_images_form" id="post_'.$post['id'].'_images_form">';
		$str.= renderItemImagesForm($post,'post_'.$post['id']);
		$str.= '</div><div class="post_files_form" id="post_'.$post['id'].'_files_form">';
		$str.= renderItemFilesForm($post,'post_'.$post['id']);
		$str.= '</div><div class="post_users_form" id="post_'.$post['id'].'_users_form">';
		$str.= renderItemUsersForm($post,$users,'post_'.$post['id']);
		$str.= '</div>';
		
		$str.='</div>';
		
	}
	
	if($user['type']!='public'){
		
		$str.= '<div class="post_comments" id="post_'.$post['id'].'_comments">';
		
		$str.= renderItemComments($post,$user,'post_'.$post['id']);
		
		$str.= '</div>';
	
	}
	
	//$str.= '</div>';
	$str.= '</div>';
	
	$str.= '<div style="clear: both;"></div>';

	return $str;

}

function renderAvatar($post){

	$str = '<a href="javascript:minmaxPost(\''.$post['id'].'\')" style="text-decoration:none;">';
	
	foreach($post['users'] as $email => $values ){
	
		if($values['mayEdit']==1) $str.='<img src="http://www.gravatar.com/avatar/'.hash('md5',$email).'.gif?s=32&default=identicon&r=PG">';
	
	}
	
	$str.= '</a>';
	
	return $str;
}

function renderItemContent($item,$location){

	$str='';

	if($location=='starter'){
	
		$str.= '<h2>'.stripslashes($item['title']).'</h2>';
		$str.= '<p>'.renderedContent($item).'</p>'; 
	
	}else if(strpos($location,'post')!==false){
	
		$str.= '<h3>'.stripslashes($item['title']).'</h3>';
		$str.= '<p>'.renderedContent($item).'</p>';
	
	}

	return $str;
	
}

function renderItemImages($item,$location){

	$str='';

	$icount=count($item['images']);
	
	if($icount>0) $str.= '<small>images:</small><br>';
	
	for($i=0; $i<$icount; $i++){
	
		$str.= '<span class="image"><small><a class="fancy" href="'.$item['images'][$i]['url'].'"><img src="'.getThumbUrl($item['images'][$i]['url']).'"></a></small></span> ';
	
	}
	
	return $str;

}

function renderItemFiles($item,$location){

	$str='';

	$fcount=count($item['files']);
	
	if($fcount>0) $str.= '<small>files:</small><br>';
	
	for($f=0; $f<$fcount; $f++){
	
		$str.= '<span class="file"><small><a href="'.$item['files'][$f]['url'].'">'.$item['files'][$f]['name'].'</a></small></span> ';
	
	}
	
	return $str;

}

function renderItemComments($item,$user,$location){

	$email=$user['email'];

	$str='';
	
	$ccount=count($item['comments']);
		
	$item['comments']=sortItemsAsc($item['comments'],'dateCreated');
	
	for($c=0; $c<$ccount; $c++){
	
		$str.= '<div class="post_comment"><img src="http://www.gravatar.com/avatar/'.hash('md5',$item['comments'][$c]['user']).'.gif?s=32&default=identicon&r=PG" class="commentavatar">';
		if($email==$item['comments'][$c]['user']) {
			$str.= '<a href="javascript:queryDeleteComment(\''.$_REQUEST['w'].'\',\''.$_REQUEST['u'].'\',\''.$item['id'].'\',\''.$item['comments'][$c]['id'].'\')">';
			$str.= '<img src="img/Actions-edit-delete-icon.png" style="float:right;">';
			$str.= '</a>';
			
		}
		$str.= '<div class="post_comment_content"><small><span class="time" utctime="'.convert_datetime($item['comments'][$c]['dateCreated']).'">'.$item['comments'][$c]['dateCreated'].' (UTC)</span> '.$item['comments'][$c]['user'].': </small><p>'.parseBBCode2HTML(parseLatexBBCode(htmlspecialchars(stripslashes($item['comments'][$c]['content'])))).'</div></div>';
		$str.= '<div style="clear: both;"></div>';
	
	}
		
	
	$str.= '<div class="post_comment_adder"><img src="http://www.gravatar.com/avatar/'.hash('md5',$email).'.gif?s=32&default=identicon&r=PG" class="commentavatar">';
	$str.= '<div class="post_comment_form"><form action="query.php" method="post" name="editpost" class="comment_add_form" autocomplete="off">
			<input type="hidden" name="w" value="'.$_REQUEST['w'].'">
			<input type="hidden" name="u" value="'.$_REQUEST['u'].'">
			<input type="hidden" name="id" value="'.$item['id'].'">
			<input type="hidden" name="action" value="comment_add">';
	$str.= '<small>'.$email.': </small>';
	
	$str.= '<p><textarea name="content" style="width:500px; height:40px;"></textarea></p>';
	
	$str.= '<p><input type="submit" value="comment"></p>';
	
	$str.= '</form></div></div>';

	return $str;

}

function renderItemContentForm($item,$location){

	if($location=='starter') $width='750';
	else $width='665';

	$str='';

	$str.= '<form action="query.php" method="post" name="edit_'.$location.'" class="edit_form" autocomplete="off">
		<input type="hidden" name="w" value="'.$_REQUEST['w'].'">
		<input type="hidden" name="u" value="'.$_REQUEST['u'].'">
		<input type="hidden" name="action" value="edit">
		<input type="hidden" name="location" value="'.$location.'">';
		
	$str.= '<p><input style="width:600px;" type="text" name="title" value="'.stripslashes($item['title']).'"></p>';

	$str.= '<small><a href="javascript:insert(\'[b]\', \'[/b]\',\'edit_'.$location.'\')" style="text-decoration: none;">[<b>b</b>]</a>';
	$str.= ' <a href="javascript:insert(\'[i]\', \'[/i]\',\'edit_'.$location.'\')" style="text-decoration: none;">[<i>i</i>]</a>';
	$str.= ' <a href="javascript:insert(\'[url]\', \'[/url]\',\'edit_'.$location.'\')" style="text-decoration: none;">[url]</a>';
	$str.= ' <a href="javascript:insert(\'[color=ff0000]\', \'[/color]\',\'edit_'.$location.'\')" style="text-decoration: none;">[color]</a>';
	$str.= ' <a href="javascript:insert(\'[tex]\', \'[/tex]\',\'edit_'.$location.'\')" style="text-decoration: none;">[tex]</a>';
	$str.= '</small><br>';
	$str.= '<textarea name="content" style="width:'.$width.'px; height:350px; margin-top: 8px;" class="mceEditor">'.stripslashes($item['content']).'</textarea>';
	

	

	if($location=='draft'){
	
		$str.= '<br><input type="submit" name="save" value="Save"><input type="submit" name="publish" value="Save & publish" style="float:right; margin-right:48px">';
	
	}else if(strpos($location,'post')!==false){
	
		$str.= '<br><input type="submit" name="save" value="Save"><input type="button" value="Delete post" style="float:right; margin-right:48px" onclick="queryDeletePost(\''.$_REQUEST['w'].'\',\''.$_REQUEST['u'].'\',\''.substr($location,5).'\')">';
	
	
	}else{
	
		$str.= '<p><input type="submit" value="save"></p>';
	
	}

	$str.= "</form>";
	
	return $str;

}

function renderItemImagesForm($item,$location){

	$str='';

	$icount=count($item['images']);
		
	for($i=0; $i<$icount; $i++){
	
		$str.= '<span class="image"><small><a class="fancy" href="'.$item['images'][$i]['url'].'"><img src="'.getThumbUrl($item['images'][$i]['url']).'"></a>';
		
		$str.= '<span style="vertical-align:top; display:inline-block;"><a class="handle" href="javascript:queryDeleteImage(\''.$_REQUEST['w'].'\',\''.$_REQUEST['u'].'\',\''.$location.'\',\''.$item['images'][$i]['id'].'\')">';
		$str.= '<img src="img/Actions-edit-delete-icon.png"></a><br><a class="handle" href="javascript:copyToClipboard(\'[image_'.$item['images'][$i]['id'].']\')"><img src="img/Actions-edit-copy-icon-1.png"></a></span>';			
		
		$str.= '</small></span>';
	
	}
	
	$str.= '<form action="query.php" method="post" enctype="multipart/form-data" class="uploader" autocomplete="off">
		<small>Image upload:</small>  
		<input type="file" name="uploaded">
		<input type="hidden" name="w" value="'.$_REQUEST['w'].'">
		<input type="hidden" name="u" value="'.$_REQUEST['u'].'">
		<input type="hidden" name="uploadType" value="image">
		<input type="hidden" name="location" value="'.$location.'">
		<input type="submit" value="Upload">
		</form>';
		
	return $str;

}

function renderItemFilesForm($item,$location){

	$str='';

	$fcount=count($item['files']);
		
	for($f=0; $f<$fcount; $f++){
	
		$str.= '<span class="file"><small><a href="javascript:queryRenameFile(\''.$_REQUEST['w'].'\',\''.$_REQUEST['u'].'\',\''.$location.'\',\''.$item['files'][$f]['id'].'\',\''.$item['files'][$f]['name'].'\')">'.$item['files'][$f]['name'].'</a>';
		
		$str.= '<a href="javascript:queryDeleteFile(\''.$_REQUEST['w'].'\',\''.$_REQUEST['u'].'\',\''.$location.'\',\''.$item['files'][$f]['id'].'\')">';
		$str.= '<img src="img/Actions-edit-delete-icon.png" style="vertical-align:top;"></a>';
		
		$str.= '</small></span>';
	
	}
	
	$str.= '<form action="query.php" method="post" enctype="multipart/form-data" class="uploader"  autocomplete="off">
		<small>File upload:</small>
		<input type="file" name="uploaded">
		<input type="hidden" name="w" value="'.$_REQUEST['w'].'">
		<input type="hidden" name="u" value="'.$_REQUEST['u'].'">
		<input type="hidden" name="uploadType" value="file">
		<input type="hidden" name="location" value="'.$location.'">
		<input type="submit" value="Upload">
		</form>';

	return $str;

}

function renderItemUsersForm($item,$allusers,$location){

	$str='<small>Users</small><br>';
	
	foreach($allusers as $user => $values) if($values['type']=='editor'){
	
		$isin=false;
		
		foreach($item['users'] as $iemail => $ivalues) if($values['email']==$iemail && $ivalues['mayEdit']==1) $isin=true;
	
		if($values['email']!=$allusers[$_REQUEST['u']]['email']){
	
			if($isin) $str.='<a href="javascript:queryKickUser(\''.$_REQUEST['w'].'\',\''.$_REQUEST['u'].'\',\''.$location.'\',\''.$values['email'].'\')">[x]</a>';
			else $str.='<a href="javascript:queryAddUser(\''.$_REQUEST['w'].'\',\''.$_REQUEST['u'].'\',\''.$location.'\',\''.$values['email'].'\')">[ ]</a>';
		
			$str.= ' <small>'.$values['email'].'</small><br>';	
			
		}
	
	}

	return $str;
	
}
?>