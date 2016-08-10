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

<script type="text/javascript">
	var internalTips = null;
	en4.core.runonce.add(function(){
		var options = {
			url: "<?php echo $this->url(array('action' => 'show-content'), 'like_default'); ?>",
			delay: 300,
      onShow: function(tip, element){
				var miniTipsOptions = {
					'htmlElement': '.he-hint-text',
					'delay': 1,
					'className': 'he-tip-mini',
					'id': 'he-mini-tool-tip-id',
					'ajax': false,
					'visibleOnHover': false
				};

				internalTips = new HETips($$('.he-hint-tip-links'), miniTipsOptions);
				Smoothbox.bind();
			}
		};

		var $thumbs = $$('.page-he-tip');
		var $mosts_hints = new HETips($thumbs, options);
	});
</script>

<?php $rand = rand(0, 10000); ?>
<div class="page_favorite_list">
    <?php if ($this->showManage) : ?>
      <div class="see_all_container" style="margin-left: 12px;">
        <a href="javascript:page.see_all('<?php echo $this->subject->getType(); ?>', '<?php echo $this->subject->getIdentity(); ?>');">
          <?php echo $this->translate('page_Manage'); ?>
        </a>
      </div>
    <?php endif; ?>
  <div class="clr"></div>
    <?php foreach($this->paginator as $page): ?>
    <div class="item">
      <?php echo $this->htmlLink(
        $page->getHref(),
        $this->itemPhoto($page, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_page')),
        array(
          'class' => 'page_profile_thumb page-he-tip item_thumb',
          'id' => $rand . '-page-profile_'.$page->getGuid()
        )
      ); ?>
    </div>
  <?php endforeach; ?>
      <div class="clr"></div>
</div>