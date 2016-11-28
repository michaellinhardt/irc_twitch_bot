<?php
$iMSGStatus = 0 ;

// VERIFIE SI COMMANDE !MSG EN ATTENTE
// Parcours le fichier quizz.txt
if (is_file(PATH_FROM_MSG)) {
	$oFile = fopen(PATH_FROM_MSG,"r");
	while (!feof($oFile)) {
		$sLine = trim(fgets($oFile, 4096));
		// explose la ligne
		$aLine = explode(' ', $sLine);
		var_dump($aLine);
		if ((isset($aLine[0])) && (isset($aLine[1]))) {
			$sName = formatName($aLine[0]);
			$sMessage = trim(substr($sLine, strlen($sName)));
			// vérifie les gils
			$aViewer = $oPDO->query('SELECT * FROM viewer WHERE gils>='.VIEWER_MSG_PRICE.' AND name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC);
			var_dump($aViewer);
			if ($aViewer) {
				$oPDO->query('UPDATE viewer SET gils=gils-'.VIEWER_MSG_PRICE.' WHERE name="'.$sName.'"');
				$oSQL = $oPDO->prepare('INSERT INTO message_viewer SET name=:name, message=:message');
				$oSQL->bindValue(':name', $sName);
				$oSQL->bindValue(':message', $sMessage);
				$oSQL->execute();
				dire('[MSG] '.$sName.', votre message est ajouté à la liste d\'attente.');
			} else dire ('[MSG] '.$sName.', enregistrer un message coute 300 gils pour 5 minutes de diffusion.');
		}
	}
	$oFile = fopen(PATH_FROM_MSG,"w+");
	fclose($oFile);
}

// VERIFIE SI QUIZZ
$aQuizz = $oPDO->query('SELECT * FROM quizz WHERE actual=1')->fetch(PDO::FETCH_ASSOC);
if (($aQuizz) && ($iMSGStatus==0)) {
	// QUIZZ EN COURS
	$sMSG = $aQuizz['question'] ;
	$sTITRE = MSG_QUIZZ_TITRE ;
	$iMSGStatus = 1 ;
}

// VERIFIE SI PVE
$aPVE = $oPDO->query('SELECT * FROM pve WHERE actual=1')->fetch(PDO::FETCH_ASSOC);
if (($aPVE) && ($iMSGStatus==0)) {
	// QUIZZ EN COURS
	$sMSG = 'Nom du mob: '. $aPVE['name'] . ' , HP: '.$aPVE['pv'] ;
	$sTITRE = MSG_PVE_TITRE ;
	$iMSGStatus = 1 ;
}

// VERIFIE SI MESSAGE VIEWER ACTIF
$aMSGViewer = $oPDO->query('SELECT * FROM cooldown WHERE status=1 AND name="MESSAGE_VIEWER"')->fetch(PDO::FETCH_ASSOC);
var_dump($aMSGViewer);
if (($aMSGViewer) && ($iMSGStatus==0)) {
	if ($aMSGViewer['timerNext']<microtrue()) {
	// perimé
	echo 'ici' ;
		$oPDO->query('DELETE FROM cooldown WHERE ID='.$aMSGViewer['ID']);
		$oPDO->query('DELETE FROM message_viewer WHERE ID='.$aMSGViewer['stepNext']);
	} else {
	// valide
	echo 'la' ;
		$aMSG = $oPDO->query('SELECT * FROM message_viewer WHERE ID='.$aMSGViewer['stepNext'])->fetch(PDO::FETCH_ASSOC);
		$sMSG = $aMSG['message'] ;
		$sTITRE = '['.$aMSG['name'].'] ' ;
		$iMSGStatus = 1 ;
	}
}
if ((!$aMSGViewer) && ($iMSGStatus==0)) {
	// AUCUN MESSAGE EN COURS, VERIFI SI ON DOIS EN FAIRE
	$aMSG = $oPDO->query('SELECT * FROM message_viewer ORDER BY ID ASC LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
	if ($aMSG) {
		// DES MESSAGE EN ATTENTE
		$iMSGEnd = microtrue()+(COOLDOWN_VIEWER_MSG_LIFETIME*60);
		$iCount = $oPDO->query('SELECT COUNT(*) FROM cooldown WHERE name="EVENT" AND status=0 AND timerNext<'.$iMSGEnd)->fetch(PDO::FETCH_COLUMN);
		if ($iCount=='0') $oPDO->query('INSERT INTO cooldown SET status=1, name="MESSAGE_VIEWER", stepNext='.$aMSG['ID'].', timerNext='.$iMSGEnd);
	}
}

// VERIFIE SI MORCEAU YOUTUBE ANNONC�
$iPlay = $oPDO->query('SELECT COUNT(*) FROM youtube WHERE timePlay>0')->fetch(PDO::FETCH_COLUMN);
if ($iPlay!='0') {
	$aVideo = $oPDO->query('SELECT * FROM youtube, cooldown WHERE timePlay>0 AND cooldown.name="YOUTUBE_MSG" AND cooldown.stepNext=youtube.ID')->fetch(PDO::FETCH_ASSOC) ;
	if (($aVideo) && ($iMSGStatus==0)) {
		// UNE ANNONCE EXISTE
		if ($aVideo['timerNext']>microtrue()) {
			// ANNONCE TOUJOURS VALABLE
			$sMSG = $aVideo['titre'] . ' ['.secToHour($aVideo['duree']).'] de '.$aVideo['auteur'] ;
			$sTITRE = MSG_YOUTUBE_TITRE ;
			$iMSGStatus = 1 ;
		}
	} else {
		if ($iMSGStatus==0) {
			// AUCUNE ANNONCE EXISTE
			$aVideo = $oPDO->query('SELECT * FROM youtube WHERE timePlay>0')->fetch(PDO::FETCH_ASSOC);
			$oPDO->query('DELETE FROM cooldown WHERE name="YOUTUBE_MSG"');
			$oPDO->query('INSERT INTO cooldown SET name="YOUTUBE_MSG", stepNext='.$aVideo['ID'].', timerNext='.(microtrue()+COOLDOWN_MSG_YOUTUBE_LIFETIME));
			$sMSG = $aVideo['titre'] . ' ['.secToHour($aVideo['duree']).'] de '.$aVideo['auteur'] ;
			$sTITRE = MSG_YOUTUBE_TITRE ;
			$iMSGStatus = 1 ;
		}
	}
}

// SINON CHOPE UNE PHRASE DANS LA TABLE
if ($iMSGStatus==0) {
	include('./php/messageStats.php');
	// VERIFIE LE TIMER
	$oPDO->query('DELETE FROM cooldown WHERE timerNext<'.microtrue().' AND name="MESSAGE"');
	$aCD = $oPDO->query('SELECT * FROM cooldown WHERE timerNext>'.microtrue().' AND name="MESSAGE"')->fetch(PDO::FETCH_ASSOC);
	if (!$aCD) {
		// AUCUN MESSAGE
		$oPDO->query('UPDATE message SET actual=0, used=1 WHERE actual=1');
		$aMSG = $oPDO->query('SELECT * FROM message WHERE actual=0 AND used=0 ORDER BY RAND() LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		if (!$aMSG) {
			$oPDO->query('UPDATE message SET actual=0, used=0');
			$aMSG = $oPDO->query('SELECT * FROM message WHERE actual=0 AND used=0 ORDER BY RAND() LIMIT 0,1')->fetch(PDO::FETCH_ASSOC);
		}
		$oPDO->query('UPDATE message SET actual=1 WHERE ID="'.$aMSG['ID'].'"');
		$oPDO->query('INSERT INTO cooldown SET name="MESSAGE", timerNext='.(microtrue()+COOLDOWN_MSG_LIFETIME));
	} else {
		$aMSG = $oPDO->query('SELECT * FROM message WHERE actual=1')->fetch(PDO::FETCH_ASSOC);
	}
	$sMSG = $aMSG['message'] ;
	$sTITRE = $aMSG['titre'] ;
	$iMSGStatus = 1 ;
}

// CREER LE MESSAGE SI STATUS = 1
if ($iMSGStatus == 1) {
	setMSG($sTITRE, $sMSG);
}