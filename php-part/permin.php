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


// personne online
if (!is_file(PATH_FROM_ONLINE)) { $oPDO = null ; exit() ; }

// Nombre onlien
$iNBOnline = count(file(PATH_FROM_ONLINE));
$iXPPermin = $iNBOnline * PERMIN_XP ;
if ($iXPPermin<25) $iXPPermin = 25 ;

// Parcours les variable mirc et trie les donnée
$oFile = fopen(PATH_FROM_ONLINE,"r");
while (!feof($oFile)) {
	$sName = formatName(fgets($oFile, 4096));
	if ($sName) {
		// AJOUTE OU MET A JOUR LE VIEWER AVEC SON isOnline
		$aAccount = $oPDO->query('SELECT * FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC);
		if (!$aAccount) {
			// VERIFIE SI ANCIEN VIEWER
			$aOld = $oPDO->query('SELECT * FROM viewerOld where name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC);
			if (!empty($aOld)) $oPDO->query('INSERT INTO viewer SET name="'.$sName.'", isOnline="'.microtrue().'", xpreq="'.XPReq($aOld['level']).'", gils="'.PERMIN_GILS.'", xpcurrent="'.$iXPPermin.'", timetotal='.$aOld['timetotal'].', level='.$aOld['level']);
			else $oPDO->query('INSERT INTO viewer SET name="'.$sName.'", isOnline="'.microtrue().'", xpreq="'.XPReq(1).'", gils="'.PERMIN_GILS.'", xpcurrent="'.$iXPPermin.'", timetotal=1');
		} else {
			$oPDO->query('UPDATE viewer SET isOnline="'.microtrue().'", xpcurrent=xpcurrent+'.calcXPTotal($aAccount, $iXPPermin).', gils=gils+'.calcGILSTotal($aAccount, PERMIN_GILS).', timetotal=timetotal+1 WHERE name="'.$sName.'"');
		}
	}
}
fclose($oFile) ;

//unlink(PATH_FROM_ONLINE);

include('./php/levelup.php');

include('./php/toplist.php');

$oPDO = null ;