<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<div class="ynlocationbased_search_box" id="ynlocationbased_search_box">
    <div class="ynlocationbased_search_box_title">
        <?php echo $this -> translate("Search by Location")?>
        <span class="ynlocationbased_btn_clear" title="<?php echo $this -> translate("Clear search")?>"><?php echo $this -> translate("Clear search")?></span>
    </div>
    <form id="ynlocationbased_form" method="GET" onsubmit="return validate_submit()">
        <div class="ynlocationbased_search_input">
            <input onkeypress="onKeyPressSearchBased()" placeholder="<?php echo $this -> translate("Enter a location");?>" name="location" id="ynlocation_location" type="text">
            <a class='ynlocationbased_location_icon' href="javascript:void()" onclick="return getCurrentLocationBased(this);" >
                <i class="fa fa-map-marker"></i>
            </a>
        </div>
        <div class="ynlocationbased_radius_input">
            <input onkeypress="onKeyPressRadius()" placeholder="<?php echo $this -> translate("Radius (miles)");?>" class="text" name="within" id="ynlocation_radius" type="number" min="0" value="">
        </div>
        <input type="hidden" name="lat" id="ynlocation_lat">
        <input type="hidden" name="long" id="ynlocation_long">
        <button type="submit" name="location_submit_search">
            <i class="fa fa-search"></i>
        </button>
    </form>
