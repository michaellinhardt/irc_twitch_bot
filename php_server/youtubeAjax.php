<?php
set_time_limit(0);
error_reporting(E_ALL);
session_start(); 
date_default_timezone_set('Europe/Paris');


// Initialise PDO pour les requete sql
include './DatabaseConfig.php' ;
include('./php/config.php');
include('./php/functions.php');

// ENVOIE DES PLAYLIST
if (isset($_GET['getPlaylist'])) {
	$aPlaylist = $oPDO->query('SELECT playlist, actual FROM playlist WHERE ID>0 ORDER BY playlist ASC')->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($aPlaylist);
	$oPDO = null ;
	exit();
}

// probléme sur certain url le v= est transmis comme une variable get dans le lien
if (isset($_GET['v'])) $_GET['link'] = 'http://www.youtube.com/watch?v=' . $_GET['v'] ;

// AJOUTE MUSIQUE
if (isset($_GET['add'])) {
	$oFile = fopen(PATH_OVERLAY_YOUTUBE_ADD,"a+");
	fputs($oFile, 'Nestoyeur ' . $_GET['link'] . ' ' . $_GET['playlist'] . "\r\n");
	fclose($oFile);
	$oPDO = null ;
	echo 'Nestoyeur ' . $_GET['link'] . ' ' . $_GET['playlist'] ;
	exit();
}

// PLAYLIST ADD
if (isset($_GET['playlistAdd'])) {
	$oPDO->query('UPDATE playlist SET actual=1 WHERE playlist="'.trim(strtolower($_GET['playlist'])).'"');
	echo 'UPDATE playlist SET actual=1 WHERE playlist="'.$_GET['playlist'].'"' ;
	$oPDO = null ;
	exit();
}

// PLAYLIST DEL
if (isset($_GET['playlistDel'])) {
	$oPDO->query('UPDATE playlist SET actual=0 WHERE playlist="'.trim(strtolower($_GET['playlist'])).'"');
	echo 'UPDATE playlist SET actual=0 WHERE playlist="'.$_GET['playlist'].'"' ;
	$oPDO = null ;
	exit();
}

// PLAY
if (isset($_GET['play'])) {
	$oFile = fopen(PATH_OVERLAY_YOUTUBE_STATUS,"w+");
	fputs($oFile, 'PLAY');
	fclose($oFile);
	$oPDO = null ;
	exit();
}

// STOP
if (isset($_GET['stop'])) {
	$oFile = fopen(PATH_OVERLAY_YOUTUBE_STATUS,"w+");
	fputs($oFile, 'STOP');
	fclose($oFile);
	$oPDO = null ;
	exit();
}

// NEXT
if (isset($_GET['next'])) {
	$oFile = fopen(PATH_OVERLAY_YOUTUBE_STATUS,"w+");
	fputs($oFile, 'NEXT');
	fclose($oFile);
	$oPDO = null ;
	exit();
}

// DEL
if (isset($_GET['del'])) {
	$aPlay = $oPDO->query('SELECT * FROM youtube WHERE timePlay!=0')->fetch(PDO::FETCH_ASSOC);
	$oPDO->query('DELETE FROM youtube WHERE timePlay!=0');
	$oPDO->query('DELETE FROM dislike WHERE youtubeID='.intval($aPlay['ID']));
	$oPDO = null ;
	exit();
}

$oPDO = null ;