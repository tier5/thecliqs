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

<ul class="store-simple-list">
  <?php if ( count( $this->locations ) > 1): ?>
    <li>
      <a class="store_sort_buttons <?php if ($this->params['sort'] == 'newest') echo 'active'; ?>" id="store_all_locations" href="javascript:store_manager.setLocation(0);"><?php echo $this->translate("STORE_All Locations"); ?></a>
    </li>
  <?php endif; ?>

  <?php foreach ( $this->locations as $location):?>
    <li>
      <a class="city_<?php echo preg_replace('/[^a-zA-Z0-9-]/', '-', strtolower(trim($location['city'])))?> store_sort_buttons <?php if ($this->params['sort'] == 'popular') echo 'active'; ?>"
         href="javascript:store_manager.setLocation(<?php
                                                      echo "'" . $location['city'] . "'";
                                                    ?>);"><?php echo $this->string()->truncate($this->translate($location['city']), 17, '...'); ?>
      </a>
      (<?php echo $location['count']; ?>)
    </li>
  <?php endforeach; ?>
</ul>
<div class="clr"></div>
