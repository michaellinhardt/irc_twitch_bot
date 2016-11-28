<?php
$oPDO->query('UPDATE pve SET actual=0, used=1 WHERE actual=1');
// PARTIE POUR LE QUIZZ
// STATUS 0 -> QUIZZ EN ATTENTE DE PROC
if ($aCooldown['status'] == '0') {
	// STATUS 0 = EN ATTENTE DE LANCEMENT, ON LE LANCE SI LE COOLDOWN EST PERIMé
	// PUIS ON CHANGE LE STATUS POUR DIRE "EN COURS"
	if ($aCooldown['timerNext'] > microtrue()) exit();
	// RESET TOUT LES MOB ACTUAL
	$oPDO->query('UPDATE quizz SET actual=0');
	$oPDO->query('DELETE FROM cooldown WHERE name="QUIZZ_INDICE"');
	// RECUPERE LA LISTE DE CATEGORIE ACTUAL
	$aPlaylist = $oPDO->query('SELECT name FROM quizzcat WHERE actual=1')->fetchAll(PDO::FETCH_ASSOC);
	$sPlaylist = '' ;
	foreach($aPlaylist as $aValue) {
		if ($sPlaylist != '') $sPlaylist .= ' OR ' ;
		$sPlaylist .= 'quizzcat="'.$aValue['name'].'"' ;
	}
	var_dump('SELECT * FROM quizz WHERE used=0 AND '.$sPlaylist.' ORDER BY RAND() LIMIT 0,1');
	
	// SELECT AU HASARD UNE QUESTION NON USED ET RESET LA LISTE SI AUCUN DISPO
	$aRandQuestion = $oPDO->query('SELECT * FROM quizz WHERE used=0 AND '.$sPlaylist.' ORDER BY RAND() LIMIT 0,1')->fetch(PDO::FETCH_ASSOC) ;
	if (!$aRandQuestion) {
		$oPDO->query('UPDATE quizz SET used=0') ;
		purgeQuizzPve();
		exit();
	}
	
	// MET A JOUR LE COOLDOWN ET SUPPRIME LES INDICE
	$oPDO->query('UPDATE cooldown SET status=1, timerNext='.(microtrue()+(COOLDOWN_QUIZZ_LIFETIME*60)).' WHERE name="EVENT"');
	$oPDO->query('DELETE FROM cooldown WHERE name="QUIZZ_INDICE');
	// CREER LE COOLDOWN INDIC
	$oPDO->query('INSERT INTO cooldown SET timerNext='.(microtrue()+((COOLDOWN_QUIZZ_LIFETIME*60)/2)).', name="QUIZZ_INDICE"');
	// ANNONCE
	mircEXEC('timer 1 '.QUIZZ_DIRE_TIMER.' dire [QUIZZ] Le quizz démarre, vous avez '.COOLDOWN_QUIZZ_LIFETIME.' minutes pour répondre à la question de '.$aRandQuestion['auteur'].' présente sur le stream en tapant: !quizz <votre réponse>)');

	// PREPARE LA QUESTION 
	$oPDO->query('UPDATE quizz SET actual=1 WHERE ID="'.$aRandQuestion['ID'].'"');
	
	// RECOMPENSE L'AUTEUR
	$aAuteur = $oPDO->query('SELECT * FROM viewer WHERE name="'.$aRandQuestion['auteur'].'"')->fetch(PDO::FETCH_ASSOC);
	$iXP = (XPReq($aAuteur['level']))*QUIZZ_AUTEUR_XP ;
	$oPDO->query('UPDATE viewer SET gils=gils+'.calcGILSTotal($aAuteur, QUIZZ_AUTEUR_GILS).', xpcurrent='.calcXPTotal($aAuteur, $iXP));
}

