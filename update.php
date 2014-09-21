<?php

date_default_timezone_set('UTC');

require_once("wakaHelper.php");
require_once("renderHelper.php");
require_once("image.php");
require_once("imagesHelper.php");


$dateNow=date("Y-m-d H:i:s");

if(wakaExists($_REQUEST['w'])){

	$waka=readWaka($_REQUEST['w']);
	
	if(isset($waka['users'][$_REQUEST['u']]['type'])) {
		$user['type']=$waka['users'][$_REQUEST['u']]['type'];
		$user['email']=$waka['users'][$_REQUEST['u']]['email'];
	}else{
		$user['type']='public';
		$user['email']='notset';
	}
	
	$out=array();
	
	if(strnatcmp( $waka['dateUsersTouched'] , $_REQUEST['d'])>0) {
	
		//echo $waka['starter']['dateTouched'].' > '.$_REQUEST['d'];
	
		$c=count($out);
	
		$out[$c]['html']=renderHead($waka,$user);
		$out[$c]['location']='head';
		$out[$c]['type']='changed';
	
	} 
	
	if(strnatcmp( $waka['starter']['dateContentTouched'] , $_REQUEST['d'])>0) {
	
		$c=count($out);
		$out[$c]['html']=renderItemContent($waka['starter'],'starter');
		$out[$c]['location']='starter_content';
		$out[$c]['type']='changed';
		
		$c=count($out);
		$out[$c]['html']=renderItemContentForm($waka['starter'],'starter');
		$out[$c]['location']='starter_content_form';
		$out[$c]['type']='changed';
	
	}
	
	if(strnatcmp( $waka['starter']['dateImagesTouched'] , $_REQUEST['d'])>0) {
	
		$c=count($out);
		$out[$c]['html']=renderItemImages($waka['starter'],'starter');
		$out[$c]['location']='starter_images';
		$out[$c]['type']='changed';
		
		$c=count($out);
		$out[$c]['html']=renderItemImagesForm($waka['starter'],'starter');
		$out[$c]['location']='starter_images_form';
		$out[$c]['type']='changed';
	
	}
	
	if(strnatcmp( $waka['starter']['dateFilesTouched'] , $_REQUEST['d'])>0) {
	
		$c=count($out);
		$out[$c]['html']=renderItemFiles($waka['starter'],'starter');
		$out[$c]['location']='starter_files';
		$out[$c]['type']='changed';
		
		$c=count($out);
		$out[$c]['html']=renderItemFilesForm($waka['starter'],'starter');
		$out[$c]['location']='starter_files_form';
		$out[$c]['type']='changed';
	
	}
	
	if($user['type']=='editor'){
	
		if(strnatcmp($waka['drafts'][$_REQUEST['u']]['dateContentTouched'],$_REQUEST['d'])>0) {
	
			$c=count($out);
		
			$out[$c]['html']=renderItemContentForm($waka['drafts'][$_REQUEST['u']],'draft');
			$out[$c]['location']='draft_content_form';
			$out[$c]['type']='changed';
		
		}
	
		if(strnatcmp($waka['drafts'][$_REQUEST['u']]['dateImagesTouched'],$_REQUEST['d'])>0) {
	
			$c=count($out);
		
			$out[$c]['html']=renderItemImagesForm($waka['drafts'][$_REQUEST['u']],'draft');
			$out[$c]['location']='draft_images_form';
			$out[$c]['type']='changed';
		
		}
	
		if(strnatcmp($waka['drafts'][$_REQUEST['u']]['dateFilesTouched'],$_REQUEST['d'])>0) {
	
			$c=count($out);
		
			$out[$c]['html']=renderItemFilesForm($waka['drafts'][$_REQUEST['u']],'draft');
			$out[$c]['location']='draft_files_form';
			$out[$c]['type']='changed';
		
		}
	
	}
	
	$oldplist=listToArray($_REQUEST['p']);
	
	$postcount=count($waka['posts']);
	
	for($p=0; $p<$postcount; $p++){
	
		for($p2=0; $p2<count($oldplist); $p2++) if($oldplist[$p2]==$waka['posts'][$p]['id']) array_splice($oldplist,$p2,1);
		
		if(strnatcmp($waka['posts'][$p]['dateCreated'],$_REQUEST['d'])>0){
		
			$c=count($out);
			$out[$c]['html']=renderPost($waka['posts'][$p],$waka['users'],$user);
			$out[$c]['location']='post_'.$waka['posts'][$p]['id'];
			$out[$c]['type']='new';
		
		}else{
		
			if(strnatcmp($waka['posts'][$p]['dateContentTouched'],$_REQUEST['d'])>0){
	
				$c=count($out);
				$out[$c]['html']=renderItemContent($waka['posts'][$p],'post_'.$waka['posts'][$p]['id']);
				$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_content';
				$out[$c]['type']='changed';
				
				if($user['type']=='editor' && userMayEdit($user['email'],$waka['posts'][$p]['users'])){
				
					$c=count($out);
					$out[$c]['html']=renderItemContentForm($waka['posts'][$p],'post_'.$waka['posts'][$p]['id']);
					$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_content_form';
					$out[$c]['type']='changed';
				
				}
			
			}
		
			if(strnatcmp($waka['posts'][$p]['dateImagesTouched'],$_REQUEST['d'])>0){
	
				$c=count($out);
				$out[$c]['html']=renderItemImages($waka['posts'][$p],'post_'.$waka['posts'][$p]['id']);
				$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_images';
				$out[$c]['type']='changed';
				
				if($user['type']=='editor' && userMayEdit($user['email'],$waka['posts'][$p]['users'])){
				
					$c=count($out);
					$out[$c]['html']=renderItemImagesForm($waka['posts'][$p],'post_'.$waka['posts'][$p]['id']);
					$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_images_form';
					$out[$c]['type']='changed';
					
				}
			
			}
		
			if(strnatcmp($waka['posts'][$p]['dateFilesTouched'],$_REQUEST['d'])>0){
	
				$c=count($out);
				$out[$c]['html']=renderItemFiles($waka['posts'][$p],'post_'.$waka['posts'][$p]['id']);
				$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_files';
				$out[$c]['type']='changed';
				
				if($user['type']=='editor' && userMayEdit($user['email'],$waka['posts'][$p]['users'])){
				
					$c=count($out);
					$out[$c]['html']=renderItemFilesForm($waka['posts'][$p],'post_'.$waka['posts'][$p]['id']);
					$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_files_form';
					$out[$c]['type']='changed';
					
				}
			
			}
		
			if(strnatcmp($waka['posts'][$p]['dateUsersTouched'],$_REQUEST['d'])>0){
	
				$c=count($out);
				$out[$c]['html']=renderAvatar($waka['posts'][$p]);
				$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_avatar';
				$out[$c]['type']='changed';
				
				if($user['type']=='editor' && userMayEdit($user['email'],$waka['posts'][$p]['users'])){
				
					$c=count($out);
					$out[$c]['html']=renderItemUsersForm($waka['posts'][$p],$waka['users'],'post_'.$waka['posts'][$p]['id']);
					$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_users_form';
					$out[$c]['type']='changed';
					
				}
			
			}
		
			if(strnatcmp($waka['posts'][$p]['dateCommentsTouched'],$_REQUEST['d'])>0){
	
				$c=count($out);
				$out[$c]['html']=renderItemComments($waka['posts'][$p],$user,'post_'.$waka['posts'][$p]['id']);
				$out[$c]['location']='post_'.$waka['posts'][$p]['id'].'_comments';
				$out[$c]['type']='changed';
			
			}
			
		}
	
	}
	
	for($p2=0; $p2<count($oldplist); $p2++){
	
		$out[$p2]['location']='post_'.$oldplist[$p2];
		$out[$p2]['type']='deleted';
	
	}
	
	//writeWaka($waka,$_REQUEST['w']);
	
	$data['out']=$out;
	$data['dateNow']=$dateNow;
	
	echo json_encode($data);

}



?>