<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>
  
<?php echo $this->suggest; ?>

<div class="suggest-view-all">
  <?php
    echo $this->htmlLink(
    $this->url(array(), 'suggest_view', true),
    $this->translate('View All Suggestions'),
    array('class' => 'bold'));
  ?>
</div>