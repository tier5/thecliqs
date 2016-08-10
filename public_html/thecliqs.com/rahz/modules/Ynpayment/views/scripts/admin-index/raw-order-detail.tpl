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
  <?php echo $this->translate("Raw Order Details") ?>
</h2>

<?php if( !is_array($this->data) ): ?>

  <div class="error">
    <span>
    <?php $this->translate('Order could not be found.') ?>
    </span>
  </div>

<?php else: ?>

  <dl class="payment_transaction_details">
    <?php foreach( $this->data as $key => $value ): ?>
      <dd>
        <?php echo $key ?>
      </dd>
      <dt>
        <?php echo $value ?>
      </dt>
    <?php endforeach; ?>
  </dl>

<?php endif; ?>