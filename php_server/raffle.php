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

$sVerif = file_get_contents(PATH_FROM_VARS) ;
if (preg_match('#raffle.pos#si', $sVerif)) exit() ;

// Parcours les variable mirc et trie les donnée
$i = 0 ;
$oFile = fopen(PATH_FROM_VARS,"r");
$aProfile = array();
while (!feof($oFile)) {
	$sLine = fgets($oFile, 4096);
	
	// RECUPERE LE PRIX DU TICKET
	if (preg_match('#raffle\.gils#si', $sLine)) {
		$aLine = explode(' ', $sLine) ;
		$iTicketGils = intval($aLine[1]) ;
	}
	
	// RECUPERE LE MAX DE TICKET
	if (preg_match('#raffle\.max#si', $sLine)) {
		$aLine = explode(' ', $sLine) ;
		$iTicketMax = intval($aLine[1]) ;
	}

	// RECUPERE LES TICKET
	if (preg_match('#raffle\.viewer\.#si', $sLine)) {
		$aLine = explode('raffle.viewer.', $sLine);
		$aLine = explode(' ', $aLine[1]) ;
		$sName = formatName($aLine[0]) ;
		$aViewer[$sName] = intval($aLine[1]) ;
		$i++ ;
	}
}
fclose($oFile) ;

if ($i==0) exit();

$iTotalTicket = 0 ;
$iTotalGils = 0 ;

foreach( $aViewer as $sName => $iTicket) {
	$sName = formatName($sName) ;
	$iGils = $oPDO->query('SELECT gils FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_COLUMN);
	
	// EMPECHE LE DEPACEMENT DE TICKET
	if ($iTicket > $iTicketMax) $iTicket = $iTicketMax ;
	
	// VERIFIE LE PRIX
	$iPrice = $iTicketGils * $iTicket ;
	if ($iPrice>$iGils) {
		$iTicket = floor($iGils/$iTicketGils) ;
		$iPrice = $iTicketGils * $iTicket ;
	}
	
	// Valide les ticket
	if ($iTicket>0) {
		$iTotalTicket = $iTotalTicket + $iTicket ;
		$iTotalGils = $iTotalGils + $iPrice ;
		$aRaffle[$sName] = $iTicket ;
		$oPDO->query('UPDATE viewer SET gils=gils-'.$iPrice.' WHERE name="'.$sName.'"') ;
	}
}

// Annonce la fermeture de la raffle
dire('[RAFFLE] Un total de '.$iTotalTicket.' ont était acheté pour la somme de '.$iTotalGils.' gils.');

// Prépare les var pour sortir le gagnant
$iPos = 0 ;
$iPoolStart = 0 ;
$iPoolEnd = 0 ;
$iTicketWin = rand(1, $iTotalTicket);

unlink(PATH_TO_RAFFLE);

raffle('set %raffle.pos 0');

while (true) {

	foreach( $aRaffle as $sName => $iTicket) {
		$sName = formatName($sName) ;
		// régle le pool start
		if ($iPoolStart == 0) $iPoolStart = 1 ;
		else $iPoolStart = $iPoolEnd + 1 ;
		// régle le pool end
		$iPoolEnd = $iPoolStart + $iTicket - 1 ;
		// vérifie si gagnant
		if (($iTicketWin >= $iPoolStart) && ($iTicketWin <= $iPoolEnd)) {
			// TICKET GAGNANT
			$iPos++ ;
			raffle('set %raffle.winner.'.$iPos.' '.$sName);
			echo 'win '.$iPos.': '. $sName.'<br />' ;
			unset($aRaffle[$sName]) ;
			// RESET LES PARAM ET BREAK LA RECHERCHE
			$iTotalTicket = $iTotalTicket - $iTicket ;
			$iPoolStart = 0 ;
			$iPoolEnd = 0 ;
			$iTicketWin = rand(1, $iTotalTicket);
			break ;
		}
	}
	if ((!$aRaffle) || (empty($aRaffle))) break ;
}

raffle('raffleResult') ;

$oPDO = null ;