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

// LEGS
if (isset($_GET['legsCount'])) {
	$iMicro = intval(getMicroItem());
	$iCount = intval($_GET['legs']);
	
	$iSecondes = intval(microtrue()-$iMicro);
	$iHour = (($iSecondes/60)/60) ;
	$sHLegs = round($iCount / $iHour, 1) ;
	
	setCountLegs($iCount);
	setHLegs($sHLegs);
}

// SET
if (isset($_GET['setCount'])) {
	$iMicro = intval(getMicroItem());
	$iCount = intval($_GET['set']);
	
	$iSecondes = intval(microtrue()-$iMicro);
	$iHour = (($iSecondes/60)/60) ;
	$sHSet = round($iCount / $iHour, 1) ;
	
	setCountSet($iCount);
	setHSet($sHSet);
}

// LEGS & SET RESET
if (isset($_GET['resetFollower'])) {
	setCountFollower(0);
}

// LEGS & SET RESET
if (isset($_GET['itemReset'])) {
	$iMicro = intval(resetMicroItem());
	setCountLegs(0);
	setCountSet(0);
	setHLegs(0);
	setHSet(0);
}

// LEGS & SET RESET
if (isset($_GET['xpHReset'])) {
	$oPDO->query('TRUNCATE xph');
	setHXP(0);
}

// XPH
if (isset($_GET['xpH'])) {
	var_dump($_GET['max']);
	$iCount = $oPDO->query('SELECT COUNT(*) FROM xph WHERE ID=1')->fetch(PDO::FETCH_COLUMN);
	if ($iCount!='0') {
		// PAS LA PREMIERE ENTREe
		$iCount = $oPDO->query('SELECT COUNT(*) FROM xph WHERE level='.intval($_GET['level']).' AND ID>1')->fetch(PDO::FETCH_COLUMN);
		if ($iCount=='0') $oPDO->query('INSERT INTO xph SET level='.intval($_GET['level']).', xpcurrent='.intval($_GET['xp']).', xpreq='.intval($_GET['max']).', microtrue='.microtrue());
		else $oPDO->query('UPDATE xph SET xpcurrent='.intval($_GET['xp']).', xpreq='.intval($_GET['max']).', microtrue='.microtrue().' WHERE ID>1 AND level='.intval($_GET['level']));
		$oPDO->query('UPDATE xph SET xpcurrent=xpreq WHERE level<'.intval($_GET['level']).' AND ID>1') ;
	} else {
		$oPDO->query('INSERT INTO xph SET level='.intval($_GET['level']).', xpcurrent='.intval($_GET['xp']).', xpreq='.intval($_GET['max']).', microtrue='.microtrue());
		$oPDO->query('INSERT INTO xph SET level='.intval($_GET['level']).', xpcurrent='.intval($_GET['xp']).', xpreq='.intval($_GET['max']).', microtrue='.microtrue());
	}
}

// CALCULE LEGS & SET
$iMicro = intval(getMicroItem());
$iCountLegs = getCountLegs();
$iCountSet = getCountSet();
$iSecondes = (microtrue()-$iMicro);
$iHour = (($iSecondes/60)/60) ;
$sHLegs = round($iCountLegs / $iHour, 1) ;
$sHSet = round($iCountSet / $iHour, 1) ;
setHLegs($sHLegs);
setHSet($sHSet);

// CALCULE XP/H
$aXP = $oPDO->query('SELECT * FROM xph WHERE ID>0 ORDER BY ID ASC')->fetchAll(PDO::FETCH_ASSOC);
$iXP = 0 ;
if (!empty($aXP)) {
	foreach($aXP as $aValue){
		if ($aValue['ID']==1) {
			$iDECLevel = $aValue['level'] ;
			$iDECXP = $aValue['xpcurrent'] ;
			$iSTARTMicro = $aValue['microtrue'] ;
		} else {
			$iXP = $iXP + $aValue['xpcurrent'] ;
			if ($aValue['level']==$iDECLevel) $iXP = $iXP - $iDECXP ;
		}
		$iLASTMicro = $aValue['microtrue'] ;
	}
	$iTOTALMicro = $iLASTMicro - $iSTARTMicro ;
	$iHour = ($iTOTALMicro/60)/60 ;
	$sHXP = round($iXP / $iHour) ;
	if (strlen($sHXP)>3) $sHXP = substr($sHXP, 0, -3) . '.' . substr($sHXP, -3) ;
	setHXP($sHXP. 'M');
} else $sHXP = getHXP();

$sOverlay = '<span class="incLegsValue">'.$iCountLegs.'</span>
<span class="incSetValue">'.$iCountSet.'</span>
<span class="xpHXP">0</span>
<span class="xpHLevel">0</span>
<span class="xpHMAX">0</span>
<span class="LH">'.$sHLegs.'</span>
<span class="SH">'.$sHSet.'</span>
<span class="XH">'.$sHXP. 'M' .'</span>' ;

setOutilsCountOverlay($sOverlay);

$oPDO = null ;

