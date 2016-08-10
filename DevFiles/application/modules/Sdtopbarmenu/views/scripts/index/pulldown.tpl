
<?php foreach( $this->notifications as $notification ): ?>
<?php $subject = Engine_Api::_()->user()->getUser($notification->subject_id)?>
  <li<?php if( !$notification->read ): ?> class="notifications_unread"<?php endif; ?> value="<?php echo $notification->getIdentity();?>">
  	<div class="sdtopbar_notification_popup_pic">
    	
            <?php echo $this->itemPhoto($subject, 'thumb.icon');?>
   
    </div>
    <span class="notification_item_general notification_type_<?php echo $notification->type ?>">
      <?php echo $notification->__toString() ?>
    </span>
    <div class="sd-date"><?php echo date('M d',strtotime($notification->date));?></div>
  </li>
<?php endforeach; ?>