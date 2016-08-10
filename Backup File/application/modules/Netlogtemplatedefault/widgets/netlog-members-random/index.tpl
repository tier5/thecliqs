<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    random-members
 * @copyright  Copyright 2011 SocialEnginePro
 * @license    http://www.socialengine.net/license/
 * @author     altrego
 */
?>

<div class="layout_random_members">
<h2><?php echo $this->translate('sep_welcome_block_title'); ?></h2>

<div class="widget-container">
    <ul>
      <?php foreach( $this->paginator as $user ): ?>
        <li>
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle())) ?>
        </li>
      <?php endforeach; ?>
    </ul>

	<div class="sep_welcome_block_descr"><?php echo $this->translate('sep_welcome_block_descr'); ?></div>
</div>
</div>