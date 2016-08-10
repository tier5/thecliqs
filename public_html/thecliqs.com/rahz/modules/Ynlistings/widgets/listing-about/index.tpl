<div class="ynlisting_widget_about">
	<div class="ynlisting_about_avatar">
	<?php echo $this->htmlLink($this->listing->getOwner()->getHref(), $this->itemPhoto($this->listing->getOwner(), 'thumb.icon')) ?>
	</div>
	<?php echo  $this->translate(' by ')?>
	<a href="<?php echo $this->listing->getOwner()->getHref();?>" title="<?php echo $this->translate($this->listing->getOwner()->getTitle());?>" style="font-weight: bold;"><?php echo $this->translate($this->listing->getOwner()->getTitle());?></a>

	<div class="ynlisting_about_count_listing">
	<?php
		$tableListing = Engine_Api::_()->getItemTable('ynlistings_listing');
		$count = $tableListing -> getTotalListingsByUser($this->listing->user_id);
	?>
	<?php echo $this -> translate(array(" %s listing."," %s listings.", $count), $count);?>
	</div>
	
	<?php if($this->listing->getOwner()->getIdentity() != $this -> viewer -> getIdentity()) :?>
	<div class="ynlisting_about_button">
		<div class="ynlisting_btn_contact">
			<span>
				<a href="mailto:<?php echo $this->listing->getOwner()->email; ?>">
					<i class="fa fa-envelope" title="Contact"></i>
					<?php echo $this->translate('Contact Seller'); ?>		
				</a>
			</span>
		</div>
		<?php if ($this->can_follow) : ?>
		<div class="ynlisting_btn_follow">
			<?php if($this -> viewer -> getIdentity()):?>
				<span class='<?php echo ($this->isFollowed)? "unfollow_seller" : "follow_seller" ?>' id='follow_seller'>
					<input id='check_follow' <?php echo ($this->isFollowed)?  "checked = 'true'" : '' ?> type='checkbox'> 
					<span class="fa fa-check-square"></span>
					<?php echo $this->translate('Following'); ?>		
				</span>
			<?php endif;?>
		</div>
		<?php endif; ?>
	</div>
	<?php endif;?>
	<div class="ynlisting_about_description rich_content_body">
		<?php echo $this -> translate($this->listing->about_us); ?>
	</div>
</div>
<?php if($this->listing->getOwner()->getIdentity() != $this -> viewer -> getIdentity()) :?>
<script type="text/javascript">
	$('check_follow').addEvent('click', function(event)
     { 	
      	var owner_id = <?php echo $this->listing->getOwner()->getIdentity();?>;
        var url = '<?php echo $this->url(array('action'=>'follow'), 'ynlistings_general')?>';
        if ($('follow_seller').className == "follow_seller") 
        {
        	var status = 1;
        }
        else
        {
        	var status = 0;
        }
	     url = url + '/status/'+status+'/owner_id/' + owner_id;
	      new Request.JSON({
				method: 'post',
				url: url,
				data: {
				},
				onSuccess: function(responseJSON) 
				{
					if(responseJSON.json == 'true')
					{
						$('follow_seller').removeProperty('class');
						$('follow_seller').addClass('unfollow_seller');
					}
					else
					{
						$('follow_seller').removeProperty('class');
						$('follow_seller').addClass('follow_seller');
					}
				}
		  }).send();
      });
</script>
<?php endif;?>