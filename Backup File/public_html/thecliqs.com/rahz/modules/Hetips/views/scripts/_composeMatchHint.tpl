<?php
  $likeCount = Engine_Api::_()->like()->getLikeCount($this->user);
  $subject = $this->user;
  $viewer  = Engine_Api::_()->user()->getViewer();
  $type = 'user';
?>
<div class="hetips_content">
 <div class="hetips_item_photo">
   <?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.profile', '', array('width' => '100px', 'height' => '100px', 'target' => '_blank'))); ?>
 </div>
 <div class="hetips_item_body">
    <div class="hetips_item_title">
      <?php echo $this->htmlLink($subject->getHref(), $this->truncate($subject->getTitle(), 20))?>
    </div>
   <div class="clr"></div>
   <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like') && Engine_Api::_()->hetips()->getSetting($type.'_likes_count')->value): ?>
   <?php if ($likeCount):?>
     <div class="likes members_description">
       <?php echo $this->translate(array('%s like', '%s likes', $likeCount), $likeCount)?>
     </div>
     <?php endif;?>
   <?php endif; ?>

   <?php if ($this->userTips > 0): ?>
     <ul id="list_tips">
       <?php echo $this->showTips($this->userTips, 'user' ,$this->settings); ?>
     </ul>
   <?php endif; ?>

  <?php if (Engine_Api::_()->hetips()->getSetting('user_display_friends')): ?>
    <?php
    $mutual_friends = null;

    if (!$subject->isSelf($viewer)){
      if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('wall')) {
        $mutual_friends = Engine_Api::_()->wall()->getMutualFriendsPaginator($subject, $viewer);
      } else {
        $mutual_friends = Engine_Api::_()->hecore()->getMutualFriends($subject, $viewer);
      }
    }
    ?>

   <?php if ($mutual_friends && $mutual_friends->getTotalItemCount()):?>
     <div class="mutual-friends members_description">
       <?php echo $this->translate(array('%s mutual friend', '%s mutual friends', $mutual_friends->getTotalItemCount()), $mutual_friends->getTotalItemCount());?>
     </div>
     <?php echo $this->partial('index/members.tpl', 'wall', array('members' => $mutual_friends))?>
     <?php else :?>
     <?php if ($subject->membership()->getMemberCount()):?>
       <?php
         $paginator = Zend_Paginator::factory($subject->membership()->getMembersObjectSelect());
         $count = $paginator->getTotalItemCount();
       ?>

       <div class="friends members_description">
         <?php echo $this->translate(array('%s friend', '%s friends', $count), $count)?>
       </div>

       <?php echo $this->partial('index/members.tpl', 'hetips', array('members' => $paginator))?>

      <?php endif;?>
    <?php endif; ?>
  <?php endif; ?>
 </div>
  <div class="clr"></div>
  <div class="hetips_item_options">
    <?php if (!$this->isSelf && $this->viewer->getIdentity()): ?>
      <ul>
        <li><?php echo $this->userFriendship($this->user); ?></li>
        <li><?php echo $this->htmlLink($this->url(array('action' => 'compose', 'to' => $this->user->getIdentity()), 'messages_general'), $this->translate("Send Message"), array('class' => 'buttonlink', 'style' => 'background-image: url(application/modules/Messages/externals/images/send.png)','target' => '_blank')); ?></li>
      </ul>
    <div class="clr"></div>
    <?php endif; ?>
  </div>
</div>