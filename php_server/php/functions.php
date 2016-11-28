<?php

// Fonction qui unifie les pseudo
function formatName($sName) { 
	$sName = ucfirst( strtolower( trim($sName) ) ) ;
	if ($sName!='') return $sName ;
	else return false ; 
}

function setCountLegs($iCount){
	$oFile = fopen(PATH_OVERLAY_COUNT_LEGS,"w+");
	fputs($oFile, $iCount);
	fclose($oFile);
	return intval(file_get_contents(PATH_OVERLAY_COUNT_LEGS));
}

function getCountLegs(){
	if (!is_file(PATH_OVERLAY_COUNT_LEGS)) {
		$oFile = fopen(PATH_OVERLAY_COUNT_LEGS,"w+");
		fputs($oFile, 0);
		fclose($oFile);
	}
	return intval(file_get_contents(PATH_OVERLAY_COUNT_LEGS));
}

function setCountSet($iCount){
	$oFile = fopen(PATH_OVERLAY_COUNT_SET,"w+");
	fputs($oFile, $iCount);
	fclose($oFile);
	return intval(file_get_contents(PATH_OVERLAY_COUNT_SET));
}

function getCountSet(){
	if (!is_file(PATH_OVERLAY_COUNT_SET)) {
		$oFile = fopen(PATH_OVERLAY_COUNT_SET,"w+");
		fputs($oFile, 0);
		fclose($oFile);
	}
	return intval(file_get_contents(PATH_OVERLAY_COUNT_SET));
}

function resetMicroItem(){
		$oFile = fopen(PATH_OVERLAY_MICRO_ITEM,"w+");
		fputs($oFile, microtrue());
		fclose($oFile);
	return intval(file_get_contents(PATH_OVERLAY_MICRO_ITEM));
}

function getMicroItem(){
	if (!is_file(PATH_OVERLAY_MICRO_ITEM)) {
		$oFile = fopen(PATH_OVERLAY_MICRO_ITEM,"w+");
		fputs($oFile, microtrue());
		fclose($oFile);
	}
	return intval(file_get_contents(PATH_OVERLAY_MICRO_ITEM));
}

function getHLegs(){
	if (!is_file(PATH_OVERLAY_H_LEGS)) {
		$oFile = fopen(PATH_OVERLAY_H_LEGS,"w+");
		fputs($oFile, 0);
		fclose($oFile);
	}
	return file_get_contents(PATH_OVERLAY_H_LEGS);
}

function getHSet(){
	if (!is_file(PATH_OVERLAY_H_SET)) {
		$oFile = fopen(PATH_OVERLAY_H_SET,"w+");
		fputs($oFile, 0);
		fclose($oFile);
	}
	return file_get_contents(PATH_OVERLAY_H_SET);
}

function getHXP(){
	if (!is_file(PATH_OVERLAY_H_XP)) {
		$oFile = fopen(PATH_OVERLAY_H_XP,"w+");
		fputs($oFile, 0);
		fclose($oFile);
	}
	return file_get_contents(PATH_OVERLAY_H_XP);
}

function setOutilsCountOverlay($sValue){
	$oFile = fopen(PATH_OVERLAY_OUTILS_COUNT,"w+");
	fputs($oFile, $sValue);
	fclose($oFile);
	return intval(file_get_contents(PATH_OVERLAY_OUTILS_COUNT));
}


function setHLegs($sValue){
	$oFile = fopen(PATH_OVERLAY_H_LEGS,"w+");
	fputs($oFile, $sValue);
	fclose($oFile);
	return intval(file_get_contents(PATH_OVERLAY_H_LEGS));
}

function setHSet($sValue){
	$oFile = fopen(PATH_OVERLAY_H_SET,"w+");
	fputs($oFile, $sValue);
	fclose($oFile);
	return intval(file_get_contents(PATH_OVERLAY_H_SET));
}

function setHXP($sValue){
	$oFile = fopen(PATH_OVERLAY_H_XP,"w+");
	fputs($oFile, $sValue);
	fclose($oFile);
	return intval(file_get_contents(PATH_OVERLAY_H_XP));
}

function microtrue() { return intval(microtime(true)); }

function XPReq($iLevel) {
	// ; k* ( ln(cosh( (n-a)/b)) - ln(cosh( -a/b )) + n/c )
	
	$iLevel = intval($iLevel) ;
	$iMultiplicateur = 10000 ;
	$iVeteran = 600 ;
	$iDifference = 60 ;
	$iPente = 30 ;
	
	$iStep1 = log(cosh(($iLevel-$iVeteran)/$iDifference)) ;
	$iStep2 = log(cosh(-$iVeteran/$iDifference)) ;
	$iStep3 = $iLevel/$iPente ;
	$iStep4 = $iStep1-$iStep2+$iStep3 ;
	$iXPReq = $iMultiplicateur*$iStep4 ;
	
	return intval($iXPReq) ;
}

// DUREE LITERAL
function secToHour($iSec) {
	$aSec = explode('.', $iSec) ;
	$iSec = intval($aSec[0]) ;
	$iSec = $iSec / 60 ;
	$aSec = explode('.', $iSec);
	$iHour = $aSec[0];
	$iMin = '0.' . $aSec[1] ;
	$iMin = 60 * floatval($iMin) ;
	$iHour = ($iHour<10) ? '0' . intval($iHour) : intval($iHour) ;
	$iMin = ($iMin<10) ? '0' . intval($iMin) : intval($iMin) ;
	return $iHour . ':' . $iMin ;
}

