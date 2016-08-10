<ul class="ynauctions_browse" style="padding: 5px;;">
    <?php
    $product = $this->product; 
    ?>
    <h3>
    <?php echo $this->translate("Shipping"); ?>
    </h3>
    <?php echo $product->shipping_delivery ?>
    <br/>
    <span style="font-weight: bold;">
    <?php echo $this->translate("Location"); ?>: 
    </span>
    <?php echo $product->getLocation(); ?>
    <br/>
    <h3>
    <?php echo $this->translate("Payment Detail"); ?>
    </h3>
    <?php echo $product->payment_method ?>
</ul>