<?php 
	$resume = $this -> resume;
	$view = Zend_Registry::get('Zend_View');
	 $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");
?>
<script type="text/javascript">
		(function () {
		
		function $(expr, con) {
			if (!expr) return null;
			return typeof expr === 'string'? (con || document).querySelector(expr) : expr;
		}
		
		function $$(expr, con) {
			return Array.prototype.slice.call((con || document).querySelectorAll(expr));
		}
		
		$.create = function(tag, o) {
			var element = document.createElement(tag);
			
			for (var i in o) {
				var val = o[i];
				
				if (i == "inside") {
					$(val).appendChild(element);
				}
				else if (i == "around") {
					var ref = $(val);
					ref.parentNode.insertBefore(element, ref);
					element.appendChild(ref);
				}
				else if (i in element) {
					element[i] = val;
				}
				else {
					element.setAttribute(i, val);
				}
			}
			
			return element;
		};
		
		$.bind = function(element, o) {
			if (element) {
				for (var event in o) {
					var callback = o[event];
					
					event.split(/\s+/).forEach(function (event) {
						element.addEventListener(event, callback);
					});
				}
			}
		};
		
		$.fire = function(target, type, properties) {
			var evt = document.createEvent("HTMLEvents");
					
			evt.initEvent(type, true, true );
		
			for (var j in properties) {
				evt[j] = properties[j];
			}
		
			target.dispatchEvent(evt);
		};
		
		var _ = self.Awesomplete = function (input, o) {
			var me = this;
			
			// Setup environment
			o = o || {};
			this.input = input;
			input.setAttribute("aria-autocomplete", "list");
			
			this.minChars = +input.getAttribute("data-minchars") || o.minChars || 1;
			this.maxItems = +input.getAttribute("data-maxitems") || o.maxItems || 100;
			
			if (input.hasAttribute("list")) {
				this.list = "#" + input.getAttribute("list");
				input.removeAttribute("list");
			}
			else {
				this.list = input.getAttribute("data-list") || o.list || [];
			}
			this.filter = o.filter || _.FILTER_CONTAINS;
			this.sort = o.sort || _.SORT_BYLENGTH;
			
			this.autoFirst = input.hasAttribute("data-autofirst") || o.autoFirst || false;
			
			this.item = o.item || function (text, input) {
				return $.create("li", {
					innerHTML: text.replace(RegExp(regEscape(input.trim()), "gi"), "<mark>$&</mark>"),
					"aria-selected": "false"
				});	
			};
			
			this.index = -1;
			
			this.container = $.create("div", {
				className: "awesomplete",
				around: input
			});
			
			this.ul = $.create("ul", {
				hidden: "",
				inside: this.container
			});
			
			// Bind events
			
			$.bind(this.input, {
				"input": function () {
					me.evaluate();
				},
				"blur": function () {
					me.close();
				},
				"keydown": function(evt) {
					var c = evt.keyCode;
					
					if (c == 13 && me.index > -1) { // Enter
						evt.preventDefault();
						me.select();
					}
					else if (c == 27) { // Esc
						me.close();
					}
					else if (c == 38 || c == 40) { // Down/Up arrow
						evt.preventDefault();
						me[c == 38? "previous" : "next"]();
					}
				}
			});
			
			$.bind(this.input.form, {"submit": function(event) {
				me.close();
			}});
			
			$.bind(this.ul, {"mousedown": function(evt) {
				var li = evt.target;
				
				if (li != this) {
					
					while (li && !/li/i.test(li.nodeName)) {
						li = li.parentNode;
					}
					
					if (li) {
						me.select(li);	
					}
				}
			}});
		};
		
		_.prototype = {
			set list(list) {
				if (Array.isArray(list)) {
					this._list = list;
				}
				else {
					if (typeof list == "string" && list.indexOf(",") > -1) {
						this._list = list.split(/\s*,\s*/);
					}
					else {
						list = $(list);
						if (list && list.children) {
							this._list = [].slice.apply(list.children).map(function (el) {
								return el.innerHTML.trim();
							});
						}
					}
				}
			},
			
			close: function () {
				this.ul.setAttribute("hidden", "");
				this.index = -1;
				
				$.fire(this.input, "awesomplete-close");
			},
			
			open: function () {
				this.ul.removeAttribute("hidden");
				
				if (this.autoFirst && this.index == -1) {
					this.goto(0);
				}
				
				$.fire(this.input, "awesomplete-open");
			},
			
			next: function () {
				var count = this.ul.children.length;
		
				this.goto(this.index < count - 1? this.index + 1 : -1);
			},
			
			previous: function () {
				var count = this.ul.children.length;
				
				this.goto(this.index > -1? this.index - 1 : count - 1);
			},
			
			// Should not be used, highlights specific item without any checks!
			goto: function (i) {
				var lis = this.ul.children;
				
				if (this.index > -1) {
					lis[this.index].setAttribute("aria-selected", "false");
				}
				
				this.index = i;
				
				if (i > -1 && lis.length > 0) {
					lis[i].setAttribute("aria-selected", "true");
				}
			},
			
			select: function (selected) {
				selected = selected || this.ul.children[this.index];
		
				if (selected) {
					var prevented;
					
					$.fire(this.input, "awesomplete-select", {
						text: selected.textContent,
						preventDefault: function () {
							prevented = true;
						}
					});
					
					if (!prevented) {
						this.input.value = selected.textContent;
						this.close();
						$.fire(this.input, "awesomplete-selectcomplete");
					}
				}
			},
			
			evaluate: function() {
				var me = this;
				var value = this.input.value;
						
				if (value.length >= this.minChars && this._list.length > 0) {
					this.index = -1;
					// Populate list with options that match
					this.ul.innerHTML = "";
		
					this._list.filter(function(item) {
						return me.filter(item, value);
					})
					.sort(this.sort)
					.every(function(text, i) {
						me.ul.appendChild(me.item(text, value));
						
						return i < me.maxItems - 1;
					});
					
					this.open();
				}
				else {
					this.close();
				}
			}
		};
		
		_.FILTER_CONTAINS = function (text, input) {
			return RegExp(regEscape(input.trim()), "i").test(text);
		};
		
		_.FILTER_STARTSWITH = function (text, input) {
			return RegExp("^" + regEscape(input.trim()), "i").test(text);
		};
		
		_.SORT_BYLENGTH = function (a, b) {
			if (a.length != b.length) {
				return a.length - b.length;
			}
			
			return a < b? -1 : 1;
		};
		
		function regEscape(s) { return s.replace(/[-\\^$*+?.()|[\]{}]/g, "\\$&"); }
		
		function init() {
			$$("input.awesomplete").forEach(function (input) {
				new Awesomplete(input);
			});
		}
		
		// DOM already loaded?
		if (document.readyState !== "loading") {
			init();
		} else {
			// Wait for it
			document.addEventListener("DOMContentLoaded", init);
		}
		
		_.$ = $;
		_.$$ = $$;
		
		})();
