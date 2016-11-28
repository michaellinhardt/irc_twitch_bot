<?php
$oPDO->query('UPDATE quizz SET actual=0, used=1 WHERE actual=1');
// PARTIE POUR LE PVE
// STATUS 0 -> MOB EN ATTENTE DE PROC
if ($aCooldown['status'] == '0') {
	// STATUS 0 = EN ATTENTE DE LANCEMENT, ON LE LANCE SI LE COOLDOWN EST PERIMé
	// PUIS ON CHANGE LE STATUS POUR DIRE "EN COURS"
	if ($aCooldown['timerNext'] > microtrue()) exit();
	// RESET TOUT LES MOB ACTUAL
	$oPDO->query('UPDATE pve SET actual=0');
	// SELECT AU HASARD UN MOB NON USED ET RESET LA LISTE SI AUCUN DISPO
	$aPVE = $oPDO->query('SELECT * FROM pve WHERE used=0 ORDER BY RAND() LIMIT 0,1')->fetch(PDO::FETCH_ASSOC) ;
	if (!$aPVE) {
		$oPDO->query('UPDATE pve SET used=0') ;
		purgeQuizzPve();
		exit();
	}
	// CALCULE ET ENREGISTRE LES HP
	$iOnline = $oPDO->query('SELECT COUNT(*) FROM viewer WHERE isOnline>'.(microtrue()-60))->fetch(PDO::FETCH_COLUMN);
	$iHP = ceil($iOnline/4) ;
	$oPDO->query('UPDATE pve SET pv='.$iHP.', actual=1 WHERE ID='.$aPVE['ID']);

	// MET A JOUR LE COOLDOWN
	$oPDO->query('UPDATE cooldown SET status=1, timerNext='.(microtrue()+(COOLDOWN_PVE_LIFETIME*60)).' WHERE name="EVENT"');

	// ANNONCE
	mircEXEC('timer 1 10 dire [PVE] Un monstre est apparu. Tapez !kill <son nom> pour l\'attaquer, il posséde seulement '.$iHP.'HP ! (une seul attaque par viewer)');
}
// STATUS 1 -> MOB PROC, ATTENDS POUR FUIR OU MOURIR
if ($aCooldown['status'] == '1') {
	$aPVE = $oPDO->query('SELECT * FROM pve WHERE actual=1')->fetch(PDO::FETCH_ASSOC) ;
	if (($aPVE['pv']==0) || ($aCooldown['timerNext']<microtrue())) {
		// CLEAN LE MOB ET CHANGE LE COOLDOWN
		$oPDO->query('UPDATE pve SET used=1, actual=0 WHERE actual=1');
		$aAtkList = $oPDO->query('SELECT * FROM cooldown WHERE name="KILL_ATK"')->fetchAll(PDO::FETCH_ASSOC);
		$sDire = '' ;
		$sListe = '' ;
		if ($aPVE['pv']==0) $sMort = ' est mort' ;
		else $sMort = ' c\'est enfuit' ;
		foreach( $aAtkList as $aValue ){
			if ($sDire == '') $sDire = $aPVE['name'].$sMort.', les viewer suivant gagnent '.(PVE_ATK_XP*100).'% XP de leurs level et '.PVE_ATK_GILS.' gils: ' ;
			if ($sListe == '') $sListe = $aValue['stepNext'] ;
			else $sListe .= ', '.$aValue['stepNext'] ;
		}
		if ($sDire == '') { $sDire = $aPVE['name'].$sMort.'.' ; }
		dire('[PVE] '.$sDire) ;
		if ($sListe != '') dire('[PVE] '.$sListe) ;
		$oPDO->query('UPDATE cooldown SET status=0, stepNext="'.EVENT_STEP_AFTER_PVE.'", timerNext='.(microtrue()+(COOLDOWN_QUIZZ*60)).' WHERE name="EVENT"');
		$oPDO->query('DELETE FROM cooldown WHERE name="KILL_ATK"');
		purgeQuizzPve();
		exit();
	}
	// Parcours le fichier kill.txt
	if (is_file(PATH_FROM_KILL)) {
		$oFile = fopen(PATH_FROM_KILL,"r");
		$aProfile = array();
		while (!feof($oFile)) {
			$sLine = trim(fgets($oFile, 4096));
			// explose la ligne
			$aLine = explode(',', $sLine);
			$sName = formatName($aLine[0]);
			if (isset($aLine[1])) $sMob = strtolower($aLine[1]);
			else $sMob = '' ;
			echo $sName .' ATK '.$sMob.'<br />' ;
			if ($sMob == strtolower($aPVE['name'])) {
				// LE NOM EST CORRECT ET LE MOB A DES HP
				$iVerif = $oPDO->query('SELECT COUNT(*) FROM cooldown WHERE name="KILL_ATK" AND stepNext="'.$sName.'"')->fetch(PDO::FETCH_COLUMN);
				if (($iVerif == '0') && ($aPVE['pv']>0)) {
					echo 'atk ok<br />';
					// ATTAQUE OK
					$oPDO->query('INSERT INTO cooldown SET name="KILL_ATK", stepNext="'.$sName.'"') ;
					$aViewer = $oPDO->query('SELECT * FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC) ;
					$iXP = $aViewer['xpreq'] * PVE_ATK_XP ;
					$iXP = calcXPTotal($aViewer, $iXP) ;
					$iGils = calcGILSTotal($aViewer, PVE_ATK_GILS) ;
					echo 'xp: '.$iXP.' gils: '.$iGils.'<br />';
					$oPDO->query('UPDATE viewer SET gils=gils+'.$iGils.', scorePve=scorePve+1, xpcurrent=xpcurrent+'.$iXP.' WHERE name="'.$sName.'"');
					$oPDO->query('UPDATE pve SET pv=pv-1 WHERE actual=1');
					$aPVE['pv'] = $aPVE['pv'] - 1 ;
				}
			}
		}
			
		$oFile = fopen(PATH_FROM_KILL,"w+");
		fclose($oFile);
	}

}