<?php if ($this->error) : ?>
<div class="tip">
    <span><?php echo $this->message?></span>
</div>
<?php else :?>
	
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
  	
  	if($$('.item-form-theme-choose').length > 0)
		$$('.item-form-theme-choose input')[0].setProperty('checked', 'true');
	
	if($$('.item-form-theme-choose').length > 0)
	{
	  	$$('.item-form-theme-choose').addEvent('click', function(){
	  		this.getElements('input')[0].set('checked','true');
	  	});
    }
  	
  	<?php if($this -> number_location_index != 0) :?>
  		var index_location = <?php echo $this -> number_location_index;?>;
  	<?php else:?>
  		var index_location = 0;
  	<?php endif;?>
  	
  	//for addmore location
  	$('add_more_location').addEvent('click', function(event) {
  		index_location++;
  		$('number_location_index').set('value', index_location);
  		$('number_location').set('value', parseInt($('number_location').value) + 1);
  		addMoreAddLocation(this, event, index_location);
  	}); 	
  	function addMoreAddLocation(obj, e, index) {
        e.preventDefault();
        var oriAddInfo_location = $('location-wrapper');
        var oriAddInfo_location_title = $('location_title');
        var oriAddInfo_location_address = $('location_address');
        var oriAddInfo_lat = $('lat');
        var oriAddInfo_long = $('long');
        
        var newAddInfo_location = oriAddInfo_location.clone(true);
        newAddInfo_location.set('class', 'sub_location-wrapper form-wrapper');
        var newAddInfo_location_title = oriAddInfo_location_title.clone(true);
        var newAddInfo_location_address = oriAddInfo_location_address.clone(true);
        var newAddInfo_lat = oriAddInfo_lat.clone(true);
        var newAddInfo_long = oriAddInfo_long.clone(true);
        
        var children = newAddInfo_location.getChildren();
        children[0].getChildren()[1].getChildren()[0].set('name','location_title_'+index_location);
        children[0].getChildren()[1].getChildren()[0].set('id','location_title_'+index_location);
        children[1].getChildren()[1].getChildren()[0].set('name','location_'+index_location);
        children[1].getChildren()[1].getChildren()[0].set('id','location_'+index_location);
        $strNewAddInfo_location = newAddInfo_location.innerHTML;
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'location'", "'location_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'location_title'", "'location_title_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'location_address'", "'location_address_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'lat'", "'lat_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'long'", "'long_"+index_location+"'");
        
        newAddInfo_location.innerHTML = $strNewAddInfo_location;
        
        newAddInfo_location_address.set('name','location_address_'+index_location);
        newAddInfo_location_address.set('id','location_address_'+index_location);
        var newAddInfo_location_address_ID = newAddInfo_location_address.get('id');
        
        newAddInfo_lat.set('name','lat_'+index_location);
        newAddInfo_lat.set('id','lat_'+index_location);
        var newAddInfo_lat_ID = newAddInfo_lat.get('id');
        
        newAddInfo_long.set('name','long_'+index_location);
        newAddInfo_long.set('id','long_'+index_location);
        var newAddInfo_long_ID = newAddInfo_long.get('id');
        
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-location',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent().destroy();
                    $(newAddInfo_location_address_ID).remove();
                    $(newAddInfo_lat_ID).remove();
                    $(newAddInfo_long_ID).remove();
                    $('number_location').set('value', $('number_location').value - 1);
                }
            }
        });
        newAddInfo_location.grab(remove);
        newAddInfo_location.inject('photo-wrapper', 'before');
        newAddInfo_location_address.inject('photo-wrapper', 'before');
        newAddInfo_lat.inject('photo-wrapper', 'before');
        newAddInfo_long.inject('photo-wrapper', 'before');
        google.maps.event.addDomListener(window, 'load', initialize('location_'+index_location, 'location_address_'+index_location,'lat_'+index_location,'long_'+index_location));
       	var return_array = [newAddInfo_location_address, newAddInfo_lat, newAddInfo_long]; 
        return return_array;
    }
  	
  	//for addmore category
  	var number_category = 1;
  	$('add_more_category').addEvent('click', function(event) {
  		addMoreAddCategory(this, event);
  	}); 	
  	function addMoreAddCategory(obj, e) {
        e.preventDefault();
        var oriAddInfo = $('category_id-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('class','sub_category btn_form_inline');
        children[1].getChildren()[0].set('name','sub_category[]');
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-category',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                    number_category--;
                    if(number_category < 3)
                    {
                        var oriAddInfo = $('add_more_category');
                        if(oriAddInfo)
                        	oriAddInfo.setStyle('display', 'inline-block');
                    }
                    
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('category_id-wrapper', 'after');
        number_category++;
        if(number_category == 3)
        {
           var oriAddInfo = $('add_more_category');
           if(oriAddInfo)
           		oriAddInfo.setStyle('display', 'none');
        }
        return newAddInfo;
    }
  	
  	$('category_id').addEvent('change', function(){
      $(this).getParent('form').submit(); 
    }); 
  	
  	if($('0_0_1-wrapper'))
  	{
  		$('0_0_1-wrapper').setStyle('display','none');
  	}
  	
  function removeSubmit()
  {
   $('buttons-wrapper').hide();
  }
  		//TODO popylate data back
  		<?php if(!empty($this -> posts['sub_category'])):?>
  			number_category = 1;
	  		<?php foreach($this -> posts['sub_category'] as $sub_category_id) :?>
				var event = document.createEvent('Event');
		  		var newAddInfo = addMoreAddCategory(null, event);
		  		var children = newAddInfo.getChildren();
		  		children[1].getChildren()[0].set('value', '<?php echo $sub_category_id ?>');
		  		if(number_category == 3)
		        {
		        	var oriAddInfo = $('add_more_category');
		        	if(oriAddInfo)
		        	{
		        		oriAddInfo.setStyle('display', 'none');
		        	}
		        }
			<?php endforeach;?>
		<?php endif;?>
		
		<?php if(!empty($this -> subLocation)) :?>
			<?php foreach($this -> subLocation as $location) :?>
		  		index_location++;
		  		$('number_location_index').set('value', index_location);
		  		var event = document.createEvent('Event');
		  		var return_array = addMoreAddLocation(null, event);
		  		var newAddInfo_location_address = return_array[0];
		  		var newAddInfo_lat = return_array[1];
		  		var newAddInfo_long = return_array[2];
		  		var title_location_name = 'location_title_'+ index_location;
		  		var address_location_name = 'location_'+ index_location;
		  		$(address_location_name).set('value', '<?php echo $location -> location ?>');
		  		$(title_location_name).set('value', '<?php echo $location -> title?>');
		  		newAddInfo_location_address.set('value', '<?php echo $location -> location ?>');
		  		newAddInfo_lat.set('value', '<?php echo $location -> latitude ?>');
		  		newAddInfo_long.set('value', '<?php echo $location -> longitude ?>');
		  	<?php endforeach;?>
	  	<?php endif;?>
	  	
	  	<?php if(!empty($this -> posts['location'])) :?>
			$('location').set('value', "<?php echo $this -> posts['location'];?>");
		<?php endif;?>
		
		<?php if(!empty($this -> posts['location_title'])) :?>
			$('location_title').set('value', '<?php echo $this -> posts['location_title'];?>');
		<?php endif;?>
  
});
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
      'topLevelId' => (int) @$this->topLevelId,
      'topLevelValue' => (int) @$this->topLevelValue
    ))