</script>

<div id="ynresume_cover_wrapper">	
	
	<script type="text/javascript">	 
		   
	     //check open popup
	    function checkOpenPopup(url) {
	        if(window.innerWidth <= 480) {
	            Smoothbox.open(url, {autoResize : true, width: 300});
	        }
	        else {
	            Smoothbox.open(url);
	        }
	    }
		
		function getPhoto()
		{
			 new Request.HTML({
				'format' : 'json',
				'url' : '<?php echo $this->url(array('action'=>'get-photo'), 'ynresume_general') ?>',
				'data' : {
					id : '<?php echo $this -> resume -> getIdentity() ?>',
					'format' : 'html',
				},
				
				'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
					$('cover_photo').innerHTML = responseHTML;
				}
			}).send();
		}		
	</script>
	<div id="cover_photo" class="ynresume-cover-photo">
		<!-- photo resume -->
		<?php echo Engine_Api::_()->ynresume()->getPhotoSpan($resume, 'thumb.main'); ?>
		
		<?php if($resume -> search) :?>
			<span class="ynresume-edit-cover-photo">
				<i class="fa fa-search"></i> <?php echo $this -> translate('This resume is searchable');?>
			</span>
		<?php endif;?>
		
	</div>
<?php if($this -> isEdit) :?>
<div id="cover_edit" class="ynresume-cover-content">
	<div class="ynresume-form-edit-cover">
		<div class="form-wrapper">
			<div class="form-label"><label><?php echo $this -> translate('*Full Name');?></label></div>
			<div class="form-element"><input id="cover_name" type="text" name="cover_name" value="<?php if(!empty($resume)) echo $resume -> name;?>"></div>
		</div>
	
		<div class="form-wrapper">
			<div class="form-label"><label><?php echo $this -> translate('*Your professional headline');?></label></div>
			<div class="form-element form-element-2item">
				<div><input id="cover_title" type="text" name="cover_title" value="<?php if(!empty($resume)) echo $resume -> title;?>"></div>
				<?php echo $this -> translate('at');?>
				<!-- add autosuggest if has ynbusinesspages -->
				<div>
				<input autocomplete="off" id="cover_company" type="text" name="cover_company" value="<?php if(!empty($resume)) echo $resume -> company;?>">
				<script type="text/javascript">
					var data_list = [];
					<?php foreach($this -> businesses  as $business) :?>
						 <?php $business_title = $business->getTitle(); ?>
        				data_list.push('<?php echo htmlspecialchars("$business_title", ENT_QUOTES);?>');
					<?php endforeach;?>
					window.addEvent('domready', function() {
						var input = document.getElementById("cover_company");
						if(input)
						{
							new Awesomplete(input, {
								list: data_list
							});
						}
					});
				</script>
				</div>
			</div>
		</div>
		
		<div class="form-wrapper">
			<div class="form-label"><label><?php echo $this -> translate('Location');?></label></div>
			<div class="form-element">
				<input type="text" name="cover_location" id="cover_location" value="<?php if(!empty($resume)) echo $resume -> location;?>">
				<a class='ynresume_location_icon' href="javascript:void(0)" id='cover-get-current-location'>
					<img src="<?php echo $this -> baseUrl();?>/application/modules/Ynresume/externals/images/icon-search-advform.png">
				</a>	
			</div>
		</div>

		<?php 
			// Populate industry list.
			$industries = Engine_Api::_() -> getItemTable('ynresume_industry') -> getIndustries();
			unset($industries[0]);
		?>	
		<div class="form-wrapper">
			<div class="form-label"><label><?php echo $this -> translate('Industry');?></label></div>
			<div class="form-element">
				<select id="cover-industry_id" name="cover-industry_id" >
					<?php foreach ($industries as $item) :?>
						<option <?php if(!empty($resume)) if($resume -> industry_id == $item['industry_id']) echo "selected" ;?> value="<?php echo $item['industry_id'];?>"><?php echo str_repeat("-- ", $item['level'] - 1) . $this -> translate($item['title']);?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		
		<div class="form-wrapper">
			<div class="form-element">
				<input type="checkbox" name="cover-search" id="cover-search" <?php if($resume -> search) echo "checked"; ?> >
				<label for="search"><?php echo $this -> translate('This resume is searchable');?></label>
			</div>
		</div>
		
		<div class="form-wrapper">
			<div class="form-element">
				<input type="hidden" name="cover_lat"  id="cover_lat" value="<?php if(!empty($resume)) echo $resume -> latitude; else echo '0';?>">
				<input type="hidden" name="cover_long"  id="cover_long" value="<?php if(!empty($resume)) echo $resume -> longitude; else echo '0';?>">
				<input type="hidden" name="cover_location_address" id="cover_location_address" value="<?php if(!empty($resume)) echo $resume -> location; else echo '0';?>">
				<button class="button" type="button" id='cover_save_btn'><?php echo $this -> translate('Save');?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
 
  window.addEvent('domready', function() {
  		
  		function reloadOptionsWidgetAdd() {
			    var options = $('cover_edit');
			    if (options) {
			        var params = {};
			        params['format'] = 'html';
			        params['isEdit'] =  '0';
			        var request = new Request.HTML({
			            url : en4.core.baseUrl + 'widget/index/name/ynresume.my-resume-cover',
			            data : params,
			            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			                var parent = options.getParent('#ynresume_cover_wrapper').getParent();
			                Elements.from(responseHTML).replaces(parent);
			                eval(responseJavaScript);
			                
			                if($('ynresume-feature-btn')){
				                $('ynresume-feature-btn').addEvent('click', function (){
						  			var url = this.get('data-url')
						  			 if(window.innerWidth <= 480) {
							            Smoothbox.open(url, {autoResize : true, width: 300});
							        }
							        else {
							            Smoothbox.open(url);
							        }
						  		});
					  		}
			  		
					  		if($('ynresume-select-theme-btn'))
					  		{
						  		$('ynresume-select-theme-btn').addEvent('click', function (){
						  			var url = this.get('data-url')
						  			 if(window.innerWidth <= 480) {
							            Smoothbox.open(url, {autoResize : true, width: 300});
							        }
							        else {
							            Smoothbox.open(url);
							        }
						  		});
					  		}
			            }
			        });
			        request.send();
			    }
		}
  		
  		if($('cover_save_btn'))
  		{
	  		$('cover_save_btn').addEvent('click', function(){
	  			var isReturn = false;
	  			if($$('.error_cover'))
	  			{
	  				$$('.error_cover').destroy();
	  			}
	  			//validate name
				var name = $('cover_name').value;
				if (name == '')
				{
					var html = '<p class="form-errors"><?php echo $this -> translate('Please enter your full name.')?></p>';
		  			var error = new Element('div', {html: html});
		  			error.addClass('error_cover');
					$('cover_name').grab(error,'after');
					isReturn = true;
					
				}
				//validate headline
				var cover_title = $('cover_title').value;
				var cover_company = $('cover_company').value;
				
				if (cover_title == '')
				{
					var html = '<p class="form-errors"><?php echo $this -> translate('Please enter your headline.')?></p>';
		  			var error = new Element('div', {html: html});
		  			error.addClass('error_cover');
					$('cover_title').grab(error,'after');
					isReturn = true;
				}
				
				if(isReturn)
				{
					return;
				}
				//get other values
				var lat = $('cover_lat').value;
				var long = $('cover_long').value;
				var location_address = $('cover_location_address').value;
				var industry_id = $('cover-industry_id').value;
				if($('cover_location').value == "")
				{
					lat = long = location_address = "";
				}
				if($('cover-search').checked)
					var search = 1;
				else
					var search = 0;
				
				var url = "<?php echo $this -> url(array('action' => 'edit-resume'), 'ynresume_general', true);?>";
				new Request.JSON({
			        url: url,
			        method: 'post',
			        data: {
			            'name': name,
			            'title': cover_title,
			            'company': cover_company,
			            'lat': lat,
			            'long': long,
			            'location_address': location_address,
			            'industry_id': industry_id,
			            'search': search,
			        },
			        'onSuccess' : function(responseJSON, responseText)
			        {
			        	if(responseJSON.error_code ==  0)
			        	{
			        		reloadOptionsWidgetAdd();
			        		active = 1;
						}
					}
				}).send();
			});	
		}
  
  function initialize() {
	 	var input = /** @type {HTMLInputElement} */(
			document.getElementById('cover_location'));
		
		if(input)
		{
	  		var autocomplete = new google.maps.places.Autocomplete(input);
	
		  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		    	var place = autocomplete.getPlace();
			    if (!place.geometry) {
			     	return;
			    }
				document.getElementById('cover_location_address').value = place.formatted_address;		
				document.getElementById('cover_lat').value = place.geometry.location.lat();		
				document.getElementById('cover_long').value = place.geometry.location.lng();
		    });
	   }
	}
  
  initialize();
  google.maps.event.addDomListener(window, 'load', initialize); 
   
  function getCurrentLocation ()
	{	
		if(navigator.geolocation) {
			
	    	navigator.geolocation.getCurrentPosition(function(position) {
	    			
	      	var pos = new google.maps.LatLng(position.coords.latitude,
	                                       position.coords.longitude);
	        
			if(pos)
			{
				
				current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynresume_general') ?>',
					'data' : {
						latitude : pos.lat(),
						longitude : pos.lng(),
					},
					'onSuccess' : function(json, text) {
						
						if(json.status == 'OK')
						{
							document.getElementById('cover_location').value = json.results[0].formatted_address;
							document.getElementById('cover_location_address').value = json.results[0].formatted_address;
							document.getElementById('cover_lat').value = json.results[0].geometry.location.lat;		
							document.getElementById('cover_long').value = json.results[0].geometry.location.lng; 		
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
	if($('cover-get-current-location'))
	{
		$('cover-get-current-location').addEvent('click', function(){
			getCurrentLocation();	
		});
	}
	function handleNoGeolocation(errorFlag) {
  		if (errorFlag) {
    		document.getElementById('cover_location').value = 'Error: The Geolocation service failed.';
  		} 
  		else {
   			document.getElementById('cover_location').value = 'Error: Your browser doesn\'t support geolocation.';
   		}
 	}
}); 	
</script>

<?php else:?>
	<div class="ynresume-cover-content">	
		<div class="ynresume-cover-description">
			<?php if(!empty($resume)):?>  
				<div class="ynresume-cover-description-title">
					<?php echo strip_tags($resume -> name);?>
				</div>

				<div class="ynresume-cover-description-position">
					<?php echo strip_tags($resume -> headline);?>
				</div>

				<div class="ynresume-cover-description-subline">
					<?php if($resume -> location) :?>
						<span><i class="fa fa-map-marker"></i> <?php echo $resume -> location;?></span>
					<?php endif;?>
					
					<?php $industry = $resume -> getIndustry();?>			
					<?php if(!empty($industry)):?>				
						<span >
							<i class="fa fa-folder-open"></i>
							<?php echo $this -> htmlLink($resume -> getIndustry() -> getHref(), $resume -> getIndustry() -> getTitle());?>
						</span>
					<?php endif;?>	
				</div>

				<div class="ynresume-cover-description-subline">					
					<!-- experience -->
					<?php
						$tableExperiences = Engine_Api::_() -> getDbTable('experiences', 'ynresume');
						$currentExperiences = $tableExperiences -> getExperiencesByResumeId($resume -> getIdentity(), true, 3);
						if(count($currentExperiences) > 0)
						{
							$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
							$experiences = array();
							foreach ($currentExperiences as $experience){
								$business = null; 
			                    if ($experience->business_id) {
			                        $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
			                    }
								if ($business && !$business->deleted) {
									$experiences[] = $business;
								}else{
									$experiences[] = $experience -> company;
								}
							}
							echo '<div class="ynresume-cover-description-info"><label>'.$this -> translate('Current').'</label><span>'.implode(", ", $experiences).'</span></div>';
						}
					?>

					<?php
						$previousExperiences = $tableExperiences -> getExperiencesByResumeId($resume -> getIdentity(), false, 3);
						if(count($previousExperiences) > 0)
						{
							$business_enable = Engine_Api::_()->hasModuleBootstrap('ynbusinesspages');
							$experiences_arr = array();
							foreach ($previousExperiences as $experience){
								$business = null; 
			                    if ($experience->business_id) {
			                        $business = ($business_enable) ? Engine_Api::_()->getItem('ynbusinesspages_business', $experience->business_id) : null;
			                    }
								if ($business && !$business->deleted) {
									$experiences_arr[] = $business;
								}else{
									$experiences_arr[] = $experience -> company;
								}
							}
							echo '<div class="ynresume-cover-description-info"><label>'.$this->translate('Previous').'</label><span>'.implode(", ", $experiences_arr).'</span></div>';
						}
					?>	

					<!-- education -->		
					<?php
						$tableEducations = Engine_Api::_() -> getDbTable('educations', 'ynresume');
						$educations = $tableEducations -> getEducationsByResumeId($resume -> getIdentity(), 3);
						if(count($educations) > 0)
						{
							$educations_arr = array();
							foreach ($educations as $education){
								$educations_arr[] = $education -> title;
							}
							echo '<div class="ynresume-cover-description-info"><label>'.$this -> translate('Education').'</label><span>'.implode(", ", $educations_arr).'</span></div>';
						}
					?>		
				</div>				
				
				<!-- button -->
				<?php if ($resume->featured && !$resume->feature_expiration_date) : ?>
				<?php else: ?>
				<a data-url="<?php echo $this -> url(array('action' => 'feature', 'resume_id' => $resume -> getIdentity()), 'ynresume_specific', true);?>" id="ynresume-feature-btn" href="javascript:void(0)" class="button bold" ><?php echo $this -> translate('Feature');?></a>
				<?php endif; ?>
				<a href="<?php echo $resume -> getHref(); ?>" class="button bold"><?php echo $this -> translate('View Resume');?></a>
				<?php echo $this->htmlLink(array('route'=>'ynresume_specific','action'=>'edit-privacy','resume_id'=>$resume->getIdentity()), $this->translate('Edit Privacy'), array('class' => 'button bold'))?>
				<a data-url="<?php echo $this -> url(array('action' => 'select-theme', 'resume_id' => $resume -> getIdentity()), 'ynresume_specific', true);?>" id="ynresume-select-theme-btn" href="javascript:void(0)" class="button bold" ><?php echo $this -> translate('Select Theme');?></a>
				<?php echo $this->htmlLink(array('route'=>'ynresume_recommend','action'=>'ask'), $this->translate('Ask for Recommendation'), array('class' => 'button bold'))?>
			<?php endif;?>
			
			<div class="ynresume-cover-edit-button" id="cover_edit_icon" title="<?php echo $this -> translate('Edit');?>">
				<i class="fa fa-pencil"></i>
			</div>
		</div>

		<script type="text/javascript">
		
		  en4.core.runonce.add(function(){
	  		if($('ynresume-feature-btn'))
	  		{
		  		$('ynresume-feature-btn').addEvent('click', function (){
		  			var url = this.get('data-url')
		  			 if(window.innerWidth <= 480) {
			            Smoothbox.open(url, {autoResize : true, width: 300});
			        }
			        else {
			            Smoothbox.open(url);
			        }
		  		});
	  		}
	  		
	  		if($('ynresume-select-theme-btn'))
	  		{
		  		$('ynresume-select-theme-btn').addEvent('click', function (){
		  			var url = this.get('data-url');
		  			 if(window.innerWidth <= 480) {
			            Smoothbox.open(url, {autoResize : true, width: 300});
			        }
			        else {
			            Smoothbox.open(url);
			        }
		  		});
	  		}
  		 });
		  
		  window.addEvent('domready', function() {
		  		
		  		function reloadOptionsWidget() {
				    var options = $('cover_edit_icon');
				    if (options) {
				        var params = {};
				        params['format'] = 'html';
				        params['isEdit'] =  '1';
				        var request = new Request.HTML({
				            url : en4.core.baseUrl + 'widget/index/name/ynresume.my-resume-cover',
				            data : params,
				            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				                var parent = options.getParent('#ynresume_cover_wrapper').getParent();
				                Elements.from(responseHTML).replaces(parent);
				                eval(responseJavaScript);
				                if($('ynresume-feature-btn'))
					  			{
							  		$('ynresume-feature-btn').addEvent('click', function (){
							  			var url = this.get('data-url')
							  			 if(window.innerWidth <= 480) {
								            Smoothbox.open(url, {autoResize : true, width: 300});
								        }
								        else {
								            Smoothbox.open(url);
								        }
							  		});
						  		}
						  		
						  		if($('ynresume-select-theme-btn'))
						  		{
							  		$('ynresume-select-theme-btn').addEvent('click', function (){
							  			var url = this.get('data-url')
							  			 if(window.innerWidth <= 480) {
								            Smoothbox.open(url, {autoResize : true, width: 300});
								        }
								        else {
								            Smoothbox.open(url);
								        }
							  		});
						  		}
				            }
				        });
				        request.send();
			  		}
				}
		  		
		  		if($('cover_edit_icon'))
		  		{
			  		$('cover_edit_icon').addEvent('click', function(){
						reloadOptionsWidget();
					});	
				}
				
			}); 	
		</script>
	</div>
<?php endif;?>
</div>

