<?php
$iMicro = microtime(true);
set_time_limit(0);
error_reporting(E_ALL);
session_start(); 
//sleep(1);

$sToday = date('d-m-Y') ;

// Récupére la date
date_default_timezone_set('Europe/Paris');

// Initialise PDO pour les requete sql
include './DatabaseConfig.php' ;
include('./php/config.php') ;
include('./php/functions.php');

$aList = array() ;
$aFollowers = json_decode(file_get_contents('https://api.twitch.tv/kraken/channels/'.TWITCH_CHAN.'/follows?direction=DESC&limit='.NEW_FOLLOW_LIMIT.'&offset=0')) ;
if (!empty($aFollowers->follows)){
    array_push($aList, $aFollowers->follows) ;
}

// RECUPERE LA LISTE ET RECONSTRUIT LA VARIABLE
$aFollowerList = $oPDO->query('SELECT * FROM follower WHERE ID>0')->fetchAll(PDO::FETCH_ASSOC);
foreach($aFollowerList as $aValue) {
	$sName = formatName($aValue['name']) ;
	$aFollower[$sName] = 1 ;
}

// AJOUTE LES NOUVEAU FOLLOW ET MET A JOUR LES ANCIEN
$sNEW = '' ;
foreach($aList as $aList2){
    foreach($aList2 as $aValue){
	    $sName = formatName($aValue->user->name);
	    // TRAVAIL UNIQUEMENT SI LA LIGNE EXISTE DANS LA TABLE VIEWER
	    $iCount = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_COLUMN);
	    if ($iCount == '1') {
		    if (!isset($aFollower[$sName])) {
		    	$aFollower[$sName] = 1 ;
		    	// NOUVEAU FOLLOWER
		    	if ($sNEW != '') $sNEW .= ',' ;
		    	$sNEW .= '("'.formatName($sName).'")' ;
		    	echo 'INSERT: ' . $sName . '<br />' ;
		    	$oPDO->query('UPDATE viewer SET follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	$iPrice = rand(0,7);
		    	if (strtolower($sName) == 'pandacool75') $iPrice = 7 ;
		    	if (strtolower($sName) == 'tiignon') $iPrice = 7 ;
		    	newFollower($sName, $iPrice);
		    	setCountFollower(intval(getCountFollower())+1) ;
		    	$i7Day = microtrue()+(60*60*24*7) ;
		    	if ($iPrice == 0) $oPDO->query('UPDATE viewer SET level=level+40, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 1) $oPDO->query('UPDATE viewer SET level=level+20, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 2) $oPDO->query('UPDATE viewer SET level=level+10, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 3) $oPDO->query('UPDATE viewer SET fortuneGils='.$i7Day.', follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 4) $oPDO->query('UPDATE viewer SET gils=gils+4000, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 5) $oPDO->query('UPDATE viewer SET gils=gils+2000, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 6) $oPDO->query('UPDATE viewer SET gils=gils+1000, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 7) $oPDO->query('UPDATE viewer SET fortuneXp='.$i7Day.', follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    } else {
		    	$aFollower[$sName] = 1 ;
		    	// ANCIEN FOLLOWER
		    	$oPDO->query('UPDATE viewer SET follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	echo 'UPDATE: ' . $sName . '<br />' ;
		    }
	    }
    }
}
$oPDO->query('INSERT INTO follower (name) VALUE ' . $sNEW) ;

// RECUPERE LE TOKEN DE LA SESSION PRECEDENTE

echo '<br />' ;

$aViewer = $oPDO->query('SELECT name FROM viewer WHERE followVerif=0 ORDER BY ID DESC LIMIT 0,'.OLD_FOLLOW_LIMIT)->fetchAll(PDO::FETCH_COLUMN);

if ($aViewer) {

	foreach( $aViewer as $aValue ) {
		$sName = $aValue ;
		$aFollow = json_decode(file_get_contents('https://api.twitch.tv/kraken/users/'.strtolower($sName).'/follows/channels/'.strtolower(TWITCH_CHAN))) ;
		if (!$aFollow) json_decode(file_get_contents('https://api.twitch.tv/kraken/users/'.strtolower($sName).'/follows/channels/'.strtolower(TWITCH_CHAN))) ;
		if (!$aFollow) {
			// NE FOLLOW PAS
			$oPDO->query('UPDATE viewer SET follower=0, followVerif=1, followerLast="00-00-0000" WHERE name="'.$sName.'"');
			echo $sName.' NE FOLLOW PAS<br />' ;
		} else {
			// FOLLOW LE STREAM
			$oPDO->query('UPDATE viewer SET follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"');
			echo 'ici' ;
			echo $sName.' FOLLOW<br />' ;
			if (!isset($aFollower[$sName])) {
				// NOUVEAU FOLLOWER
				$aFollower[$sName] = 1 ;
				$oPDO->query('INSERT INTO follower SET name="'.$sName.'"');
				echo $sName.' NEW FOLLOW<br />' ;
				$iPrice = rand(0,7);
		    	if (strtolower($sName) == 'pandacool75') $iPrice = 3 ;
		    	if (strtolower($sName) == 'tiignon') $iPrice = 3 ;
		    	newFollower($sName, $iPrice);
		    	setCountFollower(intval(getCountFollower())+1) ;
		    	$i7Day = microtrue()+(60*60*24*7) ;
		    	if ($iPrice == 0) $oPDO->query('UPDATE viewer SET level=level+40, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 1) $oPDO->query('UPDATE viewer SET level=level+20, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 2) $oPDO->query('UPDATE viewer SET level=level+10, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 3) $oPDO->query('UPDATE viewer SET fortuneGils='.$i7Day.', follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 4) $oPDO->query('UPDATE viewer SET gils=gils+4000, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 5) $oPDO->query('UPDATE viewer SET gils=gils+2000, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 6) $oPDO->query('UPDATE viewer SET gils=gils+1000, follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
		    	if ($iPrice == 7) $oPDO->query('UPDATE viewer SET fortuneXp='.$i7Day.', follower=1, followVerif=1, followerLast="'.$sToday.'" WHERE name="'.$sName.'"') ;
			}
		}
	}
} else {
	$oPDO->query('UPDATE viewer SET followVerif=0 WHERE followerLast!="'.$sToday.'"');
}

$oPDO = null ;

echo '<br />' . (microtime(true)-$iMicro) ; 