<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _checkinWall.tpl 2011-11-30 11:53 taalay $
 * @author     Taalay
 */
?>

<?php
if( empty($this->actions) ) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
  $actions = $this->actions;
  $checkins = $this->wallActivityCheckins($actions);
  $pageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
  $eventEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event');
}
?>
<?php $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');?>

<?php
foreach( $actions as $action ): // (goes to the end of the file)
  try { // prevents a bad feed item from destroying the entire page
    // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
    if( !$action->getTypeInfo()->enabled ) continue;
    if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
    if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;

    ob_start();
    ?>
  <?php if( !$this->noList ): ?><li rev="item-<?php echo $action->action_id ?>" class="wall-action-item"><?php endif; ?>
    <?php if (isset($action->grouped_subjects) && count($action->grouped_subjects) > 1):?>
    <div class='checkin_item_body'>
      <?php // Icon, time since, action links ?>
      <?php
      $icon_type = 'activity_icon_'.$action->type;
      list($attachment) = $action->getAttachments();
      if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
        $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
      endif;
      ?>
      <?php // Main Content ?>
      <span class="wall_grouped_feed_item <?php echo $icon_type?> <?php echo ( empty($action->getTypeInfo()->is_generated) ? 'checkin_item_posted' : 'checkin_item_generated' ) ?>">
            <?php
        $profile_url = $this->htmlLink($action->getSubject()->getHref(), $action->getSubject()->getTitle(), array('class' => 'wall_liketips', 'rev' => $action->getSubject()->getGuid()));
        $first_item = (isset($action->grouped_subjects[0])) ? $action->grouped_subjects[0] : '';
        $other_num = count($action->grouped_subjects)-1;

        $translate = '';
        if ($action->type == 'friends') {
          $translate = $this->translate(array('%1$s other people', '%1$s other peoples', $other_num), $other_num);
        } else if ($action->type == 'like_item_private') {
          $translate = $this->translate(array('%1$s other page', '%1$s other pages', $other_num), $other_num);
        }
        $other_link = '<a href="javascript:void(0);" class="wall_grouped_other">'.$translate.'</a>';

        $items_str = '';
        $count = count($action->grouped_subjects);
        if ($count > 2) {
          for ($i=0; $i<$count; $i++) {
            if ($i == 0) {
              continue ;
            }
            $subject = $action->grouped_subjects[$i];
            $items_str .= '<a href="'.$subject->getHref().'" class="wall_liketips" rev="'.$subject->getGuid().'">'.$subject->getTitle().'</a>';
            if ($i < $count-1) {
              $items_str .= ', ';
            }
          }
        } else {
          $subject = $action->grouped_subjects[1];
          $other_link = '<a href="'.$subject->getHref().'" class="wall_liketips" rev="'.$subject->getGuid().'">'.$subject->getTitle().'</a>';
        }

        $translate_key = '';
        if ($action->type == 'friends') {
          $translate_key = '%1$s is now friends with %2$s and %3$s';
        } else if ($action->type == 'like_item_private') {
          $translate_key = '%1$s likes %2$s and %3$s';
        }

        $first_item_link = $this->htmlLink($first_item->getHref(), $first_item->getTitle(), array('class' => 'wall_liketips', 'rev' => $first_item->getGuid()));
        echo $this->translate($translate_key, array($profile_url, $first_item_link, $other_link));
        ?>

        <div style="display:none;" class="wall_grouped_other_html">
          <?php echo $items_str?>
        </div>
          </span>
    </div>
      <?php else :?>
      <?php // User's profile photo ?>
    <div class='checkin_item_photo float_right_rtl'>
      <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($action->getObject(), $action->getSubject())): ?>
      <?php echo $this->htmlLink($action->getObject()->getHref(),
        $this->itemPhoto($action->getObject(), 'thumb.icon', $action->getObject()->getTitle()), array('class' => 'wall_liketips', 'rev' => $action->getObject()->getGuid())) ?>
      <?php else :?>
      <?php echo $this->htmlLink($action->getSubject()->getHref(),
        $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => 'wall_liketips', 'rev' => $action->getSubject()->getGuid())) ?>
      <?php endif ;?>
    </div>

    <div class='checkin_item_body'>
      <?php // Main Content ?>
    <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'checkin_item_posted' : 'feed_item_generated' ) ?> float_right_rtl" id="action_<?php echo $action->action_id?>">
            <?php echo $action->getContent() // n2br set in Wall_Model_Helper_Body ?>

      <?php
      $noPhoto = 'application/modules/Checkin/externals/images/nophoto.png';
      $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
      $isEventEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event');
      ?>
      <div class="checkin_item_locations">
              <span class="checkin_location_item">
                <div>
                  <?php
                  if (isset($action->checkin) && $action->checkin) {
                    $checkin = $action->checkin;
                    $checkin_visitors = $checkin->visitors;
                  } else {
                    $checkin = (isset($checkins[$action->getIdentity()]) && $checkins[$action->getIdentity()]) ? $checkins[$action->getIdentity()] : false;
                    $checkin_visitors = $this->matchedCheckinsCount[$action->getIdentity()];
                  }
                  ?>
                  <?php if ($checkin && $checkin->object_id && (($isPageEnabled && $checkin->object_type == 'page') || ($isEventEnabled && $checkin->object_type == 'event'))) : ?>
                  <?php $checkinObject = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id); ?>
                  <?php echo $this->htmlLink($checkinObject->getHref(), $this->itemPhoto($checkinObject, 'thumb.normal', '', array('class' => 'thumb_normal item_photo_page')), array('class' => 'page_profile_thumb item_thumb')); ?>
                  <?php else :?>
                  <a href="<?php echo $this->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'view-map', 'place_id' => $action->place_id), 'default', true)?>" class="smoothbox float_right_rtl_force">
                    <img src="<?php echo ($checkin->icon) ? $checkin->icon : $noPhoto; ?>" class="thumb_normal">
                  </a>
                  <?php endif; ?>
                  <div>
                    <div class="checkin_item_link_title">
                      <?php
                      if (($isPageEnabled && $checkin->object_type == 'page') || ($isEventEnabled && $checkin->object_type == 'event')) : ?>
                        <?php echo $this->htmlLink($checkinObject->getHref(), $checkinObject->getTitle()); ?>
                        <?php else : ?>
                        <?php echo $this->htmlLink($this->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'view-map', 'place_id' => $action->place_id), 'default', true), $checkin->name, array('class' => 'smoothbox')); ?>
                        <?php endif; ?>
                    </div>
                    <div class="checkin_item_vicinity">
                      <span>
                        <?php if ($isPageEnabled && $checkin->object_type == 'page') : ?>
                        <?php echo $checkinObject->country . ', ' . $checkinObject->city . ', ' . $checkinObject->street ?>
                        <?php else : ?>
                        <?php echo $checkin->vicinity; ?>
                        <?php endif; ?>
                      </span>
                    </div><div class="clr"></div>
                    <span class="checkin_like_count">
                      <?php if ($isPageEnabled && $checkin->object_type == 'page') : ?>
                      <i class="checkin_item_like">
                          <span>
                            <?php if (!$checkinObject->getLikesCount()) : ?>
                            <?php echo $this->translate('CHECKIN_no one likes')?>
                            <?php else : ?>
                            <?php echo $this->translate(array('CHECKIN_%s likes this', 'CHECKIN_%s like this', $checkinObject->getLikesCount()), $checkinObject->getLikesCount()); ?>
                            <?php endif; ?>
                          </span>
                      </i>
                      <?php endif; ?>
                      <i class="checkin_item_users_count">
                        <span><?php echo $this->translate(array('CHECKIN_%s was here', 'CHECKIN_%s were here', $checkin_visitors), $checkin_visitors);?></span>
                      </i>
                    </span>
                  </div>
                </div>
              </span>
      </div>

          </span>

      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
    <div class='checkin_item_attachments'>
      <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
      <?php if( count($action->getAttachments()) == 1 && current($action->getAttachments())->item &&
        !(current($action->getAttachments())->item instanceof Core_Model_Link) &&
        !(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album') && current($action->getAttachments())->item instanceof Album_Model_Photo)): ?>
        <?php if (
          (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('video') &&
          current($action->getAttachments())->item instanceof Video_Model_Video) ||
          (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('music') &&
          current($action->getAttachments())->item instanceof Music_Model_PlaylistSong)
        ):
          ?>
          <?php echo $this->checkinGetRichContent(false, array('action_id' => $action->action_id), current($action->getAttachments())->item); ?>
          <?php else : ?>
          <?php continue; ?>
          <?php endif; ?>
        <?php else: ?>
        <?php foreach( $action->getAttachments() as $attachment ): ?>
          <span class='feed_attachment_<?php echo ($attachment->meta->type == "core_link") ? $attachment->meta->type . '_checkin' : $attachment->meta->type?>'>
                    <?php if( $attachment->meta->mode == 0 ): // Silence ?>

            <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
            <div>
              <?php
              if ($attachment->item->getType() == "core_link") {
                $attribs = Array('target'=>'_blank');
              } else {
                $attribs = Array();
                $attribs['class'] = 'wall_liketips';
                $attribs['rev'] = $attachment->item->getGuid();
              }
              if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.slideshow', true) && Engine_Api::_()->wall()->isPhotoType($attachment->item->getType())){
                $attribs['onclick'] = 'this.href="javascript:void(0);";new Wall.Slideshow("'.$attachment->item->getPhotoUrl().'", "'.$attachment->item->getGuid().'", this);';
              }
              ?>
              <?php if( $attachment->item->getPhotoUrl() ): ?>
              <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
              <?php endif; ?>
              <div>
                <div class='checkin_item_link_title'>
                  <?php
                  echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                  ?>
                </div>
                <?php if ($attachment->meta->type != "core_link") : ?>
                <div class='checkin_item_link_desc'>
                  <?php echo $this->viewMore($attachment->item->getDescription()) ?>
                </div>
                <?php endif; ?>
              </div>
            </div>

            <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
            <div class="checkin_attachment_photo">
              <?php
              $attribs = array();
              $attribs['class'] = 'feed_item_thumb';
              if (Engine_Api::_()->wall()->isPhotoType($attachment->item->getType())){
                $attribs['onclick'] = 'new Wall.Slideshow("'.$attachment->item->getPhotoUrl().'", "'.$attachment->item->getGuid().'", this); return false;';
              }
              ?>
              <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
            </div>

            <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
            <?php echo $this->viewMore($attachment->item->getDescription()); ?>
            <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@ztodo) ?>
            <?php endif; ?>
                    </span>
          <?php endforeach; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
      <?php endif; ?>
    <div class="clr"></div>
      <?php // Icon, time since, action links ?>
      <?php
      $icon_type = 'checkin_icon_place';
      list($attachment) = $action->getAttachments();
      $canComment = ( $action->getTypeInfo()->commentable &&
        $this->viewer()->getIdentity() &&
        Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
        !empty($this->commentForm) );
      ?>
    <div class='feed_item_date feed_item_icon <?php echo $icon_type ?>'>
      <ul>
        <?php $is_option = false;?>
        <?php if( $canComment ): ?>
        <?php if( $action->likes()->isLike($this->viewer()) ): ?>
          <li class="feed_item_option_unlike">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('class' => 'action-unlike')) ?>
          </li>
          <?php else: ?>
          <li class="feed_item_option_like">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('class' => 'action-like')) ?>
          </li>
          <?php endif; ?>

        <?php if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
          <li class="feed_item_option_comment">
            <span>·</span>
            <?php echo $this->htmlLink(array('route'=>'default','module'=>'wall','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), $this->translate('Comment'), array(
            'class'=>'smoothbox',
          )) ?>
          </li>
          <?php else: ?>
          <li class="feed_item_option_comment">
            <span>·</span>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('class' => 'action-comment')) ?>
          </li>
          <?php endif; ?>
        <?php $is_option = true;?>
        <?php endif; ?>
        <?php if( $this->viewer()->getIdentity() && (
        $this->activity_moderate || (
          $this->allow_delete && (
            ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
              ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
          )
        )
      ) ): ?>
        <li class="feed_item_option_delete">
          <span>·</span>
          <a href="javascript:void(0);" class="action-remove"><?php echo $this->translate('Delete')?></a>
        </li>
        <?php $is_option = true;?>
        <?php endif;?>

        <?php // Share ?>
        <?php if( $action->getTypeInfo()->shareable && $this->viewer()->getIdentity() ): ?>
        <?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) ): ?>
          <li class="feed_item_option_share">
            <span>·</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
          <?php elseif( $action->getTypeInfo()->shareable == 2 ): ?>
          <li class="feed_item_option_share">
            <span>·</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
          <?php elseif( $action->getTypeInfo()->shareable == 3 ): ?>
          <li class="feed_item_option_share">
            <span>·</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
          <?php elseif( $action->getTypeInfo()->shareable == 4 ): ?>
          <li class="feed_item_option_share">
            <span>·</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
          <?php endif; ?>
        <?php $is_option = true;?>
        <?php endif; ?>

        <li>
          <?php if ($is_option) : ?>
          <span>·</span>
          <?php endif; ?>
          <?php echo $this->timestamp($action->getTimeValue()) ?>
        </li>

        <?php if (!empty($action->params) && is_array($action->params) && !empty($action->params['is_mobile'])):?>
        <li class="feed_item_option_is_mobile">
          <a href="javascript:void(0);" class="wall_tips wall-mobile" title="<?php echo $this->translate('WALL_IS_MOBILE')?>"></a>
        </li>
        <?php endif;?>
      </ul>
    </div>
    <div class='comments wall-comments'>
      <ul>
        <?php $this->action = $action;?>
        <?php echo $this->render('_comments.tpl')?>
      </ul>
      <?php if( $canComment ):
      echo $this->commentForm
        ->setActionIdentity($this->action->action_id)
        ->render();
    endif; ?>
    </div>
    </div>
      <?php endif; ?>
  <div class="clr"></div>
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