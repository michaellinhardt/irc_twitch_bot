<?php
set_time_limit(0);
error_reporting(E_ALL);
session_start(); 
//sleep(1);

// Récupére la date
date_default_timezone_set('Europe/Paris');
$sToday = date('d/m/Y') ;

// Initialise PDO pour les requete sql
include './DatabaseConfig.php' ;
include('./php/config.php') ;
include('./php/functions.php');

$sFile = 'c:/wamp/www/nesstream/mirc/oldVars.ini' ;

// Parcours les variable mirc et trie les donnée
$oFile = fopen($sFile,"r");
$aProfile = array();
while (!feof($oFile)) {
	$sLine = fgets($oFile, 4096);

	// RECUPERE LE TIME TOTAL
	if (preg_match('#profile\.time\.total\.#si', $sLine)) {
		$aLine = explode('profile.time.total.', $sLine);
		$aLine = explode(' ', $aLine[1]) ;
		$sName = formatName($aLine[0]) ;
		$aProfile[$sName]['timetotal'] = intval($aLine[1]) ;
	}
	// RECUPERE LE LEVEL
	if (preg_match('#profile\.para\.level\.#si', $sLine)) {
		$aLine = explode('profile.para.level.', $sLine);
		$aLine = explode(' ', $aLine[1]) ;
		$sName = formatName($aLine[0]) ;
		$aProfile[$sName]['level'] = intval($aLine[1]) ;
	}
	// RECUPERE LE REBORN
	if (preg_match('#profile\.reborn\.#si', $sLine)) {
		$aLine = explode('profile.reborn.', $sLine);
		$aLine = explode(' ', $aLine[1]) ;
		$sName = formatName($aLine[0]) ;
		$aProfile[$sName]['reborn'] = intval($aLine[1]) ;
	}
}
fclose($oFile) ;


$oPDO->query('TRUNCATE viewer');
$oPDO->query('TRUNCATE viewerOld');
$oPDO->query('TRUNCATE follower');
$oPDO->query('TRUNCATE dislike');
$oPDO->query('TRUNCATE message_viewer');
$oPDO->query('TRUNCATE cooldown');

$iCount = 0 ;
foreach($aProfile as $sName => $aValue) {
	if ($aValue['timetotal']>30) {
		$iCount++ ;
		if ((isset($aValue['reborn'])) && ($aValue['reborn']==2)) { $aValue['level'] = 100 + ceil($aValue['level']/2) ; }
		$oPDO->query('INSERT INTO viewerOld SET name="'.$sName.'", isOnline="'.microtrue().'", timetotal="'.$aValue['timetotal'].'", level="'.$aValue['level'].'", xpreq="'.XPReq($aValue['level']).'"');
	}
}
echo '<br >';


var_export($iCount);
var_dump($aProfile);

$oPDO = null ;