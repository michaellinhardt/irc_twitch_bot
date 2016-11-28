$(document).ready(function(){
	timerRead = '';
	iReadDelay = 3000 ;
	readFile();
	readCount();
});

function readCount(){
	// outilsCount.txt
	$.get('./overlay/outilsCount.txt', function(data) {    
	    $('#outilsCount').html(data);
	    $('#incLegsValue').val($('.incLegsValue').html());
	    $('#incSetValue').val($('.incSetValue').html());
	    //$('#xpHXP').val($('.xpHXP').html());
	    //$('#xpHMAX').val($('.xpHMAX').html());
	    //$('#xpHLevel').val($('.xpHLevel').html());
	    $('#LH').html($('.LH').html());
	    $('#SH').html($('.SH').html());
	    $('#XH').html($('.XH').html());
	});
}


function readFile() {
	// CLEAR LES TIMEOUT
	clearTimeout(timerRead);
	
	// create outilsCount
	$.get('./overlay/layout.txt', function(data) {    
		 ajaxAsync('./outilsCount.php', {}, 'readCount' );
	});
	
	// layout.txt
	$.get('./overlay/layout.txt', function(data) {    
	    $('#layout').html(data);
	});
	
	// newFollower.txt
	$.get('./overlay/newFollower2.txt', function(data) { 
	    $('#readFollower').append(data);
	    ajaxAsync('./delNewFollower.php', {} );
	});
    followerVerif();
	
	// youtube.txt
	$.get('./overlay/youtube.txt', function(data) { 
	    $('#readYoutube').html(data);
	    youtubeVerif();
	});
	
	timerRead = setTimeout('readFile()', iReadDelay);
}