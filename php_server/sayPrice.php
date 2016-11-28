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

if ((isset($_GET['name'])) && (isset($_GET['price']))) {
	$iPrice = intval($_GET['price']);
	mircEXEC('timer 1 '.NEW_FOLLOWER_DIRE_TIMER.' dire [FOLLOWER] Merci pour ton follow '.formatName($_GET['name']).', tu remporte: '.$aPrice[$iPrice]);
}

$oPDO = null ;