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

$sEvent = getEvent();

if ($sEvent == 'RESET') $oPDO->query('DELETE FROM cooldown WHERE name="EVENT"') ;
if ($sEvent == 'STOP') {
	$oPDO->query('UPDATE quizz SET actual=0, used=1 WHERE actual=1');
	$oPDO->query('UPDATE pve SET actual=0, used=1 WHERE actual=1');
	$oPDO->query('DELETE FROM cooldown WHERE name="EVENT"') ;
	$oPDO->query('INSERT INTO cooldown SET name="EVENT", status="10", stepNext="pause"');
	setEvent('') ;
}

$aCooldown = $oPDO->query('SELECT * FROM cooldown WHERE name="EVENT"')->fetch(PDO::FETCH_ASSOC);
if (!$aCooldown) {
	$oPDO->query('INSERT INTO cooldown SET name="EVENT", status=0, timerNext="'.(microtrue()+(COOLDOWN_PVE*60)).'", stepNext="PVE"') ;
	$aCooldown['name'] = 'EVENT' ; 
	$aCooldown['status'] = '0' ;
	$aCooldown['timerNext'] = (microtrue()+(COOLDOWN_PVE*60)) ;
	$aCooldown['stepNext'] = 'PVE' ;
}


if ($aCooldown['stepNext'] == 'PVE') { include('./php/pve.php'); }
if ($aCooldown['stepNext'] == 'QUIZZ') { include('./php/quizz.php'); }

purgeQuizzPve();

include('./php/eventClose.php');

$oPDO = null ;