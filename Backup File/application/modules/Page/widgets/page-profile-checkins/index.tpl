<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-12-07 17:53 taalay $
 * @author     Taalay
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
<div class="page_checkin_list">
  <div class="see_all_container" style="margin-left: 10px;">
    <a href="javascript:page.see_all_checkins('<?php echo $this->subject->user_id; ?>', '<?php echo $this->subject->page_id; ?>');">
      <?php echo $this->count . ' ' . $this->translate('See All'); ?>
    </a>
  </div>
  <div class="clr"></div>
    <?php foreach($this->users as $user): ?>
    <div class="item">
      <?php echo $this->htmlLink(
        $user->getHref(),
        $this->itemPhoto($user, 'thumb.icon', '', array('class' => 'thumb_icon item_photo_page')),
        array(
          'class' => 'page_profile_thumb page-he-tip item_thumb',
          'id' => $rand . '-page-profile_'.$user->getGuid()
        )
      ); ?>
    </div>
  <?php endforeach; ?>
      <div class="clr"></div>
</div>