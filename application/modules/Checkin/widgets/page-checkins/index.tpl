<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  09.12.11 11:33 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    <?php if( !$this->renderOne ): ?>
    var anchor = $('page_checkins').getParent();
    $('page_checkin_previous').style.display = '<?php echo ( $this->actions->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('page_checkin_next').style.display = '<?php echo ( $this->actions->count() == $this->actions->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('page_checkin_previous').removeEvents('click').addEvent('click', function() {
      var url = '<?php echo $this->url(array('action' => 'index', 'content_id' => $this->identity), 'page_widget', true) ?>';
      en4.core.request.send(new Request.HTML({
        url : url,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->actions->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('page_checkin_next').removeEvents('click').addEvent('click', function(){
      var url = '<?php echo $this->url(array('action' => 'index', 'content_id' => $this->identity), 'page_widget', true) ?>';
      en4.core.request.send(new Request.HTML({
        url : url,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->actions->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });

  Wall.runonce.add(function () {
    var feed = new Wall.Feed({
      feed_uid: '<?php echo $this->feed_uid?>',
      enableComposer: 0
    });
  });
</script>

<div id="page_checkins">
  <?php
    echo $this->render('_checkin_header.tpl');
    $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
    $actions = $this->actions;
  ?>
  <div class="wallFeed" id="<?php echo $this->feed_uid?>">
    <div class="wall-streams">
      <div class="wall-stream wall-stream-social is_active">
        <ul class="wall-feed feed" id="activity-feed">
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

                <div class='feed_item_body'>

                <?php // Icon, time since, action links ?>
                <?php
                  $icon_type = 'activity_icon_'.$action->type;
                  list($attachment) = $action->getAttachments();
                  if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
                    $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
                  endif;
                ?>

                  <?php // Main Content ?>
                  <span class="wall_grouped_feed_item <?php echo $icon_type?> <?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
                    <?php
                      $profile_url = $this->htmlLink($action->getSubject()->getHref(), $action->getSubject()->getTitle(), array('class' => 'wall_liketips', 'rev' => $action->getSubject()->getGuid()));
                      $first_item = (isset($action->grouped_subjects[0])) ? $action->grouped_subjects[0] : '';
                      $other_num = count($action->grouped_subjects)-1;

                      $translate = '';
                      if ($action->type == 'friends'){
                        $translate = $this->translate(array('%1$s other people', '%1$s other peoples', $other_num), $other_num);
                      } else if ($action->type == 'like_item_private'){
                        $translate = $this->translate(array('%1$s other page', '%1$s other pages', $other_num), $other_num);
                      }
                      $other_link = '<a href="javascript:void(0);" class="wall_grouped_other">'.$translate.'</a>';

                      $items_str = '';
                      $count = count($action->grouped_subjects);
                      if ($count > 2){
                        for ($i=0; $i<$count; $i++){
                          if ($i == 0){
                            continue ;
                          }
                          $subject = $action->grouped_subjects[$i];
                          $items_str .= '<a href="'.$subject->getHref().'" class="wall_liketips" rev="'.$subject->getGuid().'">'.$subject->getTitle().'</a>';
                          if ($i < $count-1){
                            $items_str .= ', ';
                          }
                        }
                      } else {
                        $subject = $action->grouped_subjects[1];
                        $other_link = '<a href="'.$subject->getHref().'" class="wall_liketips" rev="'.$subject->getGuid().'">'.$subject->getTitle().'</a>';
                      }

                      $translate_key = '';
                      if ($action->type == 'friends'){
                        $translate_key = '%1$s is now friends with %2$s and %3$s';
                      } else if ($action->type == 'like_item_private'){
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
              <div class='feed_item_photo'>
                <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($action->getObject(), $action->getSubject())): ?>
                  <?php echo $this->htmlLink($action->getObject()->getHref(),
                    $this->itemPhoto($action->getObject(), 'thumb.icon', $action->getObject()->getTitle()), array('class' => 'wall_liketips', 'rev' => $action->getObject()->getGuid())) ?>
                <?php else :?>
                  <?php echo $this->htmlLink($action->getSubject()->getHref(),
                    $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => 'wall_liketips', 'rev' => $action->getSubject()->getGuid())) ?>
                <?php endif ;?>
              </div>

              <div class='feed_item_body'>
                <?php // Main Content ?>
                <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
                  <?php echo $action->getContent() // n2br set in Wall_Model_Helper_Body ?>
                </span>

                <?php // Attachments ?>
                <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
                  <div class='feed_item_attachments'>
                    <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
                      <?php if( count($action->getAttachments()) == 1 && current($action->getAttachments())->item &&
                              null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>
                        <?php echo $richContent; ?>
                      <?php else: ?>
                        <?php foreach( $action->getAttachments() as $attachment ): ?>
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
                                <div class='feed_item_link_title'>
                                  <?php
                                    echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                                  ?>
                                </div>
                                <div class='feed_item_link_desc'>
                                  <?php echo $this->viewMore($attachment->item->getDescription()) ?>
                                </div>
                              </div>
                            </div>

                          <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>

                            <div class="feed_attachment_photo">
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

                    <?php $page = Engine_Api::_()->getItem('page', $action->page_id); ?>
                    <li class="feed_item_option_is_checkin">
                      <p style="padding-left: 2px"><?php echo $this->translate('at ')?><a class="feed_item_checkin wall_tips" href="<?php echo $page->getHref()?>" title="<?php echo $page->country . ', ' . $page->city . ', ' . $page->street ?>" target="_blank"><?php echo $page->getTitle()?></a></p>
                    </li>

                    <?php if (!empty($action->params) && is_array($action->params) && !empty($action->params['is_mobile'])):?>
                      <li class="feed_item_option_is_mobile">
                        <span>·</span>
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
        </ul>
      </div>
    </div>
  </div>
</div>

<div>
  <div id="page_checkin_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="page_checkin_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>