</div>
<script type="text/javascript">
    function ynlocation_initialize() {
        var input = $('ynlocation_location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry)
                return;
            $('ynlocation_lat').value = place.geometry.location.lat();
            $('ynlocation_long').value = place.geometry.location.lng();
        });
    }
    google.maps.event.addDomListener(window, 'load', ynlocation_initialize);
    var getCurrentLocationBased = function(obj)
    {
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = new google.maps.LatLng(position.coords.latitude,
                        position.coords.longitude);
                if(pos)
                {
                    var current_posstion = new Request.JSON({
                        'format' : 'json',
                        'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynlocationbased_general') ?>',
                        'data' : {
                            latitude : pos.lat(),
                            longitude : pos.lng(),
                        },
                        'onSuccess' : function(json, text) {
                            if(json.status == 'OK')
                            {
                                $('ynlocation_location').value = json.results[0].formatted_address;
                                $('ynlocation_lat').value = json.results[0].geometry.location.lat;
                                $('ynlocation_long').value = json.results[0].geometry.location.lng;
                            }
                            else{
                                handleNoGeolocationBased(true);
                            }
                        }
                    });
                    current_posstion.send();
                }
            }, function() {
                handleNoGeolocationBased(true);
            });
        }
        else {
            handleNoGeolocationBased(false);
        }
        return false;
    }

    function handleNoGeolocationBased(errorFlag) {
        if (errorFlag)
            $('ynlocation_location').value = 'Error: The Geolocation service failed.';
        else
            $('ynlocation_location').value = 'Error: Your browser doesn\'t support geolocation.';
    }
    var onKeyPressSearchBased = function()
    {
        var keyPressed = event.keyCode || event.which;
        if(keyPressed == 13)
            if(validate_submit())
                $('ynlocationbased_form').submit();
    }
    var radiusEmpty = true;
    var onKeyPressRadius = function()
    {
        if($('ynlocation_radius').value.length)
        {
            radiusEmpty = false;
        }
        var keyPressed = event.keyCode || event.which;
        if(keyPressed == 13)
            if(validate_submit())
                $('ynlocationbased_form').submit();
    }

    var validate_submit = function()
    {
        if((!$('ynlocation_radius').value && !radiusEmpty) || isNaN($('ynlocation_radius').value) || ($('ynlocation_radius').value && $('ynlocation_radius').value != parseInt($('ynlocation_radius').value)))
            return false;
        if(!$('ynlocation_location').value) {
            $('ynlocation_lat').value = '';
            $('ynlocation_long').value = '';
        }
        $('ynlocation_radius').value = parseInt($('ynlocation_radius').value);
        Cookie.write('ynlocationbased_location', $('ynlocation_location').value, {duration: 30});
        Cookie.write('ynlocationbased_lat', $('ynlocation_lat').value, {duration: 30});
        Cookie.write('ynlocationbased_long', $('ynlocation_long').value, {duration: 30});
        Cookie.write('ynlocationbased_radius', $('ynlocation_radius').value, {duration: 30});
        return true;
    }

    window.addEvent('domready', function() {
        var location_cookie = Cookie.read('ynlocationbased_location');
        var lat_cookie = Cookie.read('ynlocationbased_lat');
        var long_cookie = Cookie.read('ynlocationbased_long');
        var radius_cookie = Cookie.read('ynlocationbased_radius');
        $('ynlocation_location').value = location_cookie;
        $('ynlocation_lat').value = lat_cookie;
        $('ynlocation_long').value = long_cookie;
        $('ynlocation_radius').value = radius_cookie;
        <?php if(!$this -> hasWithIn):?>
            if($$('form.global_form_box #location').length)
                $$('form.global_form_box #location')[0].value = location_cookie;
            if($$('form.global_form_box #lat').length)
                $$('form.global_form_box #lat')[0].value = lat_cookie;
            if($$('form.global_form_box #long').length)
                $$('.global_form_box #long')[0].value = long_cookie;
            if($$('form.global_form_box #within').length)
                $$('form.global_form_box #within')[0].value = radius_cookie;
            if($$('form.field_search_criteria #location').length)
                $$('form.field_search_criteria #location')[0].value = location_cookie;
            if($$('form.field_search_criteria #lat').length)
                $$('form.field_search_criteria #lat')[0].value = lat_cookie;
            if($$('form.field_search_criteria #long').length)
                $$('form.field_search_criteria #long')[0].value = long_cookie;
            if($$('form.field_search_criteria #within').length)
                $$('form.field_search_criteria #within')[0].value = radius_cookie;
        <?php endif;?>
        // Add search button to header
        var mini_menu_element = $$('#core_menu_mini_menu > ul');
        var tagName = 'li';
        if($$('.group-mini-menu').length || $$('.group-menu-mini').length || $$('.custom-header-mini-menu-nlogin').length) {
            tagName = 'div';
            if($$('.custom-header-mini-menu-nlogin').length)
                mini_menu_element = $$('.custom-header-mini-menu-nlogin');
            else
                mini_menu_element = $$('.group-mini-menu');
        }
        var location_button = new Element(tagName, {
            'class': 'ynlocationbased_menu_item',
            html: '<span class="ynlocationbased_search_button"><i class="fa fa-map-marker" aria-hidden="true"></i></span>',
            title: '<?php echo $this -> translate("Click to insert current location")?>'
        });
        if($('global_search_form_container') || $$('.global_search_form_container').length) {
            if($$('.global_search_form_container').length)
                location_button.inject($$('.global_search_form_container')[0], 'after');
            else if($('global_search_form_container'))
                location_button.inject($('global_search_form_container'), 'after');
        }
        else if(mini_menu_element.length) {
            location_button.inject(mini_menu_element[0], 'bottom');
        }
        $('ynlocationbased_search_box').inject(location_button, 'bottom');

        if($('ynlocation_radius').value && $('ynlocation_location').value) {
            if($$('.ynlocationbased_search_button').length && Cookie.read('ynlocationbased_popup_status') != 'none'){
                setPositionPopup($$('.ynlocationbased_search_button')[0]);
                $$('.ynlocationbased_search_button').addClass('active');
            }
        }
        // Set position for popup
        $$('.ynlocationbased_search_button').addEvent('click',function(){
            setPositionPopup(this);
            this.toggleClass('active');
            var ynlocationbased_popup_status = $$('.ynlocationbased_search_box').getStyle('display');
            Cookie.write('ynlocationbased_popup_status',ynlocationbased_popup_status);
        });
    });

    //Get position for popup.
    function setPositionPopup(el) {
        var ynlocationbased_popup = $$('.ynlocationbased_search_box');
        var ynlocationbased_position_minus = el.getPosition().x
        //Check header have class container position relative
        var ynlocationbased_navbar = el.getParents('.navbar');
        var ynlocationbased_minimenu = el.getParents('.layout_ynresponsivepurity_mini_menu');
        var ynlocationbased_advmenu = el.getParents('.ynadvanced-menu-mini > ul');

        if(ynlocationbased_navbar.length)
            ynlocationbased_position_minus = ynlocationbased_position_minus - ynlocationbased_navbar[0].getPosition().x;
        else if(ynlocationbased_minimenu.length)
            ynlocationbased_position_minus = ynlocationbased_position_minus - ynlocationbased_minimenu[0].getPosition().x;
        else if(ynlocationbased_advmenu.length)
            ynlocationbased_position_minus = ynlocationbased_position_minus - ynlocationbased_advmenu[0].getPosition().x;

        if(ynlocationbased_popup.length){
            ynlocationbased_popup[0].toggle();
            var ynlocationbased_popup_position = ynlocationbased_position_minus + el.getWidth() * 1.3 - ynlocationbased_popup[0].getWidth();
            ynlocationbased_popup.setStyle('left', ynlocationbased_popup_position + 'px');
        }
    }

    //Clear value input
    $$('.ynlocationbased_btn_clear').addEvent('click',function(){
        $('ynlocation_location').value = ("");
        $('ynlocation_radius').value = ("");
    })
</script>
