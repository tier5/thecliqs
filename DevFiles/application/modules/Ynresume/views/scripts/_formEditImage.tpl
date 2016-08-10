<?php if( $this->subject()->photo_id !== null ): ?>
	<?php 
		if($this->subject() -> photo_id == 0)
		{
			$owner = $this->subject()->getOwner();
			Engine_Api::_() -> core() -> clearSubject();
			Engine_Api::_() -> core() -> setSubject($owner);
		}
	?>
  <div>
    <?php echo $this->itemPhoto($this->subject(), 'thumb.profile', "", array('id' => 'lassoImg')) ?>
  </div>
  <br />
 

<?php endif; ?>