?>

<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>
<div class='global_form'>
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  en4.core.runonce.add(function()
  {
    
   if($('text'))
    {
      new OverText($('text'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
  });
  
  
  function placeChanged(autocomplete, location, location_address, lat, long) {
		google.maps.event.addListener(autocomplete, 'place_changed', function(){
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }
			document.getElementById(location_address).value = place.formatted_address;		
			document.getElementById(lat).value = place.geometry.location.lat();		
			document.getElementById(long).value = place.geometry.location.lng();
		});
    }
  
  function initialize(location, location_address, lat, long) {
		var input = /** @type {HTMLInputElement} */(
					document.getElementById(location));
	  	var autocomplete = new google.maps.places.Autocomplete(input);
	  	placeChanged(autocomplete, location, location_address, lat, long);
  }
  
  google.maps.event.addDomListener(window, 'load', initialize('location', 'location_address','lat','long')); 
  
  var getCurrentLocation = function(obj, location, location_address, lat, long )
	{	
		if(navigator.geolocation) {
			
	    	navigator.geolocation.getCurrentPosition(function(position) {
	    			
	      	var pos = new google.maps.LatLng(position.coords.latitude,
	                                       position.coords.longitude);
	        
			if(pos)
			{
				
				current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynbusinesspages_general') ?>',
					'data' : {
						latitude : pos.lat(),
						longitude : pos.lng(),
					},
					'onSuccess' : function(json, text) {
						
						if(json.status == 'OK')
						{
							document.getElementById(location).value = json.results[0].formatted_address;
							document.getElementById(location_address).value = json.results[0].formatted_address;
							document.getElementById(lat).value = json.results[0].geometry.location.lat;		
							document.getElementById(long).value = json.results[0].geometry.location.lng; 		
						}
						else{
							handleNoGeolocation(true,location);
						}
					}
				});	
				current_posstion.send();
				
			}
	      	
	    	}, function() {
	      		handleNoGeolocation(true,location);
	    	});
	  	}
	  	else {
    		// Browser doesn't support Geolocation
    		handleNoGeolocation(false,location);
  		}
		return false;
	}
	
	function handleNoGeolocation(errorFlag, location) {
  		if (errorFlag) {
    		document.getElementById(location).value = 'Error: The Geolocation service failed.';
  		} 
  		else {
   			document.getElementById(location).value = 'Error: Your browser doesn\'t support geolocation.';
   		}
 	}
</script>


<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<?php endif; ?>