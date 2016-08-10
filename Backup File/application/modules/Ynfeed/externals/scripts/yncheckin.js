function initialize() 
{
 	var input = /** @type {HTMLInputElement} */(
		document.getElementById('ynfeed_checkinValue'));

  	var autocomplete = new google.maps.places.Autocomplete(input);
  	google.maps.event.addListener(autocomplete, 'place_changed', function() 
  	{
    	var place = autocomplete.getPlace();
	    if (!place.geometry) 
	    {
	     	return;
	    }
	    var address, checkin;
	    if($('ynfeed_checkinValue'))
	    {
			$('ynfeed_checkinValue').addClass('checkin_selected');
			address = $('ynfeed_checkinValue').value;
		}
		else
		{
			return;
		}
		
		if($('ynfeed_checkin'))
		{
			$('ynfeed_checkin').addClass('checkin_selected');
			$('ynfeed_checkin').style.display = 'none';
		}
		if($('checkin-button'))
			if(!$('checkin-button').hasClass('checkin_active'))
				$('checkin-button').addClass('checkin_active');
		
		if($('business-button'))
		{
			$('business-button').style.display = 'none';
		}
		
		if($('ynfeed_atbusiness'))
		{
			$('ynfeed_atbusiness').style.display = 'none';
		}
			
		if($('ynfeed_mdash').innerHTML == '')
		{
			$('ynfeed_mdash').innerHTML = ' — ';
		}
		if($('ynfeed_dot'))
			$('ynfeed_dot').innerHTML = '.';
		if($('ynfeed_removeCheckin'))
			$('ynfeed_removeCheckin').style.display = 'inline-block';
			
		if($('ynfeed_checkin_display'))
		{
			checkin = $('ynfeed_checkin_display');
		}
		else
		{
			return;
		}
		
		checkin.innerHTML = "";
		var myElement = new Element("span");
		myElement.innerHTML = " " + langs['at'] + " <a href='javascript:void(0);' onclick='openCheckin()'>" + address + "</a>";
		myElement.addClass("ynfeed_checkinToken");
		checkin.appendChild(myElement);
		
		document.getElementById('checkin_lat').value = place.geometry.location.lat();		
		document.getElementById('checkin_long').value = place.geometry.location.lng();
    });
}
google.maps.event.addDomListener(window, 'load', initialize);