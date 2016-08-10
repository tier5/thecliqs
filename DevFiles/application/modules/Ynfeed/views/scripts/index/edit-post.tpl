<?php 
$action = $this -> action;
$ynfeedApi = Engine_Api::_() -> ynfeed();
$hasBusiness = Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages');
$business_id = $this -> business_id;
if($hasBusiness)
    $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
$isBusiness = ($hasBusiness && $business_id && $business);
?>
<script type="text/javascript">
  // create some phrases language
  var langs = {
              "with" : '<?php echo $this->string()->escapeJavascript($this->translate("with")) ?>',
              "at" : '<?php echo $this->string()->escapeJavascript($this->translate("at")) ?>',
              "others" : '<?php echo $this->string()->escapeJavascript($this->translate("others")) ?>',
              "and" : '<?php echo $this->string()->escapeJavascript($this->translate("and")) ?>',
           };
    window.addEvent('domready', function()
    {
        if (!$('ynfeed-compose-submit')) 
        	return;
        $('ynfeed-compose-submit').addEvent('click', function(e)
        {
            e.stop();
            composeInstance.saveContent();
            <?php if($action->getTypeInfo()->attachable && $action->attachment_count > 0 ):?>      
     			composeInstance.pluginReady = true;
   			 <?php endif;?>
            if (checkStatusBody('ynfeed_activity_body', composeInstance.pluginReady))
            {
                $('ynfeed-activity-form').set('send', 
                {
                    async: false,
                    onSuccess: function(responseText)
                    {
                    	history.go(-1)
                    }
                });
				
                $('ynfeed-activity-form').send();
            }
         });
    });
