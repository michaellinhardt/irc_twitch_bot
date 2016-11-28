$(document).ready(function(){
	sAjaxResponse = '';
	sAjaxResponse2 = '';
	initAjaxDial();
	$('#_Deconnexion').click(function(){ ajaxAsync('connexion/deconnexion', {}, 'goHome');});
});

function goHome(){ window.location.href = sBaseurl + '?content=' + iCurrentContent;}

function ajaxError(){ $('#_ajaxError').dialog('open');}

function initAjaxDial()
{
	$('#_ajaxLoading').dialog({
		title: '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; CHARGEMENT',
		modal: true,
		resizable: false,
		draggable: false,
		height: 0,
		autoOpen: false
	});
	$('#_ajaxResult').dialog({
		title: 'Ajax Return',
		modal: true,
		resizable: false,
		draggable: false,
		height: 'auto',
		autoOpen: false
	});
	// Masque le bouton pour fermer la boite
	$('#_ajaxLoading').parent().find('.ui-dialog-titlebar-close').css('display', 'none');
	$('#_ajaxError').dialog({
		title: '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Ajax Error',
		modal: true,
		resizable: false,
		draggable: false,
		height: 'auto',
		autoOpen: false
	});
}

function ajaxLoading(iStatu)
{
	if ( (iStatu==undefined) || (iStatu==0) ) { $('#_ajaxLoading').dialog('close');}
	else { $('#_ajaxLoading').dialog('open');}
}

function ajaxAsync(sPath, oData, sFunc, bDisplayBox)
{
	window['sDataAjaxAsync'] = '';
	if (bDisplayBox==undefined) bDisplayBox = false;
	if (bDisplayBox) ajaxLoading(1);
	if (sFunc==undefined) sFunc = false;
	if ( oData == undefined ) oData = {};
	$.ajax({
		url: sBaseurl + sPath,
		async: false,
		type: "POST",
		data: (oData),
		success: function(sData)
		{
		if (bDisplayBox) ajaxLoading(0);
			sAjaxResponse = sData;
			window['sDataAjaxAsync'] = sData;
			if (sFunc==false) window['sDataAjaxAsync'] = sAjaxResponse;
			else { window[sFunc](sAjaxResponse); return false;}
		},
		error: function(){
			if (bDisplayBox) ajaxLoading(0);
			ajaxError();
			return false;
		}
	});
	return window['sDataAjaxAsync']; // Dans le cas ou il n'y � pas d'erreur et pas de fonction de retour
}

function alertVar(){sVar = ''; for(i=0;i<arguments.length;i++){sVar += '[ i'+i+' ] ' + arguments[i] + "\n";} alert(sVar);}

function emptyVar(){for(i=0;i<arguments.length;i++){if ((arguments[i]=='') || (arguments[i]==undefined)) return true;}return false;}

function formatNumber(n)
{
    var rx=  /(\d+)(\d{3})/;
    return String(n).replace(/^\d+/, function(w){
        while(rx.test(w)){
            w= w.replace(rx, '$1.$2');
        }
        return w;
    });
}

function formatNumberLetterShort(n)
{
	parseInt(n);
	if (n<1000) return n;
	if (n<1000000) { n = n/1000; return Math.round(n) + ' K';}
	if (n<1000000000) {	n = n/1000000; return Math.round(n) + ' M';	}
	if (n>999999999) { n = n/1000000000; return Math.round(n) + ' B';}
}

function formatNumberLetterLong(n)
{
	parseInt(n);
	if (n<1000) return n;
	if (n<1000000) { n = n/1000; return Math.round(n) + ' ' + oLang['MATH_K'];}
	if (n<1000000000) {	n = n/1000000; return Math.round(n) + ' ' + oLang['MATH_M'];}
	if (n>999999999) { n = n/1000000000; return Math.round(n) + ' ' + oLang['MATH_B'];}
}