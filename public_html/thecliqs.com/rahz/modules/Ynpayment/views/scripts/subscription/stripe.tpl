<?php

 $this->headScript()
	->appendFile('https://js.stripe.com/v2/')
    ->appendFile($this->baseUrl() . '/application/modules/Ynpayment/externals/scripts/buy.js');
	echo '<script type="text/javascript">Stripe.setPublishableKey("' . $this -> STRIPE_PUBLIC_KEY . '");</script>';
	echo $this->form->render($this);
?>


