<?php
$aUP = $oPDO->query('SELECT * FROM viewer WHERE xpcurrent>xpreq')->fetchAll(PDO::FETCH_ASSOC);
$sLVLUP = '' ;
$iTotal = 0 ;
foreach($aUP as $aViewer) {
	$iTotal++ ;
	$iLevel = $aViewer['level'] ;
	$iCurrent = $aViewer['xpcurrent'] ;
	$iReq = $aViewer['xpreq'] ;
	while(true) {
		$iLevel++ ;
		$iCurrent = $iCurrent - $iReq ;
		$iReq = XPReq($iLevel) ;
		
		if ($iCurrent<$iReq) break ;
	}
	// MET A JOUR LA DB
	$oPDO->query('UPDATE viewer SET xpcurrent='.$iCurrent.', xpreq='.$iReq.', level='.$iLevel.' WHERE name="'.$aViewer['name'].'"');
	
	// CONSTRUIT LA VAR LVLUP
	if ($sLVLUP!='') $sLVLUP .= ', ' ;
	$sLVLUP .= $aViewer['name'] .' ('.$iLevel.')' ;
	
	// DIS LA VAR LVLUP SI ELLE EXCEDE 30 LVL
	if ($iTotal>30) {
		//dire('[LVLUP] ' . $sLVLUP);
		$sLVLUP = '' ;
		$iTotal = 0 ;
	}
}

//if ($sLVLUP!='') dire('[LVLUP] ' . $sLVLUP);