<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: finish.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if (count($this->navigation)): ?>
<div class='store_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class="admin_home_middle settings">
<form method="get" action="<?php echo $this->continue_url; ?>"
      class="global_form store-transaction-form" enctype="application/x-www-form-urlencoded">
  <div>
    <div>

      <?php if ($this->status == 'processing'): ?>

      <h3>
        <?php echo $this->translate('Transaction Pending') ?>
      </h3>
      <p class="form-description">
        <?php echo $this->translate('STORE_REQUEST_TRANSACTION_PENDING_THANK_YOU') ?>
      </p>
      <div class="form-elements">
        <div class="form-wrapper">
          <button type="submit">
            <?php echo $this->translate('Continue') ?>
          </button>
        </div>
      </div>

      <?php elseif (in_array($this->status, array('completed', 'shipping'))): ?>

      <h3>
        <?php echo $this->translate('Transaction Complete') ?>
      </h3>
      <p class="form-description">
        <?php echo $this->translate('Your transaction has completed successfully.') ?>
      </p>
      <div class="form-elements">
        <div class="form-wrapper">
          <button type="submit">
            <?php echo $this->translate('Continue') ?>
          </button>
        </div>
      </div>

      <?php else: //if( $this->status == 'failed' ): ?>

      <?php if(isset($this->errorName)) : ?>
      <script type="text/javascript">
        var goToContactPageAfterError = function() {
          var url = '<?php echo $this->url(array('controller' => 'help', 'action' => 'contact'), 'default', true) ?>';
          var name = '<?php echo urlencode(base64_encode($this->errorName)) ?>';
          var loc = '<?php echo urlencode(base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) ?>';
          var time = '<?php echo urlencode(base64_encode(time())) ?>';
          window.location.href = url + '?name=' + name + '&loc=' + loc + '&time=' + time;
        }
      </script>
      <?php endif; ?>

      <h3>
        <?php echo $this->translate('Transaction Failed') ?>
      </h3>
        <p class="form-description">
          <?php if (empty($this->error)): ?>
        <?php echo $this->translate('Your transaction could not be completed successfully. ' .
          'We suggest that you try again with another credit card ' .
          'or funding source.') ?>
        <?php elseif (is_array($this->error)): ?>
        <?php foreach ($this->error as $error): ?>
          <p>
            <?php echo $this->translate($error) ?>
          </p>
          <?php endforeach; ?>
        <?php else: ?>
        <?php echo $this->translate($this->error) ?>
        <?php endif; ?>
      </p>
      <div class="form-elements">
        <div class="form-wrapper">
          <button type="submit">
            <?php echo $this->translate('Back to Request') ?>
          </button>
        </div>
      </div>

      <?php endif; ?>

    </div>
  </div>
</form>
</div>