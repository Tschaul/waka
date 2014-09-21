function insert(aTag, eTag, formname) {
  var input = document.forms[formname].elements['content'];
  input.focus();
  /* für Internet Explorer */
  if(typeof document.selection != 'undefined') {
    /* Einfügen des Formatierungscodes */
    var range = document.selection.createRange();
    var insText = range.text;
    range.text = aTag + insText + eTag;
    /* Anpassen der Cursorposition */
    range = document.selection.createRange();
    if (insText.length == 0) {
      range.move('character', -eTag.length);
    } else {
      range.moveStart('character', aTag.length + insText.length + eTag.length);      
    }
    range.select();
  }
  /* für neuere auf Gecko basierende Browser */
  else if(typeof input.selectionStart != 'undefined')
  {
    /* Einfügen des Formatierungscodes */
    var start = input.selectionStart;
    var end = input.selectionEnd;
    var insText = input.value.substring(start, end);
    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
    /* Anpassen der Cursorposition */
    var pos;
    if (insText.length == 0) {
      pos = start + aTag.length;
    } else {
      pos = start + aTag.length + insText.length + eTag.length;
    }
    input.selectionStart = pos;
    input.selectionEnd = pos;
  }
  /* für die übrigen Browser */
  else
  {
    /* Abfrage der Einfügeposition */
    var pos;
    var re = new RegExp('^[0-9]{0,3}$');
    while(!re.test(pos)) {
      pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
    }
    if(pos > input.value.length) {
      pos = input.value.length;
    }
    /* Einfügen des Formatierungscodes */
    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
  }
}


function copyToClipboard(text){
  window.prompt ("Copy to clipboard: Ctrl+C, Enter", text);
}

function switchHead(){
	$("#head").slideToggle("fast");
}

function switchStarterEdit(){
	$("#starter_display").slideToggle("fast");
	$("#starter_form").slideToggle("fast");
	$("#starterEditSwitch_on").toggle();
	$("#starterEditSwitch_off").toggle();
}

function switchDraftEdit(){
	$("#draft_small").slideToggle("fast");
	$("#draft_form").slideToggle("fast");
	$("#draftEditSwitch_on").toggle();
	$("#draftEditSwitch_off").toggle();
	
	
}

function switchPostEdit(id){
	//alert("#post_content_"+id);
	$("#post_"+id+"_display").slideToggle("fast");
	$("#post_"+id+"_form").slideToggle("fast");
	$("#postEditSwitch_"+id+"_on").toggle();
	$("#postEditSwitch_"+id+"_off").toggle();
}

function queryAddUser(w,u,location,email){

	$.ajax({
		type: "POST",
		url: "query.php",
		data: "action=addUser&w="+w+"&u="+u+"&location="+location+"&email="+email,
		success: function(msg){
			//alert( "Data Saved: " + msg );
			update();
		}
	});

}

function queryKickUser(w,u,location,email){

	$.ajax({
		type: "POST",
		url: "query.php",
		data: "action=kickUser&w="+w+"&u="+u+"&location="+location+"&email="+email,
		success: function(msg){
			//alert( "Data Saved: " + msg );
			update();
		}
	});

}


function queryDeletePost(w,u,id){

	var answer = confirm("Delete post?")
	if (answer){

		$.ajax({
			type: "POST",
			url: "query.php",
			data: "action=deletePost&w="+w+"&u="+u+"&id="+id,
			success: function(msg){
	   			//alert( "Data Saved: " + msg );
				update();
	   		}
		});

	}

}

function queryRenameFile(w,u,location,fid,oldname){

	var newname = prompt("Put in new name!",oldname)
	if(newname){
	
		$.ajax({
			type: "POST",
			url: "query.php",
			data: "action=renameFile&w="+w+"&u="+u+"&fid="+fid+"&location="+location+"&newname="+newname,
			success: function(msg){
	   			//alert( "Data Saved: " + msg );
				update();
	   		}
		});
	
	
	}



}

function queryDeleteFile(w,u,location,fid){

	//alert('test');

	var answer = confirm("Delete file?")
	if (answer){

		$.ajax({
			type: "POST",
			url: "query.php",
			data: "action=deleteFile&w="+w+"&u="+u+"&location="+location+"&fid="+fid,
			success: function(msg){
	   			//alert( "Data Saved: " + msg );
				update();
	   		}
		});

	}

}

function queryDeleteImage(w,u,location,iid){

	var answer = confirm("Delete image?")
	if (answer){

		$.ajax({
			type: "POST",
			url: "query.php",
			data: "action=deleteImage&w="+w+"&u="+u+"&location="+location+"&iid="+iid,
			success: function(msg){
	   			//alert( "Data Saved: " + msg );
				update();
	   		}
		});

	}

}

function queryDeleteComment(w,u,id,cid){

	var answer = confirm("Delete comment?")
	if (answer){

		$.ajax({
			type: "POST",
			url: "query.php",
			data: "action=comment_delete&w="+w+"&u="+u+"&id="+id+"&cid="+cid,
			success: function(msg){
	   			//alert( "Data Saved: " + msg );
				update();
	   		}
		});

	}

}


