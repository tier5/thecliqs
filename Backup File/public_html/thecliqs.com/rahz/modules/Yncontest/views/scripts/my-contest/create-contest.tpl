<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	echo $this->content()->renderWidget('yncontest.main-menu') ?>

<div class = "contestcreate_midle">
	<?php if($this->checkGateway == 0):?>
		<div class="tip">
			<span>				
				<?php echo $this->translate("The payment account is not set. Please contact admin for more details!"); ?>
			</span>
		</div>
	<?php elseif(!$this->plugin):?>
		<div class="tip">
			<span>
				<?php echo $this->translate("There are no plug-in %s","Blogs, Videos, Albums or Music") ?>
			</span>
			</div>
	<?php else:?>
		<?php echo $this->form->render($this);?>
	<?php endif;?>
</div>

<script type="text/javascript">
  var cal_start_date_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_end_date.calendars[0].start = new Date( $('start_date-date').value );
    // redraw calendar
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', 1);
    cal_end_date.navigate(cal_end_date.calendars[0], 'm', -1);
  }
  var cal_end_date_onHideStart = function(){
	    // check start date and make it the same date if it's too
	    cal_start_date.calendars[0].end = new Date( $('end_date-date').value );
	    // redraw calendar
	    cal_start_date.navigate(cal_start_date.calendars[0], 'm', 1);
	    cal_start_date.navigate(cal_start_date.calendars[0], 'm', -1);
  }

  var cal_start_date_submit_entries_onHideStart = function(){
		// check end date and make it the same date if it's too
	    cal_end_date_submit_entries.calendars[0].start = new Date( $('start_date_submit_entries-date').value );
	    // redraw calendar
	    cal_end_date_submit_entries.navigate(cal_end_date_submit_entries.calendars[0], 'm', 1);
	    cal_end_date_submit_entries.navigate(cal_end_date_submit_entries.calendars[0], 'm', -1);
  }
  var cal_end_date_submit_entries_onHideStart = function(){
	    // check start date and make it the same date if it's too
	    cal_start_date_submit_entries.calendars[0].end = new Date( $('end_date_submit_entries-date').value );
	    // redraw calendar
	    cal_start_date_submit_entries.navigate(cal_start_date_submit_entries.calendars[0], 'm', 1);
	    cal_start_date_submit_entries.navigate(cal_start_date_submit_entries.calendars[0], 'm', -1);
  }

  var cal_start_date_vote_entries_onHideStart = function(){
	// check end date and make it the same date if it's too
	    cal_end_date_vote_entries.calendars[0].start = new Date( $('start_date_vote_entries-date').value );
	    // redraw calendar
	    cal_end_date_vote_entries.navigate(cal_end_date_vote_entries.calendars[0], 'm', 1);
	    cal_end_date_vote_entries.navigate(cal_end_date_vote_entries.calendars[0], 'm', -1);
  }
  var cal_end_date_vote_entries_onHideStart = function(){
	   // check start date and make it the same date if it's too
	    cal_start_date_vote_entries.calendars[0].end = new Date( $('end_date_vote_entries-date').value );
	    // redraw calendar
	    cal_start_date_vote_entries.navigate(cal_start_date_vote_entries.calendars[0], 'm', 1);
	    cal_start_date_vote_entries.navigate(cal_start_date_vote_entries.calendars[0], 'm', -1);
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
                      'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'yncontest_general') ?>',
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