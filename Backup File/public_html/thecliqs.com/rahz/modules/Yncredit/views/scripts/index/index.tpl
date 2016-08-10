<div class="">
	<?php echo $this -> translate("YNCREDIT_GENERAL_DESCRIPTION"); ?>
</div>
<div class="yncredit-generation">
    <div class="yncredit-table-title">
        <div><?php echo $this -> translate("Action"); ?></div>
        <div><?php echo $this -> translate("Credits")?></div>
        <div><?php echo $this -> translate("Max Credits/ Period")?></div>        
    </div>
    <?php $previousCredit = null;?>
	<?php foreach ($this->credits as $k => $credit):?>
		<?php if ( ($k == 0) || ( ($k > 0) && ($credit->module != $previousCredit->module) ) ): ?>
	    <div class="yncredit-table-toggle">
	        <span class="yncredit-toggle-icon"></span>
		    <div><?php echo ucfirst($this->translate('YNCREDIT_MODULE_'. strtoupper($credit->module))); ?></div>
	    </div>
	    <div class="yncredit-table-group">
	    <?php $previousCredit = $credit;?>
	    <?php endif; ?>
	        <div class="yncredit-table-content">
	            <div>
	            	<?php $actionTxt = $this->translate('YNCREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $credit->action_type), '_')));
	            	if($credit -> link_params)
	            	{
	            		$params = Zend_Json::decode($credit -> link_params);
	            		echo $this -> htmlLink($params, $actionTxt, array('target' => '_blank'));
	            	}
					else {
						echo $actionTxt;
					}
	            	?> 
	            	<?php if($credit->first_amount):?>
	            	<br /><span><?php  echo "(". $this -> translate(array("For first %d action", "For first %d actions", $credit->first_amount),$credit->first_amount). ", " .$this -> translate(array("each action gets %d credit", "each action gets %d credits", $credit->first_credit), $credit->first_credit).")"; ?></span>
	            	<?php endif;?>
	            </div>
	            <div><?php echo $credit->credit ?></div>
	            <div>
		            <?php echo $credit->max_credit. "/". $this -> translate(array("%d day", "%d days", $credit->period), $credit->period);?>
	            </div>      
	        </div>
		 <?php if ($k == count($this->credits)- 1 || ($k < (count($this->credits) - 1) && $this->credits -> getRow($k + 1) && $credit->module != $this->credits -> getRow($k + 1) -> module)): ?>
		 </div>
		 <?php endif; ?>	
    <?php endforeach;?>  
</div>

<script type="text/javascript">
    $$('.yncredit-table-toggle').addEvent('click', function(){
        this.getFirst('span').toggleClass('toggle-open');
        this.getNext().toggle();
    });
</script>