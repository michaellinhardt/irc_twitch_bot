<?php

$i7Days = microtrue()+(60*60*24*7) ;

$aCooldown = $oPDO->query('SELECT * FROM cooldown WHERE name="CLOSE_EVENT"')->fetch(PDO::FETCH_ASSOC);
if (!$aCooldown) $oPDO->query('INSERT INTO cooldown SET name="CLOSE_EVENT", status="0", timerNext='.$i7Days.', stepNext="close_event"');

if (($aCooldown['status']==0) && ($aCooldown['stepNext']=='close_event') && ($aCooldown['timerNext']<microtrue())) {
	// STATUS: ATTENTE, STEPNEXT: CLOSE_EVENT, TIMERNEXT: DEPASSé
	// Ferme les event et incrémente le score de tout le monde
	dire('[QUIZZ/PVE] Le classement de la semaine sera bientôt disponible sur la page facebook: http://facebook.com/nesstream') ;
	// INCREMENTE DE 10 LES SCORE
	$oPDO->query('UPDATE viewer SET winPVE=winPVE+1 WHERE winPVE!=0');
	$oPDO->query('UPDATE viewer SET winQuizz=winQuizz+1 WHERE winQuizz!=0');
	
	// LISTE LES GAGNANT
	$aWINQuizz = $oPDO->query('SELECT * FROM viewer WHERE scoreQuizz>0 ORDER BY scoreQuizz DESC, level DESC, timetotal DESC LIMIT 0,5')->fetchAll(PDO::FETCH_ASSOC);
	$aWINPVE = $oPDO->query('SELECT * FROM viewer WHERE scorePVE>0 ORDER BY scorePVE DESC, level DESC, timetotal DESC LIMIT 0,5')->fetchAll(PDO::FETCH_ASSOC);
	
	// SAVE LA LISTE
	foreach( $aWINQuizz as $iKey => $aValue ) {
		$iPos = $iKey + 1 ;
		$oPDO->query('UPDATE viewer SET winQuizz=0.'.(6-$iPos).' WHERE name="'.$aValue['name'].'"');
	}
	foreach( $aWINPVE as $iKey => $aValue ) {
		$iPos = $iKey + 1 ;
		$oPDO->query('UPDATE viewer SET winPVE=0.'.(6-$iPos).' WHERE name="'.$aValue['name'].'"');
	}
	
	// RESET LE SCORE
	$oPDO->query('UPDATE viewer SET scoreQuizz=0, scorePVE=0 WHERE scoreQuizz!=0 OR scorePVE!=0');
	
	// RELANCE LE TIMER
	$oPDO->query('UPDATE cooldown SET timerNext='.$i7Days.' WHERE name="CLOSE_EVENT"');
}

$oPDO = null ;