<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = $this->resume;
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $create = (isset($params['create'])) ? $params['create'] : false;
    $edit = (isset($params['edit'])) ? $params['edit'] : false;
    $hide = (isset($params['hide'])) ? $params['hide'] : false;
?>
<?php
$contact = $resume->email;
if (count($contact) <= 0 && $manage) {
    $create = true;
}
?>
<?php if (!empty($contact) || (!$hide && ($create || $edit))) : ?>
<?php $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section); ?>
    <h3 class="section-label">
        <span class="section-label-icon"><i class="<?php echo Engine_Api::_()->ynresume()->getSectionIconClass($this->section);?>"></i></span>
        <span><?php echo $label;?></span>
    </h3>
    
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    
    <div class="ynresume-section-content">
<?php if ($manage) : ?>
    <?php if (!$hide && ($create || $edit)) : ?>
    	<?php
	    $this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Locale.en-US.DatePicker.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Picker.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Picker.Attach.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Picker.Date.js');
	    
	    $this->headLink() 
		->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/styles/picker/datepicker_dashboard.css');
		?>

    <?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"); ?>
    <div id="ynresume-section-form-contact" class="ynresume-section-form">
        <form rel="contact" class="section-form">
            <p class="error"></p>
            <?php if ($edit && isset($params['item_id'])) : ?>
            <input type="hidden" name="item_id" class="item_id" id="contact-1" value='1' />
            <?php endif; ?>

            <div id="contact-birthday-wrapper" class="ynresume-form-wrapper">
                <label for="contact-birthday"><?php echo $this->translate('Date of Birth')?></label>
                <div class="ynresume-form-input">
            	   <input type="text" name="birth_day" id="birth_day" value="<?php if ($resume && $resume->birth_day) echo date("m/d/Y", strtotime($resume->birth_day))?>" class="date_picker input_small">
                   <p class="error"></p>
                </div>
            </div>           
            
            <div id="contact-gender-wrapper" class="ynresume-form-wrapper">            	
            	<label for="contact-gender"><?php echo $this->translate('Gender')?></label>
                <div class="ynresume-form-input">
    				<li><input type="radio" name="gender" id="gender-1" value="1" <?php if ($resume && $resume -> gender == 1) echo 'checked="checked"';?>><label for="gender-1"><?php echo $this->translate('Male')?></label></li>
    				<li><input type="radio" name="gender" id="gender-0" value="0" <?php if ($resume && $resume -> gender == 0) echo 'checked="checked"';?>><label for="gender-0"><?php echo $this->translate('Female')?></label></li>
                    <p class="error"></p>
                </div>
			</div>
			
			<div id="contact-marial-wrapper" class="ynresume-form-wrapper">            	
            	<label for="contact-marial"><?php echo $this->translate('Marital Status')?></label>
                <div class="ynresume-form-input">
    				<li><input type="radio" name="marial_status" id="marial-1" value="1" <?php if ($resume && $resume -> marial_status == 1) echo 'checked="checked"';?>><label for="marial-1"><?php echo $this->translate('Single')?></label></li>
    				<li><input type="radio" name="marial_status" id="marial-0" value="0" <?php if ($resume && $resume -> marial_status == 0) echo 'checked="checked"';?>><label for="marial-0"><?php echo $this->translate('Married')?></label></li>
                    <p class="error"></p>
                </div>
			</div>
			
            <div id="contact-nationality-wrapper" class="ynresume-form-wrapper">
                <label for="contact-nationality"><?php echo $this->translate('Nationality')?></label>
                <div class="ynresume-form-input">
                    <input type="text" id="contact-nationality" name="nationality" value="<?php if ($resume) echo $resume->nationality?>"/>
                    <p class="error"></p>
                </div>
            </div>
            
            <div id="contact-phone-wrapper" class="ynresume-form-wrapper">                
                <label for="contact-phone"><?php echo $this->translate('*Phone')?></label>
                <div class="ynresume-form-input">
                    <input type="text" id="contact-phone" name="phone" value="<?php if ($resume) echo $resume->phone?>"/>
                    <p class="error"></p>
                </div>
            </div>
            
            <div id="contact-email-wrapper" class="ynresume-form-wrapper">                
                <label for="contact-email"><?php echo $this->translate('*Email')?></label>
                <div class="ynresume-form-input">
                    <input type="text" id="contact-email" name="email" value="<?php if ($resume) echo $resume->email?>"/>
                    <p class="error"></p>
                </div>
            </div>
            
           <div id="contact-location-wrapper" class="ynresume-form-wrapper">                
                <label for="contact-location"><?php echo $this->translate('Address')?></label>
                <div class="ynresume-form-input ynresume-form-input-map">
                    <input type="text" id="contact-location" name="contact_location" value="<?php if ($resume) echo $resume->contact_location?>"/>
                    <a class='ynresume_location_icon' href="javascript:void(0)" id='contact-get-current-location'>
                        <img src="<?php echo $this -> baseUrl();?>/application/modules/Ynresume/externals/images/icon-search-advform.png">
                    </a>
                    <input type="hidden" id="contact-longitude" name="contact_longitude" value="<?php if ($resume) echo $resume->contact_longitude?>"/>
                    <input type="hidden" id="contact-latitude" name="contact_latitude" value="<?php if ($resume) echo $resume->contact_latitude?>"/>
                    <p class="error"></p>
                </div>
            </div>
            
            <div class="ynresume-form-buttons ynresume-form-wrapper">
                <label></label>
                <div class="ynresume-form-input">
                    <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                    <button type="button" class="ynresume-cancel-btn"><?php echo $this->translate('Cancel')?></button>
                </div>
            </div>            
        </form>
    </div>
    <script type="text/javascript">
        //add event for form
        window.addEvent('domready', function() {
        	
	        new Picker.Date($$('.date_picker'), { 
	            positionOffset: {x: 5, y: 0}, 
	            pickerClass: 'datepicker_dashboard', 
	            useFadeInOut: !Browser.ie,
	            onSelect: function(date){
	            }
	        });
        	
            $('contact-get-current-location').addEvent('click', function(){
                getCurrentLocation();   
            });
            
            initialize();
            google.maps.event.addDomListener(window, 'load', initialize); 
        });
        
        function initialize() {
            var input = /** @type {HTMLInputElement} */(
                document.getElementById('contact-location'));
        
            var autocomplete = new google.maps.places.Autocomplete(input);
        
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                document.getElementById('contact-latitude').value = place.geometry.location.lat();     
                document.getElementById('contact-longitude').value = place.geometry.location.lng();
            });
        }
      
        function getCurrentLocation () {   
            if(navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                if(pos){
                    current_posstion = new Request.JSON({
                        'format' : 'json',
                        'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynresume_general') ?>',
                        'data' : {
                            latitude : pos.lat(),
                            longitude : pos.lng(),
                        },
                        'onSuccess' : function(json, text) {
                            if(json.status == 'OK') {
                                document.getElementById('contact-location').value = json.results[0].formatted_address;
                                document.getElementById('contact-latitude').value = json.results[0].geometry.location.lat;     
                                document.getElementById('contact-longitude').value = json.results[0].geometry.location.lng;        
                            }
                            else {
                                handleNoGeolocation(true);
                            }
                        }
                    }); 
                    current_posstion.send();
                }
                }, function() {
                    handleNoGeolocation(true);
                });
            }
            else {
                // Browser doesn't support Geolocation
                handleNoGeolocation(false);
            }
            return false;
        }
        
        function handleNoGeolocation(errorFlag) {
            if (errorFlag) {
                document.getElementById('contact-location').value = 'Error: The Geolocation service failed.';
            } 
            else {
                document.getElementById('contact-location').value = 'Error: Your browser doesn\'t support geolocation.';
            }
        }
    </script>
    <?php endif;?>
