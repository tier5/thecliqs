   <?php  $this->locale()->setLocale("en_US"); ?>
  <div class="headline">
  <h2>
    <?php echo $this->translate('Auction');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<h3>
<?php echo $this->translate('Transaction Listing');?>
</h3>
<?php if($this->history->getTotalItemCount() <= 0):?>
<div class="tip">
      <span>
        <?php echo $this->translate('You do not have any transactions.');?>
      </span>
    </div>
<?php else: ?>
 <ul align="right" class="global_form_box" style="background: none; padding: 0px; margin-bottom: 10px; overflow: auto;">
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>">   
<table class='admin_table' width="100%">
 <thead>
    <tr>
        <th height="25px" width="15%" style="padding:2px 2px 2px 10px;">
            <input type="checkbox" name="checkItem" onclick="checkAll(this);"/>
            <span style="padding-left: 10px"><?php echo $this->translate('Date'); ?></span></th>
        <th><?php echo $this->translate('Creator');?>  </td>
        <th><?php echo $this->translate('Buyer');?>  </td>
        <th><?php echo $this->translate('Auction name');?>  </td>
        <th><?php echo $this->translate('Amount');?>  </td>
        <th><?php echo $this->translate('Type');?>  </td>
        <th><?php echo $this->translate('Status');?>  </td>
        <th><?php echo $this->translate('Action');?>  </td>
    </tr>
 </thead>
<tbody>
 <?php foreach($this->history as $track):?>
    <tr>
        <td>
         <input type="checkbox" <?php if($track->approved == 1):?> disabled="disabled" <?php endif; ?> value="<?php echo $track->transactiontracking_id ?>" name="item_<?php echo $track->transactiontracking_id ; ?>"/>
        <span style="padding-left: 10px"> <?php echo $track->pDate ?> </span> </td>
        <td>
        	<?php if($track->user_seller): 
				$seller = Engine_Api::_()->getItem('user',$track->user_seller); 
		        echo $seller ?>	        
	        <?php else: echo "N/A"; endif;?> 
        </td>
        <td><?php if($track->user_buyer): 
        	$buyer = Engine_Api::_()->getItem('user',$track->user_buyer);       
       		echo $buyer ?>       
        <?php  else: echo "N/A"; endif; ?>
        	
        </td>
        <td><?php 
        $item = Engine_Api::_()->getItem('ynauction_product',$track->item_id); ?>
        <a href="<?php echo $item->getHref(); ?>"><?php echo $item->title; ?></a>
       </td>
        <td ><?php 
            echo $this->locale()->toCurrency($track->amount,$item->currency_symbol); ?>
        </td>        
        <td ><?php if($track->type == 1): echo $this->translate('bid'); endif;
        if($track->type == 3 || $track->type == 4): echo $this->translate('buy'); endif; ?> </td>
        <td ><?php if ($track->transaction_status == 1): echo $this->translate('Successful'); else: echo $this->translate('Fail'); endif; ?> </td>
        <td>
        <?php if($track->approved != 0):?>
        <?php if($track->approved == 1){ 
        echo $this->translate('Approved'); ?> | 
        <?php echo $this->htmlLink(array(
                  'action' => 'deny',
                  'tran_id' => $track->getIdentity(),
                  'route' => 'ynauction_account',
                  'reset' => true,
                ), $this->translate('Deny'), array(
                  'class' => ' smoothbox ',
                ));
        }else {
        echo $this->htmlLink(array(
                  'action' => 'approve',
                  'tran_id' => $track->getIdentity(),
                  'route' => 'ynauction_account',
                  'reset' => true,
                ), $this->translate('Approve'), array(
                  'class' => ' smoothbox ',
                )); ?> | 
               <?php
                  echo $this->translate('Denied'); 
                  }
                 ?>
        <?php else: ?>    
            <?php echo $this->htmlLink(array(
                  'action' => 'approve',
                  'tran_id' => $track->getIdentity(),
                  'route' => 'ynauction_account',
                  'reset' => true,
                ), $this->translate('Approve'), array(
                  'class' => ' smoothbox ',
                )) ?>
                 | 
                <?php echo $this->htmlLink(array(
                  'action' => 'deny',
                  'tran_id' => $track->getIdentity(),
                  'route' => 'ynauction_account',
                  'reset' => true,
                ), $this->translate('Deny'), array(
                  'class' => ' smoothbox ',
                )) ?>
        <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
<tr>
<td colspan="9" style="padding: 15px;">
<button type="submit" onclick="return checkSelect();"><?php echo $this->translate("Approve Selected"); ?></button>
</td>  
</tr>
</tbody>  
</table>  
</form>
<?php echo  $this->paginationControl($this->history); ?>  
</ul>
<?php endif; ?>
 <style type="text/css">
 table.admin_table thead tr th {
    background-color: #E5E5E5;
    border-bottom: 1px solid #AAAAAA;
    padding: 7px 10px;
    white-space: nowrap;
    text-align:left;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}
 </style>
 <script type="text/javascript"> 
function checkAll(obj)
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 0; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = obj.checked;
    }
  }
}
function checkSelect()
{
  var i;
  var count = 0;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 0; i < inputs.length; i++) {
    if (!inputs[i].disabled && inputs[i].checked == true) {
      count ++;
    }
  }
  if(count == 0)
  {
      alert('<?php echo $this->translate("Please select a transaction to approve!")?>');
      return false;
  }
  else
    return true;
}
</script>
