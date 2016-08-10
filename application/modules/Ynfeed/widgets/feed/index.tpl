<?php if(empty($this->getUpdate) && empty($this->checkUpdate)): ?>
<script type="text/javascript">
	var addMoreOptions = function() 
	{
		var ynfeed_pulldown = this.getParent('.ynfeed_pulldown_btn_wrapper');
	    if (!ynfeed_pulldown.hasClass('ynfeed_pulldown_open') ) 
	    {
	      $$('.ynfeed_pulldown_open').each(function(item, index)
	      {
	        item.removeClass('ynfeed_pulldown_open');
	      }); 
	      ynfeed_pulldown.addClass('ynfeed_pulldown_open');
	    } 
	    else {      
	      ynfeed_pulldown.removeClass('ynfeed_pulldown_open');
		}
	}
	
	var openCommentOptions = function() 
    {
        var yncomment_pulldown = this.getParent('.yncomment_replies_pulldown');
        if (!yncomment_pulldown.hasClass('yncomment_replies_pulldown_open') ) 
        {
          $$('.yncomment_replies_pulldown_open').each(function(item, index)
          {
            item.removeClass('yncomment_replies_pulldown_open');
          }); 
          yncomment_pulldown.addClass('yncomment_replies_pulldown_open');
        } 
        else {      
          yncomment_pulldown.removeClass('yncomment_replies_pulldown_open');
        }
    }
	/**
	 * extends Mootools component
	 */
	Element.implement({
		addLiveEvent : function(event, selector, fn) {
			this.addEvent(event, function(e) {
				var t = $(e.target);
				if(!t.match(selector))
					return ;
				if(typeof fn.apply !== "undefined")
				    fn.apply(t, [e]);
			}.bindWithEvent(this, selector, fn));
		}
	});
  // create some phrases language
  var langs = {
              "with" : '<?php echo $this->string()->escapeJavascript($this->translate("with")) ?>',
              "at" : '<?php echo $this->string()->escapeJavascript($this->translate("at")) ?>',
              "others" : '<?php echo $this->string()->escapeJavascript($this->translate("others")) ?>',
              "and" : '<?php echo $this->string()->escapeJavascript($this->translate("and")) ?>',
              "Undo" : '<?php echo $this->string()->escapeJavascript($this->translate("Undo")) ?>',
              "hide_all_by" : '<?php echo $this->string()->escapeJavascript($this->translate("Hide all by")) ?>',
              "message_hide_from_user" : '<?php echo $this->string()->escapeJavascript($this->translate("Feeds from %s are hidden now and will not appear in your Activity Feed anymore.")) ?>',
              'message_hide_feed': '<?php echo $this->string()->escapeJavascript($this->translate("This feed  is now hidden from your Activity Feed")) ?>',
              'message_report': '<?php echo $this -> string() -> escapeJavascript($this -> translate("To mark it offensive, please %s"))?>',
              'file a report': '<?php echo $this -> string() -> escapeJavascript($this -> translate("file a report"))?>',
              'Write a reply...': '<?php echo $this -> string() -> escapeJavascript($this -> translate("Write a reply..."))?>'
           };
    window.addEvent('domready', function()
    {
        if (!$('ynfeed-compose-submit')) 
        	return;
        $('ynfeed-compose-submit').addEvent('click', function(e)
        {
            e.stop();
            composeInstance.saveContent();
            if (checkStatusBody('ynfeed_activity_body', composeInstance.pluginReady))
            {
                var last_id = 0;
                $('ynfeed-activity-form').set('send', 
                {
                    async: false,
                    onSuccess: function(responseJson)
                    {
                    	var responseData = JSON.parse(responseJson);
                    	var url = responseData['url'];
                        last_id = parseInt(responseData['action_id']);
                        if (typeof activityUpdateHandler == 'object')
                        {
                            activityUpdateHandler.options.last_id = last_id;
                        }
						if($('ynfeed_activity_body'))
						{
	                        $('ynfeed_activity_body').value =  $('ynfeed_activity_body').getAttribute('placeholder');
	                        $('ynfeed_activity_body').addClass('input_placeholder');
	                        $('ynfeed_activity_body').setStyle('height','22px');
	                    }
                        
                        if($('ynfeed_composer_tab')) 
                        {
							$('ynfeed_composer_tab').style.display = 'none';
						}
                        if( $('ynfeed_activity_body_body_html'))
                        	$('ynfeed_activity_body_body_html').value = '';
                        if($('ynfeed_activity_body_tagged_users'))
                        	$('ynfeed_activity_body_tagged_users').value = '';
                        if($('ynfeed_activity_body_tagged_groups'))
                        	$('ynfeed_activity_body_tagged_groups').value = '';
                        if($('ynfeed_activity_body_hightlighter'))
                        {
                        	$('ynfeed_activity_body_hightlighter').innerHTML = '';
                        }
                        
                        if($('ynfeed_withfriends'))
                        {
	                        $('ynfeed_friendValues_element').innerHTML = '';
	                        $('ynfeed_friendValues').value = '';
	                        $('ynfeed_withfriends').style.display = 'none';
	                        $('add-friend-button').removeClass('addfriend_active');
	                    }
	                    
	                    if($('ynfeed_atbusiness'))
                        {
	                        $('ynfeed_businessValues_element').innerHTML = '';
	                        $('ynfeed_businessValues').value = '';
	                        $('ynfeed_atbusiness').style.display = 'none';
	                        $('business-button').removeClass('business_active');
	                    }
	                    
	                    if($('ynfeed_add_privacies'))
                        {
	                        $('ynfeed_privacyValues_element').innerHTML = '';
	                        $('ynfeed_GEValues').value = '';
	                        $('ynfeed_FLValues').value = '';
	                        $('ynfeed_NEValues').value = '';
	                        $('ynfeed_GRValues').value = '';
	                        $('ynfeed_FRValues').value = '';
	                    }
                        if($('ynfeed_mdash'))
                        	$('ynfeed_mdash').innerHTML = '';
                        if($('ynfeed_withfriends_content'))
                       		$('ynfeed_withfriends_content').innerHTML = '';
                       	if($('ynfeed_checkin_display'))
                        	$('ynfeed_checkin_display').innerHTML = '';
                        if($('ynfeed_dot'))
                        	$('ynfeed_dot').innerHTML = '';
                        
                        if($('checkin_lat'))
                        	$('checkin_lat').value = '';
                        if($('checkin_long'))
                        	$('checkin_long').value = '';
                        if($('ynfeed_checkinValue'))
                        {
                        	$('ynfeed_checkinValue').value = '';
                        	$('ynfeed_checkinValue').removeClass('checkin_selected');
                        }
                        if( $('ynfeed_removeCheckin'))
                        	$('ynfeed_removeCheckin').style.display = 'none';
                        
                        if($('checkin-button'))
                        {
                        	$('checkin-button').removeClass('checkin_active');
                        	$('checkin-button').style.display = '';
                        }
                        if($('business-button'))
                        {
                            $('business-button').removeClass('business_active');
                            $('business-button').style.display = '';
                        }
                        if($('ynfeed_businesses'))
                        {
                            $('ynfeed_businesses').style.display = '';
                        }
                        	
                        if($('ynfeed_checkin'))
                        	$('ynfeed_checkin').removeClass('checkin_selected');
                        
                        if($('ynfeed-activity-form'))
						{
							$('ynfeed-activity-form').addClass('ynfeed_form_border');
						}
						if($('ynfeed_fhighlighter'))
						{
							$('ynfeed_fhighlighter').removeClass('fhighlighter_click');
						}

                        composeInstance.setContent('');
                        if (composeInstance.plugins.video != undefined)
                        {
                          composeInstance.plugins.video.params.video_id = 0;
                        }

                        if (composeInstance.plugins.music != undefined)
                        {
                          composeInstance.plugins.music.params.song_id = 0;
                        }

                        if (composeInstance.plugins.ynultimatevideo != undefined)
                        {
                          composeInstance.plugins.ynultimatevideo.params.video_id = 0;
                        }

                        composeInstance.deactivate();

                        _ynfeedsubmiting = false;
                        if ($('nothing-tip') != undefined)
                        {
                          $('nothing-tip').style.display = 'none';
                        }
                        if ($('nothing-tip2') != undefined)
                        {
                          $('nothing-tip2').style.display = 'none';
                        }
                        
                        if(url)
                        {
                        	Smoothbox.open(url);
                        }
                    }
                });
				
				if($('feed_loading2')) 
	            {
	            	$('feed_loading2').style.display = '';
	            }
				
                $('ynfeed-activity-form').send();
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/name/ynfeed.feed',
                    data : {
                        'format' : 'html',
                        'minid' : last_id,
                        'feedOnly' : true,
                        'nolayout' : true,
                        'subject' : '<?php echo $this->subjectGuid ?>',
                        'countAutoload':<?php echo $this->countAutoload?>,
                        'filter_type':'default',
                        'filter_id':null,
                        'getUpdate':true
                    },
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
                    {
                    	if($('feed_loading2')) 
                    	{
                        	$('feed_loading2').style.display = 'none';
                    	}
                    	if(!$('activity-feed'))
                    	{
                    		location.reload();
                    		return false;
                    	}
                    	Elements.from(responseHTML).reverse().inject($('activity-feed'), 'top');
			            Smoothbox.bind($('activity-feed'));
			            en4.core.runonce.trigger();
                    }
                }));
            }
            else
            {
            	if($('feed_loading2')) 
            	{
                	$('feed_loading2').style.display = 'none';
            	}
            }
        });
		$(document.body).addLiveEvent('click', 'span.ynfeed_pulldown_btn', addMoreOptions);
        $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox i', openCommentOptions);
    });
	var countAutoload = <?php echo $this->countAutoload?>;
	var end = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;
	var loadMore = true;
	var noFeed = <?php echo ( $this->noFeed ? 'true' : 'false' ) ?>;
	
	<?php if($this -> autoLoadMore):?>
		var spyContainer = window;
		var min = spyContainer.getScrollSize().y - spyContainer.getSize().y -50; /*- 150 tolerance */
		new ScrollSpy({
		    container: spyContainer,
		    min: min,
		    onEnter: function() {
		            if ($('nothing-tip2') && $('nothing-tip2').getStyle('display') == 'block') return;
		            if (!end && countAutoload <= <?php echo $this->max_times ?> && loadMore)
		            {
		                $('feed_viewmore_link').fireEvent('click');
		            } 
		            else 
		            {}
		        }
		});
	<?php endif;?>
