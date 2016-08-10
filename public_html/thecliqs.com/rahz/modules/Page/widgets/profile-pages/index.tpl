<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<ul>
  <?php foreach( $this->paginator as $page ): ?>
    <li>
      <div class="pages_profile_tab_photo">
        <?php echo $this->htmlLink($page, $this->itemPhoto($page, 'thumb.normal')) ?>
      </div>
      <div class="pages_profile_tab_info">
        <div class="pages_profile_tab_title">
          <?php echo $this->htmlLink($page->getHref(), $page->getTitle()) ?>
        </div>
        <div class="pages_profile_tab_members">
          <?php
            $page_id = $page->getIdentity();
            $like_count = (!empty($this->like_counts[$page_id])) ? $this->like_counts[$page_id] : 0;
          ?>
          <?php if ($like_count): ?>
            <?php if($like_count == 1):?>
            <?php echo $this->translate('%s person like it.', $this->locale()->toNumber($like_count)); ?>
            <?php else: ?>
            <?php echo $this->translate('%s people like it.', $this->locale()->toNumber($like_count)); ?>
            <?php endif;?>
          <?php else: ?>
            <?php echo $this->translate('No one like it yet.'); ?>
          <?php endif; ?>
        </div>
        <div class="pages_profile_tab_desc">
          <?php echo $this->truncate($page->getDescription(), 300, '...'); ?>
        </div>
      </div>
      <div class="clr"></div>
      <div class="pages_profile_tab_status">
        <span>
          <?php echo $this->translate('Status: ').'<span class="bold">'.($page->admin_title ? $page->admin_title : $this->translate('Admin')).'</span>'; ?>
        </span>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if(true): ?>
  <br/>
  <?php echo $this->htmlLink($this->url(array(), 'page_browse'), $this->translate('View All Pages'), array('class' => 'buttonlink item_icon_page')) ?>
<?php endif; ?>