<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title>Recherche avancée des cartes HearthStone</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<?php
set_time_limit(0);
error_reporting(E_ALL);
session_start(); 
//sleep(1);

// R�cup�re la date
date_default_timezone_set('Europe/Paris');
$sToday = date('d/m/Y') ;

// Initialise PDO pour les requete sql
include './DatabaseConfig.php' ;
include('./php/config.php') ;
include('./php/functions.php');

$oPDO->query('TRUNCATE hearthstonecards');

$iStart = 0 ;
$iPage = 1 ;
$iCount = 0 ;
$aList = array();
while (true) {
	$sHTML = file_get_contents('http://hearthstone.judgehype.com/cartes-hearthstone/0,1,1,1,1,1,1,1,1,0,1,0,1,0,1,0,0,1/nom/liste/'.$iPage.'/');
	
	preg_match('#<table width=100% border=0 cellpadding=2 cellspacing=1 class=contenu>(.*?)<table width=100% border=0 cellpadding=0 cellspacing=0 summary="" class="contenu">#si', $sHTML, $aHTML);
	preg_match_all('#<tr>(.*?)</tr>#si', $aHTML[1], $aCards);
	$aCards = $aCards[1] ;
	foreach($aCards as $aValue) { 
		if ($iStart!=0) {
			preg_match_all('#<td(.*?)</td>#si', $aValue, $aLine) ;
			$aLine = $aLine[0] ;
			//var_dump($aLine);
			
			preg_match('#href="(.*?)"#si', $aLine[1], $aList[$iStart]['lien']);
			$aList[$iStart]['lien'] =  $aList[$iStart]['lien'][1];
			
			preg_match('#\)" onmouseout="hideddrivetip\(\)">(.*?)</a>#si', $aLine[1], $aList[$iStart]['name']);
			$aList[$iStart]['name'] =  $aList[$iStart]['name'][1];
			
			preg_match('#class=dbtableclair>(.*?)</td>#si', $aLine[2], $aList[$iStart]['genre']);
			if (empty($aList[$iStart]['genre'])) preg_match('#class=dbtablefonce>(.*?)</td>#si', $aLine[2], $aList[$iStart]['genre']);
			$aList[$iStart]['genre'] =  $aList[$iStart]['genre'][1];
			$aList[$iStart]['genre']= str_replace('<br><i class=smallcontenu>', ' ', $aList[$iStart]['genre']);
			$aList[$iStart]['genre']= str_replace('</i>', '', $aList[$iStart]['genre']);

			preg_match('#">(.*?)</font>#si', $aLine[3], $aList[$iStart]['rare']);
			$aList[$iStart]['rare'] =  $aList[$iStart]['rare'][1];
			
			preg_match('#class=dbtableclair>(.*?)</td>#si', $aLine[4], $aList[$iStart]['classe']);
			if (empty($aList[$iStart]['classe'])) preg_match('#class=dbtablefonce>(.*?)</td>#si', $aLine[4], $aList[$iStart]['classe']);
			$aList[$iStart]['classe'] =  $aList[$iStart]['classe'][1];
			
			preg_match('#class=dbtableclair>(.*?)<#si', $aLine[5], $aList[$iStart]['cout']);
			if (empty($aList[$iStart]['cout'])) preg_match('#class=dbtablefonce>(.*?)<#si', $aLine[5], $aList[$iStart]['cout']);
			$aList[$iStart]['cout'] =  trim($aList[$iStart]['cout'][1]);
			
			preg_match('#class=dbtableclair>(.*?)<#si', $aLine[6], $aList[$iStart]['atk']);
			if (empty($aList[$iStart]['atk'])) preg_match('#class=dbtablefonce>(.*?)<#si', $aLine[6], $aList[$iStart]['atk']);
			$aList[$iStart]['atk'] =  trim($aList[$iStart]['atk'][1]);
			
			preg_match('#class=dbtableclair>(.*?)<#si', $aLine[7], $aList[$iStart]['vie']);
			if (empty($aList[$iStart]['vie'])) preg_match('#class=dbtablefonce>(.*?)<#si', $aLine[7], $aList[$iStart]['vie']);
			$aList[$iStart]['vie'] =  trim($aList[$iStart]['vie'][1]);

			preg_match('#>(.*?)<#si', $aLine[8], $aList[$iStart]['description']);
			$aList[$iStart]['description'] =  trim(str_replace(',', '', $aList[$iStart]['description'][1]));
			
			if (preg_match('#,#si', $aList[$iStart]['name'])) unset($aList[$iStart]);
			else {
				$iCount++ ;
				$oPDO->query('INSERT INTO hearthstonecards SET lien="'.utf8_encode($aList[$iStart]['lien']).'", name="'.utf8_encode($aList[$iStart]['name']).'", genre="'.utf8_encode($aList[$iStart]['genre']).'", rare="'.utf8_encode($aList[$iStart]['rare']).'", classe="'.utf8_encode($aList[$iStart]['classe']).'", cout="'.utf8_encode($aList[$iStart]['cout']).'", atk="'.utf8_encode($aList[$iStart]['atk']).'", vie="'.utf8_encode($aList[$iStart]['vie']).'", description="'.utf8_encode($aList[$iStart]['description']).'"');
			}
			
			
			
		}
		$iStart++ ;
	}
	$iStart = 0 ;
	$iPage++;
	if ($iPage==12) break ;
	//sleep(1);
}
var_dump($iCount);
var_dump($aList);

$oPDO = null ;