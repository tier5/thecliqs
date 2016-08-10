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

<div id='profile_options'>
  <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
  $this->headTranslate(array(
    'Choose pages to add to favorites list'
  ));
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_pageNavIcons.tpl', 'core'))
      ->render();
  ?>
</div>