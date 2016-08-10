<form>
  <div>
    <?php if ($this->messages && count($this->messages)): ?>
      <?php foreach ($this->messages as $message): $user = Engine_Api::_()->getItem('user', $message->user_id); ?>
        <div class="headvmessage-item">
          <?php if ($user->getIdentity() != $this->viewer()->getIdentity()): ?>
            <div class="headvmessage-item-icon">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
            </div>
          <?php endif; ?>
          <div class="headvmessage-item-body">
            <div>
              <div class="headvmessage-item-body-title">
                <div class="headvmessage-item-body-time">
                  <small><?php echo $this->timestamp($message->date) ?></small>
                </div>
                <a class="headvmessage-item-body-user"
                   href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
              </div>
              <div class="headvmessage-item-body-message">
                <?php echo nl2br(html_entity_decode($message->body)) ?>
                <?php if( !empty($message->attachment_type) && null !== ($attachment = $this->item($message->attachment_type, $message->attachment_id))): ?>
                  <div class="message_attachment">
                    <?php if(null != ( $richContent = $attachment->getRichContent(false, array('message'=>$message->conversation_id)))): ?>
                      <?php echo $richContent; ?>
                    <?php else: ?>
                      <div class="message_attachment_photo">
                        <?php if( null !== $attachment->getPhotoUrl() ): ?>
                          <?php echo $this->itemPhoto($attachment, 'thumb.normal') ?>
                        <?php endif; ?>
                      </div>
                      <div class="message_attachment_info">
                        <div class="message_attachment_title">
                          <?php echo $this->htmlLink($attachment->getHref(array('message'=>$message->conversation_id)), $attachment->getTitle()) ?>
                        </div>
                        <div class="message_attachment_desc">
                          <?php echo $attachment->getDescription() ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php if ($user->getIdentity() == $this->viewer()->getIdentity()): ?>
            <div class="headvmessage-item-icon">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php echo $this->render('_composer.tpl'); ?>

  </div>
</form>