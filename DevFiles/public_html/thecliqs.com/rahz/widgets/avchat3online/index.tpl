<div class="generic_layout_container layout_user_list_popular">
<h3>Who's chatting</h3>
<h4><?php echo count($this->available_rooms); ?> Available rooms</h4>

<?php
	
	$base_url = Zend_Controller_Front::getInstance()->getBaseUrl(); 
	$i = 0;
	foreach($this->available_rooms as $room_id=>$room_attributes){
		
		echo '<strong>'.$room_attributes['room_name'].'</strong> ';
		if($room_attributes['is_private'] == 'true'){
			echo '<img src="videochat/widget_images/lock.png" title="Private room" style="height:14px;" /> ';
		}
		
		echo '('.$room_attributes['users_count'].')';
		
		
		if($i < count($this->available_rooms) - 1){
			echo ', ';
		}
		
		$i++;
	} 

?>
<br /><br />

<p style="text-align:center"><strong><?php echo $this->htmlLink(array('route' => 'default', 'module' => 'avchat3'), 'Chat now'); ?></strong></p>

<h4><?php echo count($this->online_chatters); ?> Members <?php if ($this->visitors != 0) echo 'and '.$this->visitors.' visitors ';?>in chat </h4>
	<?php foreach($this->online_chatters as $user_id=>$user_attributes){ ?>
		<a href="<?php echo $base_url;?>/profile/<?php echo $user_id;?>"title="<?php echo trim($user_attributes['displayname']);?>" style="text-decoration:none;">
			<img src="<?php echo $user_attributes['profile_thumb'];
			//print_r($user_attributes['profile_thumb']);
			?>" class="thumb_icon item_photo_user  thumb_icon" />
		</a>
	<?php } ?>
</div>
