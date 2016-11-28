$(document).ready(function(){
   sYoutubeAction = 'STOP' ;
   sYoutubeID = '' ;
   sPlaylist = '' ;
   sPlaylist0 = '' ;
   sPlaylist1 = '' ;
   initForm();
   initLink();
   buildYoutube();
});

function youtubeVerif() {
	if (iFollowerStatus==0) {
		// APPELLé APRES CHAQUE LECTURE DU FICHIER youtube.txt
		var sYoutubeActionNew = $('#youtubeAction').val();
		var sYoutubeIDNew = $('#youtubeID').val() ;
		
		if ((sYoutubeActionNew == 'STOP') && (sYoutubeAction!='STOP')) { sYoutubeAction = 'STOP' ; sYoutubeID = '' ; changeYoutube('') ; }
		if ((sYoutubeActionNew == 'PLAY') && (sYoutubeID != sYoutubeIDNew)) {
			changeYoutube(sYoutubeIDNew);
			sYoutubeID = sYoutubeIDNew ;
		}
	}
}

function forceYoutube(link, start) {
	if (start == null) { start = 0 }
	
	var split = link.split("youtube.com/watch?v=");
	if (split[1] == undefined) var split = link.split("youtu.be/watch?v=");
	if (split[1] == undefined) return false ;
	
	var split = split[1] ;
	var split2 = split.split("&") ;
	
	if (split2[1] == undefined) var link = '//www.youtube.com/embed/' + split ;
	else var link = '//www.youtube.com/embed/' + split2[0] + '?' + split2[1] ;
	
	var test = link.split('?') ;
	
	//if (test[1] == undefined) var link = link + '?autoplay=1&vq=tiny&sound=0&start=' + start ;
	//else var link = link + '&autoplay=1&vq=tiny&sound=0&start=' + start ;
	
	if (test[1] == undefined) var link = link + '?autoplay=1&vq=tiny&sound=0&start=' + start ;
	else var link = link + '&autoplay=1&vq=tiny&sound=0&start=' + start ;
	
	$('#youtube').attr('src', link);
}

function followSong(link, start) {
	
}

function changeYoutube(link) {
	
	$('#playing').attr('href', 'http://youtube.com/watch?v='+link).html('http://youtube.com/watch?v='+link);
	
	var link = '//www.youtube.com/embed/' + link ;
	
	var link = link + '?autoplay=1&vq=tiny&sound=0&start=' + $('#youtubeTime').val() ;
	$('#youtube').attr('src', link);
}

function initForm() {
	$('#youtubeAdd').submit(function(){
		var addPlaylist = $('#youtubeAddNewPlaylist').val();
		if (addPlaylist=='') addPlaylist = $('#youtubeAddPlaylist').val() ;
		var youtubeLink = $('#youtubeAddLink').val();
		ajaxAsync('./youtubeAjax.php?add=1&auteur=Nestoyeur&playlist='+addPlaylist+'&link='+youtubeLink, {});
		$('#youtubeAddLink').val('');
		$('#youtubeAddNewPlaylist').val('');
		return false ;
	});
	
	$('#playlistAdd').submit(function(){
		var addPlaylist = $('#playlistAddList').val();
		ajaxAsync('./youtubeAjax.php?playlistAdd=1&playlist='+addPlaylist, {});
		buildYoutube();
		return false ;
	});
	
	$('#playlistDel').submit(function(){
		var delPlaylist = $('#playlistDelList').val();
		ajaxAsync('./youtubeAjax.php?playlistDel=1&playlist='+delPlaylist, {});
		buildYoutube();
		return false ;
	});
	
	$('#youtubeForce').submit(function(){
		var link = $('#youtubeForceLink').val();
		ajaxAsync('./youtubeAjax.php?stop=1', {});
		sYoutubeAction = 'STOP' ;
		forceYoutube(link);
		return false ;
	});
}

function initLink(){
	$('#play').click(function(){
		ajaxAsync('./youtubeAjax.php?play=1', {});
		return false ;
	});
	$('#stop').click(function(){
		ajaxAsync('./youtubeAjax.php?stop=1', {});
		changeYoutube('') ;
		return false ;
	});
	$('#next').click(function(){
		ajaxAsync('./youtubeAjax.php?next=1', {});
		return false ;
	});
	$('#del').click(function(){
		ajaxAsync('./youtubeAjax.php?del=1', {});
		return false ;
	});
}

function buildYoutube() {
	ajaxAsync('./youtubeAjax.php?getPlaylist=1', {}, 'buildYoutubeOK'); 
}

function buildYoutubeOK(data) {
	aPlaylist = eval('('+data+')');
	sPlaylist = '' ;
	sPlaylist1 = '' ;
	sPlaylist0 = '' ;
	for (var i in aPlaylist) {
		aValue = aPlaylist[i] ;
		sPlaylist += '<option value="'+aValue['playlist']+'">'+aValue['playlist']+'</option>' ;
		if (aValue['actual']=='1') sPlaylist1 += '<option value="'+aValue['playlist']+'">'+aValue['playlist']+'</option>' ;
		else sPlaylist0 += '<option value="'+aValue['playlist']+'">'+aValue['playlist']+'</option>' ;
	}
	$('#youtubeAddPlaylist').html(sPlaylist);
	$('#playlistAddList').html(sPlaylist0);
	$('#playlistDelList').html(sPlaylist1);
}