//End
</script>
<?php endif;?>
<?php if( (!empty($this->feedOnly) || !$this->endOfFeed ) &&
    (empty($this->getUpdate) && empty($this->checkUpdate)) ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;
      var activityViewMore = window.activityViewMore = function(next_id, subject_guid) {
        if( en4.core.request.isRequestActive() ) return;
        
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';         
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = '';
        
          var request = new Request.HTML({
          url : url,
          data : {
            format : 'html',
            'maxid' : next_id,
            'feedOnly' : true,
            'nolayout' : true,
            'subject' : subject_guid,
            'countAutoload':<?php echo $this->countAutoload?>,
            'actionFilter': '<?php echo $this->actionFilter;?>',
            'filterValue': '<?php echo $this->filterValue;?>'
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            Elements.from(responseHTML).inject($('activity-feed'));
            en4.core.runonce.trigger();
            Smoothbox.bind($('activity-feed'));
            $(document.body).addLiveEvent('click', 'span.ynfeed_pulldown_btn', addMoreOptions);
            $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox i', openCommentOptions);
          }
        });
       request.send();
      }
      
      if( next_id > 0 && !endOfFeed ) 
      {
        $('feed_viewmore').style.display = '';
        $('feed_loading').style.display = 'none';
        $('feed_viewmore_link').removeEvents('click').addEvent('click', function(event)
        {
          if (event != undefined)
          {
	         event.stop();
	      }
          activityViewMore(next_id, subject_guid);
        });
      } 
      else 
      {
      	loadMore = false;
      	$('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = 'none';
      }
      
    });
  </script>
