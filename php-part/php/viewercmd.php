<?php
// Parcours les cmd viewer
if (is_file(PATH_FROM_CMD)) {
	$oFile = fopen(PATH_FROM_CMD,"r");
	while (!feof($oFile)) {
		$aExplode = explode(',', fgets($oFile, 4096));
		$sName = formatName($aExplode[0]);
		if (isset($aExplode[1])) $sCMD = trim($aExplode[1]) ;
		else $sCMD = '' ;
			if ($sCMD=='gils') {
			$iGils = $oPDO->query('SELECT gils FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC);
			dire('[GILS] '.$sName .': '.$iGils['gils']) ;
		}
		if ($sCMD=='level') {
			$iLevel = $oPDO->query('SELECT * FROM viewer WHERE name="'.$sName.'"')->fetch(PDO::FETCH_ASSOC);
			dire('[LEVEL] '.$sName .': '.$iLevel['level'].' ('.round(($iLevel['xpcurrent']/$iLevel['xpreq'])*100).'%)') ;
		}
	}
	fclose($oFile) ;

unlink(PATH_FROM_CMD);
} 