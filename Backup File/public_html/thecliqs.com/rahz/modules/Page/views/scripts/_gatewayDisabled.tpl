<?php if (
  isset($this->isGatewayEnabled) &&
  !$this->isGatewayEnabled ): ?>
  <ul class="form-errors store-gateway-error">
    <li>
      <?php echo $this->translate("STORE_Enable your store gateway to be able to sell your products!"); ?>
      <?php echo $this->htmlLink(
        array('route'=>'store_settings', 'action'=>'gateway', 'page_id'=>$this->page_id),
        $this->translate('STORE_Gateway Settings')); ?>
    </li>
  </ul>
<?php endif; ?>