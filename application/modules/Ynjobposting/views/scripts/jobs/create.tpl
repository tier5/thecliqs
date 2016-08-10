<?php if ($this->error) : ?>
    <div class="tip">
        <span><?php echo $this->message;?></span>
    </div>
<?php else: ?>
    <?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>
    <?php
      $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
    ?>
    <div class='global_form'>
      <?php echo $this->form->render($this) ?>
    </div>
    
    <!-- script for bind event-->
    <script type="text/javascript">
        window.addEvent('domready', function () {
            if ($('negotiable')) {
                if ($('negotiable').checked) {
                    $('salary_from').set('disabled', true);
                    $('salary_to').set('disabled', true);
                    $('salary_from').set('value', '');
                    $('salary_to').set('value', '');
                    $('salary_currency').set('disabled', true);
                    $('salary_from').addClass('disabled');
                    $('salary_to').addClass('disabled');
                    $('salary_currency').addClass('disabled');
                }
                else {
                    $('salary_from').set('disabled', false);
                    $('salary_to').set('disabled', false);
                    $('salary_currency').set('disabled', false);
                    $('salary_from').removeClass('disabled');
                    $('salary_to').removeClass('disabled');
                    $('salary_currency').removeClass('disabled');
               }
            }
            
            // disable salary input when choose negotiable
            $('negotiable').addEvent('click', function() {
               if (this.checked) {
                   $('salary_from').set('disabled', true);
                   $('salary_to').set('disabled', true);
                   $('salary_from').set('value', '');
                   $('salary_to').set('value', '');
                   $('salary_currency').set('disabled', true);
                   $('salary_from').addClass('disabled');
                   $('salary_to').addClass('disabled');
                   $('salary_currency').addClass('disabled');
               }
               else {
                   $('salary_from').set('disabled', false);
                   $('salary_to').set('disabled', false);
                   $('salary_currency').set('disabled', false);
                   $('salary_from').removeClass('disabled');
                   $('salary_to').removeClass('disabled');
                   $('salary_currency').removeClass('disabled');
               } 
            });
            
            if ($('feature') && $('feature_period')) {
                if ($('feature').checked) {
                    $('feature_period').set('disabled', false);
                    $('feature_period').removeClass('disabled');
                }
                else {
                    $('feature_period').set('disabled', true);
                    $('feature_period').addClass('disabled');
                }
                
                // disable feature period input when choose no-feature
                $('feature').addEvent('click', function() {
                   if (this.checked) {
                       $('feature_period').set('disabled', false);
                       $('feature_period').removeClass('disabled');
                   }
                   else {
                       $('feature_period').set('disabled', true);
                       $('feature_period').addClass('disabled');
                   } 
                }); 
            }
            
            //add button add more
            var addBtn = new Element('a', {
                href: 'javascript:void(0)',
                'class': 'fa fa-plus-circle add-remove-btn',
                events : {
                    click: function(event) {
                        addMoreAddInfo(this, event);
                    }
                }
            })
            addBtn.inject('header', 'after');
        });
        
        function addMoreAddInfo(obj, e) {
            e.preventDefault();
            var oriAddInfo = $('add_info-wrapper');
            var newAddInfo = oriAddInfo.clone(true);
            var index = $$('.add-info-job').length;
            newAddInfo.addClass('copy');
            newAddInfo.set('id', 'add_info_'+index+'-wrapper');
            var headerElement = newAddInfo.getElements('.form-element')[0];
            var input = headerElement.getElements('input[type="text"]')[0];
            input.value = '';
            input.set('name', 'header_'+index);
            var btn = headerElement.getElements('.add-remove-btn')[0];
            btn.set('class', 'fa fa-minus-circle add-remove-btn');
            btn.removeEvents('click').addEvent('click', function(event){
                event.preventDefault();
                this.getParent().getParent().getParent().getParent().destroy();
            });
            
            var contentElement = newAddInfo.getElements('.form-element')[1];
            var mce = contentElement.getElements('.mce-tinymce')[0];
            mce.destroy();
            var textarea = contentElement.getElements('textarea')[0];
            textarea.set('html', '');
            textarea.set('value', '');
            textarea.set('id', 'content_'+index);
            textarea.set('name', 'content_'+index);
            textarea.setStyle('display', 'block');
            var insertId = (index > 1) ? 'add_info_'+(index-1)+'-wrapper' : 'add_info-wrapper';
            newAddInfo.inject(insertId, 'after');
            tinymce.init({ mode: "exact", elements: "content_" + index, plugins: "table,fullscreen,media,preview,paste,code,image,textcolor", theme: "modern", menubar: false, statusbar: false, toolbar1: "undo,|,redo,|,removeformat,|,pastetext,|,code,|,media,|,image,|,link,|,fullscreen,|,preview", toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote", toolbar3: "", element_format: "html", height: "225px", convert_urls: false, language: "en", directionality: "ltr" });
        }
        
    </script>
    
    <!-- script for location-->
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
    					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynjobposting_general') ?>',
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
    
    <!-- script for autocomplete tags-->
    <script type="text/javascript">
    	en4.core.runonce.add(function() {
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
    </script>
    
    <!-- script for submit with draft-->
    <script type="text/javascript">
        function submitWithDraft() {
            $('published').value = 0;
            $('create_job_form').submit();
        }
    </script>
<?php endif; ?>