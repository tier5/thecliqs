<form action="<?php echo $this->url(array('action' => 'reject')) ?>" method="post">
    <div>
      <h3><?php echo $this->translate("Reject Request") ?></h3>
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
		     		<textarea rows="4" cols="50" onchange="changeText();" id='response_message' name='response_message'><?php echo $this->translate('I do not accept your request because of some reasons...'); ?></textarea>
	      		</div>
	      </li>
      </ul>
    </div>
      <br />
   <button type='submit' id="reject" class="button" name="submit_p" > 
   		<?php echo $this->translate('Reject')?>
   </button> 
   <?php echo $this->translate('or')?>
   <a href="<?php echo $this->url(array('module'=>'ynsocialads','controller'=>'money-requests','action' => 'index'),'admin_default', true) ?>">
        <?php echo $this->translate("cancel") ?>
    </a> 
    <input TYPE="hidden" NAME="id" VALUE=" <?php echo $this->money_req->getIdentity();?>">
</form>
