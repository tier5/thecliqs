<div class="frequest_heading">
    <a class="request" href="/members"><?php echo $this->translate("Find New Friends") ?></a>
</div>
<?php foreach( $this->friendrequest as $friendrequest ): ?>
<?php $subject = Engine_Api::_()->user()->getUser($friendrequest->subject_id)?>
  <li<?php if( !$friendrequest->read ): ?> class="notifications_unread"<?php endif; ?> value="<?php echo $friendrequest->getIdentity();?>">
   <div class="sdtopbar_notification_popup_pic">
   		
            <?php echo $this->itemPhoto($subject, 'thumb.icon');?>
       
    </div>
    <span class="notification_item_general notification_type_<?php echo $friendrequest->type ?>">
      <?php echo $friendrequest->__toString() ?>
    </span>
    <div class="sd-date"><?php echo date('M d',strtotime($friendrequest->date));?></div>
    <div class="sd_buttons">
    	<button class="btn_confirm"  onclick="return friendConfirm(<?php echo $friendrequest->subject_id;?>)"><?php echo $this->translate('Confirm')?></button>
    	<button class="btn_cancel" onclick="return friendCancel(<?php echo $friendrequest->subject_id;?>)"><?php echo $this->translate('Cancel')?></button>
    </div>
  </li>
<?php endforeach; ?>