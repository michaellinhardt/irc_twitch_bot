<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<?php
set_time_limit(0);
error_reporting(E_ALL);
session_start(); 
//sleep(1);

// Récupére la date
date_default_timezone_set('Europe/Paris');

// Initialise PDO pour les requete sql
include './DatabaseConfig.php' ;
include('./php/config.php') ;
include('./php/functions.php');

$sLink = '' ;
// LISTE LES FICHIER
$sFolder = 'C:/Users/Nestoyeur/Documents/groovshark-txt/' ;
$oFile = scandir($sFolder);
foreach($oFile as $sFile) {
	if (($sFile != '..') && ($sFile != '.')) {
		
		$sLink .= '<h1>'.substr($sFile, 0, -4).'</h1>' ;
		
		// LISTE LE CONTENUE DU FICHIER
		$oTXTFile = fopen($sFolder.$sFile,"r");
		while (!feof($oTXTFile)) {
			$sLine = trim(fgets($oTXTFile, 4096));
			// EXPLOSE LA LIGNE
			$aLine = explode(',', $sLine);
			if ((isset($aLine[0])) && (!empty($aLine[1])) && ($aLine[0]!='SongName')) {
				$aLine[0] = str_replace('"', '', $aLine[0]);
				$aLine[1] = str_replace('"', '', $aLine[1]);
				$sSong = htmlentities(trim($aLine[0])) ;
				$sArtiste = htmlentities(trim($aLine[1])) ;
				$sLink .= '<a target=_blank href="http://www.youtube.com/results?search_query='.$sArtiste.' '.$sSong.'">'.$sArtiste.' - '.$sSong.'</a><br />' ;
			}
		}
		$sLink .= '<hr />' ;
	}
}
$oPDO = null ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head><meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>NESTOYEUR.TV</title>
<link rel="stylesheet" type="text/css" href="./jquery/jquery.css" />
<link rel="stylesheet" type="text/css" href="./css/defaut.css" />
<script type="text/javascript" src="./jquery/jquery.js"></script>
<script type="text/javascript" src="./jquery/jqueryui.js"></script>
<script type="text/javascript" src="./js/dump.js"></script>
		
		<script type="text/javascript">
		$(document).ready(function(){
			$('A').click(function(){
				$(this).css('color', 'red');
			});
		});
		</script>
		<style type="text/css"></style>
	</head>
	<body>	
		<?php echo $sLink ?>
	</body>
</html>