<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlog Template
 * @copyright  Copyright 2010-2011 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     Vadim
 */
?>

<script type="text/javascript">

window.addEvent('domready',function(){

	$('btnShowFriends').addEvent('click',function(){

		$dropdown = $('friendsDropDownList');

		var fxFriendsDropdown = new Fx.Tween($dropdown, { property: 'height', duration: 300, fps: 50, transition:'sine'});
		<?php
			$friends_count = count($this->friendUsers);
			if ( $friends_count==0) { $height=49; } 
			elseif($friends_count<=5) { $height=$friends_count * 49; }
			else { $height=250; }
		?>

		var dropdown_height = <?php echo $height?>;

		if ( $dropdown.getStyle('height')==dropdown_height+'px' ) {
			fxFriendsDropdown.start(dropdown_height, 0);
		} else {
			fxFriendsDropdown.start(0, dropdown_height);
		}

	});

});
</script>

<div class="layout_netlog_friends">
	<a href="javascript://" title="<?php echo $this->translate('View your friends list')?>" class="btnShowFriends" id="btnShowFriends"></a>

	<div id="friendsDropDownList">
		<div class="friendsListContainer">
			<ul>
			<?php if ( !empty($this->friendUsers) ) { ?>
				<?php foreach( $this->friendUsers as $user_info ): ?>
				<li>
					<?php echo $this->htmlLink($user_info['user']->getHref(), $this->itemPhoto($user_info['user'], 'thumb.icon', $user_info['user']->getTitle()), array('title'=>$user_info['user']->getTitle()))?>
					<div class="user_title"><?php echo $this->htmlLink($user_info['user']->getHref(), $user_info['user']->getTitle(), array('title'=>$this->translate('Go to profile')))?></div>
	
					<?php if ( !empty($user_info['profile_status']) ) { ?>
					<div class="user_profile_status"><?php echo $user_info['profile_status']?><div class="quote_close"></div></div>
					<?php } ?>
	
					<?php
						if ($user_info['message']['status']=='user1_unread')
							print $this->htmlLink($user_info['message']['conversation']->getHref(), '', array('title'=>$this->translate('You have unread message from this user'), 'class'=>'btnMessage ' . $user_info['message']['status']));
						elseif ($user_info['message']['status']=='user2_unread')
							print $this->htmlLink($user_info['message']['conversation']->getHref(), '', array('title'=>$this->translate('This user didn`t read your last message'), 'class'=>'btnMessage ' . $user_info['message']['status']));
						else
							print $this->htmlLink(array('route'=>'messages_general', 'action'=>'compose', 'to'=>$user_info['user']->getIdentity()), '', array('title'=>$this->translate('Send Message'), 'class'=>'btnMessage ' . $user_info['message']['status']));
					?>
					<?php if ( $user_info['online_status']['status']=='offline' ) { ?>
						<div class="user_status user_<?php echo $user_info['online_status']['class']?>" title="User is offline"><?php echo $this->translate('sep_userstatus_' . $user_info['online_status']['status'])?></div>
					<?php } else { ?>
						<div class="user_status user_<?php echo $user_info['online_status']['class']?>" title="<?php echo $user_info['online_status']['login_time']?>"><?php echo $this->translate('sep_userstatus_' . $user_info['online_status']['status'])?></div>
					<?php } ?>
				</li>
				<?php endforeach; ?>
	
			<?php } else { ?>
				<li class="nofriends"><?php echo $this->translate('sep_You have no friends yet')?></li>
			<?php } ?>
			</ul>
		</div>
	</div>
</div>