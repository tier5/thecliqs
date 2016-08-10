<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: send.tpl  08.02.12 12:30 TeaJay $
 * @author     Taalay
 */
?>
<div style="margin: 10px">
<?php if (!$this->user->getIdentity()) : ?>
  <?php $signup_url = $this->htmlLink($this->url(array(), 'user_signup'), $this->translate("Sign Up")); ?>
  <?php $login_url = $this->htmlLink($this->url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'].'/return/1')), 'user_login'), $this->translate("Sign In")); ?>
  <span style="text-align: center"><?php echo $this->translate('HEGIFT_%s or %s', $login_url, $signup_url); ?></span>
<?php endif; ?>
</div>
