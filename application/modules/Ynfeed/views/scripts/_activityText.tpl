<?php if( empty($this->actions) ) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
   $actions = $this->actions;
} ?>

<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js') ?>

<script type="text/javascript">
  var CommentLikesTooltips;
  var unhideReqActive = false;
  en4.core.runonce.add(function()
  {

    // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo  $this->string()->escapeJavascript($this->translate('Loading...')) ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            //type : 'core_comment',
            action_id : el.getParent('li').getParent('li').getParent('li').get('id').match(/\d+/)[0],
            comment_id : id
          },
          onComplete : function(responseJSON) {
            el.store('tip:title', responseJSON.body);
            el.store('tip:text', '');
            CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
          }
        });
        req.send();
      }
    });
    // Add tooltips
    CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
      fixed : true,
      className : 'comments_comment_likes_tips',
      offset : {
        'x' : 48,
        'y' : 16
      }
    });
    // Enable links in comments
    $$('.comments_body').enableLinks();
  });
</script>

<?php if( !$this->getUpdate && !$this->feedOnly && !$this->onlyactivity): ?>
	<ul class='feed' id="activity-feed">
<?php endif ?>

<?php
  $numberOfPhotoAtts = 9;
  $saveFeedTable =  Engine_Api::_() -> getDbTable('saveFeeds', 'ynfeed');
  $optionFeedTable =  Engine_Api::_() -> getDbTable('optionFeeds', 'ynfeed');
  foreach( $actions as $action ): // (goes to the end of the file)
    try { // prevents a bad feed item from destroying the entire page
      // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
      if( !$action->getTypeInfo()->enabled ) continue;
      if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
      if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;

      ob_start();
    ?>
  <?php if( !$this->noList ): ?><li id="activity-item-<?php echo $action->action_id ?>" data-activity-feed-item="<?php echo $action->action_id ?>"><?php endif; ?>
    <?php $this->commentForm->setActionIdentity($action->action_id) ?>
    <script type="text/javascript">
      (function(){
        var action_id = '<?php echo $action->action_id ?>';
        en4.core.runonce.add(function(){
          $('activity-comment-body-' + action_id).autogrow();
          en4.activity.attachComment($('activity-comment-form-' + action_id));
        });
      })();
    </script>

    <?php // User's profile photo ?>
    <div class='feed_item_photo'><?php echo $this->htmlLink($action->getSubject()->getHref(),
      $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())
    ) ?></div>

	<?php
	// add some action on feed
	$item = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject();
	$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
	$business_id = $business_session -> businessId;
	?>
	<?php if($this->viewer()->getIdentity() && !$this -> notShowActions):?>
	  <span class="ynfeed_pulldown_btn_wrapper">
	    <div class="ynfeed_pulldown_contents_wrapper">
	      <div class="ynfeed_pulldown_contents">
	        <ul>
	        	<?php if( $this->viewer()->getIdentity() && (
	                $this->activity_moderate || (
	                ($this->viewer()->getIdentity() == $this->activity_group) || (
	                  $this->allow_delete && (
	                    ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
	                    ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
		                  )
		                )
		               )
		            ) ): ?>
		            <li class="feed_item_option_delete">
		              <?php echo $this->htmlLink(array(
		                'route' => 'default',
		                'module' => 'activity',
		                'controller' => 'index',
		                'action' => 'delete',
		                'action_id' => $action->action_id
		              ), $this->translate('Delete Feed'), array('class' => 'smoothbox')) ?>
		            </li>
	            <?php endif; ?>
	            <?php if(!$this->viewer()->isSelf($action->getSubject()) && ($action->getSubject() -> getIdentity() != $business_id)):?>
	                <li>
	                  	<a href="javascript:void(0);" onclick='hideItemFeeds("<?php echo $action->getType() ?>", "<?php echo $action->getIdentity()
	                  		?>", "<?php echo $item->getType() ?>", "<?php echo $item->getIdentity() ?>", "<?php echo $this->string()->escapeJavascript($item->getTitle()); ?>", "");' ><?php
	                       echo
	                       $this->translate("I don't want to see this");
	                       ?></a>
	                </li>
	                <li>
	                    <a onclick="hideOptions()" class="smoothbox" href="<?php echo $this->url(array('module' => 'ynfeed', 'controller' => 'index', 'action' => 'add-report', 'subject' =>
		                        $action->getGuid(), 'format' => 'smoothbox'), 'default', true)?>"><?php echo $this->translate('Report Feed'); ?></a>
                  	</li>
	                <li>
	                  	<a href="javascript:void(0);" onclick='hideItemFeeds("<?php echo $item->getType() ?>", "<?php echo $item->getIdentity() ?>", "", "<?php echo $action->getIdentity() ?>", "<?php echo $this->string()->escapeJavascript($item->getTitle()); ?>", "");' ><?php echo $this->translate('Hide all from %s', $item->getTitle()); ?></a>
	                </li>
	                <li class="sep"></li>
	                <li>
                	<a href="javascript:void(0);" title="" onclick="updateNotification('<?php echo $action->action_id ?>', 1)">
                 		 <?php echo $this->translate(($optionFeedTable -> getOptionFeed($this->viewer(), $action->action_id, 'notification')) ? 'Stop Notifications' : 'Get Notifications') ?></a>
             	 	</li>
             	 	<?php if ($this->allowSaveFeed && !$this -> subject()): ?>
		              <li>
		                	<a href="javascript:void(0);" title="" onclick="updateSaveFeed('<?php echo $action->action_id ?>')">
		                 		 <?php echo $this->translate(($saveFeedTable -> getSaveFeed($this->viewer(), $action->action_id)) ? 'Remove From Saved Feeds' : 'Save Feed') ?></a>
		              </li>
		            <?php endif; ?>
	            <?php else:?> <!-- owner of this feed-->
	            	<?php
	            	$enableComposer = false;
	            	if (!$this->subject() || ($this->subject() instanceof Core_Model_Item_Abstract && $this->subject() -> isSelf($this -> viewer()))) {
						if (Engine_Api::_() -> authorization() -> getPermission($this -> viewer() -> level_id, 'user', 'status')) {
							$enableComposer = true;
						}
					} else if ($this->subject()) {
						if (Engine_Api::_() -> authorization() -> isAllowed($this->subject(), $this -> viewer(), 'comment'))
						{
							$enableComposer = true;
						}
					}
	            	if($enableComposer && in_array($action -> type, array('status', 'post', 'post_self'))):?>
	               	<li>
		                  <?php echo $this->htmlLink(array(
			                'route' => 'ynfeed_edit_post',
			                'action_id' => $action->action_id
			              ), $this->translate('Edit Feed'), array('class' => '')) ?>
		               </li>
		             <?php endif;?>
            	   <?php if ($this->allowSaveFeed && !$this -> subject()): ?>
		              <li>
		                	<a href="javascript:void(0);" title="" onclick="updateSaveFeed('<?php echo $action->action_id ?>')">
		                 		 <?php echo $this->translate(($saveFeedTable -> getSaveFeed($this->viewer(), $action->action_id)) ? 'Remove From Saved Feeds' : 'Save Feed') ?></a>
		              </li>
		            <?php endif; ?>
		            <li class="sep"></li>
            	   <li>
                	<a href="javascript:void(0);" title="" onclick="updateNotification('<?php echo $action->action_id ?>', 0)">
                 		 <?php echo $this->translate(($optionFeedTable -> getOptionFeed($this->viewer(), $action->action_id, 'notification')) ? 'Get Notifications' : 'Stop Notifications') ?></a>
             	 	</li>
		            <li>
                		<a href="javascript:void(0);" title="" onclick="updateComment('<?php echo $action->action_id ?>')">
                 		 <?php echo $this->translate(($optionFeedTable -> getOptionFeed($this->viewer(), $action->action_id, 'comment')) ? 'Enable Comments' : 'Disable Comments') ?></a>
             	 	</li>
             	 	<li>
                		<a href="javascript:void(0);" title="" onclick="updateLock('<?php echo $action->action_id ?>')">
                 		 <?php echo $this->translate(($optionFeedTable -> getOptionFeed($this->viewer(), $action->action_id, 'lock')) ? 'Unlock Feed' : 'Lock Feed') ?></a>
             	 	</li>
	            <?php endif; ?>
	        </ul>
	      </div>
	    </div>
	    <span class="ynfeed_pulldown_btn fa fa-chevron-down"></span>
	     <!-- will be added background image when have design -->
	  </span>
	<?php endif;?>
    <!-- end add some actions -->
    <div class='feed_item_body'>
      <?php // Main Content ?>
      <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
        <?php
			echo @$action->getContent();
		$mdash = false;
		$str_content = '<span class = "ynfeed_with_checkin">';
		$feedApiCore = Engine_Api::_ ()->ynfeed ();
		list($friendIds, $friends) = $feedApiCore -> getWithFriends($action -> getIdentity());
		$checkin = $feedApiCore -> getCheckin($action -> getIdentity());

		$totalFriendTagged = count($friendIds);

		if($totalFriendTagged)
		{
			$mdash = true;
			$str_content .= ' — ';
			if($totalFriendTagged == 1)
			{
				$str_content .= $this -> translate('with'). ' '. $friends[0];
			}
			elseif($totalFriendTagged == 2)
			{
				$str_content .= $this -> translate('with'). ' '. $friends[0];
				$str_content .= ' '. $this -> translate('and'). ' '. $friends[1];
			}
			elseif($totalFriendTagged > 2)
			{
				$url = $this -> url(array('action_id' => $action -> getIdentity(), 'friend_id' => $friends[0] -> getIdentity()), 'ynfeed_more_tagfriends');
				$str_content .= $this -> translate('with'). ' '. $friends[0];
				$str_content .= ' '. $this -> translate('and').' <a href = "javascript:void(0);" onclick = "Smoothbox.open(\''.$url.'\')" >'. ($totalFriendTagged - 1). ' '. $this -> translate('others') . '</a>';
			}
			$str_content .= ' ';
		}
		if($checkin)
		{
			$url = $this -> url(array('map_id' => $checkin -> getIdentity()), 'ynfeed_map');

			if(!$mdash)
			{
				$str_content .= ' — ';
			}
			if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages') && $checkin -> business_id && $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $checkin -> business_id))
			{
				$str_content .= $this -> translate('at'). ' <a href = "'.$business -> getHref().'" title = "'.$business -> getTitle().'" >'. $business -> getTitle() . '</a>';
			}
			else
			{
				$str_content .= $this -> translate('at'). ' <a href = "javascript:void(0);" onclick = "Smoothbox.open(\''.$url.'\')" >'. $checkin -> title . '</a>';
			}
		}
		echo $str_content . '</span>';
        ?>
      </span>
      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='feed_item_attachments'>
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
            <?php if( count($action->getAttachments()) == 1 &&
                    null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>
              	<div class="feed_attachment_<?php echo current($action->getAttachments())->meta->type; ?>">
              	<?php echo $richContent; ?>
              	</div>
            <?php else: ?>
              <?php $count = 0;
              foreach( $action->getAttachments() as $attachment ): $count ++;?>
              	<span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
	                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
	                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
	                  <div>
	                    <?php
	                      if ($attachment->item->getType() == "core_link")
	                      {
	                        $attribs = Array('target'=>'_blank');
	                      }
	                      else
	                      {
	                        $attribs = Array();
	                      }
	                    ?>
	                    <?php if( $attachment->item->getPhotoUrl() ): ?>
	                      <?php
	                      $thumb_type = 'thumb.normal';
	                      if(in_array($attachment->item->getType(), array('event', 'album_photo', 'contest', 'ynfundraising_campaign', 'social_store', 'social_product', 'advalbum_photo')))
						  {
						  	 $thumb_type = 'thumb.main';
						  }
	                      echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, $thumb_type, $attachment->item->getTitle()), $attribs);
	                      ?>
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
	                <?php elseif( $attachment->meta->mode == 2 ): $thumb_type = 'thumb.main'; if($count > 1) $thumb_type = 'thumb.profile';// Thumb only type actions ?>
	                  <div class="feed_attachment_photo">
	                    <?php if($count == 1)
	                    {
	                    	echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb' .(($count == 1)?" ynfeed_first_photo":"")));
	                    }
			            else
			            {
							$photoUrl = $attachment->item -> getPhotoUrl($thumb_type);
                            if($attachment->meta->type == 'advalbum_photo' && $count >= $numberOfPhotoAtts && count($action->getAttachments()) > $numberOfPhotoAtts) {
                                $leftPhotos = count($action->getAttachments()) - $numberOfPhotoAtts + 1;
                                echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()) .'</span><span class="ynadvalbum_more_photos_parent"><span class="ynadvalbum_more_photos">+' . $leftPhotos . '</span></span>', array('class' => 'feed_item_thumb'));
                            } else {
                                echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb'));
                            }}
  	                    ?>
	                  </div>
	                <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
	                  <?php echo $this->viewMore($attachment->item->getDescription()); ?>
	                <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
	                <?php endif; ?>
                </span>
                  <?php
                  if($attachment->meta->type == 'advalbum_photo' && $count >= $numberOfPhotoAtts && count($action->getAttachments()) > $numberOfPhotoAtts) {
                      break;
                  }
                  ?>
              <?php endforeach; ?>
              <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div id='comment-likes-activity-item-<?php echo $action->action_id ?>'>

      <?php // Icon, time since, action links ?>
      <?php
        $icon_type = 'activity_icon_'.$action->type;
        list($attachment) = $action->getAttachments();
        if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
          $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
        endif;
        $canComment = ( $action->getTypeInfo()->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
            !empty($this->commentForm) &&
            (!$optionFeedTable -> getOptionFeed($action->getSubject(), $action->action_id, 'comment') || $this->viewer()->isSelf($action->getSubject())) &&
			(!$optionFeedTable -> getOptionFeed($action->getSubject(), $action->action_id, 'lock') || $this->viewer()->isSelf($action->getSubject())));
      ?>
      <div class='feed_item_date feed_item_icon <?php echo $icon_type ?>'>
        <ul>
          <li>
            <?php echo $this->timestamp($action->getTimeValue()) ?>
          </li>
          <?php if( $canComment ): ?>
            <?php if( $action->likes()->isLike($this->viewer()) ): ?>
              <li class="feed_item_option_unlike">
                <span>-</span>
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.activity.unlike('.$action->action_id.');')) ?>
              </li>
            <?php else: ?>
              <li class="feed_item_option_like">
                <span>-</span>
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick'=>'javascript:en4.activity.like('.$action->action_id.');')) ?>
              </li>
            <?php endif; ?>
            <?php if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
              <li class="feed_item_option_comment">
                <span>-</span>
                <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), $this->translate('Comment'), array(
                  'class'=>'smoothbox',
                )) ?>
              </li>
            <?php else: ?>
              <li class="feed_item_option_comment">
                <span>-</span>
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('onclick'=>'document.getElementById("'.$this->commentForm->getAttrib('id').'").style.display = ""; document.getElementById("'.$this->commentForm->submit->getAttrib('id').'").style.display = "block"; document.getElementById("'.$this->commentForm->body->getAttrib('id').'").focus();')) ?>
              </li>
            <?php endif; ?>

           <?php if( $this->viewAllComments ): ?>
              <script type="text/javascript">
                en4.core.runonce.add(function() {
                  document.getElementById('<?php echo $this->commentForm->getAttrib('id') ?>').style.display = "";
                  document.getElementById('<?php echo $this->commentForm->submit->getAttrib('id') ?>').style.display = "block";
                  document.getElementById('<?php echo $this->commentForm->body->getAttrib('id') ?>').focus();
                });
              </script>
            <?php endif ?>
          <?php endif; ?>

          <?php if($this->viewer()->getIdentity() && in_array($this->viewer()->getIdentity(), $friendIds)):?>
                <span>-</span>
                <li class="feed_item_option_remove_tag">
                    <a href="javascript:void(0);" title="" onclick="removeTag('<?php echo $action->action_id ?>')">
                       <?php echo $this->translate('Remove Tag') ?></a>
                  </li>
           <?php endif ?>

          <!-- Remove Link Preview-->
           <?php if($this->viewer()->isSelf($action->getSubject()) && is_object($attachment) && $action->attachment_count > 0 && $attachment->item && $attachment->item->getType() == 'core_link'):?>
                <span>-</span>
                <li class="feed_item_option_remove_preview">
                    <a href="javascript:void(0);" title="" onclick="removePreview('<?php echo $action->action_id ?>')">
                       <?php echo $this->translate('Remove Preview') ?></a>
                  </li>
           <?php endif ?>

          <?php // Share ?>
          <?php if( $action->getTypeInfo()->shareable && $this->viewer()->getIdentity() && (!$optionFeedTable -> getOptionFeed($action->getSubject(), $action->action_id, 'lock') || $this->viewer()->isSelf($action->getSubject()))): ?>
            <?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) ): ?>
              <li class="feed_item_option_share">
                <span>-</span>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 2 ): ?>
              <li class="feed_item_option_share">
                <span>-</span>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 3 ): ?>
              <li class="feed_item_option_share">
                <span>-</span>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 4 ): ?>
              <li class="feed_item_option_share">
                <span>-</span>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php endif; ?>
            <?php
             $type = "";
			 $id = 0;
			 if(count($action->getAttachments()) > 0)
			 {
			 	$atts = $action->getAttachments();
			 	$type = $atts[0] -> item -> getType();
				$id = $atts[0] -> item -> getIdentity();
			 }
         	 $shared = $feedApiCore -> getShareds($action -> getIdentity(), $type, $id);
			 if(count($shared)):?>
				 <li class="feed_item_shared">
				 	<a title="<?php echo $this -> translate("Total shared")?>" class="smoothbox" href="<?php echo $this->url(array('controller' => 'index', 'action' => 'view-shared', 'id' => $action->getIdentity()), 'ynfeed_extended', true);?>">
				 		<div class="yfshare_number feed_item_date"><?php echo count($shared)?></div>
				 		<div class="activity_icon_share"></div>
				 	</a>
	             </li>
	         <?php endif;?>
          <?php endif; ?>
        </ul>
      </div>
      <!--</div> End of Comment-Likes -->

      <?php if( $action->getTypeInfo()->commentable ): // Comments - likes ?>
        <div class='comments'>
          <ul>
            <?php if( $action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers())>0) ): ?>
              <li>
                <div></div>
                <div class="comments_likes">
                  <?php if( $action->likes()->getLikeCount() <= 3 || $this->viewAllLikes ): ?>
                    <?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->fluentList($action->likes()->getAllLikesUsers()) )?>

                  <?php else: ?>
                    <?php echo $this->htmlLink($action->getSubject()->getHref(array('action_id' => $action->action_id, 'show_likes' => true)),
                      $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount()) )
                    ) ?>
                  <?php endif; ?>
                </div>
              </li>
            <?php endif; ?>
            <?php if( $action->comments()->getCommentCount() > 0 ): ?>
              <?php if( $action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
                <li>
                  <div></div>
                  <div class="comments_viewall">
                    <?php if( $action->comments()->getCommentCount() > 2): ?>
                      <?php echo $this->htmlLink($action->getSubject()->getHref(array('action_id' => $action->action_id, 'show_comments' => true)),
                          $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
                          $this->locale()->toNumber($action->comments()->getCommentCount()))) ?>
                    <?php else: ?>
                      <?php echo $this->htmlLink('javascript:void(0);',
                          $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
                          $this->locale()->toNumber($action->comments()->getCommentCount())),
                          array('onclick'=>'en4.activity.viewComments('.$action->action_id.');')) ?>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endif; ?>

              <?php
                $comments = $action->getComments($this->viewAllComments);
                $commentLikes = $action->getCommentsLikes($comments, $this->viewer());
              ?>
              <?php foreach( $comments as $comment ): ?>
                <li id="comment-<?php echo $comment->comment_id ?>">
                  <div class="comments_author_photo">
                    <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(),
                      $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle())
                    ) ?>
                  </div>
                  <div class="comments_info">
                   <span class='comments_author'>
                     <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle()); ?>
                   </span>
                   <span class="comments_body">
                     <?php echo $this->viewMore($comment->body) ?>
                   </span>
                   <ul class="comments_date">
                     <li class="comments_timestamp">
                       <?php echo $this->timestamp($comment->creation_date); ?>
                     </li>
                     <?php if ( $this->viewer()->getIdentity() &&
                               (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                ($this->viewer()->getIdentity() == $comment->poster_id) ||
                                $this->activity_moderate ) ): ?>
                     <li class="sep">-</li>
                     <li class="comments_delete">
                       <?php echo $this->htmlLink(array(
                            'route'=>'default',
                            'module'    => 'activity',
                            'controller'=> 'index',
                            'action'    => 'delete',
                            'action_id' => $action->action_id,
                            'comment_id'=> $comment->comment_id,
                            ), $this->translate('delete'), array('class' => 'smoothbox')) ?>
                     </li>
                      <?php endif; ?>
                      <?php if( $canComment ):
                        $isLiked = !empty($commentLikes[$comment->comment_id]);
                      ?>
                        <li class="sep">-</li>
                        <li class="comments_like">
                          <?php if( !$isLiked ): ?>
                            <a href="javascript:void(0)" onclick="en4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)">
                              <?php echo $this->translate('like') ?>
                            </a>
                          <?php else: ?>
                            <a href="javascript:void(0)" onclick="en4.activity.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)">
                              <?php echo $this->translate('unlike') ?>
                            </a>
                          <?php endif ?>
                        </li>
                      <?php endif ?>
                      <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                        <li class="sep">-</li>
                        <li class="comments_likes_total">
                          <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
                            <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                          </a>
                        </li>
                      <?php endif ?>
                    </ul>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <?php if( $canComment ) echo $this->commentForm->render() ?>
        </div>
      <?php endif; ?>

      </div> <!-- End of Comment Likes -->


    </div>
  <?php if( !$this->noList ): ?></li><?php endif; ?>

<?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
    };
  endforeach;
?>

<?php if( !$this->getUpdate ): ?>
</ul>
<?php endif ?>