function queryMarkAsRead(location){

	$.ajax({
		type: "POST",
		url: "query.php",
		data: "action=markAsRead&w="+wkey+"&u="+ukey+"&location="+location,
		success: function(msg){
			//alert(msg);
		}
	});

}

function maketime(){

	$('.time').each(function(index){
	
		//alert(1000*$(this).attr('utctime'));
		$(this).html(dateFormat(new Date(1000*$(this).attr('utctime')), "ddd, mmmm d, yyyy H:MM"));
	
	});


}

function minmaxPost(id){

	if($("#post_small_gradient_"+id).css("display")=='none'){
		minimizePost(id);
	}else{
		maximizePost(id);
	}


}

var smallsize="113px";

var markedcolor="#E9FFD8";

function minimizePost(id){

	if($("#post_"+id+"_form").css("display")=='block'){
		switchPostEdit(id);
	}

	$("#postEditSwitch_"+id+"_on").toggle();
	
	$("#post_"+id).attr("natheight",$("#post_"+id).height()).animate({
		height: smallsize
	}, 500 );
	
	$("#post_wrapper_"+id).attr("natheight",$("#post_wrapper_"+id).height()).animate({
		height: smallsize
	}, 500 );

	$("#post_small_gradient_"+id).css("display","block");

}

function fastminPost(id){

	if($("#post_"+id+"_form").css("display")=='block'){
		switchPostEdit(id);
	}

	$("#postEditSwitch_"+id+"_on").toggle();
	
	$("#post_"+id).attr("natheight",$("#post_"+id).height()).css("height",smallsize);
	
	$("#post_wrapper_"+id).attr("natheight",$("#post_wrapper_"+id).height()).css("height",smallsize);

	//alert($("#post_"+id).attr('unread'));
	
	if( $("#post_"+id).attr('unread')=='yes' ){
		$("#post_"+id).css("background-color",markedcolor);
	}
	$("#post_small_gradient_"+id).css("display","block");
	
	

}

function maximizePost(id){

	$("#postEditSwitch_"+id+"_on").toggle();

	$("#post_"+id).css("background-color","#FFFFFF");
	
	$("#post_"+id).animate({
		height: $("#post_"+id).attr("natheight")+"px"
	}, 500 ,function() {
		$(this).css("height","100%");
	});
	
	$("#post_wrapper_"+id).animate({
		height: $("#post_wrapper_"+id).attr("natheight")+"px"
	}, 500 ,function() {
		$(this).css("height","100%");
	});
	
	$("#post_small_gradient_"+id).css("display","none");
	
	$("#post_"+id).attr('unread',"no");
	
	queryMarkAsRead("post_"+id);
	
}

function update(){

	//alert("w="+wkey+"&u="+ukey+"&d="+dateString+"&p=");
	
	var p='';
	
	$('.post').each(function(){
	
		//alert( $(this).attr('id').substr(5) );
		
		p=p+$(this).attr('id').substr(5)+';';
	
	});

	$.ajax({
		type: "GET",
		url: "update.php",
		data: "w="+wkey+"&u="+ukey+"&d="+dateString+"&p="+p,
		success: function(msg){
			//alert( msg );
			
			//$('#targetDiv').html(msg);
			
			//window.location.reload()
			
			var data=eval('(' + msg + ')');
			
			var out=data.out;
			
			dateString=data.dateNow;
			
			for(i=0; i<out.length; i++){
			
				//alert(out[i].location+" "+out[i].type+"\n"+out[i].debug+"\n"+out[i].location.substr(0,4));
			
				if(out[i].location=='head' && out[i].type=='changed'){
			
					var swi=($(".head").css("display")=='none');
				
					$('#head').html(out[i].html);
			
			
				}else if(out[i].type=='changed'){
				
					//alert( '#'+out[i].location+"\n\n"+out[i].html);
				
					$('#'+out[i].location).html(out[i].html);
					
					if(out[i].location.indexOf('post')===0 &&  out[i].location.split("_")[2]!="users" && out[i].location.split("_")[2]!="avatar"){
					
						//alert($("#post_small_gradient_"+out[i].location.split("_")[1]).css("display"));
					
						if($("#post_small_gradient_"+out[i].location.split("_")[1]).css("display")=='none'){
						
						
						}else{
							
							$("#post_"+out[i].location.split("_")[1]).attr("unread","yes");
							$("#post_"+out[i].location.split("_")[1]).css("background",markedcolor);
							
						}
						
					}
				
				}
				
				if(out[i].location.indexOf('post')===0 && out[i].type=='new'){
				
					str='<div class="post" id="'+out[i].location+'">'+out[i].html+'</div>';
				
					if($('.post').length) $('.post').first().before(str);
					else $('.draft').after(str);
					
					//fastminPost(out[i].location.substr(5));
					
					//alert();
					
				}else if(out[i].location.indexOf('post')===0 && out[i].type=='deleted'){
				
					$('#'+out[i].location).remove();
					
					//alert();
					
				}
			
			}
			
			//tinyMCE.init(mceInit);
			
			
			//$('form').bind('form-pre-serialize', function(e) {
				//tinyMCE.triggerSave();
			//});

			
			initiate();
			
			maketime();
			
		}
	});


}


