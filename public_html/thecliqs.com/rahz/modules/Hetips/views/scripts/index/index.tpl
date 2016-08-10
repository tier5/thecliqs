<?php
  $tips = $this->tips;
  $subject = $this->subject;
  $type = $subject->getType();
  $settings = $this->settings;
  $viewer = $this->viewer();
?>
<div class="hetips_content">
  <div class="hetips_item_photo">
    <?php
    if ($subject instanceof User_Model_User){
      echo $this->htmlLink($subject->getHref(), $this->itemPhoto($subject, 'thumb.profile'));
    } else {
      echo $this->htmlLink($subject->getHref(), $this->itemPhoto($subject, 'thumb.normal'));
    }
    ?>
  </div>
  <div class="hetips_item_body">
    <div class="hetips_item_title">
      <?php echo $this->htmlLink($subject->getHref(), $this->truncate($subject->getTitle(), 20))?>
    </div>

    <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like') && Engine_Api::_()->hetips()->getSetting($type.'_likes_count')->value): ?>
      <?php $likeCount = Engine_Api::_()->like()->getLikeCount($subject); ?>
      <?php if ($likeCount):?>
      <div class="likes members_description">
        <?php echo $this->translate(array('%s like', '%s likes', $likeCount), $likeCount)?>
      </div>
      <?php endif;?>
    <?php endif; ?>

    <?php if ($tips && $tips > 0): ?>
    <ul class="<?php echo $settings[$type . '_how_display']; ?>">
      <?php echo $this->showTips($tips, $type ,$settings); ?>
    </ul>
    <?php endif; ?>

    <?php if($type == 'user'): ?>
      <?php if ($settings['user_display_friends']): ?>
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
    <?php endif; ?>

    <?php if ($type == 'group'): ?>
      <?php if (Engine_Api::_()->hetips()->getSetting('group_members_count')->value): ?>
        <?php if ($subject->membership()->getMemberCount()):?>
          <div class="members members_description">
            <?php echo $this->translate(array('%s member', '%s members', $subject->membership()->getMemberCount()), $subject->membership()->getMemberCount())?>
          </div>
          <?php echo $this->partial('index/members.tpl', 'hetips', array('members' => Zend_Paginator::factory($subject->membership()->getMembersObjectSelect())))?>
        <?php endif;?>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($type == 'page'): ?>
      <?php if (Engine_Api::_()->hetips()->getSetting('page_members_like')->value): ?>
        <?php $likes_users = Engine_Api::_()->like()->getAllLikesUsers($subject);?>
        <?php if(count($likes_users)): ?>
          <div class="page_members_like">
            <div class="page_title_members_like">
              <span><?php echo $this->translate(array('Member like this', 'Members like this', count($likes_users))); ?></span>
            </div>
            <?php echo $this->partial('index/members.tpl', 'hetips', array('members' => Zend_Paginator::factory($likes_users))); ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>

  </div>
  </div>
<div style="clear:both;"></div>
<div class="hetips_item_options">
  <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('wall')): ?>
    <ul>
      <?php
      $object = Engine_Api::_()->getApi('tips', 'wall');
      if (method_exists($object, $type)){
        $result = $object->$type($subject);

        foreach ($result as $item){
          if (!$item){
            continue ;
          }
          $url = $this->url( (empty($item['params'])) ? array() : $item['params'], (empty($item['route'])) ? 'default': $item['route'], true );
          $label = (empty($item['label'])) ? '' : $this->translate($item['label']);
          $style = (empty($item['icon'])) ? '' : 'background-image: url('.$item['icon'].')';
          $class = (empty($item['class'])) ? '' : $item['class'];

          echo '<li><a href="'.$url.'" style="'.$style.'" class="buttonlink '.$class.'">'.$label.'</a></li>';
        }
      }
      ?>
    </ul>
  <?php endif; ?>
</div>