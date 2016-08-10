<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>

<?php if ($this->markers && $this->paginator->getTotalItemCount() > 0): ?>
  <?php echo $this->gmap_js; ?>
<?php endif; ?>

<script type="text/javascript">
  <?php if ($this->markers && $this->paginator->getTotalItemCount() > 0): ?>
    en4.core.runonce.add(function() {
      pages_map.construct( null, <?php echo $this->markers; ?>, 4, <?php echo $this->bounds; ?> );
    });
  <?php endif; ?>

  en4.core.runonce.add(function(){
    var miniTipsOptions1 = {
      'htmlElement': '.he-hint-text',
      'delay': 1,
      'className': 'he-tip-mini',
      'id': 'he-mini-tool-tip-id',
      'ajax': false,
      'visibleOnHover': false
    };

    var internalTips1 = new HETips($$('.he-hint-tip-links'), miniTipsOptions1);
  });

  store_manager.widget_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
  store_manager.tag_id = '<?php echo isset($this->params['tag_id']) ? $this->params['tag_id'] : ''; ?>';
</script>

<div class="layout_core_container_tabs fw_active_theme_<?php echo $this->activeTheme()?>">
  <div class="tabs_alt tabs_parent">
    <ul id="main_tabs">
      <li class="<?php if ($this->sort == 'recent') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_recent" href="javascript:store_manager.setSort('recent');"><?php echo $this->translate("Recent")?></a>
      </li>
      <li class="<?php if ($this->sort == 'popular') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_popular" href="javascript:store_manager.setSort('popular');"><?php echo $this->translate("Most Popular")?></a>
      </li>
      <li class="<?php if ($this->sort == 'sponsored') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_sponsored" href="javascript:store_manager.setSort('sponsored');"><?php echo $this->translate("Sponsored")?></a>
      </li>
      <li class="<?php if ($this->sort == 'featured') echo 'active'; ?>">
        <a class="store_sort_buttons" id="store_sort_featured" href="javascript:store_manager.setSort('featured');"><?php echo $this->translate("Featured")?></a>
      </li>
      <span class="modes">
        <li class="view_mode_wrapper">
          <a class="<?php echo ($this->view == 'map')? 'active': ''; ?> map store-view-types he-hint-tip-links" onfocus="this.blur();" href="javascript://" onclick="store_manager.setView('map', $(this));"></a>
          <div class="he-hint-text hidden"><?php echo $this->translate('Map'); ?></div>
        </li>
        <li class="view_mode_wrapper">
          <a class="<?php echo ($this->view == 'list')? 'active': ''; ?> list store-view-types he-hint-tip-links" onfocus="this.blur();" href="javascript://" onclick="store_manager.setView('list', $(this));"></a>
          <div class="he-hint-text hidden"><?php echo $this->translate('List'); ?></div>
        </li>
      </span>
      <a id="store_loader_browse" class="store_loader_browse hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Store/externals/images/loader.gif', ''); ?></a>
    </ul>
  </div>
</div>

<span id="store_tag_info" class="store_tag_info hidden"></span>
<span id="store_city_info" class="store_city_info hidden"></span>
<span id="store_category_info" class="store_category_info hidden"></span>
<div class="clr"></div>

<?php if( count($this->paginator) > 0 ): ?>
  <div style="position: relative;">
    <div id="page_map_cont" style="overflow: hidden; position: relative;">
      <div id="map_canvas" class="browse_gmap" style="position: absolute; top: 100000px;">
      <?php if (!($this->markers && $this->paginator->getTotalItemCount() > 0)): ?>
        <ul class="form-notices"><li><?php echo $this->translate('There is no location data'); ?></li></ul>
      <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="he-items" id="stores-items" style="display:<?php echo ($this->view == 'list') ? 'block' : 'none'; ?>;">
    <ul class="he-item-list">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class="he-item-photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>
          <div class="he-item-info">
            <div class="he-item-title">
              <h3>
                <?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20))?>
                <?php if ($item->sponsored) : ?>
                  <img class="icon" src="application/modules/Store/externals/images/sponsored.png" title="<?php echo $this->translate('STORE_Sponsored'); ?>">
                <?php endif; ?>
                <?php if ($item->featured) : ?>
                  <img class="icon" src="application/modules/Store/externals/images/featured.png" title="<?php echo $this->translate('STORE_Featured'); ?>">
                <?php endif; ?>
              </h3>
            </div>

            <div class="rating">
              <?php echo $this->itemRate('page', $item->getIdentity()); ?>
            </div>
            <div class="clr"></div>
            <div class="he-item-details">
              <?php if (!empty($item->category_id)): ?>
                <span class="float_left"><?php echo $this->translate("STORE_Category"); ?>:&nbsp;</span>
                <span class="float_left"><a href="javascript:store_manager.setCategory(<?php echo $item->category_id; ?>);"><?php echo $item->category; ?></a></span>
                <br />
              <?php endif; ?>

              <?php if (!empty($this->page_tags[$item->getIdentity()])): ?>
                <span class="float_left"><?php echo $this->translate("Tags"); ?>:&nbsp;</span>
                <span class="float_left">
                  <?php $counter = 0; ?>
                  <?php foreach ($this->page_tags[$item->getIdentity()] as $tag): ?>
                    <?php $counter++; ?>
                      <?php if ($counter != 1): ?> <b>&#183;</b>
                    <?php endif; ?>
                    <a href="javascript:store_manager.setTag(<?php echo $tag['tag_id']; ?>);">
                      <?php echo $tag['text']; ?>
                    </a>
                  <?php endforeach; ?>
                </span>
                <br/>
              <?php endif; ?>

              <span class="float_left"><?php echo $this->translate("Submitted by"); ?>&nbsp;</span>
              <span class="float_left"><a href="<?php echo $item->getOwner()->getHref(); ?>"><?php echo $item->getOwner()->getTitle(); ?></a>,&nbsp;</span>
              <span class="float_left"><?php echo $this->translate("updated"); ?>&nbsp;</span>
              <span class="float_left"><?php echo $this->timestamp($item->modified_date); ?>&nbsp;</span> | <?php echo $this->locale()->toNumber($item->view_count) ?> <?php echo $this->translate("views"); ?>
            </div>

            <div class="he-item-desc">
              <?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription())) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="he-pagination">
      <?php echo $this->paginationControl($this->paginator, null, array("pagination/paginator.tpl","page")); ?>
    </div>
  </div>
<?php else: ?>
  <div class="tip"><span><?php echo $this->translate("There are no stores."); ?></span></div>
<?php endif; ?>