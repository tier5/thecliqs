<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 23.08.12
 * Time: 12:29
 * To change this template use File | Settings | File Templates.
 */?>

<div class="headline">
  <h2><?php echo $this->translate('DONATION_details');?></h2>

  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>


<div class="admin_home_right he-items">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink($this->url(array('module' => 'donation', 'controller' => 'statistics', 'action' => 'list'), 'admin_default', true),
        $this->translate('Back'),
        array(
          'class' => 'buttonlink donation_back',
        )) ?>
        <br>
      </div>
    </li>
  </ul>
</div>

<div class="admin_home_middle">
  <h4 class="payment_transaction_detail_headline" style="padding: 5px 10px">
    <?php echo $this->translate("DONATION_Order Details") ?>
  </h4>

  <table class="donation-transaction-details">
    <tr>
      <td><?php echo $this->translate('Member') ?></td>
      <td>
        <?php if ($this->user && $this->user->getIdentity()): ?>
        <?php echo $this->user->__toString(); ?>
        <?php if (!_ENGINE_ADMIN_NEUTER): ?>
          <?php echo $this->translate('(%1$s)', '<a href="mailto:' .
            $this->escape($this->user->email) . '">' . $this->user->email . '</a>') ?>
          <?php endif; ?>
        <?php else: ?>
        <i><?php echo $this->translate('Deleted Member') ?></i>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Status') ?></td>
      <td>
        <?php echo ucfirst($this->item->state); ?>
        <?php if( in_array($this->item->state, array('processing', 'shipping')) ): ?>
        &nbsp;
        (<?php echo $this->htmlLink($this->url(array('action'=>'change-status')), 'Change', array('class'=>'smoothbox')); ?>)
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td><?php echo $this->translate('DONATION_Title'); ?></td>
      <?php if ($this->donation): ?>
      <td><?php echo $this->donation->__toString(); ?></td>
      <?php else: ?>
      <td><?php echo $this->item->name; ?></td>
      <?php endif; ?>
    </tr>
    <tr>
      <td><?php echo $this->translate('DONATION_Parameters'); ?></td>
      <td></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('DONATION_Date'); ?></td>
      <td><?php echo $this->timestamp($this->item->creation_date); ?></td>
    </tr>
    <tr>
      <td>
        <?php echo $this->translate("DONATION_description"); ?>
      </td>
      <td>
        <?php echo $this->item->description;?>
      </td>
    </tr>
  </table>
  <br/>

  <h4 class="payment_transaction_detail_headline" style="padding: 5px 10px">
    <?php echo $this->translate("DONATION_Payment Details") ?>
  </h4>
  <table class="donation-transaction-details">
    <tr>
      <td><?php echo $this->translate('Item Amount') ?></td>
      <td>
        <span class="donation_credit_icon">
          <span class="donation_credit_price" style="color: red;">
            <?php echo $this->locale()->toCurrency((float)($this->item->amount), $this->item->currency) ?>
            <?php echo $this->translate('(%s)', $this->item->currency) ?>
          </span>
        </span>
      </td>
    </tr>
    <tr>
      <td>
        <?php echo $this->translate('Gateway Transaction ID') ?>
      </td>
      <td>
        <?php echo $this->item->gateway_transaction_id ?>
      </td>
    </tr>
    </table>
  <br/>
</div>