function dire($sMSG) {
	$sMSG = 'dire ' . $sMSG ;
	$oFile = fopen(PATH_TO_DIRE,"a+");
	fputs($oFile, $sMSG . "\r\n");
	fclose($oFile);
}

function mircEXEC($sMSG) {
	$oFile = fopen(PATH_TO_EXEC,"a+");
	fputs($oFile, $sMSG . "\r\n");
	fclose($oFile);
}

function raffle($sMSG) {
	$oFile = fopen(PATH_TO_RAFFLE,"a+");
	fputs($oFile, $sMSG . "\r\n");
	fclose($oFile);
}

function getTokenList() {
	if (!is_file(PATH_OVERLAY_TOKEN_TOPLIST)) {
		$oFile = fopen(PATH_OVERLAY_TOKEN_TOPLIST,"w+");
		fputs($oFile, "1 ".intval(microtime(true)));
		fclose($oFile);
	}
	return file_get_contents(PATH_OVERLAY_TOKEN_TOPLIST);
}

function setTokenList($iToken) {
	$oFile = fopen(PATH_OVERLAY_TOKEN_TOPLIST,"w+");
	fputs($oFile, $iToken);
	fclose($oFile);
}

function getEvent() {
	if (!is_file(PATH_FROM_EVENT)) return '' ;
	$sReturn = trim(file_get_contents(PATH_FROM_EVENT));
	unlink(PATH_FROM_EVENT) ;
	return $sReturn ;
}

function setEvent($sEVENT) {
	$oFile = fopen(PATH_FROM_EVENT,"w+");
	fputs($oFile, $sEVENT);
	fclose($oFile);
}

function setLayout($sHTML) {
	$oFile = fopen(PATH_OVERLAY_LAYOUT,"w+");
	fputs($oFile, $sHTML);
	fclose($oFile);
}

function newFollower($sName, $iPrice) {
	$oFile = fopen(PATH_OVERLAY_NEWFOLLOWER,"a+");
	fputs($oFile, '<li class="newFollower"><span class="newFollowerPrice">'.$iPrice.'</span><span class="name">'.$sName . '</span></li>' . "\r\n");
	fclose($oFile);
}

function getYoutubeStatus() {
	if (!is_file(PATH_OVERLAY_YOUTUBE_STATUS)) return 'STOP' ;
	return file_get_contents(PATH_OVERLAY_YOUTUBE_STATUS);
}

function setYoutubeStatus($sStatus) {
	$oFile = fopen(PATH_OVERLAY_YOUTUBE_STATUS,"w+");
	fputs($oFile, $sStatus);
	fclose($oFile);
}

function getCountFollower() {
	if (!is_file(PATH_OVERLAY_COUNT_FOLLOWER)) return 0 ;
	return file_get_contents(PATH_OVERLAY_COUNT_FOLLOWER);
}

function setCountFollower($iCount) {
	$oFile = fopen(PATH_OVERLAY_COUNT_FOLLOWER,"w+");
	fputs($oFile, $iCount);
	fclose($oFile);
}

function setMSG($sTITRE, $sMSG) {
	$oFile = fopen(PATH_OVERLAY_MSG_TITRE,"w+");
	fputs($oFile, $sTITRE);
	fclose($oFile);
	$oFile = fopen(PATH_OVERLAY_MSG_MSG,"w+");
	fputs($oFile, $sMSG);
	fclose($oFile);
}

function purgeQuizzPve(){
	$oFile = fopen(PATH_FROM_QUIZZ,"w+");
	fclose($oFile);
	$oFile = fopen(PATH_FROM_KILL,"w+");
	fclose($oFile);
}

function setYoutube($sStatus, $sID, $iSecondes) {
	$sRETURN = '<input type="hidden" id="youtubeAction" value="'.$sStatus.'" /><input type="hidden" id="youtubeID" value="'.$sID.'" /><input type="hidden" id="youtubeTime" value="'.$iSecondes.'" />' ;
	$oFile = fopen(PATH_OVERLAY_YOUTUBE,"w+");
	fputs($oFile, $sRETURN);
	fclose($oFile);
}

function calcXPTotal($aViewer, $iXP) {
	$iBonus = 0 ;
	if (($aViewer['winQuizz']>0) && ($aViewer['winQuizz']<1)) $iBonus = $iBonus + ($aViewer['winQuizz']*$iXP) ;
	if (($aViewer['winPVE']>0) && ($aViewer['winPVE']<1)) $iBonus = $iBonus + ($aViewer['winPVE']*$iXP) ;
	if ($aViewer['follower'] == 1) $iBonus = $iBonus + ($iXP*0.5) ;
	if ($aViewer['fortuneXp']>microtrue()) $iBonus = $iBonus + $iXP ;
	return ( $iBonus + $iXP ) ;
}

function calcGILSTotal($aViewer, $iGils) {
	$iBonus = 0 ;
	if ($aViewer['fortuneGils']>microtrue()) $iBonus = $iBonus + $iGils ;
	return ( $iBonus + $iGils ) ;
}