<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version ID: get.tpl 2/20/12 1:51 PM mt.uulu $
 * @author Mirlan
 */
?>
<a href="javascript:void(0);"
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.slideshow', true) && $this->albumPhoto): ?>
  onclick="tl_<?php echo $this->type; ?>.slideShow(
    '<?php echo $this->albumPhoto->getPhotoUrl(); ?>',
    '<?php echo $this->albumPhoto->getGuid(); ?>',
    this)"
  <?php endif; ?>
  >

  <?php echo $this->subject()->getTimelinePhoto($this->type)?>
</a>
