<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplate
 * @copyright  Copyright 2010-2011 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     Vadim
 */
?>
<div class="layout_active_members">
<ul>
	<?php foreach( $this->paginator as $user ): ?>
	<li>
		<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('class' => 'popularmembers_thumb')) ?>
		<div class='popularmembers_info'>
			<div class='popularmembers_name'>
				<?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
			</div>
			<div class='popularmembers_friends'>
				<?php echo $this->translate(array('%s friend', '%s friends', $user->member_count),$this->locale()->toNumber($user->member_count)) ?>
			</div>
		</div>
	</li>
	<?php endforeach; ?>
</ul>
</div>