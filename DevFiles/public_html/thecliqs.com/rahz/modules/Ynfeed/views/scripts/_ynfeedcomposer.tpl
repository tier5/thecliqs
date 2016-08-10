<div class="ynfeed-post-container ynfeed-clearfix">
	<form method="post" class="ynfeed_form_border" action="<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>" class="activity" enctype="application/x-www-form-urlencoded" id="ynfeed-activity-form">
		<!-- Composer -->
		<div id="ynfeed_compose_body">
			<textarea id="ynfeed_activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate("What's on your mind?")) ?>"></textarea>
		</div>
		<!-- Show preview add with friends and checkin -->
		<div class="ynfeed_addwithfriends" id="ynfeed_addwithfriends">
			<span class="ynfeed_mdash" id="ynfeed_mdash"></span>
			<span class="ynfeed_withfriends_content" id="ynfeed_withfriends_content"></span>
			<span id="ynfeed_checkin_display"></span><span class="ynfeed_dot" id="ynfeed_dot"></span>
		</div>
		
		<!-- Show compose tray when render-->
		
		<!-- Some input hidden --> 
		<input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
		<?php $subjectType = "user";
		if($this->viewer() && $this->subject())
		{
			$subjectType = $this->subject() ->getType();
		}
		if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())):?>
		<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
	<?php endif; ?>
	<?php if( $this->formToken ): ?>
		<input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
	<?php endif ?>
	
	<!-- Add with friends -->
	<?php if( $this -> hasTag): ?>
		<div id="ynfeed_withfriends" onclick="$('ynfeed_friends').focus()">
			<div id="ynfeed_friendValues_element" class="ynfeed_friendToken"></div>
			<div class="ynfeed_addFriend" id="ynfeed_addFriend">
				<input type="hidden" name="friendValues" value="" id="ynfeed_friendValues">
				<input type="text" name="friends" id="ynfeed_friends" value="" autocomplete="off" placeholder="<?php echo $this -> translate("+ Who are you with?")?>">
			</div>
		</div>
	<?php endif;?>
	
	<!-- Add locaion or checkin -->
	<div class="ynfeed_checkin" id="ynfeed_checkin" onclick="$('ynfeed_checkinValue').focus()">
		<input type="hidden" name="checkin_lat" value="" id="checkin_lat">
		<input type="hidden" name="checkin_long" value="" id="checkin_long">
		<input type="text" onkeyup = "changeCheckin()" name="checkinValue" autocomplete="off" id="ynfeed_checkinValue" placeholder="<?php echo $this -> translate("> Where are you?")?>">
		<span id="ynfeed_removeCheckin" class="ynfeed_removeCheckin" onclick="removeCheckin()" title="<?php echo $this -> translate('Remove')?>">x</span>
	</div>
	
	 <?php if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages')):?>
		<!-- Add business -->
		<div id="ynfeed_atbusiness" class="ynfeed_atbusiness" onclick="$('ynfeed_businesses').focus()">
			<div id="ynfeed_businessValues_element" class="ynfeed_businessToken"></div>
			<div class="ynfeed_Business" id="ynfeed_Business">
				<input type="hidden" name="businessValues" value="" id="ynfeed_businessValues">
				<input type="text" name="businesses" id="ynfeed_businesses" value="" autocomplete="off" placeholder="<?php echo $this -> translate("> Which is business?")?>">
			</div>
		</div>
	<?php endif;?>
	
	<!-- Composer menus -->
	<div id="fly_ynfeed_composer">
		<div id="ynfeed_composer_tab" style="display: none">
			<!-- Add privacy -->
			<?php
			if($this -> hasPrivacy):?>
			<div class="ynfeed_add_privacies" id ="ynfeed_add_privacies">
				<span class="ynfeed_privacy_label"><?php echo $this -> translate("<i class='fa fa-lock'></i>");?></span>
				<div class="ynfeed_privacy_border">
					<div id="ynfeed_privacyValues_element" class="ynfeed_privacyToken"></div>
					<div class="ynfeed_Privacy" id="ynfeed_Privacy" onclick="$('ynfeed_privacies').focus()">
						<input type="hidden" name="SPRI_GE" value="" id="ynfeed_GEValues">
						<input type="hidden" name="SPRI_FL" value="" id="ynfeed_FLValues">
						<input type="hidden" name="SPRI_NE" value="" id="ynfeed_NEValues">
						<input type="hidden" name="SPRI_GR" value="" id="ynfeed_GRValues">
						<input type="hidden" name="SPRI_FR" value="" id="ynfeed_FRValues">
						<input type="text" name="str_privacy" id="ynfeed_privacies" value="" autocomplete="off" placeholder="<?php echo $this -> translate("+ Who can view this feed?")?>">
					</div>
					<ul class="ynfeed_privacies_tag-autosuggest" id="ynfeed_privacies_custom_choices"></ul>
				</div>
			</div>
		<?php endif;?>
		<div class="ynfeed_compose_footer ynfeed-clearfix">
			<div class="ynfeed_composer_submit">
				<button id="ynfeed-compose-submit" type="submit"><?php echo $this->translate("Share") ?></button>	
			</div>
			
			<div id="compose-menu" class="compose-menu ynfeed-clearfix">
				<?php if($this -> hasTag): ?>
					<span id="add-friend-button"  class="ynfeed_post_add_friend" title="<?php echo $this -> translate("Tag people in your post")?>"  onclick="toogleTagWith()"></span>
				<?php endif;?>
				<span id="checkin-button"  class="ynfeed_post_checkin" title="<?php echo $this -> translate("Add a location to post")?>"  onclick="toogleCheckin()"></span>
				<?php if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages')):?>
					<span id="business-button"  class="ynfeed_post_business" title="<?php echo $this -> translate("Add a business to post")?>"  onclick="toogleBusiness()"></span>
				<?php endif;?>
				<?php 
				$YN_EMOTIONS_TAG = Engine_Api::_() -> ynfeed() -> getEmoticons();
				?>
				<span id="emoticons-button"  class="ynfeed_post_smile" title="<?php echo $this -> translate("Insert Emoticons")?>"  onclick="setEmoticonsBoard()">
					<span id="emoticons-board"  class="ynfeed_embox ynfeed_embox_closed" >
						<span class="ynfeed_embox_title">
							<span class="ynfeed_fleft" id="emotion_label"></span>
							<span class="ynfeed_fright"id="emotion_symbol" ></span>
						</span>
						<?php foreach ($YN_EMOTIONS_TAG as $emoticon):?>         
							<span class="ynfeed_embox_icon" onmouseover='setEmotionLabelPlate("<?php echo $this->translate(ucwords($emoticon -> title))?>","<?php echo $emoticon -> text?>")' onclick='addEmotionIcon("<?php echo $emoticon -> text?>")'  title="<?php echo $this->translate(ucwords($emoticon -> title))."&nbsp;".$emoticon -> text; ?>">
								<?php echo "<img src=\"".$this->layout()->staticBaseUrl."application/modules/Ynfeed/externals/images/emoticons/{$emoticon->image}\" border=\"0\" alt=\"{$emoticon->image}\" />";              
								?></span>
							<?php endforeach;?>
						</span>					
					</span>	
				</div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	var composeInstance;
	en4.core.runonce.add(function() {
		// @todo integrate this into the composer
		if( true ) {
			composeInstance = new Composer('ynfeed_activity_body', {
				menuElement : 'compose-menu',
				trayElement: 'compose-tray',
				submitElement: 'ynfeed-compose-submit',
				baseHref : '<?php echo $this->baseUrl() ?>',
				lang : {
					"What's on your mind?" : '<?php echo $this->string()->escapeJavascript($this->translate("What\'s on your mind?")) ?>',
					"Get Feeds" : '<?php echo $this->string()->escapeJavascript($this->translate("Get Feeds")) ?>'
				}
			});
		}
	});
	
	  // Add tag friends and groups
	  var active_tags = false;
	  var friend_tagged = [];
	  <?php if($this -> hasTag): ?>
	  active_tags = true;
	  new YnTagsAutocompleter.RequestTag.JSON('ynfeed_activity_body', '<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'index', 'action' => 'get-users-groups'), 'default', true) ?>', {
		'minLength': 1,
		'delay' : 250,
		'selectMode': 'pick',
		'autocompleteType': 'message',
		'multiple': true,
		'className': 'tag-autosuggest',
		'filterSubset' : true,
		'tokenFormat' : 'object',
		'tokenValueKey' : 'label',
		'injectChoice': function(token)
		{
			if(token.type == 'user' || token.type == 'group')
			{
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
		}
	  });
	  <?php if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages')):?>
	  function removeBusinessValue()
	  {
	    document.getElementById('ynfeed_businessValues').value = '';
	  }
	  function removeBusinessFromToValue(id) 
	  {
		    removeBusinessValue();
		    // hide the wrapper for usernames if it is empty
		    if (document.getElementById('ynfeed_businessValues').value == "")
		    {
		    	if($('ynfeed_checkin_display'))	
					$('ynfeed_checkin_display').innerHTML = "";
				if($('ynfeed_withfriends_content').innerHTML == "")
				{
					$('ynfeed_mdash').innerHTML = '';
					$('ynfeed_dot').innerHTML = '';
				}
				$('business-button').removeClass('business_active');
				if($('checkin-button'))
				{
					$('checkin-button').style.display = 'block';
				}
		    }
		    document.getElementById('ynfeed_businesses').style.display = 'block';
	  }
	  new YnBusinessAutocompleter.Request.JSON('ynfeed_businesses', '<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'index', 'action' => 'get-businesses'), 'default', true) ?>', {
		'minLength': 1,
		'delay' : 250,
		'selectMode': 'pick',
		'autocompleteType': 'message',
		'multiple': false,
		'className': 'tag-autosuggest',
		'filterSubset' : true,
		'tokenFormat' : 'object',
		'tokenValueKey' : 'label',
		'injectChoice': function(token)
		{
			if(token.type == 'ynbusinesspages_business')
			{
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
		},
		onPush : function(){
          if( $('ynfeed_businessValues').value.split(',').length >= 1 )
          {
           	document.getElementById('ynfeed_businesses').style.display = 'none';
          }
        }
	  });
	  <?php endif;?>
	  function removeFromToValue(id, name) 
	  {
			// code to change the values in the hidden field to have updated values
			// when recipients are removed.
			var toValues = $('ynfeed_friendValues').value;
			var toValueArray = toValues.split(",");
			var toValueIndex = "";
			var checkMulti = id.search(/,/);
			// check if we are removing multiple recipients
			if (checkMulti!=-1)
			{
				var recipientsArray = id.split(",");
				for (var i = 0; i < recipientsArray.length; i++){
					removeToValue(recipientsArray[i], toValueArray, name);
				}
			}
			else
			{
				removeToValue(id, toValueArray, name);
			}
			
			$('ynfeed_friends').disabled = false;
		}
		
		function removeToValue(id, toValueArray, name)
		{
			for (var i = 0; i < toValueArray.length; i++)
			{
				if (toValueArray[i]==id) toValueIndex =i;
			}
			var nameIndex = "";
			for (var i = 0; i < friend_tagged.length; i++)
			{
				if (friend_tagged[i] == name) nameIndex =i;
			}
			
			friend_tagged.splice(nameIndex, 1);
			toValueArray.splice(toValueIndex, 1);
			$('ynfeed_friendValues').value = toValueArray.join();
			pushWithFriend();
			if(toValueArray.length == 0)
			{
				if($('ynfeed_checkin_display'))
				{
					if($('ynfeed_checkin_display').innerHTML == '')
					{
						if($('ynfeed_dot'))
						{
							$('ynfeed_dot').innerHTML = '';
						}
						if($('ynfeed_mdash'))
						{
							$('ynfeed_mdash').innerHTML = '';
						}
					}
				}
				if($('add-friend-button'))
					$('add-friend-button').removeClass('addfriend_active');
			}
		}
		function pushWithFriend()
		{
			var withFriend = $('ynfeed_withfriends_content');
			var len = friend_tagged.length;
			withFriend.innerHTML = "";
			if(len == 1)
			{
				var myElement = new Element("span");
				myElement.innerHTML = "<a href='javascript:void(0);' onclick='openTagWith()'>" + friend_tagged[0] + "</a>";
				myElement.addClass("ynfeed_withToken");
				withFriend.appendChild(myElement);
			}
			else if(len == 2)
			{
				var myElement1 = new Element("span");
				myElement1.innerHTML = "<a href='javascript:void(0);' onclick='openTagWith()'>" + friend_tagged[0] + "</a> " + langs['and'];
				myElement1.addClass("ynfeed_withToken");
				withFriend.appendChild(myElement1);
				
				var myElement2 = new Element("span");
				myElement2.innerHTML = " <a href='javascript:void(0);' onclick='openTagWith()'>" + friend_tagged[1] + "</a> ";
				myElement2.addClass("ynfeed_withToken");
				withFriend.appendChild(myElement2);
			}
			else if(len > 2)
			{
				var myElement1 = new Element("span");
				myElement1.innerHTML = "<a href='javascript:void(0);' onclick='openTagWith()'>" + friend_tagged[0] + "</a> " + langs['and'];
				myElement1.addClass("ynfeed_withToken");
				withFriend.appendChild(myElement1);
				
				var total_others = len - 1;
				var myElement2 = new Element("span");
				myElement2.innerHTML = " <a href='javascript:void(0);' onclick='openTagWith()'>" + total_others + ' ' + langs['others'] + "</a> ";
				myElement2.addClass("ynfeed_withToken");
				withFriend.appendChild(myElement2);
			}
		}
		
	  // Add with friends
	  new YnAddFriendAutocompleter.Request.JSON('ynfeed_friends', '<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'index', 'action' => 'get-friends'), 'default', true) ?>', {
		'minLength': 1,
		'delay' : 250,
		'selectMode': 'pick',
		'autocompleteType': 'message',
		'multiple': true,
		'className': 'tag-autosuggest',
		'filterSubset' : true,
		'tokenFormat' : 'object',
		'tokenValueKey' : 'label',
		'injectChoice': function(token)
		{
			if(token.type == 'user')
			{
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
		}
	  });
	<?php endif;?>
	
	// Add privacy
	<?php if($this -> hasPrivacy):?>
	var privacyList;
	new YnPrivacyAutocompleter.Request.JSON('ynfeed_privacies', '<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'index', 'action' => 'get-group-privacies', 'subjectType' => $subjectType), 'default', true) ?>', {
		'minLength': 1,
		'delay' : 250,
		'selectMode': 'pick',
		'autocompleteType': 'message',
		'multiple': true,
		'className': 'ynfeed_privacies_tag-autosuggest',
		'customChoices': 'ynfeed_privacies_custom_choices',
		'filterSubset' : true,
		'tokenFormat' : 'object',
		'tokenValueKey' : 'label',
		'injectChoice': function(token)
		{
			if($$('.ynfeed_privacies_close_button').length == 0)
			{
				var closeButton = new Element('span', {
					'class': 'ynfeed_privacies_close_button',
					'id': 'ynfeed_privacies_close_button',
					'html' : '<i class="fa fa-times"></i>'
				});
				closeButton.inject(this.choices);
				closeButton.addEvent('click', this.onClickClose.create({bind : this,
					arguments : false,
					delay : 100}));
			}
			if($$('.ynfeed_privacies_list').length == 0)
			{
				privacyList = new Element('div', {
					'class': 'ynfeed_privacies_list',
					'id': 'ynfeed_privacies_list',
					'html' : ''
				});
				privacyList.inject(this.choices);
			}
			if($$('.ynfeed_privacies_'+ token.type +'_label').length == 0)
			{
				var name = '';
				switch(token.type)
				{
					case 'general':
					name = '<?php echo $this -> translate("General")?>';
					break;
					case 'network':
					name = '<?php echo $this -> translate("Networks")?>';
					break;
					case 'friendlist':
					name = '<?php echo $this -> translate("Friend List")?>';
					break;
					case 'user':
					name = '<?php echo $this -> translate("Friends")?>';
					break;
					case 'group':
					name = '<?php echo $this -> translate("Groups")?>';
					break;
				}
				name = name + ":";
				var gen_label = new Element('div', {
					'class': 'ynfeed_privacies_' + token.type + '_label ynfeed_privacies_name',
					'id': name,
					'html' : name
				});
				gen_label.inject(privacyList);
			}
			var choice = new Element('li', {
				'class': 'autocompleter-choices ynfeed_privacies_' + token.type,
				'html': token.photo,
				'id':token.label
			});
			new Element('div', {
				'html': this.markQueryValue(token.label),
				'class': 'autocompleter-choice'
			}).inject(choice);
			this.addChoiceEvents(choice).inject(privacyList);
			choice.store('autocompleteChoice', token);
		}
	});
	function removePrivacyFromToValue(id, name, type) 
	{
			// code to change the values in the hidden field to have updated values
			// when recipients are removed.
			var toValues = $(type).value;
			var toValueArray = toValues.split(",");
			var toValueIndex = "";
			var checkMulti = id.search(/,/);
			// check if we are removing multiple recipients
			if (checkMulti!=-1)
			{
				var recipientsArray = id.split(",");
				for (var i = 0; i < recipientsArray.length; i++){
					removePrivacyToValue(recipientsArray[i], toValueArray, name);
				}
			}
			else
			{
				removePrivacyToValue(id, toValueArray, name, type);
			}
		}
		
		function removePrivacyToValue(id, toValueArray, name, type)
		{
			for (var i = 0; i < toValueArray.length; i++)
			{
				if (toValueArray[i]==id) toValueIndex =i;
			}
			var nameIndex = "";
			for (var i = 0; i < friend_tagged.length; i++)
			{
				if (friend_tagged[i] == name) nameIndex =i;
			}

			friend_tagged.splice(nameIndex, 1);
			toValueArray.splice(toValueIndex, 1);
			$(type).value = toValueArray.join();
		}
	<?php endif;?>
	
	// register event whenever loaded data.
	var ynfriends = { rows: <?php echo Zend_Json::encode($this->friendUsers); ?> };
	$(window).addEvent('domready', function()
	{
		if($('ynfeed_activity_body'))
		{
			ynfeed('ynfeed_activity_body', active_tags, 0, '');
		}
	});
</script>

<?php foreach( $this->composePartials as $partial ): ?>
	<?php echo $this->partial($partial[0], $partial[1]) ?>
<?php endforeach; ?>
</div>
