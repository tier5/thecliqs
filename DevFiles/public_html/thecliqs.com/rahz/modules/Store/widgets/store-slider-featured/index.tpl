<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 taalay $
 * @author     Taalay
 */

$this->headScript()
      ->appendFile('application/modules/Store/externals/scripts/sl_slider.js');
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
		//slider variables for making things easier below
		var itemsHolder = $('slider_container');
		var myItems = $$(itemsHolder.getElements('.item'));

		//controls for slider
		var theControls = $('controls');
		var thePlayBtn = $(theControls.getElement('.play_btn'));
		var thePauseBtn = $(theControls.getElement('.pause_btn'));
		var thePrevBtn = $(theControls.getElement('.prev_btn'));
		var theNextBtn = $(theControls.getElement('.next_btn'));


		//create instance of the slider, and start it up
		var mySlider = new SL_Slider ({
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
		mySlider.start();

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

<div id="slider_container" class="slider_container">
  <?php foreach ($this->pages as $page): ?>
    <div class="item">
      <?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.normal'), array('class' => 'slider_img')); ?>
      <div class="content_info">
        <h3>
          <?php echo $this->htmlLink($page->getHref(), $this->string()->truncate($page->getTitle(), 20, '...'), array('title' => $page->getTitle())); ?>
          <?php if($page->sponsored): ?>
            <img title="<?php echo $this->translate('STORE_Sponsored'); ?>" src="application/modules/Store/externals/images/admin/sponsored1.png">
          <?php endif;?>
          <img title="<?php echo $this->translate('STORE_Featured'); ?>" src="application/modules/Store/externals/images/admin/featured1.png">
        </h3>
        <div class="rating">
          <?php echo $this->itemRate('page', $page->getIdentity()); ?>
        </div>
        <div class="clr"></div>
        <div class="page_list_submitted">
          <span class="float_left"><?php echo $this->translate("Submitted by"); ?>&nbsp;</span>
          <span class="float_left"><a href="<?php echo $page->getOwner()->getHref(); ?>"><?php echo $page->getOwner()->getTitle(); ?></a>,&nbsp;</span>
          <span class="float_left"><?php echo $this->translate("updated"); ?>&nbsp;</span>
          <span class="float_left"><?php echo $this->timestamp($page->modified_date); ?>&nbsp;</span><br />
          <?php echo $this->locale()->toNumber($page->view_count) ?> <?php echo $this->translate("views"); ?>, <?php echo $this->locale()->toNumber($page->likes()->getLikeCount()); ?> <?php echo $this->translate("likes"); ?>
        </div>
        <div class="descr">
          <?php echo $this->string()->truncate(str_replace('<br />', ' ', $page->getDescription()), 250, '...'); ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <div id="controls">
    <div class="prev_btn"></div>
    <div class="play_btn"></div>
    <div class="next_btn"></div>
  </div>
</div>


<script type="text/javascript">
en4.core.runonce.add(function(){
  $$('.item .pagereview_count').setStyle('display', 'none');
  var miniTipsOptions = {
    'htmlElement': '.pagereview_count',
    'delay': 1,
    'className': 'he-tip-mini',
    'id': 'he-mini-tool-tip-id',
    'ajax': false,
    'visibleOnHover': false
  };

  var internalTips = new HETips($$('.item .pagereview_element'), miniTipsOptions);
});
</script>