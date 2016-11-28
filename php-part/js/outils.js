$(document).ready(function(){
  initBtn();
  initCount();
});

function initBtn() {
	$('#eventStop').click(function(){
		ajaxAsync('./eventStop.php', {});
	});
	$('#eventReset').click(function(){
		ajaxAsync('./eventReset.php', {});
	});
}

function initCount(){
	$('#incLegs').submit(function(){ legsCount(); return false; });
	$('#incSet').submit(function(){ setCount(); return false; });
	$('#xpH').submit(function(){ xpHCount(); return false; });
	$('#incLegsMoins').click(function(){ $('#incLegsValue').val(parseInt($('#incLegsValue').val())-1); legsCount(); });
	$('#incLegsPlus').click(function(){ $('#incLegsValue').val(parseInt($('#incLegsValue').val())+1); legsCount(); });
	$('#incSetMoins').click(function(){ $('#incSetValue').val(parseInt($('#incSetValue').val())-1); setCount(); });
	$('#incSetPlus').click(function(){ $('#incSetValue').val(parseInt($('#incSetValue').val())+1); setCount(); });
	
	$('#itemReset').click(function(){ ajaxAsync('./outilsCount.php?itemReset=1', {}, 'readCount' ); });
	$('#xpHReset').click(function(){ ajaxAsync('./outilsCount.php?xpHReset=1', {}, 'readCount' ); });
	$('#resetFollower').click(function(){ ajaxAsync('./outilsCount.php?resetFollower=1', {}, 'readCount' ); });	
}

function legsCount(){
	iLegs = parseInt($('#incLegsValue').val());
	ajaxAsync('./outilsCount.php?legsCount=1&legs='+iLegs, {}, 'readCount' );
}

function setCount(){
	iSet = parseInt($('#incSetValue').val());
	ajaxAsync('./outilsCount.php?setCount=1&set='+iSet, {}, 'readCount' );
	
}

function xpHCount(){
	iXP = parseInt($('#xpHXP').val());
	iLevel = parseInt($('#xpHLevel').val());
	iMax = parseInt($('#xpHMax').val());
	ajaxAsync('./outilsCount.php?xpH=1&xp='+iXP+'&level='+iLevel+'&max='+iMax, {}, 'readCount' );
}