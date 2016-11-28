$(document).ready(function(){
	timerFollowerStatus = '';
	currentSong = '' ;
	iFollowerStatusDelay = 19000 ;
	iFollowerStatus = 0 ;
	iSongRotation = 0 ;
	iPrice = '7' ;
	//$('#followerBoxName').html('Nestoyeur');
	//alertFollow('Nestoyeur');
});

function followerVerif() {
	if (iFollowerStatus==0) {
		$('#readFollower').find('.newFollower').each(function(){
			iPrice = $(this).find('.newFollowerPrice').html() ;
			var sName = $(this).find('.name').html() ;
			$(this).remove() ;
			$('#followerBoxName').html(sName);
			alertFollow(sName);
			return false ;
		});
	}
}

function alertFollow(sName) {
	//alert(iPrice);
	iFollowerStatus = 1 ;
	ajaxAsync('./sayPrice.php?name='+sName+'&price='+iPrice, {});
	clearTimeout(timerFollowerStatus);
	sYoutubeID = '' ;
	songRotation() ;
	$('#followerBox').fadeIn();
	$('.the_wheel').fadeIn() ;
	resetWheel();
	powerSelected(2);
	startSpin(iPrice);
}

function resetFollowerStatus() {
	iFollowerStatus = 0 ;
	$('#followerBox').fadeOut();
	$('.the_wheel').fadeOut() ;
}

function songRotationOff() {
	iSongRotation++ ;
	var iStart = '' ;
	
	if (iSongRotation == 1) { iFollowerStatusDelay = 21000 ; iStart = 0 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	//if (iSongRotation == 1) { iFollowerStatusDelay = 20000 ; iStart = 154 ; currentSong = 'http://www.youtube.com/watch?v=oS6wfWu0JvA' ; } // I I FOLLOW YOU BABY
	if (iSongRotation == 2) { iFollowerStatusDelay = 23000 ; iStart = 22 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 3) { iFollowerStatusDelay = 23000 ; iStart = 46 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 4) { iFollowerStatusDelay = 23000 ; iStart = 69 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 5) { iFollowerStatusDelay = 23000 ; iStart = 93 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 6) { iFollowerStatusDelay = 23000 ; iStart = 117 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 7) { iFollowerStatusDelay = 23000 ; iStart = 141 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 8) { iFollowerStatusDelay = 23000 ; iStart = 170 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 9) { iFollowerStatusDelay = 23000 ; iStart = 231 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; iSongRotation = 0 ; } // FF
		
	timerFollowerStatus = setTimeout('resetFollowerStatus()', iFollowerStatusDelay);
	//forceYoutube(currentSong, iStart);
}

function songRotation() {
	iSongRotation++ ;
	var iStart = '' ;
	
	if (iSongRotation == 1) { iFollowerStatusDelay = 21000 ; iStart = 0 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	//if (iSongRotation == 1) { iFollowerStatusDelay = 20000 ; iStart = 154 ; currentSong = 'http://www.youtube.com/watch?v=oS6wfWu0JvA' ; } // I I FOLLOW YOU BABY
	if (iSongRotation == 2) { iFollowerStatusDelay = 23000 ; iStart = 22 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 3) { iFollowerStatusDelay = 23000 ; iStart = 46 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 4) { iFollowerStatusDelay = 23000 ; iStart = 69 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 5) { iFollowerStatusDelay = 23000 ; iStart = 93 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 6) { iFollowerStatusDelay = 23000 ; iStart = 117 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 7) { iFollowerStatusDelay = 23000 ; iStart = 141 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 8) { iFollowerStatusDelay = 23000 ; iStart = 170 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; } // FF
	if (iSongRotation == 9) { iFollowerStatusDelay = 23000 ; iStart = 231 ; currentSong = 'http://www.youtube.com/watch?v=kHx5hCVN26E' ; iSongRotation = 0 ; } // FF
		
	timerFollowerStatus = setTimeout('resetFollowerStatus()', iFollowerStatusDelay);
	forceYoutube(currentSong, iStart);
}