// STATUS 1 -> QUIZZ EST PROC, ATTENDS POUR REPONSE
if ($aCooldown['status'] == '1') {
	// RECUP LA QUESTION
	$aQuizz = $oPDO->query('SELECT * FROM quizz WHERE actual=1')->fetch(PDO::FETCH_ASSOC) ;
	// Parcours le fichier quizz.txt
	if (is_file(PATH_FROM_QUIZZ)) {
		$oFile = fopen(PATH_FROM_QUIZZ,"r");
		$aProfile = array();
		while (!feof($oFile)) {
			$sLine = trim(fgets($oFile, 4096));
			// explose la ligne
			$aLine = explode(',', $sLine);
			$sName = formatName($aLine[0]);
			if (isset($aLine[1])) $sRep = strtolower($aLine[1]);
			else $sRep = '' ;
			echo $sName .' REPOND '.$sRep.'<br />' ;
			$aViewer = $oPDO->query('SELECT * FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC);
			if (($aViewer) && ($sRep!='')) {
				if ($aQuizz['auteur']!=$aViewer['name']) {
					if ($aViewer['gils']>0) {
						// RETIRE LES GILS
						$oPDO->query('UPDATE viewer SET gils=gils-1 WHERE name="'.$sName.'"');
						if (strtolower($sRep) == strtolower($aQuizz['reponse'])) {
							// BONNE REPONSE
							$oPDO->query('UPDATE quizz SET actual=0, used=1 WHERE actual=1') ;
							
							$iGils = calcGILSTotal($aViewer, QUIZZ_REP_GILS);
							$iXP = $aViewer['xpreq'] * QUIZZ_REP_XP ;
							$iXP = calcXPTotal($aViewer, $iXP) ;
							$oPDO->query('UPDATE viewer SET scoreQuizz=scoreQuizz+1, gils=gils+'.$iGils.', xpcurrent='.$iXP.' WHERE name="'.$sName.'"');
							$oPDO->query('DELETE FROM cooldown WHERE name="EVENT"');
							
							dire('[QUIZZ] Bravo '.$sName.', la réponse était: '.$aQuizz['reponse'].$aQuizz['info']);
							$iQuizzPos = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE name!="nestoyeur" AND name!="nestbot" AND scoreQuizz>'.$aViewer['scoreQuizz'])->fetch(PDO::FETCH_COLUMN);
							$iQuizzPos = intval($iQuizzPos)+1 ;
							dire('[QUIZZ] Classement #'.$iQuizzPos.'('.($aQuizz['scoreQuizz']+1).' pts), Gils: '.$aViewer['gils'].'+'.QUIZZ_REP_GILS.', LVL: '.$iLevel['level'].' ('.$iLevel['xpcurrent'].'/'.$iLevel['xpreq'].' XP) + 20% = '.round(($iLevel['xpcurrent']/$iLevel['xpreq'])*100).'%');
							
							break ;
						}
					} else dire('[QUIZZ] Pas asse de gils '.$sName.'! Il faut 1 gils par réponse proposer.');
				} else dire('[QUIZZ] L\'auteur de la question ne peux pas répondre, mais gagne '.(QUIZZ_AUTEUR_XP*100).'% d\'XP et '.QUIZZ_AUTEUR_GILS.' gils à chaque fois que la question passe.');
			}
		}
			
		$oFile = fopen(PATH_FROM_QUIZZ,"w+");
		fclose($oFile);
	}
	
	if ($aCooldown['timerNext']<microtrue()) {
		// TEMPS ECOULé
		$oPDO->query('UPDATE quizz SET actual=0, used=1 WHERE actual=1') ;
		dire('[QUIZZ] Le temps est écoulé, vous trouverez la réponse une prochaine fois.');
		$oPDO->query('DELETE FROM cooldown WHERE name="EVENT"');
		purgeQuizzPve();
		exit();
	}
	// NEED INDICE
	$aIndice = $oPDO->query('SELECT * FROM cooldown WHERE name="QUIZZ_INDICE"')->fetch(PDO::FETCH_ASSOC);
	if ($aIndice) {
		if ($aIndice['timerNext']<microtrue()) {
			// INDICE TEMPS ECOULé
			dire('[QUIZZ] La moitié du temps est écoulé, un indice: '.$aQuizz['indice']);
			$oPDO->query('DELETE FROM cooldown WHERE name="QUIZZ_INDICE"');
		}
	}

}