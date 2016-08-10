<form action="<?php echo $this->paymentForm;?>" method="post">
    <div>
      <h3><?php echo $this->translate("Accept Request") ?></h3>
      <br />
      <ul>
	      <li>
	      		<div class='payment_label'><?php echo $this->translate("Requested by").": "?></div>
	      		<div class='payment_value'><?php echo $this->money_req->getOwner() ?></div>
	      </li>
	      <li>
	      		<div class='payment_label'><?php echo $this->translate("Requested date").": "?></div>
	      		<div class='payment_value'><?php echo $this->locale()->toDateTime($this->money_req->request_date)?></div>
	      </li>
	      <li>		
	      		<div class='payment_label'><?php echo $this->translate("Amount").": "?></div>
	      		<div class='payment_value'><?php echo$this->money_req->amount." ".$this->money_req->currency?></div>
	      </li>	
	      <li>
	      		<div class='payment_label'><?php echo $this->translate("Request message").": "?></div>
	      		<div class='payment_value'><?php echo$this->money_req->request_message?></div>
	      </li>	
	      <li>
	      		<div class='payment_label'><?php echo $this->translate("Response message").": "?></div>
	    		 <div class='payment_value' style="display: block;"> 
		     		<textarea rows="4" cols="50"  id='response_message' name='response_message'><?php echo $this->translate('I have paid your money request...'); ?></textarea>
	      		</div>
	      </li>
      </ul>
    </div>
    <br />
   <button id='approve' type="submit" class="button" name="submit_p" > 
   		<?php echo $this->translate('Send Money')?>
   </button> 
   <?php echo $this->translate('or')?>
   <a href="<?php echo $this->url(array('module'=>'ynsocialads','controller'=>'money-requests','action' => 'index'),'admin_default', true) ?>">
        <?php echo $this->translate("cancel") ?>
    </a>
   <input TYPE="hidden" NAME="cmd" VALUE="_xclick">
   <input TYPE="hidden" NAME="business" VALUE=" <?php echo $this->money_req->paypal_email;?>">
   <input TYPE="hidden" NAME="amount" VALUE="<?php echo $this->money_req->amount;?>">
   <input TYPE="hidden" NAME="currency_code" VALUE="<?php echo $this->money_req->currency;?>">
   <input TYPE="hidden" NAME="description" VALUE="Money Request">
   <input type="hidden" name="notify_url" value="<?php echo $this->ipnNotificationUrl;?>"/>
   <input type="hidden" name="return" value="<?php echo $this->returnUrl;?>"/>
   <input type="hidden" name="cancel_return" value="<?php echo $this->cancelUrl;?>"/>
   <input type="hidden" name="no_shipping" value="1"/>
   <input type="hidden" name="no_note" value="1"/>
</form>

<script type="text/javascript">
   window.addEvent('load', function() {
      $('approve').addEvent('click', function(event) {
       var url = "<?php echo
     'http://' . $_SERVER['HTTP_HOST'] 
					. Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
					  'module' => 'ynsocialads',
					  'controller' => 'money-requests',
			          'action' => 'approve',
			          'id' => $this->money_req->getIdentity(),
			        ), 'admin_default', true);
		?>";
		var response_message= $('response_message').get('value');
		url = url + "/response_message/"+response_message;
	      new Request.JSON({
				method: 'post',
				url: url,
		  }).send();
      });
    });
</script>