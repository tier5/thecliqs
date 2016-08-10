window.addEvent('domready', function() 
{
  var span = new Element('span', {'html':<?php echo Zend_Json::encode($this->partial('_creditButton.tpl', 'yncredit', array('action_type' => $this -> action_type, 'item_id' => $this -> item_id, 'id' => $this -> id)))?>});
  <?php if($this -> action_type == 'upgrade_subscription'):?>
  	$('buttons-wrapper').appendChild(span);
  <?php elseif($this -> action_type == 'publish_contest'):?>
  	$$('.form-elements')[0].grab(span, 'top');
  <?php else:?>
  	$$('.contentbox').getElement('form')[0].appendChild(span);
  <?php endif;?>
});