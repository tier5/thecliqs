<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php echo $this->render('_pageMainNavigation.tpl'); ?>

<div class='layout_middle'>    
  <div class="page_list_items">
  <?php if (count($this->paginator) == 0): ?>
  	<div class="tip"><span><?php echo $this->translate("There is no pages"); ?>, <?php echo $this->htmlLink($this->url(array(), 'page_create'), $this->translate('create page')); ?>.</span></div>
  <?php endif; ?>
    <ul class="page_list_items" id="page_list_cont">
      <?php foreach( $this->paginator as $page ): ?>
      <?php $page_id = $page->getIdentity(); ?>

      <li class="<?php if($page->sponsored) echo 'page_list_item_sponsored'?>">
        <div class="page_list_item_photo  <?php if($page->featured) echo 'featured_page'?>">
          <a href="<?php echo $page->getHref()?>">
            <span style="background-image: url(<?php echo $page->getPhotoUrl('thumb.normal'); ?>);">
          </a>
          <?php if($page->featured):?>
          <div class="page_featured">
            <span><?php echo $this->translate('Featured')?></span>
          </div>
          <?php endif;?>
        </div>

        <div class="page_list_item_info">
          <?php if( $page->sponsored ) :?>
          <div class="sponsored_page"><?php echo $this->translate('SPONSORED')?></div>
          <?php endif;?>
          <div class="page_list_title">
            <a href="<?php echo $page->getHref(); ?>">
              <?php echo $page->getTitle(); ?>
            </a>
          </div>
          <div class="page_list_info">

            <div class="l">
              <div class="page_list_rating">
                <?php echo $this->itemRate('page', $page_id); ?>
              </div>
              <br/>
              <div class="page_list_submitted">
                <?php echo $this->timestamp($page->creation_date); ?> - <?php echo $this->translate("Posted by"); ?>
                <a href="<?php echo $page->getOwner()->getHref(); ?>"><?php echo $page->getOwner()->getTitle(); ?></a>
                <?php echo $this->translate('in')?>
                <?php echo $page->category; ?>
                <?php echo $this->translate('category');?>
                | <?php if (!empty($this->page_likes[$page_id])) echo $this->page_likes[$page_id]; else echo 0; ?> <?php echo $this->translate('likes this')?>
                | <?php echo $page->view_count ?> <?php echo $this->translate("views"); ?>
              </div>

              <?php if (!empty($this->page_tags[$page_id])): ?>
              <br/>
              <strong><?php echo $this->translate("Tags"); ?>:</strong>
              <?php foreach ($this->page_tags[$page_id] as $counter => $tag): ?>
                <?php if ($counter != 0): ?> <b>&#183;</b> <?php endif; ?>
                <a id="tag_<?php echo $tag['tag_id']?>" href="javascript:page_manager.setTag(<?php echo $tag['tag_id']; ?>);"><?php echo $tag['text']; ?></a>
                <?php endforeach; ?>
              <br/>
              <?php endif; ?>
            </div>

            <div class="r">
              <?php if ($page->website): ?>
              <div class="page_list_website">
                <?php echo $page->getWebsite(); ?>
              </div>
              <?php endif; ?>

              <?php if ($page->phone): ?>
              <div class="page_list_phone"><?php echo $page->phone; ?></div>
              <?php endif; ?>

              <?php if ($page->country || $page->city || $page->state): ?>
              <div class="page_list_address"><?php echo $page->displayAddress(); ?></div>
              <?php endif; ?>

              <div class="clr"></div>
            </div>

            <div class="clr"></div>
            <div class="page_list_desc"><?php echo $page->getDescription(true, true, false, 200); ?></div>
          </div>
          <div class="page_options">
            <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $page->getIdentity()), 'page_team'), $this->translate("Edit"), array("class" => "page_edit buttonlink")); ?>
            <?php echo $this->htmlLink($this->url(array('action' => 'delete', 'page_id' => $page->getIdentity()), 'page_team'), $this->translate("Delete"), array("class" => "page_delete buttonlink")); ?>
          </div>
        </div>

      </li>

      <?php endforeach; ?>
    </ul>
   <?php if( $this->paginator->count() > 1 ): ?>
     <?php echo $this->paginationControl($this->paginator); ?>
   <?php endif; ?>
   </div>
</div>