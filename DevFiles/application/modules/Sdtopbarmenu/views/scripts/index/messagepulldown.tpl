<div class="message_heading">
    <a class="compose" href="/messages/compose"><?php echo $this->translate("Send New Message") ?></a>
</div>
<?php foreach( $this->messagenew as $messagenew ): ?>
<?php $subject = Engine_Api::_()->user()->getUser($messagenew->subject_id)?>
  
  <li<?php if( !$messagenew->read ): ?> class="notifications_unread"<?php endif; ?> value="<?php echo $messagenew->getIdentity();?>">
   <div class="sdtopbar_notification_popup_pic">
    	
            <?php echo $this->itemPhoto($subject, 'thumb.icon');?>
        
    </div>
    <span class="notification_item_general notification_type_<?php echo $messagenew->type ?>">
      <?php echo $messagenew->__toString() ?>
    </span>
    <div class="sd-date"><?php echo date('M d',strtotime($messagenew->date));?></div>
  </li>
<?php endforeach; ?>