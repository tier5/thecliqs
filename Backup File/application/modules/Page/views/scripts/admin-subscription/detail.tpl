<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: detail.tpl 2011-08-11 17:53 taalay $
 * @author     Taalay
 */
?>

<h2 class="payment_transaction_detail_headline">
  <?php echo $this->translate('Subscription Details') ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>
<dl class="payment_transaction_details">
  <dd>
    <?php echo $this->translate('Subscription ID') ?>
  </dd>
  <dt>
    <?php echo $this->subscription->subscription_id ?>
  </dt>

  <dd>
    <?php echo $this->translate('Page') ?>
  </dd>
  <dt>
    <?php if( $this->page && $this->page->page_id ): ?>
      <?php echo $this->htmlLink($this->page->getHref(), $this->page->getTitle(), array('target' => '_parent')) ?>
      <?php //echo $this->user->__toString() ?>
    <?php else: ?>
      <i><?php echo $this->translate('Deleted Page') ?></i>
      <?php echo $this->translate('(%s)', $this->translate('ID: %s', $this->subscription->page_id))  ?>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Package') ?>
  </dd>
  <dt>
    <a href='<?php echo $this->url(array('module' => 'page', 'controller' => 'packages', 'action' => 'edit', 'package_id' => $this->package->package_id)) ?>'>
      <?php echo $this->translate($this->package->name) ?>
    </a>
  </dt>

  <dd>
    <?php echo $this->translate('Subscription State') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->subscription->status)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Created') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toDateTime($this->subscription->creation_date) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Expires') ?>
  </dd>
  <dt>
    <?php if( empty($this->subscription->expiration_date) ||
        $this->subscription->expiration_date == '0000-00-00 00:00:00' ): ?>
      <?php echo $this->translate('N/A') ?>
    <?php else: ?>
      <?php echo $this->locale()->toDateTime($this->subscription->expiration_date) ?>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Options') ?>
  </dd>
  <dt>
    <a href='<?php echo $this->url(array('module' => 'page', 'controller' => 'subscription', 'action' => 'index'), null, true) ?>?page_id=<?php echo $this->subscription->page_id ?>'>
      <?php echo $this->translate('Page Subscription History') ?>
    </a>
    |
    <a href='<?php echo $this->url(array('module' => 'page', 'controller' => 'subscription', 'action' => 'cancel'), null, true) ?>?subscription_id=<?php echo $this->subscription->subscription_id ?>' class="smoothbox">
      <?php echo $this->translate('Cancel Subscription') ?>
    </a>
    |
    <a class="smoothbox" href='<?php echo $this->url(array('module' => 'page', 'controller' => 'subscription', 'action' => 'edit'), null, true) ?>?subscription_id=<?php echo $this->subscription->subscription_id ?>'>
      <?php echo $this->translate('Edit Subscription') ?>
    </a>
  </dt>
</dl>


<h2 class="payment_transaction_detail_headline">
  <?php echo $this->translate('Related Transactions') ?>
</h2>

<table class="admin_table payment_transaction_list">
  <thead>
    <tr>
      <th>
        <?php echo $this->translate('Transaction ID') ?>
      </th>
      <th>
        <?php echo $this->translate('Gateway') ?>
      </th>
      <th>
        <?php echo $this->translate('Type') ?>
      </th>
      <th>
        <?php echo $this->translate('State') ?>
      </th>
      <th>
        <?php echo $this->translate('Amount') ?>
      </th>
      <th>
        <?php echo $this->translate('Date') ?>
      </th>
      <th>
        <?php echo $this->translate('Options') ?>
      </th>
    </tr>
  </thead>
  <tbody>
  <?php foreach( $this->transactions as $transaction ):
      $gateway = @$this->gateways[$transaction->gateway_id];
      $order = @$this->orders[$transaction->order_id];
      ?>
    <tr>
      <td>
        <?php echo $transaction->transaction_id ?>
      </td>
      <td>
        <?php if( $gateway ): ?>
          <?php echo $this->translate($gateway->title) ?>
        <?php else: ?>
          <i><?php echo $this->translate('Unknown Gateway') ?></i>
        <?php endif; ?>
      </td>
      <td class='admin_table_centered'>
        <?php echo $this->translate(ucfirst($transaction->type)) ?>
      </td>
      <td class='admin_table_centered'>
        <?php echo $this->translate(ucfirst($transaction->state)) ?>
      </td>
      <td class='admin_table_centered'>
        <?php echo $this->locale()->toCurrency($transaction->amount, $transaction->currency) ?>
        <?php echo $this->translate('(%s)', $transaction->currency) ?>
      </td>
      <td class='admin_table_centered'>
        <?php echo $this->locale()->toDateTime($transaction->timestamp) ?>
      </td>
      <td class='admin_table_options'>
        <a class="smoothbox" href='<?php echo $this->url(array('controller' => 'transactions', 'action' => 'detail', 'transaction_id' => $transaction->transaction_id));?>'>
          <?php echo $this->translate("details") ?>
        </a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>