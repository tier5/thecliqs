<?php if ($this->error) : ?>
<div class="tip">
    <span><?php echo $this->message?></span>
</div>
<?php else :?>
<link href="<?php $this->baseURL()?>application/modules/Ynlistings/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>


<script type="text/javascript">
  window.addEvent('domready', function() {
  	
  	<?php if(empty($this->theme)) :?>
  	if($$('.item-form-theme-choose').length > 0)
		$$('.item-form-theme-choose input')[0].setProperty('checked', 'true');
	<?php endif; ?>
	
	if($$('.item-form-theme-choose').length > 0)
	{
	  	$$('.item-form-theme-choose').addEvent('click', function(){
	  		this.getElements('input')[0].set('checked','true');
	  	});
    }
  	
	$$('.btn-preview-theme').addEvent('click', function(){
		var data_img = this.get('data-image');		
		var mypreview = new Element('div', {html: '<div class="preview-overlay"></div><div class="preview-main"><span class="btn-preview-popup-close">Close</span><img src="'+ data_img + '" /></div>'});
		mypreview.addClass('preview-popup');
		
		$$('.preview-popup').dispose();
		mypreview.inject( $$('body')[0] );
		
		$$('.btn-preview-popup-close').addEvent('click', function(){
		$$('.preview-popup').dispose();
		});	
	});
	
  	document.getElementById('toValues-wrapper').style.display = 'none';
  	
  	<?php if(empty($this->is_end)) :?>
  		$('end_date-wrapper').setStyle('display','none');
  	<?php else:?>
  		$('end_date-wrapper').setStyle('display','block');
  	<?php endif;?>
  	
  	if($('0_0_1-wrapper'))
  	{
  		$('0_0_1-wrapper').setStyle('display','none');
  	}
  	
  	$('is_end-0').addEvent('click', function(){
	    $('end_date-wrapper').setStyle('display','none');
	});
	
	$('is_end-1').addEvent('click', function(){
	    $('end_date-wrapper').setStyle('display','block');
	});
  	
    $('category_id').addEvent('change', function(){
      $(this).getParent('form').submit(); 
    }); 
  });
	
	function removeFromToValue(id) 
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = document.getElementById('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if (document.getElementById('toValues').value==""){
      document.getElementById('toValues-wrapper').style.display = 'none';
    }

    document.getElementById('to-wrapper').style.display = 'block';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
  }
	
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    
	
      new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('controller' => 'video', 'action' => 'suggest'), 'ynlistings_extended', true) ?>', {
       	'postVar' : 'text',
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){
          if(token.type == 'user'){
            var choice = new Element('li', {
              'class': 'autocompleter-choices',
              'html': token.photo,
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          else {
            var choice = new Element('li', {
              'class': 'autocompleter-choices friendlist',
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
        },
        onPush : function(){
          if( document.getElementById('toValues').value.split(',').length >= 1 ){
            document.getElementById('to-wrapper').style.display = 'none';
            document.getElementById('toValues-wrapper').style.display = 'block';
          }
        }
      });
  });

  function removeSubmit()
  {
   $('buttons-wrapper').hide();
  }
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
      'topLevelId' => (int) @$this->topLevelId,
      'topLevelValue' => (int) @$this->topLevelValue
    ))
?>

<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>
<?php
    echo $this->form->render($this)
?>

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
  
 
  function initialize() {
	 	var input = /** @type {HTMLInputElement} */(
			document.getElementById('location'));
	
	  	var autocomplete = new google.maps.places.Autocomplete(input);
	
	  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }
			document.getElementById('location_address').value = place.formatted_address;		
			document.getElementById('lat').value = place.geometry.location.lat();		
			document.getElementById('long').value = place.geometry.location.lng();
	    });
	}
  
   google.maps.event.addDomListener(window, 'load', initialize); 
  
  var getCurrentLocation = function(obj)
	{	
		if(navigator.geolocation) {
			
	    	navigator.geolocation.getCurrentPosition(function(position) {
	    			
	      	var pos = new google.maps.LatLng(position.coords.latitude,
	                                       position.coords.longitude);
	        
			if(pos)
			{
				
				current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynlistings_general') ?>',
					'data' : {
						latitude : pos.lat(),
						longitude : pos.lng(),
					},
					'onSuccess' : function(json, text) {
						
						if(json.status == 'OK')
						{
							document.getElementById('location').value = json.results[0].formatted_address;
							document.getElementById('location_address').value = json.results[0].formatted_address;
							document.getElementById('lat').value = json.results[0].geometry.location.lat;		
							document.getElementById('long').value = json.results[0].geometry.location.lng; 		
						}
						else{
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
    		document.getElementById('location').value = 'Error: The Geolocation service failed.';
  		} 
  		else {
   			document.getElementById('location').value = 'Error: Your browser doesn\'t support geolocation.';
   		}
 	}
</script>
<?php endif; ?>