<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-11-04 17:07:11 taalay $
 * @author     Taalay
 */

?>

<ul class="page-categories">
  <?php if ( count( $this->locations ) > 1): ?>
    <li>
      <a class="page_sort_buttons" id="page_all_locations"
        title="<?php echo $this->translate("PAGE_All Locations")?>"
        href="<?php echo $this->url(array('sort_type'=>'location', 'sort_value'=>0), 'page_browse_sort')?>"
        onClick="page_manager.setLocation(0);return false;">
        <?php echo $this->string()->truncate($this->translate("PAGE_All Locations"), 15, '...'); ?>
      </a>
    </li>
  <?php endif; ?>

  <?php foreach ( $this->locations as $location):?>
    <li>
      <a class="city_<?php echo str_replace(' ', '', $location['city'])?> page_sort_buttons"
        href="<?php echo $this->url(array('sort_type'=>'location', 'sort_value'=>$location['city']), 'page_browse_sort')?>"
        id="page_sort_location_<?php echo str_replace(' ', '', $location['city'])?>"
        title="<?php echo $this->translate($location['city'])?>"
        onclick="page_manager.setLocation(<?php echo "'".$location['city']."'"; ?>);return false;">
        <?php echo $this->string()->truncate($this->translate($location['city']), 15, '...'); ?>
      </a>(<?php echo $location['count']; ?>)
    </li>
  <?php endforeach; ?>
</ul>