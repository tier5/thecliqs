<?php $action_type = $this -> action_type;
if($action_type == 'upgrade_subscription'){
	$url = $this -> url(array('controller' => 'upgrade-subscription', 'action' => 'confirm'), 'yncredit_extended', true);
	echo $this->translate(' or ');
}
else 
{
	$params = array('controller' => 'spend-credit', 'action' => 'confirm', 'action_type' => $action_type, 'item_id' => $this -> item_id);
	if($this -> id)
	{
		$params['id'] = $this -> id;
	}
	$url = $this -> url($params, 'yncredit_spend', true);
}
$onclick = "window.location.href = '".$url."'; return false;";
if($action_type == 'buy_deal')
{
	$onclick = "window.location.href = '".$url."/number_item/' + $('number').value; return false;";
}
?>
<button class="spend_credit" onclick="<?php echo $onclick?>" name="execute" type="submit">
  <?php echo $this->translate('Pay with %1$s', $this->translate('Credits')); ?>
</button>
