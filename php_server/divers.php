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

include('./php/message.php');
include('./php/viewercmd.php');

$oPDO = null ;