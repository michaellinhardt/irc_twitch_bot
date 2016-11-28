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

// DISLIKE dislike.txt
if (is_file(PATH_FROM_DISLIKE)) {
	$oFile = fopen(PATH_FROM_DISLIKE,"r");
	$aProfile = array();
	while (!feof($oFile)) {
		$sName = formatName(fgets($oFile, 4096));
		if (!empty($sName)) {
			// RECUPERE LA ZIC EN COURS
			$aVideo = $oPDO->query('SELECT * FROM youtube WHERE timePlay>0')->fetch(PDO::FETCH_ASSOC) ;
			if ($aVideo) {
				// VERIFIE SI IL EXISTE DEJA
				$iCount = $oPDO->query('SELECT COUNT(*) FROM dislike WHERE viewer="'.$sName.'" AND youtubeID='.$aVideo['ID'])->fetch(PDO::FETCH_COLUMN);
				if ($iCount=='0') {
					// VERIFIE LES GILS
					$aViewer = $oPDO->query('SELECT * FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC);
					if ($aViewer['gils']>=DISLIKE_GILS) {
						// DISLIKE LA VIDEO ET ENLEVE LES GILS
						$oPDO->query('INSERT INTO dislike SET viewer="'.$sName.'", youtubeID='.$aVideo['ID']);
						$oPDO->query('UPDAT viewer SET gils=gils-'.DISLIKE_GILS.' WHERE name="'.$sName.'"');
						dire('[YOUTUBE] '.$sName.', votre dislike est ajouté à la vidéo: '.$aVideo['titre']);
					}
				}
			}
		}
	}
	$oFile = fopen(PATH_FROM_DISLIKE,"w+");
	fclose($oFile);
}

// Parcours les ligne de youtubeAdd.txt
if (is_file(PATH_OVERLAY_YOUTUBE_ADD)) {
	$oFile = fopen(PATH_OVERLAY_YOUTUBE_ADD,"r");
	$aProfile = array();
	while (!feof($oFile)) {
		$sLine = fgets($oFile, 4096);
		// explose la ligne et stop si il manque auteur &| link
		$aData = explode(' ', $sLine) ;
	  
		// Récupére les donné et stop si le lien est invalide
		$sAuteur = formatName($aData[0]) ;
		if (!isset($aData[1])) $sLink = false ;
		else $sLink = trim(getYoutubeID($aData[1])) ;
		if (!isset($aData[2])) $sPlaylist = 'viewer' ;
		else $sPlaylist = $aData[2] ;
		$sPlaylist = trim(strtolower($sPlaylist));
		
		if ((isset($sAuteur)) && (!empty($sAuteur)) && ($sLink)) {
		  
			while(true){
				// Supprime les doublons de viewer si ils ont déjà était lus, pour que le songrequest sois accepté
				$oPDO->query('DELETE FROM youtube WHERE youtubeID="'.$sLink.'" AND playlist="viewer" AND played>0');
	
				// Vérifie que la musique existe pas déjà
				$iCount = $oPDO->query('SELECT COUNT(*) FROM youtube WHERE youtubeID="'.$sLink.'" AND playlist="viewer"')->fetch(PDO::FETCH_COLUMN);
				if (($iCount != '0') && ($sAuteur != 'Nestoyeur')) { $iADD = 0 ; break ; }

				// Récupére la page en HTML
				$sURL = 'http://www.youtube.com/embed/' . $sLink ;
				$sHTTP = file_get_contents($sURL) ;
	
				// Récupére le titre
				if (!preg_match( '#<title>(.*?) - YouTube</title>#si', $sHTTP, $sTitre )) break ;
				$sTitre = trim($sTitre[1]) ;
				if (!isset($sTitre)) break ;
	
				// Vérifie la longueur
				if (!preg_match( '#length_seconds":(.*?),#si', $sHTTP, $iDuree )) { echo ' toujours pas..' ; break ; }
				$iDuree = intval($iDuree[1]) ;
				if ((empty($iDuree)) || ($iDuree == 0)) break ;
	
				$sTitre = html_entity_decode($sTitre, ENT_QUOTES, 'UTF-8');
	
				$sDuree = secToHour($iDuree) ;
	
				// Vérifie les fond du viewer
				if ($sAuteur != 'Nestoyeur') {
					$aViewer = $oPDO->query('SELECT * FROM viewer WHERE name="'.$sAuteur.'"')->fetch(PDO::FETCH_ASSOC);
					if ((!$aViewer['gils']) || (intval($aViewer['gils'])<intval($iDuree))) {
						dire('dire [YOUTUBE] Vous n\'avez pas asse de gils '.$aViewer['name'].' ('.$aViewer['gils'].' sur '.$iDuree.' gils nécessaire)');
						break ;
					}
				}
				
				// Revérifie le doublon mais pas seulement pour playlist viewer cette fois
				$iCount = $oPDO->query('SELECT COUNT(*) FROM youtube WHERE youtubeID="'.$sLink.'"')->fetch(PDO::FETCH_COLUMN);
				if ($sPlaylist=='viewer') $iCount = '0' ;
	
				// met a jour le fichier
				if ($iCount != '0') {
					$iVerif = $oPDO->query('SELECT ID FROM youtube WHERE youtubeID="'.$sLink.'" AND playlist!="viewer"')->fetch(PDO::FETCH_COLUMN);
					if ($iVerif) $sWHERE = ' AND ID='.$iVerif ;
					else $sWHERE = '' ;
					$oPDO->query('UPDATE youtube SET playlist="'.$sPlaylist.'", lastMod="'.intval(microtime(true)).'" WHERE youtubeID="'.$sLink.'"'.$sWHERE);
					dire('dire [YOUTUBE] Mise à jour: '.$sLink.', Playlist: '.$sPlaylist);
				} else {
					// ajoute le fichier
					if ($sAuteur != 'Nestoyeur') $oPDO->query('UPDATE viewer SET gils=gils-'.intval($iDuree).' WHERE name="'.$sAuteur.'"') ;
					dire('dire [YOUTUBE] Ajout de '.$sAuteur.': '.$sTitre.' ['.$sDuree.'] '.$iDuree.' gils') ;
					$insert = $oPDO->prepare('INSERT INTO youtube SET auteur="'.strtolower($sAuteur).'", lastMod="'.intval(microtime(true)).'", playlist="'.$sPlaylist.'", played=0, youtubeID="'.$sLink.'", duree="'.$iDuree.'", titre=:titre') ;
					$insert->bindValue(':titre', $sTitre) ;
					$insert->execute();
					//$oPDO->query('INSERT INTO youtube SET auteur="'.strtolower($sAuteur).'", playlist="'.$sPlaylist.'", played=0, youtubeID="'.$sLink.'", duree="'.$iDuree.'", titre="'.$sTitre.'"');
				}
				
				// ajoute la playlist
				$iCountPlaylist = $oPDO->query('SELECT COUNT(*) FROM playlist WHERE playlist="'.$sPlaylist.'"')->fetch(PDO::FETCH_COLUMN);
				if ($iCountPlaylist == '0') {
					$oPDO->query('INSERT INTO playlist SET playlist="'.$sPlaylist.'", actual="1"');
					dire('dire [YOUTUBE] Playlist '.$sPlaylist.' créé');
				}
				
				break ;
			}
		}
	  
	}

	$oFile = fopen(PATH_OVERLAY_YOUTUBE_ADD,"w+");
	fclose($oFile);
}

$sOrder = getYoutubeStatus();
if ($sOrder == 'STOP') {
	$oPDO->query('UPDATE youtube SET timePlay=0, timeStop=0 WHERE ID>0');
	setYoutube('STOP', '', '0');
}


if ($sOrder == 'NEXT') {
	$oPDO->query('UPDATE youtube SET timePlay=0, timeStop=0, penalite=1 WHERE timePlay>0');
	setYoutubeStatus('PLAY');
	$sOrder = 'PLAY' ;
}

if ($sOrder == 'PLAY') {
	$iCount = $oPDO->query('SELECT COUNT(*) FROM youtube WHERE timePlay!=0 AND timeStop>'.intval(microtime(true)))->fetch(PDO::FETCH_COLUMN);
	// AUCUN MORCEAU EN COURT
	if ($iCount=='0') {
		// MET TOUT LES MORCEAU A STOP
		$oPDO->query('UPDATE youtube SET timePlay=0, timeStop=0 WHERE ID>0');
		// CONSTRUIT LA REQUETE PLAYLIST
		$aPlaylist = $oPDO->query('SELECT playlist FROM playlist WHERE actual=1')->fetchAll(PDO::FETCH_ASSOC);
		$sPlaylist = '' ;
		foreach($aPlaylist as $aValue) {
			if ($sPlaylist != '') $sPlaylist .= ' OR ' ;
			$sPlaylist .= 'playlist="'.$aValue['playlist'].'"' ;
		}
		// RECUPERE LE PLAYED LE PLUS HAUT
		$iPlayedMax = $oPDO->query('SELECT played FROM youtube WHERE '.$sPlaylist.' ORDER BY played DESC LIMIT 0,1')->fetch(PDO::FETCH_COLUMN);
		// REMET LE PLAYED A EGALITé POUR LES MORCEAU EN ATTENTE
		// SA EVITE QU'UNE VIEILLE PLAYLIST PASSE EN PRIORITé SI ELLE EST AJOUté
		$oPDO->query('UPDATE youtube SET played='.($iPlayedMax-1).' WHERE played<'.$iPlayedMax.' AND played>0');
		
		// VERIFIE SI C'EST AU TOUR DES VIEWER
		$aToken = $oPDO->query('SELECT stepNext FROM cooldown WHERE name="YOUTUBE_VIEWER_TURN"')->fetch(PDO::FETCH_ASSOC);
		if (!$aToken) { $oPDO->query('INSERT INTO cooldown SET stepNext=0, name="YOUTUBE_VIEWER_TURN"'); $aToken['stepNext'] = 0 ; }
		$iToken = intval($aToken['stepNext']) ;
		
		if ($iToken == 0) {
			// RECUPERE LES MORCEAUX A 0 LECTURE
			$aVideo = $oPDO->query('SELECT * FROM youtube WHERE played=0 ORDER BY ID ASC LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		} 
		$iNewToken = $iToken + 1 ;
		if ($iNewToken > 1) $oPDO->query('UPDATE cooldown SET stepNext="0" WHERE name="YOUTUBE_VIEWER_TURN"');
		else  $oPDO->query('UPDATE cooldown SET stepNext="'.$iNewToken.'" WHERE name="YOUTUBE_VIEWER_TURN"'); ;
		
		
		// SINON RECUPERE UN MORCEAU RANDOM
		if (!$aVideo) $aVideo = $oPDO->query('SELECT * FROM youtube WHERE '.$sPlaylist.' AND played>0 ORDER BY played ASC, RAND() LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		if ($iPlayedMax == $aVideo['played']) $iPlayedMax++ ;
		
		// COMPTE LES DISLIKE
		$iDislike = $oPDO->query('SELECT COUNT(*) FROM dislike, viewer WHERE dislike.youtubeID='.$aVideo['ID'].' AND dislike.viewer=viewer.name AND viewer.isOnline>'.(microtrue()-60))->fetch(PDO::FETCH_COLUMN);
		
		// FAKE LA LECTURE SI LE FICHIER A UNE PENALITE OU UN DISLIKE
		if (($aVideo['penalite']!=0) || ($iDislike!='0')) {
			$oPDO->query('UPDATE youtube SET played='.$iPlayedMax.', penalite=0 WHERE ID='.$aVideo['ID']);
		// SINON ACTIVE LA LECTURE
		} else {
			// ACTIVE LA LECTURE
			$oPDO->query('UPDATE youtube SET timePlay='.intval(microtime(true)).', timeStop='.(intval(microtime(true))+intval($aVideo['duree'])).', played='.$iPlayedMax.' WHERE ID='.$aVideo['ID']);
			// ECRIS LINFO
			setYoutube('PLAY', $aVideo['youtubeID'], '0');
		}
		
	} else {
		$aVideo = $oPDO->query('SELECT * FROM youtube WHERE timePlay!=0 AND timeStop>'.intval(microtime(true)))->fetch(PDO::FETCH_ASSOC);
		$iTime = intval(microtime(true)) - intval($aVideo['timePlay']) ;
		if ($iTime<5) $iTime = -2 ;
		setYoutube('PLAY', $aVideo['youtubeID'], ($iTime+2));
	}
}

function getYoutubeID($sLink) {
	// VERIFIE LE LIEN
	$sLink = $sLink . '&' ;
	if (!preg_match( "#v=(.*?)&#si", $sLink, $sIDYoutube )) {
		$sIDYoutube = explode('.be/', $sLink) ;
		if (!isset($sIDYoutube[1])) {
			return false ;
		} else {
			$sIDYoutube[1] = substr($sIDYoutube[1], 0, -1) ;
		}
	}

	$sIDYoutube = $sIDYoutube[1] ;

	// DEGAGE LE ? dans le cas d'un tinyurl youtu.be/->354433543?list=654<-
	$aIDYoutube = explode('?', $sIDYoutube);
	if (isset($aIDYoutube[1])) $sIDYoutube = $aIDYoutube[0] ;
	return $sIDYoutube ;
}

$oPDO = null ;