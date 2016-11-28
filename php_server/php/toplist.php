<?php


$iLimit = 51 ;

$aToken = explode(' ', getTokenList());

$iPage = $aToken[0];
$iToken = $aToken[1];

$iPastTime = intval(microtime(true))-60 ;

// ROTATION
if (($iPage == '1') && (intval($iToken) < intval($iPastTime))) { $iToken = intval(microtime(true)); $iPage = "2" ; setTokenList($iPage.' '.intval(microtime(true))) ; }
if (($iPage == '2') && (intval($iToken) < intval($iPastTime))) { $iToken = intval(microtime(true)); $iPage = "3" ; setTokenList($iPage.' '.intval(microtime(true))) ; }
if (($iPage == '3') && (intval($iToken) < intval($iPastTime))) { $iToken = intval(microtime(true)); $iPage = "4" ; setTokenList($iPage.' '.intval(microtime(true))) ; }
if (($iPage == '4') && (intval($iToken) < intval($iPastTime))) { $iToken = intval(microtime(true)); $iPage = "5" ; setTokenList($iPage.' '.intval(microtime(true))) ; }
if (($iPage == '5') && (intval($iToken) < intval($iPastTime))) { $iToken = intval(microtime(true)); $iPage = "1" ; setTokenList($iPage.' '.intval(microtime(true))) ; }
var_dump($iPage);
$sHTML = '' ;
// TOP VIEWER
if ($iPage == '1') {
	$sTITRE = 'TOP VIEWER' ;
	$aViewer = $oPDO->query('SELECT * FROM viewer ORDER BY follower DESC, level DESC, timetotal DESC LIMIT 0,51')->fetchAll(PDO::FETCH_ASSOC);
}

// ONLINE
if ($iPage == '2') {
	$sTITRE = 'ONLINE' ;
	$aViewer = $oPDO->query('SELECT * FROM viewer WHERE isOnline>'.(microtime(true)-60).' ORDER BY follower DESC, level DESC, timetotal DESC LIMIT 0,51')->fetchAll(PDO::FETCH_ASSOC);
}

// QUIZZ
if ($iPage == '3') {
	$sTITRE = 'QUIZZ SCORE' ;
	$aViewer = $oPDO->query('SELECT * FROM viewer ORDER BY scoreQuizz DESC, follower DESC, level DESC, timetotal DESC LIMIT 0,51')->fetchAll(PDO::FETCH_ASSOC);
        
	$sTITRE = 'ONLINE' ;
	$aViewer = $oPDO->query('SELECT * FROM viewer WHERE isOnline>'.(microtime(true)-60).' ORDER BY follower DESC, level DESC, timetotal DESC LIMIT 0,51')->fetchAll(PDO::FETCH_ASSOC);

}

// PVE
if ($iPage == '4') {
	$sTITRE = 'PVE SCORE' ;
	$aViewer = $oPDO->query('SELECT * FROM viewer ORDER BY scorePve DESC, follower DESC, level DESC, timetotal DESC LIMIT 0,51')->fetchAll(PDO::FETCH_ASSOC);
        
	$sTITRE = 'ONLINE' ;
	$aViewer = $oPDO->query('SELECT * FROM viewer WHERE isOnline>'.(microtime(true)-60).' ORDER BY follower DESC, level DESC, timetotal DESC LIMIT 0,51')->fetchAll(PDO::FETCH_ASSOC);

}

// LAST FOLLOWER
if ($iPage == '5') {
	$sTITRE = 'DERNIER FOLLOW' ;
	$aViewer = $oPDO->query('SELECT name FROM follower ORDER BY ID DESC LIMIT 0,51')->fetchAll(PDO::FETCH_ASSOC);
	foreach($aViewer as $iKey => $aValue) {
		$aValue = $oPDO->query('SELECT * FROM viewer WHERE name="'.$aValue['name'].'"')->fetch(PDO::FETCH_ASSOC);
		$aViewer[$iKey] = $aValue ;
	}
}

foreach($aViewer as $aValue){
	$sICONE = '' ;
	
	// ScoreQUIZZ & ScorePVE
	//if ($iPage == '3') $aValue['level'] = $aValue['scoreQuizz'] ;
	//if ($iPage == '4') $aValue['level'] = $aValue['scorePVE'] ;
	
	$sWIN = '' ;
	$iWIN = 0 ;
	// Prepare winQuizz
	if ($aValue['winQuizz']!=0) {
		$aWINQuizz = explode('.', $aValue['winQuizz']) ;
		$sWINQuizz = $aWINQuizz[1] ;
		$sWINWeek = $aWINQuizz[0] ;
		$sWIN .= '<span class="icoQuizz quizz'.$sWINQuizz.'"></span>' ;
		$sWIN .= '<span class="weekQuizz weekQuizz'.$sWINWeek.'"></span>' ;
		if ($aValue['winPVE'] == 0) $sWIN .= '<span class="icoPVE pve0 weekPVE0"></span>' ;
		$iWIN = 1 ;
	}
	// Prepare winPVE
	if ($aValue['winPVE']!=0) {
		$aWINPVE = explode('.', $aValue['winPVE']) ;
		$sWINPVE = $aWINPVE[1] ;
		$sWINWeek = $aWINPVE[0] ;
		$sWIN .= '<span class="icoPVE pve'.$sWINPVE.'"></span>' ;
		$sWIN .= '<span class="weekPVE weekPVE'.$sWINWeek.'"></span>' ;
		if ($aValue['winQuizz'] == 0) $sWIN .= '<span class="icoQuizz quizz0 weekQuizz0"></span>' ;
		$iWIN = 1 ;
	}
	if ($iWIN==1) $sWIN = '<span class="icoSlot">'.$sWIN.'</span>' ;
	
	// FOLLOW ( NOM EN GRAS ET ICONE)
	if ($aValue['follower']=="1") { $sICONE .= '<span class="follower"></span>'; $sName = "<strong>".$aValue['name']."</strong>" ; }
	else $sName = $aValue['name'] ;
	
	// WIN QUIZZ & PVE
	$sICONE .= $sWIN ;

	// ISONLINE (COULEUR PSEUDO)
	if ($aValue['isOnline']>(intval(microtime(true))-60)) { $sName = '<span class="online">'.$sName.'</span>'; }
	else { $sName = '<span class="offline">'.$sName.'</span>'; }

	$sHTML .= '<li><nobr>'.$sICONE.''.$sName.'<span class="level">'.$aValue['level'].'</span></nobr></li>' ;
}


$sHTML = '<p id="nicklistTitre">'.$sTITRE.'</p><ul id="laliste">'.$sHTML.'</ul>' ;

setLayout($sHTML);