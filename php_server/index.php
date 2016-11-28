<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head><meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
<title>NESTOYEUR.TV</title>
<link rel="stylesheet" type="text/css" href="./jquery/jquery.css" />
<link rel="stylesheet" type="text/css" href="./css/defaut.css" />
<link rel="stylesheet" type="text/css" href="./css/winwheel.css" />
<script type="text/javascript" src="./jquery/jquery.js"></script>
<script type="text/javascript" src="./jquery/jqueryui.js"></script>
<script type="text/javascript" src="./js/winwheel.js"></script>
<script type="text/javascript" src="./js/defaut.js"></script>
<script type="text/javascript" src="./js/follower.js"></script>
<script type="text/javascript" src="./js/youtube.js"></script>
<script type="text/javascript" src="./js/outils.js"></script>
<script type="text/javascript" src="./js/readFile.js"></script>
<script type="text/javascript" src="./js/dump.js"></script>
		
		<script type="text/javascript">
		var sBaseurl = '<?php echo str_replace( "index.php", "", 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["PHP_SELF"] ) ; ?>' ;
		</script>
		<style type="text/css"></style>
	</head>
	<body>	

	<div id="nicklist">
	<div id="layout">

	</div>
	</div>
	
	<div id="readYoutube"></div>
	
	<iframe id="youtube" src="" frameborder="0" allowfullscreen></iframe>
	
	<div id="youtube-swap">
		<a href="#" id="play">PLAY</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="#" id="stop">STOP</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="#" id="next">NEXT</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href="#" id="del">DEL</a>
		<p>&nbsp;</p><a id="playing" href="#" target=_blank>aa</a>
		
		<form id="youtubeForce" method="post" action="llaa.html">
			Force: <input type="text" id="youtubeForceLink" size="50" value="" /> <input type="submit" id="youtubeForceSubmit" value="Forcer" />
		</form> 
		
		<form id="youtubeAdd" method="post" action="llaa.html">
			Add Lien: <input type="text" id="youtubeAddLink" size="50" value="" /> <br />Add Lien, Playlist: <select id="youtubeAddPlaylist"></select> <input type="text" size="15" id="youtubeAddNewPlaylist" value="" /><input type="submit" id="youtubeAddSubmit" value="Ajouter" />
		</form>
		<form id="playlistAdd">Playlist OFF: <select id="playlistAddList"></select><input type="submit" value="Ajouter" /></form><form id="playlistDel">Playlist ON :<select id="playlistDelList"></select><input type="submit" value="Retirer" /></form>
	</div>

	<ul id="readFollower">
	</ul>
	
	<div id="followerBox"><div id="followerBoxName"></div></div>
	
	<div id="outils">
		<input type="button" id="eventStop" value="EV STOP" /> <input type="button" id="eventReset" value="EV RESET" />
		<div id="outilsCount">
		</div>
		<form id="incLegs"><input type="button" id="incLegsMoins" value=" - " /><input type="text" id="incLegsValue" size="2" value="0" /><input type="button" id="incLegsPlus" value=" + " /> LEGS</form>
		<form id="incSet"><input type="button" id="incSetMoins" value=" - " /><input type="text" id="incSetValue" size="2" value="0" /><input type="button" id="incSetPlus" value=" + " /> SET &nbsp; <input type="button" id="itemReset" value="RESET" /></form>
		<form id="xpH"><input type="text" id="xpHLevel" value="400" size="2" /><input type="text" id="xpHXP" size="4" value="100" /><input type="text" id="xpHMax" size="5" value="100" /><input type="submit" id="incSetPlus" value="+" /><input type="button" id="xpHReset" value="REZ" /></form>
		<p>LH: <strong id="LH">1.2</strong> SH: <strong id="SH">1.2</strong> XH: <strong id="XH">1.542</strong></p>
		<input type="button" id="resetFollower" value="RESET FOLLOWER" />
	</div>
	
	<div class="power_controls">
		<table class="power" cellpadding="10" cellspacing="0">
			<tr>
				<th align="center">Power</th>
			</tr>
			<tr>
				<td width="78" align="center" id="pw3" onClick="powerSelected(3);">High</td>
			</tr>
			<tr>
				<td align="center" id="pw2" onClick="powerSelected(2);">Med</td>
			</tr>
			<tr>
				<td align="center" id="pw1" onClick="powerSelected(1);">Low</td>
			</tr>
		</table> 
		<img id="spin_button" src="./img/spin_off.png" alt="Spin" onClick="startSpin();" />
		<a href="#" onClick="resetWheel(); return false;">Play Again</a><br />(reset)
	</div>

	<div width="438" height="582" class="the_wheel" align="center" valign="center">
		<img src="./img/wheel_back.png" />
		<canvas class="the_canvas" id="myDrawingCanvas" width="434" height="434">
			<p class="noCanvasMsg" align="center">Sorry, your browser doesn't support canvas.<br />Please try another.</p>
		</canvas>
	</div>
		
	</body>
</html>