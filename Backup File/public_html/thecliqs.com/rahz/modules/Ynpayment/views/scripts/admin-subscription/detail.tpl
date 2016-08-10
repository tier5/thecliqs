<h2>
  <?php echo $this->translate("Advanced Payment Gateway") ?>
</h2>	
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<h2 class="payment_transaction_detail_headline">
  <?php echo $this->translate('Subscription Details') ?>
</h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo $this->translate('Subscription ID') ?>
  </dd>
  <dt>
    <?php echo $this->subscription->subscription_id ?>
  </dt>

  <dd>
    <?php echo $this->translate('Member') ?>
  </dd>
  <dt>
    <?php if( $this->user && $this->user->getIdentity() ): ?>
      <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?>
      <?php //echo $this->user->__toString() ?>
      <?php if( !_ENGINE_ADMIN_NEUTER ): ?>
        <?php echo $this->translate('(%1$s)', '<a href="mailto:' .
            $this->escape($this->user->email) . '">' . $this->user->email . '</a>') ?>
      <?php endif; ?>
    <?php else: ?>
      <i><?php echo $this->translate('Deleted Member') ?></i>
      <?php echo $this->translate('(%s)', $this->translate('ID: %s', $this->subscription->user_id))  ?>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Current Member Level') ?>
  </dd>
  <dt>
    <?php if( !empty($this->actualLevel) ): ?>
      <a href='<?php echo $this->url(array('module' => 'authorization', 'controller' => 'level', 'action' => 'edit', 'id' => $this->actualLevel->level_id), 'admin_default', true) ?>'>
        <?php echo $this->translate($this->actualLevel->getTitle()) ?>
      </a>
    <?php else: ?>
      <?php echo $this->translate('N/A') ?>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo $this->translate('Plan') ?>
  </dd>
  <dt>
    <a href='<?php echo $this->url(array('action' => 'edit', 'package_id' => $this->package->package_id),'ynpayment_admin_plan', true) ?>'>
      <?php echo $this->translate($this->package->title) ?>
    </a>
  </dt>

  <dd>
    <?php echo $this->translate('Plan Member Level') ?>
  </dd>
  <dt>
    <a href='<?php echo $this->url(array('module' => 'authorization', 'controller' => 'level', 'action' => 'edit', 'id' => $this->level->level_id), 'admin_default', true) ?>'>
      <?php echo $this->translate($this->level ? $this->level->getTitle() : 'Default Level') ?>
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
    <a href='<?php echo $this->url(array('action' => 'index'), 'ynpayment_admin_subscription', true) ?>?user_id=<?php echo $this->subscription->user_id ?>'>
      <?php echo $this->translate('Member Subscription History') ?>
    </a>
    |
    <a href='<?php echo $this->url(array(), 'ynpayment_admin_transaction', true) ?>?user_id=<?php echo $this->subscription->user_id ?>'>
      <?php echo $this->translate('Member Transaction History') ?>
    </a>
    <br />
    <a href='<?php echo $this->url(array( 'action' => 'cancel'), 'ynpayment_admin_subscription', true) ?>?subscription_id=<?php echo $this->subscription->subscription_id ?>' class="smoothbox">
      <?php echo $this->translate('Cancel Subscription') ?>
    </a>
    |
    <a class="smoothbox" href='<?php echo $this->url(array('action' => 'edit'), 'ynpayment_admin_subscription', true) ?>?subscription_id=<?php echo $this->subscription->subscription_id ?>'>
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
        <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail', 'transaction_id' => $transaction->transaction_id), 'ynpayment_admin_transaction', true);?>'>
          <?php echo $this->translate("details") ?>
        </a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>