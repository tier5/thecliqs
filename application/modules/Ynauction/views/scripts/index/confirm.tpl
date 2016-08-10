<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/auction.js');   
       ?>

<div style="padding: 15px;">
<input type="checkbox" id="check" value="0" name="check"/> <?php echo $this->translate('I have read & agreed to the') ?> 
<a href = 'javascript:goto();' onclick="return goto()"><?php echo $this->translate('Term of Service'); ?></a>
<input type="hidden" id="user_id" name="user_id" value="<?php echo $this->user_id; ?>"/>
<input type="hidden" id="auction_id" name="auction_id" value="<?php echo $this->auction_id; ?>"/>
<br/>
<div style="text-align: center; padding-top: 10px;">
<button type="submit" onclick ="return checkConfirm();" ><?php echo $this->translate("Save"); ?></button>
<?php echo $this->translate("or"); ?> 
<a href="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Cancel"); ?></a>
</div> 
</div> 