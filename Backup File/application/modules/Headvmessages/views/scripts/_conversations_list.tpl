<div class="headvmessages-compose-new-wrapper">
  <a id="headvmessages-compose-new">Compose new message</a>
</div>
<div class="panel panel-default">
  <?php if ($this->paginator && $this->paginator->getTotalItemCount()): ?>
    <ul class="list-group">
      <?php foreach ($this->paginator as $item): ?>
        <?php

        $msg = Engine_Api::_()->headvmessages()->getLastMessage($item);
        $message = $item->getInboxMessage($this->viewer());
        $recipient = $item->getRecipientInfo($this->viewer());
        $resource = "";
        $sender = "";
        if ($item->hasResource() &&
          ($resource = $item->getResource())
        ) {
          $sender = $resource;
        } else if ($item->recipients > 1) {
          $sender = $this->viewer();
        } else {
          foreach ($item->getRecipients() as $tmpUser) {
            if ($tmpUser->getIdentity() != $this->viewer()->getIdentity()) {
              $sender = $tmpUser;
            }
          }
        }
        if ((!isset($sender) || !$sender) && $this->viewer()->getIdentity() !== $item->user_id) {
          $sender = Engine_Api::_()->user()->getUser($item->user_id);
        }
        if (!isset($sender) || !$sender) {
          $sender = new User_Model_User(array());
        }
        ?>

        <li class="list-group-item" id="conversation-<?php echo $item->getIdentity(); ?>" data-id="<?php echo $item->getIdentity(); ?>">
            <div class="media">
              <div class="media-left">
                <?php echo $this->htmlLink($sender->getHref(), $this->itemPhoto($sender, 'thumb.icon')) ?>
                <span
                      style="display: <?php echo (!$recipient->inbox_read) ? 'block' : 'none'; ?>;"
                      class="headvmessages-new-conversation"><?php echo $this->translate('HEADVMESSAGES_New'); ?>
                </span>
              </div>
              <div class="media-body">
                  <span class="date">
                    <?php echo $this->timestamp($msg->date) ?>
                  </span>
                  <span class="user">
                    <p class="messages_list_from_name">
                      <?php if (!empty($resource)): ?>
                        <?php echo $resource->toString() ?>
                      <?php elseif ($item->recipients == 1): ?>
                        <a href="javascript://">
                          <?php echo $sender->getTitle(); ?>
                        </a>
                      <?php
                      else: ?>
                        <?php echo $this->translate(array('%s person', '%s people', $item->recipients),
                          $this->locale()->toNumber($item->recipients)) ?>
                      <?php endif; ?>
                    </p>
                  </span>

                <div class="message">
                  <?php echo $this->string()->truncate(html_entity_decode($item->title), 30) ?>
                </div>
              </div>
            </div>

          <div class="headvmessages-conversation-controls">
            <a class="headvmessages-remove hei hei-trash-o"></a>
            <a class="headvmessages-remove-confirm hei hei-check-circle-o" data-id="<?php echo $item->getIdentity(); ?>"></a>
            <a class="headvmessages-remove-cancel hei hei-times"></a>
          </div>

        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>