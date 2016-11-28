<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<?php
set_time_limit(0);
error_reporting(E_ALL);
session_start(); 
//sleep(1);

// R�cup�re la date
date_default_timezone_set('Europe/Paris');
$sToday = date('d/m/Y') ;

// Initialise PDO pour les requete sql
include './DatabaseConfig.php' ;
include('./php/config.php') ;
include('./php/functions.php');
$sFolder = '/hearthstone/' ;
// LISTE LES FICHIER
$oFile = scandir(PATH_QUIZZ_FILE . $sFolder);
foreach($oFile as $sFile) {
	if (($sFile != '..') && ($sFile != '.')) {
		// AJOUTE CAT / SCAT
		$sCat = trim('Hearthstone') ;
		$sScat = trim(substr($sFile, 0, -4)) ;
		$iCount = $oPDO->query('SELECT COUNT(*) FROM quizzcat WHERE name="'.$sCat.'"')->fetch(PDO::FETCH_COLUMN);
		if ($iCount=='0') $oPDO->query('INSERT INTO quizzcat SET name="'.$sCat.'", actual=1');
		
		$iCount = $oPDO->query('SELECT COUNT(*) FROM quizzscat WHERE name="'.$sScat.'"')->fetch(PDO::FETCH_COLUMN);
		if ($iCount=='0') $oPDO->query('INSERT INTO quizzscat SET name="'.$sScat.'", actual=1');
		
		// LISTE LE CONTENUE DU FICHIER
		$oTXTFile = fopen(PATH_QUIZZ_FILE.$sFolder.$sFile,"r");
		while (!feof($oTXTFile)) {
			$sLine = trim(fgets($oTXTFile, 4096));
			// EXPLOSE LA LIGNE
			$aLine = explode(',', $sLine);
			
			
			if ((isset($aLine[1])) && (isset($aLine[3])) && ($aLine[1]!='') && ($aLine[3]!='') && ($aLine[5]!='')) {
				if (empty($aLine[1])) var_dump($aLine);
				
				$sQuestion = trim($aLine[1]) ;
				$sIndice = trim($aLine[2]) ;
				$sReponse = trim($aLine[3]) ;
				$sInfo = trim($aLine[4]) ;
				$sAuteur = trim($aLine[5]) ;

				// QUESTION COMPLETE
				$aExist = $oPDO->query('SELECT ID FROM quizz WHERE question="'.$sQuestion.'" AND reponse="'.$sReponse.'"')->fetch(PDO::FETCH_ASSOC);
				echo 'SELECT ID FROM quizz WHERE question="'.$sQuestion.'" AND reponse="'.$sReponse.'"<br />' ;
				if ($aExist) $oPDO->query('UPDATE quizz SET auteur="'.$sAuteur.'", quizzcat="'.$sCat.'", indice="'.$sIndice.'", info="'.$sInfo.'", quizzscat="'.$sScat.'" WHERE ID='.$aExist['ID']);
				else $oPDO->query('INSERT INTO quizz SET question="'.$sQuestion.'", indice="'.$sIndice.'", reponse="'.$sReponse.'", info="'.$sInfo.'", auteur="'.$sAuteur.'", quizzcat="'.$sCat.'", quizzscat="'.$sScat.'"');
				
			}
		}
	}
}
$oPDO = null ;