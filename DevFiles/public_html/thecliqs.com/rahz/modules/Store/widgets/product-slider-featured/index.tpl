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

$this->headScript()
      ->appendFile('application/modules/Store/externals/scripts/sl_slider.js');
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
		//slider variables for making things easier below
		var itemsHolder = $('product_slider_container');
		var myItems = $$(itemsHolder.getElements('.item'));

		//controls for slider
		var theControls = $('product_controls');
		var thePlayBtn = $(theControls.getElement('.products_play_btn'));
		var thePauseBtn = $(theControls.getElement('.products_pause_btn'));
		var thePrevBtn = $(theControls.getElement('.products_prev_btn'));
		var theNextBtn = $(theControls.getElement('.products_next_btn'));


		//create instance of the slider, and start it up
		var featuredProductsSlider = new SL_Slider ({
			slideTimer: 5000,
			orientation: 'horizontal',      //vertical, horizontal, or none: None will create a fading in/out transition.
			fade: true,                    //if true will fade the outgoing slide - only used if orientation is != None
			isPaused: false,
			container: itemsHolder,
			items: myItems,
			playBtn: thePlayBtn,
			prevBtn: thePrevBtn,
			nextBtn: theNextBtn
		});
    featuredProductsSlider.start();

		//adding a little animated rollover highlight to the play and prev/next buttons
		var origBkgdColor = thePlayBtn.getStyle('background-color');
		var newBkgdColor = thePlayBtn.getStyle('border-color');
		var btnArray = new Array();

    if (thePauseBtn) {
      btnArray = new Array(thePlayBtn, thePrevBtn, theNextBtn, thePauseBtn);
    } else {
      btnArray = new Array(thePlayBtn, thePrevBtn, theNextBtn);
    }

		btnArray.each(function(e, i) {
			e.set('tween', {duration: 350, transition: 'cubic:out', link: 'cancel'});
			e.addEvents({
				'mouseenter' : function() {
					this.tween('background-color', newBkgdColor);
				},
				'mouseleave' : function() {
					this.tween('background-color', origBkgdColor);
				}
			});
		});
	});

</script>

<div id="product_slider_container" class="product_slider_container">
  <?php foreach ($this->products as $product): ?>
    <div class="item">
      <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal'), array('class' => 'slider_img')); ?>
      <div class="product_content_info">
        <h3>
          <?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 20, '...'), array('title' => $product->getTitle())); ?>
          <?php if($product->sponsored): ?>
            <img title="<?php echo $this->translate('STORE_Sponsored'); ?>" src="application/modules/Store/externals/images/admin/sponsored1.png">
          <?php endif; ?>
          <img title="<?php echo $this->translate('STORE_Featured'); ?>" src="application/modules/Store/externals/images/admin/featured1.png">
        </h3>
        <div class="rating">
          <?php echo $this->itemRate('store_product', $product->getIdentity()); ?>
        </div>
        <div class="clr"></div>
        <div class="product_list_submitted">
          <span class="float_left"><?php echo $this->translate("Submitted by"); ?>&nbsp;</span>
          <span class="float_left"><a href="<?php echo $product->getOwner()->getHref(); ?>"><?php echo $product->getOwner()->getTitle(); ?></a>,&nbsp;</span>
          <span class="float_left"><?php echo $this->translate("updated"); ?>&nbsp;</span>
          <span class="float_left"><?php echo $this->timestamp($product->modified_date); ?>&nbsp;</span><br />
          <?php echo $this->locale()->toNumber($product->view_count) ?> <?php echo $this->translate("views"); ?>, <?php echo $this->locale()->toNumber($product->likes()->getLikeCount()); ?> <?php echo $this->translate("likes"); ?>
        </div>
        <div class="descr">
          <?php echo $this->string()->truncate(str_replace('<br />', ' ', $product->getDescription()), 250, '...'); ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <div id="product_controls">
    <div class="products_prev_btn"></div>
    <div class="products_play_btn"></div>
    <div class="products_next_btn"></div>
  </div>
</div>