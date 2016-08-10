<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  12.04.12 18:21 TeaJay $
 * @author     Taalay
 */
?>
<?php
 $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/manager.js');
?>

<script type="text/javascript">
  wishlist.widget_url = '<?php echo $this->url(array('module'=>'core', 'controller'=>'widget', 'content_id' => $this->identity), 'default', 'true')?>';
  wishlist.id = '<?php echo $this->subject()->getIdentity(); ?>';
</script>

<div class="he-items" id="stores-items">
	<ul class="he-item-list">
    <?php
      /**
       * @var $item Store_Model_Product
       */

      foreach( $this->paginator as $item ):
    ?>
      <li>
        <div class="he-item-photo">
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
        </div>

        <div class="he-item-options store-item-options" style="text-align: center">
          <?php echo $this->getPriceBlock( $item ); ?>
          <?php if ($item->type == 'simple'): ?>
            <br />
            <div class="store_products_count">
              <?php echo $this->translate(
              array('%s item available', '%s items available', (int)$item->getQuantity()),
              $this->locale()->toNumber($item->getQuantity())); ?>
            </div>
          <?php endif; ?>
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
            <?php echo $this->itemRate($item->getType(), $item->getIdentity()); ?>
          </div>
          <div class="clr"></div>
          <div class="he-item-details">
            <span class="float_left"><?php echo $this->translate('STORE_Category') . ': '; ?>&nbsp;</span>
            <span class="float_left"><?php echo ((null !== ($category = $item->getCategory()->category)) ? $this->htmlLink($item->getCategoryHref(array('action'=>'products')), $this->translate($item->getCategory()->category), array()) : ("<i>".$this->translate("Uncategorized")."</i>")); ?></span><br>
            <span class="float_left"><?php echo $this->translate('Posted'); ?>&nbsp;</span>
            <span class="float_left"><?php echo $this->timestamp($item->creation_date); ?>&nbsp;</span>
            <?php if( $item->hasStore() ): ?>
              <?php echo $this->translate('in % store', $this->htmlLink($item->getStore()->getHref(), $item->getStore()->getTitle(), array('target' => '_blank'))); ?>
            <?php endif; ?>
            <br />
          </div>

          <div class="he-item-desc">
            <?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription()), 80) ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<div class="he-pagination">
	<?php echo $this->paginationControl($this->paginator, null, array("pagination/wishlist.tpl","store"), array(
		'storeAsQuery' => true
	)); ?>
</div>