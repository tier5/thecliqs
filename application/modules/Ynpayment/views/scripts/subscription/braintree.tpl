<?php echo $this->form->render($this); ?>
<script type="text/javascript" src="https://js.braintreegateway.com/v1/braintree.js"></script>
<script type="text/javascript">
  var braintree = Braintree.create('<?php echo $this -> braintree_cse_key; ?>');
  braintree.onSubmitEncryptForm('braintree-payment-form');
</script>

