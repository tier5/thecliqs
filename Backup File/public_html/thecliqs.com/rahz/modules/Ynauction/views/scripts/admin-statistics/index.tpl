 <?php  $this->locale()->setLocale("en_US"); ?>
 <h2><?php echo $this->translate("Auction Plugin"); ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
  <h2>
   <?php echo $this->translate("Transaction Tracking") ?>
</h2>
 <div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br /> 
    <table class="admin_table">
        <thead>
          <tr>
            <th style="text-align:center; "><?php echo $this->translate("Date") ?></th>
            <th style="text-align:center; "><?php echo $this->translate("Seller") ?> </th>
            <th style="text-align:center; "><?php echo $this->translate("Buyer") ?></th>
            <th style="text-align:center; white-space: normal; "><?php echo $this->translate("Auction Name") ?></th>
            <th style="text-align:center; "><?php echo $this->translate('Type');?>  </th>
            <th style="text-align:center; "><?php echo $this->translate("Amount") ?></th>
            <th style="text-align:center; "><?php echo $this->translate("Seller Account") ?></th>
            <th style="text-align:center; "><?php echo $this->translate("Buyer Account") ?></th>
            <th style="text-align:center; "><?php echo $this->translate("Method") ?></th>
            <th style="text-align:center; "><?php echo $this->translate("Status") ?></th>
          </tr>  
        </thead>
        <tbody>
            <?php foreach ($this->transtracking as $track):?>
            <tr style="border:1px solid">
                <td  style="text-align:center" ><?php echo $track['pDate'];?></td>                                                                                                         
                <td> <?php if($track['seller_user_name']): ?>      
                <?php $seller = Engine_Api::_()->getItem('user',$track['user_seller']); ?>
                <a href="<?php echo $seller->getHref(); ?>">
                <?php echo $track['seller_user_name']; ?>
                </a>
                <?php else: echo "N/A"; endif;?> </td>
                <td> <?php if($track['buyer_user_name']): ?>
                <?php $buyer = Engine_Api::_()->getItem('user',$track['user_buyer']); ?>
                <a href="<?php echo $buyer->getHref(); ?>">  
                <?php echo $track['buyer_user_name']; ?>
                </a>
                 <?php  else: echo "N/A"; endif; ?> </td>
                <td style="text-align:center"><?php 
                $item = Engine_Api::_()->getItem('ynauction_product',$track['item_id']); ?>
                <a href="<?php echo $item->getHref(); ?>"><?php echo $item->title; ?></a>
               </td>
                <td  style="text-align:center"  ><?php echo $track->params ?> </td> 
                <td style="text-align:center" ><?php 
                if($track->type == 0):
                    echo $this->locale()->toCurrency($track['amount'],Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD'));
                else:
                    echo $this->locale()->toCurrency($track['amount'],$item->currency_symbol);
                endif; ?>
                </td>        
                <td  style="text-align:center"  > <?php if($track['account_seller_email']):
                if($track->type == 0): 
                    $settings = Ynauction_Api_Gateway::getSettingGateway("paypal"); 
                    if($settings['admin_account']):
                        echo $settings['admin_account'];
                    else:
                        echo $this->translate("N/A");
                    endif;
                else:
                     echo $track['account_seller_email'];
                endif;
                else: echo "Admin";  endif;?> </td>
                <td  style="text-align:center"  > <?php if($track['account_buyer_email']): echo $track['account_buyer_email'];else: echo "N/A"; endif;?> </td>
                 <td  style="text-align:center;" ><?php echo $track -> method;?> </td>
                <td  style="text-align:center;" ><?php if($track['transaction_status'] == 1): echo $this->translate('Successful');?> <?php else: echo $this->translate('Fail');?>  <?php endif;?> </td>
            </tr>
           <?php endforeach;?> 
        </tbody>
    </table> 
    <?php  echo $this->paginationControl($this->transtracking, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->filterValues,
    ));     ?>  
    <div class="clear"></div>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {
 display: table;
  height: 65px;
}
</style>