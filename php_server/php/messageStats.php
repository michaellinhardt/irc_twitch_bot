<?php

// FOLLOWER ONLINE
$iOnline = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur" AND isOnline>'.(microtrue()-60))->fetch(PDO::FETCH_COLUMN);
$iPickup = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur" AND follower=0 AND isOnline>'.(microtrue()-60))->fetch(PDO::FETCH_COLUMN);
$iFollower = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur" AND follower=1 AND isOnline>'.(microtrue()-60))->fetch(PDO::FETCH_COLUMN);
if (!$iPickup) $iPickup = 0 ;
$iPickupPercent = round(($iPickup / $iOnline) * 100);
$iFollowerPercent = 100 - $iPickupPercent ;
$sMSG = 'En ce moment il y a '.$iOnline.' viewer online dont '.$iFollower.' follower ('.$iFollowerPercent.'%).. merci a vous *^*' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=1') ;

// LE PLUS RICHE
$aRiche = $oPDO->query('SELECT * FROM viewer WHERE name!="nestbot" AND name!="nestoyeur" ORDER BY gils DESC LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
$aRicheOnline = $oPDO->query('SELECT * FROM viewer WHERE name!="nestbot" AND name!="nestoyeur" AND isOnline>'.(microtrue()-60).' ORDER BY gils DESC LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
$sMSG = $aRiche['name'].' est le plus riche avec '.$aRiche['gils'].' gils. '.$aRicheOnline['name'].' est le plus riche online avec '.$aRicheOnline['gils'].' gils.' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=2') ;

// QUIZZ TOTAL
$iQuizzTotal = $oPDO->query('SELECT COUNT(*) FROM quizz')->fetch(PDO::FETCH_COLUMN);
$iQuizzDistinct = $oPDO->query('SELECT COUNT(DISTINCT auteur) FROM quizz WHERE ID>0')->fetch(PDO::FETCH_COLUMN);
$sMSG = 'Le quizz contiens '.$iQuizzTotal.' questions provenant de '.$iQuizzDistinct.' auteurs différents.' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=3') ;

// ROBOT UPTIME
$iUptime = $oPDO->query('SELECT timetotal FROM viewer WHERE name="nestbot"')->fetch(PDO::FETCH_COLUMN);
$iHour = round($iUptime/60) ;
$sMSG = 'Nestbot à passé '.$iUptime.' minutes sur le stream, soit '.$iHour.' heures.' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=4') ;

// STREAM UPTIME
$iUptime = $oPDO->query('SELECT timetotal FROM viewer WHERE name="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iHour = round($iUptime/60) ;
$sMSG = 'Depuis le reset, Nestoyeur a passé '.$iHour.' heures sur le stream.' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=5') ;

// MOYENNE DE VISIONNAGE
$iTotalTime = $oPDO->query('SELECT SUM(timetotal) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iTotalViewer = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iMoyenne = round($iTotalTime/$iTotalViewer) ;
$sMSG = 'Sur '.$iTotalViewer.' viewer, '.$iTotalTime.' minutes de présence soit une moyenne de '.$iMoyenne.' minutes par viewer ('.round($iMoyenne/60).' heures).' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=6') ;

// MOYENNE DE GILS
$iTotalTime = $oPDO->query('SELECT SUM(gils) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iTotalViewer = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iMoyenne = round($iTotalTime/$iTotalViewer) ;
$sMSG = 'Sur les '.$iTotalViewer.' viewer il y à un total de '.$iTotalTime.' gils, soit une moyenne de '.$iMoyenne.' gils par viewer.' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=7') ;

// YOUTUBE
$iYoutubeViewer = $oPDO->query('SELECT COUNT(*) FROM youtube WHERE playlist="viewer"')->fetch(PDO::FETCH_COLUMN);
$iYoutubeReal = $oPDO->query('SELECT COUNT(*) FROM youtube WHERE playlist!="viewer"')->fetch(PDO::FETCH_COLUMN);
$iDislike = $oPDO->query('SELECT COUNT(DISTINCT youtubeID) FROM dislike')->fetch(PDO::FETCH_COLUMN);
$sMSG = $iYoutubeViewer.' vidéos youtube joué par les viewer, '.$iYoutubeReal.' vidéos youtube dans la playlist et '.$iDislike.' vidéos dislike par les viewer' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=8') ;

// MOYENNE DES LEVEL
$iTotalLevel = $oPDO->query('SELECT SUM(level) FROM viewer WHERE name!="nestbot" AND name!="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iMoyenne = round($iTotalLevel/$iTotalViewer) ;
$iTotalTOP = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE level>='.$iMoyenne.' AND name!="nestbot" AND name!="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iTotalBOT = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE level<'.$iMoyenne.' AND name!="nestbot" AND name!="nestoyeur"')->fetch(PDO::FETCH_COLUMN);
$iTopMoyenne = round(($iTotalTOP/$iTotalViewer)*100) ;
$iBotMoyenne = 100 - $iTopMoyenne ;
$sMSG = $iMoyenne.' est la moyenne de level, '.$iTotalBOT.' viewer se trouvent en dessous ('.$iBotMoyenne.'%) et '.$iTotalTOP.' se trouvent au dessus ('.$iTopMoyenne.'%).' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=9') ;

// NOMBRE DE JOUEUR AU QUIZZ
$iQuizzPlayer = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE scoreQuizz>0')->fetch(PDO::FETCH_COLUMN);
$sMSG = $iQuizzPlayer . ' personnes ont participé au !quizz cette semaine.' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=10') ;

// NOMBRE DE JOUEUR AU PVE
$iPVEPlayer = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE scorePVE>0')->fetch(PDO::FETCH_COLUMN);
$sMSG = $iPVEPlayer . ' personnes ont participé au !kill cette semaine.' ;
$oPDO->query('UPDATE message SET message="'.$sMSG.'" WHERE ID=11') ;