<?php endif;?>
<?php if (!empty($contact)) : ?>
<div id="ynresume-section-list-contact" class="ynresume-section-list">
    <ul id="contact-list" class="section-list">
    <li class="section-item" id="contact-<?php echo $resume->getIdentity()?>">
    	
    	<?php
    		$birthDayObject = null;
			if (!is_null($resume->birth_day) && !empty($resume->birth_day) && $resume->birth_day) 
			{
				$birthDayObject = new Zend_Date(strtotime($resume->birth_day));	
			}
    	?>
        <div class="contact-content hidden visible_theme_4">
            <div>
                <?php if(!is_null($birthDayObject)) :?>
                <div class="contact-birthday inline_theme_4">
                    <span class="label"><?php echo $this->translate('Date of Birth')?></span>
                    <span class="value"><?php echo date('M d Y', $birthDayObject -> getTimestamp());?></span>
                </div>
                <?php endif;?>
                
                <div class="contact-gender inline_theme_4">
                    <span class="label"><?php echo $this->translate('Gender')?></span>
                    <span class="value"><?php echo ($resume->gender)? $this -> translate("Male") : $this -> translate("Female")?></span>
                </div>
            </div>
            
            <div>
                <div class="contact-marital inline_theme_4">
                    <span class="label"><?php echo $this->translate('Marital Status')?></span>
                    <span class="value"><?php echo ($resume->marial_status)? $this -> translate("Single") : $this -> translate("Married")?></span>
                </div>
                
                <?php if ($resume->nationality) : ?>
                <div class="contact-nationality inline_theme_4">
                    <span class="label"><?php echo $this->translate('Nationality')?></span>
                    <span class="value"><?php echo $resume->nationality?></span>
                </div>
                <?php endif;?>
            </div>
            
            <div>
                <?php if ($resume->email) : ?>
                <div class="contact-email inline_theme_4">
                    <span class="label"><?php echo $this->translate('Email')?></span>
                    <span class="value"><a href="mailto:<?php echo $resume->email?>"><?php echo $resume->email?></a></span>
                </div>
                <?php endif;?>
                
                <?php if ($resume->phone) : ?>
                <div class="contact-phone inline_theme_4">
                    <span class="label"><?php echo $this->translate('Phone Number')?></span>
                    <span class="value"><?php echo $resume->phone?></span>
                </div>
                <?php endif;?>
            </div>
            <div>  

                <?php if ($resume->contact_location) : ?>
                    <div class="contact-address">
                        <span class="label"><?php echo $this->translate('Address')?></span>
                        <span class="value"><?php echo $resume->contact_location?></span>
                    </div>
                <?php endif;?>

            </div>
        </div>

    	<div class="contact-content sub-section-item hidden_theme_4">
        	<?php if(!is_null($birthDayObject)) :?>
        	<div class="contact-birthday">
            	<span class="label"><?php echo $this->translate('Date of Birth')?></span>
                <span class="value"><?php echo date('M d Y', $birthDayObject -> getTimestamp());?></span>
            </div>
        	<?php endif;?>
        	
        	<div class="contact-gender">
            	<span class="label"><?php echo $this->translate('Gender')?></span>
                <span class="value"><?php echo ($resume->gender)? $this -> translate("Male") : $this -> translate("Female")?></span>
            </div>
            
             <div class="contact-marital">
            	<span class="label"><?php echo $this->translate('Marital Status')?></span>
                <span class="value"><?php echo ($resume->marial_status)? $this -> translate("Single") : $this -> translate("Married")?></span>
            </div>
        	
        	<?php if ($resume->nationality) : ?>
            <div class="contact-nationality">
            	<span class="label"><?php echo $this->translate('Nationality')?></span>
                <span class="value"><?php echo $resume->nationality?></span>
            </div>
            <?php endif;?>
            
            <?php if ($resume->email) : ?>
            <div class="contact-email">
            	<span class="label"><?php echo $this->translate('Email')?></span>
                <span class="value"><a href="mailto:<?php echo $resume->email?>"><?php echo $resume->email?></a></span>
            </div>
            <?php endif;?>
            
            <?php if ($resume->phone) : ?>
            <div class="contact-phone">
            	<span class="label"><?php echo $this->translate('Phone Number')?></span>
                <span class="value"><?php echo $resume->phone?></span>
            </div>
            <?php endif;?>

            <?php if ($resume->contact_location) : ?>
                <div class="contact-address">
                    <span class="label"><?php echo $this->translate('Address')?></span>
                    <span class="value"><?php echo $resume->contact_location?></span>
                </div>
            <?php endif;?>
        </div>


        
        <?php if ($manage) : ?>
        <a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a>
        <?php endif; ?>
    </li>
    </ul>
</div>    
</div>
<?php endif;?>
<?php endif; ?>