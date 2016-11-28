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

$oPDO->query('DELETE FROM viewer WHERE follower=0 AND timetotal<10 AND isOnline<'.(microtrue()-60));

$oPDO = null ;