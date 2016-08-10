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
<?php echo $this->translate('Proposal Price');?>
</h3>
<?php if(count($this->proposals)<= 0):?>
<div class="tip">
      <span>
        <?php echo $this->translate('You do not have any proposals.');?>
      </span>
    </div>
<?php else: ?>
 <ul align="right" class="global_form_box" style="background: none; padding: 0px; margin-bottom: 10px; overflow: auto;">
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>">   
<table class='admin_table' width="100%">
 <thead>
    <tr>
        <th><?php echo $this->translate('Date'); ?></th>
        <th><?php echo $this->translate('Buyer');?>  </td>
        <th><?php echo $this->translate('Amount');?>  </td>
        <th><?php echo $this->translate('Action');?>  </td>
    </tr>
 </thead>
<tbody>
 <?php foreach($this->proposals as $item):?>
    <tr>
        <td>
         <?php echo $this->locale()->toDateTime($item->proposal_time) ?> </span> </td>
        <td><?php if($item->ynauction_user_id): ?>
        <?php $buyer = Engine_Api::_()->getItem('user',$item->ynauction_user_id); ?>
        <?php echo $buyer;  else: echo "N/A"; endif;?> </td>
        <?php $pro = Engine_Api::_()->getItem('ynauction_product',$item->product_id); ?>
        <td ><?php 
            echo $this->locale()->toCurrency($item->proposal_price,$pro->currency_symbol); ?>
        </td>        
        <td>
        <?php if($item->approved != 0):?>
        <?php if($item->approved == 1){ 
        echo $this->translate('Approved'); ?> | 
        <?php echo $this->htmlLink(array(
                  'action' => 'deny',
                  'proposal_id' => $item->getIdentity(),
                  'route' => 'ynauction_proposal',
                  'reset' => true,
                ), $this->translate('Deny'), array(
                  'class' => ' smoothbox ',
                ));
        }else {
        echo $this->htmlLink(array(
                  'action' => 'approve',
                  'proposal_id' => $item->getIdentity(),
                  'route' => 'ynauction_proposal',
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
                  'proposal_id' => $item->getIdentity(),
                  'route' => 'ynauction_proposal',
                  'reset' => true,
                ), $this->translate('Approve'), array(
                  'class' => ' smoothbox ',
                )) ?>
                 | 
                <?php echo $this->htmlLink(array(
                  'action' => 'deny',
                  'proposal_id' => $item->getIdentity(),
                  'route' => 'ynauction_proposal',
                  'reset' => true,
                ), $this->translate('Deny'), array(
                  'class' => ' smoothbox ',
                )) ?>
        <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>  
</table>  
</form>
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