<?php endif; ?>

<?php if( !empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
  echo $this->ynfeedLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'feedOnly' =>$this->feedOnly,
    'getUpdate' => $this->getUpdate,
    'openHide' => $this->openHide,
  ));
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->checkUpdate) ): // if this is for the live update
  if ($this->activityCount)
  echo "<script type='text/javascript'>
          document.title = '($this->activityCount) ' + activityUpdateHandler.title;
          activityUpdateHandler.options.next_id = ".$this->firstid.";
        </script>

        <div class='ynfeed-tip'>
          <span>
            <a href='javascript:void(0);' onclick='javascript:activityUpdateHandler.getFeedUpdate(".$this->firstid.");$(\"feed-update\").empty();'>
              {$this->translate(array(
                  '%d new update is available - click this to show it.',
                  '%d new updates are available - click this to show them.',
                  $this->activityCount),
                $this->activityCount)}
            </a>
          </span>
        </div>";
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->getUpdate) ): // if this is for the get live update ?>
   <script type="text/javascript">
     activityUpdateHandler.options.last_id = <?php echo sprintf('%d', $this->firstid) ?>;
   </script>
<?php endif; ?>

<?php if( $this->enableComposer )
  echo $this->partial('_ynfeedcomposer.tpl', 'ynfeed', array(
        'enableComposer' => $this->enableComposer,
        'hasPrivacy' => $this->hasPrivacy,
        'hasTag' => $this->hasTag,
        'composePartials' => $this->composePartials,
        'formToken' => $this->formToken,
        'friendUsers' => $this->friendUsers,
        'showDefault' =>  true 
    ));