//End
</script>
<h2><?php echo $this -> translate("Edit Feed")?></h2>
<div class="ynfeed-post-container ynfeed-clearfix">
	<form method="post" class="ynfeed_form_border" action="<?php echo $this->url(array('action_id' => $action -> getIdentity()), 'ynfeed_edit_post', true) ?>" class="activity" enctype="application/x-www-form-urlencoded" id="ynfeed-activity-form">
	  <!-- Composer -->
	  <div id="ynfeed_compose_body">
	  	<textarea id="ynfeed_activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate("What's on your mind?")) ?>"><?php echo $this -> body ?></textarea>
	  </div>
	  <!-- Show preview add with friends and checkin -->
	  <div class="ynfeed_addwithfriends" id="ynfeed_addwithfriends">
	  	<span class="ynfeed_mdash" id="ynfeed_mdash"><?php if(count($this -> aWithFriend) || $this -> map) echo " — "; if(count($this -> aWithFriend)) echo $this -> translate('with');?></span>
	  	<span class="ynfeed_withfriends_content" id="ynfeed_withfriends_content"></span>
	  	<span id="ynfeed_checkin_display"></span><span class="ynfeed_dot" id="ynfeed_dot"></span>
	  </div>
	  
	  <!-- Show attachment-->
	  <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='feed_item_attachments' style="padding-top: 10px; overflow:hidden">
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
            <?php if( count($action->getAttachments()) == 1 &&
                    null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>                    
              <?php echo $richContent; ?>
            <?php else: ?>
              <?php foreach( $action->getAttachments() as $attachment ): ?>
              	<span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
	                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
	                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
	                  <div>
	                    <?php 
	                      $attribs = Array('target'=>'_blank');
	                    ?>
	                    <?php if( $attachment->item->getPhotoUrl() ): ?>
	                      <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
	                    <?php endif; ?>
	                    <div>
	                      <div class='feed_item_link_title'>
	                        <?php
	                          echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
	                        ?>
	                      </div>
	                      <div class='feed_item_link_desc'>
	                        <?php
	                        if($attachment->item -> getType() == 'activity_action')
							{
								echo $this->viewMore($attachment->item->getDescription(), null, null, null, false);
							}
							else 
							{
								echo $this->viewMore($attachment->item->getDescription());
							}
	                         ?>
	                      </div>
	                    </div>
	                  </div>
	                <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
	                  <div class="feed_attachment_photo">
	                    <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb')) ?>
	                  </div>
	                <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
	                  <?php echo $this->viewMore($attachment->item->getDescription()); ?>
	                <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
	                <?php endif; ?>
                </span>
              <?php endforeach; ?>
              <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
	  
	  <!-- Some input hidden --> 
	  <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
	  <?php $subjectType = $this -> subjectType;
	  if( $this->viewer() && $this->subject && !$this->viewer()->isSelf($this->subject)):?>
	    <input type="hidden" name="subject" value="<?php echo $this->subject->getGuid() ?>" />
	  <?php endif; ?>
	  <?php if( $this->formToken ): ?>
	    <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
	  <?php endif ?>
	  
	  <!-- Add with friends -->
	   <?php if( $this -> hasTag): ?>
		  <div id="ynfeed_withfriends" onclick="$('ynfeed_friends').focus()">
			  <div id="ynfeed_friendValues_element" class="ynfeed_friendToken">
			  	<?php foreach($this -> aWithFriend as $key): $title = $ynfeedApi -> getObjectTitle('user', $key);?>
			  		<span id="tospan_<?php echo $title?>_<?php echo $key?>" class="ynfeed_tag"><?php echo $title?> <a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue('<?php echo $key?>', '<?php echo $title?>', 'ynfeed_friendValues');">x</a></span>
			  	<?php endforeach;?>
			  </div>
			  <div class="ynfeed_addFriend" id="ynfeed_addFriend">
			  	<input type="hidden" name="friendValues" value="<?php echo $this -> sWithFriend?>" id="ynfeed_friendValues"/>
			  	<input type="text" name="friends" id="ynfeed_friends" value="" autocomplete="off" placeholder="<?php echo $this -> translate("+ Who are you with?")?>"/>
			  </div>
		  </div>
	  <?php endif;?>
	  
	  <!-- Add locaion or checkin -->
	  <div style="<?php if($isBusiness) echo "display: none";?>" class="ynfeed_checkin <?php if($this -> map && !$isBusiness) echo "checkin_selected"?>" id="ynfeed_checkin" onclick="$('ynfeed_checkinValue').focus()">
		  	<input type="hidden" name="checkin_lat" value="<?php if(!$isBusiness) echo $this -> sLat?>" id="checkin_lat"/>
		  	<input type="hidden" name="checkin_long" value="<?php if(!$isBusiness) echo $this -> sLong?>" id="checkin_long"/>
		  	<input type="text" <?php if($this -> map) echo 'class="checkin_selected"'?> value="<?php if(!$isBusiness) echo $this->string()->escapeJavascript($this -> map)?>" onkeyup = "changeCheckin()" name="checkinValue" id="ynfeed_checkinValue" placeholder="<?php echo $this -> translate("> Where are you?")?>">
		  	<span id="ynfeed_removeCheckin" <?php if($this -> map) echo 'style="display: inline-block;"'?> class="ynfeed_removeCheckin" onclick="removeCheckin()" title="<?php echo $this -> translate('Remove')?>">x</span>
	  </div>
	  
	  <?php if($hasBusiness):?>
		<!-- Add business -->
		<div id="ynfeed_atbusiness" style="<?php if($this -> map && !$isBusiness) echo "display:none"?>" class="ynfeed_atbusiness" onclick="$('ynfeed_businesses').focus()">
			<div id="ynfeed_businessValues_element" class="ynfeed_businessToken"></div>
			<div class="ynfeed_Business" id="ynfeed_Business">
				<input type="hidden" name="businessValues" value="<?php if($business_id) echo $business_id?>" id="ynfeed_businessValues">
				<input type="text" name="businesses" id="ynfeed_businesses" value="" autocomplete="off" placeholder="<?php echo $this -> translate("> Which is business?")?>">
			</div>
		</div>
	  <?php endif;?>
	  <!-- Composer menus -->
	  <div id="fly_ynfeed_composer">
	  	 <div id="ynfeed_composer_tab">
	  	 	<!-- Add privacy -->
			  <?php
			  if($this -> hasPrivacy):?>
			  <div class="ynfeed_add_privacies" id ="ynfeed_add_privacies">
				<span class="ynfeed_privacy_label"><?php echo $this -> translate("<i class='fa fa-lock'></i>");?></span>
			  	<div class="ynfeed_privacy_border">
				  	<div id="ynfeed_privacyValues_element" class="ynfeed_privacyToken">
				  		<?php foreach($this -> aGeneral as $key): $title = $ynfeedApi -> getGeneralPrivacyName($this -> subjectType, $key);?>
				  			<span id="tospan_privacy_<?php echo $title?>_<?php echo $key?>" class="ynfeed_tag yfprivacy_tag_general"><?php echo $title?> <a href="javascript:void(0);" onclick="this.parentNode.destroy();removePrivacyFromToValue('<?php echo $key?>', '<?php echo $title?>', 'ynfeed_GEValues');">x</a></span>
				  		<?php endforeach;?>
				  		
				  		<?php foreach($this -> aNetwork as $key): $title = $ynfeedApi -> getObjectTitle('network', $key);?>
				  			<span id="tospan_privacy_<?php echo $title?>_<?php echo $key?>" class="ynfeed_tag yfprivacy_tag_network"><?php echo $title?> <a href="javascript:void(0);" onclick="this.parentNode.destroy();removePrivacyFromToValue('<?php echo $key?>', '<?php echo $title?>', 'ynfeed_NEValues');">x</a></span>
				  		<?php endforeach;?>
				  		
				  		<?php foreach($this -> aFriendlist as $key): $title = $ynfeedApi -> getObjectTitle('user_list', $key);?>
				  			<span id="tospan_privacy_<?php echo $title?>_<?php echo $key?>" class="ynfeed_tag yfprivacy_tag_friendlist"><?php echo $title?> <a href="javascript:void(0);" onclick="this.parentNode.destroy();removePrivacyFromToValue('<?php echo $key?>', '<?php echo $title?>', 'ynfeed_FLValues');">x</a></span>
				  		<?php endforeach;?>
				  		
				  		<?php foreach($this -> aFriend as $key): $title = $ynfeedApi -> getObjectTitle('user', $key);?>
				  			<span id="tospan_privacy_<?php echo $title?>_<?php echo $key?>" class="ynfeed_tag yfprivacy_tag_user"><?php echo $title?> <a href="javascript:void(0);" onclick="this.parentNode.destroy();removePrivacyFromToValue('<?php echo $key?>', '<?php echo $title?>', 'ynfeed_FRValues');">x</a></span>
				  		<?php endforeach;?>
				  		
				  		<?php foreach($this -> aGroup as $key): $title = $ynfeedApi -> getObjectTitle('group', $key);?>
				  			<span id="tospan_privacy_<?php echo $title?>_<?php echo $key?>" class="ynfeed_tag yfprivacy_tag_group"><?php echo $title?> <a href="javascript:void(0);" onclick="this.parentNode.destroy();removePrivacyFromToValue('<?php echo $key?>', '<?php echo $title?>', 'ynfeed_GRValues');">x</a></span>
				  		<?php endforeach;?>
				  	</div>
					<div class="ynfeed_Privacy" id="ynfeed_Privacy" onclick="$('ynfeed_privacies').focus()">
					  	<input type="hidden" name="SPRI_GE" value="<?php echo $this -> sGeneral?>" id="ynfeed_GEValues">
					  	<input type="hidden" name="SPRI_FL" value="<?php echo $this -> sFriendlist?>" id="ynfeed_FLValues">
					  	<input type="hidden" name="SPRI_NE" value="<?php echo $this -> sNetwork?>" id="ynfeed_NEValues">
					  	<input type="hidden" name="SPRI_GR" value="<?php echo $this -> sGroup?>" id="ynfeed_GRValues">
					  	<input type="hidden" name="SPRI_FR" value="<?php echo $this -> sFriend?>" id="ynfeed_FRValues">
					  	<input type="text" name="str_privacy" id="ynfeed_privacies" value="" autocomplete="off" placeholder="<?php echo $this -> translate("+ Who can view this feed?")?>">
				  	</div>
					<ul class="ynfeed_privacies_tag-autosuggest" id="ynfeed_privacies_custom_choices"></ul>
				  </div>
			  </div>
			  <?php endif;?>
		<div class="ynfeed_compose_footer ynfeed-clearfix">
			<div class="ynfeed_composer_submit">
				<button id="ynfeed-compose-submit" type="submit"><?php echo $this->translate("Share") ?></button>
				<?php echo $this -> translate('or')?> <a href="javascript:;" onClick="history.go(-1)"><?php echo $this -> translate('cancel')?></a>		
			</div>
			
			<div id="compose-menu" class="compose-menu ynfeed-clearfix">
				  	 <?php if($this -> hasTag): ?>
				  		<span id="add-friend-button"  class="ynfeed_post_add_friend <?php if(count($this -> aWithFriend)) echo "addfriend_active"?>" title="<?php echo $this -> translate("Tag people in your post")?>"  onclick="toogleTagWith()"></span>
				  	 <?php endif;?>
				  	<span style="<?php if($isBusiness) echo "display: none";?>" id="checkin-button"  class="ynfeed_post_checkin <?php if($this -> map && !$isBusiness) echo "checkin_active"?>" title="<?php echo $this -> translate("Add a location to post")?>"  onclick="toogleCheckin()"></span>
				    <?php if($hasBusiness):?>
						<span style="<?php if($this -> map && !$isBusiness) echo "display:none"?>"  id="business-button"  class="ynfeed_post_business <?php if($isBusiness) echo "business_active"?>" title="<?php echo $this -> translate("Add a business to post")?>"  onclick="toogleBusiness()"></span>
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
	  <?php foreach($this -> aWithFriend as $key): $title = $ynfeedApi -> getObjectTitle('user', $key);?>
	  	friend_tagged.push('<?php echo $title?>');
	  <?php endforeach?>
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
	    <?php if($hasBusiness):?>
	    	  var isPopulated = false;
	    	  var business = {
				    id : false,
				    type : false,
				    guid : false,
				    title : false
				  };
			  <?php if(!empty($business)): ?>
			    isPopulated = true;
			    business = {
			      id : <?php echo sprintf("%d", $business->getIdentity()) ?>,
			      type : '<?php echo $business->getType() ?>',
			      guid : '<?php echo $business->getGuid() ?>',
			      title : '<?php echo $this->string()->escapeJavascript($business->getTitle()) ?>'
			    };
			  <?php endif; ?>
			  function removeBusinessValue(id)
			  {
			    document.getElementById('ynfeed_businessValues').value = '';
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
			  }
			  function removeBusinessFromToValue(id) 
			  {
				    removeBusinessValue(id);
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
			  if( !isPopulated ) 
			  { // NOT POPULATED
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
			} else { // POPULATED

		      var myElement = new Element("span", {
		        'id' : 'tospan_' + business.title + '_' + business.id,
		        'class' : 'tag tag_' + business.type,
		        'html' :  business.title  + ' <a href="javascript:void(0);" ' + 'onclick="this.parentNode.destroy();removeBusinessFromToValue(' + business.id + ');">x</a>' 
		      });
		      $('ynfeed_businessValues_element').appendChild(myElement);
		
		      // Hide to input?
		      $('ynfeed_businesses').setStyle('display', 'none');
		      $('ynfeed_businessValues').setStyle('display', 'none');
		    }
		<?php endif;?>
	  	function removeFromToValue(id, name) 
	  	{
		    // code to change the values in the hidden field to have updated values
		    // when recipients are removed.
		    var toValues = $('ynfeed_friendValues').value;
		    var toValueArray = [];
		    var tempToValueArray = toValues.split(",");
		    for (var i = 0; i < tempToValueArray.length; i++)
		    {
		    	if(tempToValueArray[i])
		    	{
		    		toValueArray[i] = tempToValueArray[i];
		    	}
		    }
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
          ynfeed('ynfeed_activity_body', active_tags, 1, '<?php echo $this->string()->escapeJavascript($this -> input_hidden)?>');
       }
       <?php if(count($this -> aWithFriend) && $this -> hasTag):?>
       		pushWithFriend();
       	<?php endif;?>
       	<?php if($this -> map):
			if($isBusiness)		
				$str_content = ' '.$this -> translate('at'). ' <a href = "javascript:void(0);" onclick = "openBusiness()" >'. $business -> getTitle() . '</a>';
			else
				$str_content = ' '.$this -> translate('at'). ' <a href = "javascript:void(0);" onclick = "openCheckin()" >'. $this -> map . '</a>';
       		?>
       		if($('ynfeed_checkin_display'))
			{
				checkin = $('ynfeed_checkin_display');
				checkin.innerHTML = "";
				var myElement = new Element("span");
				myElement.innerHTML = '<?php echo $str_content?>';
				myElement.addClass("ynfeed_checkinToken");
				checkin.appendChild(myElement);
			}
       	<?php endif;?>
     });
	</script>
</div>
