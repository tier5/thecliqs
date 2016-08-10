<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _gatewayState.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>

<?php if ( !$this->enabled ): ?>
  <ul class="form-errors store-gateway-error">
    <li>
      <?php echo $this->translate($this->message); ?>
      <?php echo $this->htmlLink( $this->link, $this->translate($this->linkTitle)); ?>
    </li>
  </ul>
<?php endif; ?>