?>
<?php if( $this->viewer()->getIdentity() ): ?>
   <script type="text/javascript">
          en4.user.viewer.iconUrl = '<?php echo $this->viewer()->getPhotoUrl('thumb.icon'); ?>';
          en4.user.viewer.title = '<?php echo $this->string()->escapeJavascript($this->viewer()->getTitle()); ?>';
          en4.user.viewer.href='<?php echo $this->string()->escapeJavascript($this->viewer()->getHref()); ?>';

          if(!en4.user.viewer.iconUrl){
            en4.user.viewer.iconUrl=en4.core.staticBaseUrl + 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
          }
          en4.ynfeed.fewSecHTML='<?php echo str_replace('timestamp-update','timestamp-fixed',$this->timestamp(time()-2)); ?>';
    </script>
<?php endif; ?>

<?php if ($this->updateSettings && !$this->action_id && $this -> autoUpdate): // wrap this code around a php if statement to check if there is live feed update turned on ?>
  <script type="text/javascript">
    var activityUpdateHandler;
    en4.core.runonce.add(function() {
      try {
          activityUpdateHandler = new ActivityUpdateHandler({
            'baseUrl' : en4.core.baseUrl,
            'basePath' : en4.core.basePath,
            'identity' : 4,
            'actionFilter': '<?php echo $this->actionFilter;?>',
            'filterValue': '<?php echo $this->filterValue;?>',
            'delay' : <?php echo $this->updateSettings;?>,
            'last_id': <?php echo sprintf('%d', $this->firstid) ?>,
            'subject_guid' : '<?php echo $this->subjectGuid ?>'
          });
          setTimeout("activityUpdateHandler.start()",1250);
          window._activityUpdateHandler = activityUpdateHandler;
      } catch( e ) {
      }
    });
  </script>
<?php endif;?>
<?php if( $this->activity || $this->actionFilter != 'all'): ?>
   <?php if($this->action_id == 'false'): 
   		$this->action_id = 0;
   	endif; ?>
  <?php if(!empty ($this->subjectGuid) && !$this->action_id):
	  echo $this->partial('widgets-feed/profile-content-tabs.tpl', 'ynfeed', array());	 
	 elseif(empty ($this->subjectGuid) && !$this->action_id && ($this->enableContentTabs)):	
	  echo $this->partial('widgets-feed/content-tabs.tpl', 'ynfeed', array(
	                'filterTabs' => $this->filterTabs,
	                'actionFilter' => $this->actionFilter,
	                'contentTabMax' => $this->contentTabMax,
	                'canCreateCustomList' => $this -> canCreateCustomList
	              ));
		 endif; ?>
  <?php endif; ?> 
<?php if( $this->post_failed == 1 ): ?>
  <div class="ynfeed-tip">
    <span>
      <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
      <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="'.$url.'">', '</a>') ?>
    </span>
  </div>
<?php endif; ?>

<div id="feed-update"></div>
<div class="ynfeed-tip" id="nothing-tip2" style="display:none">
  <span>
    <?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
  </span>
