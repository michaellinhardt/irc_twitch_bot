<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
set_time_limit(0);
error_reporting(E_ALL);
session_start(); 
//sleep(1);

// Récupére la date
date_default_timezone_set('Europe/Paris');
$sToday = date('d/m/Y') ;
$sFolder = '/hearthstone/' ;
// Initialise PDO pour les requete sql
include './DatabaseConfig.php' ;
include('./php/config.php') ;
include('./php/functions.php');

// CARTE-PENDU
$sLines = '' ;
$aCards = $oPDO->query('SELECT * FROM hearthstonecards WHERE rare="Rare" OR rare="Légendaire" ORDER BY ID ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach($aCards as $aValue) {
	$sQuestion = 'Retrouvez le nom de cette carte: ' ;
	$i = 0 ;
	$iStars = 0 ;
	while (true) {
		if (!isset($aValue['name'][$i])) break ;
		if ($aValue['name'][$i] == ' ' ) { $sQuestion .= ' ' ; $i++ ; $iStars = 0 ; }
		if ($aValue['name'][$i] == '-' ) { $sQuestion .= '-' ; $i++ ; $iStars = 0 ; }
		else {
			if ($iStars < 2 ) {
				$sQuestion .= mb_substr($aValue['name'], $i, 1, 'UTF-8') ;
				$i++ ;
				$iStars++ ;
			} else {
				$sQuestion .= '_' ;
				$i++ ;
				$iStars = 0 ;
			}
		}
	}
	$sReponse = trim($aValue['name']) ;
	$sInfo = '! La carte: http://hearthstone.judgehype.com/'. $aValue['lien'] ;
	$sIndice= substr($aValue['name'], 0, floor(strlen($aValue['name'])/2)).'..';
	$sLines .= ','.$sQuestion.','.$sIndice.','.$sReponse.','.$sInfo.',Nestoyeur' . "\r\n";
}
echo $sLines ;

$oFile = fopen(PATH_QUIZZ_FILE . $sFolder . 'cartes-pendu.txt',"w+");
fputs($oFile, $sLines);
fclose($oFile);

$oPDO = null ;