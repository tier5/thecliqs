function openPage(url, vHeight, vWidth) {
	//vWidth = 885;
	//winDef =
	// 'status=yes,resizable=no,scrollbars=yes,fullscreen=no,titlebar=no,width='.concat(vWidth).concat(',');
	//winDef = winDef.concat('left=').concat((screen.width - vWidth)/2);

	//winmedia= window.open(url,'music_popup', winDef);
	window.location = url;
	return false;
}

function openPageOpener(url) {
	opener = window.open(url);
	opener.focus();
	return false;
}

function getKeyDown(evt) {
	evt = evt || window.event;
	var charCode = evt.keyCode || evt.which;
	if (charCode == 13) 
	{
		var key_search = $("title").value;
    key_search = key_search.replace(/^\s+|\s+$/g,'');
    if (key_search == "")
    {
        return false;
    }
    else
    {
        $('frm_search').submit();
        return true;
    }
	}

}

function roundNumber(num, dec) {
	var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
	return result;
}

