<?php echo $this->render('_page_options_menu.tpl'); ?>

<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class='layout_middle'>
  <div class="page_get_started">
    <ul>
    <?php $index = 0; ?>

    <li>
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>

        <div class="get_started_title">
          <?php echo $this->translate('PAGE_EDIT_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_EDIT_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
            <?php echo $this->translate('Edit My Page >>')?>
          </a>
        </div>
      </div>
    </li>

    <li>
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_PRIVACY_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_PRIVACY_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('action' => 'privacy', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
            <?php echo $this->translate('My Page\'s Privacy >>' )?>
          </a>
        </div>
      </div>
    </li>

    <li>
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_PHOTO_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_PHOTO_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('action' => 'edit-photo', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
            <?php echo $this->translate('My Page\'s Photo >>')?>
          </a>
        </div>
      </div>
    </li>

    <?php if($this->isAllowLayout) :?>
    <li>
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_LAYOUT_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_LAYOUT_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('page' => $this->page->getIdentity()), 'page_editor', true)?>" target="_blank">
            <?php echo $this->translate('My Page\'s Layout >>')?>
          </a>
        </div>
      </div>
    </li>
      <?php endif;?>

    <li>
        <div class="get_started_main">
          <span class="get_started_photo"><?php $index++; echo $index; ?></span>
          <div class="get_started_title">
            <?php echo $this->translate('PAGE_TEAM_TITLE'); ?>
          </div>
         <div class="get_started_description">
            <?php echo $this->translate('PAGE_TEAM_DESCRIPTION') ?>
          </div>
          <div class="get_started_link">
            <a href="<?php echo $this->url(array('action' => 'manage-admins', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
              <?php echo $this->translate('My Page\'s Team >>')?>
            </a>
          </div>
        </div>
      </li>

    <?php if ($this->isAllowInvite) : ?>

    <li>
      <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_INVITE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_INVITE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'invite'), 'page_team', true)?>" target="_blank">
            <?php echo $this->translate('Invite >>')?>
          </a>
        </div>
      </div>
    </li>
      <?php endif; ?>

    <li>
      <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_PROMOTE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_PROMOTE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'promote'), 'page_team', true)?>" target="_blank">
            <?php echo $this->translate('Promote >>')?>
          </a>
        </div>
      </div>
    </li>


    <li>
      <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_UPDATE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_UPDATE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'update'), 'page_team', true)?>" target="_blank">
            <?php echo $this->translate('Seand Updates >>')?>
          </a>
        </div>
      </div>
    </li>

    <?php if( $this->isAllowedBadge ) : ?>
      <li>
        <div class="get_started_main">
          <span class="get_started_photo"><?php $index++; echo $index; ?></span>
          <div class="get_started_title">
            <?php echo $this->translate('PAGE_BADGE_TITLE'); ?>
          </div>
          <div class="get_started_description">
            <?php echo $this->translate('PAGE_BADGE_DESCRIPTION') ?>
          </div>
          <div class="get_started_link">
            <a href="<?php echo $this->url(array('action' => 'badges', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
              <?php echo $this->translate('My Page\'s Badges >>')?>
            </a>
          </div>
        </div>
      </li>
    <?php endif;?>

    <?php if ($this->isAllowStore && $this->page->getStorePrivacy()) : ?>
    <li>
      <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_STORE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_STORE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">
          <a href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'store'), 'page_team', true)?>" target="_blank">
            <?php echo $this->translate('My Store Product\'s >>')?>
          </a>
        </div>
      </div>
    </li>
    <?php endif; ?>

      <?php if ($this->isAllowPagecontact) : ?>
      <li>
        <div>
          <span class="get_started_photo"><?php $index++; echo $index; ?></span>
          <div class="get_started_title">
            <?php echo $this->translate('PAGE_CONTACT_TITLE'); ?>
          </div>
          <div class="get_started_description">
            <?php echo $this->translate('PAGE_CONTACT_DESCRIPTION'); ?>
          </div>
          <div class="get_started_link">
            <a href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'contact'), 'page_team', true)?>" target="_blank">
              <?php echo $this->translate('My Page\'s contact >>')?>
            </a>
          </div>
        </div>
      </li>
        <?php endif; ?>

      <?php if ($this->isAllowPagefaq) : ?>
      <li>
        <div>
                <span class="get_started_photo"><?php $index++; echo $index; ?></span>
          <div class="get_started_title">
            <?php echo $this->translate('PAGE_FAQ_TITLE'); ?>
          </div>
          <div class="get_started_description">
            <?php echo $this->translate('PAGE_FAQ_DESCRIPTION'); ?>
          </div>
          <div class="get_started_link">
            <a href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'faq'), 'page_team', true)?>" target="_blank">
              <?php echo $this->translate('My Page\'s FAQ >>')?>
            </a>
          </div>
        </div>
      </li>
        <?php endif; ?>

      <li>
        <div class="get_started_main">
          <div class="get_started_title">
            <span class="get_started_photo"><?php $index++; echo $index; ?></span>
            <?php echo $this->translate('PAGE_STATISTIC_TITLE'); ?>
          </div>
          <div class="get_started_description">
            <?php echo $this->translate('PAGE_STATISTIC_DESCRIPTION') ?>
          </div>
          <div class="get_started_link">
            <a href="<?php echo $this->url(array('page_id' => $this->page->getIdentity()), 'page_stat', true)?>" target="_blank">
              <?php echo $this->translate('My Page\'s Statistics >>')?>
            </a>
          </div>
        </div>
      </li>

    </ul>
  </div>
</div>