</div>
<div id="feed_loading2" class="ynfeed_loading" style="display: none;">
  <img src='application/modules/Ynfeed/externals/images/loading.gif' style='margin-right: 5px;' />
</div>
<?php // If requesting a single action and it doesn't exist, show error ?>
<?php if( !$this->activity  && $this->actionFilter =='all'): ?>
  <?php if( $this->action_id ): ?>
    <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
    <p>
      <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
    </p>
  <?php return; else: ?>
    <div class="ynfeed-tip">
      <span>
        <?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
      </span>
    </div>
  <?php return; endif; ?>
 <?php elseif(!$this->activity): ?>
    <ul class='feed' id="activity-feed"></ul>
<?php endif; ?>

<?php echo $this->ynfeedLoop($this->activity, array(
  'action_id' => $this->action_id,
  'viewAllComments' => $this->viewAllComments,
  'viewAllLikes' => $this->viewAllLikes,
  'getUpdate' => $this->getUpdate,
  'openHide' => $this->openHide,
)) ?>

<div class="feed_viewmore" id="feed_viewmore" style="display: none;">
  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
    'id' => 'feed_viewmore_link',
    'class' => 'buttonlink icon_viewmore'
  )) ?>
</div>

<div class="ynfeed_loading" id="feed_loading" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Ynfeed/externals/images/loading.gif' />
</div>
<div class="ynfeed_feed_tip" id="feed_no_more" style="display: <?php echo (!$this->endOfFeed || !empty($this->action_id)) ? 'none':'block'?>;"> 
  <?php echo $this->translate("There are no more posts to show.") ?>
</div>
<script type="text/javascript">
function ynfeedFilter(filter_type, filter_id)
{
	if(filter_type == 'hashtag')
	{
		if($('ynfeed_tabs_feed'))
		{
			var myContainer = $('ynfeed_tabs_feed');
   			myContainer.getElements('ul > li').removeClass('ynfeed_tab_active');  
   		}
	}
    if ($('nothing-tip'))
    {
        $('nothing-tip').style.display = 'none';
    }

    //Update content
    if (typeof activityUpdateHandler == 'object')
    {

        activityUpdateHandler.options.filter_type = filter_type;
        activityUpdateHandler.options.filter_id = filter_id;
    }
    //Send request and update content
    if (typeof activityUpdateHandler == 'Object')
    {
        last_id = activityUpdateHandler.options.last_id;
    } else 
    {
        last_id = 0;
    }
    var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
    var request = new Request.HTML({
        url : url,
        data : {
            'format' : 'html',
            'last_id' : 0,
            'feedOnly' : true,
            'nolayout' : true,
            'subject' : '<?php echo $this->subjectGuid ?>',
            'actionFilter': filter_type,
            'filterValue': filter_id,
            'isFromTab':true,
        },
        onRequest: function(){
            if($('feed_loading2')) {
            	$('feed_loading2').style.display = '';
            }
            if ($('feed_viewmore')){
                $('feed_viewmore').style.display = 'none';
            }
            if ($('nothing-tip2')){
                $('nothing-tip2').style.display = 'none';
            }
        },
        evalScripts : true,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
        {
        	if($('feed_loading2')) {
                $('feed_loading2').style.display = 'none';
            }
            var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;
            var next_id = <?php echo sprintf('%d', $this->nextid) ?>;

            if( next_id > 0 && !endOfFeed ) {
                $('feed_viewmore').style.display = '';
            }
            if(noFeed)
            {
                if ($('nothing-tip2'))
                {
                    $('nothing-tip2').style.display = '';
                    end = true;
                }
            }
            if($("ynfeed_tabs_feed_tab_more"))
           		$("ynfeed_tabs_feed_tab_more").removeClass('ynfeed_tabs_feed_tab_open').addClass('ynfeed_tabs_feed_tab_closed');
           	if(filter_type == 'hashtag')
           		window.scrollTo(0,0);
			$('activity-feed').innerHTML="";
            Elements.from(responseHTML).inject($('activity-feed'));
            en4.core.runonce.trigger();
            Smoothbox.bind($('activity-feed'));
            $(document.body).addLiveEvent('click', 'span.ynfeed_pulldown_btn', addMoreOptions);
            $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox i', openCommentOptions);
        }
       
    });
    en4.core.request.send(request);
}
</script>