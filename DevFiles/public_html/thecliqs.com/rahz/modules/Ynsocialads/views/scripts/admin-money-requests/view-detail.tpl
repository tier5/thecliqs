    <div>
      <h3><?php echo $this->translate("View Detail Money Request") ?></h3>
      <br />
      <p>
      	<?php echo $this->translate("Requested by").": ".$this->money_req->getOwner()?>
      </p>
      <br />
      <p>
      	<?php echo $this->translate("Requested date").": ".$this->locale()->toDateTime($this->money_req->request_date)?>
      </p>
      <br />
      <p>
      	<?php echo $this->translate("Amount").": ".$this->money_req->amount." ".$this->money_req->currency?>
      </p>
      <br />
      <p>
      	<?php echo $this->translate("Request message").": ".$this->money_req->request_message?>
      </p>
      <br />
      <p>
      	<?php echo $this->translate("Status").": ".$this->money_req->status?>
      </p>
       <br />
       <?php if(!empty($this->money_req->payment_transaction_id)):?>
      <p>
      	<?php echo $this->translate("Payment Transaction ID").": ".$this->money_req->payment_transaction_id?>
      </p>
      <?php endif;?>
    </div>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
